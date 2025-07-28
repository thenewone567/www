<?php
class Stock
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getStock()
    {
        $this->db->query("SELECT * FROM stock");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getStockMovements()
    {
        $this->db->query("SELECT * FROM stock_movements");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getWarehouseLocations()
    {
        $this->db->query("SELECT * FROM warehouse_locations");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addStock($data)
    {
        $this->db->query("INSERT INTO stock (product_id, batch_number, expiry_date, quantity, location_id) VALUES (:product_id, :batch_number, :expiry_date, :quantity, :location_id)");
        // Bind values
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':batch_number', $data['batch_number']);
        $this->db->bind(':expiry_date', $data['expiry_date']);
        $this->db->bind(':quantity', $data['quantity']);
        $this->db->bind(':location_id', $data['location_id']);

        // Execute
        return $this->db->execute();
    }

    public function addStockMovement($data)
    {
        $this->db->query("INSERT INTO stock_movements (product_id, from_location_id, to_location_id, quantity) VALUES (:product_id, :from_location_id, :to_location_id, :quantity)");
        // Bind values
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':from_location_id', $data['from_location_id']);
        $this->db->bind(':to_location_id', $data['to_location_id']);
        $this->db->bind(':quantity', $data['quantity']);

        // Execute
        return $this->db->execute();
    }

    public function addWarehouseLocation($data)
    {
        $this->db->query("INSERT INTO warehouse_locations (location_name, rack, shelf) VALUES (:location_name, :rack, :shelf)");
        // Bind values
        $this->db->bind(':location_name', $data['location_name']);
        $this->db->bind(':rack', $data['rack']);
        $this->db->bind(':shelf', $data['shelf']);

        // Execute
        return $this->db->execute();
    }
}
