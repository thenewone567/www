<?php
// Main entry point for the application
require_once 'config/config.php';

// Check if user is logged in, otherwise redirect to login page
if (!isset($_SESSION["user_id"]) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("location: templates/login.php");
    exit;
}

require_once 'src/includes/header.php';

// Simple router
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

switch ($page) {
    case 'dashboard':
        include 'templates/dashboard.php';
        break;
    case 'products':
        if (isset($_GET['action']) && ($_GET['action'] == 'new' || $_GET['action'] == 'edit')) {
            include 'templates/product_form.php';
        } else {
            include 'templates/products.php';
        }
        break;
    case 'sales':
        if (isset($_GET['action']) && $_GET['action'] == 'new') {
            include 'templates/pos.php';
        } else {
            include 'templates/sales.php';
        }
        break;
    case 'suppliers':
        if (isset($_GET['action']) && ($_GET['action'] == 'new' || $_GET['action'] == 'edit')) {
            include 'templates/supplier_form.php';
        } else {
            include 'templates/suppliers.php';
        }
        break;
    case 'purchases':
        if (isset($_GET['action']) && $_GET['action'] == 'new') {
            include 'templates/purchase_form.php';
        } else {
            include 'templates/purchases.php';
        }
        break;
    case 'stock_movements':
        if (isset($_GET['action']) && $_GET['action'] == 'new') {
            include 'templates/stock_movement_form.php';
        } else {
            include 'templates/stock_movements.php';
        }
        break;
    case 'returns':
        if (isset($_GET['action']) && $_GET['action'] == 'new') {
            include 'templates/return_form.php';
        } else {
            include 'templates/returns.php';
        }
        break;
    case 'customers':
        if (isset($_GET['action']) && ($_GET['action'] == 'new' || $_GET['action'] == 'edit')) {
            include 'templates/customer_form.php';
        } else {
            include 'templates/customers.php';
        }
        break;
    case 'reports':
        include 'templates/reports.php';
        break;
    // Add other cases as we build more pages
    default:
        include 'templates/dashboard.php';
        break;
}

require_once 'src/includes/footer.php';
?>
