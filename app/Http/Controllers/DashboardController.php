<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Tuman;
use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Get filter parameters
        $periodType = $request->get('period', 'all'); // all, date, range, month, quarter
        $chartPeriod = $request->get('chart_period', 'month'); // month, quarter, year

        // Base query with district filtering
        $query = Lot::query();

        if ($user->role === 'district_user' && $user->tuman_id) {
            $query->where('tuman_id', $user->tuman_id);
        }

        // Apply date filters based on period type
        $query = $this->applyDateFilter($query, $periodType, $request);

        // Get statistics
        $stats = $this->getStatistics($query);

        // Get recent lots
        $recentLots = (clone $query)
            ->with(['tuman', 'mahalla'])
            ->latest('auction_date')
            ->take(10)
            ->get();

        // Get payment status
        $paymentStatus = $this->getPaymentStatus($query);

        // Get revenue based on chart period
        $revenueData = $this->getRevenueByPeriod($query, $chartPeriod);

        // Get tumans for filter
        $tumans = $user->role === 'admin' ? Tuman::all() : Tuman::where('id', $user->tuman_id)->get();

        // Get distribution by tuman
        $tumanDistribution = $this->getTumanDistribution($user, $query);

        // Get period info for display
        $periodInfo = $this->getPeriodInfo($periodType, $request);

        return view('dashboard', compact(
            'stats',
            'recentLots',
            'paymentStatus',
            'revenueData',
            'tumans',
            'tumanDistribution',
            'user',
            'periodType',
            'chartPeriod',
            'periodInfo'
        ));
    }

    private function applyDateFilter($query, $periodType, $request)
    {
        switch ($periodType) {
            case 'date':
                // Specific date (07.10.2025)
                $date = $request->get('date', now()->format('Y-m-d'));
                $query->whereDate('auction_date', '<=', $date);
                break;

            case 'range':
                // Date range (06.10.2025 - 12.10.2025)
                $startDate = $request->get('start_date', now()->subDays(7)->format('Y-m-d'));
                $endDate = $request->get('end_date', now()->format('Y-m-d'));
                $query->whereBetween('auction_date', [$startDate, $endDate]);
                break;

            case 'month':
                // Specific month (October 2025)
                $month = $request->get('month', now()->format('m'));
                $year = $request->get('year', now()->format('Y'));
                $query->whereMonth('auction_date', $month)
                      ->whereYear('auction_date', $year);
                break;

            case 'quarter':
                // Specific quarter (Q4 2025)
                $quarter = $request->get('quarter', ceil(now()->format('m') / 3));
                $year = $request->get('year', now()->format('Y'));
                $startMonth = ($quarter - 1) * 3 + 1;
                $endMonth = $quarter * 3;
                $query->whereYear('auction_date', $year)
                      ->whereRaw('MONTH(auction_date) BETWEEN ? AND ?', [$startMonth, $endMonth]);
                break;

            case 'all':
            default:
                // No filter - all time
                break;
        }

        return $query;
    }

    private function getPeriodInfo($periodType, $request)
    {
        switch ($periodType) {
            case 'date':
                $date = $request->get('date', now()->format('Y-m-d'));
                return Carbon::parse($date)->format('d.m.Y') . ' й холатига';

            case 'range':
                $startDate = $request->get('start_date', now()->subDays(7)->format('Y-m-d'));
                $endDate = $request->get('end_date', now()->format('Y-m-d'));
                return Carbon::parse($startDate)->format('d.m.Y') . ' - ' . Carbon::parse($endDate)->format('d.m.Y') . ' й холатига';

            case 'month':
                $month = $request->get('month', now()->format('m'));
                $year = $request->get('year', now()->format('Y'));
                $monthNames = [
                    1 => 'Январ', 2 => 'Феврал', 3 => 'Март', 4 => 'Апрел',
                    5 => 'Май', 6 => 'Июн', 7 => 'Июл', 8 => 'Август',
                    9 => 'Сентябр', 10 => 'Октябр', 11 => 'Ноябр', 12 => 'Декабр'
                ];
                return $monthNames[(int)$month] . ' ' . $year . ' ойи холатига';

            case 'quarter':
                $quarter = $request->get('quarter', ceil(now()->format('m') / 3));
                $year = $request->get('year', now()->format('Y'));
                return $quarter . '-чорак холатига ' . $year . ' й';

            case 'all':
            default:
                return 'Барча вақт';
        }
    }

    private function getStatistics($query)
    {
        $totalSold = (clone $query)->sum('sold_price');
        $totalInitial = (clone $query)->sum('initial_price');
        $totalLots = (clone $query)->count();
        $contractsSigned = (clone $query)->where('contract_signed', true)->count();
        $pendingPayments = (clone $query)
            ->where('payment_type', 'muddatli')
            ->where('contract_signed', true)
            ->count();
        $completedPayments = (clone $query)
            ->where('payment_type', 'muddatli_emas')
            ->where('contract_signed', true)
            ->count();

        return [
            'total_lots' => $totalLots,
            'total_sold_value' => $totalSold / 1000000000, // Convert to billions
            'total_initial_value' => $totalInitial / 1000000000,
            'contracts_signed' => $contractsSigned,
            'pending_payments' => $pendingPayments,
            'completed_payments' => $completedPayments,
            'average_lot_value' => $totalLots > 0 ? ($totalSold / $totalLots) / 1000000000 : 0,
        ];
    }

    private function getPaymentStatus($query)
    {
        $installmentLots = (clone $query)
            ->where('payment_type', 'muddatli')
            ->where('contract_signed', true)
            ->get();

        $totalDue = 0;
        $totalPaid = 0;
        $overdue = 0;

        foreach ($installmentLots as $lot) {
            $schedules = PaymentSchedule::where('lot_id', $lot->id)
                ->where('payment_date', '<=', now())
                ->get();

            $due = $schedules->sum('planned_amount');
            $paid = $schedules->sum('actual_amount');

            $totalDue += $due;
            $totalPaid += $paid;

            if ($paid < $due) {
                $overdue++;
            }
        }

        return [
            'total_due' => $totalDue / 1000000000,
            'total_paid' => $totalPaid / 1000000000,
            'payment_percentage' => $totalDue > 0 ? ($totalPaid / $totalDue) * 100 : 0,
            'overdue_count' => $overdue,
        ];
    }

    private function getRevenueByPeriod($query, $period)
    {
        switch ($period) {
            case 'month':
                return $this->getMonthlyRevenue($query);
            case 'quarter':
                return $this->getQuarterlyRevenue($query);
            case 'year':
                return $this->getYearlyRevenue($query);
            default:
                return $this->getMonthlyRevenue($query);
        }
    }

    private function getMonthlyRevenue($query)
    {
        $currentYear = date('Y');

        $monthly = (clone $query)
            ->select(
                DB::raw('MONTH(auction_date) as month'),
                DB::raw('SUM(sold_price) as total')
            )
            ->whereYear('auction_date', $currentYear)
            ->whereNotNull('auction_date')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $months = ['Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'];
        $data = array_fill(0, 12, 0);

        foreach ($monthly as $item) {
            $data[$item->month - 1] = $item->total / 1000000;
        }

        return [
            'labels' => $months,
            'data' => $data,
            'type' => 'month'
        ];
    }

    private function getQuarterlyRevenue($query)
    {
        $currentYear = date('Y');

        $quarterly = (clone $query)
            ->select(
                DB::raw('QUARTER(auction_date) as quarter'),
                DB::raw('SUM(sold_price) as total')
            )
            ->whereYear('auction_date', $currentYear)
            ->whereNotNull('auction_date')
            ->groupBy('quarter')
            ->orderBy('quarter')
            ->get();

        $quarters = ['1-чорак', '2-чорак', '3-чорак', '4-чорак'];
        $data = array_fill(0, 4, 0);

        foreach ($quarterly as $item) {
            $data[$item->quarter - 1] = $item->total / 1000000;
        }

        return [
            'labels' => $quarters,
            'data' => $data,
            'type' => 'quarter'
        ];
    }

    private function getYearlyRevenue($query)
    {
        $startYear = date('Y') - 4; // Last 5 years
        $endYear = date('Y');

        $yearly = (clone $query)
            ->select(
                DB::raw('YEAR(auction_date) as year'),
                DB::raw('SUM(sold_price) as total')
            )
            ->whereBetween(DB::raw('YEAR(auction_date)'), [$startYear, $endYear])
            ->whereNotNull('auction_date')
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        $years = [];
        $data = [];

        for ($year = $startYear; $year <= $endYear; $year++) {
            $years[] = (string)$year;
            $yearData = $yearly->firstWhere('year', $year);
            $data[] = $yearData ? $yearData->total / 1000000 : 0;
        }

        return [
            'labels' => $years,
            'data' => $data,
            'type' => 'year'
        ];
    }

    private function getTumanDistribution($user, $query)
    {
        if ($user->role === 'district_user') {
            return [];
        }

        // Get lot IDs from filtered query
        $lotIds = (clone $query)->pluck('id');

        $distribution = Tuman::select(
                'tumans.name_uz',
                DB::raw('COUNT(lots.id) as count'),
                DB::raw('SUM(lots.sold_price) as total')
            )
            ->leftJoin('lots', function($join) use ($lotIds) {
                $join->on('tumans.id', '=', 'lots.tuman_id')
                     ->whereIn('lots.id', $lotIds);
            })
            ->groupBy('tumans.id', 'tumans.name_uz')
            ->orderBy('total', 'desc')
            ->get();

        return $distribution->map(function ($item) {
            return [
                'name' => str_replace(' тумани', '', $item->name_uz),
                'count' => $item->count,
                'total' => $item->total / 1000000000,
            ];
        });
    }
}
