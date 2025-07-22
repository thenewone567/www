<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hardware Shop Management</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
    <header>
        <h1>Hardware Shop</h1>
        <nav>
            <ul>
                <li><a href="index.php?page=dashboard">Dashboard</a></li>
                <li><a href="index.php?page=inventory">Inventory</a></li>
                <li><a href="index.php?page=sales">Sales</a></li>
                <li><a href="index.php?page=purchases">Purchases</a></li>
                <li><a href="index.php?page=returns">Returns</a></li>
                <li><a href="index.php?page=customers">Customers</a></li>
                <li><a href="index.php?page=suppliers">Suppliers</a></li>
                <li><a href="index.php?page=reports">Reports</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="src/actions/logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="theme-switcher">
            <button id="theme-toggle">Toggle Dark Mode</button>
        </div>
    </header>
    <main>
