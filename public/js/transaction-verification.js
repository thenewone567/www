/**
 * Transaction Verification System
 * Provides security and validation for transactions
 */

// Transaction verification class
class TransactionVerifier {
    constructor() {
        this.pendingTransactions = new Map();
        this.verificationTimeout = 30000; // 30 seconds
    }

    // Generate verification token
    generateToken() {
        return Math.random().toString(36).substring(2, 15) +
            Math.random().toString(36).substring(2, 15);
    }

    // Verify transaction before submission
    verifyTransaction(formData, callback) {
        const token = this.generateToken();
        const transactionId = Date.now().toString();

        this.pendingTransactions.set(transactionId, {
            token: token,
            data: formData,
            timestamp: Date.now(),
            callback: callback
        });

        // Auto-cleanup after timeout
        setTimeout(() => {
            this.pendingTransactions.delete(transactionId);
        }, this.verificationTimeout);

        return { transactionId, token };
    }

    // Complete verified transaction
    completeTransaction(transactionId, token) {
        const transaction = this.pendingTransactions.get(transactionId);

        if (!transaction) {
            throw new Error('Transaction not found or expired');
        }

        if (transaction.token !== token) {
            throw new Error('Invalid transaction token');
        }

        // Execute callback with verified data
        if (transaction.callback) {
            transaction.callback(transaction.data);
        }

        // Clean up
        this.pendingTransactions.delete(transactionId);
        return true;
    }
}

// Global instance
window.transactionVerifier = new TransactionVerifier();

// Form verification helper
function verifyForm(form, callback) {
    const formData = new FormData(form);
    const verification = window.transactionVerifier.verifyTransaction(formData, callback);

    // Add verification fields to form
    const tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = 'verification_token';
    tokenInput.value = verification.token;
    form.appendChild(tokenInput);

    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'transaction_id';
    idInput.value = verification.transactionId;
    form.appendChild(idInput);

    return verification;
}
