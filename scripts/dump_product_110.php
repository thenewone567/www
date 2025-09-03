<?php
// Dump product and suppliers for product_id 110 for debugging
require_once __DIR__ . '/../bootstrap.php';

// Increase memory/time for CLI
ini_set('display_errors', 1);
error_reporting(E_ALL);
set_time_limit(30);

try {
    $productModel = new Product();
    $id = 110;
    $product = $productModel->getProductById($id);
    $suppliers = $productModel->getProductSuppliers($id);

    echo "== PRODUCT (id={$id}) ==\n";
    if ($product) {
        foreach ((array) $product as $k => $v) {
            echo "$k: ";
            if (is_null($v)) {
                echo "NULL\n";
            } elseif (is_numeric($v)) {
                echo "$v\n";
            } else {
                echo substr((string) $v, 0, 200) . (strlen((string) $v) > 200 ? '...[truncated]' : '') . "\n";
            }
        }
    } else {
        echo "Product not found\n";
    }

    echo "\n== SUPPLIERS ==\n";
    if ($suppliers && is_array($suppliers)) {
        foreach ($suppliers as $i => $s) {
            echo "-- Supplier #" . ($i + 1) . " --\n";
            foreach ((array) $s as $k => $v) {
                echo "$k: ";
                if (is_null($v)) {
                    echo "NULL\n";
                } elseif (is_numeric($v)) {
                    echo "$v\n";
                } else {
                    echo substr((string) $v, 0, 200) . (strlen((string) $v) > 200 ? '...[truncated]' : '') . "\n";
                }
            }
        }
    } else {
        echo "No suppliers found\n";
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}


