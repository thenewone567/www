<?php
/**
 * Email Configuration for Purchase Order Notifications
 * Update these settings according to your email server configuration
 */

return [
    // SMTP Configuration
    'smtp' => [
        'host' => 'localhost',                    // Update with your SMTP server
        'port' => 587,                           // SMTP port (587 for TLS, 465 for SSL)
        'encryption' => 'tls',                   // 'tls', 'ssl', or null
        'username' => '',                        // SMTP username
        'password' => '',                        // SMTP password
    ],

    // Default sender information
    'from' => [
        'email' => 'noreply@hardwarestore.com', // Update with your from email
        'name' => 'Hardware Store System'
    ],

    // Default recipients for internal notifications
    'internal_emails' => [
        'receiving@hardwarestore.com',          // Receiving department
        'manager@hardwarestore.com'            // Management (optional)
    ],

    // Email templates configuration
    'templates' => [
        'received_confirmation' => [
            'subject' => 'Purchase Order Received - PO #{{po_number}}',
            'include_receipt' => true,
            'send_to_supplier' => true,
            'send_to_internal' => true
        ]
    ],

    // System settings
    'enable_emails' => true,                    // Set to false to disable all emails
    'log_emails' => true,                       // Log all email activity
    'backup_method' => 'file',                  // 'file' or 'database' for email backup

    // Development settings
    'debug_mode' => false,                      // Set to true for debug output
    'test_mode' => false,                       // Set to true to prevent actual sending (logs only)
    'test_email' => 'admin@hardwarestore.com'  // All emails go here when test_mode is true
];
