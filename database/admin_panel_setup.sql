-- Admin Panel Database Setup
-- Run this script to create all necessary tables for the admin panel functionality

-- Create roles table if it doesn't exist
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  `permissions` JSON,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default roles
INSERT IGNORE INTO `roles` (`name`, `description`, `permissions`, `is_active`) VALUES
('admin', 'System Administrator', 
 '{"dashboard":["access"],"products":["create","read","update","delete"],"inventory":["read","update","transfer"],"sales":["create","read","update","delete"],"purchases":["create","read","update","delete","approve"],"customers":["create","read","update","delete"],"suppliers":["create","read","update","delete"],"reports":["read","export"],"settings":["read","update"],"admin":["access"],"users":["create","read","update","delete"],"roles":["create","read","update","delete"]}', 
 1),
('manager', 'Store Manager', 
 '{"dashboard":["access"],"products":["create","read","update"],"inventory":["read","update"],"sales":["create","read","update"],"purchases":["create","read","update"],"customers":["create","read","update"],"suppliers":["read","update"],"reports":["read","export"]}', 
 1),
('employee', 'Store Employee', 
 '{"dashboard":["access"],"products":["read"],"inventory":["read"],"sales":["create","read"],"customers":["read","update"],"reports":["read"]}', 
 1),
('cashier', 'Cashier', 
 '{"dashboard":["access"],"sales":["create","read"],"customers":["read"],"products":["read"]}', 
 1);

-- Update users table to include role_id if not exists
ALTER TABLE `users` 
ADD COLUMN IF NOT EXISTS `role_id` int(11) DEFAULT 3,
ADD COLUMN IF NOT EXISTS `is_active` tinyint(1) DEFAULT 1,
ADD COLUMN IF NOT EXISTS `last_login` timestamp NULL,
ADD COLUMN IF NOT EXISTS `login_attempts` int(11) DEFAULT 0,
ADD COLUMN IF NOT EXISTS `locked_until` timestamp NULL,
ADD FOREIGN KEY IF NOT EXISTS (`role_id`) REFERENCES `roles`(`id`) ON DELETE SET NULL;

-- Create user_activity_log table if it doesn't exist
CREATE TABLE IF NOT EXISTS `user_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text,
  `ip_address` varchar(45),
  `user_agent` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create system_settings table if it doesn't exist
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` text,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default system settings
INSERT IGNORE INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'Inventory Management System', 'string', 'The name of the application'),
('timezone', 'UTC', 'string', 'Default timezone for the application'),
('items_per_page', '25', 'number', 'Number of items to display per page'),
('auto_approve_threshold', '1000', 'number', 'Purchase orders below this amount are auto-approved'),
('low_stock_threshold', '10', 'number', 'Alert when product quantity falls below this number'),
('currency', 'USD', 'string', 'Default currency for the application'),
('tax_rate', '8.25', 'number', 'Default tax rate percentage'),
('session_timeout', '60', 'number', 'Session timeout in minutes'),
('max_login_attempts', '5', 'number', 'Maximum failed login attempts before lockout'),
('backup_frequency', 'daily', 'string', 'How often to backup the database'),
('backup_retention', '30', 'number', 'How many days to keep backup files'),
('require_strong_passwords', 'true', 'boolean', 'Enforce password complexity requirements'),
('email_notifications', 'true', 'boolean', 'Enable email notifications'),
('low_stock_alerts', 'true', 'boolean', 'Enable low stock alerts'),
('system_maintenance_alerts', 'true', 'boolean', 'Enable system maintenance alerts');

-- Create notifications table if it doesn't exist
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `read_at` timestamp NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create admin_access_log table for tracking admin panel access
CREATE TABLE IF NOT EXISTS `admin_access_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `action` varchar(100) NOT NULL,
  `resource` varchar(100),
  `resource_id` int(11) DEFAULT NULL,
  `ip_address` varchar(45),
  `user_agent` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `action` (`action`),
  KEY `created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Update existing admin user if exists, otherwise create one
INSERT INTO `users` (`name`, `email`, `password_hash`, `role_id`, `is_active`) 
VALUES ('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 1)
ON DUPLICATE KEY UPDATE `role_id` = 1, `is_active` = 1;

-- Add sample activity log entries
INSERT IGNORE INTO `user_activity_log` (`user_id`, `action`, `details`, `ip_address`, `user_agent`) VALUES
(1, 'login', 'Admin user logged in', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'user_created', 'Created new user: manager@example.com', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'role_updated', 'Updated permissions for Manager role', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'settings_updated', 'Updated system settings', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

-- Add sample notifications
INSERT IGNORE INTO `notifications` (`user_id`, `title`, `message`, `type`) VALUES
(1, 'Welcome to Admin Panel', 'Your admin panel has been successfully set up!', 'success'),
(1, 'System Status', 'All systems are running normally.', 'info'),
(NULL, 'Low Stock Alert', 'Product "Widget A" is running low on stock.', 'warning');

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS `idx_users_role_active` ON `users`(`role_id`, `is_active`);
CREATE INDEX IF NOT EXISTS `idx_activity_log_user_action` ON `user_activity_log`(`user_id`, `action`);
CREATE INDEX IF NOT EXISTS `idx_notifications_user_read` ON `notifications`(`user_id`, `is_read`);
CREATE INDEX IF NOT EXISTS `idx_admin_access_user_action` ON `admin_access_log`(`user_id`, `action`);

-- Show success message
SELECT 'Admin Panel Database Setup Complete!' as Message;
SELECT 'Default admin credentials:' as Note;
SELECT 'Email: admin@example.com' as Username;
SELECT 'Password: password' as Password;
SELECT 'Please change the default password after first login!' as Warning;
