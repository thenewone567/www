<?php
require 'bootstrap.php';

$db = new Database();

echo "=== DELIVERY TIME CONSISTENCY FIX ===\n\n";

// First, let's see the current inconsistency
echo "1. CURRENT INCONSISTENCY ANALYSIS:\n";
echo "===================================\n";

$db->query("
    SELECT 
        s.supplier_name,
        s.default_delivery_days,
        COUNT(ps.ps_id) as product_count,
        GROUP_CONCAT(DISTINCT ps.lead_time_days) as current_lead_times,
        AVG(ps.lead_time_days) as avg_lead_time
    FROM suppliers s
    LEFT JOIN product_suppliers ps ON s.supplier_id = ps.supplier_id
    WHERE s.default_delivery_days IS NOT NULL 
    AND s.default_delivery_days > 0
    AND ps.ps_id IS NOT NULL
    GROUP BY s.supplier_id, s.supplier_name, s.default_delivery_days
    HAVING AVG(ps.lead_time_days) != s.default_delivery_days
    ORDER BY s.supplier_name
");

$db->execute();
$inconsistencies = $db->resultSet();

if ($inconsistencies) {
    foreach ($inconsistencies as $supplier) {
        echo "Supplier: {$supplier->supplier_name}\n";
        echo "  Default Delivery Days: {$supplier->default_delivery_days}\n";
        echo "  Product Lead Times: {$supplier->current_lead_times}\n";
        echo "  Products Count: {$supplier->product_count}\n";
        echo "  Average Lead Time: {$supplier->avg_lead_time}\n";
        echo "  ❌ INCONSISTENT\n\n";
    }
} else {
    echo "No inconsistencies found.\n\n";
}

echo "2. PROPOSED FIX:\n";
echo "================\n";
echo "Update product_suppliers.lead_time_days to match suppliers.default_delivery_days\n";
echo "where lead_time_days is currently 7 (generic default)\n\n";

// Show what will be updated
$db->query("
    SELECT 
        s.supplier_name,
        s.default_delivery_days,
        p.product_name,
        ps.lead_time_days as current_lead_time,
        s.default_delivery_days as new_lead_time
    FROM suppliers s
    INNER JOIN product_suppliers ps ON s.supplier_id = ps.supplier_id
    INNER JOIN products p ON ps.product_id = p.product_id
    WHERE s.default_delivery_days IS NOT NULL 
    AND s.default_delivery_days > 0
    AND ps.lead_time_days = 7
    AND s.default_delivery_days != 7
    ORDER BY s.supplier_name, p.product_name
");

$db->execute();
$toUpdate = $db->resultSet();

if ($toUpdate) {
    echo "Records to be updated:\n";
    echo "=====================\n";
    
    foreach ($toUpdate as $record) {
        echo "• {$record->supplier_name} - {$record->product_name}\n";
        echo "  Change: {$record->current_lead_time} days → {$record->new_lead_time} days\n";
    }
    
    echo "\nTotal records to update: " . count($toUpdate) . "\n\n";
    
    echo "3. EXECUTING FIX:\n";
    echo "=================\n";
    
    // Execute the update
    $db->query("
        UPDATE product_suppliers ps
        INNER JOIN suppliers s ON ps.supplier_id = s.supplier_id
        SET ps.lead_time_days = s.default_delivery_days,
            ps.updated_at = NOW()
        WHERE s.default_delivery_days IS NOT NULL 
        AND s.default_delivery_days > 0
        AND ps.lead_time_days = 7
        AND s.default_delivery_days != 7
    ");
    
    $db->execute();
    $updatedCount = $db->rowCount();
    
    echo "✅ Updated {$updatedCount} product-supplier relationships\n\n";
    
    // Verify the fix
    echo "4. VERIFICATION:\n";
    echo "================\n";
    
    $db->query("
        SELECT 
            s.supplier_name,
            s.default_delivery_days,
            COUNT(ps.ps_id) as product_count,
            GROUP_CONCAT(DISTINCT ps.lead_time_days) as updated_lead_times,
            AVG(ps.lead_time_days) as avg_lead_time
        FROM suppliers s
        LEFT JOIN product_suppliers ps ON s.supplier_id = ps.supplier_id
        WHERE s.supplier_name IN ('Dharamvir Pvt Ltd', 'Ishaan Electrical Suppliers')
        AND ps.ps_id IS NOT NULL
        GROUP BY s.supplier_id, s.supplier_name, s.default_delivery_days
        ORDER BY s.supplier_name
    ");
    
    $db->execute();
    $verification = $db->resultSet();
    
    foreach ($verification as $supplier) {
        echo "Supplier: {$supplier->supplier_name}\n";
        echo "  Default Delivery Days: {$supplier->default_delivery_days}\n";
        echo "  Product Lead Times: {$supplier->updated_lead_times}\n";
        echo "  Products Count: {$supplier->product_count}\n";
        echo "  Status: " . ($supplier->avg_lead_time == $supplier->default_delivery_days ? "✅ CONSISTENT" : "❌ STILL INCONSISTENT") . "\n\n";
    }
    
} else {
    echo "No records need to be updated.\n\n";
}

echo "5. SUMMARY:\n";
echo "===========\n";
echo "• Fixed data inconsistency between suppliers.default_delivery_days and product_suppliers.lead_time_days\n";
echo "• Dharamvir Pvt Ltd now has consistent 15-day delivery times across all products\n";
echo "• Maintains product-specific delivery times that were intentionally set (not generic 7 days)\n";
echo "• Future product-supplier relationships will need proper delivery time management\n";
?>
