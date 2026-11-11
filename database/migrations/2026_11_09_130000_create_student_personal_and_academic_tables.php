<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("
            ALTER TABLE crm.student_registration DROP CONSTRAINT IF EXISTS pk_student_registration CASCADE;
            ALTER TABLE crm.student_registration DROP CONSTRAINT IF EXISTS pk_student_registration_composite CASCADE;
            ALTER TABLE crm.student_registration ALTER COLUMN phoneno SET NOT NULL;
            ALTER TABLE crm.student_registration ADD CONSTRAINT pk_student_registration_composite PRIMARY KEY (applinano, phoneno);
        ");

        DB::unprepared("
            CREATE TABLE IF NOT EXISTS crm.student_personal_info (
                crmid VARCHAR(50) PRIMARY KEY,
                applino VARCHAR(20) NOT NULL,
                phoneno VARCHAR(20) NOT NULL,
                dob DATE,
                gender VARCHAR(20),
                parent_name VARCHAR(255),
                parentphono VARCHAR(20),
                caste_category VARCHAR(50),
                referedby VARCHAR(255),
                referedempcode VARCHAR(50),
                country VARCHAR(100),
                state VARCHAR(100),
                district VARCHAR(100),
                city_mandal VARCHAR(100),
                village VARCHAR(100),
                pincode VARCHAR(10),
                log_userid VARCHAR(50), 
                log_ipaddress VARCHAR(255),
                log_timestamp TIMESTAMP DEFAULT now(),
                CONSTRAINT fk_student_info_registration 
                    FOREIGN KEY (applino, phoneno) 
                    REFERENCES crm.student_registration (applinano, phoneno) 
                    ON DELETE CASCADE
            );
        ");

        DB::unprepared("
            CREATE TABLE IF NOT EXISTS crm.student_academic_details (
                crmid VARCHAR(50), 
                applino VARCHAR(20) NOT NULL,
                phoneno VARCHAR(20) NOT NULL,
                progmid VARCHAR(50),
                branchid INT4,

                tenth_school_name VARCHAR(255),
                tenth_study_board VARCHAR(100),
                tenth_school_city VARCHAR(100),
                tenth_school_district VARCHAR(100),
                tenth_roll_no VARCHAR(50),
                tenth_passed_year INT4,
                tenth_marks_type VARCHAR(50),
                tenth_marks NUMERIC(5,2),
                tenth_cgpa NUMERIC(4,2),

                pu2_college_name VARCHAR(255),
                pu2_study_board VARCHAR(100),
                pu2_college_city VARCHAR(100),
                pu2_college_district VARCHAR(100),
                pu2_roll_no VARCHAR(50),
                pu2_passed_year INT4,
                pu2_marks_type VARCHAR(50),
                pu2_marks NUMERIC(5,2),
                pu2_cgpa NUMERIC(4,2),
                pu2_current_status VARCHAR(100),

                ug_college_name VARCHAR(255),
                ug_university_board VARCHAR(100),
                ug_college_city VARCHAR(100),
                ug_college_district VARCHAR(100),
                ug_roll_no VARCHAR(50),
                ug_passed_year INT4,
                ug_marks_type VARCHAR(50),
                ug_marks NUMERIC(5,2),
                ug_cgpa NUMERIC(4,2),
                ug_current_status VARCHAR(100),

                pg_college_name VARCHAR(255),
                pg_university_board VARCHAR(100),
                pg_college_city VARCHAR(100),
                pg_college_district VARCHAR(100),
                pg_roll_no VARCHAR(50),
                pg_passed_year INT4,
                pg_marks_type VARCHAR(50),
                pg_marks NUMERIC(5,2),
                pg_cgpa NUMERIC(4,2),
                pg_current_status VARCHAR(100),

                log_userid VARCHAR(50),
                log_datetime TIMESTAMP DEFAULT now(),
                log_ipaddress VARCHAR(255),

                CONSTRAINT pk_student_academic PRIMARY KEY (applino, phoneno),
                CONSTRAINT fk_academic_registration 
                    FOREIGN KEY (applino, phoneno) 
                    REFERENCES crm.student_registration (applinano, phoneno) 
                    ON DELETE CASCADE
            );
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP TABLE IF EXISTS crm.student_academic_details CASCADE;");
        DB::statement("DROP TABLE IF EXISTS crm.student_personal_info CASCADE;");
    }
};

