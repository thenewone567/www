-- Migration: Add Customer Discount Credits and Reference System for POS
-- This creates a discount credit system similar to membership cards

-- Create customer_discount_credits table for accumulated discounts
CREATE TABLE IF NOT EXISTS customer_discount_credits (
    credit_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    unique_id VARCHAR(12) NOT NULL, -- Links to customer's unique ID
    credit_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    earned_from_sale_id INT NULL, -- Which sale generated this credit
    earned_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    expires_date DATE NULL, -- Optional expiration
    status ENUM('active', 'used', 'expired') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_customer_id (customer_id),
    INDEX idx_unique_id (unique_id),
    INDEX idx_status (status),
    INDEX idx_earned_date (earned_date),
    INDEX idx_expires_date (expires_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create contractor_commission_credits table for pending commissions
CREATE TABLE IF NOT EXISTS contractor_commission_credits (
    credit_id INT AUTO_INCREMENT PRIMARY KEY,
    contractor_id INT NOT NULL,
    unique_id VARCHAR(12) NOT NULL, -- Links to contractor's unique ID
    commission_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    earned_from_sale_id INT NULL, -- Which sale generated this commission
    reference_customer_id INT NULL, -- Which customer was referred
    commission_rate DECIMAL(5,2) NOT NULL DEFAULT 5.00, -- Percentage used
    earned_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    status ENUM('pending', 'approved', 'paid', 'cancelled') DEFAULT 'pending',
    payment_date DATE NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (contractor_id) REFERENCES contractors(contractor_id) ON DELETE CASCADE,
    FOREIGN KEY (reference_customer_id) REFERENCES customers(customer_id) ON DELETE SET NULL,
    
    -- Indexes for performance
    INDEX idx_contractor_id (contractor_id),
    INDEX idx_unique_id (unique_id),
    INDEX idx_reference_customer_id (reference_customer_id),
    INDEX idx_status (status),
    INDEX idx_earned_date (earned_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create discount_transactions table to track discount usage
CREATE TABLE IF NOT EXISTS discount_transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL, -- Links to sales table
    customer_id INT NOT NULL,
    customer_unique_id VARCHAR(12) NOT NULL,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    credits_used DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    credits_earned DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    transaction_type ENUM('discount_applied', 'credit_earned', 'credit_used') NOT NULL,
    transaction_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    
    -- Indexes for performance
    INDEX idx_sale_id (sale_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_customer_unique_id (customer_unique_id),
    INDEX idx_transaction_date (transaction_date),
    INDEX idx_transaction_type (transaction_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add discount credit balance to customers table
ALTER TABLE customers 
ADD COLUMN IF NOT EXISTS discount_credit_balance DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Available discount credits like membership points',
ADD COLUMN IF NOT EXISTS total_discount_earned DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Total discount credits ever earned',
ADD COLUMN IF NOT EXISTS total_discount_used DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Total discount credits used';

-- Add commission tracking to contractors table  
ALTER TABLE contractors
ADD COLUMN IF NOT EXISTS pending_commission_balance DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Pending commissions to be paid',
ADD COLUMN IF NOT EXISTS total_commission_earned DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Total commissions earned',
ADD COLUMN IF NOT EXISTS total_commission_paid DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Total commissions paid out';

-- Create indexes for new columns
CREATE INDEX IF NOT EXISTS idx_customers_discount_balance ON customers(discount_credit_balance);
CREATE INDEX IF NOT EXISTS idx_customers_unique_id ON customers(unique_id);
CREATE INDEX IF NOT EXISTS idx_contractors_unique_id ON contractors(unique_id);
CREATE INDEX IF NOT EXISTS idx_contractors_commission_balance ON contractors(pending_commission_balance);

-- Create view for customer discount summary
CREATE OR REPLACE VIEW customer_discount_summary AS
SELECT 
    c.customer_id,
    c.unique_id,
    c.customer_name,
    c.discount_credit_balance,
    c.total_discount_earned,
    c.total_discount_used,
    COUNT(cdc.credit_id) as active_credits_count,
    SUM(CASE WHEN cdc.status = 'active' THEN cdc.credit_amount ELSE 0 END) as available_credits,
    MIN(cdc.expires_date) as earliest_expiry
FROM customers c
LEFT JOIN customer_discount_credits cdc ON c.customer_id = cdc.customer_id AND cdc.status = 'active'
GROUP BY c.customer_id, c.unique_id, c.customer_name, c.discount_credit_balance, c.total_discount_earned, c.total_discount_used;

-- Create view for contractor commission summary
CREATE OR REPLACE VIEW contractor_commission_summary AS
SELECT 
    co.contractor_id,
    co.unique_id,
    co.contractor_name,
    co.pending_commission_balance,
    co.total_commission_earned,
    co.total_commission_paid,
    COUNT(ccc.credit_id) as pending_commissions_count,
    SUM(CASE WHEN ccc.status = 'pending' THEN ccc.commission_amount ELSE 0 END) as pending_amount,
    COUNT(CASE WHEN ccc.status = 'approved' THEN 1 END) as approved_count,
    SUM(CASE WHEN ccc.status = 'approved' THEN ccc.commission_amount ELSE 0 END) as approved_amount
FROM contractors co
LEFT JOIN contractor_commission_credits ccc ON co.contractor_id = ccc.contractor_id
GROUP BY co.contractor_id, co.unique_id, co.contractor_name, co.pending_commission_balance, co.total_commission_earned, co.total_commission_paid;

-- Insert default discount credit settings
INSERT IGNORE INTO settings (setting_key, setting_value, description) VALUES
('discount_credit_rate', '2.0', 'Percentage of purchase amount converted to discount credits (2% default)'),
('commission_rate_default', '5.0', 'Default commission rate for contractor referrals (5% default)'),
('discount_credit_minimum_purchase', '10.00', 'Minimum purchase amount to earn discount credits'),
('commission_minimum_sale', '50.00', 'Minimum sale amount to earn commission'),
('discount_credit_expiry_days', '365', 'Days until discount credits expire (0 = never expire)'),
('allow_stacked_discounts', '1', 'Allow combining discount credits with other discounts (1=yes, 0=no)');

-- Create a trigger to update customer discount balance when credits are added/used
DELIMITER $$

CREATE TRIGGER IF NOT EXISTS update_customer_discount_balance
AFTER INSERT ON customer_discount_credits
FOR EACH ROW
BEGIN
    UPDATE customers 
    SET discount_credit_balance = (
        SELECT COALESCE(SUM(credit_amount), 0) 
        FROM customer_discount_credits 
        WHERE customer_id = NEW.customer_id AND status = 'active'
    ),
    total_discount_earned = (
        SELECT COALESCE(SUM(credit_amount), 0) 
        FROM customer_discount_credits 
        WHERE customer_id = NEW.customer_id
    )
    WHERE customer_id = NEW.customer_id;
END$$

CREATE TRIGGER IF NOT EXISTS update_customer_discount_balance_on_update
AFTER UPDATE ON customer_discount_credits
FOR EACH ROW
BEGIN
    UPDATE customers 
    SET discount_credit_balance = (
        SELECT COALESCE(SUM(credit_amount), 0) 
        FROM customer_discount_credits 
        WHERE customer_id = NEW.customer_id AND status = 'active'
    ),
    total_discount_used = (
        SELECT COALESCE(SUM(credit_amount), 0) 
        FROM customer_discount_credits 
        WHERE customer_id = NEW.customer_id AND status = 'used'
    )
    WHERE customer_id = NEW.customer_id;
END$$

CREATE TRIGGER IF NOT EXISTS update_contractor_commission_balance
AFTER INSERT ON contractor_commission_credits
FOR EACH ROW
BEGIN
    UPDATE contractors 
    SET pending_commission_balance = (
        SELECT COALESCE(SUM(commission_amount), 0) 
        FROM contractor_commission_credits 
        WHERE contractor_id = NEW.contractor_id AND status IN ('pending', 'approved')
    ),
    total_commission_earned = (
        SELECT COALESCE(SUM(commission_amount), 0) 
        FROM contractor_commission_credits 
        WHERE contractor_id = NEW.contractor_id
    )
    WHERE contractor_id = NEW.contractor_id;
END$$

CREATE TRIGGER IF NOT EXISTS update_contractor_commission_balance_on_update
AFTER UPDATE ON contractor_commission_credits
FOR EACH ROW
BEGIN
    UPDATE contractors 
    SET pending_commission_balance = (
        SELECT COALESCE(SUM(commission_amount), 0) 
        FROM contractor_commission_credits 
        WHERE contractor_id = NEW.contractor_id AND status IN ('pending', 'approved')
    ),
    total_commission_paid = (
        SELECT COALESCE(SUM(commission_amount), 0) 
        FROM contractor_commission_credits 
        WHERE contractor_id = NEW.contractor_id AND status = 'paid'
    )
    WHERE contractor_id = NEW.contractor_id;
END$$

DELIMITER ;
