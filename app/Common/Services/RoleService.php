<?php

namespace App\Common\Services;

use Modules\Auth\Models\Role;
use Modules\Auth\Models\RoleAssigned;
use Illuminate\Support\Facades\Log;

class RoleService
{
    public static function defaultStaffRoleId(): int
    {
        return 5;
    }

    public static function studentRoleId(): int
    {
        return 5;
    }

    public static function telecallerRoleId(): int
    {
        return 4;
    }

    public static function inchargeRoleId(): int
    {
        return 2;
    }

    public static function deanRoleId(): int
    {
        return 3;
    }

    public static function isStudent(int|string|null $roleId): bool
    {
        return (int)$roleId === 5;
    }

    public static function isTelecaller(int $roleId): bool
    {
        return $roleId === 4;
    }

    public static function isIncharge(int $roleId): bool
    {
        return $roleId === 2;
    }

    public static function isDean(int $roleId): bool
    {
        return $roleId === 3;
    }

    public static function isStaff(int $roleId): bool
    {
        return $roleId === 1 || $roleId === 5;
    }

    public static function getRoleBadgeLabel(?int $roleId): string
    {
        return match ((int)$roleId) {
            3 => 'DEAN',
            2 => 'INCHARGE',
            4 => 'TELE CALLER',
            1 => 'EMPLOYEE',
            default => 'STAFF',
        };
    }

    public static function getAssignedRoles(string $employeeId): array
    {
        try {
            return RoleAssigned::with('role')
                ->where('emp_id', $employeeId)
                ->where(function ($q) {
                    $q->whereNull('end_date')
                        ->orWhere('end_date', '>=', now()->toDateString());
                })
                ->whereHas('role', fn ($rq) => $rq->where('role_status', 1))
                ->get()
                ->map(fn ($ra) => [
                    'role_id' => $ra->roles_role_id,
                    'role_name' => $ra->role?->role_name ?? "Role #{$ra->roles_role_id}",
                    'start_date' => $ra->start_date,
                    'end_date' => $ra->end_date,
                ])
                ->toArray();
        } catch (\Throwable $e) {
            Log::warning('RoleService::getAssignedRoles failed: ' . $e->getMessage());
            return [];
        }
    }

    public static function userHasRole(string $employeeId, int $roleId): bool
    {
        try {
            return RoleAssigned::where('emp_id', $employeeId)
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

