<?php
/**
 * Business Location Model
 * Handles multi-location enterprise operations
 */
class BusinessLocation
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all business locations for a company
     */
    public function getLocationsByCompany($companyId)
    {
        $this->db->query("
            SELECT bl.*, c.company_name, u.username as manager_name,
                   COUNT(DISTINCT ula.user_id) as staff_count,
                   COUNT(DISTINCT wl.location_id) as warehouse_locations_count
            FROM business_locations bl
            LEFT JOIN companies c ON bl.company_id = c.company_id
            LEFT JOIN users u ON bl.manager_user_id = u.user_id
            LEFT JOIN user_location_assignments ula ON bl.location_id = ula.location_id
            LEFT JOIN warehouse_locations wl ON bl.location_id = wl.business_location_id
            WHERE bl.company_id = :company_id AND bl.is_active = 1
            GROUP BY bl.location_id
            ORDER BY bl.location_name
        ");
        $this->db->bind(':company_id', $companyId);
        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Get locations accessible to a user
     */
    public function getUserAccessibleLocations($userId)
    {
        $this->db->query("
            SELECT DISTINCT bl.*, ula.access_type
            FROM business_locations bl
            INNER JOIN user_location_assignments ula ON bl.location_id = ula.location_id
            WHERE ula.user_id = :user_id AND bl.is_active = 1
            ORDER BY bl.location_name
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Get location details by ID
     */
    public function getLocationById($locationId)
    {
        $this->db->query("
            SELECT bl.*, c.company_name, u.username as manager_name, u.full_name as manager_full_name
            FROM business_locations bl
            LEFT JOIN companies c ON bl.company_id = c.company_id
            LEFT JOIN users u ON bl.manager_user_id = u.user_id
            WHERE bl.location_id = :location_id
        ");
        $this->db->bind(':location_id', $locationId);
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Create new business location
     */
    public function createLocation($data)
    {
        $this->db->query("
            INSERT INTO business_locations 
            (company_id, location_name, location_code, location_type, city, state, address, phone, email, manager_user_id, operating_hours)
            VALUES 
            (:company_id, :location_name, :location_code, :location_type, :city, :state, :address, :phone, :email, :manager_user_id, :operating_hours)
        ");
        
        $this->db->bind(':company_id', $data['company_id']);
        $this->db->bind(':location_name', $data['location_name']);
        $this->db->bind(':location_code', $data['location_code']);
        $this->db->bind(':location_type', $data['location_type']);
        $this->db->bind(':city', $data['city']);
        $this->db->bind(':state', $data['state'] ?? '');
        $this->db->bind(':address', $data['address'] ?? '');
        $this->db->bind(':phone', $data['phone'] ?? '');
        $this->db->bind(':email', $data['email'] ?? '');
        $this->db->bind(':manager_user_id', $data['manager_user_id'] ?? null);
        $this->db->bind(':operating_hours', $data['operating_hours'] ?? null);
        
        return $this->db->execute();
    }

    /**
     * Assign user to location
     */
    public function assignUserToLocation($userId, $locationId, $accessType = 'full')
    {
        $this->db->query("
            INSERT INTO user_location_assignments (user_id, location_id, access_type, assigned_by)
            VALUES (:user_id, :location_id, :access_type, :assigned_by)
            ON DUPLICATE KEY UPDATE access_type = :access_type
        ");
        
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':location_id', $locationId);
        $this->db->bind(':access_type', $accessType);
        $this->db->bind(':assigned_by', $_SESSION['user_id'] ?? 1);
        
        return $this->db->execute();
    }

    /**
     * Get location statistics
     */
    public function getLocationStats($locationId)
    {
        // Get inventory count
        $this->db->query("
            SELECT 
                COUNT(DISTINCT i.product_id) as unique_products,
                SUM(i.quantity) as total_quantity,
                COUNT(DISTINCT wl.location_id) as storage_locations
            FROM inventory i
            LEFT JOIN warehouse_locations wl ON i.location_id = wl.location_id
            WHERE wl.business_location_id = :location_id
        ");
        $this->db->bind(':location_id', $locationId);
        $this->db->execute();
        $inventory_stats = $this->db->single();

        // Get sales stats (last 30 days)
        $this->db->query("
            SELECT 
                COUNT(*) as sales_count,
                COALESCE(SUM(total_amount), 0) as total_sales
            FROM sales 
            WHERE business_location_id = :location_id 
            AND sale_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ");
        $this->db->bind(':location_id', $locationId);
        $this->db->execute();
        $sales_stats = $this->db->single();

        // Get staff count
        $this->db->query("
            SELECT COUNT(*) as staff_count
            FROM user_location_assignments ula
            INNER JOIN users u ON ula.user_id = u.user_id
            WHERE ula.location_id = :location_id AND u.is_active = 1
        ");
        $this->db->bind(':location_id', $locationId);
        $this->db->execute();
        $staff_stats = $this->db->single();

        return (object) array_merge(
            (array) $inventory_stats,
            (array) $sales_stats,
            (array) $staff_stats
        );
    }

    /**
     * Get all staff for a location
     */
    public function getLocationStaff($locationId)
    {
        $this->db->query("
            SELECT u.*, r.role_name, ula.access_type, ula.assigned_at
            FROM users u
            INNER JOIN user_location_assignments ula ON u.user_id = ula.user_id
            LEFT JOIN roles r ON u.role_id = r.role_id
            WHERE ula.location_id = :location_id AND u.is_active = 1
            ORDER BY r.role_name, u.full_name
        ");
        $this->db->bind(':location_id', $locationId);
        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Transfer inventory between locations
     */
    public function transferInventory($fromLocationId, $toLocationId, $productId, $quantity, $notes = '')
    {
        $this->db->beginTransaction();
        
        try {
            // Check if source has enough inventory
            $this->db->query("
                SELECT SUM(quantity) as available 
                FROM inventory i
                INNER JOIN warehouse_locations wl ON i.location_id = wl.location_id
                WHERE wl.business_location_id = :location_id AND i.product_id = :product_id
            ");
            $this->db->bind(':location_id', $fromLocationId);
            $this->db->bind(':product_id', $productId);
            $this->db->execute();
            $available = $this->db->single()->available ?? 0;
            
            if ($available < $quantity) {
                throw new Exception("Insufficient inventory. Available: $available, Requested: $quantity");
            }
            
            // Create transfer record
            $this->db->query("
                INSERT INTO location_transfers 
                (from_location_id, to_location_id, product_id, quantity, notes, initiated_by, status)
                VALUES (:from_location, :to_location, :product_id, :quantity, :notes, :user_id, 'pending')
            ");
            $this->db->bind(':from_location', $fromLocationId);
            $this->db->bind(':to_location', $toLocationId);
            $this->db->bind(':product_id', $productId);
            $this->db->bind(':quantity', $quantity);
            $this->db->bind(':notes', $notes);
            $this->db->bind(':user_id', $_SESSION['user_id']);
            $this->db->execute();
            
            $transferId = $this->db->lastInsertId();
            
            $this->db->commit();
            return $transferId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Check if user has access to location
     */
    public function userHasLocationAccess($userId, $locationId)
    {
        $this->db->query("
            SELECT COUNT(*) as count
            FROM user_location_assignments
            WHERE user_id = :user_id AND location_id = :location_id
        ");
        $this->db->bind(':user_id', $userId);
        $this->db->bind(':location_id', $locationId);
        $this->db->execute();
        
        return $this->db->single()->count > 0;
    }
}
?>
