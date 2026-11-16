<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $seedSqlPath = database_path('sql/vignan_master_data.sql');
        if (File::exists($seedSqlPath)) {
            $hasData = false;
            try {
                $hasData = \App\Models\Campus::exists();
            } catch (\Throwable $e) {}

            if (!$hasData) {
                DB::unprepared(File::get($seedSqlPath));
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retain master data
    }
};

