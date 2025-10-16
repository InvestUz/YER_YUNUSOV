@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <div class="flex justify-between items-start mb-4">
            <div>
                <h1 class="text-2xl font-bold">Қўшимча келишув № {{ $agreement->agreement_number }}</h1>
                <p class="text-gray-600">Сана: {{ $agreement->agreement_date->format('d.m.Y') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('contracts.show', $agreement->contract) }}"
                   class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Шартномага қайтиш
                </a>
                <form action="{{ route('additional-agreements.destroy', $agreement) }}" method="POST"
                      onsubmit="return confirm('Ростдан ҳам ўчирмоқчимисиз?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                        Ўчириш
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Шартнома маълумотлари</h3>
                <p class="text-sm"><strong>Шартнома рақами:</strong> {{ $agreement->contract->contract_number }}</p>
                <p class="text-sm"><strong>Лот:</strong> {{ $agreement->contract->lot->lot_number }}</p>
                <p class="text-sm"><strong>Асосий сумма:</strong> {{ number_format($agreement->contract->contract_amount - $agreement->new_amount, 0, '.', ' ') }} сўм</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Келишув маълумотлари</h3>
                <p class="text-sm"><strong>Қўшимча сумма:</strong> <span class="text-green-600 font-bold">{{ number_format($agreement->new_amount, 0, '.', ' ') }} сўм</span></p>
                <p class="text-sm"><strong>Янги жами сумма:</strong> {{ number_format($agreement->contract->contract_amount, 0, '.', ' ') }} сўм</p>
            </div>

            <div class="md:col-span-2">
                <h3 class="font-semibold text-gray-700 mb-2">Сабаб</h3>
                <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $agreement->reason }}</p>
            </div>

            @if($agreement->note)
            <div class="md:col-span-2">
                <h3 class="font-semibold text-gray-700 mb-2">Изоҳ</h3>
                <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded">{{ $agreement->note }}</p>
            </div>
            @endif

            <div>
                <h3 class="font-semibold text-gray-700 mb-2">Тизим маълумотлари</h3>
                <p class="text-sm"><strong>Яратди:</strong> {{ $agreement->creator?->name ?? '-' }}</p>
                <p class="text-sm"><strong>Яратилган:</strong> {{ $agreement->created_at->format('d.m.Y H:i') }}</p>
                @if($agreement->updater)
                <p class="text-sm"><strong>Янгиланган:</strong> {{ $agreement->updater->name }} ({{ $agreement->updated_at->format('d.m.Y H:i') }})</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Тўлов графиги -->
    @if($agreement->paymentSchedules->count() > 0)
    <div class="bg-white shadow rounded-lg p-6 mb-6">
        <h2 class="text-xl font-bold mb-4">Тўлов графиги</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">№</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Режалаштирилган сана</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Муддат</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Режалаштирилган</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Тўланган</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ҳолат</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($agreement->paymentSchedules as $schedule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $schedule->payment_number }}</td>
                        <td class="px-4 py-2">{{ $schedule->planned_date->format('d.m.Y') }}</td>
                        <td class="px-4 py-2">{{ $schedule->deadline_date->format('d.m.Y') }}</td>
                        <td class="px-4 py-2">{{ number_format($schedule->planned_amount, 0, '.', ' ') }}</td>
                        <td class="px-4 py-2">{{ number_format($schedule->actual_amount, 0, '.', ' ') }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded bg-{{ $schedule->status_color }}-100 text-{{ $schedule->status_color }}-800">
                                {{ $schedule->status_label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Тақсимотлар -->
    @if($agreement->distributions->count() > 0)
    <div class="bg-white shadow rounded-lg p-6">
        <h2 class="text-xl font-bold mb-4">Тақсимотлар</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Категория</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Сумма</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Сана</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ҳолат</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($agreement->distributions as $distribution)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $distribution->category_label }}</td>
                        <td class="px-4 py-2 font-semibold">{{ number_format($distribution->allocated_amount, 0, '.', ' ') }}</td>
                        <td class="px-4 py-2">{{ $distribution->distribution_date->format('d.m.Y') }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 text-xs rounded bg-{{ $distribution->status_color }}-100 text-{{ $distribution->status_color }}-800">
                                {{ $distribution->status_label }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
