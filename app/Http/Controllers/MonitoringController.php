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

        return array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });
    }
}
