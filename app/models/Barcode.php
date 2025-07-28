<?php
class Barcode
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getBarcodes()
    {
        $this->db->query("SELECT * FROM barcode");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addBarcode($data)
    {
        $this->db->query("INSERT INTO barcode (product_id, barcode_value, type) VALUES (:product_id, :barcode_value, :type)");
        // Bind values
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':barcode_value', $data['barcode_value']);
        $this->db->bind(':type', $data['type']);

        // Execute
        return $this->db->execute();
    }

    public function getBarcodeByValue($value)
    {
        $this->db->query("SELECT * FROM barcode WHERE barcode_value = :value");
        $this->db->bind(':value', $value);
        $result = $this->db->single();
        return $result ? $result : null;
    }
}
