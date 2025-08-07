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
                       wl.location_name, wl.rack, wl.shelf
                FROM Inventory s
                LEFT JOIN products p ON s.product_id = p.product_id
                LEFT JOIN warehouse_locations wl ON s.location_id = wl.location_id
                WHERE wl.location_name LIKE 'B-%' 
                AND s.quantity > 0
                ORDER BY wl.location_name, p.product_name
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
                SELECT location_id, location_name, rack, shelf
                FROM warehouse_locations 
                WHERE location_name NOT LIKE 'B-%'
                ORDER BY location_name
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
                LEFT JOIN warehouse_locations wl ON s.location_id = wl.location_id
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
                SELECT COALESCE(SUM(quantity), 0) as current_Inventory 
                FROM Inventory 
                WHERE product_id = :product_id
            ");
            $this->db->bind(':product_id', $data['product_id']);
            $InventoryResult = $this->db->single();
            $currentInventory = $InventoryResult ? $InventoryResult->current_Inventory : 0;

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
}