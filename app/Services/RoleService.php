<?php

namespace App\Services;

use App\Models\MenuAccessRole;
use App\Models\Role;
use App\Models\RoleAssigned;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleService
{
    /**
     * Get all ACTIVE roles assigned to an employee/user from master.roles_assigned.
     */
    public static function getAssignedRoles(string $employeeId): array
    {
        try {
            return DB::table('master.roles_assigned as ra')
                ->join('master.roles as r', 'ra.roles_role_id', '=', 'r.role_id')
                ->where('ra.emp_id', $employeeId)
                ->where(function ($q) {
                    $q->whereNull('ra.end_date')
                        ->orWhere('ra.end_date', '>=', now()->toDateString());
                })
                ->where('r.role_status', 1)
                ->select('r.role_id', 'r.role_name', 'ra.start_date', 'ra.end_date')
                ->orderBy('r.role_name')
                ->get()
                ->map(fn ($r) => (array) $r)
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('RoleService::getAssignedRoles failed: ' . $e->getMessage());

            // Fallback to Eloquent RoleAssigned model
            try {
                return RoleAssigned::with('role')
                    ->where('emp_id', $employeeId)
                    ->get()
                    ->map(fn ($ra) => [
                        'role_id' => $ra->roles_role_id,
                        'role_name' => $ra->role?->role_name ?? "Role #{$ra->roles_role_id}",
                        'start_date' => $ra->start_date,
                        'end_date' => $ra->end_date,
                    ])
                    ->toArray();
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }

    /**
     * Check if user has a valid assigned role in the database.
     */
    public static function userHasRole(string $employeeId, int $roleId): bool
    {
        try {
            return DB::table('master.roles_assigned')
                ->where('emp_id', $employeeId)
                ->where('roles_role_id', $roleId)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }
}

