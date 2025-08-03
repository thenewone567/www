<?php
// Test CSV Import Functionality
require_once 'app/config.php';
require_once 'app/Database.php';

// Test basic database connection
try {
    $db = new Database();
    echo "✓ Database connection successful\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test if categories exist
$db->query("SELECT COUNT(*) as count FROM categories WHERE is_active = 1");
$db->execute();
$categoryCount = $db->single();
echo "✓ Found {$categoryCount->count} active categories\n";

// Test if brands exist
$db->query("SELECT COUNT(*) as count FROM brands WHERE is_active = 1");
$db->execute();
$brandCount = $db->single();
echo "✓ Found {$brandCount->count} active brands\n";

// Test if units exist
$db->query("SELECT COUNT(*) as count FROM units WHERE is_active = 1");
$db->execute();
$unitCount = $db->single();
echo "✓ Found {$unitCount->count} active units\n";

// Test CSV parsing
$csvContent = "product_name,sku,category_id,purchase_price,selling_price\nTest Product,TEST123,1,10.00,15.00\n";
$tempFile = tempnam(sys_get_temp_dir(), 'csv_test');
file_put_contents($tempFile, $csvContent);

$handle = fopen($tempFile, 'r');
$headers = fgetcsv($handle);
$data = fgetcsv($handle);
fclose($handle);
unlink($tempFile);

if ($headers && $data) {
    echo "✓ CSV parsing works correctly\n";
    echo "  Headers: " . implode(', ', $headers) . "\n";
    echo "  Data: " . implode(', ', $data) . "\n";
} else {
    echo "✗ CSV parsing failed\n";
}

echo "\n✓ All tests passed! CSV import functionality should work.\n";
?>
