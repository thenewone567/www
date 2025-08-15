<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fa-solid fa-barcode text-primary mr-2"></i>
                    Barcode Print Manager
                </h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo URLROOT; ?>/barcodes">Barcodes</a></li>
                        <li class="breadcrumb-item active">Print</li>
                    </ol>
                </nav>
            </div>

            <!-- Entity Type Selection -->
            <div class="kpi-card mb-4">
                <h5 class="mb-3">
                    <i class="fa-solid fa-list-check mr-2"></i>
                    Select What to Print
                </h5>

                <div class="row">
                    <div class="col-md-2">
                        <div class="entity-type-card" data-type="product">
                            <i class="fa-solid fa-box"></i>
                            <h6>Products</h6>
                            <p>Product barcodes for inventory</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="entity-type-card" data-type="supplier">
                            <i class="fa-solid fa-truck"></i>
                            <h6>Suppliers</h6>
                            <p>Supplier identification codes</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="entity-type-card" data-type="customer">
                            <i class="fa-solid fa-users"></i>
                            <h6>Customers</h6>
                            <p>Customer membership codes</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="entity-type-card" data-type="location">
                            <i class="fa-solid fa-map-marker-alt"></i>
                            <h6>Locations</h6>
                            <p>Storage location labels</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="entity-type-card" data-type="invoice">
                            <i class="fa-solid fa-file-invoice"></i>
                            <h6>Invoices</h6>
                            <p>Invoice tracking codes</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="entity-type-card" data-type="custom">
                            <i class="fa-solid fa-plus"></i>
                            <h6>Custom</h6>
                            <p>Custom barcode generation</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selection Area -->
            <div class="kpi-card mb-4" id="selectionArea" style="display: none;">
                <h5 class="mb-3">
                    <i class="fa-solid fa-check-square mr-2"></i>
                    Select Items to Print
                </h5>

                <!-- Search and Filter -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-search"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="entitySearch" placeholder="Search items...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" id="categoryFilter" style="display: none;">
                            <option value="">All Categories</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-right">
                        <button class="btn btn-outline-primary" id="selectAllBtn">
                            <i class="fa-solid fa-check-double mr-1"></i>Select All
                        </button>
                        <button class="btn btn-outline-secondary" id="clearSelectionBtn">
                            <i class="fa-solid fa-times mr-1"></i>Clear
                        </button>
                    </div>
                </div>

                <!-- Items List -->
                <div id="itemsList" class="row">
                    <!-- Dynamic content will be loaded here -->
                </div>
            </div>

            <!-- Print Options -->
            <div class="kpi-card mb-4" id="printOptions" style="display: none;">
                <h5 class="mb-3">
                    <i class="fa-solid fa-cog mr-2"></i>
                    Print Options
                </h5>

                <div class="row">
                    <div class="col-md-3">
                        <label for="barcodeType">Barcode Type</label>
                        <select class="form-control" id="barcodeType">
                            <option value="CODE128">CODE 128</option>
                            <option value="CODE39">CODE 39</option>
                            <option value="EAN13">EAN-13</option>
                            <option value="UPC">UPC</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="labelSize">Label Size</label>
                        <select class="form-control" id="labelSize">
                            <option value="small">Small (30x20mm)</option>
                            <option value="medium" selected>Medium (50x30mm)</option>
                            <option value="large">Large (70x40mm)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="copiesPerItem">Copies per Item</label>
                        <input type="number" class="form-control" id="copiesPerItem" value="1" min="1" max="100">
                    </div>
                    <div class="col-md-3">
                        <label for="showText">Include Text</label>
                        <select class="form-control" id="showText">
                            <option value="yes" selected>Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includeName" checked>
                            <label class="form-check-label" for="includeName">
                                Include item name on label
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="includePrice">
                            <label class="form-check-label" for="includePrice">
                                Include price (products only)
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Selected Items Summary -->
            <div class="kpi-card mb-4" id="selectedSummary" style="display: none;">
                <h5 class="mb-3">
                    <i class="fa-solid fa-clipboard-list mr-2"></i>
                    Print Summary
                </h5>

                <div class="row">
                    <div class="col-md-8">
                        <div id="selectedItemsList">
                            <!-- Selected items will appear here -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-right">
                            <div class="mb-2">
                                <strong>Total Items: <span id="totalItems">0</span></strong>
                            </div>
                            <div class="mb-2">
                                <strong>Total Labels: <span id="totalLabels">0</span></strong>
                            </div>
                            <hr>
                            <button class="btn btn-success btn-lg" id="printBtn">
                                <i class="fa-solid fa-print mr-2"></i>
                                Print Barcodes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .entity-type-card {
        background: var(--card-bg);
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .entity-type-card:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .entity-type-card.selected {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .entity-type-card i {
        font-size: 2rem;
        color: var(--primary);
        margin-bottom: 8px;
    }

    .entity-type-card h6 {
        margin-bottom: 5px;
        font-weight: 600;
    }

    .entity-type-card p {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-bottom: 0;
    }

    .item-card {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .item-card:hover {
        border-color: var(--primary);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .item-card.selected {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .item-card .form-check-input {
        margin-top: 0;
    }
</style>

<script>
    class BarcodePrintManager {
        constructor() {
            this.selectedEntityType = null;
            this.selectedItems = new Set();
            this.entityData = {};
            this.initializeEventListeners();
        }

        initializeEventListeners() {
            // Entity type selection
            document.querySelectorAll('.entity-type-card').forEach(card => {
                card.addEventListener('click', (e) => this.selectEntityType(e.target.closest('.entity-type-card')));
            });

            // Search functionality
            document.getElementById('entitySearch').addEventListener('input', (e) => this.filterItems(e.target.value));

            // Bulk selection
            document.getElementById('selectAllBtn').addEventListener('click', () => this.selectAll());
            document.getElementById('clearSelectionBtn').addEventListener('click', () => this.clearSelection());

            // Print options change
            document.getElementById('copiesPerItem').addEventListener('change', () => this.updateSummary());

            // Print button
            document.getElementById('printBtn').addEventListener('click', () => this.printBarcodes());
        }

        async selectEntityType(card) {
            // Clear previous selection
            document.querySelectorAll('.entity-type-card').forEach(c => c.classList.remove('selected'));
            card.classList.add('selected');

            this.selectedEntityType = card.dataset.type;
            this.selectedItems.clear();

            // Show selection area
            document.getElementById('selectionArea').style.display = 'block';

            // Load entity data
            await this.loadEntityData();

            // Show category filter for products
            if (this.selectedEntityType === 'product') {
                document.getElementById('categoryFilter').style.display = 'block';
                await this.loadCategories();
            } else {
                document.getElementById('categoryFilter').style.display = 'none';
            }

            this.displayItems();
        }

        async loadEntityData() {
            try {
                const response = await fetch(`${window.URLROOT}/api/get${this.selectedEntityType.charAt(0).toUpperCase() + this.selectedEntityType.slice(1)}s.php`);
                const data = await response.json();
                this.entityData[this.selectedEntityType] = data.success ? data.data || data.products || data.suppliers : [];
            } catch (error) {
                console.error('Error loading entity data:', error);
                this.entityData[this.selectedEntityType] = [];
            }
        }

        displayItems() {
            const itemsList = document.getElementById('itemsList');
            const items = this.entityData[this.selectedEntityType] || [];

            if (items.length === 0) {
                itemsList.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-4">
                        <i class="fa-solid fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No ${this.selectedEntityType}s found</h5>
                    </div>
                </div>
            `;
                return;
            }

            itemsList.innerHTML = items.map(item => this.createItemCard(item)).join('');

            // Add click handlers
            itemsList.querySelectorAll('.item-card').forEach(card => {
                card.addEventListener('click', (e) => this.toggleItemSelection(e.target.closest('.item-card')));
            });
        }

        createItemCard(item) {
            const itemId = this.getItemId(item);
            const itemName = this.getItemName(item);
            const itemDetails = this.getItemDetails(item);

            return `
            <div class="col-md-4">
                <div class="item-card" data-item-id="${itemId}">
                    <div class="d-flex align-items-start">
                        <div class="form-check mr-3">
                            <input class="form-check-input" type="checkbox" id="item_${itemId}">
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${itemName}</h6>
                            <small class="text-muted">${itemDetails}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
        }

        getItemId(item) {
            switch (this.selectedEntityType) {
                case 'product': return item.product_id;
                case 'supplier': return item.supplier_id;
                case 'customer': return item.customer_id;
                case 'location': return item.location_id;
                case 'invoice': return item.invoice_id;
                default: return item.id;
            }
        }

        getItemName(item) {
            switch (this.selectedEntityType) {
                case 'product': return item.product_name;
                case 'supplier': return item.supplier_name || item.company_name;
                case 'customer': return item.customer_name;
                case 'location': return item.location_name;
                case 'invoice': return `Invoice #${item.invoice_number}`;
                default: return item.name;
            }
        }

        getItemDetails(item) {
            switch (this.selectedEntityType) {
                case 'product': return `SKU: ${item.sku || 'N/A'}`;
                case 'supplier': return `Contact: ${item.contact_person || 'N/A'}`;
                case 'customer': return `Email: ${item.email || 'N/A'}`;
                case 'location': return `Zone: ${item.zone || 'N/A'}`;
                case 'invoice': return `Date: ${item.created_at || 'N/A'}`;
                default: return '';
            }
        }

        toggleItemSelection(card) {
            const itemId = card.dataset.itemId;
            const checkbox = card.querySelector('input[type="checkbox"]');

            if (this.selectedItems.has(itemId)) {
                this.selectedItems.delete(itemId);
                card.classList.remove('selected');
                checkbox.checked = false;
            } else {
                this.selectedItems.add(itemId);
                card.classList.add('selected');
                checkbox.checked = true;
            }

            this.updateUI();
        }

        selectAll() {
            const items = this.entityData[this.selectedEntityType] || [];
            items.forEach(item => {
                const itemId = this.getItemId(item);
                this.selectedItems.add(itemId);
            });

            document.querySelectorAll('.item-card').forEach(card => {
                card.classList.add('selected');
                card.querySelector('input[type="checkbox"]').checked = true;
            });

            this.updateUI();
        }

        clearSelection() {
            this.selectedItems.clear();
            document.querySelectorAll('.item-card').forEach(card => {
                card.classList.remove('selected');
                card.querySelector('input[type="checkbox"]').checked = false;
            });

            this.updateUI();
        }

        updateUI() {
            if (this.selectedItems.size > 0) {
                document.getElementById('printOptions').style.display = 'block';
                document.getElementById('selectedSummary').style.display = 'block';
                this.updateSummary();
            } else {
                document.getElementById('printOptions').style.display = 'none';
                document.getElementById('selectedSummary').style.display = 'none';
            }
        }

        updateSummary() {
            const totalItems = this.selectedItems.size;
            const copiesPerItem = parseInt(document.getElementById('copiesPerItem').value) || 1;
            const totalLabels = totalItems * copiesPerItem;

            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('totalLabels').textContent = totalLabels;

            // Update selected items list
            const selectedItemsList = document.getElementById('selectedItemsList');
            const items = this.entityData[this.selectedEntityType] || [];
            const selectedItemsData = items.filter(item => this.selectedItems.has(this.getItemId(item).toString()));

            selectedItemsList.innerHTML = selectedItemsData.map(item =>
                `<div class="mb-1">
                <small class="text-muted">${this.getItemName(item)} (${copiesPerItem} ${copiesPerItem === 1 ? 'copy' : 'copies'})</small>
            </div>`
            ).join('');
        }

        async printBarcodes() {
            if (this.selectedItems.size === 0) {
                alert('Please select items to print');
                return;
            }

            const printData = {
                entity_type: this.selectedEntityType,
                entity_ids: Array.from(this.selectedItems),
                barcode_type: document.getElementById('barcodeType').value,
                label_size: document.getElementById('labelSize').value,
                copies_per_item: parseInt(document.getElementById('copiesPerItem').value),
                include_text: document.getElementById('showText').value === 'yes',
                include_name: document.getElementById('includeName').checked,
                include_price: document.getElementById('includePrice').checked
            };

            // Open print page in new window
            const printUrl = `${window.URLROOT}/barcodes/print?${new URLSearchParams(printData).toString()}`;
            window.open(printUrl, '_blank', 'width=800,height=600');
        }
    }

    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', () => {
        new BarcodePrintManager();
    });
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>