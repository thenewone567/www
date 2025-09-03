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

        // Prefer status/deleted_at fields if present, otherwise fall back to is_active flag
        if ($this->hasColumn($table, 'status')) {
            $this->db->query("UPDATE $table 
                SET status = 'deleted',
                    deleted_at = NOW(),
                    deleted_by = :deleted_by
                WHERE $idField = :id");

            $this->db->bind(':id', $id);
            $this->db->bind(':deleted_by', $userId);

            $result = $this->db->execute();
        } else {
            // Fallback: set is_active = 0 if that column exists
            if ($this->hasColumn($table, 'is_active')) {
                $this->db->query("UPDATE $table SET is_active = 0 WHERE $idField = :id");
                $this->db->bind(':id', $id);
                $result = $this->db->execute();
            } else {
                // No recognizable soft-delete columns; abort and log
                error_log("SoftDelete: no status or is_active column found on table $table");
                return false;
            }
        }
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

        if ($this->hasColumn($table, 'status')) {
            $this->db->query("UPDATE $table 
                SET status = 'active',
                    deleted_at = NULL,
                    deleted_by = NULL
                WHERE $idField = :id");

            $this->db->bind(':id', $id);

            $result = $this->db->execute();
        } else {
            if ($this->hasColumn($table, 'is_active')) {
                $this->db->query("UPDATE $table SET is_active = 1 WHERE $idField = :id");
                $this->db->bind(':id', $id);
                $result = $this->db->execute();
            } else {
                error_log("RestoreDeleted: no status or is_active column found on table $table");
                return false;
            }
        }
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
        if ($this->hasColumn($table, 'status')) {
            $whereClause = "WHERE (status != 'deleted' OR status IS NULL)";
        } elseif ($this->hasColumn($table, 'is_active')) {
            $whereClause = "WHERE is_active = 1";
        } else {
            // No soft-delete columns, return all records
            $whereClause = "";
        }

        if (!empty($conditions)) {
            $whereClause .= ($whereClause ? ' AND (' : 'WHERE (') . $conditions . ')';
        }

        $orderBy = $this->hasColumn($table, 'created_at') ? 'ORDER BY created_at DESC' : '';

        $this->db->query("SELECT * FROM $table $whereClause $orderBy");
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
        if ($this->hasColumn($table, 'status')) {
            $where = "t.status = 'deleted'";
            $order = $this->hasColumn($table, 'deleted_at') ? 'ORDER BY t.deleted_at DESC' : '';
            $join = $this->hasColumn($table, 'deleted_by') ? 'LEFT JOIN users u ON t.deleted_by = u.user_id' : '';

            $this->db->query(
                "SELECT t.*, u.username as deleted_by_username, u.full_name as deleted_by_name FROM $table t $join WHERE $where $order"
            );
            $this->db->execute();
            return $this->db->resultSet();
        } elseif ($this->hasColumn($table, 'is_active')) {
            // If using is_active flag, consider records with is_active = 0 as deleted
            $join = $this->hasColumn($table, 'deleted_by') ? 'LEFT JOIN users u ON t.deleted_by = u.user_id' : '';
            $order = $this->hasColumn($table, 'deleted_at') ? 'ORDER BY t.deleted_at DESC' : '';
            $this->db->query("SELECT t.*, u.username as deleted_by_username, u.full_name as deleted_by_name FROM $table t $join WHERE t.is_active = 0 $order");
            $this->db->execute();
            return $this->db->resultSet();
        } else {
            return [];
        }
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
        if ($this->hasColumn($table, 'status')) {
            $this->db->query("SELECT status FROM $table WHERE $idField = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
            $record = $this->db->single();
            return $record && $record->status === 'deleted';
        } elseif ($this->hasColumn($table, 'is_active')) {
            $this->db->query("SELECT is_active FROM $table WHERE $idField = :id");
            $this->db->bind(':id', $id);
            $this->db->execute();
            $record = $this->db->single();
            return $record && intval($record->is_active) === 0;
        } else {
            return false;
        }
    }

    /**
     * Helper: check if a table has a given column
     */
    private function hasColumn($table, $column)
    {
        try {
            $this->db->query("SHOW COLUMNS FROM $table LIKE :col");
            $this->db->bind(':col', $column);
            $this->db->execute();
            $row = $this->db->single();
            return (bool) $row;
        } catch (Exception $e) {
            // If any error occurs assume the column does not exist
            return false;
        }
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