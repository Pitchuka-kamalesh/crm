<?php

namespace App\Services;

use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\ReceiptDetail;
use App\Models\RoleAssigned;
use App\Models\StudentAcademicDetail;
use App\Models\StudentPersonalInfo;
use App\Models\TelecallerLead;
use App\Models\TelecallerLeadTask;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TelecallerService
{
    /**
     * Get list of telecaller employees resolved dynamically from master.roles / master.roles_assigned / master.employees.
     */
    public static function getTelecallers(): Collection
    {
        try {
            $telecallerRoleId = RoleService::telecallerRoleId();
            if ($telecallerRoleId === null) {
                return collect();
            }

            $telecallerCodes = RoleAssigned::where('roles_role_id', $telecallerRoleId)
                ->pluck('emp_id')
                ->filter()
                ->unique();

            $query = Employee::query();
            if ($telecallerCodes->isNotEmpty()) {
                $query->where(function ($q) use ($telecallerCodes, $telecallerRoleId) {
                    $q->whereIn('emp_code', $telecallerCodes)
                        ->orWhere('role_id', $telecallerRoleId);
                });
            } else {
                $query->where('role_id', $telecallerRoleId);
            }

            return $query->orderBy('full_name')->get();
        } catch (\Throwable $e) {
            Log::error('Error fetching telecallers: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Allocate one or multiple leads to a telecaller.
     * Persists into crm.telecaller_leads and updates crm.leads without creating telecaller_leads_tasks rows.
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
        $logUser = trim($params['log_userid'] ?? auth()->user()?->usercode ?? 'SYSTEM');
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
     * Log a telecaller action strictly into crm.telecaller_leads_tasks via Eloquent.
     *
     * @param array $params
     * @return array
     */
    public static function logLeadTask(array $params): array
    {
        $applino = trim((string) ($params['applino'] ?? ''));
        $phoneno = trim((string) ($params['phoneno'] ?? ''));
        $empCode = trim((string) ($params['empcode'] ?? auth()->user()?->usercode ?? ''));
        $stageName = trim((string) ($params['stage_name'] ?? 'Calling & Verification'));
        $remark = trim((string) ($params['stage_remark'] ?? ''));
        $callPerson = trim((string) ($params['stage_callperson'] ?? ''));
        $logUser = trim((string) ($params['log_userid'] ?? auth()->user()?->usercode ?? 'SYSTEM'));
        $ip = trim((string) ($params['log_ipaddress'] ?? request()->ip() ?? '127.0.0.1'));

        $student = null;
        if ($applino !== '' && $applino !== '0') {
            $student = CrmStudentRegistration::where('applinano', $applino)->first();
        }
        if (!$student && $phoneno !== '' && $phoneno !== '0') {
            $student = CrmStudentRegistration::where('phoneno', $phoneno)->first();
        }

        if ($student) {
            if ($applino === '' || $applino === '0') {
                $applino = (string) ($student->applinano ?: '0');
            }
            if ($phoneno === '' || $phoneno === '0') {
                $phoneno = (string) ($student->phoneno ?: '0');
            }
            if ($callPerson === '' && !empty($student->name)) {
                $callPerson = (string) $student->name;
            }
        }

        // Resolve existing parent record in crm.telecaller_leads (required by fk_tasks_lead) without creating/updating it
        $lead = null;
        if ($applino !== '' && $phoneno !== '') {
            $lead = TelecallerLead::where('applino', $applino)
                ->where('phoneno', $phoneno)
                ->first();
        }
        if (!$lead && $phoneno !== '' && $phoneno !== '0') {
            $lead = TelecallerLead::where('phoneno', $phoneno)->first();
        }
        if (!$lead && $applino !== '' && $applino !== '0') {
            $lead = TelecallerLead::where('applino', $applino)->first();
        }

        if (!$lead) {
            return [
                'success' => false,
                'message' => 'Lead must be assigned in telecaller_leads before logging a telecaller action.',
            ];
        }

        $now = now();
        $callTime = $now;
        if (!empty($params['stage_calltime'])) {
            try {
                $callTime = \Carbon\Carbon::parse($params['stage_calltime']);
            } catch (\Throwable $e) {
                $callTime = $now;
            }
        }

        $taskApplino = (string) $lead->applino;
        $taskPhoneno = (string) $lead->phoneno;

        // Resolve crmid strictly from crm.student_personal_info; use '0' if student has not filled personal info yet
        $personalCrmId = null;
        if ($taskApplino !== '' && $taskApplino !== '0') {
            $personalCrmId = StudentPersonalInfo::where('applino', $taskApplino)->value('crmid');
        }
        if (empty($personalCrmId) && $taskPhoneno !== '' && $taskPhoneno !== '0') {
            $personalCrmId = StudentPersonalInfo::where('phoneno', $taskPhoneno)->value('crmid');
        }
        $taskCrmId = !empty($personalCrmId) ? (string) $personalCrmId : '0';

        $taskEmpCode = $empCode !== '' ? $empCode : (string) ($lead->empcode ?: '0');
        $callDuration = trim((string) ($params['stage_call_duration'] ?? $params['call_duration'] ?? ''));

        $task = TelecallerLeadTask::create([
            'applino' => $taskApplino,
            'phoneno' => $taskPhoneno,
            'crmid' => $taskCrmId,
            'empcode' => $taskEmpCode,
            'stage_name' => $stageName,
            'stage_calltime' => $callTime,
            'stage_call_duration' => $callDuration !== '' ? $callDuration : null,
            'stage_remark' => $remark,
            'stage_callperson' => $callPerson ?: 'Student / Candidate',
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
     * Determine candidate stage based on completed admission milestones:
     * 1. Application number not created & not assigned -> 'New Lead'
     * 2. Application number not created & assigned -> 'Telecaller Stage'
     * 3. Registered (application number exists, not yet paid) -> 'Register Form'
     * 4. Paid (not yet filled personal details) -> 'Payment Stage'
     * 5. Personal details filled (not yet filled academic details) -> 'Personal Details'
     * 6. Academic details filled -> 'Academic Details'
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

        if (!$hasAppli && !$isAssigned) {
            return 'New Lead';
        }

        if (!$hasAppli && $isAssigned) {
            return 'Telecaller Stage';
        }

        $isPaid = in_array(strtolower((string) $stage1), ['paid', 'completed', 'approved'], true);
        if (!$isPaid) {
            return 'Register Form completed';
        }

        $hasPersonal = in_array(strtolower((string) $stage2), ['completed', 'approved'], true);
        if (!$hasPersonal) {
            return 'Payment Stage completed';
        }

        $hasAcademic = in_array(strtolower((string) $stage3), ['completed', 'approved'], true);
        if (!$hasAcademic) {
            return 'Personal Details completed';
        }

        return 'Academic Details completed';
    }

    /**
     * Badge visual styling for each lead stage.
     */
    public static function getStageBadgeStyle(string $stage): array
    {
        return match (strtolower(trim($stage))) {
            'new lead' => ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#cbd5e1', 'icon' => ''],
            'telecaller stage' => ['bg' => '#fef3c7', 'color' => '#b45309', 'border' => '#fde68a', 'icon' => ''],
            'register form', 'register form completed' => ['bg' => '#e0e7ff', 'color' => '#3730a3', 'border' => '#c7d2fe', 'icon' => ''],
            'payment stage', 'payment stage completed', 'not paid stage' => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fecaca', 'icon' => ''],
            'personal details', 'personal details completed' => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '#bfdbfe', 'icon' => ''],
            'academic details', 'academic details completed' => ['bg' => '#f3e8ff', 'color' => '#7e22ce', 'border' => '#e9d5ff', 'icon' => ''],
            default => ['bg' => '#f8fafc', 'color' => '#334155', 'border' => '#e2e8f0', 'icon' => ''],
        };
    }

    /**
     * Default stage dropdown permissions (Telecaller Stage is always enabled by default).
     *
     * @return array<string, bool>
     */
    public static function defaultAllowedStages(): array
    {
        return [
            'Telecaller Stage' => true,
            'Register Form completed' => false,
            'Payment Stage completed' => false,
            'Personal Details completed' => false,
            'Academic Details completed' => false,
        ];
    }

    /**
     * Resolve the displayed Current Step from the student's completed tables and the telecaller's latest selected stage.
     *
     * @param  array<string, bool>  $allowedStages
     */
    public static function resolveCurrentStep(
        ?string $empcode,
        array $allowedStages,
        ?string $lastTaskStage = null
    ): string {
        $ranks = [
            'New Lead' => 0,
            'Telecaller Stage' => 1,
            'Register Form completed' => 2,
            'Payment Stage completed' => 3,
            'Personal Details completed' => 4,
            'Academic Details completed' => 5,
        ];

        $aliases = [
            'Register Form' => 'Register Form completed',
            'Payment Stage' => 'Payment Stage completed',
            'Personal Details' => 'Personal Details completed',
            'Academic Details' => 'Academic Details completed',
        ];

        $studentStep = 'New Lead';
        if (!empty($allowedStages['Academic Details completed'])) {
            $studentStep = 'Academic Details completed';
        } elseif (!empty($allowedStages['Personal Details completed'])) {
            $studentStep = 'Personal Details completed';
        } elseif (!empty($allowedStages['Payment Stage completed'])) {
            $studentStep = 'Payment Stage completed';
        } elseif (!empty($allowedStages['Register Form completed'])) {
            $studentStep = 'Register Form completed';
        } elseif (!empty($empcode) && $empcode !== '0') {
            $studentStep = 'Telecaller Stage';
        }

        $taskStage = trim((string) $lastTaskStage);
        if (isset($aliases[$taskStage])) {
            $taskStage = $aliases[$taskStage];
        }
        if ($taskStage !== '' && isset($ranks[$taskStage])) {
            $isTaskStageEligible = ($taskStage === 'Telecaller Stage') || !empty($allowedStages[$taskStage]);
            if ($isTaskStageEligible && $ranks[$taskStage] >= $ranks[$studentStep]) {
                return $taskStage;
            }
        }

        return $studentStep;
    }

    /**
     * Batch-compute selectable action modal stages keyed by application number.
     *
     * @param  array<int, string|null>  $applinos
     * @return array<string, array<string, bool>>
     */
    public static function getStageEligibilityMap(array $applinos): array
    {
        $clean = array_values(array_unique(array_filter(
            array_map(fn ($v) => trim((string) $v), $applinos),
            fn (string $v) => $v !== '' && $v !== '0' && !str_starts_with($v, 'APP')
        )));

        if (empty($clean)) {
            return [];
        }

        $regSet = CrmStudentRegistration::whereIn('applinano', $clean)
            ->pluck('applinano')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        $receiptSet = ReceiptDetail::whereIn('applino', $clean)
            ->pluck('applino')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        $personalSet = StudentPersonalInfo::whereIn('applino', $clean)
            ->pluck('applino')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        $academicSet = StudentAcademicDetail::whereIn('applino', $clean)
            ->pluck('applino')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        $map = [];
        foreach ($clean as $app) {
            $map[$app] = [
                'Telecaller Stage' => true,
                'Register Form completed' => isset($regSet[$app]),
                'Payment Stage completed' => isset($receiptSet[$app]),
                'Personal Details completed' => isset($personalSet[$app]),
                'Academic Details completed' => isset($academicSet[$app]),
            ];
        }

        return $map;
    }

    /**
     * Resolve selectable action modal stages for a single application number.
     *
     * @return array<string, bool>
     */
    public static function getAllowedStagesForLead(?string $applino): array
    {
        $app = trim((string) $applino);
        if ($app === '' || $app === '0' || str_starts_with($app, 'APP')) {
            return self::defaultAllowedStages();
        }

        $map = self::getStageEligibilityMap([$app]);

        return $map[$app] ?? self::defaultAllowedStages();
    }
}
