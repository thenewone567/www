<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
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
<table class="table table-striped">
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
<table class="table table-striped">
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