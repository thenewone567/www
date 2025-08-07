<?php
require_once __DIR__ . '/bootstrap.php';

// Original scenario
$currentStock = 100;
$currentPrice = 100;
$newQuantity = 50;
$originalNewPrice = 120;

// Discount scenarios
$discountPercentages = [5, 10, 15, 20, 25];
$profitMargin = 50; // 50% profit margin

echo "<h1>🎁 Impact of Discount on 2nd Batch Purchase</h1>";
echo "<div style='font-family: Arial; margin: 20px;'>";

echo "<div style='border: 2px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
echo "<h2 style='color: #28a745;'>📊 Base Scenario</h2>";
echo "<p><strong>Current Stock:</strong> {$currentStock} units @ " . formatCurrency($currentPrice) . " each</p>";
echo "<p><strong>New Purchase:</strong> {$newQuantity} units @ " . formatCurrency($originalNewPrice) . " each (before discount)</p>";
echo "</div>";

echo "<h2>💰 Discount Impact Analysis</h2>";

foreach ($discountPercentages as $discount) {
    $discountedPrice = $originalNewPrice * (1 - $discount / 100);
    $discountAmount = $originalNewPrice - $discountedPrice;

    // Calculate average method with discounted price
    $averageMethod = calculateAveragePrice($currentStock, $currentPrice, $newQuantity, $discountedPrice);
    $originalAverageMethod = calculateAveragePrice($currentStock, $currentPrice, $newQuantity, $originalNewPrice);

    // Calculate sale prices
    $salePriceWithDiscount = $averageMethod['average_price'] * (1 + $profitMargin / 100);
    $salePriceWithoutDiscount = $originalAverageMethod['average_price'] * (1 + $profitMargin / 100);
    $salePriceDifference = $salePriceWithoutDiscount - $salePriceWithDiscount;

    // Calculate savings
    $totalSavings = $newQuantity * $discountAmount;

    echo "<div style='border: 1px solid #007bff; padding: 15px; margin: 15px 0; border-radius: 5px; background: #f8f9fa;'>";
    echo "<h3 style='color: #007bff;'>🏷️ {$discount}% Discount Scenario</h3>";

    echo "<div style='display: flex; gap: 20px; flex-wrap: wrap;'>";

    // Purchase Details
    echo "<div style='flex: 1; min-width: 300px;'>";
    echo "<h4>📦 Purchase Details:</h4>";
    echo "<p><strong>Original Price:</strong> " . formatCurrency($originalNewPrice) . " per unit</p>";
    echo "<p><strong>Discount:</strong> {$discount}% (-" . formatCurrency($discountAmount) . " per unit)</p>";
    echo "<p><strong>Discounted Price:</strong> <span style='color: #28a745; font-weight: bold;'>" . formatCurrency($discountedPrice) . "</span> per unit</p>";
    echo "<p><strong>Total Savings:</strong> <span style='color: #28a745; font-weight: bold;'>" . formatCurrency($totalSavings) . "</span></p>";
    echo "</div>";

    // Average Method Impact
    echo "<div style='flex: 1; min-width: 300px;'>";
    echo "<h4>⚖️ Average Method Impact:</h4>";
    echo "<p><strong>New Average Cost:</strong> " . formatCurrency($averageMethod['average_price']) . "</p>";
    echo "<p><strong>Original Average Cost:</strong> " . formatCurrency($originalAverageMethod['average_price']) . "</p>";
    echo "<p><strong>Cost Reduction:</strong> <span style='color: #28a745;'>-" . formatCurrency($originalAverageMethod['average_price'] - $averageMethod['average_price']) . "</span></p>";
    echo "</div>";

    echo "</div>";

    // Sale Price Impact
    echo "<h4>💲 Sale Price Impact (50% Margin):</h4>";
    echo "<div style='background: white; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "<p><strong>Sale Price with Discount:</strong> <span style='color: #28a745; font-size: 1.1em;'>" . formatCurrency($salePriceWithDiscount) . "</span></p>";
    echo "<p><strong>Sale Price without Discount:</strong> <span style='color: #dc3545;'>" . formatCurrency($salePriceWithoutDiscount) . "</span></p>";
    echo "<p><strong>Customer Saves:</strong> <span style='color: #28a745; font-weight: bold;'>" . formatCurrency($salePriceDifference) . "</span> per unit</p>";
    echo "</div>";

    // Business Benefits
    echo "<h4>🎯 Business Benefits:</h4>";
    echo "<ul>";
    echo "<li><strong>Direct Savings:</strong> " . formatCurrency($totalSavings) . " on purchase</li>";
    echo "<li><strong>Competitive Advantage:</strong> Can sell ₹" . number_format($salePriceDifference, 2) . " cheaper than without discount</li>";
    echo "<li><strong>Market Position:</strong> More competitive pricing while maintaining 50% margin</li>";
    echo "<li><strong>Customer Appeal:</strong> Lower prices attract more customers</li>";
    echo "</ul>";

    echo "</div>";
}

// Comparison table
echo "<div style='border: 2px solid #6f42c1; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
echo "<h2 style='color: #6f42c1;'>📊 Discount Comparison Table</h2>";

echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
echo "<tr style='background: #6f42c1; color: white;'>";
echo "<th style='border: 1px solid #ddd; padding: 12px;'>Discount %</th>";
echo "<th style='border: 1px solid #ddd; padding: 12px;'>Purchase Price</th>";
echo "<th style='border: 1px solid #ddd; padding: 12px;'>Average Cost</th>";
echo "<th style='border: 1px solid #ddd; padding: 12px;'>Sale Price</th>";
echo "<th style='border: 1px solid #ddd; padding: 12px;'>Total Savings</th>";
echo "<th style='border: 1px solid #ddd; padding: 12px;'>Customer Benefit</th>";
echo "</tr>";

// No discount row
$noDiscountAverage = calculateAveragePrice($currentStock, $currentPrice, $newQuantity, $originalNewPrice);
$noDiscountSalePrice = $noDiscountAverage['average_price'] * 1.5;

echo "<tr style='background: #f8f9fa;'>";
echo "<td style='border: 1px solid #ddd; padding: 12px;'><strong>0%</strong></td>";
echo "<td style='border: 1px solid #ddd; padding: 12px;'>" . formatCurrency($originalNewPrice) . "</td>";
echo "<td style='border: 1px solid #ddd; padding: 12px;'>" . formatCurrency($noDiscountAverage['average_price']) . "</td>";
echo "<td style='border: 1px solid #ddd; padding: 12px;'>" . formatCurrency($noDiscountSalePrice) . "</td>";
echo "<td style='border: 1px solid #ddd; padding: 12px;'>₹0</td>";
echo "<td style='border: 1px solid #ddd; padding: 12px;'>₹0</td>";
echo "</tr>";

foreach ($discountPercentages as $discount) {
    $discountedPrice = $originalNewPrice * (1 - $discount / 100);
    $averageMethod = calculateAveragePrice($currentStock, $currentPrice, $newQuantity, $discountedPrice);
    $salePrice = $averageMethod['average_price'] * 1.5;
    $totalSavings = $newQuantity * ($originalNewPrice - $discountedPrice);
    $customerBenefit = $noDiscountSalePrice - $salePrice;

    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 12px;'><strong>{$discount}%</strong></td>";
    echo "<td style='border: 1px solid #ddd; padding: 12px;'>" . formatCurrency($discountedPrice) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 12px;'>" . formatCurrency($averageMethod['average_price']) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 12px;'>" . formatCurrency($salePrice) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 12px; color: #28a745;'>" . formatCurrency($totalSavings) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 12px; color: #28a745;'>" . formatCurrency($customerBenefit) . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// Separate Batches vs Average Method with Discount
echo "<div style='border: 2px solid #fd7e14; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
echo "<h2 style='color: #fd7e14;'>⚖️ Separate Batches vs Average Method (20% Discount Example)</h2>";

$discountExample = 20;
$discountedPriceExample = $originalNewPrice * (1 - $discountExample / 100);
$averageExample = calculateAveragePrice($currentStock, $currentPrice, $newQuantity, $discountedPriceExample);

echo "<div style='display: flex; gap: 20px; flex-wrap: wrap;'>";

echo "<div style='flex: 1; min-width: 300px; background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<h3>📦 Separate Batches Method:</h3>";
echo "<p><strong>Batch-1:</strong> {$currentStock} units @ " . formatCurrency($currentPrice) . "</p>";
echo "<p><strong>Sale Price Batch-1:</strong> " . formatCurrency($currentPrice * 1.5) . " (50% margin)</p>";
echo "<p><strong>Batch-2:</strong> {$newQuantity} units @ " . formatCurrency($discountedPriceExample) . "</p>";
echo "<p><strong>Sale Price Batch-2:</strong> " . formatCurrency($discountedPriceExample * 1.5) . " (50% margin)</p>";
echo "<p><strong>Advantage:</strong> Can offer different prices, maximize profit on discounted stock</p>";
echo "</div>";

echo "<div style='flex: 1; min-width: 300px; background: #f8f9fa; padding: 15px; border-radius: 5px;'>";
echo "<h3>⚖️ Average Method:</h3>";
echo "<p><strong>All Stock:</strong> " . formatCurrency($averageExample['average_price']) . " average cost</p>";
echo "<p><strong>Uniform Sale Price:</strong> " . formatCurrency($averageExample['average_price'] * 1.5) . " (50% margin)</p>";
echo "<p><strong>Advantage:</strong> Simplified pricing, consistent customer experience</p>";
echo "<p><strong>Customer pays same price</strong> for all units regardless of when you bought them</p>";
echo "</div>";

echo "</div>";
echo "</div>";

// Strategic recommendations
echo "<div style='border: 2px solid #17a2b8; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
echo "<h2 style='color: #17a2b8;'>💡 Strategic Recommendations</h2>";

echo "<h3>🎯 When to Use Average Method with Discounts:</h3>";
echo "<ul>";
echo "<li><strong>Consistent Pricing Strategy:</strong> Customers always pay the same fair price</li>";
echo "<li><strong>Simple Operations:</strong> No need to track which units came from which batch</li>";
echo "<li><strong>Pass Savings to Customers:</strong> Discounts automatically reduce average cost and sale prices</li>";
echo "<li><strong>Competitive Advantage:</strong> Lower average costs = lower sale prices = better market position</li>";
echo "</ul>";

echo "<h3>📈 Business Impact of Supplier Discounts:</h3>";
echo "<ul>";
echo "<li><strong>Immediate Benefit:</strong> Reduced purchase costs improve cash flow</li>";
echo "<li><strong>Long-term Benefit:</strong> Lower average costs enable competitive pricing</li>";
echo "<li><strong>Customer Loyalty:</strong> Passing savings to customers builds relationships</li>";
echo "<li><strong>Market Share:</strong> Competitive prices can increase sales volume</li>";
echo "</ul>";

echo "</div>";

echo "</div>";
?>