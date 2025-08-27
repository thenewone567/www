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
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addCustomer($data)
    {
        // Map controller data to database fields
        // Use contact person name as customer name when company name is not provided
        $customerName = !empty($data['company_name']) ? $data['company_name'] : ($data['contact_person'] ?? '');
        $contactInfo = json_encode([
            'contact_person' => $data['contact_person'] ?? '',
            'email'          => $data['email'] ?? '',
            'phone'          => $data['phone'] ?? '',
            'address'        => $data['address'] ?? '',
            'city'           => $data['city'] ?? '',
            'state'          => $data['state'] ?? '',
            'zip_code'       => $data['zip_code'] ?? '',
            'discount_type'  => $data['discount_type'] ?? 'percentage',
            'discount_value' => $data['discount_value'] ?? 0,
            'payment_terms'  => $data['payment_terms'] ?? 30
        ]);
        $creditLimit = $data['credit_limit'] ?? 0;

        $this->db->query("INSERT INTO customers (customer_name, contact_info, credit_limit, status) VALUES (:customer_name, :contact_info, :credit_limit, 'active')");

        // Bind values
        $this->db->bind(':customer_name', $customerName);
        $this->db->bind(':contact_info', $contactInfo);
        $this->db->bind(':credit_limit', $creditLimit);

        // Execute
        return $this->db->execute();
    }

    public function getCustomerById($id)
    {
        $this->db->query("SELECT * FROM customers WHERE customer_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
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

    /**
     * Get customer by email for login
     */
    public function getCustomerByEmail($email)
    {
        $this->db->query("SELECT * FROM customers WHERE JSON_EXTRACT(contact_info, '$.email') = :email LIMIT 1");
        $this->db->bind(':email', $email);
        $this->db->execute();
        $customer = $this->db->single();

        if ($customer) {
            // Parse contact_info JSON
            $contactInfo = json_decode($customer->contact_info, true);
            if ($contactInfo) {
                $customer->email = $contactInfo['email'] ?? '';
                $customer->phone = $contactInfo['phone'] ?? '';
                $customer->contact_person = $contactInfo['contact_person'] ?? '';
            }
        }

        return $customer;
    }

    /**
     * Update customer profile
     */
    public function updateCustomerProfile($data)
    {
        // Get existing customer data
        $existing = $this->getCustomerById($data['customer_id']);
        if (!$existing) {
            return false;
        }

        // Parse existing contact_info and update it
        $contactInfo = json_decode($existing->contact_info, true) ?: [];

        // Update contact_info with new data
        $contactInfo['contact_person'] = $data['contact_person'] ?? $contactInfo['contact_person'] ?? '';
        $contactInfo['email'] = $data['email'] ?? $contactInfo['email'] ?? '';
        $contactInfo['phone'] = $data['phone'] ?? $contactInfo['phone'] ?? '';
        $contactInfo['address'] = $data['address'] ?? $contactInfo['address'] ?? '';
        $contactInfo['city'] = $data['city'] ?? $contactInfo['city'] ?? '';
        $contactInfo['state'] = $data['state'] ?? $contactInfo['state'] ?? '';
        $contactInfo['zip_code'] = $data['zip_code'] ?? $contactInfo['zip_code'] ?? '';

        $this->db->query("UPDATE customers SET 
            customer_name = :customer_name,
            contact_info = :contact_info 
            WHERE customer_id = :customer_id");

        $this->db->bind(':customer_name', $data['contact_person'] ?? $existing->customer_name);
        $this->db->bind(':contact_info', json_encode($contactInfo));
        $this->db->bind(':customer_id', $data['customer_id']);

        return $this->db->execute();
    }
}
