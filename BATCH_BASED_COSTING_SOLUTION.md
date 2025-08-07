# Enhanced Batch-Based Inventory Costing Solution

## Current Problem

The system has:

- ✅ **Batch tracking** in `stock` table with `batch_number`
- ✅ **Purchase prices** in `purchase_items` table with `unit_price`
- ❌ **Missing link** between batches and their purchase prices

**Result**: All inventory valued at current product price, not actual purchase cost.

## Solution: Enhanced Batch System

### 1. Database Enhancement

Add purchase price tracking to stock batches:

```sql
-- Add purchase_price to stock table
ALTER TABLE `stock` ADD COLUMN `purchase_price` DECIMAL(10,2) DEFAULT NULL AFTER `batch_number`;
ALTER TABLE `stock` ADD COLUMN `purchase_date` DATE DEFAULT NULL AFTER `purchase_price`;
ALTER TABLE `stock` ADD COLUMN `purchase_id` INT(11) DEFAULT NULL AFTER `purchase_date`;

-- Index for better performance
CREATE INDEX idx_stock_batch_price ON stock(product_id, batch_number, purchase_price);

-- Foreign key constraint
ALTER TABLE `stock` ADD CONSTRAINT `fk_stock_purchase`
FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`purchase_id`) ON DELETE SET NULL;
```

### 2. Enhanced Batch Creation

When receiving purchases, create batches with proper cost tracking:

```php
// In PurchaseController when receiving items
function createStockBatch($purchaseItem) {
    $batchNumber = $this->generateBatchNumber($purchaseItem->product_id, $purchaseItem->purchase_id);

    $stockData = [
        'product_id' => $purchaseItem->product_id,
        'batch_number' => $batchNumber,
        'quantity' => $purchaseItem->quantity,
        'purchase_price' => $purchaseItem->unit_price,  // Actual purchase cost
        'purchase_date' => $purchaseItem->purchase_date,
        'purchase_id' => $purchaseItem->purchase_id,
        'location_id' => $purchaseItem->location_id
    ];

    return $this->stockModel->addStockWithCost($stockData);
}
```

### 3. Accurate Inventory Valuation

```php
// Get true inventory value using batch costs
function getTrueInventoryValue($productId = null) {
    $whereClause = $productId ? "WHERE s.product_id = :product_id" : "";

    $this->db->query("
        SELECT
            p.product_name,
            s.batch_number,
            s.quantity,
            COALESCE(s.purchase_price, p.purchase_price) as unit_cost,
            (s.quantity * COALESCE(s.purchase_price, p.purchase_price)) as batch_value
        FROM stock s
        JOIN products p ON s.product_id = p.product_id
        $whereClause
        ORDER BY s.product_id, s.purchase_date ASC
    ");

    if ($productId) {
        $this->db->bind(':product_id', $productId);
    }

    return $this->db->resultSet();
}
```

## Benefits of Batch-Based Costing

### 1. **Accurate Inventory Valuation**

```
Product A Batches:
- Batch-001: 100 units @ ₹100 = ₹10,000
- Batch-002: 100 units @ ₹120 = ₹12,000
Total Value: ₹22,000 (not ₹24,000 at current price)
```

### 2. **FIFO/LIFO Cost Management**

```php
// FIFO: Sell oldest batches first
function allocateStockFIFO($productId, $quantityToSell) {
    $this->db->query("
        SELECT * FROM stock
        WHERE product_id = :product_id AND quantity > 0
        ORDER BY purchase_date ASC, batch_number ASC
    ");

    // Allocate from oldest batches first
}
```

### 3. **Profit Analysis by Batch**

```php
// Calculate true profit margins
function getBatchProfitability($productId) {
    $this->db->query("
        SELECT
            s.batch_number,
            s.purchase_price as cost,
            p.selling_price as selling_price,
            (p.selling_price - s.purchase_price) as margin,
            ((p.selling_price - s.purchase_price) / s.purchase_price * 100) as margin_percent
        FROM stock s
        JOIN products p ON s.product_id = p.product_id
        WHERE s.product_id = :product_id
    ");
}
```

## Implementation Example

### Scenario: Product Price Changes

```
Day 1: Buy 100 units @ ₹100 → Batch-001
Day 30: Buy 100 units @ ₹120 → Batch-002
Day 60: Buy 100 units @ ₹110 → Batch-003

Current Stock: 300 units
True Value: (100×₹100) + (100×₹120) + (100×₹110) = ₹33,000
Wrong Value: 300×₹110 (current price) = ₹33,000 (coincidentally same)

But if current price is ₹130:
True Value: ₹33,000
Wrong Value: 300×₹130 = ₹39,000 (₹6,000 overvaluation!)
```

### Sales Allocation (FIFO)

```
Sell 150 units:
- Take 100 from Batch-001 @ ₹100 cost
- Take 50 from Batch-002 @ ₹120 cost
Total Cost: (100×₹100) + (50×₹120) = ₹16,000
```

## Enhanced Helper Functions

```php
/**
 * Get inventory value using actual batch costs
 */
function getAccurateInventoryValue($productId = null) {
    if ($productId) {
        return $this->stockModel->getProductInventoryValue($productId);
    }
    return $this->stockModel->getTotalInventoryValue();
}

/**
 * Format batch information with cost details
 */
function formatBatchInfo($batch) {
    return [
        'batch_number' => $batch->batch_number,
        'quantity' => formatIndianNumber($batch->quantity, 0),
        'unit_cost' => formatCurrency($batch->purchase_price, 2),
        'total_value' => formatCurrency($batch->quantity * $batch->purchase_price, 2),
        'purchase_date' => date('d-M-Y', strtotime($batch->purchase_date))
    ];
}
```

## Migration Strategy

### Phase 1: Add Columns

```sql
ALTER TABLE `stock` ADD COLUMN `purchase_price` DECIMAL(10,2) DEFAULT NULL;
ALTER TABLE `stock` ADD COLUMN `purchase_date` DATE DEFAULT NULL;
ALTER TABLE `stock` ADD COLUMN `purchase_id` INT(11) DEFAULT NULL;
```

### Phase 2: Populate Existing Data

```sql
-- Set current product price for existing stock
UPDATE stock s
JOIN products p ON s.product_id = p.product_id
SET s.purchase_price = p.purchase_price
WHERE s.purchase_price IS NULL;
```

### Phase 3: Update Code

- Modify purchase receiving to set batch prices
- Update inventory valuation queries
- Enhance dashboard calculations

## Dashboard Integration

```php
// Enhanced dashboard metrics
$data['inventory_value'] = $this->inventoryModel->getAccurateInventoryValue();
$data['inventory_breakdown'] = $this->inventoryModel->getInventoryByBatch();
$data['low_margin_batches'] = $this->inventoryModel->getLowMarginBatches();
```

This solution leverages your existing batch system while adding proper cost tracking, giving you accurate inventory valuation and better business insights!
