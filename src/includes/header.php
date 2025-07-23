<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-dark border-right" id="sidebar-wrapper">
            <div class="sidebar-heading text-white">Dashboard</div>
            <div class="list-group list-group-flush">
                <a href="index.php?page=dashboard" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-gauge"></i> Dashboard</a>
                <div class="list-group-item list-group-item-action bg-dark text-white">
                    <a href="#salesSubmenu" data-bs-toggle="collapse" class="text-white dropdown-toggle"><i class="fas fa-chart-line"></i> Sales</a>
                    <ul class="collapse list-unstyled" id="salesSubmenu">
                        <li><a href="index.php?page=sales&action=new" class="list-group-item list-group-item-action bg-dark text-white">New Sale</a></li>
                        <li><a href="index.php?page=sales" class="list-group-item list-group-item-action bg-dark text-white">Sales List</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Invoices</a></li>
                    </ul>
                </div>
                <div class="list-group-item list-group-item-action bg-dark text-white">
                    <a href="#purchasesSubmenu" data-bs-toggle="collapse" class="text-white dropdown-toggle"><i class="fas fa-shopping-cart"></i> Purchases</a>
                    <ul class="collapse list-unstyled" id="purchasesSubmenu">
                        <li><a href="index.php?page=purchases&action=new" class="list-group-item list-group-item-action bg-dark text-white">New Purchase</a></li>
                        <li><a href="index.php?page=purchases" class="list-group-item list-group-item-action bg-dark text-white">Purchase List</a></li>
                        <li><a href="index.php?page=suppliers" class="list-group-item list-group-item-action bg-dark text-white">Vendor List</a></li>
                    </ul>
                </div>
                <div class="list-group-item list-group-item-action bg-dark text-white">
                    <a href="#returnsSubmenu" data-bs-toggle="collapse" class="text-white dropdown-toggle"><i class="fas fa-undo"></i> Returns</a>
                    <ul class="collapse list-unstyled" id="returnsSubmenu">
                        <li><a href="index.php?page=returns&action=new" class="list-group-item list-group-item-action bg-dark text-white">Sale Returns</a></li>
                        <li><a href="index.php?page=returns" class="list-group-item list-group-item-action bg-dark text-white">Purchase Returns</a></li>
                    </ul>
                </div>
                <div class="list-group-item list-group-item-action bg-dark text-white">
                    <a href="#inventorySubmenu" data-bs-toggle="collapse" class="text-white dropdown-toggle"><i class="fas fa-box"></i> Inventory</a>
                    <ul class="collapse list-unstyled" id="inventorySubmenu">
                        <li><a href="index.php?page=products" class="list-group-item list-group-item-action bg-dark text-white">Products</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Categories</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Brands</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Units</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Low Stock Alerts</a></li>
                    </ul>
                </div>
                <div class="list-group-item list-group-item-action bg-dark text-white">
                    <a href="#warehouseSubmenu" data-bs-toggle="collapse" class="text-white dropdown-toggle"><i class="fas fa-warehouse"></i> Warehouse & Stock Movement</a>
                    <ul class="collapse list-unstyled" id="warehouseSubmenu">
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Receiving</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Putaway</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Stock Transfer</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Cycle Count</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Warehouse Locations</a></li>
                    </ul>
                </div>
                <div class="list-group-item list-group-item-action bg-dark text-white">
                    <a href="#reportsSubmenu" data-bs-toggle="collapse" class="text-white dropdown-toggle"><i class="fas fa-file-alt"></i> Reports</a>
                    <ul class="collapse list-unstyled" id="reportsSubmenu">
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Daily Report</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">GST Report</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Profit/Loss</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Stock Summary</a></li>
                    </ul>
                </div>
                <div class="list-group-item list-group-item-action bg-dark text-white">
                    <a href="#expensesSubmenu" data-bs-toggle="collapse" class="text-white dropdown-toggle"><i class="fas fa-money-bill-wave"></i> Expenses</a>
                    <ul class="collapse list-unstyled" id="expensesSubmenu">
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Add Expense</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Expense Categories</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Expense Reports</a></li>
                    </ul>
                </div>
                <div class="list-group-item list-group-item-action bg-dark text-white">
                    <a href="#settingsSubmenu" data-bs-toggle="collapse" class="text-white dropdown-toggle"><i class="fas fa-cog"></i> Settings</a>
                    <ul class="collapse list-unstyled" id="settingsSubmenu">
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">User Management</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Roles & Permissions</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Shop/Warehouse Info</a></li>
                        <li><a href="#" class="list-group-item list-group-item-action bg-dark text-white">Tax Settings</a></li>
                    </ul>
                </div>
                <a href="src/actions/logout.php" class="list-group-item list-group-item-action bg-dark text-white"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
            </div>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>
            </nav>
            <main>
