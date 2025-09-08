<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<div class="row">
    <div class="col-md-12">
        <div class="float-right">
            <a href="#" class="btn btn-primary" onclick="window.print();">Print</a>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-sm-6">
                <h6 class="mb-3">From:</h6>
                <div>
                    <strong><?php echo ($data['company']->company_name ?? $data['settings']['company_name'] ?? company_name()); ?></strong>
                </div>
                <div><?php echo ($data['company']->address ?? $data['settings']['company_address'] ?? ''); ?></div>
                <div>Email: <?php echo ($data['company']->email ?? $data['settings']['company_email'] ?? ''); ?></div>
                <div>Phone: <?php echo ($data['company']->phone ?? $data['settings']['company_phone'] ?? ''); ?></div>
            </div>

            <div class="col-sm-6">
                <h6 class="mb-3">To:</h6>
                <div>
                    <strong><?php echo isset($data['customer']->customer_name) ? $data['customer']->customer_name : ''; ?></strong>
                </div>
                <div><?php echo isset($data['customer']->contact_info) ? $data['customer']->contact_info : ''; ?></div>
            </div>
        </div>

        <div class="table-responsive-sm">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="center">#</th>
                        <th>Item</th>
                        <th>Description</th>
                        <th class="right">Unit Cost</th>
                        <th class="center">Qty</th>
                        <th class="right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1;
                    if (!empty($data['saleItems'])): ?>
                        <?php foreach ($data['saleItems'] as $item): ?>
                            <tr>
                                <td class="center"><?php echo $i; ?></td>
                                <td class="left strong"><?php echo $item->product_id; ?></td>
                                <td class="left"></td>
                                <td class="right">$<?php echo $item->unit_price; ?></td>
                                <td class="center"><?php echo $item->quantity; ?></td>
                                <td class="right">$<?php echo $item->quantity * $item->unit_price; ?></td>
                            </tr>
                            <?php $i++; endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No sale items found</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-lg-4 col-sm-5">

            </div>

            <div class="col-lg-4 col-sm-5 ml-auto">
                <table class="table table-clear">
                    <tbody>
                        <tr>
                            <td class="left">
                                <strong class="text-dark">Subtotal</strong>
                            </td>
                            <td class="right">$<?php echo $data['invoice']->total_amount ?? '0'; ?></td>
                        </tr>
                        <tr>
                            <td class="left">
                                <strong class="text-dark">Discount (20%)</strong>
                            </td>
                            <td class="right">$<?php echo $data['invoice']->discount_amount ?? '0'; ?></td>
                        </tr>
                        <tr>
                            <td class="left">
                                <strong class="text-dark">VAT (10%)</strong>
                            </td>
                            <td class="right">$<?php echo $data['invoice']->tax_amount ?? '0'; ?></td>
                        </tr>
                        <tr>
                            <td class="left">
                                <strong class="text-dark">Total</strong>
                            </td>
                            <td class="right">
                                <strong class="text-dark">$<?php echo $data['invoice']->total_amount ?? '0'; ?></strong>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
<script src="<?php echo URLROOT; ?>/public/js/main.js"></script>
</body>

</html>