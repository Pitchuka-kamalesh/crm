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
        DB::statement("
            ALTER TABLE crm.student_academic_details
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
                ALTER COLUMN pg_percentage TYPE NUMERIC(10,2),
                ALTER COLUMN pg2_marks TYPE NUMERIC(12,2),
                ALTER COLUMN pg2_marks_conducted TYPE NUMERIC(12,2),
                ALTER COLUMN pg2_marks_earned TYPE NUMERIC(12,2),
                ALTER COLUMN pg2_percentage TYPE NUMERIC(10,2);
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE crm.student_academic_details
                ALTER COLUMN pu2_marks TYPE NUMERIC(7,2),
                ALTER COLUMN pu2_marks_conducted TYPE NUMERIC(7,2),
                ALTER COLUMN pu2_marks_earned TYPE NUMERIC(7,2),
                ALTER COLUMN pu2_percentage TYPE NUMERIC(5,2),
                ALTER COLUMN ug_marks TYPE NUMERIC(7,2),
                ALTER COLUMN ug_marks_conducted TYPE NUMERIC(7,2),
                ALTER COLUMN ug_marks_earned TYPE NUMERIC(7,2),
                ALTER COLUMN ug_percentage TYPE NUMERIC(5,2),
                ALTER COLUMN pg_marks TYPE NUMERIC(7,2),
                ALTER COLUMN pg_marks_conducted TYPE NUMERIC(7,2),
                ALTER COLUMN pg_marks_earned TYPE NUMERIC(7,2),
                ALTER COLUMN pg_percentage TYPE NUMERIC(5,2),
                ALTER COLUMN pg2_marks TYPE NUMERIC(10,2),
                ALTER COLUMN pg2_marks_conducted TYPE NUMERIC(10,2),
                ALTER COLUMN pg2_marks_earned TYPE NUMERIC(10,2),
                ALTER COLUMN pg2_percentage TYPE NUMERIC(5,2);
        ");
    }
};
