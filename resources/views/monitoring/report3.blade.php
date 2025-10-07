<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Свод-3 - Toshkent Invest</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    @include('layouts.sidebar')

    <div class="ml-64 p-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('monitoring.index') }}" class="text-blue-600 hover:text-blue-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-gray-900">Свод - 3</h1>
                </div>
                <p class="text-gray-600">Бўлиб тўлаш шарти билан сотилган участкалар</p>
            </div>
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Чоп этиш
            </button>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 print:hidden">
            <form method="GET" action="{{ route('monitoring.report3') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Дан</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '2023-01-01' }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Гача</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ҳисобот санаси</label>
                    <input type="date" name="current_date" value="{{ $filters['current_date'] ?? date('Y-m-d') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Қидириш
                    </button>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 print:hidden">
            <div class="bg-blue-50 rounded-xl border border-blue-200 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-blue-900">Жами бўлиб тўлаш</span>
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-blue-900">{{ $data['totals']['total']['count'] }}</p>
                <p class="text-xs text-blue-700 mt-1">{{ number_format($data['totals']['total']['sold_price'], 1) }} млрд</p>
            </div>

            <div class="bg-green-50 rounded-xl border border-green-200 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-green-900">Тўлиқ тўланган</span>
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-green-900">{{ $data['totals']['fully_paid']['count'] }}</p>
                <p class="text-xs text-green-700 mt-1">{{ number_format($data['totals']['fully_paid']['sold_price'], 1) }} млрд</p>
            </div>

            <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-yellow-900">Назоратда</span>
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-yellow-900">{{ $data['totals']['under_monitoring']['count'] }}</p>
                <p class="text-xs text-yellow-700 mt-1">{{ number_format($data['totals']['under_monitoring']['sold_price'], 1) }} млрд</p>
            </div>

            <div class="bg-red-50 rounded-xl border border-red-200 p-6">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-red-900">Муддат ўтган</span>
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-red-900">{{ $data['totals']['overdue']['count'] }}</p>
                <p class="text-xs text-red-700 mt-1">{{ number_format($data['totals']['overdue']['percentage'], 1) }}% тўланган</p>
            </div>
        </div>

        <!-- Report Table -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border-b border-r">Т/р</th>
                            <th rowspan="2" class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border-b border-r">Ҳудудлар</th>
                            <th colspan="4" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b border-r bg-blue-50">Бўлиб тўлаш</th>
                            <th colspan="4" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b border-r bg-green-50">Тўлиқ тўланган</th>
                            <th colspan="4" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b border-r bg-yellow-50">Назоратда</th>
                            <th colspan="4" class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border-b bg-red-50">Гр.ортда қолган</th>
                        </tr>
                        <tr class="bg-gray-50">
                            @for($i = 0; $i < 4; $i++)
                                <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">сони</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">майдони</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b border-r">бошл.</th>
                                <th class="px-3 py-2 text-xs font-medium text-gray-600 border-b {{ $i < 3 ? 'border-r' : '' }}">сотилган</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($data['data'] as $index => $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-gray-900 border-b border-r">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 border-b border-r">{{ $row['tuman'] }}</td>

                            <!-- Total Installment -->
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r">{{ $row['total']['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r">{{ number_format($row['total']['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r">{{ number_format($row['total']['initial_price'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 font-medium text-right border-b border-r">{{ number_format($row['total']['sold_price'], 1) }}</td>

                            <!-- Fully Paid -->
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-green-50">{{ $row['fully_paid']['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-green-50">{{ number_format($row['fully_paid']['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-green-50">{{ number_format($row['fully_paid']['initial_price'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 font-medium text-right border-b border-r bg-green-50">{{ number_format($row['fully_paid']['sold_price'], 1) }}</td>

                            <!-- Under Monitoring -->
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-yellow-50">{{ $row['under_monitoring']['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-yellow-50">{{ number_format($row['under_monitoring']['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-yellow-50">{{ number_format($row['under_monitoring']['initial_price'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 font-medium text-right border-b border-r bg-yellow-50">{{ number_format($row['under_monitoring']['sold_price'], 1) }}</td>

                            <!-- Overdue -->
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-red-50">{{ $row['overdue']['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-red-50">{{ number_format($row['overdue']['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-600 text-right border-b border-r bg-red-50">{{ number_format($row['overdue']['planned_payment'], 1) }}</td>
                            <td class="px-3 py-3 text-sm border-b bg-red-50">
                                <div class="flex items-center justify-end gap-2">
                                    <span class="text-gray-900 font-medium">{{ number_format($row['overdue']['actual_payment'], 1) }}</span>
                                    <span class="text-xs px-2 py-1 rounded-full {{ $row['overdue']['percentage'] >= 80 ? 'bg-green-100 text-green-800' : ($row['overdue']['percentage'] >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ number_format($row['overdue']['percentage'], 1) }}%
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        <!-- Totals Row -->
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="2" class="px-4 py-3 text-sm text-gray-900 border-t-2">жами:</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ $data['totals']['total']['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['total']['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['total']['initial_price'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['total']['sold_price'], 1) }}</td>

                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ $data['totals']['fully_paid']['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['fully_paid']['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['fully_paid']['initial_price'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['fully_paid']['sold_price'], 1) }}</td>

                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ $data['totals']['under_monitoring']['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['under_monitoring']['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['under_monitoring']['initial_price'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['under_monitoring']['sold_price'], 1) }}</td>

                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ $data['totals']['overdue']['count'] }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['overdue']['area'], 2) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2 border-r">{{ number_format($data['totals']['overdue']['planned_payment'], 1) }}</td>
                            <td class="px-3 py-3 text-sm text-gray-900 text-right border-t-2">
                                {{ number_format($data['totals']['overdue']['actual_payment'], 1) }}
                                <span class="text-xs ml-2 px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                                    {{ number_format($data['totals']['overdue']['percentage'], 1) }}%
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notes -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-800 print:hidden">
            <p><strong>Эслатма:</strong> Барча маблағлар миллиард сўм ҳисобида кўрсатилган. Фоиз тўлов режаси бўйича амалда тўланган маблағни кўрсатади.</p>
        </div>
    </div>
</body>
</html>
