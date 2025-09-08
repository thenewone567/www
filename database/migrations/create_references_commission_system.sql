-- Migration: Create Customer References and Commission System Tables
-- This creates the infrastructure for tracking contractor referrals and commissions

-- Create table to track customer referrals made by contractors
CREATE TABLE IF NOT EXISTS customer_references (
    reference_id INT AUTO_INCREMENT PRIMARY KEY,
    contractor_id INT NOT NULL,
    customer_id INT NOT NULL,
    reference_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    notes TEXT,
    status ENUM('active', 'inactive', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (contractor_id) REFERENCES contractors(contractor_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    
    -- Ensure one contractor can refer same customer only once
    UNIQUE KEY unique_contractor_customer (contractor_id, customer_id),
    
    -- Indexes for performance
    INDEX idx_contractor_id (contractor_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_reference_date (reference_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create commission tracking table
CREATE TABLE IF NOT EXISTS commissions (
    commission_id INT AUTO_INCREMENT PRIMARY KEY,
    reference_id INT NOT NULL,
    sale_id INT NOT NULL, -- Links to sales table
    contractor_id INT NOT NULL,
    customer_id INT NOT NULL,
    sale_amount DECIMAL(10,2) NOT NULL,
    commission_rate DECIMAL(5,2) NOT NULL DEFAULT 5.00, -- Percentage (e.g., 5.00 = 5%)
    commission_amount DECIMAL(10,2) NOT NULL,
    commission_date DATE NOT NULL DEFAULT (CURRENT_DATE),
    status ENUM('pending', 'approved', 'paid', 'cancelled') DEFAULT 'pending',
    payment_date DATE NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign key constraints
    FOREIGN KEY (reference_id) REFERENCES customer_references(reference_id) ON DELETE CASCADE,
    FOREIGN KEY (contractor_id) REFERENCES contractors(contractor_id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
    -- Note: sale_id FK would link to sales table when available
    
    -- Indexes for performance
    INDEX idx_reference_id (reference_id),
    INDEX idx_sale_id (sale_id),
    INDEX idx_contractor_id (contractor_id),
    INDEX idx_customer_id (customer_id),
    INDEX idx_commission_date (commission_date),
    INDEX idx_status (status),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create commission rates configuration table
CREATE TABLE IF NOT EXISTS commission_rates (
    rate_id INT AUTO_INCREMENT PRIMARY KEY,
    rate_name VARCHAR(100) NOT NULL,
    rate_percentage DECIMAL(5,2) NOT NULL,
    min_sale_amount DECIMAL(10,2) DEFAULT 0.00,
    max_sale_amount DECIMAL(10,2) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_is_active (is_active),
    INDEX idx_rate_name (rate_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default commission rates
INSERT INTO commission_rates (rate_name, rate_percentage, min_sale_amount, max_sale_amount) VALUES
('Standard Commission', 5.00, 0.00, 999.99),
('High Value Commission', 3.00, 1000.00, 4999.99),
('Premium Commission', 2.50, 5000.00, NULL),
('Bulk Discount Commission', 1.50, 10000.00, NULL);

-- Create view for easier commission reporting
CREATE OR REPLACE VIEW commission_summary AS
SELECT 
    c.commission_id,
    c.sale_id,
    c.commission_date,
    c.sale_amount,
    c.commission_rate,
    c.commission_amount,
    c.status,
    c.payment_date,
    
    -- Contractor information
    cr.contractor_id,
    con.contractor_name,
    con.contact_info as contractor_contact,
    
    -- Customer information
    cr.customer_id,
    cust.customer_name,
    cust.contact_info as customer_contact,
    
    -- Reference information
    cr.reference_date,
    cr.notes as reference_notes,
    c.notes as commission_notes
    
FROM commissions c
INNER JOIN customer_references cr ON c.reference_id = cr.reference_id
INNER JOIN contractors con ON c.contractor_id = con.contractor_id
INNER JOIN customers cust ON c.customer_id = cust.customer_id
ORDER BY c.commission_date DESC;
