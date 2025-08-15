// Pass PHP data to JavaScript
// Note: window.URLROOT and window.suppliersData must be defined in the HTML before this script is loaded.

// Debug: Log the data being passed
console.log('PHP Suppliers Data:', window.suppliersData);
console.log('Data length:', window.suppliersData ? window.suppliersData.length : 'No data');

document.addEventListener('DOMContentLoaded', function () {
    // Charts removed - no initialization needed
});

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.supplier-checkbox');

    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });

    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.supplier-checkbox:checked');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    if (checkboxes.length > 0) {
        bulkActions.style.display = 'block';
        selectedCount.textContent = `${checkboxes.length} item${checkboxes.length > 1 ? 's' : ''} selected`;
    } else {
        bulkActions.style.display = 'none';
    }
}

// Supplier Action Functions
function toggleSupplierStatus(supplierId, currentStatus) {
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    const action = newStatus === 'active' ? 'activate' : 'deactivate';

    if (confirm(`Are you sure you want to ${action} this supplier?`)) {
        const formData = new FormData();
        formData.append('supplier_id', supplierId);
        formData.append('status', newStatus);

        fetch(`${window.URLROOT}/suppliers/updateStatus`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(`Supplier ${action}d successfully`, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.error || `Failed to ${action} supplier`, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
            });
    }
}

function deleteSupplier(supplierId) {
    if (confirm('Archive this supplier?\\n\\nThis will hide the supplier from active lists but preserve all records and history.\\n\\nYou can restore this supplier later if needed.\\n\\nProceed to archive?')) {
        // Use fetch for AJAX request instead of redirect
        fetch(`${window.URLROOT}/suppliers/delete/${supplierId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Supplier archived successfully!');
                    location.reload();
                } else {
                    alert('Error archiving supplier: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error archiving supplier');
            });
    }
}

// New action functions to match Products Management style
function viewSupplier(supplierId) {
    window.location.href = `${window.URLROOT}/suppliers/view/${supplierId}`;
}

function editSupplier(supplierId) {
    window.location.href = `${window.URLROOT}/suppliers/edit/${supplierId}`;
}

function viewSupplierPerformance(supplierId) {
    window.location.href = `${window.URLROOT}/suppliers/performance/${supplierId}`;
}

function viewSupplierOrders(supplierId) {
    window.location.href = `${window.URLROOT}/suppliers/orders/${supplierId}`;
}

// Filter Functions - Enhanced with server-side filtering
function filterSuppliers(filterType) {
    const currentUrl = new URL(window.location.href);

    // Set filter parameters
    if (filterType === 'all') {
        currentUrl.searchParams.delete('status');
        currentUrl.searchParams.delete('tier');
    } else if (filterType === 'active' || filterType === 'inactive') {
        currentUrl.searchParams.set('status', filterType);
        currentUrl.searchParams.delete('tier');
    } else {
        currentUrl.searchParams.set('tier', filterType);
        currentUrl.searchParams.delete('status');
    }

    currentUrl.searchParams.set('page', '1'); // Reset to first page
    window.location.href = currentUrl.toString();
}

// Pagination Functions
function changeItemsPerPage() {
    const perPage = document.getElementById('itemsPerPage').value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('per_page', perPage);
    currentUrl.searchParams.set('page', '1'); // Reset to first page
    window.location.href = currentUrl.toString();
}

// Export Functions
function exportSuppliers(format) {
    const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
    const exportType = selectedSuppliers.length > 0 ? 'selected' : 'all';

    const params = new URLSearchParams({
        format: format,
        type: exportType,
        suppliers: selectedSuppliers.join(',')
    });

    window.location.href = `${window.URLROOT}/suppliers/export?${params}`;
}

// Bulk Operations
function bulkEditSuppliers() {
    const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
    if (selectedSuppliers.length === 0) {
        alert('Please select suppliers to edit');
        return;
    }
    window.location.href = `${window.URLROOT}/suppliers/bulkEdit?suppliers=${selectedSuppliers.join(',')}`;
}

function bulkActivateSuppliers() {
    const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
    if (selectedSuppliers.length === 0) {
        alert('Please select suppliers to activate');
        return;
    }

    if (confirm(`Activate ${selectedSuppliers.length} selected suppliers?`)) {
        const formData = new FormData();
        formData.append('suppliers', selectedSuppliers.join(','));
        formData.append('action', 'activate');

        fetch(`${window.URLROOT}/suppliers/bulkUpdateStatus`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Suppliers activated successfully', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.error || 'Failed to activate suppliers', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while activating suppliers', 'error');
            });
    }
}

function bulkDeactivateSuppliers() {
    const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
    if (selectedSuppliers.length === 0) {
        alert('Please select suppliers to deactivate');
        return;
    }

    if (confirm(`Deactivate ${selectedSuppliers.length} selected suppliers?`)) {
        const formData = new FormData();
        formData.append('suppliers', selectedSuppliers.join(','));
        formData.append('action', 'deactivate');

        fetch(`${window.URLROOT}/suppliers/bulkUpdateStatus`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Suppliers deactivated successfully', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.error || 'Failed to deactivate suppliers', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred while deactivating suppliers', 'error');
            });
    }
}

function bulkDeleteSuppliers() {
    const selectedSuppliers = Array.from(document.querySelectorAll('.supplier-checkbox:checked')).map(cb => cb.value);
    if (selectedSuppliers.length === 0) {
        alert('Please select suppliers to archive');
        return;
    }

    if (confirm(`Archive ${selectedSuppliers.length} selected suppliers?\\n\\nThis will hide the suppliers from active lists but preserve all records and history.\\n\\nYou can restore these suppliers later if needed.\\n\\nProceed to archive?`)) {
        // Use AJAX instead of redirect for proper handling
        const formData = new FormData();
        selectedSuppliers.forEach(id => formData.append('supplier_ids[]', id));

        fetch(`${window.URLROOT}/suppliers/bulkDelete`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`${selectedSuppliers.length} suppliers archived successfully!`);
                    location.reload();
                } else {
                    alert('Error archiving suppliers: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error archiving suppliers');
            });
    }
}

function showNotification(message, type = 'info') {
    // Simple notification function
    const alertClass = type === 'success' ? 'alert-success' :
        type === 'error' ? 'alert-danger' : 'alert-info';

    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;

    document.body.appendChild(notification);

    // Auto-remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// Initialize tooltips
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

// Column Sorting functionality
function sortTable(column) {
    console.log('Sorting by column:', column); // Debug output
    const currentUrl = new URL(window.location.href);
    const currentSort = currentUrl.searchParams.get('sort');
    const currentOrder = currentUrl.searchParams.get('order');

    // Determine new sort order
    let newOrder = 'ASC';
    if (currentSort === column && currentOrder === 'ASC') {
        newOrder = 'DESC';
    }

    // Update URL parameters
    currentUrl.searchParams.set('sort', column);
    currentUrl.searchParams.set('order', newOrder);
    currentUrl.searchParams.set('page', '1'); // Reset to first page

    console.log('New URL:', currentUrl.toString()); // Debug output

    // Navigate to new URL
    window.location.href = currentUrl.toString();
}

// Initialize sorting event listeners
$(document).ready(function () {
    // Add click event listeners to sortable columns
    $('.sortable').on('click', function (e) {
        e.preventDefault();
        const sortColumn = $(this).data('sort');
        sortTable(sortColumn);
    });
});
