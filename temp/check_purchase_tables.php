<?php
require_once 'app/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Available tables:\n";
    foreach ($tables as $table) {
        if (strpos($table, 'purchase') !== false) {
            echo "- $table\n";
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "  Records: $count\n";
        }
    }

    // Check structure of purchases table
    echo "\n=== purchases table structure ===\n";
    try {
        $columns = $pdo->query("DESCRIBE purchases")->fetchAll();
        foreach ($columns as $col) {
            echo "- {$col['Field']} ({$col['Type']})\n";
        }
    } catch (Exception $e) {
        echo "No purchases table found\n";
    }

    // Check structure of purchase_orders table
    echo "\n=== purchase_orders table structure ===\n";
    try {
        $columns = $pdo->query("DESCRIBE purchase_orders")->fetchAll();
        foreach ($columns as $col) {
            echo "- {$col['Field']} ({$col['Type']})\n";
        }
    } catch (Exception $e) {
        echo "No purchase_orders table found\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>