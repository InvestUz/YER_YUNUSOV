@extends('layouts.app')

@section('title', 'Свод-1 - Toshkent Invest')

@push('styles')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: white; }
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background: #f8f9fa;
        font-family: 'Times New Roman', Times, serif;
    }

    .report-container {
        padding: 30px;
        max-width: 100%;
    }

    .header-section {
        background: white;
        padding: 25px 35px;
        margin-bottom: 20px;
        border-left: 6px solid #1e40af;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .header-section h1 {
        margin: 0 0 10px 0;
        font-size: 22px;
        color: #1e293b;
        font-weight: 700;
        line-height: 1.4;
    }

    .header-section p {
        margin: 0;
        color: #64748b;
        font-size: 15px;
        font-weight: 600;
    }

    .filter-section {
        background: white;
        border: 1px solid #cbd5e1;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .filter-row {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 18px;
        margin-bottom: 18px;
    }

    .filter-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 9px 12px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        background: white;
        font-family: 'Times New Roman', Times, serif;
    }

    .filter-group input:focus,
    .filter-group select:focus {
        outline: none;
        border-color: #3b82f6;
    }

    .btn-filter {
        padding: 9px 24px;
        border: none;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Times New Roman', Times, serif;
    }

    .btn-search {
        background: #2563eb;
        color: white;
    }

    .btn-search:hover {
        background: #1d4ed8;
    }

    .btn-reset {
        background: #e2e8f0;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-reset:hover {
        background: #cbd5e1;
    }

    .report-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-bottom: 20px;
    }

    .table-wrapper {
        background: white;
        border: 2px solid #94a3b8;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        overflow-x: auto;
    }

    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 2200px;
        font-family: 'Times New Roman', Times, serif;
    }

    .report-table th {
        background: linear-gradient(to bottom, #f1f5f9, #e2e8f0);
        color: #1e293b;
        font-weight: 700;
        padding: 14px 12px;
        text-align: center;
        border: 1px solid #94a3b8;
        font-size: 12px;
        line-height: 1.4;
    }

    .report-table th.header-main {
        background: linear-gradient(to bottom, #dbeafe, #bfdbfe);
        font-size: 13px;
        font-weight: 800;
        color: #1e40af;
    }

    .report-table td {
        padding: 12px 14px;
        text-align: center;
        border: 1px solid #94a3b8;
        font-size: 14px;
    }

    .report-table tbody tr:nth-child(even) {
        background: #f8fafc;
    }

    .report-table tbody tr:hover {
        background: #dbeafe;
    }

    .sticky-col {
        position: sticky;
        left: 0;
        background: #e2e8f0;
        z-index: 10;
        font-weight: 700;
        box-shadow: 2px 0 4px rgba(0,0,0,0.1);
    }

    .report-table tbody tr:nth-child(even) .sticky-col {
        background: #cbd5e1;
    }

    .report-table tbody tr:hover .sticky-col {
        background: #bfdbfe;
    }

    .row-number {
        text-align: center;
        font-weight: 600;
        color: #475569;
        min-width: 70px;
    }

    .district-name {
        text-align: left;
        padding-left: 16px !important;
        color: #1e40af;
        font-weight: 600;
        min-width: 200px;
    }

    .count-cell {
        font-weight: 700;
        cursor: pointer;
        transition: all 0.15s;
    }

    .count-cell a {
        color: #2563eb;
        text-decoration: none;
        display: block;
        width: 100%;
        height: 100%;
    }

    .count-cell:hover {
        background: #93c5fd !important;
    }

    .count-cell a:hover {
        text-decoration: underline;
    }

    .amount-cell {
        font-weight: 700;
        color: #059669;
    }

    .total-row td {
        background: linear-gradient(to bottom, #d1fae5, #a7f3d0) !important;
        font-weight: 800;
        border: 2px solid #059669 !important;
        padding: 16px 14px !important;
        font-size: 15px;
        color: #065f46;
    }

    .total-row .sticky-col {
        background: linear-gradient(to bottom, #a7f3d0, #86efac) !important;
        box-shadow: 2px 0 4px rgba(0,0,0,0.15);
    }

    .section-divider {
        border-right: 2px solid #64748b !important;
    }

    .table-wrapper::-webkit-scrollbar {
        height: 16px;
    }

    .table-wrapper::-webkit-scrollbar-track {
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: #94a3b8;
        border-radius: 8px;
        border: 2px solid #f1f5f9;
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }

    .report-note {
        background: white;
        border-left: 4px solid #3b82f6;
        padding: 16px 20px;
        margin-top: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }

    .report-note h4 {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .report-note p {
        font-size: 13px;
        color: #475569;
        line-height: 1.6;
    }
</style>
@endpush

@section('content')
<div class="report-container">
    <!-- Header -->
    <div class="header-section no-print">
        <h1>ТОШКЕНТ ШАҲРИДА АУКЦИОН САВДОЛАРИДА СОТИЛГАН ЕР УЧАСТКАЛАРИ ТЎҒРИСИДА ЙИҒМА МАЪЛУМОТ</h1>
        <p>Свод - 1 | Ҳисобот даври: {{ $filters['date_from'] ?? '01.01.2023' }} - {{ $filters['date_to'] ?? date('d.m.Y') }}</p>
    </div>

    <!-- Filters -->
    <div class="filter-section no-print">
        <form method="GET" action="{{ route('monitoring.report1') }}">
            <div class="filter-row">
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
                        <option value="legal" {{ ($filters['subject_type'] ?? '') === 'legal' ? 'selected' : '' }}>Юридик шахс</option>
                        <option value="individual" {{ ($filters['subject_type'] ?? '') === 'individual' ? 'selected' : '' }}>Жисмоний шахс</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Иқтисодий зона:</label>
                    <select name="zone">
                        <option value="">Барчаси</option>
                        <option value="1" {{ ($filters['zone'] ?? '') === '1' ? 'selected' : '' }}>1-зона</option>
                        <option value="2" {{ ($filters['zone'] ?? '') === '2' ? 'selected' : '' }}>2-зона</option>
                        <option value="3" {{ ($filters['zone'] ?? '') === '3' ? 'selected' : '' }}>3-зона</option>
                        <option value="4" {{ ($filters['zone'] ?? '') === '4' ? 'selected' : '' }}>4-зона</option>
                        <option value="5" {{ ($filters['zone'] ?? '') === '5' ? 'selected' : '' }}>5-зона</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Бош режа зонаси:</label>
                    <select name="master_plan_zone">
                        <option value="">Барчаси</option>
                        <option value="реновация" {{ ($filters['master_plan_zone'] ?? '') === 'реновация' ? 'selected' : '' }}>Реновация</option>
                        <option value="реконструкция" {{ ($filters['master_plan_zone'] ?? '') === 'реконструкция' ? 'selected' : '' }}>Реконструкция</option>
                        <option value="консервация" {{ ($filters['master_plan_zone'] ?? '') === 'консервация' ? 'selected' : '' }}>Консервация</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>Хусусият:</label>
                    <select name="yangi_uzbekiston">
                        <option value="">Барчаси</option>
                        <option value="1" {{ ($filters['yangi_uzbekiston'] ?? '') === '1' ? 'selected' : '' }}>Янги Ўзбекистон</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                <button type="submit" class="btn-filter btn-search">Қидириш</button>
                <a href="{{ route('monitoring.report1') }}" class="btn-filter btn-reset" style="display: inline-block; text-decoration: none; text-align: center;">Тозалаш</a>
            </div>
        </form>
    </div>

    <!-- Actions -->
    <div class="report-actions no-print">
        <button onclick="exportToExcel()" class="btn-filter" style="background: #059669; color: white;">Excel форматда юклаш</button>
        <button onclick="window.print()" class="btn-filter" style="background: #2563eb; color: white;">Чоп этиш</button>
    </div>

    <!-- Report Table -->
    <div class="table-wrapper">
        <table class="report-table">
            <thead>
                <tr>
                    <th rowspan="3" style="width: 70px;">Т/Р</th>
                    <th rowspan="3" class="sticky-col" style="min-width: 200px;">Ҳудуд</th>
                    <th colspan="4" class="section-divider header-main">Жами АРТ(АПЗ) бўйича тузилган шартномалар</th>
                    <th colspan="8" class="section-divider header-main">шундан</th>
                    <th colspan="4" class="section-divider header-main">Амалдаги шартномалар</th>
                    <th colspan="2" class="header-main">Мулкни қабул қилиб олиш тугмаси босилмаган</th>
                </tr>
                <tr>
                    <th rowspan="2">сони</th>
                    <th rowspan="2">майдони<br>(га)</th>
                    <th rowspan="2">бошланғич<br>нархи<br>(млрд сўм)</th>
                    <th rowspan="2" class="section-divider">сотилган<br>нархи<br>(млрд сўм)</th>
                    <th colspan="4" class="section-divider">Бекор қилинган</th>
                    <th colspan="4" class="section-divider">Тўлиқ тўланган</th>
                    <th rowspan="2">сони</th>
                    <th rowspan="2">майдони<br>(га)</th>
                    <th rowspan="2">бошланғич<br>нархи<br>(млрд сўм)</th>
                    <th rowspan="2" class="section-divider">сотилган<br>нархи<br>(млрд сўм)</th>
                    <th rowspan="2">сони</th>
                    <th rowspan="2">маблағ<br>(млрд сўм)</th>
                </tr>
                <tr>
                    <th>сони</th>
                    <th>майдони<br>(га)</th>
                    <th>бошланғич<br>нархи<br>(млрд сўм)</th>
                    <th class="section-divider">сотилган<br>нархи<br>(млрд сўм)</th>
                    <th>сони</th>
                    <th>майдони<br>(га)</th>
                    <th>бошланғич<br>нархи<br>(млрд сўм)</th>
                    <th class="section-divider">сотилган<br>нархи<br>(млрд сўм)</th>
                </tr>
            </thead>
            <tbody>
                <!-- ЖАМИ row first -->
                <tr class="total-row">
                    <td colspan="2" class="sticky-col">ЖАМИ:</td>
                    <td class="count-cell" onclick="openDetails('total', 'all', 0)">
                        <a href="javascript:void(0)">{{ $data['totals']['total']['count'] }}</a>
                    </td>
                    <td>{{ number_format($data['totals']['total']['area'], 2) }}</td>
                    <td>{{ number_format($data['totals']['total']['initial_price'], 1) }}</td>
                    <td class="section-divider amount-cell">{{ number_format($data['totals']['total']['sold_price'], 1) }}</td>

                    <td class="count-cell" onclick="openDetails('one_time', 'all', 0)">
                        <a href="javascript:void(0)">{{ $data['totals']['one_time']['count'] }}</a>
                    </td>
                    <td>{{ number_format($data['totals']['one_time']['area'], 2) }}</td>
                    <td>{{ number_format($data['totals']['one_time']['initial_price'], 1) }}</td>
                    <td class="section-divider amount-cell">{{ number_format($data['totals']['one_time']['sold_price'], 1) }}</td>

                    <td class="count-cell" onclick="openDetails('installment', 'all', 0)">
                        <a href="javascript:void(0)">{{ $data['totals']['installment']['count'] }}</a>
                    </td>
                    <td>{{ number_format($data['totals']['installment']['area'], 2) }}</td>
                    <td>{{ number_format($data['totals']['installment']['initial_price'], 1) }}</td>
                    <td class="section-divider amount-cell">{{ number_format($data['totals']['installment']['sold_price'], 1) }}</td>

                    <td class="count-cell" onclick="openDetails('under_contract', 'all', 0)">
                        <a href="javascript:void(0)">{{ $data['totals']['under_contract']['count'] }}</a>
                    </td>
                    <td>{{ number_format($data['totals']['under_contract']['area'], 2) }}</td>
                    <td>{{ number_format($data['totals']['under_contract']['initial_price'], 1) }}</td>
                    <td class="section-divider amount-cell">{{ number_format($data['totals']['under_contract']['sold_price'], 1) }}</td>

                    <td class="count-cell" onclick="openDetails('not_accepted', 'all', 0)">
                        <a href="javascript:void(0)">{{ $data['totals']['not_accepted']['count'] }}</a>
                    </td>
                    <td class="amount-cell">{{ number_format($data['totals']['not_accepted']['amount'], 1) }}</td>
                </tr>

                @foreach($data['data'] as $index => $row)
                <tr>
                    <td class="row-number">{{ $index + 1 }}</td>
                    <td class="sticky-col district-name">{{ $row['tuman'] }}</td>
                    
                    <td class="count-cell" onclick="openDetails('total', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                        <a href="javascript:void(0)">{{ $row['total']['count'] }}</a>
                    </td>
                    <td>{{ number_format($row['total']['area'], 2) }}</td>
                    <td>{{ number_format($row['total']['initial_price'], 1) }}</td>
                    <td class="amount-cell section-divider">{{ number_format($row['total']['sold_price'], 1) }}</td>

                    <td class="count-cell" onclick="openDetails('one_time', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                        <a href="javascript:void(0)">{{ $row['one_time']['count'] }}</a>
                    </td>
                    <td>{{ number_format($row['one_time']['area'], 2) }}</td>
                    <td>{{ number_format($row['one_time']['initial_price'], 1) }}</td>
                    <td class="amount-cell section-divider">{{ number_format($row['one_time']['sold_price'], 1) }}</td>

                    <td class="count-cell" onclick="openDetails('installment', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                        <a href="javascript:void(0)">{{ $row['installment']['count'] }}</a>
                    </td>
                    <td>{{ number_format($row['installment']['area'], 2) }}</td>
                    <td>{{ number_format($row['installment']['initial_price'], 1) }}</td>
                    <td class="amount-cell section-divider">{{ number_format($row['installment']['sold_price'], 1) }}</td>

                    <td class="count-cell" onclick="openDetails('under_contract', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                        <a href="javascript:void(0)">{{ $row['under_contract']['count'] }}</a>
                    </td>
                    <td>{{ number_format($row['under_contract']['area'], 2) }}</td>
                    <td>{{ number_format($row['under_contract']['initial_price'], 1) }}</td>
                    <td class="amount-cell section-divider">{{ number_format($row['under_contract']['sold_price'], 1) }}</td>

                    <td class="count-cell" onclick="openDetails('not_accepted', '{{ $row['tuman'] }}', {{ $index + 1 }})">
                        <a href="javascript:void(0)">{{ $row['not_accepted']['count'] }}</a>
                    </td>
                    <td class="amount-cell">{{ number_format($row['not_accepted']['amount'], 1) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Report Note -->
    <div class="report-note no-print">
        <h4>Эслатма:</h4>
        <p>Ушбу ҳисобот давлат органлари учун мўлжалланган. Барча маълумотлар расмий манбалардан олинган ва автоматик равишда янгиланади. Маблағлар миллиард сўм ҳисобида кўрсатилган.</p>
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
    const form = document.querySelector('form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData).toString();
    const detailUrl = `/monitoring/report1/details?category=${category}&district=${encodeURIComponent(district)}&district_id=${districtId}&${params}`;
    window.location.href = detailUrl;
}
</script>
@endpush