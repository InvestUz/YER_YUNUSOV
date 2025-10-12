{{-- File: resources/views/analytics/lot-views.blade.php --}}

@extends('layouts.app')

@section('title', 'Кўришлар статистикаси - Лот ' . $lot->lot_number)

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-white border-b-2 border-gray-300">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Кўришлар статистикаси</h1>
                    <p class="text-sm text-gray-600 mt-1">Лот: {{ $lot->lot_number }} - {{ $lot->address }}</p>
                </div>
                <a href="{{ route('lots.show', $lot) }}" 
                   class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white border border-gray-900 transition text-sm font-medium">
                    Лотга қайтиш
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Жами кўришлар</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_views']) }}</div>
            </div>

            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Уникал IP</div>
                <div class="text-3xl font-bold text-blue-700">{{ number_format($stats['unique_ips']) }}</div>
            </div>

            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Рўйхатдан ўтган</div>
                <div class="text-3xl font-bold text-green-700">{{ number_format($stats['authenticated_views']) }}</div>
            </div>

            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Мехмон</div>
                <div class="text-3xl font-bold text-gray-700">{{ number_format($stats['anonymous_views']) }}</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Device Stats -->
            <div class="bg-white border border-gray-300 shadow-sm">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                    <h3 class="text-sm font-bold text-gray-900">Қурилмалар</h3>
                </div>
                <div class="p-6">
                    @foreach($deviceStats as $stat)
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-700">{{ $stat->device }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stat->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 h-2 mb-4">
                        <div class="bg-blue-600 h-2" style="width: {{ ($stat->count / $stats['total_views']) * 100 }}%"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Browser Stats -->
            <div class="bg-white border border-gray-300 shadow-sm">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                    <h3 class="text-sm font-bold text-gray-900">Браузерлар</h3>
                </div>
                <div class="p-6">
                    @foreach($browserStats as $stat)
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-700">{{ $stat->browser }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stat->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 h-2 mb-4">
                        <div class="bg-green-600 h-2" style="width: {{ ($stat->count / $stats['total_views']) * 100 }}%"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Platform Stats -->
            <div class="bg-white border border-gray-300 shadow-sm">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                    <h3 class="text-sm font-bold text-gray-900">Платформалар</h3>
                </div>
                <div class="p-6">
                    @foreach($platformStats as $stat)
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-700">{{ $stat->platform }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stat->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 h-2 mb-4">
                        <div class="bg-purple-600 h-2" style="width: {{ ($stat->count / $stats['total_views']) * 100 }}%"></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Detailed Views Table -->
        <div class="bg-white border border-gray-300 shadow-sm">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                <h3 class="text-base font-bold text-gray-900">Батафсил кўришлар</h3>
            </div>

            <!-- Filters -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <form method="GET" class="flex gap-4">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="px-3 py-2 border border-gray-300 text-sm">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="px-3 py-2 border border-gray-300 text-sm">
                    <button type="submit" 
                            class="px-4 py-2 bg-gray-900 text-white text-sm font-medium hover:bg-gray-800">
                        Фильтрлаш
                    </button>
                    <a href="{{ route('analytics.lot.views', $lot) }}" 
                       class="px-4 py-2 bg-white border border-gray-300 text-gray-900 text-sm font-medium hover:bg-gray-50">
                        Тозалаш
                    </a>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b-2 border-gray-300">
                        <tr>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Сана/Вақт</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Фойдаланувчи</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">IP Манзил</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Қурилма</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Браузер</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Платформа</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($views as $view)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-gray-900">{{ $view->viewed_at->format('d.m.Y H:i:s') }}</td>
                            <td class="py-3 px-4">
                                @if($view->user)
                                <span class="text-gray-900 font-medium">{{ $view->user->name }}</span>
                                @else
                                <span class="text-gray-500 italic">Мехмон</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-700 font-mono text-xs">{{ $view->ip_address }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $view->device }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $view->browser }}</td>
                            <td class="py-3 px-4 text-gray-700">{{ $view->platform }}</td>
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

            @if($views->hasPages())
            <div class="px-6 py-4 border-t border-gray-300">
                {{ $views->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection