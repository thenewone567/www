/**
 * Standardized Form Enhancement Module
 * Provides auto-capitalization, validation, and formatting for all forms
 */

class FormEnhancements {
    constructor() {
        this.init();
    }

    init() {
        document.addEventListener('DOMContentLoaded', () => {
            this.setupAutoCapitalization();
            this.setupAutoFormatting();
            this.setupValidationHelpers();
        });
    }

    /**
     * Setup auto-capitalization for name fields
     */
    setupAutoCapitalization() {
        // Fields that should be auto-capitalized (names, titles, etc.)
        const capitalizeFields = [
            'input[name*="name"]',
            'input[name*="title"]',
            'input[name*="contact_person"]',
            'input[name*="supplier_name"]',
            'input[name*="customer_name"]',
            'input[name*="product_name"]',
            'input[name*="category_name"]',
            'input[name*="brand_name"]',
            'input[name*="first_name"]',
            'input[name*="last_name"]',
            'input[name*="company"]',
            'input[name*="organization"]'
        ];

        capitalizeFields.forEach(selector => {
            const fields = document.querySelectorAll(selector);
            fields.forEach(field => this.addCapitalizationEvents(field));
        });
    }

    /**
     * Setup auto-formatting for specific field types
     */
    setupAutoFormatting() {
        // Auto-uppercase fields
        const uppercaseFields = [
            'input[name*="gst"]',
            'input[name*="pan"]',
            'input[name*="code"]',
            'input[name*="sku"]',
            'input[name*="barcode"]'
        ];

        uppercaseFields.forEach(selector => {
            const fields = document.querySelectorAll(selector);
            fields.forEach(field => this.addUppercaseEvents(field));
        });

        // Auto-format phone numbers
        const phoneFields = document.querySelectorAll('input[type="tel"], input[name*="phone"]');
        phoneFields.forEach(field => this.addPhoneFormatting(field));

        // Auto-format email fields
        const emailFields = document.querySelectorAll('input[type="email"], input[name*="email"]');
        emailFields.forEach(field => this.addEmailFormatting(field));
    }

    /**
     * Setup validation helpers
     */
    setupValidationHelpers() {
        // GST number validation
        const gstFields = document.querySelectorAll('input[name*="gst"]');
        gstFields.forEach(field => this.addGSTValidation(field));

        // Email validation
        const emailFields = document.querySelectorAll('input[type="email"]');
        emailFields.forEach(field => this.addEmailValidation(field));

        // Phone validation
        const phoneFields = document.querySelectorAll('input[type="tel"]');
        phoneFields.forEach(field => this.addPhoneValidation(field));
    }

    /**
     * Add capitalization events to a field
     */
    addCapitalizationEvents(field) {
        // Capitalize on blur
        field.addEventListener('blur', () => {
            if (field.value) {
                field.value = this.capitalizeWords(field.value);
            }
        });

        // Real-time capitalization
        field.addEventListener('input', () => {
            if (field.value) {
                const cursorPos = field.selectionStart;
                field.value = this.capitalizeWords(field.value);
                field.setSelectionRange(cursorPos, cursorPos);
            }
        });
    }

    /**
     * Add uppercase events to a field
     */
    addUppercaseEvents(field) {
        // Uppercase on blur
        field.addEventListener('blur', () => {
            if (field.value) {
                field.value = field.value.toUpperCase();
            }
        });

        // Real-time uppercase
        field.addEventListener('input', () => {
            if (field.value) {
                const cursorPos = field.selectionStart;
                field.value = field.value.toUpperCase();
                field.setSelectionRange(cursorPos, cursorPos);
            }
        });
    }

    /**
     * Add phone number formatting
     */
    addPhoneFormatting(field) {
        field.addEventListener('input', () => {
            let value = field.value.replace(/\D/g, ''); // Remove non-digits

            // Format for Indian numbers
            if (value.length >= 10) {
                if (value.startsWith('91') && value.length <= 12) {
                    value = '+91 ' + value.substring(2, 7) + ' ' + value.substring(7);
                } else if (value.length === 10) {
                    value = value.substring(0, 5) + ' ' + value.substring(5);
                }
            }

            field.value = value;
        });
    }

    /**
     * Add email formatting (lowercase)
     */
    addEmailFormatting(field) {
        field.addEventListener('blur', () => {
            if (field.value) {
                field.value = field.value.toLowerCase().trim();
            }
        });
    }

    /**
     * Add GST validation
     */
    addGSTValidation(field) {
        field.addEventListener('blur', () => {
            if (field.value && !this.isValidGST(field.value)) {
                this.showFieldError(field, 'Invalid GST number format');
            } else {
                this.clearFieldError(field);
            }
        });
    }

    /**
     * Add email validation
     */
    addEmailValidation(field) {
        field.addEventListener('blur', () => {
            if (field.value && !this.isValidEmail(field.value)) {
                this.showFieldError(field, 'Invalid email format');
            } else {
                this.clearFieldError(field);
            }
        });
    }

    /**
     * Add phone validation
     */
    addPhoneValidation(field) {
        field.addEventListener('blur', () => {
            if (field.value && !this.isValidPhone(field.value)) {
                this.showFieldError(field, 'Invalid phone number');
            } else {
                this.clearFieldError(field);
            }
        });
    }

    /**
     * Utility Functions
     */
    capitalizeWords(str) {
        return str.toLowerCase().replace(/\b\w/g, letter => letter.toUpperCase());
    }

    isValidGST(gst) {
        const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;
        return gstRegex.test(gst);
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidPhone(phone) {
        const phoneRegex = /^(\+91[\-\s]?)?[0]?(91)?[789]\d{9}$/;
        return phoneRegex.test(phone.replace(/\s/g, ''));
    }

    showFieldError(field, message) {
        this.clearFieldError(field);

        field.classList.add('is-invalid');

        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback validation-error';
        errorDiv.textContent = message;

        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');

        const existingError = field.parentNode.querySelector('.validation-error');
        if (existingError) {
            existingError.remove();
        }
    }
}

// Initialize form enhancements
const formEnhancements = new FormEnhancements();

// Export for manual initialization if needed
window.FormEnhancements = FormEnhancements;
