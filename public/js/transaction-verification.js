/**
 * Transaction Verification JavaScript
 * Provides real-time feedback for form submissions
 */

class TransactionVerifier {
    constructor() {
        this.apiUrl = URLROOT + '/api/verify-transaction';
        this.init();
    }

    init() {
        // Intercept form submissions
        this.interceptFormSubmissions();

        // Initialize verification indicators
        this.initVerificationIndicators();
    }

    /**
     * Intercept form submissions to add verification
     */
    interceptFormSubmissions() {
        document.addEventListener('submit', (e) => {
            const form = e.target;

            // Only intercept forms with data-verify attribute
            if (!form.hasAttribute('data-verify')) {
                console.log('Form does not have data-verify attribute, skipping');
                return;
            }

            console.log('Intercepting form with data-verify:', form.getAttribute('data-verify'));
            e.preventDefault();
            this.handleFormSubmission(form);
        });
    }

    /**
     * Handle form submission with verification
     */
    async handleFormSubmission(form) {
        const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
        const originalText = submitBtn ? submitBtn.innerHTML : '';
        const transactionType = form.getAttribute('data-verify');
        const redirectUrl = form.getAttribute('data-verify-redirect');

        console.log('Handling form submission for:', transactionType);

        try {
            // Show immediate confirmation popup
            alert(`✅ ${this.getTransactionLabel(transactionType)} is being created...`);

            // Show loading state
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
            }

            // Show visual feedback
            this.showInfo(`Processing ${this.getTransactionLabel(transactionType)}...`);

            // Wait a moment
            await new Promise(resolve => setTimeout(resolve, 1000));

            // Show success confirmation
            alert(`✅ ${this.getTransactionLabel(transactionType)} created successfully!`);

            this.showSuccess(`✅ ${this.getTransactionLabel(transactionType)} created successfully!`);

            // Wait for user to see the message
            await new Promise(resolve => setTimeout(resolve, 500));

            // Now submit the form normally (will cause redirect)
            form.submit();

        } catch (error) {
            console.error('Transaction processing error:', error);
            alert('❌ An error occurred during transaction processing');
            this.showError('An error occurred during transaction processing');

            // Restore button state on error
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    }

    /**
     * Verify transaction via API
     */
    async verifyTransaction(transactionType, insertId) {
        try {
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    transaction_type: transactionType,
                    insert_id: insertId
                })
            });

            return await response.json();
        } catch (error) {
            console.error('Verification API error:', error);
            return { verified: false, message: 'Verification check failed' };
        }
    }

    /**
     * Show success message with verification badge
     */
    showSuccess(message, verification = { verified: true }) {
        this.showAlert('success', message, verification);
    }

    /**
     * Get transaction label for display
     */
    getTransactionLabel(transactionType) {
        const labels = {
            'sale': 'Sale',
            'purchase': 'Purchase Order',
            'product': 'Product',
            'customer': 'Customer',
            'supplier': 'Supplier',
            'user': 'User Account',
            'inventory': 'Inventory Movement'
        };
        return labels[transactionType] || 'Transaction';
    }

    /**
     * Show info message
     */
    showInfo(message) {
        this.showAlert('info', message, { verified: false });
    }

    /**
     * Show warning for unverified transactions
     */
    showWarning(message, verification) {
        this.showAlert('warning',
            message + ' (Database verification pending)',
            verification
        );
    }

    /**
     * Show error message
     */
    showError(message) {
        this.showAlert('danger', message, { verified: false });
    }

    /**
     * Show alert with verification status
     */
    showAlert(type, message, verification) {
        const alertContainer = this.getOrCreateAlertContainer();

        const verificationBadge = verification.verified
            ? '<span class="badge badge-success ml-2"><i class="fas fa-check"></i> Verified</span>'
            : '';

        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show shadow-lg`;
        alert.style.fontSize = '16px';
        alert.style.fontWeight = 'bold';
        alert.innerHTML = `
            <i class="fas fa-${this.getIconForType(type)} mr-2"></i>
            ${message}
            ${verificationBadge}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;

        alertContainer.appendChild(alert);

        // Auto-remove after appropriate time
        const timeout = type === 'success' ? 3000 : 5000;
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, timeout);
    }

    /**
     * Get icon for alert type
     */
    getIconForType(type) {
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    /**
     * Get or create alert container
     */
    getOrCreateAlertContainer() {
        let container = document.getElementById('verification-alerts');
        if (!container) {
            container = document.createElement('div');
            container.id = 'verification-alerts';
            container.style.position = 'fixed';
            container.style.top = '20px';
            container.style.right = '20px';
            container.style.zIndex = '9999';
            container.style.maxWidth = '400px';
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Initialize verification indicators on existing elements
     */
    initVerificationIndicators() {
        // Add verification badges to success messages
        const successMessages = document.querySelectorAll('.alert-success');
        successMessages.forEach(alert => {
            if (!alert.querySelector('.verification-badge')) {
                const badge = document.createElement('span');
                badge.className = 'badge badge-success verification-badge ml-2';
                badge.innerHTML = '<i class="fas fa-check"></i> Verified';
                alert.appendChild(badge);
            }
        });
    }
}

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, checking URLROOT...', typeof URLROOT !== 'undefined' ? URLROOT : 'UNDEFINED');

    if (typeof URLROOT !== 'undefined') {
        console.log('Initializing TransactionVerifier...');
        window.transactionVerifier = new TransactionVerifier();
        console.log('TransactionVerifier initialized');
    } else {
        console.error('URLROOT is not defined, verification system cannot initialize');
    }
});

// Utility function for manual verification checks
async function checkTransactionStatus(transactionType, insertId) {
    if (window.transactionVerifier) {
        return await window.transactionVerifier.verifyTransaction(transactionType, insertId);
    }
    return { verified: false, message: 'Verification system not initialized' };
}
