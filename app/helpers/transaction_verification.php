<?php
/**
 * Transaction Verification Helper Functions
 * Easy-to-use functions for controllers to verify transactions
 */

/**
 * Verify and flash result to user
 * @param string $transactionType
 * @param array $data
 * @param mixed $insertId
 * @param string $redirectUrl
 */
function verifyAndFlash($transactionType, $data, $insertId, $redirectUrl = null)
{
    $verifier = new TransactionVerifier();
    $result = $verifier->verifyTransaction($transactionType, $data, $insertId);

    if ($result['success']) {
        flash('success_message', $result['message']);

        // Add verification badge to the message
        flash('verification_badge', 'verified');
    } else {
        flash('error_message', 'Transaction may not have been saved properly: ' . implode(', ', $result['errors']));
        flash('verification_badge', 'warning');

        // Log critical error
        error_log("TRANSACTION VERIFICATION FAILED: " . json_encode($result));
    }

    if ($redirectUrl) {
        redirect($redirectUrl);
    }

    return $result;
}

/**
 * Verify for AJAX responses
 * @param string $transactionType
 * @param array $data
 * @param mixed $insertId
 * @return array
 */
function verifyForAjax($transactionType, $data, $insertId)
{
    $verifier = new TransactionVerifier();
    $result = $verifier->verifyTransaction($transactionType, $data, $insertId);

    return [
        'success' => $result['success'],
        'message' => $result['message'],
        'verified' => $result['success'],
        'verification_details' => $result['details'],
        'errors' => $result['errors']
    ];
}

/**
 * Quick verification check
 * @param string $transactionType
 * @param mixed $insertId
 * @return bool
 */
function isTransactionVerified($transactionType, $insertId)
{
    $verifier = new TransactionVerifier();
    $result = $verifier->quickVerify($transactionType, $insertId);
    return $result['success'];
}

/**
 * Enhanced flash message with verification status
 * @param string $message
 * @param string $type
 * @param bool $verified
 */
function flashWithVerification($message, $type = 'success', $verified = true)
{
    flash($type . '_message', $message);
    flash('verification_status', $verified ? 'verified' : 'unverified');
}
?>