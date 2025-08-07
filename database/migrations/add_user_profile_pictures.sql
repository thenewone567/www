-- Migration: Add profile picture support to users table
-- Date: 2025-08-01
-- Purpose: Add profile_picture field to enable user profile image uploads

-- Add profile_picture column to users table
ALTER TABLE `users` 
ADD COLUMN `profile_picture` VARCHAR(255) NULL AFTER `is_active`,
ADD COLUMN `name` VARCHAR(100) NULL AFTER `username`,
ADD COLUMN `email` VARCHAR(255) NULL AFTER `name`,
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `profile_picture`,
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Create index for better performance
CREATE INDEX idx_users_profile_picture ON users(profile_picture);

-- Add comments for documentation
ALTER TABLE `users` 
MODIFY COLUMN `profile_picture` VARCHAR(255) NULL COMMENT 'Path to user profile picture file in storage/uploads/users/';
