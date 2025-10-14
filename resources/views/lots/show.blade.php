@extends('layouts.app')

@section('title', 'Лот ' . $lot->lot_number)

@section('content')
    <div class="min-h-screen bg-gray-100">
        {{-- Breadcrumb --}}
        <div class="bg-white border-b-2 border-gray-300">
            <div class="max-w-7xl mx-auto px-6 py-3">
                <nav class="flex items-center gap-2 text-sm text-gray-600">
                    <a href="{{ route('lots.index') }}" class="hover:text-gray-900">Асосий</a>
                    <span>/</span>
                    <a href="{{ route('lots.index') }}" class="hover:text-gray-900">Лотлар</a>
                    <span>/</span>
                    <span class="text-gray-900 font-medium">{{ $lot->lot_number }}</span>
                </nav>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="max-w-7xl mx-auto px-6 py-8">
            {{-- Page Header --}}
            <div class="bg-white border border-gray-300 shadow-sm mb-6 p-6">
                <h1 class="text-xl font-bold text-gray-900 mb-2">
                    Уникал № {{ $lot->unique_number }}
                </h1>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    @if ($lot->tuman)
                        <span>Туман: <strong class="text-gray-900">{{ $lot->tuman->name_uz }}</strong></span>
                    @endif
                    @if ($lot->tuman)
                        <span>Мфй: <strong class="text-gray-900">{{ $lot->mahalla->name }}</strong></span>
                    @endif
                    @if ($lot->lot_status)
                        <span class="border-l pl-4">Ҳолат: <strong
                                class="text-gray-900">{{ $lot->lot_status }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Left Column - Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Image Gallery Section --}}
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="relative bg-gray-200" style="height: 450px;">
                            @if ($lot->images && $lot->images->count() > 0)
                                {{-- Show Images --}}
                                <img id="mainImage" src="{{ $lot->primary_image_url }}" alt="Лот {{ $lot->lot_number }}"
                                    onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22800%22 height=%22600%22 viewBox=%220 0 800 600%22%3E%3Crect width=%22800%22 height=%22600%22 fill=%22%23e5e7eb%22/%3E%3Cg transform=%22translate(400,300)%22%3E%3Cpath d=%22M-80-60h160v120h-160z%22 fill=%22%239ca3af%22 opacity=%220.3%22/%3E%3Ccircle cx=%22-40%22 cy=%22-20%22 r=%2215%22 fill=%22%239ca3af%22 opacity=%220.5%22/%3E%3Cpath d=%22M-80 60l60-80 40 50 60-80 60 110h-220z%22 fill=%22%239ca3af%22 opacity=%220.4%22/%3E%3C/g%3E%3Ctext x=%22400%22 y=%22340%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2216%22 fill=%22%236b7280%22%3EРасм топилмади%3C/text%3E%3C/svg%3E'"
                                    class="w-full h-full object-contain bg-gray-100">

                                {{-- Navigation Arrows --}}
                                @if ($lot->images->count() > 1)
                                    <button onclick="previousImage()"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/95 hover:bg-white border border-gray-300 flex items-center justify-center shadow-md transition">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button onclick="nextImage()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/95 hover:bg-white border border-gray-300 flex items-center justify-center shadow-md transition">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif
                            @else
                                {{-- No Images - Show Placeholder --}}
                                <div class="w-full h-full flex items-center justify-center bg-gray-100">
                                    <div class="text-center">
                                        <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-gray-600 font-medium">Расм топилмади</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Thumbnails --}}
                        @if ($lot->images && $lot->images->count() > 1)
                            <div class="flex gap-2 p-3 bg-gray-50 border-t border-gray-300 overflow-x-auto">
                                @foreach ($lot->images as $index => $image)
                                    <div class="flex-shrink-0 w-20 h-20 border-2 cursor-pointer {{ $index === 0 ? 'border-gray-700' : 'border-gray-300' }} hover:border-gray-700 transition"
                                        onclick="showImage({{ $index }})" id="thumb-{{ $index }}">
                                        <img src="{{ $image->url }}" alt="Thumbnail {{ $index + 1 }}"
                                            onerror="this.parentElement.style.display='none'"
                                            class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Enhanced Stats Bar --}}
                        <div
                            class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-300 text-sm text-gray-600">
                            <div class="flex items-center gap-6">
                                <div class="flex items-center gap-1 group relative cursor-help">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span id="viewsCount">{{ $totalViewsCount ?? 0 }}</span>
                                    <div
                                        class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-2 px-3 whitespace-nowrap z-10">
                                        <div class="space-y-1">
                                            <div>Жами: {{ $totalViewsCount ?? 0 }}</div>
                                            <div>Уникал: {{ $uniqueViewsCount ?? 0 }}</div>
                                        </div>
                                        <div
                                            class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex items-center gap-1 group relative cursor-pointer" onclick="showMessages()">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span id="messagesCount">{{ $messagesCount ?? 0 }}</span>
                                    @if (isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                        <span
                                            class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">{{ $unreadMessagesCount }}</span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-1 cursor-pointer hover:text-red-600 transition"
                                    onclick="toggleLike()">
                                    <svg class="w-4 h-4 {{ isset($hasLiked) && $hasLiked ? 'fill-red-600 text-red-600' : '' }}"
                                        fill="{{ isset($hasLiked) && $hasLiked ? 'currentColor' : 'none' }}"
                                        stroke="currentColor" viewBox="0 0 24 24" id="likeIcon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                    <span id="likeCount">{{ $likesCount ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500">
                                Янгиланди: {{ $lot->updated_at->format('d.m.Y H:i') }}
                            </div>
                        </div>
                    </div>

                    {{-- Comprehensive Information Table --}}
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                            <h2 class="text-base font-bold text-gray-900">{{ $lot->unique_number ?? '-' }} ер
                                участкасининг маълумотлари</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-gray-200">

                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-gray-600 font-medium">Туман</td>
                                        <td class="py-3 px-4 text-gray-900">{{ optional($lot->tuman)->name_uz ?? '-' }}
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-gray-600 font-medium">Ер манзили</td>
                                        <td class="py-3 px-4 text-gray-900">{{ $lot->address ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-gray-600 font-medium">Ер майдони</td>
                                        <td class="py-3 px-4 text-gray-900 font-semibold">
                                            {{ $lot->land_area ? number_format($lot->land_area, 2) . ' га' : '-' }}</td>
                                    </tr>

                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-gray-600 font-medium">Зона</td>
                                        <td class="py-3 px-4 text-gray-900">{{ $lot->zone ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-gray-600 font-medium">Бош режа бўйича жойлашув зонаси
                                        </td>
                                        <td class="py-3 px-4 text-gray-900">{{ $lot->master_plan_zone ?? '-' }}</td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-gray-600 font-medium">Алохида дирекциясиларга мансублиги
                                        </td>
                                        <td class="py-3 px-4">
                                            @if ($lot->yangi_uzbekiston)
                                                <span
                                                    class="inline-block px-2 py-1 bg-blue-100 text-blue-800 border border-blue-300 text-xs font-medium"></span>
                                            @else
                                                <span
                                                    class="inline-block px-2 py-1 bg-gray-100 text-gray-800 border border-gray-300 text-xs font-medium">-</span>
                                            @endif
                                        </td>
                                    </tr>




                                </tbody>
                            </table>
                        </div>
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                            <h2 class="text-base font-bold text-gray-900">Аукцион маълумотлари</h2>
                        </div>

                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">

                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium w-1/3">Лот рақами</td>
                                    <td class="py-3 px-4 text-gray-900">{{ $lot->lot_number ?? '-' }}</td>
                                </tr>
                                <tr class="bg-blue-50 hover:bg-blue-100">
                                    <td class="py-3 px-4 text-gray-700 font-bold">Бошланғич нархи</td>
                                    <td class="py-3 px-4 text-blue-700 font-bold text-base">
                                        {{ $lot->initial_price ? number_format($lot->initial_price, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Аукцион санаси</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        {{ $lot->auction_date ? $lot->auction_date->format('d.m.Y') : '-' }}</td>
                                </tr>
                                <tr class="bg-green-50 hover:bg-green-100">
                                    <td class="py-3 px-4 text-gray-700 font-bold">Сотилган нархи</td>
                                    <td class="py-3 px-4 text-green-700 font-bold text-base">
                                        {{ $lot->sold_price ? number_format($lot->sold_price, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Аукцион ғолиби / Ғолиб номи</td>
                                    <td class="py-3 px-4 text-gray-900 font-semibold">{{ $lot->winner_name ?? '-' }}</td>
                                </tr>

                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Телефон рақами</td>
                                    <td class="py-3 px-4 text-gray-900">{{ $lot->winner_phone ?? '-' }}</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Шартнома холати</td>
                                    <td class="py-3 px-4">
                                        @if ($lot->contract_signed)
                                            <div>
                                                <span
                                                    class="inline-block px-2 py-1 bg-green-100 text-green-800 border border-green-300 text-xs font-medium mb-2">Тузилган</span>
                                                @if ($lot->contract_date)
                                                    <div class="text-sm text-gray-700">Сана:
                                                        {{ $lot->contract_date->format('d.m.Y') }}</div>
                                                @endif
                                                @if ($lot->contract_number)
                                                    <div class="text-sm text-gray-700">Рақам: {{ $lot->contract_number }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <span
                                                class="inline-block px-2 py-1 bg-gray-100 text-gray-800 border border-gray-300 text-xs font-medium">Тузилмаган</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Тўлов тури</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        @if ($lot->payment_type === 'muddatli')
                                            <span
                                                class="inline-block px-2 py-1 bg-blue-100 text-blue-800 border border-blue-300 text-xs font-medium">бўлиб
                                                тўлаш</span>
                                        @else
                                            <span
                                                class="inline-block px-2 py-1 bg-gray-100 text-gray-800 border border-gray-300 text-xs font-medium">бир
                                                йўла</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Асос</td>
                                    <td class="py-3 px-4 text-gray-900">{{ $lot->basis ?? '-' }}</td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Аукцион тури</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        @if ($lot->auction_type === 'ochiq')
                                            Очиқ аукцион
                                        @elseif($lot->auction_type === 'yopiq')
                                            Ёпиқ танлов
                                        @else
                                            {{ $lot->auction_type ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Лот ҳолати</td>
                                    <td class="py-3 px-4 text-gray-900">{{ $lot->lot_status ?? '-' }}</td>
                                </tr>

                                <tr class="bg-yellow-50 hover:bg-yellow-100">
                                    <td class="py-3 px-4 text-gray-700 font-bold">Ғолиб аукционга тўлаган сумма</td>
                                    <td class="py-3 px-4 text-gray-900 font-semibold">
                                        {{ $lot->paid_amount ? number_format($lot->paid_amount, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Буюртмачига ўтказилган сумма</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        {{ $lot->transferred_amount ? number_format($lot->transferred_amount, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Чегирма</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        {{ $lot->discount ? number_format($lot->discount, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Аукцион ҳаражати (1%)</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        {{ $lot->auction_fee ? number_format($lot->auction_fee, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Тушадиган маблағ</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        {{ $lot->incoming_amount ? number_format($lot->incoming_amount, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                                <tr class="bg-purple-50 hover:bg-purple-100">
                                    <td class="py-3 px-4 text-gray-700 font-bold">Давактивга тушган маблағ</td>
                                    <td class="py-3 px-4 text-purple-700 font-bold">
                                        {{ $lot->davaktiv_amount ? number_format($lot->davaktiv_amount, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Ерни аукционга чиқариш ва аукцион
                                        харажатлари</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        {{ $lot->auction_expenses ? number_format($lot->auction_expenses, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Distribution Statistics --}}
                @if (isset($distributionStats) && is_array($distributionStats) && ($distributionStats['total_distributed'] ?? 0) > 0)
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                            <h2 class="text-base font-bold text-gray-900">Тақсимот</h2>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if (($distributionStats['local_budget'] ?? 0) > 0)
                                    <div class="p-4 bg-blue-50 border border-blue-300">
                                        <p class="text-xs text-gray-600 mb-1">Махаллий бюджет</p>
                                        <p class="text-lg font-bold text-blue-700">
                                            {{ number_format($distributionStats['local_budget'], 0, '.', ' ') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">UZS</p>
                                    </div>
                                @endif

                                @if (($distributionStats['development_fund'] ?? 0) > 0)
                                    <div class="p-4 bg-green-50 border border-green-300">
                                        <p class="text-xs text-gray-600 mb-1">Тошкент шаҳрини ривожлантириш жамғармаси</p>
                                        <p class="text-lg font-bold text-green-700">
                                            {{ number_format($distributionStats['development_fund'], 0, '.', ' ') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">UZS</p>
                                    </div>
                                @endif

                                @if (($distributionStats['new_uzbekistan'] ?? 0) > 0)
                                    <div class="p-4 bg-purple-50 border border-purple-300">
                                        <p class="text-xs text-gray-600 mb-1">Алохида дирекциясиларга мансублиги</p>
                                        <p class="text-lg font-bold text-purple-700">
                                            {{ number_format($distributionStats['new_uzbekistan'], 0, '.', ' ') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">UZS</p>
                                    </div>
                                @endif

                                @if (($distributionStats['district_authority'] ?? 0) > 0)
                                    <div class="p-4 bg-orange-50 border border-orange-300">
                                        <p class="text-xs text-gray-600 mb-1">Туман ҳокимияти</p>
                                        <p class="text-lg font-bold text-orange-700">
                                            {{ number_format($distributionStats['district_authority'], 0, '.', ' ') }}</p>
                                        <p class="text-xs text-gray-500 mt-1">UZS</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Payment Schedule --}}
                @if ($lot->payment_type === 'muddatli' && $lot->paymentSchedules && $lot->paymentSchedules->count() > 0)
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                            <h2 class="text-base font-bold text-gray-900">Тўлов жадвали</h2>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-100 border-b-2 border-gray-300">
                                    <tr>
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700">№</th>
                                        <th class="py-3 px-4 text-left font-semibold text-gray-700">Тўлов санаси</th>
                                        <th class="py-3 px-4 text-right font-semibold text-gray-700">Режа (UZS)</th>
                                        <th class="py-3 px-4 text-right font-semibold text-gray-700">Тўланган (UZS)</th>
                                        <th class="py-3 px-4 text-center font-semibold text-gray-700">Ҳолат</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach ($lot->paymentSchedules->sortBy('payment_date') as $index => $schedule)
                                        <tr class="hover:bg-gray-50">
                                            <td class="py-3 px-4 font-medium text-gray-900">{{ $index + 1 }}</td>
                                            <td class="py-3 px-4 text-gray-700">
                                                {{ $schedule->payment_date->format('d.m.Y') }}</td>
                                            <td class="py-3 px-4 text-right font-medium text-gray-900">
                                                {{ number_format($schedule->planned_amount, 0, '.', ' ') }}</td>
                                            <td
                                                class="py-3 px-4 text-right font-medium {{ $schedule->actual_amount > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                                {{ number_format($schedule->actual_amount, 0, '.', ' ') }}</td>
                                            <td class="py-3 px-4 text-center">
                                                @if ($schedule->actual_amount >= $schedule->planned_amount)
                                                    <span
                                                        class="inline-block px-3 py-1 bg-green-100 text-green-800 border border-green-300 text-xs font-medium">Тўланган</span>
                                                @elseif($schedule->payment_date < now())
                                                    <span
                                                        class="inline-block px-3 py-1 bg-red-100 text-red-800 border border-red-300 text-xs font-medium">Муддати
                                                        ўтган</span>
                                                @else
                                                    <span
                                                        class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 border border-yellow-300 text-xs font-medium">Кутилмоқда</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Right Column - Sidebar --}}
            <div class="space-y-6">
                {{-- Auction Countdown Timer --}}
                @if (isset($auctionCountdown) && $auctionCountdown)
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="px-6 py-4 bg-gray-700 text-white">
                            <h3 class="text-sm font-bold text-center">Савдо тугашига қолган вақт</h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-4 gap-3">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-gray-900 mb-1">
                                        {{ str_pad($auctionCountdown->days, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-xs text-gray-600 uppercase">КУН</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-gray-900 mb-1">
                                        {{ str_pad($auctionCountdown->h, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-xs text-gray-600 uppercase">СОАТ</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-gray-900 mb-1">
                                        {{ str_pad($auctionCountdown->i, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-xs text-gray-600 uppercase">ДАҚ</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-gray-900 mb-1">
                                        {{ str_pad($auctionCountdown->s, 2, '0', STR_PAD_LEFT) }}</div>
                                    <div class="text-xs text-gray-600 uppercase">СОН</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif


                {{-- Location Map --}}
                @if ($lot->map_embed_url)
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                            <h3 class="text-sm font-bold text-gray-900">Локация</h3>
                        </div>
                        <div class="h-64 bg-gray-200 border-b border-gray-300">
                            <iframe width="100%" height="100%" frameborder="0" scrolling="no"
                                src="{{ $lot->map_embed_url }}&zoom=17" class="w-full h-full">
                            </iframe>
                        </div>
                        <div class="p-4">
                            <div class="space-y-2 text-sm">
                                @if ($lot->address)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Манзил:</p>
                                        <p class="text-gray-900 font-medium">{{ $lot->address }}</p>
                                    </div>
                                @endif
                                @if ($lot->tuman)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Туман:</p>
                                        <p class="text-gray-900 font-medium">{{ $lot->tuman->name_uz }}</p>
                                    </div>
                                @endif
                                @if ($lot->mahalla)
                                    <div>
                                        <p class="text-xs text-gray-500 mb-1">Маҳалла:</p>
                                        <p class="text-gray-900 font-medium">{{ $lot->mahalla->name_uz }}</p>
                                    </div>
                                @endif
                            </div>

                            @if ($lot->location_url)
                                <a href="{{ $lot->location_url }}" target="_blank"
                                    class="mt-4 block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 border border-gray-300 transition text-sm font-medium">
                                    Google Maps-да очиш
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Financial Metrics --}}
                @if (isset($financialMetrics) &&
                        is_array($financialMetrics) &&
                        (($financialMetrics['price_increase'] ?? 0) > 0 || $lot->investment_amount > 0))
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                            <h3 class="text-sm font-bold text-gray-900">Молиявий кўрсаткичлар</h3>
                        </div>
                        <div class="p-6">
                            <table class="w-full text-sm">
                                <tbody class="divide-y divide-gray-200">
                                    @if (($financialMetrics['price_per_hectare'] ?? 0) > 0)
                                        <tr>
                                            <td class="py-2 text-gray-600">1 га нархи:</td>
                                            <td class="py-2 text-gray-900 font-medium text-right">
                                                {{ number_format($financialMetrics['price_per_hectare'], 0, '.', ' ') }}
                                                UZS</td>
                                        </tr>
                                    @endif
                                    @if (($financialMetrics['price_increase'] ?? 0) > 0)
                                        <tr>
                                            <td class="py-2 text-gray-600">Нарх ўсиши:</td>
                                            <td class="py-2 text-green-600 font-medium text-right">
                                                +{{ number_format($financialMetrics['price_increase_percent'] ?? 0, 1) }}%
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-2 text-gray-600">Фойда:</td>
                                            <td class="py-2 text-green-600 font-medium text-right">
                                                {{ number_format($financialMetrics['price_increase'], 0, '.', ' ') }} UZS
                                            </td>
                                        </tr>
                                    @endif
                                    @if ($lot->investment_amount > 0)
                                        <tr>
                                            <td class="py-2 text-gray-600">Инвестиция:</td>
                                            <td class="py-2 text-gray-900 font-medium text-right">
                                                {{ number_format($lot->investment_amount, 0, '.', ' ') }} UZS</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="p-4 space-y-2">
                        @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
                            <a href="{{ route('lots.edit', $lot) }}"
                                class="block w-full text-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white border border-gray-900 transition text-sm font-medium">
                                Таҳрирлаш
                            </a>
                        @endif
                        <button onclick="window.print()"
                            class="block w-full text-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 transition text-sm font-medium">
                            Чоп этиш
                        </button>
                    </div>
                </div>

                {{-- Analytics Links (Admin Only) --}}
                @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                            <h3 class="text-sm font-bold text-gray-900">Аналитика</h3>
                        </div>
                        <div class="p-4 space-y-2">
                            <a href="{{ route('analytics.lot.views', $lot) }}"
                                class="block w-full text-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 transition text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Кўришлар батафсил
                            </a>

                            <a href="{{ route('analytics.lot.messages', $lot) }}"
                                class="block w-full text-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 transition text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                Хабарлар
                                @if (isset($unreadMessagesCount) && $unreadMessagesCount > 0)
                                    <span
                                        class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full ml-2">{{ $unreadMessagesCount }}</span>
                                @endif
                            </a>
                        </div>
                    </div>
                @endif

                {{-- System Information --}}
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                        <h3 class="text-sm font-bold text-gray-900">Тизим маълумотлари</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-2 text-xs text-gray-600">
                            <div class="flex justify-between">
                                <span>Яратилган:</span>
                                <span class="font-medium text-gray-900">{{ $lot->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Янгиланган:</span>
                                <span class="font-medium text-gray-900">{{ $lot->updated_at->format('d.m.Y H:i') }}</span>
                            </div>
                            @if ($lot->unique_number)
                                <div class="flex justify-between">
                                    <span>ID:</span>
                                    <span class="font-medium text-gray-900">{{ $lot->unique_number }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Message Modal --}}
    <div id="messageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-300">
                <h3 class="text-lg font-bold text-gray-900">Хабар юбориш</h3>
                <button onclick="closeMessageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="messageForm" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Исмингиз *</label>
                    <input type="text" name="name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-gray-700"
                        value="{{ Auth::check() ? Auth::user()->name : '' }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-gray-700"
                        value="{{ Auth::check() ? Auth::user()->email : '' }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Телефон</label>
                    <input type="tel" name="phone"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-gray-700"
                        placeholder="+998 XX XXX XX XX">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Хабарингиз *</label>
                    <textarea name="message" required rows="5"
                        class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-gray-700"
                        placeholder="Саволингизни ёки фикрингизни ёзинг..."></textarea>
                </div>

                <div class="flex gap-3">
                    <button type="submit"
                        class="flex-1 bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 px-6 border border-gray-900 transition">
                        Юбориш
                    </button>
                    <button type="button" onclick="closeMessageModal()"
                        class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 transition font-medium">
                        Бекор қилиш
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Image Gallery Class
        class LotGallery {
            constructor(images) {
                this.images = images || [];
                this.currentIndex = 0;
                if (this.images.length > 0) {
                    this.init();
                }
            }

            init() {
                this.updateImage();
                this.setupKeyboardNavigation();
            }

            showImage(index) {
                if (index < 0 || index >= this.images.length) return;
                this.currentIndex = index;
                this.updateImage();
            }

            previousImage() {
                if (this.images.length === 0) return;
                this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                this.updateImage();
            }

            nextImage() {
                if (this.images.length === 0) return;
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
                this.updateImage();
            }

            updateImage() {
                const mainImage = document.getElementById('mainImage');
                if (mainImage && this.images.length > 0) {
                    mainImage.src = this.images[this.currentIndex];
                }
                this.updateThumbnails();
            }

            updateThumbnails() {
                document.querySelectorAll('[id^="thumb-"]').forEach((thumb, index) => {
                    thumb.classList.toggle('border-gray-700', index === this.currentIndex);
                    thumb.classList.toggle('border-gray-300', index !== this.currentIndex);
                });
            }

            setupKeyboardNavigation() {
                if (this.images.length > 1) {
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'ArrowLeft') this.previousImage();
                        if (e.key === 'ArrowRight') this.nextImage();
                    });
                }
            }
        }

        // Lot Interactions Class
        class LotInteractions {
            constructor(lotId, csrfToken) {
                this.lotId = lotId;
                this.csrfToken = csrfToken;
            }

            async toggleLike() {
                try {
                    const response = await fetch(`/lots/${this.lotId}/toggle-like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();
                    this.updateLikeUI(data);
                } catch (error) {
                    console.error('Error toggling like:', error);
                }
            }

            updateLikeUI(data) {
                const likeCount = document.getElementById('likeCount');
                const likeIcon = document.getElementById('likeIcon');

                if (likeCount) likeCount.textContent = data.count || 0;

                if (likeIcon) {
                    likeIcon.setAttribute('fill', data.liked ? 'currentColor' : 'none');
                    likeIcon.classList.toggle('fill-red-600', data.liked);
                    likeIcon.classList.toggle('text-red-600', data.liked);
                }
            }

            async sendMessage(formData) {
                try {
                    const response = await fetch(`/lots/${this.lotId}/send-message`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': this.csrfToken
                        },
                        body: JSON.stringify(formData)
                    });

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    const data = await response.json();

                    if (data.success) {
                        this.updateMessageCount();
                        return true;
                    }
                    return false;
                } catch (error) {
                    console.error('Error sending message:', error);
                    return false;
                }
            }

            updateMessageCount() {
                const messagesCount = document.getElementById('messagesCount');
                if (messagesCount) {
                    const currentCount = parseInt(messagesCount.textContent) || 0;
                    messagesCount.textContent = currentCount + 1;
                }
            }
        }

        // Message Modal Class
        class MessageModal {
            constructor() {
                this.modal = document.getElementById('messageModal');
                this.form = document.getElementById('messageForm');
                if (this.modal && this.form) {
                    this.init();
                }
            }

            init() {
                this.setupEventListeners();
            }

            setupEventListeners() {
                // Close on outside click
                if (this.modal) {
                    this.modal.addEventListener('click', (e) => {
                        if (e.target === this.modal) this.close();
                    });
                }

                // Form submission
                if (this.form) {
                    this.form.addEventListener('submit', (e) => this.handleSubmit(e));
                }
            }

            show() {
                if (this.modal) {
                    this.modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            }

            close() {
                if (this.modal) {
                    this.modal.classList.add('hidden');
                    document.body.style.overflow = 'auto';
                }
            }

            async handleSubmit(e) {
                e.preventDefault();

                const formData = new FormData(this.form);
                const data = Object.fromEntries(formData);

                const success = await window.lotInteractions.sendMessage(data);

                if (success) {
                    alert('Хабарингиз муваффақиятли юборилди');
                    this.close();
                    this.form.reset();
                } else {
                    alert('Хабар юборишда хатолик юз берди');
                }
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize gallery
            const images = @json($lot->images && $lot->images->count() > 0 ? $lot->images->map(fn($img) => $img->url)->values() : []);
            window.lotGallery = new LotGallery(images);

            // Initialize interactions
            window.lotInteractions = new LotInteractions(
                {{ $lot->id }},
                '{{ csrf_token() }}'
            );

            // Initialize modal
            window.messageModal = new MessageModal();
        });

        // Global functions for backwards compatibility
        function showImage(index) {
            if (window.lotGallery) {
                window.lotGallery.showImage(index);
            }
        }

        function previousImage() {
            if (window.lotGallery) {
                window.lotGallery.previousImage();
            }
        }

        function nextImage() {
            if (window.lotGallery) {
                window.lotGallery.nextImage();
            }
        }

        function toggleLike() {
            if (window.lotInteractions) {
                window.lotInteractions.toggleLike();
            }
        }

        function showMessages() {
            if (window.messageModal) {
                window.messageModal.show();
            }
        }

        function closeMessageModal() {
            if (window.messageModal) {
                window.messageModal.close();
            }
        }
    </script>

    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }
        }

        button:focus,
        a:focus {
            outline: 2px solid #374151;
            outline-offset: 2px;
        }
    </style>
@endsection
