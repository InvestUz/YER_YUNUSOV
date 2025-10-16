@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Шартномани таҳрирлаш</h1>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('contracts.update', $contract) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Лот
                </label>
                <input type="text" value="{{ $contract->lot->lot_number }} - {{ $contract->lot->address }}" disabled
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 bg-gray-100">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Шартнома рақами <span class="text-red-500">*</span>
                </label>
                <input type="text" name="contract_number" value="{{ old('contract_number', $contract->contract_number) }}" required
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Шартнома санаси <span class="text-red-500">*</span>
                </label>
                <input type="date" name="contract_date" value="{{ old('contract_date', $contract->contract_date->format('Y-m-d')) }}" required
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Тўлов тури <span class="text-red-500">*</span>
                </label>
                <select name="payment_type" required
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="muddatli" {{ old('payment_type', $contract->payment_type) == 'muddatli' ? 'selected' : '' }}>Муддатли</option>
                    <option value="muddatsiz" {{ old('payment_type', $contract->payment_type) == 'muddatsiz' ? 'selected' : '' }}>Муддатсиз</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Шартнома суммаси (сўм) <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" name="contract_amount" value="{{ old('contract_amount', $contract->contract_amount) }}" required
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                <p class="text-xs text-gray-500 mt-1">Тўланган: {{ number_format($contract->paid_amount, 0, '.', ' ') }} сўм</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Ҳолат <span class="text-red-500">*</span>
                </label>
                <select name="status" required
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="active" {{ old('status', $contract->status) == 'active' ? 'selected' : '' }}>Фаол</option>
                    <option value="completed" {{ old('status', $contract->status) == 'completed' ? 'selected' : '' }}>Тўланган</option>
                    <option value="cancelled" {{ old('status', $contract->status) == 'cancelled' ? 'selected' : '' }}>Бекор қилинган</option>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Изоҳ
                </label>
                <textarea name="note" rows="3"
                          class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('note', $contract->note) }}</textarea>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Сақлаш
                </button>
                <a href="{{ route('contracts.show', $contract) }}"
                   class="text-gray-600 hover:text-gray-900">
                    Бекор қилиш
                </a>
            </div>
        </form>

        <div class="mt-6">
            <form action="{{ route('contracts.destroy', $contract) }}" method="POST"
                  onsubmit="return confirm('Шартномани ўчириш барча боғланган маълумотларни ҳам ўчиради. Давом эттирмоқчимисиз?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Шартномани ўчириш
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
