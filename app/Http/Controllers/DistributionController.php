<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Request $request)
    {
        $paymentScheduleId = $request->get('payment_schedule_id');

        if (!$paymentScheduleId) {
            return redirect()->back()
                ->with('error', 'Тўлов танланмаган');
        }

        $paymentSchedule = PaymentSchedule::with('contract.lot')->findOrFail($paymentScheduleId);

        // Check if payment has amount
        if ($paymentSchedule->actual_amount <= 0) {
            return redirect()->back()
                ->with('error', 'Тўлов суммаси киритилмаган');
        }

        // Get existing distributions
        $existingDistributions = Distribution::where('payment_schedule_id', $paymentScheduleId)->get();
        $totalDistributed = $existingDistributions->sum('allocated_amount');
        $remainingAmount = $paymentSchedule->actual_amount - $totalDistributed;

        // Distribution categories - Updated to use new constants
        $categories = Distribution::getCategories();

        return view('distributions.create', compact(
            'paymentSchedule',
            'existingDistributions',
            'totalDistributed',
            'remainingAmount',
            'categories'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'payment_schedule_id' => 'required|exists:payment_schedules,id',
            'distributions' => 'required|array|min:1',
            'distributions.*.category' => 'required|in:' . implode(',', array_keys(Distribution::getCategories())),
            'distributions.*.allocated_amount' => 'required|numeric|min:0',
            'distributions.*.distribution_date' => 'required|date',
            'distributions.*.note' => 'nullable|string|max:500',
        ]);

        $paymentSchedule = PaymentSchedule::findOrFail($validated['payment_schedule_id']);

        // Check remaining amount
        $existingTotal = Distribution::where('payment_schedule_id', $paymentSchedule->id)
            ->sum('allocated_amount');
        $newTotal = collect($validated['distributions'])->sum('allocated_amount');
        $grandTotal = $existingTotal + $newTotal;

        if ($grandTotal > $paymentSchedule->actual_amount) {
            return back()->withInput()
                ->with('error', 'Тақсимот суммаси тўлов суммасидан ошиб кетди');
        }

        DB::beginTransaction();
        try {
            foreach ($validated['distributions'] as $dist) {
                Distribution::create([
                    'contract_id' => $paymentSchedule->contract_id,
                    'payment_schedule_id' => $paymentSchedule->id,
                    'category' => $dist['category'],
                    'allocated_amount' => $dist['allocated_amount'],
                    'distribution_date' => $dist['distribution_date'],
                    'note' => $dist['note'] ?? null,
                    'status' => Distribution::STATUS_PENDING,
                ]);
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Тақсимот муваффақиятли қўшилди');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function edit(Distribution $distribution)
    {
        $paymentSchedule = $distribution->paymentSchedule;

        // Calculate available amount
        $totalDistributed = Distribution::where('payment_schedule_id', $distribution->payment_schedule_id)
            ->where('id', '!=', $distribution->id)
            ->sum('allocated_amount');
        $availableAmount = $paymentSchedule->actual_amount - $totalDistributed;

        // Get all categories
        $categories = Distribution::getCategories();

        return view('distributions.edit', compact('distribution', 'availableAmount', 'categories'));
    }

    public function update(Request $request, Distribution $distribution)
    {
        $validated = $request->validate([
            'category' => 'required|in:' . implode(',', array_keys(Distribution::getCategories())),
            'allocated_amount' => 'required|numeric|min:0',
            'distribution_date' => 'required|date',
            'status' => 'required|in:' . implode(',', [
                Distribution::STATUS_PENDING,
                Distribution::STATUS_DISTRIBUTED,
                Distribution::STATUS_CANCELLED,
            ]),
            'note' => 'nullable|string|max:500',
        ]);

        // Validate amount doesn't exceed payment
        $paymentSchedule = $distribution->paymentSchedule;
        $otherDistributions = Distribution::where('payment_schedule_id', $distribution->payment_schedule_id)
            ->where('id', '!=', $distribution->id)
            ->sum('allocated_amount');

        if (($otherDistributions + $validated['allocated_amount']) > $paymentSchedule->actual_amount) {
            return back()->withInput()
                ->with('error', 'Тақсимот суммаси тўлов суммасидан ошиб кетди');
        }

        $distribution->update($validated);

        return redirect()->back()
            ->with('success', 'Тақсимот янгиланди');
    }

    public function destroy(Distribution $distribution)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Рухсат йўқ');
        }

        $contractId = $distribution->contract_id;
        $distribution->delete();

        return redirect()->back()
            ->with('success', 'Тақсимот ўчирилди');
    }
}
