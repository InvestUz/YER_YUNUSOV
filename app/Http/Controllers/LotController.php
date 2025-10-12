<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\LotImage;
use App\Models\LotLike;
use App\Models\LotMessage;
use App\Models\LotView;
use App\Models\Tuman;
use App\Models\Mahalla;
use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource with advanced filters.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query with district filtering
        $query = Lot::with(['tuman', 'mahalla']);

        if ($user->role === 'district_user' && $user->tuman_id) {
            $query->where('tuman_id', $user->tuman_id);
        }

        // ===== EXISTING BASIC FILTERS =====
        if ($request->filled('tuman_id')) {
            $query->where('tuman_id', $request->tuman_id);
        }

        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('contract_signed')) {
            $query->where('contract_signed', $request->contract_signed === '1');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('lot_number', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('winner_name', 'like', "%{$search}%")
                    ->orWhere('unique_number', 'like', "%{$search}%");
            });
        }

        // ===== ADVANCED FILTERS FROM IMAGES =====

        // 1. Боош режа бўйича жойлашув зонаси (Zone Filter)
        if ($request->filled('zones') && is_array($request->zones)) {
            $query->whereIn('zone', $request->zones);
        }

        // 2. Master Plan Zone Filter
        if ($request->filled('master_plan_zones') && is_array($request->master_plan_zones)) {
            $query->whereIn('master_plan_zone', $request->master_plan_zones);
        }

        // 3. Янги Ўзбекистон (Yangi Uzbekiston Filter)
        if ($request->filled('yangi_uzbekiston')) {
            $query->where('yangi_uzbekiston', $request->yangi_uzbekiston === '1');
        }

        // 4. Конструксия фильтры (Construction/Reconstruction Types)
        if ($request->filled('construction_types') && is_array($request->construction_types)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->construction_types as $type) {
                    switch ($type) {
                        case 'konservatsiya':
                            $q->orWhere('lot_status', 'like', '%konservatsiya%');
                            break;
                        case 'konservatsiya_qisman':
                            $q->orWhere('lot_status', 'konservatsiya_qisman');
                            break;
                        case 'konservatsiya_qizil_chiziqda':
                            $q->orWhere('lot_status', 'konservatsiya_qizil_chiziqda');
                            break;
                        case 'rekonstruksiya':
                            $q->orWhere('lot_status', 'like', '%rekonstruksiya%');
                            break;
                        case 'renovatsiya':
                            $q->orWhere('lot_status', 'like', '%renovatsiya%');
                            break;
                        case 'uy_joy':
                            $q->orWhere('object_type', 'like', '%uy-joy%');
                            break;
                        case 'empty':
                            $q->orWhereNull('lot_status');
                            break;
                    }
                }
            });
        }

        // 5. Қурилишга рухсат берилган объект тури (Object Type Filter)
        if ($request->filled('object_types') && is_array($request->object_types)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->object_types as $type) {
                    switch ($type) {
                        case 'avtoturargoh':
                            $q->orWhere('object_type', 'like', '%avtoturargoh%');
                            break;
                        case 'boshqa':
                            $q->orWhere('object_type', 'like', '%boshqa%');
                            break;
                        case 'kop_qavatli':
                            $q->orWhere('object_type', 'like', '%ko\'p qavatli%')
                                ->orWhere('object_type', 'like', '%turar joy%');
                            break;
                        case 'logistika':
                            $q->orWhere('object_type', 'like', '%logistika%');
                            break;
                        case 'maktab':
                            $q->orWhere('object_type', 'like', '%maktab%');
                            break;
                        case 'savdo':
                            $q->orWhere('object_type', 'like', '%savdo%')
                                ->orWhere('object_type', 'like', '%maishiy%');
                            break;
                    }
                }
            });
        }

        // 6. Тўлов тури Extended (Payment Type Extended)
        if ($request->filled('payment_types_extended') && is_array($request->payment_types_extended)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->payment_types_extended as $type) {
                    switch ($type) {
                        case 'auction_tanlash':
                            $q->orWhere('auction_type', 'auction');
                            break;
                        case 'muddatli':
                            $q->orWhere('payment_type', 'muddatli');
                            break;
                        case 'muddatli_emas':
                            $q->orWhere('payment_type', 'muddatli_emas');
                            break;
                        case 'nizoli':
                            $q->orWhere('lot_status', 'nizoli');
                            break;
                        case 'savdo_tanlash':
                            $q->orWhere('auction_type', 'savdo');
                            break;
                    }
                }
            });
        }

        // 7. Асос (Basis Filter - PF Numbers)
        if ($request->filled('basis_types') && is_array($request->basis_types)) {
            $query->whereIn('basis', $request->basis_types);
        }

        // 8. Ўтказиш тури (Auction Type - Ochiq/Yopiq)
        if ($request->filled('auction_types') && is_array($request->auction_types)) {
            $query->whereIn('auction_type', $request->auction_types);
        }

        // 9. Лот ҳолати Extended (Lot Status Extended)
        if ($request->filled('lot_statuses') && is_array($request->lot_statuses)) {
            $query->whereIn('lot_status', $request->lot_statuses);
        }

        // 10. Шартнома тузганлиги (Contract Signed Status)
        if ($request->filled('contract_statuses') && is_array($request->contract_statuses)) {
            foreach ($request->contract_statuses as $status) {
                if ($status === 'signed') {
                    $query->where('contract_signed', true);
                } elseif ($status === 'not_signed') {
                    $query->where('contract_signed', false);
                } elseif ($status === 'with_date') {
                    $query->where('contract_signed', true)
                        ->whereNotNull('contract_date');
                }
            }
        }

        // 11. Ғолиб тури (Winner Type Filter)
        if ($request->filled('winner_types') && is_array($request->winner_types)) {
            $query->whereIn('winner_type', $request->winner_types);
        }

        // 12. Date Range Filters
        if ($request->filled('auction_date_from')) {
            $query->where('auction_date', '>=', $request->auction_date_from);
        }
        if ($request->filled('auction_date_to')) {
            $query->where('auction_date', '<=', $request->auction_date_to);
        }

        // 13. Price Range Filters
        if ($request->filled('price_from')) {
            $query->where('sold_price', '>=', $request->price_from);
        }
        if ($request->filled('price_to')) {
            $query->where('sold_price', '<=', $request->price_to);
        }

        // 14. Land Area Range
        if ($request->filled('land_area_from')) {
            $query->where('land_area', '>=', $request->land_area_from);
        }
        if ($request->filled('land_area_to')) {
            $query->where('land_area', '<=', $request->land_area_to);
        }

        // 15. Mahalla Filter
        if ($request->filled('mahalla_id')) {
            $query->where('mahalla_id', $request->mahalla_id);
        }

        // ===== SORTING =====
        $sortField = $request->get('sort', 'auction_date');
        $sortDirection = $request->get('direction', 'desc');

        // Custom sorting logic
        if ($sortField === 'tuman_name') {
            $query->join('tumans', 'lots.tuman_id', '=', 'tumans.id')
                ->orderBy('tumans.name_uz', $sortDirection)
                ->select('lots.*');
        } elseif ($sortField === 'mahalla_name') {
            $query->leftJoin('mahallas', 'lots.mahalla_id', '=', 'mahallas.id')
                ->orderBy('mahallas.name_uz', $sortDirection)
                ->select('lots.*');
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        // Paginate
        $perPage = $request->get('per_page', 20);
        $lots = $query->paginate($perPage)->withQueryString();

        // Get filter options
        $tumans = $user->role === 'admin'
            ? Tuman::all()
            : Tuman::where('id', $user->tuman_id)->get();

        $mahallas = $request->filled('tuman_id')
            ? Mahalla::where('tuman_id', $request->tuman_id)->get()
            : [];

        // Get unique filter values
        $filterOptions = $this->getFilterOptions($user);

        return view('lots.index', compact('lots', 'tumans', 'mahallas', 'user', 'filterOptions'));
    }

    /**
     * Get all available filter options
     */
    /**
     * Get all available filter options
     */
    private function getFilterOptions($user)
    {
        $query = Lot::query();

        // Filter by user's district if not admin
        if ($user->role === 'district_user' && $user->tuman_id) {
            $query->where('tuman_id', $user->tuman_id);
        }

        return [
            // Zones
            'zones' => $query->clone()->distinct()->pluck('zone')->filter()->sort()->values()->toArray(),

            // Master Plan Zones
            'master_plan_zones' => $query->clone()->distinct()->pluck('master_plan_zone')->filter()->sort()->values()->toArray(),

            // Construction Types
            'construction_types' => [
                'konservatsiya' => 'Консервация',
                'konservatsiya_qisman' => 'Қисман консервация',
                'konservatsiya_qizil_chiziqda' => 'Қизил чизиқда',
                'rekonstruksiya' => 'Реконструксия',
                'renovatsiya' => 'Реновация',
                'uy_joy' => 'Уй-жой',
                'empty' => 'Белгиланмаган',
            ],

            // Object Types
            'object_types' => [
                'avtoturargoh' => 'Автотураргоҳ',
                'boshqa' => 'Бошқа',
                'kop_qavatli' => 'Кўп қаватли',
                'logistika' => 'Логистика',
                'maktab' => 'Мактаб',
                'savdo' => 'Савдо-маиший',
            ],

            // Payment Types Extended
            'payment_types' => [
                'auction_tanlash' => 'Аукцион танлов',
                'muddatli' => 'Муддатли',
                'muddatli_emas' => 'Муддатли эмас',
                'nizoli' => 'Низоли',
                'savdo_tanlash' => 'Савдо танлов',
            ],

            // Basis Types (PF Numbers)
            'basis_types' => $query->clone()->distinct()->pluck('basis')->filter()->sort()->values()->toArray(),

            // Auction Types
            'auction_types' => [
                'ochiq' => 'Очиқ',
                'yopiq' => 'Ёпиқ',
                'savdo' => 'Савдо',
                'auction' => 'Аукцион',
            ],

            // Lot Statuses
            'lot_statuses' => $query->clone()->distinct()->pluck('lot_status')->filter()->sort()->values()->toArray(),

            // Winner Types
            'winner_types' => $query->clone()->distinct()->pluck('winner_type')->filter()->sort()->values()->toArray(),
        ];
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        $tumans = $user->role === 'admin'
            ? Tuman::all()
            : Tuman::where('id', $user->tuman_id)->get();

        $mahallas = [];

        return view('lots.create', compact('tumans', 'mahallas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            // Section 1: КА1726294005/2-1
            'unique_number' => 'required|string|max:255|unique:lots,unique_number',
            'land_area' => 'required|numeric|min:0',
            'buyuda' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'zone' => 'nullable|string|max:255',
            'master_plan_zone' => 'nullable|string|max:255',
            'dokanichsi' => 'nullable|string|max:255',
            'estimated_price' => 'nullable|numeric|min:0',
            'lot_status' => 'nullable|string|max:255',
            'kurinali' => 'nullable|string|in:ha,yoq',

            // Section 2: Аукцион маълумотлари
            'lot_number' => 'required|string|max:255|unique:lots,lot_number',
            'auction_buyuda' => 'nullable|string|max:255',
            'land_right_type' => 'nullable|string|max:255',
            'lot_status_auction' => 'nullable|string|max:255',
            'auction_type' => 'nullable|string|in:ochiq,yopiq',
            'basis' => 'nullable|string|max:500',
            'auction_date' => 'nullable|date',
            'winner_name' => 'nullable|string|max:255',
            'sold_price' => 'nullable|numeric|min:0',
            'sold_reference' => 'nullable|string|max:500',
            'status' => 'nullable|string|max:255',
            'property_accepted' => 'nullable|boolean',
            'visible' => 'nullable|boolean',
            'inn' => 'nullable|string|max:20',
            'jshshir' => 'nullable|string|max:20',
            'kurastin_kerak' => 'nullable|string|max:255',

            // Section 3: Шартнома шартлари
            'contract_buyuda' => 'nullable|string|max:255',
            'contract_sold_price' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|string|in:muddatli,muddatsiz',
            'distribution_conditions' => 'nullable|string|max:2000',

            // Section 4: Тўлов таксимоти
            'distribution_buyuda' => 'nullable|string|max:255',
            'completed_payments' => 'nullable|numeric|min:0',
            'local_budget' => 'nullable|numeric|min:0',
            'development_fund' => 'nullable|numeric|min:0',
            'new_uzbekistan' => 'nullable|numeric|min:0',
            'district_authority' => 'nullable|numeric|min:0',

            // Images
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max

            // Payment schedules
            'payment_schedules' => 'nullable|array',
            'payment_schedules.*.payment_date' => 'required_with:payment_schedules|date',
            'payment_schedules.*.planned_amount' => 'required_with:payment_schedules|numeric|min:0',
            'payment_schedules.*.actual_amount' => 'nullable|numeric|min:0',
        ], [
            'unique_number.required' => 'Уникал рақамни киритинг',
            'unique_number.unique' => 'Бу уникал рақам аллақачон мавжуд',
            'land_area.required' => 'Ер майдонини киритинг',
            'lot_number.required' => 'Лот рақамини киритинг',
            'lot_number.unique' => 'Бу лот рақами аллақачон мавжуд',
            'images.*.image' => 'Файл расм форматида бўлиши керак',
            'images.*.max' => 'Расм ҳажми 5MB дан ошмаслиги керак',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Create the lot
            $lot = Lot::create([
                // Section 1
                'unique_number' => $request->unique_number,
                'land_area' => $request->land_area,
                'buyuda' => $request->buyuda,
                'address' => $request->address,
                'zone' => $request->zone,
                'master_plan_zone' => $request->master_plan_zone,
                'dokanichsi' => $request->dokanichsi,
                'estimated_price' => $request->estimated_price,
                'lot_status' => $request->lot_status,
                'kurinali' => $request->kurinali,

                // Section 2
                'lot_number' => $request->lot_number,
                'auction_buyuda' => $request->auction_buyuda,
                'land_right_type' => $request->land_right_type,
                'lot_status_auction' => $request->lot_status_auction,
                'auction_type' => $request->auction_type,
                'basis' => $request->basis,
                'auction_date' => $request->auction_date,
                'winner_name' => $request->winner_name,
                'sold_price' => $request->sold_price,
                'sold_reference' => $request->sold_reference,
                'status' => $request->status,
                'property_accepted' => $request->has('property_accepted'),
                'visible' => $request->input('visible', 1),
                'inn' => $request->inn,
                'jshshir' => $request->jshshir,
                'kurastin_kerak' => $request->kurastin_kerak,

                // Section 3
                'contract_buyuda' => $request->contract_buyuda,
                'contract_sold_price' => $request->contract_sold_price,
                'payment_type' => $request->payment_type,
                'distribution_conditions' => $request->distribution_conditions,

                // Section 4
                'distribution_buyuda' => $request->distribution_buyuda,
                'completed_payments' => $request->completed_payments,
                'local_budget' => $request->local_budget,
                'development_fund' => $request->development_fund,
                'new_uzbekistan' => $request->new_uzbekistan,
                'district_authority' => $request->district_authority,

                // Auth user
                'user_id' => auth()->id(),
                'tuman_id' => auth()->user()->tuman_id ?? null,
            ]);

            // Handle image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $image) {
                    $path = $image->store('lots/' . $lot->id, 'public');

                    LotImage::create([
                        'lot_id' => $lot->id,
                        'url' => Storage::url($path),
                        'path' => $path,
                        'is_primary' => $index === 0, // First image is primary
                        'order' => $index,
                    ]);
                }
            }

            // Handle payment schedules (if payment type is muddatli)
            if ($request->payment_type === 'muddatli' && $request->has('payment_schedules')) {
                foreach ($request->payment_schedules as $schedule) {
                    if (!empty($schedule['payment_date'])) {
                        PaymentSchedule::create([
                            'lot_id' => $lot->id,
                            'payment_date' => $schedule['payment_date'],
                            'planned_amount' => $schedule['planned_amount'] ?? 0,
                            'actual_amount' => $schedule['actual_amount'] ?? 0,
                        ]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('lots.show', $lot)
                ->with('success', 'Лот муваффақиятли қўшилди');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Хатолик юз берди: ' . $e->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */
    public function show(Lot $lot)
    {
        $user = Auth::user();

        // Check access
        if ($user && $user->role === 'district_user' && $lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        // Track view with detailed information
        $this->trackView($lot, request());

        // Extract coordinates if missing
        $lot->extractCoordinatesFromUrl();

        // Load relationships
        $lot->load(['tuman', 'mahalla', 'paymentSchedules', 'distributions', 'images']);

        // Get unique views count
        $uniqueViewsCount = LotView::where('lot_id', $lot->id)
            ->distinct('ip_address')
            ->count('ip_address');

        // Get total views count
        $totalViewsCount = LotView::where('lot_id', $lot->id)->count();

        // Get messages count
        $messagesCount = LotMessage::where('lot_id', $lot->id)->count();
        $unreadMessagesCount = LotMessage::where('lot_id', $lot->id)
            ->where('status', 'pending')
            ->count();

        // Get likes count
        $likesCount = LotLike::where('lot_id', $lot->id)->count();

        // Check if current user/IP has liked
        $hasLiked = false;
        if ($user) {
            $hasLiked = LotLike::where('lot_id', $lot->id)
                ->where('user_id', $user->id)
                ->exists();
        } else {
            $hasLiked = LotLike::where('lot_id', $lot->id)
                ->where('ip_address', request()->ip())
                ->whereNull('user_id')
                ->exists();
        }

        // Calculate payment statistics
        $paymentStats = [
            'total_amount' => $lot->sold_price ?? 0,
            'paid_amount' => $lot->paid_amount ?? 0,
            'transferred_amount' => $lot->transferred_amount ?? 0,
            'remaining_amount' => 0,
            'payment_progress' => 0,
            'overdue_amount' => 0,
        ];

        $totalPaid = $paymentStats['paid_amount'] + $paymentStats['transferred_amount'];
        $paymentStats['remaining_amount'] = $paymentStats['total_amount'] - $totalPaid;

        if ($paymentStats['total_amount'] > 0) {
            $paymentStats['payment_progress'] = ($totalPaid / $paymentStats['total_amount']) * 100;
        }

        // Rest of existing code...
        $distributionStats = [
            'local_budget' => 0,
            'development_fund' => 0,
            'new_uzbekistan' => 0,
            'district_authority' => 0,
            'total_distributed' => 0,
        ];

        if ($lot->distributions->count() > 0) {
            foreach ($lot->distributions as $dist) {
                $distributionStats[$dist->category] = $dist->allocated_amount;
                $distributionStats['total_distributed'] += $dist->allocated_amount;
            }
        }

        $financialMetrics = [
            'price_increase' => 0,
            'price_increase_percent' => 0,
            'price_per_hectare' => 0,
            'net_income' => 0,
        ];

        if ($lot->initial_price > 0 && $lot->sold_price > 0) {
            $financialMetrics['price_increase'] = $lot->sold_price - $lot->initial_price;
            $financialMetrics['price_increase_percent'] = (($lot->sold_price - $lot->initial_price) / $lot->initial_price) * 100;
        }

        if ($lot->land_area > 0 && $lot->sold_price > 0) {
            $financialMetrics['price_per_hectare'] = $lot->sold_price / $lot->land_area;
        }

        $financialMetrics['net_income'] = $lot->davaktiv_amount ?? 0;

        $auctionCountdown = null;
        if ($lot->auction_date && $lot->auction_date > now()) {
            $auctionCountdown = now()->diff($lot->auction_date);
        }

        return view('lots.show', compact(
            'lot',
            'paymentStats',
            'distributionStats',
            'financialMetrics',
            'auctionCountdown',
            'uniqueViewsCount',
            'totalViewsCount',
            'messagesCount',
            'unreadMessagesCount',
            'likesCount',
            'hasLiked'
        ));
    }

    private function trackView(Lot $lot, Request $request)
    {
        $userAgent = $request->userAgent();
        $parsed = LotView::parseUserAgent($userAgent);

        LotView::create([
            'lot_id' => $lot->id,
            'user_id' => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device' => $parsed['device'],
            'browser' => $parsed['browser'],
            'platform' => $parsed['platform'],
            'session_id' => session()->getId(),
            'viewed_at' => now()
        ]);

        // Update counter in lots table
        $lot->increment('views_count');
    }

    public function toggleLike(Lot $lot)
    {
        $user = Auth::user();
        $ipAddress = request()->ip();

        if ($user) {
            $like = LotLike::where('lot_id', $lot->id)
                ->where('user_id', $user->id)
                ->first();
        } else {
            $like = LotLike::where('lot_id', $lot->id)
                ->where('ip_address', $ipAddress)
                ->whereNull('user_id')
                ->first();
        }

        if ($like) {
            $like->delete();
            $lot->decrement('likes_count');
            return response()->json([
                'liked' => false,
                'count' => LotLike::where('lot_id', $lot->id)->count()
            ]);
        } else {
            LotLike::create([
                'lot_id' => $lot->id,
                'user_id' => $user ? $user->id : null,
                'ip_address' => $ipAddress,
                'liked_at' => now()
            ]);
            $lot->increment('likes_count');
            return response()->json([
                'liked' => true,
                'count' => LotLike::where('lot_id', $lot->id)->count()
            ]);
        }
    }

    public function sendMessage(Request $request, Lot $lot)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'message' => 'required|string|max:5000'
        ]);

        LotMessage::create([
            'lot_id' => $lot->id,
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'message' => $validated['message'],
            'ip_address' => $request->ip(),
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Хабар муваффақиятли юборилди'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lot $lot)
    {
        $user = Auth::user();

        // Check access
        if ($user->role === 'district_user' && $lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        $tumans = $user->role === 'admin'
            ? Tuman::all()
            : Tuman::where('id', $user->tuman_id)->get();

        $mahallas = Mahalla::where('tuman_id', $lot->tuman_id)->get();

        return view('lots.edit', compact('lot', 'tumans', 'mahallas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lot $lot)
    {
        $user = Auth::user();

        // Check access
        if ($user->role === 'district_user' && $lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        $validated = $request->validate([
            'lot_number' => 'required|string|max:255|unique:lots,lot_number,' . $lot->id,
            'tuman_id' => 'required|exists:tumans,id',
            'mahalla_id' => 'nullable|exists:mahallas,id',
            'address' => 'required|string',
            'unique_number' => 'nullable|string|max:255',
            'zone' => 'nullable|string|max:255',
            'latitude' => 'nullable|string|max:255',
            'longitude' => 'nullable|string|max:255',
            'location_url' => 'nullable|url',
            'master_plan_zone' => 'nullable|string|max:255',
            'yangi_uzbekiston' => 'boolean',
            'land_area' => 'required|numeric|min:0',
            'object_type' => 'nullable|string|max:255',
            'object_type_ru' => 'nullable|string|max:255',
            'construction_area' => 'nullable|numeric|min:0',
            'investment_amount' => 'nullable|numeric|min:0',
            'initial_price' => 'required|numeric|min:0',
            'auction_date' => 'required|date',
            'sold_price' => 'required|numeric|min:0',
            'winner_type' => 'nullable|string|max:255',
            'winner_name' => 'required|string|max:255',
            'winner_phone' => 'nullable|string|max:50',
            'payment_type' => 'required|in:muddatli,muddatli_emas',
            'basis' => 'nullable|string|max:255',
            'auction_type' => 'nullable|in:ochiq,yopiq',
            'lot_status' => 'nullable|string|max:255',
            'contract_signed' => 'boolean',
            'contract_date' => 'nullable|date',
            'contract_number' => 'nullable|string|max:255',
            'payment_period_months' => 'nullable|integer|min:1|max:60',
            'initial_payment' => 'nullable|numeric|min:0',
        ]);

        $validated['contract_signed'] = $request->has('contract_signed');
        $validated['yangi_uzbekiston'] = $request->has('yangi_uzbekiston');

        $oldPaymentType = $lot->payment_type;
        $oldContractSigned = $lot->contract_signed;

        $lot->update($validated);

        // Create or update payment schedule if needed
        if ($lot->payment_type === 'muddatli' && $lot->contract_signed && $lot->payment_period_months) {
            if ($oldPaymentType !== 'muddatli' || !$oldContractSigned) {
                // Delete old schedules and create new ones
                $lot->paymentSchedules()->delete();
                $this->createPaymentSchedule($lot);
            }
        }

        return redirect()->route('lots.show', $lot)
            ->with('success', 'Лот маълумотлари янгиланди');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lot $lot)
    {
        $user = Auth::user();

        // Only admin can delete
        if ($user->role !== 'admin') {
            abort(403, 'Рухсат йўқ');
        }

        $lot->delete();

        return redirect()->route('lots.index')
            ->with('success', 'Лот ўчирилди');
    }

    /**
     * Get mahallas by tuman (AJAX)
     */
    public function getMahallas(Request $request)
    {
        $mahallas = Mahalla::where('tuman_id', $request->tuman_id)
            ->select('id', 'name_uz')
            ->get();

        return response()->json($mahallas);
    }

    /**
     * Create payment schedule for installment lots
     */
    private function createPaymentSchedule(Lot $lot)
    {
        if (!$lot->payment_period_months || !$lot->contract_date) {
            return;
        }

        $remainingAmount = $lot->sold_price - ($lot->initial_payment ?? 0);
        $monthlyPayment = $remainingAmount / $lot->payment_period_months;

        $startDate = \Carbon\Carbon::parse($lot->contract_date);

        for ($i = 1; $i <= $lot->payment_period_months; $i++) {
            PaymentSchedule::create([
                'lot_id' => $lot->id,
                'payment_number' => $i,
                'payment_date' => $startDate->copy()->addMonths($i),
                'planned_amount' => $monthlyPayment,
                'actual_amount' => 0,
                'status' => 'pending',
            ]);
        }
    }
}
