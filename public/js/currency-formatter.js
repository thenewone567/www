/**
 * Currency Formatter Utility
 * Provides formatting functions for currency display
 */

// Format currency with proper symbols and decimals
function formatCurrency(amount, decimals = 2, currencySymbol = '$') {
    if (isNaN(amount) || amount === null || amount === undefined) {
        return currencySymbol + '0.00';
    }

    const num = parseFloat(amount);
    return currencySymbol + num.toFixed(decimals).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Parse currency string to number
function parseCurrency(currencyString) {
    if (!currencyString || typeof currencyString !== 'string') {
        return 0;
    }

    // Remove currency symbols and commas
    const cleaned = currencyString.replace(/[$,]/g, '');
    const num = parseFloat(cleaned);
    return isNaN(num) ? 0 : num;
}

// Format currency input fields
function initializeCurrencyFields() {
    $('.currency-input').on('input', function () {
        let value = $(this).val().replace(/[^\d.]/g, '');
        let num = parseFloat(value);

        if (!isNaN(num)) {
            $(this).val(num.toFixed(2));
        }
    });
}

// Auto-initialize when DOM is ready
$(document).ready(function () {
    initializeCurrencyFields();
});
