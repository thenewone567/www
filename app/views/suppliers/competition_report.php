<?php
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'header.php';
?>

<div class="container-fluid theme-container theme-unified page-top-area mb-4">
    <div class="row align-items-center">
        <div class="col-12 col-md-8">
            <h1 class="mb-1 font-weight-bold">
                <i class="fas fa-chart-line mr-2"></i>Supplier Competition Report
            </h1>
            <small class="text-muted">Identify opportunities to negotiate better prices with suppliers</small>
        </div>
        <div class="col-12 col-md-4 text-md-right mt-3 mt-md-0">
            <a href="<?php echo URLROOT; ?>/suppliers" class="btn btn-secondary btn-lg mr-2">
                <i class="fas fa-arrow-left"></i> Back to Suppliers
            </a>
            <button onclick="clearAllTargets()" class="btn btn-outline-warning btn-lg mr-2" title="Reset all target prices to market lowest">
                <i class="fas fa-broom"></i> Clear Custom Targets
            </button>
            <a href="<?php echo URLROOT; ?>/suppliers/exportCompetitionReport" class="btn btn-success btn-lg mr-2">
                <i class="fas fa-download"></i> Export CSV
            </a>
            <button onclick="window.print()" class="btn btn-primary btn-lg">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
    </div>
</div>

<div class="container-fluid theme-container mt-0 pt-3">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-lg-4 col-md-6 mb-3">
            <div class="theme-card h-100">
                <div class="card-header bg-primary-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-box"></i> Products Analyzed</h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-primary">
                        <?php echo $data['products_analyzed']; ?>
                    </h4>
                    <small class="text-muted">With multiple suppliers</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="theme-card h-100">
                <div class="card-header bg-success-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-rupee-sign"></i> Potential Savings</h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-success">
                        ₹<?php echo number_format($data['total_potential_savings'], 2); ?>
                    </h4>
                    <small class="text-muted">If all match lowest prices</small>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-3">
            <div class="theme-card h-100">
                <div class="card-header bg-warning-theme text-white">
                    <h5 class="mb-0"><i class="fas fa-handshake"></i> Negotiation Opportunities</h5>
                </div>
                <div class="card-body text-center">
                    <h4 class="text-warning">
                        <?php 
                        $totalOpportunities = 0;
                        foreach ($data['competition_data'] as $product) {
                            $totalOpportunities += count($product['competition_opportunities']);
                        }
                        echo $totalOpportunities;
                        ?>
                    </h4>
                    <small class="text-muted">Supplier negotiations</small>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($data['competition_data'])): ?>
        <!-- No Competition Data -->
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                        <h4>No Competition Opportunities Found</h4>
                        <p class="text-muted">
                            No products were found with multiple suppliers having different prices.
                            <br>Add more suppliers to your products to enable price competition analysis.
                        </p>
                        <a href="<?php echo URLROOT; ?>/suppliers/link" class="btn btn-primary">
                            <i class="fas fa-link"></i> Link More Suppliers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Competition Analysis Results -->
        <div class="row">
            <div class="col-12">
                <div class="theme-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-trophy mr-2"></i>Competition Analysis Results
                        </h5>
                        <small class="text-muted">Products ranked by highest potential savings</small>
                    </div>
                    <div class="card-body">
                        <?php foreach ($data['competition_data'] as $index => $productData): ?>
                            <div class="competition-product-section mb-4 <?php echo $index < count($data['competition_data']) - 1 ? 'border-bottom pb-4' : ''; ?>">
                                <!-- Product Header -->
                                <div class="row align-items-center mb-3">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">
                                            <i class="fas fa-box text-primary mr-2"></i>
                                            <strong><?php echo htmlspecialchars($productData['product']->product_name); ?></strong>
                                        </h6>
                                        <small class="text-muted">SKU: <?php echo htmlspecialchars($productData['product']->sku); ?></small>
                                    </div>
                                    <div class="col-md-4 text-md-right">
                                        <span class="badge badge-success badge-lg">
                                            Potential Savings: ₹<?php echo number_format($productData['total_potential_savings'], 2); ?>
                                        </span>
                                    </div>
                                </div>

                                <!-- Current Suppliers Overview -->
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-2">Current Supplier Prices:</h6>
                                        <div class="row">
                                            <?php foreach ($productData['suppliers'] as $supplier): ?>
                                                <div class="col-md-4 mb-2">
                                                    <div class="supplier-price-card p-2 border rounded <?php echo $supplier->supplier_id == $productData['lowest_supplier']->supplier_id ? 'border-success theme-bg-secondary' : ''; ?>">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <strong class="text-primary"><?php echo htmlspecialchars($supplier->supplier_name); ?></strong>
                                                                <?php if ($supplier->supplier_id == $productData['lowest_supplier']->supplier_id): ?>
                                                                    <span class="badge badge-success badge-sm ml-1">Lowest</span>
                                                                <?php endif; ?>
                                                            </div>
                                                            <div class="text-right">
                                                                <strong class="<?php echo $supplier->supplier_id == $productData['lowest_supplier']->supplier_id ? 'text-success' : 'price-display'; ?>">
                                                                    ₹<?php echo number_format($supplier->purchase_price, 2); ?>
                                                                </strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Negotiation Opportunities -->
                                <?php if (!empty($productData['competition_opportunities'])): ?>
                                    <div class="negotiation-opportunities">
                                        <h6 class="text-warning mb-3">
                                            <i class="fas fa-handshake mr-2"></i>Negotiation Opportunities:
                                        </h6>
                                        
                                        <?php foreach ($productData['competition_opportunities'] as $opportunity): ?>
                                            <div class="opportunity-card bg-warning-light p-3 mb-2 rounded">
                                                <div class="row align-items-center">
                                                    <div class="col-md-3">
                                                        <strong class="text-primary"><?php echo htmlspecialchars($opportunity['supplier']->supplier_name); ?></strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?php if (!empty($opportunity['supplier']->contact_person)): ?>
                                                                Contact: <?php echo htmlspecialchars($opportunity['supplier']->contact_person); ?>
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <small class="text-muted">Current Price:</small><br>
                                                        <strong class="text-danger">₹<?php echo number_format($opportunity['current_price'], 2); ?></strong>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <small class="text-muted">Target Price:</small><br>
                                                        <div class="target-price-container">
                                                            <div class="input-group input-group-sm">
                                                                <div class="input-group-prepend">
                                                                    <span class="input-group-text">₹</span>
                                                                </div>
                                                                <input type="number" 
                                                                       class="form-control target-price-input" 
                                                                       step="0.01" 
                                                                       min="0"
                                                                       value="<?php echo number_format($opportunity['target_price'], 2, '.', ''); ?>"
                                                                       data-product-id="<?php echo $productData['product']->product_id; ?>"
                                                                       data-supplier-id="<?php echo $opportunity['supplier']->supplier_id; ?>"
                                                                       data-current-price="<?php echo $opportunity['current_price']; ?>"
                                                                       data-market-price="<?php echo $productData['lowest_supplier']->purchase_price; ?>">
                                                                <div class="input-group-append">
                                                                    <button class="btn btn-outline-primary btn-sm update-target-btn" type="button" title="Update Target Price">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                    <?php if (isset($opportunity['custom_target']) && $opportunity['custom_target']): ?>
                                                                        <button class="btn btn-outline-secondary btn-sm reset-target-btn" type="button" title="Reset to Market Price">
                                                                            <i class="fas fa-undo"></i>
                                                                        </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            <?php if (isset($opportunity['custom_target']) && $opportunity['custom_target']): ?>
                                                                <small class="text-info"><i class="fas fa-edit"></i> Custom target</small>
                                                            <?php else: ?>
                                                                <small class="text-muted"><i class="fas fa-chart-line"></i> Market lowest</small>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <small class="text-muted">Delivery Time:</small><br>
                                                        <div class="delivery-time-container">
                                                            <div class="input-group input-group-sm">
                                                                <input type="number" 
                                                                       class="form-control delivery-time-input" 
                                                                       step="1" 
                                                                       min="1"
                                                                       max="365"
                                                                       value="<?php echo isset($opportunity['supplier']->delivery_time) ? $opportunity['supplier']->delivery_time : '7'; ?>"
                                                                       data-product-id="<?php echo $productData['product']->product_id; ?>"
                                                                       data-supplier-id="<?php echo $opportunity['supplier']->supplier_id; ?>"
                                                                       placeholder="Days">
                                                                <div class="input-group-append">
                                                                    <span class="input-group-text">days</span>
                                                                    <button class="btn btn-outline-success btn-sm update-delivery-btn" type="button" title="Update Delivery Time">
                                                                        <i class="fas fa-truck"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <small class="text-muted"><i class="fas fa-clock"></i> Lead time</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 text-right">
                                                        <div class="savings-info">
                                                            <div class="savings-amount">
                                                                <strong class="text-success">Save ₹<?php echo number_format($opportunity['potential_savings'], 2); ?></strong>
                                                            </div>
                                                            <div class="savings-percentage">
                                                                <span class="badge badge-warning">
                                                                    <?php echo number_format($opportunity['savings_percentage'], 1); ?>% reduction
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Contact Information -->
                                                <div class="row mt-2">
                                                    <div class="col-12">
                                                        <div class="contact-actions">
                                                            <small class="text-muted">Contact Information:</small>
                                                            <?php if (!empty($opportunity['supplier']->email)): ?>
                                                                <a href="mailto:<?php echo $opportunity['supplier']->email; ?>?subject=Price Review Request - <?php echo urlencode($productData['product']->product_name); ?>&body=Dear <?php echo urlencode($opportunity['supplier']->contact_person ?: $opportunity['supplier']->supplier_name); ?>,%0A%0AI hope this email finds you well.%0A%0AAs part of our regular supplier price review, I wanted to reach out regarding <?php echo urlencode($productData['product']->product_name); ?> (SKU: <?php echo urlencode($productData['product']->sku); ?>).%0A%0ACurrent Details:%0A- Your current price: ₹<?php echo $opportunity['current_price']; ?>%0A- Market competitive price: ₹<?php echo $opportunity['target_price']; ?>%0A- Potential savings: ₹<?php echo number_format($opportunity['potential_savings'], 2); ?> (<?php echo number_format($opportunity['savings_percentage'], 1); ?>%25)%0A%0AWe value our long-term partnership and would prefer to continue sourcing this product from you. Would you be able to review your pricing and provide a competitive quote that matches or improves upon the market rate?%0A%0AAdditional considerations:%0A- We are open to discussing volume commitments%0A- Flexible payment terms if needed%0A- Long-term contract arrangements%0A%0APlease let me know your thoughts and if you would like to schedule a call to discuss this further.%0A%0AThank you for your continued partnership.%0A%0ABest regards" 
                                                                   class="btn btn-sm btn-outline-primary mr-2">
                                                                    <i class="fas fa-envelope"></i> Send Negotiation Email
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if (!empty($opportunity['supplier']->phone)): ?>
                                                                <span class="text-muted">
                                                                    <i class="fas fa-phone"></i> <?php echo htmlspecialchars($opportunity['supplier']->phone); ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Items Summary -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="theme-card">
                    <div class="card-header bg-info-theme text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-clipboard-list mr-2"></i>Action Items Summary
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Next Steps:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-envelope text-primary mr-2"></i>Send price matching requests to highlighted suppliers</li>
                                    <li><i class="fas fa-phone text-success mr-2"></i>Follow up with phone calls for high-value negotiations</li>
                                    <li><i class="fas fa-calendar text-warning mr-2"></i>Set negotiation deadlines and track responses</li>
                                    <li><i class="fas fa-chart-line text-info mr-2"></i>Update supplier prices based on negotiation results</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Tips for Successful Negotiations:</h6>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-handshake text-primary mr-2"></i>Emphasize long-term partnership value</li>
                                    <li><i class="fas fa-star text-warning mr-2"></i>Mention supplier rating and performance history</li>
                                    <li><i class="fas fa-boxes text-success mr-2"></i>Consider volume commitments for better prices</li>
                                    <li><i class="fas fa-clock text-info mr-2"></i>Be flexible on delivery terms if needed</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
@media print {
    .btn, .card-header { color-adjust: exact; }
    .bg-warning-light { background-color: #fff3cd !important; }
    .theme-card { border: 1px solid #ddd !important; }
}

.bg-warning-light {
    background-color: rgba(255, 193, 7, 0.1);
    border: 1px solid var(--warning);
}

/* Dark mode adjustments for warning background */
[data-theme="dark"] .bg-warning-light {
    background-color: rgba(255, 212, 59, 0.15);
    border: 1px solid var(--warning);
}

.supplier-price-card {
    transition: all 0.2s ease;
}

.supplier-price-card:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.opportunity-card {
    border: 1px solid var(--warning);
}

.savings-info {
    text-align: right;
}

.contact-actions {
    padding-top: 10px;
    border-top: 1px solid var(--card-border);
}

.competition-product-section:last-child {
    border-bottom: none !important;
    padding-bottom: 0 !important;
}

.target-price-container {
    max-width: 150px;
}

.target-price-input {
    font-size: 0.875rem;
}

.update-target-btn {
    border-left: none;
}

.savings-updating {
    opacity: 0.6;
    transition: opacity 0.3s ease;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle target price updates
    document.querySelectorAll('.update-target-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('.target-price-input');
            const productId = input.dataset.productId;
            const supplierId = input.dataset.supplierId;
            const currentPrice = parseFloat(input.dataset.currentPrice);
            const targetPrice = parseFloat(input.value);
            
            if (!targetPrice || targetPrice <= 0) {
                alert('Please enter a valid target price');
                return;
            }
            
            if (targetPrice >= currentPrice) {
                if (!confirm(`Target price (₹${targetPrice.toFixed(2)}) is not lower than current price (₹${currentPrice.toFixed(2)}). Continue anyway?`)) {
                    return;
                }
            }
            
            updateTargetPrice(productId, supplierId, targetPrice, input);
        });
    });

    // Handle delivery time updates
    document.querySelectorAll('.update-delivery-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('.delivery-time-input');
            const productId = input.dataset.productId;
            const supplierId = input.dataset.supplierId;
            const deliveryTime = parseInt(input.value);
            
            if (!deliveryTime || deliveryTime <= 0 || deliveryTime > 365) {
                alert('Please enter a valid delivery time (1-365 days)');
                return;
            }
            
            updateDeliveryTime(productId, supplierId, deliveryTime, input);
        });
    });

    // Allow Enter key to update delivery time
    document.querySelectorAll('.delivery-time-input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('.input-group').querySelector('.update-delivery-btn').click();
            }
        });
    });
    
    // Handle reset to market price
    document.querySelectorAll('.reset-target-btn').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('.target-price-input');
            const marketPrice = parseFloat(input.dataset.marketPrice);
            const productId = input.dataset.productId;
            const supplierId = input.dataset.supplierId;
            
            if (confirm(`Reset target price to market lowest (₹${marketPrice.toFixed(2)})?`)) {
                updateTargetPrice(productId, supplierId, marketPrice, input, true);
            }
        });
    });
    
    // Allow Enter key to update target price
    document.querySelectorAll('.target-price-input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                this.closest('.input-group').querySelector('.update-target-btn').click();
            }
        });
    });
});

function updateTargetPrice(productId, supplierId, targetPrice, inputElement, isReset = false) {
    const button = inputElement.closest('.input-group').querySelector('.update-target-btn');
    const originalButtonHTML = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('supplier_id', supplierId);
    formData.append('target_price', targetPrice);
    
    fetch(`${window.URLROOT}/suppliers/updateTargetPrice`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the input value to formatted price
            inputElement.value = parseFloat(targetPrice).toFixed(2);
            
            // Update the custom target indicator
            const container = inputElement.closest('.target-price-container');
            const indicator = container.querySelector('small');
            const resetBtn = container.querySelector('.reset-target-btn');
            
            if (isReset) {
                // Reset to market price
                indicator.innerHTML = '<i class="fas fa-chart-line"></i> Market lowest';
                indicator.className = 'text-muted';
                if (resetBtn) {
                    resetBtn.remove();
                }
            } else {
                // Custom target set
                indicator.innerHTML = '<i class="fas fa-edit"></i> Custom target';
                indicator.className = 'text-info';
                
                // Add reset button if not present
                if (!resetBtn) {
                    const newResetBtn = document.createElement('button');
                    newResetBtn.className = 'btn btn-outline-secondary btn-sm reset-target-btn';
                    newResetBtn.type = 'button';
                    newResetBtn.title = 'Reset to Market Price';
                    newResetBtn.innerHTML = '<i class="fas fa-undo"></i>';
                    
                    // Add event listener
                    newResetBtn.addEventListener('click', function() {
                        const input = this.closest('.input-group').querySelector('.target-price-input');
                        const marketPrice = parseFloat(input.dataset.marketPrice);
                        const productId = input.dataset.productId;
                        const supplierId = input.dataset.supplierId;
                        
                        if (confirm(`Reset target price to market lowest (₹${marketPrice.toFixed(2)})?`)) {
                            updateTargetPrice(productId, supplierId, marketPrice, input, true);
                        }
                    });
                    
                    button.parentNode.appendChild(newResetBtn);
                }
            }
            
            // Recalculate and update savings
            updateSavingsDisplay(inputElement, targetPrice);
            
            // Show success feedback
            button.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(() => {
                button.innerHTML = originalButtonHTML;
                button.disabled = false;
            }, 1500);
            
            showNotification(isReset ? 'Target price reset to market lowest' : 'Target price updated successfully', 'success');
        } else {
            throw new Error(data.message || 'Failed to update target price');
        }
    })
    .catch(error => {
        console.error('Error updating target price:', error);
        button.innerHTML = originalButtonHTML;
        button.disabled = false;
        showNotification(error.message || 'Failed to update target price', 'error');
    });
}

function updateSavingsDisplay(inputElement, newTargetPrice) {
    const currentPrice = parseFloat(inputElement.dataset.currentPrice);
    const newSavings = currentPrice - newTargetPrice;
    const newSavingsPercentage = ((newSavings / currentPrice) * 100);
    
    // Find the savings display elements in the same row
    const row = inputElement.closest('.row');
    const savingsAmountElement = row.querySelector('.savings-amount strong');
    const savingsPercentageElement = row.querySelector('.savings-percentage .badge');
    
    if (savingsAmountElement && savingsPercentageElement) {
        // Add updating class for visual feedback
        const savingsInfo = row.querySelector('.savings-info');
        savingsInfo.classList.add('savings-updating');
        
        setTimeout(() => {
            savingsAmountElement.textContent = `Save ₹${newSavings.toFixed(2)}`;
            savingsPercentageElement.textContent = `${newSavingsPercentage.toFixed(1)}% reduction`;
            
            // Update colors based on savings amount
            if (newSavings > 0) {
                savingsAmountElement.className = 'text-success';
                savingsPercentageElement.className = 'badge badge-warning';
            } else {
                savingsAmountElement.className = 'text-muted';
                savingsPercentageElement.className = 'badge badge-secondary';
            }
            
            savingsInfo.classList.remove('savings-updating');
        }, 300);
    }
}

function updateDeliveryTime(productId, supplierId, deliveryTime, inputElement) {
    const button = inputElement.closest('.input-group').querySelector('.update-delivery-btn');
    const originalButtonHTML = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    button.disabled = true;
    
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('supplier_id', supplierId);
    formData.append('delivery_time', deliveryTime);
    
    fetch(`${window.URLROOT}/suppliers/updateDeliveryTime`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the input value
            inputElement.value = deliveryTime;
            
            // Show success feedback
            button.innerHTML = '<i class="fas fa-check text-success"></i>';
            setTimeout(() => {
                button.innerHTML = originalButtonHTML;
                button.disabled = false;
            }, 1500);
            
            showNotification(`Delivery time updated to ${deliveryTime} days`, 'success');
        } else {
            throw new Error(data.message || 'Failed to update delivery time');
        }
    })
    .catch(error => {
        console.error('Error updating delivery time:', error);
        button.innerHTML = originalButtonHTML;
        button.disabled = false;
        showNotification(error.message || 'Failed to update delivery time', 'error');
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

function clearAllTargets() {
    if (!confirm('Clear all custom target prices and reset to market lowest? This action cannot be undone.')) {
        return;
    }
    
    fetch(`${window.URLROOT}/suppliers/clearTargetPrices`, {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('All custom target prices cleared successfully', 'success');
            // Reload page to reflect changes
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Failed to clear target prices');
        }
    })
    .catch(error => {
        console.error('Error clearing target prices:', error);
        showNotification(error.message || 'Failed to clear target prices', 'error');
    });
}
</script>

<?php
require APPROOT . DS . 'app' . DS . 'views' . DS . 'layouts' . DS . 'footer.php';
?>
