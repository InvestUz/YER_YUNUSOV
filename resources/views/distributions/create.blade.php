@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Тўловни тақсимлаш</h1>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6">
            <p class="text-sm"><strong>Шартнома:</strong> № {{ $paymentSchedule->contract->contract_number }}</p>
            <p class="text-sm"><strong>Тўлов:</strong> № {{ $paymentSchedule->payment_number }}</p>
            <p class="text-sm"><strong>Тўланган сумма:</strong> {{ number_format($paymentSchedule->actual_amount, 0, '.', ' ') }} сўм</p>
            @if($totalDistributed > 0)
            <p class="text-sm"><strong>Тақсимланган:</strong> {{ number_format($totalDistributed, 0, '.', ' ') }} сўм</p>
            <p class="text-sm"><strong>Қолган:</strong> <span class="font-bold text-green-600">{{ number_format($remainingAmount, 0, '.', ' ') }} сўм</span></p>
            @endif
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

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Мавжуд тақсимотлар -->
        @if($existingDistributions->count() > 0)
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-lg font-bold mb-4">Мавжуд тақсимотлар</h2>
            <div class="space-y-2">
                @foreach($existingDistributions as $dist)
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <span class="text-sm">{{ $dist->category_label }}</span>
                    <span class="font-semibold">{{ number_format($dist->allocated_amount, 0, '.', ' ') }} сўм</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <form action="{{ route('distributions.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8">
            @csrf
            <input type="hidden" name="payment_schedule_id" value="{{ $paymentSchedule->id }}">

            <div id="distributionsContainer">
                <div class="distribution-item border-b pb-4 mb-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Тақсимот 1</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Категория <span class="text-red-500">*</span>
                            </label>
                            <select name="distributions[0][category]" required
                                    class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                <option value="">Танланг</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Сумма (сўм) <span class="text-red-500">*</span>
                            </label>
                            <input type="number" step="0.01" name="distributions[0][allocated_amount]" required
                                   class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline amount-input"
                                   placeholder="0.00">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Тақсимлаш санаси <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="distributions[0][distribution_date]" required
                                   value="{{ date('Y-m-d') }}"
                                   class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">
                                Изоҳ
                            </label>
                            <input type="text" name="distributions[0][note]"
                                   class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <button type="button" onclick="addDistribution()"
                        class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    + Яна қўшиш
                </button>
            </div>

            <div class="bg-gray-50 p-4 rounded mb-6">
                <div class="flex justify-between items-center">
                    <span class="font-bold">Жами тақсимланаётган:</span>
                    <span id="totalAmount" class="text-xl font-bold text-blue-600">0 сўм</span>
                </div>
                <div class="flex justify-between items-center mt-2">
                    <span>Қолган сумма:</span>
                    <span id="remainingDisplay" class="font-bold">{{ number_format($remainingAmount, 0, '.', ' ') }} сўм</span>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Сақлаш
                </button>
                <a href="{{ route('lots.show', $paymentSchedule->contract->lot->id) }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Ортга қайтиш
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let distributionCount = 1;
const remainingAmount = {{ $remainingAmount }};
const existingTotal = {{ $totalDistributed }};

function addDistribution() {
    const container = document.getElementById('distributionsContainer');
    const newItem = document.createElement('div');
    newItem.className = 'distribution-item border-b pb-4 mb-4';
    newItem.innerHTML = `
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-semibold">Тақсимот ${distributionCount + 1}</h3>
            <button type="button" onclick="removeDistribution(this)"
                    class="text-red-600 hover:text-red-900">Ўчириш</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Категория <span class="text-red-500">*</span>
                </label>
                <select name="distributions[${distributionCount}][category]" required
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <option value="">Танланг</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Сумма (сўм) <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" name="distributions[${distributionCount}][allocated_amount]" required
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline amount-input"
                       placeholder="0.00">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Тақсимлаш санаси <span class="text-red-500">*</span>
                </label>
                <input type="date" name="distributions[${distributionCount}][distribution_date]" required
                       value="{{ date('Y-m-d') }}"
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Изоҳ
                </label>
                <input type="text" name="distributions[${distributionCount}][note]"
                       class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>
        </div>
    `;
    container.appendChild(newItem);
    distributionCount++;
    attachAmountListeners();
}

function removeDistribution(button) {
    button.closest('.distribution-item').remove();
    updateTotals();
}

function attachAmountListeners() {
    document.querySelectorAll('.amount-input').forEach(input => {
        input.removeEventListener('input', updateTotals);
        input.addEventListener('input', updateTotals);
    });
}

function updateTotals() {
    let total = 0;
    document.querySelectorAll('.amount-input').forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });

    document.getElementById('totalAmount').textContent = total.toLocaleString('uz-UZ') + ' сўм';

    const remaining = remainingAmount - total;
    const remainingDisplay = document.getElementById('remainingDisplay');
    remainingDisplay.textContent = remaining.toLocaleString('uz-UZ') + ' сўм';

    if (remaining < 0) {
        remainingDisplay.classList.add('text-red-600');
        remainingDisplay.classList.remove('text-green-600');
    } else {
        remainingDisplay.classList.add('text-green-600');
        remainingDisplay.classList.remove('text-red-600');
    }
}

attachAmountListeners();
</script>
@endsection
