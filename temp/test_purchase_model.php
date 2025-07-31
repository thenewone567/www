<?php
require_once 'app/config.php';
require_once 'app/Database.php';
require_once 'app/models/Purchase.php';

echo "<h2>Testing Purchase Model</h2>";

try {
    $purchaseModel = new Purchase();

    echo "✅ Purchase model instantiated<br>";

    // Test getPurchases method
    $purchases = $purchaseModel->getPurchases();

    echo "📊 Found " . count($purchases) . " purchases<br>";

    if (count($purchases) > 0) {
        echo "<h3>Sample Purchases:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>PO ID</th><th>PO Number</th><th>Supplier</th><th>Date</th><th>Amount</th><th>Status</th></tr>";

        foreach (array_slice($purchases, 0, 5) as $purchase) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($purchase->po_id) . "</td>";
            echo "<td>" . htmlspecialchars($purchase->po_number ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($purchase->supplier_name ?? 'Unknown') . "</td>";
            echo "<td>" . htmlspecialchars($purchase->order_date ?? 'N/A') . "</td>";
            echo "<td>$" . number_format($purchase->total_amount ?? 0, 2) . "</td>";
            echo "<td>" . htmlspecialchars($purchase->status_display ?? $purchase->status ?? 'N/A') . "</td>";
            echo "</tr>";
        }

        echo "</table>";
    } else {
        echo "❌ No purchases found. Let's check the query...";

        // Direct database check
        $db = new Database();
        $db->query("SELECT COUNT(*) as count FROM purchase_orders");
        $count = $db->single();
        echo "<br>📊 Direct purchase_orders count: " . $count->count;

        // Check for errors
        $db->query("SELECT po_id, po_number, status FROM purchase_orders LIMIT 3");
        $samples = $db->resultSet();

        if ($samples) {
            echo "<h4>Direct Query Sample:</h4>";
            foreach ($samples as $sample) {
                echo "- PO {$sample->po_id}: {$sample->po_number} ({$sample->status})<br>";
            }
        }
    }

    echo "<br><br><a href='http://localhost/purchases'>→ Visit Purchases Page</a>";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "Stack trace: " . $e->getTraceAsString();
}
?>