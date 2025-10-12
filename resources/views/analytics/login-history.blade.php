{{-- File: resources/views/analytics/login-history.blade.php --}}

@extends('layouts.app')

@section('title', 'Кириш тарихи')

@section('content')
<div class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-white border-b-2 border-gray-300">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <h1 class="text-xl font-bold text-gray-900">Кириш тарихи</h1>
            <p class="text-sm text-gray-600 mt-1">Барча фойдаланувчиларнинг тизимга кириш-чиқиш тарихи</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Жами киришлар</div>
                <div class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_logins']) }}</div>
            </div>

            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Актив сессиялар</div>
                <div class="text-3xl font-bold text-green-700">{{ number_format($stats['active_sessions']) }}</div>
            </div>

            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Бугунги киришлар</div>
                <div class="text-3xl font-bold text-blue-700">{{ number_format($stats['today_logins']) }}</div>
            </div>

            <div class="bg-white border border-gray-300 shadow-sm p-6">
                <div class="text-sm text-gray-600 mb-2">Уникал фойдаланувчилар (бугун)</div>
                <div class="text-3xl font-bold text-purple-700">{{ number_format($stats['unique_users_today']) }}</div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Device Stats -->
            <div class="bg-white border border-gray-300 shadow-sm">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                    <h3 class="text-sm font-bold text-gray-900">Қурилмалар бўйича</h3>
                </div>
                <div class="p-6">
                    @foreach($deviceStats as $stat)
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-700">{{ $stat->device }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stat->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 h-2 mb-4">
                        <div class="bg-blue-600 h-2" style="width: {{ ($stat->count / $stats['total_logins']) * 100 }}%"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Browser Stats -->
            <div class="bg-white border border-gray-300 shadow-sm">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                    <h3 class="text-sm font-bold text-gray-900">Браузерлар бўйича</h3>
                </div>
                <div class="p-6">
                    @foreach($browserStats as $stat)
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm text-gray-700">{{ $stat->browser }}</span>
                        <span class="text-sm font-bold text-gray-900">{{ $stat->count }}</span>
                    </div>
                    <div class="w-full bg-gray-200 h-2 mb-4">
                        <div class="bg-green-600 h-2" style="width: {{ ($stat->count / $stats['total_logins']) * 100 }}%"></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Most Active Users -->
        <div class="bg-white border border-gray-300 shadow-sm mb-6">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                <h3 class="text-sm font-bold text-gray-900">Энг актив фойдаланувчилар</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b-2 border-gray-300">
                        <tr>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Фойдаланувчи</th>
                            <th class="py-3 px-4 text-right font-semibold text-gray-700">Киришлар сони</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($activeUsers as $activeUser)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $activeUser->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $activeUser->user->email }}</div>
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-gray-900">{{ $activeUser->login_count }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Login History Table -->
        <div class="bg-white border border-gray-300 shadow-sm">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-300">
                <h3 class="text-base font-bold text-gray-900">Батафсил тарих</h3>
            </div>

            <!-- Filters -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           placeholder="Дан"
                           class="px-3 py-2 border border-gray-300 text-sm">
                    
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           placeholder="Гача"
                           class="px-3 py-2 border border-gray-300 text-sm">
                    
                    <select name="status" class="px-3 py-2 border border-gray-300 text-sm">
                        <option value="">Барча ҳолатлар</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Актив</option>
                        <option value="logged_out" {{ request('status') === 'logged_out' ? 'selected' : '' }}>Чиққан</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Эскирган</option>
                    </select>
                    
                    <div class="flex gap-2">
                        <button type="submit" 
                                class="px-4 py-2 bg-gray-900 text-white text-sm font-medium hover:bg-gray-800">
                            Фильтрлаш
                        </button>
                        <a href="{{ route('analytics.login.history') }}" 
                           class="px-4 py-2 bg-white border border-gray-300 text-gray-900 text-sm font-medium hover:bg-gray-50">
                            Тозалаш
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-100 border-b-2 border-gray-300">
                        <tr>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Фойдаланувчи</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Кирди</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Чиқди</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Давомийлик</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">IP</th>
                            <th class="py-3 px-4 text-left font-semibold text-gray-700">Қурилма</th>
                            <th class="py-3 px-4 text-center font-semibold text-gray-700">Ҳолат</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($loginHistories as $history)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $history->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $history->user->email }}</div>
                            </td>
                            <td class="py-3 px-4 text-gray-700">{{ $history->login_at->format('d.m.Y H:i:s') }}</td>
                            <td class="py-3 px-4 text-gray-700">
                                {{ $history->logout_at ? $history->logout_at->format('d.m.Y H:i:s') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-gray-700">
                                @if($history->session_duration)
                                    {{ $history->session_duration }} дақиқа
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-4 text-gray-700 font-mono text-xs">{{ $history->ip_address }}</td>
                            <td class="py-3 px-4 text-gray-700">
                                <div>{{ $history->device }}</div>
                                <div class="text-xs text-gray-500">{{ $history->browser }} / {{ $history->platform }}</div>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($history->status === 'active')
                                <span class="inline-block px-2 py-1 bg-green-100 text-green-800 border border-green-300 text-xs font-medium">
                                    Актив
                                </span>
                                @elseif($history->status === 'logged_out')
                                <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 border border-gray-300 text-xs font-medium">
                                    Чиққан
                                </span>
                                @else
                                <span class="inline-block px-2 py-1 bg-red-100 text-red-800 border border-red-300 text-xs font-medium">
                                    Эскирган
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">
                                Маълумот топилмади
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($loginHistories->hasPages())
            <div class="px-6 py-4 border-t border-gray-300">
                {{ $loginHistories->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection