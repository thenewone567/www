<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fa-solid fa-calendar-day"></i> Today's Sales</h4>
                    <div>
                        <a href="<?php echo URLROOT; ?>/sales/list" class="btn btn-outline-secondary me-2">
                            <i class="fa-solid fa-list"></i> All Sales
                        </a>
                        <a href="<?php echo URLROOT; ?>/sales" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Back to Sales Hub
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php flash('sale_message'); ?>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <h6 class="text-muted">Sales for <?php echo date('F j, Y'); ?></h6>
                    </div>
                </div>

                <?php if (empty($data['sales'])): ?>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle"></i> No sales recorded for today.
                    </div>
                <?php else: ?>
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Sales</h6>
                                            <h4><?php echo count($data['sales']); ?></h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-shopping-cart fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Total Revenue</h6>
                                            <h4>$<?php
                                            $totalRevenue = 0;
                                            foreach ($data['sales'] as $sale) {
                                                $totalRevenue += $sale->total_amount;
                                            }
                                            echo number_format($totalRevenue, 2);
                                            ?></h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-dollar-sign fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h6 class="card-title">Average Sale</h6>
                                            <h4><?php echo count($data['sales']) > 0 ? formatCurrency($totalRevenue / count($data['sales']), 2) : formatCurrency(0, 2); ?>
                                            </h4>
                                        </div>
                                        <div class="align-self-center">
                                            <i class="fa-solid fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Customer</th>
                                    <th>Time</th>
                                    <th>Total Amount</th>
                                    <th>Payment Mode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['sales'] as $sale): ?>
                                    <tr>
                                        <td>#<?php echo $sale->sale_id; ?></td>
                                        <td>
                                            <?php if (isset($sale->customer_name)): ?>
                                                <?php echo htmlspecialchars($sale->customer_name); ?>
                                            <?php else: ?>
                                                Customer ID: <?php echo $sale->customer_id; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('g:i A', strtotime($sale->sale_date)); ?></td>
                                        <td><?php echo formatCurrency($sale->total_amount, 2); ?></td>
                                        <td>
                                            <span
                                                class="badge bg-<?php echo $sale->payment_mode == 'cash' ? 'success' : ($sale->payment_mode == 'card' ? 'primary' : 'secondary'); ?>">
                                                <?php echo ucfirst($sale->payment_mode); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="<?php echo URLROOT; ?>/sales/details/<?php echo $sale->sale_id; ?>"
                                                    class="btn btn-outline-primary" title="View Details">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                <a href="<?php echo URLROOT; ?>/invoices/generate/<?php echo $sale->sale_id; ?>"
                                                    class="btn btn-outline-success" title="Generate Invoice">
                                                    <i class="fa-solid fa-file-invoice"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>


</div> <!-- End container-fluid -->
</div> <!-- End page-content-wrapper -->
</div> <!-- End wrapper -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>

</html>