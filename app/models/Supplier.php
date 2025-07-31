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
        $this->db->query("SELECT * FROM suppliers");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addSupplier($data)
    {
        $this->db->query("INSERT INTO suppliers (supplier_name, contact_info, gst_info, due_amount) VALUES (:supplier_name, :contact_info, :gst_info, :due_amount)");
        // Bind values
        $this->db->bind(':supplier_name', $data['supplier_name']);
        $this->db->bind(':contact_info', $data['contact_info']);
        $this->db->bind(':gst_info', $data['gst_info']);
        $this->db->bind(':due_amount', $data['due_amount']);

        // Execute
        return $this->db->execute();
    }

    public function getSupplierById($id)
    {
        $this->db->query("SELECT * FROM suppliers WHERE supplier_id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function updateSupplier($data)
    {
        $this->db->query("UPDATE suppliers SET supplier_name = :supplier_name, contact_info = :contact_info, gst_info = :gst_info, due_amount = :due_amount WHERE supplier_id = :id");
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':supplier_name', $data['supplier_name']);
        $this->db->bind(':contact_info', $data['contact_info']);
        $this->db->bind(':gst_info', $data['gst_info']);
        $this->db->bind(':due_amount', $data['due_amount']);

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
}
