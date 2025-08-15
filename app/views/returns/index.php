<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<div class="row">
    <div class="col-md-6">
        <h1>Sale Returns</h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/returns/addsale" class="btn btn-primary float-right">
            <i class="fa fa-plus"></i> New Sale Return
        </a>
    </div>
</div>
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Sale ID</th>
            <th>Date</th>
            <th>Reason</th>
            <th>Refund Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data['sale_returns'])): ?>
            <?php foreach ($data['sale_returns'] as $return): ?>
                <tr>
                    <td><?php echo $return->sale_return_id; ?></td>
                    <td><?php echo $return->sale_id; ?></td>
                    <td><?php echo $return->return_date; ?></td>
                    <td><?php echo $return->reason; ?></td>
                    <td><?php echo $return->refund_amount; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center text-muted">No sale returns found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="row mt-5">
    <div class="col-md-6">
        <h1>Purchase Returns</h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/returns/addpurchase" class="btn btn-primary float-right">
            <i class="fa fa-plus"></i> New Purchase Return
        </a>
    </div>
</div>
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Purchase ID</th>
            <th>Date</th>
            <th>Reason</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data['purchase_returns'])): ?>
            <?php foreach ($data['purchase_returns'] as $return): ?>
                <tr>
                    <td><?php echo $return->purchase_return_id; ?></td>
                    <td><?php echo $return->purchase_id; ?></td>
                    <td><?php echo $return->return_date; ?></td>
                    <td><?php echo $return->reason; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center text-muted">No purchase returns found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Cancelled Purchase Orders Section -->
<div class="row mt-5">
    <div class="col-md-6">
        <h1>Cancelled Purchase Orders</h1>
        <p class="text-muted">Orders cancelled before receiving - moved from main purchases to returns management</p>
    </div>
    <div class="col-md-6">
        <!-- Optional: Add action button for bulk operations -->
    </div>
</div>
<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>PO Number</th>
            <th>Supplier</th>
            <th>Order Date</th>
            <th>Amount</th>
            <th>Cancelled Date</th>
            <th>Reason</th>
            <th>Action Taken</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data['cancelled_purchases'])): ?>
            <?php foreach ($data['cancelled_purchases'] as $cancelled): ?>
                <tr>
                    <td>
                        <span class="font-weight-bold text-primary">
                            <?php echo htmlspecialchars($cancelled->po_number ?? 'PO-' . $cancelled->purchase_id); ?>
                        </span>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($cancelled->supplier_name ?? 'N/A'); ?>
                    </td>
                    <td>
                        <?php echo date('M d, Y', strtotime($cancelled->purchase_date)); ?>
                    </td>
                    <td>
                        <span class="font-weight-bold">
                            $<?php echo number_format($cancelled->total_amount ?? 0, 2); ?>
                        </span>
                    </td>
                    <td>
                        <?php if (!empty($cancelled->cancelled_date)): ?>
                            <?php echo date('M d, Y', strtotime($cancelled->cancelled_date)); ?>
                        <?php else: ?>
                            <span class="text-muted">Not recorded</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($cancelled->cancellation_reason)): ?>
                            <span class="badge badge-info"><?php echo htmlspecialchars($cancelled->cancellation_reason); ?></span>
                        <?php else: ?>
                            <span class="text-muted">No reason given</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($cancelled->cancelled_action)): ?>
                            <span class="badge badge-warning"><?php echo htmlspecialchars($cancelled->cancelled_action); ?></span>
                        <?php else: ?>
                            <span class="text-muted">No action specified</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="badge badge-danger">
                            <i class="fas fa-ban"></i> Cancelled
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center text-muted">No cancelled purchase orders found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

</div> <!-- End container-fluid -->
</div> <!-- End page-content-wrapper -->
</div> <!-- End wrapper -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
    integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
    crossorigin="anonymous"></script>
<script src="<?php echo URLROOT; ?>/public/js/main.js"></script>
</body>

</html>