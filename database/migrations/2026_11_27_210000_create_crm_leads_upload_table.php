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
            CREATE TABLE IF NOT EXISTS crm.leads_upload (
                id BIGSERIAL PRIMARY KEY,
                file_name VARCHAR(255) NOT NULL,
                file_size VARCHAR(50),
                total_records INTEGER DEFAULT 0,
                new_leads_added INTEGER DEFAULT 0,
                duplicates_skipped INTEGER DEFAULT 0,
                upload_status VARCHAR(50) DEFAULT 'Completed',
                uploaded_by VARCHAR(50),
                upload_date_time TIMESTAMP WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP
            );

            CREATE OR REPLACE VIEW crm.leads_uploads AS SELECT * FROM crm.leads_upload;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            DROP VIEW IF EXISTS crm.leads_uploads;
            DROP TABLE IF EXISTS crm.leads_upload;
        ");
    }
};
