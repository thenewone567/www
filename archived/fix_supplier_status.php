<?php
require_once 'bootstrap.php';

echo "🔍 FIXING SUPPLIERS STATUS FOR PURCHASE BOT\n";
echo "===========================================\n\n";

try {
    $db = new Database();

    echo "1️⃣ Checking supplier status values...\n";

    $db->query("SELECT DISTINCT status, COUNT(*) as count FROM suppliers GROUP BY status");
    $db->execute();
    $statusCounts = $db->resultSet();

    echo "   📊 Supplier status distribution:\n";
    foreach ($statusCounts as $status) {
        echo "      • Status: '{$status->status}' - Count: {$status->count}\n";
    }

    echo "\n2️⃣ Checking suppliers with NULL or empty status...\n";

    $db->query("SELECT COUNT(*) as null_status FROM suppliers WHERE status IS NULL OR status = ''");
    $db->execute();
    $nullStatus = $db->single()->null_status;

    echo "   📊 Suppliers with NULL/empty status: {$nullStatus}\n";

    echo "\n3️⃣ Setting active status for suppliers...\n";

    // Set status to 'active' for suppliers that don't have a status
    $db->query("UPDATE suppliers SET status = 'active' WHERE status IS NULL OR status = '' OR status = 'Active'");
    $db->execute();
    $updated = $db->rowCount();

    echo "   📊 Updated {$updated} suppliers to 'active' status\n";

    echo "\n4️⃣ Verifying active suppliers...\n";

    $db->query("SELECT supplier_id, supplier_name, status FROM suppliers WHERE status = 'active' ORDER BY supplier_name LIMIT 10");
    $db->execute();
    $activeSuppliers = $db->resultSet();

    echo "   📊 Active suppliers (first 10):\n";
    foreach ($activeSuppliers as $supplier) {
        echo "      • {$supplier->supplier_name} (ID: {$supplier->supplier_id}) - Status: {$supplier->status}\n";
    }

    if (count($activeSuppliers) > 10) {
        $db->query("SELECT COUNT(*) as total_active FROM suppliers WHERE status = 'active'");
        $db->execute();
        $totalActive = $db->single()->total_active;
        echo "      • ... and " . ($totalActive - 10) . " more\n";
    }

    echo "\n5️⃣ Final supplier statistics:\n";

    $db->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active FROM suppliers");
    $db->execute();
    $stats = $db->single();

    echo "   📊 Total suppliers: {$stats->total}\n";
    echo "   📊 Active suppliers: {$stats->active}\n";

    if ($stats->active > 0) {
        echo "   🎉 SUPPLIERS ARE NOW ACTIVE AND READY FOR PURCHASE BOT!\n";
    } else {
        echo "   ❌ STILL NO ACTIVE SUPPLIERS\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
}
?>