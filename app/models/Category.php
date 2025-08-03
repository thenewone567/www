<?php
/**
 * Category Model
 * Handles product category data operations
 */

class Category
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get category by name
     * @param string $name
     * @return object|null
     */
    public function getCategoryByName($name)
    {
        try {
            $this->db->query('SELECT * FROM categories WHERE category_name = :category_name');
            $this->db->bind(':category_name', $name);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getCategoryByName: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all categories
     * @return array
     */
    public function getCategories()
    {
        try {
            $this->db->query('SELECT * FROM categories ORDER BY category_name ASC');
            $this->db->execute();
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getCategories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get category by ID
     * @param int $id
     * @return object|null
     */
    public function getCategoryById($id)
    {
        try {
            $this->db->query('SELECT * FROM categories WHERE category_id = :category_id');
            $this->db->bind(':category_id', $id);
            $this->db->execute();
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getCategoryById: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Add new category
     * @param array $data
     * @return bool
     */
    public function addCategory($data)
    {
        try {
            $this->db->query('INSERT INTO categories (category_name, description) VALUES (:category_name, :description)');
            $this->db->bind(':category_name', $data['category_name']);
            $this->db->bind(':description', $data['description'] ?? '');
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in addCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update category
     * @param array $data
     * @return bool
     */
    public function updateCategory($data)
    {
        try {
            $this->db->query('UPDATE categories SET category_name = :category_name, description = :description WHERE category_id = :category_id');
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':category_name', $data['category_name']);
            $this->db->bind(':description', $data['description'] ?? '');
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete category
     * @param int $id
     * @return bool
     */
    public function deleteCategory($id)
    {
        try {
            // Check if category has products
            $this->db->query('SELECT COUNT(*) as count FROM products WHERE category_id = :category_id');
            $this->db->bind(':category_id', $id);
            $result = $this->db->single();

            if ($result->count > 0) {
                return false; // Cannot delete category with products
            }

            $this->db->query('DELETE FROM categories WHERE category_id = :category_id');
            $this->db->bind(':category_id', $id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in deleteCategory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if category name exists
     * @param string $name
     * @param int $excludeId
     * @return bool
     */
    public function categoryExists($name, $excludeId = null)
    {
        try {
            $sql = 'SELECT category_id FROM categories WHERE category_name = :category_name';
            if ($excludeId) {
                $sql .= ' AND category_id != :exclude_id';
            }

            $this->db->query($sql);
            $this->db->bind(':category_name', $name);
            if ($excludeId) {
                $this->db->bind(':exclude_id', $excludeId);
            }

            return $this->db->single() ? true : false;
        } catch (Exception $e) {
            error_log("Error in categoryExists: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get category statistics
     * @return object
     */
    public function getCategoryStats()
    {
        try {
            $this->db->query('SELECT 
                                COUNT(*) as total_categories,
                                COUNT(CASE WHEN products.category_id IS NOT NULL THEN 1 END) as categories_with_products
                             FROM categories 
                             LEFT JOIN products ON categories.category_id = products.category_id');

            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getCategoryStats: " . $e->getMessage());
            return (object) ['total_categories' => 0, 'categories_with_products' => 0];
        }
    }
}
?>