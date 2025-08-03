-- Migration to create cycle_counts and cycle_count_items tables
-- This creates the necessary tables for the cycle counts functionality

USE master_hardware;

-- Create cycle_counts table
CREATE TABLE IF NOT EXISTS `cycle_counts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `count_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `count_date` date NOT NULL,
  `created_by` int(11) NOT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cycle_counts_created_by` (`created_by`),
  KEY `fk_cycle_counts_assigned_to` (`assigned_to`),
  KEY `idx_cycle_counts_status` (`status`),
  KEY `idx_cycle_counts_date` (`count_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create cycle_count_items table
CREATE TABLE IF NOT EXISTS `cycle_count_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cycle_count_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `expected_quantity` decimal(10,2) DEFAULT 0.00,
  `counted_quantity` decimal(10,2) DEFAULT NULL,
  `variance` decimal(10,2) DEFAULT NULL,
  `variance_percentage` decimal(5,2) DEFAULT NULL,
  `reason_code` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('pending','counted','adjusted') DEFAULT 'pending',
  `counted_by` int(11) DEFAULT NULL,
  `counted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_cycle_count_items_cycle_count` (`cycle_count_id`),
  KEY `fk_cycle_count_items_product` (`product_id`),
  KEY `fk_cycle_count_items_counted_by` (`counted_by`),
  KEY `idx_cycle_count_items_status` (`status`),
  UNIQUE KEY `unique_cycle_count_product` (`cycle_count_id`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create cycle_count_adjustments table for tracking inventory adjustments
CREATE TABLE IF NOT EXISTS `cycle_count_adjustments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cycle_count_item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `adjustment_quantity` decimal(10,2) NOT NULL,
  `adjustment_type` enum('increase','decrease') NOT NULL,
  `cost_impact` decimal(10,2) DEFAULT 0.00,
  `reason` varchar(255) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_cycle_count_adjustments_item` (`cycle_count_item_id`),
  KEY `fk_cycle_count_adjustments_product` (`product_id`),
  KEY `fk_cycle_count_adjustments_approved_by` (`approved_by`),
  KEY `idx_cycle_count_adjustments_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add foreign key constraints (assuming users table exists)
-- Note: These will only be added if the referenced tables exist
SET @sql = 'ALTER TABLE cycle_counts 
            ADD CONSTRAINT fk_cycle_counts_created_by 
            FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE RESTRICT ON UPDATE CASCADE';
SET @table_exists = (SELECT COUNT(*) FROM information_schema.tables 
                    WHERE table_schema = DATABASE() AND table_name = 'users');
IF @table_exists > 0 THEN
    SET @sql_exec = @sql;
    PREPARE stmt FROM @sql_exec;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END IF;

SET @sql = 'ALTER TABLE cycle_counts 
            ADD CONSTRAINT fk_cycle_counts_assigned_to 
            FOREIGN KEY (assigned_to) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE';
IF @table_exists > 0 THEN
    SET @sql_exec = @sql;
    PREPARE stmt FROM @sql_exec;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
END IF;

-- Add constraints for cycle_count_items (assuming products table exists)
SET @products_exists = (SELECT COUNT(*) FROM information_schema.tables 
                       WHERE table_schema = DATABASE() AND table_name = 'products');
IF @products_exists > 0 THEN
    ALTER TABLE cycle_count_items 
    ADD CONSTRAINT fk_cycle_count_items_cycle_count 
    FOREIGN KEY (cycle_count_id) REFERENCES cycle_counts(id) ON DELETE CASCADE ON UPDATE CASCADE;
    
    ALTER TABLE cycle_count_items 
    ADD CONSTRAINT fk_cycle_count_items_product 
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT ON UPDATE CASCADE;
END IF;

IF @table_exists > 0 THEN
    ALTER TABLE cycle_count_items 
    ADD CONSTRAINT fk_cycle_count_items_counted_by 
    FOREIGN KEY (counted_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;
END IF;

-- Add constraints for cycle_count_adjustments
IF @products_exists > 0 THEN
    ALTER TABLE cycle_count_adjustments 
    ADD CONSTRAINT fk_cycle_count_adjustments_item 
    FOREIGN KEY (cycle_count_item_id) REFERENCES cycle_count_items(id) ON DELETE CASCADE ON UPDATE CASCADE;
    
    ALTER TABLE cycle_count_adjustments 
    ADD CONSTRAINT fk_cycle_count_adjustments_product 
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE RESTRICT ON UPDATE CASCADE;
END IF;

IF @table_exists > 0 THEN
    ALTER TABLE cycle_count_adjustments 
    ADD CONSTRAINT fk_cycle_count_adjustments_approved_by 
    FOREIGN KEY (approved_by) REFERENCES users(user_id) ON DELETE SET NULL ON UPDATE CASCADE;
END IF;

-- Insert some sample cycle count data for testing
INSERT INTO `cycle_counts` (`count_name`, `description`, `status`, `count_date`, `created_by`, `location`, `notes`) VALUES
('Monthly Inventory Count - January 2025', 'Regular monthly cycle count for all high-value items', 'completed', '2025-01-15', 1, 'Main Warehouse', 'Standard monthly count completed successfully'),
('Power Tools Section Count', 'Focused count on power tools section due to discrepancies', 'in_progress', '2025-01-25', 1, 'Section A - Power Tools', 'Count in progress, some variances noted'),
('Hardware Fasteners Count', 'Quarterly count of small hardware items', 'pending', '2025-02-01', 1, 'Section C - Hardware', 'Scheduled for next week');

-- Insert sample cycle count items (assuming some products exist)
-- Note: These will only be inserted if products exist
SET @product_count = (SELECT COUNT(*) FROM products WHERE product_id IN (1,2,3,4,5));
IF @product_count > 0 THEN
    INSERT INTO `cycle_count_items` (`cycle_count_id`, `product_id`, `expected_quantity`, `counted_quantity`, `variance`, `status`, `counted_by`, `counted_at`) VALUES
    (1, 1, 35.00, 34.00, -1.00, 'counted', 1, '2025-01-15 10:30:00'),
    (1, 2, 22.00, 22.00, 0.00, 'counted', 1, '2025-01-15 10:45:00'),
    (1, 3, 78.00, 80.00, 2.00, 'counted', 1, '2025-01-15 11:00:00'),
    (2, 1, 34.00, NULL, NULL, 'pending', NULL, NULL),
    (2, 2, 22.00, 21.00, -1.00, 'counted', 1, '2025-01-25 14:20:00'),
    (3, 4, 65.00, NULL, NULL, 'pending', NULL, NULL),
    (3, 5, 42.00, NULL, NULL, 'pending', NULL, NULL);
END IF;

-- Create views for reporting
CREATE OR REPLACE VIEW `cycle_count_summary` AS
SELECT 
    cc.id,
    cc.count_name,
    cc.status,
    cc.count_date,
    cc.location,
    u.username as created_by_name,
    COUNT(cci.id) as total_items,
    COUNT(CASE WHEN cci.status = 'counted' THEN 1 END) as counted_items,
    COUNT(CASE WHEN cci.variance != 0 THEN 1 END) as variance_items,
    SUM(CASE WHEN cci.variance IS NOT NULL THEN ABS(cci.variance) ELSE 0 END) as total_variance_quantity,
    cc.created_at,
    cc.completed_at
FROM cycle_counts cc
LEFT JOIN users u ON cc.created_by = u.user_id
LEFT JOIN cycle_count_items cci ON cc.id = cci.cycle_count_id
GROUP BY cc.id, cc.count_name, cc.status, cc.count_date, cc.location, u.username, cc.created_at, cc.completed_at;

COMMIT;
