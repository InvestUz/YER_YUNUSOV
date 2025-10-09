@extends('layouts.app')

@section('title', 'Свод-2 - Toshkent Invest')

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
            letter-spacing: 0.2px;
        }

        .header-section p {
            margin: 0;
            color: #525252;
            font-size: 13px;
            font-weight: 600;
        }

        .filter-section {
            background: #ffffff;
            border: 1px solid #d4d4d4;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
            margin-bottom: 15px;
        }

        .filter-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #2a2a2a;
            margin-bottom: 5px;
        }

        .filter-group input,
        .filter-group select {
            width: 100%;
            padding: 7px 10px;
            border: 1px solid #d4d4d4;
            font-size: 13px;
            background: #ffffff;
            font-family: 'Times New Roman', Times, serif;
            color: #1a1a1a;
            transition: border-color 0.2s;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #2c5282;
            box-shadow: 0 0 0 2px rgba(44, 82, 130, 0.08);
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

        .btn-search {
            background: #2c5282;
            color: white;
        }

        .btn-search:hover {
            background: #1e3a5f;
        }

        .btn-reset {
            background: #f5f5f5;
            color: #3a3a3a;
            border: 1px solid #d4d4d4;
        }

        .btn-reset:hover {
            background: #e8e8e8;
        }

        .report-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-bottom: 15px;
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
            min-width: 2800px;
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

        .report-table th.header-main {
            background: #e8edf3;
            font-size: 12px;
            font-weight: 700;
            color: #2c5282;
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

        .sticky-col-number {
            position: sticky;
            left: 0;
            background: #ebebeb;
            z-index: 10;
            font-weight: 600;
            min-width: 70px;
        }

        .sticky-col-district {
            position: sticky;
            left: 70px;
            background: #ebebeb;
            z-index: 10;
            font-weight: 700;
            box-shadow: 2px 0 3px rgba(0, 0, 0, 0.06);
            text-align: left;
            padding-left: 16px !important;
            min-width: 200px;
        }

        .report-table tbody tr:nth-child(even) .sticky-col-number,
        .report-table tbody tr:nth-child(even) .sticky-col-district {
            background: #e0e0e0;
        }

        .report-table tbody tr:hover .sticky-col-number,
        .report-table tbody tr:hover .sticky-col-district {
            background: #dae3ec;
        }

        .row-number {
            text-align: center;
            font-weight: 600;
            color: #3a3a3a;
        }

        .district-name {
            color: #2c5282;
            font-weight: 600;
        }

        .count-cell {
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s;
        }

        .count-cell a {
            color: #2c5282;
            text-decoration: none;
            display: block;
            width: 100%;
            height: 100%;
        }

        .count-cell:hover {
            background: #d6e3f0 !important;
        }

        .count-cell a:hover {
            text-decoration: underline;
        }

        .amount-cell {
            font-weight: 700;
            color: #047857;
        }

        .total-row td {
            background: #e8f5e9 !important;
            font-weight: 800;
            border: 1px solid #6b7280 !important;
            padding: 13px 11px !important;
            font-size: 14px;
            color: #065f46;
        }

        .total-row .sticky-col-number,
        .total-row .sticky-col-district {
            background: #d1f0d4 !important;
            box-shadow: 2px 0 3px rgba(0, 0, 0, 0.08);
        }

        .section-divider {
            border-right: 2px solid #6b7280 !important;
        }

        .table-wrapper::-webkit-scrollbar {
            height: 12px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: #f5f5f5;
            border: 1px solid #d4d4d4;
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: #9ca3af;
            border-radius: 6px;
            border: 2px solid #f5f5f5;
        }

        .table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
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

        .text-right {
            text-align: right !important;
        }
    </style>
@endpush

@section('content')
    <div class="report-container">

        <!-- Header -->
        <div class="header-section no-print">
            <h1>ТОШКЕНТ ШАҲРИДА АУКЦИОН САВДОЛАРИДА СОТИЛГАН ЕР УЧАСТКАЛАРИ ТЎҒРИСИДА ЙИҒМА МАЪЛУМОТ</h1>
            <p>Свод - 2 | Ҳисобот даври: {{ $filters['date_from'] ?? '01.01.2023' }} -
                {{ $filters['date_to'] ?? date('d.m.Y') }}</p>
        </div>

        <!-- Report Table -->
        <div class="table-wrapper">
            <table class="report-table">
                <thead>
                    <tr>
                        <th rowspan="3" style="width: 70px;">Т/Р</th>
                        <th rowspan="3" style="min-width: 200px;">Ҳудудлар</th>
                        <th colspan="4" class="section-divider header-main">Сотилган ер участкалар</th>
                        <th rowspan="3" class="section-divider">Аукцион хизмат ҳақи<br>(млрд сўм)</th>
                        <th colspan="2" class="section-divider header-main">Чегирма берилганлар</th>
                        <th rowspan="3" class="section-divider">Давактивга тўланган<br>маблағ<br>(млрд сўм)</th>
                        <th rowspan="3" class="section-divider">Аукционга чиқариш<br>харажати<br>(млрд сўм)</th>
                        <th colspan="8" class="section-divider header-main">Тақсимоти (млрд сўм)</th>
                        <th colspan="3" class="header-main">Келгусида тушадиган маблағлар (млрд сўм)</th>
                    </tr>
                    <tr>
                        <th rowspan="2">сони</th>
                        <th rowspan="2">майдони<br>(га)</th>
                        <th rowspan="2">бошланғич<br>нархи<br>(млрд сўм)</th>
                        <th rowspan="2" class="section-divider">сотилган<br>нархи<br>(млрд сўм)</th>
                        <th rowspan="2">сони</th>
                        <th rowspan="2" class="section-divider">қиймати<br>(млрд сўм)</th>
                        <th colspan="4" class="section-divider">шундан, амалда</th>
                        <th colspan="4" class="section-divider">шундан</th>
                        <th rowspan="2">2025 йилда</th>
                        <th rowspan="2">2026 йилда</th>
                        <th rowspan="2">2027 йилда</th>
                    </tr>
                    <tr>
                        <th>Маҳаллий<br>бюджет</th>
                        <th>Жамғарма</th>
                        <th>Янги<br>Ўзбекистон</th>
                        <th class="section-divider">Туман<br>ҳокимияти</th>
                        <th>Маҳаллий<br>бюджет</th>
                        <th>Жамғарма</th>
                        <th>Янги<br>Ўзбекистон</th>
                        <th class="section-divider">Туман<br>ҳокимияти</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ЖАМИ row first -->
                    <tr class="total-row">
                        <td class="sticky-col-number">№</td>
                        <td class="sticky-col-district">ЖАМИ:</td>
                        <td class="count-cell">
                            <a href="javascript:void(0)">{{ $data['totals']['count'] }}</a>
                        </td>
                        <td>{{ number_format($data['totals']['area'], 2) }}</td>
                        <td>{{ number_format($data['totals']['initial_price'], 1) }}</td>
                        <td class="section-divider amount-cell">
                            {{ number_format($data['totals']['sold_price'], 1) }}</td>
                        <td class="section-divider amount-cell">
                            {{ number_format($data['totals']['auction_fee'], 1) }}</td>
                        <td class="count-cell">
                            <a href="javascript:void(0)">{{ $data['totals']['discount_count'] }}</a>
                        </td>
                        <td class="section-divider amount-cell">
                            {{ number_format($data['totals']['discount_amount'], 1) }}</td>
                        <td class="section-divider amount-cell">
                            {{ number_format($data['totals']['davaktiv_amount'], 1) }}</td>
                        <td class="section-divider amount-cell">
                            {{ number_format($data['totals']['auction_expenses'], 1) }}</td>
                        <td>{{ number_format($data['totals']['distributions']['local_budget_allocated'], 1) }}</td>
                        <td>{{ number_format($data['totals']['distributions']['development_fund_allocated'], 1) }}</td>
                        <td>{{ number_format($data['totals']['distributions']['new_uzbekistan_allocated'], 1) }}</td>
                        <td class="section-divider">
                            {{ number_format($data['totals']['distributions']['district_authority_allocated'], 1) }}</td>
                        <td>{{ number_format($data['totals']['distributions']['local_budget_remaining'], 1) }}</td>
                        <td>{{ number_format($data['totals']['distributions']['development_fund_remaining'], 1) }}</td>
                        <td>{{ number_format($data['totals']['distributions']['new_uzbekistan_remaining'], 1) }}</td>
                        <td class="section-divider">
                            {{ number_format($data['totals']['distributions']['district_authority_remaining'], 1) }}</td>
                        <td>{{ number_format($data['totals']['future_payments'][2025] ?? 0, 1) }}</td>
                        <td>{{ number_format($data['totals']['future_payments'][2026] ?? 0, 1) }}</td>
                        <td>{{ number_format($data['totals']['future_payments'][2027] ?? 0, 1) }}</td>
                    </tr>

                    @foreach ($data['data'] as $index => $row)
                        <tr>
                            <td class="sticky-col-number row-number">{{ $index + 1 }}</td>
                            <td class="sticky-col-district district-name">{{ $row['tuman'] }}</td>

                            <td class="count-cell">
                                <a href="javascript:void(0)">{{ $row['count'] }}</a>
                            </td>
                            <td class="text-right">{{ number_format($row['area'], 2) }}</td>
                            <td class="text-right">{{ number_format($row['initial_price'], 1) }}</td>
                            <td class="amount-cell section-divider text-right">
                                {{ number_format($row['sold_price'], 1) }}</td>

                            <td class="section-divider text-right">{{ number_format($row['auction_fee'], 1) }}</td>

                            <td class="count-cell">
                                <a href="javascript:void(0)">{{ $row['discount_count'] }}</a>
                            </td>
                            <td class="section-divider text-right">
                                {{ number_format($row['discount_amount'], 1) }}</td>

                            <td class="section-divider amount-cell text-right">
                                {{ number_format($row['davaktiv_amount'], 1) }}</td>
                            <td class="section-divider text-right">{{ number_format($row['auction_expenses'], 1) }}</td>

                            <td class="text-right">
                                {{ number_format($row['distributions']['local_budget_allocated'], 1) }}</td>
                            <td class="text-right">
                                {{ number_format($row['distributions']['development_fund_allocated'], 1) }}</td>
                            <td class="text-right">
                                {{ number_format($row['distributions']['new_uzbekistan_allocated'], 1) }}</td>
                            <td class="section-divider text-right">
                                {{ number_format($row['distributions']['district_authority_allocated'], 1) }}</td>

                            <td class="text-right">
                                {{ number_format($row['distributions']['local_budget_remaining'], 1) }}</td>
                            <td class="text-right">
                                {{ number_format($row['distributions']['development_fund_remaining'], 1) }}</td>
                            <td class="text-right">
                                {{ number_format($row['distributions']['new_uzbekistan_remaining'], 1) }}</td>
                            <td class="section-divider text-right">
                                {{ number_format($row['distributions']['district_authority_remaining'], 1) }}</td>

                            <td class="text-right">{{ number_format($row['future_payments'][2025] ?? 0, 1) }}</td>
                            <td class="text-right">{{ number_format($row['future_payments'][2026] ?? 0, 1) }}</td>
                            <td class="text-right">{{ number_format($row['future_payments'][2027] ?? 0, 1) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Filters -->
        <div class="filter-section no-print">
            <form method="GET" action="{{ route('monitoring.report2') }}">
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
                            <option value="legal" {{ ($filters['subject_type'] ?? '') === 'legal' ? 'selected' : '' }}>
                                Юридик шахс</option>
                            <option value="individual"
                                {{ ($filters['subject_type'] ?? '') === 'individual' ? 'selected' : '' }}>Жисмоний шахс
                            </option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Иқтисодий зона:</label>
                        <select name="zone">
                            <option value="">Барчаси</option>
                            <option value="1" {{ ($filters['zone'] ?? '') === '1' ? 'selected' : '' }}>1-зона
                            </option>
                            <option value="2" {{ ($filters['zone'] ?? '') === '2' ? 'selected' : '' }}>2-зона
                            </option>
                            <option value="3" {{ ($filters['zone'] ?? '') === '3' ? 'selected' : '' }}>3-зона
                            </option>
                            <option value="4" {{ ($filters['zone'] ?? '') === '4' ? 'selected' : '' }}>4-зона
                            </option>
                            <option value="5" {{ ($filters['zone'] ?? '') === '5' ? 'selected' : '' }}>5-зона
                            </option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Бош режа зонаси:</label>
                        <select name="master_plan_zone">
                            <option value="">Барчаси</option>
                            <option value="реновация"
                                {{ ($filters['master_plan_zone'] ?? '') === 'реновация' ? 'selected' : '' }}>Реновация
                            </option>
                            <option value="реконструкция"
                                {{ ($filters['master_plan_zone'] ?? '') === 'реконструкция' ? 'selected' : '' }}>
                                Реконструкция</option>
                            <option value="консервация"
                                {{ ($filters['master_plan_zone'] ?? '') === 'консервация' ? 'selected' : '' }}>Консервация
                            </option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Хусусият:</label>
                        <select name="yangi_uzbekiston">
                            <option value="">Барчаси</option>
                            <option value="1" {{ ($filters['yangi_uzbekiston'] ?? '') === '1' ? 'selected' : '' }}>
                                Янги Ўзбекистон</option>
                        </select>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 12px;">
                    <button type="submit" class="btn-filter btn-search">Қидириш</button>
                    <a href="{{ route('monitoring.report2') }}" class="btn-filter btn-reset"
                        style="display: inline-block; text-decoration: none; text-align: center;">Тозалаш</a>
                </div>
            </form>
        </div>

        <!-- Actions -->
        <div class="report-actions no-print">
            <button onclick="exportToExcel()" class="btn-filter" style="background: #059669; color: white;">Excel
                форматда юклаш</button>
            <button onclick="window.print()" class="btn-filter" style="background: #2563eb; color: white;">Чоп
                этиш</button>
        </div>

        <!-- Report Note -->
        <div class="report-note no-print">
            <h4>Эслатма:</h4>
            <p>Ушбу ҳисобот давлат органлари учун мўлжалланган. Барча маълумотлар расмий манбалардан олинган ва автоматик
                равишда янгиланади. Маблағлар миллиард сўм ҳисобида кўрсатилган. Тақсимот: Маҳаллий бюджет, Жамғарма, Янги
                Ўзбекистон дирекцияси, Туман ҳокимияти.</p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function exportToExcel() {
            const params = new URLSearchParams(new FormData(document.querySelector('form'))).toString();
            window.location.href = '{{ route('monitoring.report2') }}?export=excel&' + params;
        }
    </script>
@endpush
