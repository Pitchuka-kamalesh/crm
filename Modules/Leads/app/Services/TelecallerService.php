<?php

namespace Modules\Leads\Services;

use Modules\Auth\Services\RoleService;
use Modules\Crm\Models\CrmStudentRegistration;
use Modules\Auth\Models\Employee;
use Modules\Leads\Models\Lead;
use Modules\Finance\Models\ReceiptDetail;
use App\Models\PayDetail;
use Modules\Auth\Models\RoleAssigned;
use Modules\Dashboards\Models\StudentAcademicDetail;
use Modules\Dashboards\Models\StudentPersonalInfo;
use Modules\Dashboards\Models\StudentUpload;
use App\Models\ExamSchedule;
use Modules\Leads\Models\TelecallerLead;
use Modules\Leads\Models\TelecallerLeadTask;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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
        @ini_set('max_execution_time', '300');
        @set_time_limit(300);
        @ini_set('memory_limit', '1024M');

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
     * Get the next telecaller in sequence for auto-allocation.
     */
    public static function getNextTelecallerInfo(): array
    {
        $telecallers = self::getTelecallers()->values();
        $numTelecallers = $telecallers->count();

        if ($numTelecallers === 0) {
            return [
                'next_telecaller' => null,
                'next_index' => 0,
                'total_telecallers' => 0,
            ];
        }

        $lastEmpCode = Cache::get('auto_allocate_last_empcode');

        if (!$lastEmpCode) {
            try {
                $lastLeadEmp = Lead::whereNotNull('empcode')
                    ->where('empcode', '<>', '0')
                    ->orderBy('allocated_at', 'desc')
                    ->orderBy('log_timestamp', 'desc')
                    ->value('empcode');
                if ($lastLeadEmp) {
                    $lastEmpCode = (string) $lastLeadEmp;
                }
            } catch (\Throwable $e) {}
        }

        $nextIndex = 0;
        if ($lastEmpCode) {
            $foundIndex = $telecallers->search(function ($tc) use ($lastEmpCode) {
                return (string) $tc->emp_code === (string) $lastEmpCode;
            });
            if ($foundIndex !== false) {
                $nextIndex = ($foundIndex + 1) % $numTelecallers;
            }
        }

        $nextTelecaller = $telecallers[$nextIndex] ?? null;

        return [
            'next_telecaller' => $nextTelecaller,
            'next_index' => $nextIndex,
            'total_telecallers' => $numTelecallers,
        ];
    }

    /**
     * Auto-allocate leads across active telecallers in sequential round-robin order.
     * Resumes from where the previous allocation stopped.
     */
    public static function autoAllocateRoundRobin(int $count, ?string $logUser = null, ?string $ipAddress = null): array
    {
        @ini_set('max_execution_time', '300');
        @set_time_limit(300);
        @ini_set('memory_limit', '1024M');

        $logUser = trim($logUser ?? auth()->user()?->usercode ?? 'SYSTEM');
        $ipAddress = trim($ipAddress ?? request()->ip() ?? '127.0.0.1');

        if ($count <= 0) {
            return [
                'success' => false,
                'message' => 'Please enter a valid number of leads to allocate (at least 1).',
                'count' => 0,
            ];
        }

        $telecallers = self::getTelecallers()->values();
        $numTelecallers = $telecallers->count();

        if ($numTelecallers === 0) {
            return [
                'success' => false,
                'message' => 'No active telecallers found in the system to allocate leads.',
                'count' => 0,
            ];
        }

        // Count available unallocated leads in crm.leads
        $availableLeadsCount = Lead::where(function ($q) {
            $q->whereNull('empcode')->orWhere('empcode', '0');
        })->count();

        // Determine start index based on last allocated telecaller
        $nextInfo = self::getNextTelecallerInfo();
        $startIndex = $nextInfo['next_index'];

        $now = now();
        $allocatedCount = 0;
        $allocatedApplis = [];
        $perTelecallerSummary = [];
        $lastAllocatedEmpCode = null;

        // If crm.leads has unallocated leads, use fast set-based PostgreSQL CTE batch allocation
        if ($availableLeadsCount > 0) {
            $countToAllocate = min($count, $availableLeadsCount);
            $base = intdiv($countToAllocate, $numTelecallers);
            $rem = $countToAllocate % $numTelecallers;

            DB::beginTransaction();
            try {
                for ($s = 0; $s < $numTelecallers; $s++) {
                    $tcIndex = ($startIndex + $s) % $numTelecallers;
                    $targetTelecaller = $telecallers[$tcIndex];
                    $targetEmpCode = (string) $targetTelecaller->emp_code;
                    $targetName = $targetTelecaller->full_name ?? $targetEmpCode;
                    $countForThis = $base + ($s < $rem ? 1 : 0);

                    if ($countForThis <= 0) {
                        continue;
                    }

                    $currMax = (int) DB::table('crm.telecaller_leads')->where('empcode', $targetEmpCode)->max('leadcount');

                    $sql = "
                        WITH unallocated AS (
                            SELECT phoneno, COALESCE(applino, '0') as applino
                            FROM crm.leads
                            WHERE empcode IS NULL OR empcode = '0'
                            ORDER BY log_timestamp DESC NULLS LAST
                            LIMIT " . (int)$countForThis . "
                        ),
                        updated_leads AS (
                            UPDATE crm.leads l
                            SET empcode = :emp1, allocated_at = :now1, log_userid = :user1
                            FROM unallocated u
                            WHERE l.phoneno = u.phoneno
                            RETURNING l.phoneno, COALESCE(l.applino, '0') as applino
                        ),
                        inserted AS (
                            INSERT INTO crm.telecaller_leads (applino, phoneno, crmid, empcode, leadcount, assigndatetime, log_userid, log_ipaddress, log_timestamp)
                            SELECT 
                                ul.applino,
                                ul.phoneno,
                                '0',
                                :emp2,
                                :curr_max + ROW_NUMBER() OVER (),
                                :now2,
                                :user2,
                                :ip,
                                :now3
                            FROM updated_leads ul
                            ON CONFLICT (applino, phoneno) DO UPDATE SET
                                empcode = EXCLUDED.empcode,
                                leadcount = EXCLUDED.leadcount,
                                assigndatetime = EXCLUDED.assigndatetime,
                                log_userid = EXCLUDED.log_userid,
                                log_ipaddress = EXCLUDED.log_ipaddress,
                                log_timestamp = EXCLUDED.log_timestamp
                            RETURNING phoneno, applino
                        )
                        SELECT 
                            COUNT(*)::int as total_allocated,
                            COALESCE(
                                (SELECT json_agg(sub.lead_ref) 
                                 FROM (SELECT COALESCE(NULLIF(applino, '0'), phoneno) as lead_ref FROM inserted LIMIT 50) sub),
                                '[]'::json
                            )::text as sample_leads
                        FROM inserted;
                    ";

                    $res = DB::selectOne($sql, [
                        'emp1' => $targetEmpCode,
                        'now1' => $now,
                        'user1' => $logUser,
                        'emp2' => $targetEmpCode,
                        'curr_max' => $currMax,
                        'now2' => $now,
                        'user2' => $logUser,
                        'ip' => $ipAddress,
                        'now3' => $now,
                    ]);

                    $actualAllocated = (int) ($res->total_allocated ?? 0);
                    if ($actualAllocated > 0) {
                        $allocatedCount += $actualAllocated;
                        $perTelecallerSummary[$targetName] = ($perTelecallerSummary[$targetName] ?? 0) + $actualAllocated;
                        $lastAllocatedEmpCode = $targetEmpCode;
                        if (count($allocatedApplis) < 50 && !empty($res->sample_leads)) {
                            $samples = json_decode($res->sample_leads, true) ?: [];
                            foreach ($samples as $sRef) {
                                if (count($allocatedApplis) >= 50) break;
                                $allocatedApplis[] = (string) $sRef;
                            }
                        }
                    }
                }

                if ($lastAllocatedEmpCode) {
                    Cache::forever('auto_allocate_last_empcode', $lastAllocatedEmpCode);
                }

                DB::commit();

                return [
                    'success' => true,
                    'message' => "Successfully auto-allocated " . number_format($allocatedCount) . " leads across {$numTelecallers} telecallers.",
                    'count' => $allocatedCount,
                    'allocated_applis' => $allocatedApplis,
                    'summary' => $perTelecallerSummary,
                    'last_empcode' => $lastAllocatedEmpCode,
                    'metrics' => self::getMetrics(),
                ];
            } catch (\Throwable $e) {
                DB::rollBack();
                Log::error('TelecallerService autoAllocateRoundRobin error: ' . $e->getMessage());
                return [
                    'success' => false,
                    'message' => 'Failed to auto-allocate leads: ' . $e->getMessage(),
                    'count' => 0,
                ];
            }
        }

        // Fallback: Check unallocated in crm.student_registration if crm.leads has no unallocated leads
        $allocatedApplis = TelecallerLead::pluck('applino');
        $candidates = CrmStudentRegistration::whereNotIn('applinano', $allocatedApplis)
            ->orderBy('log_timestamp', 'desc')
            ->take($count)
            ->get();

        if ($candidates->isEmpty()) {
            return [
                'success' => false,
                'message' => 'No unallocated leads remaining in queue to allocate.',
                'count' => 0,
            ];
        }

        // Preload current max leadcount for each telecaller
        $telecallerMaxCounts = [];
        foreach ($telecallers as $tc) {
            $emp = (string) $tc->emp_code;
            $telecallerMaxCounts[$emp] = (int) TelecallerLead::where('empcode', $emp)->max('leadcount');
        }

        DB::beginTransaction();
        try {
            foreach ($candidates as $idx => $lead) {
                $tcIndex = ($startIndex + $idx) % $numTelecallers;
                $targetTelecaller = $telecallers[$tcIndex];
                $targetEmpCode = (string) $targetTelecaller->emp_code;
                $targetName = $targetTelecaller->full_name ?? $targetEmpCode;

                $rawAppli = !empty($lead->applinano) ? (string) $lead->applinano : '0';
                $rawPhone = (string) ($lead->phoneno ?? '0');
                $candidateName = $lead->name ?: '0';
                $studentCrmId = $lead->crmid ?: '0';

                $tApplino = !empty($rawAppli) ? (string) $rawAppli : '0';
                $tPhoneno = !empty($rawPhone) ? (string) $rawPhone : '0';
                $tCrmid = !empty($studentCrmId) ? (string) $studentCrmId : '0';

                $telecallerMaxCounts[$targetEmpCode]++;
                $newLeadCount = $telecallerMaxCounts[$targetEmpCode];

                TelecallerLead::updateOrCreate(
                    ['applino' => $tApplino, 'phoneno' => $tPhoneno],
                    [
                        'crmid' => $tCrmid,
                        'empcode' => $targetEmpCode,
                        'leadcount' => $newLeadCount,
                        'assigndatetime' => $now,
                        'log_userid' => $logUser,
                        'log_ipaddress' => $ipAddress,
                        'log_timestamp' => $now,
                    ]
                );

                Lead::where('phoneno', $tPhoneno)->update([
                    'empcode' => $targetEmpCode,
                    'allocated_at' => $now,
                    'log_userid' => $logUser,
                ]);

                $allocatedCount++;
                if (count($allocatedApplis) < 50) {
                    $allocatedApplis[] = $tApplino !== '0' ? $tApplino : $tPhoneno;
                }
                $lastAllocatedEmpCode = $targetEmpCode;
                $perTelecallerSummary[$targetName] = ($perTelecallerSummary[$targetName] ?? 0) + 1;
            }

            if ($lastAllocatedEmpCode) {
                Cache::forever('auto_allocate_last_empcode', $lastAllocatedEmpCode);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => "Successfully auto-allocated " . number_format($allocatedCount) . " leads across {$numTelecallers} telecallers.",
                'count' => $allocatedCount,
                'allocated_applis' => $allocatedApplis,
                'summary' => $perTelecallerSummary,
                'last_empcode' => $lastAllocatedEmpCode,
                'metrics' => self::getMetrics(),
            ];
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('TelecallerService autoAllocateRoundRobin error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to auto-allocate leads: ' . $e->getMessage(),
                'count' => 0,
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
     * 3. Registration form completed (crm.student_registration) -> 'Register Form completed'
     * 4. Personal profile completed (crm.student_personal_info) -> 'Personal Details completed'
     * 5. Academic info completed (crm.student_academic_details) -> 'Academic Info completed'
     * 6. Document upload completed (crm.student_uploads) -> 'Document Upload completed'
     * 7. Fee payment completed (finance.paydetails / receiptdetails) -> 'Fee Payment completed'
     * 8. Exam slot booking completed (finance.exam_schedule - non-PhD only) -> 'Exam Slot Booking completed'
     */
    public static function determineLeadStage(
        ?string $applino,
        ?string $empcode,
        ?string $stage1 = null,
        ?string $stage2 = null,
        ?string $stage3 = null,
        ?string $stage4 = null,
        ?string $stage5 = null,
        bool $isPhd = false
    ): string {
        $hasAppli = !empty($applino) && $applino !== '0';
        $isAssigned = !empty($empcode) && $empcode !== '0';

        if (!$hasAppli && !$isAssigned) {
            return 'New Lead';
        }

        if (!$hasAppli && $isAssigned) {
            return 'Telecaller Stage';
        }

        // Non-PhD candidates have Step 5 as Exam Slot Booking
        if (!$isPhd && in_array(strtolower((string) $stage5), ['completed', 'approved'], true)) {
            return 'Exam Slot Booking completed';
        }

        // Step 4 is Fee Payment
        if (in_array(strtolower((string) $stage4), ['paid', 'completed', 'approved'], true) || in_array(strtolower((string) $stage1), ['paid'], true)) {
            return 'Fee Payment completed';
        }

        // Step 3 is Document Upload
        if (in_array(strtolower((string) $stage3), ['completed', 'approved'], true)) {
            return 'Document Upload completed';
        }

        // Step 2 is Academic Details
        if (in_array(strtolower((string) $stage2), ['completed', 'approved'], true)) {
            return 'Academic Info completed';
        }

        // Step 1 is Personal Profile
        if (in_array(strtolower((string) $stage1), ['completed', 'approved'], true)) {
            return 'Personal Details completed';
        }

        return 'Register Form completed';
    }

    /**
     * Badge visual styling for each lead stage.
     */
    public static function getStageBadgeStyle(string $stage): array
    {
        return match (strtolower(trim($stage))) {
            'new lead' => ['bg' => '#f1f5f9', 'color' => '#475569', 'border' => '#cbd5e1', 'icon' => ''],
            'telecaller stage' => ['bg' => '#fef3c7', 'color' => '#b45309', 'border' => '#fde68a', 'icon' => ''],
            'register form', 'register form completed', 'registration form completed' => ['bg' => '#e0e7ff', 'color' => '#3730a3', 'border' => '#c7d2fe', 'icon' => ''],
            'personal details', 'personal details completed', 'personal profile completed' => ['bg' => '#eff6ff', 'color' => '#1d4ed8', 'border' => '#bfdbfe', 'icon' => ''],
            'academic info', 'academic info completed', 'academic details', 'academic details completed' => ['bg' => '#f3e8ff', 'color' => '#7e22ce', 'border' => '#e9d5ff', 'icon' => ''],
            'document upload', 'document upload completed' => ['bg' => '#fef9c3', 'color' => '#854d0e', 'border' => '#fef08a', 'icon' => ''],
            'payment stage', 'payment stage completed', 'fee payment', 'fee payment completed' => ['bg' => '#ecfdf5', 'color' => '#065f46', 'border' => '#a7f3d0', 'icon' => ''],
            'exam slot booking', 'exam slot booking completed', 'slot booking completed' => ['bg' => '#f0fdf4', 'color' => '#15803d', 'border' => '#86efac', 'icon' => ''],
            default => ['bg' => '#f8fafc', 'color' => '#334155', 'border' => '#e2e8f0', 'icon' => ''],
        };
    }

    /**
     * Default stage dropdown permissions (Telecaller Stage is always enabled by default).
     *
     * @return array<string, bool>
     */
    public static function defaultAllowedStages(bool $isPhd = false): array
    {
        $stages = [
            'Telecaller Stage' => true,
            'Register Form completed' => false,
            'Personal Details completed' => false,
            'Academic Info completed' => false,
            'Document Upload completed' => false,
            'Fee Payment completed' => false,
        ];

        if (!$isPhd) {
            $stages['Exam Slot Booking completed'] = false;
        }

        // Backwards compatibility aliases
        $stages['Payment Stage completed'] = false;
        $stages['Academic Details completed'] = false;

        return $stages;
    }

    /**
     * Resolve the displayed Current Step from the student's completed tables and the telecaller's latest selected stage.
     *
     * @param  array<string, bool>  $allowedStages
     */
    public static function resolveCurrentStep(
        ?string $empcode,
        array $allowedStages,
        ?string $lastTaskStage = null,
        bool $isPhd = false
    ): string {
        $ranks = [
            'New Lead' => 0,
            'Telecaller Stage' => 1,
            'Register Form completed' => 2,
            'Personal Details completed' => 3,
            'Academic Info completed' => 4,
            'Academic Details completed' => 4,
            'Document Upload completed' => 5,
            'Fee Payment completed' => 6,
            'Payment Stage completed' => 6,
            'Exam Slot Booking completed' => 7,
        ];

        $aliases = [
            'Register Form' => 'Register Form completed',
            'Register Form completed' => 'Register Form completed',
            'Registration Form' => 'Register Form completed',
            'Registration Form completed' => 'Register Form completed',

            'Personal Details' => 'Personal Details completed',
            'Personal Details completed' => 'Personal Details completed',
            'Personal Profile' => 'Personal Details completed',
            'Personal Profile completed' => 'Personal Details completed',

            'Academic Details' => 'Academic Info completed',
            'Academic Details completed' => 'Academic Info completed',
            'Academic Info' => 'Academic Info completed',
            'Academic Info completed' => 'Academic Info completed',

            'Document Upload' => 'Document Upload completed',
            'Document Upload completed' => 'Document Upload completed',
            'Document upload' => 'Document Upload completed',
            'Document upload completed' => 'Document Upload completed',

            'Payment Stage' => 'Fee Payment completed',
            'Payment Stage completed' => 'Fee Payment completed',
            'Fee Payment' => 'Fee Payment completed',
            'Fee Payment completed' => 'Fee Payment completed',

            'Exam Slot Booking' => 'Exam Slot Booking completed',
            'Exam Slot Booking completed' => 'Exam Slot Booking completed',
            'Slot Booking' => 'Exam Slot Booking completed',
            'Slot Booking completed' => 'Exam Slot Booking completed',
        ];

        $studentStep = 'New Lead';
        if (!$isPhd && !empty($allowedStages['Exam Slot Booking completed'])) {
            $studentStep = 'Exam Slot Booking completed';
        } elseif (!empty($allowedStages['Fee Payment completed']) || !empty($allowedStages['Payment Stage completed'])) {
            $studentStep = 'Fee Payment completed';
        } elseif (!empty($allowedStages['Document Upload completed'])) {
            $studentStep = 'Document Upload completed';
        } elseif (!empty($allowedStages['Academic Info completed']) || !empty($allowedStages['Academic Details completed'])) {
            $studentStep = 'Academic Info completed';
        } elseif (!empty($allowedStages['Personal Details completed'])) {
            $studentStep = 'Personal Details completed';
        } elseif (!empty($allowedStages['Register Form completed'])) {
            $studentStep = 'Register Form completed';
        } elseif (!empty($empcode) && $empcode !== '0') {
            $studentStep = 'Telecaller Stage';
        }

        $taskStage = trim((string) $lastTaskStage);
        if (isset($aliases[$taskStage])) {
            $taskStage = $aliases[$taskStage];
        }

        if ($isPhd && $taskStage === 'Exam Slot Booking completed') {
            $taskStage = 'Fee Payment completed';
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
     * Checks dedicated database tables for each stage:
     * - crm.student_registration (Registration)
     * - crm.student_personal_info (Personal Profile)
     * - crm.student_academic_details (Academic Info)
     * - crm.student_uploads (Document Upload)
     * - finance.paydetails / receiptdetails (Fee Payment)
     * - finance.exam_schedule (Exam Slot Booking - non-PhD only)
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

        // 1. Registration Table (crm.student_registration)
        $students = CrmStudentRegistration::whereIn('applinano', $clean)
            ->get()
            ->keyBy('applinano');

        // 2. Personal Details Table (crm.student_personal_info)
        $personalSet = StudentPersonalInfo::whereIn('applino', $clean)
            ->pluck('applino')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        // 3. Academic Details Table (crm.student_academic_details)
        $academicSet = StudentAcademicDetail::whereIn('applino', $clean)
            ->pluck('applino')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        // 4. Document Uploads Table (crm.student_uploads)
        $uploadSet = StudentUpload::whereIn('applino', $clean)
            ->pluck('applino')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        // 5. Fee Payment (finance.receiptdetails & finance.paydetails)
        $receiptSet = ReceiptDetail::whereIn('applino', $clean)
            ->pluck('applino')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        $paySet = PayDetail::whereIn('applino', $clean)
            ->where('txnstatus', 'success')
            ->pluck('applino')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        // 6. Exam Slot Booking Table (finance.exam_schedule)
        $examSet = ExamSchedule::whereIn('applino', $clean)
            ->pluck('applino')
            ->mapWithKeys(fn ($v) => [(string) $v => true])
            ->all();

        $map = [];
        foreach ($clean as $app) {
            $student = $students[$app] ?? null;
            $progText = strtoupper(trim((string)($student?->programlevel ?? $student?->program_name ?? '')));
            $crmIdText = strtoupper(trim((string)($student?->crmid ?? '')));
            $isPhd = str_contains($progText, 'PHD') || str_contains($progText, 'PH.D') || str_contains($progText, 'DOCTOR') || str_starts_with($crmIdText, 'PHD');

            $isReg = isset($students[$app]);
            $hasPersonal = isset($personalSet[$app]) || in_array(strtolower($student?->stage1 ?? ''), ['completed', 'approved', 'paid']);
            $hasAcademic = isset($academicSet[$app]) || in_array(strtolower($student?->stage2 ?? ''), ['completed', 'approved', 'paid']);
            $hasUploads = isset($uploadSet[$app]) || in_array(strtolower($student?->stage3 ?? ''), ['completed', 'approved', 'paid']);
            $hasPayment = isset($receiptSet[$app]) || isset($paySet[$app]) || in_array(strtolower($student?->stage4 ?? ''), ['paid', 'completed', 'approved']) || in_array(strtolower($student?->stage1 ?? ''), ['paid']);
            $hasExam = !$isPhd && (isset($examSet[$app]) || in_array(strtolower($student?->stage5 ?? ''), ['completed', 'approved']));

            $stages = [
                'Telecaller Stage' => true,
                'Register Form completed' => $isReg,
                'Personal Details completed' => $hasPersonal,
                'Academic Info completed' => $hasAcademic,
                'Document Upload completed' => $hasUploads,
                'Fee Payment completed' => $hasPayment,
            ];

            if (!$isPhd) {
                $stages['Exam Slot Booking completed'] = $hasExam;
            }

            // Backwards-compatibility aliases
            $stages['Payment Stage completed'] = $hasPayment;
            $stages['Academic Details completed'] = $hasAcademic;
            $stages['is_phd'] = $isPhd;

            $map[$app] = $stages;
        }

        return $map;
    }

    /**
     * Resolve selectable action modal stages for a single application number.
     *
     * @return array<string, bool>
     */
    public static function getAllowedStagesForLead(?string $applino, bool $isPhd = false): array
    {
        $app = trim((string) $applino);
        if ($app === '' || $app === '0' || str_starts_with($app, 'APP')) {
            return self::defaultAllowedStages($isPhd);
        }

        $map = self::getStageEligibilityMap([$app]);

        return $map[$app] ?? self::defaultAllowedStages($isPhd);
    }
}

