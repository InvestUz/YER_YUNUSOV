/**
 * Modal Functions
 * Handle opening/closing modals and payment calculations
 */

// Open Add Schedule Modal
function openAddScheduleModal() {
    const modal = document.getElementById('addScheduleModal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

// Close Add Schedule Modal
function closeAddScheduleModal() {
    const modal = document.getElementById('addScheduleModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Open Payment Modal
function openPaymentModal(scheduleId, plannedDate, plannedAmount, paymentNumber = '', deadline = '') {
    const modal = document.getElementById('paymentModal');
    const form = document.getElementById('paymentForm');

    if (!modal || !form) return;

    // Set form action
    form.action = `/payment-schedules/${scheduleId}`;

    // Update modal info
    document.getElementById('modalPaymentNumber').textContent = paymentNumber || '-';
    document.getElementById('modalPlannedDate').textContent = formatDate(plannedDate);
    document.getElementById('modalPlannedAmount').textContent = formatCurrency(plannedAmount);
    document.getElementById('modalDeadline').textContent = deadline ? formatDate(deadline) : '-';

    // Pre-fill form
    document.getElementById('paymentDate').value = new Date().toISOString().split('T')[0];
    document.getElementById('paymentAmount').value = plannedAmount;

    // Store planned amount for calculations
    form.dataset.plannedAmount = plannedAmount;
    form.dataset.plannedDate = plannedDate;
    form.dataset.deadline = deadline;

    // Calculate initial difference
    calculateDifference();

    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';

    // Focus on date input
    setTimeout(() => document.getElementById('paymentDate')?.focus(), 100);
}

// Close Payment Modal
function closePaymentModal() {
    const modal = document.getElementById('paymentModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Calculate payment difference
function calculateDifference() {
    const form = document.getElementById('paymentForm');
    const actualAmount = parseFloat(document.getElementById('paymentAmount')?.value) || 0;
    const plannedAmount = parseFloat(form?.dataset.plannedAmount) || 0;
    const difference = actualAmount - plannedAmount;

    const indicator = document.getElementById('differenceIndicator');
    const amountEl = document.getElementById('differenceAmount');
    const messageEl = document.getElementById('differenceMessage');
    const warningsEl = document.getElementById('warningMessages');

    if (!indicator || !amountEl || !messageEl) return;

    // Clear previous warnings
    if (warningsEl) warningsEl.innerHTML = '';

    if (actualAmount > 0) {
        indicator.classList.remove('hidden');

        if (difference > 0) {
            // Overpayment
            indicator.className = 'p-4 rounded-lg bg-green-50 border border-green-200';
            amountEl.textContent = '+' + formatCurrency(Math.abs(difference));
            amountEl.className = 'font-bold text-lg text-green-700';
            messageEl.textContent = 'Режадан кўп тўланди';
            messageEl.className = 'text-sm text-green-700';

            if (difference > plannedAmount * 0.5) {
                addWarning('Диққат: Тўлов суммаси режадан жуда кўп!', 'warning');
            }
        } else if (difference < 0) {
            // Underpayment
            indicator.className = 'p-4 rounded-lg bg-orange-50 border border-orange-200';
            amountEl.textContent = formatCurrency(Math.abs(difference));
            amountEl.className = 'font-bold text-lg text-orange-700';
            messageEl.textContent = 'Режадан кам тўланди';
            messageEl.className = 'text-sm text-orange-700';

            const percentage = (actualAmount / plannedAmount) * 100;
            if (percentage < 50) {
                addWarning(`Фақат ${percentage.toFixed(1)}% тўланди. Қисман тўлов сифатида қайд этилади.`, 'info');
            }
        } else {
            // Exact payment
            indicator.className = 'p-4 rounded-lg bg-blue-50 border border-blue-200';
            amountEl.textContent = formatCurrency(0);
            amountEl.className = 'font-bold text-lg text-blue-700';
            messageEl.textContent = 'Режа бўйича аниқ тўланди';
            messageEl.className = 'text-sm text-blue-700';
        }

        // Check if payment is late
        const deadline = form?.dataset.deadline;
        const paymentDate = document.getElementById('paymentDate')?.value;

        if (deadline && paymentDate && paymentDate > deadline) {
            const daysLate = Math.ceil((new Date(paymentDate) - new Date(deadline)) / (1000 * 60 * 60 * 24));
            addWarning(`Тўлов ${daysLate} кун кечикди`, 'warning');
        }
    } else {
        indicator.classList.add('hidden');
    }
}

// Add warning message
function addWarning(message, type = 'warning') {
    const warningsEl = document.getElementById('warningMessages');
    if (!warningsEl) return;

    const colors = {
        'warning': 'bg-yellow-50 border-yellow-400 text-yellow-800',
        'info': 'bg-blue-50 border-blue-400 text-blue-800',
        'error': 'bg-red-50 border-red-400 text-red-800'
    };

    const warning = document.createElement('div');
    warning.className = `p-3 rounded-lg border ${colors[type] || colors.warning}`;
    warning.innerHTML = `
        <div class="flex items-start gap-2">
            <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm">${message}</span>
        </div>
    `;

    warningsEl.appendChild(warning);
}

// Format currency
function formatCurrency(amount) {
    return parseFloat(amount).toLocaleString('uz-UZ', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' сўм';
}

// Format date
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('uz-UZ', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

// Form validation before submit
document.getElementById('paymentForm')?.addEventListener('submit', function(e) {
    const actualAmount = parseFloat(document.getElementById('paymentAmount')?.value) || 0;
    const plannedAmount = parseFloat(this.dataset.plannedAmount) || 0;

    if (actualAmount === 0) {
        e.preventDefault();
        alert('Тўлов суммасини киритинг');
        return false;
    }

    if (actualAmount > plannedAmount * 2) {
        if (!confirm('Тўлов суммаси режадан 2 баравар кўп. Давом этмоқчимисиз?')) {
            e.preventDefault();
            return false;
        }
    }

    return true;
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize gallery
    if (window.lotData && window.lotData.images) {
        window.lotGallery = new LotGallery(window.lotData.images);
    }

    // Initialize interactions
    if (window.lotData) {
        window.lotInteractions = new LotInteractions(
            window.lotData.id,
            window.lotData.csrfToken
        );
    }
});
