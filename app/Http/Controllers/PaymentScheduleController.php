<?php

namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for editing the payment schedule
     */
    public function edit(PaymentSchedule $schedule)
    {
        $user = Auth::user();

        // Check access permissions
        if ($user->role === 'district_user' &&
            $schedule->contract->lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        // Don't allow editing if distributions exist
        if ($schedule->distributions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Тақсимоти бор график қаторини таҳрирлаб бўлмайди');
        }

        $contract = $schedule->contract;

        return view('payment-schedules.edit', compact('schedule', 'contract'));
    }

    /**
     * Update the payment schedule
     */
    public function update(Request $request, PaymentSchedule $schedule)
    {
        $user = Auth::user();

        // Check access permissions
        if ($user->role === 'district_user' &&
            $schedule->contract->lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        // Don't allow editing if distributions exist
        if ($schedule->distributions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Тақсимоти бор график қаторини таҳрирлаб бўлмайди');
        }

        // Determine if this is a payment recording or schedule editing
        $isPaymentRecording = $request->has('actual_amount') && $request->filled('actual_amount');

        if ($isPaymentRecording) {
            // Payment recording validation
            $validated = $request->validate([
                'actual_date' => 'required|date',
                'actual_amount' => 'required|numeric|min:0',
                'reference_number' => 'nullable|string|max:100',
                'note' => 'nullable|string|max:1000',
            ]);
        } else {
            // Schedule editing validation
            $validated = $request->validate([
                'planned_date' => 'required|date',
                'deadline_date' => 'nullable|date|after_or_equal:planned_date',
                'planned_amount' => 'required|numeric|min:0',
            ]);
        }

        DB::beginTransaction();
        try {
            if ($isPaymentRecording) {
                // Update payment information
                $schedule->update([
                    'actual_date' => $validated['actual_date'],
                    'actual_amount' => $validated['actual_amount'],
                    'difference' => $validated['actual_amount'] - $schedule->planned_amount,
                    'note' => $validated['note'] ?? $schedule->note,
                ]);

                // Auto-update status
                if ($schedule->actual_amount >= $schedule->planned_amount) {
                    $schedule->status = PaymentSchedule::STATUS_PAID;
                } elseif ($schedule->actual_amount > 0) {
                    $schedule->status = PaymentSchedule::STATUS_PARTIAL;
                }
                $schedule->save();

                // Update contract totals
                $contract = $schedule->contract;
                $contract->paid_amount = $contract->paymentSchedules()->sum('actual_amount');
                $contract->remaining_amount = $contract->contract_amount - $contract->paid_amount;

                // Auto-complete if fully paid
                if ($contract->remaining_amount <= 0) {
                    $contract->status = 'completed';
                }
                $contract->save();

                // Update lot
                $lot = $contract->lot;
                $lot->paid_amount = $contract->paid_amount;
                $lot->transferred_amount = $contract->paid_amount;
                $lot->autoCalculate();
                $lot->save();

                $successMessage = 'Тўлов маълумотлари муваффақиятли сақланди';
            } else {
                // Validate total doesn't exceed sold price
                $contract = $schedule->contract;
                $lot = $contract->lot;
                $soldPrice = $lot->sold_price ?? 0;

                // Calculate new total (excluding current schedule)
                $existingTotal = $contract->paymentSchedules()
                    ->where('id', '!=', $schedule->id)
                    ->sum('planned_amount');

                $newTotal = $existingTotal + $validated['planned_amount'];

                if ($newTotal > $soldPrice) {
                    $exceeds = $newTotal - $soldPrice;
                    return back()
                        ->withInput()
                        ->with('error',
                            'Хатолик: График суммаси аукцион нархидан ' .
                            number_format($exceeds, 0, '.', ' ') .
                            ' сўм кўп. Жами график: ' . number_format($newTotal, 0, '.', ' ') .
                            ' сўм, Сотилган нарх: ' . number_format($soldPrice, 0, '.', ' ') . ' сўм'
                        );
                }

                // Update schedule details
                $schedule->update([
                    'planned_date' => $validated['planned_date'],
                    'deadline_date' => $validated['deadline_date'] ?? $validated['planned_date'],
                    'planned_amount' => $validated['planned_amount'],
                ]);

                // Recalculate difference if payment already made
                if ($schedule->actual_amount > 0) {
                    $schedule->difference = $schedule->actual_amount - $schedule->planned_amount;
                    $schedule->save();
                }

                $successMessage = 'График маълумотлари муваффақиятли янгиланди';
            }

            DB::commit();

            Log::info('Payment schedule updated successfully', [
                'schedule_id' => $schedule->id,
                'user_id' => $user->id,
                'is_payment_recording' => $isPaymentRecording,
            ]);

            return redirect()->back()
                ->with('success', $successMessage);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment schedule update error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'schedule_id' => $schedule->id,
            ]);

            return back()
                ->withInput()
                ->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    /**
     * Remove the payment schedule
     */
    public function destroy(PaymentSchedule $schedule)
    {
        $user = Auth::user();

        // Only admin can delete payment schedules
        if ($user->role !== 'admin') {
            abort(403, 'Фақат администратор график қаторини ўчириши мумкин');
        }

        // Check if schedule has payments recorded
        if ($schedule->actual_amount > 0) {
            return redirect()->back()
                ->with('error', 'Тўлов қайд қилинган график қаторини ўчириб бўлмайди');
        }

        // Check if schedule has distributions
        if ($schedule->distributions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Тақсимоти бор график қаторини ўчириб бўлмайди');
        }

        DB::beginTransaction();
        try {
            $contractId = $schedule->contract_id;

            $schedule->delete();

            // Reorder remaining schedules
            $remainingSchedules = PaymentSchedule::where('contract_id', $contractId)
                ->orderBy('planned_date')
                ->get();

            foreach ($remainingSchedules as $index => $s) {
                $s->payment_number = $index + 1;
                $s->save();
            }

            DB::commit();

            Log::info('Payment schedule deleted successfully', [
                'schedule_id' => $schedule->id,
                'user_id' => $user->id,
            ]);

            return redirect()->back()
                ->with('success', 'График қатори муваффақиятли ўчирилди');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment schedule deletion error', [
                'error' => $e->getMessage(),
                'schedule_id' => $schedule->id,
            ]);

            return back()
                ->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    /**
     * Clear payment from a schedule (reset actual values)
     */
    public function clearPayment(PaymentSchedule $schedule)
    {
        $user = Auth::user();

        // Only admin can clear payments
        if ($user->role !== 'admin') {
            abort(403, 'Фақат администратор тўловни бекор қилиши мумкин');
        }

        // Don't allow clearing if distributions exist
        if ($schedule->distributions()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Тақсимоти бор тўловни бекор қилиб бўлмайди. Аввал тақсимотни ўчиринг.');
        }

        DB::beginTransaction();
        try {
            // Clear payment data
            $schedule->update([
                'actual_date' => null,
                'actual_amount' => 0,
                'difference' => 0,
                'status' => PaymentSchedule::STATUS_PENDING,
                'note' => ($schedule->note ? $schedule->note . "\n\n" : '') .
                          '[' . now()->format('Y-m-d H:i') . '] Тўлов бекор қилинди: ' . $user->name,
            ]);

            // Update contract totals
            $contract = $schedule->contract;
            $contract->paid_amount = $contract->paymentSchedules()->sum('actual_amount');
            $contract->remaining_amount = $contract->contract_amount - $contract->paid_amount;
            $contract->save();

            // Update lot
            $lot = $contract->lot;
            $lot->paid_amount = $contract->paid_amount;
            $lot->transferred_amount = $contract->paid_amount;
            $lot->autoCalculate();
            $lot->save();

            DB::commit();

            Log::info('Payment cleared from schedule', [
                'schedule_id' => $schedule->id,
                'user_id' => $user->id,
            ]);

            return redirect()->back()
                ->with('success', 'Тўлов маълумотлари бекор қилинди');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Payment clearing error', [
                'error' => $e->getMessage(),
                'schedule_id' => $schedule->id,
            ]);

            return back()
                ->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }
}
