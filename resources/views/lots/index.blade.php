@extends('layouts.app')

@section('title', 'Лотлар - Toshkent Invest')

@section('content')
    <div class="min-h-screen bg-gray-50">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 mb-6">
            <div class="max-w-full mx-auto px-6 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-900 mb-1">Лотлар бошқаруви</h1>
                        <p class="text-sm text-gray-600">Барча лотлар рўйхати ва мониторинг</p>
                    </div>
                    @if (Auth::user()->role === 'admin' || Auth::user()->role === 'district_user')
                        <a href="{{ route('lots.create') }}"
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Янги лот қўшиш
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="max-w-full mx-auto px-6">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-6">
                <!-- Card 1 -->
                <div class="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Жами лотлар</p>
                    <p class="text-3xl font-semibold text-gray-900">{{ number_format($lots->total()) }}</p>
                </div>

                <!-- Card 2 -->
                <div class="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-green-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Шартнома тузилган</p>
                    <p class="text-3xl font-semibold text-green-600">
                        {{ number_format($lots->where('contract_signed', true)->count()) }}</p>
                </div>

                <!-- Card 3 -->
                <div class="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Бўлиб тўлаш</p>
                    <p class="text-3xl font-semibold text-orange-600">
                        {{ number_format($lots->where('payment_type', 'muddatli')->count()) }}</p>
                </div>

                <!-- Card 4 -->
                <div class="bg-white rounded-lg border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600 mb-1">Умумий қиймат</p>
                    <p class="text-3xl font-semibold text-purple-600">
                        {{ number_format($lots->sum('sold_price') / 1000000000, 2) }} <span
                            class="text-base text-gray-600">млрд</span></p>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white rounded-lg border border-gray-200 mb-6">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Фильтрлаш
                    </h2>
                    <button type="button" onclick="toggleAdvancedFilters()"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                        <span id="toggle-text">Кенгайтирилган филтрлар</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>

                <form method="GET" action="{{ route('lots.index') }}" id="filter-form" class="p-6">
                    <!-- Basic Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Қидириш</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Лот рақами, манзил..."
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        @if (Auth::user()->role === 'admin')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Туман</label>
                                <select name="tuman_id" id="tuman_select" onchange="loadMahallas(this.value)"
                                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Барчаси</option>
                                    @foreach ($tumans as $tuman)
                                        <option value="{{ $tuman->id }}"
                                            {{ request('tuman_id') == $tuman->id ? 'selected' : '' }}>
                                            {{ $tuman->name_uz }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Маҳалла</label>
                            <select name="mahalla_id" id="mahalla_select"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Барчаси</option>
                                @foreach ($mahallas as $mahalla)
                                    <option value="{{ $mahalla->id }}"
                                        {{ request('mahalla_id') == $mahalla->id ? 'selected' : '' }}>
                                        {{ $mahalla->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Тўлов тури</label>
                            <select name="payment_type"
                                class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Барчаси</option>
                                <option value="muddatli" {{ request('payment_type') === 'muddatli' ? 'selected' : '' }}>
                                    Муддатли</option>
                                <option value="muddatli_emas"
                                    {{ request('payment_type') === 'muddatli_emas' ? 'selected' : '' }}>Муддатли эмас
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Advanced Filters -->
                    <div id="advanced-filters" class="hidden border-t border-gray-200 pt-5">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Zone Filters -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Зона</label>
                                <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                                    @foreach ($filterOptions['zones'] ?? [] as $zone)
                                        <label class="flex items-center mb-2 text-sm">
                                            <input type="checkbox" name="zones[]" value="{{ $zone }}"
                                                {{ in_array($zone, request('zones', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-gray-700">{{ $zone }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Master Plan Zone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Бош режа зонаси</label>
                                <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                                    @foreach ($filterOptions['master_plan_zones'] ?? [] as $zone)
                                        <label class="flex items-center mb-2 text-sm">
                                            <input type="checkbox" name="master_plan_zones[]"
                                                value="{{ $zone }}"
                                                {{ in_array($zone, request('master_plan_zones', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-gray-700">{{ $zone }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Yangi Uzbekiston -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Янги Ўзбекистон</label>
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm">
                                        <input type="radio" name="yangi_uzbekiston" value=""
                                            {{ request('yangi_uzbekiston') === null ? 'checked' : '' }}
                                            class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">Барчаси</span>
                                    </label>
                                    <label class="flex items-center text-sm">
                                        <input type="radio" name="yangi_uzbekiston" value="1"
                                            {{ request('yangi_uzbekiston') === '1' ? 'checked' : '' }}
                                            class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">Ҳа</span>
                                    </label>
                                    <label class="flex items-center text-sm">
                                        <input type="radio" name="yangi_uzbekiston" value="0"
                                            {{ request('yangi_uzbekiston') === '0' ? 'checked' : '' }}
                                            class="border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">Йўқ</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Construction Types -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Конструксия тури</label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                                @foreach ($filterOptions['construction_types'] ?? [] as $key => $label)
                                    <label
                                        class="flex items-center px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-sm">
                                        <input type="checkbox" name="construction_types[]" value="{{ $key }}"
                                            {{ in_array($key, request('construction_types', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Object Types -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Объект тури</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach ($filterOptions['object_types'] ?? [] as $key => $label)
                                    <label
                                        class="flex items-center px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-sm">
                                        <input type="checkbox" name="object_types[]" value="{{ $key }}"
                                            {{ in_array($key, request('object_types', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- More Filters Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <!-- Payment Types Extended -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Тўлов тури
                                    (кенгайтирилган)</label>
                                <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                                    @foreach ($filterOptions['payment_types'] ?? [] as $key => $label)
                                        <label class="flex items-center mb-2 text-sm">
                                            <input type="checkbox" name="payment_types_extended[]"
                                                value="{{ $key }}"
                                                {{ in_array($key, request('payment_types_extended', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Basis Types -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Асос</label>
                                <div class="space-y-2">
                                    @foreach ($filterOptions['basis_types'] ?? [] as $key => $label)
                                        <label class="flex items-center text-sm">
                                            <input type="checkbox" name="basis_types[]" value="{{ $key }}"
                                                {{ in_array($key, request('basis_types', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Auction Types -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ўтказиш тури</label>
                                <div class="space-y-2">
                                    @foreach ($filterOptions['auction_types'] ?? [] as $key => $label)
                                        <label class="flex items-center text-sm">
                                            <input type="checkbox" name="auction_types[]" value="{{ $key }}"
                                                {{ in_array($key, request('auction_types', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Lot Status -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Лот ҳолати</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach ($filterOptions['lot_statuses'] ?? [] as $key => $label)
                                    <label
                                        class="flex items-center px-3 py-2 border border-gray-200 rounded-lg hover:bg-gray-50 text-sm">
                                        <input type="checkbox" name="lot_statuses[]" value="{{ $key }}"
                                            {{ in_array($key, request('lot_statuses', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Contract & Winner -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <!-- Contract Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Шартнома ҳолати</label>
                                <div class="space-y-2">
                                    <label class="flex items-center text-sm">
                                        <input type="checkbox" name="contract_statuses[]" value="signed"
                                            {{ in_array('signed', request('contract_statuses', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">Тузилган</span>
                                    </label>
                                    <label class="flex items-center text-sm">
                                        <input type="checkbox" name="contract_statuses[]" value="not_signed"
                                            {{ in_array('not_signed', request('contract_statuses', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">Тузилмаган</span>
                                    </label>
                                    <label class="flex items-center text-sm">
                                        <input type="checkbox" name="contract_statuses[]" value="with_date"
                                            {{ in_array('with_date', request('contract_statuses', [])) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">Санаси билан</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Winner Type -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ғолиб тури</label>
                                <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
                                    @foreach ($filterOptions['winner_types'] ?? [] as $type)
                                        <label class="flex items-center mb-2 text-sm">
                                            <input type="checkbox" name="winner_types[]" value="{{ $type }}"
                                                {{ in_array($type, request('winner_types', [])) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            <span class="ml-2 text-gray-700">{{ $type }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Date and Price Ranges -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Auction Date Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Аукцион санаси</label>
                                <div class="space-y-2">
                                    <input type="date" name="auction_date_from"
                                        value="{{ request('auction_date_from') }}" placeholder="Дан"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <input type="date" name="auction_date_to"
                                        value="{{ request('auction_date_to') }}" placeholder="Гача"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Нарх диапазони (сўм)</label>
                                <div class="space-y-2">
                                    <input type="number" name="price_from" value="{{ request('price_from') }}"
                                        placeholder="Дан" step="1000000"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <input type="number" name="price_to" value="{{ request('price_to') }}"
                                        placeholder="Гача" step="1000000"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>

                            <!-- Land Area Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Ер майдони (га)</label>
                                <div class="space-y-2">
                                    <input type="number" name="land_area_from" value="{{ request('land_area_from') }}"
                                        placeholder="Дан" step="0.01"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                    <input type="number" name="land_area_to" value="{{ request('land_area_to') }}"
                                        placeholder="Гача" step="0.01"
                                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-5 border-t border-gray-200">
                        <button type="submit"
                            class="px-5 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Қидириш
                        </button>
                        <a href="{{ route('lots.index') }}"
                            class="px-5 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors font-medium">
                            Тозалаш
                        </a>
                        <button type="button" onclick="exportToExcel()"
                            class="px-5 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors font-medium">
                            Excel юклаш
                        </button>

                        <!-- Sorting -->
                        <div class="flex gap-2 ml-auto">
                            <select name="sort"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="auction_date" {{ request('sort') === 'auction_date' ? 'selected' : '' }}>
                                    Аукцион санаси</option>
                                <option value="lot_number" {{ request('sort') === 'lot_number' ? 'selected' : '' }}>Лот
                                    рақами</option>
                                <option value="sold_price" {{ request('sort') === 'sold_price' ? 'selected' : '' }}>Нарх
                                </option>
                                <option value="land_area" {{ request('sort') === 'land_area' ? 'selected' : '' }}>Ер
                                    майдони</option>
                            </select>
                            <select name="direction"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>А → Я
                                </option>
                                <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>Я → А
                                </option>
                            </select>
                            <select name="per_page"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>


            <!-- Table -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase">№</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Лот рақами
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Туман</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Ер манзили
                                </th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Уникал рақами
                                </th>
                                <th class="text-right py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Ер майдони
                                </th>
                                <th class="text-right py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Бошланғич
                                    нархи</th>
                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Аукцион
                                    санаси</th>
                                <th class="text-right py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Сотилган
                                    нархи</th>
                                <th class="text-left py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Ғолиб номи
                                </th>
                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Тўлов тури
                                </th>
                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Лот ҳолати
                                </th>
                                <th class="text-center py-3 px-4 text-xs font-semibold text-gray-700 uppercase">Амаллар
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($lots as $index => $lot)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-sm text-gray-600">
                                        {{ ($lots->currentPage() - 1) * $lots->perPage() + $index + 1 }}</td>
                                    <td class="py-3 px-4">
                                        <a href="{{ route('lots.show', $lot->id) }}"
                                            class="text-sm font-medium text-blue-600 hover:text-blue-700">
                                            {{ $lot->lot_number }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-4 text-sm text-gray-700">{{ $lot->tuman->name_uz ?? '-' }}</td>
                                    <td class="py-3 px-4 text-sm text-gray-600">{{ Str::limit($lot->address, 40) }}</td>
                                    <td class="py-3 px-4">
                                        @if ($lot->unique_number)
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-medium bg-indigo-50 text-indigo-700 rounded">
                                                {{ $lot->unique_number }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-sm">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right text-sm text-gray-700">
                                        {{ number_format($lot->land_area, 2) }} га</td>
                                    <td class="py-3 px-4 text-right text-sm text-gray-600">
                                        {{ number_format($lot->initial_price / 1000000, 2) }} млн</td>
                                    <td class="py-3 px-4 text-center text-sm text-gray-700">
                                        @if ($lot->auction_date)
                                            {{ date('d.m.Y', strtotime($lot->auction_date)) }}
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <span
                                            class="text-sm font-semibold text-green-600">{{ number_format($lot->sold_price / 1000000, 2) }}
                                            млн</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-medium text-xs">
                                                {{ mb_substr($lot->winner_name, 0, 1) }}
                                            </div>
                                            <span
                                                class="text-sm text-gray-700">{{ Str::limit($lot->winner_name, 20) }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($lot->payment_type === 'muddatli')
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">
                                                Муддатли
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                Шартнома
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($lot->contract_signed)
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded text-xs font-medium bg-green-50 text-green-700 border border-green-200">
                                                Шартнома
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex px-2.5 py-1 rounded text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">
                                                Муддатли
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('lots.show', $lot->id) }}"
                                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('lots.edit', $lot->id) }}"
                                                class="p-1.5 text-gray-600 hover:bg-gray-50 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="13" class="py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                </path>
                                            </svg>
                                            <p class="text-base font-medium text-gray-500">Маълумот топилмади</p>
                                            <p class="text-sm text-gray-400 mt-1">Филтрларни ўзгартириб кўринг</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($lots->hasPages())
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                {{ $lots->firstItem() }}-{{ $lots->lastItem() }} / {{ number_format($lots->total()) }}
                            </div>
                            <div>
                                {{ $lots->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
<script>
function toggleAdvancedFilters() {
    const el = document.getElementById('advanced-filters');
    const text = document.getElementById('toggle-text');
    const icon = document.getElementById('toggle-icon');

    if (el.classList.contains('hidden')) {
        el.classList.remove('hidden');
        text.textContent = 'Филтрларни яшириш';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>';
    } else {
        el.classList.add('hidden');
        text.textContent = 'Кенгайтирилган филтрлар';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>';
    }
}

function loadMahallas(tumanId) {
    const select = document.getElementById('mahalla_select');
    if (!tumanId) {
        select.innerHTML = '<option value="">Барчаси</option>';
        return;
    }
    select.innerHTML = '<option value="">Юкланмоқда...</option>';
    select.disabled = true;
    fetch(`/mahallas/by-tuman?tuman_id=${tumanId}`)
        .then(response => response.json())
        .then(data => {
            select.innerHTML = '<option value="">Барчаси</option>';
            data.forEach(mahalla => {
                const option = document.createElement('option');
                option.value = mahalla.id;
                option.textContent = mahalla.name_uz;
                select.appendChild(option);
            });
            select.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            select.innerHTML = '<option value="">Хатолик</option>';
            select.disabled = false;
        });
}

function exportToExcel() {
    const form = document.getElementById('filter-form');
    const formData = new FormData(form);
    const params = new URLSearchParams(formData);
    window.location.href = `/lots/export?${params.toString()}`;
}

document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const advancedParams = [
        'zones', 'master_plan_zones', 'yangi_uzbekiston',
        'construction_types', 'object_types', 'payment_types_extended',
        'basis_types', 'auction_types', 'lot_statuses',
        'contract_statuses', 'winner_types',
        'auction_date_from', 'auction_date_to',
        'price_from', 'price_to', 'land_area_from', 'land_area_to'
    ];

    // Only auto-open if advanced filters are actually used
    const hasAdvancedFilters = advancedParams.some(param => urlParams.has(param));

    if (hasAdvancedFilters) {
        const el = document.getElementById('advanced-filters');
        const text = document.getElementById('toggle-text');
        const icon = document.getElementById('toggle-icon');

        el.classList.remove('hidden');
        text.textContent = 'Филтрларни яшириш';
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>';
    }

    // Load mahallas if tuman is pre-selected
    const tumanSelect = document.getElementById('tuman_select');
    if (tumanSelect && tumanSelect.value) {
        loadMahallas(tumanSelect.value);
    }
});
</script>

@endsection
