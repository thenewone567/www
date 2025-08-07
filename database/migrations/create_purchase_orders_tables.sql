-- Create missing purchase_orders tables for enhanced purchase management

-- Create purchase_orders table
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `po_id` int(11) NOT NULL AUTO_INCREMENT,
  `po_number` varchar(50) NOT NULL UNIQUE,
  `supplier_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `expected_date` date NULL,
  `status` enum('pending','sent','partially_received','received','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `notes` text NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`po_id`),
  KEY `idx_po_number` (`po_number`),
  KEY `idx_supplier_id` (`supplier_id`),
  KEY `idx_status` (`status`),
  KEY `idx_order_date` (`order_date`),
  KEY `idx_created_by` (`created_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create purchase_order_items table
CREATE TABLE IF NOT EXISTS `purchase_order_items` (
  `po_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `po_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_ordered` int(11) NOT NULL,
  `quantity_received` int(11) DEFAULT 0,
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`po_item_id`),
  KEY `idx_po_id` (`po_id`),
  KEY `idx_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create suppliers table if it doesn't exist
CREATE TABLE IF NOT EXISTS `suppliers` (
  `supplier_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_code` varchar(50) UNIQUE,
  `supplier_name` varchar(255) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `gst_number` varchar(15) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT 'Net 30',
  `credit_limit` decimal(12,2) DEFAULT 0.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`supplier_id`),
  KEY `idx_supplier_name` (`supplier_name`),
  KEY `idx_supplier_gst` (`gst_number`),
  KEY `idx_supplier_email` (`email`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key constraints
ALTER TABLE `purchase_order_items` 
ADD CONSTRAINT `fk_poi_purchase_order` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`po_id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `fk_poi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `purchase_orders`
ADD CONSTRAINT `fk_po_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
ADD CONSTRAINT `fk_po_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

-- Insert some sample suppliers if none exist
INSERT IGNORE INTO `suppliers` (`supplier_code`, `supplier_name`, `contact_info`, `payment_terms`, `credit_limit`, `is_active`) VALUES
('SUP001', 'ABC Hardware Supply', 'John Smith - Phone: 555-123-4567 - Email: john@abchardware.com', 'Net 30', 10000.00, 1),
('SUP002', 'ProTools Distribution', 'Sarah Johnson - Phone: 555-234-5678 - Email: sarah@protools.com', 'Net 30', 15000.00, 1),
('SUP003', 'BuildRight Wholesale', 'Mike Wilson - Phone: 555-345-6789 - Email: mike@buildright.com', 'Net 15', 8000.00, 1),
('SUP004', 'ElectroMax Supply', 'Lisa Davis - Phone: 555-456-7890 - Email: lisa@electromax.com', 'Net 30', 12000.00, 1),
('SUP005', 'PlumbPro Distributors', 'Tom Brown - Phone: 555-567-8901 - Email: tom@plumbpro.com', 'Net 30', 10000.00, 1);
