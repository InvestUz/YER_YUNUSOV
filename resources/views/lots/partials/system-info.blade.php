<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    {{-- Header --}}
    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Тизим маълумотлари
        </h3>
    </div>

    {{-- Content --}}
    <div class="p-6">
        <div class="space-y-3 text-xs text-gray-600">

            {{-- Created At --}}
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Яратилган:
                </span>
                <span class="font-medium text-gray-900">{{ $lot->created_at->format('d.m.Y H:i') }}</span>
            </div>

            {{-- Updated At --}}
            <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Янгиланган:
                </span>
                <span class="font-medium text-gray-900">{{ $lot->updated_at->format('d.m.Y H:i') }}</span>
            </div>

            {{-- Unique Number / ID --}}
            @if($lot->unique_number)
            <div class="flex justify-between items-center">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    ID:
                </span>
                <span class="font-medium text-gray-900 font-mono">{{ $lot->unique_number }}</span>
            </div>
            @endif

        </div>
    </div>
</div>
