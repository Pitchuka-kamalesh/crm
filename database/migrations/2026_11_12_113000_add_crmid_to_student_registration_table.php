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
        DB::statement("ALTER TABLE crm.student_registration ADD COLUMN IF NOT EXISTS crmid VARCHAR(50);");
        DB::statement("CREATE INDEX IF NOT EXISTS idx_crm_student_registration_crmid ON crm.student_registration (crmid);");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe no-op to preserve assigned IDs
    }
};

