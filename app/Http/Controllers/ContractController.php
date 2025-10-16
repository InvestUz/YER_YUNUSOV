<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with(['lot', 'creator', 'updater'])
            ->latest()
            ->paginate(20);

        return view('contracts.index', compact('contracts'));
    }

    public function create(Request $request)
    {
        $lotId = $request->get('lot_id');
        $lot = null;

        if ($lotId) {
            $lot = Lot::findOrFail($lotId);
        }

        $lots = Lot::select('id', 'lot_number', 'address')
            ->orderBy('lot_number')
            ->get();

        return view('contracts.create', compact('lots', 'lot'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lot_id' => 'required|exists:lots,id',
            'contract_number' => 'required|unique:contracts,contract_number',
            'contract_date' => 'required|date',
            'payment_type' => 'required|in:muddatli,muddatsiz',
            'contract_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $contract = Contract::create($validated);

        return redirect()
            ->route('contracts.show', $contract)
            ->with('success', 'Шартнома муваффақиятли яратилди!');
    }

    public function show(Contract $contract)
    {
        $contract->load([
            'lot',
            'paymentSchedules' => fn($q) => $q->orderBy('payment_number'),
            'additionalAgreements' => fn($q) => $q->latest(),
            'distributions' => fn($q) => $q->with(['paymentSchedule', 'creator'])->latest(),
            'creator',
            'updater'
        ]);

        return view('contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        return view('contracts.edit', compact('contract'));
    }

    public function update(Request $request, Contract $contract)
    {
        // Check if this is a status-only update
        if ($request->has('status_only') && $request->status_only == '1') {
            // Only validate and update status
            $validated = $request->validate([
                'status' => 'required|in:draft,active,completed,cancelled',
                'status_reason' => 'nullable|string|max:1000',
            ]);

            try {
                DB::beginTransaction();

                $contract->status = $validated['status'];

                // If there's a status reason, append it to notes
                if (!empty($validated['status_reason'])) {
                    $timestamp = now()->format('d.m.Y H:i');
                    $user = auth()->user()->name;
                    $statusLabel = $contract->status_label;

                    $statusNote = "\n\n[{$timestamp}] {$user} статусни '{$statusLabel}' га ўзгартирди.\nСабаби: {$validated['status_reason']}";
                    $contract->note = ($contract->note ?? '') . $statusNote;
                }

                $contract->save();

                DB::commit();

                return redirect()
                    ->route('contracts.show', $contract)
                    ->with('success', 'Шартнома статуси муваффақиятли янгиланди!');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with('error', 'Статусни янгилашда хатолик: ' . $e->getMessage());
            }
        }

        // Check if contract has payment schedules - prevent full edit if it does
        if ($contract->paymentSchedules()->count() > 0) {
            return redirect()
                ->back()
                ->with('error', 'Тўлов графиги мавжуд бўлган шартномани таҳрирлаш мумкин эмас. Фақат статусни ўзгартириш мумкин.');
        }

        // Regular full update validation
        $validated = $request->validate([
            'contract_number' => 'required|string|max:255|unique:contracts,contract_number,' . $contract->id,
            'contract_date' => 'required|date',
            'payment_type' => 'required|in:bir_martalik,muddatli',
            'contract_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,completed,cancelled',
            'note' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $contract->update($validated);

            DB::commit();

            return redirect()
                ->route('contracts.show', $contract)
                ->with('success', 'Шартнома муваффақиятли янгиланди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Янгилашда хатолик: ' . $e->getMessage());
        }
    }

    public function destroy(Contract $contract)
    {
        $contract->delete();

        return redirect()
            ->route('contracts.index')
            ->with('success', 'Шартнома ўчирилди!');
    }

    public function generateSchedule(Request $request, Contract $contract)
    {
        if (!$contract->isMuddatli()) {
            return back()->with('error', 'Фақат муддатли шартномалар учун график яратилади!');
        }

        $validated = $request->validate([
            'frequency' => 'required|in:monthly,quarterly,yearly',
            'start_date' => 'required|date',
            'number_of_payments' => 'required|integer|min:1|max:120',
        ]);

        DB::beginTransaction();
        try {
            $contract->paymentSchedules()->delete();

            $amount = $contract->contract_amount;
            $numberOfPayments = $validated['number_of_payments'];
            $paymentAmount = $amount / $numberOfPayments;
            $startDate = \Carbon\Carbon::parse($validated['start_date']);

            for ($i = 1; $i <= $numberOfPayments; $i++) {
                $plannedDate = clone $startDate;

                switch ($validated['frequency']) {
                    case 'monthly':
                        $plannedDate->addMonths($i - 1);
                        $deadlineDate = (clone $plannedDate)->endOfMonth();
                        break;
                    case 'quarterly':
                        $plannedDate->addMonths(($i - 1) * 3);
                        $deadlineDate = (clone $plannedDate)->addMonths(3)->endOfMonth();
                        break;
                    case 'yearly':
                        $plannedDate->addYears($i - 1);
                        $deadlineDate = (clone $plannedDate)->endOfYear();
                        break;
                }

                $contract->paymentSchedules()->create([
                    'payment_number' => $i,
                    'planned_date' => $plannedDate,
                    'deadline_date' => $deadlineDate,
                    'planned_amount' => $paymentAmount,
                    'status' => 'pending',
                ]);
            }

            DB::commit();
            return back()->with('success', 'Тўлов графиги яратилди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }
}
