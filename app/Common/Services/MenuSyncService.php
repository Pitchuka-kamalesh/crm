<?php

namespace App\Common\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MenuSyncService
{
    private static bool $hasRun = false;

    /**
     * Ensure KPI and SMS Tracker menus are cleanly separated and accurately reflected in DB.
     */
    public static function ensureKpiAndSmsMenus(): void
    {
        if (self::$hasRun) {
            return;
        }
        self::$hasRun = true;

        try {
            // 1. Remove Counseller KPI from master & crm tables
            DB::statement("DELETE FROM master.menu_access_roles WHERE menu_id = 127 OR menu_code = 'KPII0004';");
            DB::statement("DELETE FROM master.menus WHERE menu_id = 127 OR menu_code = 'KPII0004';");

            // 2. Disconnect SMS Tracker from KPI (give it its own unique code SMSM0001, null parent, mposition=1)
            DB::statement("
                UPDATE master.menus 
                SET parent_menu_id = NULL, 
                    menu_code = 'SMSM0001',
                    menu_name = 'SMS Tracker',
                    controller_ref = 'sms/index'
                WHERE menu_id = 117 OR controller_ref = 'sms/index';
            ");
            DB::statement("
                UPDATE master.menu_access_roles 
                SET menu_code = 'SMSM0001', 
                    mposition = 1 
                WHERE menu_id = 117;
            ");

            // 3. Ensure Student KPI (108) is correctly configured under KPI (103)
            DB::statement("
                UPDATE master.menus 
                SET parent_menu_id = 103, 
                    menu_name = 'Student KPI', 
                    controller_ref = 'kpi/studentkpi',
                    menu_code = 'KPII0002'
                WHERE menu_id = 108;
            ");
            DB::statement("
                UPDATE master.menu_access_roles
                SET menu_code = 'KPII0002',
                    mposition = 2
                WHERE menu_id = 108;
            ");

            // 4. Ensure Telecaller KPI (128) and Leads KPI (129) exist in master.menus
            DB::statement("
                INSERT INTO master.menus (
                    menu_id, menu_code, module_id, menu_name, controller_ref, icon_name,
                    is_collapsible, lock_status, project_id, campus_id, created_by, log_userid,
                    log_ipaddress, log_timestamp, parent_menu_id
                ) VALUES 
                (128, 'KPII0005', 4, 'Telecaller KPI', 'kpi/telecallerkpi', 'fa fa-phone', false, 0, 0, '1', '1', '02598', '192.168.111.111', NOW(), 103)
                ON CONFLICT (menu_id) DO UPDATE SET
                    menu_name = 'Telecaller KPI',
                    menu_code = 'KPII0005',
                    controller_ref = 'kpi/telecallerkpi',
                    parent_menu_id = 103;
            ");

            DB::statement("
                INSERT INTO master.menus (
                    menu_id, menu_code, module_id, menu_name, controller_ref, icon_name,
                    is_collapsible, lock_status, project_id, campus_id, created_by, log_userid,
                    log_ipaddress, log_timestamp, parent_menu_id
                ) VALUES 
                (129, 'KPII0006', 4, 'Leads KPI', 'kpi/leadskpi', 'fa fa-line-chart', false, 0, 0, '1', '1', '02598', '192.168.111.111', NOW(), 103)
                ON CONFLICT (menu_id) DO UPDATE SET
                    menu_name = 'Leads KPI',
                    menu_code = 'KPII0006',
                    controller_ref = 'kpi/leadskpi',
                    parent_menu_id = 103;
            ");

            // 5. Ensure access roles for Telecaller KPI (128) and Leads KPI (129) for roles 1, 2, 3, 4
            $roles = [
                ['role_id' => 1, 'order_start' => 14],
                ['role_id' => 2, 'order_start' => 16],
                ['role_id' => 3, 'order_start' => 14],
                ['role_id' => 4, 'order_start' => 5],
            ];

            foreach ($roles as $r) {
                $roleId = $r['role_id'];
                $o1 = $r['order_start'];
                $o2 = $o1 + 1;

                // Delete existing role assignments for 128 and 129
                DB::statement("DELETE FROM master.menu_access_roles WHERE menu_id IN (128, 129) AND role_id = {$roleId};");

                DB::statement("
                    INSERT INTO master.menu_access_roles 
                        (menu_code, menu_id, role_id, campus_id, morder, mposition, log_userid, log_ipaddress, log_timestamp)
                    VALUES 
                        ('KPII0005', 128, {$roleId}, '1', {$o1}, 2, '02598', '192.168.111.111', NOW()),
                        ('KPII0006', 129, {$roleId}, '1', {$o2}, 2, '02598', '192.168.111.111', NOW());
                ");
            }

            // 6. Sync with crm schema if crm.menu_access_roles exists
            $hasCrmTable = DB::select("SELECT 1 FROM information_schema.tables WHERE table_schema = 'crm' AND table_name = 'menu_access_roles'");
            if (!empty($hasCrmTable)) {
                DB::statement("DELETE FROM crm.menu_access_roles WHERE menu_id = 127 OR menu_code = 'KPII0004';");
                DB::statement("DELETE FROM crm.menu_access_roles WHERE menu_id IN (128, 129);");
                DB::statement("
                    INSERT INTO crm.menu_access_roles (menu_code, menu_id, role_id, campus_id, morder, mposition, log_userid, log_ipaddress, log_timestamp)
                    SELECT menu_code, menu_id, role_id, campus_id, morder, mposition, log_userid, log_ipaddress, log_timestamp
                    FROM master.menu_access_roles
                    WHERE menu_id IN (128, 129);
                ");
            }
        } catch (\Throwable $e) {
            // Silently continue if database or table is inaccessible
        }
    }
}

