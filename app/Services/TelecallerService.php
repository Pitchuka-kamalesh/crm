<?php

namespace App\Services;

use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\RoleAssigned;
use App\Models\TelecallerLead;
use App\Models\TelecallerLeadTask;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelecallerService
{
    /**
     * Get list of telecaller employees (Role ID 4 in roles_assigned or employees).
     */
    public static function getTelecallers(): Collection
    {
        try {
            $telecallerCodes = RoleAssigned::where('roles_role_id', 4)
                ->pluck('emp_id')
                ->filter()
                ->unique();

            $query = Employee::query();
            if ($telecallerCodes->isNotEmpty()) {
                $query->whereIn('emp_code', $telecallerCodes)
                    ->orWhere('role_id', 4);
            } else {
                $query->where('role_id', 4);
            }

            $telecallers = $query->orderBy('full_name')->get();

            if ($telecallers->isEmpty()) {
                // Fallback to active employees
                $telecallers = Employee::where('is_active', true)->orderBy('full_name')->take(15)->get();
            }

            return $telecallers;
        } catch (\Throwable $e) {
            Log::error('Error fetching telecallers: ' . $e->getMessage());
            return Employee::take(10)->get();
        }
    }

    /**
     * Allocate one or multiple leads to a telecaller.
     * Persists into crm.telecaller_leads and logs into crm.telecaller_leads_tasks via Eloquent.
     *
     * @param array $params
     * @return array
     */
    public static function allocateLeads(array $params): array
    {
        $empCode = trim($params['empcode'] ?? '');
        $allotMode = trim($params['allot_mode'] ?? 'single');
        $stageName = trim($params['stage_name'] ?? 'Stage 1: Calling & Verification');
        $stageRemark = trim($params['stage_remark'] ?? 'Lead assigned to telecaller for admission follow-up');
        $logUser = trim($params['log_userid'] ?? auth()->user()?->usercode ?? '02598');
        $ipAddress = trim($params['log_ipaddress'] ?? request()->ip() ?? '127.0.0.1');

        if (empty($empCode)) {
            return ['success' => false, 'message' => 'Please select a valid telecaller employee.'];
        }

        $employee = Employee::where('emp_code', $empCode)->first();
        $employeeName = $employee ? $employee->full_name : $empCode;

        // Resolve candidate leads
        $candidates = collect();

        if ($allotMode === 'single' || !empty($params['applino'])) {
            $targetAppli = trim($params['applino'] ?? '');
            $targetPhone = trim($params['phoneno'] ?? '');
            $found = null;
            if ($targetPhone) {
                $found = Lead::where('phoneno', $targetPhone)->first();
            }
            if (!$found && $targetAppli && $targetAppli !== '0') {
                $found = Lead::where('applino', $targetAppli)->first();
            }
            if (!$found) {
                $q = CrmStudentRegistration::query();
                if ($targetAppli) $q->where('applinano', $targetAppli);
                if ($targetPhone) $q->where('phoneno', $targetPhone);
                $found = $q->first();
            }
            if ($found) {
                $candidates->push($found);
            }
        } elseif ($allotMode === 'selected') {
            $leadKeys = (array) ($params['lead_keys'] ?? []);
            if (empty($leadKeys)) {
                return [
                    'success' => false,
                    'message' => 'Please select at least one lead from the table using checkboxes, or choose a bucket size (100, 300, 500, 1000).',
                    'count' => 0
                ];
            }
            foreach ($leadKeys as $item) {
                $appli = is_array($item) ? ($item['applino'] ?? '') : (string) $item;
                $phone = is_array($item) ? ($item['phoneno'] ?? '') : '';
                $found = null;
                if ($phone) {
                    $found = Lead::where('phoneno', $phone)->first();
                }
                if (!$found && $appli && $appli !== '0') {
                    $found = Lead::where('applino', $appli)->first();
                }
                if (!$found) {
                    $found = CrmStudentRegistration::where('applinano', $appli)
                        ->when($phone, fn($q) => $q->where('phoneno', $phone))
                        ->first();
                }
                if ($found) {
                    $candidates->push($found);
                }
            }
        } elseif (
            in_array($allotMode, ['100', '300', '500', '1000', 'bucket_100', 'bucket_300', 'bucket_500', 'bucket_1000', 'unallocated', 'first 10', 'first_10', 'first 25', 'first_25', 'first 50', 'first_50', 'first 100', 'first_100', 'custom'], true)
            || is_numeric($allotMode)
        ) {
            $takeCount = match ((string) $allotMode) {
                '100', 'bucket_100', 'first 100', 'first_100' => 100,
                '300', 'bucket_300' => 300,
                '500', 'bucket_500' => 500,
                '1000', 'bucket_1000' => 1000,
                'first 10', 'first_10' => 10,
                'first 25', 'first_25' => 25,
                'first 50', 'first_50' => 50,
                'custom' => max(1, (int) ($params['allot_count'] ?? 15)),
                'unallocated' => 1000000,
                default => is_numeric($allotMode) ? (int)$allotMode : 1000000,
            };
            $candidates = Lead::where(function ($q) {
                    $q->whereNull('empcode')->orWhere('empcode', '0');
                })
                ->orderBy('log_timestamp', 'desc')
                ->take($takeCount)
                ->get();

            if ($candidates->isEmpty()) {
                $allocatedApplis = TelecallerLead::pluck('applino');
                $candidates = CrmStudentRegistration::whereNotIn('applinano', $allocatedApplis)
                    ->orderBy('log_timestamp', 'desc')
                    ->take($takeCount)
                    ->get();
            }
        } elseif ($allotMode === 'datewise' && !empty($params['allot_date'])) {
            $candidates = Lead::whereDate('log_timestamp', $params['allot_date'])
                ->orderBy('log_timestamp', 'desc')
                ->get();
            if ($candidates->isEmpty()) {
                $candidates = CrmStudentRegistration::whereDate('log_timestamp', $params['allot_date'])
                    ->orderBy('log_timestamp', 'desc')
                    ->get();
            }
        } else {
            // Fallback: take available unallocated leads (up to 100)
            $candidates = Lead::where(function ($q) {
                    $q->whereNull('empcode')->orWhere('empcode', '0');
                })
                ->orderBy('log_timestamp', 'desc')
                ->take(100)
                ->get();

            if ($candidates->isEmpty()) {
                $allocatedApplis = TelecallerLead::pluck('applino');
                $candidates = CrmStudentRegistration::whereNotIn('applinano', $allocatedApplis)
                    ->orderBy('log_timestamp', 'desc')
                    ->take(25)
                    ->get();
            }
        }

        if ($candidates->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No unallocated leads remaining in queue to allocate.',
                'count' => 0
            ];
        }

        $now = now();
        $allocatedCount = 0;
        $allocatedApplis = [];

        // When we allocate to lead, lead count incremented by 1 by comparing max count for that respective employee
        $targetEmpCode = (string) $empCode;
        $currentEmpMaxCount = (int) TelecallerLead::where('empcode', $targetEmpCode)->max('leadcount');

        DB::beginTransaction();
        try {
            foreach ($candidates as $std) {
                $rawAppli = '0';
                $rawPhone = '0';
                $candidateName = '0';
                $studentCrmId = '0';

                if ($std instanceof Lead) {
                    $rawPhone = (string) ($std->phoneno ?? '0');
                    $rawAppli = !empty($std->applino) ? (string) $std->applino : '0';
                } elseif ($std instanceof CrmStudentRegistration) {
                    $rawAppli = !empty($std->applinano) ? (string) $std->applinano : '0';
                    $rawPhone = (string) ($std->phoneno ?? '0');
                    $candidateName = $std->name ?: '0';
                    $studentCrmId = $std->crmid ?: '0';
                }

                // If data is not sufficient to fill row, just put zero in that
                $tApplino = !empty($rawAppli) ? (string) $rawAppli : '0';
                $tPhoneno = !empty($rawPhone) ? (string) $rawPhone : '0';
                $tCrmid = !empty($studentCrmId) ? (string) $studentCrmId : '0';
                $tEmpcode = !empty($empCode) ? (string) $empCode : '0';
                $tStageName = (!empty($stageName) && $stageName !== 'Stage 1: Fee Payment') ? (string) $stageName : 'Telecaller Stage';
                $tStageRemark = !empty($stageRemark) ? (string) $stageRemark : '0';
                $tCallPerson = !empty($candidateName) ? (string) $candidateName : '0';
                $tLogUser = !empty($logUser) ? (string) $logUser : '0';
                $tIp = !empty($ipAddress) ? (string) $ipAddress : '0';

                // Incremented by 1 comparing max count for that respective employee
                $currentEmpMaxCount++;
                $newLeadCount = $currentEmpMaxCount;

                // Check existing assignment in crm.telecaller_leads
                $existing = TelecallerLead::where('applino', $tApplino)
                    ->where('phoneno', $tPhoneno)
                    ->first();

                if ($existing) {
                    TelecallerLead::where('applino', $tApplino)
                        ->where('phoneno', $tPhoneno)
                        ->update([
                            'crmid' => $tCrmid,
                            'empcode' => $tEmpcode,
                            'leadcount' => $newLeadCount,
                            'assigndatetime' => $now,
                            'log_userid' => $tLogUser,
                            'log_ipaddress' => $tIp,
                            'log_timestamp' => $now,
                        ]);
                } else {
                    TelecallerLead::create([
                        'applino' => $tApplino,
                        'phoneno' => $tPhoneno,
                        'crmid' => $tCrmid,
                        'empcode' => $tEmpcode,
                        'leadcount' => $newLeadCount,
                        'assigndatetime' => $now,
                        'log_userid' => $tLogUser,
                        'log_ipaddress' => $tIp,
                        'log_timestamp' => $now,
                    ]);
                }

                // Insert into crm.telecaller_leads_tasks
                TelecallerLeadTask::create([
                    'applino' => $tApplino,
                    'phoneno' => $tPhoneno,
                    'crmid' => $tCrmid,
                    'empcode' => $tEmpcode,
                    'stage_name' => $tStageName,
                    'stage_calltime' => $now,
                    'stage_remark' => $tStageRemark,
                    'stage_callperson' => $tCallPerson,
                    'log_userid' => $tLogUser,
                    'log_ipaddress' => $tIp,
                    'log_timestamp' => $now,
                ]);

                // Update crm.leads with empcode and allocated timestamp (log_timestamp is fixed from initial entry, do not overwrite)
                Lead::where('phoneno', $tPhoneno)->update([
                    'empcode' => $tEmpcode,
                    'allocated_at' => $now,
                    'log_userid' => $tLogUser,
                ]);

                $allocatedCount++;
                $allocatedApplis[] = $tApplino !== '0' ? $tApplino : $tPhoneno;
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Successfully allocated {$allocatedCount} leads to {$employeeName} ({$empCode}).",
                'count' => $allocatedCount,
                'allocated_applis' => $allocatedApplis,
                'employee' => $employeeName,
                'empcode' => $empCode,
                'stage' => $stageName,
                'metrics' => self::getMetrics(),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TelecallerService allocateLeads error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to allocate leads: ' . $e->getMessage(),
                'count' => 0
            ];
        }
    }

    /**
     * Log a lead task into crm.telecaller_leads_tasks via Eloquent.
     *
     * @param array $params
     * @return array
     */
    public static function logLeadTask(array $params): array
    {
        $applino = trim($params['applino'] ?? '');
        $phoneno = trim($params['phoneno'] ?? '');
        $empCode = trim($params['empcode'] ?? auth()->user()?->usercode ?? '02598');
        $stageName = trim($params['stage_name'] ?? 'Calling & Verification');
        $remark = trim($params['stage_remark'] ?? '');
        $callPerson = trim($params['stage_callperson'] ?? '');
        $logUser = trim($params['log_userid'] ?? auth()->user()?->usercode ?? '02598');
        $ip = trim($params['log_ipaddress'] ?? request()->ip() ?? '127.0.0.1');

        if (empty($applino) || empty($phoneno)) {
            $student = CrmStudentRegistration::where('applinano', $applino)
                ->orWhere('phoneno', $phoneno)
                ->first();
            if ($student) {
                $applino = $student->applinano;
                $phoneno = $student->phoneno;
                if (empty($callPerson)) {
                    $callPerson = $student->name;
                }
            } else {
                return ['success' => false, 'message' => 'Application or Phone number missing.'];
            }
        }

        // Ensure parent lead exists in crm.telecaller_leads via Eloquent
        $lead = TelecallerLead::where('applino', $applino)
            ->where('phoneno', $phoneno)
            ->first();

        $now = now();
        $callTime = $now;
        if (!empty($params['stage_calltime'])) {
            try {
                $callTime = \Carbon\Carbon::parse($params['stage_calltime']);
            } catch (\Throwable $e) {
                $callTime = $now;
            }
        }

        $studentCrmId = CrmIdService::getOrGenerateByAppli($applino);

        if (!$lead) {
            $empMax = (int) TelecallerLead::where('empcode', (string) $empCode)->max('leadcount');
            TelecallerLead::create([
                'applino' => $applino,
                'phoneno' => $phoneno,
                'crmid' => $studentCrmId,
                'empcode' => $empCode,
                'leadcount' => $empMax + 1,
                'assigndatetime' => $now,
                'log_userid' => $logUser,
                'log_ipaddress' => $ip,
                'log_timestamp' => $now,
            ]);
        }

        $task = TelecallerLeadTask::create([
            'applino' => $applino,
            'phoneno' => $phoneno,
            'crmid' => $studentCrmId,
            'empcode' => $empCode,
            'stage_name' => $stageName,
            'stage_calltime' => $callTime,
            'stage_remark' => $remark,
            'stage_callperson' => $callPerson,
            'log_userid' => $logUser,
            'log_ipaddress' => $ip,
            'log_timestamp' => $now,
        ]);

        return [
            'success' => true,
            'message' => 'Task logged successfully.',
            'task_id' => $task->leadtaskid,
        ];
    }

    /**
     * Get summary metrics for leads allocation via Eloquent.
     */
    public static function getMetrics(): array
    {
        $totalRegistered = 0;
        $totalAllocated = 0;
        $totalTasks = 0;
        $unallocated = 0;

        try {
            $totalLeads = Lead::count();
            if ($totalLeads > 0) {
                $totalAllocated = Lead::whereNotNull('empcode')->where('empcode', '<>', '0')->count();
                $unallocated = Lead::where(function ($q) {
                    $q->whereNull('empcode')->orWhere('empcode', '0');
                })->count();
                $totalRegistered = $totalLeads;
            } else {
                $totalRegistered = CrmStudentRegistration::count();
                $totalAllocated = TelecallerLead::count();
                $unallocated = max(0, $totalRegistered - $totalAllocated);
            }
            $totalTasks = TelecallerLeadTask::count();
        } catch (\Throwable $e) {
            Log::error('TelecallerService getMetrics error: ' . $e->getMessage());
        }

        return [
            'total_registered' => $totalRegistered,
            'total_allocated' => $totalAllocated,
            'unallocated' => $unallocated,
            'total_tasks' => $totalTasks,
        ];
    }

    /**
     * Determine candidate stage based on user business rules:
     * 1. If application number not created, not assigned to telecaller -> 'New Lead'
     * 2. If application number not created, but assigned to telecaller -> 'Telecaller Stage'
     * 3. If application number created, but not assigned to telecaller -> 'Register Form'
     * 4. If application number exists but not paid -> 'Payment Stage'
     * 5. If application number exists and paid, but personal details not filled -> 'Personal Details'
     * 6. If application number exists, paid, and personal details filled, but academic details not filled -> 'Academic Details'
     * 7. If academic details filled -> 'Academic Details Completed'
     */
    public static function determineLeadStage(
        ?string $applino,
        ?string $empcode,
        ?string $stage1 = null,
        ?string $stage2 = null,
        ?string $stage3 = null
    ): string {
        $hasAppli = !empty($applino) && $applino !== '0';
        $isAssigned = !empty($empcode) && $empcode !== '0';

        // 1. If application number not created, not assigned to telecaller -> New Lead
        if (!$hasAppli && !$isAssigned) {
            return 'New Lead';
        }

        // 2. If application number not created, but assigned to telecaller -> Telecaller Stage
        if (!$hasAppli && $isAssigned) {
            return 'Telecaller Stage';
        }

        // 3. If application number created, but not assigned to telecaller -> Register Form
        if ($hasAppli && !$isAssigned) {
            return 'Register Form';
        }

        // Has application number and is progressing:
        $isPaid = in_array(strtolower((string) $stage1), ['paid', 'completed', 'approved']);
        // 4. If application number not paid -> Payment Stage
        if (!$isPaid) {
            return 'Payment Stage';
        }

        $hasPersonal = in_array(strtolower((string) $stage2), ['completed', 'approved']);
        // 5. If application number but not filled personal details -> Personal Details
        if (!$hasPersonal) {
            return 'Personal Details';
        }

        $hasAcademic = in_array(strtolower((string) $stage3), ['completed', 'approved']);
        // 6. If application number but not filled academic details -> Academic Details
        if (!$hasAcademic) {
            return 'Academic Details';
        }

        return 'Academic Details Completed';
    }

    /**
     * Badge visual styling for each lead stage.
     */
    public static function getStageBadgeStyle(string $stage): array
    {
        return match (strtolower(trim($stage))) {
            'new lead' => ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#cbd5e1', 'icon' => '🌱'],
            'telecaller stage' => ['bg' => '#fef3c7', 'color' => '#b45309', 'border' => '#fde68a', 'icon' => '📞'],
            'register form' => ['bg' => '#e0e7ff', 'color' => '#3730a3', 'border' => '#c7d2fe', 'icon' => '📝'],
            'payment stage', 'not paid stage' => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fecaca', 'icon' => '💳'],
            'personal details' => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '#bfdbfe', 'icon' => '👤'],
            'academic details' => ['bg' => '#f3e8ff', 'color' => '#7e22ce', 'border' => '#e9d5ff', 'icon' => '🎓'],
            'academic details completed' => ['bg' => '#dcfce7', 'color' => '#15803d', 'border' => '#bbf7d0', 'icon' => '✅'],
            default => ['bg' => '#f8fafc', 'color' => '#334155', 'border' => '#e2e8f0', 'icon' => '📌'],
        };
    }
}
