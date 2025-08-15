<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid fade-in">
    <!-- Workflow Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="kpi-card">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h1 class="mb-2">
                            <i class="fas fa-sitemap text-primary mr-3"></i>
                            Product & Supplier Management Workflow
                        </h1>
                        <p class="text-muted mb-0">Follow the step-by-step process to link products and suppliers</p>
                    </div>
                    <div class="text-right">
                        <a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Suppliers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center text-center mx-2 mb-2">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; font-weight: bold;" id="step-indicator-1">1</div>
                        <div class="ml-2">
                            <small class="text-primary font-weight-bold">Search Product</small>
                        </div>
                    </div>
                    <div class="flex-fill mx-1" style="height: 2px; background: var(--card-border);"></div>
                    <div class="d-flex align-items-center text-center mx-2 mb-2">
                        <div class="rounded-circle border d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; font-weight: bold; background: var(--card-bg); border-color: var(--card-border) !important; color: var(--text-muted);"
                            id="step-indicator-2">2</div>
                        <div class="ml-2">
                            <small class="text-muted">Verify/Add Product</small>
                        </div>
                    </div>
                    <div class="flex-fill mx-1" style="height: 2px; background: var(--card-border);"></div>
                    <div class="d-flex align-items-center text-center mx-2 mb-2">
                        <div class="rounded-circle border d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; font-weight: bold; background: var(--card-bg); border-color: var(--card-border) !important; color: var(--text-muted);"
                            id="step-indicator-3">3</div>
                        <div class="ml-2">
                            <small class="text-muted">Search Supplier</small>
                        </div>
                    </div>
                    <div class="flex-fill mx-1" style="height: 2px; background: var(--card-border);"></div>
                    <div class="d-flex align-items-center text-center mx-2 mb-2">
                        <div class="rounded-circle border d-flex align-items-center justify-content-center"
                            style="width: 40px; height: 40px; font-weight: bold; background: var(--card-bg); border-color: var(--card-border) !important; color: var(--text-muted);"
                            id="step-indicator-4">4</div>
                        <div class="ml-2">
                            <small class="text-muted">Link & Complete</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Search for Product -->
    <div class="workflow-step" id="workflow-step-1">
        <div class="row">
            <div class="col-12">
                <div class="kpi-card">
                    <h3 class="mb-4">
                        <i class="fas fa-search text-primary mr-2"></i>
                        Step 1: Search for Product
                    </h3>

                    <div class="enhanced-search mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" id="productSearch"
                                placeholder="Search by product name, SKU, or barcode...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="searchProductBtn">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="productSearchResults" class="d-none">
                        <h5 class="mb-3">Search Results:</h5>
                        <div id="productResultsList" class="enhanced-table-container">
                            <!-- Results will be populated here -->
                        </div>
                    </div>

                    <div class="quick-actions mt-4">
                        <button class="btn btn-success btn-lg" id="addNewProductBtn">
                            <i class="fas fa-plus mr-2"></i>Product Not Found? Add New Product
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Product Verification/Addition -->
    <div class="workflow-step d-none" id="workflow-step-2">
        <div class="row">
            <div class="col-12">
                <div class="kpi-card">
                    <h3 class="mb-4">
                        <i class="fas fa-check-circle text-success mr-2"></i>
                        Step 2: Product Verification
                    </h3>

                    <div id="selectedProductInfo" class="kpi-card" style="border-left: 4px solid var(--primary);">
                        <!-- Selected product info will be displayed here -->
                    </div>

                    <div id="addProductForm" class="d-none">
                        <h5 class="mb-3">Add New Product</h5>
                        <form id="newProductForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="productName" class="form-label">Product Name *</label>
                                    <input type="text" class="form-control" id="productName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productSku" class="form-label">SKU</label>
                                    <input type="text" class="form-control" id="productSku">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productCategory" class="form-label">Category</label>
                                    <select class="form-control" id="productCategory">
                                        <option value="">Select Category</option>
                                        <!-- Categories will be loaded here -->
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="productPrice" class="form-label">Price</label>
                                    <input type="number" step="0.01" class="form-control" id="productPrice">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="quick-actions mt-4">
                        <button class="btn btn-secondary" id="backToSearchBtn">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Search
                        </button>
                        <button class="btn btn-primary btn-lg ml-2" id="proceedToSupplierBtn">
                            <i class="fas fa-arrow-right mr-2"></i>Continue to Supplier Search
                        </button>
                        <button class="btn btn-success btn-lg ml-2 d-none" id="saveAndContinueBtn">
                            <i class="fas fa-save mr-2"></i>Save Product & Continue
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Search for Supplier -->
    <div class="workflow-step d-none" id="workflow-step-3">
        <div class="row">
            <div class="col-12">
                <div class="kpi-card">
                    <h3 class="mb-4">
                        <i class="fas fa-truck text-warning mr-2"></i>
                        Step 3: Search for Supplier
                    </h3>

                    <div class="enhanced-search mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg" id="supplierSearch"
                                placeholder="Search by supplier name, email, or contact...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" id="searchSupplierBtn">
                                    <i class="fas fa-search mr-2"></i>Search
                                </button>
                            </div>
                        </div>
                    </div>

                    <div id="supplierSearchResults" class="d-none">
                        <h5 class="mb-3">Available Suppliers:</h5>
                        <div id="supplierResultsList" class="enhanced-table-container">
                            <!-- Results will be populated here -->
                        </div>
                    </div>

                    <div class="quick-actions mt-4">
                        <button class="btn btn-secondary" id="backToProductBtn">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Product
                        </button>
                        <button class="btn btn-success btn-lg ml-2" id="addNewSupplierBtn">
                            <i class="fas fa-plus mr-2"></i>Supplier Not Found? Add New Supplier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 4: Link and Complete -->
    <div class="workflow-step d-none" id="workflow-step-4">
        <div class="row">
            <div class="col-12">
                <div class="kpi-card">
                    <h3 class="mb-4">
                        <i class="fas fa-link text-success mr-2"></i>
                        Step 4: Link Supplier & Product
                    </h3>

                    <div id="linkSummary" class="row mb-4">
                        <div class="col-md-6">
                            <div class="kpi-card" style="border-left: 4px solid var(--primary);">
                                <h6><i class="fas fa-box mr-2"></i>Selected Product</h6>
                                <div id="finalProductInfo">
                                    <!-- Product info will be displayed here -->
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kpi-card" style="border-left: 4px solid var(--warning);">
                                <h6><i class="fas fa-truck mr-2"></i>Selected Supplier</h6>
                                <div id="finalSupplierInfo">
                                    <!-- Supplier info will be displayed here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="addSupplierForm" class="d-none mb-4">
                        <h5 class="mb-3">Add New Supplier</h5>
                        <form id="newSupplierForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="supplierName" class="form-label">Supplier Name *</label>
                                    <input type="text" class="form-control" id="supplierName" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="supplierEmail" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="supplierEmail">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="supplierPhone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="supplierPhone">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="supplierAddress" class="form-label">Address</label>
                                    <input type="text" class="form-control" id="supplierAddress">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="linkDetailsForm">
                        <h5 class="mb-3">Link Details (Optional)</h5>
                        <form id="linkForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="supplierPrice" class="form-label">Supplier Price</label>
                                    <input type="number" step="0.01" class="form-control" id="supplierPrice">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="qualityRating" class="form-label">Quality Rating (1-5)</label>
                                    <select class="form-control" id="qualityRating">
                                        <option value="">Select Rating</option>
                                        <option value="1">1 - Poor</option>
                                        <option value="2">2 - Fair</option>
                                        <option value="3">3 - Good</option>
                                        <option value="4">4 - Very Good</option>
                                        <option value="5">5 - Excellent</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="linkNotes" class="form-label">Notes</label>
                                    <textarea class="form-control" id="linkNotes" rows="3"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="quick-actions mt-4">
                        <button class="btn btn-secondary" id="backToSupplierBtn">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Supplier Search
                        </button>
                        <button class="btn btn-success btn-lg ml-2" id="completeLinkBtn">
                            <i class="fas fa-check mr-2"></i>Complete Link
                        </button>
                        <button class="btn btn-primary btn-lg ml-2" id="linkAnotherBtn" style="display: none;">
                            <i class="fas fa-plus mr-2"></i>Link Another Supplier
                        </button>
                        <button class="btn btn-outline-primary ml-2" id="viewLinkedProductsBtn" style="display: none;">
                            <i class="fas fa-eye mr-2"></i>View All Links
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle mr-2"></i>Success!
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="successMessage"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="startNewLinkBtn">Start New Link</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    class WorkflowManager {
        constructor() {
            this.currentStep = 1;
            this.selectedProduct = null;
            this.selectedSupplier = null;
            this.init();
        }

        init() {
            this.bindEvents();
            this.loadCategories();
        }

        bindEvents() {
            // Step 1: Product Search
            document.getElementById('searchProductBtn').addEventListener('click', () => this.searchProducts());
            document.getElementById('productSearch').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.searchProducts();
            });
            document.getElementById('addNewProductBtn').addEventListener('click', () => this.showAddProductForm());

            // Step 2: Product Verification
            document.getElementById('backToSearchBtn').addEventListener('click', () => this.goToStep(1));
            document.getElementById('proceedToSupplierBtn').addEventListener('click', () => this.goToStep(3));
            document.getElementById('saveAndContinueBtn').addEventListener('click', () => this.saveProductAndContinue());

            // Step 3: Supplier Search
            document.getElementById('searchSupplierBtn').addEventListener('click', () => this.searchSuppliers());
            document.getElementById('supplierSearch').addEventListener('keypress', (e) => {
                if (e.key === 'Enter') this.searchSuppliers();
            });
            document.getElementById('backToProductBtn').addEventListener('click', () => this.goToStep(2));
            document.getElementById('addNewSupplierBtn').addEventListener('click', () => this.showAddSupplierForm());

            // Step 4: Link Completion
            document.getElementById('backToSupplierBtn').addEventListener('click', () => this.goToStep(3));
            document.getElementById('completeLinkBtn').addEventListener('click', () => this.completeLink());
            document.getElementById('linkAnotherBtn').addEventListener('click', () => this.linkAnother());
            document.getElementById('startNewLinkBtn').addEventListener('click', () => this.startNew());
        }

        goToStep(stepNumber) {
            // Hide all steps
            document.querySelectorAll('.workflow-step').forEach(step => step.classList.add('d-none'));

            // Show target step
            document.getElementById(`workflow-step-${stepNumber}`).classList.remove('d-none');

            // Update progress indicators
            for (let i = 1; i <= 4; i++) {
                const indicator = document.getElementById(`step-indicator-${i}`);
                const label = indicator.parentElement.querySelector('small');

                if (i < stepNumber) {
                    // Completed step
                    indicator.className = 'rounded-circle bg-success text-white d-flex align-items-center justify-content-center';
                    indicator.style.cssText = 'width: 40px; height: 40px; font-weight: bold;';
                    label.className = 'text-success font-weight-bold';
                } else if (i === stepNumber) {
                    // Active step
                    indicator.className = 'rounded-circle bg-primary text-white d-flex align-items-center justify-content-center';
                    indicator.style.cssText = 'width: 40px; height: 40px; font-weight: bold;';
                    label.className = 'text-primary font-weight-bold';
                } else {
                    // Future step
                    indicator.className = 'rounded-circle border d-flex align-items-center justify-content-center';
                    indicator.style.cssText = 'width: 40px; height: 40px; font-weight: bold; background: var(--card-bg); border-color: var(--card-border) !important; color: var(--text-muted);';
                    label.className = 'text-muted';
                }
            }

            this.currentStep = stepNumber;
        }

        async searchProducts() {
            const searchTerm = document.getElementById('productSearch').value.trim();
            if (!searchTerm) return;

            try {
                const response = await fetch(`${URLROOT}/api/getProducts.php?search=${encodeURIComponent(searchTerm)}`);
                const data = await response.json();

                if (data.success === true) {
                    this.displayProductResults(data.products);
                } else {
                    this.showError('Error searching products: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                this.showError('Error searching products: ' + error.message);
            }
        }

        displayProductResults(products) {
            const resultsContainer = document.getElementById('productResultsList');
            const searchResults = document.getElementById('productSearchResults');

            if (products.length === 0) {
                resultsContainer.innerHTML = '<div class="kpi-card text-center"><i class="fas fa-info-circle mr-2 text-primary"></i>No products found. Click "Add New Product" to create one.</div>';
            } else {
                resultsContainer.innerHTML = products.map(product => `
                <div class="kpi-card mb-2" style="cursor: pointer; transition: var(--theme-transition);" 
                     onclick="workflowManager.selectProduct(${product.product_id}, '${product.product_name}', '${product.sku || ''}', '${product.current_inventory || 0}', '${product.selling_price || '0.00'}')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${product.product_name}</h6>
                            <small class="text-muted">SKU: ${product.sku || 'N/A'} | Stock: ${product.current_inventory || 0}</small>
                        </div>
                        <div class="text-right">
                            <span class="badge badge-primary">$${product.selling_price || '0.00'}</span>
                        </div>
                    </div>
                </div>
            `).join('');
            }

            searchResults.classList.remove('d-none');
        }

        selectProduct(productId, name, sku, stock, price) {
            // Add visual selection - change border color of selected card
            document.querySelectorAll('#productResultsList .kpi-card').forEach(card => {
                card.style.borderColor = 'var(--card-border)';
                card.style.backgroundColor = 'var(--card-bg)';
            });
            event.currentTarget.style.borderColor = 'var(--primary)';
            event.currentTarget.style.backgroundColor = 'var(--bg-tertiary)';

            // Store selected product
            this.selectedProduct = {
                id: productId,
                name: name,
                sku: sku,
                stock: stock,
                price: price
            };

            // Show product info and enable continue button
            document.getElementById('selectedProductInfo').innerHTML = `
            <h6><i class="fas fa-check-circle text-success mr-2"></i>Product Selected</h6>
            <p class="mb-0"><strong>${name}</strong> (SKU: ${sku || 'N/A'}) has been selected. Click "Continue" to proceed to supplier search.</p>
        `;

            setTimeout(() => this.goToStep(2), 500);
        }

        showAddProductForm() {
            document.getElementById('addProductForm').classList.remove('d-none');
            document.getElementById('saveAndContinueBtn').classList.remove('d-none');
            document.getElementById('proceedToSupplierBtn').classList.add('d-none');
            document.getElementById('selectedProductInfo').innerHTML = `
            <h6><i class="fas fa-plus text-primary mr-2"></i>Adding New Product</h6>
            <p class="mb-0">Fill in the product details below and click "Save Product & Continue".</p>
        `;
            this.goToStep(2);
        }

        async saveProductAndContinue() {
            const productData = {
                name: document.getElementById('productName').value,
                sku: document.getElementById('productSku').value,
                category: document.getElementById('productCategory').value,
                price: document.getElementById('productPrice').value
            };

            if (!productData.name) {
                alert('Product name is required');
                return;
            }

            try {
                // Here you would make the API call to save the product
                console.log('Saving product:', productData);

                // Simulate successful save
                this.selectedProduct = {
                    id: Date.now(), // Temporary ID
                    name: productData.name,
                    sku: productData.sku,
                    price: productData.price
                };

                document.getElementById('selectedProductInfo').innerHTML = `
                <h6><i class="fas fa-check-circle text-success mr-2"></i>Product Created</h6>
                <p class="mb-0"><strong>${productData.name}</strong> has been created successfully. Proceeding to supplier search.</p>
            `;

                setTimeout(() => this.goToStep(3), 1000);

            } catch (error) {
                this.showError('Error saving product: ' + error.message);
            }
        }

        async searchSuppliers() {
            const searchTerm = document.getElementById('supplierSearch').value.trim();
            if (!searchTerm) return;

            try {
                const response = await fetch(`${URLROOT}/api/getSuppliers.php?search=${encodeURIComponent(searchTerm)}`);
                const data = await response.json();

                if (data.success === true) {
                    this.displaySupplierResults(data.suppliers);
                } else {
                    this.showError('Error searching suppliers: ' + (data.error || 'Unknown error'));
                }
            } catch (error) {
                this.showError('Error searching suppliers: ' + error.message);
            }
        }

        displaySupplierResults(suppliers) {
            const resultsContainer = document.getElementById('supplierResultsList');
            const searchResults = document.getElementById('supplierSearchResults');

            if (suppliers.length === 0) {
                resultsContainer.innerHTML = '<div class="kpi-card text-center"><i class="fas fa-info-circle mr-2 text-primary"></i>No suppliers found. Click "Add New Supplier" to create one.</div>';
            } else {
                resultsContainer.innerHTML = suppliers.map(supplier => `
                <div class="kpi-card mb-2" style="cursor: pointer; transition: var(--theme-transition);" 
                     onclick="workflowManager.selectSupplier(${supplier.supplier_id}, '${supplier.supplier_name}', '${supplier.email || ''}', '${supplier.phone || ''}')">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${supplier.supplier_name}</h6>
                            <small class="text-muted">${supplier.email || ''} | ${supplier.phone || ''}</small>
                        </div>
                        <div>
                            <i class="fas fa-chevron-right text-muted"></i>
                        </div>
                    </div>
                </div>
            `).join('');
            }

            searchResults.classList.remove('d-none');
        }

        selectSupplier(supplierId, name, email, phone) {
            // Add visual selection - change border color of selected card
            document.querySelectorAll('#supplierResultsList .kpi-card').forEach(card => {
                card.style.borderColor = 'var(--card-border)';
                card.style.backgroundColor = 'var(--card-bg)';
            });
            event.currentTarget.style.borderColor = 'var(--primary)';
            event.currentTarget.style.backgroundColor = 'var(--bg-tertiary)';

            this.selectedSupplier = {
                id: supplierId,
                name: name,
                email: email,
                phone: phone
            };

            // Update final step with selections
            document.getElementById('finalProductInfo').innerHTML = `
            <strong>${this.selectedProduct.name}</strong><br>
            <small>SKU: ${this.selectedProduct.sku || 'N/A'} | Price: $${this.selectedProduct.price || '0.00'}</small>
        `;

            document.getElementById('finalSupplierInfo').innerHTML = `
            <strong>${name}</strong><br>
            <small>${email || ''} | ${phone || ''}</small>
        `;

            setTimeout(() => this.goToStep(4), 500);
        }

        showAddSupplierForm() {
            document.getElementById('addSupplierForm').classList.remove('d-none');
            document.getElementById('finalProductInfo').innerHTML = `
            <strong>${this.selectedProduct.name}</strong><br>
            <small>SKU: ${this.selectedProduct.sku || 'N/A'} | Price: $${this.selectedProduct.price || '0.00'}</small>
        `;
            document.getElementById('finalSupplierInfo').innerHTML = `
            <em>New supplier will be created...</em>
        `;
            this.goToStep(4);
        }

        async completeLink() {
            try {
                // If adding new supplier, create it first
                if (document.getElementById('addSupplierForm').classList.contains('d-none') === false) {
                    const supplierData = {
                        name: document.getElementById('supplierName').value,
                        email: document.getElementById('supplierEmail').value,
                        phone: document.getElementById('supplierPhone').value,
                        address: document.getElementById('supplierAddress').value
                    };

                    if (!supplierData.name) {
                        alert('Supplier name is required');
                        return;
                    }

                    // TODO: Implement supplier creation API
                    // For now, simulate creating supplier
                    this.selectedSupplier = {
                        id: Date.now(),
                        name: supplierData.name,
                        email: supplierData.email,
                        phone: supplierData.phone
                    };
                }

                // Create the link
                const linkData = {
                    product_id: this.selectedProduct.id,
                    supplier_id: this.selectedSupplier.id,
                    price: document.getElementById('supplierPrice').value,
                    quality_rating: document.getElementById('qualityRating').value,
                    notes: document.getElementById('linkNotes').value
                };

                // Make the API call to create the link
                const response = await fetch(`${URLROOT}/api/linkProductSupplier.php`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(linkData)
                });

                const result = await response.json();

                if (result.success) {
                    this.showSuccess(result.message || `Successfully linked <strong>${this.selectedProduct.name}</strong> with supplier <strong>${this.selectedSupplier.name}</strong>!`);
                } else {
                    this.showError('Error creating link: ' + (result.error || 'Unknown error'));
                }

            } catch (error) {
                this.showError('Error creating link: ' + error.message);
            }
        }

        showSuccess(message) {
            document.getElementById('successMessage').innerHTML = `
            <div class="kpi-card" style="border-left: 4px solid var(--success);">
                <i class="fas fa-check-circle mr-2 text-success"></i>${message}
            </div>
        `;
            $('#successModal').modal('show');

            // Show additional options
            document.getElementById('linkAnotherBtn').style.display = 'inline-block';
            document.getElementById('viewLinkedProductsBtn').style.display = 'inline-block';
        }

        showError(message) {
            alert(message);
        }

        linkAnother() {
            $('#successModal').modal('hide');
            this.selectedSupplier = null;
            document.getElementById('supplierSearch').value = '';
            this.goToStep(3); // Go back to supplier search
        }

        startNew() {
            $('#successModal').modal('hide');
            this.selectedProduct = null;
            this.selectedSupplier = null;
            document.getElementById('productSearch').value = '';
            document.getElementById('supplierSearch').value = '';
            document.getElementById('addProductForm').classList.add('d-none');
            document.getElementById('addSupplierForm').classList.add('d-none');
            document.getElementById('proceedToSupplierBtn').classList.remove('d-none');
            document.getElementById('saveAndContinueBtn').classList.add('d-none');
            this.goToStep(1);
        }

        async loadCategories() {
            try {
                const response = await fetch(`${URLROOT}/api/getCategories.php`);
                const data = await response.json();

                // Check if data is an array (successful response) or has error property
                if (Array.isArray(data)) {
                    const categorySelect = document.getElementById('productCategory');
                    categorySelect.innerHTML = '<option value="">Select Category</option>' +
                        data.map(cat => `<option value="${cat.category_id}">${cat.category_name}</option>`).join('');
                } else if (data.error) {
                    console.error('Error loading categories:', data.error);
                }
            } catch (error) {
                console.error('Error loading categories:', error);
            }
        }
    }

    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        window.workflowManager = new WorkflowManager();
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>