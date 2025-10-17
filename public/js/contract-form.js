/**
 * Contract Form Handler
 * Manages multi-step contract creation form
 */
document.addEventListener('DOMContentLoaded', function() {
    const contractForm = document.getElementById('contractCreationForm');

    if (!contractForm) return;

    // Get form elements
    const paymentTypeRadios = document.querySelectorAll('.payment-type-radio');
    const contractDetailsStep = document.getElementById('contractDetailsStep');
    const muddatliScheduleStep = document.getElementById('muddatliScheduleStep');
    const muddatsizPaymentStep = document.getElementById('muddatsizPaymentStep');
    const additionalSettingsStep = document.getElementById('additionalSettingsStep');
    const submitBtn = document.getElementById('submitBtn');

    const contractAmount = document.getElementById('contractAmount');
    const scheduleFrequency = document.getElementById('scheduleFrequency');
    const numberOfPayments = document.getElementById('numberOfPayments');
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
                muddatliScheduleStep.classList.remove('hidden');
                muddatsizPaymentStep.classList.add('hidden');

                // Make muddatli fields required
                scheduleFrequency.setAttribute('required', 'required');
                document.getElementById('firstPaymentDate').setAttribute('required', 'required');
                numberOfPayments.setAttribute('required', 'required');

                // Remove muddatsiz required
                if (oneTimeAmount) oneTimeAmount.removeAttribute('required');

                updatePaymentPreview();
            } else if (selectedType === 'muddatsiz') {
                muddatliScheduleStep.classList.add('hidden');
                muddatsizPaymentStep.classList.remove('hidden');

                // Make muddatsiz fields required
                if (oneTimeAmount) oneTimeAmount.setAttribute('required', 'required');

                // Remove muddatli required
                scheduleFrequency.removeAttribute('required');
                document.getElementById('firstPaymentDate').removeAttribute('required');
                numberOfPayments.removeAttribute('required');
            }

            // Enable submit button
            submitBtn.disabled = false;
            submitBtn.classList.remove('bg-gray-400', 'cursor-not-allowed');
            submitBtn.classList.add('bg-gradient-to-r', 'from-green-600', 'to-green-700',
                                   'hover:from-green-700', 'hover:to-green-800', 'cursor-pointer');
            submitBtn.textContent = 'Шартнома яратиш';
        });
    });

    // Update payment preview for Muddatli
    function updatePaymentPreview() {
        const amount = parseFloat(contractAmount?.value) || 0;
        const payments = parseInt(numberOfPayments?.value) || 1;
        const paymentAmount = amount / payments;

        const previewAmount = document.getElementById('previewPaymentAmount');
        const previewTotal = document.getElementById('previewTotalPayments');
        const preview = document.getElementById('paymentPreview');

        if (previewAmount && previewTotal && preview) {
            previewAmount.textContent = paymentAmount.toLocaleString('uz-UZ', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }) + ' сўм';

            previewTotal.textContent = payments;
            preview.classList.remove('hidden');
        }
    }

    // Listen to changes for payment preview
    [contractAmount, scheduleFrequency, numberOfPayments].forEach(el => {
        if (el) {
            el.addEventListener('input', updatePaymentPreview);
            el.addEventListener('change', updatePaymentPreview);
        }
    });

    // Sync contract amount with one-time payment
    if (contractAmount && oneTimeAmount) {
        contractAmount.addEventListener('input', function() {
            oneTimeAmount.value = this.value;
        });
    }
});
