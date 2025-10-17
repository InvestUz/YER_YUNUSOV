@extends('layouts.app')

@section('title', 'Лот ' . $lot->lot_number)

@section('content')
    <div class="min-h-screen bg-gray-100">

        {{-- ========================================== --}}
        {{-- SUCCESS/ERROR MESSAGES                    --}}
        {{-- ========================================== --}}
        @if (session('success'))
            <div class="max-w-7xl mx-auto px-6 py-3">
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="max-w-7xl mx-auto px-6 py-3">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session('info'))
            <div class="max-w-7xl mx-auto px-6 py-3">
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('info') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="max-w-7xl mx-auto px-6 py-3">
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative">
                    <span class="block sm:inline">{{ session('warning') }}</span>
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-yellow-500" role="button" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="max-w-7xl mx-auto px-6 py-3">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <strong class="font-bold">Хатолик!</strong>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button onclick="this.parentElement.parentElement.remove()"
                        class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- ========================================== --}}
        {{-- BREADCRUMB                                --}}
        {{-- ========================================== --}}
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

        {{-- ========================================== --}}
        {{-- MAIN CONTENT                              --}}
        {{-- ========================================== --}}
        <div class="max-w-7xl mx-auto px-6 py-8">

            {{-- PAGE HEADER --}}
            <div class="bg-white border border-gray-300 shadow-sm mb-6 p-6">
                <h1 class="text-xl font-bold text-gray-900 mb-2">
                    Уникал № {{ $lot->unique_number }}
                </h1>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    @if ($lot->tuman)
                        <span>Туман: <strong class="text-gray-900">{{ $lot->tuman->name_uz }}</strong></span>
                    @endif
                    @if ($lot->mahalla)
                        <span>Мфй: <strong class="text-gray-900">{{ $lot->mahalla->name }}</strong></span>
                    @endif
                    @if ($lot->lot_status)
                        <span class="border-l pl-4">Ҳолат: <strong
                                class="text-gray-900">{{ $lot->lot_status }}</strong></span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ========================================== --}}
                {{-- LEFT COLUMN - MAIN CONTENT                --}}
                {{-- ========================================== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- IMAGE GALLERY --}}
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="relative bg-gray-200" style="height: 450px;">
                            @if ($lot->images && $lot->images->count() > 0)
                                <img id="mainImage" src="{{ $lot->primary_image_url }}" alt="Лот {{ $lot->lot_number }}"
                                    class="w-full h-full object-contain bg-gray-100">

                                @if ($lot->images->count() > 1)
                                    <button onclick="previousImage()"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/95 hover:bg-white border border-gray-300 flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button onclick="nextImage()"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/95 hover:bg-white border border-gray-300 flex items-center justify-center shadow-md">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                @endif
                            @else
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
                                    <div class="flex-shrink-0 w-20 h-20 border-2 cursor-pointer {{ $index === 0 ? 'border-gray-700' : 'border-gray-300' }} hover:border-gray-700"
                                        onclick="showImage({{ $index }})" id="thumb-{{ $index }}">
                                        <img src="{{ $image->url }}" alt="Thumbnail {{ $index + 1 }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Stats Bar --}}
                        <div
                            class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-300 text-sm text-gray-600">
                            <div class="flex items-center gap-6">
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span>{{ $totalViewsCount ?? 0 }}</span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                    </svg>
                                    <span>{{ $messagesCount ?? 0 }}</span>
                                </div>
                                <div class="flex items-center gap-1 cursor-pointer hover:text-red-600"
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

                    {{-- LOT INFORMATION TABLE --}}
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
                                            {{ $lot->land_area ? number_format($lot->land_area, 2) . ' га' : '-' }}
                                        </td>
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
                                        {{ $lot->auction_date ? $lot->auction_date->format('d.m.Y') : '-' }}
                                    </td>
                                </tr>
                                <tr class="bg-green-50 hover:bg-green-100">
                                    <td class="py-3 px-4 text-gray-700 font-bold">Сотилган нархи</td>
                                    <td class="py-3 px-4 text-green-700 font-bold text-base">
                                        {{ $lot->sold_price ? number_format($lot->sold_price, 0, '.', ' ') . ' UZS' : '-' }}
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Аукцион ғолиби</td>
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
                                            <span
                                                class="inline-block px-2 py-1 bg-green-100 text-green-800 border border-green-300 text-xs font-medium">
                                                Тузилган
                                            </span>
                                            @if ($lot->contract_date)
                                                <div class="text-sm text-gray-700 mt-1">Сана:
                                                    {{ $lot->contract_date->format('d.m.Y') }}</div>
                                            @endif
                                            @if ($lot->contract_number)
                                                <div class="text-sm text-gray-700">Рақам: {{ $lot->contract_number }}
                                                </div>
                                            @endif
                                        @else
                                            <span
                                                class="inline-block px-2 py-1 bg-gray-100 text-gray-800 border border-gray-300 text-xs font-medium">
                                                Тузилмаган
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 text-gray-600 font-medium">Тўлов тури</td>
                                    <td class="py-3 px-4 text-gray-900">
                                        @if ($lot->payment_type === 'muddatli')
                                            <span
                                                class="inline-block px-2 py-1 bg-blue-100 text-blue-800 border border-blue-300 text-xs font-medium">
                                                бўлиб тўлаш
                                            </span>
                                        @else
                                            <span
                                                class="inline-block px-2 py-1 bg-gray-100 text-gray-800 border border-gray-300 text-xs font-medium">
                                                бир йўла
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- ============================================ --}}
                    {{-- CONTRACT PAYMENT SCHEDULE TABLE             --}}
                    {{-- ============================================ --}}
                    @if ($lot->contract)
                        <div class="bg-white border border-gray-300 shadow-sm">
                            <div class="bg-blue-600 text-white px-6 py-3 flex justify-between items-center">
                                <h2 class="font-bold">ХИСОБОТ ДАВРИДА БАЖАРИЛИШИ БЕЛГИЛАНГАН МАЖБУРИЯТЛАР</h2>
                                <button onclick="openAddScheduleModal()"
                                    class="bg-white text-blue-600 px-3 py-1 rounded text-sm hover:bg-blue-50">
                                    + Қўшиш
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th rowspan="2" class="px-2 py-2 border">№</th>
                                            <th rowspan="2" class="px-2 py-2 border">сана</th>
                                            <th colspan="3" class="px-2 py-2 border text-center">хисобот даврида
                                                (сўздиришув)</th>
                                            <th colspan="3" class="px-2 py-2 border text-center">иш ўрини соли</th>
                                            <th colspan="3" class="px-2 py-2 border text-center">хисобот саналари (усиб
                                                бўлиниш)</th>
                                            <th colspan="3" class="px-2 py-2 border text-center">иш ўрини соли</th>
                                            <th rowspan="2" class="px-2 py-2 border">Амаллар</th>
                                        </tr>
                                        <tr class="bg-gray-50">
                                            <th class="px-2 py-1 border">график</th>
                                            <th class="px-2 py-1 border">амалда</th>
                                            <th class="px-2 py-1 border">+/-</th>
                                            <th class="px-2 py-1 border">график</th>
                                            <th class="px-2 py-1 border">амалда</th>
                                            <th class="px-2 py-1 border">+/-</th>
                                            <th class="px-2 py-1 border">график</th>
                                            <th class="px-2 py-1 border">амалда</th>
                                            <th class="px-2 py-1 border">+/-</th>
                                            <th class="px-2 py-1 border">график</th>
                                            <th class="px-2 py-1 border">амалда</th>
                                            <th class="px-2 py-1 border">+/-</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($lot->contract->paymentSchedules->sortBy('planned_date') as $index => $schedule)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-2 py-2 border text-center">{{ $index + 1 }}</td>
                                                <td class="px-2 py-2 border text-center">
                                                    {{ $schedule->planned_date->format('d.m.Y') }}</td>
                                                <td class="px-2 py-2 border text-right">
                                                    {{ number_format($schedule->planned_amount, 0, '.', ' ') }}</td>
                                                <td
                                                    class="px-2 py-2 border text-right {{ $schedule->actual_amount > 0 ? 'bg-green-50 font-bold' : '' }}">
                                                    {{ number_format($schedule->actual_amount, 0, '.', ' ') }}
                                                </td>
                                                <td
                                                    class="px-2 py-2 border text-right {{ $schedule->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $schedule->difference != 0 ? number_format($schedule->difference, 0, '.', ' ') : '0' }}
                                                </td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right">
                                                    {{ number_format($schedule->planned_amount, 0, '.', ' ') }}</td>
                                                <td
                                                    class="px-2 py-2 border text-right {{ $schedule->actual_amount > 0 ? 'bg-green-50 font-bold' : '' }}">
                                                    {{ number_format($schedule->actual_amount, 0, '.', ' ') }}
                                                </td>
                                                <td
                                                    class="px-2 py-2 border text-right {{ $schedule->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ $schedule->difference != 0 ? number_format($schedule->difference, 0, '.', ' ') : '0' }}
                                                </td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-center whitespace-nowrap">
                                                    <button
                                                        onclick="openPaymentModal({{ $schedule->id }}, '{{ $schedule->planned_date->format('Y-m-d') }}', {{ $schedule->planned_amount }})"
                                                        class="text-blue-600 hover:underline mr-2">
                                                        Тўлов
                                                    </button>
                                                    @if ($schedule->actual_amount > 0)
                                                        <a href="{{ route('distributions.create', ['payment_schedule_id' => $schedule->id]) }}"
                                                            class="text-green-600 hover:underline">
                                                            Тақсимлаш
                                                        </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="14" class="px-4 py-8 text-center text-gray-500">
                                                    График яратилмаган. Юқоридаги "+ Қўшиш" тугмасини босинг.
                                                </td>
                                            </tr>
                                        @endforelse

                                        @if ($lot->contract->paymentSchedules->count() > 0)
                                            <tr class="bg-gray-100 font-bold">
                                                <td colspan="2" class="px-2 py-2 border text-right">жами:</td>
                                                <td class="px-2 py-2 border text-right">
                                                    {{ number_format($lot->contract->paymentSchedules->sum('planned_amount'), 0, '.', ' ') }}
                                                </td>
                                                <td class="px-2 py-2 border text-right">
                                                    {{ number_format($lot->contract->paymentSchedules->sum('actual_amount'), 0, '.', ' ') }}
                                                </td>
                                                <td class="px-2 py-2 border"></td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right">
                                                    {{ number_format($lot->contract->paymentSchedules->sum('planned_amount'), 0, '.', ' ') }}
                                                </td>
                                                <td class="px-2 py-2 border text-right">
                                                    {{ number_format($lot->contract->paymentSchedules->sum('actual_amount'), 0, '.', ' ') }}
                                                </td>
                                                <td class="px-2 py-2 border"></td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border text-right text-gray-400">0</td>
                                                <td class="px-2 py-2 border"></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- ============================================ --}}
                        {{-- DISTRIBUTION TABLE                          --}}
                        {{-- ============================================ --}}
                        <div class="bg-white border border-gray-300 shadow-sm">
                            <div class="bg-blue-600 text-white px-6 py-3">
                                <h2 class="font-bold">ХИСОБОТ ДАВРИДА БАЖАРИЛИШИ БЕЛГИЛАНГАН МАҲБУРИЯТЛАР БЎЙИЧА ТАҚСИМОТ
                                </h2>
                            </div>

                            <div class="p-6">
                                <table class="w-full text-xs border-collapse mb-6">
                                    <thead>
                                        <tr class="bg-gray-100">
                                            <th class="px-2 py-2 border">хисобот саналари</th>
                                            <th class="px-2 py-2 border">инвестиция режаси (млн. сўм)</th>
                                            <th class="px-2 py-2 border">инвестиция режаси (минг дол.)</th>
                                            <th class="px-2 py-2 border">яратилдиган иш ўрини сони</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="px-2 py-2 border">{{ now()->format('d.m.Y') }}</td>
                                            <td class="px-2 py-2 border text-right">0</td>
                                            <td class="px-2 py-2 border text-right">0</td>
                                            <td class="px-2 py-2 border text-right">0</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <h3 class="font-bold mb-3 text-sm">ХИСОБОТ ДАВРИДА АМАЛДА БАЖАРИЛГАН МАЖБУРИЯТЛАР</h3>

                                <table class="w-full text-xs border-collapse mb-4">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="px-2 py-2 border">амалда инвестиция (млн. сўм)</th>
                                            <th class="px-2 py-2 border">амалда инвестиция (минг дол.)</th>
                                            <th class="px-2 py-2 border">яратилган иш ўрини сони</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="px-2 py-2 border text-right">
                                                {{ number_format($lot->contract->distributions->sum('allocated_amount'), 0, '.', ' ') }}
                                            </td>
                                            <td class="px-2 py-2 border text-right">0</td>
                                            <td class="px-2 py-2 border text-right">0</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="mb-4">
                                    <label class="block text-xs font-medium mb-1">Мониторинг далолатномаси:</label>
                                    <textarea class="w-full border rounded px-2 py-1 text-xs" rows="2"></textarea>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-xs font-medium mb-1">Фотосуратлар:</label>
                                    <input type="file" class="text-xs">
                                </div>
                            </div>

                            <div class="px-6 py-3 bg-gray-50 border-t flex justify-end">
                                <button class="bg-blue-600 text-white px-6 py-2 rounded text-sm hover:bg-blue-700">
                                    Чиқариш
                                </button>
                            </div>
                        </div>
                    @endif

                </div>

                {{-- ========================================== --}}
                {{-- RIGHT COLUMN - SIDEBAR                    --}}
                {{-- ========================================== --}}
                <div class="space-y-6">

                    {{-- CONTRACT STATUS CARD OR CREATE FORM --}}
                    @if ($lot->contract)
                        {{-- Existing Contract Display --}}
                        <div class="bg-white border border-gray-300 shadow-sm">
                            <div class="px-6 py-4 bg-green-50 border-b border-green-300">
                                <h3 class="font-bold text-green-900">Шартнома № {{ $lot->contract->contract_number }}</h3>
                                <p class="text-sm text-green-700">{{ $lot->contract->contract_date->format('d.m.Y') }}</p>
                            </div>

                            <div class="p-6 space-y-4">
                                <div class="grid grid-cols-3 gap-2 text-center">
                                    <div class="p-3 bg-blue-50 rounded">
                                        <p class="text-xs text-gray-600">Шартнома</p>
                                        <p class="text-sm font-bold text-blue-700">
                                            {{ number_format($lot->contract->contract_amount, 0, '.', ' ') }}</p>
                                    </div>
                                    <div class="p-3 bg-green-50 rounded">
                                        <p class="text-xs text-gray-600">Тўланган</p>
                                        <p class="text-sm font-bold text-green-700">
                                            {{ number_format($lot->contract->paid_amount, 0, '.', ' ') }}</p>
                                    </div>
                                    <div class="p-3 bg-orange-50 rounded">
                                        <p class="text-xs text-gray-600">Қолган</p>
                                        <p class="text-sm font-bold text-orange-700">
                                            {{ number_format($lot->contract->remaining_amount, 0, '.', ' ') }}</p>
                                    </div>
                                </div>

                                <div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full"
                                            style="width: {{ min($lot->contract->payment_percentage, 100) }}%"></div>
                                    </div>
                                    <p class="text-xs text-center mt-1">
                                        {{ number_format($lot->contract->payment_percentage, 1) }}%</p>
                                </div>

                                <a href="{{ route('contracts.show', $lot->contract) }}"
                                    class="block w-full text-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded text-sm font-medium">
                                    Батафсил кўриш →
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- Contract Creation Form - Show if no contract exists --}}
                        @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
                            <div class="bg-white border border-yellow-400 shadow-sm">
                                <div class="px-6 py-4 bg-yellow-50 border-b border-yellow-300">
                                    <h3 class="font-bold text-yellow-900">Шартнома яратиш</h3>
                                    <p class="text-xs text-yellow-700 mt-1">Тизимда шартнома мавжуд эмас</p>
                                </div>

                                <form action="{{ route('contracts.store') }}" method="POST" class="p-6 space-y-4">
                                    @csrf
                                    <input type="hidden" name="lot_id" value="{{ $lot->id }}">

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Шартнома рақами <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="contract_number" required
                                            value="{{ old('contract_number', $lot->contract_number) }}"
                                            class="w-full border rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Шартнома санаси <span
                                                class="text-red-500">*</span></label>
                                        <input type="date" name="contract_date" required
                                            value="{{ old('contract_date', $lot->contract_date ? $lot->contract_date->format('Y-m-d') : '') }}"
                                            class="w-full border rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Шартнома суммаси (сўм) <span
                                                class="text-red-500">*</span></label>
                                        <input type="number" step="0.01" name="contract_amount" required
                                            value="{{ old('contract_amount', $lot->sold_price) }}"
                                            class="w-full border rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Харидор номи <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="buyer_name" required
                                            value="{{ old('buyer_name', $lot->winner_name) }}"
                                            class="w-full border rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Харидор телефони</label>
                                        <input type="text" name="buyer_phone"
                                            value="{{ old('buyer_phone', $lot->winner_phone) }}"
                                            class="w-full border rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Харидор STIR</label>
                                        <input type="text" name="buyer_inn" value="{{ old('buyer_inn') }}"
                                            class="w-full border rounded px-3 py-2 text-sm">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Тўлов тури <span
                                                class="text-red-500">*</span></label>
                                        <select name="payment_type" required
                                            class="w-full border rounded px-3 py-2 text-sm">
                                            <option value="">Танланг...</option>
                                            <option value="muddatli"
                                                {{ old('payment_type', $lot->payment_type) === 'muddatli' ? 'selected' : '' }}>
                                                Бўлиб тўлаш</option>
                                            <option value="muddatsiz"
                                                {{ old('payment_type', $lot->payment_type) === 'muddatsiz' ? 'selected' : '' }}>
                                                Бир йўла</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Ҳолат <span
                                                class="text-red-500">*</span></label>
                                        <select name="status" required class="w-full border rounded px-3 py-2 text-sm">
                                            <option value="">Танланг...</option>
                                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}
                                                selected>Фаол</option>
                                            <option value="completed"
                                                {{ old('status') === 'completed' ? 'selected' : '' }}>Тўланган</option>
                                            <option value="cancelled"
                                                {{ old('status') === 'cancelled' ? 'selected' : '' }}>Бекор қилинган
                                            </option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Тўланган сумма (сўм)</label>
                                        <input type="number" step="0.01" name="paid_amount"
                                            value="{{ old('paid_amount', 0) }}"
                                            class="w-full border rounded px-3 py-2 text-sm" placeholder="0.00">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium mb-1">Изоҳ</label>
                                        <textarea name="note" rows="2" class="w-full border rounded px-3 py-2 text-sm">{{ old('note') }}</textarea>
                                    </div>

                                    <button type="submit"
                                        class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium">
                                        Шартнома яратиш
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-yellow-50 border border-yellow-300 rounded p-4">
                                <p class="text-sm text-yellow-800">
                                    <strong>Эслатма:</strong> Шартнома яратилмаган.
                                </p>
                            </div>
                        @endif
                    @endif

                    {{-- LOCATION MAP --}}
                    @if ($lot->map_embed_url)
                        <div class="bg-white border border-gray-300 shadow-sm">
                            <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                                <h3 class="text-sm font-bold text-gray-900">Локация</h3>
                            </div>
                            <div class="h-64 bg-gray-200">
                                <iframe width="100%" height="100%" frameborder="0" scrolling="no"
                                    src="{{ $lot->map_embed_url }}&zoom=15" class="w-full h-full">
                                </iframe>
                            </div>
                            @if ($lot->location_url)
                                <div class="p-4">
                                    <a href="{{ $lot->location_url }}" target="_blank"
                                        class="block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 border border-gray-300 text-sm">
                                        Google Maps-да очиш
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- ACTIONS --}}
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="p-4 space-y-2">
                            @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
                                @if (!$lot->contract && $lot->contract_signed)
                                    <a href="{{ route('contracts.create', ['lot_id' => $lot->id]) }}"
                                        class="block w-full text-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium">
                                        Шартнома яратиш
                                    </a>
                                @endif
                                <a href="{{ route('lots.edit', $lot) }}"
                                    class="block w-full text-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium">
                                    Таҳрирлаш
                                </a>
                            @endif
                            <button onclick="window.print()"
                                class="block w-full text-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 text-sm font-medium">
                                Чоп этиш
                            </button>
                        </div>
                    </div>

                    {{-- SYSTEM INFO --}}
                    <div class="bg-white border border-gray-300 shadow-sm">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                            <h3 class="text-sm font-bold text-gray-900">Тизим маълумотлари</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2 text-xs text-gray-600">
                                <div class="flex justify-between">
                                    <span>Яратилган:</span>
                                    <span
                                        class="font-medium text-gray-900">{{ $lot->created_at->format('d.m.Y H:i') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Янгиланган:</span>
                                    <span
                                        class="font-medium text-gray-900">{{ $lot->updated_at->format('d.m.Y H:i') }}</span>
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

    {{-- ============================================ --}}
    {{-- MODAL 1: ADD SCHEDULE ITEM                  --}}
    {{-- ============================================ --}}
    <div id="addScheduleModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-bold">График қўшиш</h3>
                <button onclick="closeAddScheduleModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('contracts.add-schedule-item', $lot->contract ?? 0) }}" method="POST"
                class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-2">Тўлов санаси <span class="text-red-500">*</span></label>
                    <input type="date" name="planned_date" required class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Тўлов суммаси (сўм) <span
                            class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="planned_amount" required
                        class="w-full border rounded px-3 py-2" placeholder="0.00">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Муддат (deadline)</label>
                    <input type="date" name="deadline_date" class="w-full border rounded px-3 py-2">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Қўшиш
                    </button>
                    <button type="button" onclick="closeAddScheduleModal()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
                        Бекор
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- MODAL 2: RECORD PAYMENT                     --}}
    {{-- ============================================ --}}
    <div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-bold">Тўлов қўшиш</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="paymentForm" method="POST" class="p-6 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium mb-2">Тўлов санаси <span class="text-red-500">*</span></label>
                    <input type="date" name="actual_date" id="paymentDate" required
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Тўланган сумма (сўм) <span
                            class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="actual_amount" id="paymentAmount" required
                        class="w-full border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Изоҳ</label>
                    <textarea name="note" rows="2" class="w-full border rounded px-3 py-2"></textarea>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Сақлаш
                    </button>
                    <button type="button" onclick="closePaymentModal()"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded">
                        Бекор
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ========================================== --}}
    {{-- JAVASCRIPT                                --}}
    {{-- ========================================== --}}
    <script>
        // Gallery Class
        class LotGallery {
            constructor(images) {
                this.images = images || [];
                this.currentIndex = 0;
                if (this.images.length > 0) this.init();
            }

            init() {
                this.updateImage();
                if (this.images.length > 1) {
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'ArrowLeft') this.previousImage();
                        if (e.key === 'ArrowRight') this.nextImage();
                    });
                }
            }

            showImage(index) {
                if (index < 0 || index >= this.images.length) return;
                this.currentIndex = index;
                this.updateImage();
            }

            previousImage() {
                this.currentIndex = (this.currentIndex - 1 + this.images.length) % this.images.length;
                this.updateImage();
            }

            nextImage() {
                this.currentIndex = (this.currentIndex + 1) % this.images.length;
                this.updateImage();
            }

            updateImage() {
                const mainImage = document.getElementById('mainImage');
                if (mainImage && this.images.length > 0) {
                    mainImage.src = this.images[this.currentIndex];
                }
                document.querySelectorAll('[id^="thumb-"]').forEach((thumb, index) => {
                    thumb.classList.toggle('border-gray-700', index === this.currentIndex);
                    thumb.classList.toggle('border-gray-300', index !== this.currentIndex);
                });
            }
        }

        // Interactions Class
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
                    const data = await response.json();
                    const likeCount = document.getElementById('likeCount');
                    const likeIcon = document.getElementById('likeIcon');
                    if (likeCount) likeCount.textContent = data.count || 0;
                    if (likeIcon) {
                        likeIcon.setAttribute('fill', data.liked ? 'currentColor' : 'none');
                        likeIcon.classList.toggle('fill-red-600', data.liked);
                        likeIcon.classList.toggle('text-red-600', data.liked);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        }

        // Modal Functions
        function openAddScheduleModal() {
            document.getElementById('addScheduleModal').classList.remove('hidden');
        }

        function closeAddScheduleModal() {
            document.getElementById('addScheduleModal').classList.add('hidden');
        }

        function openPaymentModal(scheduleId, plannedDate, plannedAmount) {
            const form = document.getElementById('paymentForm');
            form.action = `/payment-schedules/${scheduleId}`;
            document.getElementById('paymentDate').value = plannedDate;
            document.getElementById('paymentAmount').value = plannedAmount;
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            const images = @json($lot->images && $lot->images->count() > 0 ? $lot->images->map(fn($img) => $img->url)->values() : []);
            window.lotGallery = new LotGallery(images);
            window.lotInteractions = new LotInteractions({{ $lot->id }}, '{{ csrf_token() }}');
        });

        // Global Functions
        function showImage(index) {
            if (window.lotGallery) window.lotGallery.showImage(index);
        }

        function previousImage() {
            if (window.lotGallery) window.lotGallery.previousImage();
        }

        function nextImage() {
            if (window.lotGallery) window.lotGallery.nextImage();
        }

        function toggleLike() {
            if (window.lotInteractions) window.lotInteractions.toggleLike();
        }
    </script>

    <style>
        @media print {

            button,
            .no-print {
                display: none !important;
            }

            body {
                background: white;
            }
        }
    </style>
@endsection
