-- Migration: Add Target-Based Commission System
-- This extends the existing commission system with target-based calculations

-- Create commission target tiers table
CREATE TABLE IF NOT EXISTS commission_target_tiers (
    tier_id INT AUTO_INCREMENT PRIMARY KEY,
    tier_name VARCHAR(100) NOT NULL,
    min_monthly_sales DECIMAL(10,2) NOT NULL,
    max_monthly_sales DECIMAL(10,2) NULL,
    commission_percentage DECIMAL(5,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tier_name (tier_name),
    INDEX idx_is_active (is_active),
    INDEX idx_sales_range (min_monthly_sales, max_monthly_sales)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default target-based commission tiers
INSERT INTO commission_target_tiers (tier_name, min_monthly_sales, max_monthly_sales, commission_percentage) VALUES
('Bronze Tier', 0.00, 99999.99, 1.00),
('Silver Tier', 100000.00, 249999.99, 2.00),
('Gold Tier', 250000.00, 499999.99, 3.00),
('Platinum Tier', 500000.00, 999999.99, 4.00),
('Diamond Tier', 1000000.00, NULL, 5.00);

-- Create monthly commission summary table
CREATE TABLE IF NOT EXISTS monthly_commission_summary (
    summary_id INT AUTO_INCREMENT PRIMARY KEY,
    contractor_id INT NOT NULL,
    month CHAR(7) NOT NULL, -- Format: YYYY-MM
    total_referred_sales DECIMAL(10,2) DEFAULT 0.00,
    total_own_purchases DECIMAL(10,2) DEFAULT 0.00,
    total_monthly_sales DECIMAL(10,2) DEFAULT 0.00,
    achieved_tier_id INT NULL,
    commission_percentage DECIMAL(5,2) DEFAULT 0.00,
    total_commission_amount DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('calculating', 'finalized', 'paid') DEFAULT 'calculating',
    calculation_date TIMESTAMP NULL,
    payment_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (contractor_id) REFERENCES contractors(contractor_id) ON DELETE CASCADE,
    FOREIGN KEY (achieved_tier_id) REFERENCES commission_target_tiers(tier_id),
    
    UNIQUE KEY unique_contractor_month (contractor_id, month),
    INDEX idx_contractor_id (contractor_id),
    INDEX idx_month (month),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create detailed commission transactions table for target-based system
CREATE TABLE IF NOT EXISTS commission_transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    monthly_summary_id INT NOT NULL,
    contractor_id INT NOT NULL,
    sale_id INT NULL, -- Links to actual sale record
    customer_id INT NULL, -- NULL if contractor's own purchase
    transaction_type ENUM('referred_sale', 'own_purchase') NOT NULL,
    sale_amount DECIMAL(10,2) NOT NULL,
    transaction_date DATE NOT NULL,
    commission_percentage DECIMAL(5,2) NOT NULL,
    commission_amount DECIMAL(10,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (monthly_summary_id) REFERENCES monthly_commission_summary(summary_id) ON DELETE CASCADE,
    FOREIGN KEY (contractor_id) REFERENCES contractors(contractor_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    
    INDEX idx_monthly_summary_id (monthly_summary_id),
    INDEX idx_contractor_id (contractor_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_transaction_type (transaction_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create view for easy target-based commission reporting
CREATE OR REPLACE VIEW target_commission_summary AS
SELECT 
    mcs.summary_id,
    mcs.contractor_id,
    c.contractor_name,
    c.contact_info as contractor_contact,
    mcs.month,
    mcs.total_referred_sales,
    mcs.total_own_purchases,
    mcs.total_monthly_sales,
    
    -- Current tier information
    ctt.tier_name as achieved_tier,
    mcs.commission_percentage,
    mcs.total_commission_amount,
    mcs.status,
    
    -- Counts
    COUNT(ct.transaction_id) as total_transactions,
    SUM(CASE WHEN ct.transaction_type = 'referred_sale' THEN 1 ELSE 0 END) as referred_sales_count,
    SUM(CASE WHEN ct.transaction_type = 'own_purchase' THEN 1 ELSE 0 END) as own_purchases_count,
    
    -- Progress to next tier
    (SELECT MIN(next_tier.min_monthly_sales) 
     FROM commission_target_tiers next_tier 
     WHERE next_tier.min_monthly_sales > mcs.total_monthly_sales 
     AND next_tier.is_active = TRUE) as next_tier_threshold,
    
    mcs.calculation_date,
    mcs.payment_date,
    mcs.created_at,
    mcs.updated_at
    
FROM monthly_commission_summary mcs
INNER JOIN contractors c ON mcs.contractor_id = c.contractor_id
LEFT JOIN commission_target_tiers ctt ON mcs.achieved_tier_id = ctt.tier_id
LEFT JOIN commission_transactions ct ON mcs.summary_id = ct.monthly_summary_id
GROUP BY mcs.summary_id
ORDER BY mcs.month DESC, mcs.total_monthly_sales DESC;
