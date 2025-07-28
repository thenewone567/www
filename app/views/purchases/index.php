<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="row">
    <div class="col-md-6">
        <h1>Purchases</h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-primary float-right">
            <i class="fa fa-plus"></i> New Purchase
        </a>
    </div>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Supplier</th>
            <th>Date</th>
            <th>Total Amount</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data['purchases'])): ?>
            <?php foreach ($data['purchases'] as $purchase): ?>
                <tr>
                    <td><?php echo $purchase->purchase_id; ?></td>
                    <td><?php echo $purchase->supplier_id; ?></td>
                    <td><?php echo $purchase->purchase_date ?? '-'; ?></td>
                    <td><?php echo $purchase->total_amount; ?></td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/purchases/show/<?php echo $purchase->purchase_id; ?>"
                            class="btn btn-dark">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center text-muted">No purchases found</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>