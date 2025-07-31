<?php
require_once 'app/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

    echo "purchase_order_items table structure:\n";
    $columns = $pdo->query("DESCRIBE purchase_order_items")->fetchAll();
    foreach ($columns as $col) {
        echo "- {$col['Field']} ({$col['Type']})\n";
    }

    echo "\nSample data:\n";
    $sample = $pdo->query("SELECT * FROM purchase_order_items LIMIT 3")->fetchAll();
    foreach ($sample as $row) {
        print_r($row);
        break; // Just show structure of first record
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>