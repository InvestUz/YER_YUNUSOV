@extends('layouts.app')

@section('title', 'Лот ' . $lot->lot_number)

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Breadcrumb - Government Style -->
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

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Page Header -->
        <div class="bg-white border border-gray-300 shadow-sm mb-6 p-6">
            <h1 class="text-xl font-bold text-gray-900 mb-2">
                "{{ $lot->address }}" {{ number_format($lot->land_area, 2) }} га. бўлган савдо дўкони
            </h1>
            <div class="flex items-center gap-4 text-sm text-gray-600">
                <span>Лот №: <strong class="text-gray-900">{{ $lot->lot_number }}</strong></span>
                @if($lot->unique_number)
                <span class="border-l pl-4">Кадастр №: <strong class="text-gray-900">{{ $lot->unique_number }}</strong></span>
                @endif
                @if($lot->lot_status)
                <span class="border-l pl-4">Ҳолат: <strong class="text-gray-900">{{ $lot->lot_status }}</strong></span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Image/Map Section -->
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="relative bg-gray-200" style="height: 450px;" id="mainDisplay">
                        @php
                            $hasImages = $lot->images->count() > 0;
                            $showMap = !$hasImages && $lot->map_embed_url;
                        @endphp

                        @if($showMap)
                            <!-- Show Map if no images -->
                            <iframe
                                id="mapFrame"
                                width="100%"
                                height="100%"
                                frameborder="0"
                                scrolling="no"
                                src="{{ $lot->map_embed_url }}"
                                class="w-full h-full">
                            </iframe>
                        @else
                            <!-- Show Image -->
                            <img id="mainImage" 
                                 src="{{ $lot->primary_image_url }}" 
                                 alt="Лот {{ $lot->lot_number }}"
                                 class="w-full h-full object-contain bg-gray-100">
                        @endif

                        <!-- Navigation Arrows -->
                        @if($lot->all_images->count() > 1)
                        <button onclick="previousImage()" class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/95 hover:bg-white border border-gray-300 flex items-center justify-center shadow-md transition">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button onclick="nextImage()" class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 bg-white/95 hover:bg-white border border-gray-300 flex items-center justify-center shadow-md transition">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                        @endif

                        <!-- View Mode Toggle -->
                        @if($lot->map_embed_url)
                        <div class="absolute top-3 right-3 flex gap-2">
                            <button onclick="showImages()" id="imageBtn" class="px-3 py-2 bg-white/95 hover:bg-white border border-gray-300 text-xs font-medium text-gray-700 shadow-md transition {{ $showMap ? '' : 'bg-gray-700 text-white border-gray-700' }}">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Расм
                            </button>
                            <button onclick="showMap()" id="mapBtn" class="px-3 py-2 bg-white/95 hover:bg-white border border-gray-300 text-xs font-medium text-gray-700 shadow-md transition {{ $showMap ? 'bg-gray-700 text-white border-gray-700' : '' }}">
                                <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                                Харита
                            </button>
                        </div>
                        @endif
                    </div>

                    <!-- Thumbnails -->
                    @if($lot->all_images->count() > 1)
                    <div class="flex gap-2 p-3 bg-gray-50 border-t border-gray-300 overflow-x-auto" id="thumbnailContainer">
                        @foreach($lot->all_images as $index => $image)
                        <div class="flex-shrink-0 w-20 h-20 border-2 cursor-pointer {{ $index === 0 ? 'border-gray-700' : 'border-gray-300' }} hover:border-gray-700 transition" 
                             onclick="showImage({{ $index }})"
                             id="thumb-{{ $index }}">
                            <img src="{{ $image->url ?? $image }}" 
                                 alt="Thumbnail {{ $index + 1 }}"
                                 class="w-full h-full object-cover">
                        </div>
                        @endforeach
                    </div>
                    @endif
{{-- File: resources/views/lots/show.blade.php - Replace Stats Bar section --}}

<!-- Enhanced Stats Bar -->
<div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-300 text-sm text-gray-600">
    <div class="flex items-center gap-6">
        <!-- Views Counter with Tooltip -->
        <div class="flex items-center gap-1 group relative cursor-help">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span id="viewsCount">{{ $totalViewsCount }}</span>
            
            <!-- Tooltip -->
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-2 px-3 whitespace-nowrap z-10">
                <div class="space-y-1">
                    <div>Жами: {{ $totalViewsCount }}</div>
                    <div>Уникал: {{ $uniqueViewsCount }}</div>
                </div>
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
            </div>
        </div>

        <!-- Messages Counter -->
        <div class="flex items-center gap-1 group relative cursor-pointer" onclick="showMessages()">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span id="messagesCount">{{ $messagesCount }}</span>
            @if($unreadMessagesCount > 0)
            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
                {{ $unreadMessagesCount }}
            </span>
            @endif

            <!-- Tooltip -->
            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-gray-900 text-white text-xs rounded py-2 px-3 whitespace-nowrap z-10">
                <div class="space-y-1">
                    <div>Жами: {{ $messagesCount }}</div>
                    @if($unreadMessagesCount > 0)
                    <div>Ўқилмаган: {{ $unreadMessagesCount }}</div>
                    @endif
                </div>
                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
            </div>
        </div>

        <!-- Likes Counter -->
        <div class="flex items-center gap-1 cursor-pointer hover:text-red-600 transition" onclick="toggleLike()">
            <svg class="w-4 h-4 {{ $hasLiked ? 'fill-red-600 text-red-600' : '' }}" 
                 fill="{{ $hasLiked ? 'currentColor' : 'none' }}" 
                 stroke="currentColor" 
                 viewBox="0 0 24 24" 
                 id="likeIcon">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
            </svg>
            <span id="likeCount">{{ $likesCount }}</span>
        </div>
    </div>
    <div class="text-xs text-gray-500">
        Янгиланди: {{ $lot->updated_at->format('d.m.Y H:i') }}
    </div>
</div>

{{-- Message Modal --}}
<div id="messageModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-300">
            <h3 class="text-lg font-bold text-gray-900">Хабар юбориш</h3>
            <button onclick="closeMessageModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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
// Enhanced like toggle with proper state management
function toggleLike() {
    fetch(`/lots/{{ $lot->id }}/toggle-like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        const likeCount = document.getElementById('likeCount');
        const likeIcon = document.getElementById('likeIcon');
        
        likeCount.textContent = data.count;
        
        if (data.liked) {
            likeIcon.setAttribute('fill', 'currentColor');
            likeIcon.classList.add('fill-red-600', 'text-red-600');
        } else {
            likeIcon.setAttribute('fill', 'none');
            likeIcon.classList.remove('fill-red-600', 'text-red-600');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Message modal functions
function showMessages() {
    document.getElementById('messageModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Handle message form submission
document.getElementById('messageForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    fetch(`/lots/{{ $lot->id }}/send-message`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeMessageModal();
            this.reset();
            
            // Update message count
            const messagesCount = document.getElementById('messagesCount');
            messagesCount.textContent = parseInt(messagesCount.textContent) + 1;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Хабар юборишда хатолик юз берди');
    });
});

// Close modal on outside click
document.getElementById('messageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMessageModal();
    }
});
</script>
                </div>

                {{-- Add this section to the sidebar in lots/show.blade.php, after the Actions section --}}

<!-- Analytics Links (Admin Only) -->
@if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
<div class="bg-white border border-gray-300 shadow-sm">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
        <h3 class="text-sm font-bold text-gray-900">Аналитика</h3>
    </div>
    <div class="p-4 space-y-2">
        <a href="{{ route('analytics.lot.views', $lot) }}" 
           class="block w-full text-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 transition text-sm font-medium">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Кўришлар батафсил
        </a>
        
        <a href="{{ route('analytics.lot.messages', $lot) }}" 
           class="block w-full text-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 transition text-sm font-medium">
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Хабарлар
            @if($unreadMessagesCount > 0)
            <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full ml-2">
                {{ $unreadMessagesCount }}
            </span>
            @endif
        </a>
    </div>
</div>
@endif

                <!-- Installment Badge -->
                @if($lot->payment_type === 'muddatli')
                <div class="bg-blue-50 border-l-4 border-blue-600 p-4">
                    <p class="text-sm font-semibold text-blue-900">
                        <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Муддатли тўлов имкониятли - 60 ойгача бўлиб тўлаш
                    </p>
                </div>
                @endif

                <!-- Main Information Table -->
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                        <h2 class="text-base font-bold text-gray-900">Асосий маълумотлар</h2>
                    </div>
                    <div class="p-6">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="py-3 text-gray-600 w-1/2">Лот рақами</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ $lot->lot_number }}</td>
                                </tr>
                                @if($lot->unique_number)
                                <tr>
                                    <td class="py-3 text-gray-600">Кадастр рақами</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ $lot->unique_number }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="py-3 text-gray-600">Манзил</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ $lot->address }}</td>
                                </tr>
                                @if($lot->tuman)
                                <tr>
                                    <td class="py-3 text-gray-600">Туман</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ $lot->tuman->name_uz }}</td>
                                </tr>
                                @endif
                                @if($lot->zone)
                                <tr>
                                    <td class="py-3 text-gray-600">Зона</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ $lot->zone }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="py-3 text-gray-600">Майдон</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ number_format($lot->land_area, 4) }} га</td>
                                </tr>
                                @if($lot->auction_date)
                                <tr>
                                    <td class="py-3 text-gray-600">Савдо санаси</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ $lot->auction_date->format('d.m.Y') }}</td>
                                </tr>
                                @endif
                                @if($lot->auction_type)
                                <tr>
                                    <td class="py-3 text-gray-600">Савдо тури</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">
                                        {{ $lot->auction_type === 'ochiq' ? 'Очиқ аукцион' : 'Ёпиқ танлов' }}
                                    </td>
                                </tr>
                                @endif
                                <tr class="bg-gray-50">
                                    <td class="py-3 text-gray-600 font-semibold">Бошланғич нархи</td>
                                    <td class="py-3 text-gray-900 font-bold text-right text-base">
                                        {{ number_format($lot->initial_price, 0, '.', ' ') }} UZS
                                    </td>
                                </tr>
                                @if($lot->sold_price)
                                <tr class="bg-green-50">
                                    <td class="py-3 text-gray-600 font-semibold">Сотилган нархи</td>
                                    <td class="py-3 text-green-700 font-bold text-right text-base">
                                        {{ number_format($lot->sold_price, 0, '.', ' ') }} UZS
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td class="py-3 text-gray-600">Закалат пули (5%)</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">
                                        {{ number_format($lot->initial_price * 0.05, 0, '.', ' ') }} UZS
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-3 text-gray-600">Биринчи қадам (5%)</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">
                                        {{ number_format($lot->initial_price * 0.05, 0, '.', ' ') }} UZS
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Winner Information -->
                @if($lot->winner_name)
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                        <h2 class="text-base font-bold text-gray-900">Ғолиб маълумотлари</h2>
                    </div>
                    <div class="p-6">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                                <tr>
                                    <td class="py-3 text-gray-600 w-1/2">Ғолиб</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ $lot->winner_name }}</td>
                                </tr>
                                @if($lot->winner_phone)
                                <tr>
                                    <td class="py-3 text-gray-600">Телефон рақами</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ $lot->winner_phone }}</td>
                                </tr>
                                @endif
                                @if($lot->winner_type)
                                <tr>
                                    <td class="py-3 text-gray-600">Турi</td>
                                    <td class="py-3 text-gray-900 font-medium text-right">{{ $lot->winner_type }}</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Additional Information -->
                @if($lot->basis || $lot->notes)
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                        <h2 class="text-base font-bold text-gray-900">Қўшимча маълумотлар</h2>
                    </div>
                    <div class="p-6 text-sm text-gray-700 leading-relaxed">
                        @if($lot->basis)
                        <div class="mb-4">
                            <p class="font-semibold text-gray-900 mb-2">Асос:</p>
                            <p>{{ $lot->basis }}</p>
                        </div>
                        @endif
                        @if($lot->notes)
                        <div>
                            <p class="font-semibold text-gray-900 mb-2">Изоҳ:</p>
                            <p class="whitespace-pre-line">{{ $lot->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Payment Schedule -->
                @if($lot->payment_type === 'muddatli' && $lot->paymentSchedules && $lot->paymentSchedules->count() > 0)
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
                                @foreach($lot->paymentSchedules->sortBy('payment_date') as $index => $schedule)
                                <tr class="hover:bg-gray-50">
                                    <td class="py-3 px-4 font-medium text-gray-900">{{ $index + 1 }}</td>
                                    <td class="py-3 px-4 text-gray-700">{{ $schedule->payment_date->format('d.m.Y') }}</td>
                                    <td class="py-3 px-4 text-right font-medium text-gray-900">
                                        {{ number_format($schedule->planned_amount, 0, '.', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-medium {{ $schedule->actual_amount > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                        {{ number_format($schedule->actual_amount, 0, '.', ' ') }}
                                    </td>
                                    <td class="py-3 px-4 text-center">
                                        @if($schedule->actual_amount >= $schedule->planned_amount)
                                        <span class="inline-block px-3 py-1 bg-green-100 text-green-800 border border-green-300 text-xs font-medium">
                                            Тўланган
                                        </span>
                                        @elseif($schedule->payment_date < now())
                                        <span class="inline-block px-3 py-1 bg-red-100 text-red-800 border border-red-300 text-xs font-medium">
                                            Муддати ўтган
                                        </span>
                                        @else
                                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-800 border border-yellow-300 text-xs font-medium">
                                            Кутилмоқда
                                        </span>
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

            <!-- Right Column - Sidebar -->
            <div class="space-y-6">
                <!-- Countdown Timer -->
                @if($auctionCountdown)
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-700 text-white">
                        <h3 class="text-sm font-bold text-center">Савдо тугашига қолган вақт</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-4 gap-3">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-1">{{ str_pad($auctionCountdown->days, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-xs text-gray-600 uppercase">КУН</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-1">{{ str_pad($auctionCountdown->h, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-xs text-gray-600 uppercase">СОАТ</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-1">{{ str_pad($auctionCountdown->i, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-xs text-gray-600 uppercase">ДАҚ</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-1">{{ str_pad($auctionCountdown->s, 2, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-xs text-gray-600 uppercase">СОН</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Participation Button -->
                @if(!$lot->winner_name && $auctionCountdown)
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="p-6">
                        <h3 class="text-sm font-bold text-gray-900 mb-4">Аукционда иштирок этиш</h3>
                        <p class="text-xs text-gray-600 mb-4">
                            Савдода иштирок этиш учун рўйхатдан ўтган бўлишингиз ва ариза топширишингиз керак.
                        </p>
                        <button class="w-full bg-gray-900 hover:bg-gray-800 text-white font-semibold py-3 px-6 border border-gray-900 transition-colors duration-200">
                            Ариза бериш
                        </button>
                        <a href="#" class="block text-center text-gray-600 hover:text-gray-900 text-xs mt-3 underline">
                            Қандай ариза бериш керак?
                        </a>
                    </div>
                </div>
                @endif

                <!-- Contract Status -->
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                        <h3 class="text-sm font-bold text-gray-900">Шартнома ҳолати</h3>
                    </div>
                    <div class="p-6">
                        @if($lot->contract_signed)
                        <div class="flex items-start gap-3 p-4 bg-green-50 border border-green-300">
                            <svg class="w-5 h-5 text-green-700 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div class="flex-1">
                                <p class="font-semibold text-green-900 text-sm mb-2">Шартнома тузилган</p>
                                @if($lot->contract_number)
                                <p class="text-xs text-gray-700 mb-1">
                                    <span class="font-medium">Рақами:</span> {{ $lot->contract_number }}
                                </p>
                                @endif
                                @if($lot->contract_date)
                                <p class="text-xs text-gray-700">
                                    <span class="font-medium">Санаси:</span> {{ $lot->contract_date->format('d.m.Y') }}
                                </p>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="flex items-start gap-3 p-4 bg-gray-100 border border-gray-300">
                            <svg class="w-5 h-5 text-gray-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <p class="font-semibold text-gray-700 text-sm">Шартнома тузилмаган</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Payment Type -->
                @if($lot->payment_type)
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                        <h3 class="text-sm font-bold text-gray-900">Тўлов тури</h3>
                    </div>
                    <div class="p-6">
                        @if($lot->payment_type === 'muddatli')
                        <div class="flex items-center gap-3 p-4 bg-blue-50 border border-blue-300">
                            <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-semibold text-blue-900 text-sm">Муддатли тўлов</span>
                        </div>
                        @else
                        <div class="flex items-center gap-3 p-4 bg-gray-100 border border-gray-300">
                            <svg class="w-5 h-5 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-semibold text-gray-700 text-sm">Бир марталик тўлов</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Payment Progress -->
                @if($paymentStats['total_amount'] > 0 && $lot->sold_price)
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                        <h3 class="text-sm font-bold text-gray-900">Тўлов статистикаси</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="p-4 bg-green-50 border border-green-300">
                                <p class="text-xs text-gray-600 mb-1">Тўланган</p>
                                <p class="text-lg font-bold text-green-700">
                                    {{ number_format($paymentStats['paid_amount'] + $paymentStats['transferred_amount'], 0, '.', ' ') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">UZS</p>
                            </div>

                            <div class="p-4 bg-orange-50 border border-orange-300">
                                <p class="text-xs text-gray-600 mb-1">Қолган қарз</p>
                                <p class="text-lg font-bold text-orange-700">
                                    {{ number_format($paymentStats['remaining_amount'], 0, '.', ' ') }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">UZS</p>
                            </div>

                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-xs font-medium text-gray-700">Тўлов прогресси</span>
                                    <span class="text-xs font-bold text-gray-900">{{ number_format($paymentStats['payment_progress'], 1) }}%</span>
                                </div>
                                <div class="w-full bg-gray-300 h-2.5 border border-gray-400">
                                    <div class="bg-green-600 h-full transition-all" style="width: {{ min($paymentStats['payment_progress'], 100) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Location Information -->
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                        <h3 class="text-sm font-bold text-gray-900">Жойлашув маълумоти</h3>
                    </div>
                    <div class="p-6">
                        @if($lot->map_embed_url)
                        <div class="h-48 bg-gray-200 border border-gray-300 mb-4">
                            <iframe
                                width="100%"
                                height="100%"
                                frameborder="0"
                                scrolling="no"
                                src="{{ $lot->map_embed_url }}">
                            </iframe>
                        </div>
                        @endif

                        <div class="space-y-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Манзил:</p>
                                <p class="text-gray-900 font-medium">{{ $lot->address }}</p>
                            </div>
                            @if($lot->tuman)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Туман:</p>
                                <p class="text-gray-900 font-medium">{{ $lot->tuman->name_uz }}</p>
                            </div>
                            @endif
                            @if($lot->mahalla)
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Маҳалла:</p>
                                <p class="text-gray-900 font-medium">{{ $lot->mahalla->name }}</p>
                            </div>
                            @endif
                        </div>

                        @if($lot->location_url)
                        <a href="{{ $lot->location_url }}" target="_blank" class="mt-4 block w-full text-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 border border-gray-300 transition text-sm font-medium">
                            Google Maps-да очиш
                        </a>
                        @endif
                    </div>
                </div>

                <!-- Financial Metrics -->
                @if($financialMetrics['price_increase'] > 0 || $lot->investment_amount > 0)
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                        <h3 class="text-sm font-bold text-gray-900">Молиявий кўрсаткичлар</h3>
                    </div>
                    <div class="p-6">
                        <table class="w-full text-sm">
                            <tbody class="divide-y divide-gray-200">
                                @if($financialMetrics['price_per_hectare'] > 0)
                                <tr>
                                    <td class="py-2 text-gray-600">1 га нархи:</td>
                                    <td class="py-2 text-gray-900 font-medium text-right">
                                        {{ number_format($financialMetrics['price_per_hectare'], 0, '.', ' ') }} UZS
                                    </td>
                                </tr>
                                @endif
                                @if($financialMetrics['price_increase'] > 0)
                                <tr>
                                    <td class="py-2 text-gray-600">Нарх ўсиши:</td>
                                    <td class="py-2 text-green-600 font-medium text-right">
                                        +{{ number_format($financialMetrics['price_increase_percent'], 1) }}%
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2 text-gray-600">Фойда:</td>
                                    <td class="py-2 text-green-600 font-medium text-right">
                                        {{ number_format($financialMetrics['price_increase'], 0, '.', ' ') }} UZS
                                    </td>
                                </tr>
                                @endif
                                @if($lot->investment_amount > 0)
                                <tr>
                                    <td class="py-2 text-gray-600">Инвестиция:</td>
                                    <td class="py-2 text-gray-900 font-medium text-right">
                                        {{ number_format($lot->investment_amount, 0, '.', ' ') }} UZS
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- System Information -->
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
                            @if($lot->unique_number)
                            <div class="flex justify-between">
                                <span>ID:</span>
                                <span class="font-medium text-gray-900">{{ $lot->unique_number }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white border border-gray-300 shadow-sm">
                    <div class="p-4 space-y-2">
                        @if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
                        <a href="{{ route('lots.edit', $lot) }}" class="block w-full text-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white border border-gray-900 transition text-sm font-medium">
                            Таҳрирлаш
                        </a>
                        @endif
                        <button onclick="window.print()" class="block w-full text-center px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 border border-gray-300 transition text-sm font-medium">
                            Чоп этиш
                        </button>
                    </div>
                </div>

                <!-- Important Notice -->
                <div class="bg-yellow-50 border-l-4 border-yellow-600 p-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h4 class="text-sm font-semibold text-gray-900 mb-1">Эслатма:</h4>
                            <p class="text-xs text-gray-700 leading-relaxed">
                                Савдода иштирок этиш учун барча талаблар билан танишиб чиқинг ва белгиланган муддатда ҳужжатларни тақдим этинг.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribution Statistics -->
        @if($distributionStats['total_distributed'] > 0)
        <div class="mt-6 bg-white border border-gray-300 shadow-sm">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                <h2 class="text-base font-bold text-gray-900">Тақсимот статистикаси</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="p-4 bg-blue-50 border border-blue-300">
                        <p class="text-xs text-gray-600 mb-1">Маҳаллий бюджет</p>
                        <p class="text-lg font-bold text-blue-700">
                            {{ number_format($distributionStats['local_budget'] / 1000000, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">млн сўм</p>
                    </div>

                    <div class="p-4 bg-green-50 border border-green-300">
                        <p class="text-xs text-gray-600 mb-1">Ривожлантириш жамғармаси</p>
                        <p class="text-lg font-bold text-green-700">
                            {{ number_format($distributionStats['development_fund'] / 1000000, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">млн сўм</p>
                    </div>

                    <div class="p-4 bg-purple-50 border border-purple-300">
                        <p class="text-xs text-gray-600 mb-1">Янги Ўзбекистон</p>
                        <p class="text-lg font-bold text-purple-700">
                            {{ number_format($distributionStats['new_uzbekistan'] / 1000000, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">млн сўм</p>
                    </div>

                    <div class="p-4 bg-orange-50 border border-orange-300">
                        <p class="text-xs text-gray-600 mb-1">Туман ҳокимияти</p>
                        <p class="text-lg font-bold text-orange-700">
                            {{ number_format($distributionStats['district_authority'] / 1000000, 2) }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">млн сўм</p>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
let currentImageIndex = 0;
let displayMode = '{{ $lot->images->count() > 0 ? "image" : "map" }}'; // 'image' or 'map'
const images = @json($lot->all_images->map(function($img) { return is_object($img) ? $img->url : $img; })->values());
const hasMap = {{ $lot->map_embed_url ? 'true' : 'false' }};
const mapUrl = '{{ $lot->map_embed_url }}';

function showImage(index) {
    currentImageIndex = index;
    displayMode = 'image';
    updateDisplay();
}

function previousImage() {
    if (displayMode === 'image' && images.length > 0) {
        currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        updateDisplay();
    }
}

function nextImage() {
    if (displayMode === 'image' && images.length > 0) {
        currentImageIndex = (currentImageIndex + 1) % images.length;
        updateDisplay();
    }
}

function showImages() {
    displayMode = 'image';
    updateDisplay();
    updateModeButtons();
}

function showMap() {
    displayMode = 'map';
    updateDisplay();
    updateModeButtons();
}

function updateDisplay() {
    const mainDisplay = document.getElementById('mainDisplay');
    
    if (displayMode === 'map' && hasMap) {
        mainDisplay.innerHTML = `
            <iframe
                width="100%"
                height="100%"
                frameborder="0"
                scrolling="no"
                src="${mapUrl}"
                class="w-full h-full">
            </iframe>
        `;
    } else if (displayMode === 'image' && images.length > 0) {
        mainDisplay.innerHTML = `
            <img src="${images[currentImageIndex]}" 
                 alt="Лот изображение ${currentImageIndex + 1}"
                 class="w-full h-full object-contain bg-gray-100">
        `;
        updateThumbnails();
    }
}

function updateThumbnails() {
    const thumbnails = document.querySelectorAll('[id^="thumb-"]');
    thumbnails.forEach((thumb, index) => {
        if (index === currentImageIndex) {
            thumb.classList.remove('border-gray-300');
            thumb.classList.add('border-gray-700');
        } else {
            thumb.classList.remove('border-gray-700');
            thumb.classList.add('border-gray-300');
        }
    });
}

function updateModeButtons() {
    const imageBtn = document.getElementById('imageBtn');
    const mapBtn = document.getElementById('mapBtn');
    
    if (imageBtn && mapBtn) {
        if (displayMode === 'image') {
            imageBtn.classList.add('bg-gray-700', 'text-white', 'border-gray-700');
            imageBtn.classList.remove('bg-white/95', 'text-gray-700', 'border-gray-300');
            mapBtn.classList.remove('bg-gray-700', 'text-white', 'border-gray-700');
            mapBtn.classList.add('bg-white/95', 'text-gray-700', 'border-gray-300');
        } else {
            mapBtn.classList.add('bg-gray-700', 'text-white', 'border-gray-700');
            mapBtn.classList.remove('bg-white/95', 'text-gray-700', 'border-gray-300');
            imageBtn.classList.remove('bg-gray-700', 'text-white', 'border-gray-700');
            imageBtn.classList.add('bg-white/95', 'text-gray-700', 'border-gray-300');
        }
    }
}

function toggleLike() {
    fetch(`/lots/{{ $lot->id }}/toggle-like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        const likeCount = document.getElementById('likeCount');
        const likeIcon = document.getElementById('likeIcon');
        
        likeCount.textContent = data.count;
        
        if (data.liked) {
            likeIcon.innerHTML = '<path fill="currentColor" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>';
            likeIcon.classList.add('text-red-600');
        } else {
            likeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>';
            likeIcon.classList.remove('text-red-600');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (displayMode === 'image' && images.length > 1) {
        if (e.key === 'ArrowLeft') {
            previousImage();
        } else if (e.key === 'ArrowRight') {
            nextImage();
        }
    }
});

// Initialize display on load
document.addEventListener('DOMContentLoaded', function() {
    updateDisplay();
    updateModeButtons();
});
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

/* Remove colorful focus rings */
button:focus, a:focus {
    outline: 2px solid #374151;
    outline-offset: 2px;
}
</style>
@endsection