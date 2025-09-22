# Putaway Scanner - Partial Quantities Support

## Overview

Enhanced the putaway scanner to support partial quantities, allowing users to put away only a portion of received items while keeping track of remaining quantities.

## Key Features Implemented

### 🎯 **Partial Quantity Processing**

- ✅ **Flexible Quantities**: Users can put away any amount up to the available quantity
- ✅ **Queue Management**: Remaining items stay in the putaway queue
- ✅ **Accurate Tracking**: System tracks total received, already put away, and remaining quantities

### 📊 **Enhanced Data Display**

- ✅ **Available Quantity**: Shows exact number of units available for putaway
- ✅ **PO Information**: Displays purchase order details and quantities
- ✅ **Remaining Alerts**: Clear notification when items still need putaway
- ✅ **Putaway Status**: Visual indicators for completion status

### 🔄 **Workflow Improvements**

- ✅ **Continue Option**: Button to continue with remaining quantities
- ✅ **Smart Defaults**: Auto-fills remaining quantity when continuing
- ✅ **Validation Messages**: Helpful hints about partial quantities

## Technical Implementation

### **Backend Changes**

#### **API: processPutaway.php**

```php
// Enhanced response with remaining quantity data
'data' => [
    'product_name' => $product->product_name,
    'quantity' => $quantity,
    'location' => $location->location_name,
    'location_code' => $locationCode,
    'remaining_quantity' => $remainingInQueue,
    'has_remaining' => $remainingInQueue > 0,
    'total_received' => $pendingItem ? $pendingItem->received_quantity : 0,
    'total_putaway' => $pendingItem ? ($pendingItem->putaway_quantity + $quantity) : $quantity
]
```

#### **API: itemLookup.php**

```php
// Enhanced query to check putaway queue
SELECT
    pi.received_quantity,
    COALESCE(pi.putaway_quantity, 0) as putaway_quantity,
    (pi.received_quantity - COALESCE(pi.putaway_quantity, 0)) as available_quantity,
    po.po_number,
    l.location_name as receiving_location_name
FROM purchase_items pi
JOIN purchases po ON pi.purchase_id = po.purchase_id
WHERE pi.product_id = ?
AND pi.received_quantity > COALESCE(pi.putaway_quantity, 0)
```

### **Frontend Enhancements**

#### **Enhanced Completion Display**

```javascript
// Show remaining quantity alerts and continuation options
if (data.data.has_remaining && data.data.remaining_quantity > 0) {
  remainingMessage = `
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Remaining:</strong> ${data.data.remaining_quantity} units still need to be put away
        </div>
    `;

  actionButtons = `
        <button class="btn btn-warning" onclick="continueWithRemaining()">
            <i class="fas fa-arrow-right"></i> Put Away Remaining (${data.data.remaining_quantity})
        </button>
    `;
}
```

#### **Continue with Remaining Function**

```javascript
function continueWithRemaining() {
  // Pre-fill form with remaining item data
  document.getElementById("item-scan").value =
    window.remainingPutawayData.item_barcode;
  document.getElementById("putaway-quantity").value =
    window.remainingPutawayData.remaining_quantity;

  // Look up item again and show continuation message
  lookupItem(window.remainingPutawayData.item_barcode);
}
```

## User Experience Example

### **Scenario: 26 Bulbs Received, Only 6 Available to Put Away**

1. **Step 1**: User scans bulb barcode

   - System shows: "Available: 26 units"
   - Displays PO information and receiving details

2. **Step 2**: User scans storage location

   - Location validated and confirmed

3. **Step 3**: User enters quantity

   - Changes quantity from 26 to 6
   - System validates: "You can put away any amount up to 26 units"

4. **Step 4**: Putaway completed
   - Success message: "6 units stored at A1-B2"
   - **Warning alert**: "Remaining: 20 units still need to be put away"
   - Two action buttons:
     - "Put Away Remaining (20)" - continues with same item
     - "Put Away Different Item" - starts fresh workflow

### **Benefits for Users**

1. **🎯 Flexibility**: Can put away partial quantities based on available storage
2. **📋 Clarity**: Always knows how many items are remaining
3. **⚡ Efficiency**: Quick continuation for remaining quantities
4. **🔍 Visibility**: Clear tracking of putaway progress

### **Database Impact**

- **purchase_items.putaway_quantity**: Accurately tracks partial putaways
- **inventory**: Updates with actual quantities moved to storage
- **inventory_movements**: Records all putaway transactions with proper quantities

## Testing Recommendations

1. **Partial Putaway Test**: Receive 10 items, put away 3, verify 7 remain
2. **Multiple Partial Test**: Put away in multiple batches (3, then 4, then 3)
3. **Continuation Test**: Use "Continue with Remaining" button functionality
4. **Complete Putaway Test**: Ensure no remaining alert when fully complete

## Future Enhancements

- **Bulk Partial Putaway**: Handle multiple items with partial quantities
- **Location Splitting**: Put remaining quantities to different locations
- **Priority Alerts**: Highlight items that have been partially put away for too long
- **Reporting**: Dashboard showing partial putaway statistics

---

**Date Implemented**: September 10, 2025  
**Status**: ✅ Complete and Tested  
**Impact**: Significantly improved putaway flexibility and user experience
