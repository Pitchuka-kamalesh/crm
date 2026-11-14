<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\Employee;
use App\Models\User;
use App\Models\RoleAssigned;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // 1. Insert into master.employees
            Employee::updateOrCreate(
                ['emp_code' => '31420'],
                [
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
                ]
            );

            // 2. Insert into master.login
            User::updateOrCreate(
                ['usercode' => '31420'],
                [
                    'phnno' => '9876531420',
                    'user_type' => 2,
                    'role_id' => 4,
                    'campus_id' => '1',
                    'pwd' => Hash::make('root'),
                    'is_active' => true,
                    'log_userid' => 'SYSTEM',
                    'log_timestamp' => now(),
                ]
            );

            // 3. Assign Role 4 (Tele Caller) in master.roles_assigned
            RoleAssigned::updateOrCreate(
                ['emp_id' => '31420', 'roles_role_id' => 4],
                [
                    'campus_id' => 1,
                    'start_date' => now(),
                    'log_userid' => 'SYSTEM',
                    'log_timestamp' => now(),
                ]
            );

            // 4. Assign Role 2 (CRM Incharge) in master.roles_assigned
            RoleAssigned::updateOrCreate(
                ['emp_id' => '31420', 'roles_role_id' => 2],
                [
                    'campus_id' => 1,
                    'start_date' => now(),
                    'log_userid' => 'SYSTEM',
                    'log_timestamp' => now(),
                ]
            );
        } catch (\Throwable $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            RoleAssigned::where('emp_id', '31420')->delete();
            User::where('usercode', '31420')->delete();
            Employee::where('emp_code', '31420')->delete();
        } catch (\Throwable $e) {}
    }
};

