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
        $this->db->query("
            SELECT 
                p.*,
                c.category_name,
                b.brand_name,
                u.unit_name,
                u.abbreviation as unit_abbreviation,
                COALESCE(SUM(s.quantity), 0) as current_stock,
                p.selling_price as unit_price,
                CASE 
                    WHEN COALESCE(SUM(s.quantity), 0) <= p.min_stock_level THEN 'Low Stock'
                    WHEN COALESCE(SUM(s.quantity), 0) <= p.reorder_level THEN 'Reorder'
                    ELSE 'Normal'
                END as stock_status,
                COALESCE(SUM(s.quantity), 0) * p.purchase_price as total_cost_value,
                COALESCE(SUM(s.quantity), 0) * p.selling_price as total_retail_value,
                CASE 
                    WHEN p.purchase_price > 0 THEN ROUND(((p.selling_price - p.purchase_price) / p.purchase_price * 100), 2)
                    ELSE 0
                END as calculated_margin
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN stock s ON p.product_id = s.product_id
            GROUP BY p.product_id, p.product_name, p.sku, p.supplier_code, p.category_id, p.brand_id, p.unit_id, 
                     p.min_stock_level, p.max_stock_level, p.reorder_level, p.purchase_price, p.selling_price, 
                     p.profit_margin, p.weight, p.dimensions, p.warranty_period, p.image_path, p.is_active, 
                     c.category_name, b.brand_name, u.unit_name, u.abbreviation
            ORDER BY p.product_name ASC
        ");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getProductsForSale()
    {
        $this->db->query("
            SELECT 
                p.product_id,
                p.product_name,
                p.sku,
                p.supplier_code,
                c.category_name,
                b.brand_name,
                u.unit_name,
                u.abbreviation as unit_abbreviation,
                p.purchase_price,
                p.selling_price,
                p.profit_margin,
                COALESCE(SUM(s.quantity), 0) as current_stock,
                p.min_stock_level,
                p.weight,
                p.warranty_period,
                p.image_path,
                bar.barcode_value
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN stock s ON p.product_id = s.product_id
            LEFT JOIN barcode bar ON p.product_id = bar.product_id AND bar.is_active = 1
            WHERE p.is_active = 1 AND p.selling_price > 0
            GROUP BY p.product_id, p.product_name, p.sku, p.supplier_code, c.category_name, 
                     b.brand_name, u.unit_name, u.abbreviation, p.purchase_price, p.selling_price, 
                     p.profit_margin, p.min_stock_level, p.weight, p.warranty_period, p.image_path, bar.barcode_value
            ORDER BY p.product_name ASC
        ");
        return $this->db->resultSet() ?: [];
    }

    public function addProduct($data)
    {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Calculate profit margin if not provided
            $profitMargin = 0;
            if (
                isset($data['purchase_price']) && isset($data['selling_price']) &&
                $data['purchase_price'] > 0 && $data['selling_price'] > 0
            ) {
                $profitMargin = round((($data['selling_price'] - $data['purchase_price']) / $data['purchase_price']) * 100, 2);
            }

            // Generate SKU if not provided
            if (empty($data['sku'])) {
                $data['sku'] = $this->generateSKU($data['product_name'], $data['category_id']);
            }

            // Insert product
            $this->db->query("
                INSERT INTO products (
                    product_name, sku, supplier_code, category_id, brand_id, unit_id, 
                    min_stock_level, max_stock_level, reorder_level, purchase_price, 
                    selling_price, profit_margin, weight, dimensions, warranty_period, image_path
                ) VALUES (
                    :product_name, :sku, :supplier_code, :category_id, :brand_id, :unit_id, 
                    :min_stock_level, :max_stock_level, :reorder_level, :purchase_price, 
                    :selling_price, :profit_margin, :weight, :dimensions, :warranty_period, :image_path
                )
            ");

            // Bind values
            $this->db->bind(':product_name', $data['product_name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':supplier_code', $data['supplier_code'] ?? null);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':unit_id', $data['unit_id']);
            $this->db->bind(':min_stock_level', $data['min_stock_level']);
            $this->db->bind(':max_stock_level', $data['max_stock_level']);
            $this->db->bind(':reorder_level', $data['reorder_level']);
            $this->db->bind(':purchase_price', $data['purchase_price'] ?? 0.00);
            $this->db->bind(':selling_price', $data['selling_price'] ?? 0.00);
            $this->db->bind(':profit_margin', $profitMargin);
            $this->db->bind(':weight', $data['weight'] ?? 0.000);
            $this->db->bind(':dimensions', $data['dimensions'] ?? null);
            $this->db->bind(':warranty_period', $data['warranty_period'] ?? 0);
            $this->db->bind(':image_path', $data['image_path']);

            // Execute product insert
            if (!$this->db->execute()) {
                throw new Exception('Failed to insert product');
            }

            // Get the newly inserted product ID
            $productId = $this->db->lastInsertId();

            // Insert initial stock if quantity > 0
            if (isset($data['initial_quantity']) && $data['initial_quantity'] > 0) {
                $this->addInitialStock($productId, $data);
            }

            // Commit transaction
            $this->db->commit();
            return $productId;

        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            error_log('DB Insert Error: ' . $e->getMessage());
            return false;
        }
    }

    private function addInitialStock($productId, $data)
    {
        $this->db->query("
            INSERT INTO stock (
                product_id, location_id, quantity, purchase_price, batch_number, supplier_id
            ) VALUES (
                :product_id, :location_id, :quantity, :purchase_price, :batch_number, :supplier_id
            )
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->bind(':location_id', $data['location_id'] ?? 1); // Default to main warehouse
        $this->db->bind(':quantity', $data['initial_quantity']);
        $this->db->bind(':purchase_price', $data['purchase_price'] ?? 0.00);
        $this->db->bind(':batch_number', 'INITIAL_' . str_pad($productId, 6, '0', STR_PAD_LEFT));
        $this->db->bind(':supplier_id', $data['supplier_id'] ?? null);

        if (!$this->db->execute()) {
            throw new Exception('Failed to insert initial stock');
        }

        // Log stock movement
        $this->logStockMovement($productId, 'in', $data['initial_quantity'], null, $data['location_id'] ?? 1, 'Initial Stock');
    }

    public function updateProduct($id, $data)
    {
        try {
            // Calculate profit margin if not provided
            $profitMargin = 0;
            if (
                isset($data['purchase_price']) && isset($data['selling_price']) &&
                $data['purchase_price'] > 0 && $data['selling_price'] > 0
            ) {
                $profitMargin = round((($data['selling_price'] - $data['purchase_price']) / $data['purchase_price']) * 100, 2);
            }

            $this->db->query("
                UPDATE products SET 
                    product_name = :product_name,
                    sku = :sku,
                    supplier_code = :supplier_code,
                    category_id = :category_id,
                    brand_id = :brand_id,
                    unit_id = :unit_id,
                    min_stock_level = :min_stock_level,
                    max_stock_level = :max_stock_level,
                    reorder_level = :reorder_level,
                    purchase_price = :purchase_price,
                    selling_price = :selling_price,
                    profit_margin = :profit_margin,
                    weight = :weight,
                    dimensions = :dimensions,
                    warranty_period = :warranty_period,
                    image_path = :image_path,
                    updated_at = CURRENT_TIMESTAMP
                WHERE product_id = :product_id
            ");

            // Bind values
            $this->db->bind(':product_id', $id);
            $this->db->bind(':product_name', $data['product_name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':supplier_code', $data['supplier_code'] ?? null);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':unit_id', $data['unit_id']);
            $this->db->bind(':min_stock_level', $data['min_stock_level']);
            $this->db->bind(':max_stock_level', $data['max_stock_level']);
            $this->db->bind(':reorder_level', $data['reorder_level']);
            $this->db->bind(':purchase_price', $data['purchase_price'] ?? 0.00);
            $this->db->bind(':selling_price', $data['selling_price'] ?? 0.00);
            $this->db->bind(':profit_margin', $profitMargin);
            $this->db->bind(':weight', $data['weight'] ?? 0.000);
            $this->db->bind(':dimensions', $data['dimensions'] ?? null);
            $this->db->bind(':warranty_period', $data['warranty_period'] ?? 0);
            $this->db->bind(':image_path', $data['image_path']);

            return $this->db->execute();

        } catch (Exception $e) {
            error_log('DB Update Error: ' . $e->getMessage());
            return false;
        }
    }

    public function getProductById($id)
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name, 
                   b.brand_name, 
                   u.unit_name,
                   COALESCE(SUM(s.quantity), 0) as current_stock
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN stock s ON p.product_id = s.product_id
            WHERE p.product_id = :id
            GROUP BY p.product_id
        ");
        $this->db->bind(':id', $id);
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function getProductByBarcode($barcode)
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name, 
                   b.brand_name, 
                   u.unit_name,
                   COALESCE(SUM(s.quantity), 0) as current_stock,
                   bar.barcode_value
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN stock s ON p.product_id = s.product_id
            LEFT JOIN barcode bar ON p.product_id = bar.product_id
            WHERE bar.barcode_value = :barcode AND bar.is_active = 1 AND p.is_active = 1
            GROUP BY p.product_id
        ");
        $this->db->bind(':barcode', $barcode);
        return $this->db->single();
    }

    public function getCurrentStock($productId)
    {
        $this->db->query("SELECT COALESCE(SUM(quantity), 0) as current_stock FROM stock WHERE product_id = :product_id");
        $this->db->bind(':product_id', $productId);
        $result = $this->db->single();
        return $result ? $result->current_stock : 0;
    }

    public function getStockByLocation($productId)
    {
        $this->db->query("
            SELECT s.*, wl.location_code, wl.location_name, wl.location_type
            FROM stock s
            LEFT JOIN warehouse_locations wl ON s.location_id = wl.location_id
            WHERE s.product_id = :product_id AND s.quantity > 0
            ORDER BY wl.location_code
        ");
        $this->db->bind(':product_id', $productId);
        return $this->db->resultSet() ?: [];
    }

    public function getLowStockProducts()
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name,
                   COALESCE(SUM(s.quantity), 0) as current_stock
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN stock s ON p.product_id = s.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id
            HAVING current_stock <= p.min_stock_level
            ORDER BY current_stock ASC
        ");
        return $this->db->resultSet() ?: [];
    }

    public function getReorderProducts()
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name,
                   sup.supplier_name,
                   COALESCE(SUM(s.quantity), 0) as current_stock
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN suppliers sup ON p.supplier_code = sup.supplier_code
            LEFT JOIN stock s ON p.product_id = s.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id
            HAVING current_stock <= p.reorder_level
            ORDER BY current_stock ASC
        ");
        return $this->db->resultSet() ?: [];
    }

    public function adjustStock($productId, $newQuantity, $reason = 'Manual Adjustment', $userId = null)
    {
        try {
            $this->db->beginTransaction();

            $currentStock = $this->getCurrentStock($productId);
            $adjustment = $newQuantity - $currentStock;

            if ($adjustment != 0) {
                // Insert stock adjustment
                $this->db->query("
                    INSERT INTO stock (product_id, quantity, batch_number) 
                    VALUES (:product_id, :quantity, :batch_number)
                ");
                $this->db->bind(':product_id', $productId);
                $this->db->bind(':quantity', $adjustment);
                $this->db->bind(':batch_number', 'ADJ_' . date('YmdHis'));
                $this->db->execute();

                // Log movement
                $this->logStockMovement($productId, 'adjustment', abs($adjustment), null, null, $reason, $userId);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Stock Adjustment Error: ' . $e->getMessage());
            return false;
        }
    }

    private function logStockMovement($productId, $type, $quantity, $fromLocation = null, $toLocation = null, $notes = null, $userId = null)
    {
        $this->db->query("
            INSERT INTO stock_movements (
                product_id, movement_type, quantity, from_location_id, to_location_id, notes, user_id
            ) VALUES (
                :product_id, :movement_type, :quantity, :from_location_id, :to_location_id, :notes, :user_id
            )
        ");
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':movement_type', $type);
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':from_location_id', $fromLocation);
        $this->db->bind(':to_location_id', $toLocation);
        $this->db->bind(':notes', $notes);
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
    }

    private function generateSKU($productName, $categoryId)
    {
        // Simple SKU generation: first 3 letters of product name + category + random number
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $productName), 0, 3));
        $categoryCode = str_pad($categoryId, 2, '0', STR_PAD_LEFT);
        $random = rand(100, 999);
        return $prefix . $categoryCode . $random;
    }

    public function deleteProduct($id)
    {
        // Soft delete - just mark as inactive
        $this->db->query("UPDATE products SET is_active = 0 WHERE product_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function searchProducts($searchTerm, $categoryId = null, $inStockOnly = false)
    {
        $whereConditions = ["p.is_active = 1"];
        $params = [];

        if (!empty($searchTerm)) {
            $whereConditions[] = "(p.product_name LIKE :search OR p.sku LIKE :search OR p.supplier_code LIKE :search)";
            $params['search'] = "%$searchTerm%";
        }

        if ($categoryId) {
            $whereConditions[] = "p.category_id = :category_id";
            $params['category_id'] = $categoryId;
        }

        $havingClause = "";
        if ($inStockOnly) {
            $havingClause = "HAVING current_stock > 0";
        }

        $sql = "
            SELECT p.*, 
                   c.category_name, 
                   b.brand_name, 
                   u.unit_name,
                   COALESCE(SUM(s.quantity), 0) as current_stock
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN stock s ON p.product_id = s.product_id
            WHERE " . implode(" AND ", $whereConditions) . "
            GROUP BY p.product_id
            $havingClause
            ORDER BY p.product_name ASC
        ";

        $this->db->query($sql);

        foreach ($params as $key => $value) {
            $this->db->bind(":$key", $value);
        }

        return $this->db->resultSet() ?: [];
    }

    public function getCategories()
    {
        $this->db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY category_name");
        return $this->db->resultSet() ?: [];
    }

    public function getProductBySku($sku)
    {
        $this->db->query("SELECT * FROM products WHERE sku = :sku LIMIT 1");
        $this->db->bind(':sku', $sku);
        $this->db->execute();
        return $this->db->single();
    }

    public function addSimpleProduct($data)
    {
        try {
            $this->db->query("
                INSERT INTO products (
                    product_name, sku, category_id, brand_id, unit_id, 
                    min_stock_level, max_stock_level, reorder_level, image_path, is_active
                ) VALUES (
                    :product_name, :sku, :category_id, :brand_id, :unit_id, 
                    :min_stock_level, :max_stock_level, :reorder_level, :image_path, :is_active
                )
            ");

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
            $this->db->bind(':is_active', $data['is_active']);

            if ($this->db->execute()) {
                return $this->db->lastInsertId();
            } else {
                return false;
            }
        } catch (Exception $e) {
            error_log('Simple Product Insert Error: ' . $e->getMessage());
            return false;
        }
    }

    public function updateSimpleProduct($id, $data)
    {
        try {
            $this->db->query("
                UPDATE products SET 
                    product_name = :product_name,
                    sku = :sku,
                    category_id = :category_id,
                    brand_id = :brand_id,
                    unit_id = :unit_id,
                    min_stock_level = :min_stock_level,
                    max_stock_level = :max_stock_level,
                    reorder_level = :reorder_level,
                    image_path = :image_path,
                    is_active = :is_active
                WHERE product_id = :product_id
            ");

            // Bind values
            $this->db->bind(':product_id', $id);
            $this->db->bind(':product_name', $data['product_name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':unit_id', $data['unit_id']);
            $this->db->bind(':min_stock_level', $data['min_stock_level']);
            $this->db->bind(':max_stock_level', $data['max_stock_level']);
            $this->db->bind(':reorder_level', $data['reorder_level']);
            $this->db->bind(':image_path', $data['image_path']);
            $this->db->bind(':is_active', $data['is_active']);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log('Simple Product Update Error: ' . $e->getMessage());
            return false;
        }
    }
}