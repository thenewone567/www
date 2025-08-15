<?php
$files = [
    __DIR__ . '/../cleanup-complete.html',
    __DIR__ . '/../compact-quick-receive.html',
    __DIR__ . '/../css-optimization-demo.html',
    __DIR__ . '/../data-test.php',
    __DIR__ . '/../debug_api_response.php',
    __DIR__ . '/../debug_categories.php',
    __DIR__ . '/../debug_db_categories.php',
    __DIR__ . '/../debug_focused.php',
    __DIR__ . '/../debug_po.php',
    __DIR__ . '/../debug_products.php',
    __DIR__ . '/../debug_products_quick.php',
    __DIR__ . '/../debug_receive.php',
    __DIR__ . '/../debug_schema.php',
    __DIR__ . '/../diagnostic.html',
    __DIR__ . '/../final-css-cleanup.html',
    __DIR__ . '/../fix_products.php',
    __DIR__ . '/../last_versions_here.txt',
    __DIR__ . '/../migration-success.html',
    __DIR__ . '/../sidebar-test.html',
    __DIR__ . '/../single-css-success.html',
    __DIR__ . '/../smart-dashboard-demo.php',
    __DIR__ . '/../supplier_debug.php',
    __DIR__ . '/../temp_purchases_add.html',
    __DIR__ . '/../temp_purchases_add_auth.html',
    __DIR__ . '/../test-form-controls.html',
    __DIR__ . '/../test-smart-dashboard.php',
    __DIR__ . '/../test_api_clean.php',
    __DIR__ . '/../test_complete_workflow.php',
    __DIR__ . '/../test_dock_system.php',
    __DIR__ . '/../test_kpi_cards.html',
    __DIR__ . '/../test_quick_receive.php',
    __DIR__ . '/../test_toggle_duplicates.html',
    __DIR__ . '/../theme-test.html',
    __DIR__ . '/debug_products_count.php',
    __DIR__ . '/fix_missing_product_suppliers.php',
    __DIR__ . '/check_supplier_links.php'
];

$deleted = [];
$failed = [];
foreach ($files as $f) {
    if (file_exists($f)) {
        if (is_writable($f)) {
            if (unlink($f)) {
                $deleted[] = $f;
            } else {
                $failed[] = $f;
            }
        } else {
            // try to change permissions
            @chmod($f, 0666);
            if (file_exists($f) && unlink($f)) {
                $deleted[] = $f;
            } else {
                $failed[] = $f;
            }
        }
    }
}

echo "Deleted: \n";
foreach ($deleted as $d) echo " - $d\n";

if (!empty($failed)) {
    echo "\nFailed to delete: \n";
    foreach ($failed as $d) echo " - $d\n";
}

echo "\nDone.\n";
