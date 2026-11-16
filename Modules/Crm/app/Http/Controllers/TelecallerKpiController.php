<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Models\Employee;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\MenuAccessRole;
use Modules\Auth\Services\RoleService;
use Modules\Leads\Models\TelecallerLead;
use Modules\Leads\Models\TelecallerLeadTask;
use Modules\Finance\Models\PayDetail;
use Modules\Finance\Models\ReceiptDetail;
use Modules\Crm\Models\CrmStudentRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TelecallerKpiController extends Controller
{
    /**
     * Display the Telecaller KPI dashboard, Counselor Leaderboard & Outreach Analytics.
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

        // 1. Retrieve telecallers: if logged in as telecaller, strictly isolate to their own profile
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
        } else {
            $telecallers = Employee::whereIn('emp_code', function ($q) {
                $q->select('emp_id')->from('master.roles_assigned')->where('roles_role_id', 4);
            })->orWhereIn('emp_code', function ($q) {
                $q->select('empcode')->from('crm.telecaller_leads');
            })->orderBy('full_name')->get();
        }

        // 2. Verified fee payments from finance.paydetails and finance.receiptdetails
        $pdPaid = PayDetail::where(function ($q) {
            $q->where('txnstatus', 'success')
              ->orWhere('paidamount', '>', 0);
        })->pluck('applino')->map(fn($v) => (string)$v);

        $rdPaid = ReceiptDetail::pluck('applino')->map(fn($v) => (string)$v);
        $verifiedPaidAppNos = $pdPaid->merge($rdPaid)->unique()->filter()->values();

        // 3. Pre-fetch aggregations: strictly scoped to the telecaller if role 4
        if ($isTelecaller) {
            $leadCountsByEmp = TelecallerLead::where('empcode', $userEmpCode)
                ->select('empcode', DB::raw('count(*) as cnt'))
                ->groupBy('empcode')
                ->pluck('cnt', 'empcode');

            $taskCountsByEmp = TelecallerLeadTask::where('empcode', $userEmpCode)
                ->select('empcode', DB::raw('count(*) as cnt'))
                ->groupBy('empcode')
                ->pluck('cnt', 'empcode');

            $allLeadsByEmp = TelecallerLead::where('empcode', $userEmpCode)
                ->select('empcode', 'applino')
                ->get()
                ->groupBy('empcode');

            $totalAllocatedLeads = (int)($leadCountsByEmp[$userEmpCode] ?? 0);
            $totalOutreachCalls = (int)($taskCountsByEmp[$userEmpCode] ?? 0);
        } else {
            $leadCountsByEmp = TelecallerLead::select('empcode', DB::raw('count(*) as cnt'))
                ->groupBy('empcode')
                ->pluck('cnt', 'empcode');

            $taskCountsByEmp = TelecallerLeadTask::select('empcode', DB::raw('count(*) as cnt'))
                ->groupBy('empcode')
                ->pluck('cnt', 'empcode');

            $allLeadsByEmp = TelecallerLead::select('empcode', 'applino')->get()->groupBy('empcode');

            // Total allocated leads across the system
            $totalAllocatedLeads = TelecallerLead::count();
            $totalOutreachCalls = TelecallerLeadTask::count();
        }

        // 4. Calculate counselor statistics for executive leaderboard (or single telecaller summary)
        $telecallerLeaderboard = $telecallers->map(function ($emp) use ($leadCountsByEmp, $taskCountsByEmp, $allLeadsByEmp, $verifiedPaidAppNos) {
            $empCode = (string)$emp->emp_code;
            $allocatedCount = (int)($leadCountsByEmp[$empCode] ?? 0);
            $completedCalls = (int)($taskCountsByEmp[$empCode] ?? 0);
            
            // Leads that have not had completed outreach calls yet
            $pendingFollowups = max(0, $allocatedCount - $completedCalls);

            // Fee paid conversions from finance.paydetails for this counselor's allocated leads
            $empLeads = $allLeadsByEmp[$empCode] ?? collect();
            $paidConversions = $empLeads->filter(fn($l) => $verifiedPaidAppNos->contains((string)$l->applino))->count();

            $conversionRate = $allocatedCount > 0 ? round(($paidConversions / $allocatedCount) * 100, 1) : 0;

            return (object)[
                'emp_code'          => $empCode,
                'name'              => $emp->full_name,
                'email'             => $emp->ofc_mailid ?: ($emp->personal_mailid ?: 'N/A'),
                'phone'             => $emp->phone_primary ?: 'N/A',
                'allocated_leads'   => $allocatedCount,
                'completed_calls'   => $completedCalls,
                'pending_followups' => $pendingFollowups,
                'paid_conversions'  => $paidConversions,
                'conversion_rate'   => $conversionRate,
                'status'            => $allocatedCount > 0 ? 'Active' : 'Standby',
            ];
        })->sortByDesc('paid_conversions')->values();

        // Total conversions across telecaller pool
        $totalTelecallerConversions = $telecallerLeaderboard->sum('paid_conversions');
        $overallConversionRate = $totalAllocatedLeads > 0 ? round(($totalTelecallerConversions / $totalAllocatedLeads) * 100, 1) : 0;

        // 5. Call disposition stage breakdown: scoped to this telecaller if role 4
        $stageQuery = TelecallerLeadTask::select('stage_name', DB::raw('count(*) as count'))
            ->whereNotNull('stage_name');

        if ($isTelecaller) {
            $stageQuery->where('empcode', $userEmpCode);
        }

        $stageBreakdown = $stageQuery->groupBy('stage_name')
            ->orderByDesc('count')
            ->pluck('count', 'stage_name');

        if ($stageBreakdown->isEmpty()) {
            $stageBreakdown = collect(['Initial Contact' => 0, 'Follow-up Scheduled' => 0, 'Completed' => 0]);
        }

        // 6. Interactive Employee Filter & Detailed Outreach Leads Table
        $filterEmp = $isTelecaller ? $userEmpCode : $request->input('employee');
        $search = trim($request->input('q', ''));

        $detailQuery = TelecallerLead::with([
            'employee',
            'student.branch',
            'student.program',
            'tasks' => fn($q) => $q->latest('log_timestamp'),
        ]);

        if ($isTelecaller) {
            $detailQuery->where('empcode', $userEmpCode);
        } elseif ($filterEmp && $filterEmp !== 'all') {
            $detailQuery->where('empcode', $filterEmp);
        }

        if ($search !== '') {
            $detailQuery->where(function ($q) use ($search) {
                $q->where('applino', 'like', "%{$search}%")
                  ->orWhere('phoneno', 'like', "%{$search}%")
                  ->orWhere('crmid', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $leadsTable = $detailQuery->latest('assigndatetime')->paginate(20)->withQueryString();

        return view('crm::kpi.telecallerkpi', compact(
            'user',
            'roleId',
            'roleName',
            'activeTab',
            'isTelecaller',
            'roleMenus',
            'availableRoles',
            'telecallers',
            'telecallerLeaderboard',
            'totalAllocatedLeads',
            'totalOutreachCalls',
            'totalTelecallerConversions',
            'overallConversionRate',
            'stageBreakdown',
            'verifiedPaidAppNos',
            'leadsTable',
            'filterEmp',
            'search'
        ));
    }
}
