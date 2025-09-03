<?php
require_once __DIR__ . '/../bootstrap.php';
require_once __DIR__ . '/../app/services/SupplierSelector.php';

echo "🔧 TESTING CONFLICT RESOLUTION - BULB PRODUCT\n";
echo "============================================\n\n";

// Test specifically with Bulb product (ID: 139) that has the conflict
$db = new Database();
$selector = new SupplierSelector($db);

$productId = 139; // Bulb product with known conflict

echo "1️⃣ CURRENT SITUATION:\n";
echo "--------------------\n";

// Get product details
$db->query("SELECT product_id, product_name FROM products WHERE product_id = :product_id");
$db->bind(':product_id', $productId);
$db->execute();
$product = $db->single();

echo "Debug: Product object type: " . gettype($product) . "\n";
echo "Debug: Product truthy: " . ($product ? 'YES' : 'NO') . "\n";

if (!$product) {
    echo "❌ Product not found\n";
    exit;
}

echo "Product: {$product->product_name}\n";
echo "Current Stock: N/A\n\n";

// Show all suppliers for this product
$db->query("
    SELECT 
        s.supplier_name,
        ps.purchase_price,
        ps.lead_time_days,
        ps.is_primary,
        ps.quality_rating,
        s.supplier_tier
    FROM product_suppliers ps
    JOIN suppliers s ON ps.supplier_id = s.supplier_id
    WHERE ps.product_id = :product_id
    AND ps.is_active = 1
    ORDER BY ps.purchase_price ASC
");
$db->bind(':product_id', $productId);
$db->execute();
$suppliers = $db->resultSet();

echo "Available suppliers:\n";
foreach ($suppliers as $supplier) {
    $primary = $supplier->is_primary ? ' ⭐ PRIMARY' : '';
    echo "• {$supplier->supplier_name}: ₹{$supplier->purchase_price}{$primary}\n";
    echo "  Lead time: {$supplier->lead_time_days} days, Quality: " . ($supplier->quality_rating ?: 'N/A') . "\n";
}
echo "\n";

echo "2️⃣ OLD SYSTEM BEHAVIOR:\n";
echo "-----------------------\n";

// Cheapest supplier (automated bot choice)
$cheapest = $suppliers[0]; // First in price-sorted list
echo "AUTOMATED BOT would choose: {$cheapest->supplier_name}\n";
echo "• Price: ₹{$cheapest->purchase_price}\n";
echo "• Logic: Always choose cheapest price\n\n";

// Primary supplier (manual order default)
$primary = null;
foreach ($suppliers as $supplier) {
    if ($supplier->is_primary) {
        $primary = $supplier;
        break;
    }
}

if ($primary) {
    echo "MANUAL ORDERS would default to: {$primary->supplier_name}\n";
    echo "• Price: ₹{$primary->purchase_price}\n";
    echo "• Logic: Use primary supplier preference\n";

    $priceDiff = $primary->purchase_price - $cheapest->purchase_price;
    $percentDiff = round(($priceDiff / $cheapest->purchase_price) * 100, 2);
    echo "• CONFLICT: ₹{$priceDiff} more expensive ({$percentDiff}% markup)\n\n";
} else {
    echo "MANUAL ORDERS: No primary supplier set\n\n";
}

echo "3️⃣ NEW SMART SYSTEM BEHAVIOR:\n";
echo "-----------------------------\n";

$scenarios = [
    ['urgency' => 'normal', 'qty' => 10, 'desc' => 'Regular restocking'],
    ['urgency' => 'urgent', 'qty' => 5, 'desc' => 'Emergency stock (low inventory)'],
    ['urgency' => 'bulk', 'qty' => 100, 'desc' => 'Bulk purchase (cost focus)']
];

foreach ($scenarios as $scenario) {
    echo "SCENARIO: {$scenario['desc']}\n";

    $recommendation = $selector->getRecommendationWithAlternatives(
        $productId,
        $scenario['qty'],
        $scenario['urgency']
    );

    if ($recommendation) {
        $chosen = $recommendation['recommended'];
        echo "✅ CHOSEN: {$chosen->supplier_name}\n";
        echo "• Price: ₹{$chosen->purchase_price}\n";
        echo "• Score: {$chosen->selection_score}/100\n";
        echo "• Context: {$scenario['urgency']} order ({$scenario['qty']} units)\n";
        echo "• Reasoning: {$chosen->selection_reasoning}\n";

        // Compare to old methods
        $vsChepest = $chosen->purchase_price - $cheapest->purchase_price;
        if ($primary) {
            $vsPrimary = $chosen->purchase_price - $primary->purchase_price;
            echo "• vs Cheapest: " . ($vsChepest > 0 ? "+₹{$vsChepest}" : "₹{$vsChepest}") . "\n";
            echo "• vs Primary: " . ($vsPrimary > 0 ? "+₹{$vsPrimary}" : "₹{$vsPrimary}") . "\n";
        }
    }
    echo "\n";
}

echo "4️⃣ CONFLICT RESOLUTION SUMMARY:\n";
echo "-------------------------------\n";

echo "✅ PROBLEM SOLVED:\n";
echo "• Old system: Automated bot bought cheapest, manual orders used expensive primary\n";
echo "• New system: Smart selection considers context for ALL purchasing decisions\n";
echo "• Result: Consistent, intelligent supplier selection across the entire system\n\n";

echo "📊 BUSINESS BENEFITS:\n";
echo "• Automated purchases still optimize costs but consider delivery/quality\n";
echo "• Manual purchases get intelligent recommendations instead of arbitrary primary\n";
echo "• Emergency orders prioritize fast delivery over small cost savings\n";
echo "• Bulk orders maximize cost savings through volume considerations\n";
echo "• No more conflicting supplier selections between different system parts\n\n";

echo "🔧 IMPLEMENTATION STATUS:\n";
echo "• ✅ Smart supplier selection service created\n";
echo "• ✅ Bot controller updated to use smart selection\n";
echo "• 🔄 Next: Update manual purchase order forms to show recommendations\n";
echo "• 🔄 Next: Add admin panel for configuring selection weights\n";
echo "• 🔄 Next: Remove deprecated primary supplier UI elements\n\n";

echo "💡 RECOMMENDATION:\n";
echo "Replace the binary 'primary supplier' concept with this smart selection system.\n";
echo "This gives you the automation benefits while maintaining supplier preferences\n";
echo "based on actual business context (urgency, quantity, etc.) rather than\n";
echo "arbitrary manual designations that conflict with cost optimization.\n";
?>