<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getProducts()
    {
        $this->db->query("SELECT * FROM products");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addProduct($data)
    {
        $this->db->query("INSERT INTO products (product_name, sku, category_id, brand_id, unit_id, min_stock_level, max_stock_level, reorder_level, image_path) VALUES (:product_name, :sku, :category_id, :brand_id, :unit_id, :min_stock_level, :max_stock_level, :reorder_level, :image_path)");
        // Bind values
        $this->db->bind(':product_name', $data['product_name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':brand_id', $data['brand_id']);
        $this->db->bind(':unit_id', $data['unit_id']);
        $this->db->bind(':min_stock_level', $data['min_stock_level']);
        $this->db->bind(':max_stock_level', $data['max_stock_level']);
        $this->db->bind(':reorder_level', $data['reorder_level']);
        $this->db->bind(':image_path', $data['image_path']);

        // Execute
        return $this->db->execute();
    }

    public function getProductById($id)
    {
        $this->db->query("SELECT * FROM products WHERE product_id = :id");
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function updateProduct($data)
    {
        $this->db->query("UPDATE products SET product_name = :product_name, sku = :sku, category_id = :category_id, brand_id = :brand_id, unit_id = :unit_id, min_stock_level = :min_stock_level, max_stock_level = :max_stock_level, reorder_level = :reorder_level, image_path = :image_path WHERE product_id = :id");
        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':product_name', $data['product_name']);
        $this->db->bind(':sku', $data['sku']);
        $this->db->bind(':category_id', $data['category_id']);
        $this->db->bind(':brand_id', $data['brand_id']);
        $this->db->bind(':unit_id', $data['unit_id']);
        $this->db->bind(':min_stock_level', $data['min_stock_level']);
        $this->db->bind(':max_stock_level', $data['max_stock_level']);
        $this->db->bind(':reorder_level', $data['reorder_level']);
        $this->db->bind(':image_path', $data['image_path']);

        // Execute
        return $this->db->execute();
    }

    public function deleteProduct($id)
    {
        $this->db->query("DELETE FROM products WHERE product_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
