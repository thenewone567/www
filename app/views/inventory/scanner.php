<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid mt-0 pt-3">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fa-solid fa-barcode"></i> Barcode Scanner</h4>
                        <a href="<?php echo URLROOT; ?>/inventory" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left"></i> Back to Inventory
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barcode-input">Barcode:</label>
                                <div class="input-group">
                                    <input type="text" id="barcode-input" class="form-control form-control-lg"
                                        placeholder="Scan product or location barcode" autofocus>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" onclick="searchBarcode()">
                                            <i class="fa-solid fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fa-solid fa-info-circle"></i>
                                <strong>Instructions:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>Use a barcode scanner or type the barcode manually</li>
                                    <li>Supports both product and location barcodes</li>
                                    <li>Press Enter or click Search to find the item</li>
                                    <li>Results will appear on the right</li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <!-- Product Result -->
                            <div id="product-result" class="d-none">
                                <h5><i class="fa-solid fa-box"></i> Product Information</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title" id="product-name"></h6>
                                        <p class="card-text">
                                            <strong>Product Code:</strong> <span id="product-code"></span><br>
                                            <strong>Category:</strong> <span id="product-category"></span><br>
                                            <strong>Brand:</strong> <span id="product-brand"></span><br>
                                            <strong>Unit Price:</strong> $<span id="product-price"></span><br>
                                            <strong>Current Inventory:</strong> <span id="product-Inventory"></span>
                                        </p>
                                        <div class="btn-group">
                                            <a href="#" id="edit-product-link" class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-edit"></i> Edit Product
                                            </a>
                                            <a href="#" id="Inventory-movement-link"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="fa-solid fa-exchange-alt"></i> Inventory Movement
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Result -->
                            <div id="location-result" class="d-none">
                                <h5><i class="fa-solid fa-map-marker-alt"></i> Location Information</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title" id="location-name"></h6>
                                        <p class="card-text">
                                            <strong>Location ID:</strong> <span id="location-id"></span><br>
                                            <strong>Rack:</strong> <span id="location-rack"></span><br>
                                            <strong>Shelf:</strong> <span id="location-shelf"></span><br>
                                            <strong>Barcode:</strong> <span id="location-barcode"></span>
                                        </p>
                                        <div class="btn-group">
                                            <a href="<?php echo URLROOT; ?>/locations/location_barcodes"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="fa-solid fa-qrcode"></i> Manage Barcodes
                                            </a>
                                            <a href="<?php echo URLROOT; ?>/Inventory"
                                                class="btn btn-outline-success btn-sm">
                                                <i class="fa-solid fa-warehouse"></i> View Inventory
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="no-result" class="alert alert-warning d-none">
                                <i class="fa-solid fa-exclamation-triangle"></i>
                                No product or location found with this barcode.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const barcodeInput = document.getElementById('barcode-input');

        // Auto-submit on Enter key
        barcodeInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                searchBarcode();
            }
        });

        // Auto-submit when barcode scanner finishes (typical scanner behavior)
        let timeout;
        barcodeInput.addEventListener('input', function (e) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                if (this.value.length >= 8) { // Typical barcode length
                    searchBarcode();
                }
            }, 500);
        });
    });

    function searchBarcode() {
        const barcode = document.getElementById('barcode-input').value.trim();
        if (!barcode) {
            alert('Please enter a barcode');
            return;
        }

        // Hide all result containers
        document.getElementById('product-result').classList.add('d-none');
        document.getElementById('location-result').classList.add('d-none');
        document.getElementById('no-result').classList.add('d-none');

        // First try to find product
        fetch('<?php echo URLROOT; ?>/inventory/scanBarcode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'barcode=' + encodeURIComponent(barcode)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Display product information
                    displayProduct(data.product);
                } else {
                    // Try to find location if product not found
                    searchLocation(barcode);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Try location search as fallback
                searchLocation(barcode);
            });
    }

    function searchLocation(barcode) {
        fetch('<?php echo URLROOT; ?>/Inventory/scan_location_barcode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'barcode=' + encodeURIComponent(barcode)
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Display location information
                    displayLocation(data.location);
                } else {
                    // Show no result message
                    document.getElementById('no-result').classList.remove('d-none');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('no-result').classList.remove('d-none');
            });
    }

    function displayProduct(product) {
        document.getElementById('product-name').textContent = product.product_name || 'N/A';
        document.getElementById('product-code').textContent = product.product_code || 'N/A';
        document.getElementById('product-category').textContent = product.category_name || 'N/A';
        document.getElementById('product-brand').textContent = product.brand_name || 'N/A';
        document.getElementById('product-price').textContent = product.unit_price || '0.00';
        document.getElementById('product-Inventory').textContent = product.quantity || '0';

        // Update links
        document.getElementById('edit-product-link').href = '<?php echo URLROOT; ?>/products/edit/' + product.product_id;
        document.getElementById('Inventory-movement-link').href = '<?php echo URLROOT; ?>/inventory/movement/' + product.product_id;

        // Show result
        document.getElementById('product-result').classList.remove('d-none');
    }

    function displayLocation(location) {
        document.getElementById('location-name').textContent = location.name || 'N/A';
        document.getElementById('location-id').textContent = location.id || 'N/A';
        document.getElementById('location-rack').textContent = location.rack || 'N/A';
        document.getElementById('location-shelf').textContent = location.shelf || 'N/A';
        document.getElementById('location-barcode').textContent = location.barcode || 'N/A';

        // Show result
        document.getElementById('location-result').classList.remove('d-none');
    }
</script>


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