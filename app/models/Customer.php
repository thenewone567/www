<?php
class Customer
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getCustomers()
    {
        $this->db->query("SELECT * FROM customers");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addCustomer($data)
    {
        $this->db->query("INSERT INTO customers (customer_name, contact_info, credit_limit) VALUES (:customer_name, :contact_info, :credit_limit)");
        // Bind values
        $this->db->bind(':customer_name', $data['customer_name']);
        $this->db->bind(':contact_info', $data['contact_info']);
        $this->db->bind(':credit_limit', $data['credit_limit']);

        // Execute
        return $this->db->execute();
    }

    public function getCustomerById($id)
    {
        $this->db->query("SELECT * FROM customers WHERE customer_id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function updateCustomer($data)
    {
        $this->db->query("UPDATE customers SET customer_name = :customer_name, contact_info = :contact_info, credit_limit = :credit_limit WHERE customer_id = :id");
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':customer_name', $data['customer_name']);
        $this->db->bind(':contact_info', $data['contact_info']);
        $this->db->bind(':credit_limit', $data['credit_limit']);

        // Execute
        return $this->db->execute();
    }

    public function deleteCustomer($id)
    {
        $this->db->query("DELETE FROM customers WHERE customer_id = :id");
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        return $this->db->execute();
    }

    /**
     * Add customer credit/payment
     * @param int $customerId
     * @param float $amount
     * @param string $type (credit/payment)
     * @param string $notes
     * @return bool
     */
    public function addCustomerCredit($customerId, $amount, $type = 'credit', $notes = '')
    {
        try {
            // Add credit transaction record
            $this->db->query("
                INSERT INTO customer_transactions 
                (customer_id, amount, transaction_type, notes, transaction_date)
                VALUES (:customer_id, :amount, :type, :notes, NOW())
            ");
            $this->db->bind(':customer_id', $customerId);
            $this->db->bind(':amount', $amount);
            $this->db->bind(':type', $type);
            $this->db->bind(':notes', $notes);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error adding customer credit: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get customer purchase history
     * @param int $customerId
     * @param int $limit
     * @return array
     */
    public function getCustomerPurchases($customerId, $limit = 50)
    {
        $this->db->query("
            SELECT s.*, 
                   COUNT(si.sale_item_id) as item_count
            FROM sales s
            LEFT JOIN sale_items si ON s.sale_id = si.sale_id
            WHERE s.customer_id = :customer_id
            GROUP BY s.sale_id
            ORDER BY s.sale_date DESC
            LIMIT :limit
        ");
        $this->db->bind(':customer_id', $customerId);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Get customer statistics
     * @param int $customerId
     * @return object
     */
    public function getCustomerStats($customerId)
    {
        $this->db->query("
            SELECT 
                COUNT(s.sale_id) as total_purchases,
                COALESCE(SUM(s.total_amount), 0) as total_spent,
                COALESCE(AVG(s.total_amount), 0) as average_purchase,
                MAX(s.sale_date) as last_purchase_date,
                MIN(s.sale_date) as first_purchase_date
            FROM sales s
            WHERE s.customer_id = :customer_id
        ");
        $this->db->bind(':customer_id', $customerId);
        return $this->db->single();
    }

    /**
     * Update customer credit limit
     * @param int $customerId
     * @param float $creditLimit
     * @return bool
     */
    public function updateCreditLimit($customerId, $creditLimit)
    {
        $this->db->query("UPDATE customers SET credit_limit = :credit_limit WHERE customer_id = :customer_id");
        $this->db->bind(':credit_limit', $creditLimit);
        $this->db->bind(':customer_id', $customerId);
        return $this->db->execute();
    }
}
