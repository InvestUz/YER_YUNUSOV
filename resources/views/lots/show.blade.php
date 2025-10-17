@extends('layouts.app')

@section('title', 'Лот ' . $lot->lot_number)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50 to-gray-100">

    {{-- Flash Messages --}}
    @include('lots.partials.flash-messages')

    {{-- Breadcrumb Navigation --}}
    @include('lots.partials.breadcrumb', ['lot' => $lot])

    {{-- Main Content Container --}}
<div class="mx-auto px-4 sm:px-6 lg:px-10 xl:px-16 2xl:px-20 py-8 max-w-full lg:max-w-7xl xl:max-w-8xl 2xl:max-w-[2200px]">
        {{-- Page Header with Quick Actions --}}
        @include('lots.partials.page-header', ['lot' => $lot])

        {{-- Main Grid Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 xl:gap-10 mt-6">

            {{-- Left Column (2/3 width) --}}
            <div class="lg:col-span-2 space-y-8">
                @include('lots.partials.image-gallery', ['lot' => $lot])
                @include('lots.partials.lot-information', ['lot' => $lot])
                @include('lots.partials.payment-schedule', ['lot' => $lot])
                @include('lots.partials.distribution-table', ['lot' => $lot]) 
            </div>

            {{-- Right Column (1/3 width) - Sidebar --}}
            <aside class="space-y-8 sticky top-24 self-start">
                @include('lots.partials.contract-card', ['lot' => $lot])
                @include('lots.partials.location-map', ['lot' => $lot])
                @include('lots.partials.system-info', ['lot' => $lot])
            </aside>

        </div>
    </div>

    {{-- Modals --}}
    @include('lots.partials.modals', ['lot' => $lot])

</div>

{{-- Page-specific Scripts --}}
@push('scripts')
<script>
    // Initialize lot data for JavaScript
    window.lotData = {
        id: {{ $lot->id }},
        images: @json($lot->images ? $lot->images->pluck('url')->values() : []),
        hasLiked: {{ isset($hasLiked) && $hasLiked ? 'true' : 'false' }},
        likesCount: {{ $likesCount ?? 0 }},
        csrfToken: '{{ csrf_token() }}'
    };
</script>
<script src="{{ asset('js/lot-gallery.js') }}"></script>
<script src="{{ asset('js/lot-interactions.js') }}"></script>
<script src="{{ asset('js/contract-form.js') }}"></script>
<script src="{{ asset('js/payment-modal.js') }}"></script>
@endpush

{{-- Page-specific Styles --}}
@push('styles')
<style>
    @media print {
        .no-print, button, nav { display: none !important; }
        body { background: white; }
        .rounded-xl { border-radius: 0.5rem; }
    }

    .animate-slideDown {
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    .animate-slideUp {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .transition-smooth {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>
@endpush
@endsection
