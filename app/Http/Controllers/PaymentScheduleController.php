<?php

namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function update(Request $request, PaymentSchedule $schedule)
    {
        $validated = $request->validate([
            'actual_date' => 'required|date',
            'actual_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Update payment schedule
            $schedule->actual_date = $validated['actual_date'];
            $schedule->actual_amount = $validated['actual_amount'];
            $schedule->note = $validated['note'] ?? $schedule->note;
            $schedule->difference = $validated['actual_amount'] - $schedule->planned_amount;

            // Auto-update status
            if ($schedule->actual_amount >= $schedule->planned_amount) {
                $schedule->status = PaymentSchedule::STATUS_PAID;
            } elseif ($schedule->actual_amount > 0) {
                $schedule->status = PaymentSchedule::STATUS_PARTIAL;
            }

            $schedule->save();

            // Update contract paid amount
            $contract = $schedule->contract;
            $totalPaid = $contract->paymentSchedules()->sum('actual_amount');
            $contract->paid_amount = $totalPaid;
            $contract->remaining_amount = $contract->contract_amount - $totalPaid;

            // Auto-complete contract if fully paid
            if ($contract->remaining_amount <= 0) {
                $contract->status = Contract::STATUS_COMPLETED;
            }

            $contract->save();

            DB::commit();

            return redirect()->route('contracts.show', $contract)
                ->with('success', 'Тўлов маълумотлари янгиланди');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function destroy(PaymentSchedule $schedule)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Рухсат йўқ');
        }

        if ($schedule->distributions()->count() > 0) {
            return back()->with('error', 'Тақсимоти бор тўловни ўчириб бўлмайди');
        }

        $contractId = $schedule->contract_id;
        $schedule->delete();

        return redirect()->route('contracts.show', $contractId)
            ->with('success', 'Тўлов графигидан ўчирилди');
    }
}
