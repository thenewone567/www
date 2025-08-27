<?php
/**
 * Test the soft delete functionality
 */

require_once 'bootstrap.php';

echo "Testing Purchase Order Soft Delete Functionality\n";
echo "================================================\n\n";

try {
    // Create Purchase model instance
    $purchaseModel = new Purchase();
    
    // Test: Get all purchases to see current data
    $purchases = $purchaseModel->getPurchases();
    echo "Current purchases count: " . count($purchases) . "\n";
    
    if (!empty($purchases)) {
        echo "First purchase details:\n";
        $firstPurchase = $purchases[0];
        echo "- ID: " . $firstPurchase->purchase_id . "\n";
        echo "- PO Number: " . $firstPurchase->po_number . "\n";
        echo "- Status: " . $firstPurchase->status . "\n";
        echo "- Created By: " . $firstPurchase->created_by . "\n\n";
        
        // Test: Get detailed purchase info
        $detailedPurchase = $purchaseModel->getPurchaseById($firstPurchase->purchase_id);
        echo "Detailed purchase info:\n";
        echo "- Supplier: " . ($detailedPurchase->supplier_name ?? 'N/A') . "\n";
        echo "- Created By Username: " . ($detailedPurchase->created_by_username ?? 'N/A') . "\n";
        echo "- Created By Full Name: " . ($detailedPurchase->created_by_fullname ?? 'N/A') . "\n";
        echo "- Tracking Number: " . ($detailedPurchase->tracking_number ?? 'Not set') . "\n";
        echo "- Cancellation Reason: " . ($detailedPurchase->cancellation_reason ?? 'None') . "\n\n";
    }
    
    echo "✅ Purchase model tests completed successfully!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nTest completed.\n";
?>
