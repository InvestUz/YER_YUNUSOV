{{-- resources/views/monitoring/partials/navigation.blade.php --}}

<style>
    .report-nav {
        display: flex;
        gap: 8px;
        margin-bottom: 15px;
        padding: 0;
        background: transparent;
    }

    .report-nav-btn {
        flex: 1;
        padding: 10px 16px;
        background: #ffffff;
        border: 1px solid #d1d5db;
        color: #374151;
        text-decoration: none;
        font-size: 13px;
        font-weight: 600;
        font-family: 'Times New Roman', Times, serif;
        text-align: center;
        transition: all 0.15s ease;
        border-radius: 4px;
    }

    .report-nav-btn:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        color: #1f2937;
    }

    .report-nav-btn.active {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
    }

    .report-nav-btn .btn-label {
        display: block;
        font-size: 10px;
        opacity: 0.85;
        margin-top: 2px;
        font-weight: 400;
    }

    @media print {
        .report-nav {
            display: none !important;
        }
    }

    @media (max-width: 768px) {
        .report-nav {
            flex-direction: column;
        }

        .report-nav-btn {
            width: 100%;
        }
    }
</style>

<div class="report-nav no-print">
    <a href="{{ route('monitoring.report1') }}"
       class="report-nav-btn {{ request()->routeIs('monitoring.report1*') ? 'active' : '' }}">
        <div>Свод - 1</div>
        <span class="btn-label">Шартномалар ҳолати</span>
    </a>

    <a href="{{ route('monitoring.report2') }}"
       class="report-nav-btn {{ request()->routeIs('monitoring.report2*') ? 'active' : '' }}">
        <div>Свод - 2</div>
        <span class="btn-label">Молиявий тақсимот</span>
    </a>

    <a href="{{ route('monitoring.report3') }}"
       class="report-nav-btn {{ request()->routeIs('monitoring.report3*') ? 'active' : '' }}">
        <div>Свод - 3</div>
        <span class="btn-label">Бўлиб тўлаш назорати</span>
    </a>
</div>