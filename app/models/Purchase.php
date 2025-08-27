<?php
/**
 * ⚠️  UNIFIED PURCHASE MODEL - DO NOT CREATE SEPARATE "PurchaseOrder" MODEL ⚠️
 * 
 * This is the SINGLE, UNIFIED model for ALL purchase operations in the system.
 * It handles everything related to purchase orders using the `purchases` table.
 * 
 * IMPORTANT FOR AI MODELS & DEVELOPERS:
 * - Do NOT create a separate PurchaseOrder.php model
 * - This model contains ALL purchase functionality 
 * - Use method names starting with "purchase" for new development
 * - "PurchaseOrder" methods exist only for backward compatibility
 * 
 * Database: Uses `purchases` table (NOT `purchase_orders`)
 * Architecture: Single model approach for maintainability
 * 
 * This model handles ALL purchase order operations including:
 * - Creating and managing purchase orders
 * - Purchase order receiving workflows  
 * - Status tracking and updates
 * - Supplier and product management
 * - Reporting and analytics
 * 
 * Note: This model was consolidated from Purchase.php and PurchaseOrder.php
 * to eliminate duplication and provide a single source of truth for all
 * purchase-related operations.
 * 
 * @author Hardware Store Team
 * @version 2.0 (Unified Model)
 * @date August 10, 2025
 */
class Purchase
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Get all purchase history records
    public function getHistory()
    {
        $this->db->query('SELECT p.purchase_id as id, p.po_number as order_no, s.supplier_name as supplier, p.purchase_date as date, p.status, p.total_amount, p.status as received, p.status as pending, p.status as cancelled, u.username as created_by
                          FROM purchases p
                          LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                          LEFT JOIN users u ON p.created_by = u.user_id
                          ORDER BY p.purchase_date DESC');
        $this->db->execute();
        return $this->db->resultSet();
    }

    // Get purchases with supplier information (excluding receiving workflow statuses and cancelled orders)
    public function getPurchasesWithSuppliers()
    {
        // Show purchase orders that are in the procurement/ordering phase
        // Exclude receiving workflow statuses - these should be handled in receiving page
        // Keep 'arrived_at_dock' and 'dock_assigned' visible here as they're still dock operations
        $excludeStatuses = ['receiving_in_progress', 'partially_received', 'received', 'completed', 'cancelled', 'deleted'];
        $placeholders = str_repeat('?,', count($excludeStatuses) - 1) . '?';

        $this->db->query("
            SELECT p.purchase_id, p.purchase_id as id, p.po_number, p.supplier_id, 
                   p.purchase_date, p.expected_date, p.status, p.total_amount, 
                   p.notes, p.created_by, p.tracking_number,
                   s.supplier_name, p.created_by as created_by_name
            FROM purchases p
            LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
            WHERE p.purchase_id = (
                SELECT MAX(p2.purchase_id) 
                FROM purchases p2 
                WHERE p2.po_number = p.po_number
            )
            AND COALESCE(p.status, 'pending') NOT IN ($placeholders)
            ORDER BY p.purchase_date DESC
        ");

        // Bind the exclude statuses
        foreach ($excludeStatuses as $index => $status) {
            $this->db->bind($index + 1, $status);
        }

        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Update purchase order status with email notification and receipt generation
     */
    public function updateStatus($id, $status)
    {
        try {
            // Get purchase details before update for email/receipt
            $purchaseData = null;
            if ($status === 'received') {
                $purchaseData = $this->getPurchaseWithSupplierDetails($id);
            }

            // Update in purchases table
            $this->db->query('UPDATE purchases SET status = ?, updated_at = NOW() WHERE purchase_id = ?');
            $this->db->bind(1, $status);
            $this->db->bind(2, $id);
            $result = $this->db->execute();

            // Also log the status change
            $this->logStatusChange($id, $status, 'purchases');

            // Handle received status - trigger email and receipt
            if ($result && $status === 'received' && $purchaseData) {
                $this->handleReceivedStatusUpdate($purchaseData);
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error updating purchase status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle received status update - send emails and generate receipt
     */
    private function handleReceivedStatusUpdate($purchaseData)
    {
        try {
            // Load helpers
            require_once APPROOT . '/app/helpers/EmailHelper.php';
            require_once APPROOT . '/app/helpers/ReceiptHelper.php';

            // Get purchase items for receipt
            $items = $this->getPurchaseItems($purchaseData->purchase_id);

            // Convert object to array for helpers and include received_by name when available
            // Prefer display_name session key (used elsewhere in the app), fallback to other session keys
            $receivedByName = $_SESSION['display_name'] ?? $_SESSION['user_full_name'] ?? $_SESSION['user_username'] ?? $_SESSION['username'] ?? null;
            $purchaseArray = [
                'purchase_id'     => $purchaseData->purchase_id,
                'po_number'       => $purchaseData->po_number,
                'supplier_name'   => $purchaseData->supplier_name ?? 'Unknown Supplier',
                'supplier_email'  => $purchaseData->supplier_email ?? null,
                'purchase_date'   => $purchaseData->purchase_date,
                'expected_date'   => $purchaseData->expected_date,
                'tracking_number' => $purchaseData->tracking_number,
                'total_amount'    => $purchaseData->total_amount,
                'notes'           => $purchaseData->notes,
                'received_by'     => $receivedByName
            ];

            // Generate receiving receipt
            $receiptHtml = ReceiptHelper::generateReceivingReceipt($purchaseArray, $items);

            // Save receipt to file system
            $receiptPath = ReceiptHelper::saveReceiptToFile($receiptHtml, $purchaseData->po_number);

            // Attempt to generate PDF and attach to emails (if Dompdf present)
            $pdfPath = ReceiptHelper::saveReceiptPdf($receiptHtml, $purchaseData->po_number);

            // Prepare attachments array if PDF generated
            $attachments = [];
            if ($pdfPath && file_exists($pdfPath)) {
                $attachments[] = $pdfPath;
            }

            // Send email confirmations (include attachments if available)
            $emailSent = EmailHelper::sendPurchaseReceivedConfirmation(
                $purchaseArray,
                $purchaseData->supplier_email ?? null,  // Supplier email
                'receiving@hardwarestore.com',           // Internal email
                $attachments
            );

            // Store receipt info in session for display
            $_SESSION['show_receipt'] = [
                'html'         => $receiptHtml,
                'po_number'    => $purchaseData->po_number,
                'generated_at' => date('Y-m-d H:i:s')
            ];

            // Log the actions
            $logMessage = "Purchase {$purchaseData->po_number} received - ";
            $logMessage .= $emailSent ? "Email sent" : "Email failed";
            $logMessage .= $receiptPath ? ", Receipt saved: $receiptPath" : ", Receipt save failed";

            error_log($logMessage);

            return true;

        } catch (Exception $e) {
            error_log("Error handling received status update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get purchase with supplier details for email/receipt
     */
    private function getPurchaseWithSupplierDetails($purchaseId)
    {
        try {
            $this->db->query('
                SELECT p.*, s.supplier_name, s.email as supplier_email
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                WHERE p.purchase_id = ?
            ');
            $this->db->bind(1, $purchaseId);
            $this->db->execute();

            return $this->db->single();

        } catch (Exception $e) {
            error_log("Error getting purchase with supplier details: " . $e->getMessage());
            return null;
        }
    }    /**
         * Update purchase status to "Received and Staged At Dock" with full workflow
         */
    public function markAsReceivedAndStaged($purchaseId, $dockLocationId = null, $receivingAreaId = null, $notes = '', $receivedBy = null)
    {
        try {
            // Update status to a specific received status
            $status = 'received'; // You can change this to 'received_staged' if you want a specific status

            // Get current user if not provided
            if (!$receivedBy) {
                $receivedBy = $_SESSION['user_id'] ?? $_SESSION['username'] ?? 'System';
            }

            // Update purchase with received status, timestamp, and location assignments
            $this->db->query('
                UPDATE purchases 
                SET status = ?, 
                    received_at = NOW(), 
                    updated_at = NOW(),
                    dock_location_id = ?,
                    receiving_area_id = ?,
                    dock_assignment_notes = ?
                WHERE purchase_id = ?
            ');
            $this->db->bind(1, $status);
            $this->db->bind(2, $dockLocationId);
            $this->db->bind(3, $receivingAreaId);
            $this->db->bind(4, $notes);
            $this->db->bind(5, $purchaseId);

            $result = $this->db->execute();

            if ($result) {
                // Get purchase details for notifications
                $purchaseData = $this->getPurchaseWithSupplierDetails($purchaseId);

                if ($purchaseData) {
                    // Trigger email and receipt workflow
                    $this->handleReceivedStatusUpdate($purchaseData);

                    // Log the specific action with location details
                    $locationNotes = "Purchase received and staged at dock";
                    if ($dockLocationId) {
                        $locationNotes .= " - Dock assigned";
                    }
                    if ($receivingAreaId) {
                        $locationNotes .= " - Receiving area assigned";
                    }
                    if ($notes) {
                        $locationNotes .= " - Notes: " . $notes;
                    }

                    $this->logStatusChange(
                        $purchaseId,
                        'received_staged',
                        'purchases',
                        $locationNotes
                    );
                }
            }

            return $result;

        } catch (Exception $e) {
            error_log("Error marking purchase as received and staged: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark PO as arrived at dock (first step in receiving workflow)
     */
    public function markAsArrivedAtDock($purchaseId, $dockLocationId = null, $notes = '')
    {
        try {
            $this->db->query('
                UPDATE purchases 
                SET status = ?, 
                    dock_arrival_time = NOW(),
                    updated_at = NOW(),
                    dock_location_id = ?,
                    dock_assignment_notes = ?
                WHERE purchase_id = ?
            ');
            $this->db->bind(1, 'arrived_at_dock');
            $this->db->bind(2, $dockLocationId);
            $this->db->bind(3, $notes);
            $this->db->bind(4, $purchaseId);

            $result = $this->db->execute();

            if ($result) {
                $this->logStatusChange(
                    $purchaseId,
                    'arrived_at_dock',
                    'purchases',
                    "PO arrived at dock" . ($notes ? " - Notes: $notes" : "")
                );
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error marking PO as arrived at dock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Assign dock location to PO (second step in dock workflow)
     */
    public function assignDockLocation($purchaseId, $dockLocationId, $notes = '')
    {
        try {
            $this->db->query('
                UPDATE purchases 
                SET status = ?, 
                    dock_location_id = ?,
                    dock_assignment_notes = ?,
                    updated_at = NOW()
                WHERE purchase_id = ?
            ');
            $this->db->bind(1, 'dock_assigned');
            $this->db->bind(2, $dockLocationId);
            $this->db->bind(3, $notes);
            $this->db->bind(4, $purchaseId);

            $result = $this->db->execute();

            if ($result) {
                $this->logStatusChange(
                    $purchaseId,
                    'dock_assigned',
                    'purchases',
                    "Dock location assigned" . ($notes ? " - Notes: $notes" : "")
                );
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error assigning dock location: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark PO as ready to transfer to receiving area
     */
    public function markReadyToReceive($purchaseId, $notes = '')
    {
        try {
            $this->db->query('
                UPDATE purchases 
                SET status = ?, 
                    updated_at = NOW()
                WHERE purchase_id = ?
            ');
            $this->db->bind(1, 'ready_to_receive');
            $this->db->bind(2, $purchaseId);

            $result = $this->db->execute();

            if ($result) {
                $this->logStatusChange(
                    $purchaseId,
                    'ready_to_receive',
                    'purchases',
                    "PO ready for receiving area transfer" . ($notes ? " - Notes: $notes" : "")
                );
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error marking PO as ready to receive: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Start receiving process in receiving area
     */
    public function startReceivingProcess($purchaseId, $receivingAreaId = null, $notes = '')
    {
        try {
            $this->db->query('
                UPDATE purchases 
                SET status = ?, 
                    receiving_area_id = ?,
                    receiving_start_time = NOW(),
                    updated_at = NOW()
                WHERE purchase_id = ?
            ');
            $this->db->bind(1, 'receiving_in_progress');
            $this->db->bind(2, $receivingAreaId);
            $this->db->bind(3, $purchaseId);

            $result = $this->db->execute();

            if ($result) {
                $this->logStatusChange(
                    $purchaseId,
                    'receiving_in_progress',
                    'purchases',
                    "Receiving process started in receiving area" . ($notes ? " - Notes: $notes" : "")
                );
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error starting receiving process: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete receiving process (final step)
     */
    public function completeReceiving($purchaseId, $notes = '')
    {
        try {
            $this->db->query('
                UPDATE purchases 
                SET status = ?, 
                    received_at = NOW(),
                    receiving_complete_time = NOW(),
                    updated_at = NOW()
                WHERE purchase_id = ?
            ');
            $this->db->bind(1, 'received');
            $this->db->bind(2, $purchaseId);

            $result = $this->db->execute();

            if ($result) {
                // Get purchase details for notifications
                $purchaseData = $this->getPurchaseWithSupplierDetails($purchaseId);

                if ($purchaseData) {
                    // Trigger email and receipt workflow
                    $this->handleReceivedStatusUpdate($purchaseData);
                }

                $this->logStatusChange(
                    $purchaseId,
                    'received',
                    'purchases',
                    "Receiving process completed" . ($notes ? " - Notes: $notes" : "")
                );
            }

            return $result;
        } catch (Exception $e) {
            error_log("Error completing receiving process: " . $e->getMessage());
            return false;
        }
    }

    public function updateTracking($id, $trackingNumber)
    {
        try {
            // Update in purchases table
            $this->db->query('UPDATE purchases SET tracking_number = ?, status = ?, updated_at = NOW() WHERE purchase_id = ?');
            $this->db->bind(1, $trackingNumber);
            $this->db->bind(2, 'in_transit');
            $this->db->bind(3, $id);
            $result = $this->db->execute();

            // Also log the status change
            $this->logStatusChange($id, 'in_transit', 'purchases', "Tracking number added: $trackingNumber");

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get purchase by PO number
     */
    public function getPurchaseByPONumber($poNumber)
    {
        try {
            $this->db->query('
                SELECT p.*, s.supplier_name, s.email as supplier_email
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                WHERE p.po_number = ?
                LIMIT 1
            ');
            $this->db->bind(1, $poNumber);
            $this->db->execute();

            return $this->db->single();

        } catch (Exception $e) {
            error_log("Error getting purchase by PO number: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get purchase by ID
     * @param int $id Purchase ID
     * @return object|false
     */
    public function getPurchaseById($id)
    {
        try {
            $this->db->query("
                SELECT p.*, s.supplier_name, u.username as created_by_username, u.full_name as created_by_fullname
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                LEFT JOIN users u ON p.created_by = u.user_id
                WHERE p.purchase_id = ?
            ");
            $this->db->bind(1, $id);

            $this->db->execute();
            return $this->db->single();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Update purchase
     * @param int $id Purchase ID
     * @param array $data Update data
     * @return bool
     */
    public function updatePurchase($id, $data)
    {
        try {
            // Build dynamic query based on provided data
            $setClause = [];
            $params = [];
            $paramIndex = 1;

            if (isset($data['notes'])) {
                $setClause[] = 'notes = ?';
                $params[$paramIndex++] = $data['notes'];
            }

            if (isset($data['expected_date'])) {
                $setClause[] = 'expected_date = ?';
                $params[$paramIndex++] = $data['expected_date'];
            }

            if (isset($data['status'])) {
                $setClause[] = 'status = ?';
                $params[$paramIndex++] = $data['status'];
            }

            if (isset($data['tracking_number'])) {
                $setClause[] = 'tracking_number = ?';
                $params[$paramIndex++] = $data['tracking_number'];
            }

            if (isset($data['cancellation_reason'])) {
                $setClause[] = 'cancellation_reason = ?';
                $params[$paramIndex++] = $data['cancellation_reason'];
            }

            if (isset($data['cancelled_action'])) {
                $setClause[] = 'cancelled_action = ?';
                $params[$paramIndex++] = $data['cancelled_action'];
            }

            if (isset($data['custom_reason'])) {
                $setClause[] = 'custom_cancellation_reason = ?';
                $params[$paramIndex++] = $data['custom_reason'];
            }

            if (isset($data['cancelled_by'])) {
                $setClause[] = 'cancelled_by = ?';
                $params[$paramIndex++] = $data['cancelled_by'];
            }

            // Always update the timestamp
            $setClause[] = 'updated_at = NOW()';

            if (empty($setClause)) {
                return false; // No data to update
            }

            // Update in purchases table
            $query = 'UPDATE purchases SET ' . implode(', ', $setClause) . ' WHERE purchase_id = ?';
            $params[$paramIndex] = $id;

            $this->db->query($query);
            foreach ($params as $index => $value) {
                $this->db->bind($index, $value);
            }
            $result = $this->db->execute();

            // Log the update
            $statusToLog = $data['status'] ?? 'updated';
            $notes = isset($data['tracking_number']) && !empty($data['tracking_number'])
                ? "Purchase updated with tracking: {$data['tracking_number']}"
                : 'Purchase updated';
            $this->logStatusChange($id, $statusToLog, 'purchases', $notes);

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    // Alias for backward compatibility with "PurchaseOrder" naming
    public function updatePurchaseOrder($id, $data)
    {
        return $this->updatePurchase($id, $data);
    }

    /**
     * Log status changes for audit trail
     */
    private function logStatusChange($recordId, $newStatus, $tableName, $notes = '')
    {
        try {
            // Check if audit_log table exists
            $this->db->query("SHOW TABLES LIKE 'audit_log'");
            $this->db->execute();
            $auditExists = $this->db->single();

            if ($auditExists) {
                $this->db->query('INSERT INTO audit_log (table_name, record_id, action, details, performed_by, performed_at) VALUES (?, ?, ?, ?, ?, NOW())');
                $this->db->bind(1, $tableName);
                $this->db->bind(2, $recordId);
                $this->db->bind(3, 'status_update');
                $this->db->bind(4, "Status changed to: $newStatus" . ($notes ? " - $notes" : ''));
                $this->db->bind(5, $_SESSION['user_id'] ?? 0);
                $this->db->execute();
            }
        } catch (Exception $e) {
            // Silently fail audit logging to not break the main operation
            error_log("Audit log error: " . $e->getMessage());
        }
    }

    // Get cancelled purchases for returns system
    public function getCancelledPurchases()
    {
        $this->db->query("
            SELECT p.purchase_id, p.purchase_id as id, CONCAT('PO-', p.purchase_id) as po_number, 
                   p.supplier_id, p.purchase_date, p.expected_date, p.status, p.total_amount, 
                   p.notes, p.created_by, p.updated_at, p.tracking_number,
                   p.cancellation_reason, p.cancelled_action, p.cancelled_by,
                   NULL as cancelled_date, NULL as created_at,
                   s.supplier_name, p.created_by as created_by_name
            FROM purchases p
            LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
            WHERE p.status = 'cancelled'
            ORDER BY COALESCE(p.updated_at, p.purchase_date) DESC
        ");

        $this->db->execute();
        return $this->db->resultSet();
    }

    // Generate PO Number
    public function generatePONumber()
    {
        // Generate PO number with pattern: PO-YYMMDD-HHMMSS
        // Example: PO-250810-035427
        $date = date('ymd'); // YY MM DD format (2-digit year, month, day)
        $time = date('His'); // HH MM SS format (24-hour format)
        return "PO-{$date}-{$time}";
    }

    // Create Purchase (Primary method)
    public function createPurchase($data)
    {
        $this->db->query("
            INSERT INTO purchases (po_number, supplier_id, purchase_date, expected_date, status, total_amount, notes, average_price_method, created_by, updated_at)
            VALUES (:po_number, :supplier_id, :purchase_date, :expected_date, :status, :total_amount, :notes, :average_price_method, :created_by, NOW())
        ");

        $this->db->bind(':po_number', $data['po_number']);
        $this->db->bind(':supplier_id', $data['supplier_id']);
        // Use centralized date helper to respect configured timezone
        $this->db->bind(':purchase_date', $data['order_date'] ?? (function_exists('app_current_date') ? app_current_date() : date('Y-m-d')));
        $this->db->bind(':expected_date', $data['expected_date'] ?? date('Y-m-d', strtotime('+7 days')));
        $this->db->bind(':status', $data['status'] ?? 'pending');
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':notes', $data['notes'] ?? '');
        $this->db->bind(':average_price_method', $data['average_price_method'] ?? 0);
        $this->db->bind(':created_by', $data['created_by'] ?? 1);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Add Purchase Item (Primary method)
    public function addPurchaseItem($data)
    {
        $this->db->query("
            INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_price)
            VALUES (:purchase_id, :product_id, :quantity, :unit_price)
        ");

        $this->db->bind(':purchase_id', $data['purchase_id']);
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':unit_price', $data['unit_price']);

        return $this->db->execute();
    }

    // Get purchase order items (primary method)
    public function getPurchaseItems($purchaseId)
    {
        $this->db->query("
            SELECT pi.*, p.product_name, p.sku
            FROM purchase_items pi
            LEFT JOIN products p ON pi.product_id = p.product_id
            WHERE pi.purchase_id = ?
        ");

        $this->db->bind(1, $purchaseId);
        $this->db->execute();
        return $this->db->resultSet();
    }

    // Add purchase method for compatibility with controller
    public function addPurchase($data)
    {
        // Generate PO number
        $poNumber = $this->generatePONumber();

        // Add po_number to the data
        $data['po_number'] = $poNumber;

        // Use the existing createPurchase method
        return $this->createPurchase($data);
    }

    // Get purchases method for compatibility with controller index page
    public function getPurchases()
    {
        // Use the existing getPurchasesWithSuppliers method
        return $this->getPurchasesWithSuppliers();
    }

    // Get purchase summary statistics for dashboard
    public function getPurchaseSummaryStats()
    {
        $stats = [
            'monthly_purchases' => 0,
            'pending_orders'    => 0,
            'active_suppliers'  => 0,
            'items_received'    => 0
        ];

        try {
            // Get monthly purchases (current month)
            $this->db->query("
                SELECT COUNT(*) as count 
                FROM purchases 
                WHERE YEAR(purchase_date) = YEAR(CURDATE()) 
                AND MONTH(purchase_date) = MONTH(CURDATE())
            ");
            $this->db->execute();
            $result = $this->db->single();
            $stats['monthly_purchases'] = $result ? $result->count : 0;

            // Get pending orders count
            $this->db->query("
                SELECT COUNT(*) as count 
                FROM purchases 
                WHERE status = 'pending'
            ");
            $this->db->execute();
            $result = $this->db->single();
            $stats['pending_orders'] = $result ? $result->count : 0;

            // Get active suppliers count (suppliers with recent orders)
            $this->db->query("
                SELECT COUNT(DISTINCT supplier_id) as count 
                FROM purchases 
                WHERE purchase_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ");
            $this->db->execute();
            $result = $this->db->single();
            $stats['active_suppliers'] = $result ? $result->count : 0;

            // Get items received count (this month)
            $this->db->query("
                SELECT COUNT(*) as count 
                FROM purchases 
                WHERE status IN ('received', 'completed') 
                AND YEAR(purchase_date) = YEAR(CURDATE()) 
                AND MONTH(purchase_date) = MONTH(CURDATE())
            ");
            $this->db->execute();
            $result = $this->db->single();
            $stats['items_received'] = $result ? $result->count : 0;

        } catch (Exception $e) {
            // Return default values if there's an error
            error_log("Error in getPurchaseSummaryStats: " . $e->getMessage());
        }

        return $stats;
    }

    /**
     * Get comprehensive purchase summary for KPI cards
     * Returns both count and value data for each status
     * @return array
     */
    public function getPurchaseSummary()
    {
        $summary = [
            'total_purchases'    => 0,
            'pending'            => 0,
            'sent'               => 0,
            'in_transit'         => 0,
            'received'           => 0,
            'cancelled'          => 0,
            'overdue'            => 0,
            'suppliers'          => 0,
            'total_orders_count' => 0,
            // Count versions for additional display
            'pending_count'      => 0,
            'sent_count'         => 0,
            'in_transit_count'   => 0,
            'received_count'     => 0,
            // Debug info
            'debug_info'         => ''
        ];

        try {
            // Use the same data source as the table (getPurchases method)
            $orders = $this->getPurchases();

            if (!$orders || !is_array($orders)) {
                $summary['debug_info'] = "No orders returned from getPurchases() method";
                return $summary;
            }

            $summary['total_orders_count'] = count($orders);
            $summary['debug_info'] = "Using data from getPurchases(): " . count($orders) . " orders found";

            // Process the orders data to calculate KPIs
            $statusCounts = [];
            $statusValues = [];
            $totalActiveValue = 0;
            $overdueValue = 0;
            $suppliers = [];

            foreach ($orders as $order) {
                $status = strtolower($order->status ?? 'pending');
                $amount = (float) ($order->total_amount ?? 0);

                // Count by status
                $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
                $statusValues[$status] = ($statusValues[$status] ?? 0) + $amount;

                // Track active orders value (exclude received and cancelled)
                if (!in_array($status, ['received', 'completed', 'cancelled'])) {
                    $totalActiveValue += $amount;
                }

                // Check for overdue
                if (!empty($order->expected_date) && $order->expected_date !== '0000-00-00') {
                    if (
                        strtotime($order->expected_date) < strtotime(date('Y-m-d')) &&
                        !in_array($status, ['received', 'completed', 'cancelled'])
                    ) {
                        $overdueValue += $amount;
                    }
                }

                // Track suppliers
                if (!empty($order->supplier_id)) {
                    $suppliers[$order->supplier_id] = true;
                }
            }

            // Map status data to summary
            $summary['pending'] = $statusValues['pending'] ?? 0;
            $summary['pending_count'] = $statusCounts['pending'] ?? 0;

            $summary['sent'] = $statusValues['sent'] ?? 0;
            $summary['sent_count'] = $statusCounts['sent'] ?? 0;

            $summary['in_transit'] = ($statusValues['in_transit'] ?? 0) +
                ($statusValues['shipped'] ?? 0) +
                ($statusValues['partially_received'] ?? 0);
            $summary['in_transit_count'] = ($statusCounts['in_transit'] ?? 0) +
                ($statusCounts['shipped'] ?? 0) +
                ($statusCounts['partially_received'] ?? 0);

            $summary['received'] = ($statusValues['received'] ?? 0) + ($statusValues['completed'] ?? 0);
            $summary['received_count'] = ($statusCounts['received'] ?? 0) + ($statusCounts['completed'] ?? 0);

            $summary['cancelled'] = $statusValues['cancelled'] ?? 0;
            $summary['overdue'] = $overdueValue;
            $summary['total_purchases'] = $totalActiveValue;
            $summary['suppliers'] = count($suppliers);

            $summary['debug_info'] .= " | Status breakdown: " . json_encode($statusCounts);

        } catch (Exception $e) {
            error_log("Error in getPurchaseSummary: " . $e->getMessage());
            $summary['debug_info'] = "Error: " . $e->getMessage();
        }

        return $summary;
    }

    // Alias for backward compatibility with "PurchaseOrder" naming
    public function getPurchaseOrderSummary()
    {
        return $this->getPurchaseSummary();
    }

    // ==============================================
    // RECEIVING & ADVANCED PURCHASE ORDER METHODS
    // (Merged from PurchaseOrder.php for unified model)
    // ==============================================

    /**
     * Get purchase orders specifically for receiving workflow
     * @param array $filters Optional filters
     * @param int $limit Optional limit for pagination
     * @param int $offset Optional offset for pagination
     * @return array|false
     */
    public function getPurchaseOrdersForReceiving($filters = [], $limit = null, $offset = 0)
    {
        try {
            $whereClause = "WHERE 1=1";
            $params = [];

            // Filter by receiving-related statuses
            if (!empty($filters['status_in']) && is_array($filters['status_in'])) {
                $placeholders = str_repeat('?,', count($filters['status_in']) - 1) . '?';
                $whereClause .= " AND p.status IN ($placeholders)";
                $params = array_merge($params, $filters['status_in']);
            }

            // Add supplier filter
            if (!empty($filters['supplier_id']) && is_numeric($filters['supplier_id'])) {
                $whereClause .= " AND p.supplier_id = ?";
                $params[] = $filters['supplier_id'];
            }

            // Add date range filter
            if (!empty($filters['date_from'])) {
                $whereClause .= " AND p.purchase_date >= ?";
                $params[] = $filters['date_from'];
            }
            if (!empty($filters['date_to'])) {
                $whereClause .= " AND p.purchase_date <= ?";
                $params[] = $filters['date_to'];
            }

            $limitClause = "";
            if ($limit) {
                $limitClause = "LIMIT " . (int) $offset . ", " . (int) $limit;
            }

            $sql = "
                SELECT p.purchase_id, p.purchase_id as id, p.po_number, p.supplier_id, 
                       p.purchase_date, p.expected_date, p.status, p.total_amount, 
                       p.notes, p.created_by, p.tracking_number,
                       s.supplier_name, p.created_by as created_by_name
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                $whereClause
                ORDER BY p.purchase_date DESC
                $limitClause
            ";

            $this->db->query($sql);

            // Bind parameters
            foreach ($params as $index => $value) {
                $this->db->bind($index + 1, $value);
            }

            if (!$this->db->execute()) {
                return false;
            }

            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error in getPurchaseOrdersForReceiving: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Search purchase orders for receiving interface
     */
    public function searchForReceiving($query)
    {
        try {
            $this->db->query("
                SELECT p.purchase_id, p.purchase_id as id, p.po_number, p.supplier_id,
                       p.purchase_date, p.expected_date, p.status, p.total_amount,
                       p.tracking_number, s.supplier_name
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                WHERE p.status IN ('pending', 'sent', 'in_transit', 'ready_to_receive')
                  AND ((p.po_number LIKE :query1 OR p.po_number = :exact_query1 OR p.po_number LIKE :starts_with1)
                       OR s.supplier_name LIKE :query2
                       OR p.tracking_number LIKE :query3
                       OR p.purchase_id = :exact_query2)
                ORDER BY 
                    CASE 
                        WHEN p.po_number = :exact_query3 THEN 1
                        WHEN p.po_number LIKE :starts_with2 THEN 2
                        ELSE 3
                    END,
                    p.purchase_date DESC
                LIMIT 20
            ");

            $searchTerm = "%{$query}%";
            $this->db->bind(':query1', $searchTerm);
            $this->db->bind(':query2', $searchTerm);
            $this->db->bind(':query3', $searchTerm);
            $this->db->bind(':exact_query1', $query);
            $this->db->bind(':exact_query2', $query);
            $this->db->bind(':exact_query3', $query);
            $this->db->bind(':starts_with1', $query . '%');
            $this->db->bind(':starts_with2', $query . '%');

            $this->db->execute();
            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error in searchForReceiving: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update received quantity for purchase order item
     * @param int $itemId
     * @param int $quantity
     * @return bool
     */
    public function updateReceivedQuantity($itemId, $quantity)
    {
        try {
            $this->db->query("
                UPDATE purchase_items 
                SET received_quantity = :quantity, received_date = NOW()
                WHERE purchase_item_id = :item_id
            ");
            $this->db->bind(':quantity', $quantity);
            $this->db->bind(':item_id', $itemId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error updating received quantity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get purchase orders requiring approval
     * @param float $threshold
     * @return array
     */
    public function getPurchaseOrdersRequiringApproval($threshold = 1000)
    {
        try {
            $this->db->query("
                SELECT p.purchase_id, p.purchase_id as id, p.po_number, p.supplier_id,
                       p.purchase_date, p.expected_date, p.status, p.total_amount,
                       s.supplier_name
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                WHERE p.status = 'pending' AND p.total_amount > :threshold
                ORDER BY p.total_amount DESC
            ");
            $this->db->bind(':threshold', $threshold);
            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error in getPurchaseOrdersRequiringApproval: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Auto-approve purchase orders below threshold
     * @param float $threshold
     * @return int Number of approved orders
     */
    public function autoApprovePurchaseOrders($threshold = 1000)
    {
        try {
            $this->db->query("
                UPDATE purchases 
                SET status = 'approved' 
                WHERE status = 'pending' AND total_amount <= :threshold
            ");
            $this->db->bind(':threshold', $threshold);
            $this->db->execute();
            return $this->db->rowCount();
        } catch (Exception $e) {
            error_log("Error in autoApprovePurchaseOrders: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get overdue purchase orders
     * @param int $days
     * @return array
     */
    public function getOverduePurchaseOrders($days = 7)
    {
        try {
            $this->db->query("
                SELECT p.purchase_id, p.purchase_id as id, p.po_number, p.supplier_id,
                       p.purchase_date, p.expected_date, p.status, p.total_amount,
                       s.supplier_name,
                       DATEDIFF(CURDATE(), p.expected_date) as days_pending
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                WHERE p.expected_date < DATE_SUB(CURDATE(), INTERVAL :days DAY)
                  AND p.status NOT IN ('received', 'completed', 'cancelled')
                ORDER BY days_pending DESC
            ");
            $this->db->bind(':days', $days);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting overdue POs: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Bulk status update
     * @param array $orderIds
     * @param string $status
     * @return bool
     */
    public function bulkUpdateStatus($orderIds, $status)
    {
        $validStatuses = ['pending', 'sent', 'partially_received', 'received', 'cancelled'];

        if (!in_array($status, $validStatuses) || empty($orderIds)) {
            return false;
        }

        try {
            $placeholders = str_repeat('?,', count($orderIds) - 1) . '?';
            $this->db->query("
                UPDATE purchases 
                SET status = ?, updated_at = NOW() 
                WHERE purchase_id IN ($placeholders)
            ");
            $this->db->bind(1, $status);

            foreach ($orderIds as $index => $id) {
                $this->db->bind($index + 2, $id);
            }

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in bulk status update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update purchase order status with additional data
     */
    public function updatePurchaseStatus($purchaseId, $updateData)
    {
        try {
            $setClause = [];
            $params = [];
            $paramIndex = 1;

            if (isset($updateData['status'])) {
                $setClause[] = 'status = ?';
                $params[$paramIndex++] = $updateData['status'];
            }

            if (isset($updateData['received_date'])) {
                $setClause[] = 'received_date = ?';
                $params[$paramIndex++] = $updateData['received_date'];
            }

            if (isset($updateData['received_by'])) {
                $setClause[] = 'received_by = ?';
                $params[$paramIndex++] = $updateData['received_by'];
            }

            if (isset($updateData['receiving_notes'])) {
                $setClause[] = 'receiving_notes = ?';
                $params[$paramIndex++] = $updateData['receiving_notes'];
            }

            $setClause[] = 'updated_at = NOW()';

            if (empty($setClause)) {
                return false;
            }

            $query = 'UPDATE purchases SET ' . implode(', ', $setClause) . ' WHERE purchase_id = ?';
            $params[$paramIndex] = $purchaseId;

            $this->db->query($query);
            foreach ($params as $index => $value) {
                $this->db->bind($index, $value);
            }

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updatePurchaseStatus: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update item receiving details
     */
    public function updateItemReceiving($itemId, $updateData)
    {
        try {
            $setClause = [];
            $params = [];
            $paramIndex = 1;

            if (isset($updateData['received_quantity'])) {
                $setClause[] = 'received_quantity = ?';
                $params[$paramIndex++] = $updateData['received_quantity'];
            }

            if (isset($updateData['discrepancy_reason'])) {
                $setClause[] = 'discrepancy_reason = ?';
                $params[$paramIndex++] = $updateData['discrepancy_reason'];
            }

            if (isset($updateData['condition_notes'])) {
                $setClause[] = 'condition_notes = ?';
                $params[$paramIndex++] = $updateData['condition_notes'];
            }

            $setClause[] = 'received_date = NOW()';

            if (empty($setClause)) {
                return false;
            }

            $query = 'UPDATE purchase_items SET ' . implode(', ', $setClause) . ' WHERE purchase_item_id = ?';
            $params[$paramIndex] = $itemId;

            $this->db->query($query);
            foreach ($params as $index => $value) {
                $this->db->bind($index, $value);
            }

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateItemReceiving: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all suppliers for dropdown
     */
    public function getSuppliers()
    {
        $this->db->query("SELECT supplier_id, supplier_name FROM suppliers WHERE is_active = 1 ORDER BY supplier_name");
        return $this->db->resultSet();
    }

    /**
     * Get all products for dropdown
     */
    public function getProducts()
    {
        $this->db->query("
            SELECT product_id, product_name, sku, purchase_price 
            FROM products 
            WHERE is_active = 1 
            ORDER BY product_name
        ");
        return $this->db->resultSet();
    }

    /**
     * Delete purchase order
     */
    public function deletePurchaseOrder($id)
    {
        try {
            $this->db->query("START TRANSACTION");
            $this->db->execute();

            // Delete purchase order items first
            $this->db->query("DELETE FROM purchase_items WHERE purchase_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // Delete purchase order
            $this->db->query("DELETE FROM purchases WHERE purchase_id = :id");
            $this->db->bind(':id', $id);
            $result = $this->db->execute();

            $this->db->query("COMMIT");
            $this->db->execute();
            return $result;
        } catch (Exception $e) {
            $this->db->query("ROLLBACK");
            $this->db->execute();
            return false;
        }
    }

    /**
     * Sync purchase order status with receiving status
     * Automatically updates purchase status based on receiving table
     */
    public function syncStatusWithReceiving($purchaseId)
    {
        try {
            // Get the latest receiving status
            $this->db->query("SELECT status, received_date FROM receiving WHERE purchase_id = :purchase_id ORDER BY created_at DESC LIMIT 1");
            $this->db->bind(':purchase_id', $purchaseId);
            $this->db->execute();
            $receiving = $this->db->single();

            if ($receiving) {
                $newStatus = '';
                switch ($receiving->status) {
                    case 'received':
                        $newStatus = 'received';
                        break;
                    case 'partially_received':
                        $newStatus = 'partially_received';
                        break;
                    case 'pending':
                        // Don't change purchase status if receiving is still pending
                        return true;
                }

                if ($newStatus) {
                    // Update purchase status
                    $this->db->query("UPDATE purchases SET status = :status, received_at = :received_at WHERE purchase_id = :purchase_id");
                    $this->db->bind(':status', $newStatus);
                    $this->db->bind(':received_at', $receiving->received_date);
                    $this->db->bind(':purchase_id', $purchaseId);

                    if ($this->db->execute()) {
                        error_log("Purchase {$purchaseId} status synced to: {$newStatus}");
                        return true;
                    }
                }
            }

            return false;

        } catch (Exception $e) {
            error_log("Error syncing purchase status: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Soft delete a purchase order (only if status is 'pending')
     * @param int $id Purchase ID
     * @param string $reason Deletion reason
     * @return bool
     */
    public function softDeletePurchase($id, $reason)
    {
        try {
            // First check if the purchase exists and has pending status
            $this->db->query("SELECT purchase_id, status FROM purchases WHERE purchase_id = ?");
            $this->db->bind(1, $id);
            $this->db->execute();
            $purchase = $this->db->single();

            if (!$purchase) {
                return false;
            }

            if ($purchase->status !== 'pending') {
                return false; // Can only delete pending orders
            }

            // Update the purchase with deleted status and reason
            $this->db->query("
                UPDATE purchases 
                SET status = 'deleted', 
                    cancellation_reason = ?, 
                    updated_at = NOW() 
                WHERE purchase_id = ?
            ");
            $this->db->bind(1, $reason);
            $this->db->bind(2, $id);

            if ($this->db->execute()) {
                // Log the deletion
                $this->logStatusChange($id, 'deleted', 'purchases', "Purchase order soft deleted. Reason: $reason");
                return true;
            }

            return false;

        } catch (Exception $e) {
            error_log("Error soft deleting purchase: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel a purchase order with email notification
     * @param int $id Purchase ID
     * @param string $reason Cancellation reason
     * @return bool
     */
    public function cancelPurchaseOrder($id, $reason)
    {
        try {
            // First check if the purchase exists and has pending status
            $this->db->query("SELECT p.*, s.supplier_name, s.email as supplier_email FROM purchases p LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id WHERE p.purchase_id = ?");
            $this->db->bind(1, $id);
            $this->db->execute();
            $purchase = $this->db->single();

            if (!$purchase) {
                return false;
            }

            if ($purchase->status !== 'pending') {
                return false; // Can only cancel pending orders
            }

            // Update the purchase with cancelled status and reason
            $this->db->query("
                UPDATE purchases 
                SET status = 'cancelled', 
                    cancellation_reason = ?, 
                    cancelled_by = ?,
                    updated_at = NOW() 
                WHERE purchase_id = ?
            ");
            $this->db->bind(1, $reason);
            $this->db->bind(2, $_SESSION['user_id'] ?? 0);
            $this->db->bind(3, $id);

            if ($this->db->execute()) {
                // Log the cancellation
                $this->logStatusChange($id, 'cancelled', 'purchases', "Purchase order cancelled. Reason: $reason");

                // Send cancellation email notification
                try {
                    require_once APPROOT . DS . 'app' . DS . 'helpers' . DS . 'EmailHelper.php';

                    $purchaseData = [
                        'po_number'     => $purchase->po_number,
                        'supplier_name' => $purchase->supplier_name,
                        'purchase_date' => $purchase->purchase_date,
                        'total_amount'  => $purchase->total_amount
                    ];

                    // Try to send email to supplier and internal team
                    $supplierEmail = $purchase->supplier_email;
                    $internalEmail = 'admin@hardwarestore.com'; // Configure as needed

                    EmailHelper::sendPurchaseCancellationNotification(
                        $purchaseData,
                        $reason,
                        $supplierEmail,
                        $internalEmail
                    );

                } catch (Exception $e) {
                    // Log email error but don't fail the cancellation
                    error_log("Email notification failed for cancelled PO #$id: " . $e->getMessage());
                }

                return true;
            }

            return false;

        } catch (Exception $e) {
            error_log("Error cancelling purchase: " . $e->getMessage());
            return false;
        }
    }
}
