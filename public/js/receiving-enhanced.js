/**
 * Enhanced Receiving Page JavaScript
 * Integrates barcode scanning with receiving functionality
 */

class ReceivingManager {
    constructor() {
        this.scanner = null;
        this.currentMode = 'search'; // 'search', 'receive', 'location'
        this.scanHistory = [];
        this.alertSystem = new AlertSystem();
        this.filters = {
            supplier: '',
            status: '',
            dateRange: '',
            search: ''
        };
        this.selectedItems = new Set();
        this.init();
    }

    init() {
        this.initBarcodeScanner();
        this.initEventListeners();
        this.initRealTimeAlerts();
        this.loadKPIData();
        this.loadRecentActivity();
    }

    initBarcodeScanner() {
        try {
            this.scanner = new EnhancedBarcodeScanner({
                videoElement: document.getElementById('barcodeScannerVideo'),
                onScan: (result) => this.handleBarcodeScanned(result),
                onError: (message, error) => this.handleScannerError(message, error),
                onStatusChange: (status) => this.updateScannerStatus(status),
                autoStop: false,
                scanCooldown: 1500
            });

            console.log('Barcode scanner initialized successfully');
        } catch (error) {
            console.error('Failed to initialize barcode scanner:', error);
            this.showAlert('Scanner initialization failed. Manual entry available.', 'warning');
        }
    }

    initEventListeners() {
        // Unified search functionality
        const unifiedSearch = document.getElementById('unifiedSearch');
        if (unifiedSearch) {
            unifiedSearch.addEventListener('input', (e) => {
                this.handleUnifiedSearch(e.target.value);
            });

            unifiedSearch.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.performSearch(e.target.value);
                }
            });
        }

        // Barcode scanner controls
        document.getElementById('startScanner')?.addEventListener('click', () => this.startScanning());
        document.getElementById('stopScanner')?.addEventListener('click', () => this.stopScanning());
        document.getElementById('switchCamera')?.addEventListener('click', () => this.switchCamera());
        document.getElementById('manualEntry')?.addEventListener('click', () => this.showManualEntry());

        // Quick actions
        document.getElementById('markAllReceived')?.addEventListener('click', () => this.markAllAsReceived());
        document.getElementById('exportReport')?.addEventListener('click', () => this.exportReport());
        document.getElementById('refreshData')?.addEventListener('click', () => this.refreshAllData());

        // Advanced filters
        document.querySelectorAll('.filter-input').forEach(input => {
            input.addEventListener('change', () => this.applyFilters());
        });

        // Bulk operations
        document.getElementById('selectAll')?.addEventListener('change', (e) => this.toggleSelectAll(e.target.checked));
        document.getElementById('bulkReceive')?.addEventListener('click', () => this.bulkReceiveSelected());

        // Modal close handlers
        document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
            button.addEventListener('click', (e) => {
                const modal = e.target.closest('.modal');
                if (modal) {
                    bootstrap.Modal.getInstance(modal)?.hide();
                }
            });
        });

        // Camera permission handler
        document.getElementById('requestCameraPermission')?.addEventListener('click', () => this.requestCameraPermission());
    }

    initRealTimeAlerts() {
        // Check for discrepancies every 30 seconds
        setInterval(() => {
            this.checkDiscrepancies();
        }, 30000);

        // Initial check
        this.checkDiscrepancies();
    }

    async handleBarcodeScanned(result) {
        console.log('Barcode scanned:', result);

        // Add to scan history
        this.scanHistory.unshift({
            ...result,
            timestamp: new Date(),
            mode: this.currentMode
        });

        // Keep only last 50 scans
        if (this.scanHistory.length > 50) {
            this.scanHistory = this.scanHistory.slice(0, 50);
        }

        // Validate barcode
        const validation = BarcodeValidator.validate(result.value);
        if (!validation.valid) {
            this.showAlert(`Invalid barcode: ${validation.error}`, 'warning');
            return;
        }

        // Update unified search
        const searchInput = document.getElementById('unifiedSearch');
        if (searchInput) {
            searchInput.value = result.value;
        }

        // Process based on current mode
        switch (this.currentMode) {
            case 'search':
                await this.searchByBarcode(result.value);
                break;
            case 'receive':
                await this.receiveByBarcode(result.value);
                break;
            case 'location':
                await this.updateLocationByBarcode(result.value);
                break;
        }

        // Update scan history display
        this.updateScanHistoryDisplay();

        // Visual feedback
        this.showScanSuccess(result.value);
    }

    async searchByBarcode(barcode) {
        try {
            this.showLoading('Searching for product...');

            const response = await fetch('/receiving/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    query: barcode,
                    type: 'barcode'
                })
            });

            const data = await response.json();

            if (data.success) {
                this.displaySearchResults(data.results);
                if (data.results.length === 1) {
                    this.highlightItem(data.results[0].id);
                }
            } else {
                this.showAlert(data.message || 'Product not found', 'info');
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showAlert('Search failed. Please try again.', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async receiveByBarcode(barcode) {
        try {
            this.showLoading('Processing receipt...');

            const response = await fetch('/receiving/receive-by-barcode', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    barcode: barcode,
                    timestamp: new Date().toISOString()
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert(`Received: ${data.product_name} (Qty: ${data.quantity})`, 'success');
                this.updateKPIData();
                this.loadRecentActivity();
            } else {
                this.showAlert(data.message || 'Failed to receive item', 'error');
            }
        } catch (error) {
            console.error('Receive error:', error);
            this.showAlert('Receipt failed. Please try again.', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async updateLocationByBarcode(barcode) {
        const location = prompt('Enter new location for this item:');
        if (!location) return;

        try {
            this.showLoading('Updating location...');

            const response = await fetch('/receiving/update-location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    barcode: barcode,
                    location: location
                })
            });

            const data = await response.json();

            if (data.success) {
                this.showAlert(`Location updated to: ${location}`, 'success');
                this.loadRecentActivity();
            } else {
                this.showAlert(data.message || 'Failed to update location', 'error');
            }
        } catch (error) {
            console.error('Location update error:', error);
            this.showAlert('Location update failed. Please try again.', 'error');
        } finally {
            this.hideLoading();
        }
    }

    handleUnifiedSearch(query) {
        if (query.length < 2) {
            this.clearSearchResults();
            return;
        }

        // Debounce search
        clearTimeout(this.searchTimeout);
        this.searchTimeout = setTimeout(() => {
            this.performSearch(query);
        }, 300);
    }

    async performSearch(query) {
        if (!query.trim()) return;

        try {
            this.showLoading('Searching...');

            const response = await fetch('/receiving/search', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    query: query.trim(),
                    filters: this.filters
                })
            });

            const data = await response.json();

            if (data.success) {
                this.displaySearchResults(data.results);
            } else {
                this.showAlert(data.message || 'No results found', 'info');
                this.clearSearchResults();
            }
        } catch (error) {
            console.error('Search error:', error);
            this.showAlert('Search failed. Please try again.', 'error');
        } finally {
            this.hideLoading();
        }
    }

    displaySearchResults(results) {
        const container = document.getElementById('searchResults');
        if (!container) return;

        if (results.length === 0) {
            container.innerHTML = '<div class="text-center text-muted py-4">No results found</div>';
            return;
        }

        const html = results.map(item => `
            <div class="search-result-item" data-id="${item.id}" data-barcode="${item.barcode || ''}">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <h6 class="mb-1">${this.escapeHtml(item.name)}</h6>
                        <small class="text-muted">
                            SKU: ${item.sku || 'N/A'} | 
                            Supplier: ${item.supplier_name || 'N/A'} |
                            Expected: ${item.expected_quantity || 0} |
                            Received: ${item.received_quantity || 0}
                        </small>
                        ${item.barcode ? `<br><small class="text-info">Barcode: ${item.barcode}</small>` : ''}
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary receive-item" 
                                data-id="${item.id}" data-name="${this.escapeHtml(item.name)}">
                            <i class="fas fa-check"></i> Receive
                        </button>
                        <button class="btn btn-sm btn-outline-secondary view-details" 
                                data-id="${item.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                ${item.discrepancy ? `
                    <div class="alert alert-warning alert-sm mt-2 mb-0">
                        <i class="fas fa-exclamation-triangle"></i>
                        Discrepancy detected: ${item.discrepancy}
                    </div>
                ` : ''}
            </div>
        `).join('');

        container.innerHTML = html;

        // Add event listeners for result actions
        container.querySelectorAll('.receive-item').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const itemId = e.target.closest('[data-id]').dataset.id;
                const itemName = e.target.dataset.name;
                this.showReceiveModal(itemId, itemName);
            });
        });

        container.querySelectorAll('.view-details').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const itemId = e.target.closest('[data-id]').dataset.id;
                this.showItemDetails(itemId);
            });
        });
    }

    clearSearchResults() {
        const container = document.getElementById('searchResults');
        if (container) {
            container.innerHTML = '';
        }
    }

    async checkDiscrepancies() {
        try {
            const response = await fetch('/receiving/check-discrepancies', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success && data.discrepancies.length > 0) {
                this.alertSystem.showDiscrepancyAlert(data.discrepancies);
                this.updateDiscrepancyCounter(data.discrepancies.length);
            }
        } catch (error) {
            console.error('Discrepancy check failed:', error);
        }
    }

    async loadKPIData() {
        try {
            const response = await fetch('/receiving/kpi-data', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateKPICards(data.kpis);
            }
        } catch (error) {
            console.error('KPI data load failed:', error);
        }
    }

    updateKPICards(kpis) {
        // Update KPI values
        document.getElementById('totalOrders')?.setAttribute('data-value', kpis.total_orders || 0);
        document.getElementById('completedOrders')?.setAttribute('data-value', kpis.completed_orders || 0);
        document.getElementById('pendingItems')?.setAttribute('data-value', kpis.pending_items || 0);
        document.getElementById('overdueItems')?.setAttribute('data-value', kpis.overdue_items || 0);

        // Update progress rings
        this.updateProgressRing('totalOrdersProgress', kpis.completion_rate || 0);
        this.updateProgressRing('completedOrdersProgress', 100);
        this.updateProgressRing('pendingItemsProgress', kpis.pending_rate || 0);
        this.updateProgressRing('overdueItemsProgress', kpis.overdue_rate || 0);
    }

    updateProgressRing(elementId, percentage) {
        const ring = document.getElementById(elementId);
        if (ring) {
            const circumference = 2 * Math.PI * 36; // radius = 36
            const offset = circumference - (percentage / 100) * circumference;
            ring.style.strokeDasharray = circumference;
            ring.style.strokeDashoffset = offset;
        }
    }

    async loadRecentActivity() {
        try {
            const response = await fetch('/receiving/recent-activity', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.updateActivityTable(data.activities);
            }
        } catch (error) {
            console.error('Activity data load failed:', error);
        }
    }

    updateActivityTable(activities) {
        const tbody = document.querySelector('#activityTable tbody');
        if (!tbody) return;

        if (activities.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No recent activity</td></tr>';
            return;
        }

        const html = activities.map(activity => `
            <tr>
                <td>
                    <input type="checkbox" class="form-check-input item-select" 
                           value="${activity.id}" ${this.selectedItems.has(activity.id) ? 'checked' : ''}>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${activity.product_image || '/images/placeholder-product.png'}" 
                             alt="Product" class="product-thumb me-2">
                        <div>
                            <div class="fw-medium">${this.escapeHtml(activity.product_name)}</div>
                            <small class="text-muted">${activity.sku || 'N/A'}</small>
                        </div>
                    </div>
                </td>
                <td>${this.escapeHtml(activity.supplier_name || 'N/A')}</td>
                <td>
                    <span class="text-nowrap">${activity.expected_quantity || 0}</span>
                </td>
                <td>
                    <span class="text-nowrap">${activity.received_quantity || 0}</span>
                </td>
                <td>
                    <span class="badge bg-${this.getStatusColor(activity.status)}">${activity.status || 'Unknown'}</span>
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary receive-btn" 
                                data-id="${activity.id}" data-name="${this.escapeHtml(activity.product_name)}"
                                ${activity.status === 'completed' ? 'disabled' : ''}>
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-outline-secondary details-btn" data-id="${activity.id}">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-outline-info notes-btn" data-id="${activity.id}">
                            <i class="fas fa-sticky-note"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        tbody.innerHTML = html;

        // Add event listeners
        tbody.querySelectorAll('.item-select').forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const itemId = parseInt(e.target.value);
                if (e.target.checked) {
                    this.selectedItems.add(itemId);
                } else {
                    this.selectedItems.delete(itemId);
                }
                this.updateBulkActionsState();
            });
        });

        tbody.querySelectorAll('.receive-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const itemId = e.target.closest('[data-id]').dataset.id;
                const itemName = e.target.dataset.name;
                this.showReceiveModal(itemId, itemName);
            });
        });

        tbody.querySelectorAll('.details-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const itemId = e.target.closest('[data-id]').dataset.id;
                this.showItemDetails(itemId);
            });
        });

        tbody.querySelectorAll('.notes-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const itemId = e.target.closest('[data-id]').dataset.id;
                this.showNotesModal(itemId);
            });
        });
    }

    getStatusColor(status) {
        const colors = {
            'completed': 'success',
            'partial': 'warning',
            'pending': 'primary',
            'overdue': 'danger'
        };
        return colors[status] || 'secondary';
    }

    // Scanner control methods
    async startScanning() {
        if (!this.scanner) {
            this.showAlert('Scanner not available', 'error');
            return;
        }

        try {
            await this.scanner.startScanning();
            this.updateScannerUI(true);
        } catch (error) {
            console.error('Failed to start scanner:', error);
            this.showAlert('Failed to start scanner. Check camera permissions.', 'error');
        }
    }

    stopScanning() {
        if (this.scanner) {
            this.scanner.stopScanning();
            this.updateScannerUI(false);
        }
    }

    async switchCamera() {
        if (this.scanner) {
            await this.scanner.switchCamera();
        }
    }

    showManualEntry() {
        if (this.scanner) {
            this.scanner.manualEntry();
        }
    }

    updateScannerUI(isScanning) {
        const startBtn = document.getElementById('startScanner');
        const stopBtn = document.getElementById('stopScanner');
        const switchBtn = document.getElementById('switchCamera');

        if (startBtn) startBtn.disabled = isScanning;
        if (stopBtn) stopBtn.disabled = !isScanning;
        if (switchBtn) switchBtn.disabled = !isScanning;

        // Update scanner modal visibility
        const modal = document.getElementById('barcodeScannerModal');
        if (modal) {
            if (isScanning) {
                new bootstrap.Modal(modal).show();
            } else {
                bootstrap.Modal.getInstance(modal)?.hide();
            }
        }
    }

    // Utility methods
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, (m) => map[m]);
    }

    showAlert(message, type = 'info') {
        this.alertSystem.show(message, type);
    }

    showLoading(message = 'Loading...') {
        // Implementation for loading indicator
        const loader = document.getElementById('loadingIndicator');
        if (loader) {
            loader.textContent = message;
            loader.style.display = 'block';
        }
    }

    hideLoading() {
        const loader = document.getElementById('loadingIndicator');
        if (loader) {
            loader.style.display = 'none';
        }
    }

    updateKPIData() {
        this.loadKPIData();
    }

    refreshAllData() {
        this.loadKPIData();
        this.loadRecentActivity();
        this.checkDiscrepancies();
        this.showAlert('Data refreshed successfully', 'success');
    }

    updateScanHistoryDisplay() {
        const container = document.getElementById('scanHistoryList');
        if (!container) return;

        if (this.scanHistory.length === 0) {
            container.innerHTML = '<small class="text-muted">No scans yet</small>';
            return;
        }

        const html = this.scanHistory.slice(0, 5).map(scan => `
            <div class="scan-history-item">
                <div class="scan-value">${this.escapeHtml(scan.value)}</div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">${scan.format}</small>
                    <small class="scan-time">${this.formatTime(scan.timestamp)}</small>
                </div>
            </div>
        `).join('');

        container.innerHTML = html;
    }

    formatTime(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleTimeString('en-US', {
            hour12: false,
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    highlightItem(itemId) {
        // Remove previous highlights
        document.querySelectorAll('.search-result-item').forEach(item => {
            item.classList.remove('highlight');
        });

        // Highlight the found item
        const item = document.querySelector(`[data-id="${itemId}"]`);
        if (item) {
            item.classList.add('highlight');
            item.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    showScanSuccess(barcode) {
        // Visual feedback for successful scan
        const notification = document.createElement('div');
        notification.className = 'scan-success-notification';
        notification.innerHTML = `
            <i class="fas fa-check-circle"></i>
            Scanned: ${this.escapeHtml(barcode)}
        `;

        document.body.appendChild(notification);

        // Auto-remove after 2 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 2000);
    }

    showReceiveModal(itemId, itemName) {
        // Create or show receive modal
        let modal = document.getElementById('receiveItemModal');
        if (!modal) {
            modal = this.createReceiveModal();
        }

        // Update modal content
        const modalTitle = modal.querySelector('.modal-title');
        const itemNameField = modal.querySelector('#receiveItemName');
        const itemIdField = modal.querySelector('#receiveItemId');

        if (modalTitle) modalTitle.textContent = `Receive Item: ${itemName}`;
        if (itemNameField) itemNameField.value = itemName;
        if (itemIdField) itemIdField.value = itemId;

        new bootstrap.Modal(modal).show();
    }

    createReceiveModal() {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = 'receiveItemModal';
        modal.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Receive Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="receiveItemForm">
                            <input type="hidden" id="receiveItemId">
                            <div class="mb-3">
                                <label class="form-label">Item Name</label>
                                <input type="text" class="form-control" id="receiveItemName" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Quantity Received</label>
                                <input type="number" class="form-control" id="receiveQuantity" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Location</label>
                                <input type="text" class="form-control" id="receiveLocation" placeholder="Enter storage location">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" id="receiveNotes" rows="3" placeholder="Optional notes"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="submitReceiveItem()">Receive Item</button>
                    </div>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        return modal;
    }

    showItemDetails(itemId) {
        // Show item details in a modal or expand inline
        console.log('Show item details for:', itemId);
        this.showAlert('Item details feature coming soon', 'info');
    }

    showNotesModal(itemId) {
        // Show notes modal for the item
        console.log('Show notes modal for:', itemId);
        this.showAlert('Notes feature coming soon', 'info');
    }

    updateBulkActionsState() {
        const bulkBtn = document.getElementById('bulkReceive');
        if (bulkBtn) {
            bulkBtn.disabled = this.selectedItems.size === 0;
            bulkBtn.textContent = `Bulk Receive (${this.selectedItems.size})`;
        }
    }

    toggleSelectAll(checked) {
        document.querySelectorAll('.item-select').forEach(checkbox => {
            checkbox.checked = checked;
            const itemId = parseInt(checkbox.value);
            if (checked) {
                this.selectedItems.add(itemId);
            } else {
                this.selectedItems.delete(itemId);
            }
        });
        this.updateBulkActionsState();
    }

    async markAllAsReceived() {
        if (confirm('Mark all visible items as received?')) {
            try {
                this.showLoading('Processing bulk receive...');

                const response = await fetch('/receiving/bulk-receive-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    this.showAlert(`Marked ${data.count} items as received`, 'success');
                    this.refreshAllData();
                } else {
                    this.showAlert(data.message || 'Bulk receive failed', 'error');
                }
            } catch (error) {
                console.error('Bulk receive error:', error);
                this.showAlert('Bulk receive failed. Please try again.', 'error');
            } finally {
                this.hideLoading();
            }
        }
    }

    async exportReport() {
        try {
            this.showLoading('Generating report...');

            const response = await fetch('/receiving/export-report', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `receiving-report-${new Date().toISOString().split('T')[0]}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);

                this.showAlert('Report exported successfully', 'success');
            } else {
                throw new Error('Export failed');
            }
        } catch (error) {
            console.error('Export error:', error);
            this.showAlert('Export failed. Please try again.', 'error');
        } finally {
            this.hideLoading();
        }
    }

    updateDiscrepancyCounter(count) {
        const counter = document.getElementById('discrepancyCounter');
        if (counter) {
            counter.textContent = count;
            counter.style.display = count > 0 ? 'inline' : 'none';
        }
    }

    async applyFilters() {
        // Collect filter values
        this.filters = {
            supplier: document.getElementById('supplierFilter')?.value || '',
            status: document.getElementById('statusFilter')?.value || '',
            dateRange: document.getElementById('dateRangeFilter')?.value || '',
            search: document.getElementById('unifiedSearchInput')?.value || ''
        };

        // Apply filters to current search/view
        if (this.filters.search) {
            await this.performSearch(this.filters.search);
        } else {
            await this.loadRecentActivity();
        }
    }
}

// Alert System Class
class AlertSystem {
    constructor() {
        this.container = this.createAlertContainer();
    }

    createAlertContainer() {
        let container = document.getElementById('alertContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'alertContainer';
            container.className = 'alert-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        return container;
    }

    show(message, type = 'info', duration = 5000) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        this.container.appendChild(alert);

        // Auto-remove after duration
        if (duration > 0) {
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.remove();
                }
            }, duration);
        }

        return alert;
    }

    showDiscrepancyAlert(discrepancies) {
        const message = `${discrepancies.length} discrepancy(ies) detected. Click to review.`;
        const alert = this.show(message, 'warning', 10000);

        alert.style.cursor = 'pointer';
        alert.addEventListener('click', () => {
            this.showDiscrepancyModal(discrepancies);
        });
    }

    showDiscrepancyModal(discrepancies) {
        // Implementation for discrepancy modal
        console.log('Show discrepancy modal:', discrepancies);
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.receivingManager = new ReceivingManager();
    console.log('Receiving Manager initialized');
});

// Export for global access
window.ReceivingManager = ReceivingManager;
window.AlertSystem = AlertSystem;
