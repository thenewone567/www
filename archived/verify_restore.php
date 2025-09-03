<?php
// Verify database restore
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'master_hardware';

echo "Verifying database restore...\n";

$mysqli = new mysqli($host, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Check products
$result = $mysqli->query("SELECT COUNT(*) as count FROM products");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Products in database: " . $row['count'] . "\n";
}

// Show sample products
echo "\nSample products:\n";
$result = $mysqli->query("SELECT id, name, sku FROM products LIMIT 10");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: {$row['id']}, Name: {$row['name']}, SKU: {$row['sku']}\n";
    }
}

// Show all tables
$result = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}
echo "\nTables restored (" . count($tables) . "):\n";
echo implode(', ', $tables) . "\n";

$mysqli->close();
echo "\nDatabase restore verification complete!\n";
?>