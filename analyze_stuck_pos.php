<?php
require 'bootstrap.php';

$db = new Database();

echo "=== STUCK PURCHASE ORDERS ANALYSIS ===\n\n";

// Check the stuck POs
$stuckPOs = [
    'PO-SENT-FIX-001', 'PO-SENT-FIX-002', 'PO-SENT-FIX-003', 
    'PO-SENT-FIX-004', 'PO-SENT-FIX-005', 'PO-SENT-FIX-006',
    'PO-TRANSIT-FIX-001', 'PO-TRANSIT-FIX-002', 'PO-TRANSIT-FIX-003', 'PO-TRANSIT-FIX-004'
];

echo "1. CURRENT STATUS OF STUCK POs:\n";
echo "===============================\n";

foreach ($stuckPOs as $poNumber) {
    $db->query("SELECT purchase_id, po_number, status, supplier_name, total_amount, purchase_date 
                FROM purchases p 
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id 
                WHERE p.po_number = :po_number");
    $db->bind(':po_number', $poNumber);
    $db->execute();
    $po = $db->single();
    
    if ($po) {
        echo "• {$po->po_number}: {$po->status} - ₹{$po->total_amount} ({$po->supplier_name})\n";
    } else {
        echo "• {$poNumber}: NOT FOUND\n";
    }
}

echo "\n2. VALID STATUSES FOR RECEIVING:\n";
echo "================================\n";

// Check what statuses are allowed for receiving
$db->query("SELECT DISTINCT status FROM purchases ORDER BY status");
$db->execute();
$allStatuses = $db->resultSet();

echo "All PO statuses in system:\n";
foreach ($allStatuses as $status) {
    echo "- {$status->status}\n";
}

echo "\n3. RECEIVING API FILTER ANALYSIS:\n";
echo "=================================\n";

// Check what the receiving API is looking for
echo "Current receiving API filters for statuses: ready_to_receive, receiving_in_progress, received, at_dock\n\n";

echo "4. PROPOSED SOLUTION:\n";
echo "====================\n";

echo "Option A: Update PO statuses to make them receivable\n";
echo "-----------------------------------------------------\n";
echo "Update 'Sent to Supplier' → 'ready_to_receive'\n";
echo "Update 'In Transit' → 'at_dock'\n\n";

echo "Option B: Add current statuses to receiving filter\n";
echo "--------------------------------------------------\n";
echo "Modify API to also include: 'Sent to Supplier', 'In Transit'\n\n";

echo "5. RECOMMENDED ACTION:\n";
echo "=====================\n";
echo "Since 'In Transit' POs should logically be receivable at dock,\n";
echo "and 'Sent to Supplier' might be mislabeled completed orders,\n";
echo "I recommend Option A: Update the statuses to proper receiving workflow.\n\n";

// Show the update SQL
echo "6. UPDATE COMMANDS:\n";
echo "==================\n";
echo "-- Update In Transit to At Dock (ready for receiving)\n";
foreach ($stuckPOs as $poNumber) {
    if (strpos($poNumber, 'TRANSIT') !== false) {
        echo "UPDATE purchases SET status = 'at_dock' WHERE po_number = '{$poNumber}';\n";
    }
}

echo "\n-- Update Sent to Supplier to Ready to Receive\n";
foreach ($stuckPOs as $poNumber) {
    if (strpos($poNumber, 'SENT') !== false) {
        echo "UPDATE purchases SET status = 'ready_to_receive' WHERE po_number = '{$poNumber}';\n";
    }
}

echo "\nWould you like me to execute these updates? (This will make the POs available for receiving)\n";
?>
