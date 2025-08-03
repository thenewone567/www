-- Create user_permissions table for granular user access control
CREATE TABLE IF NOT EXISTS user_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    permissions JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_permission (user_id)
);

-- Add some sample permissions for existing users
INSERT INTO user_permissions (user_id, permissions) 
SELECT user_id, JSON_OBJECT(
    'dashboard', true,
    'sales', true,
    'purchases', true,
    'inventory', true,
    'customers', true,
    'suppliers', true,
    'products', true,
    'reports', true,
    'settings', false,
    'users', false,
    'cycle_counts', true,
    'returns', true,
    'expenses', true,
    'notifications', true
) 
FROM users 
WHERE role_id = (SELECT role_id FROM roles WHERE role_name = 'employee')
ON DUPLICATE KEY UPDATE permissions = VALUES(permissions);

-- Give admin users full permissions
INSERT INTO user_permissions (user_id, permissions) 
SELECT user_id, JSON_OBJECT(
    'dashboard', true,
    'sales', true,
    'purchases', true,
    'inventory', true,
    'customers', true,
    'suppliers', true,
    'products', true,
    'reports', true,
    'settings', true,
    'users', true,
    'cycle_counts', true,
    'returns', true,
    'expenses', true,
    'notifications', true
) 
FROM users 
WHERE role_id IN (
    SELECT role_id FROM roles WHERE role_name IN ('admin', 'super_admin')
)
ON DUPLICATE KEY UPDATE permissions = VALUES(permissions);
