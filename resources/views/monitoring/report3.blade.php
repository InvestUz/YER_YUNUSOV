@extends('layouts.app')

@section('title', 'Свод-3 - Бўлиб тўлаш')

@push('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f5f5f5;
        font-family: 'Times New Roman', Times, serif;
    }

    .report-wrapper {
        max-width: 100%;
        padding: 15px;
    }

    /* Header */
    .report-header {
        background: white;
        border: 1px solid #d1d5db;
        padding: 20px;
        margin-bottom: 15px;
    }

    .report-header h1 {
        font-size: 16px;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 8px;
        text-align: center;
        text-transform: uppercase;
    }

    .report-header p {
        font-size: 13px;
        color: #4b5563;
        text-align: center;
    }

    /* Table Container */
    .table-container {
        background: white;
        border: 2px solid #6b7280;
        overflow-x: auto;
        margin-bottom: 15px;
    }

    /* Main Table */
    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
        min-width: 2400px;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #6b7280;
        padding: 6px 8px;
        text-align: center;
        vertical-align: middle;
    }

    /* Header Styles */
    .report-table thead th {
        background: #e5e7eb;
        font-weight: bold;
        color: #1f2937;
        font-size: 9px;
        line-height: 1.4;
    }

    .report-table thead .main-title {
        background: #d1d5db;
        font-size: 12px;
        padding: 10px;
    }

    .report-table thead .group-header {
        background: #dbeafe;
        font-size: 10px;
    }

    /* Sticky Columns */
    .sticky-num {
        position: sticky;
        left: 0;
        background: #f9fafb;
        z-index: 20;
        min-width: 50px;
        font-weight: 600;
        border-right: 2px solid #374151 !important;
    }

    .sticky-district {
        position: sticky;
        left: 50px;
        background: #f9fafb;
        z-index: 20;
        text-align: left;
        padding-left: 12px !important;
        min-width: 180px;
        font-weight: 600;
        border-right: 2px solid #374151 !important;
    }

    thead .sticky-num,
    thead .sticky-district {
        background: #e5e7eb;
        z-index: 25;
    }

    /* Body Rows */
    .report-table tbody td {
        color: #374151;
        font-size: 10px;
    }

    .report-table tbody tr:hover td {
        background: #f3f4f6;
    }

    .report-table tbody tr:hover .sticky-num,
    .report-table tbody tr:hover .sticky-district {
        background: #dbeafe;
    }

    /* Clickable Counts */
    .count-link {
        color: #2563eb;
        cursor: pointer;
        font-weight: 600;
        text-decoration: underline;
    }

    .count-link:hover {
        color: #1d4ed8;
    }

    /* Total Row */
    .total-row td {
        background: #fef3c7 !important;
        font-weight: bold;
        border: 2px solid #1f2937 !important;
        font-size: 11px;
    }

    .total-row .sticky-num,
    .total-row .sticky-district {
        background: #fde68a !important;
    }

    /* Section Dividers */
    .section-divider {
        border-right: 2px solid #374151 !important;
    }

    /* Right Align Numbers */
    .text-right {
        text-align: right !important;
        padding-right: 10px !important;
    }

    /* Amount Cells */
    .amount-cell {
        font-weight: 600;
        color: #047857;
    }

    /* Percentage Cells */
    .percentage-cell {
        font-weight: 700;
    }

    .percentage-good {
        color: #047857;
    }

    .percentage-warning {
        color: #d97706;
    }

    .percentage-danger {
        color: #dc2626;
    }

    /* Filters */
    .filter-section {
        background: white;
        border: 1px solid #d1d5db;
        padding: 15px;
        margin-bottom: 15px;
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .filter-group label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 5px;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 7px 10px;
        border: 1px solid #d1d5db;
        font-size: 12px;
        font-family: 'Times New Roman', Times, serif;
        border-radius: 4px;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }

    /* Buttons */
    .btn {
        padding: 8px 20px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        font-family: 'Times New Roman', Times, serif;
        border-radius: 4px;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #2563eb;
        color: white;
    }

    .btn-primary:hover {
        background: #1d4ed8;
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
    }

    .btn-secondary:hover {
        background: #4b5563;
    }

    .btn-success {
        background: #059669;
        color: white;
    }

    .btn-success:hover {
        background: #047857;
    }

    .btn-reset {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }

    .btn-reset:hover {
        background: #e5e7eb;
    }

    /* Actions */
    .actions-row {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
        margin-bottom: 15px;
    }

    /* Scrollbar */
    .table-container::-webkit-scrollbar {
        height: 12px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f3f4f6;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 6px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }

    /* Print */
    @media print {
        .no-print {
            display: none !important;
        }

        body {
            background: white;
        }

        .report-table {
            font-size: 8px;
        }

        .sticky-num,
        .sticky-district {
            position: static;
        }
    }
</style>
@endpush

@section('content')
<div class="report-wrapper">

    {{-- Header --}}
    <div class="report-header">
        <h1>Тошкент шаҳрида аукцион савдоларида бўлиб тўлаш шарти билан сотилган ер участкалари тўғрисида йиғма маълумот</h1>
        <p>СВОД - 3 | Ҳисобот даври: {{ $filters['date_from'] ?? '01.01.2023' }} - {{ $filters['date_to'] ?? date('d.m.Y') }} | Ҳисоб санаси: {{ $filters['current_date'] ?? date('d.m.Y') }}</p>
    </div>

    {{-- Navigation --}}
    @include('monitoring.partials.navigation')

    {{-- Filters --}}
    <div class="filter-section no-print">
        <form method="GET" action="{{ route('monitoring.report3') }}">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Бошланиш санаси:</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '2023-01-01' }}">
                </div>
                <div class="filter-group">
                    <label>Тугаш санаси:</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? date('Y-m-d') }}">
                </div>
                <div class="filter-group">
                    <label>Ҳисоб санаси:</label>
                    <input type="date" name="current_date" value="{{ $filters['current_date'] ?? date('Y-m-d') }}">
                </div>
                <div class="filter-group">
                    <label>Субъект тури:</label>
                    <select name="subject_type">
                        <option value="">Барчаси</option>
                        <option value="legal" {{ ($filters['subject_type'] ?? '') === 'legal' ? 'selected' : '' }}>Юридик</option>
                        <option value="individual" {{ ($filters['subject_type'] ?? '') === 'individual' ? 'selected' : '' }}>Жисмоний</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Иқтисодий зонаси:</label>
                    <select name="zone">
                        <option value="">Барчаси</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ ($filters['zone'] ?? '') == $i ? 'selected' : '' }}>{{ $i }}-зона</option>
                        @endfor
                    </select>
                </div>
                <div class="filter-group">
                    <label>Ҳусусияти:</label>
                    <select name="yangi_uzbekiston">
                        <option value="">Барчаси</option>
                        <option value="1" {{ ($filters['yangi_uzbekiston'] ?? '') === '1' ? 'selected' : '' }}>Янги Ўзбекистон</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 10px;">
                <button type="submit" class="btn btn-primary">Қидириш</button>
                <a href="{{ route('monitoring.report3') }}" class="btn btn-reset" style="text-decoration: none;">Тозалаш</a>
            </div>
        </form>
    </div>

    {{-- Actions --}}
    <div class="actions-row no-print">
        <button onclick="exportToExcel()" class="btn btn-success">Excel</button>
        <button onclick="window.print()" class="btn btn-secondary">Чоп этиш</button>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table class="report-table">
            <thead>
                <!-- Main Title Row -->
                <tr>
                    <th colspan="19" class="main-title">
                        Тошкент шаҳрида аукцион савдоларида бўлиб тўлаш шарти билан сотилган ер участкалари тўғрисида<br>
                        ЙИҒМА МАЪЛУМОТ (Свод - 3)
                    </th>
                </tr>

                <!-- First Level: Main Groups -->
                <tr>
                    <th rowspan="3" class="sticky-num">Т/р</th>
                    <th rowspan="3" class="sticky-district">Ҳудудлар</th>
                    <th colspan="4" class="group-header section-divider">Нархини бўлиб тўлаш шарти билан сотилган</th>
                    <th colspan="8" class="group-header section-divider">шундан, {{ $filters['current_date'] ?? date('d.m.Y') }} ҳолатига</th>
                    <th colspan="5" class="group-header">шундан, гр.ортда қолганлар</th>
                </tr>

                <!-- Second Level: Sub Groups -->
                <tr>
                    <!-- Нархини бўлиб тўлаш -->
                    <th rowspan="2">сони</th>
                    <th rowspan="2">майдони<br>(га)</th>
                    <th rowspan="2">бошланғич<br>нархи<br>(млрд сўм)</th>
                    <th rowspan="2" class="section-divider">сотилган<br>нархи<br>(млрд сўм)</th>

                    <!-- шундан -->
                    <th colspan="4" class="section-divider">тўлиқ тўланганлар</th>
                    <th colspan="4" class="section-divider">назоратдагилар</th>

                    <!-- гр.ортда қолганлар -->
                    <th rowspan="2">сони</th>
                    <th rowspan="2">майдони<br>(га)</th>
                    <th rowspan="2">график б-ча<br>тўлов суммаси<br>(млрд сўм)</th>
                    <th rowspan="2">амалда тўлов<br>суммаси<br>(млрд сўм)</th>
                    <th rowspan="2">%</th>
                </tr>

                <!-- Third Level: Detailed Columns -->
                <tr>
                    <!-- тўлиқ тўланганлар -->
                    <th>сони</th>
                    <th>майдони<br>(га)</th>
                    <th>бошланғич<br>нархи<br>(млрд сўм)</th>
                    <th class="section-divider">сотилган<br>нархи<br>(млрд сўм)</th>

                    <!-- назоратдагилар -->
                    <th>сони</th>
                    <th>майдони<br>(га)</th>
                    <th>бошланғич<br>нархи<br>(млрд сўм)</th>
                    <th class="section-divider">сотилган<br>нархи<br>(млрд сўм)</th>
                </tr>
            </thead>

            <tbody>
                <!-- ЖАМИ row first -->
                @if(isset($data['totals']))
                <tr class="total-row">
                    <td class="sticky-num"></td>
                    <td class="sticky-district">ЖАМИ:</td>

                    <td class="count-link" onclick="openDetails('installment_total', 'all', 0)">{{ $data['totals']['total']['count'] }}</td>
                    <td class="text-right">{{ number_format($data['totals']['total']['area'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totals']['total']['initial_price'], 1) }}</td>
                    <td class="text-right section-divider amount-cell">{{ number_format($data['totals']['total']['sold_price'], 1) }}</td>

                    <td class="count-link" onclick="openDetails('fully_paid', 'all', 0)">{{ $data['totals']['fully_paid']['count'] }}</td>
                    <td class="text-right">{{ number_format($data['totals']['fully_paid']['area'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totals']['fully_paid']['initial_price'], 1) }}</td>
                    <td class="text-right section-divider amount-cell">{{ number_format($data['totals']['fully_paid']['sold_price'], 1) }}</td>

                    <td class="count-link" onclick="openDetails('under_monitoring', 'all', 0)">{{ $data['totals']['under_monitoring']['count'] }}</td>
                    <td class="text-right">{{ number_format($data['totals']['under_monitoring']['area'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totals']['under_monitoring']['initial_price'], 1) }}</td>
                    <td class="text-right section-divider amount-cell">{{ number_format($data['totals']['under_monitoring']['sold_price'], 1) }}</td>

                    <td class="count-link" onclick="openDetails('overdue', 'all', 0)">{{ $data['totals']['overdue']['count'] }}</td>
                    <td class="text-right">{{ number_format($data['totals']['overdue']['area'], 2) }}</td>
                    <td class="text-right">{{ number_format($data['totals']['overdue']['planned_payment'], 1) }}</td>
                    <td class="text-right amount-cell">{{ number_format($data['totals']['overdue']['actual_payment'], 1) }}</td>
                    <td class="text-right percentage-cell {{ $data['totals']['overdue']['percentage'] >= 80 ? 'percentage-good' : ($data['totals']['overdue']['percentage'] >= 50 ? 'percentage-warning' : 'percentage-danger') }}">
                        {{ number_format($data['totals']['overdue']['percentage'], 1) }}
                    </td>
                </tr>
                @endif

                @forelse($data['data'] ?? [] as $row)
                <tr>
                    <td class="sticky-num">{{ $loop->iteration }}</td>
                    <td class="sticky-district">{{ $row['tuman'] }}</td>

                    <td class="count-link" onclick="openDetails('installment_total', '{{ $row['tuman'] }}', {{ $loop->iteration }})">{{ $row['total']['count'] }}</td>
                    <td class="text-right">{{ number_format($row['total']['area'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['total']['initial_price'], 1) }}</td>
                    <td class="text-right section-divider amount-cell">{{ number_format($row['total']['sold_price'], 1) }}</td>

                    <td class="count-link" onclick="openDetails('fully_paid', '{{ $row['tuman'] }}', {{ $loop->iteration }})">{{ $row['fully_paid']['count'] }}</td>
                    <td class="text-right">{{ number_format($row['fully_paid']['area'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['fully_paid']['initial_price'], 1) }}</td>
                    <td class="text-right section-divider amount-cell">{{ number_format($row['fully_paid']['sold_price'], 1) }}</td>

                    <td class="count-link" onclick="openDetails('under_monitoring', '{{ $row['tuman'] }}', {{ $loop->iteration }})">{{ $row['under_monitoring']['count'] }}</td>
                    <td class="text-right">{{ number_format($row['under_monitoring']['area'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['under_monitoring']['initial_price'], 1) }}</td>
                    <td class="text-right section-divider amount-cell">{{ number_format($row['under_monitoring']['sold_price'], 1) }}</td>

                    <td class="count-link" onclick="openDetails('overdue', '{{ $row['tuman'] }}', {{ $loop->iteration }})">{{ $row['overdue']['count'] }}</td>
                    <td class="text-right">{{ number_format($row['overdue']['area'], 2) }}</td>
                    <td class="text-right">{{ number_format($row['overdue']['planned_payment'], 1) }}</td>
                    <td class="text-right amount-cell">{{ number_format($row['overdue']['actual_payment'], 1) }}</td>
                    <td class="text-right percentage-cell {{ $row['overdue']['percentage'] >= 80 ? 'percentage-good' : ($row['overdue']['percentage'] >= 50 ? 'percentage-warning' : 'percentage-danger') }}">
                        {{ number_format($row['overdue']['percentage'], 1) }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="19" style="text-align: center; padding: 20px; color: #6b7280;">
                        Маълумотлар топилмади
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function exportToExcel() {
        const params = new URLSearchParams(new FormData(document.querySelector('form'))).toString();
        window.location.href = '{{ route('monitoring.report3') }}?export=excel&' + params;
    }

    function openDetails(category, district, districtId) {
        const form = document.querySelector('form');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        window.location.href = `/monitoring/report3/details?category=${category}&district=${encodeURIComponent(district)}&district_id=${districtId}&${params}`;
    }
</script>
@endpush