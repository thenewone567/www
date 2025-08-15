-- Migration: Create product_suppliers table for multiple suppliers per product
-- Date: 2025-08-06
-- Description: Allow products to have multiple suppliers with different pricing and terms

CREATE TABLE IF NOT EXISTS `product_suppliers` (
    `ps_id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `supplier_id` int(11) NOT NULL,
    
    -- Supplier-specific pricing
    `supplier_sku` varchar(100) DEFAULT NULL,              -- Supplier's SKU for this product
    `supplier_name_for_product` varchar(255) DEFAULT NULL, -- How supplier names this product
    `purchase_price` decimal(10,2) NOT NULL,               -- Supplier's price
    `min_order_quantity` int(11) DEFAULT 1,                -- Minimum order quantity
    `lead_time_days` int(11) DEFAULT 7,                    -- Delivery time in days
    
    -- Additional supplier terms
    `payment_terms` varchar(100) DEFAULT NULL,             -- e.g., "Net 30", "COD"
    `shipping_cost` decimal(10,2) DEFAULT 0.00,           -- Additional shipping cost
    `discount_percentage` decimal(5,2) DEFAULT 0.00,       -- Volume discount
    `currency` varchar(3) DEFAULT 'INR',                   -- Currency code
    
    -- Status and preferences
    `is_primary` tinyint(1) DEFAULT 0,                     -- Primary supplier for this product
    `is_active` tinyint(1) DEFAULT 1,                      -- Is this supplier active for this product
    `last_order_date` date DEFAULT NULL,                   -- Last time ordered from this supplier
    `total_orders` int(11) DEFAULT 0,                      -- Total orders from this supplier
    
    -- Quality and performance metrics
    `quality_rating` decimal(2,1) DEFAULT NULL,            -- 1-5 rating
    `delivery_rating` decimal(2,1) DEFAULT NULL,           -- 1-5 rating
    `notes` text DEFAULT NULL,                             -- Additional notes
    
    -- Timestamps
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`ps_id`),
    UNIQUE KEY `unique_product_supplier` (`product_id`, `supplier_id`),
    KEY `idx_product_id` (`product_id`),
    KEY `idx_supplier_id` (`supplier_id`),
    KEY `idx_is_primary` (`is_primary`),
    KEY `idx_is_active` (`is_active`),
    
    FOREIGN KEY (`product_id`) REFERENCES `products`(`product_id`) ON DELETE CASCADE,
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`supplier_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create trigger to ensure only one primary supplier per product
DELIMITER $$
CREATE TRIGGER `ensure_one_primary_supplier` 
BEFORE UPDATE ON `product_suppliers`
FOR EACH ROW 
BEGIN
    IF NEW.is_primary = 1 AND OLD.is_primary = 0 THEN
        UPDATE product_suppliers 
        SET is_primary = 0 
        WHERE product_id = NEW.product_id AND ps_id != NEW.ps_id;
    END IF;
END$$

CREATE TRIGGER `ensure_one_primary_supplier_insert` 
BEFORE INSERT ON `product_suppliers`
FOR EACH ROW 
BEGIN
    IF NEW.is_primary = 1 THEN
        UPDATE product_suppliers 
        SET is_primary = 0 
        WHERE product_id = NEW.product_id;
    END IF;
END$$
DELIMITER ;

-- Add indexes for better performance
CREATE INDEX `idx_purchase_price` ON `product_suppliers`(`purchase_price`);
CREATE INDEX `idx_lead_time` ON `product_suppliers`(`lead_time_days`);
CREATE INDEX `idx_last_order` ON `product_suppliers`(`last_order_date`);
