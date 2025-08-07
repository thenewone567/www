<?php
class Supplier
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getSuppliers()
    {
        $this->db->query("SELECT * FROM suppliers ORDER BY supplier_name");
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addSupplier($data)
    {
        $this->db->query("INSERT INTO suppliers (supplier_name, contact_person, phone, email, address, gst_number) VALUES (:supplier_name, :contact_person, :phone, :email, :address, :gst_number)");
        // Bind values
        $this->db->bind(':supplier_name', $data['supplier_name']);
        $this->db->bind(':contact_person', $data['contact_person']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':gst_number', $data['gst_number']);

        // Execute
        return $this->db->execute();
    }

    public function getSupplierById($id)
    {
        $this->db->query("SELECT * FROM suppliers WHERE supplier_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function updateSupplier($data)
    {
        $this->db->query("UPDATE suppliers SET supplier_name = :supplier_name, contact_person = :contact_person, phone = :phone, email = :email, address = :address, gst_number = :gst_number WHERE supplier_id = :id");
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':supplier_name', $data['supplier_name']);
        $this->db->bind(':contact_person', $data['contact_person']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':gst_number', $data['gst_number']);

        // Execute
        return $this->db->execute();
    }

    public function deleteSupplier($id)
    {
        $this->db->query("DELETE FROM suppliers WHERE supplier_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Check if supplier name already exists
     * @param string $supplierName
     * @param int $excludeId (optional) - exclude this ID from check (for edit)
     * @return bool
     */
    public function isSupplierNameExists($supplierName, $excludeId = null)
    {
        if ($excludeId) {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE supplier_name = :supplier_name AND supplier_id != :exclude_id");
            $this->db->bind(':exclude_id', $excludeId);
        } else {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE supplier_name = :supplier_name");
        }
        $this->db->bind(':supplier_name', $supplierName);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Check if GST number already exists
     * @param string $gstNumber
     * @param int $excludeId (optional) - exclude this ID from check (for edit)
     * @return bool
     */
    public function isGstNumberExists($gstNumber, $excludeId = null)
    {
        if (empty($gstNumber)) {
            return false; // GST number is optional, so empty is allowed
        }

        if ($excludeId) {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE gst_number = :gst_number AND supplier_id != :exclude_id");
            $this->db->bind(':exclude_id', $excludeId);
        } else {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE gst_number = :gst_number");
        }
        $this->db->bind(':gst_number', $gstNumber);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Check if email already exists
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function isEmailExists($email, $excludeId = null)
    {
        if (empty($email)) {
            return false;
        }

        if ($excludeId) {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE email = :email AND supplier_id != :exclude_id");
            $this->db->bind(':exclude_id', $excludeId);
        } else {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE email = :email");
        }
        $this->db->bind(':email', $email);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Get supplier purchase history
     * @param int $supplierId
     * @param int $limit
     * @return array
     */
    public function getSupplierPurchases($supplierId, $limit = 50)
    {
        $this->db->query("
            SELECT p.*, 
                   COUNT(pi.purchase_item_id) as item_count
            FROM purchases p
            LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
            WHERE p.supplier_id = :supplier_id
            GROUP BY p.purchase_id
            ORDER BY p.purchase_date DESC
            LIMIT :limit
        ");
        $this->db->bind(':supplier_id', $supplierId);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Get supplier statistics
     * @param int $supplierId
     * @return object
     */
    public function getSupplierStats($supplierId)
    {
        $this->db->query("
            SELECT 
                COUNT(p.purchase_id) as total_orders,
                COALESCE(SUM(p.total_amount), 0) as total_purchased,
                COALESCE(AVG(p.total_amount), 0) as average_order,
                MAX(p.purchase_date) as last_order_date,
                MIN(p.purchase_date) as first_order_date
            FROM purchases p
            WHERE p.supplier_id = :supplier_id
        ");
        $this->db->bind(':supplier_id', $supplierId);
        return $this->db->single();
    }

    /**
     * Update supplier due amount
     * @param int $supplierId
     * @param float $amount
     * @return bool
     */
    public function updateDueAmount($supplierId, $amount)
    {
        $this->db->query("UPDATE suppliers SET due_amount = :amount WHERE supplier_id = :supplier_id");
        $this->db->bind(':amount', $amount);
        $this->db->bind(':supplier_id', $supplierId);
        return $this->db->execute();
    }

    /**
     * Add supplier payment
     * @param int $supplierId
     * @param float $amount
     * @param string $notes
     * @return bool
     */
    public function addSupplierPayment($supplierId, $amount, $notes = '')
    {
        try {
            // Record payment
            $this->db->query("
                INSERT INTO supplier_payments 
                (supplier_id, amount, payment_date, notes)
                VALUES (:supplier_id, :amount, NOW(), :notes)
            ");
            $this->db->bind(':supplier_id', $supplierId);
            $this->db->bind(':amount', $amount);
            $this->db->bind(':notes', $notes);
            $this->db->execute();

            // Update due amount
            $this->db->query("
                UPDATE suppliers 
                SET due_amount = due_amount - :amount 
                WHERE supplier_id = :supplier_id
            ");
            $this->db->bind(':amount', $amount);
            $this->db->bind(':supplier_id', $supplierId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error adding supplier payment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get suppliers with outstanding dues
     * @return array
     */
    public function getSuppliersWithDues()
    {
        $this->db->query("
            SELECT * FROM suppliers 
            WHERE due_amount > 0 
            ORDER BY due_amount DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Get supplier evaluation metrics
     * @param int $supplierId
     * @return object
     */
    public function getSupplierEvaluation($supplierId)
    {
        $this->db->query("
            SELECT 
                COUNT(po.purchase_order_id) as total_orders,
                AVG(DATEDIFF(po.updated_at, po.created_at)) as avg_delivery_days,
                COUNT(CASE WHEN po.status = 'received' THEN 1 END) as completed_orders,
                COUNT(CASE WHEN po.status = 'cancelled' THEN 1 END) as cancelled_orders,
                (COUNT(CASE WHEN po.status = 'received' THEN 1 END) / COUNT(po.purchase_order_id) * 100) as success_rate
            FROM suppliers s
            LEFT JOIN purchase_orders po ON s.supplier_id = po.supplier_id
            WHERE s.supplier_id = :supplier_id
            GROUP BY s.supplier_id
        ");
        $this->db->bind(':supplier_id', $supplierId);
        return $this->db->single();
    }

    /**
     * Get supplier overview statistics for dashboard cards
     */
    public function getSupplierOverviewStats()
    {
        // Get total suppliers
        $this->db->query("SELECT COUNT(*) as total FROM suppliers");
        $this->db->execute();
        $total_result = $this->db->single();
        $total = $total_result ? $total_result->total : 0;

        // Get suppliers with recent activity (active - have orders in last 30 days)
        $this->db->query("
            SELECT COUNT(DISTINCT s.supplier_id) as active 
            FROM suppliers s 
            INNER JOIN purchases p ON s.supplier_id = p.supplier_id 
            WHERE p.purchase_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $this->db->execute();
        $active_result = $this->db->single();
        $active = $active_result ? $active_result->active : 0;

        // Get suppliers with pending orders (recent activity in last 7 days)
        $this->db->query("
            SELECT COUNT(DISTINCT s.supplier_id) as pending 
            FROM suppliers s 
            INNER JOIN purchases p ON s.supplier_id = p.supplier_id 
            WHERE p.purchase_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $this->db->execute();
        $pending_result = $this->db->single();
        $pending = $pending_result ? $pending_result->pending : 0;

        // Get suppliers with no recent activity (on hold - no orders in last 90 days)
        $this->db->query("
            SELECT COUNT(*) as onhold 
            FROM suppliers s 
            WHERE s.supplier_id NOT IN (
                SELECT DISTINCT p.supplier_id 
                FROM purchases p 
                WHERE p.purchase_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                AND p.supplier_id IS NOT NULL
            )
        ");
        $this->db->execute();
        $onhold_result = $this->db->single();
        $onhold = $onhold_result ? $onhold_result->onhold : 0;

        return [
            'total' => $total,
            'active' => $active,
            'pending' => $pending,
            'onhold' => $onhold
        ];
    }
}
