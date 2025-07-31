<?php
require_once 'app/config.php';
require_once 'app/Database.php';

echo "<h2>Debug Purchase Model Query</h2>";

try {
    $db = new Database();

    // Test 1: Simple query first
    echo "<h3>Test 1: Simple count query</h3>";
    $db->query("SELECT COUNT(*) as count FROM purchase_orders");
    $result = $db->single();
    echo "Count: " . $result->count . "<br>";

    // Test 2: Simple SELECT
    echo "<h3>Test 2: Simple select with LIMIT</h3>";
    $db->query("SELECT po_id, po_number, status FROM purchase_orders LIMIT 3");
    $results = $db->resultSet();
    echo "Found " . count($results) . " records<br>";
    foreach ($results as $row) {
        echo "- PO {$row->po_id}: {$row->po_number}<br>";
    }

    // Test 3: Complex query like in Purchase model but simplified
    echo "<h3>Test 3: Simplified complex query</h3>";
    $sql = "
        SELECT po.po_id, po.po_number, po.status, s.supplier_name
        FROM purchase_orders po
        LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
        WHERE 1=1
        ORDER BY po.created_at DESC
        LIMIT 5
    ";

    $db->query($sql);
    $results = $db->resultSet();
    echo "Found " . count($results) . " records<br>";

    if (count($results) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>PO ID</th><th>PO Number</th><th>Status</th><th>Supplier</th></tr>";
        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>{$row->po_id}</td>";
            echo "<td>{$row->po_number}</td>";
            echo "<td>{$row->status}</td>";
            echo "<td>" . ($row->supplier_name ?? 'No supplier') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Test 4: Check if suppliers exist
    echo "<h3>Test 4: Check suppliers</h3>";
    $db->query("SELECT COUNT(*) as count FROM suppliers");
    $result = $db->single();
    echo "Suppliers count: " . $result->count . "<br>";

    if ($result->count > 0) {
        $db->query("SELECT supplier_id, supplier_name FROM suppliers LIMIT 3");
        $suppliers = $db->resultSet();
        foreach ($suppliers as $supplier) {
            echo "- Supplier {$supplier->supplier_id}: {$supplier->supplier_name}<br>";
        }
    }

    // Test 5: Check users table (for created_by)
    echo "<h3>Test 5: Check users table</h3>";
    $db->query("SELECT COUNT(*) as count FROM users");
    $result = $db->single();
    echo "Users count: " . $result->count . "<br>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
}
?>