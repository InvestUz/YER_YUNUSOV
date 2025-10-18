{{-- contract-card.blade.php --}}

@if ($lot->contract)
    {{-- EXISTING CONTRACT DISPLAY --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
            <h3 class="font-bold text-green-900 text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Шартнома № {{ $lot->contract->contract_number }}
            </h3>
            <p class="text-sm text-green-700 mt-1">{{ $lot->contract->contract_date->format('d.m.Y') }}</p>
            <p class="text-xs text-green-600 mt-1 font-medium">
                Тўлов тури:
                <strong>{{ $lot->contract->payment_type === 'muddatli' ? 'Муддатли' : 'Муддатсиз' }}</strong>
            </p>
        </div>

        <div class="p-6 space-y-4">
            <div class="grid grid-cols-3 gap-3">
                <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                    <p class="text-xs text-blue-700 font-medium mb-1">Сотиб олиш киймати</p>
                    <p class="text-sm font-bold text-blue-900">
                        {{ number_format($lot->contract->contract_amount / 1000000, 1) }}М
                    </p>
                </div>
                <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                    <p class="text-xs text-green-700 font-medium mb-1">Тўланган</p>
                    <p class="text-sm font-bold text-green-900">
                        {{ number_format($lot->contract->paid_amount / 1000000, 1) }}М
                    </p>
                </div>
                <div class="p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border border-orange-200">
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
                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500 shadow-sm"
                        style="width: {{ min($lot->contract->payment_percentage, 100) }}%"></div>
                </div>
            </div>

        </div>
    </div>
@elseif($lot->payment_type === 'muddatsiz' && $lot->paid_amount > 0)
    {{-- MUDDATSIZ PAYMENT DISPLAY --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-blue-100 border-b border-blue-200">
            <h3 class="font-bold text-blue-900 text-lg flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Муддатсиз тўлов
            </h3>
            <p class="text-xs text-blue-700 mt-1">Шартнома талаб қилинмайди</p>
        </div>

        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border border-purple-200">
                    <p class="text-xs text-purple-700 font-medium mb-1">Аукцион нархи</p>
                    <p class="text-sm font-bold text-purple-900">
                        {{ number_format($lot->sold_price / 1000000, 1) }}М
                    </p>
                </div>
                <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                    <p class="text-xs text-green-700 font-medium mb-1">Тўланган</p>
                    <p class="text-sm font-bold text-green-900">
                        {{ number_format($lot->paid_amount / 1000000, 1) }}М
                    </p>
                </div>
            </div>

            @php
                $difference = $lot->paid_amount - $lot->sold_price;
            @endphp
            @if ($difference != 0)
                <div class="p-4 rounded-lg border {{ $difference > 0 ? 'bg-green-50 border-green-200' : 'bg-orange-50 border-orange-200' }}">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">Фарқ:</span>
                        <span class="font-bold text-lg {{ $difference > 0 ? 'text-green-700' : 'text-orange-700' }}">
                            {{ number_format(abs($difference), 0, '.', ' ') }} сўм
                        </span>
                    </div>
                    <p class="text-sm {{ $difference > 0 ? 'text-green-700' : 'text-orange-700' }}">
                        @if ($difference > 0)
                            ✓ Аукцион нархидан {{ number_format($difference, 0, '.', ' ') }} сўм кўп тўланган
                        @else
                            ⚠ Аукцион нархидан {{ number_format(abs($difference), 0, '.', ' ') }} сўм кам тўланган
                        @endif
                    </p>
                </div>
            @endif

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                <p class="text-xs text-blue-800 flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                    <span><strong>Эслатма:</strong> Муддатсиз тўлов учун шартнома яратилмаган. Тўлов амалга оширилган.</span>
                </p>
            </div>
        </div>
    </div>
@else
    {{-- CONTRACT/PAYMENT CREATION FORM --}}
    @if (Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
        <div class="bg-white rounded-xl shadow-sm border-2 border-yellow-400 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-yellow-100 border-b border-yellow-200">
                <h3 class="font-bold text-yellow-900 text-lg flex items-center gap-2">

                    Тўлов тури
                </h3>
                <p class="text-xs text-yellow-700 mt-1">Тизимда шартнома мавжуд эмас</p>
            </div>

            <form action="{{ route('contracts.store') }}" method="POST" class="p-6 space-y-4" id="contractCreationForm">
                @csrf
                <input type="hidden" name="lot_id" value="{{ $lot->id }}">

                <style>
                    #contractDetailsStep.hidden,
                    #muddatsizPaymentStep.hidden,
                    #muddatliNote.hidden,
                    #initialPaymentSection.hidden {
                        display: none !important;
                    }
                </style>

                {{-- STEP 1: Payment Type Selection --}}
                <div id="paymentTypeStep">
                    <div class="space-y-3">
                        <label class="relative flex cursor-pointer border-2 border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-all payment-type-card group" data-type="muddatli">
                            <input type="radio" name="payment_type" value="muddatli" required class="sr-only payment-type-radio">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-base font-bold text-gray-900 group-hover:text-blue-700">Муддатли</span>
                                    <svg class="w-6 h-6 text-blue-600 hidden check-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-600">Бўлиб тўлаш</p>
                            </div>
                        </label>

                        <label class="relative flex cursor-pointer border-2 border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-all payment-type-card group" data-type="muddatsiz">
                            <input type="radio" name="payment_type" value="muddatsiz" required class="sr-only payment-type-radio">
                            <div class="flex-1">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-base font-bold text-gray-900 group-hover:text-blue-700">Муддатсиз</span>
                                    <svg class="w-6 h-6 text-blue-600 hidden check-icon" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-600">Бир йўла тўлаш </p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- STEP 2: Contract Details (FOR MUDDATLI) --}}
                <div id="contractDetailsStep" class="hidden space-y-4">
                    <div class="border-t pt-4">
                        <label class="block text-sm font-bold mb-3 text-gray-900">2. Шартнома маълумотлари</label>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Шартнома рақами <span class="text-red-500">*</span></label>
                        <input type="text" name="contract_number" value="{{ old('contract_number') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Шартнома санаси <span class="text-red-500">*</span></label>
                        <input type="date" name="contract_date" value="{{ old('contract_date', now()->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Шартнома суммаси (сўм) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="contract_amount" id="contractAmount"
                            value="{{ old('contract_amount', $lot->sold_price) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Харидор номи <span class="text-red-500">*</span></label>
                        <input type="text" name="buyer_name" value="{{ old('buyer_name', $lot->winner_name) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Харидор телефони</label>
                        <input type="text" name="buyer_phone" value="{{ old('buyer_phone', $lot->winner_phone) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Харидор STIR</label>
                        <input type="text" name="buyer_inn" value="{{ old('buyer_inn') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Ҳолат <span class="text-red-500">*</span></label>
                        <select name="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active" selected>Фаол</option>
                            <option value="completed">Тўланган</option>
                            <option value="cancelled">Бекор қилинган</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Изоҳ</label>
                        <textarea name="note" rows="3"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="Қўшимча маълумот...">{{ old('note') }}</textarea>
                    </div>

                    {{-- NEW: Initial Payment Section for Muddatli --}}
                    <div id="initialPaymentSection" class="border-t pt-4">
                        <label class="block text-sm font-bold mb-3 text-gray-900">3. Аввал тўланган сумма (Опционал)</label>
                        <p class="text-xs text-gray-600 mb-3">
                            Агар шартнома имзолашдан олдин тўлов бўлган бўлса, киритинг
                        </p>

                        <div>
                            <label class="block text-xs font-medium mb-1 text-gray-700">Аввал тўланган сумма (сўм)</label>
                            <input type="number" step="0.01" name="initial_paid_amount" id="initialPaidAmount"
                                value="{{ old('initial_paid_amount', 0) }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                min="0">
                            <p class="text-xs text-gray-500 mt-1">Шартнома суммасидан кам бўлиши керак</p>
                        </div>

                        <div class="mt-3">
                            <label class="block text-xs font-medium mb-1 text-gray-700">Аввал тўлов санаси</label>
                            <input type="date" name="initial_payment_date" id="initialPaymentDate"
                                value="{{ old('initial_payment_date') }}"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                max="{{ now()->format('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                {{-- STEP 3: Muddatsiz Payment (NO CONTRACT) --}}
                <div id="muddatsizPaymentStep" class="hidden space-y-4">
                    <div class="border-t pt-4">
                        <label class="block text-sm font-bold mb-3 text-gray-900">2. Бир йўла тўлов маълумотлари</label>
                        <p class="text-xs text-gray-600 mb-3">
                            <strong>Эслатма:</strong> Муддатсиз тўловда шартнома яратилмайди, фақат амалда тўланган сумма қайд қилинади.
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Факт тўланган сумма (сўм) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="actual_paid_amount" id="actualPaidAmount"
                            value="{{ old('actual_paid_amount', $lot->sold_price) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            min="0" oninput="validateMuddatsizAmount()">
                        <p class="text-xs text-gray-500 mt-1">Аукционда сотилган нарх: {{ number_format($lot->sold_price, 0, '.', ' ') }} сўм</p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Тўлов санаси <span class="text-red-500">*</span></label>
                        <input type="date" name="actual_payment_date" id="actualPaymentDate"
                            value="{{ old('actual_payment_date', now()->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            max="{{ now()->format('Y-m-d') }}">
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1 text-gray-700">Тўлов ҳужжат рақами</label>
                        <input type="text" name="reference_number"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Масалан: ТБ-2024-12345">
                    </div>

                    <div id="muddatsizDifferenceIndicator" class="hidden p-4 rounded-lg border">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-gray-700">Фарқ (Факт - Аукцион нархи):</span>
                            <span id="muddatsizDifferenceAmount" class="font-bold text-lg"></span>
                        </div>
                        <p id="muddatsizDifferenceMessage" class="text-sm"></p>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <p class="text-xs text-blue-800 flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span><strong>Эслатма:</strong> Муддатсиз тўлов учун шартнома яратилмайди. Факт тўланган сумма бевосита қайд қилинади.</span>
                        </p>
                    </div>
                </div>

                {{-- NOTE --}}
                <div id="muddatliNote" class="hidden">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-800 flex items-start gap-2">
                            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd" />
                            </svg>
<span><strong>Муҳим:</strong> Шартнома яратилгандан кейин, тўлов графигини қўлда қўшишингиз керак. Ҳар бир тўлов қаторини "+ Қўшиш" тугмаси орқали қўшинг.</span>
                        </p>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="border-t pt-4">
                    <button type="submit" id="submitBtn" disabled
                        class="w-full bg-gray-400 text-white px-4 py-3 rounded-lg text-sm font-semibold cursor-not-allowed transition-all">
                        Тўлов турини танланг
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-300 rounded-xl p-4">
            <p class="text-sm text-yellow-800 flex items-start gap-2">
                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                        clip-rule="evenodd" />
                </svg>
                <span><strong>Эслатма:</strong> Шартнома яратилмаган.</span>
            </p>
        </div>
    @endif
@endif

{{-- JavaScript --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contractForm = document.getElementById('contractCreationForm');
        if (!contractForm) return;

        const paymentTypeRadios = document.querySelectorAll('.payment-type-radio');
        const contractDetailsStep = document.getElementById('contractDetailsStep');
        const muddatsizPaymentStep = document.getElementById('muddatsizPaymentStep');
        const muddatliNote = document.getElementById('muddatliNote');
        const initialPaymentSection = document.getElementById('initialPaymentSection');
        const submitBtn = document.getElementById('submitBtn');

        function showElement(element) {
            if (element) {
                element.style.display = 'block';
                element.classList.remove('hidden');
            }
        }

        function hideElement(element) {
            if (element) {
                element.style.display = 'none';
                element.classList.add('hidden');
            }
        }

        paymentTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedType = this.value;

                document.querySelectorAll('.payment-type-card').forEach(card => {
                    const checkIcon = card.querySelector('.check-icon');
                    if (card.dataset.type === selectedType) {
                        card.classList.add('border-blue-500', 'bg-blue-50');
                        card.classList.remove('border-gray-300');
                        checkIcon.classList.remove('hidden');
                    } else {
                        card.classList.remove('border-blue-500', 'bg-blue-50');
                        card.classList.add('border-gray-300');
                        checkIcon.classList.add('hidden');
                    }
                });

                if (selectedType === 'muddatli') {
                    showElement(contractDetailsStep);
                    showElement(muddatliNote);
                    showElement(initialPaymentSection);
                    hideElement(muddatsizPaymentStep);

                    contractDetailsStep.querySelectorAll('input, select, textarea').forEach(field => {
                        const fieldName = field.getAttribute('name');
                        if (['contract_number', 'contract_date', 'contract_amount', 'buyer_name', 'status'].includes(fieldName)) {
                            field.setAttribute('required', 'required');
                        }
                    });

                    muddatsizPaymentStep.querySelectorAll('input, select').forEach(field => {
                        field.removeAttribute('required');
                    });

                    submitBtn.textContent = 'Шартнома яратиш';

                } else if (selectedType === 'muddatsiz') {
                    hideElement(contractDetailsStep);
                    hideElement(muddatliNote);
                    hideElement(initialPaymentSection);
                    showElement(muddatsizPaymentStep);

                    contractDetailsStep.querySelectorAll('input, select, textarea').forEach(field => {
                        field.removeAttribute('required');
                    });

                    const actualPaidInput = document.getElementById('actualPaidAmount');
                    const actualDateInput = document.getElementById('actualPaymentDate');
                    if (actualPaidInput) actualPaidInput.setAttribute('required', 'required');
                    if (actualDateInput) actualDateInput.setAttribute('required', 'required');

                    submitBtn.textContent = 'Тўловни қайд қилиш';
                }

                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                submitBtn.classList.add('bg-gradient-to-r', 'from-green-600', 'to-green-700', 'hover:from-green-700', 'hover:to-green-800', 'cursor-pointer');
            });
        });

        // Validate initial payment amount
        const contractAmountInput = document.getElementById('contractAmount');
        const initialPaidInput = document.getElementById('initialPaidAmount');

        if (initialPaidInput && contractAmountInput) {
            initialPaidInput.addEventListener('input', function() {
                const contractAmount = parseFloat(contractAmountInput.value) || 0;
                const initialPaid = parseFloat(this.value) || 0;

                if (initialPaid > contractAmount) {
                    this.setCustomValidity('Аввал тўланган сумма шартнома суммасидан кўп бўлолмайди');
                } else {
                    this.setCustomValidity('');
                }
            });

            contractAmountInput.addEventListener('input', function() {
                const contractAmount = parseFloat(this.value) || 0;
                const initialPaid = parseFloat(initialPaidInput.value) || 0;

                if (initialPaid > contractAmount) {
                    initialPaidInput.setCustomValidity('Аввал тўланган сумма шартнома суммасидан кўп бўлолмайди');
                } else {
                    initialPaidInput.setCustomValidity('');
                }
            });
        }
    });

    function validateMuddatsizAmount() {
        const auctionPrice = {{ $lot->sold_price ?? 0 }};
        const actualPaidInput = document.getElementById('actualPaidAmount');
        const indicator = document.getElementById('muddatsizDifferenceIndicator');
        const differenceAmount = document.getElementById('muddatsizDifferenceAmount');
        const differenceMessage = document.getElementById('muddatsizDifferenceMessage');

        if (!actualPaidInput || !indicator) return;

        const actualPaid = parseFloat(actualPaidInput.value) || 0;
        const difference = actualPaid - auctionPrice;

        if (actualPaid > 0 && actualPaid !== auctionPrice) {
            indicator.classList.remove('hidden');
            indicator.style.display = 'block';
            differenceAmount.textContent = new Intl.NumberFormat('uz-UZ').format(Math.abs(difference)) + ' сўм';

            if (difference > 0) {
                indicator.className = 'p-4 rounded-lg border bg-green-50 border-green-200';
                differenceAmount.className = 'font-bold text-lg text-green-700';
                differenceMessage.textContent = '✓ Аукцион нархидан ' + new Intl.NumberFormat('uz-UZ').format(difference) + ' сўм кўп тўланган';
                differenceMessage.className = 'text-sm text-green-700';
            } else {
                indicator.className = 'p-4 rounded-lg border bg-orange-50 border-orange-200';
                differenceAmount.className = 'font-bold text-lg text-orange-700';
                differenceMessage.textContent = '⚠ Аукцион нархидан ' + new Intl.NumberFormat('uz-UZ').format(Math.abs(difference)) + ' сўм кам тўланган';
                differenceMessage.className = 'text-sm text-orange-700';
            }
        } else {
            indicator.classList.add('hidden');
            indicator.style.display = 'none';
        }
    }
</script>
