<?php
/**
 * Repair inconsistent deleted_at values where is_active = 1 but deleted_at IS NOT NULL
 * Usage:
 *  php scripts/repair_deleted_at_by_is_active.php           # dry-run, lists rows
 *  php scripts/repair_deleted_at_by_is_active.php --apply   # performs update (with backup)
 */
require_once __DIR__ . '/../bootstrap.php';

$apply = in_array('--apply', $argv);

$db = new Database();

echo "Searching for products with is_active=1 but deleted_at IS NOT NULL...\n";
$db->query("SELECT product_id, product_name, sku, is_active, deleted_at FROM products WHERE is_active = 1 AND deleted_at IS NOT NULL");
$db->execute();
$rows = $db->resultSet();
$count = count($rows);
echo "Found {$count} inconsistent products.\n";
if ($count === 0)
    exit(0);

foreach ($rows as $r) {
    echo "ID: {$r->product_id} | {$r->product_name} | SKU: {$r->sku} | deleted_at: {$r->deleted_at}\n";
}

if (!$apply) {
    echo "\nDry-run only. Re-run with --apply to fix these rows (a backup will be created).\n";
    exit(0);
}

// Create a backup table if not exists
$db->query("CREATE TABLE IF NOT EXISTS products_deleted_at_backup LIKE products");
$db->execute();

// Insert backup rows for affected product_ids
$ids = array_map(function ($r) {
    return intval($r->product_id); }, $rows);
$idList = implode(',', $ids);

echo "Backing up affected rows into products_deleted_at_backup...\n";
$db->query("INSERT INTO products_deleted_at_backup SELECT * FROM products WHERE product_id IN ({$idList})");
$db->execute();

// Perform update
echo "Setting deleted_at = NULL for affected rows...\n";
$db->query("UPDATE products SET deleted_at = NULL WHERE product_id IN ({$idList})");
$db->execute();
$affected = $db->rowCount();
echo "Updated {$affected} rows.\n";

echo "Repair complete. It's recommended to review the backup table 'products_deleted_at_backup'.\n";

?>