-- ====================================================================
-- VFSTR Admissions CRM - Schema Update & Sync Script
-- Target Database: vu_crm26 | Schema: crm
-- Date: 18-Nov-2026
-- Description:
--   1. Adds all academic columns (PG2, States, Board of Study, Status).
--   2. Expands numeric precision to NUMERIC(12,2) to avoid overflow on big numbers.
--   3. Adds experience letter, PG1/PG2 CMM, PC/OD, and Doctoral file upload columns.
--   4. Creates high-performance index for bulk leads auto-allocation.
-- ====================================================================

-- 1. Academic Details Table Upgrades
ALTER TABLE crm.student_academic_details
    ADD COLUMN IF NOT EXISTS tenth_school_state VARCHAR(100),
    ADD COLUMN IF NOT EXISTS tenth_current_status VARCHAR(50),
    ADD COLUMN IF NOT EXISTS pu2_board_of_study VARCHAR(255),
    ADD COLUMN IF NOT EXISTS ug_college_state VARCHAR(100),
    ADD COLUMN IF NOT EXISTS pg_college_state VARCHAR(100),
    ADD COLUMN IF NOT EXISTS pg2_college_name VARCHAR(255),
    ADD COLUMN IF NOT EXISTS pg2_university_board VARCHAR(255),
    ADD COLUMN IF NOT EXISTS pg2_college_city VARCHAR(100),
    ADD COLUMN IF NOT EXISTS pg2_college_district VARCHAR(100),
    ADD COLUMN IF NOT EXISTS pg2_college_state VARCHAR(100),
    ADD COLUMN IF NOT EXISTS pg2_roll_no VARCHAR(100),
    ADD COLUMN IF NOT EXISTS pg2_passed_year INT,
    ADD COLUMN IF NOT EXISTS pg2_marks_type VARCHAR(50),
    ADD COLUMN IF NOT EXISTS pg2_marks NUMERIC(12,2),
    ADD COLUMN IF NOT EXISTS pg2_marks_conducted NUMERIC(12,2),
    ADD COLUMN IF NOT EXISTS pg2_marks_earned NUMERIC(12,2),
    ADD COLUMN IF NOT EXISTS pg2_percentage NUMERIC(10,2),
    ADD COLUMN IF NOT EXISTS pg2_current_status VARCHAR(50),
    ADD COLUMN IF NOT EXISTS pg1_doc_type VARCHAR(50),
    ADD COLUMN IF NOT EXISTS pg2_doc_type VARCHAR(50);

-- Prevent numeric overflow when large test numbers or marks are inputted
ALTER TABLE crm.student_academic_details
    ALTER COLUMN tenth_marks_conducted TYPE NUMERIC(12,2),
    ALTER COLUMN tenth_marks_earned TYPE NUMERIC(12,2),
    ALTER COLUMN pu2_marks TYPE NUMERIC(12,2),
    ALTER COLUMN pu2_marks_conducted TYPE NUMERIC(12,2),
    ALTER COLUMN pu2_marks_earned TYPE NUMERIC(12,2),
    ALTER COLUMN pu2_percentage TYPE NUMERIC(10,2),
    ALTER COLUMN ug_marks TYPE NUMERIC(12,2),
    ALTER COLUMN ug_marks_conducted TYPE NUMERIC(12,2),
    ALTER COLUMN ug_marks_earned TYPE NUMERIC(12,2),
    ALTER COLUMN ug_percentage TYPE NUMERIC(10,2),
    ALTER COLUMN pg_marks TYPE NUMERIC(12,2),
    ALTER COLUMN pg_marks_conducted TYPE NUMERIC(12,2),
    ALTER COLUMN pg_marks_earned TYPE NUMERIC(12,2),
    ALTER COLUMN pg_percentage TYPE NUMERIC(10,2);

-- 2. Student Uploads Table Upgrades (Experience Letter, PG1/PG2 CMM & PC/OD, Doctoral)
ALTER TABLE crm.student_uploads
    ADD COLUMN IF NOT EXISTS experience_file_name VARCHAR(255),
    ADD COLUMN IF NOT EXISTS experience_file_path VARCHAR(500),
    ADD COLUMN IF NOT EXISTS pg1_cmm_file_name VARCHAR(255),
    ADD COLUMN IF NOT EXISTS pg1_cmm_file_path VARCHAR(500),
    ADD COLUMN IF NOT EXISTS pg1_pc_file_name VARCHAR(255),
    ADD COLUMN IF NOT EXISTS pg1_pc_file_path VARCHAR(500),
    ADD COLUMN IF NOT EXISTS pg2_cmm_file_name VARCHAR(255),
    ADD COLUMN IF NOT EXISTS pg2_cmm_file_path VARCHAR(500),
    ADD COLUMN IF NOT EXISTS pg2_pc_file_name VARCHAR(255),
    ADD COLUMN IF NOT EXISTS pg2_pc_file_path VARCHAR(500),
    ADD COLUMN IF NOT EXISTS doctoral_file_name VARCHAR(255),
    ADD COLUMN IF NOT EXISTS doctoral_file_path VARCHAR(500);

-- 3. High-Performance Partial Index for 100k+ Leads Auto-Allocation
CREATE INDEX IF NOT EXISTS idx_leads_unallocated 
    ON crm.leads (allocatedto, status) 
    WHERE allocatedto IS NULL;
