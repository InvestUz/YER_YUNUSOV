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

        // 1. Zone Filter
        if ($request->filled('zones') && is_array($request->zones)) {
            $query->whereIn('zone', $request->zones);
        }

        // 2. Master Plan Zone Filter
        if ($request->filled('master_plan_zones') && is_array($request->master_plan_zones)) {
            $query->whereIn('master_plan_zone', $request->master_plan_zones);
        }

        // 3. Yangi Uzbekiston Filter
        if ($request->filled('yangi_uzbekiston')) {
            $query->where('yangi_uzbekiston', $request->yangi_uzbekiston === '1');
        }

        // 4. Construction Types
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

        // 5. Object Type Filter
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

        // 6. Payment Type Extended
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

        // 7. Basis Filter
        if ($request->filled('basis_types') && is_array($request->basis_types)) {
            $query->whereIn('basis', $request->basis_types);
        }

        // 8. Auction Type
        if ($request->filled('auction_types') && is_array($request->auction_types)) {
            $query->whereIn('auction_type', $request->auction_types);
        }

        // 9. Lot Status Extended
        if ($request->filled('lot_statuses') && is_array($request->lot_statuses)) {
            $query->whereIn('lot_status', $request->lot_statuses);
        }

        // 10. Contract Signed Status
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

        // 11. Winner Type Filter
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

        // ===== CALCULATE TOTALS BEFORE PAGINATION =====
        // Clone query to calculate totals across ALL pages
        $totalStatsQuery = clone $query;

        $totalStats = [
            'total_area' => $totalStatsQuery->sum('land_area'),
            'total_initial_price' => $totalStatsQuery->sum('initial_price'),
            'total_sold_price' => $totalStatsQuery->sum('sold_price'),
        ];

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

        // ===== PAGINATE =====
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

        return view('lots.index', compact('lots', 'tumans', 'mahallas', 'user', 'filterOptions', 'totalStats'));
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
        $validated = $request->validate([
            'lot_number' => 'required|string|unique:lots,lot_number',
            'tuman_id' => 'required|exists:tumans,id',
            'mahalla_id' => 'nullable|exists:mahallas,id',
            'address' => 'nullable|string',
            'unique_number' => 'required|string',
            'land_area' => 'required|string',
            'zone' => 'nullable|string',
            'master_plan_zone' => 'nullable|string',
            'yangi_uzbekiston' => 'boolean',
            'auction_date' => 'nullable|date',
            'sold_price' => 'nullable|string',
            'winner_name' => 'nullable|string',
            'winner_type' => 'nullable|string',
            'huquqiy_subyekt' => 'nullable|string',
            'winner_phone' => 'nullable|string',
            'basis' => 'nullable|string',
            'auction_type' => 'nullable|in:ochiq,yopiq',
            'object_type' => 'nullable|string',
            'latitude' => 'nullable|string',
            'longitude' => 'nullable|string',
            'location_url' => 'nullable|url',
        ]);

        $validated['yangi_uzbekiston'] = $request->has('yangi_uzbekiston') ? 1 : 0;
        $validated['contract_signed'] = false;
        $validated['paid_amount'] = 0;
        $validated['transferred_amount'] = 0;
        $validated['discount'] = 0;

        $lot = Lot::create($validated);

        if ($lot->sold_price) {
            $lot->autoCalculate();
            $lot->save();
        }

        return redirect()->route('lots.show', $lot)
            ->with('success', 'Лот муваффақиятли яратилди!');
    }

    /**
     * Save individual section (partial lot data)
     */
    public function saveSection(Request $request)
    {
        $sectionNumber = (int) $request->input('section_number', 1);
        $lotId = $request->input('lot_id');

        // Validate based on section
        if ($sectionNumber === 1) {
            $validated = $request->validate([
                'lot_number' => 'required|string|unique:lots,lot_number,' . ($lotId ?? 'NULL'),
                'tuman_id' => 'required|exists:tumans,id',
                'mahalla_id' => 'nullable|exists:mahallas,id',
                'address' => 'nullable|string',
                'unique_number' => 'required|string',
                'land_area' => 'required|numeric|min:0',
                'zone' => 'nullable|string',
                'master_plan_zone' => 'nullable|string',
                'yangi_uzbekiston' => 'nullable|boolean',
            ]);

            $validated['wizard_step'] = 1;
            $validated['yangi_uzbekiston'] = $request->has('yangi_uzbekiston') ? 1 : 0;
        } elseif ($sectionNumber === 2) {
            $validated = $request->validate([
                'lot_id' => 'required|exists:lots,id',
                'auction_date' => 'nullable|date',
                'sold_price' => 'nullable|numeric',
                'payment_type' => 'nullable|in:muddatli,muddatli_emas',
                'winner_name' => 'nullable|string',
                'winner_type' => 'nullable|string',
                'winner_phone' => 'nullable|string',
                'basis' => 'nullable|string',
                'auction_type' => 'nullable|in:ochiq,yopiq',
            ]);

            $lotId = $validated['lot_id'];
            unset($validated['lot_id']);
            $validated['wizard_step'] = 2;
        } else { // Section 3
            $validated = $request->validate([
                'lot_id' => 'required|exists:lots,id',
                'object_type' => 'nullable|string',
                'latitude' => 'nullable|string',
                'longitude' => 'nullable|string',
                'location_url' => 'nullable|url',
            ]);

            $lotId = $validated['lot_id'];
            unset($validated['lot_id']);
            $validated['wizard_step'] = 3;
        }

        // Set default values for section 1
        if ($sectionNumber === 1) {
            $validated['contract_signed'] = false;
            $validated['paid_amount'] = 0;
            $validated['transferred_amount'] = 0;
            $validated['discount'] = 0;
        }

        // Create or update lot
        if ($lotId) {
            $lot = Lot::findOrFail($lotId);
            $lot->update($validated);
        } else {
            $lot = Lot::create($validated);
        }

        return response()->json([
            'success' => true,
            'message' => "Бўлим {$sectionNumber} сақланди",
            'lot_id' => $lot->id,
            'section' => $sectionNumber,
            'redirect_url' => $sectionNumber === 3 ? route('lots.edit', $lot->id) : null
        ]);
    }

    /**
     * Display the specified resource.
     */
    /**
     * Display the specified resource.
     */

    public function updateStatus(Request $request, Lot $lot)
    {
        $user = Auth::user();

        // Check access
        if ($user->role === 'district_user' && $lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        $validated = $request->validate([
            'lot_status' => 'required|string|max:255',
        ]);

        $lot->update([
            'lot_status' => $validated['lot_status']
        ]);

        return redirect()->back()
            ->with('success', 'Лот холати муваффақиятли янгиланди');
    }
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

        // Load relationships - FIXED: proper eager loading
        $lot->load([
            'tuman',
            'mahalla',
            'images',
            'contract.paymentSchedules' => function ($query) {
                $query->orderBy('payment_number');
            },
            'contract.distributions',
            'contract.additionalAgreements'
        ]);

        // ==================== DEBUG LOGGING START ====================
        \Log::info('=== LOT SHOW DEBUG START ===', [
            'lot_id' => $lot->id,
            'lot_number' => $lot->lot_number,
            'timestamp' => now()->toDateTimeString()
        ]);

        // 1. Basic Lot Information
        \Log::info('1. Basic Lot Info', [
            'payment_type' => $lot->payment_type,
            'auction_date' => $lot->auction_date ? $lot->auction_date->format('Y-m-d') : 'NULL',
            'sold_price' => $lot->sold_price,
            'initial_price' => $lot->initial_price,
            'land_area' => $lot->land_area,
        ]);

        // 2. Payment Amounts
        \Log::info('2. Payment Amounts', [
            'paid_amount' => $lot->paid_amount,
            'transferred_amount' => $lot->transferred_amount,
            'auction_fee' => $lot->auction_fee,
            'auction_expenses' => $lot->auction_expenses,
        ]);

        // 3. Discount Qualification Check
        $qualifiesForDiscount = $lot->qualifiesForDiscount();
        \Log::info('3. Discount Qualification', [
            'qualifies_for_discount' => $qualifiesForDiscount,
            'payment_type_check' => in_array($lot->payment_type, ['muddatsiz', 'muddatli_emas']),
            'has_auction_date' => !is_null($lot->auction_date),
            'auction_date_gt_cutoff' => $lot->auction_date ? $lot->auction_date->gt(\Carbon\Carbon::parse('2024-09-10')) : false,
        ]);

        // 4. Calculated Amounts
        \Log::info('4. Calculated Amounts', [
            'discount' => $lot->discount,
            'incoming_amount' => $lot->incoming_amount,
            'davaktiv_amount' => $lot->davaktiv_amount,
            'distributable_amount' => $lot->distributable_amount,
        ]);

        // 5. Manual Discount Calculation Test
        $manualDiscount = 0;
        $manualIncoming = 0;
        $manualDistributable = 0;

        if ($qualifiesForDiscount && $lot->paid_amount > 0) {
            $manualDiscount = $lot->paid_amount * 0.20;
            $manualIncoming = $lot->transferred_amount - $manualDiscount - ($lot->auction_fee ?? 0);
            $manualDistributable = $manualIncoming * 0.20;
        } else {
            $manualIncoming = $lot->transferred_amount - ($lot->auction_fee ?? 0);
            $manualDistributable = $manualIncoming;
        }

        \Log::info('5. Manual Calculation Test', [
            'manual_discount' => $manualDiscount,
            'manual_incoming' => $manualIncoming,
            'manual_distributable' => $manualDistributable,
            'matches_lot_discount' => $manualDiscount == $lot->discount,
            'matches_lot_incoming' => $manualIncoming == $lot->incoming_amount,
            'matches_lot_distributable' => $manualDistributable == $lot->distributable_amount,
        ]);

        // 6. Contract Information
        \Log::info('6. Contract Info', [
            'has_contract' => !is_null($lot->contract),
            'contract_id' => $lot->contract?->id,
            'contract_payment_type' => $lot->contract?->payment_type,
            'contract_amount' => $lot->contract?->contract_amount,
            'contract_paid_amount' => $lot->contract?->paid_amount,
        ]);

        // 7. Distribution Information
        if ($lot->contract && $lot->contract->distributions->count() > 0) {
            \Log::info('7. Distributions', [
                'distribution_count' => $lot->contract->distributions->count(),
                'distributions' => $lot->contract->distributions->map(function ($dist) {
                    return [
                        'category' => $dist->category,
                        'allocated_amount' => $dist->allocated_amount,
                        'percentage' => $dist->percentage,
                    ];
                })->toArray()
            ]);
        } else {
            \Log::info('7. Distributions', ['message' => 'No distributions found']);
        }

        // 8. Attribute Accessors Test
        \Log::info('8. Testing Attribute Accessors', [
            'getDiscountAttribute' => $lot->getAttribute('discount'),
            'getIncomingAmountAttribute' => $lot->getAttribute('incoming_amount'),
            'getDavaktivAmountAttribute' => $lot->getAttribute('davaktiv_amount'),
            'getDistributableAmountAttribute' => $lot->getAttribute('distributable_amount'),
        ]);

        // 9. Database Raw Values
        \Log::info('9. Database Raw Values', [
            'db_discount' => $lot->getAttributes()['discount'] ?? 'NOT_IN_DB',
            'db_incoming_amount' => $lot->getAttributes()['incoming_amount'] ?? 'NOT_IN_DB',
            'db_davaktiv_amount' => $lot->getAttributes()['davaktiv_amount'] ?? 'NOT_IN_DB',
            'db_paid_amount' => $lot->getAttributes()['paid_amount'] ?? 'NOT_IN_DB',
            'db_transferred_amount' => $lot->getAttributes()['transferred_amount'] ?? 'NOT_IN_DB',
        ]);

        // 10. Full Lot Attributes Dump
        \Log::info('10. Full Lot Attributes', [
            'all_attributes' => $lot->getAttributes()
        ]);

        \Log::info('=== LOT SHOW DEBUG END ===');
        // ==================== DEBUG LOGGING END ====================

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

        // Distribution statistics - FIXED: get from contract if exists
        $distributionStats = [
            'local_budget' => 0,
            'development_fund' => 0,
            'new_uzbekistan' => 0,
            'district_authority' => 0,
            'total_distributed' => 0,
        ];

        if ($lot->contract && $lot->contract->distributions->count() > 0) {
            foreach ($lot->contract->distributions as $dist) {
                if (isset($distributionStats[$dist->category])) {
                    $distributionStats[$dist->category] += $dist->allocated_amount;
                    $distributionStats['total_distributed'] += $dist->allocated_amount;
                }
            }
        }

        // Financial metrics
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

        // Auction countdown
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
            'unique_number' => 'required|string|max:255',
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

        // Auto-calculate financial fields
        if ($lot->sold_price) {
            $lot->autoCalculate();
            $lot->save();
        }

        // Create or update payment schedule if needed
        if ($lot->payment_type === 'muddatli' && $lot->contract_signed && $lot->payment_period_months) {
            if ($oldPaymentType !== 'muddatli' || !$oldContractSigned) {
                $lot->paymentSchedules()->delete();
                $this->createPaymentSchedule($lot);
            }
        }

        // Handle AJAX requests
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Маълумотлар янгиланди'
            ]);
        }

        return redirect()->route('lots.show', $lot)
            ->with('success', 'Лот маълумотлари муваффақиятли янгиланди');
    }
    /**
     * Create payment schedule for installment lots
     */

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
