@extends('layouts.app')

@section('title', 'Лот ' . $lot->lot_number . ' - Toshkent Invest')

@section('content')
<!-- Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('lots.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Лот {{ $lot->lot_number }}</h1>
            @if($lot->lot_status)
            <span class="px-3 py-1 text-sm font-medium rounded-full {{ str_contains($lot->lot_status, 'якунланди') ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ $lot->lot_status }}
            </span>
            @endif
        </div>
        <p class="text-gray-600">Лот тўлиқ маълумоти</p>
    </div>
    <div class="flex items-center gap-3">
        <a href="{{ route('lots.edit', $lot) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
            Таҳрирлаш
        </a>
        @if(Auth::user()->role === 'admin')
        <form method="POST" action="{{ route('lots.destroy', $lot) }}" onsubmit="return confirm('Ростдан ҳам ўчирмоқчимисиз?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                Ўчириш
            </button>
        </form>
        @endif
    </div>
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg">
    <p class="font-medium">{{ session('success') }}</p>
</div>
@endif

<!-- Main Info Grid -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <!-- Left Column - Main Details -->
    <div class="lg:col-span-2 space-y-6">
        <!-- General Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Умумий маълумот
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Лот рақами</p>
                    <p class="font-semibold text-gray-900">{{ $lot->lot_number }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Уникал рақам</p>
                    <p class="font-semibold text-gray-900">{{ $lot->unique_number ?? '-' }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Туман</p>
                    <p class="font-semibold text-gray-900">{{ $lot->tuman->name_uz ?? '-' }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Маҳалла</p>
                    <p class="font-semibold text-gray-900">{{ $lot->mahalla->name_uz ?? '-' }}</p>
                </div>
                <div class="col-span-2 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Манзил</p>
                    <p class="font-semibold text-gray-900">{{ $lot->address }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Зона</p>
                    <p class="font-semibold text-gray-900">{{ $lot->zone ?? '-' }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Ер майдони</p>
                    <p class="font-semibold text-gray-900">{{ number_format($lot->land_area, 2) }} га</p>
                </div>
                @if($lot->latitude && $lot->longitude)
                <div class="col-span-2 p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-2">Координаталар</p>
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900 text-sm">{{ $lot->latitude }}, {{ $lot->longitude }}</p>
                        @if($lot->location_url)
                        <a href="{{ $lot->location_url }}" target="_blank" class="text-blue-600 hover:text-blue-700 text-sm font-medium flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Харитада
                        </a>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Object Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Объект маълумотлари
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Объект тури</p>
                    <p class="font-semibold text-gray-900">{{ $lot->object_type ?? '-' }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Объект тури (Рус)</p>
                    <p class="font-semibold text-gray-900">{{ $lot->object_type_ru ?? '-' }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Қурилиш майдони</p>
                    <p class="font-semibold text-gray-900">{{ $lot->construction_area ? number_format($lot->construction_area, 2) . ' м²' : '-' }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Инвестиция</p>
                    <p class="font-semibold text-gray-900">{{ $lot->investment_amount ? number_format($lot->investment_amount / 1000000, 2) . ' млн' : '-' }}</p>
                </div>
                @if($lot->master_plan_zone)
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Бош режа зонаси</p>
                    <p class="font-semibold text-gray-900">{{ $lot->master_plan_zone }}</p>
                </div>
                @endif
                @if($lot->yangi_uzbekiston)
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Янги Ўзбекистон</p>
                    <p class="font-semibold text-gray-900">{{ $lot->yangi_uzbekiston }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Auction Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                Аукцион маълумотлари
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Аукцион санаси</p>
                    <p class="font-semibold text-gray-900">{{ $lot->auction_date ? \Carbon\Carbon::parse($lot->auction_date)->format('d.m.Y') : '-' }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Аукцион тури</p>
                    <p class="font-semibold text-gray-900">{{ $lot->auction_type ?? '-' }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Асос</p>
                    <p class="font-semibold text-gray-900">{{ $lot->basis ?? '-' }}</p>
                </div>
                @if($lot->auction_expenses > 0)
                <div class="p-3 bg-red-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Аукцион ҳаражатлари</p>
                    <p class="font-semibold text-red-700">{{ number_format($lot->auction_expenses / 1000000, 2) }} млн</p>
                </div>
                @endif
                @if($lot->auction_fee > 0)
                <div class="p-3 bg-orange-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Аукцион тўлови</p>
                    <p class="font-semibold text-orange-700">{{ number_format($lot->auction_fee / 1000000, 2) }} млн</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Price Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Нарх маълумотлари
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Бошланғич нарх</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($lot->initial_price / 1000000, 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">млн сўм</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Сотилган нарх</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($lot->sold_price / 1000000, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-1">млн сўм</p>
                </div>
                @if($lot->sold_price > $lot->initial_price)
                <div class="col-span-2 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
                    <p class="text-sm text-gray-600 mb-1">Фойда</p>
                    <p class="text-2xl font-bold text-green-600">+{{ number_format(($lot->sold_price - $lot->initial_price) / 1000000, 2) }} млн</p>
                    <p class="text-sm text-green-600 mt-1">({{ number_format((($lot->sold_price - $lot->initial_price) / $lot->initial_price) * 100, 1) }}% ўсиш)</p>
                </div>
                @endif
                @if($lot->discount > 0)
                <div class="p-3 bg-orange-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Чегирма</p>
                    <p class="font-semibold text-orange-700">{{ number_format($lot->discount / 1000000, 2) }} млн</p>
                </div>
                @endif
                @if($lot->incoming_amount > 0)
                <div class="p-3 bg-blue-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Кирим суммаси</p>
                    <p class="font-semibold text-blue-700">{{ number_format($lot->incoming_amount / 1000000, 2) }} млн</p>
                </div>
                @endif
                @if($lot->davaktiv_amount > 0)
                <div class="p-3 bg-purple-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Давактив суммаси</p>
                    <p class="font-semibold text-purple-700">{{ number_format($lot->davaktiv_amount / 1000000, 2) }} млн</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Winner Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                Ғолиб маълумотлари
            </h3>
            <div class="grid grid-cols-2 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Ғолиб тури</p>
                    <p class="font-semibold text-gray-900">{{ $lot->winner_type ?? '-' }}</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Ғолиб</p>
                    <p class="font-semibold text-gray-900">{{ $lot->winner_name }}</p>
                </div>
                @if($lot->winner_phone)
                <div class="col-span-2 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Телефон рақами</p>
                    <p class="font-semibold text-gray-900">{{ $lot->winner_phone }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Right Column - Status & Payment -->
    <div class="space-y-6">
        <!-- Contract Status -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Шартнома ҳолати</h3>

            @if($lot->contract_signed)
            <div class="mb-4 p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-bold text-green-800">Шартнома тузилган</span>
                </div>
                <div class="space-y-3 mt-3">
                    <div class="p-2 bg-white rounded">
                        <p class="text-xs text-gray-600 mb-1">Шартнома рақами</p>
                        <p class="font-semibold text-gray-900">{{ $lot->contract_number ?? '-' }}</p>
                    </div>
                    <div class="p-2 bg-white rounded">
                        <p class="text-xs text-gray-600 mb-1">Шартнома санаси</p>
                        <p class="font-semibold text-gray-900">{{ $lot->contract_date ? \Carbon\Carbon::parse($lot->contract_date)->format('d.m.Y') : '-' }}</p>
                    </div>
                </div>
            </div>
            @else
            <div class="p-4 bg-yellow-50 border-2 border-yellow-200 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-bold text-yellow-800">Шартнома тузилмаган</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment Type -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Тўлов тури</h3>

            @if($lot->payment_type === 'muddatli')
            <div class="p-4 bg-orange-50 border-2 border-orange-200 rounded-lg">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-bold text-orange-800">Бўлиб тўлаш</span>
                </div>
            </div>
            @else
            <div class="p-4 bg-green-50 border-2 border-green-200 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-bold text-green-800">Бир марта тўлов</span>
                </div>
            </div>
            @endif
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Тўлов маълумотлари</h3>
            <div class="space-y-3">
                @if($lot->paid_amount > 0)
                <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                    <p class="text-xs text-gray-600 mb-1">Тўланган сумма</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($lot->paid_amount / 1000000, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-1">млн сўм</p>
                </div>
                @endif

                @if($lot->transferred_amount > 0)
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <p class="text-xs text-gray-600 mb-1">Ўтказилган сумма</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($lot->transferred_amount / 1000000, 2) }}</p>
                    <p class="text-xs text-gray-600 mt-1">млн сўм</p>
                </div>
                @endif

                @if($lot->paid_amount > 0 || $lot->transferred_amount > 0)
                <div class="pt-3 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-600">Тўлов прогресси</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format((($lot->paid_amount + $lot->transferred_amount) / $lot->sold_price) * 100, 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500" style="width: {{ (($lot->paid_amount + $lot->transferred_amount) / $lot->sold_price) * 100 }}%"></div>
                    </div>
                    <div class="mt-2 flex justify-between text-xs text-gray-500">
                        <span>{{ number_format(($lot->paid_amount + $lot->transferred_amount) / 1000000, 2) }} млн</span>
                        <span>{{ number_format($lot->sold_price / 1000000, 2) }} млн</span>
                    </div>
                </div>
                @else
                <div class="text-center py-4 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm">Тўлов маълумоти йўқ</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Timestamps -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Тизим маълумотлари</h3>
            <div class="space-y-3">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Яратилган</p>
                    <p class="text-sm font-medium text-gray-900">{{ $lot->created_at->format('d.m.Y H:i') }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500 mb-1">Янгиланган</p>
                    <p class="text-sm font-medium text-gray-900">{{ $lot->updated_at->format('d.m.Y H:i') }}</p>
                </div>
                @if($lot->deleted_at)
                <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                    <p class="text-xs text-red-600 mb-1">Ўчирилган</p>
                    <p class="text-sm font-medium text-red-700">{{ \Carbon\Carbon::parse($lot->deleted_at)->format('d.m.Y H:i') }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-sm border border-blue-200 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Тезкор статистика</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                    <span class="text-sm text-gray-600">Умумий қиймат</span>
                    <span class="text-lg font-bold text-gray-900">{{ number_format($lot->sold_price / 1000000000, 2) }} млрд</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                    <span class="text-sm text-gray-600">1 га нархи</span>
                    <span class="text-lg font-bold text-blue-600">{{ $lot->land_area > 0 ? number_format(($lot->sold_price / $lot->land_area) / 1000000, 2) : '0' }} млн</span>
                </div>
                @if($lot->sold_price > $lot->initial_price)
                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg border border-green-200">
                    <span class="text-sm text-gray-600">Фойда фоизи</span>
                    <span class="text-lg font-bold text-green-600">+{{ number_format((($lot->sold_price - $lot->initial_price) / $lot->initial_price) * 100, 1) }}%</span>
                </div>
                @endif
                @if($lot->paid_amount > 0)
                <div class="flex items-center justify-between p-3 bg-white rounded-lg">
                    <span class="text-sm text-gray-600">Қолган қарз</span>
                    <span class="text-lg font-bold text-orange-600">{{ number_format(($lot->sold_price - $lot->paid_amount - $lot->transferred_amount) / 1000000, 2) }} млн</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Payment Schedule -->
@if($lot->payment_type === 'muddatli' && $lot->contract_signed && isset($lot->paymentSchedules) && $lot->paymentSchedules->count() > 0)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            Тўлов жадвали
        </h3>
        <span class="text-sm text-gray-600">{{ $lot->paymentSchedules->count() }} та тўлов</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b-2 border-gray-200 bg-gray-50">
                    <th class="text-left py-4 px-4 text-sm font-bold text-gray-700">№</th>
                    <th class="text-left py-4 px-4 text-sm font-bold text-gray-700">Тўлов санаси</th>
                    <th class="text-right py-4 px-4 text-sm font-bold text-gray-700">Режа бўйича</th>
                    <th class="text-right py-4 px-4 text-sm font-bold text-gray-700">Тўланган</th>
                    <th class="text-right py-4 px-4 text-sm font-bold text-gray-700">Фарқ</th>
                    <th class="text-center py-4 px-4 text-sm font-bold text-gray-700">Ҳолат</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lot->paymentSchedules as $schedule)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-4 text-sm font-semibold text-gray-900">{{ $schedule->payment_number }}</td>
                    <td class="py-4 px-4">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($schedule->payment_date)->format('d.m.Y') }}</p>
                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($schedule->payment_date)->locale('uz')->translatedFormat('l') }}</p>
                        </div>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($schedule->planned_amount / 1000000, 2) }}</p>
                        <p class="text-xs text-gray-500">млн сўм</p>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <p class="text-sm font-semibold {{ $schedule->actual_amount > 0 ? 'text-green-600' : 'text-gray-400' }}">
                            {{ number_format($schedule->actual_amount / 1000000, 2) }}
                        </p>
                        <p class="text-xs text-gray-500">млн сўм</p>
                    </td>
                    <td class="py-4 px-4 text-right">
                        @php
                            $diff = $schedule->actual_amount - $schedule->planned_amount;
                        @endphp
                        <p class="text-sm font-semibold {{ $diff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff / 1000000, 2) }}
                        </p>
                        <p class="text-xs text-gray-500">млн сўм</p>
                    </td>
                    <td class="py-4 px-4 text-center">
                        @if($schedule->status === 'paid')
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Тўланган
                        </span>
                        @elseif($schedule->payment_date < now())
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Муддати ўтган
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Кутилмоқда
                        </span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t-2 border-gray-300 bg-gray-50">
                <tr>
                    <td colspan="2" class="py-4 px-4 text-sm font-bold text-gray-900">Жами:</td>
                    <td class="py-4 px-4 text-right">
                        <p class="text-sm font-bold text-gray-900">{{ number_format($lot->paymentSchedules->sum('planned_amount') / 1000000, 2) }}</p>
                        <p class="text-xs text-gray-600">млн сўм</p>
                    </td>
                    <td class="py-4 px-4 text-right">
                        <p class="text-sm font-bold text-green-600">{{ number_format($lot->paymentSchedules->sum('actual_amount') / 1000000, 2) }}</p>
                        <p class="text-xs text-gray-600">млн сўм</p>
                    </td>
                    <td class="py-4 px-4 text-right">
                        @php
                            $totalDiff = $lot->paymentSchedules->sum('actual_amount') - $lot->paymentSchedules->sum('planned_amount');
                        @endphp
                        <p class="text-sm font-bold {{ $totalDiff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ $totalDiff >= 0 ? '+' : '' }}{{ number_format($totalDiff / 1000000, 2) }}
                        </p>
                        <p class="text-xs text-gray-600">млн сўм</p>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Payment Summary -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
            <p class="text-xs text-gray-600 mb-1">Режа бўйича жами</p>
            <p class="text-xl font-bold text-blue-600">{{ number_format($lot->paymentSchedules->sum('planned_amount') / 1000000, 2) }} млн</p>
        </div>
        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
            <p class="text-xs text-gray-600 mb-1">Тўланган жами</p>
            <p class="text-xl font-bold text-green-600">{{ number_format($lot->paymentSchedules->sum('actual_amount') / 1000000, 2) }} млн</p>
        </div>
        <div class="p-4 bg-orange-50 rounded-lg border border-orange-200">
            <p class="text-xs text-gray-600 mb-1">Қолган қарз</p>
            <p class="text-xl font-bold text-orange-600">{{ number_format(($lot->paymentSchedules->sum('planned_amount') - $lot->paymentSchedules->sum('actual_amount')) / 1000000, 2) }} млн</p>
        </div>
    </div>
</div>
@endif

<!-- Additional Information -->
@if($lot->notes || $lot->basis || $lot->auction_type)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
        </svg>
        Қўшимча маълумот
    </h3>
    <div class="prose max-w-none">
        @if($lot->notes)
        <div class="p-4 bg-gray-50 rounded-lg mb-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Изоҳлар:</p>
            <p class="text-gray-900 whitespace-pre-line">{{ $lot->notes }}</p>
        </div>
        @endif
        @if($lot->basis)
        <div class="p-4 bg-blue-50 rounded-lg mb-4">
            <p class="text-sm font-medium text-gray-700 mb-2">Асос:</p>
            <p class="text-gray-900">{{ $lot->basis }}</p>
        </div>
        @endif
        @if($lot->auction_type)
        <div class="p-4 bg-purple-50 rounded-lg">
            <p class="text-sm font-medium text-gray-700 mb-2">Аукцион тури:</p>
            <p class="text-gray-900">{{ $lot->auction_type }}</p>
        </div>
        @endif
    </div>
</div>
@endif
@endsection
