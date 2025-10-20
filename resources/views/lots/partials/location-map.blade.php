@if($lot->map_embed_url)
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    {{-- Map Header --}}
    <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-purple-100 border-b border-purple-200">
        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
            </svg>
            Локация
        </h3>
    </div>

    {{-- Map Container with Zoom Control --}}
    <div class="relative h-64 bg-gray-200">
        <iframe
            id="mapIframe"
            width="100%"
            height="100%"
            frameborder="0"
            scrolling="no"
            src="{{ $lot->map_embed_url }}&zoom=15"
            class="w-full h-full"
            loading="lazy">
        </iframe>

        {{-- Zoom Controls --}}
        <div class="absolute top-4 right-4 bg-white rounded-lg shadow-lg overflow-hidden">
            <button
                onclick="adjustMapZoom('in')"
                class="block w-10 h-10 flex items-center justify-center hover:bg-gray-100 transition-colors border-b border-gray-200"
                title="Zoom In">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </button>
            <button
                onclick="adjustMapZoom('out')"
                class="block w-10 h-10 flex items-center justify-center hover:bg-gray-100 transition-colors"
                title="Zoom Out">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Map Actions --}}
    @if($lot->location_url)
    <div class="p-4">
        <a href="{{ $lot->location_url }}"
           target="_blank"
           class="block w-full text-center px-4 py-2 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white rounded-lg text-sm font-medium transition-all shadow-sm hover:shadow-md">
            <span class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Google Maps-да очиш
            </span>
        </a>
    </div>
    @endif
</div>
@endif

<script>
// Map zoom functionality
let currentZoom = 15;
function adjustMapZoom(direction) {
    const iframe = document.getElementById('mapIframe');
    if (!iframe) return;

    if (direction === 'in' && currentZoom < 20) {
        currentZoom++;
    } else if (direction === 'out' && currentZoom > 5) {
        currentZoom--;
    }

    const currentSrc = iframe.src;
    const newSrc = currentSrc.replace(/zoom=\d+/, `zoom=${currentZoom}`);
    iframe.src = newSrc;
}
</script>


