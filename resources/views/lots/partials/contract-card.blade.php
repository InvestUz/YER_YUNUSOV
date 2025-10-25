{{-- contract-card.blade.php --}}
@if ($lot->contract)
    {{-- EXISTING CONTRACT DISPLAY --}}
    <div class="bg-white rounded border border-gray-300 overflow-hidden mb-4">
        <div class="px-6 py-3 bg-gray-100 border-b border-gray-300">
            <p class="text-sm text-gray-700 font-medium">
                Тўлов тури: <strong>{{ $lot->contract->payment_type === 'muddatli' ? 'Муддатли' : 'Муддатсиз' }}</strong>
            </p>
        </div>

        <div class="p-6 space-y-4">
            <div class="grid grid-cols-3 gap-3">
                <div class="p-3 bg-blue-50 rounded border border-blue-200">
                    <p class="text-xs text-blue-700 font-medium mb-1">Шартнома суммаси</p>
                    <p class="text-sm font-bold text-blue-900">
                        {{ number_format($lot->contract->contract_amount / 1000000, 1) }}М
                    </p>
                </div>
                <div class="p-3 bg-green-50 rounded border border-green-200">
                    <p class="text-xs text-green-700 font-medium mb-1">Тўланған</p>
                    <p class="text-sm font-bold text-green-900">
                        {{ number_format($lot->contract->paid_amount / 1000000, 1) }}М
                    </p>
                </div>
                <div class="p-3 bg-orange-50 rounded border border-orange-200">
                    <p class="text-xs text-orange-700 font-medium mb-1">Қолган</p>
                    <p class="text-sm font-bold text-orange-900">
                        {{ number_format($lot->contract->remaining_amount / 1000000, 1) }}М
                    </p>
                </div>
            </div>

            <div>
                <div class="flex justify-between text-xs text-gray-600 mb-2">
                    <span>Тўлов прогресси</span>
                    <span class="font-bold text-gray-900">{{ number_format($lot->contract->payment_percentage, 1) }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                    <div class="bg-green-600 h-3 rounded-full transition-all"
                        style="width: {{ min($lot->contract->payment_percentage, 100) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Discount Information --}}
        @if ($lot->qualifiesForDiscount())
            <div class="px-6 pb-4">
                <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                    <p class="text-sm font-semibold text-yellow-800 mb-2">Чегирма қўлланилди (20%)</p>
                    <p class="text-xs text-yellow-700 mb-3">
                        Аукцион санаси: {{ $lot->auction_date->format('d.m.Y') }} (10.09.2024 дан кейин)
                    </p>
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between py-1 border-b border-yellow-200">
                            <span>Тўланган:</span>
                            <strong>{{ number_format($lot->paid_amount, 0, '.', ' ') }} сўм</strong>
                        </div>
                        <div class="flex justify-between py-1 border-b border-yellow-200">
                            <span>Чегирма (20%):</span>
                            <strong>{{ number_format($lot->discount, 0, '.', ' ') }} сўм</strong>
                        </div>
                        <div class="flex justify-between py-1 bg-green-50 px-2 rounded">
                            <span class="font-medium">Тақсимотга:</span>
                            <strong>{{ number_format($lot->distributable_amount, 0, '.', ' ') }} сўм</strong>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Admin Actions --}}
        @if(Auth::check() && Auth::user()->role === 'admin')
            <div class="px-6 pb-4">
                {{-- Edit/Delete (only when no schedules/distributions) --}}
                @if($lot->contract->paymentSchedules()->count() == 0 && $lot->contract->distributions()->count() == 0)
                    <div class="flex gap-2 mb-2">
                        <a href="{{ route('contracts.edit', $lot->contract) }}"
                            class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded font-medium text-sm text-center">
                            Таҳрирлаш
                        </a>
                        <form action="{{ route('contracts.destroy', $lot->contract) }}" method="POST"
                            onsubmit="return confirm('Шартномани ўчирмоқчимисиз?');" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded font-medium text-sm">
                                Ўчириш
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Rollback Button (always show when contract exists) --}}
                <button type="button" onclick="document.getElementById('rollbackModal').classList.remove('hidden')"
                    class="w-full px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded font-medium text-sm">
                    ⚠ Шартномани бекор қилиш ва янгидан яратиш
                </button>

                {{-- Warning --}}
                @if($lot->contract->paymentSchedules()->count() > 0 || $lot->contract->distributions()->count() > 0)
                    <p class="text-xs text-gray-600 mt-2 text-center">
                        @if($lot->contract->distributions()->count() > 0)
                            ⚠ Тақсимот мавжуд - бекор қилиш учун аввал тақсимотни ўчиринг
                        @else
                            ⚠ Тўлов графиги мавжуд - бекор қилишингиз мумкин
                        @endif
                    </p>
                @endif
            </div>
        @endif
    </div>

@else
    {{-- CONTRACT CREATION FORM --}}
    @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
        <div class="bg-white rounded border-2 border-yellow-400 overflow-hidden mb-4">
            <div class="px-6 py-3 bg-yellow-50 border-b border-yellow-200">
                <h3 class="font-bold text-yellow-900">Янги шартнома яратиш</h3>
                <p class="text-xs text-yellow-700">Тўлов турини танланг</p>
            </div>

            <form action="{{ route('contracts.store') }}" method="POST" class="p-6 space-y-4" id="contractCreationForm">
                @csrf
                <input type="hidden" name="lot_id" value="{{ $lot->id }}">

                {{-- Payment Type Selection --}}
                <div class="space-y-2">
                    <label class="flex cursor-pointer border-2 border-gray-300 rounded p-3 hover:border-blue-500 payment-type-card" data-type="muddatli">
                        <input type="radio" name="payment_type" value="muddatli" required class="sr-only payment-type-radio">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900">Муддатли</span>
                                <span class="check-icon hidden text-blue-600 text-xl">✓</span>
                            </div>
                            <p class="text-xs text-gray-600">Бўлиб тўлаш (график билан)</p>
                        </div>
                    </label>

                    <label class="flex cursor-pointer border-2 border-gray-300 rounded p-3 hover:border-blue-500 payment-type-card" data-type="muddatsiz">
                        <input type="radio" name="payment_type" value="muddatsiz" required class="sr-only payment-type-radio">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-900">Муддатсиз</span>
                                <span class="check-icon hidden text-blue-600 text-xl">✓</span>
                            </div>
                            <p class="text-xs text-gray-600">Бир йўла тўлаш</p>
                        </div>
                    </label>
                </div>

                {{-- Muddatli Details --}}
                <div id="contractDetailsStep" class="hidden space-y-3">
                    <h4 class="font-bold text-gray-900 border-t pt-3">Шартнома маълумотлари</h4>
                    <input type="text" name="contract_number" placeholder="Шартнома рақами"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <input type="date" name="contract_date" value="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <input type="number" step="0.01" name="contract_amount" value="{{ $lot->sold_price }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Шартнома суммаси">
                    <input type="text" name="buyer_name" value="{{ $lot->winner_name }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Харидор номи">
                    <input type="text" name="buyer_phone" value="{{ $lot->winner_phone }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Телефон">
                    <input type="text" name="buyer_inn" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="STIR">
                    <select name="status" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="active">Фаол</option>
                        <option value="completed">Тўланған</option>
                        <option value="cancelled">Бекор қилинган</option>
                    </select>
                    <textarea name="note" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Изоҳ"></textarea>
                    
                    <div class="border-t pt-3">
                        <p class="text-sm font-medium text-gray-700 mb-2">Аввал тўланған (опционал)</p>
                        <input type="number" step="0.01" name="initial_paid_amount" value="0"
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm mb-2" placeholder="Аввал тўланган сумма">
                        <input type="date" name="initial_payment_date" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    </div>
                </div>

                {{-- Muddatsiz Details --}}
                <div id="muddatsizPaymentStep" class="hidden space-y-3">
                    <h4 class="font-bold text-gray-900 border-t pt-3">Бир йўла тўлов</h4>
                    <input type="number" step="0.01" name="actual_paid_amount" id="actualPaidAmount" value="{{ $lot->sold_price }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="Факт тўланган сумма"
                        oninput="validateMuddatsizAmount()">
                    
                    @if ($lot->auction_date && $lot->auction_date->gt(\Carbon\Carbon::parse('2024-09-10')))
                        <div class="bg-yellow-50 border border-yellow-200 rounded p-3">
                            <p class="text-sm font-semibold text-yellow-800 mb-2">Чегирма қўлланади (20%)</p>
                            <input type="number" step="0.01" name="discount_amount" id="discountAmount" readonly value="0"
                                class="w-full border border-yellow-300 rounded px-3 py-2 text-sm bg-yellow-50 mb-2">
                            <div class="bg-white rounded p-2 text-xs space-y-1">
                                <div class="flex justify-between">
                                    <span>Факт:</span>
                                    <span id="displayPaidAmount" class="font-semibold">0</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Чегирма:</span>
                                    <span id="displayDiscountAmount" class="font-semibold text-yellow-700">0</span>
                                </div>
                                <div class="flex justify-between border-t pt-1">
                                    <span class="font-medium">Тақсимотга:</span>
                                    <span id="displayDistributableAmount" class="font-bold text-green-700">0</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <input type="date" name="actual_payment_date" value="{{ now()->format('Y-m-d') }}"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <input type="text" name="reference_number" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                        placeholder="Тўлов ҳужжат рақами">
                </div>

                <button type="submit" id="submitBtn" disabled
                    class="w-full bg-gray-400 text-white px-4 py-3 rounded font-semibold cursor-not-allowed">
                    Тўлов турини танланг
                </button>
            </form>
        </div>
    @endif
@endif

{{-- ROLLBACK MODAL --}}
@if(Auth::check() && Auth::user()->role === 'admin' && $lot->contract)
<div id="rollbackModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded max-w-lg w-full shadow-lg">
        <div class="px-6 py-3 border-b bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-900">⚠ Шартномани бекор қилиш</h3>
            <button onclick="document.getElementById('rollbackModal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <div class="bg-red-50 border border-red-300 rounded p-4 mb-4">
                <p class="font-bold text-red-900 mb-2">ДИҚҚАТ!</p>
                <p class="text-sm text-red-800 mb-3">Шартнома тўлиқ ўчирилади:</p>
                
                <ul class="text-sm text-gray-700 space-y-1 mb-3">
                    <li>✗ Шартнома тўлиқ ўчирилади</li>
                    <li>✗ Барча тўлов графиги ўчирилади</li>
                    <li>✗ Лот маълумотлари тозаланади</li>
                    <li>✓ Янги шартнома яратиш имконияти пайдо бўлади</li>
                </ul>

                @if($lot->contract->distributions()->count() > 0)
                    <div class="bg-yellow-50 border border-yellow-300 rounded p-2 mb-2">
                        <p class="text-sm text-yellow-800 font-medium">⚠ Тақсимот мавжуд! Аввал тақсимотни ўчиринг!</p>
                    </div>
                @endif

                <p class="text-xs text-gray-600"><strong>Эслатма:</strong> Бу амалдан кейин янги шартнома яратишингиз керак.</p>
            </div>

            <form action="{{ route('contracts.rollback', $lot->contract) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    <button type="submit"
                            @if($lot->contract->distributions()->count() > 0) disabled @endif
                            class="flex-1 px-4 py-3 rounded font-semibold
                                   {{ $lot->contract->distributions()->count() > 0 ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-red-600 hover:bg-red-700 text-white' }}">
                        Ҳа, бекор қилиш
                    </button>
                    <button type="button" onclick="document.getElementById('rollbackModal').classList.add('hidden')"
                            class="px-6 py-3 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded font-medium">
                        Ёпиш
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contract Form
    const contractForm = document.getElementById('contractCreationForm');
    if (contractForm) {
        const paymentTypeRadios = document.querySelectorAll('.payment-type-radio');
        const contractDetailsStep = document.getElementById('contractDetailsStep');
        const muddatsizPaymentStep = document.getElementById('muddatsizPaymentStep');
        const submitBtn = document.getElementById('submitBtn');

        paymentTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedType = this.value;
                document.querySelectorAll('.payment-type-card').forEach(card => {
                    const checkIcon = card.querySelector('.check-icon');
                    if (card.dataset.type === selectedType) {
                        card.classList.add('border-blue-500', 'bg-blue-50');
                        checkIcon.classList.remove('hidden');
                    } else {
                        card.classList.remove('border-blue-500', 'bg-blue-50');
                        checkIcon.classList.add('hidden');
                    }
                });

                if (selectedType === 'muddatli') {
                    contractDetailsStep.classList.remove('hidden');
                    muddatsizPaymentStep.classList.add('hidden');
                    submitBtn.textContent = 'Муддатли шартнома яратиш';
                } else {
                    contractDetailsStep.classList.add('hidden');
                    muddatsizPaymentStep.classList.remove('hidden');
                    submitBtn.textContent = 'Муддатсиз тўловни қайд қилиш';
                }

                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                submitBtn.classList.add('bg-green-600', 'hover:bg-green-700', 'cursor-pointer');
            });
        });
    }

    // Modal
    const modal = document.getElementById('rollbackModal');
    if (modal) {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                modal.classList.add('hidden');
            }
        });
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });
    }
});

function validateMuddatsizAmount() {
    const actualPaidInput = document.getElementById('actualPaidAmount');
    const discountInput = document.getElementById('discountAmount');
    const displayPaid = document.getElementById('displayPaidAmount');
    const displayDiscount = document.getElementById('displayDiscountAmount');
    const displayDistributable = document.getElementById('displayDistributableAmount');

    if (!actualPaidInput) return;

    const actualPaid = parseFloat(actualPaidInput.value) || 0;
    
    @if ($lot->auction_date && $lot->auction_date->gt(\Carbon\Carbon::parse('2024-09-10')))
        const discount = actualPaid * 0.20;
        const distributable = actualPaid - discount;

        if (discountInput) discountInput.value = discount.toFixed(2);
        if (displayPaid) displayPaid.textContent = new Intl.NumberFormat('uz-UZ').format(actualPaid) + ' сўм';
        if (displayDiscount) displayDiscount.textContent = new Intl.NumberFormat('uz-UZ').format(discount) + ' сўм';
        if (displayDistributable) displayDistributable.textContent = new Intl.NumberFormat('uz-UZ').format(distributable) + ' сўм';
    @endif
}
</script>