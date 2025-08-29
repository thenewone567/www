-- ============================================
-- MULTI-LOCATION ENTERPRISE SYSTEM MIGRATION
-- Transform single warehouse into multi-location chain
-- ============================================

-- 1. CREATE COMPANIES/ORGANIZATIONS TABLE
CREATE TABLE IF NOT EXISTS `companies` (
  `company_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) NOT NULL,
  `company_code` varchar(50) NOT NULL UNIQUE,
  `address` text,
  `phone` varchar(20),
  `email` varchar(100),
  `tax_number` varchar(50),
  `logo_path` varchar(255),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. CREATE BUSINESS LOCATIONS TABLE
CREATE TABLE IF NOT EXISTS `business_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `location_code` varchar(50) NOT NULL,
  `location_type` enum('store', 'warehouse', 'combined') NOT NULL DEFAULT 'combined',
  `city` varchar(100) NOT NULL,
  `state` varchar(100),
  `address` text,
  `phone` varchar(20),
  `email` varchar(100),
  `manager_user_id` int(11),
  `is_active` tinyint(1) DEFAULT 1,
  `operating_hours` JSON,
  `timezone` varchar(50) DEFAULT 'Asia/Kolkata',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `unique_location_code` (`company_id`, `location_code`),
  KEY `idx_company` (`company_id`),
  KEY `idx_manager` (`manager_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. ENHANCE USERS TABLE FOR MULTI-LOCATION
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `company_id` int(11) DEFAULT 1 AFTER `role_id`,
ADD COLUMN IF NOT EXISTS `default_location_id` int(11) DEFAULT NULL AFTER `company_id`,
ADD COLUMN IF NOT EXISTS `employee_id` varchar(50) DEFAULT NULL AFTER `default_location_id`,
ADD COLUMN IF NOT EXISTS `full_name` varchar(255) DEFAULT NULL AFTER `employee_id`,
ADD COLUMN IF NOT EXISTS `email` varchar(255) DEFAULT NULL AFTER `full_name`,
ADD COLUMN IF NOT EXISTS `phone` varchar(20) DEFAULT NULL AFTER `email`,
ADD COLUMN IF NOT EXISTS `hire_date` date DEFAULT NULL AFTER `phone`,
ADD COLUMN IF NOT EXISTS `access_level` enum('global', 'location', 'limited') DEFAULT 'location' AFTER `hire_date`;

-- 4. CREATE USER LOCATION ASSIGNMENTS
CREATE TABLE IF NOT EXISTS `user_location_assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `access_type` enum('full', 'read_only', 'limited') DEFAULT 'full',
  `assigned_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `assigned_by` int(11),
  PRIMARY KEY (`assignment_id`),
  UNIQUE KEY `unique_user_location` (`user_id`, `location_id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_location` (`location_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 5. ENHANCE ROLES FOR MULTI-LOCATION
CREATE TABLE IF NOT EXISTS `location_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  `role_code` varchar(50) NOT NULL UNIQUE,
  `description` text,
  `access_level` enum('global', 'location', 'department') NOT NULL DEFAULT 'location',
  `permissions` JSON,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 6. UPDATE WAREHOUSE LOCATIONS FOR MULTI-LOCATION
-- First, backup existing warehouse_locations
CREATE TABLE IF NOT EXISTS `warehouse_locations_backup` AS SELECT * FROM `warehouse_locations`;

-- Add location relationship to warehouse locations
ALTER TABLE `warehouse_locations` 
ADD COLUMN IF NOT EXISTS `business_location_id` int(11) DEFAULT 1 AFTER `location_id`,
ADD COLUMN IF NOT EXISTS `zone` varchar(100) DEFAULT NULL AFTER `shelf`,
ADD COLUMN IF NOT EXISTS `location_type` enum('storage', 'bin', 'receiving', 'dock', 'shipping') DEFAULT 'storage' AFTER `zone`,
ADD COLUMN IF NOT EXISTS `capacity_cubic_feet` decimal(10,2) DEFAULT NULL AFTER `location_type`,
ADD COLUMN IF NOT EXISTS `max_weight_kg` decimal(10,2) DEFAULT NULL AFTER `capacity_cubic_feet`,
ADD COLUMN IF NOT EXISTS `climate_controlled` tinyint(1) DEFAULT 0 AFTER `max_weight_kg`,
ADD COLUMN IF NOT EXISTS `standardized_address` varchar(255) DEFAULT NULL AFTER `climate_controlled`,
ADD COLUMN IF NOT EXISTS `is_active` tinyint(1) DEFAULT 1 AFTER `standardized_address`;

-- 7. UPDATE INVENTORY FOR MULTI-LOCATION TRACKING
ALTER TABLE `inventory` 
ADD COLUMN IF NOT EXISTS `business_location_id` int(11) DEFAULT 1 AFTER `location_id`;

-- 8. SALES AND PURCHASES LOCATION TRACKING
ALTER TABLE `sales` 
ADD COLUMN IF NOT EXISTS `business_location_id` int(11) DEFAULT 1 AFTER `customer_id`;

-- Check if purchases table exists, if not create basic structure
CREATE TABLE IF NOT EXISTS `purchases` (
  `purchase_id` int(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` int(11),
  `business_location_id` int(11) DEFAULT 1,
  `purchase_date` date NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('pending', 'completed', 'cancelled') DEFAULT 'pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`purchase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. INSERT DEFAULT COMPANY
INSERT IGNORE INTO `companies` (`company_id`, `company_name`, `company_code`, `address`) 
VALUES (1, 'Hardware Store Chain', 'HSC', 'Head Office Address');

-- 10. INSERT BUSINESS LOCATIONS (Your 3 Stores)
INSERT IGNORE INTO `business_locations` 
(`location_id`, `company_id`, `location_name`, `location_code`, `location_type`, `city`, `address`) 
VALUES 
(1, 1, 'Kurukshetra Store & Warehouse', 'KRK', 'combined', 'Kurukshetra', 'Kurukshetra Store Address'),
(2, 1, 'Ambala Store & Warehouse', 'AMB', 'combined', 'Ambala', 'Ambala Store Address'),  
(3, 1, 'Panchkula Store & Warehouse', 'PKL', 'combined', 'Panchkula', 'Panchkula Store Address');

-- 11. INSERT LOCATION-BASED ROLES
INSERT IGNORE INTO `location_roles` 
(`role_name`, `role_code`, `description`, `access_level`, `permissions`) 
VALUES 
('Corporate Admin', 'CORP_ADMIN', 'Full system access across all locations', 'global', 
 '{"sales": ["create", "read", "update", "delete"], "inventory": ["create", "read", "update", "delete"], "users": ["create", "read", "update", "delete"], "locations": ["create", "read", "update", "delete"], "reports": ["read", "export"]}'),
('Location Manager', 'LOC_MANAGER', 'Full access to assigned location', 'location',
 '{"sales": ["create", "read", "update", "delete"], "inventory": ["create", "read", "update", "delete"], "customers": ["create", "read", "update"], "reports": ["read"]}'),
('Store Staff', 'STORE_STAFF', 'Sales and customer service', 'department',
 '{"sales": ["create", "read"], "customers": ["read", "update"], "inventory": ["read"]}'),
('Warehouse Staff', 'WAREHOUSE_STAFF', 'Inventory and warehouse operations', 'department',
 '{"inventory": ["create", "read", "update"], "receiving": ["create", "read"], "transfers": ["create", "read"]}');

-- 12. UPDATE EXISTING USERS WITH DEFAULT ASSIGNMENTS
UPDATE `users` SET 
  `company_id` = 1,
  `default_location_id` = 1,
  `access_level` = CASE 
    WHEN `role_id` = 1 THEN 'global'  -- Assuming role_id 1 is admin
    ELSE 'location'
  END
WHERE `company_id` IS NULL;

-- 13. ASSIGN ALL EXISTING USERS TO KURUKSHETRA LOCATION (DEFAULT)
INSERT IGNORE INTO `user_location_assignments` (`user_id`, `location_id`, `access_type`)
SELECT `user_id`, 1, 'full' FROM `users` WHERE `is_active` = 1;

-- 14. UPDATE EXISTING WAREHOUSE LOCATIONS TO KURUKSHETRA
UPDATE `warehouse_locations` SET 
  `business_location_id` = 1,
  `zone` = 'Kurukshetra',
  `is_active` = 1
WHERE `business_location_id` IS NULL;

-- 15. UPDATE EXISTING INVENTORY TO KURUKSHETRA  
UPDATE `inventory` SET `business_location_id` = 1 WHERE `business_location_id` IS NULL;

-- 16. UPDATE EXISTING SALES TO KURUKSHETRA
UPDATE `sales` SET `business_location_id` = 1 WHERE `business_location_id` IS NULL;

-- 17. ADD FOREIGN KEY CONSTRAINTS
ALTER TABLE `business_locations` 
ADD CONSTRAINT `fk_business_location_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
ADD CONSTRAINT `fk_business_location_manager` FOREIGN KEY (`manager_user_id`) REFERENCES `users` (`user_id`);

ALTER TABLE `users`
ADD CONSTRAINT `fk_user_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`),
ADD CONSTRAINT `fk_user_default_location` FOREIGN KEY (`default_location_id`) REFERENCES `business_locations` (`location_id`);

ALTER TABLE `user_location_assignments`
ADD CONSTRAINT `fk_assignment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_assignment_location` FOREIGN KEY (`location_id`) REFERENCES `business_locations` (`location_id`) ON DELETE CASCADE;

ALTER TABLE `warehouse_locations`
ADD CONSTRAINT `fk_warehouse_business_location` FOREIGN KEY (`business_location_id`) REFERENCES `business_locations` (`location_id`);

ALTER TABLE `inventory`
ADD CONSTRAINT `fk_inventory_business_location` FOREIGN KEY (`business_location_id`) REFERENCES `business_locations` (`location_id`);

-- 18. CREATE INDEXES FOR PERFORMANCE
CREATE INDEX `idx_user_company_location` ON `users` (`company_id`, `default_location_id`);
CREATE INDEX `idx_warehouse_location_business` ON `warehouse_locations` (`business_location_id`, `location_type`);
CREATE INDEX `idx_inventory_location` ON `inventory` (`business_location_id`, `product_id`);
CREATE INDEX `idx_sales_location` ON `sales` (`business_location_id`, `sale_date`);

-- 19. CREATE SYSTEM SETTINGS FOR MULTI-LOCATION
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('multi_location_enabled', 'true'),
('default_company_id', '1'),
('inter_location_transfers', 'true'),
('centralized_inventory', 'false'),
('location_based_pricing', 'false');

-- MIGRATION COMPLETE
-- Your system is now ready for multi-location enterprise operations!
