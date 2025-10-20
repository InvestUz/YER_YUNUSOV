@extends('layouts.app')

@section('title', 'Янги лот қўшиш')

{{-- ====================================
     SECTION 1: STYLES
     ==================================== --}}
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container {
        cursor: crosshair;
    }

    .section-saved {
        animation: pulse 0.5s ease-in-out;
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.02);
        }
    }
</style>
@endpush

@section('content')

{{-- ====================================
     SECTION 2: MAIN CONTAINER
     ==================================== --}}
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-6">

        {{-- ====================================
             SECTION 3: PAGE HEADER
             ==================================== --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Янги лот қўшиш</h1>
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('lots.index') }}" class="hover:text-blue-700 font-medium">Лотлар</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-900 font-semibold">Янги лот</span>
            </nav>
        </div>

        {{-- ====================================
             SECTION 4: ERROR MESSAGES
             ==================================== --}}
        @if ($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-4 rounded">
            <div class="flex">
                <svg class="h-5 w-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <h3 class="text-sm font-bold text-red-800 mb-2">Хатоликлар:</h3>
                    <ul class="text-sm text-red-700 space-y-1">
                        @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        {{-- ====================================
             SECTION 5: SUCCESS MESSAGES
             ==================================== --}}
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-600 p-4 rounded">
            <div class="flex">
                <svg class="h-5 w-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        <div id="success-message" class="hidden mb-6 bg-green-50 border-l-4 border-green-600 p-4 rounded">
            <div class="flex">
                <svg class="h-5 w-5 text-green-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p id="success-text" class="text-sm font-medium text-green-800"></p>
            </div>
        </div>

        {{-- ====================================
             SECTION 6: MAIN FORM START
             ==================================== --}}
        <form action="{{ route('lots.store') }}" method="POST" id="lotForm">
            @csrf

            {{-- Hidden field to store lot_id for section saves --}}
            <input type="hidden" name="lot_id" id="lot_id" value="{{ old('lot_id', session('lot_id')) }}">

            {{-- ====================================
                 SECTION 7: FORM SECTION 1 - АСОСИЙ МАЪЛУМОТЛАР
                 ==================================== --}}
            <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden mb-6" id="section-1">

                {{-- Section Header --}}
                <div class="px-6 py-4 bg-blue-600 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">1. АСОСИЙ МАЪЛУМОТЛАР</h2>
                    <span class="section-status text-sm text-white opacity-75">
                        {{ session('section_1_saved') ? '✓ Сақланди' : 'Сақланмаган' }}
                    </span>
                </div>

                {{-- Section Content --}}
                <div class="p-6 space-y-6">

                    {{-- Field: Лот рақами --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Лот рақами <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="lot_number" id="lot_number" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition @error('lot_number') border-red-500 @enderror"
                            value="{{ old('lot_number', session('lot_data.lot_number')) }}"
                            placeholder="Мисол: 18477002">
                        @error('lot_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Field: Туман --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Туман <span class="text-red-600">*</span>
                        </label>
                        <select name="tuman_id" id="tuman_select" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition @error('tuman_id') border-red-500 @enderror">
                            <option value="">-- Туманни танланг --</option>
                            @foreach($tumans as $tuman)
                            <option value="{{ $tuman->id }}"
                                {{ old('tuman_id', session('lot_data.tuman_id')) == $tuman->id ? 'selected' : '' }}>
                                {{ $tuman->name_uz }}
                            </option>
                            @endforeach
                        </select>
                        @error('tuman_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Field: Маҳалла with Add Button --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Маҳалла / МФЙ</label>
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" id="mahalla_search"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    placeholder="Туманни танланг..."
                                    autocomplete="off"
                                    value="{{ old('mahalla_search', session('lot_data.mahalla_search')) }}"
                                    {{ old('tuman_id', session('lot_data.tuman_id')) ? '' : 'disabled' }}>
                                <input type="hidden" name="mahalla_id" id="mahalla_id"
                                    value="{{ old('mahalla_id', session('lot_data.mahalla_id')) }}">
                                <div id="mahalla_dropdown" class="hidden absolute z-50 w-full mt-1 bg-white border-2 border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <div class="p-2 text-sm text-gray-600 text-center">Туманни танланг</div>
                                </div>
                            </div>
                            <button type="button" id="add_mahalla_btn"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition border-2 border-green-700 whitespace-nowrap">
                                + Янги
                            </button>
                        </div>
                    </div>

                    {{-- Field: Тўлиқ манзил --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Тўлиқ манзил <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="address" id="address" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition @error('address') border-red-500 @enderror"
                            value="{{ old('address', session('lot_data.address')) }}"
                            placeholder="Мисол: Fidoyilar MFY">
                        @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fields: Уникал рақами and Ер майдони (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Уникал рақами <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="unique_number" id="unique_number" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition @error('unique_number') border-red-500 @enderror"
                                value="{{ old('unique_number', session('lot_data.unique_number')) }}"
                                placeholder="KA1726290029/1-1">
                            @error('unique_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Ер майдони (га) <span class="text-red-600">*</span>
                            </label>
                            <input type="number" name="land_area" id="land_area" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition @error('land_area') border-red-500 @enderror"
                                value="{{ old('land_area', session('lot_data.land_area')) }}"
                                placeholder="0.01">
                            @error('land_area')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Fields: Зона, Бош режа, Янги Ўзбекистон (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Зона</label>
                            <select name="zone" id="zone"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="1-зона" {{ old('zone', session('lot_data.zone')) == '1-зона' ? 'selected' : '' }}>1-зона</option>
                                <option value="2-зона" {{ old('zone', session('lot_data.zone')) == '2-зона' ? 'selected' : '' }}>2-зона</option>
                                <option value="3-зона" {{ old('zone', session('lot_data.zone')) == '3-зона' ? 'selected' : '' }}>3-зона</option>
                                <option value="4-зона" {{ old('zone', session('lot_data.zone')) == '4-зона' ? 'selected' : '' }}>4-зона</option>
                                <option value="5-зона" {{ old('zone', session('lot_data.zone')) == '5-зона' ? 'selected' : '' }}>5-зона</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Бош режа</label>
                            <select name="master_plan_zone" id="master_plan_zone"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="Konservatsiya" {{ old('master_plan_zone', session('lot_data.master_plan_zone')) == 'Konservatsiya' ? 'selected' : '' }}>Konservatsiya</option>
                                <option value="Rekonstruksiya" {{ old('master_plan_zone', session('lot_data.master_plan_zone')) == 'Rekonstruksiya' ? 'selected' : '' }}>Rekonstruksiya</option>
                                <option value="Renovatsiya" {{ old('master_plan_zone', session('lot_data.master_plan_zone')) == 'Renovatsiya' ? 'selected' : '' }}>Renovatsiya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Янги Ўзбекистон</label>
                            <select name="yangi_uzbekiston" id="yangi_uzbekiston"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                <option value="0" {{ old('yangi_uzbekiston', session('lot_data.yangi_uzbekiston', '0')) == '0' ? 'selected' : '' }}>Йўқ</option>
                                <option value="1" {{ old('yangi_uzbekiston', session('lot_data.yangi_uzbekiston')) == '1' ? 'selected' : '' }}>Ҳа</option>
                            </select>
                        </div>
                    </div>

                </div>

                {{-- Section Footer with Save Button --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="button" onclick="saveSection(1)" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition">
                        💾 Сақлаш
                    </button>
                </div>
            </div>

            {{-- ====================================
                 SECTION 8: FORM SECTION 2 - АУКЦИОН МАЪЛУМОТЛАРИ
                 ==================================== --}}
            <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden mb-6" id="section-2">

                {{-- Section Header --}}
                <div class="px-6 py-4 bg-green-600 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">2. АУКЦИОН МАЪЛУМОТЛАРИ</h2>
                    <span class="section-status text-sm text-white opacity-75">
                        {{ session('section_2_saved') ? '✓ Сақланди' : 'Сақланмаган' }}
                    </span>
                </div>

                {{-- Section Content --}}
                <div class="p-6 space-y-6">

                    {{-- Fields: Аукцион санаси, Сотилган нарх, Тўлов тури (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион санаси</label>
                            <input type="date" name="auction_date" id="auction_date"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('auction_date', session('lot_data.auction_date')) }}">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Сотилган нарх (сўм)</label>
                            <input type="number" name="sold_price" id="sold_price"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('sold_price', session('lot_data.sold_price')) }}"
                                placeholder="267924294.00">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Тўлов тури</label>
                            <select name="payment_type" id="payment_type"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="muddatli" {{ old('payment_type', session('lot_data.payment_type')) == 'muddatli' ? 'selected' : '' }}>Муддатли</option>
                                <option value="muddatli_emas" {{ old('payment_type', session('lot_data.payment_type')) == 'muddatli_emas' ? 'selected' : '' }}>Муддатли эмас</option>
                            </select>
                        </div>
                    </div>

                    {{-- Field: Ғолиб номи --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Ғолиб номи</label>
                        <input type="text" name="winner_name" id="winner_name"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                            value="{{ old('winner_name', session('lot_data.winner_name')) }}"
                            placeholder="GAZ NEFT-AVTO BENZIN MChJ">
                    </div>

                    {{-- Fields: Ғолиб тури, Телефон (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Ғолиб тури</label>
                            <select name="winner_type" id="winner_type"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="G`olib" {{ old('winner_type', session('lot_data.winner_type')) == 'G`olib' ? 'selected' : '' }}>G`olib</option>
                                <option value="Yuridik shaxs" {{ old('winner_type', session('lot_data.winner_type')) == 'Yuridik shaxs' ? 'selected' : '' }}>Yuridik shaxs</option>
                                <option value="Jismoniy shaxs" {{ old('winner_type', session('lot_data.winner_type')) == 'Jismoniy shaxs' ? 'selected' : '' }}>Jismoniy shaxs</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Телефон</label>
                            <input type="text" name="winner_phone" id="winner_phone"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('winner_phone', session('lot_data.winner_phone')) }}"
                                placeholder="(098) 300-5885">
                        </div>
                    </div>

                    {{-- Fields: Асос, Аукцион тури (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Асос (ПФ)</label>
                            <input type="text" name="basis" id="basis"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('basis', session('lot_data.basis')) }}"
                                placeholder="ПФ-93">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион тури</label>
                            <select name="auction_type" id="auction_type"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="ochiq" {{ old('auction_type', session('lot_data.auction_type')) == 'ochiq' ? 'selected' : '' }}>Очиқ</option>
                                <option value="yopiq" {{ old('auction_type', session('lot_data.auction_type')) == 'yopiq' ? 'selected' : '' }}>Ёпиқ</option>
                            </select>
                        </div>
                    </div>

                </div>

                {{-- Section Footer with Save Button --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="button" onclick="saveSection(2)" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition">
                        💾 Сақлаш
                    </button>
                </div>
            </div>

            {{-- ====================================
                 SECTION 9: FORM SECTION 3 - ҚЎШИМЧА МАЪЛУМОТЛАР
                 ==================================== --}}
            <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden mb-6" id="section-3">

                {{-- Section Header --}}
                <div class="px-6 py-4 bg-purple-600 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">3. ҚЎШИМЧА МАЪЛУМОТЛАР</h2>
                    <span class="section-status text-sm text-white opacity-75">
                        {{ session('section_3_saved') ? '✓ Сақланди' : 'Сақланмаган' }}
                    </span>
                </div>

                {{-- Section Content --}}
                <div class="p-6 space-y-6">

                    {{-- Field: Объект тури --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Объект тури</label>
                        <input type="text" name="object_type" id="object_type"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                            value="{{ old('object_type', session('lot_data.object_type')) }}"
                            placeholder="Yoqilg'i quyish shoxobchasi">
                    </div>

                    {{-- Fields: Latitude, Longitude (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Latitude</label>
                            <input type="text" name="latitude" id="latitude"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                                value="{{ old('latitude', session('lot_data.latitude')) }}"
                                placeholder="41.3419730499832">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Longitude</label>
                            <input type="text" name="longitude" id="longitude"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                                value="{{ old('longitude', session('lot_data.longitude')) }}"
                                placeholder="69.16886331525568">
                        </div>
                    </div>

                    {{-- Field: Interactive Map --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Харитада жойлашувни белгиланг
                            <span class="text-sm font-normal text-gray-600">(харитага босинг)</span>
                        </label>
                        <div id="map" class="w-full h-96 border-2 border-gray-300 rounded-lg"></div>
                        <p class="mt-2 text-sm text-gray-600">💡 Харитага босиб, ер участкасини белгиланг. Координаталар автоматик тўлдирилади.</p>
                    </div>

                    {{-- Field: Google Maps URL --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Google Maps URL</label>
                        <input type="url" name="location_url" id="location_url"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                            value="{{ old('location_url', session('lot_data.location_url')) }}"
                            placeholder="https://www.google.com/maps?q=...">
                        <p class="mt-1 text-sm text-gray-600">Google Maps ҳавола автоматик ясалди</p>
                    </div>

                </div>

                {{-- Section Footer with Save Button --}}
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="button" onclick="saveSection(3)" class="px-6 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold transition">
                        💾 Сақлаш
                    </button>
                </div>
            </div>

            {{-- ====================================
                 SECTION 10: FINAL SUBMIT SECTION
                 ==================================== --}}
            <div class="bg-gradient-to-r from-green-50 to-blue-50 border-2 border-green-300 shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Барча маълумотларни тўлдирдингизми?</h3>
                            <p class="text-sm text-gray-600">Барча бўлимларни сақлаб, лотни яратинг</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('lots.index') }}"
                                class="px-8 py-3 bg-white hover:bg-gray-100 text-gray-900 border-2 border-gray-400 rounded-lg font-bold transition">
                                Бекор қилиш
                            </a>
                            <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-green-600 to-blue-600 hover:from-green-700 hover:to-blue-700 text-white rounded-lg font-bold transition border-2 border-green-700 shadow-lg">
                                ✓ Лотни яратиш
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
        {{-- End of Main Form --}}

    </div>
</div>

{{-- ====================================
     SECTION 11: MAHALLA MODAL
     ==================================== --}}
<div id="mahalla_modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 bg-green-600 rounded-t-lg">
            <h3 class="text-lg font-bold text-white">Янги маҳалла қўшиш</h3>
        </div>
        <div class="p-6">
            <div class="mb-4">
                <label class="block text-sm font-bold text-gray-900 mb-2">Маҳалла номи</label>
                <input type="text" id="new_mahalla_name"
                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium"
                    placeholder="Мисол: Fidoyilar MFY">
                <p id="mahalla_error" class="mt-1 text-sm text-red-600 hidden"></p>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="button" id="cancel_mahalla"
                    class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded-lg font-bold transition">
                    Бекор
                </button>
                <button type="button" id="save_mahalla"
                    class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition">
                    Сақлаш
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

{{-- ====================================
     SECTION 12: JAVASCRIPT
     ==================================== --}}
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ====================================
// JAVASCRIPT LOGIC WITH SESSION PERSISTENCE
// ====================================

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Map variables
let map, marker;
const latInput = document.getElementById('latitude');
const lngInput = document.getElementById('longitude');
const locationUrlInput = document.getElementById('location_url');
let mapInitialized = false;

// Mahalla variables
let mahallas = @json($mahallas ?? []);
const mahallaSearch = document.getElementById('mahalla_search');
const mahallaDropdown = document.getElementById('mahalla_dropdown');
const mahallaIdInput = document.getElementById('mahalla_id');
const tumanSelect = document.getElementById('tuman_select');
const addMahallaBtn = document.getElementById('add_mahalla_btn');
const mahallaModal = document.getElementById('mahalla_modal');
const newMahallaName = document.getElementById('new_mahalla_name');
const saveMahallaBtn = document.getElementById('save_mahalla');
const cancelMahallaBtn = document.getElementById('cancel_mahalla');
const mahallaError = document.getElementById('mahalla_error');

// ====================================
// PAGE LOAD: RESTORE MAHALLA IF TUMAN SELECTED
// ====================================
document.addEventListener('DOMContentLoaded', function() {
    const selectedTumanId = tumanSelect.value;
    const savedMahallaId = mahallaIdInput.value;
    const savedMahallaSearch = mahallaSearch.value;

    if (selectedTumanId) {
        // Load mahallas for selected tuman
        fetch(`/mahallas/${selectedTumanId}`)
            .then(response => response.json())
            .then(data => {
                mahallas = data;
                mahallaSearch.disabled = false;
                mahallaSearch.placeholder = 'Маҳалла номини ёзинг...';

                // If we have saved mahalla data, keep it visible
                if (savedMahallaId && savedMahallaSearch) {
                    mahallaSearch.value = savedMahallaSearch;
                    mahallaIdInput.value = savedMahallaId;
                }
            })
            .catch(error => {
                console.error('Error loading mahallas:', error);
            });
    }
});

// ====================================
// SECTION SAVE - AJAX TO DATABASE WITH SESSION
// ====================================
function saveSection(sectionNumber) {
    const section = document.getElementById(`section-${sectionNumber}`);
    const statusEl = section.querySelector('.section-status');
    const saveBtn = section.querySelector('button[onclick*="saveSection"]');

    // Get form data
    const formData = new FormData(document.getElementById('lotForm'));
    formData.append('section_number', sectionNumber);

    // Get lot_id if exists (for updates)
    const lotIdInput = document.getElementById('lot_id');
    if (lotIdInput && lotIdInput.value) {
        formData.append('lot_id', lotIdInput.value);
    }

    // Show loading state
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '⏳ Сақланмоқда...';
    }

    // Send AJAX request
    fetch('/lots/save-section', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Save lot_id for subsequent sections
            if (data.lot_id && lotIdInput) {
                lotIdInput.value = data.lot_id;
            }

            // Update UI
            statusEl.textContent = '✓ Сақланди';
            statusEl.classList.remove('opacity-75');
            statusEl.classList.add('font-bold');

            section.classList.add('section-saved');
            setTimeout(() => section.classList.remove('section-saved'), 500);

            // Show success message
            showSuccessMessage(data.message);

            // Scroll to next section
            if (sectionNumber < 3) {
                setTimeout(() => {
                    document.getElementById(`section-${sectionNumber + 1}`).scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }, 600);
            }
        } else {
            alert('Хатолик: ' + (data.message || 'Маълумотлар сақланмади'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Хатолик юз берди. Илтимос, қайта уриниб кўринг.');
    })
    .finally(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '💾 Сақлаш';
        }
    });
}

// Show success message helper
function showSuccessMessage(text) {
    const messageEl = document.getElementById('success-message');
    const textEl = document.getElementById('success-text');

    if (messageEl && textEl) {
        textEl.textContent = text;
        messageEl.classList.remove('hidden');

        setTimeout(() => {
            messageEl.classList.add('hidden');
        }, 3000);

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// ====================================
// MAP FUNCTIONALITY
// ====================================
const mapContainer = document.getElementById('map');
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting && !mapInitialized) {
            setTimeout(() => {
                initMap();
                mapInitialized = true;
            }, 100);
        }
    });
});
observer.observe(mapContainer);

function initMap() {
    const defaultLat = 41.2995;
    const defaultLng = 69.2401;
    const oldLat = parseFloat(latInput.value) || defaultLat;
    const oldLng = parseFloat(lngInput.value) || defaultLng;

    map = L.map('map').setView([oldLat, oldLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    if (latInput.value && lngInput.value) {
        marker = L.marker([oldLat, oldLng], { draggable: true }).addTo(map);
        marker.on('dragend', function() {
            const pos = marker.getLatLng();
            updateCoordinates(pos.lat, pos.lng);
        });
    }

    map.on('click', function(e) {
        if (marker) {
            marker.setLatLng([e.latlng.lat, e.latlng.lng]);
        } else {
            marker = L.marker([e.latlng.lat, e.latlng.lng], { draggable: true }).addTo(map);
            marker.on('dragend', function() {
                const pos = marker.getLatLng();
                updateCoordinates(pos.lat, pos.lng);
            });
        }
        updateCoordinates(e.latlng.lat, e.latlng.lng);
    });
}

function updateCoordinates(lat, lng) {
    latInput.value = lat.toFixed(10);
    lngInput.value = lng.toFixed(10);
    locationUrlInput.value = `https://www.google.com/maps?q=${lat},${lng}`;
}

// ====================================
// TUMAN -> MAHALLA LOADING
// ====================================
tumanSelect.addEventListener('change', function() {
    const tumanId = this.value;
    mahallaSearch.value = '';
    mahallaIdInput.value = '';

    if (!tumanId) {
        mahallaSearch.disabled = true;
        mahallaSearch.placeholder = 'Туманни танланг...';
        mahallas = [];
        return;
    }

    // Show loading state
    mahallaSearch.disabled = true;
    mahallaSearch.placeholder = 'Юкланмоқда...';

    fetch(`/mahallas/${tumanId}`)
        .then(response => response.json())
        .then(data => {
            mahallas = data;
            mahallaSearch.disabled = false;
            mahallaSearch.placeholder = 'Маҳалла номини ёзинг...';
        })
        .catch(error => {
            console.error('Error loading mahallas:', error);
            mahallaSearch.disabled = false;
            mahallaSearch.placeholder = 'Хатолик юз берди';
        });
});

// ====================================
// MAHALLA SEARCH
// ====================================
mahallaSearch.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase().trim();

    if (!searchTerm) {
        mahallaDropdown.classList.add('hidden');
        return;
    }

    const filtered = mahallas.filter(m =>
        m && m.name && m.name.toLowerCase().includes(searchTerm)
    );

    if (filtered.length === 0) {
        mahallaDropdown.innerHTML = '<div class="p-2 text-sm text-gray-600 text-center">Натижа топилмади</div>';
    } else {
        mahallaDropdown.innerHTML = filtered.map(m => `
            <div class="p-3 hover:bg-blue-50 cursor-pointer border-b mahalla-option" data-id="${m.id}" data-name="${m.name}">
                ${m.name}
            </div>
        `).join('');

        document.querySelectorAll('.mahalla-option').forEach(option => {
            option.addEventListener('click', function() {
                mahallaSearch.value = this.dataset.name;
                mahallaIdInput.value = this.dataset.id;
                mahallaDropdown.classList.add('hidden');
            });
        });
    }

    mahallaDropdown.classList.remove('hidden');
});

document.addEventListener('click', function(e) {
    if (!mahallaSearch.contains(e.target) && !mahallaDropdown.contains(e.target)) {
        mahallaDropdown.classList.add('hidden');
    }
});

// ====================================
// ADD MAHALLA MODAL
// ====================================
addMahallaBtn.addEventListener('click', () => {
    if (!tumanSelect.value) {
        alert('Илтимос, аввал туманни танланг');
        return;
    }
    newMahallaName.value = '';
    mahallaError.classList.add('hidden');
    mahallaModal.classList.remove('hidden');
});

cancelMahallaBtn.addEventListener('click', () => {
    mahallaModal.classList.add('hidden');
});

saveMahallaBtn.addEventListener('click', function() {
    const name = newMahallaName.value.trim();

    if (!name) {
        mahallaError.textContent = 'Маҳалла номини киритинг';
        mahallaError.classList.remove('hidden');
        return;
    }

    if (!tumanSelect.value) {
        mahallaError.textContent = 'Туман танланмаган';
        mahallaError.classList.remove('hidden');
        return;
    }

    saveMahallaBtn.disabled = true;
    saveMahallaBtn.textContent = 'Сақланмоқда...';
    mahallaError.classList.add('hidden');

    fetch('/mahallas', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            tuman_id: tumanSelect.value,
            name: name
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new mahalla to list
            mahallas.push(data.mahalla);

            // Set as selected
            mahallaSearch.value = data.mahalla.name;
            mahallaIdInput.value = data.mahalla.id;

            // Close modal
            mahallaModal.classList.add('hidden');

            // Show success
            showSuccessMessage('Маҳалла муваффақиятли қўшилди');
        } else {
            mahallaError.textContent = data.message || 'Хатолик юз берди';
            mahallaError.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mahallaError.textContent = 'Хатолик юз берди. Қайта уриниб кўринг';
        mahallaError.classList.remove('hidden');
    })
    .finally(() => {
        saveMahallaBtn.disabled = false;
        saveMahallaBtn.textContent = 'Сақлаш';
    });
});

// Close modal on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        mahallaModal.classList.add('hidden');
    }
});

// ====================================
// FINAL FORM SUBMISSION
// ====================================
document.getElementById('lotForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Add wizard_step for final submission
    let wizardStepInput = document.querySelector('input[name="wizard_step"]');
    if (!wizardStepInput) {
        wizardStepInput = document.createElement('input');
        wizardStepInput.type = 'hidden';
        wizardStepInput.name = 'wizard_step';
        this.appendChild(wizardStepInput);
    }
    wizardStepInput.value = '3'; // Mark as complete

    // Submit form normally
    this.submit();
});

// ====================================
// AUTO-SAVE FORM DATA TO PREVENT LOSS
// ====================================
let autoSaveTimeout;
const formInputs = document.querySelectorAll('#lotForm input, #lotForm select, #lotForm textarea');

formInputs.forEach(input => {
    input.addEventListener('change', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            console.log('Form data changed, ready for section save');
        }, 500);
    });
});

</script>
@endpush
