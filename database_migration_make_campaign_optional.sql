-- =====================================================
-- Database Migration: Make campaign_id Optional for IVR Menus
-- =====================================================
-- Version: 1.0
-- Date: 2025-12-29
-- Description: Allow IVR menus to exist independently without campaigns
-- Backward Compatibility: YES - all existing records remain unchanged
-- =====================================================

-- Step 1: Disable foreign key checks for this migration
SET @OLD_FOREIGN_KEY_CHECKS = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 0;

-- Step 2: Modify the column to allow NULL values
ALTER TABLE `ivr_menus`
MODIFY COLUMN `campaign_id` INT(11) DEFAULT NULL
COMMENT 'Campaign ID (optional) - NULL for standalone IVR menus';

-- Step 3: Drop existing foreign key constraint
ALTER TABLE `ivr_menus`
DROP FOREIGN KEY `ivr_menus_ibfk_1`;

-- Step 4: Add new foreign key constraint with ON DELETE SET NULL
-- This allows campaigns to be deleted without removing their IVR menus
ALTER TABLE `ivr_menus`
ADD CONSTRAINT `ivr_menus_ibfk_1`
FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE SET NULL;

-- Step 5: Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = @OLD_FOREIGN_KEY_CHECKS;

-- =====================================================
-- Verification Queries (run separately to confirm migration success):
-- =====================================================
-- SELECT COUNT(*) as total_ivr_menus FROM ivr_menus;
-- SELECT COUNT(*) as standalone_ivr_menus FROM ivr_menus WHERE campaign_id IS NULL;
-- DESCRIBE ivr_menus;
-- SHOW CREATE TABLE ivr_menus\G
-- =====================================================
