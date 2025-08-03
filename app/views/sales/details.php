<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="row">
    <div class="col-md-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fa-solid fa-receipt"></i> Sale Details
                        #<?php echo $data['sale']->sale_id; ?></h4>
                    <div>
                        <a href="<?php echo URLROOT; ?>/invoices/generate/<?php echo $data['sale']->sale_id; ?>"
                            class="btn btn-success me-2">
                            <i class="fa-solid fa-file-invoice"></i> Generate Invoice
                        </a>
                        <a href="<?php echo URLROOT; ?>/sales/list" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Back to Sales
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php flash('sale_message'); ?>

                <!-- Sale Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Sale Information</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Sale ID:</strong></td>
                                <td>#<?php echo $data['sale']->sale_id; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Date:</strong></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($data['sale']->sale_date)); ?></td>
                            </tr>
                            <tr>
                                <td><strong>Customer ID:</strong></td>
                                <td><?php echo $data['sale']->customer_id; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Payment Mode:</strong></td>
                                <td>
                                    <span
                                        class="badge bg-<?php echo $data['sale']->payment_mode == 'cash' ? 'success' : ($data['sale']->payment_mode == 'card' ? 'primary' : 'secondary'); ?>">
                                        <?php echo ucfirst($data['sale']->payment_mode); ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Amount Details</h6>
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-end">
                                    <strong>$<?php echo number_format($data['sale']->total_amount, 2); ?></strong>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Sale Items -->
                <div class="row">
                    <div class="col-md-12">
                        <h6>Sale Items</h6>
                        <?php if (empty($data['saleItems'])): ?>
                            <div class="alert alert-info">
                                <i class="fa-solid fa-info-circle"></i> No items found for this sale.
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Product ID</th>
                                            <th>Quantity</th>
                                            <th>Unit Price</th>
                                            <th>Discount</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $subtotal = 0;
                                        foreach ($data['saleItems'] as $item):
                                            $itemSubtotal = ($item->quantity * $item->unit_price) - $item->discount;
                                            $subtotal += $itemSubtotal;
                                            ?>
                                            <tr>
                                                <td><?php echo $item->product_id; ?></td>
                                                <td><?php echo $item->quantity; ?></td>
                                                <td>$<?php echo number_format($item->unit_price, 2); ?></td>
                                                <td>$<?php echo number_format($item->discount, 2); ?></td>
                                                <td>$<?php echo number_format($itemSubtotal, 2); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <th colspan="4" class="text-end">Total:</th>
                                            <th>$<?php echo number_format($subtotal, 2); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


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