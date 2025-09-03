<?php
require_once 'bootstrap.php';

echo "=== All Columns in Products Table ===\n";

$db = new Database();
$db->query('DESCRIBE products');
$columns = $db->resultSet();

foreach ($columns as $column) {
    echo $column->Field . " | " . $column->Type . " | " . $column->Null . " | " . ($column->Default ?? 'NULL') . "\n";
}

echo "\n=== Checking for dimension-related columns ===\n";
foreach ($columns as $column) {
    if (stripos($column->Field, 'dimension') !== false || stripos($column->Field, 'width') !== false || stripos($column->Field, 'height') !== false || stripos($column->Field, 'length') !== false) {
        echo "Found dimension-related column: " . $column->Field . " (" . $column->Type . ")\n";
    }
}
?>