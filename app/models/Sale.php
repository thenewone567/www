<?php
class Sale
{
    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    public function getSales()
    {
        $this->db->query("
            SELECT s.*, c.customer_name as customer_name 
            FROM sales s 
            LEFT JOIN customers c ON s.customer_id = c.customer_id 
            ORDER BY s.sale_date DESC
        ");
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getTodaysSales()
    {
        $this->db->query("
            SELECT s.*, c.customer_name as customer_name 
            FROM sales s 
            LEFT JOIN customers c ON s.customer_id = c.customer_id 
            WHERE DATE(s.sale_date) = CURDATE() 
            ORDER BY s.sale_date DESC
        ");
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addSale($data)
    {
        $this->db->query("INSERT INTO sales (customer_id, total_amount, payment_mode, sale_date) VALUES (:customer_id, :total_amount, :payment_mode, :sale_date)");
        // Bind values
        $this->db->bind(':customer_id', $data['customer_id']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':payment_mode', $data['payment_mode']);
        $this->db->bind(':sale_date', $data['sale_date'] ?? date('Y-m-d H:i:s'));

        // Execute
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }

    public function addSaleItem($data)
    {
        try {
            $this->db->beginTransaction();

            // Add sale item
            $this->db->query("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, discount) VALUES (:sale_id, :product_id, :quantity, :unit_price, :discount)");
            $this->db->bind(':sale_id', $data['sale_id']);
            $this->db->bind(':product_id', $data['product_id']);
            $this->db->bind(':quantity', $data['quantity']);
            $this->db->bind(':unit_price', $data['unit_price']);
            // Ensure discount is defined (default 0)
            $discountVal = isset($data['discount']) ? $data['discount'] : 0;
            $this->db->bind(':discount', $discountVal);
            $this->db->execute();

            // Deduct inventory from inventory
            $inventoryDeducted = $this->deductInventory($data['product_id'], $data['quantity']);
            if (!$inventoryDeducted) {
                throw new Exception("Insufficient inventory for product ID: " . $data['product_id']);
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in addSaleItem: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Deduct Inventory from inventory when items are sold
     * Uses FIFO logic - deducts from oldest Inventory first
     * Prioritizes regular locations over bulk locations
     */
    private function deductInventory($productId, $quantityToDeduct)
    {
        try {
            // Get available inventory ordered by location priority and date
            // Prioritize non-bulk locations (not starting with 'B-')
            $this->db->query("
                SELECT i.inventory_id, i.quantity, i.location_id, l.location_name
                FROM inventory i
                LEFT JOIN locations l ON i.location_id = l.location_id
                WHERE i.product_id = :product_id 
                AND i.quantity > 0
                ORDER BY 
                    CASE WHEN l.location_name LIKE 'B-%' THEN 1 ELSE 0 END,
                    i.inventory_id ASC
            ");
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            $inventoryEntries = $this->db->resultSet();

            $remainingToDeduct = $quantityToDeduct;

            foreach ($inventoryEntries as $inventoryEntry) {
                if ($remainingToDeduct <= 0)
                    break;

                $deductFromThisEntry = min($inventoryEntry->quantity, $remainingToDeduct);
                $newQuantity = $inventoryEntry->quantity - $deductFromThisEntry;

                // Update inventory entry
                $this->db->query("
                    UPDATE inventory 
                    SET quantity = :new_quantity 
                    WHERE inventory_id = :inventory_id
                ");
                $this->db->bind(':new_quantity', $newQuantity);
                $this->db->bind(':inventory_id', $inventoryEntry->inventory_id);
                $this->db->execute();

                // Log inventory movement
                $this->db->query("
                    INSERT INTO inventory_movements (product_id, from_location_id, quantity, movement_date) 
                    VALUES (:product_id, :location_id, :quantity, NOW())
                ");
                $this->db->bind(':product_id', $productId);
                $this->db->bind(':location_id', $inventoryEntry->location_id);
                $this->db->bind(':quantity', $deductFromThisEntry);
                $this->db->execute();

                $remainingToDeduct -= $deductFromThisEntry;
            }

            // Check if we were able to deduct all requested quantity
            if ($remainingToDeduct > 0) {
                throw new Exception("Insufficient inventory. Could only deduct " . ($quantityToDeduct - $remainingToDeduct) . " of " . $quantityToDeduct);
            }

            // Update main product inventory quantity
            $this->db->query("
                UPDATE products 
                SET current_inventory = current_inventory - :quantity 
                WHERE product_id = :product_id
            ");
            $this->db->bind(':quantity', $quantityToDeduct);
            $this->db->bind(':product_id', $productId);
            $this->db->execute();

            return true;
        } catch (Exception $e) {
            error_log("Error in deductInventory: " . $e->getMessage());
            return false;
        }
    }

    public function getSaleById($id)
    {
        $this->db->query("SELECT * FROM sales WHERE sale_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function getSaleItemsBySaleId($sale_id)
    {
        $this->db->query("SELECT * FROM sale_items WHERE sale_id = :sale_id");
        $this->db->bind(':sale_id', $sale_id);
        $this->db->execute();
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    // =============== BOT HELPER METHODS ===============

    /**
     * Get today's sales count for bot dashboard
     */
    public function getTodaysSalesCount()
    {
        try {
            $this->db->query("SELECT COUNT(*) as count FROM sales WHERE DATE(sale_date) = CURDATE()");
            $result = $this->db->executeSingle();
            return $result->count ?? 0;
        } catch (Exception $e) {
            error_log('Error getting today\'s sales count: ' . $e->getMessage());
            return 0;
        }
    }
}

?>