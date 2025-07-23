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
                <a href="index.php?page=sales" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-chart-line"></i> Sales</a>
                <a href="index.php?page=purchases" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-shopping-cart"></i> Purchases</a>
                <a href="index.php?page=returns" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-undo"></i> Returns</a>
                <a href="index.php?page=inventory" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-box"></i> Inventory</a>
                <a href="index.php?page=warehouse" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-warehouse"></i> Warehouse & Stock Movement</a>
                <a href="index.php?page=reports" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-file-alt"></i> Reports</a>
                <a href="index.php?page=expenses" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-money-bill-wave"></i> Expenses</a>
                <a href="index.php?page=settings" class="list-group-item list-group-item-action bg-dark text-white"><i class="fas fa-cog"></i> Settings</a>
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
