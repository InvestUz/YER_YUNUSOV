<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\PaymentSchedule;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        $paymentSchedule = PaymentSchedule::with(['contract.lot.tuman', 'contract.lot.mahalla'])->findOrFail($paymentScheduleId);
        $lot = $paymentSchedule->contract->lot;

        // Check if payment has amount
        if ($paymentSchedule->actual_amount <= 0) {
            return redirect()->back()
                ->with('error', 'Тўлов суммаси киритилмаган');
        }

        // ✅ CRITICAL: Use distributable_amount from lot (handles discount automatically)
        $distributableAmount = $lot->distributable_amount;

        // Get existing distributions for this payment schedule
        $existingDistributions = Distribution::where('payment_schedule_id', $paymentScheduleId)->get();
        $totalDistributed = $existingDistributions->sum('allocated_amount');
        $remainingAmount = $distributableAmount - $totalDistributed;

        // Distribution categories
        $categories = Distribution::getCategories();

        // Discount information
        $discountInfo = [
            'qualifies' => $lot->qualifiesForDiscount(),
            'paid_amount' => $lot->paid_amount,
            'discount' => $lot->discount,
            'incoming_amount' => $lot->incoming_amount,
            'distributable_amount' => $distributableAmount,
        ];

        Log::info('Distribution create view', [
            'lot_id' => $lot->id,
            'payment_schedule_id' => $paymentScheduleId,
            'paid_amount' => $lot->paid_amount,
            'discount' => $lot->discount,
            'distributable_amount' => $distributableAmount,
            'already_distributed' => $totalDistributed,
            'remaining' => $remainingAmount,
        ]);

        return view('distributions.create', compact(
            'paymentSchedule',
            'lot',
            'existingDistributions',
            'totalDistributed',
            'remainingAmount',
            'distributableAmount',
            'categories',
            'discountInfo'
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

        $paymentSchedule = PaymentSchedule::with('contract.lot')->findOrFail($validated['payment_schedule_id']);
        $lot = $paymentSchedule->contract->lot;

        // ✅ CRITICAL: Use distributable_amount from lot (handles discount)
        $distributableAmount = $lot->distributable_amount;

        // Check remaining amount
        $existingTotal = Distribution::where('payment_schedule_id', $paymentSchedule->id)
            ->sum('allocated_amount');
        $newTotal = collect($validated['distributions'])->sum('allocated_amount');
        $grandTotal = $existingTotal + $newTotal;

        Log::info('Distribution validation', [
            'distributable_amount' => $distributableAmount,
            'existing_total' => $existingTotal,
            'new_total' => $newTotal,
            'grand_total' => $grandTotal,
        ]);

        // Validate against distributable amount (not paid amount)
        if ($grandTotal > $distributableAmount) {
            return back()->withInput()
                ->with('error', sprintf(
                    'Тақсимот суммаси (%s сўм) тақсимланадиган суммадан (%s сўм) ошиб кетди',
                    number_format($grandTotal, 0, '.', ' '),
                    number_format($distributableAmount, 0, '.', ' ')
                ));
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

            Log::info('Distributions created successfully', [
                'payment_schedule_id' => $paymentSchedule->id,
                'count' => count($validated['distributions']),
                'total_amount' => $newTotal,
            ]);

            DB::commit();

            return redirect()->route('lots.show', $lot)
                ->with('success', 'Тақсимот муваффақиятли қўшилди');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Distribution creation error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()
                ->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function edit(Distribution $distribution)
    {
        $paymentSchedule = $distribution->paymentSchedule;
        $lot = $paymentSchedule->contract->lot;

        // ✅ Use distributable amount from lot
        $distributableAmount = $lot->distributable_amount;

        // Calculate available amount
        $totalDistributed = Distribution::where('payment_schedule_id', $distribution->payment_schedule_id)
            ->where('id', '!=', $distribution->id)
            ->sum('allocated_amount');
        $availableAmount = $distributableAmount - $totalDistributed;

        // Get all categories
        $categories = Distribution::getCategories();

        return view('distributions.edit', compact(
            'distribution',
            'availableAmount',
            'distributableAmount',
            'lot',
            'categories'
        ));
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

        $paymentSchedule = $distribution->paymentSchedule;
        $lot = $paymentSchedule->contract->lot;

        // ✅ Use distributable amount from lot
        $distributableAmount = $lot->distributable_amount;

        // Validate amount doesn't exceed distributable amount
        $otherDistributions = Distribution::where('payment_schedule_id', $distribution->payment_schedule_id)
            ->where('id', '!=', $distribution->id)
            ->sum('allocated_amount');

        if (($otherDistributions + $validated['allocated_amount']) > $distributableAmount) {
            return back()->withInput()
                ->with('error', sprintf(
                    'Тақсимот суммаси тақсимланадиган суммадан (%s сўм) ошиб кетди',
                    number_format($distributableAmount, 0, '.', ' ')
                ));
        }

        $distribution->update($validated);

        Log::info('Distribution updated', [
            'distribution_id' => $distribution->id,
            'allocated_amount' => $validated['allocated_amount'],
        ]);

        return redirect()->route('lots.show', $lot)
            ->with('success', 'Тақсимот янгиланди');
    }

    public function destroy(Distribution $distribution)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Рухсат йўқ');
        }

        $lot = $distribution->paymentSchedule->contract->lot;
        $distribution->delete();

        Log::info('Distribution deleted', [
            'distribution_id' => $distribution->id,
        ]);

        return redirect()->route('lots.show', $lot)
            ->with('success', 'Тақсимот ўчирилди');
    }
}
