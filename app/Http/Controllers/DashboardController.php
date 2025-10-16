<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PaymentSchedule;
use App\Models\Distribution;
use App\Models\Lot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // Period
        $periodType = $request->get('period', 'all');
        $chartPeriod = $request->get('chart_period', 'month');

        // Lot Stats (for old dashboard compatibility)
        $stats = [
            'total_lots' => Lot::count(),
            'total_sold_value' => Lot::sum('sold_price') / 1000000000,
            'contracts_signed' => Lot::where('contract_signed', true)->count(),
            'pending_payments' => Lot::where('payment_type', 'muddatli')->count(),
        ];

        // Payment Status (for old dashboard compatibility)
        $paymentStatus = [
            'total_paid' => Contract::sum('paid_amount') / 1000000000,
        ];

        // Recent Lots (for old dashboard compatibility)
        $recentLots = Lot::with(['tuman', 'mahalla'])->latest()->take(10)->get();

        // Tuman Distribution (for old dashboard compatibility)
        $tumanDistribution = collect();

        // Contract Stats
        $totalContracts = Contract::count();
        $activeContracts = Contract::where('status', 'active')->count();
        $completedContracts = Contract::where('status', 'completed')->count();

        $totalContractAmount = Contract::sum('contract_amount') ?: 0;
        $totalPaidAmount = Contract::sum('paid_amount') ?: 0;
        $totalRemainingAmount = Contract::sum('remaining_amount') ?: 0;

        $paymentPercentage = $totalContractAmount > 0
            ? round(($totalPaidAmount / $totalContractAmount) * 100, 1)
            : 0;

        // Revenue data
        $revenueData = $this->getRevenueData($chartPeriod);

        // Distributions
        $distributionsByCategory = Distribution::select('category', DB::raw('SUM(allocated_amount) as total'))
            ->where('status', 'distributed')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                $cats = Distribution::categories();
                return [$cats[$item->category] ?? $item->category => $item->total];
            });

        // Overdue payments
        $overduePayments = PaymentSchedule::where('deadline_date', '<', now())
            ->where('status', '!=', 'paid')
            ->with('contract.lot')
            ->orderBy('deadline_date', 'desc')
            ->take(10)
            ->get();

        // Upcoming payments
        $upcomingPayments = PaymentSchedule::where('status', 'pending')
            ->where('deadline_date', '>=', now())
            ->where('deadline_date', '<=', now()->addDays(30))
            ->with('contract.lot')
            ->orderBy('deadline_date', 'asc')
            ->take(10)
            ->get();

        // Recent contracts
        $recentContracts = Contract::with('lot')
            ->latest('created_at')
            ->take(10)
            ->get();

        // Period info
        $periodInfo = $this->getPeriodInfo($periodType, $request);

        return view('dashboard', compact(
            'user',
            'stats',
            'paymentStatus',
            'recentLots',
            'tumanDistribution',
            'totalContracts',
            'activeContracts',
            'completedContracts',
            'totalContractAmount',
            'totalPaidAmount',
            'totalRemainingAmount',
            'paymentPercentage',
            'revenueData',
            'distributionsByCategory',
            'overduePayments',
            'upcomingPayments',
            'recentContracts',
            'periodType',
            'chartPeriod',
            'periodInfo'
        ));
    }

    private function getRevenueData($period)
    {
        $year = date('Y');

        if ($period === 'month') {
            $data = Contract::select(
                    DB::raw('MONTH(contract_date) as month'),
                    DB::raw('SUM(contract_amount) as total')
                )
                ->whereYear('contract_date', $year)
                ->whereNotNull('contract_date')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $labels = ['Янв','Фев','Мар','Апр','Май','Июн','Июл','Авг','Сен','Окт','Ноя','Дек'];
            $values = [];

            for ($i = 1; $i <= 12; $i++) {
                $values[] = isset($data[$i]) ? round($data[$i] / 1000000, 2) : 0;
            }

            return ['labels' => $labels, 'data' => $values];
        }

        if ($period === 'quarter') {
            $data = Contract::select(
                    DB::raw('QUARTER(contract_date) as quarter'),
                    DB::raw('SUM(contract_amount) as total')
                )
                ->whereYear('contract_date', $year)
                ->whereNotNull('contract_date')
                ->groupBy('quarter')
                ->orderBy('quarter')
                ->pluck('total', 'quarter');

            $labels = ['1-чорак','2-чорак','3-чорак','4-чорак'];
            $values = [];

            for ($i = 1; $i <= 4; $i++) {
                $values[] = isset($data[$i]) ? round($data[$i] / 1000000, 2) : 0;
            }

            return ['labels' => $labels, 'data' => $values];
        }

        // Year
        $startYear = $year - 4;
        $data = Contract::select(
                DB::raw('YEAR(contract_date) as year'),
                DB::raw('SUM(contract_amount) as total')
            )
            ->whereRaw('YEAR(contract_date) BETWEEN ? AND ?', [$startYear, $year])
            ->whereNotNull('contract_date')
            ->groupBy('year')
            ->orderBy('year')
            ->pluck('total', 'year');

        $labels = [];
        $values = [];

        for ($y = $startYear; $y <= $year; $y++) {
            $labels[] = (string)$y;
            $values[] = isset($data[$y]) ? round($data[$y] / 1000000, 2) : 0;
        }

        return ['labels' => $labels, 'data' => $values];
    }

    private function getPeriodInfo($type, $request)
    {
        if ($type === 'month') {
            $months = ['','Январ','Феврал','Март','Апрел','Май','Июн','Июл','Август','Сентябр','Октябр','Ноябр','Декабр'];
            $m = $request->get('month', now()->month);
            $y = $request->get('year', now()->year);
            return $months[$m] . ' ' . $y;
        }

        if ($type === 'quarter') {
            $q = $request->get('quarter', ceil(now()->month / 3));
            $y = $request->get('year', now()->year);
            return $q . '-чорак ' . $y . ' й';
        }

        return 'Барча вақт';
    }
}


