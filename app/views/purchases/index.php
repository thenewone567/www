<?php require APPROOT . DS . 'app' . DS . views/layout/header.php'; ?>
    <div class="row">
        <div class="col-md-6">
            <h1>Purchases</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/purchases/add" class="btn btn-primary pull-right">
                <i class="fa fa-pencil"></i> New Purchase
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
    <?php foreach($data['purchases'] as $purchase) : ?>
        <tr>
            <td><?php echo $purchase->purchase_id; ?></td>
            <td><?php echo $purchase->supplier_id; ?></td>
            <td><?php echo $purchase->purchase_date; ?></td>
            <td><?php echo $purchase->total_amount; ?></td>
            <td>
                <a href="<?php echo URLROOT; ?>/purchases/show/<?php echo $purchase->purchase_id; ?>" class="btn btn-dark">View</a>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php require APPROOT . DS . 'app' . DS . views/layout/footer.php'; ?>
