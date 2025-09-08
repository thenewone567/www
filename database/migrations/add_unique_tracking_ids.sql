-- Migration: Add 12-digit unique tracking IDs for Users, Contractors, and Customers
-- This migration adds unique tracking IDs to help track activities across the system

-- Create contractors table if it doesn't exist
CREATE TABLE IF NOT EXISTS contractors (
    contractor_id INT AUTO_INCREMENT PRIMARY KEY,
    contractor_name VARCHAR(100) NOT NULL,
    contact_info VARCHAR(255) DEFAULT NULL,
    commission_rate DECIMAL(5,2) DEFAULT 5.00,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_contractor_name (contractor_name),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add unique_id columns to existing tables
-- For users table
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS unique_id VARCHAR(12) UNIQUE AFTER user_id,
ADD COLUMN IF NOT EXISTS name VARCHAR(100) AFTER username,
ADD COLUMN IF NOT EXISTS email VARCHAR(255) AFTER name,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER is_active,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- For customers table  
ALTER TABLE customers 
ADD COLUMN IF NOT EXISTS unique_id VARCHAR(12) UNIQUE AFTER customer_id,
ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER credit_limit,
ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

-- For contractors table
ALTER TABLE contractors 
ADD COLUMN IF NOT EXISTS unique_id VARCHAR(12) UNIQUE AFTER contractor_id;

-- Create indexes for the unique_id fields
CREATE INDEX IF NOT EXISTS idx_users_unique_id ON users(unique_id);
CREATE INDEX IF NOT EXISTS idx_customers_unique_id ON customers(unique_id);
CREATE INDEX IF NOT EXISTS idx_contractors_unique_id ON contractors(unique_id);

-- Create function to generate 12-digit unique ID
DELIMITER $$

CREATE FUNCTION IF NOT EXISTS generate_unique_id(entity_type VARCHAR(10)) 
RETURNS VARCHAR(12)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE new_id VARCHAR(12);
    DECLARE counter INT DEFAULT 0;
    DECLARE prefix VARCHAR(2);
    DECLARE timestamp_part VARCHAR(8);
    DECLARE random_part VARCHAR(2);
    DECLARE exists_count INT DEFAULT 1;
    
    -- Set prefix based on entity type
    CASE entity_type
        WHEN 'user' THEN SET prefix = 'US';
        WHEN 'customer' THEN SET prefix = 'CU';
        WHEN 'contractor' THEN SET prefix = 'CO';
        ELSE SET prefix = 'XX';
    END CASE;
    
    -- Generate timestamp part (8 digits from current timestamp)
    SET timestamp_part = RIGHT(UNIX_TIMESTAMP(), 8);
    
    -- Loop until we find a unique ID
    WHILE exists_count > 0 AND counter < 100 DO
        -- Generate 2-digit random number
        SET random_part = LPAD(FLOOR(RAND() * 100), 2, '0');
        
        -- Combine parts: 2-char prefix + 8-char timestamp + 2-char random = 12 chars
        SET new_id = CONCAT(prefix, timestamp_part, random_part);
        
        -- Check if this ID already exists in any table
        SET exists_count = (
            SELECT COUNT(*) FROM (
                SELECT unique_id FROM users WHERE unique_id = new_id
                UNION ALL
                SELECT unique_id FROM customers WHERE unique_id = new_id  
                UNION ALL
                SELECT unique_id FROM contractors WHERE unique_id = new_id
            ) AS combined_ids
        );
        
        SET counter = counter + 1;
    END WHILE;
    
    -- If we couldn't generate unique ID, append counter
    IF exists_count > 0 THEN
        SET new_id = CONCAT(LEFT(new_id, 10), LPAD(counter, 2, '0'));
    END IF;
    
    RETURN new_id;
END$$

DELIMITER ;

-- Create stored procedure to generate and assign unique IDs to existing records
DELIMITER $$

CREATE PROCEDURE IF NOT EXISTS assign_unique_ids()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE current_id INT;
    DECLARE new_unique_id VARCHAR(12);
    
    -- Cursor for users without unique_id
    DECLARE user_cursor CURSOR FOR 
        SELECT user_id FROM users WHERE unique_id IS NULL OR unique_id = '';
    
    -- Cursor for customers without unique_id
    DECLARE customer_cursor CURSOR FOR 
        SELECT customer_id FROM customers WHERE unique_id IS NULL OR unique_id = '';
        
    -- Cursor for contractors without unique_id
    DECLARE contractor_cursor CURSOR FOR 
        SELECT contractor_id FROM contractors WHERE unique_id IS NULL OR unique_id = '';
    
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Update users
    OPEN user_cursor;
    user_loop: LOOP
        FETCH user_cursor INTO current_id;
        IF done THEN
            LEAVE user_loop;
        END IF;
        
        SET new_unique_id = generate_unique_id('user');
        UPDATE users SET unique_id = new_unique_id WHERE user_id = current_id;
    END LOOP;
    CLOSE user_cursor;
    
    -- Reset done flag
    SET done = FALSE;
    
    -- Update customers
    OPEN customer_cursor;
    customer_loop: LOOP
        FETCH customer_cursor INTO current_id;
        IF done THEN
            LEAVE customer_loop;
        END IF;
        
        SET new_unique_id = generate_unique_id('customer');
        UPDATE customers SET unique_id = new_unique_id WHERE customer_id = current_id;
    END LOOP;
    CLOSE customer_cursor;
    
    -- Reset done flag
    SET done = FALSE;
    
    -- Update contractors
    OPEN contractor_cursor;
    contractor_loop: LOOP
        FETCH contractor_cursor INTO current_id;
        IF done THEN
            LEAVE contractor_loop;
        END IF;
        
        SET new_unique_id = generate_unique_id('contractor');
        UPDATE contractors SET unique_id = new_unique_id WHERE contractor_id = current_id;
    END LOOP;
    CLOSE contractor_cursor;
    
END$$

DELIMITER ;

-- Insert some sample contractors if the table is empty
INSERT IGNORE INTO contractors (contractor_name, contact_info, commission_rate, status) VALUES
('John Smith', 'phone: +91-9876543210, email: john@example.com', 5.00, 'active'),
('Sarah Johnson', 'phone: +91-9876543211, email: sarah@example.com', 4.50, 'active'),
('Mike Davis', 'phone: +91-9876543212, email: mike@example.com', 5.50, 'active');

-- Execute the procedure to assign unique IDs to existing records
CALL assign_unique_ids();

-- Create triggers to auto-generate unique IDs for new records
DELIMITER $$

CREATE TRIGGER IF NOT EXISTS before_insert_users
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.unique_id IS NULL OR NEW.unique_id = '' THEN
        SET NEW.unique_id = generate_unique_id('user');
    END IF;
END$$

CREATE TRIGGER IF NOT EXISTS before_insert_customers  
BEFORE INSERT ON customers
FOR EACH ROW
BEGIN
    IF NEW.unique_id IS NULL OR NEW.unique_id = '' THEN
        SET NEW.unique_id = generate_unique_id('customer');
    END IF;
END$$

CREATE TRIGGER IF NOT EXISTS before_insert_contractors
BEFORE INSERT ON contractors
FOR EACH ROW
BEGIN
    IF NEW.unique_id IS NULL OR NEW.unique_id = '' THEN
        SET NEW.unique_id = generate_unique_id('contractor');
    END IF;
END$$

DELIMITER ;

-- Add some useful views for tracking
CREATE OR REPLACE VIEW user_tracking AS
SELECT 
    user_id,
    unique_id,
    username,
    name,
    email,
    is_active,
    created_at,
    'USER' as entity_type
FROM users;

CREATE OR REPLACE VIEW customer_tracking AS
SELECT 
    customer_id,
    unique_id, 
    customer_name as name,
    contact_info,
    credit_limit,
    created_at,
    'CUSTOMER' as entity_type
FROM customers;

CREATE OR REPLACE VIEW contractor_tracking AS
SELECT 
    contractor_id,
    unique_id,
    contractor_name as name, 
    contact_info,
    commission_rate,
    status,
    created_at,
    'CONTRACTOR' as entity_type
FROM contractors;

CREATE OR REPLACE VIEW all_entities_tracking AS
SELECT unique_id, name, entity_type, created_at FROM user_tracking
UNION ALL
SELECT unique_id, name, entity_type, created_at FROM customer_tracking  
UNION ALL
SELECT unique_id, name, entity_type, created_at FROM contractor_tracking
ORDER BY created_at DESC;

-- Verification queries (commented out - uncomment to run manually)
-- SELECT 'Users with Unique IDs' as info, COUNT(*) as count FROM users WHERE unique_id IS NOT NULL;
-- SELECT 'Customers with Unique IDs' as info, COUNT(*) as count FROM customers WHERE unique_id IS NOT NULL;
-- SELECT 'Contractors with Unique IDs' as info, COUNT(*) as count FROM contractors WHERE unique_id IS NOT NULL;
-- SELECT * FROM all_entities_tracking LIMIT 10;
