<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<div class="row">
    <div class="col-md-6">
        <h1>Invoices</h1>
    </div>
</div>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Invoice Number</th>
            <th>Sale ID</th>
            <th>Date</th>
            <th>Total Amount</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data['invoices'])): ?>
            <?php foreach ($data['invoices'] as $invoice): ?>
                <tr>
                    <td><?php echo $invoice->invoice_id; ?></td>
                    <td><?php echo $invoice->invoice_number; ?></td>
                    <td><?php echo $invoice->sale_id; ?></td>
                    <td><?php echo $invoice->invoice_date ?? '-'; ?></td>
                    <td><?php echo $invoice->total_amount; ?></td>
                    <td>
                        <a href="<?php echo URLROOT; ?>/invoices/show/<?php echo $invoice->invoice_id; ?>"
                            class="btn btn-dark">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" class="text-center text-muted">No invoices found</td>
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