<?php
require_once 'bootstrap.php';

try {
    $db = new Database();

    echo "=== CHECKING TABLE STRUCTURE ===\n\n";

    echo "Purchases table structure:\n";
    $db->query('DESCRIBE purchases');
    $db->execute();
    $columns = $db->resultSet();

    foreach ($columns as $col) {
        echo "- {$col->Field}: {$col->Type}";
        if ($col->Field === 'status') {
            echo " *** STATUS COLUMN ***";
        }
        echo "\n";
    }

    echo "\nChecking current status values in database:\n";
    $db->query('SELECT DISTINCT status FROM purchases WHERE status IS NOT NULL ORDER BY status');
    $db->execute();
    $statuses = $db->resultSet();

    foreach ($statuses as $status) {
        echo "- '{$status->status}'\n";
    }

    // Test if we can insert 'off-loading' status
    echo "\nTesting if 'off-loading' status is allowed...\n";

    try {
        $db->query('SELECT * FROM purchases WHERE status = :status LIMIT 1');
        $db->bind(':status', 'off-loading');
        $db->execute();
        echo "✅ Query with 'off-loading' status works\n";
    } catch (Exception $e) {
        echo "❌ Error with 'off-loading' status: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>