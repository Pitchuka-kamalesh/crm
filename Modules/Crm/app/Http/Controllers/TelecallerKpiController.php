<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Models\Employee;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\MenuAccessRole;
use Modules\Auth\Services\RoleService;
use Modules\Leads\Models\TelecallerLead;
use Modules\Leads\Models\TelecallerLeadTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TelecallerKpiController extends Controller
{
    /**
     * Display the Telecaller KPI & Outreach Performance dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : RoleService::defaultStaffRoleId();
        $roleName = $user ? $user->role_label : 'CRM Official';
        $activeTab = 'telecallerkpi';
        $isTelecaller = RoleService::isTelecaller($roleId);
        $userEmpCode = (string)($user?->usercode ?? '');

        // Role-based assigned menus for sidebar
        $roleMenus = collect();
        try {
            $roleMenus = MenuAccessRole::forRole($roleId);
        } catch (\Throwable $e) {}

        // Available roles for UI role switcher
        $availableRoles = collect();
        try {
            $userAssigned = RoleService::getAssignedRoles($user->usercode ?? '');
            $availableRoles = !empty($userAssigned)
                ? collect($userAssigned)->map(fn($r) => (object)['role_id' => (int)$r['role_id'], 'role_name' => $r['role_name']])
                : Role::where('role_id', $roleId)->get();
        } catch (\Throwable $e) {}

        // 1. Identify Telecallers pool
        if ($isTelecaller) {
            $telecallers = Employee::where('emp_code', $userEmpCode)->get();
            if ($telecallers->isEmpty()) {
                $telecallers = collect([(object)[
                    'emp_code'        => $userEmpCode,
                    'full_name'       => $user?->name ?? 'Telecaller',
                    'ofc_mailid'      => $user?->email ?? 'N/A',
                    'personal_mailid' => 'N/A',
                    'phone_primary'   => $user?->phoneno ?? 'N/A',
                ]]);
            }
            $selectedTelecaller = $userEmpCode;
        } else {
            $telecallers = Employee::whereIn('emp_code', function ($q) {
                $q->select('emp_id')->from('master.roles_assigned')->where('roles_role_id', 4);
            })->orWhere('role_id', 4)
            ->orWhereIn('emp_code', function ($q) {
                $q->select('empcode')->from('crm.telecaller_leads');
            })->orderBy('full_name')->get();

            $selectedTelecaller = (string)$request->query('telecaller', $request->query('employee', 'all'));
        }

        // 2. Pre-fetch aggregations grouped by empcode
        $leadCountsByEmp = TelecallerLead::select('empcode', DB::raw('count(*) as cnt'))
            ->when($isTelecaller, fn($q) => $q->where('empcode', $userEmpCode))
            ->groupBy('empcode')
            ->pluck('cnt', 'empcode');

        $taskCountsByEmp = TelecallerLeadTask::select('empcode', DB::raw('count(*) as cnt'))
            ->when($isTelecaller, fn($q) => $q->where('empcode', $userEmpCode))
            ->groupBy('empcode')
            ->pluck('cnt', 'empcode');

        $contactedByEmp = TelecallerLeadTask::select('empcode', DB::raw('count(DISTINCT phoneno) as cnt'))
            ->when($isTelecaller, fn($q) => $q->where('empcode', $userEmpCode))
            ->whereNotNull('phoneno')
            ->groupBy('empcode')
            ->pluck('cnt', 'empcode');

        // Task stages grouped by empcode
        $stagesRaw = DB::table('crm.telecaller_leads_tasks')
            ->select('empcode', 'stage_name', DB::raw('count(*) as count'))
            ->when($isTelecaller, fn($q) => $q->where('empcode', $userEmpCode))
            ->whereNotNull('stage_name')
            ->groupBy('empcode', 'stage_name')
            ->orderBy('count', 'desc')
            ->get()
            ->groupBy('empcode');

        // Pre-fetch completed indicators across the system for "all steps completed" vs "not completed"
        $completedFromReg = DB::table('crm.student_registration')
            ->where(function ($q) {
                $q->whereIn(DB::raw('LOWER(stage1)'), ['paid', 'completed'])
                  ->whereIn(DB::raw('LOWER(stage2)'), ['completed', 'na'])
                  ->whereIn(DB::raw('LOWER(stage3)'), ['completed', 'na']);
            })
            ->orWhere('std_status', 'Admitted')
            ->get(['phoneno', 'applinano']);

        $regCompletedPhones = $completedFromReg->pluck('phoneno')->filter()->map(fn($p) => preg_replace('/\D/', '', (string)$p))->unique();
        $regCompletedApplis = $completedFromReg->pluck('applinano')->filter()->map(fn($a) => (string)$a)->unique();

        $examScheduled = DB::table('exam_schedule')->get(['phoneno', 'applino']);
        $examPhones = $examScheduled->pluck('phoneno')->filter()->map(fn($p) => preg_replace('/\D/', '', (string)$p))->unique();
        $examApplis = $examScheduled->pluck('applino')->filter()->map(fn($a) => (string)$a)->unique();

        $paidApplis = DB::table('finance.paydetails')
            ->where(function ($q) {
                $q->where('txnstatus', 'success')->orWhere('paymentstatus', 'SUCCESS');
            })
            ->pluck('applino')
            ->filter()
            ->map(fn($a) => (string)$a)
            ->unique();

        $personalPhones = DB::table('crm.student_personal_info')->pluck('phoneno')->map(fn($p) => preg_replace('/\D/', '', (string)$p))->unique();
        $personalApplis = DB::table('crm.student_personal_info')->pluck('applino')->map(fn($a) => (string)$a)->unique();

        $academicPhones = DB::table('crm.student_academic_details')->pluck('phoneno')->map(fn($p) => preg_replace('/\D/', '', (string)$p))->unique();
        $academicApplis = DB::table('crm.student_academic_details')->pluck('applino')->map(fn($a) => (string)$a)->unique();

        $completedTaskStages = [
            'academic details completed',
            'admission confirmed',
            'admitted',
            'all steps completed',
            'completed',
            'exam slot booked',
        ];
        $taskCompleted = DB::table('crm.telecaller_leads_tasks')
            ->whereIn(DB::raw('LOWER(stage_name)'), $completedTaskStages)
            ->get(['phoneno', 'applino']);
        $taskCompletedPhones = $taskCompleted->pluck('phoneno')->filter()->map(fn($p) => preg_replace('/\D/', '', (string)$p))->unique();
        $taskCompletedApplis = $taskCompleted->pluck('applino')->filter()->map(fn($a) => (string)$a)->unique();

        // Calculate completed vs not completed per allocated lead
        $allAllocLeads = TelecallerLead::when($isTelecaller, fn($q) => $q->where('empcode', $userEmpCode))
            ->select('empcode', 'phoneno', 'applino')
            ->get();

        $stepStatsByEmp = [];
        $totalAllStepsCompleted = 0;
        $totalAllStepsNotCompleted = 0;

        foreach ($allAllocLeads as $lead) {
            $emp = (string)$lead->empcode;
            $cleanPhone = preg_replace('/\D/', '', (string)$lead->phoneno);
            $appNo = (string)($lead->applino ?? '0');

            if (!isset($stepStatsByEmp[$emp])) {
                $stepStatsByEmp[$emp] = [
                    'allocated' => 0,
                    'completed' => 0,
                    'not_completed' => 0,
                ];
            }
            $stepStatsByEmp[$emp]['allocated']++;

            $isCompleted = false;
            if ($regCompletedPhones->contains($cleanPhone) || ($appNo !== '0' && $regCompletedApplis->contains($appNo))) {
                $isCompleted = true;
            } elseif ($examPhones->contains($cleanPhone) || ($appNo !== '0' && $examApplis->contains($appNo))) {
                $isCompleted = true;
            } elseif ($taskCompletedPhones->contains($cleanPhone) || ($appNo !== '0' && $taskCompletedApplis->contains($appNo))) {
                $isCompleted = true;
            } elseif ($appNo !== '0' && $paidApplis->contains($appNo)) {
                $hasPersonal = $personalPhones->contains($cleanPhone) || $personalApplis->contains($appNo);
                $hasAcademic = $academicPhones->contains($cleanPhone) || $academicApplis->contains($appNo);
                if ($hasPersonal && $hasAcademic) {
                    $isCompleted = true;
                }
            }

            if ($isCompleted) {
                $stepStatsByEmp[$emp]['completed']++;
                $totalAllStepsCompleted++;
            } else {
                $stepStatsByEmp[$emp]['not_completed']++;
                $totalAllStepsNotCompleted++;
            }
        }

        // Overall Global Aggregates (or Scoped to user if Telecaller)
        if ($isTelecaller) {
            $totalTelecallerLeads = (int)($leadCountsByEmp[$userEmpCode] ?? 0);
            $teleTasksCount = (int)($taskCountsByEmp[$userEmpCode] ?? 0);
            $teleDistinctContacted = (int)($contactedByEmp[$userEmpCode] ?? 0);
            $telePendingCount = max(0, $totalTelecallerLeads - $teleDistinctContacted);

            $teleTaskStages = DB::table('crm.telecaller_leads_tasks')
                ->select('stage_name', DB::raw('count(*) as count'))
                ->where('empcode', $userEmpCode)
                ->whereNotNull('stage_name')
                ->groupBy('stage_name')
                ->orderBy('count', 'desc')
                ->get();
        } else {
            $totalTelecallerLeads = TelecallerLead::count();
            $teleTasksCount = TelecallerLeadTask::count();
            $teleDistinctContacted = TelecallerLeadTask::whereNotNull('phoneno')->distinct('phoneno')->count('phoneno');
            $telePendingCount = max(0, $totalTelecallerLeads - $teleDistinctContacted);

            $teleTaskStages = DB::table('crm.telecaller_leads_tasks')
                ->select('stage_name', DB::raw('count(*) as count'))
                ->whereNotNull('stage_name')
                ->groupBy('stage_name')
                ->orderBy('count', 'desc')
                ->get();
        }

        // Build individual telecaller stats for dropdown filtering and live JS switching
        $telecallerStats = $telecallers->map(function ($emp) use ($leadCountsByEmp, $taskCountsByEmp, $contactedByEmp, $stagesRaw, $stepStatsByEmp) {
            $empCode = (string)$emp->emp_code;
            $leadsCount = (int)($leadCountsByEmp[$empCode] ?? 0);
            $tasksCount = (int)($taskCountsByEmp[$empCode] ?? 0);
            $contactedCount = (int)($contactedByEmp[$empCode] ?? 0);
            $pendingCount = max(0, $leadsCount - $contactedCount);
            $empStages = ($stagesRaw->get($empCode) ?? collect())->values();
            $stepStats = $stepStatsByEmp[$empCode] ?? ['allocated' => $leadsCount, 'completed' => 0, 'not_completed' => $leadsCount];

            return (object)[
                'emp_code'        => $empCode,
                'name'            => $emp->full_name,
                'email'           => $emp->ofc_mailid ?: ($emp->personal_mailid ?: 'N/A'),
                'phone'           => $emp->phone_primary ?: 'N/A',
                'leads_count'     => $leadsCount,
                'tasks_count'     => $tasksCount,
                'contacted_count' => $contactedCount,
                'pending_count'   => $pendingCount,
                'stages'          => $empStages,
                'allocated_completed'     => (int)$stepStats['completed'],
                'allocated_not_completed' => (int)$stepStats['not_completed'],
                'status'          => $leadsCount > 0 ? 'Active' : 'Standby',
            ];
        });

        // 3. Counselor Lead Outreach Directory List
        $leadsQuery = DB::table('crm.telecaller_leads as tl')
            ->leftJoin('master.employees as e', 'e.emp_code', '=', 'tl.empcode')
            ->select(
                'tl.applino',
                'tl.phoneno',
                'tl.empcode',
                'tl.assigndatetime',
                'tl.leadcount',
                'e.full_name as emp_name',
                'e.phone_primary as emp_phone',
                'e.ofc_mailid as emp_email'
            );

        if ($isTelecaller) {
            $leadsQuery->where('tl.empcode', $userEmpCode);
        }

        $telecallerLeadsList = $leadsQuery
            ->orderBy('tl.leadcount', 'asc')
            ->take(500)
            ->get();

        // Batch load latest tasks and call counts for directory leads
        $leadPhones = $telecallerLeadsList->pluck('phoneno')->filter()->unique()->values()->all();

        $callCountsByPhone = DB::table('crm.telecaller_leads_tasks')
            ->whereIn('phoneno', $leadPhones)
            ->select('phoneno', DB::raw('count(*) as cnt'))
            ->groupBy('phoneno')
            ->pluck('cnt', 'phoneno');

        $latestTasks = DB::table('crm.telecaller_leads_tasks')
            ->whereIn('phoneno', $leadPhones)
            ->orderBy('leadtaskid', 'desc')
            ->get()
            ->groupBy('phoneno');

        $telecallerLeadsList = $telecallerLeadsList->map(function ($lead) use ($callCountsByPhone, $latestTasks) {
            $phone = (string)$lead->phoneno;
            $tasksForLead = $latestTasks->get($phone);
            $latestTask = $tasksForLead ? $tasksForLead->first() : null;

            $lead->call_count = (int)($callCountsByPhone[$phone] ?? 0);
            $lead->latest_stage = $latestTask ? $latestTask->stage_name : 'Pending Call';
            $lead->latest_calltime = $latestTask ? $latestTask->stage_calltime : null;
            $lead->latest_duration = $latestTask ? $latestTask->stage_call_duration : null;
            $lead->latest_remark = $latestTask ? ($latestTask->stage_remark ?: 'Call logged') : 'No calls recorded yet';
            $lead->call_person = $latestTask ? $latestTask->stage_callperson : null;

            return $lead;
        });

        return view('crm::kpi.telecallerkpi', compact(
            'user',
            'roleId',
            'roleName',
            'activeTab',
            'isTelecaller',
            'roleMenus',
            'availableRoles',
            'selectedTelecaller',
            'totalTelecallerLeads',
            'teleTasksCount',
            'teleDistinctContacted',
            'telePendingCount',
            'teleTaskStages',
            'telecallerStats',
            'telecallerLeadsList',
            'totalAllStepsCompleted',
            'totalAllStepsNotCompleted'
        ));
    }
}
