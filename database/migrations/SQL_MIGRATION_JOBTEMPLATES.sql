-- ================================================================
-- JobTemplate Rebuild - SQL Migration Commands
-- ================================================================
-- Since we cannot run migrate:fresh, execute these SQL commands
-- to update your database schema

-- ================================================================
-- 1. BACKUP (Run these first to preserve existing data if needed)
-- ================================================================

-- Create backup of old job_templates table (optional, for safety)
-- CREATE TABLE job_templates_backup AS SELECT * FROM job_templates;
-- CREATE TABLE locked_fields_backup AS SELECT * FROM locked_fields;


-- ================================================================
-- 2. DROP OLD CONSTRAINTS
-- ================================================================

-- If foreign keys exist from jobs table
-- ALTER TABLE jobs DROP FOREIGN KEY jobs_job_template_id_foreign;


-- ================================================================
-- 3. DROP OLD TABLE
-- ================================================================

DROP TABLE IF EXISTS job_templates;


-- ================================================================
-- 4. CREATE NEW job_templates TABLE (FRESH SCHEMA)
-- ================================================================

CREATE TABLE job_templates (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    name VARCHAR(255) NOT NULL,
    client_id BIGINT UNSIGNED NULL,
    pickup_address_id BIGINT UNSIGNED NULL,
    pickup_time_begin DATETIME NULL,
    pickup_time_end DATETIME NULL,
    template_data JSON NULL COMMENT 'Stores: {pickup, dropoffs, return}',
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    INDEX idx_name (name),
    INDEX idx_client_id (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- 5. ENSURE locked_fields TABLE EXISTS
-- ================================================================

-- Check if locked_fields table exists, if not create it
CREATE TABLE IF NOT EXISTS locked_fields (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    field_name VARCHAR(255) NOT NULL,
    is_locked BOOLEAN DEFAULT TRUE,
    model VARCHAR(255) NULL COMMENT 'job or job_template',
    model_id BIGINT UNSIGNED NULL,
    INDEX idx_model_id (model, model_id),
    INDEX idx_field_name (field_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ================================================================
-- 6. CLEAN UP locked_fields (optional - remove old entries)
-- ================================================================

-- Delete locked fields for old job_template entries
-- DELETE FROM locked_fields WHERE model = 'job_template';


-- ================================================================
-- 7. UPDATE jobs TABLE (ensure job_template_id foreign key)
-- ================================================================

-- Add column if it doesn't exist
ALTER TABLE jobs ADD COLUMN IF NOT EXISTS job_template_id BIGINT UNSIGNED NULL;

-- Add foreign key if it doesn't exist
ALTER TABLE jobs ADD CONSTRAINT IF NOT EXISTS jobs_job_template_id_foreign 
    FOREIGN KEY (job_template_id) REFERENCES job_templates(id) ON DELETE SET NULL;


-- ================================================================
-- 8. VERIFICATION QUERIES
-- ================================================================

-- Check job_templates structure
-- DESCRIBE job_templates;

-- Check locked_fields structure
-- DESCRIBE locked_fields;

-- Check jobs table job_template_id column
-- DESCRIBE jobs;

-- Show all templates
-- SELECT id, name, client_id, created_at FROM job_templates;

-- Show all locked fields
-- SELECT * FROM locked_fields WHERE model = 'job_template';
