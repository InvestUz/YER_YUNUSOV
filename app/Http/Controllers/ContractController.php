<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Lot;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContractController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Contract::with(['lot.tuman', 'creator']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment type
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contract_number', 'like', "%{$search}%")
                    ->orWhereHas('lot', function ($lotQuery) use ($search) {
                        $lotQuery->where('lot_number', 'like', "%{$search}%")
                            ->orWhere('winner_name', 'like', "%{$search}%");
                    });
            });
        }

        $contracts = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('contracts.index', compact('contracts'));
    }

    public function create(Request $request)
    {
        $lotId = $request->get('lot_id');

        if (!$lotId) {
            return redirect()->route('lots.index')
                ->with('error', 'Лот танланмаган');
        }

        $lot = Lot::with(['tuman', 'mahalla'])->findOrFail($lotId);

        // Check if contract already exists
        if ($lot->contract) {
            return redirect()->back()
                ->with('error', 'Бу лот учун шартнома мавжуд');
        }

        // Check access
        $user = Auth::user();
        if ($user->role === 'district_user' && $lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        // Auto-fill contract number suggestion
        $lastContract = Contract::orderBy('id', 'desc')->first();
        $nextNumber = $lastContract ? ((int)filter_var($lastContract->contract_number, FILTER_SANITIZE_NUMBER_INT)) + 1 : 1;
        $suggestedNumber = 'Ш-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        return view('contracts.create', compact('lot', 'suggestedNumber'));
    }

    public function store(Request $request)
    {
        // Determine which fields to validate based on payment type
        $paymentType = $request->input('payment_type');

        if ($paymentType === 'muddatli') {
            $validated = $request->validate([
                'lot_id' => 'required|exists:lots,id',
                'payment_type' => 'required|in:muddatli,muddatsiz',
                'contract_number' => 'required|string|max:255',
                'contract_date' => 'required|date',
                'contract_amount' => 'required|numeric|min:0',
                'buyer_name' => 'required|string|max:255',
                'buyer_phone' => 'nullable|string|max:50',
                'buyer_inn' => 'nullable|string|max:50',
                'note' => 'nullable|string',
                'initial_paid_amount' => 'nullable|numeric|min:0',
                'initial_payment_date' => 'nullable|date',
            ]);

            // Additional validation: initial payment can't exceed contract amount
            if (!empty($validated['initial_paid_amount']) && $validated['initial_paid_amount'] > $validated['contract_amount']) {
                return back()
                    ->withInput()
                    ->withErrors(['initial_paid_amount' => 'Аввал тўланған сумма шартнома суммасидан кўп бўлолмайди']);
            }
        } else {
            // Muddatsiz validation
            $validated = $request->validate([
                'lot_id' => 'required|exists:lots,id',
                'payment_type' => 'required|in:muddatli,muddatsiz',
                'actual_paid_amount' => 'required|numeric|min:0',
                'actual_payment_date' => 'nullable',
                'reference_number' => 'nullable|string|max:255',
            ]);
        }

        DB::beginTransaction();
        try {
            $lot = Lot::findOrFail($validated['lot_id']);

            // Check if lot already has a contract
            if ($lot->hasContract()) {
                return back()
                    ->withInput()
                    ->with('error', 'Бу лот учун шартнома аллақачон мавжуд');
            }

            if ($validated['payment_type'] === 'muddatli') {
                // ============ MUDDATLI PAYMENT ============
                $contract = Contract::create([
                    'lot_id' => $lot->id,
                    'contract_number' => $validated['contract_number'],
                    'contract_date' => $validated['contract_date'],
                    'contract_amount' => $validated['contract_amount'],
                    'buyer_name' => $validated['buyer_name'],
                    'buyer_phone' => $validated['buyer_phone'] ?? null,
                    'buyer_inn' => $validated['buyer_inn'] ?? null,
                    'payment_type' => 'muddatli',
                    'status' => $validated['status'] ?? 'active',
                    'note' => $validated['note'] ?? null,
                ]);

                Log::info('Muddatli contract created', [
                    'contract_id' => $contract->id,
                    'lot_id' => $lot->id,
                    'contract_number' => $contract->contract_number
                ]);

                // Update lot
                $lot->payment_type = 'muddatli';

                // Handle initial payment if provided
                if (!empty($validated['initial_paid_amount']) && $validated['initial_paid_amount'] > 0) {
                    $lot->paid_amount = $validated['initial_paid_amount'];
                    $lot->transferred_amount = $validated['initial_paid_amount'];

                    Log::info('Initial payment recorded for muddatli contract', [
                        'lot_id' => $lot->id,
                        'initial_paid_amount' => $validated['initial_paid_amount']
                    ]);
                }

                $lot->save();
                $lot->autoCalculate();
                $lot->save();

                $successMessage = 'Шартнома муваффақиятли яратилди';
            } else {
                // ============ MUDDATSIZ PAYMENT ============

                // Update lot first
                $lot->payment_type = 'muddatsiz';
                $lot->paid_amount = $validated['actual_paid_amount'];
                $lot->transferred_amount = $validated['actual_paid_amount'];
                $lot->save();
                $lot->autoCalculate();
                $lot->save();

                Log::info('Muddatsiz payment recorded on lot', [
                    'lot_id' => $lot->id,
                    'paid_amount' => $validated['actual_paid_amount']
                ]);

                // Create a "virtual" contract for muddatsiz
                $contract = Contract::create([
                    'lot_id' => $lot->id,
                    'contract_number' => 'MUDDATSIZ-' . $lot->lot_number . '-' . now()->format('Ymd'),
                    'contract_date' => $validated['actual_payment_date'] ?? '',
                    'contract_amount' => $validated['actual_paid_amount'],
                    'buyer_name' => $lot->winner_name,
                    'buyer_phone' => $lot->winner_phone,
                    'payment_type' => 'muddatsiz',
                    'status' => 'completed',
                    'note' => 'Муддатсиз тўлов - Автоматик яратилган',
                ]);

                Log::info('Virtual contract created for muddatsiz', [
                    'contract_id' => $contract->id,
                    'contract_number' => $contract->contract_number
                ]);

                // ✅ CREATE AUTOMATIC PAYMENT SCHEDULE FOR MUDDATSIZ
                $paymentSchedule = PaymentSchedule::create([
                    'contract_id' => $contract->id,
                    'payment_number' => 1,
                    'planned_date' => $validated['actual_payment_date'] ?? '',
                    'planned_amount' => $validated['actual_paid_amount'],
                    'actual_date' => $validated['actual_payment_date'] ?? '',
                    'actual_amount' => $validated['actual_paid_amount'],
                    'status' => 'paid',
                    'payment_method' => 'bank_transfer',
                    'reference_number' => $validated['reference_number'] ?? null,
                    'note' => 'Муддатсиз тўлов - Бир марталик тўлов',
                ]);

                Log::info('Automatic payment schedule created for muddatsiz', [
                    'payment_schedule_id' => $paymentSchedule->id,
                    'contract_id' => $contract->id
                ]);

                $successMessage = 'Муддатсиз тўлов муваффақиятли қайд қилинди';
            }

            DB::commit();

            Log::info('Contract creation completed successfully', [
                'lot_id' => $lot->id,
                'contract_id' => $contract->id,
                'payment_type' => $validated['payment_type']
            ]);

            return redirect()
                ->route('lots.show', $lot)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Contract creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'lot_id' => $validated['lot_id'] ?? null
            ]);

            return back()
                ->withInput()
                ->with('error', 'Хатолик юз берди: ' . $e->getMessage());
        }
    }

    public function addScheduleItem(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'planned_date' => 'required|date',
            'planned_amount' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Get lot's sold price
            $lot = $contract->lot;
            $soldPrice = $lot->sold_price ?? 0;

            // Calculate total planned amount (existing + new)
            $existingTotal = $contract->paymentSchedules()->sum('planned_amount');
            $newTotal = $existingTotal + $validated['planned_amount'];

            // Check if exceeding sold price
            if ($newTotal > $soldPrice) {
                $exceeds = $newTotal - $soldPrice;
                return back()->with(
                    'error',
                    'Хатолик: График суммаси аукцион нархидан ' .
                        number_format($exceeds, 0, '.', ' ') .
                        ' сўм кўп. Жами график: ' . number_format($newTotal, 0, '.', ' ') .
                        ' сўм, Сотилган нарх: ' . number_format($soldPrice, 0, '.', ' ') . ' сўм'
                );
            }

            // Get next payment number
            $lastPayment = $contract->paymentSchedules()->orderBy('payment_number', 'desc')->first();
            $paymentNumber = $lastPayment ? $lastPayment->payment_number + 1 : 1;

            // Create payment schedule item
            PaymentSchedule::create([
                'contract_id' => $contract->id,
                'payment_number' => $paymentNumber,
                'planned_date' => $validated['planned_date'],
                'deadline_date' => $validated['deadline_date'] ?? $validated['planned_date'],
                'planned_amount' => $validated['planned_amount'],
                'actual_amount' => 0,
                'status' => PaymentSchedule::STATUS_PENDING,
            ]);

            DB::commit();

            // Prepare success message
            $successMessage = 'График қатори муваффақиятли қўшилди.';

            if ($newTotal == $soldPrice) {
                $successMessage .= ' ✓ График суммаси аукцион нархига тўғри келди.';
            } elseif ($newTotal < $soldPrice) {
                $remaining = $soldPrice - $newTotal;
                $successMessage .= ' Қолган сумма: ' . number_format($remaining, 0, '.', ' ') . ' сўм';
            }

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function recordPayment(Request $request, PaymentSchedule $schedule)
    {
        $validated = $request->validate([
            'actual_date' => 'required|date',
            'actual_amount' => 'required|numeric|min:0',
            'reference_number' => 'nullable|string|max:100',
            'note' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Update payment schedule
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
                $contract->status = Contract::STATUS_COMPLETED;
            }
            $contract->save();

            // Update lot
            $contract->lot->update([
                'paid_amount' => $contract->paid_amount,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Тўлов маълумотлари сақланди');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function show(Contract $contract)
    {
        $contract->load([
            'lot.tuman',
            'lot.mahalla',
            'paymentSchedules' => function ($query) {
                $query->orderBy('payment_number');
            },
            'distributions',
            'additionalAgreements',
            'creator',
            'updater'
        ]);

        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        $user = Auth::user();

        // Check access
        if ($user->role === 'district_user' && $contract->lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        // Check if contract has payment schedules or distributions
        $hasSchedules = $contract->paymentSchedules()->count() > 0;
        $hasDistributions = $contract->distributions()->count() > 0;

        if ($hasSchedules || $hasDistributions) {
            return redirect()->route('lots.show', $contract->lot_id)
                ->with('error', 'Тўлов графиги ёки тақсимоти бор шартномани таҳрирлаб бўлмайди. Фақат статусни ўзгартириш мумкин.');
        }

        $lot = $contract->lot;

        return view('contracts.edit', compact('contract', 'lot'));
    }
    public function update(Request $request, Contract $contract)
    {
        $user = Auth::user();

        // Check access
        if ($user->role === 'district_user' && $contract->lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        // Check if only updating status
        if ($request->has('status_only')) {
            $validated = $request->validate([
                'status' => 'required|in:active,completed,cancelled',
                'status_reason' => 'nullable|string|max:1000',
            ]);

            $contract->update([
                'status' => $validated['status'],
                'note' => $validated['status_reason'] ?
                    ($contract->note ? $contract->note . "\n\nСтатус ўзгартирилди: " . $validated['status_reason'] : $validated['status_reason'])
                    : $contract->note,
                'updated_by' => $user->id,
            ]);

            return redirect()->route('lots.show', $contract->lot_id)
                ->with('success', 'Шартнома статуси янгиланди');
        }

        // Full update - only if no payment schedules or distributions
        if ($contract->paymentSchedules()->count() > 0 || $contract->distributions()->count() > 0) {
            return redirect()->route('lots.show', $contract->lot_id)
                ->with('error', 'Тўлов графиги ёки тақсимоти бор шартномани таҳрирлаб бўлмайди');
        }
        $validated = $request->validate([
            'contract_number' => 'required|string|max:255',
            'contract_date' => 'required|date',
            'contract_amount' => 'required|numeric|min:0',
            'buyer_name' => 'required|string|max:255',
            'buyer_phone' => 'nullable|string|max:50',
            'buyer_inn' => 'nullable|string|max:50',
            'payment_type' => 'required|in:muddatli,muddatsiz',
            'status' => 'required|in:active,completed,cancelled',
            'note' => 'nullable|string|max:1000',
            'initial_paid_amount' => 'nullable|numeric|min:0',
            'initial_payment_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
            $validated['updated_by'] = $user->id;
            $contract->update($validated);

            // Update lot if needed
            $lot = $contract->lot;
            $lot->payment_type = $validated['payment_type'];
            $lot->save();

            DB::commit();

            Log::info('Contract updated successfully', [
                'contract_id' => $contract->id,
                'lot_id' => $lot->id,
                'updated_by' => $user->id
            ]);

            return redirect()->route('lots.show', $contract->lot_id)
                ->with('success', 'Шартнома муваффақиятли янгиланди');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Contract update error', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->with('error', 'Хатолик юз берди: ' . $e->getMessage());
        }
    }

    public function destroy(Contract $contract)
    {
        $user = Auth::user();

        // Only admin can delete
        if ($user->role !== 'admin') {
            abort(403, 'Рухсат йўқ');
        }

        // Check if contract has payment schedules or distributions
        $hasSchedules = $contract->paymentSchedules()->count() > 0;
        $hasDistributions = $contract->distributions()->count() > 0;

        if ($hasSchedules || $hasDistributions) {
            return redirect()->route('lots.show', $contract->lot_id)
                ->with('error', 'Тўлов графиги ёки тақсимоти бор шартномани ўчириб бўлмайди');
        }

        $lotId = $contract->lot_id;

        DB::beginTransaction();
        try {
            // Update lot
            $lot = Lot::find($lotId);
            if ($lot) {
                $lot->payment_type = null;
                $lot->paid_amount = 0;
                $lot->transferred_amount = 0;
                $lot->save();
                $lot->autoCalculate();
                $lot->save();
            }

            $contract->delete();

            DB::commit();

            Log::info('Contract deleted', [
                'contract_id' => $contract->id,
                'lot_id' => $lotId,
                'deleted_by' => $user->id
            ]);

            return redirect()->route('lots.show', $lotId)
                ->with('success', 'Шартнома муваффақиятли ўчирилди');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Contract deletion error', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('lots.show', $contract->lot_id)
                ->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function generateSchedule(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'frequency' => 'required|in:monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'number_of_payments' => 'required|integer|min:1|max:120',
        ]);

        if ($contract->paymentSchedules()->count() > 0) {
            return back()->with('error', 'График аллақачон мавжуд');
        }

        DB::beginTransaction();
        try {
            $startDate = Carbon::parse($validated['start_date']);
            $numberOfPayments = $validated['number_of_payments'];
            $remainingAmount = $contract->contract_amount - $contract->paid_amount;
            $paymentAmount = $remainingAmount / $numberOfPayments;

            $frequencyMap = [
                'monthly' => 1,
                'quarterly' => 3,
                'yearly' => 12,
            ];
            $monthsIncrement = $frequencyMap[$validated['frequency']];

            for ($i = 1; $i <= $numberOfPayments; $i++) {
                $plannedDate = $startDate->copy()->addMonths($monthsIncrement * ($i - 1));
                $deadlineDate = $plannedDate->copy()->addDays(10); // 10 days grace period

                PaymentSchedule::create([
                    'contract_id' => $contract->id,
                    'payment_number' => $i,
                    'planned_date' => $plannedDate,
                    'deadline_date' => $deadlineDate,
                    'planned_amount' => $paymentAmount,
                    'actual_amount' => 0,
                    'status' => PaymentSchedule::STATUS_PENDING,
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Тўлов графиги яратилди');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }
    public function rollback(Contract $contract)
    {
        $user = Auth::user();

        // Only admin can rollback
        if ($user->role !== 'admin') {
            abort(403, 'Рухсат йўқ');
        }

        // Check if contract has distributions
        if ($contract->distributions()->count() > 0) {
            return redirect()->route('lots.show', $contract->lot_id)
                ->with('error', 'Аввал тақсимотни ўчиринг! Тақсимот мавжуд бўлганда шартномани бекор қилиб бўлмайди.');
        }

        DB::beginTransaction();
        try {
            $lot = $contract->lot;

            // Delete all payment schedules
            $contract->paymentSchedules()->delete();

            // Delete all additional agreements
            $contract->additionalAgreements()->delete();

            // Delete the contract completely
            $contract->delete();

            // Reset lot fields
            $lot->payment_type = null;
            $lot->paid_amount = 0;
            $lot->transferred_amount = 0;
            $lot->discount = 0;
            $lot->auction_fee = 0;
            $lot->auction_expenses = 0;
            $lot->contract_signed = false;
            $lot->contract_date = null;
            $lot->contract_number = null;
            $lot->save();

            // Recalculate lot
            $lot->autoCalculate();
            $lot->save();

            DB::commit();

            Log::info('Contract rolled back and deleted', [
                'contract_id' => $contract->id,
                'lot_id' => $lot->id,
                'rolled_back_by' => $user->id
            ]);

            return redirect()->route('lots.show', $contract->lot_id)
                ->with('success', 'Шартнома тўлиқ ўчирилди. Энди янги шартнома яратишингиз мумкин.');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Contract rollback error', [
                'contract_id' => $contract->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('lots.show', $contract->lot_id)
                ->with('error', 'Хатолик юз берди: ' . $e->getMessage());
        }
    }
}
