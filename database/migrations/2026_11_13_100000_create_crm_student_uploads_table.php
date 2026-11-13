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
            CREATE TABLE IF NOT EXISTS crm.student_uploads (
                id BIGSERIAL PRIMARY KEY,
                crmid VARCHAR(50),
                applino VARCHAR(50) NOT NULL,
                phoneno VARCHAR(20) NOT NULL,

                tenth_file_name VARCHAR(255),
                tenth_file_path VARCHAR(500),

                inter_file_name VARCHAR(255),
                inter_file_path VARCHAR(500),

                btech_file_name VARCHAR(255),
                btech_file_path VARCHAR(500),

                mtech_file_name VARCHAR(255),
                mtech_file_path VARCHAR(500),

                pg_file_name VARCHAR(255),
                pg_file_path VARCHAR(500),

                aadhar_file_name VARCHAR(255),
                aadhar_file_path VARCHAR(500),

                passport_file_name VARCHAR(255),
                passport_file_path VARCHAR(500),

                transfer_file_name VARCHAR(255),
                transfer_file_path VARCHAR(500),

                study_conduct_file_name VARCHAR(255),
                study_conduct_file_path VARCHAR(500),

                log_userid VARCHAR(50),
                log_ipaddress VARCHAR(255),
                log_timestamp TIMESTAMP DEFAULT now(),

                CONSTRAINT uq_student_uploads_applino_phone UNIQUE (applino, phoneno)
            );

            CREATE INDEX IF NOT EXISTS idx_student_uploads_crmid ON crm.student_uploads (crmid);
            CREATE INDEX IF NOT EXISTS idx_student_uploads_applino ON crm.student_uploads (applino);
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TABLE IF EXISTS crm.student_uploads CASCADE;");
    }
};

