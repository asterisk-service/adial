-- =====================================================
-- Database Migration: Add Timeout Fields to Campaigns
-- =====================================================
-- Version: 1.0
-- Date: 2025-12-29
-- Description: Add dial_timeout and call_timeout to campaigns table
-- Backward Compatibility: YES - defaults provided for existing records
-- =====================================================

-- Add dial_timeout column (default 30 seconds)
ALTER TABLE `campaigns`
ADD COLUMN `dial_timeout` INT(11) NOT NULL DEFAULT 30
COMMENT 'Time to wait for number to answer (seconds)' AFTER `retry_delay`;

-- Add call_timeout column (default 300 seconds = 5 minutes)
ALTER TABLE `campaigns`
ADD COLUMN `call_timeout` INT(11) NOT NULL DEFAULT 300
COMMENT 'Maximum conversation duration (seconds)' AFTER `dial_timeout`;

-- =====================================================
-- Verification Queries (run separately to confirm):
-- =====================================================
-- DESCRIBE campaigns;
-- SELECT id, name, dial_timeout, call_timeout FROM campaigns;
-- =====================================================
