<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="row">
    <div class="col-md-6">
        <h1>Customers</h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/customers/add" class="btn btn-primary float-right">
            <i class="fa fa-plus"></i> Add Customer
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
        <?php if (!empty($data['customers'])): ?>
            <?php foreach ($data['customers'] as $customer): ?>
                <tr>
                    <td><?php echo $customer->customer_id; ?></td>
                    <td><?php echo $customer->customer_name; ?></td>
                    <td><?php echo $customer->contact_info; ?></td>
                    <td><?php echo $customer->credit_limit; ?></td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/customers/edit/<?php echo $customer->customer_id; ?>"
                            class="btn btn-dark">Edit</a>
                        <form class="d-inline"
                            action="<?php echo URLROOT; ?>/customers/delete/<?php echo $customer->customer_id; ?>" method="post"
                            style="display:inline;">
                            <input type="submit" value="Delete" class="btn btn-danger">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center text-muted">No customers found</td>
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
    <script src="<?php echo URLROOT; ?>/js/main.js"></script>
</body>
</html>