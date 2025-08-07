// Enhanced Inventory Management JavaScript

$(document).ready(function () {
    initializeInventoryPage();
});

function initializeInventoryPage() {
    // Initialize DataTable
    initializeDataTable();

    // Initialize Chart
    initializeInventoryChart();

    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Set up event listeners
    setupEventListeners();

    // Initialize filters
    setupFilters();

    // Set up keyboard shortcuts
    setupKeyboardShortcuts();

    // Update adjustment preview when form changes
    setupAdjustmentPreview();
}

function initializeDataTable() {
    if ($.fn.DataTable.isDataTable('#inventoryTable')) {
        $('#inventoryTable').DataTable().destroy();
    }

    $('#inventoryTable').DataTable({
        "responsive": true,
        "pageLength": 25,
        "order": [[8, "asc"], [4, "asc"]], // Sort by status then Inventory level
        "columnDefs": [
            { "orderable": false, "targets": [0, 9] }, // Disable sorting for checkbox and actions
            { "searchable": false, "targets": [0, 9] }
        ],
        "dom": '<"top"lf>rt<"bottom"ip>',
        "language": {
            "search": "",
            "searchPlaceholder": "Search inventory...",
            "lengthMenu": "Show _MENU_ items per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ items",
            "paginate": {
                "first": "<i class='fas fa-angle-double-left'></i>",
                "last": "<i class='fas fa-angle-double-right'></i>",
                "next": "<i class='fas fa-angle-right'></i>",
                "previous": "<i class='fas fa-angle-left'></i>"
            }
        },
        "initComplete": function () {
            // Hide default search box since we have custom search
            $('.dataTables_filter').hide();
        }
    });
}

function initializeInventoryChart() {
    const ctx = document.getElementById('InventoryStatusChart');
    if (!ctx) return;

    // Calculate Inventory distribution
    const products = window.inventoryData || [];
    let inInventory = 0, lowInventory = 0, outOfInventory = 0;

    products.forEach(product => {
        const Inventory = product.current_Inventory || 0;
        const reorder = product.reorder_level || 0;

        if (Inventory <= 0) {
            outOfInventory++;
        } else if (Inventory <= reorder) {
            lowInventory++;
        } else {
            inInventory++;
        }
    });

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['In Inventory', 'Low Inventory', 'Out of Inventory'],
            datasets: [{
                data: [inInventory, lowInventory, outOfInventory],
                backgroundColor: [
                    '#28a745',
                    '#ffc107',
                    '#dc3545'
                ],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed * 100) / total).toFixed(1);
                            return `${context.label}: ${context.parsed} items (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '70%',
            animation: {
                animateRotate: true,
                duration: 1000
            }
        }
    });
}

function setupEventListeners() {
    // Select all checkbox
    $('#selectAll').on('change', toggleSelectAll);

    // Individual product checkboxes
    $(document).on('change', '.product-checkbox', updateBulkActionsPanel);

    // Filter changes
    $('#inventorySearch').on('keyup', debounce(applyCustomSearch, 300));
    $('#categoryFilter, #statusFilter, #locationFilter').on('change', applyFilters);

    // Adjustment type changes
    $('#adjust_type, #adjust_quantity').on('change input', updateAdjustmentPreview);
}

function setupFilters() {
    // Custom search functionality
    $('#inventorySearch').on('keyup', function () {
        const table = $('#inventoryTable').DataTable();
        table.search(this.value).draw();
    });
}

function setupKeyboardShortcuts() {
    $(document).keydown(function (e) {
        // Ctrl+F - Focus search
        if (e.ctrlKey && e.which === 70) {
            e.preventDefault();
            $('#inventorySearch').focus();
        }

        // Ctrl+A - Select all visible items
        if (e.ctrlKey && e.which === 65) {
            e.preventDefault();
            $('#selectAll').prop('checked', true).trigger('change');
        }

        // Escape - Clear selection
        if (e.which === 27) {
            clearSelection();
        }
    });
}

function setupAdjustmentPreview() {
    $('#adjust_type, #adjust_quantity').on('input change', function () {
        updateAdjustmentPreview();
    });
}

// Selection Management
function toggleSelectAll() {
    const isChecked = $('#selectAll').is(':checked');
    $('.product-checkbox').prop('checked', isChecked);
    updateBulkActionsPanel();
}

function updateBulkActionsPanel() {
    const selectedCount = $('.product-checkbox:checked').length;
    $('#selectedCount').text(selectedCount);

    if (selectedCount > 0) {
        $('#bulkActionsPanel').slideDown();
    } else {
        $('#bulkActionsPanel').slideUp();
    }
}

function clearSelection() {
    $('#selectAll').prop('checked', false);
    $('.product-checkbox').prop('checked', false);
    updateBulkActionsPanel();
}

// Filter Functions
function applyFilters() {
    const category = $('#categoryFilter').val();
    const status = $('#statusFilter').val();
    const location = $('#locationFilter').val();

    const table = $('#inventoryTable').DataTable();

    // Apply category filter
    if (category) {
        table.column(2).search(category, true, false);
    } else {
        table.column(2).search('');
    }

    // Apply status filter
    if (status) {
        let statusText = '';
        switch (status) {
            case 'in_Inventory': statusText = 'Good|Medium'; break;
            case 'low_Inventory': statusText = 'Low Inventory'; break;
            case 'out_of_Inventory': statusText = 'Out of Inventory'; break;
            case 'critical': statusText = 'Low Inventory|Out of Inventory'; break;
        }
        table.column(8).search(statusText, true, false);
    } else {
        table.column(8).search('');
    }

    table.draw();
    showNotification('Filters applied successfully', 'success');
}

function clearFilters() {
    $('#inventorySearch').val('');
    $('#categoryFilter').val('');
    $('#statusFilter').val('');
    $('#locationFilter').val('');

    const table = $('#inventoryTable').DataTable();
    table.search('').columns().search('').draw();

    showNotification('Filters cleared', 'info');
}

function applyCustomSearch() {
    const searchTerm = $('#inventorySearch').val();
    const table = $('#inventoryTable').DataTable();
    table.search(searchTerm).draw();
}

// Inventory Management Functions
function adjustInventory(productId) {
    const product = window.inventoryData?.find(p => p.product_id == productId);

    if (product) {
        $('#adjust_product_id').val(product.product_id);
        $('#adjust_product_name').val(product.product_name);
        $('#adjust_current_Inventory').val(product.current_Inventory || 0);
        $('#adjust_type').val('');
        $('#adjust_quantity').val('');
        $('#adjust_reason_type').val('');
        $('#adjust_reason').val('');
        updateAdjustmentPreview();
        $('#InventoryAdjustmentModal').modal('show');
    }
}

function updateAdjustmentPreview() {
    const currentInventory = parseInt($('#adjust_current_Inventory').val()) || 0;
    const adjustType = $('#adjust_type').val();
    const quantity = parseInt($('#adjust_quantity').val()) || 0;

    let newInventory = currentInventory;
    let previewText = 'Make your selections to see the preview';

    if (adjustType && quantity >= 0) {
        switch (adjustType) {
            case 'add':
                newInventory = currentInventory + quantity;
                previewText = `Current: ${currentInventory} → New: ${newInventory} (+${quantity})`;
                break;
            case 'remove':
                newInventory = Math.max(0, currentInventory - quantity);
                previewText = `Current: ${currentInventory} → New: ${newInventory} (-${quantity})`;
                break;
            case 'set':
                newInventory = quantity;
                previewText = `Current: ${currentInventory} → New: ${newInventory} (set to ${quantity})`;
                break;
        }
    }

    $('#adjustment_preview').text(previewText);
}

function submitInventoryAdjustment() {
    const formData = {
        product_id: $('#adjust_product_id').val(),
        type: $('#adjust_type').val(),
        quantity: $('#adjust_quantity').val(),
        reason_type: $('#adjust_reason_type').val(),
        reason: $('#adjust_reason').val()
    };

    // Validate form
    if (!formData.type || !formData.quantity) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }

    showLoading(true);

    // Simulate API call
    setTimeout(() => {
        showLoading(false);
        $('#InventoryAdjustmentModal').modal('hide');
        showNotification('Inventory adjustment applied successfully!', 'success');

        // In real implementation, refresh the data
        setTimeout(() => {
            location.reload();
        }, 1000);
    }, 1500);
}

// Quick Actions
function quickInventoryAdjustment() {
    showNotification('Quick adjustment panel coming soon!', 'info');
}

function performInventoryTake() {
    if (confirm('This will start a new Inventory take session. Continue?')) {
        showNotification('Inventory take session started', 'success');
        // Implement Inventory take functionality
    }
}

function bulkInventoryUpdate() {
    showNotification('Bulk update feature coming soon!', 'info');
}

function generateAlerts() {
    const criticalCount = $('.status-out-of-Inventory, .status-low-Inventory').length;
    showNotification(`Found ${criticalCount} items requiring attention`, 'warning');
}

// Bulk Operations
function bulkAdjustInventory() {
    const selectedIds = $('.product-checkbox:checked').map(function () {
        return this.value;
    }).get();

    if (selectedIds.length === 0) {
        showNotification('Please select items first', 'warning');
        return;
    }

    showNotification(`Bulk adjustment for ${selectedIds.length} items - feature coming soon!`, 'info');
}

function bulkSetReorder() {
    const selectedIds = $('.product-checkbox:checked').map(function () {
        return this.value;
    }).get();

    showNotification(`Setting reorder levels for ${selectedIds.length} items - feature coming soon!`, 'info');
}

function bulkPrintLabels() {
    const selectedIds = $('.product-checkbox:checked').map(function () {
        return this.value;
    }).get();

    if (selectedIds.length === 0) {
        showNotification('Please select items first', 'warning');
        return;
    }

    showNotification(`Printing labels for ${selectedIds.length} items...`, 'success');
}

function bulkExport() {
    const selectedIds = $('.product-checkbox:checked').map(function () {
        return this.value;
    }).get();

    if (selectedIds.length === 0) {
        showNotification('Please select items first', 'warning');
        return;
    }

    showNotification(`Exporting ${selectedIds.length} selected items...`, 'success');
}

// Individual Item Actions
function viewMovements(productId) {
    showNotification('Inventory movement history - feature coming soon!', 'info');
}

function setReorderLevel(productId) {
    const newLevel = prompt('Enter new reorder level:');
    if (newLevel && !isNaN(newLevel)) {
        showNotification(`Reorder level updated to ${newLevel}`, 'success');
    }
}

function printLabel(productId) {
    showNotification('Printing label...', 'success');
}

// Utility Functions
function refreshData() {
    showLoading(true);
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function exportInventory() {
    showLoading(true);
    setTimeout(() => {
        showLoading(false);
        showNotification('Inventory exported successfully!', 'success');
    }, 2000);
}

function printReport() {
    window.print();
}

function showLoading(show = true) {
    if (show) {
        if ($('#loadingOverlay').length === 0) {
            $('body').append(`
                <div id="loadingOverlay" class="loading-overlay">
                    <div class="text-center text-white">
                        <div class="loading-spinner mb-3"></div>
                        <p>Processing...</p>
                    </div>
                </div>
            `);
        }
    } else {
        $('#loadingOverlay').remove();
    }
}

function showNotification(message, type = 'info') {
    const alertClass = `alert-${type === 'error' ? 'danger' : type}`;
    const iconClass = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    }[type] || 'fa-info-circle';

    const alert = $(`
        <div class="alert ${alertClass} notification fade show" role="alert">
            <i class="fas ${iconClass} mr-2"></i>
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `);

    $('body').append(alert);

    // Auto-dismiss after 4 seconds
    setTimeout(() => {
        alert.alert('close');
    }, 4000);
}

// Debounce function for search
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Format numbers
function formatNumber(num, decimals = 0) {
    return Number(num).toLocaleString('en-US', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    });
}

// Auto-refresh every 5 minutes
setInterval(() => {
    if (document.visibilityState === 'visible') {
        // Refresh data silently
        console.log('Auto-refreshing inventory data...');
    }
}, 300000);
