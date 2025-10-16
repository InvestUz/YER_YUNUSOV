<?php

namespace App\Http\Controllers;

use App\Models\Distribution;
use App\Models\PaymentSchedule;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DistributionController extends Controller
{
    public function index(Request $request)
    {
        $query = Distribution::with(['contract', 'paymentSchedule', 'creator', 'updater']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by contract
        if ($request->filled('contract_id')) {
            $query->where('contract_id', $request->contract_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('distribution_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('distribution_date', '<=', $request->date_to);
        }

        $distributions = $query->latest()->paginate(20);

        $categories = Distribution::categories();
        $contracts = Contract::select('id', 'contract_number')->get();

        return view('distributions.index', compact('distributions', 'categories', 'contracts'));
    }


    public function create(Request $request)
    {
        $paymentScheduleId = $request->get('payment_schedule_id');

        // If no payment_schedule_id, show selection page or redirect to contracts
        if (!$paymentScheduleId) {
            return redirect()->route('contracts.index')
                ->with('error', 'Илтимос, аввал шартномани танланг ва тўлов жадвалини кўрсатинг');
        }

        try {
            $paymentSchedule = PaymentSchedule::with('contract')->findOrFail($paymentScheduleId);
        } catch (\Exception $e) {
            return redirect()->route('contracts.index')
                ->with('error', 'Тўлов жадвали топилмади');
        }

        $categories = Distribution::categories();

        $existingDistributions = Distribution::where('payment_schedule_id', $paymentScheduleId)
            ->with(['creator', 'updater'])
            ->get();

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
            'distributions.*.category' => 'required|in:' . implode(',', array_keys(Distribution::categories())),
            'distributions.*.allocated_amount' => 'required|numeric|min:0.01',
            'distributions.*.distribution_date' => 'required|date',
            'distributions.*.note' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $paymentSchedule = PaymentSchedule::findOrFail($validated['payment_schedule_id']);

            // Calculate existing + new distributions
            $existingTotal = Distribution::where('payment_schedule_id', $paymentSchedule->id)
                ->sum('allocated_amount');
            $newTotal = collect($validated['distributions'])->sum('allocated_amount');
            $grandTotal = $existingTotal + $newTotal;

            // Validate total doesn't exceed payment amount
            if ($grandTotal > $paymentSchedule->actual_amount) {
                throw new \Exception("Тақсимланаётган умумий сумма ({$grandTotal}) тўлов суммасидан ({$paymentSchedule->actual_amount}) ошиб кетди!");
            }

            // Create distributions
            foreach ($validated['distributions'] as $distributionData) {
                Distribution::create([
                    'contract_id' => $paymentSchedule->contract_id,
                    'payment_schedule_id' => $paymentSchedule->id,
                    'additional_agreement_id' => $paymentSchedule->additional_agreement_id,
                    'category' => $distributionData['category'],
                    'allocated_amount' => $distributionData['allocated_amount'],
                    'distribution_date' => $distributionData['distribution_date'],
                    'status' => Distribution::STATUS_PENDING,
                    'note' => $distributionData['note'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('contracts.show', $paymentSchedule->contract_id)
                ->with('success', 'Тақсимлаш муваффақиятли яратилди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
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
            'category' => 'required|in:' . implode(',', array_keys(Distribution::categories())),
            'allocated_amount' => 'required|numeric|min:0.01',
            'distribution_date' => 'required|date',
            'status' => 'required|in:' . implode(',', [
                Distribution::STATUS_PENDING,
                Distribution::STATUS_DISTRIBUTED,
                Distribution::STATUS_CANCELLED
            ]),
            'note' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            // Validate total amount if amount changed
            if ($distribution->allocated_amount != $validated['allocated_amount']) {
                $existingTotal = Distribution::where('payment_schedule_id', $distribution->payment_schedule_id)
                    ->where('id', '!=', $distribution->id)
                    ->sum('allocated_amount');

                $newTotal = $existingTotal + $validated['allocated_amount'];

                if ($newTotal > $distribution->paymentSchedule->actual_amount) {
                    throw new \Exception("Тақсимланаётган умумий сумма тўлов суммасидан ошиб кетди!");
                }
            }

            $distribution->update($validated);

            DB::commit();

            return redirect()
                ->route('contracts.show', $distribution->contract_id)
                ->with('success', 'Тақсимлаш муваффақиятли янгиланди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Distribution $distribution)
    {
        try {
            $contractId = $distribution->contract_id;
            $distribution->delete();

            return redirect()
                ->route('contracts.show', $contractId)
                ->with('success', 'Тақсимлаш муваффақиятли ўчирилди!');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Ўчиришда хатолик: ' . $e->getMessage());
        }
    }
}
