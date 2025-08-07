<?php
/**
 * Cycle Count Model
 * Handles cycle count data operations
 */
class CycleCount
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all cycle counts
     * @return array|false
     */
    public function getCycleCounts($limit = null)
    {
        try {
            $sql = "SELECT cc.*, u.username as created_by_name,
                           COUNT(cci.id) as item_count
                    FROM cycle_counts cc
                    LEFT JOIN users u ON cc.created_by = u.user_id
                    LEFT JOIN cycle_count_items cci ON cc.id = cci.cycle_count_id
                    GROUP BY cc.id
                    ORDER BY cc.created_at DESC";

            if ($limit) {
                $sql .= " LIMIT :limit";
            }

            $this->db->query($sql);

            if ($limit) {
                $this->db->bind(':limit', $limit);
            }

            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getCycleCounts: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cycle count by ID
     * @param int $id
     * @return object|false|null
     */
    public function getCycleCountById($id)
    {
        try {
            $this->db->query("SELECT cc.*, u.username as created_by_name
                             FROM cycle_counts cc
                             LEFT JOIN users u ON cc.created_by = u.user_id
                             WHERE cc.id = :id");
            $this->db->bind(':id', $id);
            $result = $this->db->single();
            return $result ?: false;
        } catch (Exception $e) {
            error_log("Error in getCycleCountById: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Create new cycle count
     * @param array $data
     * @return bool
     */
    public function createCycleCount($data)
    {
        try {
            $this->db->query("INSERT INTO cycle_counts 
                             (count_name, type, location_id, category_id, planned_date, notes, created_by, status) 
                             VALUES (:count_name, :type, :location_id, :category_id, :planned_date, :notes, :created_by, 'planned')");

            $this->db->bind(':count_name', $data['count_name']);
            $this->db->bind(':type', $data['type']);
            $this->db->bind(':location_id', !empty($data['location_id']) ? $data['location_id'] : null);
            $this->db->bind(':category_id', !empty($data['category_id']) ? $data['category_id'] : null);
            $this->db->bind(':planned_date', $data['planned_date']);
            $this->db->bind(':notes', $data['notes']);
            $this->db->bind(':created_by', $data['created_by']);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in createCycleCount: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Start cycle count and generate items
     * @param int $id
     * @param array $products
     * @return bool
     */
    public function startCycleCount($id, $products)
    {
        try {
            $this->db->beginTransaction();

            // Update status to in_progress
            $this->db->query("UPDATE cycle_counts SET status = 'in_progress', started_at = NOW() WHERE id = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();

            // Create cycle count items for each product
            foreach ($products as $product) {
                // Get current Inventory quantity
                $this->db->query("SELECT COALESCE(SUM(quantity), 0) as current_Inventory 
                                 FROM Inventory WHERE product_id = :product_id");
                $this->db->bind(':product_id', $product->product_id);
                $InventoryResult = $this->db->single();
                $expectedQuantity = $InventoryResult ? $InventoryResult->current_Inventory : 0;

                // Insert cycle count item
                $this->db->query("INSERT INTO cycle_count_items 
                                 (cycle_count_id, product_id, expected_quantity) 
                                 VALUES (:cycle_count_id, :product_id, :expected_quantity)");

                $this->db->bind(':cycle_count_id', $id);
                $this->db->bind(':product_id', $product->product_id);
                $this->db->bind(':expected_quantity', $expectedQuantity);
                $this->db->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in startCycleCount: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cycle count items
     * @param int $cycleCountId
     * @return array|false
     */
    public function getCycleCountItems($cycleCountId)
    {
        try {
            $this->db->query("SELECT cci.*, p.product_name, p.product_code,
                                    (cci.counted_quantity - cci.expected_quantity) as variance_calc,
                                    u.username as counted_by_name
                             FROM cycle_count_items cci
                             LEFT JOIN products p ON cci.product_id = p.product_id
                             LEFT JOIN users u ON cci.counted_by = u.user_id
                             WHERE cci.cycle_count_id = :cycle_count_id
                             ORDER BY p.product_name");

            $this->db->bind(':cycle_count_id', $cycleCountId);
            return $this->db->resultSet();
        } catch (Exception $e) {
            error_log("Error in getCycleCountItems: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update item count
     * @param int $itemId
     * @param int $countedQuantity
     * @param string $notes
     * @param int $countedBy
     * @return bool
     */
    public function updateItemCount($itemId, $countedQuantity, $notes, $countedBy)
    {
        try {
            $this->db->query("UPDATE cycle_count_items 
                             SET counted_quantity = :counted_quantity,
                                 variance = (:counted_quantity - expected_quantity),
                                 notes = :notes,
                                 counted_by = :counted_by,
                                 counted_at = NOW()
                             WHERE id = :id");

            $this->db->bind(':id', $itemId);
            $this->db->bind(':counted_quantity', $countedQuantity);
            $this->db->bind(':notes', $notes);
            $this->db->bind(':counted_by', $countedBy);

            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in updateItemCount: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete cycle count
     * @param int $id
     * @return bool
     */
    public function completeCycleCount($id)
    {
        try {
            $this->db->query("UPDATE cycle_counts SET status = 'completed', completed_at = NOW() WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in completeCycleCount: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel cycle count
     * @param int $id
     * @return bool
     */
    public function cancelCycleCount($id)
    {
        try {
            $this->db->query("UPDATE cycle_counts SET status = 'cancelled' WHERE id = :id");
            $this->db->bind(':id', $id);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error in cancelCycleCount: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get cycle count statistics
     * @return object|false
     */
    public function getCycleCountStats()
    {
        try {
            $this->db->query("SELECT 
                                COUNT(*) as total_counts,
                                SUM(CASE WHEN status = 'planned' THEN 1 ELSE 0 END) as planned_counts,
                                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as active_counts,
                                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_counts,
                                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_counts
                             FROM cycle_counts
                             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)");

            return $this->db->single();
        } catch (Exception $e) {
            error_log("Error in getCycleCountStats: " . $e->getMessage());
            return false;
        }
    }
}