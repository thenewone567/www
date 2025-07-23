<?php
// Main entry point for the application
require_once 'src/includes/check_auth.php';
require_once 'src/includes/header.php';

// Simple router
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$action = isset($_GET['action']) ? $_GET['action'] : null;

switch ($page) {
    case 'dashboard':
        include 'templates/dashboard.php';
        break;
    case 'inventory':
        include 'templates/inventory.php';
        break;
    case 'products':
        if ($action == 'new' || $action == 'edit') {
            include 'templates/product_form.php';
        } else {
            include 'templates/inventory.php'; // Redirect to inventory
        }
        break;
    case 'sales':
        if ($action == 'new') {
            include 'templates/pos.php';
        } else {
            include 'templates/sales.php';
        }
        break;
    case 'suppliers':
        if ($action == 'new' || $action == 'edit') {
            include 'templates/supplier_form.php';
        } else {
            include 'templates/suppliers.php';
        }
        break;
    case 'purchases':
        if ($action == 'new') {
            include 'templates/purchase_form.php';
        } else {
            include 'templates/purchases.php';
        }
        break;
    case 'returns':
        if ($action == 'new') {
            include 'templates/return_form.php';
        } else {
            include 'templates/returns.php';
        }
        break;
    case 'customers':
        if ($action == 'new' || $action == 'edit') {
            include 'templates/customer_form.php';
        } else {
            include 'templates/customers.php';
        }
        break;
    case 'reports':
        include 'templates/reports.php';
        break;
    case 'warehouse':
        include 'templates/warehouse.php';
        break;
    case 'expenses':
        include 'templates/expenses.php';
        break;
    case 'settings':
        include 'templates/settings.php';
        break;
    // Add other cases as we build more pages
    default:
        include 'templates/dashboard.php';
        break;
}

require_once 'src/includes/footer.php';
?>
