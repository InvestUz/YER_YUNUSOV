<?php

namespace App\Http\Controllers;

use App\Models\Lot;
use App\Models\Tuman;
use App\Models\Mahalla;
use App\Models\PaymentSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
    private function getFilterOptions($user)
    {
        $query = Lot::query();

        if ($user->role === 'district_user' && $user->tuman_id) {
            $query->where('tuman_id', $user->tuman_id);
        }

        return [
            'zones' => $query->select('zone')
                ->distinct()
                ->whereNotNull('zone')
                ->pluck('zone')
                ->sort()
                ->values(),

            'master_plan_zones' => Lot::select('master_plan_zone')
                ->distinct()
                ->whereNotNull('master_plan_zone')
                ->pluck('master_plan_zone')
                ->sort()
                ->values(),

            'construction_types' => [
                'konservatsiya' => 'Konservatsiya',
                'konservatsiya_qisman' => 'Konservatsiya (qisman qizil chiziq)',
                'konservatsiya_qizil_chiziqda' => 'Konservatsiya (qisman qizil chiziqda)',
                'konservatsiya_qizil_chiziq' => 'Konservatsiya (qizil chiziq)',
                'konservatsiya_soqliqni' => 'Konservatsiya (Soq\'liqni saqlash muassasalari)',
                'konservatsiya_yashil' => 'Konservatsiya (yashil hudud)',
                'rekonstruksiya' => 'Rekonstruksiya',
                'rekonstruksiya_qisman' => 'Rekonstruksiya (qisman qizil chiziq)',
                'renovatsiya' => 'Renovatsiya',
                'renovatsiya_qisman' => 'Renovatsiya (qisman qizil chiziq)',
                'renovatsiya_yashil' => 'Renovatsiya (yashil hudud)',
                'renovatsiya_yashil_qisman' => 'Renovatsiya (yashil hudud, qisman qizil chiziq)',
                'uy_joy' => 'уй-жой',
                'empty' => '(Пустые)'
            ],

            'object_types' => [
                'avtoturargoh' => 'автотурарғоҳ ва электр',
                'boshqa' => 'бошқа',
                'kop_qavatli' => 'кўп қаватли турар жой',
                'logistika' => 'логистика маркази, куртка',
                'maktab' => 'мактаб ва мактабгача таълим',
                'savdo' => 'савдо ва маиший хизмат'
            ],

            'payment_types' => [
                'auction_tanlash' => 'Аукцион/Танлов жунланди (12)',
                'muddatli' => 'муддатли',
                'muddatli_emas' => 'муддатли эмас',
                'nizoli' => 'Низоли',
                'savdo_tanlash' => 'Савдо/танлов натижаларини расмийла'
            ],

            'basis_types' => [
                'boshqa' => 'бошқа',
                'PF-135' => 'ПФ-135',
                'PF-153' => 'ПФ-153',
                'PF-33' => 'ПФ-33',
                'PF-93' => 'ПФ-93'
            ],

            'auction_types' => [
                'yopiq' => 'Ёпиқ аукцион',
                'ochiq' => 'Очиқ аукцион'
            ],

            'lot_statuses' => [
                'buyurtmachi_roziligi' => 'Buyurtmachi roziligini kutish jarayonida',
                'ishtirokchi_roziligi' => 'Ishtirokchi roziligini kutish jarayonida (34)',
                'auction' => 'Аукцион/Танлов жунланди (12)',
                'vakticha_tuxtildi' => 'Вақтинча тухтатилди',
                'ishtirokchi_buyurtmachi' => 'Иштирокчи ва Бюртмачи келишуви ёкка',
                'akunlandi' => 'Лот акунланди (29)',
                'savdo' => 'Савдо/танлов натижаларини расмийла'
            ],

            'winner_types' => Lot::select('winner_type')
                ->distinct()
                ->whereNotNull('winner_type')
                ->pluck('winner_type')
                ->sort()
                ->values(),
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
        $validated = $request->validate([
            'lot_number' => 'required|string|max:255|unique:lots',
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

        $lot = Lot::create($validated);

        // Create payment schedule if installment payment
        if ($lot->payment_type === 'muddatli' && $lot->contract_signed && $lot->payment_period_months) {
            $this->createPaymentSchedule($lot);
        }

        return redirect()->route('lots.show', $lot)
            ->with('success', 'Лот муваффақиятли яратилди');
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
        if ($user->role === 'district_user' && $lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        // Load relationships
        $lot->load(['tuman', 'mahalla', 'paymentSchedules', 'distributions']);
        // Calculate payment statistics
        $paymentStats = [
            'total_amount' => $lot->sold_price ?? 0,
            'paid_amount' => $lot->paid_amount ?? 0,
            'transferred_amount' => $lot->transferred_amount ?? 0,
            'remaining_amount' => 0,
            'payment_progress' => 0,
            'overdue_amount' => 0,
        ];

        // Calculate remaining amount and progress
        $totalPaid = $paymentStats['paid_amount'] + $paymentStats['transferred_amount'];
        $paymentStats['remaining_amount'] = $paymentStats['total_amount'] - $totalPaid;


        if ($paymentStats['total_amount'] > 0) {
            $paymentStats['payment_progress'] = ($totalPaid / $paymentStats['total_amount']) * 100;
        }

        // Calculate installment payment statistics
        if ($lot->payment_type === 'muddatli' && $lot->contract_signed && $lot->paymentSchedules->count() > 0) {
            $schedules = $lot->paymentSchedules;

            $paymentStats['scheduled_total'] = $schedules->sum('planned_amount');
            $paymentStats['scheduled_paid'] = $schedules->sum('actual_amount');
            $paymentStats['scheduled_remaining'] = $paymentStats['scheduled_total'] - $paymentStats['scheduled_paid'];

            // Calculate overdue payments
            $overdueSchedules = $schedules->where('payment_date', '<=', now())
                ->filter(function ($schedule) {
                    return $schedule->actual_amount < $schedule->planned_amount;
                });

            $paymentStats['overdue_amount'] = $overdueSchedules->sum(function ($schedule) {
                return $schedule->planned_amount - $schedule->actual_amount;
            });

            $paymentStats['overdue_count'] = $overdueSchedules->count();
        }

        // Calculate distribution statistics
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

        // Calculate financial metrics
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

        // Net income = Davaktiv amount (final after all deductions)
        $financialMetrics['net_income'] = $lot->davaktiv_amount ?? 0;

        // Auction countdown (if auction hasn't happened yet)
        $auctionCountdown = null;
        if ($lot->auction_date && $lot->auction_date > now()) {
            $auctionCountdown = now()->diff($lot->auction_date);
        }

        return view('lots.show', compact(
            'lot',
            'paymentStats',
            'distributionStats',
            'financialMetrics',
            'auctionCountdown'
        ));
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
