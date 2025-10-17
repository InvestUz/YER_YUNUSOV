<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Lot;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $validated = $request->validate([
            'lot_id' => 'required|exists:lots,id',
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number',
            'contract_date' => 'required|date',
            'contract_amount' => 'required|numeric|min:0',
            'buyer_name' => 'required|string|max:255',
            'buyer_phone' => 'nullable|string|max:50',
            'buyer_inn' => 'nullable|string|max:50',
            'payment_type' => 'required|in:muddatli,muddatsiz',
            'status' => 'required|in:active,completed,cancelled',
            'note' => 'nullable|string|max:1000',

            // REMOVED: schedule_frequency, first_payment_date, number_of_payments
            // Payment schedule will be added manually later

            // For muddatsiz payment only
            'one_time_payment_amount' => 'required_if:payment_type,muddatsiz|numeric|min:0',
            'one_time_payment_date' => 'required_if:payment_type,muddatsiz|date',
        ]);

        DB::beginTransaction();
        try {
            // Create contract
            $contract = Contract::create([
                'lot_id' => $validated['lot_id'],
                'contract_number' => $validated['contract_number'],
                'contract_date' => $validated['contract_date'],
                'contract_amount' => $validated['contract_amount'],
                'payment_type' => $validated['payment_type'],
                'status' => $validated['status'],
                'note' => $validated['note'] ?? null,
                'paid_amount' => 0,
            ]);

            // Handle payment schedule based on type
            if ($validated['payment_type'] === 'muddatli') {
                // Muddatli - NO automatic schedule creation
                // User will manually add payment schedule rows using "+ Қўшиш" button
                // DO NOTHING HERE - schedule will be added via addScheduleItem method
            } else {
                // Muddatsiz - Create single payment record
                PaymentSchedule::create([
                    'contract_id' => $contract->id,
                    'payment_number' => 1,
                    'planned_date' => $validated['one_time_payment_date'],
                    'deadline_date' => $validated['one_time_payment_date'],
                    'planned_amount' => $validated['one_time_payment_amount'],
                    'actual_amount' => 0,
                    'status' => PaymentSchedule::STATUS_PENDING,
                ]);
            }

            // Update lot
            $lot = Lot::find($validated['lot_id']);
            $lot->contract_signed = true;
            $lot->contract_date = $validated['contract_date'];
            $lot->contract_number = $validated['contract_number'];
            $lot->payment_type = $validated['payment_type'];
            $lot->winner_name = $validated['buyer_name'];
            $lot->winner_phone = $validated['buyer_phone'];
            $lot->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Шартнома муваффақиятли яратилди. Муддатли тўлов учун "+ Қўшиш" тугмаси орқали график қўшинг.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    // Method to manually add schedule items (should already exist)
    public function addScheduleItem(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'planned_date' => 'required|date',
            'planned_amount' => 'required|numeric|min:0',
            'deadline_date' => 'nullable|date',
        ]);

        DB::beginTransaction();
        try {
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

            return redirect()->back()
                ->with('success', 'График қатори муваффақиятли қўшилди');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    private function createPaymentSchedule(Contract $contract, array $params)
    {
        $startDate = Carbon::parse($params['start_date']);
        $numberOfPayments = $params['number_of_payments'];
        $paymentAmount = $contract->contract_amount / $numberOfPayments;

        $frequencyMap = [
            'monthly' => 1,
            'quarterly' => 3,
            'yearly' => 12,
        ];
        $monthsIncrement = $frequencyMap[$params['frequency']];

        for ($i = 1; $i <= $numberOfPayments; $i++) {
            $plannedDate = $startDate->copy()->addMonths($monthsIncrement * ($i - 1));
            $deadlineDate = $plannedDate->copy()->addDays(10); // Grace period

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
    }
    public function recordPayment(Request $request, PaymentSchedule $schedule)
    {
        $validated = $request->validate([
            'actual_date' => 'required|date',
            'actual_amount' => 'required|numeric|min:0',
            'payment_method' => 'nullable|string|max:100',
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
        // Check if contract has payment schedules
        if ($contract->paymentSchedules()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Тўлов графиги бор шартномани таҳрирлаб бўлмайди. Фақат статусни ўзгартириш мумкин.');
        }

        $lot = $contract->lot;

        return view('contracts.edit', compact('contract', 'lot'));
    }

    public function update(Request $request, Contract $contract)
    {
        // Check if only updating status
        if ($request->has('status_only')) {
            $validated = $request->validate([
                'status' => 'required|in:draft,active,completed,cancelled',
                'status_reason' => 'nullable|string|max:1000',
            ]);

            $contract->update([
                'status' => $validated['status'],
                'note' => $validated['status_reason'] ?
                    ($contract->note ? $contract->note . "\n\nСтатус ўзгартирилди: " . $validated['status_reason'] : $validated['status_reason'])
                    : $contract->note,
            ]);

            return redirect()->back()
                ->with('success', 'Шартнома статуси янгиланди');
        }

        // Full update - only if no payment schedules
        if ($contract->paymentSchedules()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Тўлов графиги бор шартномани таҳрирлаб бўлмайди');
        }

        $validated = $request->validate([
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number,' . $contract->id,
            'contract_date' => 'required|date',
            'contract_amount' => 'required|numeric|min:0',
            'payment_type' => 'required|in:muddatli,muddatsiz',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,cancelled',
            'note' => 'nullable|string|max:1000',
        ]);

        $contract->update($validated);

        // Update lot
        $contract->lot->update([
            'contract_number' => $validated['contract_number'],
            'contract_date' => $validated['contract_date'],
        ]);

        return redirect()->back()
            ->with('success', 'Шартнома янгиланди');
    }

    public function destroy(Contract $contract)
    {
        $user = Auth::user();

        if ($user->role !== 'admin') {
            abort(403, 'Рухсат йўқ');
        }

        if ($contract->paymentSchedules()->count() > 0 || $contract->distributions()->count() > 0) {
            return back()->with('error', 'Тўлов графиги ёки тақсимоти бор шартномани ўчириб бўлмайди');
        }

        $lotId = $contract->lot_id;

        DB::beginTransaction();
        try {
            // Update lot
            $lot = Lot::find($lotId);
            $lot->contract_signed = false;
            $lot->contract_date = null;
            $lot->contract_number = null;
            $lot->save();

            $contract->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Шартнома ўчирилди');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
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
}
