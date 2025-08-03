<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container">
    <div class="row align-items-center mb-4">
        <div class="col-12">
            <h1 class="mb-0">
                <i class="fas fa-plus-circle"></i>
                Create New Cycle Count
            </h1>
            <p class="text-muted mb-0">Set up a new inventory cycle count session</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check"></i> Cycle Count Details</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> <?php echo $data['success']; ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($data['errors'])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($data['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo URLROOT; ?>/cycle_counts/create" method="post">
                        <div class="form-group">
                            <label for="count_name">Count Name</label>
                            <input type="text" name="count_name" id="count_name" class="form-control" required
                                value="<?php echo htmlspecialchars($data['count_name'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control"
                                rows="3"><?php echo htmlspecialchars($data['description'] ?? ''); ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="count_date">Count Date</label>
                            <input type="date" name="count_date" id="count_date" class="form-control" required
                                value="<?php echo htmlspecialchars($data['count_date'] ?? ''); ?>">
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <?php if (!empty($data['locations'])): ?>
                                <select name="location" id="location" class="form-control">
                                    <option value="">Select Location</option>
                                    <?php foreach ($data['locations'] as $loc): ?>
                                        <option value="<?php echo htmlspecialchars($loc); ?>" <?php echo (isset($data['location']) && $data['location'] == $loc) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($loc); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <input type="text" name="location" id="location" class="form-control"
                                    value="<?php echo htmlspecialchars($data['location'] ?? ''); ?>">
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="products">Products to Count</label>
                            <select name="products[]" id="products" class="form-control" multiple>
                                <?php if (!empty($data['products'])): ?>
                                    <?php foreach ($data['products'] as $product): ?>
                                        <option value="<?php echo $product->id; ?>" <?php echo (!empty($data['selected_products']) && in_array($product->id, $data['selected_products'])) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($product->name); ?> (SKU:
                                            <?php echo htmlspecialchars($product->sku); ?>)</option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option disabled>No products available</option>
                                <?php endif; ?>
                            </select>
                            <small class="form-text text-muted">Hold Ctrl (Windows) or Command (Mac) to select multiple
                                products.</small>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i> Create Cycle Count
                        </button>
                        <a href="<?php echo URLROOT; ?>/cycle_counts" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>