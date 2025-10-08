@extends('layouts.app')

@section('title', 'Свод-1 - Toshkent Invest')

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white; }
        .print-table { font-size: 10px; }
    }

    .report-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }

    .header-section {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .filter-section {
        background: #fffef7;
        border: 2px solid #e5e5e5;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }

    .filter-group label {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 8px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 13px;
        background: white;
    }

    .filter-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
    }

    .btn-filter {
        padding: 8px 20px;
        border: none;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-search {
        background: #4CAF50;
        color: white;
    }

    .btn-search:hover {
        background: #45a049;
    }

    .btn-reset {
        background: #f5f5f5;
        color: #333;
    }

    .btn-reset:hover {
        background: #e0e0e0;
    }

    .report-table-container {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .report-table-header {
        background: #e8f5e9;
        padding: 15px 20px;
        border-bottom: 2px solid #4CAF50;
    }

    .report-table-header h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #2c3e50;
    }

    .report-table-wrapper {
        overflow-x: auto;
        border: 1px solid #ddd;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 11px;
    }

    .report-table th {
        background: linear-gradient(180deg, #5c6bc0 0%, #3f51b5 100%);
        color: white;
        font-weight: 700;
        text-transform: uppercase;
        padding: 10px 8px;
        text-align: center;
        border: 1px solid #3949ab;
        font-size: 10px;
        letter-spacing: 0.3px;
    }

    .report-table th.header-main {
        background: linear-gradient(180deg, #1976d2 0%, #1565c0 100%);
    }

    .report-table td {
        padding: 8px;
        text-align: center;
        border: 1px solid #e0e0e0;
        font-size: 11px;
    }

    .report-table tbody tr:nth-child(even) {
        background: #f9f9f9;
    }

    .report-table tbody tr:hover {
        background: #e3f2fd;
    }

    .report-table .district-name {
        text-align: left;
        font-weight: 600;
        color: #1565c0;
        padding-left: 15px;
    }

    .report-table .count-cell {
        color: #1976d2;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .report-table .count-cell:hover {
        background: #bbdefb;
        color: #0d47a1;
        transform: scale(1.05);
    }

    .report-table .amount-cell {
        font-weight: 600;
        color: #2e7d32;
    }

    .report-table .total-row {
        background: linear-gradient(90deg, #e8f5e9 0%, #c8e6c9 100%);
        font-weight: 700;
        border-top: 3px solid #4CAF50;
    }

    .report-table .total-row td {
        padding: 12px 8px;
        font-size: 12px;
        color: #1b5e20;
    }

    .section-divider {
        border-right: 2px solid #1565c0 !important;
    }

    .watermark {
        position: absolute;
        font-size: 60px;
        color: rgba(0,0,0,0.03);
        transform: rotate(-45deg);
        top: 50%;
        left: 50%;
        pointer-events: none;
        font-weight: 900;
        letter-spacing: 20px;
    }
</style>
@endpush

@section('content')
<div class="report-container">
    <!-- Header -->
    <div class="header-section no-print">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="margin: 0 0 8px 0; font-size: 24px; color: #1565c0;">
                    Тошкент шаҳрида аукцион савдоларида сотилган ер участкалари тўғрисида
                </h1>
                <p style="margin: 0; color: #666; font-size: 13px; font-weight: 600;">ЙИҒМА МАЪЛУМОТ</p>
            </div>
            <div style="display: flex; gap: 10px;">
                <button onclick="exportToExcel()" class="btn-filter" style="background: #2e7d32; color: white;">
                    📊 Excel юклаш
                </button>
                <button onclick="window.print()" class="btn-filter" style="background: #1565c0; color: white;">
                    🖨️ Чоп этиш
                </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-section no-print">
        <form method="GET" action="{{ route('monitoring.report1') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label>дан:</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '2023-01-01' }}">
                </div>
                <div class="filter-group">
                    <label>гача:</label>
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
                        <option value="1" {{ ($filters['zone'] ?? '') === '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ ($filters['zone'] ?? '') === '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ ($filters['zone'] ?? '') === '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ ($filters['zone'] ?? '') === '4' ? 'selected' : '' }}>4</option>
                        <option value="5" {{ ($filters['zone'] ?? '') === '5' ? 'selected' : '' }}>5</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Бош режа бўйича жойлашув зонаси:</label>
                    <select name="master_plan_zone">
                        <option value="">Барчаси</option>
                        <option value="реновация" {{ ($filters['master_plan_zone'] ?? '') === 'реновация' ? 'selected' : '' }}>реновация</option>
                        <option value="реконструкция" {{ ($filters['master_plan_zone'] ?? '') === 'реконструкция' ? 'selected' : '' }}>реконструкция</option>
                        <option value="консервация" {{ ($filters['master_plan_zone'] ?? '') === 'консервация' ? 'selected' : '' }}>консервация</option>
                    </select>
                </div>
            </div>
            <div class="filter-row">
                <div class="filter-group">
                    <label>ҳусусияти:</label>
                    <select name="yangi_uzbekiston">
                        <option value="">Барчаси</option>
                        <option value="1" {{ ($filters['yangi_uzbekiston'] ?? '') === '1' ? 'selected' : '' }}>Янги Ўзбекистон</option>
                    </select>
                </div>
                <div class="filter-group" style="grid-column: span 3;"></div>
                <div class="filter-group">
                    <label style="opacity: 0;">Actions</label>
                    <div class="filter-actions">
                        <button type="submit" class="btn-filter btn-search">Қидириш</button>
                        <a href="{{ route('monitoring.report1') }}" class="btn-filter btn-reset">Тозалаш</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <div class="report-table-container">
        <div class="report-table-header">
            <h3>Свод - 1 | Давр: {{ $filters['date_from'] ?? '01.01.2023' }} - {{ $filters['date_to'] ?? date('d.m.Y') }}</h3>
        </div>

        <div class="report-table-wrapper" style="position: relative;">
            <div class="watermark">Тошкент Инвест</div>
            <table class="report-table">
                <thead>
                    <tr>
                        <th rowspan="3" style="width: 40px;">Т/р</th>
                        <th rowspan="3" style="min-width: 150px;">Ҳудудлар</th>
                        <th colspan="4" class="section-divider">Сотилган ер участкалар</th>
                        <th colspan="4" class="section-divider">шундан</th>
                        <th colspan="4" class="section-divider"></th>
                        <th colspan="4" class="section-divider">Аукционда сотилган ва савдо натижасини расмийлаштишда турган ерлар</th>
                        <th colspan="2">Мулкни қабул қилиб олиш тугмаси босилмаган ерлар</th>
                    </tr>
                    <tr>
                        <th rowspan="2">сони</th>
                        <th rowspan="2">майдони<br>(га)</th>
                        <th rowspan="2">бошланғич нархи<br>(млрд сўм)</th>
                        <th rowspan="2" class="section-divider">сотилган нархи<br>(млрд сўм)</th>
                        <th colspan="4" class="section-divider">Бир йўла тўлаш шарти билан сотилган</th>
                        <th colspan="4" class="section-divider">Нархини бўлиб тўлаш шарти билан сотилган</th>
                        <th rowspan="2">сони</th>
                        <th rowspan="2">майдони<br>(га)</th>
                        <th rowspan="2">бошланғич нархи<br>(млрд сўм)</th>
                        <th rowspan="2" class="section-divider">сотилган нархи<br>(млрд сўм)</th>
                        <th rowspan="2">сони</th>
                        <th rowspan="2">Аукционда турган маблағ<br>(млрд сўм)</th>
                    </tr>
                    <tr>
                        <th>сони</th>
                        <th>майдони<br>(га)</th>
                        <th>бошланғич нархи<br>(млрд сўм)</th>
                        <th class="section-divider">сотилган нархи<br>(млрд сўм)</th>
                        <th>сони</th>
                        <th>майдони<br>(га)</th>
                        <th>бошланғич нархи<br>(млрд сўм)</th>
                        <th class="section-divider">сотилган нархи<br>(млрд сўм)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['data'] as $index => $row)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="district-name">{{ $row['tuman'] }}</td>
                        
                        <!-- Total -->
                        <td class="count-cell" onclick="openDetails('total', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                            {{ $row['total']['count'] }}
                        </td>
                        <td>{{ number_format($row['total']['area'], 2) }}</td>
                        <td>{{ number_format($row['total']['initial_price'], 1) }}</td>
                        <td class="amount-cell section-divider">{{ number_format($row['total']['sold_price'], 1) }}</td>

                        <!-- One Time Payment -->
                        <td class="count-cell" onclick="openDetails('one_time', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                            {{ $row['one_time']['count'] }}
                        </td>
                        <td>{{ number_format($row['one_time']['area'], 2) }}</td>
                        <td>{{ number_format($row['one_time']['initial_price'], 1) }}</td>
                        <td class="amount-cell section-divider">{{ number_format($row['one_time']['sold_price'], 1) }}</td>

                        <!-- Installment Payment -->
                        <td class="count-cell" onclick="openDetails('installment', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                            {{ $row['installment']['count'] }}
                        </td>
                        <td>{{ number_format($row['installment']['area'], 2) }}</td>
                        <td>{{ number_format($row['installment']['initial_price'], 1) }}</td>
                        <td class="amount-cell section-divider">{{ number_format($row['installment']['sold_price'], 1) }}</td>

                        <!-- Under Contract -->
                        <td class="count-cell" onclick="openDetails('under_contract', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                            {{ $row['under_contract']['count'] }}
                        </td>
                        <td>{{ number_format($row['under_contract']['area'], 2) }}</td>
                        <td>{{ number_format($row['under_contract']['initial_price'], 1) }}</td>
                        <td class="amount-cell section-divider">{{ number_format($row['under_contract']['sold_price'], 1) }}</td>

                        <!-- Not Accepted -->
                        <td class="count-cell" onclick="openDetails('not_accepted', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                            {{ $row['not_accepted']['count'] }}
                        </td>
                        <td class="amount-cell">{{ number_format($row['not_accepted']['amount'], 1) }}</td>
                    </tr>
                    @endforeach

                    <!-- Totals Row -->
                    <tr class="total-row">
                        <td colspan="2">ЖАМИ:</td>
                        <td class="count-cell" onclick="openDetails('total', 'all', 0)">{{ $data['totals']['total']['count'] }}</td>
                        <td>{{ number_format($data['totals']['total']['area'], 2) }}</td>
                        <td>{{ number_format($data['totals']['total']['initial_price'], 1) }}</td>
                        <td class="section-divider">{{ number_format($data['totals']['total']['sold_price'], 1) }}</td>

                        <td class="count-cell" onclick="openDetails('one_time', 'all', 0)">{{ $data['totals']['one_time']['count'] }}</td>
                        <td>{{ number_format($data['totals']['one_time']['area'], 2) }}</td>
                        <td>{{ number_format($data['totals']['one_time']['initial_price'], 1) }}</td>
                        <td class="section-divider">{{ number_format($data['totals']['one_time']['sold_price'], 1) }}</td>

                        <td class="count-cell" onclick="openDetails('installment', 'all', 0)">{{ $data['totals']['installment']['count'] }}</td>
                        <td>{{ number_format($data['totals']['installment']['area'], 2) }}</td>
                        <td>{{ number_format($data['totals']['installment']['initial_price'], 1) }}</td>
                        <td class="section-divider">{{ number_format($data['totals']['installment']['sold_price'], 1) }}</td>

                        <td class="count-cell" onclick="openDetails('under_contract', 'all', 0)">{{ $data['totals']['under_contract']['count'] }}</td>
                        <td>{{ number_format($data['totals']['under_contract']['area'], 2) }}</td>
                        <td>{{ number_format($data['totals']['under_contract']['initial_price'], 1) }}</td>
                        <td class="section-divider">{{ number_format($data['totals']['under_contract']['sold_price'], 1) }}</td>

                        <td class="count-cell" onclick="openDetails('not_accepted', 'all', 0)">{{ $data['totals']['not_accepted']['count'] }}</td>
                        <td>{{ number_format($data['totals']['not_accepted']['amount'], 1) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function exportToExcel() {
    const params = new URLSearchParams(new FormData(document.querySelector('form'))).toString();
    window.location.href = '{{ route("monitoring.report1") }}?export=excel&' + params;
}

function openDetails(category, district, districtId) {
    // Get current filters
    const form = document.querySelector('form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    
    // Build detail page URL with filters
    const detailUrl = `/monitoring/report1/details?category=${category}&district=${encodeURIComponent(district)}&district_id=${districtId}&${params}`;
    
    // Open in new window or same window
    window.location.href = detailUrl;
}
</script>
@endpush