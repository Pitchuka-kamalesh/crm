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
        DB::statement("ALTER TABLE crm.student_academic_details ADD COLUMN IF NOT EXISTS pg_study_type VARCHAR(50) DEFAULT 'Regular'");
        DB::statement("ALTER TABLE crm.student_academic_details ADD COLUMN IF NOT EXISTS pg2_study_type VARCHAR(50) DEFAULT 'Regular'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE crm.student_academic_details DROP COLUMN IF EXISTS pg_study_type");
        DB::statement("ALTER TABLE crm.student_academic_details DROP COLUMN IF EXISTS pg2_study_type");
    }
};
