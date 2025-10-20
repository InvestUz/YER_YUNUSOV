@extends('layouts.app')

@section('title', 'Лотни таҳрирлаш')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .leaflet-container { cursor: crosshair; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-5xl mx-auto px-6">

        {{-- PAGE HEADER --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Лотни таҳрирлаш</h1>
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('lots.index') }}" class="hover:text-blue-700 font-medium">Лотлар</a>
                <span class="text-gray-400">/</span>
                <a href="{{ route('lots.show', $lot) }}" class="hover:text-blue-700 font-medium">{{ $lot->lot_number }}</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-900 font-semibold">Таҳрирлаш</span>
            </nav>
        </div>

        {{-- ERROR MESSAGES --}}
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

        {{-- MAIN FORM --}}
        <form action="{{ route('lots.update', $lot) }}" method="POST" id="lotForm">
            @csrf
            @method('PUT')

            {{-- SECTION 1: АСОСИЙ МАЪЛУМОТЛАР --}}
            <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-blue-600">
                    <h2 class="text-lg font-bold text-white">1. АСОСИЙ МАЪЛУМОТЛАР</h2>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Лот рақами <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="lot_number" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('lot_number', $lot->lot_number) }}" placeholder="Мисол: 18477002">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Туман <span class="text-red-600">*</span>
                        </label>
                        <select name="tuman_id" id="tuman_select" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                            <option value="">-- Туманни танланг --</option>
                            @foreach($tumans as $tuman)
                            <option value="{{ $tuman->id }}" {{ old('tuman_id', $lot->tuman_id) == $tuman->id ? 'selected' : '' }}>
                                {{ $tuman->name_uz }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Маҳалла / МФЙ</label>
                        <div class="flex gap-2">
                            <div class="flex-1 relative">
                                <input type="text" id="mahalla_search"
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                    placeholder="Маҳалла номини ёзинг..." autocomplete="off"
                                    value="{{ old('mahalla_search', $lot->mahalla?->name_uz) }}">
                                <input type="hidden" name="mahalla_id" id="mahalla_id" value="{{ old('mahalla_id', $lot->mahalla_id) }}">
                                <div id="mahalla_dropdown" class="hidden absolute z-50 w-full mt-1 bg-white border-2 border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto"></div>
                            </div>
                            <button type="button" id="add_mahalla_btn"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold transition border-2 border-green-700 whitespace-nowrap">
                                + Янги
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Тўлиқ манзил</label>
                        <input type="text" name="address"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('address', $lot->address) }}" placeholder="Мисол: Fidoyilar MFY">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Уникал рақами <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="unique_number" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('unique_number', $lot->unique_number) }}" placeholder="KA1726290029/1-1">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Ер майдони (га) <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="land_area" required
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('land_area', $lot->land_area) }}" placeholder="0.01">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Зона</label>
                            <select name="zone"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="1-зона" {{ old('zone', $lot->zone) == '1-зона' ? 'selected' : '' }}>1-зона</option>
                                <option value="2-зона" {{ old('zone', $lot->zone) == '2-зона' ? 'selected' : '' }}>2-зона</option>
                                <option value="3-зона" {{ old('zone', $lot->zone) == '3-зона' ? 'selected' : '' }}>3-зона</option>
                                <option value="4-зона" {{ old('zone', $lot->zone) == '4-зона' ? 'selected' : '' }}>4-зона</option>
                                <option value="5-зона" {{ old('zone', $lot->zone) == '5-зона' ? 'selected' : '' }}>5-зона</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Бош режа</label>
                            <select name="master_plan_zone"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="Konservatsiya" {{ old('master_plan_zone', $lot->master_plan_zone) == 'Konservatsiya' ? 'selected' : '' }}>Konservatsiya</option>
                                <option value="Rekonstruksiya" {{ old('master_plan_zone', $lot->master_plan_zone) == 'Rekonstruksiya' ? 'selected' : '' }}>Rekonstruksiya</option>
                                <option value="Renovatsiya" {{ old('master_plan_zone', $lot->master_plan_zone) == 'Renovatsiya' ? 'selected' : '' }}>Renovatsiya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Янги Ўзбекистон</label>
                            <select name="yangi_uzbekiston"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                                <option value="0" {{ old('yangi_uzbekiston', $lot->yangi_uzbekiston) == '0' ? 'selected' : '' }}>Йўқ</option>
                                <option value="1" {{ old('yangi_uzbekiston', $lot->yangi_uzbekiston) == '1' ? 'selected' : '' }}>Ҳа</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: АУКЦИОН МАЪЛУМОТЛАРИ --}}
            <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-green-600">
                    <h2 class="text-lg font-bold text-white">2. АУКЦИОН МАЪЛУМОТЛАРИ</h2>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион санаси</label>
                            <input type="date" name="auction_date"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('auction_date', $lot->auction_date?->format('Y-m-d')) }}">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Сотилган нарх (сўм)</label>
                            <input type="text" name="sold_price"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('sold_price', $lot->sold_price) }}" placeholder="267924294.00">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Ғолиб номи</label>
                        <input type="text" name="winner_name"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                            value="{{ old('winner_name', $lot->winner_name) }}" placeholder="GAZ NEFT-AVTO BENZIN MChJ">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион ғолиби</label>
                            <select name="winner_type"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="G`olib" {{ old('winner_type', $lot->winner_type) == 'G`olib' ? 'selected' : '' }}>G`olib</option>
                                <option value="Salohiyatli g`olib" {{ old('winner_type', $lot->winner_type) == 'Salohiyatli g`olib' ? 'selected' : '' }}>Salohiyatli g`olib</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Ҳуқуқий субъект</label>
                            <select name="huquqiy_subyekt"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="Jismoniy shaxs" {{ old('huquqiy_subyekt', $lot->huquqiy_subyekt) == 'Jismoniy shaxs' ? 'selected' : '' }}>Jismoniy shaxs</option>
                                <option value="Yuridik shaxs" {{ old('huquqiy_subyekt', $lot->huquqiy_subyekt) == 'Yuridik shaxs' ? 'selected' : '' }}>Yuridik shaxs</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Телефон</label>
                            <input type="text" name="winner_phone"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('winner_phone', $lot->winner_phone) }}" placeholder="(098) 300-5885">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Асос (ПФ)</label>
                            <input type="text" name="basis"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition"
                                value="{{ old('basis', $lot->basis) }}" placeholder="ПФ-93">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион тури</label>
                            <select name="auction_type"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-medium transition">
                                <option value="">-- Танланг --</option>
                                <option value="ochiq" {{ old('auction_type', $lot->auction_type) == 'ochiq' ? 'selected' : '' }}>Очиқ</option>
                                <option value="yopiq" {{ old('auction_type', $lot->auction_type) == 'yopiq' ? 'selected' : '' }}>Ёпиқ</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: ҚЎШИМЧА МАЪЛУМОТЛАР --}}
            <div class="bg-white border-2 border-gray-300 shadow-lg rounded-lg overflow-hidden mb-6">
                <div class="px-6 py-4 bg-purple-600">
                    <h2 class="text-lg font-bold text-white">3. ҚЎШИМЧА МАЪЛУМОТЛАР</h2>
                </div>

                <div class="p-6 space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Объект тури</label>
                        <input type="text" name="object_type"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                            value="{{ old('object_type', $lot->object_type) }}" placeholder="Yoqilg'i quyish shoxobchasi">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Latitude</label>
                            <input type="text" name="latitude" id="latitude"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                                value="{{ old('latitude', $lot->latitude) }}" placeholder="41.3419730499832">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Longitude</label>
                            <input type="text" name="longitude" id="longitude"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                                value="{{ old('longitude', $lot->longitude) }}" placeholder="69.16886331525568">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">
                            Харитада жойлашувни белгиланг
                            <span class="text-sm font-normal text-gray-600">(харитага босинг)</span>
                        </label>
                        <div id="map" class="w-full h-96 border-2 border-gray-300 rounded-lg"></div>
                        <p class="mt-2 text-sm text-gray-600">💡 Харитага босиб, ер участкасини белгиланг. Координаталар автоматик тўлдирилади.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-2">Google Maps URL</label>
                        <input type="url" name="location_url" id="location_url"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-purple-600 font-medium transition"
                            value="{{ old('location_url', $lot->location_url) }}" placeholder="https://www.google.com/maps?q=...">
                        <p class="mt-1 text-sm text-gray-600">Google Maps ҳавола автоматик ясалди</p>
                    </div>
                </div>
            </div>

            {{-- FINAL SUBMIT --}}
            <div class="bg-gradient-to-r from-blue-50 to-green-50 border-2 border-blue-300 shadow-lg rounded-lg overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">Ўзгаришларни сақлаш</h3>
                            <p class="text-sm text-gray-600">Барча маълумотларни текширинг ва сақланг</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('lots.show', $lot) }}"
                                class="px-8 py-3 bg-white hover:bg-gray-100 text-gray-900 border-2 border-gray-400 rounded-lg font-bold transition">
                                Бекор қилиш
                            </a>
                            <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white rounded-lg font-bold transition border-2 border-blue-700 shadow-lg">
                                ✓ Сақлаш
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- MAHALLA MODAL --}}
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

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Map variables
    let map, marker;
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const locationUrlInput = document.getElementById('location_url');
    let mapInitialized = false;

    // Mahalla variables
    let mahallas = @json($mahallas);
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

    // MAP INITIALIZATION
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

    // TUMAN CHANGE - LOAD MAHALLAS FROM DATABASE
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

        mahallaSearch.disabled = true;
        mahallaSearch.placeholder = 'Юкланмоқда...';

        fetch(`/mahallas/${tumanId}`)
            .then(response => response.json())
            .then(data => {
                mahallas = data.map(m => ({ id: m.id, name: m.name_uz }));
                mahallaSearch.disabled = false;
                mahallaSearch.placeholder = 'Маҳалла номини ёзинг...';
            })
            .catch(error => {
                console.error('Error loading mahallas:', error);
                mahallaSearch.disabled = false;
                mahallaSearch.placeholder = 'Хатолик юз берди';
            });
    });

    // MAHALLA SEARCH
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

    // ADD NEW MAHALLA
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
                mahallas.push({ id: data.mahalla.id, name: data.mahalla.name });
                mahallaSearch.value = data.mahalla.name;
                mahallaIdInput.value = data.mahalla.id;
                mahallaModal.classList.add('hidden');
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

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            mahallaModal.classList.add('hidden');
        }
    });

}); // End DOMContentLoaded
</script>
@endpush
