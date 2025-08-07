<?php
require_once __DIR__ . '/bootstrap.php';

// Example scenario from the documentation
$currentStock = 100;
$currentPrice = 100;
$newQuantity = 50;
$newPrice = 120;
$profitMargin = 50; // 50% profit margin

echo "<h2>Sale Price Calculation with 50% Profit Margin</h2>";
echo "<div style='font-family: Arial; margin: 20px;'>";

// Calculate average price method
$averageMethod = calculateAveragePrice($currentStock, $currentPrice, $newQuantity, $newPrice);

echo "<div style='border: 2px solid #007bff; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
echo "<h3 style='color: #007bff;'>📊 Average Price Method Analysis</h3>";

echo "<h4>📦 Inventory Details:</h4>";
echo "<p><strong>Current Stock:</strong> {$currentStock} units @ " . formatCurrency($currentPrice) . " each = " . formatCurrency($currentStock * $currentPrice) . "</p>";
echo "<p><strong>New Purchase:</strong> {$newQuantity} units @ " . formatCurrency($newPrice) . " each = " . formatCurrency($newQuantity * $newPrice) . "</p>";
echo "<p><strong>Total Stock:</strong> {$averageMethod['total_quantity']} units</p>";
echo "<p><strong>Average Cost:</strong> " . formatCurrency($averageMethod['average_price']) . " per unit</p>";

echo "<hr style='margin: 20px 0;'>";

echo "<h4>💰 Sale Price Calculations (50% Profit Margin):</h4>";

// Before - using current price
$salePriceBefore = $currentPrice * (1 + $profitMargin / 100);
echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-left: 4px solid #dc3545;'>";
echo "<h5 style='color: #dc3545;'>BEFORE (Using Current Price):</h5>";
echo "<p><strong>Cost Price:</strong> " . formatCurrency($currentPrice) . "</p>";
echo "<p><strong>Profit Margin:</strong> {$profitMargin}%</p>";
echo "<p><strong>Sale Price:</strong> " . formatCurrency($currentPrice) . " + ({$profitMargin}% of " . formatCurrency($currentPrice) . ") = <strong style='color: #dc3545; font-size: 1.2em;'>" . formatCurrency($salePriceBefore) . "</strong></p>";
echo "</div>";

// After - using average price
$salePriceAfter = $averageMethod['average_price'] * (1 + $profitMargin / 100);
echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0; border-left: 4px solid #28a745;'>";
echo "<h5 style='color: #28a745;'>AFTER (Using Average Price):</h5>";
echo "<p><strong>Average Cost:</strong> " . formatCurrency($averageMethod['average_price']) . "</p>";
echo "<p><strong>Profit Margin:</strong> {$profitMargin}%</p>";
echo "<p><strong>Sale Price:</strong> " . formatCurrency($averageMethod['average_price']) . " + ({$profitMargin}% of " . formatCurrency($averageMethod['average_price']) . ") = <strong style='color: #28a745; font-size: 1.2em;'>" . formatCurrency($salePriceAfter) . "</strong></p>";
echo "</div>";

echo "<hr style='margin: 20px 0;'>";

echo "<h4>📈 Impact Analysis:</h4>";
$priceDifference = $salePriceAfter - $salePriceBefore;
$percentageIncrease = (($salePriceAfter - $salePriceBefore) / $salePriceBefore) * 100;

echo "<div style='background: #fff3cd; padding: 15px; margin: 10px 0; border: 1px solid #ffeaa7; border-radius: 5px;'>";
echo "<p><strong>Sale Price Increase:</strong> " . formatCurrency($priceDifference) . "</p>";
echo "<p><strong>Percentage Increase:</strong> " . number_format($percentageIncrease, 2) . "%</p>";
echo "<p><strong>Impact:</strong> Your sale price increases by " . formatCurrency($priceDifference) . " due to the higher average cost</p>";
echo "</div>";

echo "<h4>💡 Business Insights:</h4>";
echo "<ul>";
echo "<li><strong>Profit per unit remains constant at 50%</strong> but sale price adjusts to maintain margin</li>";
echo "<li><strong>Revenue per unit increases</strong> by " . formatCurrency($priceDifference) . "</li>";
echo "<li><strong>Competitive impact:</strong> Higher sale price may affect market competitiveness</li>";
echo "<li><strong>Customer impact:</strong> " . number_format($percentageIncrease, 1) . "% price increase for customers</li>";
echo "</ul>";

echo "</div>";

// Additional scenario comparison
echo "<div style='border: 2px solid #6f42c1; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
echo "<h3 style='color: #6f42c1;'>🔄 Multiple Profit Margin Scenarios</h3>";

$scenarios = [
    ['margin' => 25, 'description' => 'Conservative'],
    ['margin' => 50, 'description' => 'Standard'],
    ['margin' => 75, 'description' => 'Premium'],
    ['margin' => 100, 'description' => 'High-end']
];

echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>Profit Margin</th>";
echo "<th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>Before (Current Price)</th>";
echo "<th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>After (Average Price)</th>";
echo "<th style='border: 1px solid #ddd; padding: 12px; text-align: left;'>Difference</th>";
echo "</tr>";

foreach ($scenarios as $scenario) {
    $margin = $scenario['margin'];
    $beforePrice = $currentPrice * (1 + $margin / 100);
    $afterPrice = $averageMethod['average_price'] * (1 + $margin / 100);
    $diff = $afterPrice - $beforePrice;

    echo "<tr>";
    echo "<td style='border: 1px solid #ddd; padding: 12px;'>{$margin}% ({$scenario['description']})</td>";
    echo "<td style='border: 1px solid #ddd; padding: 12px;'>" . formatCurrency($beforePrice) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 12px;'>" . formatCurrency($afterPrice) . "</td>";
    echo "<td style='border: 1px solid #ddd; padding: 12px; color: " . ($diff > 0 ? '#28a745' : '#dc3545') . ";'>+" . formatCurrency($diff) . "</td>";
    echo "</tr>";
}

echo "</table>";
echo "</div>";

// Comparison with separate batches method
echo "<div style='border: 2px solid #17a2b8; padding: 20px; margin: 20px 0; border-radius: 8px;'>";
echo "<h3 style='color: #17a2b8;'>⚖️ Separate Batches vs Average Method (50% Margin)</h3>";

echo "<h4>Separate Batches Method:</h4>";
echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0;'>";
echo "<p><strong>Batch-1 Sale Price:</strong> " . formatCurrency($currentPrice * 1.5) . " (based on " . formatCurrency($currentPrice) . " cost)</p>";
echo "<p><strong>Batch-2 Sale Price:</strong> " . formatCurrency($newPrice * 1.5) . " (based on " . formatCurrency($newPrice) . " cost)</p>";
echo "<p><strong>Advantage:</strong> Can sell older stock at lower price, newer stock at higher price</p>";
echo "<p><strong>Flexibility:</strong> FIFO allows selling cheaper stock first</p>";
echo "</div>";

echo "<h4>Average Method:</h4>";
echo "<div style='background: #f8f9fa; padding: 15px; margin: 10px 0;'>";
echo "<p><strong>All Units Sale Price:</strong> " . formatCurrency($averageMethod['average_price'] * 1.5) . " (single blended price)</p>";
echo "<p><strong>Advantage:</strong> Simplified pricing, consistent margin</p>";
echo "<p><strong>Consideration:</strong> May overprice older stock, underprice newer stock</p>";
echo "</div>";

echo "</div>";

echo "</div>";
?>