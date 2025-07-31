-- Migration for Feature Enhancements 1-6
-- Hardware Store Management System
-- Date: 2025-01-11

-- Create roles table for RBAC system
CREATE TABLE IF NOT EXISTS `roles` (
    `role_id` int(11) NOT NULL AUTO_INCREMENT,
    `role_name` varchar(50) NOT NULL UNIQUE,
    `permissions` JSON,
    `description` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create user_activity_log table for audit trail
CREATE TABLE IF NOT EXISTS `user_activity_log` (
    `log_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `action` varchar(100) NOT NULL,
    `details` text,
    `ip_address` varchar(45),
    `user_agent` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_created_at` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create customer_transactions table for credit management
CREATE TABLE IF NOT EXISTS `customer_transactions` (
    `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
    `customer_id` int(11) NOT NULL,
    `transaction_type` enum('sale','return','payment','adjustment') NOT NULL,
    `amount` decimal(10,2) NOT NULL,
    `reference_id` int(11), -- sale_id, return_id, etc.
    `description` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`transaction_id`),
    KEY `idx_customer_id` (`customer_id`),
    KEY `idx_type` (`transaction_type`),
    KEY `idx_created_at` (`created_at`),
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`customer_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create supplier_payments table for payment tracking
CREATE TABLE IF NOT EXISTS `supplier_payments` (
    `payment_id` int(11) NOT NULL AUTO_INCREMENT,
    `supplier_id` int(11) NOT NULL,
    `amount` decimal(10,2) NOT NULL,
    `payment_date` datetime NOT NULL,
    `payment_method` varchar(50) DEFAULT 'cash',
    `reference_number` varchar(100),
    `notes` text,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`payment_id`),
    KEY `idx_supplier_id` (`supplier_id`),
    KEY `idx_payment_date` (`payment_date`),
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`supplier_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add role_id column to users table if not exists
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `role_id` int(11) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `last_login` timestamp NULL,
ADD COLUMN IF NOT EXISTS `status` enum('active','inactive','suspended') DEFAULT 'active';

-- Add foreign key for role_id
ALTER TABLE `users` 
ADD CONSTRAINT `fk_users_role` 
FOREIGN KEY (`role_id`) REFERENCES `roles`(`role_id`) ON DELETE SET NULL;

-- Add credit management columns to customers table
ALTER TABLE `customers` 
ADD COLUMN IF NOT EXISTS `credit_limit` decimal(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS `current_balance` decimal(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS `loyalty_points` int(11) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `total_purchases` decimal(10,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS `last_purchase_date` date NULL;

-- Add performance tracking columns to suppliers table
ALTER TABLE `suppliers` 
ADD COLUMN IF NOT EXISTS `rating` decimal(3,2) DEFAULT 0.00,
ADD COLUMN IF NOT EXISTS `total_orders` int(11) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `on_time_deliveries` int(11) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `last_evaluation_date` date NULL,
ADD COLUMN IF NOT EXISTS `contract_start_date` date NULL,
ADD COLUMN IF NOT EXISTS `contract_end_date` date NULL,
ADD COLUMN IF NOT EXISTS `terms` text;

-- Add barcode and minimum stock columns to products table
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `barcode` varchar(100),
ADD COLUMN IF NOT EXISTS `minimum_stock` int(11) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `reorder_point` int(11) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `category` varchar(100);

-- Add unique index for barcode
ALTER TABLE `products` 
ADD UNIQUE INDEX IF NOT EXISTS `idx_barcode` (`barcode`);

-- Add approval workflow columns to purchase_orders table
ALTER TABLE `purchase_orders` 
ADD COLUMN IF NOT EXISTS `approval_status` enum('pending','approved','rejected') DEFAULT 'pending',
ADD COLUMN IF NOT EXISTS `approved_by` int(11) NULL,
ADD COLUMN IF NOT EXISTS `approved_at` timestamp NULL,
ADD COLUMN IF NOT EXISTS `priority` enum('low','medium','high','urgent') DEFAULT 'medium',
ADD COLUMN IF NOT EXISTS `auto_approve_amount` decimal(10,2) DEFAULT 1000.00;

-- Add foreign key for approved_by
ALTER TABLE `purchase_orders` 
ADD CONSTRAINT `fk_po_approved_by` 
FOREIGN KEY (`approved_by`) REFERENCES `users`(`user_id`) ON DELETE SET NULL;

-- Insert default roles
INSERT IGNORE INTO `roles` (`role_id`, `role_name`, `permissions`, `description`) VALUES
(1, 'admin', '{"all": true, "users": ["create","read","update","delete"], "sales": ["create","read","update","delete"], "purchases": ["create","read","update","delete","approve"], "inventory": ["create","read","update","delete"], "reports": ["read"], "settings": ["update"]}', 'Full system access'),
(2, 'manager', '{"sales": ["create","read","update"], "purchases": ["create","read","update","approve"], "inventory": ["read","update"], "reports": ["read"], "customers": ["create","read","update"], "suppliers": ["create","read","update"]}', 'Manager level access with approval rights'),
(3, 'cashier', '{"sales": ["create","read"], "customers": ["read"], "inventory": ["read"], "products": ["read"]}', 'Sales and customer service'),
(4, 'stock_clerk', '{"inventory": ["read","update"], "products": ["read","update"], "purchases": ["read"], "cycle_counts": ["create","read","update"]}', 'Inventory management');

-- Update existing users to have admin role
UPDATE `users` SET `role_id` = 1 WHERE `role_id` IS NULL;

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_products_minimum_stock` ON `products`(`minimum_stock`);
CREATE INDEX IF NOT EXISTS `idx_products_category` ON `products`(`category`);
CREATE INDEX IF NOT EXISTS `idx_customers_credit` ON `customers`(`credit_limit`, `current_balance`);
CREATE INDEX IF NOT EXISTS `idx_suppliers_rating` ON `suppliers`(`rating`);
CREATE INDEX IF NOT EXISTS `idx_purchase_orders_approval` ON `purchase_orders`(`approval_status`, `priority`);

-- Create triggers for automatic updates

-- Trigger to update customer balance on sale
DELIMITER $$
CREATE TRIGGER IF NOT EXISTS `update_customer_balance_on_sale` 
AFTER INSERT ON `sales` 
FOR EACH ROW 
BEGIN
    UPDATE `customers` 
    SET `current_balance` = `current_balance` + NEW.total_amount,
        `total_purchases` = `total_purchases` + NEW.total_amount,
        `last_purchase_date` = NEW.sale_date
    WHERE `customer_id` = NEW.customer_id;
    
    INSERT INTO `customer_transactions` 
    (`customer_id`, `transaction_type`, `amount`, `reference_id`, `description`)
    VALUES 
    (NEW.customer_id, 'sale', NEW.total_amount, NEW.sale_id, CONCAT('Sale #', NEW.sale_id));
END$$

-- Trigger to update supplier performance on purchase order completion
CREATE TRIGGER IF NOT EXISTS `update_supplier_performance` 
AFTER UPDATE ON `purchase_orders` 
FOR EACH ROW 
BEGIN
    IF NEW.status = 'received' AND OLD.status != 'received' THEN
        UPDATE `suppliers` 
        SET `total_orders` = `total_orders` + 1,
            `on_time_deliveries` = `on_time_deliveries` + 
                CASE WHEN NEW.updated_at <= NEW.expected_date THEN 1 ELSE 0 END
        WHERE `supplier_id` = NEW.supplier_id;
    END IF;
END$$

DELIMITER ;

-- Insert sample data for testing
INSERT IGNORE INTO `customer_transactions` (`customer_id`, `transaction_type`, `amount`, `description`) 
SELECT `customer_id`, 'adjustment', 0.00, 'Initial balance' FROM `customers` LIMIT 5;

COMMIT;
