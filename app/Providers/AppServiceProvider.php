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

            // Settle confirmed payment for candidate 2027000005 & sync stage1 in student_registration
            try {
                \Illuminate\Support\Facades\DB::table('crm.student_registration')
                    ->where('applinano', '2027000005')
                    ->update(['stage1' => 'paid']);

                if (!\Illuminate\Support\Facades\DB::table('finance.paydetails')->where('applino', '2027000005')->where('txnstatus', 'success')->exists()) {
                    $nextFeeId = (\Illuminate\Support\Facades\DB::table('finance.paydetails')->max('feeid') ?? 0) + 1;
                    \Illuminate\Support\Facades\DB::table('finance.paydetails')->insert([
                        'feeid' => $nextFeeId,
                        'applino' => '2027000005',
                        'feepayable' => 1200.00,
                        'defcoupon' => '27EARLYB100',
                        'defcouponamount' => 500.00,
                        'percoupon' => '27000005P1',
                        'percouponamount' => 500.00,
                        'totalcouponamount' => 1000.00,
                        'paidamount' => 200.00,
                        'paytype' => 'PayU (Online)',
                        'txncrmid' => 'TXN2611122027000005',
                        'txnpayuid' => 'PAYU2611122027000005',
                        'txnstatus' => 'success',
                        'txnremark' => 'Net Paid: ₹200.00 | Concessions: ₹1,000.00 (27EARLYB100 & 27000005P1)',
                        'txndatetime' => now(),
                        'log_timestamp' => now(),
                    ]);
                }

                if (!\Illuminate\Support\Facades\DB::table('finance.receiptdetails')->where('applino', '2027000005')->exists()) {
                    $nextRecId = (\Illuminate\Support\Facades\DB::table('finance.receiptdetails')->max('id') ?? 0) + 1;
                    \Illuminate\Support\Facades\DB::table('finance.receiptdetails')->insert([
                        'id' => $nextRecId,
                        'applino' => '2027000005',
                        'receiptno' => 'REC-2026-1112-0005',
                        'paytype' => 'PayU',
                        'feepayable' => 200.00,
                        'defcouponamount' => 500.00,
                        'percouponamount' => 500.00,
                        'totalcouponamount' => 1000.00,
                        'txndatetime' => now(),
                        'receiptdatetime' => now(),
                        'log_userid' => '2027000005',
                        'log_ipaddress' => '127.0.0.1',
                        'log_timestamp' => now(),
                    ]);
                }
            } catch (\Throwable $e) {}
        }

        // Ensure user Mani (31420) exists in database with roles: Tele Caller (4) & CRM Incharge (2)
        try {
            if (!\Illuminate\Support\Facades\DB::table('master.employees')->where('emp_code', '31420')->exists()) {
                \Illuminate\Support\Facades\DB::table('master.employees')->insert([
                    'emp_code' => '31420',
                    'full_name' => 'Mani',
                    'ofc_mailid' => 'mani@vignan.ac.in',
                    'personal_mailid' => 'mani@gmail.com',
                    'phone_primary' => '9876531420',
                    'phone_secondary' => '31420',
                    'campus_id' => '1',
                    'role_id' => 4,
                    'is_active' => true,
                    'date_of_join' => now()->toDateString(),
                    'log_userid' => 'SYSTEM',
                    'log_timestamp' => now(),
                ]);
            }

            if (!\Illuminate\Support\Facades\DB::table('master.login')->where('usercode', '31420')->exists()) {
                \Illuminate\Support\Facades\DB::table('master.login')->insert([
                    'usercode' => '31420',
                    'phnno' => '9876531420',
                    'user_type' => 2,
                    'role_id' => 4,
                    'campus_id' => '1',
                    'pwd' => \Illuminate\Support\Facades\Hash::make('root'),
                    'is_active' => true,
                    'log_userid' => 'SYSTEM',
                    'log_timestamp' => now(),
                ]);
            }

            // Role 4: Tele Caller
            if (!\Illuminate\Support\Facades\DB::table('master.roles_assigned')->where('emp_id', '31420')->where('roles_role_id', 4)->exists()) {
                \Illuminate\Support\Facades\DB::table('master.roles_assigned')->insert([
                    'emp_id' => '31420',
                    'campus_id' => 1,
                    'roles_role_id' => 4,
                    'start_date' => now(),
                    'log_userid' => 'SYSTEM',
                    'log_timestamp' => now(),
                ]);
            }

            // Role 2: CRM Incharge
            if (!\Illuminate\Support\Facades\DB::table('master.roles_assigned')->where('emp_id', '31420')->where('roles_role_id', 2)->exists()) {
                \Illuminate\Support\Facades\DB::table('master.roles_assigned')->insert([
                    'emp_id' => '31420',
                    'campus_id' => 1,
                    'roles_role_id' => 2,
                    'start_date' => now(),
                    'log_userid' => 'SYSTEM',
                    'log_timestamp' => now(),
                ]);
            }
        } catch (\Throwable $e) {}
    }
}
