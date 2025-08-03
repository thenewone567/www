
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Data Check</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body style="background: #f4f6fb;">
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="mb-2"><i class="fas fa-database"></i> Dashboard Data Check</h2>
            <p class="lead">Database Config: <span class="badge badge-info"><?= DB_HOST ?>/<?= DB_NAME ?> (User: <?= DB_USER ?>)</span></p>
        </div>
    </div>
    <?php
    require_once 'app/config.php';
    require_once 'app/Database.php';
    try {
        $db = new Database();
        $db->query("USE " . DB_NAME);
        $db->execute();
        $db->query("SELECT DATABASE() as current_db");
        $db->execute();
        $currentDbResult = $db->single();
        $currentDb = $currentDbResult ? $currentDbResult->current_db : 'NULL';
        $db->query("SHOW TABLES");
        $db->execute();
        $tables = $db->resultSet();

        $db->query("SELECT COUNT(*) AS sales FROM sales");
        $db->execute();
        $salesResult = $db->single();
        $sales = $salesResult ? $salesResult->sales : 0;

        $db->query("SELECT COUNT(*) AS products FROM products");
        $db->execute();
        $productsResult = $db->single();
        $products = $productsResult ? $productsResult->products : 0;

        $db->query("SELECT COUNT(*) AS customers FROM customers");
        $db->execute();
        $customersResult = $db->single();
        $customers = $customersResult ? $customersResult->customers : 0;

        $db->query("SELECT COUNT(*) AS stock FROM stock");
        $db->execute();
        $stockResult = $db->single();
        $stock = $stockResult ? $stockResult->stock : 0;

        $db->query("SELECT COUNT(*) AS purchases FROM purchases");
        $db->execute();
        $purchasesResult = $db->single();
        $purchases = $purchasesResult ? $purchasesResult->purchases : 0;

    ?>
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-server"></i> Database Status</h5>
                    <span class="badge badge-success">Connected</span>
                    <p class="mt-2">Current database: <strong><?= $currentDb ?></strong></p>
                    <p>Tables found: <span class="badge badge-primary"><?= count($tables) ?></span></p>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($tables as $table): ?>
                            <li class="list-group-item"><i class="fas fa-table"></i> <?= current((array)$table) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-list-ol"></i> Table Counts</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Sales <span class="badge badge-info float-right"><?= $sales ?></span></li>
                        <li class="list-group-item">Products <span class="badge badge-info float-right"><?= $products ?></span></li>
                        <li class="list-group-item">Customers <span class="badge badge-info float-right"><?= $customers ?></span></li>
                        <li class="list-group-item">Stock <span class="badge badge-info float-right"><?= $stock ?></span></li>
                        <li class="list-group-item">Purchases <span class="badge badge-info float-right"><?= $purchases ?></span></li>
                    </ul>
                    <div class="mt-3">
                        <?php if ($sales == 0 || $products == 0 || $customers == 0 || $stock == 0 || $purchases == 0): ?>
                            <span class="badge badge-warning"><i class="fas fa-exclamation-triangle"></i> Some tables are empty. Demo data should be added.</span>
                        <?php else: ?>
                            <span class="badge badge-success"><i class="fas fa-check-circle"></i> All tables have data. Dashboard should show real KPIs!</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    } catch (Exception $e) {
    ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-times-circle"></i> Database error: <?= $e->getMessage() ?>
        </div>
    <?php
    }
    ?>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>