<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "<h3>Database Migration: Adding Dock Columns to Purchases Table</h3>";

    // First check what columns currently exist
    echo "<h4>Current Table Structure:</h4>";
    $db->query("DESCRIBE purchases");
    $db->execute();
    $columns = $db->resultSet();

    $existingColumns = array_column($columns, 'Field');

    echo "<ul>";
    foreach ($existingColumns as $column) {
        echo "<li>{$column}</li>";
    }
    echo "</ul>";

    // Check which dock columns are missing
    $dockColumns = [
        'dock_location_id' => 'INT NULL',
        'dock_assignment_notes' => 'TEXT NULL',
        'dock_arrival_time' => 'DATETIME NULL'
    ];

    $columnsToAdd = [];

    echo "<h4>Dock Columns Status:</h4>";
    foreach ($dockColumns as $columnName => $columnDef) {
        if (in_array($columnName, $existingColumns)) {
            echo "<p style='color: green;'>✓ {$columnName} already exists</p>";
        } else {
            echo "<p style='color: orange;'>⚠ {$columnName} needs to be added</p>";
            $columnsToAdd[$columnName] = $columnDef;
        }
    }

    // Add missing columns
    if (!empty($columnsToAdd)) {
        echo "<h4>Adding Missing Columns:</h4>";

        foreach ($columnsToAdd as $columnName => $columnDef) {
            try {
                $sql = "ALTER TABLE purchases ADD COLUMN {$columnName} {$columnDef}";
                echo "<p>Executing: {$sql}</p>";

                $db->query($sql);
                $result = $db->execute();

                if ($result) {
                    echo "<p style='color: green;'>✓ Successfully added {$columnName}</p>";
                } else {
                    echo "<p style='color: red;'>✗ Failed to add {$columnName}</p>";
                }

            } catch (Exception $e) {
                echo "<p style='color: red;'>Error adding {$columnName}: " . $e->getMessage() . "</p>";
            }
        }

        echo "<h4>Updated Table Structure:</h4>";
        $db->query("DESCRIBE purchases");
        $db->execute();
        $newColumns = $db->resultSet();

        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th style='padding: 8px;'>Field</th>";
        echo "<th style='padding: 8px;'>Type</th>";
        echo "<th style='padding: 8px;'>Null</th>";
        echo "<th style='padding: 8px;'>Default</th>";
        echo "</tr>";

        foreach ($newColumns as $column) {
            $isNew = in_array($column->Field, array_keys($columnsToAdd));
            $rowStyle = $isNew ? "background-color: #d4edda;" : "";

            echo "<tr style='{$rowStyle}'>";
            echo "<td style='padding: 8px;'>{$column->Field}</td>";
            echo "<td style='padding: 8px;'>{$column->Type}</td>";
            echo "<td style='padding: 8px;'>{$column->Null}</td>";
            echo "<td style='padding: 8px;'>{$column->Default}</td>";
            echo "</tr>";
        }
        echo "</table>";

    } else {
        echo "<h4>✓ All required columns already exist!</h4>";
    }

    // Test the update after adding columns
    echo "<h4>Testing Update After Migration:</h4>";

    // Find or create a test PO
    $db->query("SELECT purchase_id FROM purchases WHERE po_number = ? LIMIT 1");
    $db->bind(1, 'PO-250905-140201');
    $db->execute();
    $testPO = $db->single();

    if (!$testPO) {
        // Create test PO
        $db->query("INSERT INTO purchases (po_number, supplier_name, purchase_date, status, total_amount, created_at, updated_at) VALUES (?, ?, NOW(), ?, ?, NOW(), NOW())");
        $db->bind(1, 'PO-250905-140201');
        $db->bind(2, 'Test Supplier');
        $db->bind(3, 'pending');
        $db->bind(4, 1000.00);
        $db->execute();

        $db->query("SELECT purchase_id FROM purchases WHERE po_number = ?");
        $db->bind(1, 'PO-250905-140201');
        $db->execute();
        $testPO = $db->single();
    }

    if ($testPO) {
        try {
            $db->query('
                UPDATE purchases 
                SET status = ?, 
                    dock_arrival_time = NOW(),
                    updated_at = NOW(),
                    dock_location_id = ?,
                    dock_assignment_notes = ?
                WHERE purchase_id = ?
            ');
            $db->bind(1, 'arrived_at_facility');
            $db->bind(2, 18);
            $db->bind(3, 'Test migration update');
            $db->bind(4, $testPO->purchase_id);

            $result = $db->execute();

            if ($result) {
                echo "<p style='color: green;'>✓ Test update successful! The markAsArrivedAtFacility method should now work.</p>";

                // Show the updated record
                $db->query("SELECT * FROM purchases WHERE purchase_id = ?");
                $db->bind(1, $testPO->purchase_id);
                $db->execute();
                $updated = $db->single();

                echo "<h5>Updated Record:</h5>";
                echo "<p>Status: {$updated->status}</p>";
                echo "<p>Dock Location ID: " . ($updated->dock_location_id ?? 'NULL') . "</p>";
                echo "<p>Dock Notes: " . ($updated->dock_assignment_notes ?? 'NULL') . "</p>";
                echo "<p>Arrival Time: " . ($updated->dock_arrival_time ?? 'NULL') . "</p>";

            } else {
                echo "<p style='color: red;'>✗ Test update still failing</p>";
            }

        } catch (Exception $e) {
            echo "<p style='color: red;'>Test Error: " . $e->getMessage() . "</p>";
        }
    }

} catch (Exception $e) {
    echo "<p style='color: red;'>Migration Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
    }

    table {
        margin: 10px 0;
    }

    th,
    td {
        text-align: left;
    }
</style>