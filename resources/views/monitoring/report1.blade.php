@extends('layouts.app')

@section('title', 'Свод-1 - Йиғма маълумот')

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
        <p>СВОД - 1 | Ҳисобот даври: {{ $filters['date_from'] ?? '01.01.2023' }} - {{ $filters['date_to'] ?? date('d.m.Y') }}</p>
    </div>

    {{-- Navigation --}}
    @include('monitoring.partials.navigation')

    {{-- Table --}}
    <div class="table-container">
        <table class="report-table">
            <thead>
                {{-- Row 1: Main Categories --}}
                <tr>
                    <th rowspan="3" style="width: 50px;">Т/Р</th>
                    <th rowspan="3" style="min-width: 180px;">Ҳудудлар</th>
                    <th colspan="4" class="section-divider main-header">Сотилган ер участкалар</th>
                    <th colspan="8" class="section-divider main-header">шундан</th>
                    <th colspan="4" class="section-divider main-header">Аукционда сотилган ва савдо натижасини расмийлаштишда турган ерлар</th>
                    <th colspan="2" class="main-header">Мулкни қабул қилиб олиш тугмаси босилмаган ерлар</th>
                </tr>
                
                {{-- Row 2: Sub-categories --}}
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
                
                {{-- Row 3: Details --}}
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
                {{-- Total Row --}}
                <tr class="total-row">
                    <td class="sticky-num">№</td>
                    <td class="sticky-district">ЖАМИ:</td>
                    <td class="count-link" onclick="openDetails('total', 'all', 0)">{{ $data['totals']['total']['count'] }}</td>
                    <td>{{ number_format($data['totals']['total']['area'], 2) }}</td>
                    <td>{{ number_format($data['totals']['total']['initial_price'], 1) }}</td>
                    <td class="section-divider">{{ number_format($data['totals']['total']['sold_price'], 1) }}</td>
                    
                    <td class="count-link" onclick="openDetails('one_time', 'all', 0)">{{ $data['totals']['one_time']['count'] }}</td>
                    <td>{{ number_format($data['totals']['one_time']['area'], 2) }}</td>
                    <td>{{ number_format($data['totals']['one_time']['initial_price'], 1) }}</td>
                    <td class="section-divider">{{ number_format($data['totals']['one_time']['sold_price'], 1) }}</td>
                    
                    <td class="count-link" onclick="openDetails('installment', 'all', 0)">{{ $data['totals']['installment']['count'] }}</td>
                    <td>{{ number_format($data['totals']['installment']['area'], 2) }}</td>
                    <td>{{ number_format($data['totals']['installment']['initial_price'], 1) }}</td>
                    <td class="section-divider">{{ number_format($data['totals']['installment']['sold_price'], 1) }}</td>
                    
                    <td class="count-link" onclick="openDetails('under_contract', 'all', 0)">{{ $data['totals']['under_contract']['count'] }}</td>
                    <td>{{ number_format($data['totals']['under_contract']['area'], 2) }}</td>
                    <td>{{ number_format($data['totals']['under_contract']['initial_price'], 1) }}</td>
                    <td class="section-divider">{{ number_format($data['totals']['under_contract']['sold_price'], 1) }}</td>
                    
                    <td class="count-link" onclick="openDetails('not_accepted', 'all', 0)">{{ $data['totals']['not_accepted']['count'] }}</td>
                    <td>{{ number_format($data['totals']['not_accepted']['amount'], 1) }}</td>
                </tr>

                {{-- District Rows --}}
                @foreach($data['data'] as $index => $row)
                <tr>
                    <td class="sticky-num">{{ $index + 1 }}</td>
                    <td class="sticky-district">{{ $row['tuman'] }}</td>
                    
                    <td class="count-link" onclick="openDetails('total', '{{ $row['tuman'] }}', {{ $index + 1 }})">{{ $row['total']['count'] }}</td>
                    <td>{{ number_format($row['total']['area'], 2) }}</td>
                    <td>{{ number_format($row['total']['initial_price'], 1) }}</td>
                    <td class="section-divider">{{ number_format($row['total']['sold_price'], 1) }}</td>
                    
                    <td class="count-link" onclick="openDetails('one_time', '{{ $row['tuman'] }}', {{ $index + 1 }})">{{ $row['one_time']['count'] }}</td>
                    <td>{{ number_format($row['one_time']['area'], 2) }}</td>
                    <td>{{ number_format($row['one_time']['initial_price'], 1) }}</td>
                    <td class="section-divider">{{ number_format($row['one_time']['sold_price'], 1) }}</td>
                    
                    <td class="count-link" onclick="openDetails('installment', '{{ $row['tuman'] }}', {{ $index + 1 }})">{{ $row['installment']['count'] }}</td>
                    <td>{{ number_format($row['installment']['area'], 2) }}</td>
                    <td>{{ number_format($row['installment']['initial_price'], 1) }}</td>
                    <td class="section-divider">{{ number_format($row['installment']['sold_price'], 1) }}</td>
                    
                    <td class="count-link" onclick="openDetails('under_contract', '{{ $row['tuman'] }}', {{ $index + 1 }})">{{ $row['under_contract']['count'] }}</td>
                    <td>{{ number_format($row['under_contract']['area'], 2) }}</td>
                    <td>{{ number_format($row['under_contract']['initial_price'], 1) }}</td>
                    <td class="section-divider">{{ number_format($row['under_contract']['sold_price'], 1) }}</td>
                    
                    <td class="count-link" onclick="openDetails('not_accepted', '{{ $row['tuman'] }}', {{ $index + 1 }})">{{ $row['not_accepted']['count'] }}</td>
                    <td>{{ number_format($row['not_accepted']['amount'], 1) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Filters --}}
    <div class="filter-section no-print">
        <form method="GET" action="{{ route('monitoring.report1') }}">
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
                <a href="{{ route('monitoring.report1') }}" class="btn btn-reset" style="text-decoration: none;">Тозалаш</a>
            </div>
        </form>
    </div>

    {{-- Actions --}}
    <div class="actions-row no-print">
        <button onclick="exportToExcel()" class="btn btn-success">Excel форматда юклаш</button>
        <button onclick="window.print()" class="btn btn-secondary">Чоп этиш</button>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function exportToExcel() {
        const params = new URLSearchParams(new FormData(document.querySelector('form'))).toString();
        window.location.href = '{{ route('monitoring.report1') }}?export=excel&' + params;
    }

    function openDetails(category, district, districtId) {
        const form = document.querySelector('form');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        window.location.href = `/monitoring/report1/details?category=${category}&district=${encodeURIComponent(district)}&district_id=${districtId}&${params}`;
    }
</script>
@endpush