@extends('layouts.app')

@section('title', 'Янги лот қўшиш')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Page Header --}}
        <div class="mb-8 pb-4 border-b-2 border-gray-300">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Янги лот қўшиш</h1>
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('lots.index') }}" class="hover:text-blue-700 font-medium">Лотлар</a>
                <span class="text-gray-400">/</span>
                <span class="text-gray-900 font-semibold">Янги лот</span>
            </nav>
        </div>

        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-red-800">Хатоликлар мавжуд:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('lots.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- 1. Асосий маълумотлар --}}
            <div class="bg-white border-2 border-gray-300 shadow">
                <div class="px-6 py-4 bg-gray-800 border-b-2 border-gray-900">
                    <h2 class="text-lg font-bold text-white">1. АСОСИЙ МАЪЛУМОТЛАР</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Лот рақами --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Лот рақами <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="lot_number" required
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('lot_number') }}"
                                   placeholder="Мисол: 7815834">
                            @error('lot_number')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Туман --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Туман <span class="text-red-600">*</span>
                            </label>
                            <select name="tuman_id" id="tuman_select" required
                                    onchange="loadMahallas(this.value)"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium">
                                <option value="">-- Туманни танланг --</option>
                                @foreach($tumans as $tuman)
                                    <option value="{{ $tuman->id }}" {{ old('tuman_id') == $tuman->id ? 'selected' : '' }}>
                                        {{ $tuman->name_uz }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tuman_id')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Маҳалла --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Маҳалла / Кўча
                            </label>
                            <select name="mahalla_id" id="mahalla_select"
                                    class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium">
                                <option value="">-- Аввал туманни танланг --</option>
                            </select>
                            @error('mahalla_id')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Манзил --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Тўлiq манзил <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="address" required
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('address') }}"
                                   placeholder="Мисол: Yangi Beltepa MFY">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Уникал рақам --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Кадастр рақами <span class="text-red-600">*</span>
                            </label>
                            <input type="text" name="unique_number" required
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('unique_number') }}"
                                   placeholder="MG1726277007/2">
                            @error('unique_number')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Зона --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Зона</label>
                            <select name="zone" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium">
                                <option value="">-- Танланг --</option>
                                <option value="1-зона" {{ old('zone') == '1-зона' ? 'selected' : '' }}>1-зона</option>
                                <option value="2-зона" {{ old('zone') == '2-зона' ? 'selected' : '' }}>2-зона</option>
                                <option value="3-зона" {{ old('zone') == '3-зона' ? 'selected' : '' }}>3-зона</option>
                                <option value="4-зона" {{ old('zone') == '4-зона' ? 'selected' : '' }}>4-зона</option>
                            </select>
                        </div>

                        {{-- Бош режа зонаси --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Бош режа зонаси</label>
                            <select name="master_plan_zone" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium">
                                <option value="">-- Танланг --</option>
                                <option value="Konservatsiya" {{ old('master_plan_zone') == 'Konservatsiya' ? 'selected' : '' }}>Konservatsiya</option>
                                <option value="Rekonstruksiya" {{ old('master_plan_zone') == 'Rekonstruksiya' ? 'selected' : '' }}>Rekonstruksiya</option>
                                <option value="Renovatsiya" {{ old('master_plan_zone') == 'Renovatsiya' ? 'selected' : '' }}>Renovatsiya</option>
                            </select>
                        </div>

                        {{-- Янги Ўзбекистон --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Янги Ўзбекистон</label>
                            <select name="yangi_uzbekiston" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium">
                                <option value="0" {{ old('yangi_uzbekiston', '0') == '0' ? 'selected' : '' }}>Йўқ</option>
                                <option value="1" {{ old('yangi_uzbekiston') == '1' ? 'selected' : '' }}>Ҳа</option>
                            </select>
                        </div>

                        {{-- Ер майдони --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Ер майдони (га) <span class="text-red-600">*</span>
                            </label>
                            <input type="number" step="0.01" name="land_area" required
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('land_area') }}"
                                   placeholder="0.45">
                            @error('land_area')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Объект тури --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-900 mb-2">Объект тури</label>
                            <input type="text" name="object_type"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('object_type') }}"
                                   placeholder="Maktabgacha ta'lim muassasalari">
                        </div>

                        {{-- Қурилиш майдони --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Қурилиш майдони (м²)</label>
                            <input type="number" step="0.01" name="construction_area"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('construction_area') }}"
                                   placeholder="5850.00">
                        </div>

                        {{-- Инвестиция --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Инвестиция ($)</label>
                            <input type="number" step="0.01" name="investment_amount"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('investment_amount') }}"
                                   placeholder="2340000.00">
                        </div>

                        {{-- Координаталар --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Latitude</label>
                            <input type="text" name="latitude"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('latitude') }}"
                                   placeholder="41.3419730499832">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Longitude</label>
                            <input type="text" name="longitude"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('longitude') }}"
                                   placeholder="69.16886331525568">
                        </div>

                        {{-- Локация URL --}}
                        <div class="md:col-span-3">
                            <label class="block text-sm font-bold text-gray-900 mb-2">Google Maps URL</label>
                            <input type="url" name="location_url"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('location_url') }}"
                                   placeholder="https://www.google.com/maps?q=...">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Аукцион маълумотлари --}}
            <div class="bg-white border-2 border-gray-300 shadow">
                <div class="px-6 py-4 bg-gray-800 border-b-2 border-gray-900">
                    <h2 class="text-lg font-bold text-white">2. АУКЦИОН МАЪЛУМОТЛАРИ</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Бошланғич нарх --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">
                                Бошланғич нарх (сўм) <span class="text-red-600">*</span>
                            </label>
                            <input type="number" step="0.01" name="initial_price" required
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('initial_price') }}"
                                   placeholder="3350266925.0">
                            @error('initial_price')
                                <p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Аукцион санаси --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион санаси</label>
                            <input type="date" name="auction_date"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('auction_date') }}">
                        </div>

                        {{-- Сотилган нарх --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Сотилган нарх (сўм)</label>
                            <input type="number" step="0.01" name="sold_price"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('sold_price') }}"
                                   placeholder="6197993811.3">
                        </div>

                        {{-- Ғолиб тури --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Ғолиб тури</label>
                            <select name="winner_type" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium">
                                <option value="">-- Танланг --</option>
                                <option value="G`olib" {{ old('winner_type') == 'G`olib' ? 'selected' : '' }}>G`olib</option>
                                <option value="Yuridik shaxs" {{ old('winner_type') == 'Yuridik shaxs' ? 'selected' : '' }}>Yuridik shaxs</option>
                                <option value="Jismoniy shaxs" {{ old('winner_type') == 'Jismoniy shaxs' ? 'selected' : '' }}>Jismoniy shaxs</option>
                            </select>
                        </div>

                        {{-- Ғолиб номи --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-bold text-gray-900 mb-2">Ғолиб номи</label>
                            <input type="text" name="winner_name"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('winner_name') }}"
                                   placeholder="NTM WISDOM">
                        </div>

                        {{-- Телефон --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Телефон рақами</label>
                            <input type="text" name="winner_phone"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('winner_phone') }}"
                                   placeholder="+998 90 123 45 67">
                        </div>

                        {{-- Тўлов тури --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Тўлов тури</label>
                            <select name="payment_type" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium">
                                <option value="">-- Танланг --</option>
                                <option value="muddatli" {{ old('payment_type') == 'muddatli' ? 'selected' : '' }}>Муддатли</option>
                                <option value="muddatli_emas" {{ old('payment_type') == 'muddatli_emas' ? 'selected' : '' }}>Муддатли эмас</option>
                            </select>
                        </div>

                        {{-- Асос --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Асос (ПФ)</label>
                            <input type="text" name="basis"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('basis') }}"
                                   placeholder="ПФ-93">
                        </div>

                        {{-- Аукцион тури --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Аукцион тури</label>
                            <select name="auction_type" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium">
                                <option value="">-- Танланг --</option>
                                <option value="ochiq" {{ old('auction_type') == 'ochiq' ? 'selected' : '' }}>Очиқ аукцион</option>
                                <option value="yopiq" {{ old('auction_type') == 'yopiq' ? 'selected' : '' }}>Ёпиқ аукцион</option>
                            </select>
                        </div>

                        {{-- Лот холати --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Лот холати</label>
                            <input type="text" name="lot_status"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('lot_status', 'Лот якунланди (29)') }}"
                                   placeholder="Лот якунланди (29)">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Шартнома маълумотлари --}}
            <div class="bg-white border-2 border-gray-300 shadow">
                <div class="px-6 py-4 bg-gray-800 border-b-2 border-gray-900">
                    <h2 class="text-lg font-bold text-white">3. ШАРТНОМА МАЪЛУМОТЛАРИ</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {{-- Шартнома тузилди --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Шартнома тузилдими?</label>
                            <select name="contract_signed" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium">
                                <option value="0" {{ old('contract_signed', '0') == '0' ? 'selected' : '' }}>Йўқ</option>
                                <option value="1" {{ old('contract_signed') == '1' ? 'selected' : '' }}>Ҳа</option>
                            </select>
                        </div>

                        {{-- Шартнома санаси --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Шартнома санаси</label>
                            <input type="date" name="contract_date"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('contract_date') }}">
                        </div>

                        {{-- Шартнома рақами --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Шартнома рақами</label>
                            <input type="text" name="contract_number"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('contract_number') }}"
                                   placeholder="2e">
                        </div>

                        {{-- Тўланган сумма --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Тўланган сумма</label>
                            <input type="number" step="0.01" name="paid_amount"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('paid_amount') }}"
                                   placeholder="134010677.00">
                        </div>

                        {{-- Ўтказилган сумма --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Ўтказилган сумма</label>
                            <input type="number" step="0.01" name="transferred_amount"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('transferred_amount') }}"
                                   placeholder="72030738.89">
                        </div>

                        {{-- Чегирма --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-900 mb-2">Чегирма</label>
                            <input type="number" step="0.01" name="discount"
                                   class="w-full px-4 py-2.5 border-2 border-gray-300 rounded focus:outline-none focus:border-blue-600 font-medium"
                                   value="{{ old('discount', '0') }}"
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-between pt-6 border-t-2 border-gray-300">
                <a href="{{ route('lots.index') }}"
                   class="px-8 py-3 bg-white hover:bg-gray-100 text-gray-900 border-2 border-gray-400 rounded font-bold transition">
                    ◄ БЕКОР ҚИЛИШ
                </a>
                <button type="submit"
                        class="px-8 py-3 bg-blue-700 hover:bg-blue-800 text-white rounded font-bold transition border-2 border-blue-900">
                    ✓ САҚЛАШ
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Load mahallas when tuman is selected
function loadMahallas(tumanId) {
    const mahallaSelect = document.getElementById('mahalla_select');

    if (!tumanId) {
        mahallaSelect.innerHTML = '<option value="">-- Аввал туманни танланг --</option>';
        return;
    }

    mahallaSelect.disabled = true;
    mahallaSelect.innerHTML = '<option value="">Юкланмоқда...</option>';

    fetch(`/api/mahallas/${tumanId}`)
        .then(response => response.json())
        .then(data => {
            mahallaSelect.innerHTML = '<option value="">-- Маҳалла танланг --</option>';

            data.forEach(mahalla => {
                const option = document.createElement('option');
                option.value = mahalla.id;
                option.textContent = mahalla.name;

                if ('{{ old("mahalla_id") }}' == mahalla.id) {
                    option.selected = true;
                }

                mahallaSelect.appendChild(option);
            });

            mahallaSelect.disabled = false;
        })
        .catch(error => {
            console.error('Error loading mahallas:', error);
            mahallaSelect.innerHTML = '<option value="">Хатолик юз берди</option>';
            mahallaSelect.disabled = false;
        });
}

// Load mahallas on page load if tuman is selected
document.addEventListener('DOMContentLoaded', function() {
    const tumanSelect = document.getElementById('tuman_select');
    if (tumanSelect && tumanSelect.value) {
        loadMahallas(tumanSelect.value);
    }
});

// Form validation before submit
document.querySelector('form').addEventListener('submit', function(e) {
    const requiredFields = this.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.classList.add('border-red-600');
        } else {
            field.classList.remove('border-red-600');
        }
    });

    if (!isValid) {
        e.preventDefault();
        alert('Илтимос, барча мажбурий майдонларни тўлдиринг!');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>
@endsection
