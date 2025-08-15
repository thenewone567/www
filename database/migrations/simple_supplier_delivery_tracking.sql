-- Simple migration to add delivery performance fields to suppliers table
-- Add delivery performance tracking columns to suppliers table
ALTER TABLE suppliers 
ADD COLUMN delivery_performance_score DECIMAL(5,2) DEFAULT 50.00 COMMENT 'Overall performance score (0-100)',
ADD COLUMN avg_delivery_days DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Average delivery time in days',
ADD COLUMN early_deliveries_count INT DEFAULT 0 COMMENT 'Number of early deliveries',
ADD COLUMN late_deliveries_count INT DEFAULT 0 COMMENT 'Number of late deliveries', 
ADD COLUMN on_time_deliveries_count INT DEFAULT 0 COMMENT 'Number of on-time deliveries',
ADD COLUMN total_completed_orders INT DEFAULT 0 COMMENT 'Total completed purchase orders',
ADD COLUMN last_performance_update TIMESTAMP NULL DEFAULT NULL COMMENT 'Last time performance was calculated';

-- Create delivery_tracking table for detailed delivery records
CREATE TABLE IF NOT EXISTS delivery_tracking (
    delivery_id INT AUTO_INCREMENT PRIMARY KEY,
    purchase_order_id INT NOT NULL,
    supplier_id INT NOT NULL,
    expected_delivery_date DATE NOT NULL,
    actual_delivery_date DATE NULL,
    delivery_status ENUM('pending', 'early', 'on_time', 'late') DEFAULT 'pending',
    days_variance INT DEFAULT 0 COMMENT 'Positive = late, Negative = early, 0 = on time',
    delivery_notes TEXT NULL,
    recorded_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE CASCADE,
    INDEX idx_supplier_delivery (supplier_id, delivery_status),
    INDEX idx_delivery_date (actual_delivery_date),
    INDEX idx_expected_date (expected_delivery_date)
);

-- Create supplier_evaluations table for periodic performance reviews
CREATE TABLE IF NOT EXISTS supplier_evaluations (
    evaluation_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    evaluation_period_start DATE NOT NULL,
    evaluation_period_end DATE NOT NULL,
    total_orders INT DEFAULT 0,
    on_time_orders INT DEFAULT 0,
    early_orders INT DEFAULT 0,
    late_orders INT DEFAULT 0,
    average_delivery_days DECIMAL(5,2) DEFAULT 0.00,
    performance_score DECIMAL(5,2) DEFAULT 0.00,
    performance_grade ENUM('A+', 'A', 'B+', 'B', 'C+', 'C', 'D', 'F') DEFAULT 'C',
    evaluator_notes TEXT NULL,
    evaluated_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id) ON DELETE CASCADE,
    INDEX idx_supplier_evaluation (supplier_id, evaluation_period_end),
    INDEX idx_performance_score (performance_score DESC)
);
