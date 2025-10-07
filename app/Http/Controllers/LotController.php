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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Base query with district filtering
        $query = Lot::with(['tuman', 'mahalla']);

        if ($user->role === 'district_user' && $user->tuman_id) {
            $query->where('tuman_id', $user->tuman_id);
        }

        // Apply filters
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
            $query->where(function($q) use ($search) {
                $q->where('lot_number', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('winner_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'auction_date');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginate
        $lots = $query->paginate(20)->withQueryString();

        // Get tumans for filter
        $tumans = $user->role === 'admin'
            ? Tuman::all()
            : Tuman::where('id', $user->tuman_id)->get();

        return view('lots.index', compact('lots', 'tumans', 'user'));
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
            'mahalla_id' => 'required|exists:mahallas,id',
            'address' => 'required|string',
            'land_area' => 'required|numeric|min:0',
            'initial_price' => 'required|numeric|min:0',
            'sold_price' => 'required|numeric|min:0',
            'auction_date' => 'required|date',
            'winner_name' => 'required|string|max:255',
            'winner_phone' => 'nullable|string|max:50',
            'winner_passport' => 'nullable|string|max:50',
            'payment_type' => 'required|in:muddatli,muddatli_emas',
            'contract_signed' => 'boolean',
            'contract_date' => 'nullable|date',
            'contract_number' => 'nullable|string|max:255',
            'payment_period_months' => 'nullable|integer|min:1|max:60',
            'initial_payment' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['contract_signed'] = $request->has('contract_signed');

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
    public function show(Lot $lot)
    {
        $user = Auth::user();

        // Check access
        if ($user->role === 'district_user' && $lot->tuman_id !== $user->tuman_id) {
            abort(403, 'Рухсат йўқ');
        }

        $lot->load(['tuman', 'mahalla', 'paymentSchedules']);

        // Calculate payment statistics
        $paymentStats = [
            'total_amount' => $lot->sold_price,
            'initial_payment' => $lot->initial_payment ?? 0,
            'remaining_amount' => 0,
            'paid_amount' => 0,
            'overdue_amount' => 0,
        ];

        if ($lot->payment_type === 'muddatli' && $lot->contract_signed) {
            $schedules = $lot->paymentSchedules;
            $paymentStats['remaining_amount'] = $lot->sold_price - $lot->initial_payment;
            $paymentStats['paid_amount'] = $schedules->sum('actual_amount');

            $overdueSchedules = $schedules->where('payment_date', '<=', now())
                ->where('status', '!=', 'paid');
            $paymentStats['overdue_amount'] = $overdueSchedules->sum('planned_amount')
                - $overdueSchedules->sum('actual_amount');
        }

        return view('lots.show', compact('lot', 'paymentStats'));
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
            'mahalla_id' => 'required|exists:mahallas,id',
            'address' => 'required|string',
            'land_area' => 'required|numeric|min:0',
            'initial_price' => 'required|numeric|min:0',
            'sold_price' => 'required|numeric|min:0',
            'auction_date' => 'required|date',
            'winner_name' => 'required|string|max:255',
            'winner_phone' => 'nullable|string|max:50',
            'winner_passport' => 'nullable|string|max:50',
            'payment_type' => 'required|in:muddatli,muddatli_emas',
            'contract_signed' => 'boolean',
            'contract_date' => 'nullable|date',
            'contract_number' => 'nullable|string|max:255',
            'payment_period_months' => 'nullable|integer|min:1|max:60',
            'initial_payment' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['contract_signed'] = $request->has('contract_signed');

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
