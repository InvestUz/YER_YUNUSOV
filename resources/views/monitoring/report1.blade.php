@extends('layouts.app')

@section('title', 'Свод-1 - Toshkent Invest')

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white; }
        .print-table { font-size: 10px; }
    }

    .report-table th {
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }

    .report-table td {
        font-size: 12px;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="flex items-center justify-between mb-6 no-print">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('monitoring.index') }}" class="text-blue-600 hover:text-blue-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Свод - 1</h1>
        </div>
        <p class="text-gray-600">Сотилган ер участкалар умумий маълумотлари</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="exportToExcel()" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Excel юклаш
        </button>
        <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
            </svg>
            Чоп этиш
        </button>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 no-print">
    <form method="GET" action="{{ route('monitoring.report1') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Дан</label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '2023-01-01' }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Гача</label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? date('Y-m-d') }}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Субъект</label>
            <select name="subject_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                <option value="">Барчаси</option>
                <option value="legal" {{ ($filters['subject_type'] ?? '') === 'legal' ? 'selected' : '' }}>Юридик</option>
                <option value="individual" {{ ($filters['subject_type'] ?? '') === 'individual' ? 'selected' : '' }}>Жисмоний</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Зона</label>
            <select name="zone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                <option value="">Барчаси</option>
                <option value="1-зона" {{ ($filters['zone'] ?? '') === '1-зона' ? 'selected' : '' }}>1-зона</option>
                <option value="2-зона" {{ ($filters['zone'] ?? '') === '2-зона' ? 'selected' : '' }}>2-зона</option>
                <option value="3-зона" {{ ($filters['zone'] ?? '') === '3-зона' ? 'selected' : '' }}>3-зона</option>
                <option value="4-зона" {{ ($filters['zone'] ?? '') === '4-зона' ? 'selected' : '' }}>4-зона</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Бош режа</label>
            <select name="master_plan_zone" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                <option value="">Барчаси</option>
                <option value="Renovatsiya" {{ ($filters['master_plan_zone'] ?? '') === 'Renovatsiya' ? 'selected' : '' }}>Реновация</option>
                <option value="Konservatsiya" {{ ($filters['master_plan_zone'] ?? '') === 'Konservatsiya' ? 'selected' : '' }}>Консервация</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                Қидириш
            </button>
            <a href="{{ route('monitoring.report1') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-semibold">
                Тозалаш
            </a>
        </div>
    </form>
</div>

<!-- Report Table -->
<div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
    <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-300">
        <h3 class="text-base font-bold text-gray-800 uppercase tracking-wide">Сотилган ер участкалар бўйича маълумот</h3>
        <p class="text-xs text-gray-600 mt-1">Давр: {{ $filters['date_from'] ?? '2023-01-01' }} - {{ $filters['date_to'] ?? date('Y-m-d') }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full report-table print-table">
            <thead class="bg-gradient-to-r from-blue-700 to-blue-800">
                <tr>
                    <th rowspan="2" class="px-4 py-4 text-left text-white border-r-2 border-blue-600">Т/Р</th>
                    <th rowspan="2" class="px-4 py-4 text-left text-white border-r-2 border-blue-600">ҲУДУДЛАР</th>
                    <th colspan="4" class="px-4 py-3 text-center text-white border-r-2 border-blue-600">СОТИЛГАН ЕР УЧАСТКАЛАР</th>
                    <th colspan="4" class="px-4 py-3 text-center text-white border-r-2 border-blue-600">БИР ЙЎЛА ТЎЛАШ</th>
                    <th colspan="4" class="px-4 py-3 text-center text-white border-r-2 border-blue-600">БЎЛИБ ТЎЛАШ</th>
                    <th colspan="4" class="px-4 py-3 text-center text-white border-r-2 border-blue-600">РАСМИЙЛАШТИРИШДА</th>
                    <th colspan="2" class="px-4 py-3 text-center text-white">ҚАБУЛ ҚИЛИНМАГАН</th>
                </tr>
                <tr class="bg-gradient-to-r from-blue-700 to-blue-800">
                    @foreach(['сони', 'майдони (га)', 'бошл. (млрд)', 'сотилган (млрд)'] as $col)
                        <th class="px-3 py-3 text-center text-white border-r border-blue-600">{{ $col }}</th>
                    @endforeach
                    @foreach(['сони', 'майдони (га)', 'бошл. (млрд)', 'сотилган (млрд)'] as $col)
                        <th class="px-3 py-3 text-center text-white border-r border-blue-600">{{ $col }}</th>
                    @endforeach
                    @foreach(['сони', 'майдони (га)', 'бошл. (млрд)', 'сотилган (млрд)'] as $col)
                        <th class="px-3 py-3 text-center text-white border-r border-blue-600">{{ $col }}</th>
                    @endforeach
                    @foreach(['сони', 'майдони (га)', 'бошл. (млрд)', 'сотилган (млрд)'] as $col)
                        <th class="px-3 py-3 text-center text-white border-r border-blue-600">{{ $col }}</th>
                    @endforeach
                    <th class="px-3 py-3 text-center text-white border-r border-blue-600">сони</th>
                    <th class="px-3 py-3 text-center text-white">маблағ (млрд)</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($data['data'] as $index => $row)
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="px-4 py-4 text-sm font-bold text-gray-900 border-r border-gray-200">{{ $index + 1 }}</td>
                    <td class="px-4 py-4 text-sm font-semibold text-gray-900 border-r border-gray-200">{{ $row['tuman'] }}</td>

                    <!-- Total -->
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ $row['total']['count'] }}</td>
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ number_format($row['total']['area'], 2) }}</td>
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ number_format($row['total']['initial_price'], 1) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r-2 border-gray-300">{{ number_format($row['total']['sold_price'], 1) }}</td>

                    <!-- One Time -->
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ $row['one_time']['count'] }}</td>
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ number_format($row['one_time']['area'], 2) }}</td>
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ number_format($row['one_time']['initial_price'], 1) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r-2 border-gray-300">{{ number_format($row['one_time']['sold_price'], 1) }}</td>

                    <!-- Installment -->
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ $row['installment']['count'] }}</td>
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ number_format($row['installment']['area'], 2) }}</td>
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ number_format($row['installment']['initial_price'], 1) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r-2 border-gray-300">{{ number_format($row['installment']['sold_price'], 1) }}</td>

                    <!-- Under Contract -->
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ $row['under_contract']['count'] }}</td>
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ number_format($row['under_contract']['area'], 2) }}</td>
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ number_format($row['under_contract']['initial_price'], 1) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r-2 border-gray-300">{{ number_format($row['under_contract']['sold_price'], 1) }}</td>

                    <!-- Not Accepted -->
                    <td class="px-3 py-4 text-sm text-gray-700 text-center border-r border-gray-200">{{ $row['not_accepted']['count'] }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center">{{ number_format($row['not_accepted']['amount'], 1) }}</td>
                </tr>
                @endforeach

                <!-- Totals Row -->
                <tr class="bg-gradient-to-r from-gray-100 to-gray-200 font-bold border-t-4 border-blue-700">
                    <td colspan="2" class="px-4 py-4 text-sm font-bold text-gray-900 uppercase">ЖАМИ:</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ $data['totals']['total']['count'] }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ number_format($data['totals']['total']['area'], 2) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ number_format($data['totals']['total']['initial_price'], 1) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-blue-700 text-center border-r-2 border-gray-400">{{ number_format($data['totals']['total']['sold_price'], 1) }}</td>

                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ $data['totals']['one_time']['count'] }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ number_format($data['totals']['one_time']['area'], 2) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ number_format($data['totals']['one_time']['initial_price'], 1) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-blue-700 text-center border-r-2 border-gray-400">{{ number_format($data['totals']['one_time']['sold_price'], 1) }}</td>

                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ $data['totals']['installment']['count'] }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ number_format($data['totals']['installment']['area'], 2) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ number_format($data['totals']['installment']['initial_price'], 1) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-blue-700 text-center border-r-2 border-gray-400">{{ number_format($data['totals']['installment']['sold_price'], 1) }}</td>

                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ $data['totals']['under_contract']['count'] }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ number_format($data['totals']['under_contract']['area'], 2) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ number_format($data['totals']['under_contract']['initial_price'], 1) }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-blue-700 text-center border-r-2 border-gray-400">{{ number_format($data['totals']['under_contract']['sold_price'], 1) }}</td>

                    <td class="px-3 py-4 text-sm font-bold text-gray-900 text-center border-r border-gray-300">{{ $data['totals']['not_accepted']['count'] }}</td>
                    <td class="px-3 py-4 text-sm font-bold text-blue-700 text-center">{{ number_format($data['totals']['not_accepted']['amount'], 1) }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Notes -->
<div class="mt-6 bg-gray-50 border-2 border-gray-300 rounded-lg p-4 no-print">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <p class="text-sm font-semibold text-gray-900 mb-1">Эслатма:</p>
            <p class="text-sm text-gray-700">Барча маблағлар миллиард сўм ҳисобида кўрсатилган. Ҳисобот автоматик равишда янгиланади.</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    window.location.href = '{{ route("monitoring.report1") }}?export=excel&' + new URLSearchParams(new FormData(document.querySelector('form'))).toString();
}
</script>
@endpus
