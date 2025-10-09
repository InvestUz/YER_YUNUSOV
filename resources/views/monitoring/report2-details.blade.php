@extends('layouts.app')

@section('title', 'Свод-2 Детали - Toshkent Invest')

@push('styles')
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }

            .table-wrapper {
                box-shadow: none;
                border: 1px solid #000;
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #fafafa;
            font-family: 'Times New Roman', Times, serif;
            color: #1a1a1a;
        }

        .report-container {
            padding: 20px;
            max-width: 100%;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid #d4d4d4;
            color: #2c5282;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 15px;
            transition: all 0.2s;
        }

        .back-button:hover {
            background: #f5f5f5;
            border-color: #2c5282;
        }

        .header-section {
            background: #ffffff;
            padding: 25px 35px;
            margin-bottom: 20px;
            border-left: 4px solid #2c5282;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .header-section h1 {
            margin: 0 0 10px 0;
            font-size: 19px;
            color: #1a1a1a;
            font-weight: 700;
            line-height: 1.5;
        }

        .header-section p {
            margin: 0;
            color: #525252;
            font-size: 13px;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #d4d4d4;
            padding: 18px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .stat-card .stat-label {
            font-size: 12px;
            color: #525252;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .stat-card .stat-value {
            font-size: 20px;
            color: #2c5282;
            font-weight: 700;
        }

        .report-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 15px;
        }

        .btn-filter {
            padding: 8px 22px;
            border: none;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Times New Roman', Times, serif;
        }

        .table-wrapper {
            background: white;
            border: 1px solid #9ca3af;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            overflow-x: auto;
            margin-bottom: 20px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            min-width: 1800px;
            font-family: 'Times New Roman', Times, serif;
        }

        .report-table th {
            background: #f5f5f5;
            color: #1a1a1a;
            font-weight: 700;
            padding: 11px 9px;
            text-align: center;
            border: 1px solid #9ca3af;
            font-size: 12px;
            line-height: 1.4;
        }

        .report-table td {
            padding: 9px 11px;
            text-align: center;
            border: 1px solid #9ca3af;
            font-size: 13px;
            color: #1a1a1a;
        }

        .report-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .report-table tbody tr:hover {
            background: #f0f4f8;
        }

        .text-left {
            text-align: left !important;
        }

        .text-right {
            text-align: right !important;
        }

        .amount-cell {
            font-weight: 700;
            color: #047857;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .pagination a,
        .pagination span {
            padding: 8px 12px;
            border: 1px solid #d4d4d4;
            background: #ffffff;
            color: #2c5282;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .pagination a:hover {
            background: #2c5282;
            color: white;
            border-color: #2c5282;
        }

        .pagination .active {
            background: #2c5282;
            color: white;
            border-color: #2c5282;
        }

        .report-note {
            background: #f9fafb;
            border-left: 3px solid #2c5282;
            padding: 16px 22px;
            margin-top: 15px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .report-note h4 {
            font-size: 13px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 7px;
        }

        .report-note p {
            font-size: 12px;
            color: #3a3a3a;
            line-height: 1.6;
        }
    </style>
@endpush

@section('content')
    <div class="report-container">

        <!-- Back Button -->
        <a href="{{ route('monitoring.report2') }}?{{ http_build_query($filters) }}" class="back-button no-print">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Орқага қайтиш
        </a>

        <!-- Header -->
        <div class="header-section">
            <h1>{{ $categoryName }}</h1>
            <p>{{ $districtName }} | Ҳисобот даври: {{ $filters['date_from'] ?? '01.01.2023' }} -
                {{ $filters['date_to'] ?? date('d.m.Y') }}</p>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid no-print">
            <div class="stat-card">
                <div class="stat-label">Жами участкалар:</div>
                <div class="stat-value">{{ $stats['count'] }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Умумий майдон (га):</div>
                <div class="stat-value">{{ number_format($stats['total_area'], 2) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Бошланғич нарх (млрд сўм):</div>
                <div class="stat-value">{{ number_format($stats['total_initial_price'], 1) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Сотилган нарх (млрд сўм):</div>
                <div class="stat-value">{{ number_format($stats['total_sold_price'], 1) }}</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Аукцион ҳақи (млрд сўм):</div>
                <div class="stat-value">{{ number_format($stats['total_auction_fee'], 1) }}</div>
            </div>
            @if($category === 'discount')
            <div class="stat-card">
                <div class="stat-label">Чегирма (млрд сўм):</div>
                <div class="stat-value">{{ number_format($stats['total_discount'], 1) }}</div>
            </div>
            @endif
        </div>

        <!-- Actions -->
        <div class="report-actions no-print">
            <button onclick="exportToExcel()" class="btn-filter" style="background: #059669; color: white;">Excel
                форматда юклаш</button>
            <button onclick="window.print()" class="btn-filter" style="background: #2563eb; color: white;">Чоп
                этиш</button>
        </div>

        <!-- Report Table -->
        <div class="table-wrapper">
            <table class="report-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">№</th>
                        <th>Участка рақами</th>
                        <th>Туман</th>
                        <th>Маҳалла</th>
                        <th>Манзил</th>
                        <th>Майдони<br>(га)</th>
                        <th>Зона</th>
                        <th>Бошланғич нарх<br>(млрд сўм)</th>
                        <th>Сотилган нарх<br>(млрд сўм)</th>
                        <th>Аукцион ҳақи<br>(млрд сўм)</th>
                        @if($category === 'discount')
                        <th>Чегирма<br>(млрд сўм)</th>
                        @endif
                        <th>Эгаси</th>
                        <th>Субъект тури</th>
                        <th>Аукцион санаси</th>
                        <th>Янги Ўзбекистон</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lots as $index => $lot)
                        <tr>
                            <td>{{ ($lots->currentPage() - 1) * $lots->perPage() + $index + 1 }}</td>
                            <td class="text-left">{{ $lot->lot_number ?? 'N/A' }}</td>
                            <td class="text-left">{{ $lot->tuman->name_uz ?? '' }}</td>
                            <td class="text-left">{{ $lot->mahalla->name_uz ?? '' }}</td>
                            <td class="text-left">{{ $lot->address ?? '' }}</td>
                            <td class="text-right">{{ number_format($lot->land_area, 2) }}</td>
                            <td>{{ $lot->zone ?? '' }}</td>
                            <td class="text-right">{{ number_format($lot->initial_price / 1000000000, 1) }}</td>
                            <td class="amount-cell text-right">{{ number_format($lot->sold_price / 1000000000, 1) }}</td>
                            <td class="text-right">{{ number_format($lot->auction_fee / 1000000000, 1) }}</td>
                            @if($category === 'discount')
                            <td class="text-right">{{ number_format($lot->discount / 1000000000, 1) }}</td>
                            @endif
                            <td class="text-left">{{ $lot->winner_name ?? '' }}</td>
                            <td>{{ $lot->winner_type === 'legal' ? 'Юридик' : 'Жисмоний' }}</td>
                            <td>{{ $lot->auction_date ? \Carbon\Carbon::parse($lot->auction_date)->format('d.m.Y') : '' }}
                            </td>
                            <td>{{ $lot->yangi_uzbekiston ? 'Ҳа' : 'Йўқ' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" style="padding: 40px; text-align: center; color: #9ca3af;">
                                Маълумотлар топилмади
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination no-print">
            {{ $lots->appends($filters)->links() }}
        </div>

        <!-- Report Note -->
        <div class="report-note no-print">
            <h4>Эслатма:</h4>
            <p>Ушбу жадвал {{ $categoryName }} тўғрисида батафсил маълумотларни ўз ичига олади. Барча маблағлар миллиард
                сўм ҳисобида кўрсатилган. Жами: {{ $stats['count'] }} та участка.</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function exportToExcel() {
            const params = new URLSearchParams({
                category: '{{ $category }}',
                district: '{{ $district }}',
                district_id: '{{ request()->input('district_id') }}',
                export: 'excel',
                ...@json($filters)
            }).toString();
            window.location.href = '{{ route('monitoring.report2.details') }}?' + params;
        }
    </script>
@endpush
