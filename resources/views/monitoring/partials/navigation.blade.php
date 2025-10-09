{{-- Add this navigation section to your monitoring layout --}}

<style>
    .report-nav {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        padding: 15px;
        background: #ffffff;
        border: 1px solid #d4d4d4;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
    }

    .report-nav-btn {
        flex: 1;
        padding: 12px 24px;
        background: #ffffff;
        border: 2px solid #d4d4d4;
        color: #2c5282;
        text-decoration: none;
        font-size: 14px;
        font-weight: 600;
        font-family: 'Times New Roman', Times, serif;
        text-align: center;
        transition: all 0.2s;
        position: relative;
        cursor: pointer;
    }

    .report-nav-btn:hover {
        background: #f0f4f8;
        border-color: #2c5282;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(44, 82, 130, 0.15);
    }

    .report-nav-btn.active {
        background: #2c5282;
        color: #ffffff;
        border-color: #2c5282;
        box-shadow: 0 2px 6px rgba(44, 82, 130, 0.25);
    }

    .report-nav-btn.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 8px solid transparent;
        border-right: 8px solid transparent;
        border-bottom: 8px solid #2c5282;
    }

    .report-nav-btn .btn-label {
        display: block;
        font-size: 11px;
        opacity: 0.8;
        margin-top: 2px;
    }

    @media print {
        .report-nav {
            display: none !important;
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
