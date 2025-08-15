-- Migration: Add cancellation tracking fields to purchases table
-- Date: 2025-08-09
-- Description: Add fields to track cancellation reasons and post-cancellation actions

-- Add cancellation tracking columns to purchases table
ALTER TABLE `purchases` 
ADD COLUMN `cancellation_reason` ENUM('supplier_cancelled', 'out_of_stock', 'pricing_issue', 'business_decision', 'duplicate_order', 'supplier_issue', 'other') DEFAULT NULL COMMENT 'Reason for order cancellation',
ADD COLUMN `cancelled_action` ENUM('close_only', 'create_return', 'vendor_return', 'partial_cancel') DEFAULT NULL COMMENT 'Action to take after cancellation',
ADD COLUMN `custom_cancellation_reason` VARCHAR(255) DEFAULT NULL COMMENT 'Custom cancellation reason when other is selected',
ADD COLUMN `cancelled_at` TIMESTAMP NULL DEFAULT NULL COMMENT 'When the order was cancelled',
ADD COLUMN `cancelled_by` VARCHAR(255) DEFAULT NULL COMMENT 'Who cancelled the order';

-- Create index for better performance on cancellation queries
CREATE INDEX idx_purchases_cancellation ON purchases(cancellation_reason, cancelled_action);

-- Create trigger to automatically set cancelled_at and cancelled_by when status changes to cancelled
DELIMITER $$
CREATE TRIGGER `update_cancellation_timestamp` 
BEFORE UPDATE ON `purchases`
FOR EACH ROW 
BEGIN
    -- If status is being changed to cancelled
    IF NEW.status = 'cancelled' AND (OLD.status IS NULL OR OLD.status != 'cancelled') THEN
        SET NEW.cancelled_at = NOW();
        -- Try to get the current user from session or set to 'system'
        SET NEW.cancelled_by = COALESCE(@current_user_id, 'system');
    END IF;
    
    -- If status is being changed away from cancelled, clear cancellation timestamp
    IF OLD.status = 'cancelled' AND NEW.status != 'cancelled' THEN
        SET NEW.cancelled_at = NULL;
        SET NEW.cancelled_by = NULL;
    END IF;
END$$
DELIMITER ;

-- Update existing cancelled orders to have a cancelled_at timestamp
UPDATE purchases 
SET cancelled_at = updated_at, cancelled_by = 'system' 
WHERE status = 'cancelled' AND cancelled_at IS NULL;
