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
            ALTER TABLE crm.leads ADD COLUMN IF NOT EXISTS empcode VARCHAR(50) DEFAULT '0';
            ALTER TABLE crm.leads ADD COLUMN IF NOT EXISTS allocated_at TIMESTAMP NULL;
            UPDATE crm.leads SET empcode = '0' WHERE empcode IS NULL;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            ALTER TABLE crm.leads DROP COLUMN IF EXISTS allocated_at;
            ALTER TABLE crm.leads DROP COLUMN IF EXISTS empcode;
        ");
    }
};

