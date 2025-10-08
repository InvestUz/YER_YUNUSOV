@extends('layouts.app')

@section('title', 'E-Auksion Parser')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-6 py-8">
        <!-- Header -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">E-Auksion.uz Parser</h1>
            <p class="text-sm text-gray-600">Лотларни автоматик равишда e-auksion.uz дан олиб келиш</p>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-medium mb-1">Парсер ҳақида:</p>
                    <p>Бу парсер HTML парсинг орқали ишлайди ва Chrome/Selenium талаб қилмайди. Лотлар автоматик тарзда маълумотлар базасига сақланади.</p>
                </div>
            </div>
        </div>

        <!-- Parse All Lots -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Барча лотларни парсинг қилиш</h2>

            <form id="parse-all-form" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Регион</label>
                        <select name="region" class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="1">Тошкент шаҳар</option>
                            <option value="2">Тошкент вилояти</option>
                            <option value="3">Андижон вилояти</option>
                            <option value="4">Бухоро вилояти</option>
                            <option value="5">Фарғона вилояти</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Саҳифалар сони</label>
                        <input type="number" name="max_pages" value="5" min="1" max="20"
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Максимум 20 саҳифа</p>
                    </div>
                </div>

                <button type="submit"
                    class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors">
                    Парсингни бошлаш
                </button>
            </form>

            <div id="parse-all-result" class="mt-4 hidden"></div>
            <div id="parse-all-loading" class="mt-4 hidden text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent"></div>
                <p class="text-gray-600 mt-2 text-sm">Парсинг жараёнида... Бу бир неча дақиқа давом этиши мумкин</p>
            </div>
        </div>

        <!-- Parse Single Lot -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Битта лотни парсинг қилиш</h2>

            <form id="parse-single-form" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Лот рақами</label>
                    <input type="text" name="lot_id" placeholder="18646244"
                        class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Мисол: https://e-auksion.uz/lot-view?lot_id=<strong>18646244</strong></p>
                </div>

                <button type="submit"
                    class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition-colors">
                    Лотни парсинг қилиш
                </button>
            </form>

            <div id="parse-single-result" class="mt-4 hidden"></div>
            <div id="parse-single-loading" class="mt-4 hidden text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-green-600 border-t-transparent"></div>
                <p class="text-gray-600 mt-2 text-sm">Парсинг жараёнида...</p>
            </div>
        </div>

        <!-- Command Line -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Командалар</h2>

            <div class="space-y-3">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Барча лотларни парсинг қилиш (браузер орқали):</p>
                    <code class="font-mono text-sm text-gray-900">GET /parser/parse-lots?region=1&max_pages=5</code>
                </div>

                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-600 mb-1">Битта лотни парсинг қилиш:</p>
                    <code class="font-mono text-sm text-gray-900">GET /parser/parse-single?lot_id=18646244</code>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Parse all lots
document.getElementById('parse-all-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const params = new URLSearchParams(formData);

    const loadingEl = document.getElementById('parse-all-loading');
    const resultEl = document.getElementById('parse-all-result');
    const submitBtn = e.target.querySelector('button[type="submit"]');

    loadingEl.classList.remove('hidden');
    resultEl.classList.add('hidden');
    submitBtn.disabled = true;

    try {
        const response = await fetch(`/parser/parse-lots?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();

        loadingEl.classList.add('hidden');
        resultEl.classList.remove('hidden');

        if (data.success) {
            resultEl.innerHTML = `
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800 font-medium mb-2">✓ ${data.message}</p>
                    <div class="text-sm text-green-700 space-y-1">
                        <p>Олинган лотлар: <strong>${data.total_fetched}</strong></p>
                        <p>Сақланган лотлар: <strong>${data.total_saved}</strong></p>
                    </div>
                </div>
            `;
        } else {
            resultEl.innerHTML = `
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800 font-medium">✗ Хатолик: ${data.error}</p>
                </div>
            `;
        }
    } catch (error) {
        loadingEl.classList.add('hidden');
        resultEl.classList.remove('hidden');
        resultEl.innerHTML = `
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 font-medium">✗ Хатолик: ${error.message}</p>
            </div>
        `;
    } finally {
        submitBtn.disabled = false;
    }
});

// Parse single lot
document.getElementById('parse-single-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(e.target);
    const params = new URLSearchParams(formData);

    const loadingEl = document.getElementById('parse-single-loading');
    const resultEl = document.getElementById('parse-single-result');
    const submitBtn = e.target.querySelector('button[type="submit"]');

    loadingEl.classList.remove('hidden');
    resultEl.classList.add('hidden');
    submitBtn.disabled = true;

    try {
        const response = await fetch(`/parser/parse-single?${params}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();

        loadingEl.classList.add('hidden');
        resultEl.classList.remove('hidden');

        if (data.success) {
            resultEl.innerHTML = `
                <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-green-800 font-medium mb-2">✓ Лот муваффақиятли парсинг қилинди</p>
                    <div class="text-sm text-gray-700 mt-3">
                        <table class="w-full">
                            <tr><td class="py-1 text-gray-600">Лот №:</td><td class="py-1 font-medium">${data.lot.lot_number}</td></tr>
                            <tr><td class="py-1 text-gray-600">Номи:</td><td class="py-1">${data.lot.property_name || '-'}</td></tr>
                            <tr><td class="py-1 text-gray-600">Бошланғич нарх:</td><td class="py-1">${data.lot.initial_price ? data.lot.initial_price.toLocaleString() + ' UZS' : '-'}</td></tr>
                            <tr><td class="py-1 text-gray-600">Ер майдони:</td><td class="py-1">${data.lot.land_area || '-'}</td></tr>
                            <tr><td class="py-1 text-gray-600">Манзил:</td><td class="py-1">${data.lot.address || data.lot.full_address || '-'}</td></tr>
                        </table>
                    </div>
                    ${data.saved ? '<p class="text-sm text-green-700 mt-2">✓ Маълумотлар базасига сақланди</p>' : ''}
                </div>
            `;
        } else {
            resultEl.innerHTML = `
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-red-800 font-medium">✗ Хатолик: ${data.error}</p>
                </div>
            `;
        }
    } catch (error) {
        loadingEl.classList.add('hidden');
        resultEl.classList.remove('hidden');
        resultEl.innerHTML = `
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 font-medium">✗ Хатолик: ${error.message}</p>
            </div>
        `;
    } finally {
        submitBtn.disabled = false;
    }
});
</script>
@endsection
