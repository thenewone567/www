<?php
require_once 'bootstrap.php';

try {
    $db = new Database();
    
    echo "<h3>Available Purchase Orders</h3>";
    
    // Check all purchases
    $db->query("SELECT purchase_id, po_number, status, supplier_name, purchase_date FROM purchases ORDER BY purchase_id DESC LIMIT 10");
    $db->execute();
    $purchases = $db->resultSet();
    
    if (empty($purchases)) {
        echo "<p style='color: red;'>No purchase orders found in database!</p>";
        
        // Let's create a sample purchase order for testing
        echo "<h4>Creating sample purchase order...</h4>";
        
        $db->query("
            INSERT INTO purchases (po_number, supplier_name, purchase_date, status, total_amount, created_at, updated_at) 
            VALUES (?, ?, NOW(), ?, ?, NOW(), NOW())
        ");
        $db->bind(1, 'PO-2024-005');
        $db->bind(2, 'Test Supplier');
        $db->bind(3, 'pending');
        $db->bind(4, 1000.00);
        
        $result = $db->execute();
        
        if ($result) {
            echo "<p style='color: green;'>Sample PO created: PO-2024-005</p>";
        } else {
            echo "<p style='color: red;'>Failed to create sample PO</p>";
        }
        
        // Re-query to show the created PO
        $db->query("SELECT purchase_id, po_number, status, supplier_name, purchase_date FROM purchases ORDER BY purchase_id DESC LIMIT 10");
        $db->execute();
        $purchases = $db->resultSet();
    }
    
    echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th style='padding: 8px; border: 1px solid #ccc;'>ID</th>";
    echo "<th style='padding: 8px; border: 1px solid #ccc;'>PO Number</th>";
    echo "<th style='padding: 8px; border: 1px solid #ccc;'>Status</th>";
    echo "<th style='padding: 8px; border: 1px solid #ccc;'>Supplier</th>";
    echo "<th style='padding: 8px; border: 1px solid #ccc;'>Date</th>";
    echo "</tr>";
    
    foreach($purchases as $po) {
        echo "<tr>";
        echo "<td style='padding: 8px; border: 1px solid #ccc;'>{$po->purchase_id}</td>";
        echo "<td style='padding: 8px; border: 1px solid #ccc;'><strong>{$po->po_number}</strong></td>";
        echo "<td style='padding: 8px; border: 1px solid #ccc;'>";
        
        $statusColor = 'black';
        switch(strtolower($po->status)) {
            case 'pending': $statusColor = 'orange'; break;
            case 'sent': $statusColor = 'blue'; break;
            case 'in_transit': $statusColor = 'purple'; break;
            case 'arrived_at_facility': $statusColor = 'green'; break;
            case 'ready_to_receive': $statusColor = 'darkgreen'; break;
            case 'received': $statusColor = 'gray'; break;
        }
        
        echo "<span style='color: {$statusColor};'>{$po->status}</span>";
        echo "</td>";
        echo "<td style='padding: 8px; border: 1px solid #ccc;'>{$po->supplier_name}</td>";
        echo "<td style='padding: 8px; border: 1px solid #ccc;'>{$po->purchase_date}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h4>Status Definitions:</h4>";
    echo "<ul>";
    echo "<li><span style='color: orange;'>pending</span> - Can be off-loaded</li>";
    echo "<li><span style='color: blue;'>sent</span> - Can be off-loaded</li>";
    echo "<li><span style='color: purple;'>in_transit</span> - Can be off-loaded</li>";
    echo "<li><span style='color: green;'>arrived_at_facility</span> - Off-loading in progress</li>";
    echo "<li><span style='color: darkgreen;'>ready_to_receive</span> - Ready for inventory receiving</li>";
    echo "<li><span style='color: gray;'>received</span> - Completed</li>";
    echo "</ul>";
    
} catch(Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
