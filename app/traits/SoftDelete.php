<?php

/**
 * SoftDelete Trait
 * 
 * Provides soft delete functionality for models
 * Records are marked as deleted but never actually removed from database
 */
trait SoftDelete
{
    /**
     * Soft delete a record
     * 
     * @param int $id Record ID to delete
     * @param string $table Table name
     * @param string $idField Primary key field name
     * @return bool Success status
     */
    protected function softDelete($id, $table, $idField)
    {
        $userId = $_SESSION['user_id'] ?? null;

        $this->db->query("
            UPDATE $table 
            SET status = 'deleted',
                deleted_at = NOW(),
                deleted_by = :deleted_by
            WHERE $idField = :id
        ");

        $this->db->bind(':id', $id);
        $this->db->bind(':deleted_by', $userId);

        $result = $this->db->execute();

        // Log the soft delete action
        if ($result) {
            $this->logSoftDelete($table, $id, $userId);
        }

        return $result;
    }

    /**
     * Restore a soft deleted record
     * 
     * @param int $id Record ID to restore
     * @param string $table Table name  
     * @param string $idField Primary key field name
     * @return bool Success status
     */
    protected function restoreDeleted($id, $table, $idField)
    {
        $userId = $_SESSION['user_id'] ?? null;

        $this->db->query("
            UPDATE $table 
            SET status = 'active',
                deleted_at = NULL,
                deleted_by = NULL
            WHERE $idField = :id
        ");

        $this->db->bind(':id', $id);

        $result = $this->db->execute();

        // Log the restore action
        if ($result) {
            $this->logRestore($table, $id, $userId);
        }

        return $result;
    }

    /**
     * Get only non-deleted records
     * 
     * @param string $table Table name
     * @param string $conditions Additional WHERE conditions
     * @return array Records
     */
    protected function getActiveRecords($table, $conditions = '')
    {
        $whereClause = "WHERE (status != 'deleted' OR status IS NULL)";

        if (!empty($conditions)) {
            $whereClause .= " AND ($conditions)";
        }

        $this->db->query("SELECT * FROM $table $whereClause ORDER BY created_at DESC");
        $this->db->execute();

        return $this->db->resultSet();
    }

    /**
     * Get deleted records (for restore functionality)
     * 
     * @param string $table Table name
     * @return array Deleted records
     */
    protected function getDeletedRecords($table)
    {
        $this->db->query("
            SELECT *, 
                   u.username as deleted_by_username,
                   u.full_name as deleted_by_name
            FROM $table t
            LEFT JOIN users u ON t.deleted_by = u.user_id
            WHERE t.status = 'deleted' 
            ORDER BY t.deleted_at DESC
        ");

        $this->db->execute();
        return $this->db->resultSet();
    }

    /**
     * Check if record is deleted
     * 
     * @param int $id Record ID
     * @param string $table Table name
     * @param string $idField Primary key field name
     * @return bool True if deleted
     */
    protected function isDeleted($id, $table, $idField)
    {
        $this->db->query("
            SELECT status 
            FROM $table 
            WHERE $idField = :id
        ");

        $this->db->bind(':id', $id);
        $this->db->execute();

        $record = $this->db->single();

        return $record && $record->status === 'deleted';
    }

    /**
     * Log soft delete action for audit trail
     * 
     * @param string $table Table name
     * @param int $recordId Record ID
     * @param int $userId User ID who performed the action
     */
    private function logSoftDelete($table, $recordId, $userId)
    {
        try {
            $this->db->query("
                INSERT INTO audit_log (
                    table_name, 
                    record_id, 
                    action, 
                    performed_by, 
                    performed_at,
                    details
                ) VALUES (
                    :table_name,
                    :record_id, 
                    'SOFT_DELETE',
                    :performed_by,
                    NOW(),
                    :details
                )
            ");

            $this->db->bind(':table_name', $table);
            $this->db->bind(':record_id', $recordId);
            $this->db->bind(':performed_by', $userId);
            $this->db->bind(':details', "Record soft deleted from $table");

            $this->db->execute();
        } catch (Exception $e) {
            // Log audit failure but don't break the main operation
            error_log("Audit log failed for soft delete: " . $e->getMessage());
        }
    }

    /**
     * Log restore action for audit trail
     * 
     * @param string $table Table name
     * @param int $recordId Record ID
     * @param int $userId User ID who performed the action
     */
    private function logRestore($table, $recordId, $userId)
    {
        try {
            $this->db->query("
                INSERT INTO audit_log (
                    table_name, 
                    record_id, 
                    action, 
                    performed_by, 
                    performed_at,
                    details
                ) VALUES (
                    :table_name,
                    :record_id, 
                    'RESTORE',
                    :performed_by,
                    NOW(),
                    :details
                )
            ");

            $this->db->bind(':table_name', $table);
            $this->db->bind(':record_id', $recordId);
            $this->db->bind(':performed_by', $userId);
            $this->db->bind(':details', "Record restored from soft delete in $table");

            $this->db->execute();
        } catch (Exception $e) {
            // Log audit failure but don't break the main operation
            error_log("Audit log failed for restore: " . $e->getMessage());
        }
    }
}
?>