<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php'; ?>

<div class="container-fluid theme-container theme-unified mt-0 pt-3">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-6 mb-2 mb-md-0">
            <a href="<?php echo URLROOT; ?>/receiving/pending" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Back to Receiving
            </a>
        </div>
        <div class="col-12 col-md-6 text-md-right">
            <h2 class="mb-0"><i class="fa-solid fa-print"></i> <?php echo $data['title']; ?></h2>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php flash('purchase_message'); ?>

    <!-- Purchase Info -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-info-circle"></i> Purchase Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Purchase ID:</strong><br>
                            <span class="text-primary">#<?php echo $data['purchase']->purchase_id; ?></span>
                        </div>
                        <div class="col-md-3">
                            <strong>Supplier:</strong><br>
                            <?php echo htmlspecialchars($data['purchase']->supplier_name ?? 'Unknown'); ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Date:</strong><br>
                            <?php echo date('M j, Y', strtotime($data['purchase']->purchase_date)); ?>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Items:</strong><br>
                            <span class="badge badge-info"><?php echo count($data['barcodes']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Options -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-print"></i> Print Options</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="labels-per-item">Labels per Item:</label>
                                <select id="labels-per-item" class="form-control">
                                    <option value="1">1 label per item</option>
                                    <option value="2">2 labels per item</option>
                                    <option value="5">5 labels per item</option>
                                    <option value="10">10 labels per item</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="label-size">Label Size:</label>
                                <select id="label-size" class="form-control">
                                    <option value="small">Small (2" x 1")</option>
                                    <option value="medium" selected>Medium (3" x 1.5")</option>
                                    <option value="large">Large (4" x 2")</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button class="btn btn-primary btn-lg mr-2" onclick="printAllBarcodes()">
                                <i class="fa-solid fa-print"></i> Print All Barcodes
                            </button>
                            <button class="btn btn-outline-primary mr-2" onclick="printSelectedBarcodes()">
                                <i class="fa-solid fa-check-square"></i> Print Selected
                            </button>
                            <button class="btn btn-outline-secondary mr-2" onclick="generateMissingBarcodes()">
                                <i class="fa-solid fa-barcode"></i> Generate Missing Barcodes
                            </button>
                            <button class="btn btn-outline-info" onclick="previewPrint()">
                                <i class="fa-solid fa-eye"></i> Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barcode List -->
    <div class="row">
        <div class="col-12">
            <div class="theme-card">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-barcode"></i> Items to Print</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($data['barcodes'])): ?>
                        <div class="text-center py-4">
                            <i class="fa-solid fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <h5>No Items with Barcodes Found</h5>
                            <p class="text-muted">Generate barcodes for received items to enable printing.</p>
                            <button class="btn btn-primary" onclick="generateMissingBarcodes()">
                                <i class="fa-solid fa-barcode"></i> Generate Barcodes Now
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">
                                            <input type="checkbox" id="select-all" onclick="toggleAllSelection()">
                                        </th>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Barcode</th>
                                        <th>Location</th>
                                        <th>Qty Received</th>
                                        <th>Preview</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['barcodes'] as $index => $item): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="barcode-select" 
                                                       data-index="<?php echo $index; ?>" checked>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($item['product_name']); ?></strong>
                                                <br><small class="text-muted">ID: <?php echo $item['product_id']; ?></small>
                                            </td>
                                            <td>
                                                <code><?php echo htmlspecialchars($item['sku']); ?></code>
                                            </td>
                                            <td>
                                                <code class="text-primary"><?php echo htmlspecialchars($item['barcode_value']); ?></code>
                                                <br><small class="text-muted"><?php echo $item['barcode_type']; ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary"><?php echo htmlspecialchars($item['location']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge badge-success"><?php echo $item['quantity_received']; ?></span>
                                            </td>
                                            <td>
                                                <img src="<?php echo URLROOT; ?>/purchases/generate_barcode_image" 
                                                     class="barcode-preview" 
                                                     data-barcode="<?php echo htmlspecialchars($item['barcode_value']); ?>"
                                                     data-type="<?php echo $item['barcode_type']; ?>"
                                                     style="height: 30px; cursor: pointer;"
                                                     onclick="showBarcodePreview('<?php echo htmlspecialchars($item['barcode_value']); ?>', '<?php echo $item['barcode_type']; ?>')"
                                                     title="Click to view larger">
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" 
                                                        onclick="printSingleBarcode(<?php echo $index; ?>)">
                                                    <i class="fa-solid fa-print"></i>
                                                </button>
                                                <button class="btn btn-outline-secondary btn-sm" 
                                                        onclick="editLabelsCount(<?php echo $index; ?>)">
                                                    <i class="fa-solid fa-edit"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
                <img id="modal-barcode-image" src="" alt="Barcode" class="img-fluid mb-3">
                <div id="modal-barcode-text" class="h5"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="printModalBarcode()">
                    <i class="fa-solid fa-print"></i> Print This Barcode
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Print Preview Modal -->
<div class="modal fade" id="printPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-eye"></i> Print Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="print-preview-content" class="print-layout">
                    <!-- Print preview will be generated here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="proceedToPrint()">
                    <i class="fa-solid fa-print"></i> Print
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.print-layout {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    padding: 20px;
}

.barcode-label {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: center;
    background: white;
    border-radius: 4px;
}

.barcode-label.small {
    width: 2in;
    height: 1in;
}

.barcode-label.medium {
    width: 3in;
    height: 1.5in;
}

.barcode-label.large {
    width: 4in;
    height: 2in;
}

@media print {
    .container-fluid, .theme-container, .row {
        all: unset !important;
    }
    
    .print-layout {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 5px;
        margin: 0;
        padding: 5px;
    }
    
    .barcode-label {
        break-inside: avoid;
        page-break-inside: avoid;
    }
}
</style>

<script>
const barcodeData = <?php echo json_encode($data['barcodes']); ?>;

// Load barcode images
document.addEventListener('DOMContentLoaded', function() {
    loadBarcodeImages();
});

function loadBarcodeImages() {
    const previews = document.querySelectorAll('.barcode-preview');
    previews.forEach(function(img) {
        const barcode = img.dataset.barcode;
        const type = img.dataset.type;
        
        if (barcode) {
            const formData = new FormData();
            formData.append('barcode', barcode);
            formData.append('type', type);
            
            fetch('<?php echo URLROOT; ?>/purchases/generate_barcode_image', {
                method: 'POST',
                body: formData
            })
            .then(response => response.blob())
            .then(blob => {
                const url = URL.createObjectURL(blob);
                img.src = url;
            })
            .catch(error => {
                console.error('Error loading barcode:', error);
                img.alt = 'Error loading barcode';
            });
        }
    });
}

function toggleAllSelection() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.barcode-select');
    
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = selectAll.checked;
    });
}

function showBarcodePreview(barcode, type) {
    const modal = new bootstrap.Modal(document.getElementById('barcodePreviewModal'));
    const modalImage = document.getElementById('modal-barcode-image');
    const modalText = document.getElementById('modal-barcode-text');
    
    modalText.textContent = barcode;
    
    // Load barcode image
    const formData = new FormData();
    formData.append('barcode', barcode);
    formData.append('type', type);
    
    fetch('<?php echo URLROOT; ?>/purchases/generate_barcode_image', {
        method: 'POST',
        body: formData
    })
    .then(response => response.blob())
    .then(blob => {
        const url = URL.createObjectURL(blob);
        modalImage.src = url;
    });
    
    modal.show();
}

function generateMissingBarcodes() {
    const purchaseId = <?php echo $data['purchase']->purchase_id; ?>;
    
    if (confirm('Generate barcodes for all products without barcodes?')) {
        fetch('<?php echo URLROOT; ?>/purchases/generate_missing_barcodes/' + purchaseId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error generating barcodes');
        });
    }
}

function previewPrint() {
    const selectedItems = getSelectedItems();
    if (selectedItems.length === 0) {
        alert('Please select at least one item to preview');
        return;
    }
    
    generatePrintPreview(selectedItems);
    const modal = new bootstrap.Modal(document.getElementById('printPreviewModal'));
    modal.show();
}

function generatePrintPreview(items) {
    const labelsPerItem = parseInt(document.getElementById('labels-per-item').value);
    const labelSize = document.getElementById('label-size').value;
    const previewContent = document.getElementById('print-preview-content');
    
    let html = '';
    
    items.forEach(function(item) {
        for (let i = 0; i < labelsPerItem; i++) {
            html += `
                <div class="barcode-label ${labelSize}">
                    <div style="font-size: 0.8em; font-weight: bold; margin-bottom: 5px;">
                        ${item.product_name.substring(0, 20)}
                    </div>
                    <img src="data:image/png;base64,${item.barcode_image}" style="max-width: 100%; height: auto;">
                    <div style="font-size: 0.7em; margin-top: 2px;">
                        ${item.barcode_value}
                    </div>
                    <div style="font-size: 0.6em; color: #666;">
                        SKU: ${item.sku} | Loc: ${item.location}
                    </div>
                </div>
            `;
        }
    });
    
    previewContent.innerHTML = html;
}

function getSelectedItems() {
    const selected = [];
    const checkboxes = document.querySelectorAll('.barcode-select:checked');
    
    checkboxes.forEach(function(checkbox) {
        const index = parseInt(checkbox.dataset.index);
        selected.push(barcodeData[index]);
    });
    
    return selected;
}

function printAllBarcodes() {
    const checkboxes = document.querySelectorAll('.barcode-select');
    checkboxes.forEach(checkbox => checkbox.checked = true);
    printSelectedBarcodes();
}

function printSelectedBarcodes() {
    const selectedItems = getSelectedItems();
    if (selectedItems.length === 0) {
        alert('Please select at least one item to print');
        return;
    }
    
    // Generate print content and print
    generatePrintContent(selectedItems);
}

function printSingleBarcode(index) {
    const item = barcodeData[index];
    generatePrintContent([item]);
}

function generatePrintContent(items) {
    const labelsPerItem = parseInt(document.getElementById('labels-per-item').value);
    const labelSize = document.getElementById('label-size').value;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Barcodes</title>
            <style>
                body { margin: 0; padding: 10px; font-family: Arial, sans-serif; }
                .print-layout {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 5px;
                }
                .barcode-label {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: center;
                    background: white;
                    break-inside: avoid;
                    page-break-inside: avoid;
                }
                .barcode-label.small { width: 2in; height: 1in; font-size: 8px; }
                .barcode-label.medium { width: 3in; height: 1.5in; font-size: 10px; }
                .barcode-label.large { width: 4in; height: 2in; font-size: 12px; }
                img { max-width: 100%; height: auto; }
            </style>
        </head>
        <body>
            <div class="print-layout">
    `);
    
    items.forEach(function(item) {
        for (let i = 0; i < labelsPerItem; i++) {
            printWindow.document.write(`
                <div class="barcode-label ${labelSize}">
                    <div style="font-weight: bold; margin-bottom: 3px;">
                        ${item.product_name.substring(0, 25)}
                    </div>
                    <div style="margin: 3px 0;">
                        <canvas id="barcode_${item.product_id}_${i}" width="150" height="40"></canvas>
                    </div>
                    <div style="margin-top: 2px; font-size: 0.9em;">
                        ${item.barcode_value}
                    </div>
                    <div style="font-size: 0.8em; color: #666; margin-top: 1px;">
                        SKU: ${item.sku} | ${item.location}
                    </div>
                </div>
            `);
        }
    });
    
    printWindow.document.write(`
            </div>
            <script>
                window.onload = function() {
                    setTimeout(function() {
                        window.print();
                        window.close();
                    }, 500);
                }
            </script>
        </body>
        </html>
    `);
    
    printWindow.document.close();
}

function proceedToPrint() {
    const selectedItems = getSelectedItems();
    generatePrintContent(selectedItems);
    
    const modal = bootstrap.Modal.getInstance(document.getElementById('printPreviewModal'));
    modal.hide();
}

function editLabelsCount(index) {
    const newCount = prompt('Enter number of labels to print for this item:', '1');
    if (newCount && !isNaN(newCount) && newCount > 0) {
        // Update the count for this specific item
        // This could be stored in a data attribute or array
        console.log('Setting labels count for item', index, 'to', newCount);
    }
}
</script>

<?php require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php'; ?>
