<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fa-solid fa-list"></i> Sales History</h4>
                    <a href="<?php echo URLROOT; ?>/sales" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Back to Sales Hub
                    </a>
                </div>
            </div>
            <div class="card-body">
                <?php flash('sale_message'); ?>

                <?php if (empty($data['sales'])): ?>
                    <div class="alert alert-info">
                        <i class="fa-solid fa-info-circle"></i> No sales records found.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Sale ID</th>
                                    <th>Customer</th>
                                    <th>Date</th>
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
                                        <td><?php echo date('M j, Y g:i A', strtotime($sale->sale_date)); ?></td>
                                        <td>$<?php echo number_format($sale->total_amount, 2); ?></td>
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

                    <div class="mt-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="text-muted">
                                    <strong>Total Sales:</strong> <?php echo count($data['sales']); ?> transactions
                                </p>
                            </div>
                            <div class="col-md-6 text-end">
                                <p class="text-muted">
                                    <strong>Total Revenue:</strong> $<?php
                                    $totalRevenue = 0;
                                    foreach ($data['sales'] as $sale) {
                                        $totalRevenue += $sale->total_amount;
                                    }
                                    echo number_format($totalRevenue, 2);
                                    ?>
                                </p>
                            </div>
                        </div>
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