<?php
/**
 * Email Helper - Purchase Order Notifications
 * Handles email confirmations for purchase order status changes
 */

class EmailHelper
{
    private static $smtp_config = [
        'host' => 'localhost',     // Update with your SMTP server
        'port' => 587,             // SMTP port
        'username' => '',          // SMTP username
        'password' => '',          // SMTP password  
        'from_email' => 'noreply@hardwarestore.com',
        'from_name' => 'Hardware Store System'
    ];

    /**
     * Send purchase order received confirmation email
     */
    public static function sendPurchaseReceivedConfirmation($purchaseData, $supplierEmail = null, $internalEmail = null)
    {
        try {
            $subject = "Purchase Order Received - PO #" . $purchaseData['po_number'];

            // Email content
            $message = self::buildReceivedConfirmationEmail($purchaseData);

            // Send to supplier if email provided
            if ($supplierEmail && filter_var($supplierEmail, FILTER_VALIDATE_EMAIL)) {
                self::sendEmail($supplierEmail, $subject, $message);
                error_log("Purchase received email sent to supplier: $supplierEmail");
            }

            // Send to internal team if email provided  
            if ($internalEmail && filter_var($internalEmail, FILTER_VALIDATE_EMAIL)) {
                self::sendEmail($internalEmail, $subject, $message);
                error_log("Purchase received email sent to internal: $internalEmail");
            }

            return true;

        } catch (Exception $e) {
            error_log("Failed to send purchase received email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Build email content for received confirmation
     */
    private static function buildReceivedConfirmationEmail($purchaseData)
    {
        $html = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; }
                .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                .details { background-color: #f8f9fa; padding: 15px; margin: 10px 0; border-radius: 5px; }
                .footer { background-color: #ecf0f1; padding: 15px; text-align: center; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #3498db; color: white; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h2>Purchase Order Received & Staged at Dock</h2>
            </div>
            
            <div class='content'>
                <h3>Purchase Order Details</h3>
                <div class='details'>
                    <p><strong>PO Number:</strong> " . htmlspecialchars($purchaseData['po_number']) . "</p>
                    <p><strong>Supplier:</strong> " . htmlspecialchars($purchaseData['supplier_name'] ?? 'N/A') . "</p>
                    <p><strong>Received Date:</strong> " . date('Y-m-d H:i:s') . "</p>
                    <p><strong>Status:</strong> Received and Staged at Dock</p>
                    <p><strong>Total Amount:</strong> $" . number_format($purchaseData['total_amount'] ?? 0, 2) . "</p>
                </div>
                
                <h3>Next Steps</h3>
                <ul>
                    <li>Items have been received and staged at the dock</li>
                    <li>Purchase order will be moved to Receiving Page for processing</li>
                    <li>Inventory will be updated upon final processing</li>
                    <li>A receiving receipt has been generated</li>
                </ul>
                
                <p><em>This is an automated notification from the Hardware Store Inventory Management System.</em></p>
            </div>
            
            <div class='footer'>
                <p>Hardware Store Inventory Management System<br>
                Email sent on " . date('Y-m-d H:i:s') . "</p>
            </div>
        </body>
        </html>";

        return $html;
    }

    /**
     * Send email using PHP mail function (basic implementation)
     * For production, consider using PHPMailer or similar library
     */
    private static function sendEmail($to, $subject, $message)
    {
        // Headers for HTML email
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . self::$smtp_config['from_name'] . ' <' . self::$smtp_config['from_email'] . '>',
            'Reply-To: ' . self::$smtp_config['from_email'],
            'X-Mailer: PHP/' . phpversion()
        ];

        $header_string = implode("\r\n", $headers);

        // Send email
        $result = mail($to, $subject, $message, $header_string);

        if (!$result) {
            throw new Exception("Failed to send email to: $to");
        }

        return $result;
    }

    /**
     * Update SMTP configuration (for production use)
     */
    public static function updateConfig($config)
    {
        self::$smtp_config = array_merge(self::$smtp_config, $config);
    }

    /**
     * Log email activity
     */
    private static function logEmailActivity($to, $subject, $status)
    {
        $logMessage = "[EMAIL] To: $to | Subject: $subject | Status: $status | Time: " . date('Y-m-d H:i:s');
        error_log($logMessage);
    }
}
