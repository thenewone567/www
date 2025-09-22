<?php

/**
 * Audit Trail Logging Trait
 * Provides comprehensive audit logging functionality for all controllers
 */
trait AuditTrail
{
    /**
     * Log audit trail for any controller action
     * @param string $action Action performed (CREATE, UPDATE, DELETE, etc.)
     * @param string $entity Entity type (Product, Sale, Purchase, etc.)
     * @param mixed $entityId Entity identifier
     * @param string $details Detailed description of changes
     * @param array $oldData Previous data state (optional)
     * @param array $newData New data state (optional)
     * @return bool
     */
    protected function logAudit($action, $entity, $entityId = null, $details = '', $oldData = null, $newData = null)
    {
        try {
            // Get current user information
            $userId = $_SESSION['user_id'] ?? 0;
            $userName = $_SESSION['user_name'] ?? 'System';
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? $_SERVER['HTTP_X_FORWARDED_FOR'] ?? 'unknown';

            // Enhanced details with before/after comparison
            if ($oldData && $newData && is_array($oldData) && is_array($newData)) {
                $changes = $this->getDataChanges($oldData, $newData);
                if (!empty($changes)) {
                    $details = $details ? $details . ' | ' . $changes : $changes;
                }
            }

            // Load User model if not already available
            if (!isset($this->userModel)) {
                require_once APPROOT . DS . 'app' . DS . 'models' . DS . 'User.php';
                $this->userModel = new User();
            }

            // Log the audit trail
            return $this->userModel->logAuditTrail(
                $userId,
                strtoupper($action),
                $entity,
                $entityId,
                $details,
                $ipAddress
            );

        } catch (Exception $e) {
            error_log("Audit logging failed: " . $e->getMessage());
            return false; // Don't fail the main operation
        }
    }

    /**
     * Compare old and new data to generate change description
     * @param array $oldData
     * @param array $newData  
     * @return string
     */
    private function getDataChanges($oldData, $newData)
    {
        $changes = [];
        $keysToCheck = array_unique(array_merge(array_keys($oldData), array_keys($newData)));

        foreach ($keysToCheck as $key) {
            $oldValue = $oldData[$key] ?? null;
            $newValue = $newData[$key] ?? null;

            // Skip certain fields that are not relevant for audit
            if (in_array($key, ['updated_at', 'created_at', 'password', 'password_hash'])) {
                continue;
            }

            if ($oldValue != $newValue) {
                $changes[] = "{$key}: " . ($oldValue ?? 'null') . " → " . ($newValue ?? 'null');
            }
        }

        return implode(', ', $changes);
    }

    /**
     * Log product-related activities
     */
    protected function logProductAudit($action, $productId, $details = '', $oldData = null, $newData = null)
    {
        return $this->logAudit($action, 'Product', $productId, $details, $oldData, $newData);
    }

    /**
     * Log sales-related activities  
     */
    protected function logSalesAudit($action, $saleId, $details = '', $oldData = null, $newData = null)
    {
        return $this->logAudit($action, 'Sale', $saleId, $details, $oldData, $newData);
    }

    /**
     * Log purchase-related activities
     */
    protected function logPurchaseAudit($action, $purchaseId, $details = '', $oldData = null, $newData = null)
    {
        return $this->logAudit($action, 'Purchase', $purchaseId, $details, $oldData, $newData);
    }

    /**
     * Log supplier-related activities
     */
    protected function logSupplierAudit($action, $supplierId, $details = '', $oldData = null, $newData = null)
    {
        return $this->logAudit($action, 'Supplier', $supplierId, $details, $oldData, $newData);
    }

    /**
     * Log return-related activities
     */
    protected function logReturnAudit($action, $returnId, $details = '', $oldData = null, $newData = null)
    {
        return $this->logAudit($action, 'Return', $returnId, $details, $oldData, $newData);
    }

    /**
     * Log inventory-related activities
     */
    protected function logInventoryAudit($action, $itemId, $details = '', $oldData = null, $newData = null)
    {
        return $this->logAudit($action, 'Inventory', $itemId, $details, $oldData, $newData);
    }

    /**
     * Log expense-related activities
     */
    protected function logExpenseAudit($action, $expenseId, $details = '', $oldData = null, $newData = null)
    {
        return $this->logAudit($action, 'Expense', $expenseId, $details, $oldData, $newData);
    }

    /**
     * Log user authentication activities
     */
    protected function logAuthAudit($action, $userId = null, $details = '')
    {
        return $this->logAudit($action, 'System', $userId, $details);
    }

    /**
     * Bulk audit logging for batch operations
     * @param string $action
     * @param string $entity
     * @param array $entityIds
     * @param string $details
     * @return bool
     */
    protected function logBulkAudit($action, $entity, $entityIds, $details = '')
    {
        if (is_array($entityIds) && count($entityIds) > 1) {
            $details = "Bulk operation on " . count($entityIds) . " items: " . $details;
            return $this->logAudit($action, $entity, implode(',', $entityIds), $details);
        } elseif (is_array($entityIds) && count($entityIds) == 1) {
            return $this->logAudit($action, $entity, $entityIds[0], $details);
        }
        return $this->logAudit($action, $entity, null, $details);
    }
}