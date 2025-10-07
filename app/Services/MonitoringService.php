<?php

namespace App\Services;

use App\Models\Lot;
use App\Models\Tuman;
use App\Models\PaymentSchedule;
use Illuminate\Support\Facades\DB;

class MonitoringService
{
    /**
     * Свод-1: Overview of sold land parcels
     */
    public function getReport1($filters = [])
    {
        $tumans = Tuman::all();
        $data = [];

        foreach ($tumans as $tuman) {
            $query = Lot::where('tuman_id', $tuman->id);

            // Apply filters
            $query = $this->applyFilters($query, $filters);

            // One-time payment
            $oneTimePayment = (clone $query)->oneTimePayment();
            // Installment payment
            $installmentPayment = (clone $query)->installmentPayment();
            // Under contract processing
            $underContract = (clone $query)->where('contract_signed', false)->where('lot_status', 'sold');
            // Not accepted property
            $notAccepted = (clone $query)->where('lot_status', 'pending_acceptance');

            $data[] = [
                'tuman' => $tuman->name_uz,
                'total' => [
                    'count' => $query->count(),
                    'area' => $query->sum('land_area'),
                    'initial_price' => $query->sum('initial_price') / 1000000000, // млрд
                    'sold_price' => $query->sum('sold_price') / 1000000000,
                ],
                'one_time' => [
                    'count' => $oneTimePayment->count(),
                    'area' => $oneTimePayment->sum('land_area'),
                    'initial_price' => $oneTimePayment->sum('initial_price') / 1000000000,
                    'sold_price' => $oneTimePayment->sum('sold_price') / 1000000000,
                ],
                'installment' => [
                    'count' => $installmentPayment->count(),
                    'area' => $installmentPayment->sum('land_area'),
                    'initial_price' => $installmentPayment->sum('initial_price') / 1000000000,
                    'sold_price' => $installmentPayment->sum('sold_price') / 1000000000,
                ],
                'under_contract' => [
                    'count' => $underContract->count(),
                    'area' => $underContract->sum('land_area'),
                    'initial_price' => $underContract->sum('initial_price') / 1000000000,
                    'sold_price' => $underContract->sum('sold_price') / 1000000000,
                ],
                'not_accepted' => [
                    'count' => $notAccepted->count(),
                    'amount' => $notAccepted->sum('sold_price') / 1000000000,
                ]
            ];
        }

        return [
            'data' => $data,
            'totals' => $this->calculateTotals($data)
        ];
    }

    /**
     * Свод-2: Distribution and payments
     */
    public function getReport2($filters = [])
    {
        $tumans = Tuman::all();
        $data = [];

        foreach ($tumans as $tuman) {
            $query = Lot::where('tuman_id', $tuman->id)->withContract();
            $query = $this->applyFilters($query, $filters);

            $lots = $query->with('distributions')->get();

            // Calculate distributions
            $distributions = [
                'local_budget_allocated' => 0,
                'development_fund_allocated' => 0,
                'new_uzbekistan_allocated' => 0,
                'district_authority_allocated' => 0,
                'local_budget_remaining' => 0,
                'development_fund_remaining' => 0,
                'new_uzbekistan_remaining' => 0,
                'district_authority_remaining' => 0,
            ];

            foreach ($lots as $lot) {
                foreach ($lot->distributions as $dist) {
                    $distributions[$dist->category . '_allocated'] += $dist->allocated_amount;
                    $distributions[$dist->category . '_remaining'] += $dist->remaining_amount;
                }
            }

            // Future payments calculation
            $futurePayments = $this->calculateFuturePayments($tuman->id, $filters);

            $data[] = [
                'tuman' => $tuman->name_uz,
                'count' => $query->count(),
                'area' => $query->sum('land_area'),
                'initial_price' => $query->sum('initial_price') / 1000000000,
                'sold_price' => $query->sum('sold_price') / 1000000000,
                'auction_fee' => $query->sum('auction_fee') / 1000000000,
                'discount_count' => $query->where('discount', '>', 0)->count(),
                'discount_amount' => $query->sum('discount') / 1000000000,
                'davaktiv_amount' => $query->sum('davaktiv_amount') / 1000000000,
                'auction_expenses' => $query->sum('auction_expenses') / 1000000000,
                'distributions' => $distributions,
                'future_payments' => $futurePayments,
            ];
        }

        return [
            'data' => $data,
            'totals' => $this->calculateTotals2($data)
        ];
    }

    /**
     * Свод-3: Installment payment monitoring
     */
    public function getReport3($filters = [])
    {
        $tumans = Tuman::all();
        $data = [];
        $currentDate = $filters['current_date'] ?? now();

        foreach ($tumans as $tuman) {
            $query = Lot::where('tuman_id', $tuman->id)
                ->installmentPayment()
                ->withContract();

            $query = $this->applyFilters($query, $filters);

            $lots = $query->with('paymentSchedules')->get();

            // Fully paid
            $fullyPaid = $lots->filter(function($lot) {
                return $lot->paymentSchedules->sum('actual_amount') >= $lot->sold_price;
            });

            // Under monitoring (not fully paid)
            $underMonitoring = $lots->filter(function($lot) {
                return $lot->paymentSchedules->sum('actual_amount') < $lot->sold_price;
            });

            // Overdue
            $overdue = $underMonitoring->filter(function($lot) use ($currentDate) {
                return $lot->paymentSchedules->where('payment_date', '<', $currentDate)
                    ->where('actual_amount', '<', 'planned_amount')->count() > 0;
            });

            // Calculate payment percentages
            $totalPlanned = $underMonitoring->sum(function($lot) use ($currentDate) {
                return $lot->paymentSchedules
                    ->where('payment_date', '<=', $currentDate)
                    ->sum('planned_amount');
            });

            $totalActual = $underMonitoring->sum(function($lot) {
                return $lot->paymentSchedules->sum('actual_amount');
            });

            $paymentPercentage = $totalPlanned > 0 ? ($totalActual / $totalPlanned) * 100 : 0;

            $data[] = [
                'tuman' => $tuman->name_uz,
                'total' => [
                    'count' => $lots->count(),
                    'area' => $lots->sum('land_area'),
                    'initial_price' => $lots->sum('initial_price') / 1000000000,
                    'sold_price' => $lots->sum('sold_price') / 1000000000,
                ],
                'fully_paid' => [
                    'count' => $fullyPaid->count(),
                    'area' => $fullyPaid->sum('land_area'),
                    'initial_price' => $fullyPaid->sum('initial_price') / 1000000000,
                    'sold_price' => $fullyPaid->sum('sold_price') / 1000000000,
                ],
                'under_monitoring' => [
                    'count' => $underMonitoring->count(),
                    'area' => $underMonitoring->sum('land_area'),
                    'initial_price' => $underMonitoring->sum('initial_price') / 1000000000,
                    'sold_price' => $underMonitoring->sum('sold_price') / 1000000000,
                ],
                'overdue' => [
                    'count' => $overdue->count(),
                    'area' => $overdue->sum('land_area'),
                    'planned_payment' => $totalPlanned / 1000000000,
                    'actual_payment' => $totalActual / 1000000000,
                    'percentage' => round($paymentPercentage, 1),
                ]
            ];
        }

        return [
            'data' => $data,
            'totals' => $this->calculateTotals3($data)
        ];
    }

    /**
     * Payment schedule details
     */
    public function getPaymentSchedule($lotId)
    {
        $lot = Lot::with('paymentSchedules')->findOrFail($lotId);

        $schedules = $lot->paymentSchedules->map(function($schedule) {
            return [
                'date' => $schedule->payment_date->format('d.m.Y'),
                'planned' => $schedule->planned_amount,
                'actual' => $schedule->actual_amount,
                'difference' => $schedule->difference,
                'percentage' => $schedule->percentage_paid,
                'is_overdue' => $schedule->is_overdue,
                'frequency' => $schedule->payment_frequency,
                'is_additional' => $schedule->is_additional_agreement,
            ];
        });

        return [
            'lot' => $lot,
            'schedules' => $schedules,
            'totals' => [
                'planned' => $schedules->sum('planned'),
                'actual' => $schedules->sum('actual'),
                'difference' => $schedules->sum('difference'),
            ],
            'current_period' => $this->getCurrentPeriodPayment($lot),
        ];
    }

    // Helper methods
    private function applyFilters($query, $filters)
    {
        if (!empty($filters['date_from'])) {
            $query->where('auction_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('auction_date', '<=', $filters['date_to']);
        }

        if (!empty($filters['subject_type'])) {
            $query->where('winner_type', $filters['subject_type']);
        }

        if (!empty($filters['zone'])) {
            $query->where('zone', $filters['zone']);
        }

        if (!empty($filters['master_plan_zone'])) {
            $query->where('master_plan_zone', $filters['master_plan_zone']);
        }

        if (isset($filters['yangi_uzbekiston'])) {
            $query->where('yangi_uzbekiston', $filters['yangi_uzbekiston']);
        }

        return $query;
    }

    private function calculateFuturePayments($tumanId, $filters)
    {
        $years = [2025, 2026, 2027];
        $payments = [];

        foreach ($years as $year) {
            $amount = PaymentSchedule::whereHas('lot', function($q) use ($tumanId) {
                $q->where('tuman_id', $tumanId);
            })
            ->whereYear('payment_date', $year)
            ->sum('planned_amount');

            $payments[$year] = $amount / 1000000000;
        }

        return $payments;
    }

    private function getCurrentPeriodPayment($lot)
    {
        $currentDate = now();
        $currentPeriod = PaymentSchedule::where('lot_id', $lot->id)
            ->where('payment_date', '<=', $currentDate)
            ->orderBy('payment_date', 'desc')
            ->first();

        if (!$currentPeriod) return null;

        return [
            'date' => $currentPeriod->payment_date->format('d.m.Y'),
            'planned' => $currentPeriod->planned_amount,
            'percentage' => $currentPeriod->percentage_paid,
        ];
    }

    private function calculateTotals($data)
    {
        $totals = [
            'total' => ['count' => 0, 'area' => 0, 'initial_price' => 0, 'sold_price' => 0],
            'one_time' => ['count' => 0, 'area' => 0, 'initial_price' => 0, 'sold_price' => 0],
            'installment' => ['count' => 0, 'area' => 0, 'initial_price' => 0, 'sold_price' => 0],
            'under_contract' => ['count' => 0, 'area' => 0, 'initial_price' => 0, 'sold_price' => 0],
            'not_accepted' => ['count' => 0, 'amount' => 0],
        ];

        foreach ($data as $row) {
            foreach (['total', 'one_time', 'installment', 'under_contract'] as $key) {
                $totals[$key]['count'] += $row[$key]['count'];
                $totals[$key]['area'] += $row[$key]['area'];
                $totals[$key]['initial_price'] += $row[$key]['initial_price'];
                $totals[$key]['sold_price'] += $row[$key]['sold_price'];
            }
            $totals['not_accepted']['count'] += $row['not_accepted']['count'];
            $totals['not_accepted']['amount'] += $row['not_accepted']['amount'];
        }

        return $totals;
    }

    private function calculateTotals2($data)
    {
        $totals = [
            'count' => 0, 'area' => 0, 'initial_price' => 0, 'sold_price' => 0,
            'auction_fee' => 0, 'discount_count' => 0, 'discount_amount' => 0,
            'davaktiv_amount' => 0, 'auction_expenses' => 0,
            'distributions' => [
                'local_budget_allocated' => 0, 'development_fund_allocated' => 0,
                'new_uzbekistan_allocated' => 0, 'district_authority_allocated' => 0,
                'local_budget_remaining' => 0, 'development_fund_remaining' => 0,
                'new_uzbekistan_remaining' => 0, 'district_authority_remaining' => 0,
            ],
        ];

        foreach ($data as $row) {
            $totals['count'] += $row['count'];
            $totals['area'] += $row['area'];
            $totals['initial_price'] += $row['initial_price'];
            $totals['sold_price'] += $row['sold_price'];
            $totals['auction_fee'] += $row['auction_fee'];
            $totals['discount_count'] += $row['discount_count'];
            $totals['discount_amount'] += $row['discount_amount'];
            $totals['davaktiv_amount'] += $row['davaktiv_amount'];
            $totals['auction_expenses'] += $row['auction_expenses'];

            foreach ($row['distributions'] as $key => $value) {
                $totals['distributions'][$key] += $value;
            }
        }

        return $totals;
    }

    private function calculateTotals3($data)
    {
        $totals = [
            'total' => ['count' => 0, 'area' => 0, 'initial_price' => 0, 'sold_price' => 0],
            'fully_paid' => ['count' => 0, 'area' => 0, 'initial_price' => 0, 'sold_price' => 0],
            'under_monitoring' => ['count' => 0, 'area' => 0, 'initial_price' => 0, 'sold_price' => 0],
            'overdue' => ['count' => 0, 'area' => 0, 'planned_payment' => 0, 'actual_payment' => 0],
        ];

        foreach ($data as $row) {
            foreach (['total', 'fully_paid', 'under_monitoring'] as $key) {
                $totals[$key]['count'] += $row[$key]['count'];
                $totals[$key]['area'] += $row[$key]['area'];
                $totals[$key]['initial_price'] += $row[$key]['initial_price'];
                $totals[$key]['sold_price'] += $row[$key]['sold_price'];
            }
            $totals['overdue']['count'] += $row['overdue']['count'];
            $totals['overdue']['area'] += $row['overdue']['area'];
            $totals['overdue']['planned_payment'] += $row['overdue']['planned_payment'];
            $totals['overdue']['actual_payment'] += $row['overdue']['actual_payment'];
        }

        if ($totals['overdue']['planned_payment'] > 0) {
            $totals['overdue']['percentage'] = round(
                ($totals['overdue']['actual_payment'] / $totals['overdue']['planned_payment']) * 100, 1
            );
        } else {
            $totals['overdue']['percentage'] = 0;
        }

        return $totals;
    }
}
