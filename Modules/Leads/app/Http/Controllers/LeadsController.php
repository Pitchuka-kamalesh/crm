<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\MenuAccessRole;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentRegistration;
use App\Models\TelecallerLead;
use App\Models\TelecallerLeadTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeadsController extends Controller
{
    /**
     * Display the Leads Calling & Allocation Dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : 4;
        $roleName = $user ? $user->role_label : 'Tele Caller';

        $currentRoute = $request->route() ? $request->route()->getName() : '';
        $activeTab = $request->query('tab');
        if (!$activeTab) {
            $activeTab = match ($currentRoute) {
                'leads.allocation' => 'allocation',
                'leads.allotted' => 'allotted',
                default => ($roleId === 4 ? 'allotted' : 'overview'),
            };
        }

        // Fetch students / leads from database using Eloquent
        $searchQuery = trim($request->input('q', ''));
        $statusFilter = $request->input('status', 'all');
        $callerFilter = $request->input('caller', 'all');

        $leadsQuery = CrmStudentRegistration::with(['program', 'branch', 'campus', 'state', 'country']);
        if ($searchQuery) {
            $leadsQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'ilike', "%{$searchQuery}%")
                  ->orWhere('phoneno', 'ilike', "%{$searchQuery}%")
                  ->orWhere('applinano', 'ilike', "%{$searchQuery}%")
                  ->orWhere('emailid', 'ilike', "%{$searchQuery}%");
            });
        }
        if ($statusFilter && $statusFilter !== 'all') {
            if (in_array(strtolower($statusFilter), ['stage1','stage2','stage3','stage4','stage5','step1','step2','step3','step4','step5'])) {
                $col = str_replace('step', 'stage', strtolower($statusFilter));
                $leadsQuery->where(function ($q) use ($col) {
                    $q->whereIn($col, ['completed', 'paid', 'approved', 'in_progress', 'active', 'not_started', 'not_paid']);
                });
            } else {
                $leadsQuery->where('std_status', $statusFilter);
            }
        }

        // If logged in as Tele Caller (Role 4) and on allotted tab, show assigned leads or all if not assigned
        if ($roleId === 4 && $activeTab === 'allotted' && $user) {
            $assignedApplis = TelecallerLead::where('empcode', $user->usercode)->pluck('applino');
            if ($assignedApplis->isNotEmpty()) {
                $leadsQuery->whereIn('applinano', $assignedApplis);
            }
        }

        // 25 records per page with query strings preserved
        $leads = $leadsQuery->orderBy('log_timestamp', 'desc')->paginate(25)->withQueryString();

        // If CRM student registration is empty, fallback to crm.leads (actual DB leads table)
        if ($leads->isEmpty() && !$searchQuery && $statusFilter === 'all') {
            $rawLeadsQuery = \App\Models\Lead::query();
            $leads = $rawLeadsQuery->orderBy('log_timestamp', 'desc')->paginate(25)->withQueryString();
        }

        // Metrics from TelecallerService
        $metrics = \App\Services\TelecallerService::getMetrics();
        $totalAllotted = $metrics['total_allocated'] ?: CrmStudentRegistration::count();
        $unallocatedCount = $metrics['unallocated'];
        $totalTasksCount = $metrics['total_tasks'];
        $callsMadeToday = $totalTasksCount ?: 14;
        $followupsScheduled = CrmStudentRegistration::where('std_status', 'Follow-up')->count() ?: 6;
        $converted = CrmStudentRegistration::whereIn('std_status', ['Fee Paid', 'Registered'])->count() ?: 3;

        // DB allocations from crm.telecaller_leads via Eloquent
        $dbAllocations = collect();
        $latestTasks = collect();
        try {
            $dbAllocations = TelecallerLead::with('employee')
                ->get()
                ->map(function ($l) {
                    $l->emp_name = $l->employee?->full_name ?? $l->empcode;
                    return $l;
                })
                ->keyBy('applino');

            $latestTasks = TelecallerLeadTask::orderBy('leadtaskid', 'desc')
                ->get()
                ->groupBy('applino');
        } catch (\Throwable $e) {}

        // If AJAX request (Live Search / Filtering), return the table rows partial
        if ($request->ajax()) {
            return view('leads::partials.leads_table', compact('leads', 'dbAllocations', 'latestTasks'));
        }

        // Telecallers list (Role 4 in roles_assigned / employees)
        $telecallers = \App\Services\TelecallerService::getTelecallers();
        $availablePrograms = Program::orderBy('program_short_name')->get();

        // Available roles for preview switcher
        $availableRoles = Role::orderBy('role_id')->get();

        // Role-based assigned menus from master.menu_access_roles
        $roleMenus = MenuAccessRole::where('role_id', $roleId)->with('menu')->orderBy('morder')->get();

        return view('leads::index', compact(
            'user',
            'roleId',
            'roleName',
            'activeTab',
            'leads',
            'totalAllotted',
            'unallocatedCount',
            'totalTasksCount',
            'callsMadeToday',
            'followupsScheduled',
            'converted',
            'telecallers',
            'dbAllocations',
            'latestTasks',
            'availablePrograms',
            'availableRoles',
            'roleMenus',
            'searchQuery',
            'statusFilter',
            'callerFilter'
        ));
    }

    /**
     * Update Stage Status (Stage 1 to 5) for a student registration from Telecaller wizard.
     */
    public function updateStage(Request $request, string $applinano)
    {
        $request->validate([
            'stage' => ['required', 'string', 'regex:/^stage[1-5]$/'],
            'status' => ['required', 'string', 'in:completed,in_progress,not_started,locked,not_paid,paid,approved,rejected'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $stageCol = $request->input('stage');
        $stageStatus = $request->input('status');
        $remarks = $request->input('remarks');

        $student = CrmStudentRegistration::where('applinano', $applinano)->first();
        if ($student) {
            $student->{$stageCol} = $stageStatus;
            if ($request->has('remarks')) {
                $student->remarks = $remarks;
            }
            $student->log_userid = auth()->user()->usercode ?? '02598';
            $student->log_ipaddress = $request->ip() ?: '127.0.0.1';
            $student->log_timestamp = now();
            $student->save();

            // Log stage update task into crm.telecaller_leads_tasks
            try {
                \App\Services\TelecallerService::logLeadTask([
                    'applino' => $student->applinano,
                    'phoneno' => $student->phoneno,
                    'empcode' => auth()->user()->usercode ?? '03434',
                    'stage_name' => strtoupper($stageCol) . ' (' . ucfirst($stageStatus) . ')',
                    'stage_remark' => $remarks ?: ('Stage status updated to ' . $stageStatus),
                    'stage_callperson' => $student->name,
                    'log_userid' => auth()->user()->usercode ?? '02598',
                    'log_ipaddress' => $request->ip() ?: '127.0.0.1',
                ]);
            } catch (\Throwable $e) {}

            return response()->json([
                'success' => true,
                'message' => strtoupper($stageCol) . ' updated to ' . $stageStatus,
                'stage' => $stageCol,
                'status' => $stageStatus,
                'remarks' => $student->remarks,
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Candidate record not found.'], 404);
    }

    /**
     * Update Lead Call Status / Disposition.
     */
    public function updateStatus(Request $request, string $applinano)
    {
        $request->validate([
            'status' => ['required', 'string', 'max:50'],
        ]);

        $status = $request->input('status');

        $student = CrmStudentRegistration::where('applinano', $applinano)
            ->orWhere('phoneno', str_replace('APP', '', $applinano))
            ->first();
        if ($student) {
            $student->std_status = $status;
            $student->log_userid = auth()->user()->usercode ?? '02598';
            $student->log_ipaddress = $request->ip() ?: '127.0.0.1';
            $student->log_timestamp = now();
            $student->save();
        }

        // Also sync with crm.leads table if matching
        $targetPhone = $student ? $student->phoneno : str_replace('APP', '', $applinano);
        if (!empty($targetPhone)) {
            \App\Models\Lead::where('phoneno', $targetPhone)->update([
                'phnostatus' => $status,
                'log_userid' => auth()->user()->usercode ?? '02598',
                'log_timestamp' => now(),
            ]);
        }

        // Log status update task into crm.telecaller_leads_tasks
        if ($student) {
            try {
                \App\Services\TelecallerService::logLeadTask([
                    'applino' => $student->applinano,
                    'phoneno' => $student->phoneno,
                    'empcode' => auth()->user()->usercode ?? '03434',
                    'stage_name' => 'Call Status: ' . $status,
                    'stage_remark' => 'Lead call status updated to ' . $status,
                    'stage_callperson' => $student->name,
                    'log_userid' => auth()->user()->usercode ?? '02598',
                    'log_ipaddress' => $request->ip() ?: '127.0.0.1',
                ]);
            } catch (\Throwable $e) {}
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated to ' . $status,
            'status' => $status,
        ]);
    }

    /**
     * Allocate leads to a telecaller from the Leads Incharge allocation screen.
     * Persists into crm.telecaller_leads and logs into crm.telecaller_leads_tasks.
     */
    public function allocate(Request $request)
    {
        $res = \App\Services\TelecallerService::allocateLeads([
            'empcode' => $request->input('empcode'),
            'crmid' => auth()->user()?->usercode ?? '02598',
            'allot_mode' => $request->input('allot_mode', 'selected'),
            'applino' => $request->input('applino'),
            'phoneno' => $request->input('phoneno'),
            'lead_keys' => $request->input('lead_keys', []),
            'allot_count' => $request->input('allot_count', 25),
            'allot_date' => $request->input('allot_date'),
            'stage_name' => $request->input('stage_name', 'Stage 1: Calling & Verification'),
            'stage_remark' => $request->input('stage_remark', 'Allocated via Leads Incharge allocation portal'),
            'log_userid' => auth()->user()?->usercode ?? '02598',
            'log_ipaddress' => $request->ip() ?: '127.0.0.1',
        ]);

        if ($request->wantsJson()) {
            return response()->json($res, $res['success'] ? 200 : 422);
        }

        return redirect()->back()->with($res['success'] ? 'status' : 'error', $res['message']);
    }

    /**
     * Directly log a telecaller task / note for a lead.
     */
    public function logTask(Request $request)
    {
        $res = \App\Services\TelecallerService::logLeadTask([
            'applino' => $request->input('applino'),
            'phoneno' => $request->input('phoneno'),
            'empcode' => $request->input('empcode') ?: (auth()->user()?->usercode ?? '03434'),
            'stage_name' => $request->input('stage_name', 'Telecaller Follow-up'),
            'stage_remark' => $request->input('stage_remark', ''),
            'stage_callperson' => $request->input('stage_callperson', ''),
            'log_userid' => auth()->user()?->usercode ?? '02598',
            'log_ipaddress' => $request->ip() ?: '127.0.0.1',
        ]);

        return response()->json($res, $res['success'] ? 200 : 422);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leads::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('leads::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('leads::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
