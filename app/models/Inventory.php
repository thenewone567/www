<?php
/**
 * Inventory Model
 * Handles inventory data operations including Inventory movements and adjustments
 */
class Inventory
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }



    /**
     * Get low inventory items (inventory terminology version)
     * @param int $threshold
     * @return array
     */
    public function getLowInventoryItems($threshold = 10)
    {
        // No longer supported: legacy Inventory method removed
        return [];
    }


    /**
     * Get inventory movements/adjustments (inventory terminology version)
     * @param int $limit
     * @return array
     */
    public function getInventoryMovements($limit = 50)
    {
        // No longer supported: legacy Inventory method removed
        return [];
    }

    /**
     * Adjust Inventory quantity
     * @param array $data
     * @return bool
     */
    public function adjustInventory($data)
    {
        try {
            $this->db->beginTransaction();

            // Get current Inventory
            $this->db->query("SELECT quantity FROM Inventory 
                             WHERE product_id = :product_id 
                             AND warehouse_id = :warehouse_id 
                             AND location_id = :location_id");

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':warehouse_id', $data['warehouse_id']);
            $this->db->bind(':location_id', $data['location_id']);

            $currentInventory = $this->db->single();
            $oldQuantity = $currentInventory ? $currentInventory->quantity : 0;

            // Update or insert Inventory record
            if ($currentInventory) {
                $this->db->query("UPDATE Inventory 
                                 SET quantity = :new_quantity, updated_at = NOW()
                                 WHERE product_id = :product_id 
                                 AND warehouse_id = :warehouse_id 
                                 AND location_id = :location_id");
            } else {
                $this->db->query("INSERT INTO Inventory 
                                 (product_id, warehouse_id, location_id, quantity) 
                                 VALUES (:product_id, :warehouse_id, :location_id, :new_quantity)");
            }

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':warehouse_id', $data['warehouse_id']);
            $this->db->bind(':location_id', $data['location_id']);
            $this->db->bind(':new_quantity', $data['new_quantity']);

            if (!$this->db->execute()) {
                throw new Exception("Failed to update Inventory");
            }

            // Record Inventory movement
            $this->db->query("INSERT INTO Inventory_movements 
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
                throw new Exception("Failed to record Inventory movement");
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in adjustInventory: " . $e->getMessage());
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
                                COALESCE(SUM(s.quantity), 0) as total_Inventory_quantity,
                                COALESCE(SUM(s.quantity * p.unit_price), 0) as total_Inventory_value
                             FROM products p
                             LEFT JOIN Inventory s ON p.product_id = s.product_id");

            $summary = $this->db->single();

            // If no summary data, create default object
            if (!$summary) {
                $summary = (object) [
                    'total_products' => 0,
                    'total_warehouses' => 0,
                    'total_Inventory_quantity' => 0,
                    'total_Inventory_value' => 0
                ];
            }

            // Get low Inventory count
            $summary->low_Inventory_items = 0;

            return $summary;
        } catch (Exception $e) {
            error_log("Error in getInventorySummary: " . $e->getMessage());
            return (object) [
                'total_products' => 0,
                'total_warehouses' => 0,
                'total_Inventory_quantity' => 0,
                'total_Inventory_value' => 0,
                'low_Inventory_items' => 0
            ];
        }
    }


    /**
     * Transfer Inventory between locations
     * @param array $data
     * @return bool
     */
    public function transferInventory($data)
    {
        try {
            $this->db->beginTransaction();

            // Check source Inventory availability
            $this->db->query("SELECT quantity FROM Inventory 
                             WHERE product_id = :product_id 
                             AND warehouse_id = :from_warehouse_id 
                             AND location_id = :from_location_id");

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':from_warehouse_id', $data['from_warehouse_id']);
            $this->db->bind(':from_location_id', $data['from_location_id']);

            $sourceInventory = $this->db->single();

            if (!$sourceInventory || $sourceInventory->quantity < $data['quantity']) {
                throw new Exception("Insufficient Inventory for transfer");
            }

            // Reduce source Inventory
            $newSourceQuantity = $sourceInventory->quantity - $data['quantity'];
            $this->db->query("UPDATE Inventory 
                             SET quantity = :quantity, updated_at = NOW()
                             WHERE product_id = :product_id 
                             AND warehouse_id = :warehouse_id 
                             AND location_id = :location_id");

            $this->db->bind(':quantity', $newSourceQuantity);
            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':warehouse_id', $data['from_warehouse_id']);
            $this->db->bind(':location_id', $data['from_location_id']);
            $this->db->execute();

            // Add to destination Inventory
            $this->db->query("SELECT quantity FROM Inventory 
                             WHERE product_id = :product_id 
                             AND warehouse_id = :to_warehouse_id 
                             AND location_id = :to_location_id");

            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':to_warehouse_id', $data['to_warehouse_id']);
            $this->db->bind(':to_location_id', $data['to_location_id']);

            $destInventory = $this->db->single();

            if ($destInventory) {
                $newDestQuantity = $destInventory->quantity + $data['quantity'];
                $this->db->query("UPDATE Inventory 
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
                $this->db->query("INSERT INTO Inventory 
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
            error_log("Error in transferInventory: " . $e->getMessage());
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
        $this->db->query("INSERT INTO Inventory_movements 
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
        $this->db->bind(':reason', 'Inventory transfer');
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
                    FROM Inventory s
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
     * Enhanced product search for Unified Smart Search
     */
    public function searchProducts($query)
    {
        try {
            $searchTerm = '%' . $query . '%';

            $this->db->query("
                SELECT 
                    p.product_id,
                    p.product_name,
                    p.product_code,
                    p.sku,
                    p.unit_price,
                    p.current_inventory,
                    p.reorder_level,
                    p.barcode,
                    c.category_name,
                    b.brand_name,
                    l.location_name,
                    l.location_id
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.category_id
                LEFT JOIN brands b ON p.brand_id = b.brand_id
                LEFT JOIN product_locations pl ON p.product_id = pl.product_id
                LEFT JOIN locations l ON pl.location_id = l.location_id
                WHERE p.product_name LIKE :search 
                   OR p.product_code LIKE :search 
                   OR p.sku LIKE :search
                   OR p.barcode LIKE :search
                   OR c.category_name LIKE :search
                   OR b.brand_name LIKE :search
                ORDER BY 
                    CASE 
                        WHEN p.product_code = :exact_search THEN 1
                        WHEN p.sku = :exact_search THEN 2
                        WHEN p.barcode = :exact_search THEN 3
                        WHEN p.product_name LIKE :exact_search THEN 4
                        ELSE 5
                    END,
                    p.product_name
                LIMIT 20
            ");

            $this->db->bind(':search', $searchTerm);
            $this->db->bind(':exact_search', $query);

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in searchProducts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Enhanced location search for Unified Smart Search
     */
    public function searchLocations($query)
    {
        try {
            $searchTerm = '%' . $query . '%';

            $this->db->query("
                SELECT 
                    l.location_id,
                    l.location_name,
                    l.location_type,
                    l.capacity,
                    l.rack,
                    l.shelf,
                    l.aisle,
                    COUNT(pl.product_id) as item_count,
                    SUM(p.current_inventory) as total_inventory,
                    lb.barcode_value
                FROM locations l
                LEFT JOIN product_locations pl ON l.location_id = pl.location_id
                LEFT JOIN products p ON pl.product_id = p.product_id
                LEFT JOIN location_barcodes lb ON l.location_id = lb.location_id
                WHERE l.location_name LIKE :search 
                   OR l.location_type LIKE :search
                   OR l.rack LIKE :search
                   OR l.shelf LIKE :search
                   OR l.aisle LIKE :search
                   OR lb.barcode_value LIKE :search
                GROUP BY l.location_id
                ORDER BY 
                    CASE 
                        WHEN l.location_name = :exact_search THEN 1
                        WHEN lb.barcode_value = :exact_search THEN 2
                        ELSE 3
                    END,
                    l.location_name
                LIMIT 15
            ");

            $this->db->bind(':search', $searchTerm);
            $this->db->bind(':exact_search', $query);

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in searchLocations: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search inventory adjustments
     */
    public function searchAdjustments($query)
    {
        try {
            $searchTerm = '%' . $query . '%';

            $this->db->query("
                SELECT 
                    ia.adjustment_id,
                    ia.product_id,
                    ia.quantity_change,
                    ia.reason,
                    ia.adjustment_date as created_at,
                    ia.updated_by,
                    p.product_name,
                    p.product_code,
                    p.sku,
                    u.name as user_name
                FROM inventory_adjustments ia
                INNER JOIN products p ON ia.product_id = p.product_id
                LEFT JOIN users u ON ia.updated_by = u.user_id
                WHERE p.product_name LIKE :search
                   OR p.product_code LIKE :search
                   OR p.sku LIKE :search
                   OR ia.reason LIKE :search
                   OR u.name LIKE :search
                   OR DATE_FORMAT(ia.adjustment_date, '%Y-%m-%d') LIKE :search
                ORDER BY ia.adjustment_date DESC
                LIMIT 25
            ");

            $this->db->bind(':search', $searchTerm);

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in searchAdjustments: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Search cycle counts
     */
    public function searchCycleCounts($query)
    {
        try {
            $searchTerm = '%' . $query . '%';

            $this->db->query("
                SELECT 
                    cc.count_id,
                    cc.location_id,
                    cc.count_date,
                    cc.status,
                    cc.notes,
                    l.location_name,
                    COUNT(DISTINCT cci.product_id) as products_counted,
                    SUM(CASE WHEN cci.variance != 0 THEN 1 ELSE 0 END) as variances,
                    u.name as counted_by
                FROM cycle_counts cc
                INNER JOIN locations l ON cc.location_id = l.location_id
                LEFT JOIN cycle_count_items cci ON cc.count_id = cci.count_id
                LEFT JOIN users u ON cc.counted_by = u.user_id
                WHERE l.location_name LIKE :search
                   OR cc.status LIKE :search
                   OR cc.notes LIKE :search
                   OR u.name LIKE :search
                   OR DATE_FORMAT(cc.count_date, '%Y-%m-%d') LIKE :search
                GROUP BY cc.count_id
                ORDER BY cc.count_date DESC
                LIMIT 20
            ");

            $this->db->bind(':search', $searchTerm);

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in searchCycleCounts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Save cycle count with all counted items
     */
    public function saveCycleCount($countInfo, $countedItems, $userId = null)
    {
        try {
            $this->db->beginTransaction();

            // Create cycle count record
            $this->db->query("
                INSERT INTO cycle_counts (
                    location_id, count_type, notes, status, count_date, counted_by
                ) VALUES (
                    :location_id, :count_type, :notes, 'completed', NOW(), :user_id
                )
            ");

            $this->db->bind(':location_id', $countInfo['location_id']);
            $this->db->bind(':count_type', $countInfo['type']);
            $this->db->bind(':notes', $countInfo['notes'] ?? '');
            $this->db->bind(':user_id', $userId);

            $this->db->execute();
            $cycleCountId = $this->db->lastInsertId();

            // Process each counted item
            $varianceCount = 0;
            foreach ($countedItems as $item) {
                // Insert cycle count item
                $this->db->query("
                    INSERT INTO cycle_count_items (
                        count_id, product_id, system_count, physical_count, variance, notes
                    ) VALUES (
                        :count_id, :product_id, :system_count, :physical_count, :variance, :notes
                    )
                ");

                $this->db->bind(':count_id', $cycleCountId);
                $this->db->bind(':product_id', $item['product_id']);
                $this->db->bind(':system_count', $item['system_count']);
                $this->db->bind(':physical_count', $item['physical_count']);
                $this->db->bind(':variance', $item['variance']);
                $this->db->bind(':notes', $item['notes'] ?? '');

                $this->db->execute();

                if ($item['variance'] != 0) {
                    $varianceCount++;
                }
            }

            $this->db->commit();

            return [
                'success' => true,
                'data' => [
                    'cycle_count_id' => $cycleCountId,
                    'items_counted' => count($countedItems),
                    'variances_found' => $varianceCount
                ]
            ];

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in saveCycleCount: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to save cycle count: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Update minimum Inventory level for a product
     * @param int $productId
     * @param int $minimumInventory
     * @return bool
     */
    public function updateMinimumInventory($productId, $minimumInventory)
    {
        try {
            $sql = "UPDATE products SET minimum_Inventory_level = :minimum_Inventory WHERE product_id = :product_id";
            $this->db->query($sql);
            $this->db->bind(':minimum_Inventory', $minimumInventory);
            $this->db->bind(':product_id', $productId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error updating minimum Inventory: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Inventory items in bulk locations (B-*)
     */
    public function getBulkLocationInventory()
    {
        try {
            $this->db->query("
                SELECT s.Inventory_id, s.product_id, s.quantity, s.batch_number,
                       p.product_name, p.sku, 
                       wl.location_name, wl.shelf, wl.bin
                FROM Inventory s
                LEFT JOIN products p ON s.product_id = p.product_id
                LEFT JOIN locations wl ON s.location_id = wl.location_id
                WHERE wl.location_type = 'storage' 
                AND s.quantity > 0
                ORDER BY wl.standardized_address, p.product_name
            ");

            $result = $this->db->resultSet();
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in getBulkLocationInventory: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get regular (non-bulk) warehouse locations
     */
    public function getRegularLocations()
    {
        try {
            $this->db->query("
                SELECT location_id, location_name, standardized_address, shelf, bin
                FROM locations 
                WHERE location_type IN ('storage', 'bin')
                ORDER BY standardized_address
            ");

            $result = $this->db->resultSet();
            return $result ? $result : [];
        } catch (Exception $e) {
            error_log("Error in getRegularLocations: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Transfer items from bulk location to regular location
     */
    public function transferFromBulkLocation($InventoryId, $quantity, $toLocationId)
    {
        try {
            $this->db->beginTransaction();

            // Get source Inventory details
            $this->db->query("
                SELECT s.*, wl.location_name as from_location_name
                FROM Inventory s
                LEFT JOIN locations wl ON s.location_id = wl.location_id
                WHERE s.Inventory_id = :Inventory_id
            ");
            $this->db->bind(':Inventory_id', $InventoryId);
            $sourceInventory = $this->db->single();

            if (!$sourceInventory || $sourceInventory->quantity < $quantity) {
                throw new Exception("Insufficient Inventory for transfer");
            }

            // Update source Inventory
            $newSourceQuantity = $sourceInventory->quantity - $quantity;
            $this->db->query("
                UPDATE Inventory 
                SET quantity = :quantity 
                WHERE Inventory_id = :Inventory_id
            ");
            $this->db->bind(':quantity', $newSourceQuantity);
            $this->db->bind(':Inventory_id', $InventoryId);
            $this->db->execute();

            // Add to destination location
            $this->db->query("
                SELECT Inventory_id, quantity 
                FROM Inventory 
                WHERE product_id = :product_id 
                AND location_id = :location_id
            ");
            $this->db->bind(':product_id', $sourceInventory->product_id);
            $this->db->bind(':location_id', $toLocationId);
            $destInventory = $this->db->single();

            if ($destInventory) {
                // Update existing Inventory entry
                $newDestQuantity = $destInventory->quantity + $quantity;
                $this->db->query("
                    UPDATE Inventory 
                    SET quantity = :quantity 
                    WHERE Inventory_id = :Inventory_id
                ");
                $this->db->bind(':quantity', $newDestQuantity);
                $this->db->bind(':Inventory_id', $destInventory->Inventory_id);
                $this->db->execute();
            } else {
                // Create new Inventory entry
                $this->db->query("
                    INSERT INTO Inventory (product_id, quantity, location_id, batch_number) 
                    VALUES (:product_id, :quantity, :location_id, :batch_number)
                ");
                $this->db->bind(':product_id', $sourceInventory->product_id);
                $this->db->bind(':quantity', $quantity);
                $this->db->bind(':location_id', $toLocationId);
                $this->db->bind(':batch_number', $sourceInventory->batch_number);
                $this->db->execute();
            }

            // Log the movement
            $this->db->query("
                INSERT INTO Inventory_movements (product_id, from_location_id, to_location_id, quantity, movement_date) 
                VALUES (:product_id, :from_location_id, :to_location_id, :quantity, NOW())
            ");
            $this->db->bind(':product_id', $sourceInventory->product_id);
            $this->db->bind(':from_location_id', $sourceInventory->location_id);
            $this->db->bind(':to_location_id', $toLocationId);
            $this->db->bind(':quantity', $quantity);
            $this->db->execute();

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in transferFromBulkLocation: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Simple Inventory adjustment based on quantity change
     * @param array $data
     * @return bool
     */
    public function adjustInventorySimple($data)
    {
        try {
            $this->db->beginTransaction();

            // Get current Inventory for this product
            $this->db->query("
                SELECT COALESCE(SUM(quantity), 0) as current_inventory 
                FROM Inventory 
                WHERE product_id = :product_id
            ");
            $this->db->bind(':product_id', $data['product_id']);
            $InventoryResult = $this->db->single();
            $currentInventory = $InventoryResult ? $InventoryResult->current_inventory : 0;

            // Calculate new Inventory
            $quantityChange = (int) $data['quantity_change'];
            $newInventory = $currentInventory + $quantityChange;

            if ($newInventory < 0) {
                throw new Exception("Insufficient Inventory. Current: {$currentInventory}, Change: {$quantityChange}");
            }

            // Check if there's an existing Inventory record for this product
            $this->db->query("
                SELECT Inventory_id, quantity 
                FROM Inventory 
                WHERE product_id = :product_id 
                ORDER BY Inventory_id DESC 
                LIMIT 1
            ");
            $this->db->bind(':product_id', $data['product_id']);
            $existingInventory = $this->db->single();

            if ($existingInventory && $quantityChange != 0) {
                // Update existing record
                $newQuantity = $existingInventory->quantity + $quantityChange;
                if ($newQuantity < 0) {
                    throw new Exception("Cannot reduce Inventory below zero");
                }

                if ($newQuantity == 0) {
                    // Delete the Inventory record if quantity becomes 0
                    $this->db->query("DELETE FROM Inventory WHERE Inventory_id = :Inventory_id");
                    $this->db->bind(':Inventory_id', $existingInventory->Inventory_id);
                } else {
                    // Update the Inventory record
                    $this->db->query("UPDATE Inventory SET quantity = :quantity WHERE Inventory_id = :Inventory_id");
                    $this->db->bind(':quantity', $newQuantity);
                    $this->db->bind(':Inventory_id', $existingInventory->Inventory_id);
                }
                $this->db->execute();
            } else if ($quantityChange > 0) {
                // Create new Inventory record only if adding Inventory
                $batchNumber = 'ADJ-' . $data['product_id'] . '-' . date('YmdHis');
                $this->db->query("
                    INSERT INTO Inventory (product_id, quantity, batch_number) 
                    VALUES (:product_id, :quantity, :batch_number)
                ");
                $this->db->bind(':product_id', $data['product_id']);
                $this->db->bind(':quantity', $quantityChange);
                $this->db->bind(':batch_number', $batchNumber);
                $this->db->execute();
            } else if ($quantityChange < 0) {
                throw new Exception("No existing Inventory to reduce");
            }

            // Record the adjustment in Inventory_adjustments table
            if ($quantityChange != 0) {
                $this->db->query("
                    INSERT INTO Inventory_adjustments (
                        product_id, quantity_change, reason, adjustment_date
                    ) VALUES (
                        :product_id, :quantity_change, :reason, NOW()
                    )
                ");

                $this->db->bind(':product_id', $data['product_id']);
                $this->db->bind(':quantity_change', $quantityChange);
                $this->db->bind(':reason', $data['reason'] ?? 'Manual adjustment');
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in adjustInventorySimple: " . $e->getMessage());
            return false;
        }
    }

    // =============== BOT HELPER METHODS ===============

    /**
     * Get low stock count for bot dashboard
     */
    public function getLowStockCount($threshold = 10)
    {
        try {
            $this->db->query("
                SELECT COUNT(DISTINCT p.product_id) as count 
                FROM products p
                WHERE p.is_active = 1 
                AND p.deleted_at IS NULL
                AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, :threshold)
            ");
            $this->db->bind(':threshold', $threshold);
            $result = $this->db->executeSingle();
            return $result->count ?? 0;
        } catch (Exception $e) {
            error_log('Error getting low stock count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get low stock products for bot operations
     */
    public function getLowStockProducts($threshold = 10)
    {
        try {
            $this->db->query("
                SELECT 
                    p.product_id, 
                    p.product_name, 
                    COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) as stock_quantity,
                    p.reorder_level
                FROM products p
                WHERE p.is_active = 1 
                AND p.deleted_at IS NULL
                AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, :threshold)
                ORDER BY stock_quantity ASC
                LIMIT 50
            ");
            $this->db->bind(':threshold', $threshold);
            $this->db->execute();
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log('Error getting low stock products: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Update stock quantity for a product
     */
    public function updateStock($productId, $quantityChange)
    {
        try {
            // First check if inventory record exists
            $this->db->query("SELECT inventory_id, quantity FROM inventory WHERE product_id = ?");
            $this->db->bind(1, $productId);
            $existing = $this->db->executeSingle();

            // Debug logging for inventory updates
            $logFile = APPROOT . DS . 'bot_inventory.log';
            $time = date('Y-m-d H:i:s');
            if ($existing) {
                $msg = sprintf("%s - updateStock called for product_id=%s existing_inventory_id=%s existing_quantity=%s quantityChange=%s\n", $time, $productId, $existing->inventory_id, $existing->quantity, $quantityChange);
            } else {
                $msg = sprintf("%s - updateStock called for product_id=%s no_existing_record quantityChange=%s\n", $time, $productId, $quantityChange);
            }
            @file_put_contents($logFile, $msg, FILE_APPEND);

            if ($existing) {
                // Update existing inventory
                $newQuantity = max(0, $existing->quantity + $quantityChange);
                $this->db->query("UPDATE inventory SET quantity = ? WHERE inventory_id = ?");
                $this->db->bind(1, $newQuantity);
                $this->db->bind(2, $existing->inventory_id);
                $res = $this->db->execute();
                $afterMsg = sprintf("%s - updateStock result for product_id=%s inventory_id=%s new_quantity=%s success=%s\n", date('Y-m-d H:i:s'), $productId, $existing->inventory_id, $newQuantity, $res ? 'true' : 'false');
                @file_put_contents($logFile, $afterMsg, FILE_APPEND);
                return $res;
            } else {
                // If trying to reduce stock for a product with no inventory record, fail and log
                if ($quantityChange < 0) {
                    $msg = sprintf("%s - updateStock attempted negative change for non-existing product_id=%s quantityChange=%s - aborting\n", date('Y-m-d H:i:s'), $productId, $quantityChange);
                    @file_put_contents($logFile, $msg, FILE_APPEND);
                    return false;
                }

                // Create new inventory record if it doesn't exist (positive additions only)
                $this->db->query("INSERT INTO inventory (product_id, quantity, location_id) VALUES (?, ?, 1)");
                $this->db->bind(1, $productId);
                $this->db->bind(2, max(0, $quantityChange));
                $res = $this->db->execute();
                $insertMsg = sprintf("%s - updateStock inserted product_id=%s inserted_quantity=%s success=%s\n", date('Y-m-d H:i:s'), $productId, max(0, $quantityChange), $res ? 'true' : 'false');
                @file_put_contents($logFile, $insertMsg, FILE_APPEND);
                return $res;
            }
        } catch (Exception $e) {
            error_log('Error updating stock: ' . $e->getMessage());
            @file_put_contents(APPROOT . DS . 'bot_inventory.log', date('Y-m-d H:i:s') . " - updateStock exception for product_id={$productId} message=" . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }

    /**
     * Get total inventory quantity for a single product
     */
    public function getProductTotal($productId)
    {
        try {
            $this->db->query('SELECT COALESCE(SUM(quantity),0) as total FROM inventory WHERE product_id = ?');
            $this->db->bind(1, $productId);
            $this->db->execute();
            $r = $this->db->single();
            return $r ? (int) $r->total : 0;
        } catch (Exception $e) {
            error_log('Error in getProductTotal: ' . $e->getMessage());
            return null;
        }
    }
}
?>