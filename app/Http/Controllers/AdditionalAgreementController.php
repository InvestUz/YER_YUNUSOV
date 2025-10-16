<?php

namespace App\Http\Controllers;

use App\Models\AdditionalAgreement;
use App\Models\Contract;
use App\Models\PaymentSchedule;
use Carbon\Carbon;
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
            'agreement_number' => 'required|string|max:255',
            'agreement_date' => 'required|date',
            'change_type' => 'required|in:increase,decrease',
            'new_amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|max:1000',
            'note' => 'nullable|string|max:1000',
            'generate_schedule' => 'nullable|boolean',
            'frequency' => 'required_if:generate_schedule,1|in:monthly,quarterly,yearly',
            'start_date' => 'required_if:generate_schedule,1|date',
            'number_of_payments' => 'required_if:generate_schedule,1|integer|min:1|max:120',
        ]);

        try {
            DB::beginTransaction();

            // Calculate the actual amount based on change type
            $changeAmount = $validated['new_amount'];
            if ($validated['change_type'] === 'decrease') {
                $changeAmount = -$changeAmount; // Make it negative for decrease
            }

            // Validate that decrease doesn't make contract amount negative
            $newContractAmount = $contract->contract_amount + $changeAmount;
            if ($newContractAmount < 0) {
                throw new \Exception('Камайтириш суммаси жуда катта! Шартнома суммаси манфий бўла олмайди.');
            }

            // Create additional agreement
            $agreement = AdditionalAgreement::create([
                'contract_id' => $contract->id,
                'agreement_number' => $validated['agreement_number'],
                'agreement_date' => $validated['agreement_date'],
                'new_amount' => $changeAmount, // Store with sign (+ or -)
                'reason' => $validated['reason'],
                'note' => $validated['note'],
            ]);

            // Update contract amount
            $contract->contract_amount = $newContractAmount;
            $contract->save();

            // Generate payment schedule if requested
            if ($request->generate_schedule && $contract->isMuddatli()) {
                $this->generateScheduleForAgreement(
                    $agreement,
                    $validated['frequency'],
                    $validated['start_date'],
                    $validated['number_of_payments']
                );
            }

            DB::commit();

            return redirect()
                ->route('contracts.show', $contract)
                ->with('success', 'Қўшимча келишув муваффақиятли яратилди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    private function generateScheduleForAgreement($agreement, $frequency, $startDate, $numberOfPayments)
    {
        $amount = $agreement->new_amount;
        if ($amount <= 0) {
            return; // Don't generate schedule for decrease
        }

        $perPayment = $amount / $numberOfPayments;
        $currentDate = Carbon::parse($startDate);

        for ($i = 1; $i <= $numberOfPayments; $i++) {
            $deadlineDate = clone $currentDate;

            switch ($frequency) {
                case 'monthly':
                    $deadlineDate->addMonth();
                    break;
                case 'quarterly':
                    $deadlineDate->addMonths(3);
                    break;
                case 'yearly':
                    $deadlineDate->addYear();
                    break;
            }

            PaymentSchedule::create([
                'contract_id' => $agreement->contract_id,
                'additional_agreement_id' => $agreement->id,
                'payment_number' => $i,
                'planned_date' => $currentDate->format('Y-m-d'),
                'deadline_date' => $deadlineDate->format('Y-m-d'),
                'planned_amount' => round($perPayment, 2),
                'actual_amount' => 0,
                'status' => 'pending',
            ]);

            switch ($frequency) {
                case 'monthly':
                    $currentDate->addMonth();
                    break;
                case 'quarterly':
                    $currentDate->addMonths(3);
                    break;
                case 'yearly':
                    $currentDate->addYear();
                    break;
            }
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
