<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seedSqlPath = database_path('sql/vignan_master_data.sql');
        if (File::exists($seedSqlPath)) {
            $hasData = false;
            try {
                $hasData = DB::table('master.campuses')->exists();
            } catch (\Throwable $e) {}

            if (!$hasData) {
                DB::unprepared(File::get($seedSqlPath));
                if (isset($this->command)) {
                    $this->command->info('Vignan University master seed data imported successfully.');
                }
            } else {
                if (isset($this->command)) {
                    $this->command->info('Master tables already contain data, skipping seed.');
                }
            }
        }
    }
}
