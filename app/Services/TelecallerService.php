<?php

namespace App\Services;

use App\Models\CrmStudentRegistration;
use App\Models\Employee;
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
            $q = CrmStudentRegistration::query();
            if ($targetAppli) {
                $q->where('applinano', $targetAppli);
            }
            if ($targetPhone) {
                $q->where('phoneno', $targetPhone);
            }
            $single = $q->first();
            if ($single) {
                $candidates->push($single);
            }
        } elseif ($allotMode === 'selected' && !empty($params['lead_keys'])) {
            $leadKeys = (array) $params['lead_keys'];
            foreach ($leadKeys as $item) {
                $appli = is_array($item) ? ($item['applino'] ?? '') : $item;
                $phone = is_array($item) ? ($item['phoneno'] ?? '') : '';
                $found = CrmStudentRegistration::where('applinano', $appli)
                    ->when($phone, fn($q) => $q->where('phoneno', $phone))
                    ->first();
                if ($found) {
                    $candidates->push($found);
                }
            }
        } elseif (in_array($allotMode, ['unallocated', 'first 10', 'first_10', 'first 25', 'first_25', 'first 50', 'first_50', 'first 100', 'first_100', 'custom'], true)) {
            $takeCount = match ($allotMode) {
                'first 10', 'first_10' => 10,
                'first 25', 'first_25' => 25,
                'first 50', 'first_50' => 50,
                'custom' => max(1, (int) ($params['allot_count'] ?? 15)),
                default => 100,
            };
            $allocatedApplis = TelecallerLead::pluck('applino');
            $candidates = CrmStudentRegistration::whereNotIn('applinano', $allocatedApplis)
                ->orderBy('log_timestamp', 'desc')
                ->take($takeCount)
                ->get();
        } elseif ($allotMode === 'datewise' && !empty($params['allot_date'])) {
            $candidates = CrmStudentRegistration::whereDate('log_timestamp', $params['allot_date'])
                ->orderBy('log_timestamp', 'desc')
                ->get();
        } else {
            // Fallback: take first 25 unallocated leads
            $allocatedApplis = TelecallerLead::pluck('applino');
            $candidates = CrmStudentRegistration::whereNotIn('applinano', $allocatedApplis)
                ->orderBy('log_timestamp', 'desc')
                ->take(25)
                ->get();
        }

        if ($candidates->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No matching student lead records found to allocate.',
                'count' => 0
            ];
        }

        $now = now();
        $allocatedCount = 0;
        $allocatedApplis = [];

        DB::beginTransaction();
        try {
            foreach ($candidates as $std) {
                $applino = $std->applinano;
                $phoneno = $std->phoneno;
                $candidateName = $std->name ?? 'Candidate';
                $studentCrmId = $std->crmid ?? CrmIdService::getOrGenerateByAppli($applino);

                // Check existing assignment via Eloquent
                $existing = TelecallerLead::where('applino', $applino)
                    ->where('phoneno', $phoneno)
                    ->first();

                if ($existing) {
                    $newLeadCount = ((int)$existing->leadcount) + 1;
                    $existing->update([
                        'crmid' => $studentCrmId,
                        'empcode' => $empCode,
                        'leadcount' => $newLeadCount,
                        'assigndatetime' => $now,
                        'log_userid' => $logUser,
                        'log_ipaddress' => $ipAddress,
                        'log_timestamp' => $now,
                    ]);
                } else {
                    TelecallerLead::create([
                        'applino' => $applino,
                        'phoneno' => $phoneno,
                        'crmid' => $studentCrmId,
                        'empcode' => $empCode,
                        'leadcount' => 1,
                        'assigndatetime' => $now,
                        'log_userid' => $logUser,
                        'log_ipaddress' => $ipAddress,
                        'log_timestamp' => $now,
                    ]);
                }

                // Insert into crm.telecaller_leads_tasks via Eloquent
                TelecallerLeadTask::create([
                    'applino' => $applino,
                    'phoneno' => $phoneno,
                    'crmid' => $studentCrmId,
                    'empcode' => $empCode,
                    'stage_name' => $stageName,
                    'stage_calltime' => $now,
                    'stage_remark' => $stageRemark,
                    'stage_callperson' => $candidateName,
                    'log_userid' => $logUser,
                    'log_ipaddress' => $ipAddress,
                    'log_timestamp' => $now,
                ]);

                $allocatedCount++;
                $allocatedApplis[] = $applino;
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
            TelecallerLead::create([
                'applino' => $applino,
                'phoneno' => $phoneno,
                'crmid' => $studentCrmId,
                'empcode' => $empCode,
                'leadcount' => 1,
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

        try {
            $totalRegistered = CrmStudentRegistration::count();
            $totalAllocated = TelecallerLead::count();
            $totalTasks = TelecallerLeadTask::count();
        } catch (\Throwable $e) {}

        $unallocated = max(0, $totalRegistered - $totalAllocated);

        return [
            'total_registered' => $totalRegistered,
            'total_allocated' => $totalAllocated,
            'unallocated' => $unallocated,
            'total_tasks' => $totalTasks,
        ];
    }
}
