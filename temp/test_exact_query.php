<?php
require_once 'app/config.php';
require_once 'app/Database.php';

echo "<h2>Testing Exact Purchase Model Query</h2>";

try {
    $db = new Database();

    // This is the exact query from the Purchase model
    $sql = "
        SELECT po.*, s.supplier_name, s.contact_info,
               u.username as created_by_name,
               COUNT(poi.po_item_id) as item_count,
               COALESCE(SUM(poi.quantity_ordered), 0) as total_items_ordered,
               COALESCE(SUM(poi.quantity_received), 0) as total_items_received,
               CASE 
                   WHEN po.status = 'received' THEN 'Complete'
                   WHEN po.status = 'partially_received' THEN 'Partial'
                   WHEN po.status = 'sent' THEN 'In Transit'
                   WHEN po.status = 'pending' THEN 'Pending'
                   WHEN po.status = 'cancelled' THEN 'Cancelled'
                   ELSE 'Unknown'
               END as status_display
        FROM purchase_orders po
        LEFT JOIN suppliers s ON po.supplier_id = s.supplier_id
        LEFT JOIN users u ON po.created_by = u.user_id
        LEFT JOIN purchase_order_items poi ON po.po_id = poi.po_id
        WHERE 1=1
        GROUP BY po.po_id
        ORDER BY po.created_at DESC
        LIMIT 5
    ";

    echo "<h3>Executing the exact Purchase model query...</h3>";

    $db->query($sql);
    $results = $db->resultSet();

    echo "Found " . count($results) . " records<br><br>";

    if (count($results) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr>";
        echo "<th>PO ID</th><th>PO Number</th><th>Supplier</th><th>Status</th>";
        echo "<th>Items</th><th>Created By</th><th>Status Display</th>";
        echo "</tr>";

        foreach ($results as $row) {
            echo "<tr>";
            echo "<td>{$row->po_id}</td>";
            echo "<td>{$row->po_number}</td>";
            echo "<td>" . ($row->supplier_name ?? 'Unknown') . "</td>";
            echo "<td>{$row->status}</td>";
            echo "<td>{$row->item_count}</td>";
            echo "<td>" . ($row->created_by_name ?? 'Unknown') . "</td>";
            echo "<td>{$row->status_display}</td>";
            echo "</tr>";
        }
        echo "</table>";

        echo "<h4>Sample Record Details:</h4>";
        $first = $results[0];
        echo "<pre>";
        print_r($first);
        echo "</pre>";

    } else {
        echo "❌ No results returned. Let's check for SQL errors...<br>";

        // Check if the query itself is valid
        try {
            $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $pdoResults = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo "Direct PDO query returned " . count($pdoResults) . " results<br>";

            if (count($pdoResults) > 0) {
                echo "✅ The query works with direct PDO, issue is with Database class<br>";
            }

        } catch (Exception $e) {
            echo "❌ SQL Error: " . $e->getMessage() . "<br>";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
}
?>