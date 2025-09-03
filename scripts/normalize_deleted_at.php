<?php
/**
 * Normalize deleted_at values in products table
 * - Convert '' and '0000-00-00 00:00:00' to NULL
 * Run: php scripts/normalize_deleted_at.php
 */
require_once __DIR__ . '/../bootstrap.php';

$db = new Database();

echo "Checking products table for malformed deleted_at values...\n";

$db->query("SELECT COUNT(*) as cnt FROM products WHERE deleted_at = '' OR deleted_at = '0000-00-00 00:00:00'");
$db->execute();
$c = $db->single();
$toFix = intval($c->cnt ?? 0);

echo "Rows to normalize: {$toFix}\n";

if ($toFix === 0) {
    echo "Nothing to do.\n";
    exit(0);
}

echo "Updating records...\n";
$db->query("UPDATE products SET deleted_at = NULL WHERE deleted_at = '' OR deleted_at = '0000-00-00 00:00:00'");
$db->execute();
$affected = $db->rowCount();

echo "Updated {$affected} rows.\n";

// Verify
$db->query("SELECT COUNT(*) as cnt FROM products WHERE deleted_at IS NULL");
$db->execute();
$c2 = $db->single();
echo "Products with deleted_at NULL: " . ($c2->cnt ?? 0) . "\n";

echo "Normalization complete. Consider re-enabling strict deleted_at filtering in queries.\n";

?>