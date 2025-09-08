<?php
require_once 'bootstrap.php';

echo "=== DATABASE STATUS MIGRATION: pending_arrival → off-loading ===\n\n";

$db = new Database();

try {
    echo "🔍 Step 1: Checking current database structure...\n";

    // Check current table structure
    $db->query('DESCRIBE purchases');
    $columns = $db->resultset();

    $statusColumn = null;
    foreach ($columns as $column) {
        if ($column->Field === 'status') {
            $statusColumn = $column;
            break;
        }
    }

    if (!$statusColumn) {
        echo "❌ Status column not found in purchases table\n";
        exit;
    }

    echo "✅ Current status column type: {$statusColumn->Type}\n";
    echo "✅ Current default: " . ($statusColumn->Default ?: 'NULL') . "\n\n";

    echo "🔍 Step 2: Checking for existing 'pending_arrival' records...\n";

    $db->query('SELECT COUNT(*) as count FROM purchases WHERE status = :status');
    $db->bind(':status', 'pending_arrival');
    $pendingCount = $db->single();

    echo "Found {$pendingCount->count} records with 'pending_arrival' status\n\n";

    if ($pendingCount->count > 0) {
        echo "📋 Current pending_arrival records:\n";
        $db->query('SELECT po_number, status, dock_arrival_time FROM purchases WHERE status = :status');
        $db->bind(':status', 'pending_arrival');
        $records = $db->resultset();

        foreach ($records as $record) {
            echo "   - {$record->po_number}: {$record->status} (Dock: " . ($record->dock_arrival_time ?: 'NULL') . ")\n";
        }
        echo "\n";
    }

    echo "🔧 Step 3: Preparing migration SQL...\n\n";

    // Create the migration SQL
    $migrationSQL = [
        "-- Step 1: Add temporary column with new ENUM values",
        "ALTER TABLE purchases ADD COLUMN status_new ENUM('pending', 'sent', 'in_transit', 'shipped', 'off-loading', 'ready_to_receive', 'receiving_in_progress', 'received', 'completed', 'cancelled', 'returned') DEFAULT 'pending';",
        "",
        "-- Step 2: Copy data with mapping pending_arrival → off-loading",
        "UPDATE purchases SET status_new = CASE",
        "    WHEN status = 'pending_arrival' THEN 'off-loading'",
        "    ELSE status",
        "END;",
        "",
        "-- Step 3: Drop old status column",
        "ALTER TABLE purchases DROP COLUMN status;",
        "",
        "-- Step 4: Rename new column to status",
        "ALTER TABLE purchases CHANGE status_new status ENUM('pending', 'sent', 'in_transit', 'shipped', 'off-loading', 'ready_to_receive', 'receiving_in_progress', 'received', 'completed', 'cancelled', 'returned') DEFAULT 'pending';",
    ];

    echo "📝 Migration SQL:\n";
    foreach ($migrationSQL as $line) {
        if (!empty(trim($line)) && !str_starts_with($line, '--')) {
            echo "   $line\n";
        } else {
            echo "$line\n";
        }
    }

    echo "\n⚠️  WARNING: This will modify the database structure!\n";
    echo "⚠️  Make sure to backup your database before running this migration.\n\n";

    echo "🚀 Ready to execute migration? (This script is just showing the plan)\n";
    echo "To execute, run: php execute_status_migration.php\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>