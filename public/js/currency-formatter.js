/**
 * Indian Currency and Number Formatting Utilities
 * Client-side JavaScript functions for consistent formatting
 */

/**
 * Format currency in Indian Rupees with Indian number formatting
 * @param {number} amount The amount to format
 * @param {number} decimals Number of decimal places (default: 2)
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount, decimals = 2) {
    return '₹' + formatIndianNumber(amount, decimals);
}

/**
 * Format numbers in Indian numbering system (lakhs, crores)
 * @param {number} number The number to format
 * @param {number} decimals Number of decimal places (default: 2)
 * @returns {string} Formatted number string
 */
function formatIndianNumber(number, decimals = 2) {
    if (number === 0) {
        return number.toFixed(decimals);
    }

    const isNegative = number < 0;
    number = Math.abs(number);

    // Format based on Indian numbering system
    let formatted;
    if (number >= 10000000) {
        // Crores
        formatted = (number / 10000000).toFixed(decimals) + ' Cr';
    } else if (number >= 100000) {
        // Lakhs
        formatted = (number / 100000).toFixed(decimals) + ' L';
    } else if (number >= 1000) {
        // Thousands with Indian comma placement
        formatted = formatWithIndianCommas(number, decimals);
    } else {
        formatted = number.toFixed(decimals);
    }

    return isNegative ? '-' + formatted : formatted;
}

/**
 * Format numbers with Indian comma placement (xx,xx,xxx)
 * @param {number} number The number to format
 * @param {number} decimals Number of decimal places
 * @returns {string} Formatted number string
 */
function formatWithIndianCommas(number, decimals = 2) {
    const fixed = number.toFixed(decimals);
    const parts = fixed.split('.');
    let integerPart = parts[0];
    const decimalPart = parts[1];

    // Add commas in Indian format
    const length = integerPart.length;
    if (length > 3) {
        const lastThree = integerPart.slice(-3);
        const remaining = integerPart.slice(0, -3);
        integerPart = remaining.replace(/\B(?=(\d{2})+(?!\d))/g, ',') + ',' + lastThree;
    }

    return decimalPart ? integerPart + '.' + decimalPart : integerPart;
}

/**
 * Format currency for display without denomination suffix
 * @param {number} amount The amount to format
 * @param {number} decimals Number of decimal places (default: 2)
 * @returns {string} Formatted currency string
 */
function formatCurrencySimple(amount, decimals = 2) {
    return '₹' + formatWithIndianCommas(amount, decimals);
}

/**
 * Parse Indian formatted number back to float
 * @param {string} formattedNumber The formatted number string
 * @returns {number} Parsed number
 */
function parseIndianNumber(formattedNumber) {
    // Remove currency symbol, spaces, and parse suffixes
    let cleanNumber = formattedNumber.replace(/[₹,\s]/g, '');

    if (cleanNumber.includes('Cr')) {
        return parseFloat(cleanNumber.replace('Cr', '')) * 10000000;
    } else if (cleanNumber.includes('L')) {
        return parseFloat(cleanNumber.replace('L', '')) * 100000;
    } else {
        return parseFloat(cleanNumber);
    }
}

// Export functions if using modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatCurrency,
        formatIndianNumber,
        formatWithIndianCommas,
        formatCurrencySimple,
        parseIndianNumber
    };
}
