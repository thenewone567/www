<?php
/*
 Brand Model
 Handles product brand data operations
*/

class Brand
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    // Get brand by name
    // @param string $name
    // @return object|null
    public function getBrandByName($name)
    {
        try {
            $this->db->query('SELECT * FROM brands WHERE brand_name = :brand_name');
            $this->db->bind(':brand_name', $name);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getBrandByName: " . $e->getMessage());
            return null;
        }
    }

    // Get all brands
    // @return array
    public function getBrands()
    {
        try {
            $this->db->query('SELECT * FROM brands ORDER BY brand_name ASC');
            $this->db->execute();
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getBrands: " . $e->getMessage());
            return [];
        }
    }

    // Get brand by ID
    // @param int $id
    // @return object|null
    public function getBrandById($id)
    {
        try {
            $this->db->query('SELECT * FROM brands WHERE brand_id = :brand_id');
            $this->db->bind(':brand_id', $id);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getBrandById: " . $e->getMessage());
            return null;
        }
    }

    // Add new brand
    // @param array $data
    // @return bool
    public function addBrand($data)
    {
        try {
            $this->db->query('INSERT INTO brands (brand_name, description) VALUES (:brand_name, :description)');
            $this->db->bind(':brand_name', $data['brand_name']);
            $this->db->bind(':description', isset($data['description']) ? $data['description'] : '');
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in addBrand: " . $e->getMessage());
            return false;
        }
    }

    // Update brand
    // @param array $data
    // @return bool
    public function updateBrand($data)
    {
        try {
            $this->db->query('UPDATE brands SET brand_name = :brand_name, description = :description WHERE brand_id = :brand_id');
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':brand_name', $data['brand_name']);
            $this->db->bind(':description', isset($data['description']) ? $data['description'] : '');
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateBrand: " . $e->getMessage());
            return false;
        }
    }

    // Delete brand
    // @param int $id
    // @return bool
    public function deleteBrand($id)
    {
        try {
            // Check if brand has products
            $this->db->query('SELECT COUNT(*) as count FROM products WHERE brand_id = :brand_id');
            $this->db->bind(':brand_id', $id);
            $result = $this->db->single();

            if ($result && $result->count > 0) {
                return false; // Cannot delete brand with products
            }

            $this->db->query('DELETE FROM brands WHERE brand_id = :brand_id');
            $this->db->bind(':brand_id', $id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in deleteBrand: " . $e->getMessage());
            return false;
        }
    }

    // Check if brand name exists
    // @param string $name
    // @param int $excludeId
    // @return bool
    public function brandExists($name, $excludeId = null)
    {
        try {
            $sql = 'SELECT brand_id FROM brands WHERE brand_name = :brand_name';
            if ($excludeId) {
                $sql .= ' AND brand_id != :exclude_id';
            }

            $this->db->query($sql);
            $this->db->bind(':brand_name', $name);
            if ($excludeId) {
                $this->db->bind(':exclude_id', $excludeId);
            }

            return $this->db->single() ? true : false;
        } catch (Exception $e) {
            error_log("Error in brandExists: " . $e->getMessage());
            return false;
        }
    }

    // Get brand statistics
    // @return object
    public function getBrandStats()
    {
        try {
            $this->db->query('SELECT 
                                COUNT(*) as total_brands,
                                COUNT(CASE WHEN products.brand_id IS NOT NULL THEN 1 END) as brands_with_products
                             FROM brands 
                             LEFT JOIN products ON brands.brand_id = products.brand_id');

            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getBrandStats: " . $e->getMessage());
            return (object) ['total_brands' => 0, 'brands_with_products' => 0];
        }
    }

    // Get products by brand
    // @param int $brandId
    // @return array
    public function getProductsByBrand($brandId)
    {
        try {
            $this->db->query('SELECT p.*, c.category_name 
                             FROM products p 
                             LEFT JOIN categories c ON p.category_id = c.category_id 
                             WHERE p.brand_id = :brand_id 
                             ORDER BY p.product_name ASC');
            $this->db->bind(':brand_id', $brandId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getProductsByBrand: " . $e->getMessage());
            return [];
        }
    }
}

// End of file
?>