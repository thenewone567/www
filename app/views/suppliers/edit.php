<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>
<div class="container-fluid mt-2">
    <div class="row">
        <div class="col-12">
            <a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="fa fa-arrow-left"></i> Back to Suppliers
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h5 class="mb-0 text-dark"><i class="fas fa-edit text-primary me-2"></i>Edit Supplier</h5>
                            <small class="text-muted">Update supplier information</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="<?php echo URLROOT; ?>/suppliers/edit/<?php echo $data['id']; ?>" method="post">

                        <!-- Basic Information -->
                        <div class="mb-4">
                            <h6 class="text-secondary mb-3 border-bottom pb-2">
                                <i class="fas fa-building me-1"></i> Basic Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="supplier_name" class="form-label">Supplier Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="supplier_name"
                                        class="form-control form-control-sm <?php echo (!empty($data['supplier_name_err'])) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $data['supplier_name']; ?>">
                                    <span class="invalid-feedback"><?php echo $data['supplier_name_err']; ?></span>
                                </div>
                                <div class="col-md-6">
                                    <label for="contact_person" class="form-label">Contact Person</label>
                                    <input type="text" name="contact_person" class="form-control form-control-sm"
                                        value="<?php echo $data['contact_person'] ?? ''; ?>" placeholder="John Doe">
                                </div>
                                <div class="col-md-4">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="tel" name="phone" class="form-control form-control-sm"
                                        value="<?php echo $data['phone'] ?? ''; ?>" placeholder="+91 98765 43210">
                                </div>
                                <div class="col-md-4">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email"
                                        class="form-control form-control-sm <?php echo (!empty($data['email_err'])) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $data['email'] ?? ''; ?>" placeholder="supplier@company.com">
                                    <span class="invalid-feedback"><?php echo $data['email_err'] ?? ''; ?></span>
                                </div>
                                <div class="col-md-4">
                                    <label for="gst_number" class="form-label">GST Number</label>
                                    <input type="text" name="gst_number"
                                        class="form-control form-control-sm <?php echo (!empty($data['gst_number_err'])) ? 'is-invalid' : ''; ?>"
                                        value="<?php echo $data['supplier']->gst_number ?? ''; ?>"
                                        placeholder="22AAAAA0000A1Z5" maxlength="15">
                                    <span class="invalid-feedback"><?php echo $data['gst_number_err'] ?? ''; ?></span>
                                </div>
                                <div class="col-12">
                                    <label for="address" class="form-label">Address</label>
                                    <textarea name="address" class="form-control form-control-sm" rows="2"
                                        placeholder="Complete business address"><?php echo $data['address'] ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Payment & Financial Information -->
                        <div class="mb-4">
                            <h6 class="text-secondary mb-3 border-bottom pb-2">
                                <i class="fas fa-credit-card me-1"></i> Payment & Financial
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="preferred_payment_terms" class="form-label">Payment Terms</label>
                                    <select name="preferred_payment_terms" class="form-select form-select-sm">
                                        <option value="Net 15" <?php echo (isset($data['supplier']->preferred_payment_terms) && $data['supplier']->preferred_payment_terms === 'Net 15') ? 'selected' : ''; ?>>Net 15 Days</option>
                                        <option value="Net 30" <?php echo (isset($data['supplier']->preferred_payment_terms) && $data['supplier']->preferred_payment_terms === 'Net 30') ? 'selected' : (empty($data['supplier']->preferred_payment_terms) ? 'selected' : ''); ?>>
                                            Net 30 Days</option>
                                        <option value="Net 45" <?php echo (isset($data['supplier']->preferred_payment_terms) && $data['supplier']->preferred_payment_terms === 'Net 45') ? 'selected' : ''; ?>>Net 45 Days</option>
                                        <option value="Net 60" <?php echo (isset($data['supplier']->preferred_payment_terms) && $data['supplier']->preferred_payment_terms === 'Net 60') ? 'selected' : ''; ?>>Net 60 Days</option>
                                        <option value="COD" <?php echo (isset($data['supplier']->preferred_payment_terms) && $data['supplier']->preferred_payment_terms === 'COD') ? 'selected' : ''; ?>>
                                            Cash on Delivery</option>
                                        <option value="Advance" <?php echo (isset($data['supplier']->preferred_payment_terms) && $data['supplier']->preferred_payment_terms === 'Advance') ? 'selected' : ''; ?>>Advance Payment</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="credit_limit" class="form-label">Credit Limit (₹)</label>
                                    <input type="number" name="credit_limit" class="form-control form-control-sm"
                                        value="<?php echo $data['supplier']->credit_limit ?? 0; ?>" placeholder="0.00"
                                        step="0.01" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="current_outstanding" class="form-label">Outstanding (₹)</label>
                                    <input type="number" name="current_outstanding" class="form-control form-control-sm"
                                        value="<?php echo $data['supplier']->current_outstanding ?? 0; ?>"
                                        placeholder="0.00" step="0.01" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Information -->
                        <div class="mb-4">
                            <h6 class="text-secondary mb-3 border-bottom pb-2">
                                <i class="fas fa-truck me-1"></i> Delivery Information
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="default_delivery_days" class="form-label">Default Delivery Time
                                        (Days)</label>
                                    <input type="number" name="default_delivery_days"
                                        class="form-control form-control-sm"
                                        value="<?php echo $data['supplier']->default_delivery_days ?? 7; ?>" min="1"
                                        max="365" placeholder="e.g., 7">
                                    <small class="text-muted">Standard delivery time for this supplier</small>
                                </div>
                            </div>
                        </div>

                        <!-- Verification & Notes -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="text-secondary mb-3 border-bottom pb-2">
                                        <i class="fas fa-shield-alt me-1"></i> Verification
                                    </h6>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input type="hidden" name="is_verified" value="0">
                                            <input type="checkbox" name="is_verified" value="1" class="form-check-input"
                                                id="is_verified" <?php echo (isset($data['supplier']->is_verified) && $data['supplier']->is_verified == 1) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="is_verified">
                                                Verified Supplier
                                            </label>
                                        </div>
                                        <small class="text-muted">Mark as verified after document verification</small>
                                    </div>
                                    <div>
                                        <label for="verification_date" class="form-label">Verification Date</label>
                                        <input type="date" name="verification_date" class="form-control form-control-sm"
                                            value="<?php echo $data['supplier']->verification_date ?? ''; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <h6 class="text-secondary mb-3 border-bottom pb-2">
                                        <i class="fas fa-sticky-note me-1"></i> Additional Notes
                                    </h6>
                                    <div>
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea name="notes" class="form-control form-control-sm" rows="3"
                                            placeholder="Special instructions, terms, etc."><?php echo $data['supplier']->notes ?? ''; ?></textarea>
                                        <small class="text-muted">Internal notes for supplier management</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Update Supplier
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-capitalize supplier name and contact person name
    document.addEventListener('DOMContentLoaded', function () {
        // Auto-capitalize supplier name
        const supplierNameField = document.querySelector('input[name="supplier_name"]');
        if (supplierNameField) {
            supplierNameField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = capitalizeWords(this.value);
                }
            });
        }

        // Auto-capitalize contact person name
        const contactPersonField = document.querySelector('input[name="contact_person"]');
        if (contactPersonField) {
            contactPersonField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = capitalizeWords(this.value);
                }
            });
        }

        // Auto-uppercase GST number for consistency
        const gstNumberField = document.querySelector('input[name="gst_number"]');
        if (gstNumberField) {
            gstNumberField.addEventListener('blur', function () {
                if (this.value) {
                    this.value = this.value.toUpperCase();
                }
            });
        }
    });

    // Capitalize first letter of each word
    function capitalizeWords(str) {
        return str.toLowerCase().replace(/\b\w/g, function (letter) {
            return letter.toUpperCase();
        });
    }
</script>

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