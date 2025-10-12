@extends('layouts.app')

@section('title', 'Янги лот қўшиш')

@section('content')
<div class="min-h-screen bg-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-6">
        {{-- Page Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">Янги лот қўшиш</h1>
            <nav class="flex items-center gap-2 text-sm text-gray-600">
                <a href="{{ route('lots.index') }}" class="hover:text-gray-900">Лотлар</a>
                <span>/</span>
                <span class="text-gray-900">Янги лот</span>
            </nav>
        </div>

        <form action="{{ route('lots.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- 1. КА1726294005/2-1 – ер участкаларининг хисобини юритиш маълумотлари --}}
            <div class="bg-white border border-gray-300 shadow-sm">
                <div class="px-6 py-4 bg-blue-600 text-white">
                    <h2 class="text-base font-bold">1. КА1726294005/2-1 – ер участкаларининг хисобини юритиш маълумотлари</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Буюда --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Буюда</label>
                            <input type="text" name="buyuda" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('buyuda') }}">
                            @error('buyuda')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Уникал рақами --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Уникал рақами <span class="text-red-600">*</span></label>
                            <input type="text" name="unique_number" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('unique_number') }}"
                                   placeholder="Кадастр рақами">
                            @error('unique_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Манзили --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Манзили</label>
                            <input type="text" name="address"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('address') }}">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Майдони --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Майдони (га) <span class="text-red-600">*</span></label>
                            <input type="number" step="0.01" name="land_area" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('land_area') }}"
                                   placeholder="0.00">
                            @error('land_area')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Жойлашган зонаси --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Жойлашган зонаси</label>
                            <input type="text" name="zone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('zone') }}">
                            @error('zone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Бош река зонаси --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бош река зонаси</label>
                            <input type="text" name="master_plan_zone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('master_plan_zone') }}">
                            @error('master_plan_zone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Доканичси --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Доканичси</label>
                            <input type="text" name="dokanichsi"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('dokanichsi') }}">
                            @error('dokanichsi')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Бахоланган нархи --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Бахоланган нархи (UZS)</label>
                            <input type="number" step="0.01" name="estimated_price"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('estimated_price') }}"
                                   placeholder="0.00">
                            @error('estimated_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Расмлари --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Расмлари</label>
                            <input type="file" name="images[]" multiple accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   onchange="previewImages(event)">
                            <p class="mt-1 text-xs text-gray-500">Бир неча расм танлашингиз мумкин</p>
                            <div id="imagePreview" class="mt-4 grid grid-cols-4 gap-4"></div>
                            @error('images')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Холати --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Холати</label>
                            <input type="text" name="lot_status"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('lot_status') }}">
                            @error('lot_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Кўринали --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Кўринали</label>
                            <select name="kurinali" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <option value="">Танланг</option>
                                <option value="ha" {{ old('kurinali') == 'ha' ? 'selected' : '' }}>Ҳа</option>
                                <option value="yoq" {{ old('kurinali') == 'yoq' ? 'selected' : '' }}>Йўқ</option>
                            </select>
                            @error('kurinali')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Аукцион маълумотлари --}}
            <div class="bg-white border border-gray-300 shadow-sm">
                <div class="px-6 py-4 bg-blue-600 text-white">
                    <h2 class="text-base font-bold">2. Аукцион маълумотлари</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Буюда --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Буюда</label>
                            <input type="text" name="auction_buyuda"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('auction_buyuda') }}">
                        </div>

                        {{-- Ер участкасига бўлган хуқуқ тури --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Ер участкасига бўлган хуқуқ тури</label>
                            <input type="text" name="land_right_type"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('land_right_type') }}">
                        </div>

                        {{-- Лот рақами --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Лот рақами <span class="text-red-600">*</span></label>
                            <input type="text" name="lot_number" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('lot_number') }}">
                            @error('lot_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Лот холати --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Лот холати</label>
                            <select name="lot_status_auction" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <option value="">Танланг</option>
                                <option value="active">Актив</option>
                                <option value="sold">Сотилган</option>
                                <option value="pending">Кутилмоқда</option>
                            </select>
                        </div>

                        {{-- Аукцион ўтказиш тури --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Аукцион ўтказиш тури</label>
                            <select name="auction_type" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <option value="">Танланг</option>
                                <option value="ochiq" {{ old('auction_type') == 'ochiq' ? 'selected' : '' }}>Очиқ аукцион</option>
                                <option value="yopiq" {{ old('auction_type') == 'yopiq' ? 'selected' : '' }}>Ёпиқ танлов</option>
                            </select>
                        </div>

                        {{-- Асос --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Асос</label>
                            <input type="text" name="basis"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('basis') }}">
                        </div>

                        {{-- Аукцион ўтказилиган сана --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Аукцион ўтказилиган сана</label>
                            <input type="date" name="auction_date"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('auction_date') }}">
                        </div>

                        {{-- Голиб ФИО --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Голиб ФИО</label>
                            <input type="text" name="winner_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('winner_name') }}">
                        </div>

                        {{-- Сотилган нархи --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Сотилган нархи (UZS)</label>
                            <input type="number" step="0.01" name="sold_price"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('sold_price') }}"
                                   placeholder="0.00">
                        </div>

                        {{-- Сотилган ва рақами --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Сотилган ва рақами (муниципал бўлса, санаси ва рақами)</label>
                            <input type="text" name="sold_reference"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('sold_reference') }}">
                        </div>

                        {{-- Холати --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Холати</label>
                            <input type="text" name="status"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('status') }}">
                        </div>

                        {{-- Мулкни қабул қилиб олиш тугмаси босилган ерлар кўринали --}}
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="property_accepted" value="1" 
                                       {{ old('property_accepted') ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Мулкни қабул қилиб олиш тугмаси босилган ерлар кўринали</span>
                            </label>
                        </div>

                        {{-- Кўринали --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Кўринали</label>
                            <select name="visible" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <option value="1" {{ old('visible', '1') == '1' ? 'selected' : '' }}>Ҳа</option>
                                <option value="0" {{ old('visible') == '0' ? 'selected' : '' }}>Йўқ</option>
                            </select>
                        </div>

                        {{-- ИНН --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ИНН</label>
                            <input type="text" name="inn"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('inn') }}">
                        </div>

                        {{-- ЖШШИРга --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ЖШШИРга</label>
                            <input type="text" name="jshshir"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('jshshir') }}">
                        </div>

                        {{-- Кўрастин керак --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Кўрастин керак</label>
                            <input type="text" name="kurastin_kerak"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('kurastin_kerak') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Шартнома шартлари --}}
            <div class="bg-white border border-gray-300 shadow-sm">
                <div class="px-6 py-4 bg-blue-600 text-white">
                    <h2 class="text-base font-bold">3. Шартнома шартлари</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Буюда --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Буюда</label>
                            <input type="text" name="contract_buyuda"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('contract_buyuda') }}">
                        </div>

                        {{-- Сотилган нархи --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Сотилган нархи</label>
                            <input type="number" step="0.01" name="contract_sold_price"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('contract_sold_price') }}">
                        </div>

                        {{-- Тўлов тури --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Тўлов тури (муддатли, муддатли змас)</label>
                            <select name="payment_type" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
                                <option value="">Танланг</option>
                                <option value="muddatli" {{ old('payment_type') == 'muddatli' ? 'selected' : '' }}>Муддатли</option>
                                <option value="muddatsiz" {{ old('payment_type') == 'muddatsiz' ? 'selected' : '' }}>Муддатли эмас</option>
                            </select>
                        </div>

                        {{-- Муддатли тўлов танланганда тўлов графики кўринали --}}
                        <div class="md:col-span-2" id="paymentScheduleSection" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Муддатли тўлов танланганда тўлов графики кўринали</label>
                            <div id="paymentScheduleContainer">
                                <button type="button" onclick="addPaymentSchedule()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    + Тўлов қўшиш
                                </button>
                            </div>
                        </div>

                        {{-- Унинг таксимланиш холатлари кўринали --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Унинг таксимланиш холатлари кўринали</label>
                            <textarea name="distribution_conditions" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">{{ old('distribution_conditions') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 4. Тўлов таксимоти --}}
            <div class="bg-white border border-gray-300 shadow-sm">
                <div class="px-6 py-4 bg-blue-600 text-white">
                    <h2 class="text-base font-bold">4. Тўлов таксимоти</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Буюда --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Буюда</label>
                            <input type="text" name="distribution_buyuda"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('distribution_buyuda') }}">
                        </div>

                        {{-- Амалга оширилган тўловлар --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Амалга оширилган тўловлар</label>
                            <input type="number" step="0.01" name="completed_payments"
                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                   value="{{ old('completed_payments') }}">
                        </div>

                        {{-- Унинг таксимланиш холатлари кўринали --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Унинг таксимланиш холатлари кўринали</label>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Махаллий бюджет</label>
                                    <input type="number" step="0.01" name="local_budget"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                           value="{{ old('local_budget') }}">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Тошкент шаҳрини ривожлантириш жамғармаси</label>
                                    <input type="number" step="0.01" name="development_fund"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                           value="{{ old('development_fund') }}">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Янги Ўзбекистон дирекцияси</label>
                                    <input type="number" step="0.01" name="new_uzbekistan"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                           value="{{ old('new_uzbekistan') }}">
                                </div>
                                <div>
                                    <label class="block text-xs text-gray-600 mb-1">Туман ҳокимияти</label>
                                    <input type="number" step="0.01" name="district_authority"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                           value="{{ old('district_authority') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('lots.index') }}" 
                   class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 rounded transition font-medium">
                    Бекор қилиш
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded transition font-medium">
                    Сақлаш
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Toggle payment schedule section
document.querySelector('select[name="payment_type"]').addEventListener('change', function removePaymentSchedule(button) {
    button.closest('.payment-schedule-item').remove();
}

// Image preview function
function previewImages(event) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = event.target.files;
    
    if (files) {
        Array.from(files).forEach((file, index) => {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-32 object-cover border border-gray-300 rounded">
                    <div class="absolute top-1 right-1 bg-white rounded-full p-1 shadow">
                        <span class="text-xs text-gray-600">${index + 1}</span>
                    </div>
                `;
                preview.appendChild(div);
            };
            
            reader.readAsDataURL(file);
        });
    }
}
</script>

<style>
@media print {
    form {
        display: none;
    }
}
</style>
@endsection