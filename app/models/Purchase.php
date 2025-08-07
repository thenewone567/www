<?php

/**
 * Purchase Model
 * Handles purchase operations and integrates with purchase orders system
 * This model provides a simplified interface for basic purchase operations
 */
class Purchase
{
    private $db;

    // Purchase status constants
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_PARTIALLY_RECEIVED = 'partially_received';
    const STATUS_RECEIVED = 'received';
    const STATUS_CANCELLED = 'cancelled';

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all purchases with enhanced information and optional filtering
     * @param array $filters Optional filters (supplier_id, limit)
     * @return array
     */
    public function getPurchases($filters = [])
    {
        try {
            $whereClause = "WHERE 1=1";
            $params = [];

            // Add supplier filter
            if (!empty($filters['supplier_id']) && is_numeric($filters['supplier_id'])) {
                $whereClause .= " AND p.supplier_id = :supplier_id";
                $params[':supplier_id'] = $filters['supplier_id'];
            }

            // Add status filter
            if (!empty($filters['status'])) {
                if (is_array($filters['status'])) {
                    // Handle array of statuses
                    $statusPlaceholders = [];
                    foreach ($filters['status'] as $index => $status) {
                        $placeholder = ":status_" . $index;
                        $statusPlaceholders[] = $placeholder;
                        $params[$placeholder] = $status;
                    }
                    $whereClause .= " AND p.status IN (" . implode(',', $statusPlaceholders) . ")";
                } else {
                    // Handle single status
                    $whereClause .= " AND p.status = :status";
                    $params[':status'] = $filters['status'];
                }
            }

            // Add date received filter
            if (!empty($filters['date_received'])) {
                $whereClause .= " AND DATE(p.updated_at) = :date_received";
                $params[':date_received'] = $filters['date_received'];
            }

            // Add date received from filter (for date ranges)
            if (!empty($filters['date_received_from'])) {
                $whereClause .= " AND DATE(p.updated_at) >= :date_received_from";
                $params[':date_received_from'] = $filters['date_received_from'];
            }

            // Add order by filter
            $orderClause = "ORDER BY p.purchase_date DESC";
            if (!empty($filters['order_by'])) {
                $orderClause = "ORDER BY " . $filters['order_by'];
            }

            $limitClause = "";
            if (!empty($filters['limit']) && is_numeric($filters['limit'])) {
                $limitClause = " LIMIT " . intval($filters['limit']);
            }

            $sql = "
                SELECT p.*, s.supplier_name, s.contact_person, s.phone, s.email,
                       COUNT(pi.purchase_item_id) as item_count,
                       COALESCE(SUM(pi.quantity), 0) as total_items,
                       CONCAT('PO-', LPAD(p.purchase_id, 6, '0')) as purchase_number,
                       p.purchase_date as created_at,
                       NULL as expected_date,
                       'normal' as priority
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
                {$whereClause}
                GROUP BY p.purchase_id
                {$orderClause}
                {$limitClause}
            ";

            $this->db->query($sql);

            // Bind parameters
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }

            $this->db->execute();
            $result = $this->db->resultSet();
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in getPurchases: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get purchase by ID with comprehensive details
     * @param int $id Purchase ID
     * @return object|false
     */
    public function getPurchaseById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return false;
        }

        try {
            $this->db->query("
                SELECT p.*, s.supplier_name, s.contact_person, s.phone, s.email,
                       COUNT(pi.purchase_item_id) as item_count,
                       COALESCE(SUM(pi.quantity * pi.unit_price), 0) as calculated_total
                FROM purchases p
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id
                LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
                WHERE p.purchase_id = :id
                GROUP BY p.purchase_id
            ");
            $this->db->bind(':id', $id);
            $this->db->execute();
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getPurchaseById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get purchase items by purchase ID
     * @param int $purchaseId Purchase ID
     * @return array
     */
    public function getPurchaseItems($purchaseId)
    {
        if (!is_numeric($purchaseId) || $purchaseId <= 0) {
            return [];
        }

        try {
            $this->db->query("
                SELECT pi.*, p.product_name, p.sku, p.category_id,
                       (pi.quantity * pi.unit_price) as total_price,
                       COALESCE(pi.received_quantity, 0) as received_quantity,
                       CASE 
                           WHEN COALESCE(pi.received_quantity, 0) = 0 THEN 'pending'
                           WHEN COALESCE(pi.received_quantity, 0) < pi.quantity THEN 'partial'
                           WHEN COALESCE(pi.received_quantity, 0) >= pi.quantity THEN 'received'
                           ELSE 'pending'
                       END as item_status
                FROM purchase_items pi
                LEFT JOIN products p ON pi.product_id = p.product_id
                WHERE pi.purchase_id = :purchase_id
                ORDER BY pi.purchase_item_id
            ");
            $this->db->bind(':purchase_id', $purchaseId);
            $this->db->execute();
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getPurchaseItems: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Add new purchase with enhanced validation
     * @param array $data Purchase data
     * @return int|false Purchase ID on success, false on failure
     */
    public function addPurchase($data)
    {
        // Validate required fields
        $required = ['supplier_id'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        // Set default values
        $data['purchase_date'] = $data['purchase_date'] ?? date('Y-m-d');

        try {
            $this->db->query("
                INSERT INTO purchases (supplier_id, purchase_date, total_amount, invoice_attachment)
                VALUES (:supplier_id, :purchase_date, :total_amount, :invoice_attachment)
            ");

            $this->db->bind(':supplier_id', $data['supplier_id']);
            $this->db->bind(':purchase_date', $data['purchase_date']);
            $this->db->bind(':total_amount', $data['total_amount'] ?? 0.00);
            $this->db->bind(':invoice_attachment', $data['invoice_attachment'] ?? '');

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log("Error in addPurchase: " . $e->getMessage());
            return false;
        }
    }

    // Add purchase item
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

    /**
     * Update received quantity for a purchase item
     */
    public function updateReceivedQuantity($itemId, $receivedQty)
    {
        try {
            $this->db->query("
                UPDATE purchase_items 
                SET received_quantity = received_quantity + :received_qty 
                WHERE purchase_item_id = :item_id
            ");
            $this->db->bind(':item_id', $itemId);
            $this->db->bind(':received_qty', $receivedQty);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateReceivedQuantity: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update product Inventory when items are received
     */
    public function updateProductInventory($itemId, $receivedQty)
    {
        try {
            // Get product ID from purchase item
            $this->db->query("SELECT product_id FROM purchase_items WHERE purchase_item_id = :item_id");
            $this->db->bind(':item_id', $itemId);
            $this->db->execute();
            $item = $this->db->single();

            if ($item) {
                // Update product Inventory
                $this->db->query("
                    UPDATE products 
                    SET Inventory_quantity = Inventory_quantity + :received_qty 
                    WHERE product_id = :product_id
                ");
                $this->db->bind(':product_id', $item->product_id);
                $this->db->bind(':received_qty', $receivedQty);
                return $this->db->execute();
            }
            return false;
        } catch (Exception $e) {
            error_log("Error in updateProductInventory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update purchase status
     */
    public function updatePurchaseStatus($purchaseId, $status)
    {
        try {
            $this->db->query("
                UPDATE purchases 
                SET status = :status 
                WHERE purchase_id = :purchase_id
            ");
            $this->db->bind(':purchase_id', $purchaseId);
            $this->db->bind(':status', $status);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updatePurchaseStatus: " . $e->getMessage());
            return false;
        }
    }

    // Get suppliers for dropdown
    public function getSuppliers()
    {
        $this->db->query("SELECT supplier_id, supplier_name FROM suppliers WHERE is_active = 1 ORDER BY supplier_name");
        $this->db->execute();
        return $this->db->resultSet();
    }

    // Get products for dropdown
    public function getProducts()
    {
        $this->db->query("
            SELECT product_id, product_name, sku, purchase_price 
            FROM products 
            WHERE is_active = 1 
            ORDER BY product_name
        ");
        $this->db->execute();
        return $this->db->resultSet();
    }

    // Generate next PO number
    public function generatePONumber()
    {
        $this->db->query("
            SELECT po_number 
            FROM purchase_orders 
            WHERE po_number LIKE 'PO-" . date('Y') . "-%' 
            ORDER BY po_number DESC 
            LIMIT 1
        ");

        $lastPO = $this->db->single();

        if ($lastPO) {
            preg_match('/PO-\d{4}-(\d+)/', $lastPO->po_number, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }


        return 'PO-' . date('Y') . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get purchase statistics for dashboard
     * @return object|false
     */
    public function getPurchaseStats()
    {
        try {
            $this->db->query("
                SELECT 
                    COUNT(*) as total_orders,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_orders,
                    SUM(CASE WHEN status = 'received' THEN 1 ELSE 0 END) as completed_orders,
                    SUM(CASE WHEN status = 'partially_received' THEN 1 ELSE 0 END) as partial_orders,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
                    COALESCE(SUM(total_amount), 0) as total_amount,
                    COALESCE(AVG(total_amount), 0) as average_order_value
                FROM purchase_orders
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            ");
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getPurchaseStats: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get recent purchases for quick access
     * @param int $limit Number of recent purchases to return
     * @return array
     */
    public function getRecentPurchases($limit = 5)
    {
        return $this->getPurchases(['limit' => $limit]);
    }

    /**
     * Get purchases by status
     * @param string $status Purchase status
     * @return array
     */
    public function getPurchasesByStatus($status)
    {
        return $this->getPurchases(['status' => $status]);
    }

    /**
     * Search purchases by PO number or supplier name
     * @param string $searchTerm Search term
     * @return array
     */
    public function searchPurchases($searchTerm)
    {
        if (empty($searchTerm)) {
            return [];
        }

        try {
            $this->db->query("
                SELECT po.*, s.supplier_name, u.username as created_by_name,
                       COUNT(poi.poi_id) as item_count
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
                LEFT JOIN users u ON po.created_by = u.user_id
                LEFT JOIN purchase_order_items poi ON po.po_id = poi.po_id
                WHERE po.po_number LIKE :search_term 
                   OR s.supplier_name LIKE :search_term
                   OR po.notes LIKE :search_term
                GROUP BY po.po_id
                ORDER BY po.created_at DESC
                LIMIT 20
            ");

            $searchPattern = '%' . $searchTerm . '%';
            $this->db->bind(':search_term', $searchPattern);

            $result = $this->db->resultSet();
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in searchPurchases: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get bulk locations for receiving shipments
     * Bulk locations start with 'B-' prefix
     */
    public function getBulkLocations()
    {
        try {
            $this->db->query("
                SELECT location_id, location_name, rack, shelf 
                FROM warehouse_locations 
                WHERE location_name LIKE 'B-%' 
                ORDER BY location_name
            ");
            $this->db->execute();

            $result = $this->db->resultSet();
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in getBulkLocations: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a specific bulk location by ID
     */
    public function getBulkLocationById($locationId)
    {
        try {
            $this->db->query("
                SELECT location_id, location_name, rack, shelf 
                FROM warehouse_locations 
                WHERE location_id = :location_id
            ");
            $this->db->bind(':location_id', $locationId);

            $result = $this->db->single();
            return $result;
        } catch (Exception $e) {
            error_log("Error in getBulkLocationById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update product Inventory with location tracking when items are received
     */
    public function updateProductInventoryWithLocation($itemId, $receivedQty, $locationId)
    {
        try {
            $this->db->beginTransaction();

            // Get product ID from purchase item
            $this->db->query("SELECT product_id FROM purchase_items WHERE purchase_item_id = :item_id");
            $this->db->bind(':item_id', $itemId);
            $this->db->execute();
            $item = $this->db->single();

            if ($item) {
                // Update main product Inventory
                $this->db->query("
                    UPDATE products 
                    SET Inventory_quantity = Inventory_quantity + :received_qty 
                    WHERE product_id = :product_id
                ");
                $this->db->bind(':product_id', $item->product_id);
                $this->db->bind(':received_qty', $receivedQty);
                $this->db->execute();

                // Add Inventory entry with location tracking
                $this->db->query("
                    INSERT INTO Inventory (product_id, quantity, location_id, batch_number) 
                    VALUES (:product_id, :quantity, :location_id, :batch_number)
                ");
                $this->db->bind(':product_id', $item->product_id);
                $this->db->bind(':quantity', $receivedQty);
                $this->db->bind(':location_id', $locationId);
                $this->db->bind(':batch_number', 'RCV-' . date('Ymd') . '-' . $itemId);
                $this->db->execute();

                // Log Inventory movement for audit trail
                $this->db->query("
                    INSERT INTO Inventory_movements (product_id, to_location_id, quantity, movement_date) 
                    VALUES (:product_id, :location_id, :quantity, NOW())
                ");
                $this->db->bind(':product_id', $item->product_id);
                $this->db->bind(':location_id', $locationId);
                $this->db->bind(':quantity', $receivedQty);
                $this->db->execute();

                $this->db->commit();
                return true;
            }

            $this->db->rollback();
            return false;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in updateProductInventoryWithLocation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get purchase summary statistics for dashboard
     * @return array
     */
    public function getPurchaseSummaryStats()
    {
        try {
            // Get monthly purchases total
            $this->db->query("
                SELECT COALESCE(SUM(total_amount), 0) as monthly_total
                FROM purchases 
                WHERE YEAR(purchase_date) = YEAR(CURDATE()) 
                AND MONTH(purchase_date) = MONTH(CURDATE())
                AND status != 'cancelled'
            ");
            $monthlyResult = $this->db->single();
            $monthlyPurchases = $monthlyResult ? $monthlyResult->monthly_total : 0;

            // Get pending orders count
            $this->db->query("
                SELECT COUNT(*) as pending_count
                FROM purchases 
                WHERE status IN ('pending', 'sent', 'partially_received')
            ");
            $pendingResult = $this->db->single();
            $pendingOrders = $pendingResult ? $pendingResult->pending_count : 0;

            // Get active suppliers count (suppliers with purchases in last 6 months)
            $this->db->query("
                SELECT COUNT(DISTINCT supplier_id) as active_count
                FROM purchases 
                WHERE purchase_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
                AND status != 'cancelled'
            ");
            $activeResult = $this->db->single();
            $activeSuppliers = $activeResult ? $activeResult->active_count : 0;

            // Get items received this month
            $this->db->query("
                SELECT COALESCE(SUM(pi.received_quantity), 0) as items_received
                FROM purchase_items pi
                JOIN purchases p ON pi.purchase_id = p.purchase_id
                WHERE YEAR(p.purchase_date) = YEAR(CURDATE()) 
                AND MONTH(p.purchase_date) = MONTH(CURDATE())
                AND pi.received_quantity > 0
            ");
            $itemsResult = $this->db->single();
            $itemsReceived = $itemsResult ? $itemsResult->items_received : 0;

            return [
                'monthly_purchases' => number_format($monthlyPurchases, 2),
                'pending_orders' => $pendingOrders,
                'active_suppliers' => $activeSuppliers,
                'items_received' => $itemsReceived
            ];
        } catch (Exception $e) {
            error_log("Error getting purchase summary stats: " . $e->getMessage());
            return [
                'monthly_purchases' => '0.00',
                'pending_orders' => 0,
                'active_suppliers' => 0,
                'items_received' => 0
            ];
        }
    }

    /**
     * Process receipt data and update purchase status
     * @param array $receiptData Receipt information
     * @param array $processedItems Array of items being received
     * @return bool Success status
     */
    public function processReceipt($receiptData, $processedItems)
    {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Update purchase status and receipt information
            $this->db->query("
                UPDATE purchases 
                SET status = :status, 
                    receipt_reference = :receipt_reference,
                    delivery_note = :delivery_note,
                    notes = :notes,
                    received_by = :received_by,
                    updated_at = NOW()
                WHERE purchase_id = :purchase_id
            ");

            $status = ($receiptData['action'] === 'complete') ? 'received' : 'partially_received';

            $this->db->bind(':status', $status);
            $this->db->bind(':receipt_reference', $receiptData['receipt_reference']);
            $this->db->bind(':delivery_note', $receiptData['delivery_note']);
            $this->db->bind(':notes', $receiptData['notes']);
            $this->db->bind(':received_by', $receiptData['received_by']);
            $this->db->bind(':purchase_id', $receiptData['purchase_id']);

            if (!$this->db->execute()) {
                throw new Exception("Failed to update purchase record");
            }

            // Process each received item
            foreach ($processedItems as $item) {
                // Update purchase_items with received quantity
                $this->db->query("
                    UPDATE purchase_items 
                    SET received_quantity = COALESCE(received_quantity, 0) + :receive_quantity,
                        updated_at = NOW()
                    WHERE purchase_item_id = :purchase_item_id
                ");

                $this->db->bind(':receive_quantity', $item['receive_quantity']);
                $this->db->bind(':purchase_item_id', $item['purchase_item_id']);

                if (!$this->db->execute()) {
                    throw new Exception("Failed to update purchase item");
                }

                // Update inventory/stock levels
                // First check if product exists in inventory
                $this->db->query("
                    SELECT stock_quantity 
                    FROM products 
                    WHERE product_id = :product_id
                ");
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->execute();
                $product = $this->db->single();

                if ($product) {
                    // Update product stock quantity
                    $this->db->query("
                        UPDATE products 
                        SET stock_quantity = stock_quantity + :receive_quantity,
                            updated_at = NOW()
                        WHERE product_id = :product_id
                    ");

                    $this->db->bind(':receive_quantity', $item['receive_quantity']);
                    $this->db->bind(':product_id', $item['product_id']);

                    if (!$this->db->execute()) {
                        throw new Exception("Failed to update product inventory");
                    }
                }

                // Log inventory transaction if inventory_transactions table exists
                $this->db->query("SHOW TABLES LIKE 'inventory_transactions'");
                $this->db->execute();
                if ($this->db->single()) {
                    $this->db->query("
                        INSERT INTO inventory_transactions 
                        (product_id, transaction_type, quantity, reference_type, reference_id, notes, created_at)
                        VALUES (:product_id, 'receive', :quantity, 'purchase', :purchase_id, :notes, NOW())
                    ");

                    $this->db->bind(':product_id', $item['product_id']);
                    $this->db->bind(':quantity', $item['receive_quantity']);
                    $this->db->bind(':purchase_id', $receiptData['purchase_id']);
                    $this->db->bind(':notes', 'Received from purchase order');

                    $this->db->execute();
                }
            }

            // Commit transaction
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Rollback transaction on error
            $this->db->rollback();
            error_log("Error processing receipt: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update item location
     */
    public function updateItemLocation($itemId, $locationId)
    {
        try {
            $this->db->query("UPDATE purchase_items SET location_id = :location_id WHERE id = :item_id");
            $this->db->bind(':location_id', $locationId);
            $this->db->bind(':item_id', $itemId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error updating item location: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update item condition
     */
    public function updateItemCondition($itemId, $condition)
    {
        try {
            $this->db->query("UPDATE purchase_items SET condition_status = :condition WHERE id = :item_id");
            $this->db->bind(':condition', $condition);
            $this->db->bind(':item_id', $itemId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error updating item condition: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Count purchases by date range
     */
    public function countPurchasesByDateRange($dateFrom, $dateTo)
    {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM purchases 
                             WHERE order_date BETWEEN :date_from AND :date_to");
            $this->db->bind(':date_from', $dateFrom);
            $this->db->bind(':date_to', $dateTo);
            $result = $this->db->single();
            return $result ? $result->count : 0;
        } catch (Exception $e) {
            error_log("Error counting purchases by date range: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Count purchases by status within date range
     */
    public function countByStatus($status, $dateFrom, $dateTo)
    {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM purchases 
                             WHERE status = :status AND order_date BETWEEN :date_from AND :date_to");
            $this->db->bind(':status', $status);
            $this->db->bind(':date_from', $dateFrom);
            $this->db->bind(':date_to', $dateTo);
            $result = $this->db->single();
            return $result ? $result->count : 0;
        } catch (Exception $e) {
            error_log("Error counting purchases by status: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get detailed receiving report data
     */
    public function getDetailedReceivingReport($filters)
    {
        try {
            $sql = "SELECT p.*, s.name as supplier_name, 
                           COUNT(pi.id) as total_items,
                           SUM(pi.quantity * pi.unit_price) as total_value,
                           p.received_date
                    FROM purchases p
                    LEFT JOIN suppliers s ON p.supplier_id = s.id
                    LEFT JOIN purchase_items pi ON p.id = pi.purchase_id
                    WHERE p.order_date BETWEEN :date_from AND :date_to";

            $params = [
                ':date_from' => $filters['date_from'],
                ':date_to' => $filters['date_to']
            ];

            if (!empty($filters['supplier'])) {
                $sql .= " AND p.supplier_id = :supplier_id";
                $params[':supplier_id'] = $filters['supplier'];
            }

            $sql .= " GROUP BY p.id ORDER BY p.order_date DESC";

            $this->db->query($sql);
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting detailed receiving report: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get supplier receiving report data
     */
    public function getSupplierReceivingReport($filters)
    {
        try {
            $sql = "SELECT s.name as supplier_name,
                           COUNT(DISTINCT p.id) as total_orders,
                           COUNT(DISTINCT CASE WHEN p.status = 'received' THEN p.id END) as completed_orders,
                           COUNT(DISTINCT CASE WHEN p.status IN ('pending', 'sent') THEN p.id END) as pending_orders,
                           COALESCE(SUM(pi.quantity * pi.unit_price), 0) as total_value,
                           AVG(CASE WHEN p.received_date IS NOT NULL THEN 
                               DATEDIFF(p.received_date, p.order_date) END) as avg_processing_time,
                           CASE WHEN COUNT(DISTINCT p.id) > 0 THEN
                               (COUNT(DISTINCT CASE WHEN p.status = 'received' THEN p.id END) * 100.0 / COUNT(DISTINCT p.id))
                           ELSE 0 END as performance_score
                    FROM suppliers s
                    LEFT JOIN purchases p ON s.id = p.supplier_id 
                        AND p.order_date BETWEEN :date_from AND :date_to
                    LEFT JOIN purchase_items pi ON p.id = pi.purchase_id";

            $params = [
                ':date_from' => $filters['date_from'],
                ':date_to' => $filters['date_to']
            ];

            if (!empty($filters['supplier'])) {
                $sql .= " WHERE s.id = :supplier_id";
                $params[':supplier_id'] = $filters['supplier'];
            }

            $sql .= " GROUP BY s.id, s.name HAVING total_orders > 0 ORDER BY performance_score DESC";

            $this->db->query($sql);
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting supplier receiving report: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Count completed receipts within date range
     */
    public function countCompletedReceipts($dateFrom, $dateTo)
    {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM purchases 
                             WHERE status = 'received' AND received_date BETWEEN :date_from AND :date_to");
            $this->db->bind(':date_from', $dateFrom);
            $this->db->bind(':date_to', $dateTo);
            $result = $this->db->single();
            return $result ? $result->count : 0;
        } catch (Exception $e) {
            error_log("Error counting completed receipts: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get total receiving value within date range
     */
    public function getTotalReceivingValue($dateFrom, $dateTo)
    {
        try {
            $this->db->query("SELECT COALESCE(SUM(pi.received_quantity * pi.unit_price), 0) as total_value
                             FROM purchase_items pi
                             JOIN purchases p ON pi.purchase_id = p.id
                             WHERE p.received_date BETWEEN :date_from AND :date_to
                             AND pi.received_quantity > 0");
            $this->db->bind(':date_from', $dateFrom);
            $this->db->bind(':date_to', $dateTo);
            $result = $this->db->single();
            return $result ? $result->total_value : 0;
        } catch (Exception $e) {
            error_log("Error getting total receiving value: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get all purchases that need receiving (legacy compatibility)
     */
    public function getPurchasesForReceiving()
    {
        return $this->getPurchases([
            'status' => ['sent', 'pending', 'partially_received']
        ]);
    }

    /**
     * Get received purchases (legacy compatibility)
     */
    public function getReceivedPurchases($filters = [])
    {
        $receivedFilters = array_merge($filters, [
            'status' => ['received', 'completed']
        ]);
        return $this->getPurchases($receivedFilters);
    }

    /**
     * Update purchase items with bulk location assignment
     */
    public function bulkUpdateItemLocations($purchaseId, $locationId)
    {
        try {
            $this->db->query("UPDATE purchase_items SET location_id = :location_id 
                             WHERE purchase_id = :purchase_id");
            $this->db->bind(':location_id', $locationId);
            $this->db->bind(':purchase_id', $purchaseId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error bulk updating item locations: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get purchase statistics for reports
     */
    public function getReceivingStatistics($dateFrom = null, $dateTo = null)
    {
        if (!$dateFrom)
            $dateFrom = date('Y-m-01');
        if (!$dateTo)
            $dateTo = date('Y-m-d');

        try {
            $this->db->query("SELECT 
                                COUNT(DISTINCT CASE WHEN status IN ('sent', 'pending') THEN id END) as pending_count,
                                COUNT(DISTINCT CASE WHEN status = 'partially_received' THEN id END) as partial_count,
                                COUNT(DISTINCT CASE WHEN status = 'received' THEN id END) as completed_count,
                                COUNT(DISTINCT CASE WHEN received_date = CURDATE() THEN id END) as completed_today,
                                COALESCE(SUM(CASE WHEN status = 'received' THEN 
                                    (SELECT SUM(quantity * unit_price) FROM purchase_items WHERE purchase_id = purchases.id)
                                END), 0) as total_received_value
                             FROM purchases 
                             WHERE order_date BETWEEN :date_from AND :date_to");

            $this->db->bind(':date_from', $dateFrom);
            $this->db->bind(':date_to', $dateTo);

            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error getting receiving statistics: " . $e->getMessage());
            return (object) [
                'pending_count' => 0,
                'partial_count' => 0,
                'completed_count' => 0,
                'completed_today' => 0,
                'total_received_value' => 0
            ];
        }
    }
}