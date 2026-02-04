-- ================================================================
-- STEP BY STEP: Copy and Paste These Commands
-- ================================================================
-- Database: neko
-- Execute in your preferred MySQL client

-- ================================================================
-- Step 1: BACKUP (Optional - for safety)
-- ================================================================
-- If you want to keep old data, run these first:

-- CREATE TABLE job_templates_backup AS SELECT * FROM job_templates;
-- CREATE TABLE locked_fields_backup AS SELECT * FROM locked_fields;


-- ================================================================
-- Step 2: DROP & CREATE (CRITICAL)
-- ================================================================

-- Drop old table structure
DROP TABLE IF EXISTS job_templates;

-- Create fresh job_templates table
CREATE TABLE job_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    name VARCHAR(255) NOT NULL,
    client_id BIGINT UNSIGNED NULL,
    pickup_address_id BIGINT UNSIGNED NULL,
    pickup_time_begin DATETIME NULL,
    pickup_time_end DATETIME NULL,
    template_data JSON NULL,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    INDEX idx_name (name),
    INDEX idx_client_id (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- Step 3: ENSURE locked_fields TABLE (if not exists)
-- ================================================================

CREATE TABLE IF NOT EXISTS locked_fields (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    field_name VARCHAR(255) NOT NULL,
    is_locked BOOLEAN DEFAULT TRUE,
    model VARCHAR(255) NULL,
    model_id BIGINT UNSIGNED NULL,
    INDEX idx_model_id (model, model_id),
    INDEX idx_field_name (field_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- Step 4: ENSURE jobs TABLE HAS job_template_id (if not already)
-- ================================================================

-- Add column
ALTER TABLE jobs ADD COLUMN IF NOT EXISTS job_template_id BIGINT UNSIGNED NULL;

-- Add foreign key
ALTER TABLE jobs ADD CONSTRAINT IF NOT EXISTS jobs_job_template_id_foreign 
    FOREIGN KEY (job_template_id) REFERENCES job_templates(id) ON DELETE SET NULL;


-- ================================================================
-- VERIFY: Run these to confirm everything worked
-- ================================================================

-- Check job_templates structure (should show: id, created_at, updated_at, name, client_id, pickup_address_id, pickup_time_begin, pickup_time_end, template_data)
DESCRIBE job_templates;

-- Check locked_fields structure
DESCRIBE locked_fields;

-- Check jobs has job_template_id
DESCRIBE jobs;

-- See all current templates (should be empty)
SELECT * FROM job_templates;

-- See all locked fields for templates (should be empty)
SELECT * FROM locked_fields WHERE model = 'job_template';
