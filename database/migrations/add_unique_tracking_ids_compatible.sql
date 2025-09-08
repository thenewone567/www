-- MySQL Compatible Unique ID Migration
-- This migration adds unique tracking IDs for users, customers, and contractors

-- First, ensure contractors table exists
CREATE TABLE IF NOT EXISTS contractors (
    contractor_id INT PRIMARY KEY AUTO_INCREMENT,
    contractor_name VARCHAR(100),
    company_name VARCHAR(100),
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(50),
    postal_code VARCHAR(20),
    specialization TEXT,
    license_number VARCHAR(100),
    commission_type ENUM('percentage', 'fixed') DEFAULT 'percentage',
    commission_rate DECIMAL(10,2) DEFAULT 0.00,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add unique_id columns with compatibility check
-- Users table
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns 
                     WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'unique_id');
SET @sql = IF(@column_exists = 0, 
              'ALTER TABLE users ADD COLUMN unique_id VARCHAR(12) UNIQUE AFTER user_id', 
              'SELECT "unique_id column already exists in users table" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Customers table
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns 
                     WHERE table_schema = DATABASE() AND table_name = 'customers' AND column_name = 'unique_id');
SET @sql = IF(@column_exists = 0, 
              'ALTER TABLE customers ADD COLUMN unique_id VARCHAR(12) UNIQUE AFTER customer_id', 
              'SELECT "unique_id column already exists in customers table" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Contractors table
SET @column_exists = (SELECT COUNT(*) FROM information_schema.columns 
                     WHERE table_schema = DATABASE() AND table_name = 'contractors' AND column_name = 'unique_id');
SET @sql = IF(@column_exists = 0, 
              'ALTER TABLE contractors ADD COLUMN unique_id VARCHAR(12) UNIQUE AFTER contractor_id', 
              'SELECT "unique_id column already exists in contractors table" as message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create unique ID generation function
DELIMITER //
DROP FUNCTION IF EXISTS generate_unique_id//
CREATE FUNCTION generate_unique_id(entity_type VARCHAR(20)) 
RETURNS VARCHAR(12) 
READS SQL DATA 
DETERMINISTIC
BEGIN
    DECLARE prefix VARCHAR(2);
    DECLARE timestamp_part VARCHAR(8);
    DECLARE random_part VARCHAR(2);
    DECLARE new_id VARCHAR(12);
    DECLARE id_exists INT DEFAULT 1;
    DECLARE attempts INT DEFAULT 0;
    DECLARE max_attempts INT DEFAULT 100;
    
    -- Set prefix based on entity type
    CASE entity_type
        WHEN 'user' THEN SET prefix = 'US';
        WHEN 'customer' THEN SET prefix = 'CU';
        WHEN 'contractor' THEN SET prefix = 'CO';
        ELSE SET prefix = 'XX';
    END CASE;
    
    -- Generate timestamp part (8 digits from Unix timestamp)
    SET timestamp_part = RIGHT(UNIX_TIMESTAMP(), 8);
    
    -- Loop until we find a unique ID
    WHILE id_exists > 0 AND attempts < max_attempts DO
        -- Generate 2-digit random number
        SET random_part = LPAD(FLOOR(RAND() * 100), 2, '0');
        
        -- Combine parts
        SET new_id = CONCAT(prefix, timestamp_part, random_part);
        
        -- Check if ID exists in any table
        SET id_exists = 0;
        
        IF entity_type = 'user' THEN
            SELECT COUNT(*) INTO id_exists FROM users WHERE unique_id = new_id;
        ELSEIF entity_type = 'customer' THEN
            SELECT COUNT(*) INTO id_exists FROM customers WHERE unique_id = new_id;
        ELSEIF entity_type = 'contractor' THEN
            SELECT COUNT(*) INTO id_exists FROM contractors WHERE unique_id = new_id;
        END IF;
        
        SET attempts = attempts + 1;
    END WHILE;
    
    RETURN new_id;
END//
DELIMITER ;

-- Create triggers for automatic ID assignment
-- Users trigger
DROP TRIGGER IF EXISTS users_before_insert_unique_id;
DELIMITER //
CREATE TRIGGER users_before_insert_unique_id
BEFORE INSERT ON users
FOR EACH ROW
BEGIN
    IF NEW.unique_id IS NULL OR NEW.unique_id = '' THEN
        SET NEW.unique_id = generate_unique_id('user');
    END IF;
END//
DELIMITER ;

-- Customers trigger
DROP TRIGGER IF EXISTS customers_before_insert_unique_id;
DELIMITER //
CREATE TRIGGER customers_before_insert_unique_id
BEFORE INSERT ON customers
FOR EACH ROW
BEGIN
    IF NEW.unique_id IS NULL OR NEW.unique_id = '' THEN
        SET NEW.unique_id = generate_unique_id('customer');
    END IF;
END//
DELIMITER ;

-- Contractors trigger  
DROP TRIGGER IF EXISTS contractors_before_insert_unique_id;
DELIMITER //
CREATE TRIGGER contractors_before_insert_unique_id
BEFORE INSERT ON contractors
FOR EACH ROW
BEGIN
    IF NEW.unique_id IS NULL OR NEW.unique_id = '' THEN
        SET NEW.unique_id = generate_unique_id('contractor');
    END IF;
END//
DELIMITER ;

-- Create procedure to assign unique IDs to existing records
DELIMITER //
DROP PROCEDURE IF EXISTS assign_unique_ids//
CREATE PROCEDURE assign_unique_ids()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE current_id INT;
    DECLARE new_unique_id VARCHAR(12);
    
    -- Cursor for users without unique IDs
    DECLARE user_cursor CURSOR FOR 
        SELECT user_id FROM users WHERE unique_id IS NULL OR unique_id = '';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    -- Process users
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
    
    -- Reset for customers
    SET done = FALSE;
    BEGIN
        DECLARE customer_cursor CURSOR FOR 
            SELECT customer_id FROM customers WHERE unique_id IS NULL OR unique_id = '';
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
        
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
    END;
    
    -- Reset for contractors
    SET done = FALSE;
    BEGIN
        DECLARE contractor_cursor CURSOR FOR 
            SELECT contractor_id FROM contractors WHERE unique_id IS NULL OR unique_id = '';
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
        
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
    END;
END//
DELIMITER ;

-- Execute the assignment procedure
CALL assign_unique_ids();

-- Create view for tracking unique IDs
CREATE OR REPLACE VIEW unique_id_tracking AS
SELECT 
    'users' as entity_type,
    COUNT(*) as total_records,
    SUM(CASE WHEN unique_id IS NOT NULL AND unique_id != '' THEN 1 ELSE 0 END) as with_unique_id,
    SUM(CASE WHEN unique_id IS NULL OR unique_id = '' THEN 1 ELSE 0 END) as without_unique_id
FROM users
UNION ALL
SELECT 
    'customers' as entity_type,
    COUNT(*) as total_records,
    SUM(CASE WHEN unique_id IS NOT NULL AND unique_id != '' THEN 1 ELSE 0 END) as with_unique_id,
    SUM(CASE WHEN unique_id IS NULL OR unique_id = '' THEN 1 ELSE 0 END) as without_unique_id
FROM customers  
UNION ALL
SELECT 
    'contractors' as entity_type,
    COUNT(*) as total_records,
    SUM(CASE WHEN unique_id IS NOT NULL AND unique_id != '' THEN 1 ELSE 0 END) as with_unique_id,
    SUM(CASE WHEN unique_id IS NULL OR unique_id = '' THEN 1 ELSE 0 END) as without_unique_id
FROM contractors;

-- Insert sample data for testing
-- Sample users (will auto-generate unique IDs via trigger)
INSERT IGNORE INTO users (username, full_name, email, password_hash, role_id, is_active) VALUES
('admin', 'System Administrator', 'admin@hardware.com', '$2y$10$hash1', 1, 1),
('manager', 'Store Manager', 'manager@hardware.com', '$2y$10$hash2', 2, 1),
('employee', 'Store Employee', 'employee@hardware.com', '$2y$10$hash3', 3, 1);

-- Sample customers (will auto-generate unique IDs via trigger)
INSERT IGNORE INTO customers (customer_name, contact_info, credit_limit, status) VALUES
('ABC Construction', '{"contact_person":"John Smith","email":"john@abc.com","phone":"555-0101"}', 50000.00, 'active'),
('XYZ Contractors', '{"contact_person":"Jane Doe","email":"jane@xyz.com","phone":"555-0102"}', 25000.00, 'active'),
('Home Repair Pro', '{"contact_person":"Mike Johnson","email":"mike@repair.com","phone":"555-0103"}', 15000.00, 'active');

-- Sample contractors (will auto-generate unique IDs via trigger)
INSERT IGNORE INTO contractors (contractor_name, company_name, email, phone, specialization, commission_type, commission_rate) VALUES
('Robert Wilson', 'Wilson Electrical', 'rob@wilson.com', '555-0201', 'Electrical Work', 'percentage', 5.00),
('Sarah Davis', 'Davis Plumbing', 'sarah@davis.com', '555-0202', 'Plumbing Services', 'percentage', 4.50),
('Tom Brown', 'Brown Roofing', 'tom@brown.com', '555-0203', 'Roofing & Repairs', 'fixed', 100.00);

-- Verification queries
SELECT 'Migration completed successfully!' as status;
SELECT * FROM unique_id_tracking;
