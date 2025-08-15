<?php
class Location
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getLocations()
    {
        $this->db->query("SELECT 
            location_id, 
            location_code, 
            standardized_address,
            location_name, 
            location_type,
            zone,
            CONCAT(COALESCE(standardized_address, location_code), ' - ', location_name) as display_name
        FROM locations 
        ORDER BY standardized_address, location_code");
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getLocationById($id)
    {
        $this->db->query("SELECT * FROM locations WHERE location_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function addLocation($data)
    {
        $this->db->query("INSERT INTO locations (location_code, standardized_address, location_name, location_type, zone, aisle, shelf, bin) 
                         VALUES (:location_code, :standardized_address, :location_name, :location_type, :zone, :aisle, :shelf, :bin)");
        $this->db->bind(':location_code', $data['location_code']);
        $this->db->bind(':standardized_address', $data['standardized_address'] ?? '');
        $this->db->bind(':location_name', $data['location_name']);
        $this->db->bind(':location_type', $data['location_type']);
        $this->db->bind(':zone', $data['zone'] ?? '');
        $this->db->bind(':aisle', $data['aisle'] ?? '');
        $this->db->bind(':shelf', $data['shelf'] ?? '');
        $this->db->bind(':bin', $data['bin'] ?? '');
        return $this->db->execute();
    }
}
?>