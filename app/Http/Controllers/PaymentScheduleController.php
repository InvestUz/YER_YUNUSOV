<?php

namespace App\Http\Controllers;

use App\Models\PaymentSchedule;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentScheduleController extends Controller
{
    public function update(Request $request, PaymentSchedule $schedule)
    {
        $validated = $request->validate([
            'actual_date' => 'required|date',
            'actual_amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $oldAmount = $schedule->actual_amount;
            $newAmount = $validated['actual_amount'];
            $difference = $newAmount - $oldAmount;

            $schedule->update($validated);

            $contract = $schedule->contract;
            $contract->update([
                'paid_amount' => $contract->paid_amount + $difference
            ]);

            if ($contract->isCompleted() && $contract->status !== Contract::STATUS_COMPLETED) {
                $contract->update(['status' => Contract::STATUS_COMPLETED]);
            }

            DB::commit();
            return back()->with('success', 'Тўлов маълумоти янгиланди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }

    public function destroy(PaymentSchedule $schedule)
    {
        DB::beginTransaction();
        try {
            $contract = $schedule->contract;

            if ($schedule->actual_amount > 0) {
                $contract->update([
                    'paid_amount' => $contract->paid_amount - $schedule->actual_amount
                ]);
            }

            $schedule->delete();

            DB::commit();
            return back()->with('success', 'Тўлов графиги ўчирилди!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Хатолик: ' . $e->getMessage());
        }
    }
}
