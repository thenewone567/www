<?php
class Purchase
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getPurchases()
    {
        $this->db->query("SELECT * FROM purchases");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addPurchase($data)
    {
        $this->db->query("INSERT INTO purchases (supplier_id, total_amount, invoice_attachment) VALUES (:supplier_id, :total_amount, :invoice_attachment)");
        // Bind values
        $this->db->bind(':supplier_id', $data['supplier_id']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':invoice_attachment', $data['invoice_attachment']);

        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function addPurchaseItem($data)
    {
        $this->db->query("INSERT INTO purchase_items (purchase_id, product_id, quantity, unit_price) VALUES (:purchase_id, :product_id, :quantity, :unit_price)");
        // Bind values
        $this->db->bind(':purchase_id', $data['purchase_id']);
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':unit_price', $data['unit_price']);

        // Execute
        return $this->db->execute();
    }
}
