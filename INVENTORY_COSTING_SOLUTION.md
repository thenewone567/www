# Inventory Costing Methods Implementation

## Current Problem Analysis

The current system has a critical flaw in inventory valuation:

### Current Flawed Approach:

```sql
-- Current inventory value calculation (WRONG)
SELECT SUM(quantity * purchase_price)
FROM products p
LEFT JOIN stock s ON p.product_id = s.product_id
```

**Problem**: Uses a single `purchase_price` from products table for all stock, regardless of when it was purchased.

**Scenario**:

- Buy 100 units at ₹100 each (Total: ₹10,000)
- Price increases to ₹120
- System now values existing 100 units at ₹12,000 (WRONG!)

## Proposed Solution: FIFO/LIFO/Average Cost Methods

### 1. Database Schema Enhancement

```sql
-- Add cost tracking to purchase_items (already exists)
CREATE TABLE `purchase_items` (
  `purchase_item_id` int(11) NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL  -- This tracks actual purchase cost
);

-- Enhance stock table to track cost layers
ALTER TABLE `stock` ADD COLUMN `cost_price` decimal(10,2) DEFAULT NULL;
ALTER TABLE `stock` ADD COLUMN `purchase_date` date DEFAULT NULL;
ALTER TABLE `stock` ADD COLUMN `batch_id` varchar(50) DEFAULT NULL;

-- Create inventory cost layers table (NEW)
CREATE TABLE `inventory_cost_layers` (
  `layer_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `purchase_date` date NOT NULL,
  `quantity_received` int(11) NOT NULL,
  `quantity_remaining` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `purchase_item_id` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`layer_id`),
  KEY `idx_product_date` (`product_id`, `purchase_date`),
  KEY `idx_product_remaining` (`product_id`, `quantity_remaining`)
);
```

### 2. Inventory Costing Methods

#### A. FIFO (First In, First Out) - Recommended for India

```php
class InventoryCosting {

    public function calculateFIFOValue($productId) {
        $this->db->query("
            SELECT SUM(quantity_remaining * unit_cost) as fifo_value
            FROM inventory_cost_layers
            WHERE product_id = :product_id
            AND quantity_remaining > 0
            ORDER BY purchase_date ASC
        ");
        $this->db->bind(':product_id', $productId);
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result->fifo_value : 0;
    }

    public function getTotalInventoryValueFIFO() {
        $this->db->query("
            SELECT SUM(quantity_remaining * unit_cost) as total_value
            FROM inventory_cost_layers
            WHERE quantity_remaining > 0
        ");
        $this->db->execute();
        $result = $this->db->single();
        return $result ? $result->total_value : 0;
    }
}
```

#### B. Weighted Average Cost Method

```php
public function calculateWeightedAverageValue($productId) {
    $this->db->query("
        SELECT
            SUM(quantity_remaining) as total_qty,
            SUM(quantity_remaining * unit_cost) as total_value
        FROM inventory_cost_layers
        WHERE product_id = :product_id
        AND quantity_remaining > 0
    ");
    $this->db->bind(':product_id', $productId);
    $this->db->execute();
    $result = $this->db->single();

    if ($result && $result->total_qty > 0) {
        $avgCost = $result->total_value / $result->total_qty;
        return $result->total_qty * $avgCost;
    }
    return 0;
}
```

### 3. Implementation Strategy

#### Phase 1: Add Cost Layer Tracking

```php
// When receiving purchase items
public function receivePurchaseItems($purchaseId, $items) {
    foreach ($items as $item) {
        // Add to inventory_cost_layers
        $this->db->query("
            INSERT INTO inventory_cost_layers
            (product_id, purchase_date, quantity_received, quantity_remaining, unit_cost, purchase_item_id)
            VALUES (:product_id, :purchase_date, :quantity, :quantity, :unit_cost, :purchase_item_id)
        ");
        $this->db->bind(':product_id', $item['product_id']);
        $this->db->bind(':purchase_date', date('Y-m-d'));
        $this->db->bind(':quantity', $item['quantity']);
        $this->db->bind(':unit_cost', $item['unit_price']);
        $this->db->bind(':purchase_item_id', $item['purchase_item_id']);
        $this->db->execute();
    }
}
```

#### Phase 2: Update Sale Processing (FIFO)

```php
public function processSaleFIFO($productId, $quantitySold) {
    // Get oldest cost layers first
    $this->db->query("
        SELECT * FROM inventory_cost_layers
        WHERE product_id = :product_id
        AND quantity_remaining > 0
        ORDER BY purchase_date ASC, layer_id ASC
    ");
    $this->db->bind(':product_id', $productId);
    $this->db->execute();
    $costLayers = $this->db->resultSet();

    $remainingToSell = $quantitySold;
    $totalCostOfSale = 0;

    foreach ($costLayers as $layer) {
        if ($remainingToSell <= 0) break;

        $quantityFromThisLayer = min($remainingToSell, $layer->quantity_remaining);
        $costFromThisLayer = $quantityFromThisLayer * $layer->unit_cost;

        // Update the cost layer
        $newRemaining = $layer->quantity_remaining - $quantityFromThisLayer;
        $this->db->query("
            UPDATE inventory_cost_layers
            SET quantity_remaining = :new_remaining
            WHERE layer_id = :layer_id
        ");
        $this->db->bind(':new_remaining', $newRemaining);
        $this->db->bind(':layer_id', $layer->layer_id);
        $this->db->execute();

        $totalCostOfSale += $costFromThisLayer;
        $remainingToSell -= $quantityFromThisLayer;
    }

    return $totalCostOfSale; // Cost of goods sold
}
```

### 4. Enhanced Dashboard Methods

```php
// Update Dashboard.php
public function getTotalInventoryValueFIFO() {
    $this->db->query("
        SELECT SUM(icl.quantity_remaining * icl.unit_cost) as total_value
        FROM inventory_cost_layers icl
        JOIN products p ON icl.product_id = p.product_id
        WHERE icl.quantity_remaining > 0
        AND p.is_active = 1
    ");
    $this->db->execute();
    $result = $this->db->single();
    return $result ? $result->total_value : 0;
}

public function getInventoryValueByMethod($method = 'FIFO') {
    switch($method) {
        case 'FIFO':
            return $this->getTotalInventoryValueFIFO();
        case 'AVERAGE':
            return $this->getTotalInventoryValueAverage();
        case 'LIFO':
            return $this->getTotalInventoryValueLIFO();
        default:
            return $this->getTotalInventoryValueFIFO();
    }
}
```

### 5. Reporting Enhancements

#### Inventory Valuation Report

```php
public function getInventoryValuationReport() {
    $this->db->query("
        SELECT
            p.product_name,
            p.sku,
            SUM(icl.quantity_remaining) as current_stock,
            SUM(icl.quantity_remaining * icl.unit_cost) as fifo_value,
            CASE
                WHEN SUM(icl.quantity_remaining) > 0
                THEN SUM(icl.quantity_remaining * icl.unit_cost) / SUM(icl.quantity_remaining)
                ELSE 0
            END as avg_cost_per_unit,
            MIN(icl.purchase_date) as oldest_stock_date,
            MAX(icl.purchase_date) as newest_stock_date
        FROM products p
        LEFT JOIN inventory_cost_layers icl ON p.product_id = icl.product_id
        WHERE p.is_active = 1
        AND icl.quantity_remaining > 0
        GROUP BY p.product_id, p.product_name, p.sku
        ORDER BY fifo_value DESC
    ");
    $this->db->execute();
    return $this->db->resultSet();
}
```

### 6. Configuration Options

```php
// Add to app/config.php
define('INVENTORY_COSTING_METHOD', 'FIFO'); // FIFO, LIFO, AVERAGE
define('ENABLE_COST_LAYER_TRACKING', true);
define('AUTO_MIGRATE_EXISTING_INVENTORY', false);
```

### 7. Migration Strategy for Existing Data

```sql
-- Migration script to convert existing inventory
INSERT INTO inventory_cost_layers
(product_id, purchase_date, quantity_received, quantity_remaining, unit_cost)
SELECT
    s.product_id,
    COALESCE(DATE(s.last_updated), CURDATE()) as purchase_date,
    s.quantity,
    s.quantity,
    COALESCE(p.purchase_price, 0) as unit_cost
FROM stock s
JOIN products p ON s.product_id = p.product_id
WHERE s.quantity > 0;
```

## Benefits of This Solution

### 1. **Accurate Inventory Valuation**

- ✅ Tracks actual purchase costs for each batch
- ✅ Proper COGS calculation
- ✅ Compliant with accounting standards

### 2. **Multiple Costing Methods**

- ✅ FIFO (recommended for India)
- ✅ Weighted Average
- ✅ LIFO (where applicable)

### 3. **Enhanced Reporting**

- ✅ Inventory aging reports
- ✅ Cost layer analysis
- ✅ Profit margin tracking

### 4. **Backward Compatibility**

- ✅ Existing code continues to work
- ✅ Gradual migration possible
- ✅ Configurable implementation

## Implementation Priority

1. **High Priority**: Add cost layer tracking for new purchases
2. **Medium Priority**: Implement FIFO valuation
3. **Low Priority**: Add LIFO/Average cost methods
4. **Optional**: Migrate existing inventory data

This solution addresses your concern about handling purchase price increases correctly while providing a foundation for professional inventory management.
