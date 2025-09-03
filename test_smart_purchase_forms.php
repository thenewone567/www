<?php
/**
 * Test Smart Purchase Forms Integration
 * Tests the complete smart supplier recommendation system in purchase forms
 */

// Test the purchase form helper service
echo "=== Testing Purchase Form Helper Service ===\n\n";

require_once 'bootstrap.php';
require_once 'app/services/PurchaseFormHelper.php';

$database = new Database();
$helper = new PurchaseFormHelper($database);

// Test product IDs that we know have suppliers
$testProducts = [1, 2, 3]; // Adjust based on your products

foreach ($testProducts as $productId) {
    echo "Testing Product ID: $productId\n";
    echo str_repeat('-', 40) . "\n";

    try {
        // Test normal urgency
        $normalRecommendations = $helper->getSuppliersWithRecommendations($productId, 10, 'normal');
        echo "Normal Urgency Recommendations:\n";
        foreach ($normalRecommendations as $supplier) {
            echo "  - {$supplier->supplier_name}: ₹{$supplier->purchase_price} ";
            if ($supplier->is_recommended) {
                echo "⭐ RECOMMENDED ({$supplier->recommendation_badge})";
            }
            echo "\n";
        }

        // Test urgent context
        $urgentRecommendations = $helper->getSuppliersWithRecommendations($productId, 10, 'urgent');
        echo "\nUrgent Context Recommendations:\n";
        foreach ($urgentRecommendations as $supplier) {
            echo "  - {$supplier->supplier_name}: ₹{$supplier->purchase_price} ";
            if ($supplier->is_recommended) {
                echo "⭐ RECOMMENDED ({$supplier->recommendation_badge})";
            }
            echo "\n";
        }

    } catch (Exception $e) {
        echo "Error testing product $productId: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

// Test the smart recommendations API endpoint
echo "=== Testing Smart Recommendations API ===\n\n";

$testApiUrl = 'http://localhost/api/smartSupplierRecommendations.php';

foreach ($testProducts as $productId) {
    echo "API Test for Product ID: $productId\n";
    echo str_repeat('-', 40) . "\n";

    $apiUrl = $testApiUrl . "?product_id=$productId&quantity=10&urgency=normal";

    // Use curl to test the API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $data = json_decode($response, true);
        if ($data && $data['success']) {
            echo "API Response: SUCCESS\n";
            echo "Context: {$data['context']['urgency']} urgency, {$data['context']['quantity']} units\n";
            echo "Suppliers:\n";
            foreach ($data['suppliers'] as $supplier) {
                echo "  - {$supplier['supplier_name']}: ₹{$supplier['price']} ";
                if ($supplier['is_recommended']) {
                    echo "⭐ RECOMMENDED";
                }
                echo "\n";
            }
            if (!empty($data['reasoning'])) {
                echo "Reasoning: {$data['reasoning']}\n";
            }
        } else {
            echo "API Error: " . ($data['message'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "HTTP Error: $httpCode\n";
        echo "Response: $response\n";
    }

    echo "\n";
}

// Test bot integration
echo "=== Testing Bot Integration ===\n\n";

require_once 'app/controllers/BotController.php';

$bot = new BotController();

foreach ($testProducts as $productId) {
    echo "Bot Test for Product ID: $productId\n";
    echo str_repeat('-', 40) . "\n";

    try {
        // Test normal bot operation
        $normalChoice = $bot->testFindOptimalSupplier($productId, 10, 'normal');
        echo "Normal Context Choice:\n";
        echo "  Supplier: {$normalChoice->supplier_name}\n";
        echo "  Price: ₹{$normalChoice->purchase_price}\n";
        echo "  Score: {$normalChoice->selection_score}\n";
        echo "  Reasoning: {$normalChoice->selection_reasoning}\n";

        // Test urgent context
        $urgentChoice = $bot->testFindOptimalSupplier($productId, 10, 'urgent');
        echo "\nUrgent Context Choice:\n";
        echo "  Supplier: {$urgentChoice->supplier_name}\n";
        echo "  Price: ₹{$urgentChoice->purchase_price}\n";
        echo "  Score: {$urgentChoice->selection_score}\n";
        echo "  Reasoning: {$urgentChoice->selection_reasoning}\n";

    } catch (Exception $e) {
        echo "Error testing bot for product $productId: " . $e->getMessage() . "\n";
    }

    echo "\n";
}

// Test the specific Bulb conflict resolution
echo "=== Testing Bulb Conflict Resolution ===\n\n";

try {
    // Find the Bulb product ID
    $db = new Database();
    $bulbQuery = "SELECT product_id, product_name FROM products WHERE product_name LIKE '%bulb%' LIMIT 1";
    $bulbResult = $db->query($bulbQuery);

    if ($bulbResult && is_array($bulbResult) && count($bulbResult) > 0) {
        $bulbProduct = $bulbResult[0];
        $bulbId = $bulbProduct['product_id'];

        echo "Testing Bulb Product: {$bulbProduct['product_name']} (ID: $bulbId)\n";
        echo str_repeat('-', 50) . "\n";

        // Test manual form helper
        $manualRecommendations = $helper->getSuppliersWithRecommendations($bulbId, 10, 'normal');
        echo "Manual Form Recommendations:\n";
        foreach ($manualRecommendations as $supplier) {
            echo "  - {$supplier->supplier_name}: ₹{$supplier->purchase_price} ";
            if ($supplier->is_recommended) {
                echo "⭐ RECOMMENDED ({$supplier->recommendation_badge})";
            }
            echo "\n";
        }

        // Test bot choice
        $botChoice = $bot->testFindOptimalSupplier($bulbId, 10, 'normal');
        echo "\nBot Automated Choice:\n";
        echo "  Supplier: {$botChoice->supplier_name}\n";
        echo "  Price: ₹{$botChoice->purchase_price}\n";
        echo "  Score: {$botChoice->selection_score}\n";
        echo "  Reasoning: {$botChoice->selection_reasoning}\n";

        // Verify consistency
        $topManualChoice = $manualRecommendations[0] ?? null;
        if ($topManualChoice && $topManualChoice->supplier_id == $botChoice->supplier_id) {
            echo "\n✅ SUCCESS: Manual and automated systems now choose the same supplier!\n";
            echo "   This resolves the original conflict between primary supplier and cheapest price.\n";
        } else {
            echo "\n⚠️  WARNING: Manual and automated systems still choose different suppliers.\n";
            if ($topManualChoice) {
                echo "   Manual: {$topManualChoice->supplier_name} (₹{$topManualChoice->purchase_price})\n";
            }
            echo "   Bot: {$botChoice->supplier_name} (₹{$botChoice->purchase_price})\n";
        }

    } else {
        echo "No Bulb product found in database.\n";
    }

} catch (Exception $e) {
    echo "Error testing Bulb conflict: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "Smart supplier recommendation system integration tested.\n";
echo "If you see ✅ SUCCESS messages, the conflict resolution is working properly.\n";
?>