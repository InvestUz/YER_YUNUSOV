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

            // Clone the base query for each calculation
            $baseQuery = clone $query;

            // One-time payment - include NULL payment types
            $oneTimePayment = (clone $baseQuery)->where(function ($q) {
                $q->where('payment_type', 'muddatsiz')
                    ->orWhereNull('payment_type'); // Include NULL as one-time
            });

            // Installment payment - only muddatli
            $installmentPayment = (clone $baseQuery)->where('payment_type', 'muddatli');

            // Under contract processing
            $underContract = (clone $baseQuery)
                ->where('contract_signed', false)
                ->where('lot_status', 'sold');

            // Not accepted property
            $notAccepted = (clone $baseQuery)->where('lot_status', 'pending_acceptance');

            // Get totals using the base query (this should capture ALL lots)
            $totalCount = $baseQuery->count();
            $totalArea = $baseQuery->sum('land_area');
            $totalInitialPrice = $baseQuery->sum('initial_price');
            $totalSoldPrice = $baseQuery->sum('sold_price');

            $data[] = [
                'tuman' => $tuman->name_uz,
                'tuman_id' => $tuman->id, // Add this for debugging
                'total' => [
                    'count' => $totalCount,
                    'area' => $totalArea,
                    'initial_price' => $totalInitialPrice / 1000000000, // млрд
                    'sold_price' => $totalSoldPrice / 1000000000,
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
            $query = Lot::where('tuman_id', $tuman->id)
                ->where('lot_status', 'sold')
                ->where('contract_signed', true);

            $query = $this->applyFilters($query, $filters);

            $lots = $query->with('distributions')->get();

            // Initialize distributions
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

            // Calculate distributions from lots
            foreach ($lots as $lot) {
                if ($lot->distributions && $lot->distributions->count() > 0) {
                    foreach ($lot->distributions as $dist) {
                        // Allocated (already paid)
                        $distributions[$dist->category . '_allocated'] += $dist->allocated_amount / 1000000000;
                        // Remaining (to be paid)
                        $distributions[$dist->category . '_remaining'] += $dist->remaining_amount / 1000000000;
                    }
                }
            }

            // Calculate future payments by year
            $futurePayments = $this->calculateFuturePayments($tuman->id, $filters);

            // Calculate auction expenses and fees
            $auctionFee = $query->sum('auction_fee') / 1000000000;
            $discountCount = $query->where('discount', '>', 0)->count();
            $discountAmount = $query->sum('discount') / 1000000000;
            $davaktivAmount = $query->sum('davaktiv_amount') / 1000000000;
            $auctionExpenses = $query->sum('auction_expenses') / 1000000000;

            $data[] = [
                'tuman' => $tuman->name_uz,
                'count' => $query->count(),
                'area' => $query->sum('land_area'),
                'initial_price' => $query->sum('initial_price') / 1000000000,
                'sold_price' => $query->sum('sold_price') / 1000000000,
                'auction_fee' => $auctionFee,
                'discount_count' => $discountCount,
                'discount_amount' => $discountAmount,
                'davaktiv_amount' => $davaktivAmount,
                'auction_expenses' => $auctionExpenses,
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

            // Fix: Load payment schedules through contract
            $lots = $query->with(['contract.paymentSchedules'])->get();

            // Fully paid
            $fullyPaid = $lots->filter(function ($lot) {
                $totalPaid = $lot->contract?->paymentSchedules->sum('actual_amount') ?? 0;
                return $totalPaid >= $lot->sold_price;
            });

            // Under monitoring (not fully paid)
            $underMonitoring = $lots->filter(function ($lot) {
                $totalPaid = $lot->contract?->paymentSchedules->sum('actual_amount') ?? 0;
                return $totalPaid < $lot->sold_price;
            });

            // Overdue
            $overdue = $underMonitoring->filter(function ($lot) use ($currentDate) {
                if (!$lot->contract || !$lot->contract->paymentSchedules) {
                    return false;
                }

                return $lot->contract->paymentSchedules
                    ->where('planned_date', '<', $currentDate)
                    ->filter(function ($schedule) {
                        return ($schedule->actual_amount ?? 0) < $schedule->planned_amount;
                    })
                    ->count() > 0;
            });

            // Calculate payment percentages
            $totalPlanned = $underMonitoring->sum(function ($lot) use ($currentDate) {
                if (!$lot->contract || !$lot->contract->paymentSchedules) {
                    return 0;
                }

                return $lot->contract->paymentSchedules
                    ->where('planned_date', '<=', $currentDate)
                    ->sum('planned_amount');
            });

            $totalActual = $underMonitoring->sum(function ($lot) {
                if (!$lot->contract || !$lot->contract->paymentSchedules) {
                    return 0;
                }

                return $lot->contract->paymentSchedules->sum('actual_amount');
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

        $schedules = $lot->paymentSchedules->map(function ($schedule) {
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
            $query->where(function ($q) use ($filters) {
                $q->where('auction_date', '>=', $filters['date_from'])
                    ->orWhereNull('auction_date'); // Include NULL dates
            });
        }

        if (!empty($filters['date_to'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('auction_date', '<=', $filters['date_to'])
                    ->orWhereNull('auction_date'); // Include NULL dates
            });
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

        if (isset($filters['yangi_uzbekiston']) && $filters['yangi_uzbekiston'] !== '') {
            $query->where('yangi_uzbekiston', $filters['yangi_uzbekiston']);
        }

        return $query;
    }
    private function calculateFuturePayments($tumanId, $filters)
    {
        $years = [2025, 2026, 2027];
        $payments = [];

        foreach ($years as $year) {
            $query = PaymentSchedule::whereHas('contract.lot', function ($q) use ($tumanId, $filters) {
                $q->where('tuman_id', $tumanId);

                // Apply filters
                if (!empty($filters['yangi_uzbekiston'])) {
                    $q->where('yangi_uzbekiston', $filters['yangi_uzbekiston']);
                }
                if (!empty($filters['payment_type'])) {
                    $q->where('payment_type', $filters['payment_type']);
                }
                if (!empty($filters['contract_signed'])) {
                    $q->where('contract_signed', $filters['contract_signed']);
                }
            })->whereYear('planned_date', $year);

            $amount = $query->sum('planned_amount');
            $payments[$year] = $amount / 1000000000; // Convert to billions
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
            'count' => 0,
            'area' => 0,
            'initial_price' => 0,
            'sold_price' => 0,
            'auction_fee' => 0,
            'discount_count' => 0,
            'discount_amount' => 0,
            'davaktiv_amount' => 0,
            'auction_expenses' => 0,
            'distributions' => [
                'local_budget_allocated' => 0,
                'development_fund_allocated' => 0,
                'new_uzbekistan_allocated' => 0,
                'district_authority_allocated' => 0,
                'local_budget_remaining' => 0,
                'development_fund_remaining' => 0,
                'new_uzbekistan_remaining' => 0,
                'district_authority_remaining' => 0,
            ],
            'future_payments' => [
                2025 => 0,
                2026 => 0,
                2027 => 0,
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

            foreach ($row['future_payments'] as $year => $amount) {
                $totals['future_payments'][$year] += $amount;
            }
        }

        return $totals;
    }
    private function calculateTotals3($data)
    {
        $totals = [
            'total' => [
                'count' => 0,
                'area' => 0,
                'initial_price' => 0,
                'sold_price' => 0,
            ],
            'fully_paid' => [
                'count' => 0,
                'area' => 0,
                'initial_price' => 0,
                'sold_price' => 0,
            ],
            'under_monitoring' => [
                'count' => 0,
                'area' => 0,
                'initial_price' => 0,
                'sold_price' => 0,
            ],
            'overdue' => [
                'count' => 0,
                'area' => 0,
                'planned_payment' => 0,
                'actual_payment' => 0,
                'percentage' => 0,
            ],
        ];

        foreach ($data as $row) {
            // Total
            $totals['total']['count'] += $row['total']['count'];
            $totals['total']['area'] += $row['total']['area'];
            $totals['total']['initial_price'] += $row['total']['initial_price'];
            $totals['total']['sold_price'] += $row['total']['sold_price'];

            // Fully Paid
            $totals['fully_paid']['count'] += $row['fully_paid']['count'];
            $totals['fully_paid']['area'] += $row['fully_paid']['area'];
            $totals['fully_paid']['initial_price'] += $row['fully_paid']['initial_price'];
            $totals['fully_paid']['sold_price'] += $row['fully_paid']['sold_price'];

            // Under Monitoring
            $totals['under_monitoring']['count'] += $row['under_monitoring']['count'];
            $totals['under_monitoring']['area'] += $row['under_monitoring']['area'];
            $totals['under_monitoring']['initial_price'] += $row['under_monitoring']['initial_price'];
            $totals['under_monitoring']['sold_price'] += $row['under_monitoring']['sold_price'];

            // Overdue
            $totals['overdue']['count'] += $row['overdue']['count'];
            $totals['overdue']['area'] += $row['overdue']['area'];
            $totals['overdue']['planned_payment'] += $row['overdue']['planned_payment'];
            $totals['overdue']['actual_payment'] += $row['overdue']['actual_payment'];
        }

        // Calculate total percentage
        if ($totals['overdue']['planned_payment'] > 0) {
            $totals['overdue']['percentage'] = round(
                ($totals['overdue']['actual_payment'] / $totals['overdue']['planned_payment']) * 100,
                1
            );
        }

        return $totals;
    }
}
