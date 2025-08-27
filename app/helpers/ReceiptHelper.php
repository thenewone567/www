<?php
/**
 * Receipt Helper - Purchase Order Receiving Receipts
 * Generates receiving receipts for purchase orders
 */

class ReceiptHelper
{
    /**
     * Generate receiving receipt HTML
     */
    public static function generateReceivingReceipt($purchaseData, $items = [])
    {
        $receiptNumber = self::generateReceiptNumber();
        // Determine displayed receiver name: prefer passed purchaseData value, then common session name keys, then purchase supplier, then System
        $receivedByName = '';
        if (!empty($purchaseData['received_by'])) {
            $receivedByName = $purchaseData['received_by'];
        } elseif (!empty($_SESSION['display_name'])) {
            $receivedByName = $_SESSION['display_name'];
        } elseif (!empty($_SESSION['user_full_name'])) {
            $receivedByName = $_SESSION['user_full_name'];
        } elseif (!empty($_SESSION['user_username'])) {
            $receivedByName = $_SESSION['user_username'];
        } elseif (!empty($_SESSION['user_name'])) {
            $receivedByName = $_SESSION['user_name'];
        } elseif (!empty($_SESSION['username'])) {
            $receivedByName = $_SESSION['username'];
        } elseif (!empty($purchaseData['supplier_name'])) {
            $receivedByName = $purchaseData['supplier_name'];
        } else {
            $receivedByName = 'System';
        }

        // Optional logo (public/uploads/logo.png)
        $logoHtml = '';
        $logoPath = APPROOT . '/public/uploads/logo.png';
        if (file_exists($logoPath)) {
            $logoUrl = (defined('URLROOT') ? rtrim(URLROOT, '/') : '') . '/public/uploads/logo.png';
            $logoHtml = "<div style='text-align:center; margin-bottom:10px;'><img src='" . htmlspecialchars($logoUrl) . "' alt='Logo' style='max-height:60px;'/></div>";
        }

        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>Receiving Receipt - " . htmlspecialchars($purchaseData['po_number']) . "</title>
            <style>
                /* A4 sizing for print */
                @page { size: A4; margin: 10mm; }
                body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
                .receipt-container { width: 210mm; min-height: 297mm; margin: 0 auto; border: 1px solid #ccc; box-sizing: border-box; background: #fff; }
                .header { background-color: #2c3e50; color: white; padding: 20px; text-align: center; }
                .company-info { text-align: center; padding: 15px; background-color: #ecf0f1; }
                .receipt-info { padding: 20px; background-color: #f8f9fa; }
                .purchase-details { padding: 20px; }
                .items-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                .items-table th, .items-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .items-table th { background-color: #3498db; color: white; }
                .summary { padding: 20px; background-color: #f8f9fa; }
                .signatures { margin-top: 40px; }
                .signature-box { display: inline-block; width: 45%; margin: 10px; }
                .signature-line { border-bottom: 1px solid #000; height: 50px; margin-bottom: 5px; }
                .footer { text-align: center; padding: 15px; font-size: 12px; color: #666; }
                .print-only { display: none; }
                @media print {
                    .no-print { display: none; }
                    .print-only { display: block; }
                    body { margin: 0; }
                }
                .barcode { text-align: center; font-family: monospace; font-size: 14px; letter-spacing: 2px; padding: 10px; }
            </style>
        </head>
        <body>
            <div class='receipt-container'>
                <div class='header'>
                    <h1>RECEIVING RECEIPT</h1>
                    <p>Receipt #: " . $receiptNumber . "</p>
                </div>
                
                " . $logoHtml . "

                <div class='company-info'>
                    <h3>Hardware Store Inventory Management</h3>
                    <p>123 Hardware Street, Industrial District<br>
                    Phone: (555) 123-4567 | Email: receiving@hardwarestore.com</p>
                </div>
                
                <div class='receipt-info'>
                    <div style='display: flex; justify-content: space-between;'>
                        <div>
                            <strong>Receipt Date:</strong> " . date('Y-m-d H:i:s') . "<br>
                            <strong>Received By:</strong> " . htmlspecialchars($receivedByName) . "<br>
                            <strong>Status:</strong> Received and Staged at Dock
                        </div>
                        <div style='text-align: right;'>
                            <div class='barcode'>
                                |||| | || ||| | | ||| ||||<br>
                                " . $receiptNumber . "
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class='purchase-details'>
                    <h3>Purchase Order Information</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='border: 1px solid #ddd; padding: 8px; background-color: #f8f9fa;'><strong>PO Number:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($purchaseData['po_number']) . "</td>
                            <td style='border: 1px solid #ddd; padding: 8px; background-color: #f8f9fa;'><strong>Order Date:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($purchaseData['purchase_date'] ?? 'N/A') . "</td>
                        </tr>
                        <tr>
                            <td style='border: 1px solid #ddd; padding: 8px; background-color: #f8f9fa;'><strong>Supplier:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($purchaseData['supplier_name'] ?? 'N/A') . "</td>
                            <td style='border: 1px solid #ddd; padding: 8px; background-color: #f8f9fa;'><strong>Expected Date:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($purchaseData['expected_date'] ?? 'N/A') . "</td>
                        </tr>
                        <tr>
                            <td style='border: 1px solid #ddd; padding: 8px; background-color: #f8f9fa;'><strong>Tracking:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px;'>" . htmlspecialchars($purchaseData['tracking_number'] ?? 'N/A') . "</td>
                            <td style='border: 1px solid #ddd; padding: 8px; background-color: #f8f9fa;'><strong>Total Amount:</strong></td>
                            <td style='border: 1px solid #ddd; padding: 8px; font-weight: bold;'>$" . number_format($purchaseData['total_amount'] ?? 0, 2) . "</td>
                        </tr>
                    </table>
                </div>
                
                " . (count($items) > 0 ? self::buildItemsTable($items) : '') . "
                
                <div class='summary'>
                    <h3>Receiving Summary</h3>
                    <p><strong>Status Change:</strong> Purchase order status updated to 'Received and Staged at Dock'</p>
                    <p><strong>Location:</strong> Receiving Dock - Staging Area</p>
                    <p><strong>Notes:</strong> " . htmlspecialchars($purchaseData['notes'] ?? 'No additional notes') . "</p>
                </div>
                
                <div class='signatures'>
                    <div class='signature-box'>
                        <div class='signature-line'></div>
                        <div style='text-align: center; margin: 5px 0;'>
                            <strong>Received By</strong>
                            <div style='margin-top:8px; font-weight:600;'>" . htmlspecialchars($receivedByName) . "</div>
                            <div style='font-size:12px; color:#666;'>Print Name & Sign</div>
                        </div>
                    </div>
                    <div class='signature-box' style='float: right;'>
                        <div class='signature-line'></div>
                        <div style='text-align: center; margin: 5px 0;'>
                            <strong>Delivered By</strong>
                            <div style='margin-top:8px; font-weight:600;'>&nbsp;</div>
                            <div style='font-size:12px; color:#666;'>Print Name & Sign</div>
                        </div>
                    </div>
                    <div style='clear: both;'></div>
                </div>
                
                <div class='footer'>
                    <p>This is an automated receiving receipt generated by the Hardware Store Inventory Management System.<br>
                    Receipt generated on " . date('Y-m-d H:i:s') . " | User: " . htmlspecialchars($receivedByName) . "</p>
                </div>
            </div>
            
            <div class='no-print' style='text-align: center; margin: 20px;'>
                <button onclick='window.print()' style='background-color: #3498db; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px;'>Print Receipt</button>
                <button onclick='window.close()' style='background-color: #95a5a6; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; margin: 5px;'>Close</button>
            </div>
        </body>
        </html>";

        return $html;
    }

    /**
     * Build items table for receipt
     */
    private static function buildItemsTable($items)
    {
        if (empty($items)) {
            return '';
        }

        $html = "
        <div class='purchase-details'>
            <h3>Items Received</h3>
            <table class='items-table'>
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Quantity Ordered</th>
                        <th>Quantity Received</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>";

        $totalAmount = 0;
        foreach ($items as $item) {
            $lineTotal = ($item->quantity ?? 0) * ($item->unit_price ?? 0);
            $totalAmount += $lineTotal;

            $html .= "
                    <tr>
                        <td>" . htmlspecialchars($item->sku ?? 'N/A') . "</td>
                        <td>" . htmlspecialchars($item->product_name ?? 'N/A') . "</td>
                        <td>" . htmlspecialchars($item->quantity ?? 0) . "</td>
                        <td>" . htmlspecialchars($item->quantity_received ?? $item->quantity ?? 0) . "</td>
                        <td>$" . number_format($item->unit_price ?? 0, 2) . "</td>
                        <td>$" . number_format($lineTotal, 2) . "</td>
                        <td><span style='color: green;'>✓ Received</span></td>
                    </tr>";
        }

        $html .= "
                </tbody>
                <tfoot>
                    <tr style='background-color: #f8f9fa; font-weight: bold;'>
                        <td colspan='5' style='text-align: right;'>Total:</td>
                        <td>$" . number_format($totalAmount, 2) . "</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>";

        return $html;
    }

    /**
     * Generate unique receipt number
     */
    private static function generateReceiptNumber()
    {
        $date = date('Ymd');
        $time = date('His');
        $random = str_pad(mt_rand(1, 999), 3, '0', STR_PAD_LEFT);

        return "RCP-{$date}-{$time}-{$random}";
    }

    /**
     * Save receipt to file system
     */
    public static function saveReceiptToFile($receiptHtml, $poNumber)
    {
        try {
            // Create receipts directory if it doesn't exist
            $receiptsDir = APPROOT . '/storage/receipts/' . date('Y/m');
            if (!is_dir($receiptsDir)) {
                mkdir($receiptsDir, 0755, true);
            }

            // Generate filename
            $filename = "receipt_" . $poNumber . "_" . date('YmdHis') . ".html";
            $filepath = $receiptsDir . '/' . $filename;

            // Save receipt
            file_put_contents($filepath, $receiptHtml);

            // Log the action
            error_log("Receiving receipt saved: $filepath");

            return $filepath;

        } catch (Exception $e) {
            error_log("Failed to save receipt: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Display receipt in new window/tab
     */
    public static function displayReceipt($receiptHtml)
    {
        // Set headers for HTML display
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache, must-revalidate');

        echo $receiptHtml;
        exit();
    }

    /**
     * Save receipt as PDF using Dompdf if available.
     * Returns the generated file path on success, or false on failure.
     */
    public static function saveReceiptPdf($receiptHtml, $poNumber)
    {
        try {
            // Create receipts dir
            $receiptsDir = APPROOT . '/storage/receipts/' . date('Y/m');
            if (!is_dir($receiptsDir)) {
                mkdir($receiptsDir, 0755, true);
            }

            $filename = "receipt_" . preg_replace('/[^A-Za-z0-9_-]/', '_', $poNumber) . "_" . date('YmdHis') . ".pdf";
            $filepath = $receiptsDir . '/' . $filename;

            // If Dompdf is installed, use it
            if (class_exists('\Dompdf\\Dompdf')) {
                // Instantiate and use Dompdf only when it's available
                $dompdfClass = '\\Dompdf\\Dompdf';
                $dompdf = new $dompdfClass();
                // Ensure HTML has UTF-8 meta
                $dompdf->loadHtml($receiptHtml);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();
                $output = $dompdf->output();
                file_put_contents($filepath, $output);
                error_log("PDF receipt saved: $filepath");
                return $filepath;
            }

            // Fallback: try to write a .html file and return that path (caller can convert externally)
            $htmlFallback = $receiptsDir . '/fallback_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $poNumber) . '_' . date('YmdHis') . '.html';
            file_put_contents($htmlFallback, $receiptHtml);
            error_log("Dompdf not installed; saved HTML fallback: $htmlFallback");
            return false;

        } catch (Exception $e) {
            error_log('Failed to save receipt PDF: ' . $e->getMessage());
            return false;
        }
    }
}
