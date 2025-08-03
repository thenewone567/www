<?php
/**
 * Inventory Model
 * Handles inventory data operations including stock movements and adjustments
 */
class Inventory
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all stock records with product details
     * @param int $limit Optional limit
     * @return array
     */
    public function getAllStock($limit = null)
    {
        try {
            $sql = "SELECT s.*, p.product_name, p.product_code, p.description, p.unit_price,
                           c.category_name, b.brand_name, u.unit_name,
                           w.warehouse_name, l.location_name
                    FROM stock s
                    LEFT JOIN products p ON s.product_id = p.product_id
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    LEFT JOIN units u ON p.unit_id = u.unit_id
                    LEFT JOIN warehouses w ON s.warehouse_id = w.warehouse_id
                    LEFT JOIN locations l ON s.location_id = l.location_id
                    ORDER BY p.product_name";

            if ($limit) {
                $sql .= " LIMIT :limit";
            }

            $this->db->query($sql);

            if ($limit) {
                $this->db->bind(':limit', $limit);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getAllStock: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get low stock items
     * @param int $threshold
     * @return array
     */
    public function getLowStockItems($threshold = 10)
    {
        try {
            $this->db->query("SELECT p.product_id, p.product_name, p.product_code,
                                    COALESCE(SUM(s.quantity), 0) as total_stock,
                                    p.minimum_stock_level,
                                    c.category_name
                             FROM products p
                             LEFT JOIN stock s ON p.product_id = s.product_id
                             LEFT JOIN categories c ON p.category_id = c.category_id
                             GROUP BY p.product_id
                             HAVING total_stock <= :threshold OR total_stock <= p.minimum_stock_level
                             ORDER BY total_stock ASC");

            $this->db->bind(':threshold', $threshold);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getLowStockItems: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get stock movements/adjustments
     * @param int $limit
     * @return array
     */
    public function getStockMovements($limit = 50)
    {
        try {
            $this->db->query("SELECT sm.*, p.product_name, p.product_code,
                                    u.username as created_by_name,
                                    w.warehouse_name, l.location_name
                             FROM stock_movements sm
                             LEFT JOIN products p ON sm.product_id = p.product_id
                             LEFT JOIN users u ON sm.created_by = u.user_id
                             LEFT JOIN warehouses w ON sm.warehouse_id = w.warehouse_id
                             LEFT JOIN locations l ON sm.location_id = l.location_id
                             ORDER BY sm.created_at DESC
                             LIMIT :limit");

            $this->db->bind(':limit', $limit);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getStockMovements: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Adjust stock quantity
     * @param array $data
     * @return bool
     */
    public function adjustStock($data)
    {
        try {
            $this->db->beginTransaction();

            // Get current stock
            $this->db->query("SELECT quantity FROM stock 
                             WHERE product_id = :product_id 
                             AND warehouse_id = :warehouse_id 
                             AND location_id = :location_id");

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':warehouse_id', $data['warehouse_id']);
            $this->db->bind(':location_id', $data['location_id']);

            $currentStock = $this->db->single();
            $oldQuantity = $currentStock ? $currentStock->quantity : 0;

            // Update or insert stock record
            if ($currentStock) {
                $this->db->query("UPDATE stock 
                                 SET quantity = :new_quantity, updated_at = NOW()
                                 WHERE product_id = :product_id 
                                 AND warehouse_id = :warehouse_id 
                                 AND location_id = :location_id");
            } else {
                $this->db->query("INSERT INTO stock 
                                 (product_id, warehouse_id, location_id, quantity) 
                                 VALUES (:product_id, :warehouse_id, :location_id, :new_quantity)");
            }

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':warehouse_id', $data['warehouse_id']);
            $this->db->bind(':location_id', $data['location_id']);
            $this->db->bind(':new_quantity', $data['new_quantity']);

            if (!$this->db->execute()) {
                throw new Exception("Failed to update stock");
            }

            // Record stock movement
            $this->db->query("INSERT INTO stock_movements 
                             (product_id, warehouse_id, location_id, movement_type, quantity_before, 
                              quantity_after, quantity_change, reason, notes, created_by)
                             VALUES (:product_id, :warehouse_id, :location_id, 'adjustment', 
                                     :quantity_before, :quantity_after, :quantity_change, 
                                     :reason, :notes, :created_by)");

            $quantityChange = $data['new_quantity'] - $oldQuantity;

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':warehouse_id', $data['warehouse_id']);
            $this->db->bind(':location_id', $data['location_id']);
            $this->db->bind(':quantity_before', $oldQuantity);
            $this->db->bind(':quantity_after', $data['new_quantity']);
            $this->db->bind(':quantity_change', $quantityChange);
            $this->db->bind(':reason', $data['reason'] ?? 'Manual adjustment');
            $this->db->bind(':notes', $data['notes'] ?? '');
            $this->db->bind(':created_by', $data['created_by']);

            if (!$this->db->execute()) {
                throw new Exception("Failed to record stock movement");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in adjustStock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get inventory summary
     * @return object|false
     */
    public function getInventorySummary()
    {
        try {
            $this->db->query("SELECT 
                                COUNT(DISTINCT p.product_id) as total_products,
                                COUNT(DISTINCT s.warehouse_id) as total_warehouses,
                                COALESCE(SUM(s.quantity), 0) as total_stock_quantity,
                                COALESCE(SUM(s.quantity * p.unit_price), 0) as total_stock_value
                             FROM products p
                             LEFT JOIN stock s ON p.product_id = s.product_id");

            $summary = $this->db->single();

            // If no summary data, create default object
            if (!$summary) {
                $summary = (object) [
                    'total_products' => 0,
                    'total_warehouses' => 0,
                    'total_stock_quantity' => 0,
                    'total_stock_value' => 0
                ];
            }

            // Get low stock count
            $lowStockItems = $this->getLowStockItems();
            $summary->low_stock_items = is_array($lowStockItems) ? count($lowStockItems) : 0;

            return $summary;
        } catch (Exception $e) {
            error_log("Error in getInventorySummary: " . $e->getMessage());
            return (object) [
                'total_products' => 0,
                'total_warehouses' => 0,
                'total_stock_quantity' => 0,
                'total_stock_value' => 0,
                'low_stock_items' => 0
            ];
        }
    }

    /**
     * Get stock by product ID
     * @param int $productId
     * @return array
     */
    public function getStockByProduct($productId)
    {
        try {
            $this->db->query("SELECT s.*, w.warehouse_name, l.location_name
                             FROM stock s
                             LEFT JOIN warehouses w ON s.warehouse_id = w.warehouse_id
                             LEFT JOIN locations l ON s.location_id = l.location_id
                             WHERE s.product_id = :product_id
                             ORDER BY w.warehouse_name, l.location_name");

            $this->db->bind(':product_id', $productId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getStockByProduct: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Transfer stock between locations
     * @param array $data
     * @return bool
     */
    public function transferStock($data)
    {
        try {
            $this->db->beginTransaction();

            // Check source stock availability
            $this->db->query("SELECT quantity FROM stock 
                             WHERE product_id = :product_id 
                             AND warehouse_id = :from_warehouse_id 
                             AND location_id = :from_location_id");

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':from_warehouse_id', $data['from_warehouse_id']);
            $this->db->bind(':from_location_id', $data['from_location_id']);

            $sourceStock = $this->db->single();

            if (!$sourceStock || $sourceStock->quantity < $data['quantity']) {
                throw new Exception("Insufficient stock for transfer");
            }

            // Reduce source stock
            $newSourceQuantity = $sourceStock->quantity - $data['quantity'];
            $this->db->query("UPDATE stock 
                             SET quantity = :quantity, updated_at = NOW()
                             WHERE product_id = :product_id 
                             AND warehouse_id = :warehouse_id 
                             AND location_id = :location_id");

            $this->db->bind(':quantity', $newSourceQuantity);
            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':warehouse_id', $data['from_warehouse_id']);
            $this->db->bind(':location_id', $data['from_location_id']);
            $this->db->execute();

            // Add to destination stock
            $this->db->query("SELECT quantity FROM stock 
                             WHERE product_id = :product_id 
                             AND warehouse_id = :to_warehouse_id 
                             AND location_id = :to_location_id");

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':to_warehouse_id', $data['to_warehouse_id']);
            $this->db->bind(':to_location_id', $data['to_location_id']);

            $destStock = $this->db->single();

            if ($destStock) {
                $newDestQuantity = $destStock->quantity + $data['quantity'];
                $this->db->query("UPDATE stock 
                                 SET quantity = :quantity, updated_at = NOW()
                                 WHERE product_id = :product_id 
                                 AND warehouse_id = :warehouse_id 
                                 AND location_id = :location_id");

                $this->db->bind(':quantity', $newDestQuantity);
                $this->db->bind(':product_id', $data['product_id']);
                $this->db->bind(':warehouse_id', $data['to_warehouse_id']);
                $this->db->bind(':location_id', $data['to_location_id']);
                $this->db->execute();
            } else {
                $this->db->query("INSERT INTO stock 
                                 (product_id, warehouse_id, location_id, quantity) 
                                 VALUES (:product_id, :warehouse_id, :location_id, :quantity)");

                $this->db->bind(':product_id', $data['product_id']);
                $this->db->bind(':warehouse_id', $data['to_warehouse_id']);
                $this->db->bind(':location_id', $data['to_location_id']);
                $this->db->bind(':quantity', $data['quantity']);
                $this->db->execute();
            }

            // Record transfer movements
            $this->recordTransferMovement($data, 'transfer_out', $data['from_warehouse_id'], $data['from_location_id']);
            $this->recordTransferMovement($data, 'transfer_in', $data['to_warehouse_id'], $data['to_location_id']);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in transferStock: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Record transfer movement
     * @param array $data
     * @param string $type
     * @param int $warehouseId
     * @param int $locationId
     */
    private function recordTransferMovement($data, $type, $warehouseId, $locationId)
    {
        $this->db->query("INSERT INTO stock_movements 
                         (product_id, warehouse_id, location_id, movement_type, quantity_change, 
                          reason, notes, created_by)
                         VALUES (:product_id, :warehouse_id, :location_id, :movement_type, 
                                 :quantity_change, :reason, :notes, :created_by)");

        $quantityChange = ($type === 'transfer_out') ? -$data['quantity'] : $data['quantity'];

        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':warehouse_id', $warehouseId);
        $this->db->bind(':location_id', $locationId);
        $this->db->bind(':movement_type', $type);
        $this->db->bind(':quantity_change', $quantityChange);
        $this->db->bind(':reason', 'Stock transfer');
        $this->db->bind(':notes', $data['notes'] ?? '');
        $this->db->bind(':created_by', $data['created_by']);

        $this->db->execute();
    }

    /**
     * Get products by barcode
     * @param string $barcode
     * @return object|null
     */
    public function getProductByBarcode($barcode)
    {
        try {
            $sql = "SELECT s.*, p.product_name, p.product_code, p.unit_price, p.barcode,
                           c.category_name, b.brand_name
                    FROM stock s
                    LEFT JOIN products p ON s.product_id = p.product_id
                    LEFT JOIN categories c ON p.category_id = c.category_id
                    LEFT JOIN brands b ON p.brand_id = b.brand_id
                    WHERE p.barcode = :barcode
                    LIMIT 1";

            $this->db->query($sql);
            $this->db->bind(':barcode', $barcode);
            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error getting product by barcode: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update minimum stock level for a product
     * @param int $productId
     * @param int $minimumStock
     * @return bool
     */
    public function updateMinimumStock($productId, $minimumStock)
    {
        try {
            $sql = "UPDATE products SET minimum_stock_level = :minimum_stock WHERE product_id = :product_id";
            $this->db->query($sql);
            $this->db->bind(':minimum_stock', $minimumStock);
            $this->db->bind(':product_id', $productId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error updating minimum stock: " . $e->getMessage());
            return false;
        }
    }
}