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

        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('monitoring.report2', compact('data', 'filters'));
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
