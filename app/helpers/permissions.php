<?php
/**
 * Permission Helper Functions
 * Provides utilities for checking user permissions across the application
 */

/**
 * Check if current user has a specific role (case-insensitive)
 * @param string|array $roles Single role or array of roles to check
 * @return bool
 */
function hasRole($roles)
{
    if (!isset($_SESSION['user_role'])) {
        return false;
    }

    $userRole = strtolower($_SESSION['user_role']);

    if (is_string($roles)) {
        return $userRole === strtolower($roles);
    }

    if (is_array($roles)) {
        $lowercaseRoles = array_map('strtolower', $roles);
        return in_array($userRole, $lowercaseRoles);
    }

    return false;
}

/**
 * Check if current user is admin (case-insensitive)
 * @return bool
 */
function isAdmin()
{
    return hasRole(['admin', 'super_admin', 'administrator']);
}

/**
 * Check if current user has permission to access a specific page/module
 * @param string $page The page/module name to check
 * @return bool
 */
function hasPermission($page)
{
    if (!isLoggedIn()) {
        return false;
    }

    $userModel = new User();
    $userId = $_SESSION['user_id'];

    // Super admin and admin have access to everything
    if (isAdmin()) {
        return true;
    }

    // For now, use simple role-based access
    // You can extend this later when permission tables are implemented
    $userRole = strtolower($_SESSION['user_role'] ?? '');

    // Define basic page access by role
    $rolePermissions = [
        'manager' => ['dashboard', 'products', 'inventory', 'sales', 'purchases', 'reports', 'customers', 'suppliers'],
        'cashier' => ['dashboard', 'sales', 'products', 'customers'],
        'inventory_manager' => ['dashboard', 'products', 'inventory', 'purchases', 'suppliers'],
        'user' => ['dashboard'],
        'bot' => ['dashboard', 'bot'], // Bot role for automation
    ];

    if (isset($rolePermissions[$userRole])) {
        return in_array(strtolower($page), $rolePermissions[$userRole]);
    }

    // Default: deny access
    return false;
}

/**
 * Check if current user has permission to access a specific page/module
 * @param string $page The page/module name to check
 * @return bool
 */
function hasPageAccess($page)
{
    if (!isLoggedIn()) {
        return false;
    }

    $userModel = new User();
    $userId = $_SESSION['user_id'];

    // Super admin and admin have access to everything
    if (isAdmin()) {
        return true;
    }

    // Use the same logic as hasPermission
    return hasPermission($page);
}

/**
 * Redirect user if they don't have permission for the current page
 * @param string $page The page/module name to check
 * @param string $redirectTo Optional redirect destination
 */
function requirePageAccess($page, $redirectTo = 'dashboard')
{
    if (!hasPageAccess($page)) {
        flash('error_message', 'Access denied. You do not have permission to access this page.', 'alert alert-danger');
        redirect($redirectTo);
    }
}

/**
 * Get all pages the current user has access to
 * @return array
 */
function getUserAccessiblePages()
{
    if (!isLoggedIn()) {
        return [];
    }

    $userModel = new User();
    $userId = $_SESSION['user_id'];

    // Super admin and admin have access to everything
    if (isAdmin()) {
        return [
            'dashboard',
            'sales',
            'purchases',
            'inventory',
            'customers',
            'suppliers',
            'products',
            'reports',
            'settings',
            'users',
            'cycle_counts',
            'returns',
            'expenses',
            'notifications',
            'bot'
        ];
    }

    try {
        // Use role-based permissions instead of database lookup
        $userRole = strtolower($_SESSION['user_role'] ?? '');

        // Define basic page access by role
        $rolePermissions = [
            'manager' => ['dashboard', 'products', 'inventory', 'sales', 'purchases', 'reports', 'customers', 'suppliers'],
            'cashier' => ['dashboard', 'sales', 'products', 'customers'],
            'inventory_manager' => ['dashboard', 'products', 'inventory', 'purchases', 'suppliers'],
            'user' => ['dashboard'],
            'bot' => ['dashboard', 'bot'], // Bot role for automation
        ];

        $accessiblePages = $rolePermissions[$userRole] ?? ['dashboard'];

        // If no permissions found, give basic access to prevent empty sidebar
        if (empty($accessiblePages)) {
            return ['dashboard', 'sales', 'inventory', 'customers'];
        }

        return $accessiblePages;
    } catch (Exception $e) {
        // Fallback permissions if there's an error (e.g., table doesn't exist)
        error_log("Error getting user permissions: " . $e->getMessage());
        return ['dashboard', 'sales', 'inventory', 'customers', 'reports'];
    }
}

/**
 * Generate navigation menu based on user permissions
 * @return array
 */
function getPermissionBasedNavigation()
{
    $accessiblePages = getUserAccessiblePages();
    $navigation = [];

    $allPages = [
        'dashboard' => [
            'label' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'url' => 'dashboard'
        ],
        'sales' => [
            'label' => 'Sales',
            'icon' => 'fas fa-shopping-cart',
            'url' => 'sales'
        ],
        'purchases' => [
            'label' => 'Purchases',
            'icon' => 'fas fa-shopping-bag',
            'url' => 'purchases'
        ],
        'customers' => [
            'label' => 'Customers',
            'icon' => 'fas fa-users',
            'url' => 'customers'
        ],
        'suppliers' => [
            'label' => 'Suppliers',
            'icon' => 'fas fa-truck',
            'url' => 'suppliers'
        ],
        'reports' => [
            'label' => 'Reports',
            'icon' => 'fas fa-chart-bar',
            'url' => 'reports'
        ],
        'inventory' => [
            'label' => 'Inventory',
            'icon' => 'fa-solid fa-warehouse',
            'url' => 'inventory'
        ],
        'products' => [
            'label' => 'Products',
            'icon' => 'fa-solid fa-box',
            'url' => 'products'
        ],
        'cycle_counts' => [
            'label' => 'Cycle Counts',
            'icon' => 'fas fa-clipboard-list',
            'url' => 'cycle_counts'
        ],
        'returns' => [
            'label' => 'Returns',
            'icon' => 'fas fa-undo',
            'url' => 'returns'
        ],
        'expenses' => [
            'label' => 'Expenses',
            'icon' => 'fas fa-money-bill-wave',
            'url' => 'expenses'
        ],
        'settings' => [
            'label' => 'Settings',
            'icon' => 'fas fa-cog',
            'url' => 'settings'
        ],
        'users' => [
            'label' => 'Users',
            'icon' => 'fas fa-user-friends',
            'url' => 'users'
        ]
    ];

    // Define the desired order of navigation items (Dashboard first)
    $desiredOrder = [
        'dashboard',
        'sales',
        'purchases',
        'returns',
        'inventory',
        'customers',
        'suppliers',
        'products',
        'cycle_counts',
        'reports',
        'expenses',
        'settings',
        'users'
    ];

    // Build navigation in the desired order
    foreach ($desiredOrder as $page) {
        if (in_array($page, $accessiblePages) && isset($allPages[$page])) {
            $navigation[$page] = $allPages[$page];
        }
    }

    return $navigation;
}
?>