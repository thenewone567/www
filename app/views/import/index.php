<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Bulk Import</h1>
            <p class="text-muted">Import products, suppliers, and relationships from CSV files</p>
        </div>
        <a href="<?php echo URLROOT; ?>/products" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <!-- Import Options -->
    <div class="row">
        <!-- Products Import -->
        <div class="col-lg-4 mb-4">
            <div class="card theme-card-light h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-box mr-2"></i>Import Products
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Import multiple products with their basic information, pricing, and inventory
                        details.</p>

                    <div class="mb-3">
                        <strong>Includes:</strong>
                        <ul class="list-unstyled ml-3 mt-2">
                            <li><i class="fas fa-check text-success mr-2"></i>Product information</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Pricing and margins</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Inventory levels</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Categories and brands</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/import/downloadTemplate/products"
                            class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-download mr-2"></i>Download Template
                        </a>
                        <a href="<?php echo URLROOT; ?>/import/products" class="btn btn-primary">
                            <i class="fas fa-upload mr-2"></i>Import Products
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suppliers Import -->
        <div class="col-lg-4 mb-4">
            <div class="card theme-card-light h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-users mr-2"></i>Import Suppliers
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Import supplier information including contact details and business information.
                    </p>

                    <div class="mb-3">
                        <strong>Includes:</strong>
                        <ul class="list-unstyled ml-3 mt-2">
                            <li><i class="fas fa-check text-success mr-2"></i>Supplier details</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Contact information</li>
                            <li><i class="fas fa-check text-success mr-2"></i>GST and business info</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Address details</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/import/downloadTemplate/suppliers"
                            class="btn btn-outline-success btn-sm">
                            <i class="fas fa-download mr-2"></i>Download Template
                        </a>
                        <a href="<?php echo URLROOT; ?>/import/suppliers" class="btn btn-success">
                            <i class="fas fa-upload mr-2"></i>Import Suppliers
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product-Suppliers Import -->
        <div class="col-lg-4 mb-4">
            <div class="card theme-card-light h-100">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-link mr-2"></i>Import Product-Supplier Links
                    </h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Import product-supplier relationships with pricing and terms for multi-supplier
                        setup.</p>

                    <div class="mb-3">
                        <strong>Includes:</strong>
                        <ul class="list-unstyled ml-3 mt-2">
                            <li><i class="fas fa-check text-success mr-2"></i>Supplier pricing</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Lead times</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Payment terms</li>
                            <li><i class="fas fa-check text-success mr-2"></i>Primary supplier flags</li>
                        </ul>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="<?php echo URLROOT; ?>/import/downloadTemplate/product_suppliers"
                            class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-download mr-2"></i>Download Template
                        </a>
                        <a href="<?php echo URLROOT; ?>/import/productSuppliers" class="btn btn-warning">
                            <i class="fas fa-upload mr-2"></i>Import Relationships
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Instructions -->
    <div class="row">
        <div class="col-12">
            <div class="card theme-card-light">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle text-info mr-2"></i>Import Instructions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-list-ol mr-2"></i>Step-by-Step Process:</h6>
                            <ol>
                                <li class="mb-2"><strong>Download Template:</strong> Use the template files to ensure
                                    correct formatting</li>
                                <li class="mb-2"><strong>Fill Data:</strong> Add your data to the CSV template (keep
                                    headers intact)</li>
                                <li class="mb-2"><strong>Validate:</strong> Check for required fields and correct
                                    formats</li>
                                <li class="mb-2"><strong>Import:</strong> Upload your completed CSV file</li>
                                <li class="mb-2"><strong>Review:</strong> Check import results and fix any errors</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-exclamation-triangle text-warning mr-2"></i>Important Notes:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Use UTF-8 encoding for
                                    CSV files</li>
                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Required fields must be
                                    filled</li>
                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>SKUs must be unique for
                                    products</li>
                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>Supplier names must be
                                    unique</li>
                                <li class="mb-2"><i class="fas fa-check text-success mr-2"></i>For relationships,
                                    products and suppliers must exist first</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recommended Import Order -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h6><i class="fas fa-sort-numeric-up mr-2"></i>Recommended Import Order:</h6>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="badge badge-primary p-2 mr-2">1</span>
                    <span class="flex-grow-1 mr-2">Import Suppliers first</span>
                    <i class="fas fa-arrow-right text-muted mr-2"></i>
                    <span class="badge badge-success p-2 mr-2">2</span>
                    <span class="flex-grow-1 mr-2">Then import Products</span>
                    <i class="fas fa-arrow-right text-muted mr-2"></i>
                    <span class="badge badge-warning p-2 mr-2">3</span>
                    <span class="flex-grow-1">Finally import Product-Supplier relationships</span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>