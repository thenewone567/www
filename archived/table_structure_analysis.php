<?php
require_once 'bootstrap.php';

echo "=== TABLE STRUCTURE ANALYSIS ===\n\n";

$database = new Database();

// Check structure of each table
$tables = ['products', 'purchases', 'purchase_items', 'receiving', 'inventory_transactions'];

foreach ($tables as $table) {
    echo "TABLE: $table\n";
    $database->query("DESCRIBE $table");
    if ($database->execute()) {
        $stmt = $database->getStatement();
        while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
            echo "   {$row->Field} | {$row->Type} | {$row->Null} | {$row->Key} | {$row->Default}\n";
        }
    } else {
        echo "   ERROR: " . $database->getLastError() . "\n";
    }
    echo "\n";
}

echo "=== STRUCTURE ANALYSIS COMPLETE ===\n";
