<?php require APPROOT . DS . 'app' . DS . '' . ''views/layout/header.php'; ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="mt-4"><?php echo $data['title']; ?></h1>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Sales (Today)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $data['sales_today']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Sales (This Week)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $data['sales_week']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sales (This Month)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$<?php echo $data['sales_month']; ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Requests</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">18</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Selling Products</h6>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach($data['top_selling'] as $product) : ?>
                        <li class="list-group-item"><?php echo $product->product_name; ?> - <?php echo $product->total_quantity; ?> sold</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Low Stock Alerts</h6>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach($data['low_stock'] as $product) : ?>
                        <li class="list-group-item"><?php echo $product->product_name; ?> - <?php echo $product->min_stock_level; ?> remaining</li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . '' . ''views/layout/footer.php'; ?>
