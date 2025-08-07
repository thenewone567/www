<?php

/**
 * Duplicate Validation Helper Trait
 * Provides standardized duplicate checking functionality for all models
 */
trait DuplicateValidation
{
    /**
     * Check if a field value already exists in the table
     * @param string $table The database table name
     * @param string $field The field name to check
     * @param mixed $value The value to check for duplicates
     * @param int|null $excludeId ID to exclude from check (for updates)
     * @return bool True if duplicate exists, false otherwise
     */
    public function isDuplicateExists($table, $field, $value, $excludeId = null)
    {
        if (empty($value)) {
            return false;
        }

        $sql = "SELECT id FROM {$table} WHERE {$field} = :{$field}";
        $bindings = [":{$field}" => $value];

        if ($excludeId) {
            $sql .= " AND id != :exclude_id";
            $bindings[':exclude_id'] = $excludeId;
        }

        $this->db->query($sql);
        foreach ($bindings as $param => $val) {
            $this->db->bind($param, $val);
        }
        $this->db->execute();

        return $this->db->rowCount() > 0;
    }

    /**
     * Check multiple fields for duplicates
     * @param string $table
     * @param array $fields Array of field=>value pairs
     * @param int|null $excludeId
     * @return array Array of duplicate field names
     */
    public function checkMultipleDuplicates($table, $fields, $excludeId = null)
    {
        $duplicates = [];

        foreach ($fields as $field => $value) {
            if ($this->isDuplicateExists($table, $field, $value, $excludeId)) {
                $duplicates[] = $field;
            }
        }

        return $duplicates;
    }

    /**
     * Generate duplicate validation errors
     * @param array $duplicateFields
     * @param array $fieldLabels Optional custom field labels
     * @return array
     */
    public function generateDuplicateErrors($duplicateFields, $fieldLabels = [])
    {
        $errors = [];

        foreach ($duplicateFields as $field) {
            $label = $fieldLabels[$field] ?? ucwords(str_replace('_', ' ', $field));
            $errors[$field . '_err'] = "{$label} already exists";
        }

        return $errors;
    }
}
