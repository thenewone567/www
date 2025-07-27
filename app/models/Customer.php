<?php
class Customer {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getCustomers(){
        $this->db->query("SELECT * FROM customers");
        return $this->db->resultSet();
    }

    public function addCustomer($data){
        $this->db->query("INSERT INTO customers (customer_name, contact_info, credit_limit) VALUES (:customer_name, :contact_info, :credit_limit)");
        // Bind values
        $this->db->bind(':customer_name', $data['customer_name']);
        $this->db->bind(':contact_info', $data['contact_info']);
        $this->db->bind(':credit_limit', $data['credit_limit']);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function getCustomerById($id){
        $this->db->query("SELECT * FROM customers WHERE customer_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateCustomer($data){
        $this->db->query("UPDATE customers SET customer_name = :customer_name, contact_info = :contact_info, credit_limit = :credit_limit WHERE customer_id = :id");
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':customer_name', $data['customer_name']);
        $this->db->bind(':contact_info', $data['contact_info']);
        $this->db->bind(':credit_limit', $data['credit_limit']);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function deleteCustomer($id){
        $this->db->query("DELETE FROM customers WHERE customer_id = :id");
        // Bind values
        $this->db->bind(':id', $id);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }
}
