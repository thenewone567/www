-- Migration: Add currency column to companies table
-- Purpose: store per-company currency (e.g., INR, USD) in companies table
-- IMPORTANT: Run on staging first. For very large tables consider running during maintenance window.

ALTER TABLE `companies`
ADD COLUMN IF NOT EXISTS `currency` VARCHAR(10) DEFAULT 'INR' AFTER `email`;

-- Notes:
-- - Default is set to 'INR' to match the project's Indian currency defaults. Change as required.
-- - If your MySQL version doesn't support IF NOT EXISTS for ADD COLUMN, run a check first:
--   SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'companies' AND COLUMN_NAME = 'currency';
--   Then run the ALTER TABLE without IF NOT EXISTS if not present.

-- Recommended safe index if you query by company and currency (optional):
-- CREATE INDEX idx_company_currency ON companies (company_id, currency);
