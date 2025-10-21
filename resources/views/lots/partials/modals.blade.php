{{-- MODAL: EDIT PAYMENT SCHEDULE --}}
@if($lot->contract && $lot->contract->payment_type === 'muddatli')
<div id="editScheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 animate-fadeIn">
    <div class="bg-white rounded-xl max-w-md w-full shadow-2xl animate-slideUp">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-blue-100">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                График таҳрирлаш
            </h3>
            <button onclick="closeEditScheduleModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <form id="editScheduleForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')

            {{-- Schedule Information Display --}}
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                <div class="text-sm">
                    <span class="text-gray-600">Тўлов рақами:</span>
                    <span class="font-medium text-gray-900 ml-2" id="editPaymentNumber">-</span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700">
                    Режа бўйича сана <span class="text-red-500">*</span>
                </label>
                <input type="date" name="planned_date" id="editPlannedDate" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700">
                    Охирги муддат
                </label>
                <input type="date" name="deadline_date" id="editDeadlineDate"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium mb-2 text-gray-700">
                    Режа бўйича сумма (сўм) <span class="text-red-500">*</span>
                </label>
                <input type="number" step="0.01" name="planned_amount" id="editPlannedAmount" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="0.00" min="0">
            </div>

            {{-- Warning if payment already recorded --}}
            <div id="editPaymentWarning" class="hidden bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="text-sm text-yellow-800">
                        <p class="font-medium">Диққат: Бу график бўйича тўлов қайд қилинган</p>
                        <p class="mt-1">Тўланган сумма: <span id="editActualAmount" class="font-semibold">-</span></p>
                    </div>
                </div>
            </div>

            {{-- Modal Actions --}}
            <div class="flex gap-2 pt-4 border-t border-gray-200">
                <button type="submit"
                        class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 text-white px-4 py-2 rounded-lg hover:from-blue-700 hover:to-blue-800 font-medium transition-all shadow-sm hover:shadow-md">
                    Сақлаш
                </button>
                <button type="button" onclick="closeEditScheduleModal()"
                        class="px-6 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                    Бекор
                </button>
            </div>
        </form>
    </div>
</div>

{{-- DELETE CONFIRMATION MODAL --}}
<div id="deleteScheduleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 animate-fadeIn">
    <div class="bg-white rounded-xl max-w-md w-full shadow-2xl animate-slideUp">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-red-50 to-red-100">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                График қаторини ўчириш
            </h3>
            <button onclick="closeDeleteScheduleModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <p class="text-gray-700 mb-3">Сиз ҳақиқатан ҳам ушбу график қаторини ўчирмоқчимисиз?</p>
                <div class="text-sm text-gray-600">
                    <p>• Тўлов рақами: <span id="deletePaymentNumber" class="font-medium">-</span></p>
                    <p>• Режа бўйича сана: <span id="deletePlannedDate" class="font-medium">-</span></p>
                    <p>• Сумма: <span id="deletePlannedAmount" class="font-medium">-</span></p>
                </div>
            </div>

            <form id="deleteScheduleForm" method="POST">
                @csrf
                @method('DELETE')

                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-red-600 to-red-700 text-white px-4 py-3 rounded-lg hover:from-red-700 hover:to-red-800 font-semibold transition-all shadow-sm hover:shadow-md">
                        Ҳа, ўчириш
                    </button>
                    <button type="button" onclick="closeDeleteScheduleModal()"
                            class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                        Бекор қилиш
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
// ===== EDIT SCHEDULE MODAL =====
function openEditScheduleModal(scheduleId, paymentNumber, plannedDate, deadlineDate, plannedAmount, actualAmount) {
    const modal = document.getElementById('editScheduleModal');
    const form = document.getElementById('editScheduleForm');

    // Set form action
    form.action = `/payment-schedules/${scheduleId}`;

    // Populate fields
    document.getElementById('editPaymentNumber').textContent = paymentNumber;
    document.getElementById('editPlannedDate').value = plannedDate;
    document.getElementById('editDeadlineDate').value = deadlineDate || plannedDate;
    document.getElementById('editPlannedAmount').value = plannedAmount;

    // Show warning if payment recorded
    if (actualAmount > 0) {
        document.getElementById('editPaymentWarning').classList.remove('hidden');
        document.getElementById('editActualAmount').textContent = new Intl.NumberFormat('uz-UZ').format(actualAmount) + ' сўм';
    } else {
        document.getElementById('editPaymentWarning').classList.add('hidden');
    }

    modal.classList.remove('hidden');
}

function closeEditScheduleModal() {
    document.getElementById('editScheduleModal').classList.add('hidden');
}

// ===== DELETE SCHEDULE MODAL =====
function openDeleteScheduleModal(scheduleId, paymentNumber, plannedDate, plannedAmount) {
    const modal = document.getElementById('deleteScheduleModal');
    const form = document.getElementById('deleteScheduleForm');

    // Set form action
    form.action = `/payment-schedules/${scheduleId}`;

    // Populate info
    document.getElementById('deletePaymentNumber').textContent = paymentNumber;
    document.getElementById('deletePlannedDate').textContent = new Date(plannedDate).toLocaleDateString('uz-UZ');
    document.getElementById('deletePlannedAmount').textContent = new Intl.NumberFormat('uz-UZ').format(plannedAmount) + ' сўм';

    modal.classList.remove('hidden');
}

function closeDeleteScheduleModal() {
    document.getElementById('deleteScheduleModal').classList.add('hidden');
}

// Close modals on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditScheduleModal();
        closeDeleteScheduleModal();
    }
});

// Close modals on backdrop click
document.getElementById('editScheduleModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeEditScheduleModal();
});

document.getElementById('deleteScheduleModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeDeleteScheduleModal();
});
</script>

{{-- MODAL: CLEAR PAYMENT (Admin Only) --}}
@if(auth()->user()->role === 'admin' && $lot->contract)
<div id="clearPaymentModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 animate-fadeIn">
    <div class="bg-white rounded-xl max-w-md w-full shadow-2xl animate-slideUp">
        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-orange-100">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                Тўловни бекор қилиш
            </h3>
            <button onclick="closeClearPaymentModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6">
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-4">
                <div class="flex items-start gap-3 mb-3">
                    <svg class="w-6 h-6 text-orange-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-orange-900 mb-2">Диққат! Бу амал тўлов маълумотларини ўчиради:</p>
                        <ul class="text-sm text-orange-800 space-y-1">
                            <li>• Тўланган сумма 0 га тенг бўлади</li>
                            <li>• Тўлов санаси ўчирилади</li>
                            <li>• Статус "Кутилмоқда" га ўзгаради</li>
                            <li>• Шартнома ва лот суммалари қайта ҳисобланади</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded p-3 text-sm text-gray-700">
                    <p class="font-medium mb-2">График маълумотлари:</p>
                    <div class="space-y-1">
                        <p>• Тўлов рақами: <span id="clearPaymentNumber" class="font-semibold">-</span></p>
                        <p>• Режа бўйича сана: <span id="clearPlannedDate" class="font-semibold">-</span></p>
                        <p>• Тўланган сумма: <span id="clearActualAmount" class="font-semibold text-red-600">-</span></p>
                    </div>
                </div>
            </div>

            {{-- Warning about distributions --}}
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-4">
                <p class="text-sm text-red-800 font-medium">
                    <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    Агар тақсимот мавжуд бўлса, аввал уни ўчиринг!
                </p>
            </div>

            <form id="clearPaymentForm" method="POST">
                @csrf

                <div class="flex gap-2">
                    <button type="submit"
                            class="flex-1 bg-gradient-to-r from-orange-600 to-orange-700 text-white px-4 py-3 rounded-lg hover:from-orange-700 hover:to-orange-800 font-semibold transition-all shadow-sm hover:shadow-md">
                        Ҳа, бекор қилиш
                    </button>
                    <button type="button" onclick="closeClearPaymentModal()"
                            class="px-6 py-3 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium transition-colors">
                        Ёпиш
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
// ===== CLEAR PAYMENT MODAL =====
function openClearPaymentModal(scheduleId, paymentNumber, plannedDate, actualAmount) {
    const modal = document.getElementById('clearPaymentModal');
    const form = document.getElementById('clearPaymentForm');

    // Set form action
    form.action = `/payment-schedules/${scheduleId}/clear-payment`;

    // Populate info
    document.getElementById('clearPaymentNumber').textContent = paymentNumber;
    document.getElementById('clearPlannedDate').textContent = new Date(plannedDate).toLocaleDateString('uz-UZ');
    document.getElementById('clearActualAmount').textContent = new Intl.NumberFormat('uz-UZ').format(actualAmount) + ' сўм';

    modal.classList.remove('hidden');
}

function closeClearPaymentModal() {
    document.getElementById('clearPaymentModal').classList.add('hidden');
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeClearPaymentModal();
    }
});

// Close modal on backdrop click
document.getElementById('clearPaymentModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeClearPaymentModal();
});
</script>

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
