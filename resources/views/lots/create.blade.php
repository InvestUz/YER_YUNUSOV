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
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.02); }
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
             SECTION 5: SUCCESS MESSAGES (Hidden by default)
             ==================================== --}}
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

            {{-- ====================================
                 SECTION 7: FORM SECTION 1 - АСОСИЙ МАЪЛУМОТЛАР
                 ==================================== --}}
            <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden mb-6" id="section-1">
                
                {{-- Section Header --}}
                <div class="px-6 py-4 bg-blue-600 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-white">1. АСОСИЙ МАЪЛУМОТЛАР</h2>
                    <span class="section-status text-sm text-white opacity-75">Сақланмаган</span>
                </div>

                {{-- Section Content --}}
                <div class="p-6 space-y-6">
                    
                    {{-- Field: Лот рақами --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Лот рақами <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="lot_number" id="lot_number" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('lot_number') }}"
                            placeholder="Мисол: 18477002">
                    </div>

                    {{-- Field: Туман --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Туман <span class="text-red-600">*</span>
                        </label>
                        <select name="tuman_id" id="tuman_select" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                            <option value="">-- Туманни танланг --</option>
                            @foreach($tumans as $tuman)
                            <option value="{{ $tuman->id }}" {{ old('tuman_id') == $tuman->id ? 'selected' : '' }}>
                                {{ $tuman->name_uz }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Field: Маҳалла with Add Button --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Маҳалла / МФЙ</label>
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" id="mahalla_search"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    placeholder="Туманни танланг..." autocomplete="off" disabled>
                                <input type="hidden" name="mahalla_id" id="mahalla_id" value="{{ old('mahalla_id') }}">
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
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('address') }}"
                            placeholder="Мисол: Fidoyilar MFY">
                    </div>

                    {{-- Fields: Уникал рақами and Ер майдони (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Уникал рақами <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="unique_number" id="unique_number" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('unique_number') }}"
                                placeholder="KA1726290029/1-1">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Ер майдони (га) <span class="text-red-600">*</span>
                            </label>
                            <input type="number" step="0.01" name="land_area" id="land_area" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('land_area') }}"
                                placeholder="0.01">
                        </div>
                    </div>

                    {{-- Field: Бошланғич нархи --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Бошланғич нархи (сўм) <span class="text-red-600">*</span>
                        </label>
                        <input type="number" step="0.01" name="initial_price" id="initial_price" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('initial_price') }}"
                            placeholder="250000000.00">
                    </div>

                    {{-- Fields: Зона, Бош режа, Янги Ўзбекистон (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Зона</label>
                            <select name="zone" id="zone" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="1-зона" {{ old('zone') == '1-зона' ? 'selected' : '' }}>1-зона</option>
                                <option value="2-зона" {{ old('zone') == '2-зона' ? 'selected' : '' }}>2-зона</option>
                                <option value="3-зона" {{ old('zone') == '3-зона' ? 'selected' : '' }}>3-зона</option>
                                <option value="4-зона" {{ old('zone') == '4-зона' ? 'selected' : '' }}>4-зона</option>
                                <option value="5-зона" {{ old('zone') == '5-зона' ? 'selected' : '' }}>5-зона</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Бош режа</label>
                            <select name="master_plan_zone" id="master_plan_zone" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="Konservatsiya" {{ old('master_plan_zone') == 'Konservatsiya' ? 'selected' : '' }}>Konservatsiya</option>
                                <option value="Rekonstruksiya" {{ old('master_plan_zone') == 'Rekonstruksiya' ? 'selected' : '' }}>Rekonstruksiya</option>
                                <option value="Renovatsiya" {{ old('master_plan_zone') == 'Renovatsiya' ? 'selected' : '' }}>Renovatsiya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Янги Ўзбекистон</label>
                            <select name="yangi_uzbekiston" id="yangi_uzbekiston" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                <option value="0" {{ old('yangi_uzbekiston', '0') == '0' ? 'selected' : '' }}>Йўқ</option>
                                <option value="1" {{ old('yangi_uzbekiston') == '1' ? 'selected' : '' }}>Ҳа</option>
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
                    <span class="section-status text-sm text-white opacity-75">Сақланмаган</span>
                </div>

                {{-- Section Content --}}
                <div class="p-6 space-y-6">
                    
                    {{-- Fields: Аукцион санаси, Сотилган нарх, Тўлов тури (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион санаси</label>
                            <input type="date" name="auction_date" id="auction_date"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('auction_date') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Сотилган нарх (сўм)</label>
                            <input type="number" step="0.01" name="sold_price" id="sold_price"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('sold_price') }}"
                                placeholder="267924294.00">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Тўлов тури</label>
                            <select name="payment_type" id="payment_type" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="muddatli" {{ old('payment_type') == 'muddatli' ? 'selected' : '' }}>Муддатли</option>
                                <option value="muddatli_emas" {{ old('payment_type') == 'muddatli_emas' ? 'selected' : '' }}>Муддатли эмас</option>
                            </select>
                        </div>
                    </div>

                    {{-- Field: Ғолиб номи --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Ғолиб номи</label>
                        <input type="text" name="winner_name" id="winner_name"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                            value="{{ old('winner_name') }}"
                            placeholder="GAZ NEFT-AVTO BENZIN MChJ">
                    </div>

                    {{-- Fields: Ғолиб тури, Телефон (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Ғолиб тури</label>
                            <select name="winner_type" id="winner_type" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="G`olib" {{ old('winner_type') == 'G`olib' ? 'selected' : '' }}>G`olib</option>
                                <option value="Yuridik shaxs" {{ old('winner_type') == 'Yuridik shaxs' ? 'selected' : '' }}>Yuridik shaxs</option>
                                <option value="Jismoniy shaxs" {{ old('winner_type') == 'Jismoniy shaxs' ? 'selected' : '' }}>Jismoniy shaxs</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Телефон</label>
                            <input type="text" name="winner_phone" id="winner_phone"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('winner_phone') }}"
                                placeholder="(098) 300-5885">
                        </div>
                    </div>

                    {{-- Fields: Асос, Аукцион тури, Лот холати (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Асос (ПФ)</label>
                            <input type="text" name="basis" id="basis"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('basis') }}"
                                placeholder="ПФ-93">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион тури</label>
                            <select name="auction_type" id="auction_type" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="ochiq" {{ old('auction_type') == 'ochiq' ? 'selected' : '' }}>Очиқ</option>
                                <option value="yopiq" {{ old('auction_type') == 'yopiq' ? 'selected' : '' }}>Ёпиқ</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Лот холати</label>
                            <input type="text" name="lot_status" id="lot_status"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('lot_status', 'active') }}"
                                placeholder="active">
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
                    <span class="section-status text-sm text-white opacity-75">Сақланмаган</span>
                </div>

                {{-- Section Content --}}
                <div class="p-6 space-y-6">
                    
                    {{-- Field: Объект тури --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Объект тури</label>
                        <input type="text" name="object_type" id="object_type"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                            value="{{ old('object_type') }}"
                            placeholder="Yoqilg'i quyish shoxobchasi">
                    </div>

                    {{-- Fields: Қурилиш майдони, Инвестиция суммаси (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Қурилиш майдони (м²)</label>
                            <input type="number" step="0.01" name="construction_area" id="construction_area"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                                value="{{ old('construction_area') }}"
                                placeholder="500.00">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Инвестиция суммаси (сўм)</label>
                            <input type="number" step="0.01" name="investment_amount" id="investment_amount"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                                value="{{ old('investment_amount') }}"
                                placeholder="1000000000.00">
                        </div>
                    </div>

                    {{-- Fields: Latitude, Longitude (Grid) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Latitude</label>
                            <input type="text" name="latitude" id="latitude"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                                value="{{ old('latitude') }}"
                                placeholder="41.3419730499832">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Longitude</label>
                            <input type="text" name="longitude" id="longitude"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                                value="{{ old('longitude') }}"
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
                            value="{{ old('location_url') }}"
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
     SECTION 12: JAVASCRIPT - Leaflet Map Library
     ==================================== --}}
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
// ====================================
// PART 1: GLOBAL VARIABLES & CONFIGURATION
// ====================================

// Get CSRF token for AJAX requests
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Section save tracking object
const savedSections = {
    1: false,
    2: false,
    3: false
};

// Map-related variables
let map;
let marker;
const latInput = document.getElementById('latitude');
const lngInput = document.getElementById('longitude');
const locationUrlInput = document.getElementById('location_url');
let mapInitialized = false;

// Mahalla-related variables
let mahallas = [];
const mahallaSearch = document.getElementById('mahalla_search');
const mahallaDropdown = document.getElementById('mahalla_dropdown');
const mahallaIdInput = document.getElementById('mahalla_id');
const tumanSelect = document.getElementById('tuman_select');

// Modal elements
const addMahallaBtn = document.getElementById('add_mahalla_btn');
const mahallaModal = document.getElementById('mahalla_modal');
const newMahallaName = document.getElementById('new_mahalla_name');
const saveMahallaBtn = document.getElementById('save_mahalla');
const cancelMahallaBtn = document.getElementById('cancel_mahalla');
const mahallaError = document.getElementById('mahalla_error');


// ====================================
// PART 2: SECTION SAVE FUNCTIONALITY
// ====================================

/**
 * Save section data to localStorage
 * @param {number} sectionNumber - The section number (1, 2, or 3)
 */
function saveSection(sectionNumber) {
    const section = document.getElementById(`section-${sectionNumber}`);
    const statusEl = section.querySelector('.section-status');
    
    // Get all inputs in this section
    const inputs = section.querySelectorAll('input, select, textarea');
    let isValid = true;
    
    // Validate required fields in section 1
    if (sectionNumber === 1) {
        const requiredFields = ['lot_number', 'tuman_id', 'address', 'unique_number', 'land_area', 'initial_price'];
        requiredFields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (!field || !field.value.trim()) {
                isValid = false;
                if (field) {
                    field.classList.add('border-red-600');
                    field.classList.remove('border-gray-300');
                }
            } else {
                if (field) {
                    field.classList.remove('border-red-600');
                    field.classList.add('border-gray-300');
                }
            }
        });
        
        if (!isValid) {
            showMessage('Илтимос, барча мажбурий майдонларни тўлдиринг!', 'error');
            return;
        }
    }
    
    // Save data to localStorage
    const sectionData = {};
    inputs.forEach(input => {
        if (input.name) {
            sectionData[input.name] = input.value;
        }
    });
    localStorage.setItem(`lot_section_${sectionNumber}`, JSON.stringify(sectionData));
    
    // Mark as saved
    savedSections[sectionNumber] = true;
    statusEl.textContent = '✓ Сақланди';
    statusEl.classList.remove('opacity-75');
    statusEl.classList.add('font-bold');
    
    // Add animation
    section.classList.add('section-saved');
    setTimeout(() => {
        section.classList.remove('section-saved');
    }, 500);
    
    showMessage(`${sectionNumber}-бўлим маълумотлари сақланди`, 'success');
    
    // Scroll to next section if not last
    if (sectionNumber < 3) {
        setTimeout(() => {
            const nextSection = document.getElementById(`section-${sectionNumber + 1}`);
            nextSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }, 600);
    }
}


// ====================================
// PART 3: MESSAGE DISPLAY HELPER
// ====================================

/**
 * Show success or error message
 * @param {string} text - Message text to display
 * @param {string} type - Message type ('success' or 'error')
 */
function showMessage(text, type = 'success') {
    const messageEl = document.getElementById('success-message');
    const textEl = document.getElementById('success-text');
    
    textEl.textContent = text;
    
    if (type === 'error') {
        messageEl.className = 'mb-6 bg-red-50 border-l-4 border-red-600 p-4 rounded';
        textEl.className = 'text-sm font-medium text-red-800';
    } else {
        messageEl.className = 'mb-6 bg-green-50 border-l-4 border-green-600 p-4 rounded';
        textEl.className = 'text-sm font-medium text-green-800';
    }
    
    messageEl.classList.remove('hidden');
    
    // Auto-hide after 3 seconds
    setTimeout(() => {
        messageEl.classList.add('hidden');
    }, 3000);
    
    // Scroll to top to show message
    window.scrollTo({ top: 0, behavior: 'smooth' });
}


// ====================================
// PART 4: LOAD SAVED DATA ON PAGE LOAD
// ====================================

window.addEventListener('DOMContentLoaded', function() {
    // Load saved section data from localStorage
    for (let i = 1; i <= 3; i++) {
        const savedData = localStorage.getItem(`lot_section_${i}`);
        if (savedData) {
            try {
                const data = JSON.parse(savedData);
                Object.keys(data).forEach(name => {
                    const input = document.querySelector(`[name="${name}"]`);
                    if (input && !input.value) {
                        input.value = data[name];
                    }
                });
                
                // Mark as saved
                savedSections[i] = true;
                const section = document.getElementById(`section-${i}`);
                const statusEl = section.querySelector('.section-status');
                statusEl.textContent = '✓ Сақланди';
                statusEl.classList.remove('opacity-75');
                statusEl.classList.add('font-bold');
            } catch (e) {
                console.error('Error loading saved data:', e);
            }
        }
    }
});


// ====================================
// PART 5: FORM SUBMISSION HANDLING
// ====================================

document.getElementById('lotForm').addEventListener('submit', function(e) {
    // Validate that section 1 required fields are complete
    const requiredFields = ['lot_number', 'tuman_id', 'address', 'unique_number', 'land_area', 'initial_price'];
    let isValid = true;
    
    requiredFields.forEach(fieldName => {
        const field = document.getElementById(fieldName);
        if (!field || !field.value.trim()) {
            isValid = false;
            if (field) {
                field.classList.add('border-red-600');
            }
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        showMessage('Илтимос, камида асосий маълумотларни тўлдиринг!', 'error');
        document.getElementById('section-1').scrollIntoView({ behavior: 'smooth' });
        return;
    }
    
    // Clear localStorage after successful validation
    setTimeout(() => {
        for (let i = 1; i <= 3; i++) {
            localStorage.removeItem(`lot_section_${i}`);
        }
    }, 100);
});


// ====================================
// PART 6: LEAFLET MAP INITIALIZATION
// ====================================

// Observe when map container becomes visible
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

/**
 * Initialize Leaflet map
 */
function initMap() {
    const defaultLat = 41.2995;
    const defaultLng = 69.2401;

    const oldLat = parseFloat(latInput.value) || defaultLat;
    const oldLng = parseFloat(lngInput.value) || defaultLng;

    // Create map instance
    map = L.map('map').setView([oldLat, oldLng], 13);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Add marker if coordinates already exist
    if (latInput.value && lngInput.value) {
        marker = L.marker([oldLat, oldLng], {
            draggable: true
        }).addTo(map);

        marker.on('dragend', function(e) {
            const position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });
    }

    // Handle map click to add/move marker
    map.on('click', function(e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;

        if (marker) {
            marker.setLatLng([lat, lng]);
        } else {
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });
        }

        updateCoordinates(lat, lng);
    });
}


// ====================================
// PART 7: COORDINATE UPDATE FUNCTIONS
// ====================================

/**
 * Update coordinate inputs and generate Google Maps URL
 * @param {number} lat - Latitude
 * @param {number} lng - Longitude
 */
function updateCoordinates(lat, lng) {
    latInput.value = lat.toFixed(10);
    lngInput.value = lng.toFixed(10);

    // Generate Google Maps URL
    const googleMapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
    locationUrlInput.value = googleMapsUrl;

    // Visual feedback
    latInput.classList.add('border-green-500');
    lngInput.classList.add('border-green-500');
    setTimeout(() => {
        latInput.classList.remove('border-green-500');
        lngInput.classList.remove('border-green-500');
    }, 1000);
}

// Handle manual latitude input
latInput.addEventListener('change', function() {
    const lat = parseFloat(this.value);
    const lng = parseFloat(lngInput.value);

    if (!isNaN(lat) && !isNaN(lng)) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else if (map) {
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });
        }
        if (map) {
            map.setView([lat, lng], 13);
        }

        const googleMapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
        locationUrlInput.value = googleMapsUrl;
    }
});

// Handle manual longitude input
lngInput.addEventListener('change', function() {
    const lat = parseFloat(latInput.value);
    const lng = parseFloat(this.value);

    if (!isNaN(lat) && !isNaN(lng)) {
        if (marker) {
            marker.setLatLng([lat, lng]);
        } else if (map) {
            marker = L.marker([lat, lng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });
        }
        if (map) {
            map.setView([lat, lng], 13);
        }

        const googleMapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
        locationUrlInput.value = googleMapsUrl;
    }
});


// ====================================
// PART 8: TUMAN SELECT - LOAD MAHALLAS
// ====================================

tumanSelect.addEventListener('change', function() {
    const tumanId = this.value;
    mahallaSearch.value = '';
    mahallaIdInput.value = '';

    if (!tumanId) {
        mahallaDropdown.innerHTML = '<div class="p-2 text-sm text-gray-600 text-center">Туманни танланг</div>';
        mahallaDropdown.classList.add('hidden');
        mahallaSearch.disabled = true;
        mahallaSearch.placeholder = 'Туманни танланг...';
        return;
    }

    // Fetch mahallas for selected tuman
    fetch(`/mahallas/${tumanId}`)
        .then(response => response.json())
        .then(data => {
            mahallas = data;
            mahallaSearch.placeholder = 'Маҳалла номини ёзинг...';
            mahallaSearch.disabled = false;

            // Pre-select if old value exists
            const oldValue = '{{ old("mahalla_id") }}';
            if (oldValue && mahallas.length > 0) {
                const selected = mahallas.find(m => m.id == oldValue);
                if (selected) {
                    mahallaSearch.value = selected.name;
                    mahallaIdInput.value = selected.id;
                }
            }
        })
        .catch(error => {
            console.error('Error loading mahallas:', error);
            alert('Маҳаллалар юкланмади');
        });
});


// ====================================
// PART 9: MAHALLA SEARCH FUNCTIONALITY
// ====================================

mahallaSearch.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();

    if (!searchTerm) {
        mahallaDropdown.classList.add('hidden');
        mahallaIdInput.value = '';
        return;
    }

    // Check if mahallas array is populated
    if (!mahallas || mahallas.length === 0) {
        mahallaDropdown.innerHTML = '<div class="p-2 text-sm text-gray-600 text-center">Туманни танланг</div>';
        mahallaDropdown.classList.remove('hidden');
        return;
    }

    // Filter mahallas based on search term
    const filtered = mahallas.filter(m => {
        // Safety check: ensure m and m.name exist
        return m && m.name && m.name.toLowerCase().includes(searchTerm);
    });

    if (filtered.length === 0) {
        mahallaDropdown.innerHTML = '<div class="p-2 text-sm text-gray-600 text-center">Натижа топилмади</div>';
        mahallaDropdown.classList.remove('hidden');
        return;
    }

    // Display filtered results
    mahallaDropdown.innerHTML = filtered.map(m => `
        <div class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-200 mahalla-option" 
             data-id="${m.id}" 
             data-name="${m.name}">
            <div class="font-medium text-gray-900">${m.name}</div>
        </div>
    `).join('');

    mahallaDropdown.classList.remove('hidden');

    // Add click handlers to dropdown options
    document.querySelectorAll('.mahalla-option').forEach(option => {
        option.addEventListener('click', function() {
            mahallaSearch.value = this.dataset.name;
            mahallaIdInput.value = this.dataset.id;
            mahallaDropdown.classList.add('hidden');
        });
    });
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!mahallaSearch.contains(e.target) && !mahallaDropdown.contains(e.target)) {
        mahallaDropdown.classList.add('hidden');
    }
});


// ====================================
// PART 10: ADD NEW MAHALLA - MODAL HANDLERS
// ====================================

// Open modal
addMahallaBtn.addEventListener('click', function() {
    const tumanId = tumanSelect.value;

    if (!tumanId) {
        alert('Аввал туманни танланг!');
        tumanSelect.focus();
        return;
    }

    newMahallaName.value = '';
    mahallaError.classList.add('hidden');
    mahallaModal.classList.remove('hidden');
    newMahallaName.focus();
});

// Close modal
cancelMahallaBtn.addEventListener('click', function() {
    mahallaModal.classList.add('hidden');
});

// Save new mahalla
saveMahallaBtn.addEventListener('click', function() {
    const name = newMahallaName.value.trim();
    const tumanId = tumanSelect.value;

    if (!name) {
        mahallaError.textContent = 'Маҳалла номини киритинг';
        mahallaError.classList.remove('hidden');
        return;
    }

    // Check for duplicates
    if (mahallas.some(m => m.name && m.name.toLowerCase() === name.toLowerCase())) {
        mahallaError.textContent = 'Бу маҳалла аллақачон мавжуд';
        mahallaError.classList.remove('hidden');
        return;
    }

    // Disable button during save
    saveMahallaBtn.disabled = true;
    saveMahallaBtn.textContent = 'Сақланмоқда...';

    // Send AJAX request to save mahalla
    fetch('/mahallas', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                tuman_id: tumanId,
                name: name
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add to local array
                mahallas.push(data.mahalla);
                
                // Set as selected
                mahallaSearch.value = data.mahalla.name;
                mahallaIdInput.value = data.mahalla.id;
                
                // Close modal
                mahallaModal.classList.add('hidden');
                
                // Show success message
                showMessage('Маҳалла муваффақиятли қўшилди!', 'success');
            } else {
                mahallaError.textContent = data.message || 'Хатолик юз берди';
                mahallaError.classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mahallaError.textContent = 'Хатолик юз берди';
            mahallaError.classList.remove('hidden');
        })
        .finally(() => {
            saveMahallaBtn.disabled = false;
            saveMahallaBtn.textContent = 'Сақлаш';
        });
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        mahallaModal.classList.add('hidden');
    }
});


// ====================================
// PART 11: INITIALIZE - LOAD PRE-SELECTED TUMAN
// ====================================

// If tuman is pre-selected (from old input), load its mahallas
if (tumanSelect.value) {
    tumanSelect.dispatchEvent(new Event('change'));
}

</script>
@endpush