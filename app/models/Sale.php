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
            SELECT s.*, c.name as customer_name 
            FROM sales s 
            LEFT JOIN customers c ON s.customer_id = c.customer_id 
            ORDER BY s.sale_date DESC
        ");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function getTodaysSales()
    {
        $this->db->query("
            SELECT s.*, c.name as customer_name 
            FROM sales s 
            LEFT JOIN customers c ON s.customer_id = c.customer_id 
            WHERE DATE(s.sale_date) = CURDATE() 
            ORDER BY s.sale_date DESC
        ");
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

    public function addSale($data)
    {
        $this->db->query("INSERT INTO sales (customer_id, total_amount, payment_mode) VALUES (:customer_id, :total_amount, :payment_mode)");
        // Bind values
        $this->db->bind(':customer_id', $data['customer_id']);
        $this->db->bind(':total_amount', $data['total_amount']);
        $this->db->bind(':payment_mode', $data['payment_mode']);

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
            $this->db->bind(':discount', $data['discount']);
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
            // Get available Inventory ordered by location priority and date
            // Prioritize non-bulk locations (not starting with 'B-')
            $this->db->query("
                SELECT s.Inventory_id, s.quantity, s.location_id, wl.location_name
                FROM Inventory s
                LEFT JOIN locations l ON s.location_id = l.id
                WHERE s.product_id = :product_id 
                AND s.quantity > 0
                ORDER BY 
                    CASE WHEN wl.location_name LIKE 'B-%' THEN 1 ELSE 0 END,
                    s.Inventory_id ASC
            ");
            $this->db->bind(':product_id', $productId);
            $InventoryEntries = $this->db->resultSet();

            $remainingToDeduct = $quantityToDeduct;

            foreach ($InventoryEntries as $InventoryEntry) {
                if ($remainingToDeduct <= 0)
                    break;

                $deductFromThisEntry = min($InventoryEntry->quantity, $remainingToDeduct);
                $newQuantity = $InventoryEntry->quantity - $deductFromThisEntry;

                // Update Inventory entry
                $this->db->query("
                    UPDATE Inventory 
                    SET quantity = :new_quantity 
                    WHERE Inventory_id = :Inventory_id
                ");
                $this->db->bind(':new_quantity', $newQuantity);
                $this->db->bind(':Inventory_id', $InventoryEntry->Inventory_id);
                $this->db->execute();

                // Log Inventory movement
                $this->db->query("
                    INSERT INTO Inventory_movements (product_id, from_location_id, quantity, movement_date) 
                    VALUES (:product_id, :location_id, :quantity, NOW())
                ");
                $this->db->bind(':product_id', $productId);
                $this->db->bind(':location_id', $InventoryEntry->location_id);
                $this->db->bind(':quantity', $deductFromThisEntry);
                $this->db->execute();

                $remainingToDeduct -= $deductFromThisEntry;
            }

            // Check if we were able to deduct all requested quantity
            if ($remainingToDeduct > 0) {
                throw new Exception("Insufficient Inventory. Could only deduct " . ($quantityToDeduct - $remainingToDeduct) . " of " . $quantityToDeduct);
            }

            // Update main product Inventory quantity
            $this->db->query("
                UPDATE products 
                SET Inventory_quantity = Inventory_quantity - :quantity 
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
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function getSaleItemsBySaleId($sale_id)
    {
        $this->db->query("SELECT * FROM sale_items WHERE sale_id = :sale_id");
        $this->db->bind(':sale_id', $sale_id);
        $result = $this->db->resultSet();
        return $result ? $result : [];
    }

}
