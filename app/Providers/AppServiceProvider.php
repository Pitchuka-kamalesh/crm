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
        // Register Module View Namespaces
        $modules = ['Auth', 'Crm', 'Dashboards', 'Finance', 'Leads', 'Sms'];
        foreach ($modules as $module) {
            $viewPath = base_path("Modules/{$module}/resources/views");
            if (is_dir($viewPath)) {
                $this->loadViewsFrom($viewPath, strtolower($module));
            }
        }

        // Ensure crmid column exists on student_registration and fix any legacy CRMA_/CRMP_ IDs
        if (!app()->runningInConsole()) {
            \App\Services\CrmIdService::ensureColumnExists();
            \App\Services\CrmIdService::fixAllLegacyCrmIds();
            \App\Services\MenuSyncService::ensureKpiAndSmsMenus();
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS discount NUMERIC(10,2) DEFAULT 0.00;");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS crmid VARCHAR(50);");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS usercode VARCHAR(50);");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS user_ip VARCHAR(45);");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS applino VARCHAR(50);");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS couponsno VARCHAR(50);");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS coupcount INT DEFAULT 1;");
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE finance.couponinfo ADD COLUMN IF NOT EXISTS coupcurrcount INT DEFAULT 0;");
            } catch (\Throwable $e) {}
        }
    }
}
