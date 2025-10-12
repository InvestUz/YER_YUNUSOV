{{-- File: resources/views/analytics/lot-messages.blade.php --}}

@extends('layouts.app')

@section('title', 'Хабарлар - Лот ' . $lot->lot_number)

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-white border-b-2 border-gray-300">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">Хабарлар</h1>
                    <p class="text-sm text-gray-600 mt-1">Лот: {{ $lot->lot_number }} - {{ $lot->address }}</p>
                </div>
                <a href="{{ route('lots.show', $lot) }}" 
                   class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white border border-gray-900 transition text-sm font-medium">
                    Лотга қайтиш
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Жами хабарлар</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</div>
            </div>

            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Кутилмоқда</div>
                <div class="text-3xl font-bold text-yellow-700">{{ number_format($stats['pending']) }}</div>
            </div>

            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Ўқилган</div>
                <div class="text-3xl font-bold text-blue-700">{{ number_format($stats['read']) }}</div>
            </div>

            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Жавоб берилган</div>
                <div class="text-3xl font-bold text-green-700">{{ number_format($stats['replied']) }}</div>
            </div>
        </div>

        <!-- Messages List -->
        <div class="bg-white border border-gray-300 shadow-sm">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Хабарлар рўйхати</h3>
                    
                    <!-- Filter -->
                    <form method="GET" class="flex gap-2">
                        <select name="status" class="px-3 py-2 border border-gray-300 text-sm" onchange="this.form.submit()">
                            <option value="">Барча ҳолатлар</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Кутилмоқда</option>
                            <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Ўқилган</option>
                            <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>Жавоб берилган</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="divide-y divide-gray-200">
                @forelse($messages as $message)
                <div class="p-6 hover:bg-gray-50 transition">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $message->name }}</h4>
                                
                                @if($message->status === 'pending')
                                <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 border border-yellow-300 text-xs font-medium">
                                    Янги
                                </span>
                                @elseif($message->status === 'read')
                                <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 border border-blue-300 text-xs font-medium">
                                    Ўқилган
                                </span>
                                @else
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 border border-green-300 text-xs font-medium">
                                    Жавоб берилган
                                </span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                                <span>
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $message->email }}
                                </span>
                                
                                @if($message->phone)
                                <span>
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    {{ $message->phone }}
                                </span>
                                @endif
                                
                                <span>
                                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $message->created_at->format('d.m.Y H:i') }}
                                </span>
                            </div>
                            
                            <div class="bg-gray-50 border border-gray-200 p-4 rounded">
                                <p class="text-sm text-gray-700 whitespace-pre-line">{{ $message->message }}</p>
                            </div>
                            
                            <div class="mt-3 text-xs text-gray-500">
                                IP: <span class="font-mono">{{ $message->ip_address }}</span>
                                @if($message->user)
                                | Фойдаланувчи: <span class="font-medium">{{ $message->user->name }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="ml-4 space-y-2">
                            @if($message->status === 'pending')
                            <button onclick="markAsRead({{ $message->id }})" 
                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition">
                                Ўқилган деб белгилаш
                            </button>
                            @endif
                            
                            @if($message->status !== 'replied')
                            <button onclick="markAsReplied({{ $message->id }})" 
                                    class="block px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium transition">
                                Жавоб берилди
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-gray-500">
                    Хабарлар топилмади
                </div>
                @endforelse
            </div>

            @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-gray-300">
                {{ $messages->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function markAsRead(messageId) {
    if (!confirm('Хабарни ўқилган деб белгилайсизми?')) return;
    
    fetch(`/analytics/messages/${messageId}/mark-read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAsReplied(messageId) {
    if (!confirm('Хабарга жавоб берилдими?')) return;
    
    fetch(`/analytics/messages/${messageId}/mark-replied`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => console.error('Error:', error));
}
</script>
@endsection