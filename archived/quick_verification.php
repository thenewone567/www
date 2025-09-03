<?php
require_once 'bootstrap.php';

echo "Quick Dashboard Fix Verification\n";
echo "===============================\n";

$db = new Database();

// Test the corrected calculation
$db->query("
    SELECT COUNT(DISTINCT p.product_id) as total 
    FROM products p
    LEFT JOIN inventory i ON p.product_id = i.product_id
    WHERE p.is_active = 1
    AND COALESCE((SELECT SUM(quantity) FROM inventory WHERE product_id = p.product_id), 0) <= COALESCE(p.reorder_level, 10)
");
$db->execute();
$result = $db->single();

echo "Corrected low inventory count: " . $result->total . "\n";
echo "This should match the Hardware Store Dashboard number (17)\n";

if ($result->total == 17) {
    echo "✅ SUCCESS: Bot Dashboard will now show 17 (same as Hardware Dashboard)\n";
} else {
    echo "❌ Result: " . $result->total . " (expected 17)\n";
}
?>