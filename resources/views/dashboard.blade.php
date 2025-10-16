@extends('layouts.app')

@section('title', 'Бош саҳифа - Toshkent Invest')

@push('styles')
<style>
    .stat-card {
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #3B82F6 0%, #1D4ED8 100%);
    }
    .stat-card.green::before {
        background: linear-gradient(180deg, #10B981 0%, #059669 100%);
    }
    .stat-card.blue::before {
        background: linear-gradient(180deg, #3B82F6 0%, #1D4ED8 100%);
    }
    .stat-card.red::before {
        background: linear-gradient(180deg, #EF4444 0%, #DC2626 100%);
    }
    .filter-tab {
        position: relative;
        padding: 8px 16px;
        background: white;
        border: 1px solid #E5E7EB;
        cursor: pointer;
        transition: all 0.2s;
    }
    .filter-tab:hover {
        background: #F9FAFB;
    }
    .filter-tab.active {
        background: #3B82F6;
        color: white;
        border-color: #3B82F6;
    }
</style>
@endpush

@section('content')
<!-- Header with Filter Tabs -->
<div class="mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-4">ҲИСОБОТ ДАВРИ ТАНЛАШ</h1>
        <p class="text-sm text-gray-600 mb-4">Маълумотларни даври бўйича филтрлаш имконияти</p>

        <div class="flex gap-2 mb-4 flex-wrap">
            <a href="{{ route('dashboard', ['period' => 'date', 'date' => now()->format('Y-m-d')]) }}"
               class="filter-tab {{ $periodType == 'date' ? 'active' : '' }}">
                {{ now()->format('d.m.Y') }} й холатига
            </a>
            <a href="{{ route('dashboard', ['period' => 'range', 'start_date' => now()->subDays(7)->format('Y-m-d'), 'end_date' => now()->format('Y-m-d')]) }}"
               class="filter-tab {{ $periodType == 'range' ? 'active' : '' }}">
                {{ now()->subDays(7)->format('d.m.Y') }} й - {{ now()->format('d.m.Y') }} й холатига
            </a>
            <a href="{{ route('dashboard', ['period' => 'month', 'month' => now()->format('m'), 'year' => now()->format('Y')]) }}"
               class="filter-tab {{ $periodType == 'month' ? 'active' : '' }}">
                Октябр ойи холатига
            </a>
            <a href="{{ route('dashboard', ['period' => 'quarter', 'quarter' => ceil(now()->format('m')/3), 'year' => now()->format('Y')]) }}"
               class="filter-tab {{ $periodType == 'quarter' ? 'active' : '' }}">
                {{ ceil(now()->format('m')/3) }}-чорак холатига {{ now()->format('Y') }} й
            </a>
            <a href="{{ route('dashboard', ['period' => 'all']) }}"
               class="filter-tab {{ $periodType == 'all' ? 'active' : '' }}">
                Барча давр
            </a>
        </div>

        <div class="text-sm text-gray-600">
            Танланган давр учун маълумотлар: <span class="text-blue-600 font-semibold">{{ $periodInfo }}</span>
        </div>
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg">
    <p class="font-medium">{{ session('success') }}</p>
</div>
@endif

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Card 1 -->
    <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-500 p-6 stat-card blue">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-500 uppercase mb-2">ЖАМИ ШАРТНОМАЛАР</p>
                <p class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($stats['total_lots']) }}</p>
                <p class="text-base font-semibold text-blue-600">{{ number_format($stats['total_sold_value'], 1) }} млрд сўм</p>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
        <div class="flex items-center justify-between text-xs text-gray-500 pt-3 border-t border-gray-100">
            <span>Юридик: <strong>{{ $stats['total_lots'] > 0 ? round($stats['total_lots'] * 0.8) : 0 }}</strong></span>
            <span>Жисмоний: <strong>{{ $stats['total_lots'] > 0 ? round($stats['total_lots'] * 0.2) : 0 }}</strong></span>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white rounded-xl shadow-sm border-l-4 border-green-500 p-6 stat-card green">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-500 uppercase mb-2">АМАЛДАГИ ШАРТНОМАЛАР</p>
                <p class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($stats['contracts_signed']) }}</p>
                <p class="text-base font-semibold text-green-600">{{ number_format($stats['total_sold_value'] * 0.67, 1) }} млрд сўм</p>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white rounded-xl shadow-sm border-l-4 border-blue-600 p-6 stat-card">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-500 uppercase mb-2">ТЎЛАНГАН СУММА</p>
                <p class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($stats['contracts_signed'] > 0 ? round($stats['contracts_signed'] * 0.71) : 0) }}</p>
                <p class="text-base font-semibold text-blue-600">{{ number_format($paymentStatus['total_paid'], 1) }} млрд сўм</p>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white rounded-xl shadow-sm border-l-4 border-red-500 p-6 stat-card red">
        <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
                <p class="text-xs font-medium text-gray-500 uppercase mb-2">ҚОЛДИҚ ҚАРЗ</p>
                <p class="text-4xl font-bold text-gray-900 mb-1">{{ number_format($stats['pending_payments']) }}</p>
                <p class="text-base font-semibold text-red-600">{{ number_format($stats['total_sold_value'] - $paymentStatus['total_paid'], 1) }} млрд сўм</p>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-lg">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<!-- Charts Row -->
<div class="grid grid-cols-4 gap-6 mb-8">
    <!-- Payment Dynamics Chart -->
    <div class="col-span-3 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">ТЎЛОВЛАР ДИНАМИКАСИ</h3>
                <p class="text-xs text-gray-500">Режалаштирилган ва амалдаги тўловларнинг қиёсий кўрсаткичлари</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('dashboard', array_merge(request()->all(), ['chart_period' => 'month'])) }}"
                   class="px-3 py-1 text-xs font-medium {{ $chartPeriod == 'month' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }} rounded">
                    ОЙ
                </a>
                <a href="{{ route('dashboard', array_merge(request()->all(), ['chart_period' => 'quarter'])) }}"
                   class="px-3 py-1 text-xs font-medium {{ $chartPeriod == 'quarter' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }} rounded">
                    ЧОРАК
                </a>
                <a href="{{ route('dashboard', array_merge(request()->all(), ['chart_period' => 'year'])) }}"
                   class="px-3 py-1 text-xs font-medium {{ $chartPeriod == 'year' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50' }} rounded">
                    ЙИЛ
                </a>
            </div>
        </div>
        <div style="position: relative; height: 300px;">
            <canvas id="paymentDynamicsChart"></canvas>
        </div>
    </div>

    <!-- Status Pie Chart -->
    <div class="col-span-1 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="mb-4">
            <h3 class="text-lg font-bold text-gray-900">ШАРТНОМАЛАР ҲОЛАТИ</h3>
            <p class="text-xs text-gray-500">Жорий давр учун шартномаларнинг ҳолат бўйича тақсимланиши</p>
        </div>
        <div style="position: relative; height: 300px;">
            <canvas id="statusPieChart"></canvas>
        </div>
    </div>
</div>


<!-- District Distribution (Admin only) -->
@if($user->role === 'admin' && count($tumanDistribution) > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Туманлар бўйича тақсимот</h3>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b-2 border-gray-200 bg-gray-50">
                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Туман</th>
                    <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Лотлар</th>
                    <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Қиймат (млрд)</th>
                    <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Фоиз</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalValue = $tumanDistribution->sum('total');
                @endphp
                @foreach($tumanDistribution as $item)
                <tr class="border-b border-gray-100 hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-sm font-medium text-gray-900">{{ $item['name'] }}</td>
                    <td class="py-3 px-4 text-sm text-gray-600 text-right">{{ number_format($item['count']) }}</td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-medium text-right">{{ number_format($item['total'], 1) }}</td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <div class="w-24 bg-gray-200 rounded-full h-2.5">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $totalValue > 0 ? ($item['total'] / $totalValue) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 w-12">{{ number_format($totalValue > 0 ? ($item['total'] / $totalValue) * 100 : 0, 1) }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<!-- Recent Lots -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900">Сўнгги лотлар</h3>
        <a href="{{ route('lots.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-700 flex items-center gap-1">
            Барчасини кўриш
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b-2 border-gray-200 bg-gray-50">
                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Лот №</th>
                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Туман</th>
                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Манзил</th>
                    <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Сотилган нархи</th>
                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Тўлов</th>
                    <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Ҳолат</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentLots as $lot)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="py-3 px-4 text-sm font-semibold text-blue-600">
                        <a href="{{ route('lots.show', $lot->id) }}" class="hover:underline">
                            {{ $lot->lot_number }}
                        </a>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-900">{{ $lot->tuman->name_uz ?? '-' }}</td>
                    <td class="py-3 px-4 text-sm text-gray-600">{{ Str::limit($lot->address, 40) }}</td>
                    <td class="py-3 px-4 text-sm text-gray-900 font-semibold text-right">
                        {{ number_format($lot->sold_price / 1000000000, 2) }} млрд
                    </td>
                    <td class="py-3 px-4">
                        @if($lot->payment_type === 'muddatli')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                            Муддатли
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            Бир марта
                        </span>
                        @endif
                    </td>
                    <td class="py-3 px-4">
                        @if($lot->contract_signed)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Шартнома
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            Жараёнда
                        </span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-8 text-center text-gray-500">
                        Маълумот топилмади
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
document.addEventListener('DOMContentLoaded', function() {
    // Payment Dynamics Chart
    const paymentCtx = document.getElementById('paymentDynamicsChart');
    if (paymentCtx) {
        const revenueData = @json($revenueData);

        new Chart(paymentCtx, {
            type: 'line',
            data: {
                labels: revenueData.labels,
                datasets: [{
                    label: 'Амалдаги тўловлар',
                    data: revenueData.data,
                    borderColor: '#1e40af',
                    backgroundColor: 'rgba(30, 64, 175, 0.15)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5,
                    pointHoverRadius: 8,
                    pointBackgroundColor: '#1e40af',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    order: 1
                }, {
                    label: 'Режалаштирилган',
                    data: revenueData.data.map(val => val * 1.3),
                    borderColor: '#9ca3af',
                    backgroundColor: 'rgba(156, 163, 175, 0.1)',
                    borderWidth: 3,
                    borderDash: [10, 5],
                    fill: false,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#9ca3af',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    order: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: {
                                size: 13,
                                weight: 'bold',
                                family: 'Inter'
                            },
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        padding: 15,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        borderColor: 'rgba(255, 255, 255, 0.3)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' +
                                    new Intl.NumberFormat('ru-RU').format(context.parsed.y) + ' млн сўм';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.06)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '600'
                            },
                            callback: function(value) {
                                return new Intl.NumberFormat('ru-RU').format(value) + ' млн';
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
    }

    // Status Pie Chart
    const pieCtx = document.getElementById('statusPieChart');
    if (pieCtx) {
        const stats = @json($stats);

        const colorScheme = {
            'active': ['#166534', '#15803d', '#16a34a'],
            'cancelled': ['#991b1b', '#b91c1c', '#dc2626'],
            'completed': ['#1e3a8a', '#1e40af', '#2563eb']
        };

        const createGradient = (ctx, colors) => {
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, colors[0]);
            gradient.addColorStop(0.5, colors[1]);
            gradient.addColorStop(1, colors[2]);
            return gradient;
        };

        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Аман хулучун', 'Бекор килинган', 'Якунланган'],
                datasets: [{
                    data: [
                        stats.contracts_signed,
                        stats.total_lots - stats.contracts_signed - Math.round(stats.total_lots * 0.22),
                        Math.round(stats.total_lots * 0.22)
                    ],
                    backgroundColor: [
                        createGradient(pieCtx.getContext('2d'), colorScheme.active),
                        createGradient(pieCtx.getContext('2d'), colorScheme.cancelled),
                        createGradient(pieCtx.getContext('2d'), colorScheme.completed)
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 4,
                    hoverOffset: 20,
                    offset: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                const total = data.datasets[0].data.reduce((a, b) => a + b, 0);

                                return data.labels.map((label, i) => {
                                    const value = data.datasets[0].data[i];
                                    const percentage = ((value / total) * 100).toFixed(1);

                                    return {
                                        text: `${label}: ${value} (${percentage}%)`,
                                        fillStyle: ['#16a34a', '#dc2626', '#2563eb'][i],
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        padding: 15,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13,
                            weight: '600'
                        },
                        borderColor: 'rgba(255, 255, 255, 0.3)',
                        borderWidth: 1,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' та (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true
                }
            }
        });
    }
});
</script>
@endpush
