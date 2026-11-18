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

        // Pre-fetch completed payment indicators (payment completed = lead completed)
        $paidApplis = DB::table('finance.paydetails')
            ->where(function ($q) {
                $q->where(DB::raw('LOWER(txnstatus)'), 'success')
                  ->orWhere(DB::raw('LOWER(paymentstatus)'), 'success');
            })
            ->pluck('applino')
            ->filter()
            ->map(fn($a) => (string)$a)
            ->unique();

        $regPaidRecords = DB::table('crm.student_registration')
            ->where(function ($q) {
                $q->where(DB::raw('LOWER(stage1)'), 'paid')
                  ->orWhere(DB::raw('LOWER(std_status)'), 'fee paid')
                  ->orWhere(DB::raw('LOWER(std_status)'), 'admitted');
            })
            ->get(['phoneno', 'applinano']);

        $regPaidPhones = $regPaidRecords->pluck('phoneno')->filter()->map(fn($p) => preg_replace('/\D/', '', (string)$p))->unique();
        $regPaidApplis = $regPaidRecords->pluck('applinano')->filter()->map(fn($a) => (string)$a)->unique();

        // Calculate completed vs incompleted leads strictly based on payment completion
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

            // Lead is completed whenever payment is completed
            $isPaid = false;
            if ($appNo !== '0' && $paidApplis->contains($appNo)) {
                $isPaid = true;
            } elseif ($regPaidPhones->contains($cleanPhone)) {
                $isPaid = true;
            } elseif ($appNo !== '0' && $regPaidApplis->contains($appNo)) {
                $isPaid = true;
            }

            if ($isPaid) {
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

        // 3. Counselor Lead Outreach Directory List (Built early so lead stages can be aggregated for Card 2)
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

        // Group allotted leads by their current call stage (for exact allotted lead distribution in Card 2)
        $globalLeadStages = $telecallerLeadsList->groupBy('latest_stage')->map(function ($group, $stage) {
            return [
                'stage_name' => $stage,
                'count'      => $group->count(),
            ];
        })->sortByDesc('count')->values();

        $leadStagesByEmp = $telecallerLeadsList->groupBy('empcode')->map(function ($empLeads) {
            return $empLeads->groupBy('latest_stage')->map(function ($group, $stage) {
                return [
                    'stage_name' => $stage,
                    'count'      => $group->count(),
                ];
            })->sortByDesc('count')->values();
        });

        // Build individual telecaller stats for dropdown filtering and live JS switching
        $telecallerStats = $telecallers->map(function ($emp) use ($leadCountsByEmp, $taskCountsByEmp, $contactedByEmp, $stagesRaw, $stepStatsByEmp, $leadStagesByEmp) {
            $empCode = (string)$emp->emp_code;
            $leadsCount = (int)($leadCountsByEmp[$empCode] ?? 0);
            $tasksCount = (int)($taskCountsByEmp[$empCode] ?? 0);
            $contactedCount = (int)($contactedByEmp[$empCode] ?? 0);
            $pendingCount = max(0, $leadsCount - $contactedCount);
            $empStages = ($stagesRaw->get($empCode) ?? collect())->values();
            $empLeadStages = ($leadStagesByEmp->get($empCode) ?? collect())->values();
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
                'lead_stages'     => $empLeadStages,
                'allocated_completed'     => (int)$stepStats['completed'],
                'allocated_not_completed' => (int)$stepStats['not_completed'],
                'status'          => $leadsCount > 0 ? 'Active' : 'Standby',
            ];
        });

        // 4. All Call Tasks for Date/Month/Year Analytics
        $allTasksRaw = DB::table('crm.telecaller_leads_tasks')
            ->select(
                'leadtaskid',
                'empcode',
                'phoneno',
                'applino',
                'stage_name',
                'stage_calltime',
                'log_timestamp',
                'stage_call_duration',
                'stage_remark'
            )
            ->when($isTelecaller, fn($q) => $q->where('empcode', $userEmpCode))
            ->orderBy('stage_calltime', 'desc')
            ->get();

        $allTasksData = $allTasksRaw->map(function ($t) {
            $rawTime = $t->stage_calltime ?: $t->log_timestamp;
            $ts = $rawTime ? strtotime($rawTime) : time();
            return [
                'id'             => (int)$t->leadtaskid,
                'empcode'        => (string)$t->empcode,
                'phone'          => (string)$t->phoneno,
                'applino'        => (string)($t->applino ?? ''),
                'stage'          => (string)($t->stage_name ?: 'Telecaller Stage'),
                'year'           => (int)date('Y', $ts),
                'month'          => (int)date('n', $ts),
                'month_name'     => date('M', $ts),
                'day'            => (int)date('j', $ts),
                'date_str'       => date('Y-m-d', $ts),
                'date_label'     => date('d M', $ts),
                'date_formatted' => date('d M Y', $ts),
                'time_str'       => date('h:i A', $ts),
                'duration'       => (string)($t->stage_call_duration ?: 'N/A'),
                'remark'         => (string)($t->stage_remark ?: ''),
            ];
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
            'globalLeadStages',
            'telecallerStats',
            'telecallerLeadsList',
            'totalAllStepsCompleted',
            'totalAllStepsNotCompleted',
            'allTasksData'
        ));
    }
}
