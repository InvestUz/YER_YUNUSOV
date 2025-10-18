{{--
    File: resources/views/lots/partials/contract-card.blade.php
    Purpose: Contract status card or creation form with MANUAL payment schedule
--}}

@if($lot->contract)
{{-- ========================================
         EXISTING CONTRACT DISPLAY
         ======================================== --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-green-100 border-b border-green-200">
        <h3 class="font-bold text-green-900 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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
        {{-- Financial Summary Cards --}}
        <div class="grid grid-cols-3 gap-3">
            {{-- Contract Amount --}}
            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border border-blue-200">
                <p class="text-xs text-blue-700 font-medium mb-1">Шартнома</p>
                <p class="text-sm font-bold text-blue-900">
                    {{ number_format($lot->contract->contract_amount / 1000000, 1) }}М
                </p>
            </div>

            {{-- Paid Amount --}}
            <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg border border-green-200">
                <p class="text-xs text-green-700 font-medium mb-1">Тўланган</p>
                <p class="text-sm font-bold text-green-900">
                    {{ number_format($lot->contract->paid_amount / 1000000, 1) }}М
                </p>
            </div>

            {{-- Remaining Amount --}}
            <div class="p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border border-orange-200">
                <p class="text-xs text-orange-700 font-medium mb-1">Қолган</p>
                <p class="text-sm font-bold text-orange-900">
                    {{ number_format($lot->contract->remaining_amount / 1000000, 1) }}М
                </p>
            </div>
        </div>

        {{-- Progress Bar --}}
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

        {{-- View Details Button --}}
        <a href="{{ route('contracts.show', $lot->contract) }}"
            class="block w-full text-center px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white rounded-lg text-sm font-semibold transition-all shadow-sm hover:shadow-md">
            <span class="flex items-center justify-center gap-2">
                Батафсил кўриш
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        </a>
    </div>
</div>
@else
{{-- ========================================
         CONTRACT CREATION FORM (SIMPLIFIED - WITHOUT AUTO SCHEDULE)
         ======================================== --}}
@if(Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->tuman_id === $lot->tuman_id))
<div class="bg-white rounded-xl shadow-sm border-2 border-yellow-400 overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-yellow-50 to-yellow-100 border-b border-yellow-200">
        <h3 class="font-bold text-yellow-900 text-lg flex items-center gap-2">
            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Тўлов турини яратиш
        </h3>
        <p class="text-xs text-yellow-700 mt-1">Тизимда шартнома мавжуд эмас</p>
    </div>

    <form action="{{ route('contracts.store') }}" method="POST" class="p-6 space-y-4" id="contractCreationForm">
        @csrf
        <input type="hidden" name="lot_id" value="{{ $lot->id }}">

        {{-- STEP 1: Payment Type Selection --}}
        <div id="paymentTypeStep">


            <div class="space-y-3">
                {{-- Muddatli Option --}}
                <label class="relative flex cursor-pointer border-2 border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-all payment-type-card group"
                    data-type="muddatli">
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

                {{-- Muddatsiz Option --}}
                <label class="relative flex cursor-pointer border-2 border-gray-300 rounded-lg p-4 hover:border-blue-500 transition-all payment-type-card group"
                    data-type="muddatsiz">
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

        {{-- STEP 2: Contract Details (Hidden initially) --}}
        <div id="contractDetailsStep" class="hidden space-y-4">
            <div class="border-t pt-4">
                <label class="block text-sm font-bold mb-3 text-gray-900">2. Шартнома маълумотлари</label>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Шартнома рақами <span class="text-red-500">*</span></label>
                <input type="text" name="contract_number" required value="{{ old('contract_number') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Шартнома санаси <span class="text-red-500">*</span></label>
                <input type="date" name="contract_date" required value="{{ old('contract_date', now()->format('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Шартнома суммаси (сўм) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="contract_amount" required id="contractAmount"
                    value="{{ old('contract_amount', $lot->sold_price) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Харидор номи <span class="text-red-500">*</span></label>
                <input type="text" name="buyer_name" required value="{{ old('buyer_name', $lot->winner_name) }}"
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
        </div>

        {{-- STEP 3: Muddatsiz One-Time Payment (Conditional) --}}
        <div id="muddatsizPaymentStep" class="hidden space-y-4">
            <div class="border-t pt-4">
                <label class="block text-sm font-bold mb-3 text-gray-900">3. Бир йўла тўлов маълумотлари</label>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Тўлов суммаси (сўм) <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" name="one_time_payment_amount" id="oneTimeAmount"
                    value="{{ old('one_time_payment_amount', $lot->sold_price) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Тўлов муддати <span class="text-red-500">*</span></label>
                <input type="date" name="one_time_payment_date"
                    value="{{ old('one_time_payment_date', now()->addDays(30)->format('Y-m-d')) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                <p class="text-xs text-green-800 flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span><strong>Эслатма:</strong> Муддатсиз тўлов учун битта тўлов санаси белгиланади.</span>
                </p>
            </div>
        </div>

        {{-- STEP 3: Muddatli Payment Schedule - HIDDEN FIELDS FOR BACKEND --}}
        <div id="muddatliHiddenFields" class="hidden">
            {{-- Hidden fields to satisfy backend validation but won't be used --}}
            <input type="hidden" name="schedule_frequency" value="monthly">
            <input type="hidden" name="first_payment_date" value="{{ now()->addMonth()->format('Y-m-d') }}">
            <input type="hidden" name="number_of_payments" value="1">
        </div>

        {{-- NOTE: Manual schedule for Muddatli --}}
        <div id="muddatliNote" class="hidden">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800 flex items-start gap-2">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span><strong>Муҳим:</strong> Шартнома яратилгандан кейин, тўлов графигини қўлда қўшишингиз керак. Ҳар бир тўлов қаторини "+ Қўшиш" тугмаси орқали қўшинг.</span>
                </p>
            </div>
        </div>

        {{-- STEP 3: Additional Settings --}}
        <div id="additionalSettingsStep" class="hidden space-y-4">
            <div class="border-t pt-4">
                <label class="block text-sm font-bold mb-3 text-gray-900">3. Қўшимча созламалар</label>
            </div>

            <div>
                <label class="block text-xs font-medium mb-1 text-gray-700">Ҳолат <span class="text-red-500">*</span></label>
                <select name="status" required
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
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <span><strong>Эслатма:</strong> Шартнома яратилмаган.</span>
    </p>
</div>
@endif
@endif

{{-- JavaScript for Form Logic --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const contractForm = document.getElementById('contractCreationForm');

        if (!contractForm) return;

        const paymentTypeRadios = document.querySelectorAll('.payment-type-radio');
        const contractDetailsStep = document.getElementById('contractDetailsStep');
        const muddatsizPaymentStep = document.getElementById('muddatsizPaymentStep');
        const muddatliNote = document.getElementById('muddatliNote');
        const additionalSettingsStep = document.getElementById('additionalSettingsStep');
        const submitBtn = document.getElementById('submitBtn');

        const contractAmount = document.getElementById('contractAmount');
        const oneTimeAmount = document.getElementById('oneTimeAmount');

        // Payment type selection handler
        paymentTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const selectedType = this.value;

                // Update UI for selected card
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

                // Show contract details
                contractDetailsStep.classList.remove('hidden');
                additionalSettingsStep.classList.remove('hidden');

                // Show/hide specific payment type sections
                if (selectedType === 'muddatli') {
                    muddatsizPaymentStep.classList.add('hidden');
                    muddatliNote.classList.remove('hidden');

                    // Remove muddatsiz required
                    if (oneTimeAmount) oneTimeAmount.removeAttribute('required');

                } else if (selectedType === 'muddatsiz') {
                    muddatsizPaymentStep.classList.remove('hidden');
                    muddatliNote.classList.add('hidden');

                    // Make muddatsiz fields required
                    if (oneTimeAmount) oneTimeAmount.setAttribute('required', 'required');
                }

                // Enable submit button
                submitBtn.disabled = false;
                submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                submitBtn.classList.add('bg-gradient-to-r', 'from-green-600', 'to-green-700',
                    'hover:from-green-700', 'hover:to-green-800', 'cursor-pointer');
                submitBtn.textContent = 'Шартнома яратиш';
            });
        });

        // Sync contract amount with one-time payment
        if (contractAmount && oneTimeAmount) {
            contractAmount.addEventListener('input', function() {
                oneTimeAmount.value = this.value;
            });
        }
    });
</script>
