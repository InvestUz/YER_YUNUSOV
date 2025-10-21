@extends('layouts.app')

@section('title', 'Шартномани таҳрирлаш')

@section('content')
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-4xl mx-auto px-6">

            {{-- HEADER --}}
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Шартномани таҳрирлаш</h1>
                <nav class="flex items-center gap-2 text-sm text-gray-600">
                    <a href="{{ route('lots.index') }}" class="hover:text-blue-700 font-medium">Лотлар</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('lots.show', $lot) }}" class="hover:text-blue-700 font-medium">Лот
                        #{{ $lot->lot_number }}</a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('contracts.show', $contract) }}" class="hover:text-blue-700 font-medium">Шартнома</a>
                    <span class="text-gray-400">/</span>
                    <span class="text-gray-900 font-semibold">Таҳрирлаш</span>
                </nav>
            </div>

            {{-- ERROR/SUCCESS MESSAGES --}}
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-4 rounded">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-bold text-red-800 mb-2">Хатоликлар:</h3>
                            <ul class="text-sm text-red-700 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>• {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-600 p-4 rounded">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-4 rounded">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            {{-- EDIT FORM --}}
            <form action="{{ route('contracts.update', $contract) }}" method="POST"
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @csrf
                @method('PUT')

                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
                    <h2 class="text-lg font-bold text-blue-900">Шартнома маълумотлари</h2>
                    <p class="text-xs text-blue-700 mt-1">Шартнома № {{ $contract->contract_number }}</p>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-900">
                            Шартнома рақами <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="contract_number" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('contract_number', $contract->contract_number) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-900">
                            Шартнома санаси <span class="text-red-600">*</span>
                        </label>
                        <input type="date" name="contract_date" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('contract_date', $contract->contract_date?->format('Y-m-d')) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-900">
                            Шартнома суммаси (сўм) <span class="text-red-600">*</span>
                        </label>
                        <input type="number" step="0.01" name="contract_amount" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('contract_amount', $contract->contract_amount) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-900">
                            Харидор номи <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="buyer_name" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('buyer_name', $contract->buyer_name) }}">
                    </div>



                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-900">
                            Ғолиб аукционга тўлаган сумма <span class="text-red-600">*</span>
                        </label>
                        <input type="text" name="initial_paid_amount" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            value="{{ old('initial_paid_amount', $contract->initial_paid_amount) }}">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold mb-2 text-gray-900">Харидор телефони</label>
                            <input type="text" name="buyer_phone"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('buyer_phone', $contract->buyer_phone) }}">
                        </div>

                        <div>
                            <label class="block text-sm font-bold mb-2 text-gray-900">Харидор STIR</label>
                            <input type="text" name="buyer_inn"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                                value="{{ old('buyer_inn', $contract->buyer_inn) }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-900">
                            Тўлов тури <span class="text-red-600">*</span>
                        </label>
                        <select name="payment_type" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                            <option value="muddatli"
                                {{ old('payment_type', $contract->payment_type) == 'muddatli' ? 'selected' : '' }}>Муддатли
                            </option>
                            <option value="muddatsiz"
                                {{ old('payment_type', $contract->payment_type) == 'muddatsiz' ? 'selected' : '' }}>
                                Муддатсиз</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-900">
                            Ҳолат <span class="text-red-600">*</span>
                        </label>
                        <select name="status" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition">
                            <option value="active" {{ old('status', $contract->status) == 'active' ? 'selected' : '' }}>
                                Фаол</option>
                            <option value="completed"
                                {{ old('status', $contract->status) == 'completed' ? 'selected' : '' }}>Тўланган</option>
                            <option value="cancelled"
                                {{ old('status', $contract->status) == 'cancelled' ? 'selected' : '' }}>Бекор қилинган
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold mb-2 text-gray-900">Изоҳ</label>
                        <textarea name="note" rows="4"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-blue-600 font-medium transition"
                            placeholder="Қўшимча маълумот...">{{ old('note', $contract->note) }}</textarea>
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                    <a href="{{ route('lots.show', $lot) }}"
                        class="px-6 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-900 rounded-lg font-bold transition">
                        Бекор қилиш
                    </a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold transition">
                        Сақлаш
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
