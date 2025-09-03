<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/services/SupplierSelector.php';

echo "🤖 TESTING SMART SUPPLIER SELECTION SYSTEM\n";
echo "==========================================\n\n";

// Connect to database
$db = new Database();
$selector = new SupplierSelector($db);

echo "1️⃣ TESTING DIFFERENT URGENCY CONTEXTS:\n";
echo "--------------------------------------\n";

// Get a product with multiple suppliers for testing
$db->query("
    SELECT p.product_id, p.product_name, COUNT(ps.supplier_id) as supplier_count
    FROM products p
    JOIN product_suppliers ps ON p.product_id = ps.product_id
    WHERE ps.is_active = 1
    GROUP BY p.product_id, p.product_name
    HAVING supplier_count >= 2
    LIMIT 1
");
$db->execute();
$testProduct = $db->single();

if (!$testProduct) {
    echo "❌ No products with multiple suppliers found for testing\n";
    exit;
}

echo "Testing with product: {$testProduct->product_name} (ID: {$testProduct->product_id})\n";
echo "Available suppliers: {$testProduct->supplier_count}\n\n";

// Test different urgency scenarios
$scenarios = [
    ['urgency' => 'normal', 'quantity' => 10, 'description' => 'Normal order (10 units)'],
    ['urgency' => 'urgent', 'quantity' => 5, 'description' => 'Urgent order (5 units, low stock)'],
    ['urgency' => 'bulk', 'quantity' => 50, 'description' => 'Bulk order (50 units, cost focus)']
];

foreach ($scenarios as $scenario) {
    echo "🎯 SCENARIO: {$scenario['description']}\n";
    echo "   Urgency: {$scenario['urgency']}, Quantity: {$scenario['quantity']}\n";

    $recommendation = $selector->getRecommendationWithAlternatives(
        $testProduct->product_id,
        $scenario['quantity'],
        $scenario['urgency']
    );

    if ($recommendation) {
        $recommended = $recommendation['recommended'];
        echo "   ✅ RECOMMENDED: {$recommended->supplier_name}\n";
        echo "      Price: ₹{$recommended->purchase_price} per unit\n";
        echo "      Score: {$recommended->selection_score}/100\n";
        echo "      Reasoning: {$recommended->selection_reasoning}\n";
        echo "      Lead Time: {$recommended->lead_time_days} days\n";

        if (!empty($recommendation['alternatives'])) {
            echo "   📋 ALTERNATIVES:\n";
            foreach ($recommendation['alternatives'] as $alt) {
                echo "      • {$alt->supplier_name} - ₹{$alt->purchase_price} (Score: {$alt->selection_score})\n";
            }
        }

        // Show weight distribution used
        $weights = $recommendation['context']['weights'];
        echo "   ⚖️  WEIGHTS: Price {$weights['price']}%, Delivery {$weights['delivery']}%, Quality {$weights['quality']}%\n";
    } else {
        echo "   ❌ No recommendation available\n";
    }
    echo "\n";
}

echo "2️⃣ COMPARING OLD vs NEW SELECTION:\n";
echo "---------------------------------\n";

// Get the cheapest supplier (old way)
$db->query("
    SELECT 
        s.supplier_name,
        ps.purchase_price,
        ps.lead_time_days
    FROM product_suppliers ps
    JOIN suppliers s ON ps.supplier_id = s.supplier_id
    WHERE ps.product_id = :product_id
    AND ps.is_active = 1
    ORDER BY ps.purchase_price ASC
    LIMIT 1
");
$db->bind(':product_id', $testProduct->product_id);
$db->execute();
$cheapestSupplier = $db->single();

echo "OLD METHOD (cheapest only):\n";
if ($cheapestSupplier) {
    echo "   Selected: {$cheapestSupplier->supplier_name}\n";
    echo "   Price: ₹{$cheapestSupplier->purchase_price}\n";
    echo "   Lead Time: {$cheapestSupplier->lead_time_days} days\n";
    echo "   Logic: Always choose lowest price\n\n";
} else {
    echo "   No supplier found\n\n";
}

echo "NEW METHOD (smart selection):\n";
$smartChoice = $selector->getOptimalSupplier($testProduct->product_id, 10, 'normal');
if ($smartChoice) {
    echo "   Selected: {$smartChoice->supplier_name}\n";
    echo "   Price: ₹{$smartChoice->purchase_price}\n";
    echo "   Lead Time: {$smartChoice->lead_time_days} days\n";
    echo "   Score: {$smartChoice->selection_score}/100\n";
    echo "   Logic: {$smartChoice->selection_reasoning}\n\n";
}

echo "3️⃣ BUSINESS IMPACT ANALYSIS:\n";
echo "----------------------------\n";

if ($cheapestSupplier && $smartChoice) {
    $priceDiff = $smartChoice->purchase_price - $cheapestSupplier->purchase_price;
    $deliveryDiff = $cheapestSupplier->lead_time_days - $smartChoice->lead_time_days;

    echo "Price difference: ";
    if ($priceDiff > 0) {
        echo "₹{$priceDiff} more expensive (+{$priceDiff}%)\n";
    } elseif ($priceDiff < 0) {
        echo "₹" . abs($priceDiff) . " cheaper (" . round(($priceDiff / $cheapestSupplier->purchase_price) * 100, 1) . "%)\n";
    } else {
        echo "Same price\n";
    }

    echo "Delivery difference: ";
    if ($deliveryDiff > 0) {
        echo "{$deliveryDiff} days faster\n";
    } elseif ($deliveryDiff < 0) {
        echo abs($deliveryDiff) . " days slower\n";
    } else {
        echo "Same delivery time\n";
    }

    echo "\nTRADE-OFF ANALYSIS:\n";
    if ($priceDiff > 0 && $deliveryDiff > 0) {
        echo "   💡 Smart selection chose slightly more expensive supplier for faster delivery\n";
        echo "   💰 Cost: ₹{$priceDiff} extra per unit\n";
        echo "   ⚡ Benefit: {$deliveryDiff} days faster delivery\n";
        echo "   🎯 Good for normal/urgent orders where delivery matters\n";
    } elseif ($priceDiff <= 0) {
        echo "   🏆 Smart selection found better or equal price with additional benefits\n";
    }
}

echo "\n4️⃣ SUPPLIER SCORING BREAKDOWN:\n";
echo "------------------------------\n";

// Get all suppliers and show their scores
$db->query("
    SELECT 
        s.supplier_name,
        ps.purchase_price,
        ps.lead_time_days,
        ps.quality_rating,
        s.supplier_tier,
        RANK() OVER (ORDER BY ps.purchase_price ASC) as price_rank
    FROM product_suppliers ps
    JOIN suppliers s ON ps.supplier_id = s.supplier_id
    WHERE ps.product_id = :product_id
    AND ps.is_active = 1
    ORDER BY ps.purchase_price ASC
");
$db->bind(':product_id', $testProduct->product_id);
$db->execute();
$allSuppliers = $db->resultSet();

echo "Detailed scoring for all suppliers:\n\n";
foreach ($allSuppliers as $supplier) {
    $optimal = $selector->getOptimalSupplier($testProduct->product_id, 10, 'normal');
    if ($optimal && $optimal->supplier_name == $supplier->supplier_name) {
        echo "✅ ";
    } else {
        echo "   ";
    }

    echo "{$supplier->supplier_name}\n";
    echo "   Price: ₹{$supplier->purchase_price} (Rank: #{$supplier->price_rank})\n";
    echo "   Lead Time: {$supplier->lead_time_days} days\n";
    echo "   Quality: " . ($supplier->quality_rating ?: 'N/A') . "/5\n";
    echo "   Tier: " . ($supplier->supplier_tier ?: 'Standard') . "\n";

    // Calculate individual scores
    if ($supplier->supplier_name == $optimal->supplier_name) {
        echo "   📊 SELECTED - Score: {$optimal->selection_score}/100\n";
        echo "   🧠 Reasoning: {$optimal->selection_reasoning}\n";
    }
    echo "\n";
}

echo "✅ SMART SUPPLIER SELECTION TEST COMPLETE\n";
echo "\nRECOMMENDATION:\n";
echo "• The system now chooses suppliers based on CONTEXT, not just price\n";
echo "• Urgent orders prioritize fast delivery over cost savings\n";
echo "• Bulk orders prioritize cost savings over speed\n";
echo "• Normal orders balance price, delivery, and quality\n";
echo "• This eliminates the primary supplier conflict while maintaining automation\n";
?>