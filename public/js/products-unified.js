// Products & Inventory Unified Page JavaScript

$(document).ready(function() {
    // Initialize the page
    initializeUnifiedPage();
    
    // Add keyboard shortcuts
    addKeyboardShortcuts();
    
    // Auto-refresh every 5 minutes
    setInterval(refreshData, 300000);
});

function initializeUnifiedPage() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Load initial products view (list view by default)
    loadProductsView('list');
    
    // Set up event handlers
    setupEventHandlers();
    
    // Initialize filters
    setupFilters();
    
    // Show loading animation
    showLoading(false);
}

function addKeyboardShortcuts() {
    $(document).keydown(function(e) {
        // Ctrl+N - Add new product
        if (e.ctrlKey && e.which === 78) {
            e.preventDefault();
            window.location.href = `${window.URLROOT}/products/add`;
        }
        
        // Ctrl+F - Focus search
        if (e.ctrlKey && e.which === 70) {
            e.preventDefault();
            $('#search').focus();
        }
        
        // Ctrl+R - Refresh data
        if (e.ctrlKey && e.which === 82) {
            e.preventDefault();
            refreshData();
        }
    });
}

function showLoading(show = true) {
    if (show) {
        if ($('#loadingOverlay').length === 0) {
            $('body').append(`
                <div id="loadingOverlay" class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center" 
                     style="top: 0; left: 0; background: rgba(0,0,0,0.3); z-index: 9999;">
                    <div class="text-center text-white">
                        <i class="fas fa-spinner fa-spin fa-3x mb-3"></i>
                        <p>Loading...</p>
                    </div>
                </div>
            `);
        }
    } else {
        $('#loadingOverlay').remove();
    }
}

function refreshData() {
    showLoading(true);
    
    // Simulate refresh - in real implementation, you'd make an AJAX call
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function setupEventHandlers() {
    // View mode switching
    $('#listViewBtn').click(function() {
        $(this).addClass('btn-primary').removeClass('btn-outline-secondary');
        $('#cardViewBtn').removeClass('btn-primary').addClass('btn-outline-secondary');
        loadProductsView('list');
    });

    $('#cardViewBtn').click(function() {
        $(this).addClass('btn-primary').removeClass('btn-outline-secondary');
        $('#listViewBtn').removeClass('btn-primary').addClass('btn-outline-secondary');
        loadProductsView('card');
    });

    // Tab switching
    $('#productTabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
        
        // Initialize DataTable when switching to inventory tab
        if ($(this).attr('href') === '#inventory') {
            initializeStockTable();
        }
    });
}

function setupFilters() {
    // Search filter
    $('#search').on('keyup', function() {
        applyFilters();
    });

    // Category filter
    $('#category_filter').on('change', function() {
        applyFilters();
    });

    // Status filter
    $('#status_filter').on('change', function() {
        applyFilters();
    });
}

function loadProductsView(viewType) {
    const container = $('#productsContainer');
    
    if (viewType === 'card') {
        loadCardView(container);
    } else {
        loadListView(container);
    }
}

function loadListView(container) {
    const products = window.productsData || [];
    
    let html = `
        <div class="table-responsive">
            <table id="productsTable" class="table table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>Image</th>
                        <th>Product Name</th>
                        <th>SKU</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;

    if (products.length > 0) {
        products.forEach(product => {
            const stockStatus = getStockStatus(product);
            const statusBadge = getStatusBadge(stockStatus);
            
            html += `
                <tr>
                    <td>
                        ${product.image_path ? 
                            `<img src="${window.URLROOT}/${product.image_path}" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">` :
                            `<div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-image text-muted"></i>
                             </div>`
                        }
                    </td>
                    <td>
                        <div class="font-weight-bold">${escapeHtml(product.product_name)}</div>
                        <small class="text-muted">${escapeHtml(product.brand_name || 'No Brand')}</small>
                    </td>
                    <td><span class="font-weight-bold">${escapeHtml(product.sku || 'N/A')}</span></td>
                    <td><span class="badge badge-secondary">${escapeHtml(product.category_name || 'Uncategorized')}</span></td>
                    <td>
                        <span class="font-weight-bold">${formatNumber(product.current_stock || 0)}</span>
                        <small class="text-muted d-block">Min: ${formatNumber(product.reorder_level || 0)}</small>
                    </td>
                    <td>$${formatNumber(product.selling_price || 0, 2)}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-primary" onclick="viewProduct(${product.product_id})" data-toggle="tooltip" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="${window.URLROOT}/products/edit/${product.product_id}" class="btn btn-outline-secondary" data-toggle="tooltip" title="Edit Product">
                                <i class="fas fa-pencil-alt"></i>
                            </a>
                            <button class="btn btn-outline-info" onclick="adjustStock(${product.product_id})" data-toggle="tooltip" title="Adjust Stock">
                                <i class="fas fa-boxes"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
    } else {
        html += `
            <tr>
                <td colspan="8" class="text-center py-4">
                    <div class="text-muted">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <p>No products found</p>
                        <a href="${window.URLROOT}/products/add" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Add Your First Product
                        </a>
                    </div>
                </td>
            </tr>
        `;
    }

    html += `
                </tbody>
            </table>
        </div>
    `;

    container.html(html);
    
    // Initialize DataTable
    $('#productsTable').DataTable({
        "responsive": true,
        "pageLength": 25,
        "order": [[1, "asc"]], // Sort by product name
        "columnDefs": [
            { "orderable": false, "targets": [0, 7] } // Disable sorting for image and actions columns
        ]
    });
}

function loadCardView(container) {
    const products = window.productsData || [];
    
    let html = '<div class="row">';

    if (products.length > 0) {
        products.forEach(product => {
            const stockStatus = getStockStatus(product);
            const statusBadge = getStatusBadge(stockStatus);
            
            html += `
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-img-top d-flex align-items-center justify-content-center" style="height: 200px; background-color: #f8f9fa;">
                            ${product.image_path ? 
                                `<img src="${window.URLROOT}/${product.image_path}" class="img-fluid" style="max-height: 180px; max-width: 100%; object-fit: cover;">` :
                                `<i class="fas fa-image fa-3x text-muted"></i>`
                            }
                        </div>
                        <div class="card-body">
                            <h6 class="card-title font-weight-bold">${escapeHtml(product.product_name)}</h6>
                            <p class="card-text text-muted small">${escapeHtml(product.brand_name || 'No Brand')}</p>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge badge-secondary">${escapeHtml(product.category_name || 'Uncategorized')}</span>
                                ${statusBadge}
                            </div>
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="font-weight-bold">$${formatNumber(product.selling_price || 0, 2)}</div>
                                    <small class="text-muted">Price</small>
                                </div>
                                <div class="col-6">
                                    <div class="font-weight-bold">${formatNumber(product.current_stock || 0)}</div>
                                    <small class="text-muted">Stock</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="btn-group w-100">
                                <button class="btn btn-outline-primary btn-sm" onclick="viewProduct(${product.product_id})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="${window.URLROOT}/products/edit/${product.product_id}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <button class="btn btn-outline-info btn-sm" onclick="adjustStock(${product.product_id})">
                                    <i class="fas fa-boxes"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        html += `
            <div class="col-12 text-center py-5">
                <div class="text-muted">
                    <i class="fas fa-box-open fa-3x mb-3"></i>
                    <p>No products found</p>
                    <a href="${window.URLROOT}/products/add" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i> Add Your First Product
                    </a>
                </div>
            </div>
        `;
    }

    html += '</div>';
    container.html(html);
}

function initializeStockTable() {
    if (!$.fn.DataTable.isDataTable('#stockTable')) {
        $('#stockTable').DataTable({
            "responsive": true,
            "pageLength": 25,
            "order": [[7, "asc"]], // Sort by status
            "columnDefs": [
                { "orderable": false, "targets": [8] } // Disable sorting for actions column
            ]
        });
    }
}

function getStockStatus(product) {
    const stock = product.current_stock || 0;
    const reorder = product.reorder_level || 0;
    
    if (stock <= 0) {
        return 'out_of_stock';
    } else if (stock <= reorder) {
        return 'low_stock';
    } else {
        return 'in_stock';
    }
}

function getStatusBadge(status) {
    switch (status) {
        case 'out_of_stock':
            return '<span class="badge badge-danger">Out of Stock</span>';
        case 'low_stock':
            return '<span class="badge badge-warning">Low Stock</span>';
        case 'in_stock':
        default:
            return '<span class="badge badge-success">In Stock</span>';
    }
}

function applyFilters() {
    const searchTerm = $('#search').val().toLowerCase();
    const categoryFilter = $('#category_filter').val();
    const statusFilter = $('#status_filter').val();
    
    // Apply filters to the current view
    // This would filter the products data and reload the view
    console.log('Applying filters:', { searchTerm, categoryFilter, statusFilter });
}

function clearFilters() {
    $('#search').val('');
    $('#category_filter').val('');
    $('#status_filter').val('');
    applyFilters();
}

// Stock adjustment functions
function adjustStock(productId) {
    // Find product data
    const product = window.productsData?.find(p => p.product_id == productId);
    
    if (product) {
        $('#adjust_product_id').val(product.product_id);
        $('#adjust_product_name').val(product.product_name);
        $('#adjust_current_stock').val(product.current_stock || 0);
        $('#stockAdjustmentModal').modal('show');
    }
}

function submitStockAdjustment() {
    const formData = {
        product_id: $('#adjust_product_id').val(),
        type: $('#adjust_type').val(),
        quantity: $('#adjust_quantity').val(),
        reason: $('#adjust_reason').val()
    };

    // Here you would make an AJAX call to your backend
    console.log('Stock adjustment:', formData);
    
    // For now, just close the modal and show success
    $('#stockAdjustmentModal').modal('hide');
    
    // Show success message
    showNotification('Stock adjustment saved successfully!', 'success');
    
    // In a real implementation, you would reload the data
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Other utility functions
function viewProduct(productId) {
    window.location.href = `${window.URLROOT}/products/view/${productId}`;
}

function openStockAdjustment() {
    $('#inventory-tab').tab('show');
}

function viewStockMovements() {
    window.location.href = `${window.URLROOT}/inventory/movements`;
}

function generateLowStockReport() {
    window.open(`${window.URLROOT}/reports/low-stock`, '_blank');
}

function performStockTake() {
    window.location.href = `${window.URLROOT}/inventory/stock-take`;
}

function generateReport(type) {
    window.open(`${window.URLROOT}/reports/${type}`, '_blank');
}

function exportData(format) {
    window.location.href = `${window.URLROOT}/products/exportCSV`;
}

function formatNumber(num, decimals = 0) {
    return Number(num).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text ? text.replace(/[&<>"']/g, function(m) { return map[m]; }) : '';
}

function showNotification(message, type = 'info') {
    // Simple notification - you can replace with a more sophisticated solution
    const alertClass = `alert-${type === 'error' ? 'danger' : type}`;
    const alert = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999;">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);
    
    $('body').append(alert);
    
    // Auto-dismiss after 3 seconds
    setTimeout(() => {
        alert.alert('close');
    }, 3000);
}

// CSV Import Functions
function showImportModal() {
    $('#csvImportModal').modal('show');
    resetImportForm();
}

function resetImportForm() {
    $('#csvImportForm')[0].reset();
    $('#importProgress').hide();
    $('#importResults').hide();
    $('#importBtn').prop('disabled', false).html('<i class="fas fa-upload mr-1"></i>Start Import');
}

function downloadSampleCSV() {
    window.location.href = `${window.URLROOT}/products/downloadSampleCSV`;
}

function startImport() {
    const fileInput = document.getElementById('csvFile');
    if (!fileInput.files[0]) {
        showNotification('Please select a CSV file to import', 'danger');
        return;
    }

    const formData = new FormData();
    formData.append('csvFile', fileInput.files[0]);
    formData.append('update_existing', document.getElementById('updateExisting').checked ? 'on' : 'off');
    formData.append('validate_only', document.getElementById('validateOnly').checked ? 'on' : 'off');

    // Show progress
    $('#importProgress').show();
    $('#importResults').hide();
    $('#importBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Processing...');

    // Start progress animation
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += Math.random() * 10;
        if (progress > 90) progress = 90;
        $('.progress-bar').css('width', progress + '%');
        $('#progressText').text(`Processing... ${Math.round(progress)}%`);
    }, 100);

    fetch(`${window.URLROOT}/products/importCSV`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        clearInterval(progressInterval);
        $('.progress-bar').css('width', '100%');
        $('#progressText').text('Complete!');

        setTimeout(() => {
            $('#importProgress').hide();
            showImportResults(data);
        }, 500);
    })
    .catch(error => {
        clearInterval(progressInterval);
        $('#importProgress').hide();
        console.error('Error:', error);
        showImportResults({
            success: false,
            message: 'An error occurred during import. Please try again.'
        });
    })
    .finally(() => {
        $('#importBtn').prop('disabled', false).html('<i class="fas fa-upload mr-1"></i>Start Import');
    });
}

function showImportResults(data) {
    $('#importResults').show();
    
    if (data.success) {
        let alertClass = 'alert-success';
        let title = 'Import Successful!';
        let content = `
            <div class="row">
                <div class="col-md-6">
                    <strong>Summary:</strong>
                    <ul class="mb-0">
                        <li>Total rows processed: ${data.total_rows || 0}</li>
                        <li>Successfully imported: ${data.processed || 0}</li>
                        <li>Skipped: ${data.skipped || 0}</li>
                    </ul>
                </div>
            </div>
        `;

        if (data.errors && data.errors.length > 0) {
            alertClass = 'alert-warning';
            title = 'Import Completed with Issues';
            content += `
                <div class="mt-3">
                    <strong>Errors:</strong>
                    <ul class="text-danger small">
                        ${data.errors.map(error => `<li>${error}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        if (data.warnings && data.warnings.length > 0) {
            content += `
                <div class="mt-3">
                    <strong>Warnings:</strong>
                    <ul class="text-warning small">
                        ${data.warnings.map(warning => `<li>${warning}</li>`).join('')}
                    </ul>
                </div>
            `;
        }

        $('#importResults .alert').removeClass().addClass(`alert ${alertClass}`);
        $('#resultTitle').text(title);
        $('#resultContent').html(content);

        // Refresh the products list if import was successful
        if (data.processed > 0) {
            setTimeout(() => {
                refreshData();
                $('#csvImportModal').modal('hide');
            }, 3000);
        }
    } else {
        $('#importResults .alert').removeClass().addClass('alert alert-danger');
        $('#resultTitle').text('Import Failed');
        $('#resultContent').html(`<p class="mb-0">${data.message || 'Unknown error occurred'}</p>`);
    }
}
