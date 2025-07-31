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
     * @param array $filters Optional filters (status, supplier_id, limit)
     * @return array
     */
    public function getPurchases($filters = [])
    {
        try {
            $whereClause = "WHERE 1=1";
            $params = [];

            // Add status filter
            if (!empty($filters['status'])) {
                $whereClause .= " AND po.status = :status";
                $params[':status'] = $filters['status'];
            }

            // Add supplier filter
            if (!empty($filters['supplier_id']) && is_numeric($filters['supplier_id'])) {
                $whereClause .= " AND po.supplier_id = :supplier_id";
                $params[':supplier_id'] = $filters['supplier_id'];
            }

            $limitClause = "";
            if (!empty($filters['limit']) && is_numeric($filters['limit'])) {
                $limitClause = " LIMIT :limit";
                $params[':limit'] = $filters['limit'];
            }

            $sql = "
                SELECT po.*, s.supplier_name, s.contact_info,
                       u.username as created_by_name,
                       COUNT(poi.poi_id) as item_count,
                       COALESCE(SUM(poi.quantity_ordered), 0) as total_items_ordered,
                       COALESCE(SUM(poi.quantity_received), 0) as total_items_received,
                       CASE 
                           WHEN po.status = 'received' THEN 'Complete'
                           WHEN po.status = 'partially_received' THEN 'Partial'
                           WHEN po.status = 'sent' THEN 'In Transit'
                           WHEN po.status = 'pending' THEN 'Pending'
                           WHEN po.status = 'cancelled' THEN 'Cancelled'
                           ELSE 'Unknown'
                       END as status_display
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

            $result = $this->db->resultSet();
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in getPurchases: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get purchase by ID with comprehensive details
     * @param int $id Purchase order ID
     * @return object|false
     */
    public function getPurchaseById($id)
    {
        if (!is_numeric($id) || $id <= 0) {
            return false;
        }

        try {
            $this->db->query("
                SELECT po.*, s.supplier_name, s.contact_info, s.payment_terms,
                       u.username as created_by_name, u.email as created_by_email,
                       COUNT(poi.poi_id) as item_count,
                       COALESCE(SUM(poi.quantity_ordered * poi.unit_price), 0) as calculated_total,
                       CASE 
                           WHEN po.status = 'received' THEN 'Complete'
                           WHEN po.status = 'partially_received' THEN 'Partial'
                           WHEN po.status = 'sent' THEN 'In Transit'
                           WHEN po.status = 'pending' THEN 'Pending'
                           WHEN po.status = 'cancelled' THEN 'Cancelled'
                           ELSE 'Unknown'
                       END as status_display
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
            error_log("Error in getPurchaseById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add new purchase order with enhanced validation
     * @param array $data Purchase order data
     * @return int|false Purchase order ID on success, false on failure
     */
    public function addPurchase($data)
    {
        // Validate required fields
        $required = ['supplier_id', 'created_by'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }

        // Set default values
        $data['po_number'] = $data['po_number'] ?? $this->generatePONumber();
        $data['order_date'] = $data['order_date'] ?? date('Y-m-d');
        $data['status'] = $data['status'] ?? self::STATUS_PENDING;

        try {
            $this->db->query("
                INSERT INTO purchase_orders (po_number, supplier_id, order_date, expected_date, status, total_amount, notes, created_by)
                VALUES (:po_number, :supplier_id, :order_date, :expected_date, :status, :total_amount, :notes, :created_by)
            ");

            $this->db->bind(':po_number', $data['po_number']);
            $this->db->bind(':supplier_id', $data['supplier_id']);
            $this->db->bind(':order_date', $data['order_date']);
            $this->db->bind(':expected_date', $data['expected_date'] ?? null);
            $this->db->bind(':status', $data['status']);
            $this->db->bind(':total_amount', $data['total_amount'] ?? 0.00);
            $this->db->bind(':notes', $data['notes'] ?? '');
            $this->db->bind(':created_by', $data['created_by']);

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

    // Add purchase order item
    public function addPurchaseItem($data)
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

    // Get suppliers for dropdown
    public function getSuppliers()
    {
        $this->db->query("SELECT supplier_id, supplier_name FROM suppliers WHERE is_active = 1 ORDER BY supplier_name");
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
}