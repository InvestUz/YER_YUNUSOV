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
