<?php
class Supplier {
    private $db;

    public function __construct(){
        $this->db = new Database;
    }

    public function getSuppliers(){
        $this->db->query("SELECT * FROM suppliers");
        return $this->db->resultSet();
    }

    public function addSupplier($data){
        $this->db->query("INSERT INTO suppliers (supplier_name, contact_info, gst_info, due_amount) VALUES (:supplier_name, :contact_info, :gst_info, :due_amount)");
        // Bind values
        $this->db->bind(':supplier_name', $data['supplier_name']);
        $this->db->bind(':contact_info', $data['contact_info']);
        $this->db->bind(':gst_info', $data['gst_info']);
        $this->db->bind(':due_amount', $data['due_amount']);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function getSupplierById($id){
        $this->db->query("SELECT * FROM suppliers WHERE supplier_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    public function updateSupplier($data){
        $this->db->query("UPDATE suppliers SET supplier_name = :supplier_name, contact_info = :contact_info, gst_info = :gst_info, due_amount = :due_amount WHERE supplier_id = :id");
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':supplier_name', $data['supplier_name']);
        $this->db->bind(':contact_info', $data['contact_info']);
        $this->db->bind(':gst_info', $data['gst_info']);
        $this->db->bind(':due_amount', $data['due_amount']);

        // Execute
        if($this->db->execute()){
            return true;
        } else {
            return false;
        }
    }

    public function deleteSupplier($id){
        $this->db->query("DELETE FROM suppliers WHERE supplier_id = :id");
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
