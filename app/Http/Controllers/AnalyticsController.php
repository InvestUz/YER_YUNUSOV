<?php
// File: app/Http/Controllers/AnalyticsController.php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\LotView;
use App\Models\LotMessage;
use App\Models\LoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * View detailed analytics for a specific lot
     */
    public function lotViews(Request $request, Lot $lot)
    {
        $user = Auth::user();

        // Check permission
        if ($user->role === 'district_user' && $lot->tuman_id !== $user->tuman_id) {
            abort(403);
        }

        // Get views with filters
        $query = LotView::where('lot_id', $lot->id)
            ->with('user')
            ->orderBy('viewed_at', 'desc');

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('viewed_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('viewed_at', '<=', $request->date_to);
        }

        $views = $query->paginate(50);

        // Statistics
        $stats = [
            'total_views' => LotView::where('lot_id', $lot->id)->count(),
            'unique_ips' => LotView::where('lot_id', $lot->id)
                ->distinct('ip_address')
                ->count('ip_address'),
            'authenticated_views' => LotView::where('lot_id', $lot->id)
                ->whereNotNull('user_id')
                ->count(),
            'anonymous_views' => LotView::where('lot_id', $lot->id)
                ->whereNull('user_id')
                ->count(),
        ];

        // Device breakdown
        $deviceStats = LotView::where('lot_id', $lot->id)
            ->select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->get();

        // Browser breakdown
        $browserStats = LotView::where('lot_id', $lot->id)
            ->select('browser', DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->get();

        // Platform breakdown
        $platformStats = LotView::where('lot_id', $lot->id)
            ->select('platform', DB::raw('count(*) as count'))
            ->groupBy('platform')
            ->get();

        // Daily views for the last 30 days
        $dailyViews = LotView::where('lot_id', $lot->id)
            ->where('viewed_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(viewed_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('analytics.lot-views', compact(
            'lot',
            'views',
            'stats',
            'deviceStats',
            'browserStats',
            'platformStats',
            'dailyViews'
        ));
    }

    /**
     * View messages for a specific lot
     */
    public function lotMessages(Request $request, Lot $lot)
    {
        $user = Auth::user();

        // Check permission
        if ($user->role === 'district_user' && $lot->tuman_id !== $user->tuman_id) {
            abort(403);
        }

        // Get messages with filters
        $query = LotMessage::where('lot_id', $lot->id)
            ->with('user')
            ->orderBy('created_at', 'desc');

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $messages = $query->paginate(20);

        // Statistics
        $stats = [
            'total' => LotMessage::where('lot_id', $lot->id)->count(),
            'pending' => LotMessage::where('lot_id', $lot->id)
                ->where('status', 'pending')
                ->count(),
            'read' => LotMessage::where('lot_id', $lot->id)
                ->where('status', 'read')
                ->count(),
            'replied' => LotMessage::where('lot_id', $lot->id)
                ->where('status', 'replied')
                ->count(),
        ];

        return view('analytics.lot-messages', compact('lot', 'messages', 'stats'));
    }

    /**
     * View login history
     */
    public function loginHistory(Request $request)
    {
        $user = Auth::user();

        // Only admin can view all login history
        if ($user->role !== 'admin') {
            abort(403);
        }

        $query = LoginHistory::with('user')
            ->orderBy('login_at', 'desc');

        // User filter
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('login_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('login_at', '<=', $request->date_to);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loginHistories = $query->paginate(50);

        // Statistics
        $stats = [
            'total_logins' => LoginHistory::count(),
            'active_sessions' => LoginHistory::where('status', 'active')->count(),
            'today_logins' => LoginHistory::whereDate('login_at', today())->count(),
            'unique_users_today' => LoginHistory::whereDate('login_at', today())
                ->distinct('user_id')
                ->count('user_id'),
        ];

        // Device breakdown
        $deviceStats = LoginHistory::select('device', DB::raw('count(*) as count'))
            ->groupBy('device')
            ->get();

        // Browser breakdown
        $browserStats = LoginHistory::select('browser', DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->get();

        // Most active users
        $activeUsers = LoginHistory::select('user_id', DB::raw('count(*) as login_count'))
            ->with('user')
            ->groupBy('user_id')
            ->orderBy('login_count', 'desc')
            ->limit(10)
            ->get();

        return view('analytics.login-history', compact(
            'loginHistories',
            'stats',
            'deviceStats',
            'browserStats',
            'activeUsers'
        ));
    }

    
public function markMessageAsRead(Request $request, $messageId)
{
    $message = LotMessage::findOrFail($messageId);
    
    // Check permission
    $user = Auth::user();
    if ($user->role === 'district_user' && $message->lot->tuman_id !== $user->tuman_id) {
        abort(403);
    }
    
    $message->markAsRead();
    
    return response()->json([
        'success' => true,
        'message' => 'Хабар ўқилган деб белгиланди'
    ]);
}

public function markMessageAsReplied(Request $request, $messageId)
{
    $message = LotMessage::findOrFail($messageId);
    
    // Check permission
    $user = Auth::user();
    if ($user->role === 'district_user' && $message->lot->tuman_id !== $user->tuman_id) {
        abort(403);
    }
    
    $message->update([
        'status' => LotMessage::STATUS_REPLIED,
        'read_at' => $message->read_at ?? now()
    ]);
    
    return response()->json([
        'success' => true,
        'message' => 'Хабарга жавоб берилди деб белгиланди'
    ]);
}
}