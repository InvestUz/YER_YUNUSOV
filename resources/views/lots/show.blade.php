@extends('layouts.app')

@section('title', 'Лот ' . $lot->lot_number)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="{{ route('lots.index') }}" class="text-blue-600 hover:text-blue-700">Асосий саҳифа</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <a href="{{ route('lots.index') }}" class="text-blue-600 hover:text-blue-700">Лотлар</a>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                <span class="text-gray-700 font-medium">{{ $lot->lot_number }}</span>
            </nav>
        </div>
    </div>

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="px-4 py-2 bg-white bg-opacity-20 rounded-lg text-2xl font-bold">
                            Лот № {{ $lot->lot_number }}
                        </span>
                        @if($lot->lot_status)
                        <span class="px-3 py-1 bg-white bg-opacity-90 text-blue-700 rounded-full text-sm font-semibold">
                            {{ ucfirst($lot->lot_status) }}
                        </span>
                        @endif
                    </div>
                    <p class="text-blue-100 text-sm mb-4">{{ $lot->address }}</p>
                    <div class="flex items-center gap-6 text-sm">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>{{ $lot->tuman->name_uz ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span>{{ $lot->auction_date ? $lot->auction_date->format('d.m.Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('lots.edit', $lot) }}" class="px-4 py-2 bg-white text-blue-600 rounded-lg hover:bg-gray-100 transition-colors font-medium text-sm">
                        Таҳрирлаш
                    </a>
                    @if(Auth::user()->role === 'admin')
                    <form method="POST" action="{{ route('lots.destroy', $lot) }}" onsubmit="return confirm('Ростдан ҳам ўчирмоқчимисиз?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium text-sm">
                            Ўчириш
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-6">
        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded">
            {{ session('success') }}
        </div>
        @endif

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Lot Image/Map Placeholder -->
                @if($lot->latitude && $lot->longitude)
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <div class="h-64 bg-gray-100 relative">
                        <iframe
                            width="100%"
                            height="100%"
                            frameborder="0"
                            scrolling="no"
                            marginheight="0"
                            marginwidth="0"
                            src="https://www.openstreetmap.org/export/embed.html?bbox={{ $lot->longitude-0.01 }}%2C{{ $lot->latitude-0.01 }}%2C{{ $lot->longitude+0.01 }}%2C{{ $lot->latitude+0.01 }}&layer=mapnik&marker={{ $lot->latitude }}%2C{{ $lot->longitude }}">
                        </iframe>
                        @if($lot->location_url)
                        <a href="{{ $lot->location_url }}" target="_blank" class="absolute bottom-4 right-4 px-3 py-2 bg-white rounded-lg shadow-lg text-sm font-medium text-blue-600 hover:bg-gray-50">
                            Харитада кўриш
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Auction Information Card -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Аукцион маълумотлари</h3>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-xs text-gray-500">Аризаларни қабул қилишнинг охирги муддати:</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $lot->auction_date ? $lot->auction_date->format('d.m.Y') : '-' }}
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs text-gray-500">Савдо бошланиш вақти:</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $lot->auction_date ? $lot->auction_date->format('H:i') : '10:00' }}
                            </p>
                        </div>

                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs text-gray-600">Бошланғич нархи:</span>
                            </div>
                            <p class="text-lg font-bold text-blue-700">
                                {{ number_format($lot->initial_price, 0, '.', ' ') }} UZS
                            </p>
                        </div>

                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-xs text-gray-600">Закалат пули миқдори:</span>
                            </div>
                            <p class="text-lg font-bold text-green-700">
                                {{ number_format($lot->initial_price * 0.125, 0, '.', ' ') }} UZS
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-xs text-gray-500">Савдо ўтказиш тури:</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $lot->auction_type === 'ochiq' ? 'Аукцион' : 'Ёпиқ аукцион' }}
                            </p>
                        </div>

                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-xs text-gray-500">Савдо ўтказиш услуби:</span>
                            </div>
                            <p class="text-sm font-semibold text-green-700">
                                Ошириб борилиш
                            </p>
                        </div>

                        <div class="col-span-2 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-sm text-gray-700">
                                <span class="font-semibold">Лот ҳолати:</span>
                                Савдода иштирок этиш учун электрон аризаларни қабул қилиш
                            </p>
                        </div>
                    </div>

                    <!-- Basis Info -->
                    @if($lot->basis)
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="text-sm font-semibold text-gray-900 mb-2">Асос:</h4>
                        <p class="text-sm text-gray-700">{{ $lot->basis }}</p>
                    </div>
                    @endif

                    <!-- Birinchi qadam bahosi -->
                    <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                            <span class="text-xs text-gray-500">Биринчи қадам баҳоси (10%):</span>
                        </div>
                        <p class="text-base font-bold text-gray-900">
                            {{ number_format($lot->initial_price * 0.10, 0, '.', ' ') }} UZS
                        </p>
                    </div>
                </div>

                <!-- Lot Details -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Лот маълумотлари</h3>

                    <table class="w-full">
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-3 text-sm text-gray-600">Ер участкасига бўлган ҳуқуқ тури</td>
                                <td class="py-3 text-sm font-medium text-gray-900 text-right">Мулк</td>
                            </tr>
                            <tr>
                                <td class="py-3 text-sm text-gray-600">Ер майдонидан фойдаланиш шартномаси оиод бошқа маълумотлар</td>
                                <td class="py-3 text-sm font-medium text-gray-900 text-right">{{ $lot->object_type ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="py-3 text-sm text-gray-600">Участкани жойлашган манзили</td>
                                <td class="py-3 text-sm font-medium text-gray-900 text-right">{{ $lot->address }}</td>
                            </tr>
                            <tr>
                                <td class="py-3 text-sm text-gray-600">Бўш ер участкасининг белгиланган максади</td>
                                <td class="py-3 text-sm font-medium text-gray-900 text-right">{{ $lot->winner_type ?? 'уй-жой учун' }}</td>
                            </tr>
                            <tr>
                                <td class="py-3 text-sm text-gray-600">Ер участкаси кир адаги ёрлар тонфаси</td>
                                <td class="py-3 text-sm font-medium text-gray-900 text-right">{{ $lot->unique_number ?? '-' }}</td>
                            </tr>
                            @if($lot->zone)
                            <tr>
                                <td class="py-3 text-sm text-gray-600">Зона</td>
                                <td class="py-3 text-sm font-medium text-gray-900 text-right">{{ $lot->zone }}</td>
                            </tr>
                            @endif
                            @if($lot->construction_area)
                            <tr>
                                <td class="py-3 text-sm text-gray-600">Қурилиш майдони</td>
                                <td class="py-3 text-sm font-medium text-gray-900 text-right">{{ number_format($lot->construction_area, 2) }} м²</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Winner Information (if sold) -->
                @if($lot->winner_name)
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Ғолиб маълумотлари</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-yellow-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Ғолиб</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $lot->winner_name }}</p>
                        </div>
                        @if($lot->winner_phone)
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Телефон</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $lot->winner_phone }}</p>
                        </div>
                        @endif
                        @if($lot->sold_price)
                        <div class="col-span-2 p-4 bg-green-50 rounded-lg border border-green-200">
                            <p class="text-xs text-gray-600 mb-1">Сотилган нархи</p>
                            <p class="text-2xl font-bold text-green-700">{{ number_format($lot->sold_price, 0, '.', ' ') }} UZS</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Payment Schedule -->
                @if($lot->payment_type === 'muddatli' && $lot->paymentSchedules && $lot->paymentSchedules->count() > 0)
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Тўлов жадвали</h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b-2 border-gray-200 bg-gray-50">
                                    <th class="py-3 px-3 text-left font-semibold text-gray-700">№</th>
                                    <th class="py-3 px-3 text-left font-semibold text-gray-700">Тўлов санаси</th>
                                    <th class="py-3 px-3 text-right font-semibold text-gray-700">Режа</th>
                                    <th class="py-3 px-3 text-right font-semibold text-gray-700">Тўланган</th>
                                    <th class="py-3 px-3 text-center font-semibold text-gray-700">Ҳолат</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($lot->paymentSchedules->sortBy('payment_date') as $index => $schedule)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-3 font-medium text-gray-900">{{ $index + 1 }}</td>
                                    <td class="py-3 px-3 text-gray-700">{{ $schedule->payment_date->format('d.m.Y') }}</td>
                                    <td class="py-3 px-3 text-right font-medium text-gray-900">
                                        {{ number_format($schedule->planned_amount, 0, '.', ' ') }}
                                    </td>
                                    <td class="py-3 px-3 text-right font-medium {{ $schedule->actual_amount > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ number_format($schedule->actual_amount, 0, '.', ' ') }}
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        @if($schedule->actual_amount >= $schedule->planned_amount)
                                        <span class="inline-flex px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-medium">
                                            Тўланган
                                        </span>
                                        @elseif($schedule->payment_date < now())
                                        <span class="inline-flex px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-medium">
                                            Муддати ўтган
                                        </span>
                                        @else
                                        <span class="inline-flex px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-medium">
                                            Кутилмоқда
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Countdown Timer (if applicable) -->
                @if($auctionCountdown)
                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border border-orange-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 text-center">Аризалар қабул қилишнинг якунланишига</h3>

                    <div class="grid grid-cols-4 gap-2 text-center">
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-2xl font-bold text-orange-600">{{ $auctionCountdown->days }}</p>
                            <p class="text-xs text-gray-600 uppercase">СОАТ</p>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-2xl font-bold text-orange-600">{{ $auctionCountdown->h }}</p>
                            <p class="text-xs text-gray-600 uppercase">ДАҚИҚА</p>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-2xl font-bold text-orange-600">{{ $auctionCountdown->i }}</p>
                            <p class="text-xs text-gray-600 uppercase">СОНИЯ</p>
                        </div>
                        <div class="bg-white rounded-lg p-3">
                            <p class="text-2xl font-bold text-orange-600">{{ $auctionCountdown->s }}</p>
                            <p class="text-xs text-gray-600 uppercase"></p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Contract Status -->
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Шартнома ҳолати</h3>

                    @if($lot->contract_signed)
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-2 mb-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-semibold text-green-800">Тузилган</span>
                        </div>
                        @if($lot->contract_number)
                        <p class="text-sm text-gray-700 mt-2">
                            <span class="text-gray-600">Рақами:</span>
                            <span class="font-medium">{{ $lot->contract_number }}</span>
                        </p>
                        @endif
                        @if($lot->contract_date)
                        <p class="text-sm text-gray-700 mt-1">
                            <span class="text-gray-600">Санаси:</span>
                            <span class="font-medium">{{ $lot->contract_date->format('d.m.Y') }}</span>
                        </p>
                        @endif
                    </div>
                    @else
                    <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold text-yellow-800">Тузилмаган</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Payment Type -->
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Тўлов тури</h3>

                    @if($lot->payment_type === 'muddatli')
                    <div class="p-4 bg-orange-50 border border-orange-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold text-orange-800">Муддатли тўлов</span>
                        </div>
                    </div>
                    @else
                    <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-semibold text-green-800">Бир марталик тўлов</span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Payment Progress -->
                @if($paymentStats['total_amount'] > 0)
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Тўлов статистикаси</h3>

                    <div class="space-y-4">
                        <div class="p-3 bg-green-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Тўланган</p>
                            <p class="text-lg font-bold text-green-600">
                                {{ number_format($paymentStats['paid_amount'] + $paymentStats['transferred_amount'], 0, '.', ' ') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">UZS</p>
                        </div>

                        <div class="p-3 bg-orange-50 rounded-lg">
                            <p class="text-xs text-gray-600 mb-1">Қолган қарз</p>
                            <p class="text-lg font-bold text-orange-600">
                                {{ number_format($paymentStats['remaining_amount'], 0, '.', ' ') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">UZS</p>
                        </div>

                        <!-- Progress Bar -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Прогресс</span>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($paymentStats['payment_progress'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-green-600 h-3 rounded-full transition-all" style="width: {{ min($paymentStats['payment_progress'], 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Quick Stats -->
                <div class="bg-blue-50 rounded-lg border border-blue-200 p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Асосий кўрсаткичлар</h3>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-blue-100">
                            <span class="text-sm text-gray-600">Ер майдони</span>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($lot->land_area, 2) }} га</span>
                        </div>

                        @if($financialMetrics['price_per_hectare'] > 0)
                        <div class="flex items-center justify-between py-2 border-b border-blue-100">
                            <span class="text-sm text-gray-600">1 га нархи</span>
                            <span class="text-sm font-semibold text-blue-600">
                                {{ number_format($financialMetrics['price_per_hectare'] / 1000000, 2) }} млн
                            </span>
                        </div>
                        @endif

                        @if($financialMetrics['price_increase'] > 0)
                        <div class="flex items-center justify-between py-2 border-b border-blue-100">
                            <span class="text-sm text-gray-600">Фойда</span>
                            <span class="text-sm font-semibold text-green-600">
                                +{{ number_format($financialMetrics['price_increase_percent'], 1) }}%
                            </span>
                        </div>
                        @endif

                        @if($lot->investment_amount > 0)
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Инвестиция</span>
                            <span class="text-sm font-semibold text-purple-600">
                                {{ number_format($lot->investment_amount / 1000000, 2) }} млн
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- System Info -->
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Тизим маълумотлари</h3>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Яратилган:</span>
                            <span class="font-medium text-gray-900">{{ $lot->created_at->format('d.m.Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Янгиланган:</span>
                            <span class="font-medium text-gray-900">{{ $lot->updated_at->format('d.m.Y H:i') }}</span>
                        </div>
                        @if($lot->unique_number)
                        <div class="flex justify-between">
                            <span class="text-gray-600">ID:</span>
                            <span class="font-medium text-gray-900">{{ $lot->unique_number }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="bg-white rounded-lg border border-gray-200 p-5">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Иштирок этиш учун</h3>

                    <div class="space-y-3">
                        @if(!$lot->contract_signed)
                        <button class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold text-sm">
                            Ариза бериш
                        </button>
                        @endif

                        @if($lot->location_url)
                        <a href="{{ $lot->location_url }}" target="_blank" class="block w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm text-center">
                            Харитада кўриш
                        </a>
                        @endif

                        <button onclick="window.print()" class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm">
                            Чоп этиш
                        </button>
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="bg-yellow-50 rounded-lg border border-yellow-200 p-5">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Эслатма:</h4>
                            <p class="text-xs text-gray-700 leading-relaxed">
                                Ҳурматли фойдаланувчилар! Аукцион/Танлов жараёнида олиб боришлик натижа бўйича қарорларни Марказий банкнинг ҳисоботига оид техник яқладириқ қаноатлантириқған ҳолда реализатсия қилинган янги аукцион муста стно.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information Sections -->
        @if($lot->notes || $lot->master_plan_zone || $lot->yangi_uzbekiston)
        <div class="mt-6 bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Қўшимча маълумот</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($lot->master_plan_zone)
                <div class="p-4 bg-blue-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-1">Бош режа зонаси</p>
                    <p class="text-sm text-gray-900">{{ $lot->master_plan_zone }}</p>
                </div>
                @endif

                @if($lot->yangi_uzbekiston)
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-1">Янги Ўзбекистон</p>
                    <p class="text-sm text-gray-900">Ҳа</p>
                </div>
                @endif

                @if($lot->notes)
                <div class="col-span-2 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm font-medium text-gray-700 mb-2">Изоҳ</p>
                    <p class="text-sm text-gray-900 whitespace-pre-line">{{ $lot->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Distribution Statistics -->
        @if($distributionStats['total_distributed'] > 0)
        <div class="mt-6 bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Тақсимот статистикаси</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs text-gray-600 mb-1">Маҳаллий бюджет</p>
                    <p class="text-lg font-bold text-blue-600">
                        {{ number_format($distributionStats['local_budget'] / 1000000, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">млн сўм</p>
                </div>

                <div class="p-4 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-xs text-gray-600 mb-1">Ривожлантириш жамғармаси</p>
                    <p class="text-lg font-bold text-green-600">
                        {{ number_format($distributionStats['development_fund'] / 1000000, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">млн сўм</p>
                </div>

                <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                    <p class="text-xs text-gray-600 mb-1">Янги Ўзбекистон</p>
                    <p class="text-lg font-bold text-purple-600">
                        {{ number_format($distributionStats['new_uzbekistan'] / 1000000, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">млн сўм</p>
                </div>

                <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
                    <p class="text-xs text-gray-600 mb-1">Туман ҳокимияти</p>
                    <p class="text-lg font-bold text-orange-600">
                        {{ number_format($distributionStats['district_authority'] / 1000000, 2) }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">млн сўм</p>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    body {
        background: white;
    }
}
</style>
@endsection
