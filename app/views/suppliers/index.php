<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
<div class="row">
    <div class="col-md-6">
        <h1>Suppliers</h1>
    </div>
    <div class="col-md-6">
        <a href="<?php echo URLROOT; ?>/suppliers/add" class="btn btn-primary float-right">
            <i class="fa fa-plus"></i> Add Supplier
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
        <?php if (!empty($data['suppliers'])): ?>
            <?php foreach ($data['suppliers'] as $supplier): ?>
                <tr>
                    <td><?php echo $supplier->supplier_id; ?></td>
                    <td><?php echo $supplier->supplier_name; ?></td>
                    <td><?php echo $supplier->contact_info; ?></td>
                    <td><?php echo $supplier->gst_info; ?></td>
                    <td><?php echo $supplier->due_amount; ?></td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/suppliers/edit/<?php echo $supplier->supplier_id; ?>"
                            class="btn btn-dark">Edit</a>
                        <form class="d-inline"
                            action="<?php echo URLROOT; ?>/suppliers/delete/<?php echo $supplier->supplier_id; ?>" method="post"
                            style="display:inline;">
                            <input type="submit" value="Delete" class="btn btn-danger">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted">No suppliers found</td>
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