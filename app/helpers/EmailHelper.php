<?php
/**
 * Email Helper - Purchase Order Notifications
 * Uses PHPMailer with SMTP when available, falls back to PHP mail()
 */

class EmailHelper
{
    private static $smtp_config = [
        'host'       => 'localhost',     // Update with your SMTP server
        'port'       => 587,             // SMTP port
        'username'   => '',          // SMTP username
        'password'   => '',          // SMTP password
        'encryption' => 'tls',     // tls or ssl
        'from_email' => 'noreply@hardwarestore.com',
        'from_name'  => 'Hardware Store System'
    ];

    /**
     * Public wrapper to send raw email
     */
    public static function sendRawEmail($to, $subject, $message, $attachments = [])
    {
        return self::sendEmail($to, $subject, $message, $attachments);
    }

    /**
     * Send purchase order received confirmation email
     * $attachments = array of absolute file paths
     */
    public static function sendPurchaseReceivedConfirmation($purchaseData, $supplierEmail = null, $internalEmail = null, $attachments = [])
    {
        try {
            $subject = "Purchase Order Received - PO #" . $purchaseData['po_number'];

            // Email content
            $message = self::buildReceivedConfirmationEmail($purchaseData);

            // Send to supplier if email provided
            if ($supplierEmail && filter_var($supplierEmail, FILTER_VALIDATE_EMAIL)) {
                self::sendEmail($supplierEmail, $subject, $message, $attachments);
                self::logEmailActivity($supplierEmail, $subject, 'sent_to_supplier');
            }

            // Send to internal team if email provided
            if ($internalEmail && filter_var($internalEmail, FILTER_VALIDATE_EMAIL)) {
                self::sendEmail($internalEmail, $subject, $message, $attachments);
                self::logEmailActivity($internalEmail, $subject, 'sent_to_internal');
            }

            return true;

        } catch (Exception $e) {
            error_log("Failed to send purchase received email: " . $e->getMessage());
            return false;
        }
    }

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
     * Send email using PHPMailer+SMTP when available; fallback to mail()
     */
    private static function sendEmail($to, $subject, $message, $attachments = [])
    {
        // If PHPMailer is available, use it via SMTP
        if (class_exists('PHPMailer\\PHPMailer\\PHPMailer')) {
            try {
                $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = self::$smtp_config['host'];
                $mail->Port = self::$smtp_config['port'];
                $mail->SMTPAuth = !empty(self::$smtp_config['username']);
                if (!empty(self::$smtp_config['username'])) {
                    $mail->Username = self::$smtp_config['username'];
                    $mail->Password = self::$smtp_config['password'];
                }
                if (!empty(self::$smtp_config['encryption'])) {
                    $mail->SMTPSecure = self::$smtp_config['encryption'];
                }

                $mail->setFrom(self::$smtp_config['from_email'], self::$smtp_config['from_name']);
                $mail->addAddress($to);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $message;

                foreach ($attachments as $filePath) {
                    if (file_exists($filePath)) {
                        $mail->addAttachment($filePath);
                    }
                }

                $mail->send();
                self::logEmailActivity($to, $subject, 'sent_via_phpmailer');
                return true;
            } catch (\Exception $e) {
                error_log('PHPMailer error: ' . $e->getMessage());
                // fall through to mail()
            }
        }

        // Fallback to PHP mail()
        $from = self::$smtp_config['from_name'] . ' <' . self::$smtp_config['from_email'] . '>';
        if (!empty($attachments)) {
            $boundary = md5(time());
            $headers = [];
            $headers[] = 'From: ' . $from;
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

            $body = "--$boundary\r\n";
            $body .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
            $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $body .= $message . "\r\n\r\n";

            foreach ($attachments as $filePath) {
                if (!file_exists($filePath))
                    continue;
                $fileName = basename($filePath);
                $fileData = file_get_contents($filePath);
                $fileData = chunk_split(base64_encode($fileData));
                $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

                $body .= "--$boundary\r\n";
                $body .= "Content-Type: $mimeType; name=\"$fileName\"\r\n";
                $body .= "Content-Disposition: attachment; filename=\"$fileName\"\r\n";
                $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
                $body .= $fileData . "\r\n\r\n";
            }

            $body .= "--$boundary--";
            $header_string = implode("\r\n", $headers);
            $result = mail($to, $subject, $body, $header_string);
            if (!$result) {
                self::logEmailActivity($to, $subject, 'failed');
                throw new Exception("Failed to send email with attachments to: $to");
            }
            self::logEmailActivity($to, $subject, 'sent_with_attachments');
            return $result;
        }

        $headers = [];
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
        $headers[] = 'From: ' . $from;
        $headers[] = 'Reply-To: ' . self::$smtp_config['from_email'];
        $headers[] = 'X-Mailer: PHP/' . phpversion();

        $header_string = implode("\r\n", $headers);
        $result = mail($to, $subject, $message, $header_string);
        if (!$result) {
            self::logEmailActivity($to, $subject, 'failed');
            throw new Exception("Failed to send email to: $to");
        }
        self::logEmailActivity($to, $subject, 'sent');
        return $result;
    }

    public static function updateConfig($config)
    {
        self::$smtp_config = array_merge(self::$smtp_config, $config);
    }

    private static function logEmailActivity($to, $subject, $status)
    {
        $logDir = APPROOT . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($logDir))
            @mkdir($logDir, 0755, true);
        $logFile = $logDir . DIRECTORY_SEPARATOR . 'email_helper.log';
        $entry = date('Y-m-d H:i:s') . "\t" . $status . "\t" . $to . "\t" . $subject . "\n";
        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    /**
     * Send purchase order cancellation notification email
     */
    public static function sendPurchaseCancellationNotification($purchaseData, $cancellationReason, $supplierEmail = null, $internalEmail = null)
    {
        try {
            $subject = "Purchase Order CANCELLED - PO #" . $purchaseData['po_number'];

            // Email content
            $message = self::buildCancellationEmail($purchaseData, $cancellationReason);

            $emailsSent = 0;

            // Send to supplier if email provided
            if ($supplierEmail && filter_var($supplierEmail, FILTER_VALIDATE_EMAIL)) {
                self::sendEmail($supplierEmail, $subject, $message);
                self::logEmailActivity($supplierEmail, $subject, 'cancellation_sent_to_supplier');
                $emailsSent++;
            }

            // Send to internal team if email provided
            if ($internalEmail && filter_var($internalEmail, FILTER_VALIDATE_EMAIL)) {
                self::sendEmail($internalEmail, $subject, $message);
                self::logEmailActivity($internalEmail, $subject, 'cancellation_sent_to_internal');
                $emailsSent++;
            }

            return $emailsSent > 0;

        } catch (Exception $e) {
            error_log("Failed to send cancellation email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Build cancellation email content
     */
    private static function buildCancellationEmail($purchaseData, $cancellationReason)
    {
        $html = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 8px;'>
            <div style='background-color: #dc3545; color: white; padding: 15px; border-radius: 8px 8px 0 0; text-align: center;'>
                <h2 style='margin: 0; font-size: 24px;'>
                    <span style='font-size: 20px;'>🚫</span> PURCHASE ORDER CANCELLED
                </h2>
            </div>
            
            <div style='padding: 20px; background-color: #f8f9fa;'>
                <div style='background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
                    <p style='margin: 0; font-weight: bold; color: #856404;'>
                        ⚠️ IMPORTANT: This purchase order has been cancelled and should not be fulfilled.
                    </p>
                </div>
                
                <h3 style='color: #333; border-bottom: 2px solid #dc3545; padding-bottom: 5px;'>Order Details</h3>
                <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                    <tr>
                        <td style='padding: 8px 0; font-weight: bold; width: 150px;'>PO Number:</td>
                        <td style='padding: 8px 0;'>" . htmlspecialchars($purchaseData['po_number']) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; font-weight: bold;'>Supplier:</td>
                        <td style='padding: 8px 0;'>" . htmlspecialchars($purchaseData['supplier_name'] ?? 'N/A') . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; font-weight: bold;'>Original Date:</td>
                        <td style='padding: 8px 0;'>" . date('F j, Y', strtotime($purchaseData['purchase_date'])) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; font-weight: bold;'>Total Amount:</td>
                        <td style='padding: 8px 0; font-weight: bold; color: #dc3545;'>₹" . number_format($purchaseData['total_amount'], 2) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px 0; font-weight: bold;'>Cancelled Date:</td>
                        <td style='padding: 8px 0;'>" . date('F j, Y g:i A') . "</td>
                    </tr>
                </table>

                <h3 style='color: #333; border-bottom: 2px solid #dc3545; padding-bottom: 5px;'>Cancellation Reason</h3>
                <div style='background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px;'>
                    <p style='margin: 0; color: #721c24;'>" . htmlspecialchars($cancellationReason) . "</p>
                </div>

                <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;'>
                    <p style='margin: 5px 0; font-size: 14px;'>This is an automated notification from Hardware Store Management System</p>
                    <p style='margin: 5px 0; font-size: 12px;'>Generated on " . date('F j, Y \a\t g:i A') . "</p>
                </div>
            </div>
        </div>";

        return $html;
    }
}
