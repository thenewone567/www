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
                COALESCE(SUM(s.quantity), 0) as current_Inventory,
                COALESCE(lp.unit_price, 50.00) as unit_price,
                CASE 
                    WHEN COALESCE(SUM(s.quantity), 0) <= p.min_Inventory_level THEN 'Low Inventory'
                    WHEN COALESCE(SUM(s.quantity), 0) <= p.reorder_level THEN 'Reorder'
                    ELSE 'Normal'
                END as Inventory_status,
                MIN(s.expiry_date) as expiry_date
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN inventory s ON p.product_id = s.product_id
            LEFT JOIN (
                SELECT 
                    pi.product_id,
                    pi.unit_price,
                    ROW_NUMBER() OVER (PARTITION BY pi.product_id ORDER BY pur.purchase_date DESC) as rn
                FROM purchase_items pi
                JOIN purchases pur ON pi.purchase_id = pur.purchase_id
            ) lp ON p.product_id = lp.product_id AND lp.rn = 1
            WHERE p.is_active = 1
            GROUP BY p.product_id, p.product_name, p.sku, p.category_id, p.brand_id, p.unit_id, 
                     p.min_Inventory_level, p.max_Inventory_level, p.reorder_level, p.image_path, p.is_active, 
                     c.category_name, b.brand_name, u.unit_name, lp.unit_price
            ORDER BY p.product_name ASC
        ");
        $this->db->execute();
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
                c.category_name,
                b.brand_name,
                u.unit_name,
                COALESCE(lp.unit_price, 50.00) as purchase_price,
                COALESCE(lp.unit_price * 1.3, 65.00) as selling_price,
                30 as profit_margin,
                COALESCE(SUM(s.quantity), 0) as current_Inventory,
                p.min_Inventory_level,
                p.image_path,
                bar.barcode_value
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN inventory s ON p.product_id = s.product_id
            LEFT JOIN barcode bar ON p.product_id = bar.product_id AND bar.is_active = 1
            LEFT JOIN (
                SELECT 
                    pi.product_id,
                    pi.unit_price,
                    ROW_NUMBER() OVER (PARTITION BY pi.product_id ORDER BY pur.purchase_date DESC) as rn
                FROM purchase_items pi
                JOIN purchases pur ON pi.purchase_id = pur.purchase_id
            ) lp ON p.product_id = lp.product_id AND lp.rn = 1
            WHERE p.is_active = 1
            GROUP BY p.product_id, p.product_name, p.sku, c.category_name, 
                     b.brand_name, u.unit_name, p.min_Inventory_level, p.image_path, 
                     bar.barcode_value, lp.unit_price
            ORDER BY p.product_name ASC
        ");
        $this->db->execute();
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
                    product_name, sku, barcode, model_number, supplier_code, category_id, supplier_id, brand_id, unit_id, 
                    min_Inventory_level, max_Inventory_level, reorder_level, purchase_price, 
                    selling_price, profit_margin, gst_rate, weight, dimensions, warranty_period, 
                    storage_location, product_status, image_path, is_active
                ) VALUES (
                    :product_name, :sku, :barcode, :model_number, :supplier_code, :category_id, :supplier_id, :brand_id, :unit_id, 
                    :min_Inventory_level, :max_Inventory_level, :reorder_level, :purchase_price, 
                    :selling_price, :profit_margin, :gst_rate, :weight, :dimensions, :warranty_period, 
                    :storage_location, :product_status, :image_path, :is_active
                )
            ");

            // Bind values with proper null handling
            $this->db->bind(':product_name', $data['product_name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':barcode', !empty($data['barcode']) ? $data['barcode'] : null);
            $this->db->bind(':model_number', !empty($data['model_number']) ? $data['model_number'] : null);
            $this->db->bind(':supplier_code', !empty($data['supplier_code']) ? $data['supplier_code'] : null);
            $this->db->bind(':category_id', (int) $data['category_id']);
            $this->db->bind(':supplier_id', !empty($data['supplier_id']) ? (int) $data['supplier_id'] : null);
            $this->db->bind(':brand_id', !empty($data['brand_id']) ? (int) $data['brand_id'] : null);
            $this->db->bind(':unit_id', !empty($data['unit_id']) ? (int) $data['unit_id'] : null);
            $this->db->bind(':min_Inventory_level', (int) ($data['min_Inventory_level'] ?? 5));
            $this->db->bind(':max_Inventory_level', (int) ($data['max_Inventory_level'] ?? 100));
            $this->db->bind(':reorder_level', (int) ($data['reorder_level'] ?? 10));
            $this->db->bind(':purchase_price', !empty($data['purchase_price']) ? (float) $data['purchase_price'] : null);
            $this->db->bind(':selling_price', !empty($data['selling_price']) ? (float) $data['selling_price'] : null);
            $this->db->bind(':profit_margin', $profitMargin);
            $this->db->bind(':gst_rate', !empty($data['gst_rate']) ? (float) $data['gst_rate'] : 18.00);
            $this->db->bind(':weight', !empty($data['weight']) ? (float) $data['weight'] : null);
            $this->db->bind(':dimensions', !empty($data['dimensions']) ? $data['dimensions'] : null);
            $this->db->bind(':warranty_period', (int) ($data['warranty_period'] ?? 0));
            $this->db->bind(':storage_location', !empty($data['storage_location']) ? $data['storage_location'] : null);
            $this->db->bind(':product_status', $data['product_status'] ?? 'active');
            $this->db->bind(':image_path', !empty($data['image_path']) ? $data['image_path'] : null);
            $this->db->bind(':is_active', 1);

            // Execute product insert
            if (!$this->db->execute()) {
                throw new Exception('Failed to insert product');
            }

            // Get the newly inserted product ID
            $productId = $this->db->lastInsertId();

            // Insert initial Inventory if quantity > 0
            if (isset($data['initial_quantity']) && $data['initial_quantity'] > 0) {
                $this->addInitialInventory($productId, $data);
            }

            // Commit transaction
            $this->db->commit();
            return $productId;

        } catch (Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            error_log('DB Insert Error in Product::addProduct(): ' . $e->getMessage());
            error_log('Data submitted: ' . print_r($data, true));
            return false;
        }
    }

    private function addInitialInventory($productId, $data)
    {
        $this->db->query("
            INSERT INTO inventory (
                product_id, location_id, quantity, batch_number, expiry_date
            ) VALUES (
                :product_id, :location_id, :quantity, :batch_number, :expiry_date
            )
        ");

        $this->db->bind(':product_id', $productId);
        $this->db->bind(':location_id', $data['location_id'] ?? 1); // Default to main warehouse
        $this->db->bind(':quantity', $data['initial_quantity']);
        $this->db->bind(':batch_number', 'INITIAL_' . str_pad($productId, 6, '0', STR_PAD_LEFT));
        $this->db->bind(':expiry_date', $data['expiry_date'] ?? null); // Optional expiry date

        if (!$this->db->execute()) {
            throw new Exception('Failed to insert initial inventory');
        }

        // Log Inventory movement
        $this->logInventoryMovement($productId, 'in', $data['initial_quantity'], null, $data['location_id'] ?? 1, 'Initial Inventory');
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
                    min_Inventory_level = :min_Inventory_level,
                    max_Inventory_level = :max_Inventory_level,
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
            $this->db->bind(':min_Inventory_level', $data['min_Inventory_level']);
            $this->db->bind(':max_Inventory_level', $data['max_Inventory_level']);
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
                   COALESCE(SUM(s.quantity), 0) as current_Inventory
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN Inventory s ON p.product_id = s.product_id
            WHERE p.product_id = :id
            GROUP BY p.product_id
        ");
        $this->db->bind(':id', $id);
        $this->db->execute();
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
                   COALESCE(SUM(s.quantity), 0) as current_Inventory,
                   bar.barcode_value
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN Inventory s ON p.product_id = s.product_id
            LEFT JOIN barcode bar ON p.product_id = bar.product_id
            WHERE bar.barcode_value = :barcode AND bar.is_active = 1 AND p.is_active = 1
            GROUP BY p.product_id
        ");
        $this->db->bind(':barcode', $barcode);
        $this->db->execute();
        return $this->db->single();
    }

    public function getCurrentInventory($productId)
    {
        $this->db->query("SELECT COALESCE(SUM(quantity), 0) as current_Inventory FROM Inventory WHERE product_id = :product_id");
        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result->current_Inventory : 0;
    }

    public function getInventoryByLocation($productId)
    {
        $this->db->query("
            SELECT s.*, wl.location_code, wl.location_name, wl.location_type
            FROM Inventory s
            LEFT JOIN warehouse_locations wl ON s.location_id = wl.location_id
            WHERE s.product_id = :product_id AND s.quantity > 0
            ORDER BY wl.location_code
        ");
        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        return $this->db->resultSet() ?: [];
    }

    public function getLowInventoryProducts()
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name,
                   COALESCE(SUM(s.quantity), 0) as current_Inventory
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN Inventory s ON p.product_id = s.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id
            HAVING current_Inventory <= p.min_Inventory_level
            ORDER BY current_Inventory ASC
        ");
        $this->db->execute();
        return $this->db->resultSet() ?: [];
    }

    public function getReorderProducts()
    {
        $this->db->query("
            SELECT p.*, 
                   c.category_name,
                   sup.supplier_name,
                   COALESCE(SUM(s.quantity), 0) as current_Inventory
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN suppliers sup ON p.supplier_code = sup.supplier_code
            LEFT JOIN Inventory s ON p.product_id = s.product_id
            WHERE p.is_active = 1
            GROUP BY p.product_id
            HAVING current_Inventory <= p.reorder_level
            ORDER BY current_Inventory ASC
        ");
        $this->db->execute();
        return $this->db->resultSet() ?: [];
    }

    /**
     * Adjust inventory - proper inventory terminology version
     * @param int $productId
     * @param int $newQuantity
     * @param string $reason
     * @param int $userId
     * @return bool
     */
    public function adjustInventory($productId, $newQuantity, $reason = 'Manual Inventory Adjustment', $userId = null)
    {
        try {
            $this->db->beginTransaction();

            $currentInventory = $this->getCurrentInventory($productId);
            $adjustment = $newQuantity - $currentInventory;

            if ($adjustment != 0) {
                // Insert inventory adjustment
                $this->db->query("
                    INSERT INTO Inventory (product_id, quantity, batch_number) 
                    VALUES (:product_id, :quantity, :batch_number)
                ");
                $this->db->bind(':product_id', $productId);
                $this->db->bind(':quantity', $adjustment);
                $this->db->bind(':batch_number', 'INV_ADJ_' . date('YmdHis'));
                $this->db->execute();

                // Log inventory movement
                $this->logInventoryMovement($productId, 'inventory_adjustment', abs($adjustment), null, null, $reason, $userId);
            }

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollback();
            error_log('Inventory Adjustment Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Log inventory movement - proper inventory terminology version
     */
    private function logInventoryMovement($productId, $type, $quantity, $fromLocation = null, $toLocation = null, $notes = null, $userId = null)
    {
        $this->db->query("
            INSERT INTO inventory_movements (
                product_id, quantity, from_location_id, to_location_id
            ) VALUES (
                :product_id, :quantity, :from_location_id, :to_location_id
            )
        ");
        $this->db->bind(':product_id', $productId);
        $this->db->bind(':quantity', $quantity);
        $this->db->bind(':from_location_id', $fromLocation);
        $this->db->bind(':to_location_id', $toLocation);
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

    public function searchProducts($searchTerm, $categoryId = null, $inInventoryOnly = false)
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
        if ($inInventoryOnly) {
            $havingClause = "HAVING current_Inventory > 0";
        }

        $sql = "
            SELECT p.*, 
                   c.category_name, 
                   b.brand_name, 
                   u.unit_name,
                   COALESCE(SUM(s.quantity), 0) as current_Inventory
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id
            LEFT JOIN brands b ON p.brand_id = b.brand_id
            LEFT JOIN units u ON p.unit_id = u.unit_id
            LEFT JOIN Inventory s ON p.product_id = s.product_id
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
        $this->db->execute();
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
                    min_Inventory_level, max_Inventory_level, reorder_level, image_path, is_active
                ) VALUES (
                    :product_name, :sku, :category_id, :brand_id, :unit_id, 
                    :min_Inventory_level, :max_Inventory_level, :reorder_level, :image_path, :is_active
                )
            ");

            // Bind values
            $this->db->bind(':product_name', $data['product_name']);
            $this->db->bind(':sku', $data['sku']);
            $this->db->bind(':category_id', $data['category_id']);
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':unit_id', $data['unit_id']);
            $this->db->bind(':min_Inventory_level', $data['min_Inventory_level']);
            $this->db->bind(':max_Inventory_level', $data['max_Inventory_level']);
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
                    min_Inventory_level = :min_Inventory_level,
                    max_Inventory_level = :max_Inventory_level,
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
            $this->db->bind(':min_Inventory_level', $data['min_Inventory_level']);
            $this->db->bind(':max_Inventory_level', $data['max_Inventory_level']);
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