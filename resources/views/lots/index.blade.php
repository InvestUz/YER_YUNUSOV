@extends('layouts.app')

@section('title', 'Лотлар - Toshkent Invest')

@section('content')
<!-- Header -->
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Лотлар</h1>
        <p class="text-gray-600">Барча лотлар рўйхати</p>
    </div>
    @if(Auth::user()->role === 'admin' || Auth::user()->role === 'district_user')
    <a href="{{ route('lots.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Янги лот
    </a>
    @endif
</div>

@if(session('success'))
<div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg">
    <p class="font-medium">{{ session('success') }}</p>
</div>
@endif

<!-- Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="{{ route('lots.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Қидириш</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Лот рақами, манзил..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        @if(Auth::user()->role === 'admin')
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Туман</label>
            <select name="tuman_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Барчаси</option>
                @foreach($tumans as $tuman)
                <option value="{{ $tuman->id }}" {{ request('tuman_id') == $tuman->id ? 'selected' : '' }}>
                    {{ $tuman->name_uz }}
                </option>
                @endforeach
            </select>
        </div>
        @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Тўлов тури</label>
            <select name="payment_type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Барчаси</option>
                <option value="muddatli" {{ request('payment_type') === 'muddatli' ? 'selected' : '' }}>Бўлиб тўлаш</option>
                <option value="muddatli_emas" {{ request('payment_type') === 'muddatli_emas' ? 'selected' : '' }}>Бир марта</option>
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Шартнома</label>
            <select name="contract_signed" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Барчаси</option>
                <option value="1" {{ request('contract_signed') === '1' ? 'selected' : '' }}>Тузилган</option>
                <option value="0" {{ request('contract_signed') === '0' ? 'selected' : '' }}>Тузилмаган</option>
            </select>
        </div>

        <div class="md:col-span-4 flex gap-3">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Қидириш
            </button>
            <a href="{{ route('lots.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                Тозалаш
            </a>
        </div>
    </form>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <p class="text-sm text-gray-600 mb-1">Жами лотлар</p>
        <p class="text-2xl font-bold text-gray-900">{{ $lots->total() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <p class="text-sm text-gray-600 mb-1">Шартнома тузилган</p>
        <p class="text-2xl font-bold text-green-600">{{ $lots->where('contract_signed', true)->count() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <p class="text-sm text-gray-600 mb-1">Бўлиб тўлаш</p>
        <p class="text-2xl font-bold text-orange-600">{{ $lots->where('payment_type', 'muddatli')->count() }}</p>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <p class="text-sm text-gray-600 mb-1">Умумий қиймат</p>
        <p class="text-2xl font-bold text-blue-600">{{ number_format($lots->sum('sold_price') / 1000000000, 2) }} млрд</p>
    </div>
</div>

<!-- Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">
                        <a href="{{ route('lots.index', array_merge(request()->all(), ['sort' => 'lot_number', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-1 hover:text-blue-600">
                            Лот №
                            @if(request('sort') === 'lot_number')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Туман</th>
                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Манзил</th>
                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Ер (га)</th>
                    <th class="text-right py-4 px-4 text-sm font-semibold text-gray-700">
                        <a href="{{ route('lots.index', array_merge(request()->all(), ['sort' => 'sold_price', 'direction' => request('direction') === 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center justify-end gap-1 hover:text-blue-600">
                            Нарх
                            @if(request('sort') === 'sold_price')
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ request('direction') === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                            </svg>
                            @endif
                        </a>
                    </th>
                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Ғолиб</th>
                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Тўлов</th>
                    <th class="text-left py-4 px-4 text-sm font-semibold text-gray-700">Ҳолат</th>
                    <th class="text-right py-4 px-4 text-sm font-semibold text-gray-700">Амаллар</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lots as $lot)
                <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                    <td class="py-4 px-4 text-sm font-medium text-blue-600">
                        <a href="{{ route('lots.show', $lot->id) }}" class="hover:underline">
                            {{ $lot->lot_number }}
                        </a>
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-900">{{ $lot->tuman->name_uz ?? '-' }}</td>
                    <td class="py-4 px-4 text-sm text-gray-600">{{ Str::limit($lot->address, 30) }}</td>
                    <td class="py-4 px-4 text-sm text-gray-900">{{ number_format($lot->land_area, 2) }}</td>
                    <td class="py-4 px-4 text-sm text-gray-900 font-medium text-right">
                        {{ number_format($lot->sold_price / 1000000, 2) }} млн
                    </td>
                    <td class="py-4 px-4 text-sm text-gray-900">{{ Str::limit($lot->winner_name, 20) }}</td>
                    <td class="py-4 px-4">
                        @if($lot->payment_type === 'muddatli')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            Муддатли
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Бир марта
                        </span>
                        @endif
                    </td>
                    <td class="py-4 px-4">
                        @if($lot->contract_signed)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            Шартнома
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Жараёнда
                        </span>
                        @endif
                    </td>
                    <td class="py-4 px-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('lots.show', $lot->id) }}" class="text-blue-600 hover:text-blue-700" title="Кўриш">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('lots.edit', $lot->id) }}" class="text-gray-600 hover:text-gray-700" title="Таҳрирлаш">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-12 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-lg font-medium">Маълумот топилмади</p>
                        <p class="text-sm mt-1">Филтрларни ўзгартириб кўринг</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($lots->hasPages())
    <div class="px-6 py-4 border-t border-gray-200">
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Жами <span class="font-medium">{{ $lots->total() }}</span> дан
                <span class="font-medium">{{ $lots->firstItem() }}</span> -
                <span class="font-medium">{{ $lots->lastItem() }}</span> кўрсатилмоқда
            </div>
            <div>
                {{ $lots->links() }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
