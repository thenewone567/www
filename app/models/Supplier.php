<?php
require_once APPROOT . DS . 'app' . DS . 'traits' . DS . 'SoftDelete.php';

class Supplier
{
    use SoftDelete;

    private $db;

    public function __construct()
    {
        $this->db = new Database;
    }

    // Enhanced getSuppliers method with performance data
    public function getSuppliers()
    {
        $this->db->query("
            SELECT s.*,
                   s.reliability_score as delivery_performance_score,
                   s.average_delivery_days as avg_delivery_days,
                   s.early_delivery_count as early_deliveries_count,
                   s.late_delivery_count as late_deliveries_count,
                   (s.total_deliveries - s.early_delivery_count - s.late_delivery_count) as on_time_deliveries_count,
                   s.total_deliveries as total_completed_orders,
                   s.last_evaluation_date as last_performance_update,
                   u.username AS added_by_username,
                   u.full_name AS added_by_full_name
            FROM suppliers s
            LEFT JOIN users u ON s.added_by = u.user_id
            WHERE (s.status != 'deleted' OR s.status IS NULL)
            ORDER BY s.reliability_score DESC, s.supplier_name ASC
        ");

        $this->db->execute();
        return $this->db->resultSet();
    }

    // Enhanced getSuppliers method with search, filter, and sorting capabilities
    public function getSuppliersFiltered($search = '', $status = '', $tier = '', $sortBy = 'supplier_name', $sortOrder = 'ASC', $limit = null, $offset = 0)
    {
        $whereConditions = ["(s.status != 'deleted' OR s.status IS NULL)"];
        $params = [];

        // Search functionality
        if (!empty($search)) {
            $whereConditions[] = "(
                LOWER(s.supplier_name) LIKE LOWER(:search) OR
                LOWER(s.contact_person) LIKE LOWER(:search) OR
                LOWER(s.email) LIKE LOWER(:search) OR
                s.phone LIKE :phone_search OR
                LOWER(s.address) LIKE LOWER(:search) OR
                LOWER(s.gst_number) LIKE LOWER(:search)
            )";
            $params[':search'] = '%' . $search . '%';
            $params[':phone_search'] = '%' . preg_replace('/[^0-9]/', '', $search) . '%';
        }

        // Status filter
        if (!empty($status) && $status !== 'all') {
            if ($status === 'active') {
                $whereConditions[] = "(s.status = 'active' OR s.status IS NULL)";
            } elseif ($status === 'inactive') {
                $whereConditions[] = "s.status = 'inactive'";
            }
        }

        // Tier filter
        if (!empty($tier) && $tier !== 'all') {
            if ($tier === 'gold_tier') {
                $whereConditions[] = "s.supplier_tier = 'Gold'";
            } elseif ($tier === 'silver_tier') {
                $whereConditions[] = "s.supplier_tier = 'Silver'";
            } elseif ($tier === 'bronze_tier') {
                $whereConditions[] = "(s.supplier_tier = 'Bronze' OR s.supplier_tier = 'Standard')";
            } elseif ($tier === 'poor_performance') {
                $whereConditions[] = "s.reliability_score < 70";
            }
        }

        // Valid sort columns mapping
        $validSortColumns = [
            'supplier_id' => 's.supplier_id',
            'supplier_name' => 's.supplier_name',
            'contact_person' => 's.contact_person',
            'email' => 's.email',
            'phone' => 's.phone',
            'reliability_score' => 's.reliability_score',
            'average_delivery_days' => 's.average_delivery_days',
            'supplier_tier' => 's.supplier_tier',
            'status' => 's.status',
            'created_at' => 's.created_at'
        ];

        $orderColumn = isset($validSortColumns[$sortBy]) ? $validSortColumns[$sortBy] : 's.supplier_name';
        $orderDirection = (strtoupper($sortOrder) === 'DESC') ? 'DESC' : 'ASC';

        $whereClause = implode(' AND ', $whereConditions);

        $sql = "
            SELECT s.*,
                   s.reliability_score as delivery_performance_score,
                   s.average_delivery_days as avg_delivery_days,
                   s.early_delivery_count as early_deliveries_count,
                   s.late_delivery_count as late_deliveries_count,
                   (s.total_deliveries - s.early_delivery_count - s.late_delivery_count) as on_time_deliveries_count,
                   s.total_deliveries as total_completed_orders,
                   s.last_evaluation_date as last_performance_update,
                   u.username AS added_by_username,
                   u.full_name AS added_by_full_name
            FROM suppliers s
            LEFT JOIN users u ON s.added_by = u.user_id
            WHERE {$whereClause}
            ORDER BY {$orderColumn} {$orderDirection}
        ";

        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
            $params[':limit'] = (int) $limit;
            $params[':offset'] = (int) $offset;
        }

        $this->db->query($sql);

        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        $this->db->execute();
        return $this->db->resultSet();
    }

    // Get total count for filtered suppliers (for pagination)
    public function getFilteredSuppliersCount($search = '', $status = '', $tier = '')
    {
        $whereConditions = ["(s.status != 'deleted' OR s.status IS NULL)"];
        $params = [];

        // Search functionality
        if (!empty($search)) {
            $whereConditions[] = "(
                LOWER(s.supplier_name) LIKE LOWER(:search) OR
                LOWER(s.contact_person) LIKE LOWER(:search) OR
                LOWER(s.email) LIKE LOWER(:search) OR
                s.phone LIKE :phone_search OR
                LOWER(s.address) LIKE LOWER(:search) OR
                LOWER(s.gst_number) LIKE LOWER(:search)
            )";
            $params[':search'] = '%' . $search . '%';
            $params[':phone_search'] = '%' . preg_replace('/[^0-9]/', '', $search) . '%';
        }

        // Status filter
        if (!empty($status) && $status !== 'all') {
            if ($status === 'active') {
                $whereConditions[] = "(s.status = 'active' OR s.status IS NULL)";
            } elseif ($status === 'inactive') {
                $whereConditions[] = "s.status = 'inactive'";
            }
        }

        // Tier filter
        if (!empty($tier) && $tier !== 'all') {
            if ($tier === 'gold_tier') {
                $whereConditions[] = "s.supplier_tier = 'Gold'";
            } elseif ($tier === 'silver_tier') {
                $whereConditions[] = "s.supplier_tier = 'Silver'";
            } elseif ($tier === 'bronze_tier') {
                $whereConditions[] = "(s.supplier_tier = 'Bronze' OR s.supplier_tier = 'Standard')";
            } elseif ($tier === 'poor_performance') {
                $whereConditions[] = "s.reliability_score < 70";
            }
        }

        $whereClause = implode(' AND ', $whereConditions);

        $this->db->query("
            SELECT COUNT(*) as total
            FROM suppliers s
            WHERE {$whereClause}
        ");

        foreach ($params as $key => $value) {
            $this->db->bind($key, $value);
        }

        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result->total : 0;
    }    // Get supplier analytics data
    public function getSupplierAnalytics()
    {
        $analytics = [];

        // Total suppliers count
        $this->db->query("SELECT COUNT(*) as total FROM suppliers");
        $this->db->execute();
        $result = $this->db->single();
        $analytics['total_suppliers'] = $result ? $result->total : 0;

        // Active suppliers count
        $this->db->query("SELECT COUNT(*) as active FROM suppliers WHERE status = 'active' OR status IS NULL");
        $this->db->execute();
        $result = $this->db->single();
        $analytics['active_suppliers'] = $result ? $result->active : 0;

        // Average delivery days across all suppliers
        $this->db->query("
            SELECT AVG(average_delivery_days) as avg_delivery 
            FROM suppliers 
            WHERE average_delivery_days > 0 AND average_delivery_days IS NOT NULL
        ");
        $this->db->execute();
        $result = $this->db->single();
        $analytics['avg_delivery_days'] = $result ? $result->avg_delivery : 0;

        // Average on-time rate
        $this->db->query("
            SELECT AVG(on_time_delivery_rate) as avg_on_time_rate
            FROM suppliers 
            WHERE total_deliveries > 0 AND on_time_delivery_rate IS NOT NULL
        ");
        $this->db->execute();
        $result = $this->db->single();
        $analytics['avg_on_time_rate'] = $result ? $result->avg_on_time_rate : 0;

        // Gold tier suppliers
        $this->db->query("
            SELECT COUNT(*) as gold_tier 
            FROM suppliers 
            WHERE supplier_tier = 'Gold'
        ");
        $this->db->execute();
        $result = $this->db->single();
        $analytics['gold_tier_suppliers'] = $result ? $result->gold_tier : 0;

        // Total order value
        $this->db->query("
            SELECT COALESCE(SUM(total_order_value), 0) as total_value
            FROM suppliers
            WHERE total_order_value > 0 AND total_order_value IS NOT NULL
        ");
        $this->db->execute();
        $result = $this->db->single();
        $analytics['total_order_value'] = $result ? $result->total_value : 0;

        return $analytics;
    }

    // Get delivery statistics for charts
    public function getDeliveryStats()
    {
        $this->db->query("
            SELECT 
                SUM(total_deliveries - early_delivery_count - late_delivery_count) as on_time,
                SUM(early_delivery_count) as early,
                SUM(late_delivery_count) as late
            FROM suppliers 
            WHERE total_deliveries > 0
        ");

        $this->db->execute();
        $result = $this->db->single();
        return [
            'on_time' => $result ? $result->on_time : 0,
            'early' => $result ? $result->early : 0,
            'late' => $result ? $result->late : 0
        ];
    }

    // Get tier distribution statistics
    public function getTierStats()
    {
        $this->db->query("
            SELECT 
                SUM(CASE WHEN supplier_tier = 'Gold' THEN 1 ELSE 0 END) as gold,
                SUM(CASE WHEN supplier_tier = 'Silver' THEN 1 ELSE 0 END) as silver,
                SUM(CASE WHEN supplier_tier = 'Bronze' OR supplier_tier = 'Standard' THEN 1 ELSE 0 END) as bronze
            FROM suppliers
        ");
        $this->db->execute();
        $result = $this->db->single();
        return [
            'gold' => $result ? $result->gold : 0,
            'silver' => $result ? $result->silver : 0,
            'bronze' => $result ? $result->bronze : 0
        ];
    }

    // Get top performing suppliers
    public function getTopPerformers($limit = 5)
    {
        $this->db->query("
            SELECT s.*,
                   s.on_time_delivery_rate
            FROM suppliers s
            WHERE s.total_deliveries >= 3
            ORDER BY s.reliability_score DESC, s.on_time_delivery_rate DESC
            LIMIT :limit
        ");

        $this->db->bind(':limit', $limit);
        $this->db->execute();
        return $this->db->resultSet();
    }

    // Get poor performing suppliers that need attention
    public function getPoorPerformers($limit = 5)
    {
        $this->db->query("
            SELECT s.*,
                   s.on_time_delivery_rate
            FROM suppliers s
            WHERE s.total_deliveries >= 1 
              AND (s.reliability_score < 7.0 
                   OR s.on_time_delivery_rate < 60)
            ORDER BY s.reliability_score ASC, s.on_time_delivery_rate ASC
            LIMIT :limit
        ");

        $this->db->bind(':limit', $limit);
        $this->db->execute();
        return $this->db->resultSet();
    }

    // Get recent deliveries for dashboard (using delivery_tracking table)
    public function getRecentDeliveries($limit = 10)
    {
        $this->db->query("
            SELECT dt.*, s.supplier_name,
                   dt.days_early_late,
                   dt.actual_delivery_date
            FROM delivery_tracking dt
            JOIN suppliers s ON dt.supplier_id = s.supplier_id
            WHERE dt.actual_delivery_date IS NOT NULL
            ORDER BY dt.actual_delivery_date DESC
            LIMIT :limit
        ");

        $this->db->bind(':limit', $limit);
        $this->db->execute();
        return $this->db->resultSet();
    }

    // Update supplier performance metrics manually
    public function updateSupplierPerformance($supplier_id)
    {
        // Get delivery data from delivery_tracking table
        $this->db->query("
            SELECT 
                COUNT(*) as total_deliveries,
                SUM(CASE WHEN days_early_late = 0 THEN 1 ELSE 0 END) as on_time_count,
                SUM(CASE WHEN days_early_late < 0 THEN 1 ELSE 0 END) as early_count,
                SUM(CASE WHEN days_early_late > 0 THEN 1 ELSE 0 END) as late_count,
                AVG(ABS(days_early_late)) as avg_delivery_variance
            FROM delivery_tracking 
            WHERE supplier_id = :supplier_id 
              AND actual_delivery_date IS NOT NULL
              AND delivery_status = 'delivered'
        ");

        $this->db->bind(':supplier_id', $supplier_id);
        $this->db->execute();
        $delivery_data = $this->db->single();

        if ($delivery_data && $delivery_data->total_deliveries > 0) {
            // Calculate on-time delivery rate
            $on_time_rate = ($delivery_data->on_time_count / $delivery_data->total_deliveries) * 100;

            // Calculate reliability score (0-10 scale)
            $reliability_score = 5.0; // Default score
            if ($delivery_data->total_deliveries >= 3) {
                // Base score on on-time percentage
                $reliability_score = ($on_time_rate / 10) + 1; // Scale to 1-11, then cap at 10

                // Bonus for early deliveries
                $early_bonus = ($delivery_data->early_count / $delivery_data->total_deliveries) * 2;

                // Penalty for late deliveries
                $late_penalty = ($delivery_data->late_count / $delivery_data->total_deliveries) * 3;

                $reliability_score = min(10.0, max(1.0, $reliability_score + $early_bonus - $late_penalty));
            }

            // Update supplier record
            $this->db->query("
                UPDATE suppliers 
                SET 
                    total_deliveries = :total_deliveries,
                    early_delivery_count = :early_count,
                    late_delivery_count = :late_count,
                    on_time_delivery_rate = :on_time_rate,
                    average_delivery_days = :avg_variance,
                    reliability_score = :reliability_score,
                    last_evaluation_date = CURDATE()
                WHERE supplier_id = :supplier_id
            ");

            $this->db->bind(':supplier_id', $supplier_id);
            $this->db->bind(':total_deliveries', $delivery_data->total_deliveries);
            $this->db->bind(':early_count', $delivery_data->early_count);
            $this->db->bind(':late_count', $delivery_data->late_count);
            $this->db->bind(':on_time_rate', $on_time_rate);
            $this->db->bind(':avg_variance', $delivery_data->avg_delivery_variance);
            $this->db->bind(':reliability_score', $reliability_score);

            $this->db->execute();

            // Update supplier tier based on performance
            $this->updateSupplierTier($supplier_id, $reliability_score, $on_time_rate);
        }

        return true;
    }

    // Update supplier tier based on performance metrics
    private function updateSupplierTier($supplier_id, $reliability_score, $on_time_rate)
    {
        $tier = 'Standard';

        if ($reliability_score >= 9.0 && $on_time_rate >= 95) {
            $tier = 'Gold';
        } elseif ($reliability_score >= 7.5 && $on_time_rate >= 85) {
            $tier = 'Silver';
        } elseif ($reliability_score >= 6.0 && $on_time_rate >= 70) {
            $tier = 'Bronze';
        }

        $this->db->query("UPDATE suppliers SET supplier_tier = :tier WHERE supplier_id = :supplier_id");
        $this->db->bind(':tier', $tier);
        $this->db->bind(':supplier_id', $supplier_id);

        return $this->db->execute();
    }

    // Refresh all suppliers' performance data
    public function refreshAllPerformanceData()
    {
        $this->db->query("SELECT supplier_id FROM suppliers WHERE status = 'active'");
        $this->db->execute();
        $suppliers = $this->db->resultSet();

        foreach ($suppliers as $supplier) {
            $this->updateSupplierPerformance($supplier->supplier_id);
        }

        return true;
    }

    public function addSupplier($data)
    {
        $this->db->query("INSERT INTO suppliers (supplier_name, contact_person, phone, email, address, gst_number, default_delivery_days, added_by, status) VALUES (:supplier_name, :contact_person, :phone, :email, :address, :gst_number, :default_delivery_days, :added_by, :status)");
        // Bind values
        $this->db->bind(':supplier_name', $data['supplier_name']);
        $this->db->bind(':contact_person', $data['contact_person']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':gst_number', $data['gst_number']);
        $this->db->bind(':default_delivery_days', $data['default_delivery_days']);
        $this->db->bind(':added_by', $data['added_by']);
        $this->db->bind(':status', 'active');
        // Execute
        return $this->db->execute();
    }

    public function getSupplierById($id)
    {
        $this->db->query("
            SELECT s.*, 
                   u.username AS added_by_username, 
                   u.full_name AS added_by_full_name,
                   u2.username AS updated_by_username,
                   u2.full_name AS updated_by_full_name,
                   s.created_at, 
                   s.updated_at 
            FROM suppliers s 
            LEFT JOIN users u ON s.added_by = u.user_id 
            LEFT JOIN users u2 ON s.updated_by = u2.user_id 
            WHERE s.supplier_id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result : null;
    }

    public function updateSupplier($data)
    {
        // First, get the current supplier data to compare changes
        $currentSupplier = $this->getSupplierById($data['id']);
        if (!$currentSupplier) {
            return false;
        }

        // Track which fields are being changed
        $changedFields = [];
        $fieldMapping = [
            'supplier_name' => 'supplier_name',
            'contact_person' => 'contact_person',
            'phone' => 'phone',
            'email' => 'email',
            'address' => 'address',
            'gst_number' => 'gst_number',
            'default_delivery_days' => 'default_delivery_days',
            'preferred_payment_terms' => 'preferred_payment_terms',
            'credit_limit' => 'credit_limit',
            'current_outstanding' => 'current_outstanding',
            'is_verified' => 'is_verified',
            'verification_date' => 'verification_date',
            'notes' => 'notes'
        ];

        foreach ($fieldMapping as $formField => $dbField) {
            if (isset($data[$formField]) && $data[$formField] !== $currentSupplier->$dbField) {
                $changedFields[] = [
                    'field' => $dbField,
                    'old_value' => $currentSupplier->$dbField,
                    'new_value' => $data[$formField]
                ];
            }
        }

        // Only update if there are actual changes
        if (empty($changedFields)) {
            return true; // No changes to make
        }

        $this->db->query("
            UPDATE suppliers 
            SET supplier_name = :supplier_name, 
                contact_person = :contact_person, 
                phone = :phone, 
                email = :email, 
                address = :address, 
                gst_number = :gst_number,
                default_delivery_days = :default_delivery_days,
                preferred_payment_terms = :preferred_payment_terms,
                credit_limit = :credit_limit,
                current_outstanding = :current_outstanding,
                is_verified = :is_verified,
                verification_date = :verification_date,
                notes = :notes,
                updated_by = :updated_by,
                updated_at = NOW()
            WHERE supplier_id = :id
        ");

        // Bind values
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':supplier_name', $data['supplier_name']);
        $this->db->bind(':contact_person', $data['contact_person']);
        $this->db->bind(':phone', $data['phone']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':address', $data['address']);
        $this->db->bind(':gst_number', $data['gst_number']);
        $this->db->bind(':default_delivery_days', $data['default_delivery_days'] ?? 7);
        $this->db->bind(':preferred_payment_terms', $data['preferred_payment_terms'] ?? 'Net 30');
        $this->db->bind(':credit_limit', $data['credit_limit'] ?? 0);
        $this->db->bind(':current_outstanding', $data['current_outstanding'] ?? 0);
        $this->db->bind(':is_verified', $data['is_verified'] ?? 0);
        $this->db->bind(':verification_date', !empty($data['verification_date']) ? $data['verification_date'] : null);
        $this->db->bind(':notes', $data['notes'] ?? '');
        $this->db->bind(':updated_by', $data['updated_by'] ?? $_SESSION['user_id'] ?? null);

        // Execute the update
        $result = $this->db->execute();

        // If successful, log the individual field changes
        if ($result) {
            $userId = $data['updated_by'] ?? $_SESSION['user_id'] ?? null;
            foreach ($changedFields as $change) {
                $this->logFieldChange($data['id'], $change['field'], $change['old_value'], $change['new_value'], $userId);
            }
        }

        return $result;
    }

    /**
     * Log individual field changes to audit table
     */
    private function logFieldChange($supplierId, $fieldName, $oldValue, $newValue, $userId)
    {
        $this->db->query("
            INSERT INTO supplier_audit (supplier_id, field_name, old_value, new_value, updated_by, updated_at)
            VALUES (:supplier_id, :field_name, :old_value, :new_value, :updated_by, NOW())
        ");

        $this->db->bind(':supplier_id', $supplierId);
        $this->db->bind(':field_name', $fieldName);
        $this->db->bind(':old_value', $oldValue);
        $this->db->bind(':new_value', $newValue);
        $this->db->bind(':updated_by', $userId);

        return $this->db->execute();
    }

    /**
     * Get the last update info for a specific field
     */
    public function getFieldLastUpdate($supplierId, $fieldName)
    {
        $this->db->query("
            SELECT sa.updated_at, sa.updated_by, u.full_name as updated_by_name
            FROM supplier_audit sa
            LEFT JOIN users u ON sa.updated_by = u.user_id
            WHERE sa.supplier_id = :supplier_id AND sa.field_name = :field_name
            ORDER BY sa.updated_at DESC
            LIMIT 1
        ");

        $this->db->bind(':supplier_id', $supplierId);
        $this->db->bind(':field_name', $fieldName);
        $this->db->execute();

        return $this->db->single();
    }

    public function deleteSupplier($id)
    {
        // Use soft delete instead of hard delete
        return $this->softDelete($id, 'suppliers', 'supplier_id');
    }

    /**
     * Restore a soft deleted supplier
     */
    public function restoreSupplier($id)
    {
        return $this->restoreDeleted($id, 'suppliers', 'supplier_id');
    }

    /**
     * Get deleted suppliers for admin recovery
     */
    public function getDeletedSuppliers()
    {
        return $this->getDeletedRecords('suppliers');
    }

    /**
     * Set supplier status (active/inactive)
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function setSupplierStatus($id, $status)
    {
        // Get current status to track the change
        $currentSupplier = $this->getSupplierById($id);
        if (!$currentSupplier) {
            return false;
        }

        $oldStatus = $currentSupplier->status;

        $this->db->query("
            UPDATE suppliers 
            SET status = :status, 
                updated_by = :updated_by, 
                updated_at = NOW() 
            WHERE supplier_id = :id
        ");
        $this->db->bind(':id', $id);
        $this->db->bind(':status', $status);
        $this->db->bind(':updated_by', $_SESSION['user_id'] ?? null);

        $result = $this->db->execute();

        // Log the status change if successful and different
        if ($result && $oldStatus !== $status) {
            $this->logFieldChange($id, 'status', $oldStatus, $status, $_SESSION['user_id'] ?? null);
        }

        return $result;
    }

    /**
     * Check if supplier name already exists
     * @param string $supplierName
     * @param int $excludeId (optional) - exclude this ID from check (for edit)
     * @return bool
     */
    public function isSupplierNameExists($supplierName, $excludeId = null)
    {
        if ($excludeId) {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE supplier_name = :supplier_name AND supplier_id != :exclude_id");
            $this->db->bind(':exclude_id', $excludeId);
        } else {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE supplier_name = :supplier_name");
        }
        $this->db->bind(':supplier_name', $supplierName);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Check if GST number already exists
     * @param string $gstNumber
     * @param int $excludeId (optional) - exclude this ID from check (for edit)
     * @return bool
     */
    public function isGstNumberExists($gstNumber, $excludeId = null)
    {
        if (empty($gstNumber)) {
            return false; // GST number is optional, so empty is allowed
        }

        if ($excludeId) {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE gst_number = :gst_number AND supplier_id != :exclude_id");
            $this->db->bind(':exclude_id', $excludeId);
        } else {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE gst_number = :gst_number");
        }
        $this->db->bind(':gst_number', $gstNumber);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Check if email already exists
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function isEmailExists($email, $excludeId = null)
    {
        if (empty($email)) {
            return false;
        }

        if ($excludeId) {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE email = :email AND supplier_id != :exclude_id");
            $this->db->bind(':exclude_id', $excludeId);
        } else {
            $this->db->query("SELECT supplier_id FROM suppliers WHERE email = :email");
        }
        $this->db->bind(':email', $email);
        $this->db->execute();
        return $this->db->rowCount() > 0;
    }

    /**
     * Get supplier purchase history
     * @param int $supplierId
     * @param int $limit
     * @return array
     */
    public function getSupplierPurchases($supplierId, $limit = 50)
    {
        $this->db->query("
            SELECT p.*, 
                   COUNT(pi.purchase_item_id) as item_count
            FROM purchases p
            LEFT JOIN purchase_items pi ON p.purchase_id = pi.purchase_id
            WHERE p.supplier_id = :supplier_id
            GROUP BY p.purchase_id
            ORDER BY p.purchase_date DESC
            LIMIT :limit
        ");
        $this->db->bind(':supplier_id', $supplierId);
        $this->db->bind(':limit', $limit);
        return $this->db->resultSet();
    }

    /**
     * Get supplier statistics
     * @param int $supplierId
     * @return object
     */
    public function getSupplierStats($supplierId)
    {
        $this->db->query("
            SELECT 
                COUNT(p.purchase_id) as total_orders,
                COALESCE(SUM(p.total_amount), 0) as total_purchased,
                COALESCE(AVG(p.total_amount), 0) as average_order,
                MAX(p.purchase_date) as last_order_date,
                MIN(p.purchase_date) as first_order_date
            FROM purchases p
            WHERE p.supplier_id = :supplier_id
        ");
        $this->db->bind(':supplier_id', $supplierId);
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Update supplier due amount
     * @param int $supplierId
     * @param float $amount
     * @return bool
     */
    public function updateDueAmount($supplierId, $amount)
    {
        $this->db->query("UPDATE suppliers SET due_amount = :amount WHERE supplier_id = :supplier_id");
        $this->db->bind(':amount', $amount);
        $this->db->bind(':supplier_id', $supplierId);
        return $this->db->execute();
    }

    /**
     * Add supplier payment
     * @param int $supplierId
     * @param float $amount
     * @param string $notes
     * @return bool
     */
    public function addSupplierPayment($supplierId, $amount, $notes = '')
    {
        try {
            // Record payment
            $this->db->query("
                INSERT INTO supplier_payments 
                (supplier_id, amount, payment_date, notes)
                VALUES (:supplier_id, :amount, NOW(), :notes)
            ");
            $this->db->bind(':supplier_id', $supplierId);
            $this->db->bind(':amount', $amount);
            $this->db->bind(':notes', $notes);
            $this->db->execute();

            // Update due amount
            $this->db->query("
                UPDATE suppliers 
                SET due_amount = due_amount - :amount 
                WHERE supplier_id = :supplier_id
            ");
            $this->db->bind(':amount', $amount);
            $this->db->bind(':supplier_id', $supplierId);
            return $this->db->execute();
        } catch (Exception $e) {
            error_log("Error adding supplier payment: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get suppliers with outstanding dues
     * @return array
     */
    public function getSuppliersWithDues()
    {
        $this->db->query("
            SELECT * FROM suppliers 
            WHERE due_amount > 0 
            ORDER BY due_amount DESC
        ");
        return $this->db->resultSet();
    }

    /**
     * Get supplier evaluation metrics
     * @param int $supplierId
     * @return object
     */
    public function getSupplierEvaluation($supplierId)
    {
        $this->db->query("
            SELECT 
                COUNT(po.purchase_order_id) as total_orders,
                AVG(DATEDIFF(po.updated_at, po.created_at)) as avg_delivery_days,
                COUNT(CASE WHEN po.status = 'received' THEN 1 END) as completed_orders,
                COUNT(CASE WHEN po.status = 'cancelled' THEN 1 END) as cancelled_orders,
                (COUNT(CASE WHEN po.status = 'received' THEN 1 END) / COUNT(po.purchase_order_id) * 100) as success_rate
            FROM suppliers s
            LEFT JOIN purchase_orders po ON s.supplier_id = po.supplier_id
            WHERE s.supplier_id = :supplier_id
            GROUP BY s.supplier_id
        ");
        $this->db->bind(':supplier_id', $supplierId);
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Get active supplier count (suppliers with active status)
     * @return int
     */
    public function getActiveSupplierCount()
    {
        $this->db->query("SELECT COUNT(*) as count FROM suppliers WHERE status = 'active'");
        $this->db->execute();
        $result = $this->db->single();
        return $result ? (int) $result->count : 0;
    }

    /**
     * Get supplier overview statistics for dashboard cards
     */
    public function getSupplierOverviewStats()
    {
        // Get total suppliers
        $this->db->query("SELECT COUNT(*) as total FROM suppliers");
        $this->db->execute();
        $total_result = $this->db->single();
        $total = $total_result ? $total_result->total : 0;

        // Get suppliers with recent activity (active - have orders in last 30 days)
        $this->db->query("
            SELECT COUNT(DISTINCT s.supplier_id) as active 
            FROM suppliers s 
            INNER JOIN purchases p ON s.supplier_id = p.supplier_id 
            WHERE p.purchase_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $this->db->execute();
        $active_result = $this->db->single();
        $active = $active_result ? $active_result->active : 0;

        // Get suppliers with pending orders (recent activity in last 7 days)
        $this->db->query("
            SELECT COUNT(DISTINCT s.supplier_id) as pending 
            FROM suppliers s 
            INNER JOIN purchases p ON s.supplier_id = p.supplier_id 
            WHERE p.purchase_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $this->db->execute();
        $pending_result = $this->db->single();
        $pending = $pending_result ? $pending_result->pending : 0;

        // Get suppliers with no recent activity (on hold - no orders in last 90 days)
        $this->db->query("
            SELECT COUNT(*) as onhold 
            FROM suppliers s 
            WHERE s.supplier_id NOT IN (
                SELECT DISTINCT p.supplier_id 
                FROM purchases p 
                WHERE p.purchase_date >= DATE_SUB(NOW(), INTERVAL 90 DAY)
                AND p.supplier_id IS NOT NULL
            )
        ");
        $this->db->execute();
        $onhold_result = $this->db->single();
        $onhold = $onhold_result ? $onhold_result->onhold : 0;

        return [
            'total' => $total,
            'active' => $active,
            'pending' => $pending,
            'onhold' => $onhold
        ];
    }

    /**
     * Get all active suppliers for dropdowns
     * @return array
     */
    public function getActiveSuppliers()
    {
        $this->db->query("
            SELECT supplier_id, supplier_name, email
            FROM suppliers 
            WHERE status = 'active'
            ORDER BY supplier_name ASC
        ");
        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Find supplier by name (case-insensitive)
     * @param string $name
     * @return object|null
     */
    public function getSupplierByName($name)
    {
        $this->db->query("
            SELECT * FROM suppliers 
            WHERE LOWER(supplier_name) = LOWER(:name)
            LIMIT 1
        ");
        $this->db->bind(':name', trim($name));
        $this->db->execute();
        return $this->db->single();
    }

    /**
     * Search suppliers by name or contact information
     * @param string $query
     * @param int $limit
     * @return array
     */
    public function searchSuppliers($query, $limit = 10)
    {
        $this->db->query("
            SELECT 
                supplier_id,
                supplier_name,
                contact_person,
                phone,
                email,
                status,
                default_delivery_days
            FROM suppliers 
            WHERE status = 'active'
              AND (
                  LOWER(supplier_name) LIKE LOWER(:query)
                  OR LOWER(contact_person) LIKE LOWER(:query)
                  OR LOWER(email) LIKE LOWER(:query)
                  OR phone LIKE :phone_query
              )
            ORDER BY 
                CASE WHEN LOWER(supplier_name) LIKE LOWER(:exact_query) THEN 1 ELSE 2 END,
                supplier_name ASC
            LIMIT :limit
        ");

        $searchQuery = '%' . $query . '%';
        $exactQuery = $query . '%';
        $phoneQuery = '%' . preg_replace('/[^0-9]/', '', $query) . '%';

        $this->db->bind(':query', $searchQuery);
        $this->db->bind(':exact_query', $exactQuery);
        $this->db->bind(':phone_query', $phoneQuery);
        $this->db->bind(':limit', $limit);

        $this->db->execute();
        return $this->db->resultSet();
    }

    // Get products linked to a supplier
    public function getLinkedProducts($supplierId)
    {
        $this->db->query("
            SELECT 
                p.product_id,
                p.product_name,
                p.sku,
                ps.purchase_price,
                ps.supplier_sku,
                ps.lead_time_days,
                ps.min_order_quantity,
                ps.notes as supplier_notes,
                ps.quality_rating as supplier_rating,
                ps.created_at as linked_date
            FROM product_suppliers ps
            INNER JOIN products p ON ps.product_id = p.product_id
            WHERE ps.supplier_id = :supplier_id 
            AND ps.is_active = 1
            AND p.deleted_at IS NULL
            ORDER BY p.product_name ASC
        ");

        $this->db->bind(':supplier_id', $supplierId);
        $this->db->execute();

        return $this->db->resultSet();
    }    // Check if a product is already linked to a supplier
    public function isProductLinked($supplierId, $productId)
    {
        $this->db->query("
            SELECT COUNT(*) as count 
            FROM product_suppliers 
            WHERE supplier_id = :supplier_id 
            AND product_id = :product_id 
            AND is_active = 1
        ");

        $this->db->bind(':supplier_id', $supplierId);
        $this->db->bind(':product_id', $productId);
        $this->db->execute();

        $result = $this->db->single();
        return $result && $result->count > 0;
    }

    // Link a product to a supplier
    public function linkProduct($data)
    {
        $this->db->query("
            INSERT INTO product_suppliers (
                supplier_id, 
                product_id, 
                purchase_price, 
                supplier_sku, 
                lead_time_days, 
                min_order_quantity, 
                notes, 
                quality_rating, 
                is_active
            ) VALUES (
                :supplier_id, 
                :product_id, 
                :purchase_price, 
                :supplier_sku, 
                :lead_time_days, 
                :min_order_quantity, 
                :notes, 
                :quality_rating, 
                :is_active
            )
        ");

        // Bind parameters
        $this->db->bind(':supplier_id', $data['supplier_id']);
        $this->db->bind(':product_id', $data['product_id']);
        $this->db->bind(':purchase_price', $data['purchase_price']);
        $this->db->bind(':supplier_sku', $data['supplier_sku']);
        $this->db->bind(':lead_time_days', $data['lead_time_days']);
        $this->db->bind(':min_order_quantity', $data['min_order_quantity']);
        $this->db->bind(':notes', $data['supplier_notes']);
        $this->db->bind(':quality_rating', $data['supplier_rating']);
        $this->db->bind(':is_active', $data['is_active']);

        try {
            $result = $this->db->execute();
            if (!$result) {
                error_log('Database execute() returned false in linkProduct');
                if (method_exists($this->db, 'getLastError')) {
                    error_log('Database error: ' . $this->db->getLastError());
                }
            }
            return $result;
        } catch (Exception $e) {
            error_log('Exception in linkProduct: ' . $e->getMessage());
            throw $e;
        }
    }

    // Unlink a product from a supplier
    public function unlinkProduct($supplierId, $productId)
    {
        $this->db->query("
            DELETE FROM product_suppliers 
            WHERE supplier_id = :supplier_id 
            AND product_id = :product_id
        ");

        $this->db->bind(':supplier_id', $supplierId);
        $this->db->bind(':product_id', $productId);

        return $this->db->execute();
    }
}
