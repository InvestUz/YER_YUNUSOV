@extends('layouts.app')

@section('title', 'Мониторинг - Toshkent Invest')

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Мониторинг панели</h1>
    <p class="text-gray-600">Аукцион савдолари бўйича ҳисоботлар</p>
</div>

<!-- Report Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Report 1 -->
    <a href="{{ route('monitoring.report1') }}" class="group">
        <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-6 hover:border-blue-500 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg group-hover:bg-blue-600 transition-colors">
                    <svg class="w-6 h-6 text-blue-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Свод - 1</h3>
            <p class="text-sm text-gray-600 mb-4">Сотилган ер участкалар умумий маълумотлари</p>
            <div class="flex items-center text-sm font-medium text-blue-600 group-hover:text-blue-700">
                <span>Ҳисоботни кўриш</span>
                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </div>
    </a>

    <!-- Report 2 -->
    <a href="{{ route('monitoring.report2') }}" class="group">
        <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-6 hover:border-green-500 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg group-hover:bg-green-600 transition-colors">
                    <svg class="w-6 h-6 text-green-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Свод - 2</h3>
            <p class="text-sm text-gray-600 mb-4">Тақсимот ва тўловлар бўйича маълумотлар</p>
            <div class="flex items-center text-sm font-medium text-green-600 group-hover:text-green-700">
                <span>Ҳисоботни кўриш</span>
                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </div>
    </a>

    <!-- Report 3 -->
    <a href="{{ route('monitoring.report3') }}" class="group">
        <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-6 hover:border-purple-500 transition-all duration-200 hover:shadow-lg transform hover:-translate-y-1">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg group-hover:bg-purple-600 transition-colors">
                    <svg class="w-6 h-6 text-purple-600 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <svg class="w-5 h-5 text-gray-400 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Свод - 3</h3>
            <p class="text-sm text-gray-600 mb-4">Бўлиб тўлаш шарти билан сотилганлар</p>
            <div class="flex items-center text-sm font-medium text-purple-600 group-hover:text-purple-700">
                <span>Ҳисоботни кўриш</span>
                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                </svg>
            </div>
        </div>
    </a>
</div>

<!-- Info Section -->
<div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6 mb-8">
    <div class="flex items-start gap-4">
        <div class="flex-shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="flex-1">
            <h4 class="text-lg font-semibold text-blue-900 mb-3">Ҳисоботлар ҳақида</h4>
            <ul class="space-y-3 text-sm text-blue-800">
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span><strong>Свод-1:</strong> Сотилган ерлар, бир марта ва бўлиб тўлаш, расмийлаштиришда турган ва қабул қилинмаган участкалар тўғрисида маълумот</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span><strong>Свод-2:</strong> Тақсимот (маҳаллий бюджет, жамғарма, Янги Ўзбекистон, туман ҳокимияти), чегирмалар ва келгусида тушадиган маблағлар</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span><strong>Свод-3:</strong> Бўлиб тўлаш шарти билан сотилган участкалар, тўлиқ тўланган, назоратда ва график ортда қолганлар тўғрисида маълумот</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Умумий ҳисоботлар</p>
                <p class="text-3xl font-bold text-gray-900">3</p>
                <p class="text-xs text-gray-500 mt-1">Мавжуд ҳисоботлар сони</p>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-lg">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Туманлар</p>
                <p class="text-3xl font-bold text-gray-900">12</p>
                <p class="text-xs text-gray-500 mt-1">Тошкент шаҳри туманлари</p>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-lg">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-shadow">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 mb-1">Фильтрлар</p>
                <p class="text-3xl font-bold text-gray-900">6+</p>
                <p class="text-xs text-gray-500 mt-1">Ҳисоботларда мавжуд</p>
            </div>
            <div class="flex items-center justify-center w-12 h-12 bg-gray-100 rounded-lg">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Footer Note -->
<div class="mt-8 text-center text-sm text-gray-500">
    <p>Барча ҳисоботлар реал вақт режимида янгиланади</p>
</div>
@endsection
