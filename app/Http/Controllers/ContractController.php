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
            return redirect()->route('contracts.show', $lot->contract)
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
            'payment_type' => 'required|in:muddatli,muddatsiz',
            'paid_amount' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,completed,cancelled',
            'note' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Create contract
            $contract = Contract::create([
                'lot_id' => $validated['lot_id'],
                'contract_number' => $validated['contract_number'],
                'contract_date' => $validated['contract_date'],
                'contract_amount' => $validated['contract_amount'],
                'paid_amount' => $validated['paid_amount'] ?? 0,
                'payment_type' => $validated['payment_type'],
                'status' => $validated['status'],
                'note' => $validated['note'] ?? null,
            ]);

            // Update lot
            $lot = Lot::find($validated['lot_id']);
            $lot->contract_signed = true;
            $lot->contract_date = $validated['contract_date'];
            $lot->contract_number = $validated['contract_number'];
            $lot->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Шартнома муваффақиятли яратилди');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Хатолик: ' . $e->getMessage());
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

            return redirect()->route('lots.show', $lotId)
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

    public function addScheduleItem(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'planned_date' => 'required|date',
            'planned_amount' => 'required|numeric|min:0',
            'deadline_date' => 'nullable|date|after_or_equal:planned_date',
        ]);

        DB::beginTransaction();
        try {
            // Get next payment number
            $nextNumber = $contract->paymentSchedules()->max('payment_number') + 1;

            // Create schedule item
            PaymentSchedule::create([
                'contract_id' => $contract->id,
                'payment_number' => $nextNumber,
                'planned_date' => $validated['planned_date'],
                'deadline_date' => $validated['deadline_date'] ?? Carbon::parse($validated['planned_date'])->addDays(10),
                'planned_amount' => $validated['planned_amount'],
                'actual_amount' => 0,
                'difference' => -$validated['planned_amount'],
                'status' => PaymentSchedule::STATUS_PENDING,
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'График қўшилди');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }
}
