<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Import Products</h1>
            <p class="text-muted">Upload CSV file to import multiple products</p>
        </div>
        <a href="<?php echo URLROOT; ?>/import" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Import
        </a>
    </div>

    <!-- Alert Messages -->
    <?php if (!empty($data['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle mr-2"></i><?php echo $data['message']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle mr-2"></i><?php echo $data['error']; ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (!isset($data['results'])): ?>
        <!-- Import Form -->
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card theme-card-light">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-upload text-primary mr-2"></i>Upload Products CSV
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Instructions -->
                        <div class="alert alert-info mb-4">
                            <h6><i class="fas fa-info-circle mr-2"></i>Before you start:</h6>
                            <ul class="mb-0">
                                <li>Download the template CSV file to ensure proper formatting</li>
                                <li>Fill in your product data (required fields: product_name, sku, selling_price)</li>
                                <li>Categories and brands will be created automatically if they don't exist</li>
                                <li>Products with duplicate SKUs will be skipped</li>
                            </ul>
                        </div>

                        <!-- Template Download -->
                        <div class="mb-4">
                            <a href="<?php echo URLROOT; ?>/import/downloadTemplate/products"
                                class="btn btn-outline-primary">
                                <i class="fas fa-download mr-2"></i>Download Template CSV
                            </a>
                        </div>

                        <!-- Upload Form -->
                        <form action="<?php echo URLROOT; ?>/import/products" method="post" enctype="multipart/form-data"
                            id="importForm">
                            <div class="mb-3">
                                <label for="csv_file" class="form-label">Select CSV File <span
                                        class="text-danger">*</span></label>
                                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv"
                                    required>
                                <div class="form-text">Please select a CSV file (maximum size: 10MB)</div>
                            </div>

                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="confirm_backup" required>
                                    <label class="form-check-label" for="confirm_backup">
                                        I confirm that I have backed up my data before importing
                                    </label>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="previewCSV()">
                                    <i class="fas fa-eye mr-2"></i>Preview
                                </button>
                                <button type="submit" class="btn btn-primary" id="importBtn">
                                    <i class="fas fa-upload mr-2"></i>Import Products
                                </button>
                            </div>
                        </form>

                        <!-- CSV Preview -->
                        <div id="csvPreview" class="mt-4" style="display: none;">
                            <h6><i class="fas fa-eye mr-2"></i>CSV Preview</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm" id="previewTable">
                                    <thead class="table-light">
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CSV Format Help -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card theme-card-light">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-table text-info mr-2"></i>CSV Format Guide
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Column</th>
                                        <th>Required</th>
                                        <th>Format</th>
                                        <th>Example</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><code>product_name</code></td>
                                        <td><span class="badge badge-danger">Required</span></td>
                                        <td>Text</td>
                                        <td>Heavy Duty Drill Machine</td>
                                        <td>Name of the product</td>
                                    </tr>
                                    <tr>
                                        <td><code>sku</code></td>
                                        <td><span class="badge badge-danger">Required</span></td>
                                        <td>Text (Unique)</td>
                                        <td>DRILL-HD-001</td>
                                        <td>Stock Keeping Unit (must be unique)</td>
                                    </tr>
                                    <tr>
                                        <td><code>selling_price</code></td>
                                        <td><span class="badge badge-danger">Required</span></td>
                                        <td>Decimal</td>
                                        <td>15000.00</td>
                                        <td>Customer selling price</td>
                                    </tr>
                                    <tr>
                                        <td><code>category_name</code></td>
                                        <td><span class="badge badge-secondary">Optional</span></td>
                                        <td>Text</td>
                                        <td>Power Tools</td>
                                        <td>Product category (will be created if not exists)</td>
                                    </tr>
                                    <tr>
                                        <td><code>brand_name</code></td>
                                        <td><span class="badge badge-secondary">Optional</span></td>
                                        <td>Text</td>
                                        <td>DeWalt</td>
                                        <td>Product brand (will be created if not exists)</td>
                                    </tr>
                                    <tr>
                                        <td><code>cost_price</code></td>
                                        <td><span class="badge badge-secondary">Optional</span></td>
                                        <td>Decimal</td>
                                        <td>12000.00</td>
                                        <td>Purchase/manufacturing cost</td>
                                    </tr>
                                    <tr>
                                        <td><code>initial_quantity</code></td>
                                        <td><span class="badge badge-secondary">Optional</span></td>
                                        <td>Integer</td>
                                        <td>10</td>
                                        <td>Starting stock quantity</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php else: ?>
        <!-- Import Results -->
        <div class="row">
            <div class="col-12">
                <div class="card theme-card-light">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar text-success mr-2"></i>Import Results
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Summary Stats -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4><?php echo $data['results']['total_rows']; ?></h4>
                                        <small>Total Rows</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4><?php echo $data['results']['successful']; ?></h4>
                                        <small>Successful</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h4><?php echo $data['results']['failed']; ?></h4>
                                        <small>Failed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h4><?php echo round(($data['results']['successful'] / $data['results']['total_rows']) * 100, 1); ?>%
                                        </h4>
                                        <small>Success Rate</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Successful Imports -->
                        <?php if (!empty($data['results']['created_products'])): ?>
                            <div class="mb-4">
                                <h6><i class="fas fa-check-circle text-success mr-2"></i>Successfully Imported Products</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Row</th>
                                                <th>SKU</th>
                                                <th>Product Name</th>
                                                <th>Product ID</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['results']['created_products'] as $product): ?>
                                                <tr>
                                                    <td><?php echo $product['row']; ?></td>
                                                    <td><code><?php echo htmlspecialchars($product['sku']); ?></code></td>
                                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                    <td>
                                                        <a href="<?php echo URLROOT; ?>/products/view/<?php echo $product['id']; ?>"
                                                            class="btn btn-sm btn-outline-primary">
                                                            View #<?php echo $product['id']; ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Import Errors -->
                        <?php if (!empty($data['results']['errors'])): ?>
                            <div class="mb-4">
                                <h6><i class="fas fa-exclamation-triangle text-danger mr-2"></i>Import Errors</h6>
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <tr>
                                                <th>Row</th>
                                                <th>SKU</th>
                                                <th>Error</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($data['results']['errors'] as $error): ?>
                                                <tr>
                                                    <td><?php echo $error['row']; ?></td>
                                                    <td><code><?php echo htmlspecialchars($error['sku']); ?></code></td>
                                                    <td class="text-danger"><?php echo htmlspecialchars($error['error']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo URLROOT; ?>/import/products" class="btn btn-primary">
                                <i class="fas fa-upload mr-2"></i>Import More Products
                            </a>
                            <a href="<?php echo URLROOT; ?>/products" class="btn btn-success">
                                <i class="fas fa-box mr-2"></i>View All Products
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    // CSV Preview functionality
    function previewCSV() {
        const fileInput = document.getElementById('csv_file');
        const file = fileInput.files[0];

        if (!file) {
            alert('Please select a CSV file first');
            return;
        }

        if (file.type !== 'text/csv' && !file.name.endsWith('.csv')) {
            alert('Please select a valid CSV file');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            const csv = e.target.result;
            const lines = csv.split('\n').slice(0, 6); // Show only first 5 rows + header

            if (lines.length < 2) {
                alert('CSV file appears to be empty or invalid');
                return;
            }

            const table = document.getElementById('previewTable');
            const thead = table.querySelector('thead');
            const tbody = table.querySelector('tbody');

            // Clear previous content
            thead.innerHTML = '';
            tbody.innerHTML = '';

            // Create header row
            const headers = lines[0].split(',').map(h => h.trim().replace(/['"]/g, ''));
            const headerRow = document.createElement('tr');
            headers.forEach(header => {
                const th = document.createElement('th');
                th.textContent = header;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);

            // Create data rows
            for (let i = 1; i < lines.length && i <= 5; i++) {
                if (lines[i].trim()) {
                    const cells = lines[i].split(',').map(c => c.trim().replace(/['"]/g, ''));
                    const row = document.createElement('tr');

                    cells.forEach((cell, index) => {
                        const td = document.createElement('td');
                        td.textContent = cell || '-';

                        // Truncate long text
                        if (cell.length > 30) {
                            td.textContent = cell.substring(0, 30) + '...';
                            td.title = cell;
                        }

                        row.appendChild(td);
                    });
                    tbody.appendChild(row);
                }
            }

            // Show preview
            document.getElementById('csvPreview').style.display = 'block';
        };

        reader.readAsText(file);
    }

    // Form validation
    document.getElementById('importForm').addEventListener('submit', function (e) {
        const fileInput = document.getElementById('csv_file');
        const confirmCheckbox = document.getElementById('confirm_backup');

        if (!fileInput.files[0]) {
            e.preventDefault();
            alert('Please select a CSV file');
            return;
        }

        if (!confirmCheckbox.checked) {
            e.preventDefault();
            alert('Please confirm that you have backed up your data');
            return;
        }

        // Show loading state
        const importBtn = document.getElementById('importBtn');
        importBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Importing...';
        importBtn.disabled = true;
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>