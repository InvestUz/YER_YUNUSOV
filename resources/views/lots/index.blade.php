@extends('layouts.app')

@section('title', 'Лотлар - Toshkent Invest')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 sticky top-0 z-30 shadow-sm">
        <div class="max-w-full mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Лотлар бошқаруви</h1>
                    <p class="text-sm text-gray-600 mt-0.5">Барча лотлар рўйхати ва мониторинг</p>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="openFilterModal()"
                        class="px-4 py-2.5 bg-white border-2 border-blue-500 text-blue-600 rounded-lg hover:bg-blue-50 transition-colors font-medium flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                        Фильтрлаш
                        @if (request()->hasAny([
                        'search',
                        'tuman_id',
                        'mahalla_id',
                        'payment_type',
                        'zones',
                        'master_plan_zones',
                        'yangi_uzbekiston',
                        'construction_types',
                        'object_types',
                        'payment_types_extended',
                        'basis_types',
                        'auction_types',
                        'lot_statuses',
                        'contract_statuses',
                        'winner_types',
                        ]))
                        <span
                            class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 text-xs font-bold text-white bg-red-500 rounded-full animate-pulse">
                            {{ collect(request()->except(['page', 'sort', 'direction', 'per_page']))->filter()->count() }}
                        </span>
                        @endif
                    </button>
                    <button type="button" onclick="exportToExcel()"
                        class="px-4 py-2.5 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Excel
                    </button>
                    @if (Auth::user()->role === 'admin' || Auth::user()->role === 'district_user')
                    <a href="{{ route('lots.create') }}"
                        class="px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2 shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4"></path>
                        </svg>
                        Янги лот
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-full mx-auto px-6 py-6">
        @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-lg shadow-sm">
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

        <!-- Active Filters Display -->
        @if (request()->hasAny(['search', 'tuman_id', 'mahalla_id', 'payment_type']))
        <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-sm font-semibold text-blue-900 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                        </path>
                    </svg>
                    Актив филтрлар:
                </span>
                @if (request('search'))
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-blue-300 text-blue-800 rounded-full text-sm font-medium shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    {{ request('search') }}
                    <button onclick="removeFilter('search')"
                        class="hover:bg-blue-100 rounded-full p-0.5 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
                @endif
                <a href="{{ route('lots.index') }}"
                    class="ml-auto text-sm text-red-600 hover:text-red-700 font-semibold flex items-center gap-1 hover:underline">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Барча филтрларни тозалаш
                </a>
            </div>
        </div>
        @endif

        {{-- Minimal Professional Government Table --}}
        <div class="bg-white rounded-lg border border-gray-300 overflow-hidden shadow">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    {{-- Table Header --}}
                    <thead>
                        <tr class="bg-gray-100 border-b-2 border-gray-300">
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">№</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Лот рақами</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Туман</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Манзил</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Уникал №</th>
                            <th class="text-right py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Майдон</th>
                            <th class="text-right py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Бошл. нарх</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Санаси</th>
                            <th class="text-right py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Сотилган</th>
                            <th class="text-left py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Ғолиб</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Тўлов</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-700 uppercase border-r border-gray-300">Ҳолат</th>
                            <th class="text-center py-3 px-3 text-xs font-semibold text-gray-700 uppercase">Амаллар</th>
                        </tr>
                    </thead>

                    <tbody class="text-sm">
                        {{-- Summary Row --}}
                        <tr class="bg-gray-50 border-b-2 border-gray-400 font-semibold">
                            <td colspan="2" class="py-2.5 px-3 text-center border-r border-gray-300">
                                <span class="text-gray-900 uppercase">ЖАМИ: {{ number_format($lots->total()) }} та</span>
                            </td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 text-gray-500">-</td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 text-gray-500">-</td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 text-gray-500">-</td>
                            <td class="py-2.5 px-3 text-right border-r border-gray-300">
                                @if(isset($totalStats['total_area']) && $totalStats['total_area'] > 0)
                                <span class="text-gray-900">{{ number_format($totalStats['total_area'], 2) }}</span>
                                @else
                                <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right border-r border-gray-300">
                                @if(isset($totalStats['total_initial_price']) && $totalStats['total_initial_price'] > 0)
                                <span class="text-gray-900">{{ number_format($totalStats['total_initial_price'] / 1000000, 1) }}</span>
                                @else
                                <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 text-gray-500">-</td>
                            <td class="py-2.5 px-3 text-right border-r border-gray-300">
                                @if(isset($totalStats['total_sold_price']) && $totalStats['total_sold_price'] > 0)
                                <span class="text-gray-900">{{ number_format($totalStats['total_sold_price'] / 1000000, 1) }}</span>
                                @else
                                <span class="text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 text-gray-500">-</td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 text-gray-500">-</td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-300 text-gray-500">-</td>
                            <td class="py-2.5 px-3 text-center text-gray-500">-</td>
                        </tr>

                        {{-- Data Rows --}}
                        @forelse($lots as $index => $lot)
                        <tr class="border-b border-gray-200 hover:bg-gray-50">
                            <td class="py-2.5 px-3 text-center border-r border-gray-200 text-gray-600">
                                {{ ($lots->currentPage() - 1) * $lots->perPage() + $index + 1 }}
                            </td>
                            <td class="py-2.5 px-3 border-r border-gray-200">
                                <a href="{{ route('lots.show', $lot->id) }}" class="text-blue-600 hover:underline font-medium">
                                    {{ $lot->lot_number }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3 border-r border-gray-200 text-gray-700">
                                {{ $lot->tuman->name_uz ?? '-' }}
                            </td>
                            <td class="py-2.5 px-3 border-r border-gray-200 text-gray-600" title="{{ $lot->address }}">
                                {{ Str::limit($lot->address, 35) }}
                            </td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-200">
                                @if($lot->unique_number)
                                <span class="text-xs font-medium text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                    {{ $lot->unique_number }}
                                </span>
                                @else
                                <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-right border-r border-gray-200 text-gray-700 font-medium">
                                {{ number_format($lot->land_area, 2) }}
                            </td>
                            <td class="py-2.5 px-3 text-right border-r border-gray-200 text-gray-700">
                                {{ number_format($lot->initial_price / 1000000, 1) }}
                            </td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-200 text-gray-600">
                                {{ $lot->auction_date ? date('d.m.Y', strtotime($lot->auction_date)) : '-' }}
                            </td>
                            <td class="py-2.5 px-3 text-right border-r border-gray-200 text-gray-700 font-medium">
                                {{ number_format($lot->sold_price / 1000000, 1) }}
                            </td>
                            <td class="py-2.5 px-3 border-r border-gray-200 text-gray-700" title="{{ $lot->winner_name }}">
                                {{ Str::limit($lot->winner_name, 20) }}
                            </td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-200">
                                @if($lot->payment_type === 'muddatli')
                                <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-700 rounded border border-gray-300">Муддатли</span>
                                @else
                                <span class="text-xs px-2 py-0.5 bg-gray-100 text-gray-700 rounded border border-gray-300">Муддатсиз</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center border-r border-gray-200">
                                @if($lot->contract_signed)
                                <span class="text-xs px-2 py-0.5 bg-green-50 text-green-700 rounded border border-green-200">Тузилган</span>
                                @else
                                <span class="text-xs px-2 py-0.5 bg-gray-50 text-gray-600 rounded border border-gray-200">Кутилмоқда</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('lots.show', $lot->id) }}"
                                        class="p-1.5 text-blue-600 hover:bg-blue-50 rounded"
                                        title="Кўриш">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'district_user')
                                    <a href="{{ route('lots.edit', $lot->id) }}"
                                        class="p-1.5 text-gray-600 hover:bg-gray-50 rounded"
                                        title="Таҳрирлаш">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="13" class="py-12 text-center">
                                <div class="text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="font-medium text-gray-700">Маълумот топилмади</p>
                                    <p class="text-sm text-gray-500 mt-1">Филтрларни ўзгартириб кўринг</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Pagination -->
        @if ($lots->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700 font-medium">
                    <span class="font-bold text-blue-600">{{ $lots->firstItem() }}</span> -
                    <span class="font-bold text-blue-600">{{ $lots->lastItem() }}</span> /
                    <span class="font-bold text-gray-900">{{ number_format($lots->total()) }}</span> дан
                </div>
                <div class="flex items-center gap-2">
                    {{ $lots->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Enhanced Filter Modal -->
<div id="filterModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-60 z-50 overflow-y-auto backdrop-blur-sm">
    <div class="flex items-start justify-center min-h-screen pt-10 pb-20 px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-7xl transform transition-all animate-fadeIn">
            <!-- Modal Header -->
            <div
                class="flex items-center justify-between px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-t-2xl">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                            </path>
                        </svg>
                    </div>
                    Кенгайтирилган филтрлаш
                </h3>
                <button onclick="closeFilterModal()"
                    class="text-gray-400 hover:text-gray-600 transition-colors p-2 hover:bg-white rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <form method="GET" action="{{ route('lots.index') }}" id="filter-form"
                class="p-6 max-h-[70vh] overflow-y-auto">
                <!-- Basic Filters -->
                <div class="mb-8">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                        <h4 class="text-lg font-bold text-gray-800">Асосий филтрлар</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Қидириш</label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Лот рақами, манзил..."
                                class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        </div>

                        @if (Auth::user()->role === 'admin')
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Туман</label>
                            <select name="tuman_id" id="tuman_select" onchange="loadMahallas(this.value)"
                                class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Маҳалла</label>
                            <select name="mahalla_id" id="mahalla_select"
                                class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
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
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Тўлов тури</label>
                            <select name="payment_type"
                                class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                <option value="">Барчаси</option>
                                <option value="muddatli"
                                    {{ request('payment_type') === 'muddatli' ? 'selected' : '' }}>Муддатли</option>
                                <option value="muddatli_emas"
                                    {{ request('payment_type') === 'muddatli_emas' ? 'selected' : '' }}>Муддатсиз
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Advanced Filters -->
                <div class="space-y-8">
                    <!-- Zones & Master Plan -->
                    <div class="bg-purple-50 rounded-xl p-5 border-2 border-purple-200">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1 h-6 bg-purple-600 rounded-full"></div>
                            <h4 class="text-lg font-bold text-gray-800">Жойлашув ва зоналар</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Зона</label>
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach ($filterOptions['zones'] ?? [] as $zone)
                                    <label
                                        class="flex items-center p-2.5 bg-white border-2 border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 cursor-pointer transition-all">
                                        <input type="checkbox" name="zones[]" value="{{ $zone }}"
                                            {{ in_array($zone, request('zones', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-700">{{ $zone }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Бош режа зонаси</label>
                                <div class="grid grid-cols-1 gap-2">
                                    @php
                                    // Only show these 3 main zones, nothing else
                                    $allowedZones = ['Konservatsiya', 'Rekonstruksiya', 'Renovatsiya'];

                                    // Filter to show ONLY exact matches
                                    $filteredZones = [];
                                    foreach ($filterOptions['master_plan_zones'] ?? [] as $zone) {
                                    if (in_array($zone, $allowedZones)) {
                                    $filteredZones[] = $zone;
                                    }
                                    }
                                    @endphp

                                    @foreach ($filteredZones as $zone)
                                    <label
                                        class="flex items-center p-2.5 bg-white border-2 border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 cursor-pointer transition-all">
                                        <input type="checkbox" name="master_plan_zones[]"
                                            value="{{ $zone }}"
                                            {{ in_array($zone, request('master_plan_zones', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-700">{{ $zone }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Янги Ўзбекистон</label>
                                <div class="space-y-2">
                                    <label
                                        class="flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 cursor-pointer transition-all">
                                        <input type="radio" name="yangi_uzbekiston" value=""
                                            {{ request('yangi_uzbekiston') === null ? 'checked' : '' }}
                                            class="w-4 h-4 border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700">Барчаси</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 cursor-pointer transition-all">
                                        <input type="radio" name="yangi_uzbekiston" value="1"
                                            {{ request('yangi_uzbekiston') === '1' ? 'checked' : '' }}
                                            class="w-4 h-4 border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700">Ҳа</span>
                                    </label>
                                    <label
                                        class="flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg hover:border-purple-400 hover:bg-purple-50 cursor-pointer transition-all">
                                        <input type="radio" name="yangi_uzbekiston" value="0"
                                            {{ request('yangi_uzbekiston') === '0' ? 'checked' : '' }}
                                            class="w-4 h-4 border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-3 text-sm font-medium text-gray-700">Йўқ</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Construction & Object Types -->
                    <div class="bg-green-50 rounded-xl p-5 border-2 border-green-200">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1 h-6 bg-green-600 rounded-full"></div>
                            <h4 class="text-lg font-bold text-gray-800">Объект турлари</h4>
                        </div>
                        <div class="space-y-4">


                            <div>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($filterOptions['object_types'] ?? [] as $key => $label)
                                    <label
                                        class="flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg hover:border-green-400 hover:bg-green-50 cursor-pointer transition-all">
                                        <input type="checkbox" name="object_types[]" value="{{ $key }}"
                                            {{ in_array($key, request('object_types', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment, Basis & Auction Types -->
                    <div class="bg-orange-50 rounded-xl p-5 border-2 border-orange-200">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1 h-6 bg-orange-600 rounded-full"></div>
                            <h4 class="text-lg font-bold text-gray-800">Тўлов ва аукцион</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Тўлов тури (кенг)</label>
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach ($filterOptions['payment_types'] ?? [] as $key => $label)
                                    <label
                                        class="flex items-center p-2.5 bg-white border-2 border-gray-200 rounded-lg hover:border-orange-400 hover:bg-orange-50 cursor-pointer transition-all">
                                        <input type="checkbox" name="payment_types_extended[]"
                                            value="{{ $key }}"
                                            {{ in_array($key, request('payment_types_extended', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Асос</label>
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach ($filterOptions['basis_types'] ?? [] as $key => $label)
                                    <label
                                        class="flex items-center p-2.5 bg-white border-2 border-gray-200 rounded-lg hover:border-orange-400 hover:bg-orange-50 cursor-pointer transition-all">
                                        <input type="checkbox" name="basis_types[]" value="{{ $key }}"
                                            {{ in_array($key, request('basis_types', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Ўтказиш тури</label>
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach ($filterOptions['auction_types'] ?? [] as $key => $label)
                                    <label
                                        class="flex items-center p-2.5 bg-white border-2 border-gray-200 rounded-lg hover:border-orange-400 hover:bg-orange-50 cursor-pointer transition-all">
                                        <input type="checkbox" name="auction_types[]"
                                            value="{{ $key }}"
                                            {{ in_array($key, request('auction_types', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-orange-600 focus:ring-orange-500">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Lot Status & Contract -->
                    <div class="bg-indigo-50 rounded-xl p-5 border-2 border-indigo-200">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1 h-6 bg-indigo-600 rounded-full"></div>
                            <h4 class="text-lg font-bold text-gray-800">Лот ҳолати ва шартнома</h4>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Лот ҳолати</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                    @foreach ($filterOptions['lot_statuses'] ?? [] as $key => $label)
                                    <label
                                        class="flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-all">
                                        <input type="checkbox" name="lot_statuses[]" value="{{ $key }}"
                                            {{ in_array($key, request('lot_statuses', [])) ? 'checked' : '' }}
                                            class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span
                                            class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Шартнома
                                        ҳолати</label>
                                    <div class="space-y-2">
                                        <label
                                            class="flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-all">
                                            <input type="checkbox" name="contract_statuses[]" value="signed"
                                                {{ in_array('signed', request('contract_statuses', [])) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Тузилган</span>
                                        </label>
                                        <label
                                            class="flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-all">
                                            <input type="checkbox" name="contract_statuses[]" value="not_signed"
                                                {{ in_array('not_signed', request('contract_statuses', [])) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Тузилмаган</span>
                                        </label>
                                        <label
                                            class="flex items-center p-3 bg-white border-2 border-gray-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-all">
                                            <input type="checkbox" name="contract_statuses[]" value="with_date"
                                                {{ in_array('with_date', request('contract_statuses', [])) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span class="ml-3 text-sm font-medium text-gray-700">Санаси билан</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Ғолиб тури</label>
                                    <div class="grid grid-cols-1 gap-2">
                                        @foreach ($filterOptions['winner_types'] ?? [] as $type)
                                        <label
                                            class="flex items-center p-2.5 bg-white border-2 border-gray-200 rounded-lg hover:border-indigo-400 hover:bg-indigo-50 cursor-pointer transition-all">
                                            <input type="checkbox" name="winner_types[]"
                                                value="{{ $type }}"
                                                {{ in_array($type, request('winner_types', [])) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                            <span
                                                class="ml-3 text-sm font-medium text-gray-700">{{ $type }}</span>
                                        </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date and Price Ranges -->
                    <div class="bg-red-50 rounded-xl p-5 border-2 border-red-200">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1 h-6 bg-red-600 rounded-full"></div>
                            <h4 class="text-lg font-bold text-gray-800">Сана ва нарх диапазони</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Аукцион санаси</label>
                                <div class="space-y-2">
                                    <input type="date" name="auction_date_from"
                                        value="{{ request('auction_date_from') }}"
                                        class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                                    <input type="date" name="auction_date_to"
                                        value="{{ request('auction_date_to') }}"
                                        class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Нарх (млн сўм)</label>
                                <div class="space-y-2">
                                    <input type="number" name="price_from" value="{{ request('price_from') }}"
                                        placeholder="Дан" step="1000000"
                                        class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                                    <input type="number" name="price_to" value="{{ request('price_to') }}"
                                        placeholder="Гача" step="1000000"
                                        class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Майдон (га)</label>
                                <div class="space-y-2">
                                    <input type="number" name="land_area_from"
                                        value="{{ request('land_area_from') }}" placeholder="Дан" step="0.01"
                                        class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                                    <input type="number" name="land_area_to" value="{{ request('land_area_to') }}"
                                        placeholder="Гача" step="0.01"
                                        class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sorting Options -->
                    <div class="bg-gray-50 rounded-xl p-5 border-2 border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-1 h-6 bg-gray-600 rounded-full"></div>
                            <h4 class="text-lg font-bold text-gray-800">Саралаш</h4>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Майдон бўйича</label>
                                <select name="sort"
                                    class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all">
                                    <option value="auction_date"
                                        {{ request('sort') === 'auction_date' ? 'selected' : '' }}>Аукцион санаси
                                    </option>
                                    <option value="lot_number"
                                        {{ request('sort') === 'lot_number' ? 'selected' : '' }}>Лот рақами</option>
                                    <option value="sold_price"
                                        {{ request('sort') === 'sold_price' ? 'selected' : '' }}>Нарх</option>
                                    <option value="land_area" {{ request('sort') === 'land_area' ? 'selected' : '' }}>
                                        Ер майдони</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Тартиб</label>
                                <select name="direction"
                                    class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all">
                                    <option value="asc" {{ request('direction') === 'asc' ? 'selected' : '' }}>Ўсиш
                                        бўйича</option>
                                    <option value="desc" {{ request('direction') === 'desc' ? 'selected' : '' }}>
                                        Камайиш бўйича</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Саҳифада</label>
                                <select name="per_page"
                                    class="w-full px-4 py-2.5 text-sm border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500 transition-all">
                                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 та
                                    </option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 та
                                    </option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 та
                                    </option>
                                    <option value="200" {{ request('per_page') == 200 ? 'selected' : '' }}>200 та
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between px-6 py-5 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <a href="{{ route('lots.index') }}"
                    class="px-6 py-3 bg-white border-2 border-red-300 text-red-600 rounded-lg hover:bg-red-50 transition-colors font-semibold flex items-center gap-2 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Тозалаш
                </a>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeFilterModal()"
                        class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-semibold shadow-sm">
                        Бекор қилиш
                    </button>
                    <button type="button" onclick="document.getElementById('filter-form').submit()"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-semibold flex items-center gap-2 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Қидириш
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.2s ease-out;
    }
</style>

<script>
    function openFilterModal() {
        document.getElementById('filterModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeFilterModal() {
        document.getElementById('filterModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function removeFilter(param) {
        const url = new URL(window.location.href);
        url.searchParams.delete(param);
        window.location.href = url.toString();
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
                    if ('{{ request('
                        mahalla_id ') }}' == mahalla.id) {
                        option.selected = true;
                    }
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

    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFilterModal();
        }
    });

    // Close modal on outside click
    document.getElementById('filterModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeFilterModal();
        }
    });

    // Load mahallas on page load if tuman is selected
    document.addEventListener('DOMContentLoaded', function() {
        const tumanSelect = document.getElementById('tuman_select');
        if (tumanSelect && tumanSelect.value) {
            loadMahallas(tumanSelect.value);
        }
    });
</script>
@endsection
