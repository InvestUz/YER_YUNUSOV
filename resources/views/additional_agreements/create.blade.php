@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Қўшимча келишув яратиш</h1>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-sm"><strong>Шартнома:</strong> № {{ $contract->contract_number }}</p>
            <p class="text-sm"><strong>Лот:</strong> {{ $contract->lot->lot_number }}</p>
            <p class="text-sm"><strong>Жорий сумма:</strong> {{ number_format($contract->contract_amount, 0, '.', ' ') }} сўм</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('additional-agreements.store', $contract) }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Келишув рақами <span class="text-red-500">*</span>
                </label>
                <input type="text" name="agreement_number" value="{{ old('agreement_number') }}" required
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="Масalan: ҚК-001">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Келишув санаси <span class="text-red-500">*</span>
                </label>
                <input type="date" name="agreement_date" value="{{ old('agreement_date') }}" required
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Қўшимча сумма (сўм) <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" name="new_amount" value="{{ old('new_amount') }}" required
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                       placeholder="0.00">
                <p class="text-xs text-gray-500 mt-1">Бу сумма шартнома суммасига қўшилади</p>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Сабаби <span class="text-red-500">*</span>
                </label>
                <textarea name="reason" rows="3" required
                          class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                          placeholder="Қўшимча келишув сабабини кўрсатинг">{{ old('reason') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Изоҳ
                </label>
                <textarea name="note" rows="2"
                          class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('note') }}</textarea>
            </div>

            @if($contract->isMuddatli())
            <div class="border-t pt-4 mb-6">
                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="generate_schedule" value="1" id="generateSchedule"
                               class="mr-2" {{ old('generate_schedule') ? 'checked' : '' }}>
                        <span class="text-sm font-bold">Тўлов графиги яратиш</span>
                    </label>
                </div>

                <div id="scheduleFields" class="space-y-4 {{ old('generate_schedule') ? '' : 'hidden' }}">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Даврийлик <span class="text-red-500">*</span>
                        </label>
                        <select name="frequency" id="frequency"
                                class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="monthly" {{ old('frequency') == 'monthly' ? 'selected' : '' }}>Ойлик</option>
                            <option value="quarterly" {{ old('frequency') == 'quarterly' ? 'selected' : '' }}>Чораклик</option>
                            <option value="yearly" {{ old('frequency') == 'yearly' ? 'selected' : '' }}>Йиллик</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Бошланиш санаси <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="start_date" id="startDate" value="{{ old('start_date') }}"
                               class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Тўловлар сони <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="number_of_payments" id="numberOfPayments" min="1" max="120" value="{{ old('number_of_payments') }}"
                               class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                </div>
            </div>
            @endif

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
    </div>
</div>

<script>
document.getElementById('generateSchedule')?.addEventListener('change', function() {
    const fields = document.getElementById('scheduleFields');
    const frequency = document.getElementById('frequency');
    const startDate = document.getElementById('startDate');
    const numberOfPayments = document.getElementById('numberOfPayments');

    if (this.checked) {
        fields.classList.remove('hidden');
        frequency.required = true;
        startDate.required = true;
        numberOfPayments.required = true;
    } else {
        fields.classList.add('hidden');
        frequency.required = false;
        startDate.required = false;
        numberOfPayments.required = false;
    }
});
</script>
@endsection
