<?php
class Sale
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getSales()
    {
        $this->db->query("SELECT * FROM sales");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addSale($data)
    {
        $this->db->query("INSERT INTO sales (customer_id, total_amount, payment_mode) VALUES (:customer_id, :total_amount, :payment_mode)");
        // Bind values
        $this->db->bind(':customer_id', $data['customer_id']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':payment_mode', $data['payment_mode']);

        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function addSaleItem($data)
    {
        $this->db->query("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, discount) VALUES (:sale_id, :product_id, :quantity, :unit_price, :discount)");
        // Bind values
        $this->db->bind(':sale_id', $data['sale_id']);
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':unit_price', $data['unit_price']);
        $this->db->bind(':discount', $data['discount']);

        // Execute
        return $this->db->execute();
    }

    public function getSaleById($id)
    {
        $this->db->query("SELECT * FROM sales WHERE sale_id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function getSaleItemsBySaleId($sale_id)
    {
        $this->db->query("SELECT * FROM sale_items WHERE sale_id = :sale_id");
        $this->db->bind(':sale_id', $sale_id);
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }
}
?>