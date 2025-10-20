@extends('layouts.app')

@section('title', 'Детальная информация - Toshkent Invest')

@section('content')
<div class="min-h-screen bg-slate-50 py-6 px-4">
    <!-- Breadcrumb -->
    <div class="bg-white border-2 border-slate-400 px-5 py-3 mb-5 flex items-center gap-3 text-sm font-bold shadow-sm">
        <a href="{{ route('dashboard') }}" class="text-slate-700 hover:text-blue-700 hover:underline uppercase">БОШ САҲИФА</a>
        <span class="text-slate-400">›</span>
        @php
            // Determine which report based on current route
            $currentRoute = Route::currentRouteName();
            $reportName = 'СВОД-1';
            $reportRoute = 'monitoring.report1';

            if (str_contains($currentRoute, 'report2')) {
                $reportName = 'СВОД-2';
                $reportRoute = 'monitoring.report2';
            } elseif (str_contains($currentRoute, 'report3')) {
                $reportName = 'СВОД-3';
                $reportRoute = 'monitoring.report3';
            }
        @endphp
        <a href="{{ route($reportRoute) }}" class="text-slate-700 hover:text-blue-700 hover:underline uppercase">{{ $reportName }}</a>
        <span class="text-slate-400">›</span>
        <span class="text-slate-600 uppercase">{{ $categoryName }}</span>
    </div>

    <!-- Header -->
    <div class="bg-white border-2 border-slate-400 p-6 mb-5 shadow-sm">
        <h1 class="text-2xl font-black text-slate-800 uppercase border-b-4 border-blue-600 pb-3 mb-4">
            {{ $categoryName }}
        </h1>

        <!-- Filter Info -->
        <div class="flex flex-wrap gap-6 text-sm font-semibold">
            <div class="flex items-center gap-2">
                <span class="font-black text-slate-700">ҲУДУД:</span>
                <span class="text-slate-600">{{ $districtName }}</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="font-black text-slate-700">ДАВР:</span>
                <span class="text-slate-600">{{ $filters['date_from'] ?? '01.01.2023' }} - {{ $filters['date_to'] ?? date('d.m.Y') }}</span>
            </div>
            @if(!empty($filters['subject_type']))
            <div class="flex items-center gap-2">
                <span class="font-black text-slate-700">СУБЪЕКТ:</span>
                <span class="text-slate-600">{{ $filters['subject_type'] === 'legal' ? 'Юридик' : 'Жисмоний' }}</span>
            </div>
            @endif
            @if(!empty($filters['zone']))
            <div class="flex items-center gap-2">
                <span class="font-black text-slate-700">ЗОНА:</span>
                <span class="text-slate-600">{{ $filters['zone'] }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="bg-white border-2 border-slate-400 px-5 py-4 mb-5 flex items-center justify-between shadow-sm no-print">
        <div class="flex-1 max-w-md">
            <input type="text" id="searchInput"
                   placeholder="ҚИДИРИШ..."
                   onkeyup="filterTable()"
                   class="w-full px-4 py-2.5 border-2 border-slate-300 text-sm font-semibold focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
        </div>
        <div class="flex gap-3">
            <button onclick="exportToExcel()"
                    class="px-5 py-2.5 border-2 border-emerald-600 bg-emerald-50 text-emerald-700 text-sm font-bold uppercase hover:bg-emerald-600 hover:text-white transition shadow-sm">
                📊 EXCEL
            </button>
            <button onclick="window.print()"
                    class="px-5 py-2.5 border-2 border-blue-600 bg-blue-50 text-blue-700 text-sm font-bold uppercase hover:bg-blue-600 hover:text-white transition shadow-sm">
                🖨️ ЧОП ЭТИШ
            </button>
            <a href="{{ route($reportRoute) }}"
               class="px-5 py-2.5 border-2 border-slate-400 bg-slate-50 text-slate-700 text-sm font-bold uppercase hover:bg-slate-600 hover:text-white transition shadow-sm">
                ← ОРҚАГА
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white border-2 border-slate-400 overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            @if($lots->count() > 0)
            <table class="w-full border-collapse" id="lotsTable">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-700 to-slate-600">
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">№</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">УЧАСТКА РАҚАМИ</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">МАНЗИЛ</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">МАЙДОНИ (ГА)</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">ЗОНА</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">БОШЛАНҒИЧ НАРХ</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">СОТИЛГАН НАРХ</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">ТЎЛАШ</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">ЭГАСИ</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">СУБЪЕКТ</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">АУКЦИОН САНАСИ</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase border-r border-slate-500">ҲОЛАТИ</th>
                        <th class="px-4 py-3 text-left text-white text-xs font-black uppercase no-print">АМАЛЛАР</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- SUMMARY ROW - First row under headers -->
                    <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 border-t-4 border-b-4 border-blue-600">
                        <td colspan="1" class="px-4 py-4 text-left text-sm font-black text-slate-800 uppercase">
                            ЖАМИ:
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-lg font-black text-blue-700">{{ number_format($stats['count']) }}</div>
                            <div class="text-xs text-slate-600 font-semibold">дона</div>
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-slate-500 font-bold">-</td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-lg font-black text-emerald-700">{{ number_format($stats['total_area'], 2) }}</div>
                            <div class="text-xs text-slate-600 font-semibold">га</div>
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-slate-500 font-bold">-</td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-lg font-black text-amber-700">{{ number_format($stats['total_initial_price'], 1) }}</div>
                            <div class="text-xs text-slate-600 font-semibold">млрд</div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="text-lg font-black text-indigo-700">{{ number_format($stats['total_sold_price'], 1) }}</div>
                            <div class="text-xs text-slate-600 font-semibold">млрд</div>
                        </td>
                        <td class="px-4 py-4 text-center text-sm text-slate-500 font-bold">-</td>
                        <td class="px-4 py-4 text-center text-sm text-slate-500 font-bold">-</td>
                        <td class="px-4 py-4 text-center text-sm text-slate-500 font-bold">-</td>
                        <td class="px-4 py-4 text-center text-sm text-slate-500 font-bold">-</td>
                        <td class="px-4 py-4 text-center text-sm text-slate-500 font-bold">-</td>
                        <td class="px-4 py-4 text-center text-sm text-slate-500 font-bold no-print">-</td>
                    </tr>

                    <!-- DATA ROWS -->
                    @foreach($lots as $index => $lot)
                    <tr class="border-b border-slate-200 hover:bg-blue-50 transition">
                        <td class="px-4 py-3 text-sm font-semibold text-slate-600">
                            {{ ($lots->currentPage() - 1) * $lots->perPage() + $index + 1 }}
                        </td>
                        <td class="px-4 py-3 text-sm font-black text-blue-700">
                            {{ $lot->lot_number ?? 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-700 max-w-xs">
                            {{ $lot->address ?? 'Манзил кўрсатилмаган' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-800 text-right">
                            {{ number_format($lot->land_area, 4) }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-slate-600">
                            {{ $lot->zone ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-bold text-slate-700 text-right">
                            {{ number_format($lot->initial_price / 1000000, 1) }} <span class="text-xs text-slate-500">млн</span>
                        </td>
                        <td class="px-4 py-3 text-sm font-black text-emerald-700 text-right">
                            {{ number_format($lot->sold_price / 1000000, 1) }} <span class="text-xs text-slate-500">млн</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($lot->payment_type === 'muddatli_emas')
                                <span class="inline-block px-3 py-1 bg-emerald-50 border-2 border-emerald-600 text-xs font-black text-emerald-700 uppercase">
                                    БИР ЙЎЛА
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 bg-amber-50 border-2 border-amber-600 text-xs font-black text-amber-700 uppercase">
                                    МУДДАТЛИ
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm font-medium text-slate-700">
                            {{ $lot->winner_name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-slate-600">
                            {{ $lot->winner_type === 'legal' ? 'Юридик' : 'Жисмоний' }}
                        </td>
                        <td class="px-4 py-3 text-sm font-semibold text-slate-600">
                            {{ $lot->auction_date ? \Carbon\Carbon::parse($lot->auction_date)->format('d.m.Y') : '-' }}
                        </td>
                        <td class="px-4 py-3">
                            @if($lot->contract_signed)
                                <span class="inline-block px-3 py-1 bg-emerald-50 border-2 border-emerald-600 text-xs font-black text-emerald-700 uppercase">
                                    СОТИЛГАН
                                </span>
                            @elseif($lot->lot_status === 'sold')
                                <span class="inline-block px-3 py-1 bg-blue-50 border-2 border-blue-600 text-xs font-black text-blue-700 uppercase">
                                    ЖАРАЁНДА
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 bg-amber-50 border-2 border-amber-600 text-xs font-black text-amber-700 uppercase">
                                    КУТИЛМОҚДА
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 no-print">
                            <a href="{{ route('lots.show', $lot->id) }}"
                               class="inline-block px-4 py-2 bg-blue-600 text-white text-xs font-bold uppercase hover:bg-blue-700 transition shadow-sm">
                                КЎРИШ
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-16 px-5">
                <svg class="w-20 h-20 mx-auto mb-5 text-slate-400 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3 class="text-xl font-bold text-slate-700 mb-2">МАЪЛУМОТ ТОПИЛМАДИ</h3>
                <p class="text-sm font-semibold text-slate-500">Танланган фильтр бўйича ҳеч қандай участка топилмади.</p>
            </div>
            @endif
        </div>

        @if($lots->count() > 0)
        <!-- Pagination -->
        <div class="flex items-center justify-center gap-2 px-5 py-5 bg-slate-50 border-t-2 border-slate-300">
            @if($lots->onFirstPage())
                <span class="px-3 py-2 bg-slate-100 border-2 border-slate-300 text-slate-400 text-xs font-bold">«</span>
            @else
                <a href="{{ $lots->previousPageUrl() }}"
                   class="px-3 py-2 bg-white border-2 border-slate-400 text-slate-700 text-xs font-bold hover:bg-blue-600 hover:text-white hover:border-blue-600 transition">«</a>
            @endif

            @foreach(range(1, $lots->lastPage()) as $page)
                @if($page == $lots->currentPage())
                    <span class="px-3 py-2 bg-blue-600 border-2 border-blue-600 text-white text-xs font-bold shadow">{{ $page }}</span>
                @else
                    <a href="{{ $lots->url($page) }}"
                       class="px-3 py-2 bg-white border-2 border-slate-400 text-slate-700 text-xs font-bold hover:bg-blue-600 hover:text-white hover:border-blue-600 transition">{{ $page }}</a>
                @endif
            @endforeach

            @if($lots->hasMorePages())
                <a href="{{ $lots->nextPageUrl() }}"
                   class="px-3 py-2 bg-white border-2 border-slate-400 text-slate-700 text-xs font-bold hover:bg-blue-600 hover:text-white hover:border-blue-600 transition">»</a>
            @else
                <span class="px-3 py-2 bg-slate-100 border-2 border-slate-300 text-slate-400 text-xs font-bold">»</span>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('lotsTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 2; i < tr.length; i++) { // Start from 2 to skip header and summary
        let found = false;
        const td = tr[i].getElementsByTagName('td');

        for (let j = 0; j < td.length; j++) {
            if (td[j]) {
                const txtValue = td[j].textContent || td[j].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }

        tr[i].style.display = found ? '' : 'none';
    }
}

function exportToExcel() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = window.location.pathname + '?' + params.toString();
}
</script>

<style>
@media print {
    .no-print {
        display: none !important;
    }
}
</style>
@endsection
