-- Simple Unique ID Migration
-- Add unique_id columns to tables

-- Add unique_id to users table if it doesn't exist
SELECT COUNT(*) FROM information_schema.columns 
WHERE table_schema = DATABASE() AND table_name = 'users' AND column_name = 'unique_id' INTO @users_has_unique_id;

SET @sql = IF(@users_has_unique_id = 0, 
    'ALTER TABLE users ADD COLUMN unique_id VARCHAR(12) UNIQUE', 
    'SELECT "users.unique_id already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Add unique_id to customers table if it doesn't exist  
SELECT COUNT(*) FROM information_schema.columns 
WHERE table_schema = DATABASE() AND table_name = 'customers' AND column_name = 'unique_id' INTO @customers_has_unique_id;

SET @sql = IF(@customers_has_unique_id = 0, 
    'ALTER TABLE customers ADD COLUMN unique_id VARCHAR(12) UNIQUE', 
    'SELECT "customers.unique_id already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Create contractors table if it doesn't exist
CREATE TABLE IF NOT EXISTS contractors (
    contractor_id INT PRIMARY KEY AUTO_INCREMENT,
    unique_id VARCHAR(12) UNIQUE,
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
    commission_type VARCHAR(20) DEFAULT 'percentage',
    commission_rate DECIMAL(10,2) DEFAULT 0.00,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add unique_id to contractors table if it doesn't exist
SELECT COUNT(*) FROM information_schema.columns 
WHERE table_schema = DATABASE() AND table_name = 'contractors' AND column_name = 'unique_id' INTO @contractors_has_unique_id;

SET @sql = IF(@contractors_has_unique_id = 0, 
    'ALTER TABLE contractors ADD COLUMN unique_id VARCHAR(12) UNIQUE AFTER contractor_id', 
    'SELECT "contractors.unique_id already exists"');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SELECT 'Unique ID columns added successfully!' as result;
