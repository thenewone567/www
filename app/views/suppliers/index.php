<?php require APPROOT . DS . 'app' . DS . views/layout/header.php'; ?>
    <div class="row">
        <div class="col-md-6">
            <h1>Suppliers</h1>
        </div>
        <div class="col-md-6">
            <a href="<?php echo URLROOT; ?>/suppliers/add" class="btn btn-primary pull-right">
                <i class="fa fa-pencil"></i> Add Supplier
            </a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Contact Info</th>
                <th>GST Info</th>
                <th>Due Amount</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
    <?php foreach($data['suppliers'] as $supplier) : ?>
        <tr>
            <td><?php echo $supplier->supplier_id; ?></td>
            <td><?php echo $supplier->supplier_name; ?></td>
            <td><?php echo $supplier->contact_info; ?></td>
            <td><?php echo $supplier->gst_info; ?></td>
            <td><?php echo $supplier->due_amount; ?></td>
            <td>
                <a href="<?php echo URLROOT; ?>/suppliers/edit/<?php echo $supplier->supplier_id; ?>" class="btn btn-dark">Edit</a>
                <form class="inline" action="<?php echo URLROOT; ?>/suppliers/delete/<?php echo $supplier->supplier_id; ?>" method="post">
                    <input type="submit" value="Delete" class="btn btn-danger">
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
<?php require APPROOT . DS . 'app' . DS . views/layout/footer.php'; ?>
