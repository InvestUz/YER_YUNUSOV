<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\PaymentSchedule;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    public function create(Request $request)
    {
        $paymentScheduleId = $request->get('payment_schedule_id');
        $paymentSchedule = PaymentSchedule::with('contract')->findOrFail($paymentScheduleId);

        $categories = Distribution::categories();

        $existingDistributions = Distribution::where('payment_schedule_id', $paymentScheduleId)->get();
        $totalDistributed = $existingDistributions->sum('allocated_amount');
        $remainingAmount = $paymentSchedule->actual_amount - $totalDistributed;

        return view('distributions.create', compact(
            'paymentSchedule',
            'categories',
            'existingDistributions',
            'totalDistributed',
            'remainingAmount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_schedule_id' => 'required|exists:payment_schedules,id',
            'distributions' => 'required|array|min:1',
            'distributions.*.category' => 'required|string',
            'distributions.*.allocated_amount' => 'required|numeric|min:0',
            'distributions.*.distribution_date' => 'required|date',
            'distributions.*.note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $paymentSchedule = PaymentSchedule::findOrFail($validated['payment_schedule_id']);

            $existingTotal = Distribution::where('payment_schedule_id', $paymentSchedule->id)
                ->sum('allocated_amount');

            $newTotal = collect($validated['distributions'])->sum('allocated_amount');
            $grandTotal = $existingTotal + $newTotal;

            if ($grandTotal > $paymentSchedule->actual_amount) {
                return back()->with('error', "Тақсимланаётган сумма ({$grandTotal}) тўловдан ({$paymentSchedule->actual_amount}) кўп!");
            }

            foreach ($validated['distributions'] as $dist) {
                Distribution::create([
                    'contract_id' => $paymentSchedule->contract_id,
                    'payment_schedule_id' => $paymentSchedule->id,
                    'additional_agreement_id' => $paymentSchedule->additional_agreement_id,
                    'category' => $dist['category'],
                    'allocated_amount' => $dist['allocated_amount'],
                    'distribution_date' => $dist['distribution_date'],
                    'status' => 'distributed',
                    'note' => $dist['note'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()
                ->route('contracts.show', $paymentSchedule->contract_id)
                ->with('success', 'Тақсимот муваффақиятли сақланди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function edit(Distribution $distribution)
    {
        $categories = Distribution::categories();
        return view('distributions.edit', compact('distribution', 'categories'));
    }

    public function update(Request $request, Distribution $distribution)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'allocated_amount' => 'required|numeric|min:0',
            'distribution_date' => 'required|date',
            'status' => 'required|in:pending,distributed,cancelled',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $paymentSchedule = $distribution->paymentSchedule;

            $existingTotal = Distribution::where('payment_schedule_id', $paymentSchedule->id)
                ->where('id', '!=', $distribution->id)
                ->sum('allocated_amount');

            $newTotal = $existingTotal + $validated['allocated_amount'];

            if ($newTotal > $paymentSchedule->actual_amount) {
                return back()->with('error', "Тақсимланаётган сумма ({$newTotal}) тўловдан ({$paymentSchedule->actual_amount}) кўп!");
            }

            $distribution->update($validated);

            DB::commit();
            return back()->with('success', 'Тақсимот янгиланди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function destroy(Distribution $distribution)
    {
        $distribution->delete();
        return back()->with('success', 'Тақсимот ўчирилди!');
    }
}
