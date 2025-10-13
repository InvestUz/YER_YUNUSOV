<?php

namespace App\Http\Controllers;

use App\Services\MonitoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    protected $monitoringService;

    public function __construct(MonitoringService $monitoringService)
    {
        $this->monitoringService = $monitoringService;
    }

    public function index()
    {
        $user = Auth::user();

        // Check user permissions
        if (!$user->is_active) {
            abort(403, 'Your account is inactive');
        }

        return view('monitoring.index');
    }

    public function report1(Request $request)
    {
        $filters = $this->getFilters($request);
        $data = $this->monitoringService->getReport1($filters);

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('monitoring.report1', compact('data', 'filters'));
    }

    // Add this method to your MonitoringController class

    public function report1Details(Request $request)
    {
        $category = $request->input('category');
        $district = $request->input('district');
        $districtId = $request->input('district_id');
        $filters = $this->getFilters($request);

        // Get category name in Uzbek
        $categoryNames = [
            'total' => 'Барча сотилган ер участкалар',
            'one_time' => 'Бир йўла тўлаш шарти билан сотилган участкалар',
            'installment' => 'Нархини бўлиб тўлаш шарти билан сотилган участкалар',
            'under_contract' => 'Расмийлаштиришда турган участкалар',
            'not_accepted' => 'Мулкни қабул қилиб олиш тугмаси босилмаган участкалар'
        ];

        $categoryName = $categoryNames[$category] ?? 'Ер участкалар';
        $districtName = $district === 'all' ? 'Барча туманлар' : $district;

        // Build the query based on category
        $query = \App\Models\Lot::query();

        // Apply district filter
        if ($district !== 'all') {
            $tuman = \App\Models\Tuman::where('name_uz', $district)->first();
            if ($tuman) {
                $query->where('tuman_id', $tuman->id);
            }
        }

        // Apply date filters
        if (!empty($filters['date_from'])) {
            $query->where('auction_date', '>=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $query->where('auction_date', '<=', $filters['date_to']);
        }

        // Apply other filters
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

        // Apply category-specific filters using scopes
        switch ($category) {
            case 'one_time':
                // Use the scope from your Lot model if it exists
                if (method_exists(\App\Models\Lot::class, 'scopeOneTimePayment')) {
                    $query->oneTimePayment();
                } else {
                    // Fallback: assuming one-time payment has payment_type or similar field
                    // Adjust this based on your actual database column
                    $query->where('payment_type', 'one_time')
                        ->orWhere('installment_months', 0)
                        ->orWhereNull('installment_months');
                }
                break;

            case 'installment':
                // Use the scope from your Lot model if it exists
                if (method_exists(\App\Models\Lot::class, 'scopeInstallmentPayment')) {
                    $query->installmentPayment();
                } else {
                    // Fallback: assuming installment has installment_months > 0
                    $query->where('installment_months', '>', 0);
                }
                break;

            case 'under_contract':
                $query->where('contract_signed', false)
                    ->where('lot_status', 'sold');
                break;

            case 'not_accepted':
                $query->where('lot_status', 'pending_acceptance');
                break;

            case 'total':
            default:
                // No additional filters for total
                break;
        }

        // Clone query for stats calculation before pagination
        $statsQuery = clone $query;

        // Get lots with pagination
        $lots = $query->with(['tuman', 'mahalla'])
            ->orderBy('auction_date', 'desc')
            ->paginate(50);

        // Calculate statistics using the cloned query
        $stats = [
            'count' => $statsQuery->count(),
            'total_area' => $statsQuery->sum('land_area'),
            'total_initial_price' => $statsQuery->sum('initial_price') / 1000000000, // млрд
            'total_sold_price' => $statsQuery->sum('sold_price') / 1000000000, // млрд
        ];

        // Handle Excel export
        if ($request->input('export') === 'excel') {
            return $this->exportDetailsToExcel($lots->items(), $categoryName, $districtName);
        }

        return view('monitoring.report1-details', compact(
            'lots',
            'categoryName',
            'districtName',
            'category',
            'stats',
            'filters'
        ));
    }
    private function exportDetailsToExcel($lots, $categoryName, $districtName)
    {
        $filename = 'details_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($lots) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                '№',
                'Участка рақами',
                'Манзил',
                'Майдони (га)',
                'Зона',
                'Бошланғич нарх (сўм)',
                'Сотилган нарх (сўм)',
                'Тўлаш усули',
                'Эгаси',
                'Субъект тури',
                'Аукцион санаси',
                'Ҳолати'
            ]);

            // Data
            $index = 1;
            foreach ($lots as $lot) {
                // Determine payment method based on installment_months
                $paymentMethod = 'Бир йўла';
                if (isset($lot->installment_months) && $lot->installment_months > 0) {
                    $paymentMethod = 'Бўлиб тўлаш';
                }

                fputcsv($file, [
                    $index++,
                    $lot->lot_number ?? 'N/A',
                    $lot->address ?? '',
                    number_format($lot->land_area, 2),
                    $lot->zone ?? '',
                    $lot->initial_price,
                    $lot->sold_price,
                    $paymentMethod,
                    $lot->winner_name ?? '',
                    $lot->winner_type === 'legal' ? 'Юридик' : 'Жисмоний',
                    $lot->auction_date ? \Carbon\Carbon::parse($lot->auction_date)->format('d.m.Y') : '',
                    $lot->contract_signed ? 'Сотилган' : ($lot->lot_status === 'sold' ? 'Расмийлаштирилмоқда' : 'Кутилмоқда')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function report2(Request $request)
    {
        $filters = $this->getFilters($request);
        $data = $this->monitoringService->getReport2($filters);

        // Handle Excel export
        if ($request->input('export') === 'excel') {
            return $this->exportReport2ToExcel($data, $filters);
        }

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('monitoring.report2', compact('data', 'filters'));
    }

public function report2Details(Request $request)
{
    $category = $request->input('category');
    $district = $request->input('district');
    $districtId = $request->input('district_id');
    $filters = $this->getFilters($request);

    // Category names in Uzbek
    $categoryNames = [
        'sold' => 'Сотилган ер участкалар',
        'discount' => 'Чегирма берилган участкалар',
    ];

    $categoryName = $categoryNames[$category] ?? 'Ер участкалар';
    $districtName = $district === 'all' ? 'Барча туманлар' : $district;

    // Build query
    $query = \App\Models\Lot::query();

    // Apply district filter
    if ($district !== 'all') {
        $tuman = \App\Models\Tuman::where('name_uz', $district)->first();
        if ($tuman) {
            $query->where('tuman_id', $tuman->id);
        }
    }

    // Only sold lots with contracts
    $query->where('contract_signed', true)->where('lot_status', 'sold');

    // Apply date filters
    if (!empty($filters['date_from'])) {
        $query->where('auction_date', '>=', $filters['date_from']);
    }
    if (!empty($filters['date_to'])) {
        $query->where('auction_date', '<=', $filters['date_to']);
    }

    // Apply other filters
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

    // Apply category-specific filters
    switch ($category) {
        case 'discount':
            $query->where('discount', '>', 0);
            break;
        case 'sold':
        default:
            // No additional filters for total sold
            break;
    }

    // Clone query for stats
    $statsQuery = clone $query;

    // Get lots with pagination
    $lots = $query->with(['tuman', 'mahalla', 'distributions'])
        ->orderBy('auction_date', 'desc')
        ->paginate(50);

    // Calculate statistics
    $stats = [
        'count' => $statsQuery->count(),
        'total_area' => $statsQuery->sum('land_area'),
        'total_initial_price' => $statsQuery->sum('initial_price') / 1000000000,
        'total_sold_price' => $statsQuery->sum('sold_price') / 1000000000,
        'total_auction_fee' => $statsQuery->sum('auction_fee') / 1000000000,
        'total_discount' => $statsQuery->sum('discount') / 1000000000,
    ];

    // Handle Excel export
    if ($request->input('export') === 'excel') {
        return $this->exportReport2DetailsToExcel($lots->items(), $categoryName, $districtName);
    }

    return view('monitoring.report-details', compact(
        'lots',
        'categoryName',
        'districtName',
        'category',
        'stats',
        'filters'
    ));
}

/**
 * Show detailed information for Report 3 (Svod-3)
 */
public function report3Details(Request $request)
{
    $category = $request->input('category');
    $district = $request->input('district');
    $districtId = $request->input('district_id');
    $filters = $this->getFilters($request);
    $currentDate = $filters['current_date'] ?? now();

    // Category names in Uzbek
    $categoryNames = [
        'installment_total' => 'Бўлиб тўлаш шарти билан сотилган участкалар',
        'fully_paid' => 'Тўлиқ тўланган участкалар',
        'under_monitoring' => 'Назоратдаги участкалар',
        'overdue' => 'Муддат ўтган участкалар',
    ];

    $categoryName = $categoryNames[$category] ?? 'Ер участкалар';
    $districtName = $district === 'all' ? 'Барча туманлар' : $district;

    // Build query
    $query = \App\Models\Lot::query();

    // Apply district filter
    if ($district !== 'all') {
        $tuman = \App\Models\Tuman::where('name_uz', $district)->first();
        if ($tuman) {
            $query->where('tuman_id', $tuman->id);
        }
    }

    // Installment payment lots only
    if (method_exists(\App\Models\Lot::class, 'scopeInstallmentPayment')) {
        $query->installmentPayment();
    } else {
        $query->where('installment_months', '>', 0);
    }

    // Apply date filters
    if (!empty($filters['date_from'])) {
        $query->where('auction_date', '>=', $filters['date_from']);
    }
    if (!empty($filters['date_to'])) {
        $query->where('auction_date', '<=', $filters['date_to']);
    }

    // Apply other filters
    if (!empty($filters['subject_type'])) {
        $query->where('winner_type', $filters['subject_type']);
    }
    if (!empty($filters['zone'])) {
        $query->where('zone', $filters['zone']);
    }
    if (isset($filters['yangi_uzbekiston'])) {
        $query->where('yangi_uzbekiston', $filters['yangi_uzbekiston']);
    }

    // Apply category-specific filters
    $lotsQuery = clone $query;

    switch ($category) {
        case 'fully_paid':
            // Lots where total actual payments >= sold price
            $lotsQuery->whereHas('paymentSchedules', function($q) {
                $q->selectRaw('lot_id, SUM(actual_amount) as total_paid')
                  ->groupBy('lot_id')
                  ->havingRaw('SUM(actual_amount) >= (SELECT sold_price FROM lots WHERE id = lot_id)');
            });
            break;

        case 'under_monitoring':
            // Lots where payments are ongoing but not overdue
            $lotsQuery->where('contract_signed', true)
                ->whereHas('paymentSchedules', function($q) {
                    $q->selectRaw('lot_id, SUM(actual_amount) as total_paid')
                      ->groupBy('lot_id')
                      ->havingRaw('SUM(actual_amount) < (SELECT sold_price FROM lots WHERE id = lot_id)');
                });
            break;

        case 'overdue':
            // Lots with overdue payments
            $lotsQuery->whereHas('paymentSchedules', function($q) use ($currentDate) {
                $q->where('payment_date', '<', $currentDate)
                  ->whereRaw('actual_amount < planned_amount');
            });
            break;

        case 'installment_total':
        default:
            // All installment lots - no additional filter
            break;
    }

    // Clone for stats
    $statsQuery = clone $lotsQuery;

    // Get lots with pagination
    $lots = $lotsQuery->with(['tuman', 'mahalla', 'paymentSchedules'])
        ->orderBy('auction_date', 'desc')
        ->paginate(50);

    // Calculate statistics
    $stats = [
        'count' => $statsQuery->count(),
        'total_area' => $statsQuery->sum('land_area'),
        'total_initial_price' => $statsQuery->sum('initial_price') / 1000000000,
        'total_sold_price' => $statsQuery->sum('sold_price') / 1000000000,
    ];

    // Calculate payment statistics for overdue category
    if ($category === 'overdue') {
        $paymentStats = \App\Models\PaymentSchedule::whereIn('lot_id', $statsQuery->pluck('id'))
            ->where('payment_date', '<=', $currentDate)
            ->selectRaw('SUM(planned_amount) as total_planned, SUM(actual_amount) as total_actual')
            ->first();

        $stats['total_planned_payment'] = ($paymentStats->total_planned ?? 0) / 1000000000;
        $stats['total_actual_payment'] = ($paymentStats->total_actual ?? 0) / 1000000000;
        $stats['percentage'] = $stats['total_planned_payment'] > 0
            ? round(($stats['total_actual_payment'] / $stats['total_planned_payment']) * 100, 1)
            : 0;
    }

    // Handle Excel export
    if ($request->input('export') === 'excel') {
        return $this->exportReport3DetailsToExcel($lots->items(), $categoryName, $districtName);
    }

    return view('monitoring.report-details', compact(
        'lots',
        'categoryName',
        'districtName',
        'category',
        'stats',
        'filters',
        'currentDate'
    ));
}

/**
 * Export Report 2 details to Excel
 */
private function exportReport2DetailsToExcel($lots, $categoryName, $districtName)
{
    $filename = 'svod2_details_' . date('Y-m-d_H-i-s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function () use ($lots, $categoryName, $districtName) {
        $file = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers
        fputcsv($file, [
            '№',
            'Участка рақами',
            'Туман',
            'Манзил',
            'Майдони (га)',
            'Зона',
            'Бошланғич нарх (млрд сўм)',
            'Сотилган нарх (млрд сўм)',
            'Аукцион ҳақи (млрд сўм)',
            'Чегирма (млрд сўм)',
            'Эгаси',
            'Субъект тури',
            'Аукцион санаси',
            'Янги Ўзбекистон'
        ]);

        // Data
        $index = 1;
        foreach ($lots as $lot) {
            fputcsv($file, [
                $index++,
                $lot->lot_number ?? 'N/A',
                $lot->tuman->name_uz ?? '',
                $lot->address ?? '',
                number_format($lot->land_area, 2),
                $lot->zone ?? '',
                number_format($lot->initial_price / 1000000000, 1),
                number_format($lot->sold_price / 1000000000, 1),
                number_format($lot->auction_fee / 1000000000, 1),
                number_format($lot->discount / 1000000000, 1),
                $lot->winner_name ?? '',
                $lot->winner_type === 'legal' ? 'Юридик' : 'Жисмоний',
                $lot->auction_date ? \Carbon\Carbon::parse($lot->auction_date)->format('d.m.Y') : '',
                $lot->yangi_uzbekiston ? 'Ҳа' : 'Йўқ'
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

/**
 * Export Report 3 details to Excel
 */
private function exportReport3DetailsToExcel($lots, $categoryName, $districtName)
{
    $filename = 'svod3_details_' . date('Y-m-d_H-i-s') . '.csv';

    $headers = [
        'Content-Type' => 'text/csv; charset=utf-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ];

    $callback = function () use ($lots, $categoryName, $districtName) {
        $file = fopen('php://output', 'w');

        // Add BOM for UTF-8
        fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Headers
        fputcsv($file, [
            '№',
            'Участка рақами',
            'Туман',
            'Манзил',
            'Майдони (га)',
            'Зона',
            'Бошланғич нарх (млрд сўм)',
            'Сотилган нарх (млрд сўм)',
            'Бўлиб тўлаш муддати (ой)',
            'Режадаги тўлов (млрд сўм)',
            'Амалдаги тўлов (млрд сўм)',
            'Фоиз (%)',
            'Эгаси',
            'Субъект тури',
            'Аукцион санаси'
        ]);

        // Data
        $index = 1;
        foreach ($lots as $lot) {
            $totalPlanned = $lot->paymentSchedules->sum('planned_amount');
            $totalActual = $lot->paymentSchedules->sum('actual_amount');
            $percentage = $totalPlanned > 0 ? round(($totalActual / $totalPlanned) * 100, 1) : 0;

            fputcsv($file, [
                $index++,
                $lot->lot_number ?? 'N/A',
                $lot->tuman->name_uz ?? '',
                $lot->address ?? '',
                number_format($lot->land_area, 2),
                $lot->zone ?? '',
                number_format($lot->initial_price / 1000000000, 1),
                number_format($lot->sold_price / 1000000000, 1),
                $lot->installment_months ?? 0,
                number_format($totalPlanned / 1000000000, 1),
                number_format($totalActual / 1000000000, 1),
                number_format($percentage, 1),
                $lot->winner_name ?? '',
                $lot->winner_type === 'legal' ? 'Юридик' : 'Жисмоний',
                $lot->auction_date ? \Carbon\Carbon::parse($lot->auction_date)->format('d.m.Y') : ''
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
    private function exportReport2ToExcel($data, $filters)
    {
        $filename = 'svod2_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Headers
            fputcsv($file, [
                'Т/Р',
                'Ҳудудлар',
                'Сони',
                'Майдони (га)',
                'Бошланғич нархи (млрд)',
                'Сотилган нархи (млрд)',
                'Аукцион ҳақи (млрд)',
                'Чегирма сони',
                'Чегирма қиймати (млрд)',
                'Давактив (млрд)',
                'Харажат (млрд)',
                'М.бюджет (амалда)',
                'Жамғарма (амалда)',
                'Я.Ўзб (амалда)',
                'Туман (амалда)',
                'М.бюджет (келгусида)',
                'Жамғарма (келгусида)',
                'Я.Ўзб (келгусида)',
                'Туман (келгусида)',
                '2025 йилда',
                '2026 йилда',
                '2027 йилда',
            ]);

            // Data rows
            $index = 1;
            foreach ($data['data'] as $row) {
                fputcsv($file, [
                    $index++,
                    $row['tuman'],
                    $row['count'],
                    number_format($row['area'], 2),
                    number_format($row['initial_price'], 1),
                    number_format($row['sold_price'], 1),
                    number_format($row['auction_fee'], 1),
                    $row['discount_count'],
                    number_format($row['discount_amount'], 1),
                    number_format($row['davaktiv_amount'], 1),
                    number_format($row['auction_expenses'], 1),
                    number_format($row['distributions']['local_budget_allocated'], 1),
                    number_format($row['distributions']['development_fund_allocated'], 1),
                    number_format($row['distributions']['new_uzbekistan_allocated'], 1),
                    number_format($row['distributions']['district_authority_allocated'], 1),
                    number_format($row['distributions']['local_budget_remaining'], 1),
                    number_format($row['distributions']['development_fund_remaining'], 1),
                    number_format($row['distributions']['new_uzbekistan_remaining'], 1),
                    number_format($row['distributions']['district_authority_remaining'], 1),
                    number_format($row['future_payments'][2025] ?? 0, 1),
                    number_format($row['future_payments'][2026] ?? 0, 1),
                    number_format($row['future_payments'][2027] ?? 0, 1),
                ]);
            }

            // Totals row
            $totals = $data['totals'];
            fputcsv($file, [
                '',
                'ЖАМИ:',
                $totals['count'],
                number_format($totals['area'], 2),
                number_format($totals['initial_price'], 1),
                number_format($totals['sold_price'], 1),
                number_format($totals['auction_fee'], 1),
                $totals['discount_count'],
                number_format($totals['discount_amount'], 1),
                number_format($totals['davaktiv_amount'], 1),
                number_format($totals['auction_expenses'], 1),
                number_format($totals['distributions']['local_budget_allocated'], 1),
                number_format($totals['distributions']['development_fund_allocated'], 1),
                number_format($totals['distributions']['new_uzbekistan_allocated'], 1),
                number_format($totals['distributions']['district_authority_allocated'], 1),
                number_format($totals['distributions']['local_budget_remaining'], 1),
                number_format($totals['distributions']['development_fund_remaining'], 1),
                number_format($totals['distributions']['new_uzbekistan_remaining'], 1),
                number_format($totals['distributions']['district_authority_remaining'], 1),
                number_format($totals['future_payments'][2025] ?? 0, 1),
                number_format($totals['future_payments'][2026] ?? 0, 1),
                number_format($totals['future_payments'][2027] ?? 0, 1),
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report3(Request $request)
    {
        $filters = $this->getFilters($request);
        $data = $this->monitoringService->getReport3($filters);

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('monitoring.report3', compact('data', 'filters'));
    }

    public function paymentSchedule($lotId)
    {
        $data = $this->monitoringService->getPaymentSchedule($lotId);
        return response()->json($data);
    }

    public function export(Request $request)
    {
        $reportType = $request->input('report_type', 'report1');
        $filters = $this->getFilters($request);

        // Export to Excel logic
        $data = $this->monitoringService->{"get" . ucfirst($reportType)}($filters);

        return response()->json([
            'message' => 'Export functionality - implement with Laravel Excel',
            'data' => $data
        ]);
    }

    private function getFilters(Request $request)
    {
        $filters = [
            'date_from' => $request->input('date_from', '2023-01-01'),
            'date_to' => $request->input('date_to', now()->format('Y-m-d')),
            'subject_type' => $request->input('subject_type'),
            'zone' => $request->input('zone'),
            'master_plan_zone' => $request->input('master_plan_zone'),
            'yangi_uzbekiston' => $request->input('yangi_uzbekiston'),
            'current_date' => $request->input('current_date', now()),
        ];

        // District users can only see their district data
        $user = Auth::user();
        if ($user->role === 'district_user' && $user->tuman_id) {
            $filters['tuman_id'] = $user->tuman_id;
        }

        return array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
