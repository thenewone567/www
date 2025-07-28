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
}
