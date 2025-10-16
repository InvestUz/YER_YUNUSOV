<?php

namespace App\Http\Controllers;

use App\Models\AdditionalAgreement;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdditionalAgreementController extends Controller
{
    public function create(Contract $contract)
    {
        return view('additional_agreements.create', compact('contract'));
    }

    public function store(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'agreement_number' => 'required|string',
            'agreement_date' => 'required|date',
            'new_amount' => 'required|numeric|min:0',
            'reason' => 'required|string',
            'note' => 'nullable|string',
            'generate_schedule' => 'nullable|boolean',
            'frequency' => 'required_if:generate_schedule,1|in:monthly,quarterly,yearly',
            'start_date' => 'required_if:generate_schedule,1|date',
            'number_of_payments' => 'required_if:generate_schedule,1|integer|min:1|max:120',
        ]);

        DB::beginTransaction();
        try {
            $agreement = $contract->additionalAgreements()->create([
                'agreement_number' => $validated['agreement_number'],
                'agreement_date' => $validated['agreement_date'],
                'new_amount' => $validated['new_amount'],
                'reason' => $validated['reason'],
                'note' => $validated['note'] ?? null,
            ]);

            if ($request->generate_schedule && $contract->isMuddatli()) {
                $amount = $validated['new_amount'];
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

                    $agreement->paymentSchedules()->create([
                        'contract_id' => $contract->id,
                        'payment_number' => $i,
                        'planned_date' => $plannedDate,
                        'deadline_date' => $deadlineDate,
                        'planned_amount' => $paymentAmount,
                        'status' => 'pending',
                    ]);
                }
            }

            $contract->update([
                'contract_amount' => $contract->contract_amount + $validated['new_amount']
            ]);

            DB::commit();
            return redirect()
                ->route('contracts.show', $contract)
                ->with('success', 'Қўшимча келишув яратилди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function show(AdditionalAgreement $agreement)
    {
        $agreement->load([
            'contract.lot',
            'paymentSchedules',
            'distributions',
            'creator',
            'updater'
        ]);

        return view('additional_agreements.show', compact('agreement'));
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
