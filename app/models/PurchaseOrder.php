<?php
/**
 * Backwards compatibility wrapper for older code referencing PurchaseOrder
 * Delegates to the unified Purchase model.
 */
require_once APPROOT . DS . 'app' . DS . 'models' . DS . 'Purchase.php';

class PurchaseOrder extends Purchase
{
    // Intentionally empty: inherit everything from Purchase model
}

?>