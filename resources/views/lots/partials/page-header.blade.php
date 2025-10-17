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
