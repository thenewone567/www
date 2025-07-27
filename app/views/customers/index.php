<?php require APPROOT . '/views/layout/header.php'; ?>
    <div class="row">
        <div class="col-md-6">
            <h1>Customers</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/customers/add" class="btn btn-primary pull-right">
                <i class="fa fa-pencil"></i> Add Customer
            </a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact Info</th>
                <th>Credit Limit</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($data['customers'] as $customer) : ?>
        <tr>
            <td><?php echo $customer->customer_id; ?></td>
            <td><?php echo $customer->customer_name; ?></td>
            <td><?php echo $customer->contact_info; ?></td>
            <td><?php echo $customer->credit_limit; ?></td>
            <td>
                <a href="<?php echo URLROOT; ?>/customers/edit/<?php echo $customer->customer_id; ?>" class="btn btn-dark">Edit</a>
                <form class="inline" action="<?php echo URLROOT; ?>/customers/delete/<?php echo $customer->customer_id; ?>" method="post">
                    <input type="submit" value="Delete" class="btn btn-danger">
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php require APPROOT . '/views/layout/footer.php'; ?>
