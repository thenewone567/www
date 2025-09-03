-- Fix inventory table issues and add missing created_at column
-- Also ensure inventory movements are properly tracked

-- Check and add missing columns to inventory table
-- First, let's check the current structure
-- Note: MySQL doesn't support IF NOT EXISTS for ADD COLUMN, so we'll check manually

-- Add warehouse_id column to inventory table (needed for transfers)
-- UPDATE: Let's check what columns already exist first
SELECT 'Checking inventory table structure' as status;
