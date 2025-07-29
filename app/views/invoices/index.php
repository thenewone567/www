<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'header.php'; ?>
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
<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layout' . DS . 'footer.php'; ?>