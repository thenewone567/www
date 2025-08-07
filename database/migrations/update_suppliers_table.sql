-- Migration: Update suppliers table for Indian business requirements
-- Date: 2025-08-02
-- Description: Replace generic contact_info with specific contact fields for Indian suppliers

-- First, let's add the new columns
ALTER TABLE `suppliers` 
ADD COLUMN `contact_person` VARCHAR(100) NULL AFTER `supplier_name`,
ADD COLUMN `phone` VARCHAR(20) NULL AFTER `contact_person`,
ADD COLUMN `email` VARCHAR(100) NULL AFTER `phone`,
ADD COLUMN `address` TEXT NULL AFTER `email`,
ADD COLUMN `gst_number` VARCHAR(15) NULL AFTER `address`;

-- Migrate existing data from old columns to new columns (if any data exists)
-- Move contact_info to phone field (assuming most contact_info was phone numbers)
UPDATE `suppliers` SET `phone` = `contact_info` WHERE `contact_info` IS NOT NULL AND `contact_info` != '';

-- Move gst_info to gst_number field
UPDATE `suppliers` SET `gst_number` = `gst_info` WHERE `gst_info` IS NOT NULL AND `gst_info` != '';

-- Drop the old columns that are no longer needed
ALTER TABLE `suppliers` 
DROP COLUMN `contact_info`,
DROP COLUMN `gst_info`,
DROP COLUMN `due_amount`;

-- Add indexes for better performance
ALTER TABLE `suppliers`
ADD INDEX `idx_supplier_gst` (`gst_number`),
ADD INDEX `idx_supplier_email` (`email`);

-- Update the primary key constraint (if needed)
ALTER TABLE `suppliers`
MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT;
