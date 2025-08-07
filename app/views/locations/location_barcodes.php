<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified mt-0 pt-3">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/locations" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Inventory Management
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0"><i class="fa-solid fa-qrcode"></i> <?php echo $data['title']; ?></h2>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('Inventory_message'); ?>

    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-tools"></i> Location Barcode Management</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-success btn-lg w-100 mb-2" onclick="bulkGenerateBarcodes()">
                                <i class="fa-solid fa-magic"></i> Generate Missing Barcodes
                            </button>
                        </div>
                        <div class="col-md-6">
                            <a href="<?php echo URLROOT; ?>/locations/print_location_barcodes"
                                class="btn btn-warning btn-lg w-100 mb-2">
                                <i class="fa-solid fa-print"></i> Print All Barcodes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barcode Scanner for Testing -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-info-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-barcode"></i> Location Barcode Scanner</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                                <input type="text" id="barcode-input" class="form-control"
                                    placeholder="Scan location barcode here..." autofocus>
                                <button class="btn btn-info" type="button" onclick="scanLocationBarcode()">
                                    <i class="fa-solid fa-search"></i> Scan
                                </button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div id="scan-result" class="alert alert-info" style="display: none;">
                                <strong>Scanned Location:</strong>
                                <div id="location-info"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Locations without Barcodes -->
    <?php
    $locationsWithoutBarcodes = [];
    foreach ($data['locations'] as $location) {
        $hasBarcode = false;
        foreach ($data['location_barcodes'] as $barcode) {
            if ($barcode->location_id == $location->location_id) {
                $hasBarcode = true;
                break;
            }
        }
        if (!$hasBarcode) {
            $locationsWithoutBarcodes[] = $location;
        }
    }
    ?>

    <?php if (!empty($locationsWithoutBarcodes)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="theme-card">
                    <div class="card-header bg-warning-theme text-white">
                        <h5 class="mb-0"><i class="fa-solid fa-exclamation-triangle"></i> Locations Without Barcodes</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Location ID</th>
                                        <th>Location Name</th>
                                        <th>Rack</th>
                                        <th>Shelf</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($locationsWithoutBarcodes as $location): ?>
                                        <tr>
                                            <td><?php echo $location->location_id; ?></td>
                                            <td><strong><?php echo htmlspecialchars($location->location_name); ?></strong></td>
                                            <td><?php echo htmlspecialchars($location->rack ?? 'N/A'); ?></td>
                                            <td><?php echo htmlspecialchars($location->shelf ?? 'N/A'); ?></td>
                                            <td>
                                                <button class="btn btn-primary btn-sm"
                                                    onclick="generateLocationBarcode(<?php echo $location->location_id; ?>)">
                                                    <i class="fa-solid fa-barcode"></i> Generate Barcode
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Existing Location Barcodes -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-check-circle"></i> Locations with Barcodes</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($data['location_barcodes'])): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Rack/Shelf</th>
                                        <th>Barcode</th>
                                        <th>Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['location_barcodes'] as $barcode): ?>
                                        <tr>
                                            <td><strong><?php echo htmlspecialchars($barcode->location_name); ?></strong></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?php echo htmlspecialchars($barcode->rack); ?> -
                                                    <?php echo htmlspecialchars($barcode->shelf); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <code class="theme-code-block p-1"><?php echo $barcode->barcode_value; ?></code>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary"><?php echo $barcode->type; ?></span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-info btn-sm"
                                                        onclick="showBarcodePreview('<?php echo $barcode->barcode_value; ?>', '<?php echo $barcode->type; ?>')">
                                                        <i class="fa-solid fa-eye"></i> Preview
                                                    </button>
                                                    <a href="<?php echo URLROOT; ?>/locations/print_location_barcodes/<?php echo $barcode->location_id; ?>"
                                                        class="btn btn-warning btn-sm">
                                                        <i class="fa-solid fa-print"></i> Print
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fa-solid fa-barcode fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No Location Barcodes Found</h4>
                            <p class="text-muted">Generate barcodes for your warehouse locations to enable barcode scanning.
                            </p>
                            <button class="btn btn-primary btn-lg" onclick="bulkGenerateBarcodes()">
                                <i class="fa-solid fa-magic"></i> Generate All Barcodes
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Barcode Preview Modal -->
<div class="modal fade" id="barcodePreviewModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-barcode"></i> Barcode Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="barcode-preview"></div>
                <p class="mt-2"><strong>Barcode Value:</strong> <span id="barcode-value"></span></p>
            </div>
        </div>
    </div>
</div>

<script>
    // Barcode scanning for location
    document.getElementById('barcode-input').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            scanLocationBarcode();
        }
    });

    function scanLocationBarcode() {
        const barcode = document.getElementById('barcode-input').value.trim();
        if (!barcode) {
            alert('Please enter a barcode');
            return;
        }

        fetch('<?php echo URLROOT; ?>/locations/scan_location_barcode', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'barcode=' + encodeURIComponent(barcode)
        })
            .then(response => response.json())
            .then(data => {
                const resultDiv = document.getElementById('scan-result');
                const infoDiv = document.getElementById('location-info');

                if (data.success) {
                    infoDiv.innerHTML = `
                <strong>${data.location.name}</strong><br>
                Rack: ${data.location.rack} | Shelf: ${data.location.shelf}<br>
                Location ID: ${data.location.id}
            `;
                    resultDiv.className = 'alert alert-success';
                    resultDiv.style.display = 'block';
                    document.getElementById('barcode-input').value = '';
                } else {
                    infoDiv.innerHTML = data.message;
                    resultDiv.className = 'alert alert-danger';
                    resultDiv.style.display = 'block';
                }

                // Hide result after 5 seconds
                setTimeout(() => {
                    resultDiv.style.display = 'none';
                }, 5000);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error scanning barcode');
            });
    }

    function generateLocationBarcode(locationId) {
        fetch('<?php echo URLROOT; ?>/locations/generate_location_barcode/' + locationId, {
            method: 'GET'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Barcode generated: ' + data.barcode);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating barcode');
            });
    }

    function bulkGenerateBarcodes() {
        if (!confirm('Generate barcodes for all locations without barcodes?')) {
            return;
        }

        fetch('<?php echo URLROOT; ?>/locations/bulk_generate_location_barcodes', {
            method: 'GET'
        })
            .then(response => response.json())
            .then(data => {
                alert(data.message);
                if (data.generated > 0) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating barcodes');
            });
    }

    function showBarcodePreview(barcodeValue, type) {
        document.getElementById('barcode-value').textContent = barcodeValue;

        // Generate barcode image
        const previewDiv = document.getElementById('barcode-preview');
        previewDiv.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating preview...';

        // Create form data for barcode generation
        const formData = new FormData();
        formData.append('barcode', barcodeValue);
        formData.append('type', type);

        fetch('<?php echo URLROOT; ?>/purchases/generate_barcode_image', {
            method: 'POST',
            body: formData
        })
            .then(response => response.blob())
            .then(blob => {
                const imageUrl = URL.createObjectURL(blob);
                previewDiv.innerHTML = `<img src="${imageUrl}" alt="Barcode" class="img-fluid">`;
            })
            .catch(error => {
                console.error('Error:', error);
                previewDiv.innerHTML = '<div class="alert alert-danger">Error generating barcode preview</div>';
            });

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('barcodePreviewModal'));
        modal.show();
    }
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>