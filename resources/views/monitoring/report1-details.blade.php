@extends('layouts.app')

@section('title', 'Детальная информация - Toshkent Invest')

@push('styles')
<style>
    .details-container {
        background: #f8f9fa;
        min-height: 100vh;
        padding: 20px;
    }

    .breadcrumb {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .breadcrumb a {
        color: #1976d2;
        text-decoration: none;
        font-weight: 600;
    }

    .breadcrumb a:hover {
        text-decoration: underline;
    }

    .breadcrumb span {
        color: #666;
    }

    .details-header {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        color: white;
        padding: 25px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .details-header h1 {
        margin: 0 0 10px 0;
        font-size: 24px;
        font-weight: 700;
    }

    .details-header .info-row {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        margin-top: 15px;
    }

    .details-header .info-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .details-header .info-item strong {
        font-weight: 600;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #1976d2;
    }

    .stat-card .label {
        font-size: 11px;
        color: #666;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .stat-card .value {
        font-size: 24px;
        font-weight: 700;
        color: #1976d2;
    }

    .stat-card .unit {
        font-size: 12px;
        color: #999;
        margin-left: 4px;
    }

    .actions-bar {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .search-box {
        display: flex;
        gap: 10px;
        flex: 1;
        max-width: 500px;
    }

    .search-box input {
        flex: 1;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 13px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-primary {
        background: #1976d2;
        color: white;
    }

    .btn-primary:hover {
        background: #1565c0;
    }

    .btn-success {
        background: #2e7d32;
        color: white;
    }

    .btn-success:hover {
        background: #1b5e20;
    }

    .btn-secondary {
        background: #757575;
        color: white;
    }

    .btn-secondary:hover {
        background: #616161;
    }

    .table-container {
        background: white;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .table-wrapper {
        overflow-x: auto;
    }

    .lots-table {
        width: 100%;
        border-collapse: collapse;
    }

    .lots-table thead {
        background: linear-gradient(180deg, #5c6bc0 0%, #3f51b5 100%);
    }

    .lots-table th {
        padding: 12px 15px;
        text-align: left;
        color: white;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        border-right: 1px solid rgba(255,255,255,0.1);
        white-space: nowrap;
    }

    .lots-table th:last-child {
        border-right: none;
    }

    .lots-table tbody tr {
        border-bottom: 1px solid #e0e0e0;
        transition: background 0.2s;
    }

    .lots-table tbody tr:hover {
        background: #e3f2fd;
    }

    .lots-table td {
        padding: 12px 15px;
        font-size: 12px;
        color: #333;
    }

    .lots-table .lot-number {
        font-weight: 700;
        color: #1976d2;
    }

    .lots-table .lot-address {
        max-width: 300px;
        white-space: normal;
    }

    .status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-sold {
        background: #c8e6c9;
        color: #1b5e20;
    }

    .status-contract {
        background: #fff9c4;
        color: #f57f17;
    }

    .status-pending {
        background: #ffccbc;
        color: #bf360c;
    }

    .payment-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
    }

    .payment-onetime {
        background: #e1f5fe;
        color: #01579b;
    }

    .payment-installment {
        background: #f3e5f5;
        color: #4a148c;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 5px;
        padding: 20px;
        background: white;
        border-top: 1px solid #e0e0e0;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 12px;
        text-decoration: none;
        color: #333;
    }

    .pagination a:hover {
        background: #e3f2fd;
        border-color: #1976d2;
        color: #1976d2;
    }

    .pagination .active {
        background: #1976d2;
        color: white;
        border-color: #1976d2;
    }

    .pagination .disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .no-data svg {
        width: 80px;
        height: 80px;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    @media print {
        .no-print {
            display: none !important;
        }
        
        .lots-table {
            font-size: 9px;
        }
        
        .lots-table th,
        .lots-table td {
            padding: 6px 8px;
        }
    }
</style>
@endpush

@section('content')
<div class="details-container">
    <!-- Breadcrumb -->
    <div class="breadcrumb no-print">
        <a href="{{ route('dashboard') }}">🏠 Бош саҳифа</a>
        <span>›</span>
        <a href="{{ route('monitoring.report1') }}">Свод-1</a>
        <span>›</span>
        <span>{{ $categoryName }}</span>
    </div>

    <!-- Header -->
    <div class="details-header">
        <h1>{{ $categoryName }}</h1>
        <div class="info-row">
            <div class="info-item">
                <span>📍</span>
                <span><strong>Ҳудуд:</strong> {{ $districtName }}</span>
            </div>
            <div class="info-item">
                <span>📅</span>
                <span><strong>Давр:</strong> {{ $filters['date_from'] ?? '01.01.2023' }} - {{ $filters['date_to'] ?? date('d.m.Y') }}</span>
            </div>
            @if(!empty($filters['subject_type']))
            <div class="info-item">
                <span>👤</span>
                <span><strong>Субъект:</strong> {{ $filters['subject_type'] === 'legal' ? 'Юридик' : 'Жисмоний' }}</span>
            </div>
            @endif
            @if(!empty($filters['zone']))
            <div class="info-item">
                <span>🏢</span>
                <span><strong>Зона:</strong> {{ $filters['zone'] }}</span>
            </div>
            @endif
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-cards no-print">
        <div class="stat-card">
            <div class="label">Жами участкалар</div>
            <div class="value">{{ $stats['count'] }}<span class="unit">дона</span></div>
        </div>
        <div class="stat-card">
            <div class="label">Умумий майдон</div>
            <div class="value">{{ number_format($stats['total_area'], 2) }}<span class="unit">га</span></div>
        </div>
        <div class="stat-card">
            <div class="label">Бошланғич нарх</div>
            <div class="value">{{ number_format($stats['total_initial_price'], 1) }}<span class="unit">млрд</span></div>
        </div>
        <div class="stat-card">
            <div class="label">Сотилган нарх</div>
            <div class="value">{{ number_format($stats['total_sold_price'], 1) }}<span class="unit">млрд</span></div>
        </div>
    </div>

    <!-- Actions Bar -->
    <div class="actions-bar no-print">
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Участка рақами, манзил ёки эгаси бўйича қидириш..." onkeyup="filterTable()">
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="exportToExcel()" class="btn btn-success">
                <span>📊</span> Excel
            </button>
            <button onclick="window.print()" class="btn btn-primary">
                <span>🖨️</span> Чоп этиш
            </button>
            <a href="{{ route('monitoring.report1') }}" class="btn btn-secondary">
                <span>←</span> Орқага
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-wrapper">
            @if($lots->count() > 0)
            <table class="lots-table" id="lotsTable">
                <thead>
                    <tr>
                        <th style="width: 50px;">№</th>
                        <th>Участка рақами</th>
                        <th>Манзил</th>
                        <th>Майдони (га)</th>
                        <th>Зона</th>
                        <th>Бошланғич нарх (сўм)</th>
                        <th>Сотилган нарх (сўм)</th>
                        <th>Тўлаш усули</th>
                        <th>Эгаси</th>
                        <th>Субъект тури</th>
                        <th>Аукцион санаси</th>
                        <th>Ҳолати</th>
                        <th class="no-print">Амаллар</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lots as $index => $lot)
                    <tr>
                        <td>{{ ($lots->currentPage() - 1) * $lots->perPage() + $index + 1 }}</td>
                        <td class="lot-number">{{ $lot->lot_number ?? 'N/A' }}</td>
                        <td class="lot-address">{{ $lot->address ?? 'Манзил кўрсатилмаган' }}</td>
                        <td>{{ number_format($lot->land_area, 2) }}</td>
                        <td>{{ $lot->zone ?? '-' }}</td>
                        <td>{{ number_format($lot->initial_price, 0, '.', ' ') }}</td>
                        <td style="font-weight: 700; color: #2e7d32;">{{ number_format($lot->sold_price, 0, '.', ' ') }}</td>
                        <td>
                            @if($lot->payment_method === 'one_time')
                                <span class="payment-badge payment-onetime">Бир йўла</span>
                            @else
                                <span class="payment-badge payment-installment">Бўлиб тўлаш</span>
                            @endif
                        </td>
                        <td>{{ $lot->winner_name ?? '-' }}</td>
                        <td>{{ $lot->winner_type === 'legal' ? 'Юридик' : 'Жисмоний' }}</td>
                        <td>{{ $lot->auction_date ? \Carbon\Carbon::parse($lot->auction_date)->format('d.m.Y') : '-' }}</td>
                        <td>
                            @if($lot->contract_signed)
                                <span class="status-badge status-sold">Сотилган</span>
                            @elseif($lot->lot_status === 'sold')
                                <span class="status-badge status-contract">Расмийлаштирилмоқда</span>
                            @else
                                <span class="status-badge status-pending">Кутилмоқда</span>
                            @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ route('lots.show', $lot->id) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 11px;">
                                Кўриш
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="no-data">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <h3>Маълумот топилмади</h3>
                <p>Танланган фильтр бўйича ҳеч қандай участка топилмади.</p>
            </div>
            @endif
        </div>

        @if($lots->count() > 0)
        <!-- Pagination -->
        <div class="pagination">
            @if($lots->onFirstPage())
                <span class="disabled">«</span>
            @else
                <a href="{{ $lots->previousPageUrl() }}">«</a>
            @endif

            @foreach(range(1, $lots->lastPage()) as $page)
                @if($page == $lots->currentPage())
                    <span class="active">{{ $page }}</span>
                @else
                    <a href="{{ $lots->url($page) }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($lots->hasMorePages())
                <a href="{{ $lots->nextPageUrl() }}">»</a>
            @else
                <span class="disabled">»</span>
            @endif
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toUpperCase();
    const table = document.getElementById('lotsTable');
    const tr = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
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
    window.location.href = '{{ route("monitoring.report1.details") }}?' + params.toString();
}
</script>
@endpush