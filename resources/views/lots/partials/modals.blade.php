@if($lot->contract && $lot->contract->payment_type === 'muddatli')
<div id="addScheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 animate-fadeIn">
    <div class="bg-white rounded-xl max-w-md w-full shadow-2xl animate-slideUp">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                График қўшиш
            </h3>
            <button onclick="closeAddScheduleModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <form action="{{ route('contracts.add-schedule-item', $lot->contract) }}" method="POST" class="p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700">
                    Тўлов санаси <span class="text-red-500">*</span>
                </label>
                <input type="date" name="planned_date" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700">
                    Тўлов суммаси (сўм) <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" name="planned_amount" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="0.00">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700">Муддат (deadline)</label>
                <input type="date" name="deadline_date"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            {{-- Modal Actions --}}
            <div class="flex gap-2 pt-4">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium transition-all shadow-sm hover:shadow-md">
                    Қўшиш
                </button>
                <button type="button" onclick="closeAddScheduleModal()"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                    Бекор
                </button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- MODAL: PAYMENT RECORDING --}}
@if($lot->contract)
<div id="paymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 animate-fadeIn">
    <div class="bg-white rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto shadow-2xl animate-slideUp">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 sticky top-0 bg-gradient-to-r from-green-50 to-green-100 z-10">
            <div>
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Тўлов қайд қилиш
                </h3>
                <p class="text-sm text-gray-600 mt-1" id="modalSubtitle">График бўйича тўлов</p>
            </div>
            <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="paymentForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            {{-- Schedule Information Card --}}
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h4 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    График маълумотлари
                </h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Тўлов рақами:</span>
                        <p class="font-medium text-gray-900" id="modalPaymentNumber">-</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Режа бўйича сана:</span>
                        <p class="font-medium text-gray-900" id="modalPlannedDate">-</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Режа бўйича сумма:</span>
                        <p class="font-bold text-blue-700" id="modalPlannedAmount">-</p>
                    </div>
                    <div>
                        <span class="text-gray-600">Охирги муддат:</span>
                        <p class="font-medium text-gray-900" id="modalDeadline">-</p>
                    </div>
                </div>
            </div>

            {{-- Payment Input Fields --}}
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            Тўлов санаси <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="actual_date" id="paymentDate" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               max="{{ date('Y-m-d') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2 text-gray-700">
                            Тўланган сумма (сўм) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" name="actual_amount" id="paymentAmount" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               min="0" oninput="calculateDifference()">
                    </div>
                </div>

                {{-- Difference Indicator --}}
                <div id="differenceIndicator" class="hidden p-4 rounded-lg transition-all">
                    <div class="flex items-center justify-between mb-2">
                        <span class="font-medium text-gray-700">Фарқ:</span>
                        <span id="differenceAmount" class="font-bold text-lg"></span>
                    </div>
                    <div class="text-sm" id="differenceMessage"></div>
                </div>


                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Тўлов ҳужжат рақами</label>
                    <input type="text" name="reference_number"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="Масалан: ТБ-2024-12345">
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2 text-gray-700">Изоҳ</label>
                    <textarea name="note" rows="3"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="Қўшимча маълумот..."></textarea>
                </div>
            </div>

            {{-- Warning Messages Container --}}
            <div id="warningMessages" class="space-y-2"></div>

            {{-- Modal Actions --}}
            <div class="flex gap-2 pt-4 border-t border-gray-200">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white px-4 py-3 rounded-lg hover:from-green-700 hover:to-green-800 font-semibold transition-all shadow-sm hover:shadow-md">
                    <span class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Тўловни сақлаш
                    </span>
                </button>
                <button type="button" onclick="closePaymentModal()"
                        class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                    Бекор қилиш
                </button>
            </div>
        </form>
    </div>
</div>
@endif
