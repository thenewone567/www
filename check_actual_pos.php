<?php
require 'bootstrap.php';

$db = new Database();

echo "=== ACTUAL PURCHASE ORDERS IN DATABASE ===\n\n";

// Get all POs with sent or in_transit status
$db->query("SELECT p.purchase_id, p.po_number, p.status, s.supplier_name, p.total_amount, p.purchase_date 
            FROM purchases p 
            LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id 
            WHERE p.status IN ('sent', 'in_transit') 
            ORDER BY p.purchase_date DESC, p.po_number");
$db->execute();
$stuckPOs = $db->resultSet();

if (count($stuckPOs) > 0) {
    echo "Found " . count($stuckPOs) . " POs stuck in sent/in_transit status:\n";
    echo "================================================================\n";
    
    foreach ($stuckPOs as $po) {
        echo "• {$po->po_number}: {$po->status} - ₹{$po->total_amount} ({$po->supplier_name}) - {$po->purchase_date}\n";
    }
    
    echo "\n=== PROBLEM ANALYSIS ===\n";
    echo "The receiving API looks for these statuses: ready_to_receive, receiving_in_progress, received, at_dock\n";
    echo "But the stuck POs have statuses: sent, in_transit\n\n";
    
    echo "=== SOLUTION OPTIONS ===\n\n";
    
    echo "Option 1: Update API to include current statuses\n";
    echo "-----------------------------------------------\n";
    echo "Modify getAvailablePOs.php to include 'sent' and 'in_transit' statuses\n\n";
    
    echo "Option 2: Update PO statuses to match receiving workflow\n";
    echo "-------------------------------------------------------\n";
    echo "Change 'sent' → 'ready_to_receive'\n";
    echo "Change 'in_transit' → 'at_dock'\n\n";
    
    echo "=== RECOMMENDED SOLUTION ===\n";
    echo "Option 1 is better because:\n";
    echo "- Less data manipulation\n";
    echo "- Preserves original status meanings\n";
    echo "- 'in_transit' logically should be receivable\n";
    echo "- 'sent' orders that arrived should be receivable\n\n";
    
} else {
    echo "No POs found with 'sent' or 'in_transit' status.\n\n";
    
    // Show all POs to understand the situation
    echo "All Purchase Orders in system:\n";
    echo "==============================\n";
    
    $db->query("SELECT p.purchase_id, p.po_number, p.status, s.supplier_name, p.total_amount 
                FROM purchases p 
                LEFT JOIN suppliers s ON p.supplier_id = s.supplier_id 
                ORDER BY p.purchase_date DESC 
                LIMIT 20");
    $db->execute();
    $allPOs = $db->resultSet();
    
    foreach ($allPOs as $po) {
        echo "• {$po->po_number}: {$po->status} - ₹{$po->total_amount} ({$po->supplier_name})\n";
    }
}

echo "\n=== CURRENT RECEIVING API FILTER ===\n";
echo "File: api/getAvailablePOs.php\n";
echo "Current filter: status=ready_to_receive,receiving_in_progress,received,at_dock\n";
echo "Suggested addition: ,sent,in_transit\n";
?>
