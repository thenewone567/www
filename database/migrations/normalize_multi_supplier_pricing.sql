-- Migration: Normalize multi-supplier pricing system
-- Date: 2025-08-07
-- Description: Remove legacy single-supplier fields and fully normalize pricing

-- First, migrate any existing single-supplier data to product_suppliers table
INSERT IGNORE INTO product_suppliers (
    product_id, 
    supplier_id, 
    purchase_price, 
    is_primary, 
    is_active,
    created_at,
    updated_at
)
SELECT 
    p.product_id,
    COALESCE(p.supplier_id, 1) as supplier_id, -- Default to supplier 1 if null
    COALESCE(p.purchase_price, 0.00) as purchase_price,
    1 as is_primary, -- Make existing supplier primary
    1 as is_active,
    NOW(),
    NOW()
FROM products p 
WHERE p.supplier_id IS NOT NULL 
   OR p.purchase_price IS NOT NULL
ON DUPLICATE KEY UPDATE 
    purchase_price = VALUES(purchase_price),
    is_primary = 1;

-- Remove legacy single-supplier columns from products table
ALTER TABLE products 
DROP COLUMN IF EXISTS supplier_id,
DROP COLUMN IF EXISTS purchase_price;

-- Ensure product_suppliers table has all needed columns
ALTER TABLE product_suppliers 
ADD COLUMN IF NOT EXISTS supplier_code VARCHAR(50) DEFAULT NULL AFTER supplier_sku,
ADD COLUMN IF NOT EXISTS tax_rate DECIMAL(5,2) DEFAULT 0.00 AFTER discount_percentage,
ADD COLUMN IF NOT EXISTS is_preferred TINYINT(1) DEFAULT 0 AFTER is_primary,
ADD COLUMN IF NOT EXISTS last_purchase_price DECIMAL(10,2) DEFAULT NULL AFTER purchase_price,
ADD COLUMN IF NOT EXISTS price_valid_until DATE DEFAULT NULL AFTER last_purchase_price;

-- Add indexes for better performance
CREATE INDEX IF NOT EXISTS idx_ps_product_supplier ON product_suppliers(product_id, supplier_id);
CREATE INDEX IF NOT EXISTS idx_ps_primary ON product_suppliers(product_id, is_primary);
CREATE INDEX IF NOT EXISTS idx_ps_active ON product_suppliers(is_active);
CREATE INDEX IF NOT EXISTS idx_ps_price ON product_suppliers(purchase_price);

-- Update products table to ensure selling prices are calculated from suppliers
UPDATE products p 
SET selling_price = (
    SELECT ps.purchase_price * (1 + (p.profit_margin / 100))
    FROM product_suppliers ps 
    WHERE ps.product_id = p.product_id 
      AND ps.is_primary = 1 
      AND ps.is_active = 1
    LIMIT 1
)
WHERE p.selling_price IS NULL OR p.selling_price = 0;

-- Create view for easy product pricing queries
CREATE OR REPLACE VIEW product_pricing_view AS
SELECT 
    p.product_id,
    p.product_name,
    p.sku,
    p.selling_price,
    p.profit_margin,
    ps.supplier_id,
    s.supplier_name,
    ps.purchase_price,
    ps.supplier_sku,
    ps.is_primary,
    ps.lead_time_days,
    ps.min_order_quantity,
    ps.payment_terms,
    (ps.purchase_price + ps.shipping_cost) as total_supplier_cost,
    (p.selling_price - ps.purchase_price) as profit_amount,
    CASE 
        WHEN ps.purchase_price > 0 THEN 
            ROUND(((p.selling_price - ps.purchase_price) / ps.purchase_price) * 100, 2)
        ELSE 0 
    END as actual_profit_margin
FROM products p
LEFT JOIN product_suppliers ps ON p.product_id = ps.product_id AND ps.is_active = 1
LEFT JOIN suppliers s ON ps.supplier_id = s.supplier_id
WHERE p.is_active = 1;
