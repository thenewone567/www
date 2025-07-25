<?php require_once ROOT_PATH . 'views/header.php'; ?>

<div class="row">
    <div class="col-md-3">
        <?php require_once ROOT_PATH . 'views/sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <h1>Dashboard</h1>
        <hr>
        <!-- Sales Overview -->
        <div class="card mb-4">
            <div class="card-header">
                Sales Overview
            </div>
            <div class="card-body">
                <p>Placeholder for sales overview chart.</p>
            </div>
        </div>
        <!-- Top Selling Items -->
        <div class="card mb-4">
            <div class="card-header">
                Top Selling Items
            </div>
            <div class="card-body">
                <p>Placeholder for top selling items list.</p>
            </div>
        </div>
        <!-- Pending Restock -->
        <div class="card mb-4">
            <div class="card-header">
                Pending Restock
            </div>
            <div class="card-body">
                <p>Placeholder for pending restock list.</p>
            </div>
        </div>
        <!-- Low Stock Alerts -->
        <div class="card mb-4">
            <div class="card-header">
                Low Stock Alerts
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($lowStockProducts as $product) : ?>
                        <li class="list-group-item">
                            <?php echo $product['ProductName']; ?> - <strong>Quantity: <?php echo $product['Quantity']; ?></strong>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <!-- Shortcuts -->
        <div class="card">
            <div class="card-header">
                Shortcuts
            </div>
            <div class="card-body">
                <a href="#" class="btn btn-primary">Add New Product</a>
                <a href="#" class="btn btn-primary">Create New Sale</a>
            </div>
        </div>
    </div>
</div>

<?php require_once ROOT_PATH . 'views/footer.php'; ?>
