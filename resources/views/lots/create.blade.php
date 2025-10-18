@extends('layouts.app')

@section('title', 'Янги лот қўшиш')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container {
        cursor: crosshair;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-6">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Янги лот қўшиш</h1>
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('lots.index') }}" class="hover:text-blue-700 font-medium">Лотлар</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-900 font-semibold">Янги лот</span>
            </nav>
        </div>

        {{-- Progress Steps --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex-1 text-center step-item active" data-step="1">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">1</div>
                    <p class="text-sm font-bold text-blue-600">Асосий</p>
                </div>
                <div class="flex-1 border-t-4 border-gray-300 step-line" data-step="1"></div>
                <div class="flex-1 text-center step-item" data-step="2">
                    <div class="w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">2</div>
                    <p class="text-sm font-bold text-gray-500">Аукцион</p>
                </div>
                <div class="flex-1 border-t-4 border-gray-300 step-line" data-step="2"></div>
                <div class="flex-1 text-center step-item" data-step="3">
                    <div class="w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold">3</div>
                    <p class="text-sm font-bold text-gray-500">Қўшимча</p>
                </div>
            </div>
        </div>

        {{-- Error Messages --}}
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

        <form action="{{ route('lots.store') }}" method="POST" id="lotForm" class="space-y-6">
            @csrf

            {{-- STEP 1: Асосий маълумотлар --}}
            <div class="form-step active" data-step="1">
                <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600">
                        <h2 class="text-lg font-bold text-white">1. АСОСИЙ МАЪЛУМОТЛАР</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Лот рақами --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Лот рақами <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="lot_number" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('lot_number') }}"
                                placeholder="Мисол: 18477002">
                            @error('lot_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Туман --}}
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
                            @error('tuman_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Маҳалла with Add Button --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Маҳалла / МФЙ
                            </label>
                            <div class="flex gap-2">
                                <div class="flex-1 relative">
                                    <input type="text" id="mahalla_search"
                                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                        placeholder="Маҳалла номини ёзинг..." autocomplete="off">
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
                            @error('mahalla_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Тўлиқ манзил --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Тўлиқ манзил <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="address" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('address') }}"
                                placeholder="Мисол: Fidoyilar MFY">
                            @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Кадастр рақами --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">
                                    Уникал рақами <span class="text-red-600">*</span>
                                </label>
                                <input type="text" name="unique_number" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    value="{{ old('unique_number') }}"
                                    placeholder="KA1726290029/1-1">
                                @error('unique_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Ер майдони --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">
                                    Ер майдони (га) <span class="text-red-600">*</span>
                                </label>
                                <input type="number" step="0.01" name="land_area" required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    value="{{ old('land_area') }}"
                                    placeholder="0.01">
                                @error('land_area')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Зона --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Зона</label>
                                <select name="zone" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                    <option value="">-- Танланг --</option>
                                    <option value="1-зона" {{ old('zone') == '1-зона' ? 'selected' : '' }}>1-зона</option>
                                    <option value="2-зона" {{ old('zone') == '2-зона' ? 'selected' : '' }}>2-зона</option>
                                    <option value="3-зона" {{ old('zone') == '3-зона' ? 'selected' : '' }}>3-зона</option>
                                    <option value="4-зона" {{ old('zone') == '4-зона' ? 'selected' : '' }}>4-зона</option>
                                    <option value="5-зона" {{ old('zone') == '5-зона' ? 'selected' : '' }}>5-зона</option>
                                </select>
                            </div>

                            {{-- Бош режа --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Бош режа</label>
                                <select name="master_plan_zone" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                    <option value="">-- Танланг --</option>
                                    <option value="Konservatsiya" {{ old('master_plan_zone') == 'Konservatsiya' ? 'selected' : '' }}>Konservatsiya</option>
                                    <option value="Rekonstruksiya" {{ old('master_plan_zone') == 'Rekonstruksiya' ? 'selected' : '' }}>Rekonstruksiya</option>
                                    <option value="Renovatsiya" {{ old('master_plan_zone') == 'Renovatsiya' ? 'selected' : '' }}>Renovatsiya</option>
                                </select>
                            </div>

                            {{-- Янги Ўзбекистон --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Янги Ўзбекистон</label>
                                <select name="yangi_uzbekiston" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                    <option value="0" {{ old('yangi_uzbekiston', '0') == '0' ? 'selected' : '' }}>Йўқ</option>
                                    <option value="1" {{ old('yangi_uzbekiston') == '1' ? 'selected' : '' }}>Ҳа</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 2: Аукцион маълумотлари --}}
            <div class="form-step" data-step="2" style="display: none;">
                <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600">
                        <h2 class="text-lg font-bold text-white">2. АУКЦИОН МАЪЛУМОТЛАРИ</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                            {{-- Аукцион санаси --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион санаси</label>
                                <input type="date" name="auction_date"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    value="{{ old('auction_date') }}">
                            </div>

                            {{-- Сотилган нарх --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Сотилган нарх (сўм)</label>
                                <input type="number" step="0.01" name="sold_price"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    value="{{ old('sold_price') }}"
                                    placeholder="267924294.00">
                            </div>

                            {{-- Тўлов тури --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Тўлов тури</label>
                                <select name="payment_type" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                    <option value="">-- Танланг --</option>
                                    <option value="muddatli" {{ old('payment_type') == 'muddatli' ? 'selected' : '' }}>Муддатли</option>
                                    <option value="muddatli_emas" {{ old('payment_type') == 'muddatli_emas' ? 'selected' : '' }}>Муддатли эмас</option>
                                </select>
                            </div>
                        </div>

                        {{-- Ғолиб маълумотлари --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Ғолиб номи</label>
                            <input type="text" name="winner_name"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('winner_name') }}"
                                placeholder="GAZ NEFT-AVTO BENZIN MChJ">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Ғолиб тури --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Ғолиб тури</label>
                                <select name="winner_type" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                    <option value="">-- Танланг --</option>
                                    <option value="G`olib" {{ old('winner_type') == 'G`olib' ? 'selected' : '' }}>G`olib</option>
                                    <option value="Yuridik shaxs" {{ old('winner_type') == 'Yuridik shaxs' ? 'selected' : '' }}>Yuridik shaxs</option>
                                    <option value="Jismoniy shaxs" {{ old('winner_type') == 'Jismoniy shaxs' ? 'selected' : '' }}>Jismoniy shaxs</option>
                                </select>
                            </div>

                            {{-- Телефон --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Телефон</label>
                                <input type="text" name="winner_phone"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    value="{{ old('winner_phone') }}"
                                    placeholder="(098) 300-5885">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Асос --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Асос (ПФ)</label>
                                <input type="text" name="basis"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    value="{{ old('basis') }}"
                                    placeholder="ПФ-93">
                            </div>

                            {{-- Аукцион тури --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион тури</label>
                                <select name="auction_type" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                    <option value="">-- Танланг --</option>
                                    <option value="ochiq" {{ old('auction_type') == 'ochiq' ? 'selected' : '' }}>Очиқ</option>
                                    <option value="yopiq" {{ old('auction_type') == 'yopiq' ? 'selected' : '' }}>Ёпиқ</option>
                                </select>
                            </div>

                            {{-- Лот холати --}}
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Лот холати</label>
                                <input type="text" name="lot_status"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    value="{{ old('lot_status', 'active') }}"
                                    placeholder="active">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STEP 3: Қўшимча маълумотлар --}}
            <div class="form-step" data-step="3" style="display: none;">
                <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-blue-600">
                        <h2 class="text-lg font-bold text-white">3. ҚЎШИМЧА МАЪЛУМОТЛАР</h2>
                    </div>
                    <div class="p-6 space-y-6">
                        {{-- Объект тури --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Объект тури</label>
                            <input type="text" name="object_type"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('object_type') }}"
                                placeholder="Yoqilg'i quyish shoxobchasi">
                        </div>

                        {{-- Координаталар --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Latitude</label>
                                <input type="text" name="latitude" id="latitude"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    value="{{ old('latitude') }}"
                                    placeholder="41.3419730499832">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-2">Longitude</label>
                                <input type="text" name="longitude" id="longitude"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    value="{{ old('longitude') }}"
                                    placeholder="69.16886331525568">
                            </div>
                        </div>

                        {{-- Interactive Map --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Харитада жойлашувни белгиланг
                                <span class="text-sm font-normal text-gray-600">(харитага босинг)</span>
                            </label>
                            <div id="map" class="w-full h-96 border-2 border-gray-300 rounded-lg"></div>
                            <p class="mt-2 text-sm text-gray-600">💡 Харитага босиб, ер участкасини белгиланг. Координаталар автоматик тўлдирилади.</p>
                        </div>

                        {{-- Google Maps URL --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Google Maps URL</label>
                            <input type="url" name="location_url" id="location_url"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('location_url') }}"
                                placeholder="https://www.google.com/maps?q=...">
                            <p class="mt-1 text-sm text-gray-600">Google Maps ҳавола автоматик ясалди</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Navigation Buttons --}}
            <div class="flex items-center justify-between pt-6 border-t-2 border-gray-300">
                <button type="button" id="prevBtn" style="display: none;"
                    class="px-8 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold transition border-2 border-gray-700">
                    ← Олдинги
                </button>
                <a href="{{ route('lots.index') }}"
                    class="px-8 py-3 bg-white hover:bg-gray-100 text-gray-900 border-2 border-gray-400 rounded-lg font-bold transition">
                    Бекор қилиш
                </a>
                <div class="flex gap-3">
                    <button type="button" id="nextBtn"
                        class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition border-2 border-blue-700">
                        Кейинги →
                    </button>
                    <button type="submit" id="submitBtn" style="display: none;"
                        class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition border-2 border-green-700">
                        ✓ Сақлаш
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Add Mahalla Modal --}}
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

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    // Get CSRF token from meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Map variables
    let map;
    let marker;
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const locationUrlInput = document.getElementById('location_url');

    // Initialize map
    function initMap() {
        // Default to Tashkent center
        const defaultLat = 41.2995;
        const defaultLng = 69.2401;

        // Use old values if exist
        const oldLat = parseFloat(latInput.value) || defaultLat;
        const oldLng = parseFloat(lngInput.value) || defaultLng;

        // Create map
        map = L.map('map').setView([oldLat, oldLng], 13);

        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Add marker if coordinates exist
        if (latInput.value && lngInput.value) {
            marker = L.marker([oldLat, oldLng], {
                draggable: true
            }).addTo(map);

            marker.on('dragend', function(e) {
                const position = marker.getLatLng();
                updateCoordinates(position.lat, position.lng);
            });
        }

        // Click on map to add/move marker
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

    // Update coordinates in inputs
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

    // Manual coordinate input
    latInput.addEventListener('change', function() {
        const lat = parseFloat(this.value);
        const lng = parseFloat(lngInput.value);

        if (!isNaN(lat) && !isNaN(lng)) {
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
            map.setView([lat, lng], 13);

            // Update Google Maps URL
            const googleMapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
            locationUrlInput.value = googleMapsUrl;
        }
    });

    lngInput.addEventListener('change', function() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(this.value);

        if (!isNaN(lat) && !isNaN(lng)) {
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
            map.setView([lat, lng], 13);

            // Update Google Maps URL
            const googleMapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
            locationUrlInput.value = googleMapsUrl;
        }
    });

    // Multi-step form
    let currentStep = 1;
    const totalSteps = 3;

    function showStep(step) {
        document.querySelectorAll('.form-step').forEach(el => el.style.display = 'none');
        document.querySelector(`.form-step[data-step="${step}"]`).style.display = 'block';

        // Initialize map when showing step 3
        if (step === 3 && !map) {
            setTimeout(() => {
                initMap();
            }, 100);
        }

        // Update progress
        document.querySelectorAll('.step-item').forEach((el, index) => {
            const stepNum = index + 1;
            const circle = el.querySelector('div');
            const text = el.querySelector('p');

            if (stepNum < step) {
                circle.className = 'w-10 h-10 bg-green-600 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold';
                text.className = 'text-sm font-bold text-green-600';
                el.classList.remove('active');
            } else if (stepNum === step) {
                circle.className = 'w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold';
                text.className = 'text-sm font-bold text-blue-600';
                el.classList.add('active');
            } else {
                circle.className = 'w-10 h-10 bg-gray-300 text-white rounded-full flex items-center justify-center mx-auto mb-2 font-bold';
                text.className = 'text-sm font-bold text-gray-500';
                el.classList.remove('active');
            }
        });

        // Update lines
        document.querySelectorAll('.step-line').forEach((el, index) => {
            if (index + 1 < step) {
                el.className = 'flex-1 border-t-4 border-green-600 step-line';
            } else {
                el.className = 'flex-1 border-t-4 border-gray-300 step-line';
            }
        });

        // Update buttons
        document.getElementById('prevBtn').style.display = step === 1 ? 'none' : 'block';
        document.getElementById('nextBtn').style.display = step === totalSteps ? 'none' : 'block';
        document.getElementById('submitBtn').style.display = step === totalSteps ? 'block' : 'none';

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    document.getElementById('nextBtn').addEventListener('click', function() {
        if (validateStep(currentStep)) {
            currentStep++;
            showStep(currentStep);
        }
    });

    document.getElementById('prevBtn').addEventListener('click', function() {
        currentStep--;
        showStep(currentStep);
    });

    function validateStep(step) {
        const currentStepEl = document.querySelector(`.form-step[data-step="${step}"]`);
        const requiredFields = currentStepEl.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-red-600');
                field.classList.remove('border-gray-300');
            } else {
                field.classList.remove('border-red-600');
                field.classList.add('border-gray-300');
            }
        });

        if (!isValid) {
            alert('Илтимос, барча мажбурий майдонларни тўлдиринг!');
        }

        return isValid;
    }

    // Mahalla search and management
    let mahallas = [];
    const mahallaSearch = document.getElementById('mahalla_search');
    const mahallaDropdown = document.getElementById('mahalla_dropdown');
    const mahallaIdInput = document.getElementById('mahalla_id');
    const tumanSelect = document.getElementById('tuman_select');

    tumanSelect.addEventListener('change', function() {
        const tumanId = this.value;
        mahallaSearch.value = '';
        mahallaIdInput.value = '';

        if (!tumanId) {
            mahallaDropdown.innerHTML = '<div class="p-2 text-sm text-gray-600 text-center">Туманни танланг</div>';
            mahallaDropdown.classList.add('hidden');
            return;
        }

        // Load mahallas
        fetch(`/mahallas/${tumanId}`)
            .then(response => response.json())
            .then(data => {
                mahallas = data;
                mahallaSearch.placeholder = 'Маҳалла номини ёзинг...';
                mahallaSearch.disabled = false;

                // If old value exists
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

    // Search mahalla
    mahallaSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();

        if (!searchTerm) {
            mahallaDropdown.classList.add('hidden');
            mahallaIdInput.value = '';
            return;
        }

        const filtered = mahallas.filter(m =>
            m.name.toLowerCase().includes(searchTerm)
        );

        if (filtered.length === 0) {
            mahallaDropdown.innerHTML = '<div class="p-2 text-sm text-gray-600 text-center">Натижа топилмади</div>';
            mahallaDropdown.classList.remove('hidden');
            return;
        }

        mahallaDropdown.innerHTML = filtered.map(m => `
        <div class="p-3 hover:bg-blue-50 cursor-pointer border-b border-gray-200 mahalla-option" data-id="${m.id}" data-name="${m.name}">
            <div class="font-medium text-gray-900">${m.name}</div>
        </div>
    `).join('');

        mahallaDropdown.classList.remove('hidden');

        // Add click handlers
        document.querySelectorAll('.mahalla-option').forEach(option => {
            option.addEventListener('click', function() {
                mahallaSearch.value = this.dataset.name;
                mahallaIdInput.value = this.dataset.id;
                mahallaDropdown.classList.add('hidden');
            });
        });
    });

    // Click outside to close
    document.addEventListener('click', function(e) {
        if (!mahallaSearch.contains(e.target) && !mahallaDropdown.contains(e.target)) {
            mahallaDropdown.classList.add('hidden');
        }
    });

    // Add new mahalla
    const addMahallaBtn = document.getElementById('add_mahalla_btn');
    const mahallaModal = document.getElementById('mahalla_modal');
    const newMahallaName = document.getElementById('new_mahalla_name');
    const saveMahallaBtn = document.getElementById('save_mahalla');
    const cancelMahallaBtn = document.getElementById('cancel_mahalla');
    const mahallaError = document.getElementById('mahalla_error');

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

    cancelMahallaBtn.addEventListener('click', function() {
        mahallaModal.classList.add('hidden');
    });

    saveMahallaBtn.addEventListener('click', function() {
        const name = newMahallaName.value.trim();
        const tumanId = tumanSelect.value;

        if (!name) {
            mahallaError.textContent = 'Маҳалла номини киритинг';
            mahallaError.classList.remove('hidden');
            return;
        }

        // Check duplicate
        if (mahallas.some(m => m.name.toLowerCase() === name.toLowerCase())) {
            mahallaError.textContent = 'Бу маҳалла аллақачон мавжуд';
            mahallaError.classList.remove('hidden');
            return;
        }

        // Save via AJAX
        saveMahallaBtn.disabled = true;
        saveMahallaBtn.textContent = 'Сақланмоқда...';

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
                    // Add to list
                    mahallas.push(data.mahalla);

                    // Set as selected
                    mahallaSearch.value = data.mahalla.name;
                    mahallaIdInput.value = data.mahalla.id;

                    // Close modal
                    mahallaModal.classList.add('hidden');

                    // Show success
                    alert('Маҳалла муваффақиятли қўшилди!');
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

    // Close modal on ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            mahallaModal.classList.add('hidden');
        }
    });

    // Form validation on submit
    document.getElementById('lotForm').addEventListener('submit', function(e) {
        if (!validateStep(currentStep)) {
            e.preventDefault();
        }
    });

    // Initialize
    showStep(1);

    // Load mahallas if tuman is pre-selected (old value)
    if (tumanSelect.value) {
        tumanSelect.dispatchEvent(new Event('change'));
    }
</script>

<style>
    .form-step {
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .step-item {
        transition: all 0.3s ease;
    }

    .step-line {
        transition: all 0.3s ease;
    }

    input:focus,
    select:focus,
    textarea:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .mahalla-option:last-child {
        border-bottom: none;
    }
</style>
@endsection
