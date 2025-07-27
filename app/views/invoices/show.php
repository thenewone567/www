<?php require APPROOT . '/views/layout/header.php'; ?>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
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
                        <strong><?php echo SITENAME; ?></strong>
                    </div>
                    <div>123 Street</div>
                    <div>City, State, Zip</div>
                    <div>Email: info@masterhardware.com</div>
                    <div>Phone: +1 234 567 890</div>
                </div>

                <div class="col-sm-6">
                    <h6 class="mb-3">To:</h6>
                    <div>
                        <strong>Customer Name</strong>
                    </div>
                    <div>Address</div>
                    <div>City, State, Zip</div>
                    <div>Email: customer@example.com</div>
                    <div>Phone: +1 234 567 890</div>
                </div>
            </div>

            <div class="table-responsive-sm">
                <table class="table table-striped">
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
                        <?php $i = 1; foreach($data['saleItems'] as $item) : ?>
                        <tr>
                            <td class="center"><?php echo $i; ?></td>
                            <td class="left strong"><?php echo $item->product_id; ?></td>
                            <td class="left"></td>
                            <td class="right">$<?php echo $item->unit_price; ?></td>
                            <td class="center"><?php echo $item->quantity; ?></td>
                            <td class="right">$<?php echo $item->quantity * $item->unit_price; ?></td>
                        </tr>
                        <?php $i++; endforeach; ?>
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
                                <td class="right">$<?php echo $data['invoice']->total_amount; ?></td>
                            </tr>
                            <tr>
                                <td class="left">
                                    <strong class="text-dark">Discount (20%)</strong>
                                </td>
                                <td class="right">$<?php echo $data['invoice']->discount_amount; ?></td>
                            </tr>
                            <tr>
                                <td class="left">
                                    <strong class="text-dark">VAT (10%)</strong>
                                </td>
                                <td class="right">$<?php echo $data['invoice']->tax_amount; ?></td>
                            </tr>
                            <tr>
                                <td class="left">
                                    <strong class="text-dark">Total</strong>
                                </td>
                                <td class="right">
                                    <strong class="text-dark">$<?php echo $data['invoice']->total_amount; ?></strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php require APPROOT . '/views/layout/footer.php'; ?>
