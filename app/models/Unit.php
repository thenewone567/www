<?php
/*
 Unit Model
 Handles product unit/measurement data operations
*/

class Unit
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Get unit by name
    // @param string $name
    // @return object|null
    public function getUnitByName($name)
    {
        try {
            $this->db->query('SELECT * FROM units WHERE unit_name = :unit_name');
            $this->db->bind(':unit_name', $name);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getUnitByName: " . $e->getMessage());
            return null;
        }
    }

    // Get all units
    // @return array
    public function getUnits()
    {
        try {
            $this->db->query('SELECT * FROM units ORDER BY unit_name ASC');
            $this->db->execute();
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getUnits: " . $e->getMessage());
            return [];
        }
    }

    // Get unit by ID
    // @param int $id
    // @return object|null
    public function getUnitById($id)
    {
        try {
            $this->db->query('SELECT * FROM units WHERE unit_id = :unit_id');
            $this->db->bind(':unit_id', $id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getUnitById: " . $e->getMessage());
            return null;
        }
    }

    // Add new unit
    // @param array $data
    // @return bool
    public function addUnit($data)
    {
        try {
            $this->db->query('INSERT INTO units (unit_name, unit_symbol, description) VALUES (:unit_name, :unit_symbol, :description)');
            $this->db->bind(':unit_name', $data['unit_name']);
            $this->db->bind(':unit_symbol', isset($data['unit_symbol']) ? $data['unit_symbol'] : '');
            $this->db->bind(':description', isset($data['description']) ? $data['description'] : '');
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in addUnit: " . $e->getMessage());
            return false;
        }
    }

    // Update unit
    // @param array $data
    // @return bool
    public function updateUnit($data)
    {
        try {
            $this->db->query('UPDATE units SET unit_name = :unit_name, unit_symbol = :unit_symbol, description = :description WHERE unit_id = :unit_id');
            $this->db->bind(':unit_id', $data['unit_id']);
            $this->db->bind(':unit_name', $data['unit_name']);
            $this->db->bind(':unit_symbol', isset($data['unit_symbol']) ? $data['unit_symbol'] : '');
            $this->db->bind(':description', isset($data['description']) ? $data['description'] : '');
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateUnit: " . $e->getMessage());
            return false;
        }
    }

    // Delete unit
    // @param int $id
    // @return bool
    public function deleteUnit($id)
    {
        try {
            // Check if unit has products
            $this->db->query('SELECT COUNT(*) as count FROM products WHERE unit_id = :unit_id');
            $this->db->bind(':unit_id', $id);
            $result = $this->db->single();

            if ($result && $result->count > 0) {
                return false; // Cannot delete unit with products
            }

            $this->db->query('DELETE FROM units WHERE unit_id = :unit_id');
            $this->db->bind(':unit_id', $id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in deleteUnit: " . $e->getMessage());
            return false;
        }
    }

    // Check if unit name exists
    // @param string $name
    // @param int $excludeId
    // @return bool
    public function unitExists($name, $excludeId = null)
    {
        try {
            $sql = 'SELECT unit_id FROM units WHERE unit_name = :unit_name';
            if ($excludeId) {
                $sql .= ' AND unit_id != :exclude_id';
            }

            $this->db->query($sql);
            $this->db->bind(':unit_name', $name);
            if ($excludeId) {
                $this->db->bind(':exclude_id', $excludeId);
            }

            return $this->db->single() ? true : false;
        } catch (Exception $e) {
            error_log("Error in unitExists: " . $e->getMessage());
            return false;
        }
    }

    // Get unit statistics
    // @return object
    public function getUnitStats()
    {
        try {
            $this->db->query('SELECT 
                                COUNT(*) as total_units,
                                COUNT(CASE WHEN products.unit_id IS NOT NULL THEN 1 END) as units_with_products
                             FROM units 
                             LEFT JOIN products ON units.unit_id = products.unit_id');

            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getUnitStats: " . $e->getMessage());
            return (object) ['total_units' => 0, 'units_with_products' => 0];
        }
    }

    // Get products by unit
    // @param int $unitId
    // @return array
    public function getProductsByUnit($unitId)
    {
        try {
            $this->db->query('SELECT p.*, c.category_name, b.brand_name 
                             FROM products p 
                             LEFT JOIN categories c ON p.category_id = c.category_id 
                             LEFT JOIN brands b ON p.brand_id = b.brand_id 
                             WHERE p.unit_id = :unit_id 
                             ORDER BY p.product_name ASC');
            $this->db->bind(':unit_id', $unitId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getProductsByUnit: " . $e->getMessage());
            return [];
        }
    }

    // Get common units (predefined list)
    // @return array
    public function getCommonUnits()
    {
        return [
            ['unit_name' => 'Piece', 'unit_symbol' => 'pcs', 'description' => 'Individual items'],
            ['unit_name' => 'Kilogram', 'unit_symbol' => 'kg', 'description' => 'Weight measurement'],
            ['unit_name' => 'Gram', 'unit_symbol' => 'g', 'description' => 'Weight measurement'],
            ['unit_name' => 'Liter', 'unit_symbol' => 'L', 'description' => 'Volume measurement'],
            ['unit_name' => 'Milliliter', 'unit_symbol' => 'ml', 'description' => 'Volume measurement'],
            ['unit_name' => 'Meter', 'unit_symbol' => 'm', 'description' => 'Length measurement'],
            ['unit_name' => 'Centimeter', 'unit_symbol' => 'cm', 'description' => 'Length measurement'],
            ['unit_name' => 'Box', 'unit_symbol' => 'box', 'description' => 'Packaging unit'],
            ['unit_name' => 'Pack', 'unit_symbol' => 'pack', 'description' => 'Packaging unit'],
            ['unit_name' => 'Dozen', 'unit_symbol' => 'dz', 'description' => '12 pieces']
        ];
    }
}

// End of file
?>