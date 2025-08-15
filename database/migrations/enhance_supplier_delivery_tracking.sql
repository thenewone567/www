-- Migration: Enhance suppliers table for delivery tracking and performance analytics
-- Date: 2025-08-06
-- Description: Add delivery tracking, performance metrics, and analytics fields

-- Add delivery tracking and performance columns to suppliers table
ALTER TABLE `suppliers` 
ADD COLUMN `average_delivery_days` DECIMAL(4,1) DEFAULT NULL COMMENT 'Average delivery time in days',
ADD COLUMN `on_time_delivery_rate` DECIMAL(5,2) DEFAULT NULL COMMENT 'Percentage of on-time deliveries',
ADD COLUMN `early_delivery_count` INT(11) DEFAULT 0 COMMENT 'Number of early deliveries',
ADD COLUMN `late_delivery_count` INT(11) DEFAULT 0 COMMENT 'Number of late deliveries',
ADD COLUMN `total_deliveries` INT(11) DEFAULT 0 COMMENT 'Total number of deliveries',
ADD COLUMN `last_delivery_date` DATE DEFAULT NULL COMMENT 'Date of last delivery',
ADD COLUMN `average_order_value` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Average order value',
ADD COLUMN `total_order_value` DECIMAL(15,2) DEFAULT 0.00 COMMENT 'Total value of all orders',
ADD COLUMN `reliability_score` DECIMAL(3,1) DEFAULT NULL COMMENT 'Overall reliability score (1-10)',
ADD COLUMN `quality_rating` DECIMAL(2,1) DEFAULT NULL COMMENT 'Quality rating (1-5)',
ADD COLUMN `communication_rating` DECIMAL(2,1) DEFAULT NULL COMMENT 'Communication rating (1-5)',
ADD COLUMN `preferred_payment_terms` VARCHAR(50) DEFAULT 'Net 30' COMMENT 'Preferred payment terms',
ADD COLUMN `credit_limit` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Credit limit for this supplier',
ADD COLUMN `current_outstanding` DECIMAL(12,2) DEFAULT 0.00 COMMENT 'Current outstanding amount',
ADD COLUMN `last_evaluation_date` DATE DEFAULT NULL COMMENT 'Date of last performance evaluation',
ADD COLUMN `supplier_tier` ENUM('Gold', 'Silver', 'Bronze', 'Standard') DEFAULT 'Standard' COMMENT 'Supplier tier based on performance',
ADD COLUMN `notes` TEXT DEFAULT NULL COMMENT 'Additional notes about supplier',
ADD COLUMN `is_verified` TINYINT(1) DEFAULT 0 COMMENT 'Whether supplier is verified',
ADD COLUMN `verification_date` DATE DEFAULT NULL COMMENT 'Date when supplier was verified';

-- Create delivery_tracking table for detailed delivery records
CREATE TABLE IF NOT EXISTS `delivery_tracking` (
    `delivery_id` int(11) NOT NULL AUTO_INCREMENT,
    `purchase_order_id` int(11) NOT NULL,
    `supplier_id` int(11) NOT NULL,
    `expected_delivery_date` date NOT NULL,
    `actual_delivery_date` date DEFAULT NULL,
    `delivery_status` enum('pending','partial','delivered','cancelled') DEFAULT 'pending',
    `days_early_late` int(11) DEFAULT NULL COMMENT 'Positive for late, negative for early',
    `delivery_quality` enum('excellent','good','average','poor') DEFAULT NULL,
    `delivery_notes` text DEFAULT NULL,
    `received_by` varchar(100) DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`delivery_id`),
    KEY `idx_purchase_order` (`purchase_order_id`),
    KEY `idx_supplier` (`supplier_id`),
    KEY `idx_delivery_date` (`actual_delivery_date`),
    KEY `idx_status` (`delivery_status`),
    
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`supplier_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create supplier_evaluations table for periodic evaluations
CREATE TABLE IF NOT EXISTS `supplier_evaluations` (
    `evaluation_id` int(11) NOT NULL AUTO_INCREMENT,
    `supplier_id` int(11) NOT NULL,
    `evaluation_date` date NOT NULL,
    `evaluator_name` varchar(100) NOT NULL,
    `delivery_score` decimal(2,1) DEFAULT NULL COMMENT 'Delivery performance score (1-5)',
    `quality_score` decimal(2,1) DEFAULT NULL COMMENT 'Product quality score (1-5)',
    `price_competitiveness` decimal(2,1) DEFAULT NULL COMMENT 'Price competitiveness score (1-5)',
    `communication_score` decimal(2,1) DEFAULT NULL COMMENT 'Communication score (1-5)',
    `overall_score` decimal(2,1) DEFAULT NULL COMMENT 'Overall score (1-5)',
    `strengths` text DEFAULT NULL,
    `weaknesses` text DEFAULT NULL,
    `improvement_areas` text DEFAULT NULL,
    `recommended_actions` text DEFAULT NULL,
    `next_evaluation_date` date DEFAULT NULL,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`evaluation_id`),
    KEY `idx_supplier` (`supplier_id`),
    KEY `idx_evaluation_date` (`evaluation_date`),
    
    FOREIGN KEY (`supplier_id`) REFERENCES `suppliers`(`supplier_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Create trigger to automatically update supplier performance metrics when delivery is recorded
DELIMITER $$
CREATE TRIGGER `update_supplier_delivery_performance` 
AFTER UPDATE ON `delivery_tracking`
FOR EACH ROW 
BEGIN
    DECLARE total_count INT DEFAULT 0;
    DECLARE early_count INT DEFAULT 0;
    DECLARE late_count INT DEFAULT 0;
    DECLARE on_time_count INT DEFAULT 0;
    DECLARE avg_days DECIMAL(4,1) DEFAULT 0;
    DECLARE on_time_rate DECIMAL(5,2) DEFAULT 0;
    
    -- Only update if delivery was just completed
    IF NEW.actual_delivery_date IS NOT NULL AND OLD.actual_delivery_date IS NULL THEN
        
        -- Calculate days early/late
        UPDATE delivery_tracking 
        SET days_early_late = DATEDIFF(actual_delivery_date, expected_delivery_date)
        WHERE delivery_id = NEW.delivery_id;
        
        -- Get updated statistics for this supplier
        SELECT 
            COUNT(*) as total,
            COUNT(CASE WHEN days_early_late < 0 THEN 1 END) as early,
            COUNT(CASE WHEN days_early_late > 0 THEN 1 END) as late,
            COUNT(CASE WHEN days_early_late = 0 THEN 1 END) as on_time,
            AVG(ABS(days_early_late)) as avg_deviation
        INTO total_count, early_count, late_count, on_time_count, avg_days
        FROM delivery_tracking 
        WHERE supplier_id = NEW.supplier_id 
        AND actual_delivery_date IS NOT NULL;
        
        -- Calculate on-time delivery rate
        IF total_count > 0 THEN
            SET on_time_rate = (on_time_count / total_count) * 100;
        END IF;
        
        -- Update supplier performance metrics
        UPDATE suppliers 
        SET 
            total_deliveries = total_count,
            early_delivery_count = early_count,
            late_delivery_count = late_count,
            on_time_delivery_rate = on_time_rate,
            average_delivery_days = avg_days,
            last_delivery_date = NEW.actual_delivery_date
        WHERE supplier_id = NEW.supplier_id;
        
    END IF;
END$$
DELIMITER ;

-- Create indexes for better performance
CREATE INDEX `idx_supplier_performance` ON `suppliers`(`on_time_delivery_rate`, `average_delivery_days`);
CREATE INDEX `idx_supplier_tier` ON `suppliers`(`supplier_tier`);
CREATE INDEX `idx_reliability_score` ON `suppliers`(`reliability_score`);
