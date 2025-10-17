@extends('layouts.app')

@section('title', 'Свод-2 - Тақсимот')


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
        font-size: 12px;
        min-width: 1800px;
    }

    .report-table th,
    .report-table td {
        border: 1px solid #6b7280;
        padding: 6px 8px;
        text-align: center;
    }

    /* Header Styles */
    .report-table thead th {
        background: #e5e7eb;
        font-weight: bold;
        color: #1f2937;
        font-size: 11px;
        line-height: 1.3;
    }

    .report-table thead th.main-header {
        background: #d1d5db;
        font-size: 12px;
    }

    /* Sticky Columns */
    .sticky-num {
        position: sticky;
        left: 0;
        background: #f3f4f6;
        z-index: 10;
        min-width: 50px;
        font-weight: 600;
    }

    .sticky-district {
        position: sticky;
        left: 50px;
        background: #f3f4f6;
        z-index: 10;
        text-align: left;
        padding-left: 12px !important;
        min-width: 180px;
        font-weight: 600;
    }

    /* Body Rows */
    .report-table tbody td {
        color: #374151;
        font-size: 12px;
    }

    .report-table tbody tr:hover td {
        background: #f9fafb;
    }

    .report-table tbody tr:hover .sticky-num,
    .report-table tbody tr:hover .sticky-district {
        background: #e5e7eb;
    }

    /* Clickable Counts */
    .count-link {
        color: #2563eb;
        cursor: pointer;
        font-weight: 600;
    }

    .count-link:hover {
        text-decoration: underline;
    }

    /* Total Row */
    .total-row td {
        background: #dbeafe !important;
        font-weight: bold;
        border: 1px solid #1f2937 !important;
        font-size: 13px;
    }

    .total-row .sticky-num,
    .total-row .sticky-district {
        background: #bfdbfe !important;
    }

    /* Section Dividers */
    .section-divider {
        border-right: 2px solid #374151 !important;
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
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }

    .filter-group label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 6px 8px;
        border: 1px solid #d1d5db;
        font-size: 12px;
        font-family: 'Times New Roman', Times, serif;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #2563eb;
    }

    /* Buttons */
    .btn {
        padding: 7px 18px;
        font-size: 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        font-family: 'Times New Roman', Times, serif;
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
        height: 10px;
    }

    .table-container::-webkit-scrollbar-track {
        background: #f3f4f6;
    }

    .table-container::-webkit-scrollbar-thumb {
        background: #9ca3af;
        border-radius: 4px;
    }

    .table-container::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }

    /* Print Styles */
    @media print {
        .no-print {
            display: none !important;
        }
        body {
            background: white;
        }
        .table-container {
            border: 1px solid #000;
        }
    }
</style>
@endpush


@section('content')
<div class="report-wrapper">

    {{-- Header --}}
    <div class="report-header">
        <h1>Тошкент шаҳрида аукцион савдоларида сотилган ер участкалари тўғрисида йиғма маълумот</h1>
        <p>СВОД - 2 (ТАҚСИМОТ) | Ҳисобот даври: {{ $filters['date_from'] ?? '01.01.2023' }} - {{ $filters['date_to'] ?? date('d.m.Y') }}</p>
    </div>

    {{-- Navigation --}}
    @include('monitoring.partials.navigation')

   

    {{-- Table --}}
    <div class="table-container">
        <table class="report-table">
            <thead>
                <!-- Main Title Row -->
                <tr>
                    <th colspan="27" class="main-title">
                        Тошкент шаҳрида аукцион савдоларида сотилган ер участкалари тўғрисида<br>
                        ЙИҒМА МАЪЛУМОТ (Свод - 2)
                    </th>
                </tr>

                <!-- First Level: Main Groups -->
                <tr>
                    <th rowspan="3" class="sticky-num">Т/р</th>
                    <th rowspan="3" class="sticky-district">Ҳудудлар</th>
                    <th colspan="5" class="group-header section-divider">Сотилган ер участкалар</th>
                    <th colspan="15" class="group-header section-divider">шундан,</th>
                    <th colspan="5" class="group-header">Аукционда сотилган бирок, шартнома тузиш</th>
                </tr>

                <!-- Second Level: Sub Groups -->
                <tr>
                    <!-- Сотилган ер участкалар -->
                    <th rowspan="2">сони</th>
                    <th rowspan="2">майдони<br>(га)</th>
                    <th rowspan="2">бошланич<br>нарҳи<br>(млрд сўм)</th>
                    <th rowspan="2">сотилган<br>нарҳи<br>(млрд сўм)</th>
                    <th rowspan="2" class="section-divider">Аукцион<br>ҳизмат<br>ҳаки<br>(млрд сўм)</th>

                    <!-- шундан -->
                    <th rowspan="2">Чегирма<br>берилганлар<br>сони</th>
                    <th rowspan="2">Чегирма<br>қилмати<br>(млрд сўм)</th>
                    <th rowspan="2">Давактив<br>мабладr<br>(млрд сўм)</th>
                    <th rowspan="2">Аукцион<br>ҳаражати<br>(млрд сўм)</th>

                    <th colspan="4">Тасдиқоти (Тақсимот)<br>(млрд сўм)</th>
                    <th colspan="4">Қолдиқ (амалда)</th>

                    <th rowspan="2">Келгусида<br>тўланадиган<br>жами<br>(млрд сўм)</th>
                    <th colspan="3" class="section-divider">шундан</th>

                    <!-- Аукционда сотилган бирок -->
                    <th rowspan="2">сони</th>
                    <th rowspan="2">майдони<br>(га)</th>
                    <th rowspan="2">бошланич<br>нарҳи<br>(млрд сўм)</th>
                    <th rowspan="2">сотилган<br>нарҳи<br>(млрд сўм)</th>
                </tr>

                <!-- Third Level: Detailed Columns -->
                <tr>
                    <!-- Тақсимот columns -->
                    <th>Маҳаллий<br>бюджет</th>
                    <th>Ҳамжарма<br>фонди</th>
                    <th>Янги<br>Ўзбекистон</th>
                    <th>Туман<br>ҳокимлиги</th>

                    <!-- Қолдиқ columns -->
                    <th>Маҳаллий<br>бюджет</th>
                    <th>Ҳамжарма<br>фонди</th>
                    <th>Янги<br>Ўзбекистон</th>
                    <th>Туман<br>ҳокимлиги</th>

                    <!-- Future payments breakdown -->
                    <th>2025 йилда</th>
                    <th>2026 йилда</th>
                    <th class="section-divider">2027 йилда</th>
                </tr>
            </thead>

  <tbody>
    @forelse($data as $row)
    @php
        $futureTotal = ($row['future_payments'][2025] ?? 0) + 
                       ($row['future_payments'][2026] ?? 0) + 
                       ($row['future_payments'][2027] ?? 0);
        $isTotal = false;
    @endphp
    <tr>
        <td class="sticky-num">{{ $loop->iteration }}</td>
        <td class="sticky-district">{{ $row['tuman'] }}</td>
        
        <!-- Сотилган ер участкалар -->
        <td class="text-right">{{ $row['count'] }}</td>
        <td class="text-right">{{ number_format($row['area'], 2) }}</td>
        <td class="text-right">{{ number_format($row['initial_price'], 2) }}</td>
        <td class="text-right">{{ number_format($row['sold_price'], 2) }}</td>
        <td class="text-right section-divider">{{ number_format($row['auction_fee'], 2) }}</td>
        
        <!-- шундан -->
        <td class="text-right">{{ $row['discount_count'] }}</td>
        <td class="text-right">{{ number_format($row['discount_amount'], 2) }}</td>
        <td class="text-right">{{ number_format($row['davaktiv_amount'], 2) }}</td>
        <td class="text-right">{{ number_format($row['auction_expenses'], 2) }}</td>
        
        <!-- Тақсимот -->
        <td class="text-right">{{ number_format($row['distributions']['local_budget_allocated'], 2) }}</td>
        <td class="text-right">{{ number_format($row['distributions']['development_fund_allocated'], 2) }}</td>
        <td class="text-right">{{ number_format($row['distributions']['new_uzbekistan_allocated'], 2) }}</td>
        <td class="text-right">{{ number_format($row['distributions']['district_authority_allocated'], 2) }}</td>
        
        <!-- Қолдиқ -->
        <td class="text-right">{{ number_format($row['distributions']['local_budget_remaining'], 2) }}</td>
        <td class="text-right">{{ number_format($row['distributions']['development_fund_remaining'], 2) }}</td>
        <td class="text-right">{{ number_format($row['distributions']['new_uzbekistan_remaining'], 2) }}</td>
        <td class="text-right">{{ number_format($row['distributions']['district_authority_remaining'], 2) }}</td>
        
        <!-- Future Payments -->
        <td class="text-right">{{ number_format($futureTotal, 2) }}</td>
        <td class="text-right">{{ number_format($row['future_payments'][2025], 2) }}</td>
        <td class="text-right">{{ number_format($row['future_payments'][2026], 2) }}</td>
        <td class="text-right section-divider">{{ number_format($row['future_payments'][2027], 2) }}</td>
        
        <!-- Аукционда сотилган бирок -->
        <td class="text-right">{{ $row['unsigned_count'] ?? 0 }}</td>
        <td class="text-right">{{ number_format($row['unsigned_area'] ?? 0, 2) }}</td>
        <td class="text-right">{{ number_format($row['unsigned_initial_price'] ?? 0, 2) }}</td>
        <td class="text-right">{{ number_format($row['unsigned_sold_price'] ?? 0, 2) }}</td>
    </tr>
    @empty
    <tr>
        <td colspan="27" style="text-align: center; padding: 20px; color: #6b7280;">
            Маълумотлар топилмади
        </td>
    </tr>
    @endforelse
    
    {{-- Totals Row --}}
    @if(isset($totals))
    @php
        $totalFuture = ($totals['future_payments'][2025] ?? 0) + 
                       ($totals['future_payments'][2026] ?? 0) + 
                       ($totals['future_payments'][2027] ?? 0);
    @endphp
    <tr class="total-row">
        <td class="sticky-num">№</td>
        <td class="sticky-district">жами:</td>
        
        <td class="text-right">{{ $totals['count'] }}</td>
        <td class="text-right">{{ number_format($totals['area'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['initial_price'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['sold_price'], 2) }}</td>
        <td class="text-right section-divider">{{ number_format($totals['auction_fee'], 2) }}</td>
        
        <td class="text-right">{{ $totals['discount_count'] }}</td>
        <td class="text-right">{{ number_format($totals['discount_amount'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['davaktiv_amount'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['auction_expenses'], 2) }}</td>
        
        <td class="text-right">{{ number_format($totals['distributions']['local_budget_allocated'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['distributions']['development_fund_allocated'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['distributions']['new_uzbekistan_allocated'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['distributions']['district_authority_allocated'], 2) }}</td>
        
        <td class="text-right">{{ number_format($totals['distributions']['local_budget_remaining'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['distributions']['development_fund_remaining'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['distributions']['new_uzbekistan_remaining'], 2) }}</td>
        <td class="text-right">{{ number_format($totals['distributions']['district_authority_remaining'], 2) }}</td>
        
        <td class="text-right">{{ number_format($totalFuture, 2) }}</td>
        <td class="text-right">{{ number_format($totals['future_payments'][2025], 2) }}</td>
        <td class="text-right">{{ number_format($totals['future_payments'][2026], 2) }}</td>
        <td class="text-right section-divider">{{ number_format($totals['future_payments'][2027], 2) }}</td>
        
        <td class="text-right">{{ $totals['unsigned_count'] ?? 0 }}</td>
        <td class="text-right">{{ number_format($totals['unsigned_area'] ?? 0, 2) }}</td>
        <td class="text-right">{{ number_format($totals['unsigned_initial_price'] ?? 0, 2) }}</td>
        <td class="text-right">{{ number_format($totals['unsigned_sold_price'] ?? 0, 2) }}</td>
    </tr>
    @endif
</tbody>
        </table>
    </div>

     {{-- Filters --}}
    <div class="filter-section no-print">
        <form method="GET" action="{{ route('monitoring.report2') }}">
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
                    <label>Бош режа зонаси:</label>
                    <select name="master_plan_zone">
                        <option value="">Барчаси</option>
                        <option value="реновация" {{ ($filters['master_plan_zone'] ?? '') === 'реновация' ? 'selected' : '' }}>реновация</option>
                        <option value="реконструкция" {{ ($filters['master_plan_zone'] ?? '') === 'реконструкция' ? 'selected' : '' }}>реконструкция</option>
                        <option value="консервация" {{ ($filters['master_plan_zone'] ?? '') === 'консервация' ? 'selected' : '' }}>консервация</option>
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
                <a href="{{ route('monitoring.report2') }}" class="btn btn-reset" style="text-decoration: none;">Тозалаш</a>
            </div>
        </form>
    </div>

    {{-- Actions --}}
    <div class="actions-row no-print">
        <button onclick="exportToExcel()" class="btn btn-success">Excel</button>
        <button onclick="window.print()" class="btn btn-secondary">Чоп этиш</button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function exportToExcel() {
        const params = new URLSearchParams(new FormData(document.querySelector('form'))).toString();
        window.location.href = '{{ route('monitoring.report2') }}?export=excel&' + params;
    }

    function openDetails(category, district, districtId) {
        const form = document.querySelector('form');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        window.location.href = `/monitoring/report2/details?category=${category}&district=${encodeURIComponent(district)}&district_id=${districtId}&${params}`;
    }
</script>
@endpush