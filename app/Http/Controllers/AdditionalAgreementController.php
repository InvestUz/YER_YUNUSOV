<?php

namespace App\Http\Controllers;

use App\Models\AdditionalAgreement;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdditionalAgreementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Contract $contract)
    {
        // Auto-fill agreement number
        $agreementCount = $contract->additionalAgreements()->count();
        $suggestedNumber = $contract->contract_number . '-ҚК-' . ($agreementCount + 1);

        return view('additional-agreements.create', compact('contract', 'suggestedNumber'));
    }

    public function store(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'agreement_number' => 'required|string|max:255|unique:additional_agreements,agreement_number',
            'agreement_date' => 'required|date',
            'new_amount' => 'required|numeric',
            'reason' => 'required|string|max:1000',
            'note' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Create additional agreement
            $agreement = AdditionalAgreement::create([
                'contract_id' => $contract->id,
                'agreement_number' => $validated['agreement_number'],
                'agreement_date' => $validated['agreement_date'],
                'new_amount' => $validated['new_amount'],
                'reason' => $validated['reason'],
                'note' => $validated['note'] ?? null,
            ]);

            // Update contract amount
            $contract->contract_amount += $validated['new_amount'];
            $contract->remaining_amount = $contract->contract_amount - $contract->paid_amount;
            $contract->save();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Қўшимча келишув яратилди');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function show(AdditionalAgreement $agreement)
    {
        $agreement->load(['contract.lot', 'paymentSchedules', 'distributions', 'creator', 'updater']);

        return view('additional-agreements.show', compact('agreement'));
    }

    public function destroy(AdditionalAgreement $agreement)
    {
        DB::beginTransaction();
        try {
            $contract = $agreement->contract;
            $amount = $agreement->new_amount;

            $contract->update([
                'contract_amount' => $contract->contract_amount - $amount
            ]);

            $agreement->delete();

            DB::commit();
            return redirect()
                ->route('contracts.show', $contract)
                ->with('success', 'Қўшимча келишув ўчирилди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }
}
