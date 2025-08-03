<?php
/**
 * SidebarHelper: Returns sidebar navigation items based on user role/permissions
 */
function getSidebarItems($userRole = null, $roleId = null)
{
    // Accept both role name and role id
    if (!$userRole && isset($_SESSION['user_role'])) {
        $userRole = $_SESSION['user_role'];
    }
    if (!$roleId && isset($_SESSION['role_id'])) {
        $roleId = $_SESSION['role_id'];
    }
    $role = '';
    if (is_string($userRole)) {
        $role = strtolower(trim($userRole));
    }
    $roleMap = [
        // Admins (role_id 1 or role_name 'admin' or 'super admin')
        'admin' => [
            ['url' => 'dashboard', 'icon' => 'fa-solid fa-gauge', 'label' => 'Dashboard'],
            ['url' => 'sales', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Sales'],
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
            ['url' => 'purchases', 'icon' => 'fa-solid fa-cart-plus', 'label' => 'Purchases'],
            ['url' => 'inventory', 'icon' => 'fa-solid fa-warehouse', 'label' => 'Inventory'],
            ['url' => 'reports', 'icon' => 'fa-solid fa-file-alt', 'label' => 'Reports'],
            ['url' => 'settings', 'icon' => 'fa-solid fa-cogs', 'label' => 'Settings'],
            // Removed Users button from sidebar for admin
            ['url' => 'suppliers', 'icon' => 'fa-solid fa-truck', 'label' => 'Suppliers'],
            ['url' => 'expenses', 'icon' => 'fa-solid fa-wallet', 'label' => 'Expenses'],
            ['url' => 'cycle-counts', 'icon' => 'fas fa-clipboard-list', 'label' => 'Cycle Counts'],
        ],
        'super admin' => [], // Will fallback to 'admin'
        // Manager
        'manager' => [
            ['url' => 'dashboard', 'icon' => 'fa-solid fa-gauge', 'label' => 'Dashboard'],
            ['url' => 'sales', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Sales'],
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
            ['url' => 'purchases', 'icon' => 'fa-solid fa-cart-plus', 'label' => 'Purchases'],
            ['url' => 'inventory', 'icon' => 'fa-solid fa-warehouse', 'label' => 'Inventory'],
            ['url' => 'reports', 'icon' => 'fa-solid fa-file-alt', 'label' => 'Reports'],
            ['url' => 'suppliers', 'icon' => 'fa-solid fa-truck', 'label' => 'Suppliers'],
            ['url' => 'cycle-counts', 'icon' => 'fas fa-clipboard-list', 'label' => 'Cycle Counts'],
        ],
        // Cashier
        'cashier' => [
            ['url' => 'dashboard', 'icon' => 'fa-solid fa-gauge', 'label' => 'Dashboard'],
            ['url' => 'sales', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Sales'],
            ['url' => 'customers', 'icon' => 'fa-solid fa-users', 'label' => 'Customers'],
        ],
        // Stock Clerk
        'stock clerk' => [
            ['url' => 'dashboard', 'icon' => 'fa-solid fa-gauge', 'label' => 'Dashboard'],
            ['url' => 'inventory', 'icon' => 'fa-solid fa-warehouse', 'label' => 'Inventory'],
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
        ],
        // Default/basic user
        'associate' => [
            ['url' => 'dashboard', 'icon' => 'fa-solid fa-gauge', 'label' => 'Dashboard'],
            ['url' => 'sales', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Sales'],
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
        ],
    ];

    // Admin detection by role_id or role_name
    if ($roleId == 1 || $role === 'admin' || $role === 'super admin') {
        return $roleMap['admin'];
    }
    // Manager (role_id 2 or role_name)
    if ($roleId == 2 || $role === 'manager') {
        return $roleMap['manager'];
    }
    // Cashier (role_id 3 or role_name)
    if ($roleId == 3 || $role === 'cashier') {
        return $roleMap['cashier'];
    }
    // Stock Clerk (role_id 4 or role_name)
    if ($roleId == 4 || $role === 'stock clerk') {
        return $roleMap['stock clerk'];
    }
    // Fallback/default
    return $roleMap['associate'];
}
