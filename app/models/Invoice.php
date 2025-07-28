<?php
class Invoice
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getInvoices()
    {
        $this->db->query("SELECT * FROM invoices");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addInvoice($data)
    {
        $this->db->query("INSERT INTO invoices (sale_id, invoice_number, total_amount, tax_amount, discount_amount) VALUES (:sale_id, :invoice_number, :total_amount, :tax_amount, :discount_amount)");
        // Bind values
        $this->db->bind(':sale_id', $data['sale_id']);
        $this->db->bind(':invoice_number', $data['invoice_number']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':tax_amount', $data['tax_amount']);
        $this->db->bind(':discount_amount', $data['discount_amount']);

        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function getInvoiceById($id)
    {
        $this->db->query("SELECT * FROM invoices WHERE invoice_id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result ? $result : null;
    }
}
