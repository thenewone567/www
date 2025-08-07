<?php

/**
 * PurchaseOrder Model
 * Handles all purchase order operations including CRUD, status management, and reporting
 */
class PurchaseOrder
{
    private $db;

    // Valid status values
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_PARTIALLY_RECEIVED = 'partially_received';
    const STATUS_RECEIVED = 'received';
    const STATUS_CANCELLED = 'cancelled';

    // Valid statuses array for validation
    private $validStatuses = [
        self::STATUS_PENDING,
        self::STATUS_SENT,
        self::STATUS_PARTIALLY_RECEIVED,
        self::STATUS_RECEIVED,
        self::STATUS_CANCELLED
    ];

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all purchase orders with optional filtering
     * @param array $filters Optional filters (status, supplier_id, date_range)
     * @param int $limit Optional limit for pagination
     * @param int $offset Optional offset for pagination
     * @return array|false
     */
    public function getPurchaseOrders($filters = [], $limit = null, $offset = 0)
    {
        try {
            $whereClause = "WHERE 1=1";
            $params = [];

            // Add status filter
            if (!empty($filters['status']) && in_array($filters['status'], $this->validStatuses)) {
                $whereClause .= " AND po.status = :status";
                $params[':status'] = $filters['status'];
            }

            // Add supplier filter
            if (!empty($filters['supplier_id']) && is_numeric($filters['supplier_id'])) {
                $whereClause .= " AND po.supplier_id = :supplier_id";
                $params[':supplier_id'] = $filters['supplier_id'];
            }

            // Add date range filter
            if (!empty($filters['date_from'])) {
                $whereClause .= " AND po.order_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            if (!empty($filters['date_to'])) {
                $whereClause .= " AND po.order_date <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }

            $limitClause = "";
            if ($limit) {
                $limitClause = " LIMIT :limit OFFSET :offset";
                $params[':limit'] = $limit;
                $params[':offset'] = $offset;
            }

            $sql = "
                SELECT po.*, s.supplier_name, s.contact_info,
                       u.username as created_by_name,
                       COUNT(poi.poi_id) as item_count,
                       COALESCE(SUM(poi.quantity_ordered), 0) as total_items
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
                LEFT JOIN users u ON po.created_by = u.user_id
                LEFT JOIN purchase_order_items poi ON po.po_id = poi.po_id
                {$whereClause}
                GROUP BY po.po_id
                ORDER BY po.created_at DESC
                {$limitClause}
            ";

            $this->db->query($sql);

            // Bind parameters
            foreach ($params as $key => $value) {
                $this->db->bind($key, $value);
            }

            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error in getPurchaseOrders: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get purchase order by ID with full details
     * @param int $id Purchase order ID
     * @return object|false
     */
    public function getPurchaseOrderById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return false;
        }

        try {
            $this->db->query("
                SELECT po.*, s.supplier_name, s.contact_info, s.payment_terms,
                       u.username as created_by_name, u.email as created_by_email,
                       COUNT(poi.poi_id) as item_count,
                       COALESCE(SUM(poi.quantity_ordered * poi.unit_price), 0) as calculated_total
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
                LEFT JOIN users u ON po.created_by = u.user_id
                LEFT JOIN purchase_order_items poi ON po.po_id = poi.po_id
                WHERE po.po_id = :id
                GROUP BY po.po_id
            ");
            $this->db->bind(':id', $id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getPurchaseOrderById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get purchase order items with product details
     * @param int $poId Purchase order ID
     * @return array|false
     */
    public function getPurchaseOrderItems($poId)
    {
        if (!is_numeric($poId) || $poId <= 0) {
            return false;
        }

        try {
            $this->db->query("
                SELECT poi.*, p.product_name, p.sku, p.purchase_price as current_purchase_price,
                       c.category_name, u.unit_name,
                       (poi.quantity_ordered * poi.unit_price) as line_total,
                       CASE 
                           WHEN poi.quantity_received >= poi.quantity_ordered THEN 'complete'
                           WHEN poi.quantity_received > 0 THEN 'partial'
                           ELSE 'pending'
                       END as receive_status
                FROM purchase_order_items poi
                LEFT JOIN products p ON poi.product_id = p.product_id
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN units u ON p.unit_id = u.unit_id
                WHERE poi.po_id = :po_id
                ORDER BY p.product_name
            ");
            $this->db->bind(':po_id', $poId);
            return $this->db->resultSet() ?: [];
        } catch (Exception $e) {
            error_log("Error in getPurchaseOrderItems: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new purchase order with validation
     * @param array $data Purchase order data
     * @return int|false Purchase order ID on success, false on failure
     */
    public function createPurchaseOrder($data)
    {
        $this->db->query("
            INSERT INTO purchase_orders (po_number, supplier_id, order_date, expected_date, status, total_amount, notes, created_by)
            VALUES (:po_number, :supplier_id, :order_date, :expected_date, :status, :total_amount, :notes, :created_by)
        ");

        $this->db->bind(':po_number', $data['po_number']);
        $this->db->bind(':supplier_id', $data['supplier_id']);
        $this->db->bind(':order_date', $data['order_date']);
        $this->db->bind(':expected_date', $data['expected_date']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':notes', $data['notes']);
        $this->db->bind(':created_by', $data['created_by']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    // Add items to purchase order
    public function addPurchaseOrderItem($data)
    {
        $this->db->query("
            INSERT INTO purchase_order_items (po_id, product_id, quantity_ordered, unit_price)
            VALUES (:po_id, :product_id, :quantity_ordered, :unit_price)
        ");

        $this->db->bind(':po_id', $data['po_id']);
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':quantity_ordered', $data['quantity_ordered']);
        $this->db->bind(':unit_price', $data['unit_price']);

        return $this->db->execute();
    }

    // Update purchase order
    public function updatePurchaseOrder($id, $data)
    {
        $this->db->query("
            UPDATE purchase_orders 
            SET supplier_id = :supplier_id, order_date = :order_date, expected_date = :expected_date, 
                status = :status, total_amount = :total_amount, notes = :notes
            WHERE po_id = :id
        ");

        $this->db->bind(':id', $id);
        $this->db->bind(':supplier_id', $data['supplier_id']);
        $this->db->bind(':order_date', $data['order_date']);
        $this->db->bind(':expected_date', $data['expected_date']);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':notes', $data['notes']);

        return $this->db->execute();
    }

    // Update purchase order status
    public function updateStatus($id, $status)
    {
        $this->db->query("UPDATE purchase_orders SET status = :status WHERE po_id = :id");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    // Receive purchase order items
    public function receiveItems($poId, $items)
    {
        try {
            // Start transaction
            $this->db->beginTransaction();

            foreach ($items as $item) {
                // Update quantity received
                $this->db->query("
                    UPDATE purchase_order_items 
                    SET quantity_received = :quantity_received 
                    WHERE poi_id = :item_id
                ");
                $this->db->bind(':quantity_received', $item['quantity_received']);
                $this->db->bind(':item_id', $item['poi_id']);
                $this->db->execute();

                // Update Inventory if quantity received > 0
                if ($item['quantity_received'] > 0) {
                    // Check if Inventory exists for this product
                    $this->db->query("SELECT Inventory_id FROM Inventory WHERE product_id = :product_id LIMIT 1");
                    $this->db->bind(':product_id', $item['product_id']);
                    $existingInventory = $this->db->single();

                    if ($existingInventory) {
                        // Update existing Inventory
                        $this->db->query("
                            UPDATE Inventory 
                            SET current_Inventory = current_Inventory + :quantity 
                            WHERE product_id = :product_id
                        ");
                    } else {
                        // Create new Inventory entry
                        $this->db->query("
                            INSERT INTO Inventory (product_id, current_Inventory, last_updated)
                            VALUES (:product_id, :quantity, NOW())
                        ");
                    }
                    $this->db->bind(':product_id', $item['product_id']);
                    $this->db->bind(':quantity', $item['quantity_received']);
                    $this->db->execute();
                }
            }

            // Check if all items are fully received
            $this->db->query("
                SELECT COUNT(*) as pending_items
                FROM purchase_order_items 
                WHERE po_id = :po_id AND (quantity_received < quantity_ordered OR quantity_received IS NULL)
            ");
            $this->db->bind(':po_id', $poId);
            $result = $this->db->single();

            // Update PO status based on received items
            $newStatus = ($result->pending_items == 0) ? 'received' : 'partially_received';
            $this->updateStatus($poId, $newStatus);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    // Get all suppliers for dropdown
    public function getSuppliers()
    {
        $this->db->query("SELECT supplier_id, supplier_name FROM suppliers WHERE is_active = 1 ORDER BY supplier_name");
        return $this->db->resultSet();
    }

    // Get all products for dropdown
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
            // Extract number from PO-YYYY-XXX format
            preg_match('/PO-\d{4}-(\d+)/', $lastPO->po_number, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        } else {
            $nextNumber = 1;
        }

        return 'PO-' . date('Y') . '-' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Delete purchase order
    public function deletePurchaseOrder($id)
    {
        try {
            $this->db->beginTransaction();

            // Delete purchase order items first
            $this->db->query("DELETE FROM purchase_order_items WHERE po_id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // Delete purchase order
            $this->db->query("DELETE FROM purchase_orders WHERE po_id = :id");
            $this->db->bind(':id', $id);
            $result = $this->db->execute();

            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    // Get purchase order statistics
    public function getPurchaseOrderStats()
    {
        $this->db->query("
            SELECT 
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent_orders,
                SUM(CASE WHEN status = 'received' THEN 1 ELSE 0 END) as received_orders,
                SUM(CASE WHEN status = 'partially_received' THEN 1 ELSE 0 END) as partial_orders,
                SUM(total_amount) as total_value
            FROM purchase_orders
        ");
        return $this->db->single();
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
                UPDATE purchase_orders 
                SET status = 'sent', 
                    updated_at = NOW() 
                WHERE status = 'pending' 
                AND total_amount <= :threshold
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
     * Get purchase orders requiring approval
     * @param float $threshold
     * @return array
     */
    public function getPurchaseOrdersRequiringApproval($threshold = 1000)
    {
        try {
            $this->db->query("
                SELECT po.*, s.supplier_name, 
                       COUNT(poi.purchase_order_item_id) as item_count
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
                LEFT JOIN purchase_order_items poi ON po.purchase_order_id = poi.purchase_order_id
                WHERE po.status = 'pending' 
                AND po.total_amount > :threshold
                GROUP BY po.purchase_order_id
                ORDER BY po.total_amount DESC
            ");
            $this->db->bind(':threshold', $threshold);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error getting POs requiring approval: " . $e->getMessage());
            return [];
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
                SELECT po.*, s.supplier_name,
                       DATEDIFF(NOW(), po.created_at) as days_pending
                FROM purchase_orders po
                LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
                WHERE po.status = 'sent' 
                AND DATEDIFF(NOW(), po.created_at) > :days
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
        if (!in_array($status, $this->validStatuses) || empty($orderIds)) {
            return false;
        }

        try {
            $placeholders = str_repeat('?,', count($orderIds) - 1) . '?';
            $this->db->query("
                UPDATE purchase_orders 
                SET status = :status, updated_at = NOW() 
                WHERE purchase_order_id IN ($placeholders)
            ");
            $this->db->bind(':status', $status);

            foreach ($orderIds as $index => $id) {
                $this->db->bind(':id' . $index, $id);
            }

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in bulk status update: " . $e->getMessage());
            return false;
        }
    }
}
