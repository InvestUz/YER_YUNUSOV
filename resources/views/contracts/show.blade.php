@extends('layouts.app')

@section('title', 'Лот ' . $lot->lot_number)

@section('content')
<div class="min-h-screen bg-gray-50">

    {{-- ============================================ --}}
    {{-- SECTION 1: MESSAGES (Success/Error)         --}}
    {{-- ============================================ --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-6 py-3">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-6 py-3">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- ============================================ --}}
    {{-- SECTION 2: BREADCRUMB                        --}}
    {{-- ============================================ --}}
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-6 py-3">
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('lots.index') }}" class="hover:text-gray-900">Асосий</a>
                <span>/</span>
                <a href="{{ route('lots.index') }}" class="hover:text-gray-900">Лотлар</a>
                <span>/</span>
                <span class="text-gray-900 font-medium">{{ $lot->lot_number }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-6">

        {{-- ============================================ --}}
        {{-- SECTION 3: LOT HEADER INFO                  --}}
        {{-- ============================================ --}}
        <div class="bg-white shadow-sm rounded mb-4 p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">
                Лот № {{ $lot->lot_number }} - {{ $lot->address }}
            </h1>
            <div class="text-sm text-gray-600">
                <span>Туман: <strong>{{ $lot->tuman->name_uz ?? '-' }}</strong></span>
                <span class="ml-4">Ғолиб: <strong>{{ $lot->winner_name ?? '-' }}</strong></span>
                <span class="ml-4">Сотилган нарх: <strong>{{ number_format($lot->sold_price, 0, '.', ' ') }} сўм</strong></span>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- SECTION 4: CONTRACT STATUS CARD             --}}
        {{-- Show only if contract exists                --}}
        {{-- ============================================ --}}
        @if($lot->contract)
        <div class="bg-white shadow-sm rounded mb-4 p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-lg font-bold">Шартнома № {{ $lot->contract->contract_number }}</h2>
                    <p class="text-sm text-gray-600">Сана: {{ $lot->contract->contract_date->format('d.m.Y') }}</p>
                </div>
                <a href="{{ route('contracts.show', $lot->contract) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Батафсил
                </a>
            </div>

            {{-- Contract Summary Stats --}}
            <div class="grid grid-cols-3 gap-4">
                <div class="p-4 bg-blue-50 rounded text-center">
                    <p class="text-xs text-gray-600">Шартнома</p>
                    <p class="text-xl font-bold text-blue-700">{{ number_format($lot->contract->contract_amount, 0, '.', ' ') }}</p>
                </div>
                <div class="p-4 bg-green-50 rounded text-center">
                    <p class="text-xs text-gray-600">Тўланган</p>
                    <p class="text-xl font-bold text-green-700">{{ number_format($lot->contract->paid_amount, 0, '.', ' ') }}</p>
                </div>
                <div class="p-4 bg-orange-50 rounded text-center">
                    <p class="text-xs text-gray-600">Қолган</p>
                    <p class="text-xl font-bold text-orange-700">{{ number_format($lot->contract->remaining_amount, 0, '.', ' ') }}</p>
                </div>
            </div>
        </div>
        @else
            {{-- No Contract Yet --}}
            @if($lot->contract_signed)
            <div class="bg-yellow-50 border border-yellow-200 rounded mb-4 p-4">
                <p class="text-yellow-800">
                    <strong>Эслатма:</strong> Шартнома тузилган, лекин тизимда яратилмаган.
                    <a href="{{ route('contracts.create', ['lot_id' => $lot->id]) }}" class="underline ml-2">Шартнома яратиш →</a>
                </p>
            </div>
            @endif
        @endif

        {{-- ============================================ --}}
        {{-- SECTION 5: PAYMENT SCHEDULE TABLE (ГРАФИК)  --}}
        {{-- This is the main table from Image 1         --}}
        {{-- ============================================ --}}
        @if($lot->contract)
        <div class="bg-white shadow-sm rounded mb-4">

            {{-- Table Header with Add Button --}}
            <div class="bg-blue-600 text-white px-6 py-3 flex justify-between items-center">
                <h2 class="font-bold">ХИСОБОТ ДАВРИДА БАЖАРИЛИШИ БЕЛГИЛАНГАН МАЖБУРИЯТЛАР</h2>
                <button onclick="openAddScheduleModal()"
                        class="bg-white text-blue-600 px-3 py-1 rounded text-sm hover:bg-blue-50">
                    + Қўшиш
                </button>
            </div>

            {{-- Payment Schedule Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full text-xs border-collapse">
                    <thead>
                        {{-- First Header Row --}}
                        <tr class="bg-gray-100">
                            <th rowspan="2" class="px-2 py-2 border">№</th>
                            <th rowspan="2" class="px-2 py-2 border">сана</th>
                            <th colspan="3" class="px-2 py-2 border text-center">хисобот даврида (сўздиришув)</th>
                            <th colspan="3" class="px-2 py-2 border text-center">иш ўрини соли</th>
                            <th colspan="3" class="px-2 py-2 border text-center">хисобот саналари (усиб бўлиниш)</th>
                            <th colspan="3" class="px-2 py-2 border text-center">иш ўрини соли</th>
                            <th rowspan="2" class="px-2 py-2 border">Амаллар</th>
                        </tr>

                        {{-- Second Header Row --}}
                        <tr class="bg-gray-50">
                            <th class="px-2 py-1 border">график</th>
                            <th class="px-2 py-1 border">амалда</th>
                            <th class="px-2 py-1 border">+/-</th>
                            <th class="px-2 py-1 border">график</th>
                            <th class="px-2 py-1 border">амалда</th>
                            <th class="px-2 py-1 border">+/-</th>
                            <th class="px-2 py-1 border">график</th>
                            <th class="px-2 py-1 border">амалда</th>
                            <th class="px-2 py-1 border">+/-</th>
                            <th class="px-2 py-1 border">график</th>
                            <th class="px-2 py-1 border">амалда</th>
                            <th class="px-2 py-1 border">+/-</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($lot->contract->paymentSchedules->sortBy('planned_date') as $index => $schedule)
                        <tr class="hover:bg-gray-50">
                            {{-- Row Number --}}
                            <td class="px-2 py-2 border text-center">{{ $index + 1 }}</td>

                            {{-- Date --}}
                            <td class="px-2 py-2 border text-center">{{ $schedule->planned_date->format('d.m.Y') }}</td>

                            {{-- Column 1: хисобот даврида (сўздиришув) --}}
                            <td class="px-2 py-2 border text-right">{{ number_format($schedule->planned_amount, 0, '.', ' ') }}</td>
                            <td class="px-2 py-2 border text-right {{ $schedule->actual_amount > 0 ? 'bg-green-50 font-bold' : '' }}">
                                {{ number_format($schedule->actual_amount, 0, '.', ' ') }}
                            </td>
                            <td class="px-2 py-2 border text-right {{ $schedule->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $schedule->difference != 0 ? number_format($schedule->difference, 0, '.', ' ') : '0' }}
                            </td>

                            {{-- Column 2: иш ўрини соли (placeholder zeros) --}}
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>

                            {{-- Column 3: хисобот саналари (усиб бўлиниш) --}}
                            <td class="px-2 py-2 border text-right">{{ number_format($schedule->planned_amount, 0, '.', ' ') }}</td>
                            <td class="px-2 py-2 border text-right {{ $schedule->actual_amount > 0 ? 'bg-green-50 font-bold' : '' }}">
                                {{ number_format($schedule->actual_amount, 0, '.', ' ') }}
                            </td>
                            <td class="px-2 py-2 border text-right {{ $schedule->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $schedule->difference != 0 ? number_format($schedule->difference, 0, '.', ' ') : '0' }}
                            </td>

                            {{-- Column 4: иш ўрини соли (placeholder zeros) --}}
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>

                            {{-- Action Buttons --}}
                            <td class="px-2 py-2 border text-center whitespace-nowrap">
                                <button onclick="openPaymentModal({{ $schedule->id }}, '{{ $schedule->planned_date->format('Y-m-d') }}', {{ $schedule->planned_amount }})"
                                        class="text-blue-600 hover:underline mr-2">
                                    Тўлов
                                </button>
                                @if($schedule->actual_amount > 0)
                                <a href="{{ route('distributions.create', ['payment_schedule_id' => $schedule->id]) }}"
                                   class="text-green-600 hover:underline">
                                    Тақсимлаш
                                </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="14" class="px-4 py-8 text-center text-gray-500">
                                График яратилмаган. Юқоридаги "+ Қўшиш" тугмасини босинг.
                            </td>
                        </tr>
                        @endforelse

                        {{-- TOTAL ROW --}}
                        @if($lot->contract->paymentSchedules->count() > 0)
                        <tr class="bg-gray-100 font-bold">
                            <td colspan="2" class="px-2 py-2 border text-right">жами:</td>
                            <td class="px-2 py-2 border text-right">{{ number_format($lot->contract->paymentSchedules->sum('planned_amount'), 0, '.', ' ') }}</td>
                            <td class="px-2 py-2 border text-right">{{ number_format($lot->contract->paymentSchedules->sum('actual_amount'), 0, '.', ' ') }}</td>
                            <td class="px-2 py-2 border"></td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border text-right">{{ number_format($lot->contract->paymentSchedules->sum('planned_amount'), 0, '.', ' ') }}</td>
                            <td class="px-2 py-2 border text-right">{{ number_format($lot->contract->paymentSchedules->sum('actual_amount'), 0, '.', ' ') }}</td>
                            <td class="px-2 py-2 border"></td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border text-right text-gray-400">0</td>
                            <td class="px-2 py-2 border"></td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- SECTION 6: DISTRIBUTION TABLE (ТАҚСИМОТ)    --}}
        {{-- This is the table from Image 2              --}}
        {{-- ============================================ --}}
        <div class="bg-white shadow-sm rounded mb-4">
            <div class="bg-blue-600 text-white px-6 py-3">
                <h2 class="font-bold">ХИСОБОТ ДАВРИДА БАЖАРИЛИШИ БЕЛГИЛАНГАН МАҲБУРИЯТЛАР БЎЙИЧА ТАҚСИМОТ</h2>
            </div>

            <div class="p-6">
                {{-- Investment Plan Table --}}
                <table class="w-full text-xs border-collapse mb-6">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-2 py-2 border">хисобот саналари</th>
                            <th class="px-2 py-2 border">инвестиция режаси (млн. сўм)</th>
                            <th class="px-2 py-2 border">инвестиция режаси (минг дол.)</th>
                            <th class="px-2 py-2 border">яратилдиган иш ўрини сони</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-2 py-2 border">{{ now()->format('d.m.Y') }}</td>
                            <td class="px-2 py-2 border text-right">0</td>
                            <td class="px-2 py-2 border text-right">0</td>
                            <td class="px-2 py-2 border text-right">0</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Actual Performance --}}
                <h3 class="font-bold mb-3 text-sm">ХИСОБОТ ДАВРИДА АМАЛДА БАЖАРИЛГАН МАЖБУРИЯТЛАР</h3>

                <table class="w-full text-xs border-collapse mb-4">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-2 py-2 border">амалда инвестиция (млн. сўм)</th>
                            <th class="px-2 py-2 border">амалда инвестиция (минг дол.)</th>
                            <th class="px-2 py-2 border">яратилган иш ўрини сони</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-2 py-2 border text-right">
                                {{ number_format($lot->contract->distributions->sum('allocated_amount'), 0, '.', ' ') }}
                            </td>
                            <td class="px-2 py-2 border text-right">0</td>
                            <td class="px-2 py-2 border text-right">0</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mb-4">
                    <label class="block text-xs font-medium mb-1">Мониторинг далолатномаси:</label>
                    <textarea class="w-full border rounded px-2 py-1 text-xs" rows="2"></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-medium mb-1">Фотосуратлар:</label>
                    <input type="file" class="text-xs">
                </div>
            </div>

            <div class="px-6 py-3 bg-gray-50 border-t flex justify-end">
                <button class="bg-blue-600 text-white px-6 py-2 rounded text-sm hover:bg-blue-700">
                    Чиқариш
                </button>
            </div>
        </div>
        @endif

        {{-- ============================================ --}}
        {{-- SECTION 7: ACTION BUTTONS                   --}}
        {{-- ============================================ --}}
        <div class="flex gap-2">
            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
                @if(!$lot->contract && $lot->contract_signed)
                    <a href="{{ route('contracts.create', ['lot_id' => $lot->id]) }}"
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Шартнома яратиш
                    </a>
                @endif
                <a href="{{ route('lots.edit', $lot) }}"
                   class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Таҳрирлаш
                </a>
            @endif
            <button onclick="window.print()" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Чоп этиш
            </button>
        </div>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL 1: ADD SCHEDULE ITEM                  --}}
{{-- Opens when clicking "+ Қўшиш" button        --}}
{{-- ============================================ --}}
<div id="addScheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-bold">График қўшиш</h3>
            <button onclick="closeAddScheduleModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('contracts.add-schedule-item', $lot->contract ?? 0) }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-2">Тўлов санаси <span class="text-red-500">*</span></label>
                <input type="date" name="planned_date" required class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Тўлов суммаси (сўм) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="planned_amount" required class="w-full border rounded px-3 py-2" placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Муддат (deadline)</label>
                <input type="date" name="deadline_date" class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Қўшиш
                </button>
                <button type="button" onclick="closeAddScheduleModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
                    Бекор
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================ --}}
{{-- MODAL 2: RECORD PAYMENT                     --}}
{{-- Opens when clicking "Тўлов" button          --}}
{{-- ============================================ --}}
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-bold">Тўлов қўшиш</h3>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form id="paymentForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-2">Тўлов санаси <span class="text-red-500">*</span></label>
                <input type="date" name="actual_date" id="paymentDate" required class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Тўланган сумма (сўм) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="actual_amount" id="paymentAmount" required class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Изоҳ</label>
                <textarea name="note" rows="2" class="w-full border rounded px-3 py-2"></textarea>
            </div>

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    Сақлаш
                </button>
                <button type="button" onclick="closePaymentModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
                    Бекор
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================ --}}
{{-- JAVASCRIPT FUNCTIONS                        --}}
{{-- ============================================ --}}
<script>
// Open/Close Add Schedule Modal
function openAddScheduleModal() {
    document.getElementById('addScheduleModal').classList.remove('hidden');
}

function closeAddScheduleModal() {
    document.getElementById('addScheduleModal').classList.add('hidden');
}

// Open/Close Payment Modal
function openPaymentModal(scheduleId, plannedDate, plannedAmount) {
    const form = document.getElementById('paymentForm');
    form.action = `/payment-schedules/${scheduleId}`;
    document.getElementById('paymentDate').value = plannedDate;
    document.getElementById('paymentAmount').value = plannedAmount;
    document.getElementById('paymentModal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.add('hidden');
}
</script>

{{-- Hide buttons when printing --}}
<style>
@media print {
    button, .no-print {
        display: none !important;
    }
}
</style>
@endsection
