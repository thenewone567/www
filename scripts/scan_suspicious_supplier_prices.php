<?php
require_once __DIR__ . '/../bootstrap.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

try {
    $db = new Database();
    // Find supplier rows with very large purchase_price
    $sql = "SELECT ps.*, p.selling_price, p.product_name FROM product_suppliers ps LEFT JOIN products p ON ps.product_id = p.product_id WHERE ps.purchase_price > 1000 ORDER BY ps.purchase_price DESC LIMIT 100";
    $db->query($sql);
    $db->execute();
    $rows = $db->resultSet();

    echo "Rows with purchase_price > 1000:\n";
    if ($rows) {
        foreach ($rows as $r) {
            // support both array and object row formats
            $ps_id = is_object($r) ? ($r->ps_id ?? '') : ($r['ps_id'] ?? '');
            $product_id = is_object($r) ? ($r->product_id ?? '') : ($r['product_id'] ?? '');
            $product_name = is_object($r) ? ($r->product_name ?? '') : ($r['product_name'] ?? '');
            $supplier_id = is_object($r) ? ($r->supplier_id ?? '') : ($r['supplier_id'] ?? '');
            $purchase_price = is_object($r) ? ($r->purchase_price ?? '') : ($r['purchase_price'] ?? '');
            $selling_price = is_object($r) ? ($r->selling_price ?? '') : ($r['selling_price'] ?? '');

            echo sprintf(
                "ps_id=%s product_id=%s product_name=%s supplier_id=%s purchase_price=%s selling_price=%s\n",
                $ps_id,
                $product_id,
                substr($product_name ?: '(no name)', 0, 40),
                $supplier_id,
                $purchase_price,
                $selling_price ?: 'NULL'
            );
        }
    } else {
        echo "None\n";
    }

    // Now find rows where purchase_price is > 10x selling_price (and selling_price > 0)
    $sql2 = "SELECT ps.*, p.selling_price, p.product_name, (ps.purchase_price / NULLIF(p.selling_price,0)) as ratio FROM product_suppliers ps LEFT JOIN products p ON ps.product_id = p.product_id WHERE p.selling_price IS NOT NULL AND p.selling_price > 0 AND ps.purchase_price / p.selling_price > 10 ORDER BY ratio DESC LIMIT 200";
    $db->query($sql2);
    $db->execute();
    $rows2 = $db->resultSet();

    echo "\nRows with purchase_price > 10x selling_price:\n";
    if ($rows2) {
        foreach ($rows2 as $r) {
            $ps_id = is_object($r) ? ($r->ps_id ?? '') : ($r['ps_id'] ?? '');
            $product_id = is_object($r) ? ($r->product_id ?? '') : ($r['product_id'] ?? '');
            $product_name = is_object($r) ? ($r->product_name ?? '') : ($r['product_name'] ?? '');
            $supplier_id = is_object($r) ? ($r->supplier_id ?? '') : ($r['supplier_id'] ?? '');
            $purchase_price = is_object($r) ? ($r->purchase_price ?? '') : ($r['purchase_price'] ?? '');
            $selling_price = is_object($r) ? ($r->selling_price ?? '') : ($r['selling_price'] ?? '');
            $ratio = is_object($r) ? ($r->ratio ?? 0) : ($r['ratio'] ?? 0);

            echo sprintf(
                "ps_id=%s product_id=%s product_name=%s supplier_id=%s purchase_price=%s selling_price=%s ratio=%.2f\n",
                $ps_id,
                $product_id,
                substr($product_name ?: '(no name)', 0, 40),
                $supplier_id,
                $purchase_price,
                $selling_price ?: 'NULL',
                floatval($ratio)
            );
        }
    } else {
        echo "None\n";
    }

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

