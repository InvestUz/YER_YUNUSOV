{{-- ============================================
     FILE 1: resources/views/lots/partials/flash-messages.blade.php
     PURPOSE: Display success/error messages with animations
     ============================================ --}}

@if(session('success'))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="bg-green-50 border-l-4 border-green-500 rounded-r-lg p-4 shadow-sm animate-slideDown" role="alert">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-semibold text-green-900">Муваффақият</p>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-green-500 hover:text-green-700 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 shadow-sm animate-slideDown" role="alert">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-semibold text-red-900">Хатолик</p>
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
</div>
@endif

@if($errors->any())
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
    <div class="bg-red-50 border-l-4 border-red-500 rounded-r-lg p-4 shadow-sm animate-slideDown" role="alert">
        <div class="flex items-start justify-between">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-red-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="font-semibold text-red-900 mb-2">Хатоликлар топилди</p>
                    <ul class="list-disc list-inside space-y-1 text-sm text-red-800">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-red-500 hover:text-red-700 transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    </div>
</div>
@endif


{{-- ============================================
     FILE 2: resources/views/lots/partials/breadcrumb.blade.php
     PURPOSE: Navigation breadcrumb with icons
     ============================================ --}}

<div class="bg-white shadow-sm border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <nav class="flex items-center space-x-2 text-sm" aria-label="Breadcrumb">
            {{-- Home Link --}}
            <a href="{{ route('lots.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Асосий
            </a>

            {{-- Separator --}}
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>

            {{-- Lots Link --}}
            <a href="{{ route('lots.index') }}" class="text-gray-600 hover:text-blue-600 transition-colors">
                Лотлар
            </a>

            {{-- Separator --}}
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>

            {{-- Current Page --}}
            <span class="text-gray-900 font-semibold">{{ $lot->lot_number }}</span>
        </nav>
    </div>
</div>


{{-- ============================================
     FILE 3: resources/views/lots/partials/page-header.blade.php
     PURPOSE: Page header with lot info and quick actions
     ============================================ --}}

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        {{-- Left Side: Lot Information --}}
        <div class="flex-1">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">
                Уникал № {{ $lot->unique_number }}
            </h1>

            <div class="flex flex-wrap items-center gap-4 text-sm">
                {{-- District Info --}}
                @if($lot->tuman)
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-gray-600">Туман:</span>
                    <strong class="text-gray-900">{{ $lot->tuman->name_uz }}</strong>
                </div>
                @endif

                {{-- Mahalla Info --}}
                @if($lot->mahalla)
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="text-gray-600">Мфй:</span>
                    <strong class="text-gray-900">{{ $lot->mahalla->name }}</strong>
                </div>
                @endif

                {{-- Status Badge --}}
                @if($lot->lot_status)
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                        {{ $lot->lot_status === 'active' ? 'bg-green-100 text-green-800' :
                           ($lot->lot_status === 'sold' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        <span class="w-2 h-2 rounded-full mr-1.5
                            {{ $lot->lot_status === 'active' ? 'bg-green-500' :
                               ($lot->lot_status === 'sold' ? 'bg-blue-500' : 'bg-gray-500') }}"></span>
                        {{ ucfirst($lot->lot_status) }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Right Side: Quick Actions --}}
        <div class="flex items-center gap-2">
            @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
            <a href="{{ route('lots.edit', $lot) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Таҳрирлаш
            </a>
            @endif

            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-lg text-sm font-medium transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Чоп этиш
            </button>
        </div>
    </div>
</div>


{{-- ============================================
     FILE 4: resources/views/lots/partials/image-gallery.blade.php
     PURPOSE: Image gallery with navigation and interactions
     ============================================ --}}

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    {{-- Main Image Display --}}
    <div class="relative bg-gradient-to-br from-gray-100 to-gray-200" style="height: 500px;">
        @if($lot->images && $lot->images->count() > 0)
            {{-- Main Image --}}
            <img
                id="mainImage"
                src="{{ $lot->primary_image_url }}"
                alt="Лот {{ $lot->lot_number }}"
                class="w-full h-full object-contain bg-gray-50 transition-opacity duration-300">

            {{-- Navigation Arrows (only if multiple images) --}}
            @if($lot->images->count() > 1)
                {{-- Previous Button --}}
                <button
                    onclick="previousImage()"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/95 hover:bg-white border border-gray-300 flex items-center justify-center shadow-lg rounded-full transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Next Button --}}
                <button
                    onclick="nextImage()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/95 hover:bg-white border border-gray-300 flex items-center justify-center shadow-lg rounded-full transition-all hover:scale-110 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

                {{-- Image Counter --}}
                <div class="absolute top-4 right-4 bg-black/70 text-white px-3 py-1 rounded-full text-sm font-medium">
                    <span id="currentImageIndex">1</span> / {{ $lot->images->count() }}
                </div>
            @endif
        @else
            {{-- No Image Placeholder --}}
            <div class="w-full h-full flex items-center justify-center bg-gray-100">
                <div class="text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="text-gray-600 font-medium">Расм топилмади</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Thumbnail Strip (only if multiple images) --}}
    @if($lot->images && $lot->images->count() > 1)
    <div class="flex gap-2 p-3 bg-gray-50 border-t border-gray-200 overflow-x-auto">
        @foreach($lot->images as $index => $image)
        <div
            class="flex-shrink-0 w-20 h-20 border-2 cursor-pointer rounded-lg overflow-hidden transition-all
                   {{ $index === 0 ? 'border-blue-600 ring-2 ring-blue-200' : 'border-gray-300' }}
                   hover:border-blue-500 hover:scale-105"
            onclick="showImage({{ $index }})"
            id="thumb-{{ $index }}">
            <img
                src="{{ $image->url }}"
                alt="Thumbnail {{ $index + 1 }}"
                class="w-full h-full object-cover">
        </div>
        @endforeach
    </div>
    @endif

    {{-- Stats Footer --}}
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-200 text-sm text-gray-600">
        <div class="flex items-center gap-4 sm:gap-6">
            {{-- View Count --}}
            <div class="flex items-center gap-1.5">
                <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                <span class="font-medium text-gray-900">{{ $totalViewsCount ?? 0 }}</span>
            </div>

            {{-- Like Button --}}
            <button
                onclick="toggleLike()"
                class="flex items-center gap-1.5 cursor-pointer hover:text-red-600 transition-colors group">
                <svg
                    id="likeIcon"
                    class="w-5 h-5 transition-all group-hover:scale-110
                           {{ isset($hasLiked) && $hasLiked ? 'fill-red-600 text-red-600' : 'text-gray-500' }}"
                    fill="{{ isset($hasLiked) && $hasLiked ? 'currentColor' : 'none' }}"
                    stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span id="likeCount" class="font-medium text-gray-900">{{ $likesCount ?? 0 }}</span>
            </button>
        </div>

        {{-- Last Updated --}}
        <div class="text-xs text-gray-500 hidden sm:block">
            Янгиланди: {{ $lot->updated_at->format('d.m.Y H:i') }}
        </div>
    </div>
</div>


{{-- ============================================
     FILE 5: resources/views/lots/partials/lot-information.blade.php
     PURPOSE: Lot details and auction information tables
     ============================================ --}}

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    {{-- Land Information Section --}}
    <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            {{ $lot->unique_number ?? '-' }} ер участкасининг маълумотлари
        </h2>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-200">
                {{-- District --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium w-1/3">Туман</td>
                    <td class="py-3 px-4 text-gray-900">{{ optional($lot->tuman)->name_uz ?? '-' }}</td>
                </tr>

                {{-- Address --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium">Ер манзили</td>
                    <td class="py-3 px-4 text-gray-900">{{ $lot->address ?? '-' }}</td>
                </tr>

                {{-- Land Area --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium">Ер майдони</td>
                    <td class="py-3 px-4 text-gray-900 font-semibold">
                        {{ $lot->land_area ? number_format($lot->land_area, 2) . ' га' : '-' }}
                    </td>
                </tr>

                {{-- Zone --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium">Зона</td>
                    <td class="py-3 px-4 text-gray-900">{{ $lot->zone ?? '-' }}</td>
                </tr>

                {{-- Master Plan Zone --}}
                <tr class="hover:bg-blue-50 transition-colors">
                    <td class="py-3 px-4 text-gray-600 font-medium">Бош режа бўйича жойлашув зонаси</td>
                    <td class="py-3 px-4 text-gray-900">{{ $lot->master_plan_zone ?? '-' }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Auction Information Section --}}
    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200 mt-6">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Аукцион маълумотлари
        </h2>
    </div>


    <table class="w-full text-sm">
           <tbody class="divide-y divide-gray-200">
            {{-- Lot Number --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium w-1/3">Лот рақами</td>
                <td class="py-3 px-4 text-gray-900">{{ $lot->lot_number ?? '-' }}</td>
            </tr>

            {{-- Initial Price (Highlighted) --}}
            <tr class="bg-blue-50 hover:bg-blue-100 transition-colors">
                <td class="py-3 px-4 text-gray-700 font-bold">Бошланғич нархи</td>
                <td class="py-3 px-4 text-blue-700 font-bold text-base">
                    {{ $lot->initial_price ? number_format($lot->initial_price, 0, '.', ' ') . ' UZS' : '-' }}
                </td>
            </tr>

            {{-- Auction Date --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Аукцион санаси</td>
                <td class="py-3 px-4 text-gray-900">
                    {{ $lot->auction_date ? $lot->auction_date->format('d.m.Y') : '-' }}
                </td>
            </tr>

            {{-- Sold Price (Highlighted) --}}
            <tr class="bg-green-50 hover:bg-green-100 transition-colors">
                <td class="py-3 px-4 text-gray-700 font-bold">Сотилган нархи</td>
                <td class="py-3 px-4 text-green-700 font-bold text-base">
                    {{ $lot->sold_price ? number_format($lot->sold_price, 0, '.', ' ') . ' UZS' : '-' }}
                </td>
            </tr>

            {{-- Winner Name --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Аукцион ғолиби</td>
                <td class="py-3 px-4 text-gray-900 font-semibold">{{ $lot->winner_name ?? '-' }}</td>
            </tr>

            {{-- Winner Phone --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Телефон рақами</td>
                <td class="py-3 px-4 text-gray-900">{{ $lot->winner_phone ?? '-' }}</td>
            </tr>

            {{-- Contract Status --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Шартнома холати</td>
                <td class="py-3 px-4">
                    @if($lot->contract_signed)
                        <span class="inline-flex items-center px-3 py-1 bg-green-100 text-green-800 border border-green-300 text-xs font-medium rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Тузилган
                        </span>
                        @if($lot->contract_date)
                            <div class="text-sm text-gray-700 mt-1">Сана: {{ $lot->contract_date->format('d.m.Y') }}</div>
                        @endif
                        @if($lot->contract_number)
                            <div class="text-sm text-gray-700">Рақам: {{ $lot->contract_number }}</div>
                        @endif
                    @else
                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 border border-gray-300 text-xs font-medium rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                            </svg>
                            Тузилмаган
                        </span>
                    @endif
                </td>
            </tr>

            {{-- Payment Type --}}
            <tr class="hover:bg-green-50 transition-colors">
                <td class="py-3 px-4 text-gray-600 font-medium">Тўлов тури</td>
                <td class="py-3 px-4 text-gray-900">
                    @if($lot->payment_type === 'muddatli')
                        <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 border border-blue-300 text-xs font-medium rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                            бўлиб тўлаш
                        </span>
                    @elseif($lot->payment_type === 'muddatsiz')
                        <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-800 border border-gray-300 text-xs font-medium rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                            </svg>
                            бир йўла
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>

