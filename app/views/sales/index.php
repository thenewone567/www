<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
    <div class="row">
        <div class="col-md-6">
            <h1>Sales</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/sales/add" class="btn btn-primary pull-right">
                <i class="fa fa-pencil"></i> New Sale
            </a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Payment Mode</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($data['sales'] as $sale) : ?>
        <tr>
            <td><?php echo $sale->sale_id; ?></td>
            <td><?php echo $sale->customer_id; ?></td>
            <td><?php echo $sale->sale_date; ?></td>
            <td><?php echo $sale->total_amount; ?></td>
            <td><?php echo $sale->payment_mode; ?></td>
            <td>
                <a href="<?php echo URLROOT; ?>/sales/show/<?php echo $sale->sale_id; ?>" class="btn btn-dark">View</a>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>
