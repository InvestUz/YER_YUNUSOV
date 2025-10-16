@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Шартномалар</h1>
        <a href="{{ route('contracts.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Янги шартнома
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">№</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Шартнома рақами</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Лот</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сана</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тури</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Тўланди</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ҳолат</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Амаллар</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($contracts as $contract)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $loop->iteration }}</td>
                    <td class="px-6 py-4 font-semibold">{{ $contract->contract_number }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm">{{ $contract->lot->lot_number }}</div>
                        <div class="text-xs text-gray-500">{{ Str::limit($contract->lot->address, 30) }}</div>
                    </td>
                    <td class="px-6 py-4">{{ $contract->contract_date->format('d.m.Y') }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded
                            {{ $contract->isMuddatli() ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                            {{ $contract->payment_type_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">{{ number_format($contract->contract_amount, 0, '.', ' ') }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm">{{ number_format($contract->paid_amount, 0, '.', ' ') }}</div>
                        <div class="text-xs text-gray-500">{{ number_format($contract->payment_percentage, 1) }}%</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded
                            @if($contract->status === 'active') bg-yellow-100 text-yellow-800
                            @elseif($contract->status === 'completed') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $contract->status_label }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('contracts.show', $contract) }}"
                           class="text-blue-600 hover:text-blue-900 mr-3">Кўриш</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                        Шартномалар топилмади
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $contracts->links() }}
    </div>
</div>
@endsection
