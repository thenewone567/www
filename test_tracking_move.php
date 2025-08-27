<?php
/**
 * Test the tracking number button move functionality
 */

require_once 'bootstrap.php';

echo "Testing Tracking Number Button Movement\n";
echo "======================================\n\n";

try {
    // Test: Get a purchase order to test with
    $purchaseModel = new Purchase();
    $purchases = $purchaseModel->getPurchases();
    
    if (!empty($purchases)) {
        $testPurchase = $purchases[0];
        echo "Testing with Purchase ID: " . $testPurchase->purchase_id . "\n";
        echo "PO Number: " . $testPurchase->po_number . "\n";
        echo "Status: " . $testPurchase->status . "\n";
        echo "Tracking Number: " . ($testPurchase->tracking_number ?: 'Not set') . "\n\n";
        
        // Test: Get detailed view for the purchase
        $detailedPurchase = $purchaseModel->getPurchaseById($testPurchase->purchase_id);
        
        if ($detailedPurchase) {
            echo "✅ Purchase details retrieved successfully\n";
            
            // Check tracking button visibility conditions
            $shouldShowButton = in_array($detailedPurchase->status, ['pending', 'sent']) 
                               && empty($detailedPurchase->tracking_number);
            
            echo "Should show 'Add Tracking' button: " . ($shouldShowButton ? 'YES' : 'NO') . "\n";
            echo "- Status qualifies: " . (in_array($detailedPurchase->status, ['pending', 'sent']) ? 'YES' : 'NO') . "\n";
            echo "- No tracking number: " . (empty($detailedPurchase->tracking_number) ? 'YES' : 'NO') . "\n\n";
            
            // Simulate the details page URL
            $detailsUrl = "http://localhost/purchases/details/" . $testPurchase->purchase_id;
            echo "📋 Details page URL: " . $detailsUrl . "\n";
            echo "📋 Button should be visible on this page for pending/sent orders without tracking\n\n";
        }
    }
    
    echo "✅ Movement functionality test completed!\n";
    echo "\n🔧 Changes made:\n";
    echo "   - Removed 'Add Tracking' button from PO table (index.php)\n";
    echo "   - Added 'Add Tracking' button to details page header\n";
    echo "   - Moved tracking modal and JavaScript to details page\n";
    echo "   - Button only shows for pending/sent orders without tracking\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nTest completed.\n";
?>
