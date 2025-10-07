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
                    <h1 class="text-3xl font-bold text-gray-900">Свод - 2</h1>
                </div>
                <p class="text-gray-600">Тақсимот ва тўловлар бўйича маълумотлар</p>
            </div>
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors print:hidden">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Чоп этиш
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 no-print">
            <form method="GET" action="{{ route('monitoring.report2') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Дан</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '2023-01-01' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Гача</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Янги Ўзбекистон</label>
                    <select name="yangi_uzbekiston" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Барчаси</option>
                        <option value="1" {{ ($filters['yangi_uzbekiston'] ?? '') == '1' ? 'selected' : '' }}>Ҳа</option>
                        <option value="0" {{ ($filters['yangi_uzbekiston'] ?? '') == '0' ? 'selected' : '' }}>Йўқ</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Қидириш
                    </button>
                </div>
            </form>
        </div>

        <!-- Report Table -->
        <div class="bg-white rounded-xl shadow-md border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border-b border-r">Т/р</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border-b border-r">Ҳудудлар</th>
                            <th colspan="4" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b border-r bg-blue-50">Сотилган</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b border-r">Аукцион ҳақи</th>
                            <th colspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b border-r bg-yellow-50">Чегирма</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b border-r">Давактивга</th>
                            <th rowspan="2" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b border-r">Харажат</th>
                            <th colspan="4" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b border-r bg-green-50">Тақсимланган</th>
                            <th colspan="3" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b bg-purple-50">Келгусида</th>
                        </tr>
                        <tr class="bg-gray-50">
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">сони</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">майдони</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">бошл.</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">сотилган</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">сони</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">қиймати</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">М.бюджет</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">Жамғарма</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">Я.Ўзб</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">Туман</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">2025</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">2026</th>
                            <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b">2027</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($data['data'] as $index => $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-900 border-b border-r">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 border-b border-r">{{ $row['tuman'] }}</td>

                            <!-- Sold -->
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r">{{ $row['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r">{{ number_format($row['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r">{{ number_format($row['initial_price'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 font-medium text-right border-b border-r">{{ number_format($row['sold_price'], 1) }}</td>

                            <!-- Auction Fee -->
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r">{{ number_format($row['auction_fee'], 1) }}</td>

                            <!-- Discount -->
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-yellow-50">{{ $row['discount_count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-yellow-50">{{ number_format($row['discount_amount'], 1) }}</td>

                            <!-- Davaktiv & Expenses -->
                            <td class="px-3 py-3 text-sm text-gray-900 font-medium text-right border-b border-r">{{ number_format($row['davaktiv_amount'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r">{{ number_format($row['auction_expenses'], 1) }}</td>

                            <!-- Distributions -->
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-green-50">{{ number_format($row['distributions']['local_budget_allocated'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-green-50">{{ number_format($row['distributions']['development_fund_allocated'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-green-50">{{ number_format($row['distributions']['new_uzbekistan_allocated'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-green-50">{{ number_format($row['distributions']['district_authority_allocated'], 1) }}</td>

                            <!-- Future Payments -->
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-purple-50">{{ number_format($row['future_payments'][2025] ?? 0, 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-purple-50">{{ number_format($row['future_payments'][2026] ?? 0, 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b bg-purple-50">{{ number_format($row['future_payments'][2027] ?? 0, 1) }}</td>
                        </tr>
                        @endforeach

                        <!-- Totals Row -->
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="2" class="px-4 py-3 text-sm text-gray-900 border-t-2 border-r">жами:</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ $data['totals']['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['initial_price'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['sold_price'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['auction_fee'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ $data['totals']['discount_count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['discount_amount'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['davaktiv_amount'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['auction_expenses'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['distributions']['local_budget_allocated'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['distributions']['development_fund_allocated'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['distributions']['new_uzbekistan_allocated'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['distributions']['district_authority_allocated'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">0.0</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">0.0</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2">0.0</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notes -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800 print:hidden">
            <p><strong>Эслатма:</strong> Барча маблағлар миллиард сўм ҳисобида кўрсатилган. Тақсимот: Маҳаллий бюджет, Жамғарма, Янги Ўзбекистон дирекцияси, Туман ҳокимияти</p>
        </div>
@endsection
