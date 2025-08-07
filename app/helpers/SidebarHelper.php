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
            ['url' => 'purchases', 'icon' => 'fa-solid fa-cart-plus', 'label' => 'Purchases'],
            ['url' => 'suppliers', 'icon' => 'fa-solid fa-truck', 'label' => 'Suppliers'],
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
            ['url' => 'receiving', 'icon' => 'fa-solid fa-dolly', 'label' => 'Receiving'],
            ['url' => 'inventory', 'icon' => 'fa-solid fa-warehouse', 'label' => 'Inventory'],
            ['url' => 'inventory/bulk_transfer', 'icon' => 'fa-solid fa-truck-moving', 'label' => 'Bulk Transfer'],
            ['url' => 'expenses', 'icon' => 'fa-solid fa-wallet', 'label' => 'Expenses'],
            ['url' => 'cycle-counts', 'icon' => 'fas fa-clipboard-list', 'label' => 'Cycle Counts'],
            ['url' => 'reports', 'icon' => 'fa-solid fa-file-alt', 'label' => 'Reports'],
            ['url' => 'company-profile', 'icon' => 'fa-solid fa-building', 'label' => 'Company Profile'],
        ],
        'super admin' => [], // Will fallback to 'admin'

        // Manager (role_id 2)
        'manager' => [
            ['url' => 'dashboard', 'icon' => 'fa-solid fa-gauge', 'label' => 'Dashboard'],
            ['url' => 'sales', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Sales'],
            ['url' => 'purchases', 'icon' => 'fa-solid fa-cart-plus', 'label' => 'Purchases'],
            ['url' => 'suppliers', 'icon' => 'fa-solid fa-truck', 'label' => 'Suppliers'],
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
            ['url' => 'receiving', 'icon' => 'fa-solid fa-dolly', 'label' => 'Receiving'],
            ['url' => 'inventory', 'icon' => 'fa-solid fa-warehouse', 'label' => 'Inventory'],
            ['url' => 'inventory/bulk_transfer', 'icon' => 'fa-solid fa-truck-moving', 'label' => 'Bulk Transfer'],
            ['url' => 'cycle-counts', 'icon' => 'fas fa-clipboard-list', 'label' => 'Cycle Counts'],
            ['url' => 'reports', 'icon' => 'fa-solid fa-file-alt', 'label' => 'Reports'],
            ['url' => 'company-profile', 'icon' => 'fa-solid fa-building', 'label' => 'Company Profile'],
        ],

        // Supervisor (role_id 3) - oversight capabilities
        'supervisor' => [
            ['url' => 'dashboard', 'icon' => 'fa-solid fa-gauge', 'label' => 'Dashboard'],
            ['url' => 'sales', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Sales'],
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
            ['url' => 'receiving', 'icon' => 'fa-solid fa-dolly', 'label' => 'Receiving'],
            ['url' => 'inventory', 'icon' => 'fa-solid fa-warehouse', 'label' => 'Inventory'],
            ['url' => 'cycle-counts', 'icon' => 'fas fa-clipboard-list', 'label' => 'Cycle Counts'],
            ['url' => 'reports', 'icon' => 'fa-solid fa-file-alt', 'label' => 'Reports'],
        ],

        // Cashier (role_id 4) - customer facing
        'cashier' => [
            ['url' => 'sales', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Sales'],
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
        ],

        // Inventory Clerk (role_id 5) - inventory focused
        'Inventory clerk' => [
            ['url' => 'inventory', 'icon' => 'fa-solid fa-warehouse', 'label' => 'Inventory'],
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
            ['url' => 'receiving', 'icon' => 'fa-solid fa-dolly', 'label' => 'Receiving'],
        ],

        // Default/basic user
        'associate' => [
            ['url' => 'products', 'icon' => 'fa-solid fa-box', 'label' => 'Products'],
            ['url' => 'sales', 'icon' => 'fa-solid fa-chart-line', 'label' => 'Sales'],
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
    // Supervisor (role_id 3 or role_name)
    if ($roleId == 3 || $role === 'supervisor') {
        return $roleMap['supervisor'];
    }
    // Cashier (role_id 4 or role_name)
    if ($roleId == 4 || $role === 'cashier') {
        return $roleMap['cashier'];
    }
    // Inventory Clerk (role_id 5 or role_name)
    if ($roleId == 5 || $role === 'Inventory clerk') {
        return $roleMap['Inventory clerk'];
    }
    // Fallback/default
    return $roleMap['associate'];
}
