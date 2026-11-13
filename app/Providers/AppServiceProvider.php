<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure crmid column exists on student_registration and fix any legacy CRMA_/CRMP_ IDs
        if (!app()->runningInConsole()) {
            \App\Services\CrmIdService::ensureColumnExists();
            \App\Services\CrmIdService::fixAllLegacyCrmIds();
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS discount NUMERIC(10,2) DEFAULT 0.00;");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS crmid VARCHAR(50);");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS usercode VARCHAR(50);");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS user_ip VARCHAR(45);");
            } catch (\Throwable $e) {}
        }
    }
}
