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
                'leads.input' => 'input',
                'leads.allotted' => 'allotted',
                default => ($roleId === 4 ? 'allotted' : 'input'),
            };
        }

        // Fetch students / leads from database using Eloquent
        $searchQuery = trim($request->input('q', ''));
        $statusFilter = $request->input('status', 'all');
        $callerFilter = $request->input('caller', 'all');
        $allocStatusFilter = $request->input('alloc_status', 'all');
        $programFilter = $request->input('program', 'all');

        $allocatedApplis = TelecallerLead::pluck('applino');

        $leadsQuery = CrmStudentRegistration::with(['program', 'branch', 'campus', 'state', 'country']);
        if ($searchQuery) {
            $leadsQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'ilike', "%{$searchQuery}%")
                  ->orWhere('phoneno', 'ilike', "%{$searchQuery}%")
                  ->orWhere('applinano', 'ilike', "%{$searchQuery}%")
                  ->orWhere('crmid', 'ilike', "%{$searchQuery}%")
                  ->orWhere('emailid', 'ilike', "%{$searchQuery}%");
            });
        }
        if ($statusFilter && $statusFilter !== 'all') {
            if (in_array(strtolower($statusFilter), ['stage1','stage2','stage3','stage4','stage5','step1','step2','step3','step4','step5'])) {
                $col = str_replace('step', 'stage', strtolower($statusFilter));
                $leadsQuery->whereIn($col, ['completed', 'paid', 'approved', 'in_progress', 'active']);
            } else {
                $leadsQuery->where('std_status', $statusFilter);
            }
        }

        // On Leads Allocation tab, strictly show only UNALLOCATED leads so allocated ones clear out automatically
        if ($activeTab === 'allocation') {
            if ($allocatedApplis->isNotEmpty()) {
                $leadsQuery->whereNotIn('applinano', $allocatedApplis);
            }
        } elseif ($activeTab === 'input') {
            // On Lead input tab, reflect the entire dataset with optional allocation/telecaller/program filters
            if ($allocStatusFilter === 'allocated') {
                $leadsQuery->whereIn('applinano', $allocatedApplis);
            } elseif ($allocStatusFilter === 'unallocated' && $allocatedApplis->isNotEmpty()) {
                $leadsQuery->whereNotIn('applinano', $allocatedApplis);
            }

            if ($callerFilter && $callerFilter !== 'all') {
                $callerApplis = TelecallerLead::where('empcode', $callerFilter)->pluck('applino');
                $leadsQuery->whereIn('applinano', $callerApplis);
            }
        }

        if ($programFilter && $programFilter !== 'all') {
            $leadsQuery->where('program_id', $programFilter);
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

        // If CRM student registration is empty, fallback to crm.leads only when viewing full input without filters
        if ($leads->isEmpty() && $activeTab !== 'allocation' && !$searchQuery && $statusFilter === 'all' && $allocStatusFilter === 'all' && $callerFilter === 'all' && $programFilter === 'all') {
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
            return view('leads::partials.leads_table', compact('leads', 'dbAllocations', 'latestTasks', 'activeTab'));
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
            'callerFilter',
            'allocStatusFilter',
            'programFilter'
        ));
    }

    /**
     * Store a newly inputted lead directly from Lead input tab into crm.student_registration.
     */
    public function storeLeadInput(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phoneno' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'emailid' => ['nullable', 'email', 'max:150'],
            'program_id' => ['required', 'integer'],
            'empcode' => ['nullable', 'string', 'max:50'],
        ]);

        $phone = trim($request->input('phoneno'));
        $existing = CrmStudentRegistration::where('phoneno', $phone)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => "A lead with mobile +91 {$phone} already exists (App #{$existing->applinano}).",
            ], 422);
        }

        $maxApp = CrmStudentRegistration::max('applinano');
        $nextAppNo = is_numeric($maxApp) ? (string) ((int) $maxApp + 1) : '2027000001';
        $nextId = (CrmStudentRegistration::max('id') ?? 0) + 1;
        $programId = (int) $request->input('program_id');
        $prog = Program::find($programId);

        $student = CrmStudentRegistration::create([
            'id' => $nextId,
            'applinano' => $nextAppNo,
            'phoneno' => $phone,
            'name' => trim($request->input('name')),
            'emailid' => trim((string) $request->input('emailid', '')) ?: null,
            'program_id' => $programId,
            'programlevel' => $prog?->program_short_name ?? null,
            'campus_id' => 1,
            'std_status' => 'Interested',
            'stage1' => 'not_paid',
            'stage2' => 'locked',
            'stage3' => 'locked',
            'stage4' => 'locked',
            'stage5' => 'locked',
            'log_userid' => auth()->user()?->usercode ?? '02598',
            'log_ipaddress' => $request->ip() ?: '127.0.0.1',
            'log_timestamp' => now(),
        ]);

        $crmId = \App\Services\CrmIdService::generateForStudent($student);

        $empCode = trim((string) $request->input('empcode', ''));
        if ($empCode !== '') {
            \App\Services\TelecallerService::allocateLeads([
                'empcode' => $empCode,
                'allot_mode' => 'single',
                'applino' => $student->applinano,
                'phoneno' => $student->phoneno,
                'stage_name' => 'Stage 1: Calling & Verification',
                'stage_remark' => 'Assigned directly on Lead input',
                'log_userid' => auth()->user()?->usercode ?? '02598',
                'log_ipaddress' => $request->ip() ?: '127.0.0.1',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Lead [{$student->name}] added successfully (App #{$student->applinano}, CRM ID: {$crmId}).",
        ]);
    }

    /**
     * Update Stage Status (Stage 1 to 5) for a student registration from Telecaller wizard.
     */
    public function updateStage(Request $request, string $applinano)
    {
        $request->validate([
            'stage' => ['required', 'string', 'regex:/^stage[1-5]$/'],
            'status' => ['required', 'string', 'in:completed,in_progress,not_started,locked,not_paid,paid,approved,rejected'],
            'stage_name' => ['nullable', 'string', 'max:255'],
            'stage_calltime' => ['nullable', 'string', 'max:60'],
            'stage_callperson' => ['nullable', 'string', 'max:255'],
            'call_duration' => ['nullable', 'string', 'max:50'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $stageCol = $request->input('stage');
        $stageStatus = $request->input('status');
        $customStageName = trim((string) $request->input('stage_name', ''));
        $callTime = trim((string) $request->input('stage_calltime', ''));
        $callPerson = trim((string) $request->input('stage_callperson', ''));
        $callDuration = trim((string) $request->input('call_duration', ''));
        $remarks = trim((string) $request->input('remarks', ''));

        $fullRemark = $remarks ?: ('Stage status updated to ' . $stageStatus);
        if ($callDuration !== '') {
            $fullRemark = "[Duration: {$callDuration}] " . $fullRemark;
        }

        $student = CrmStudentRegistration::where('applinano', $applinano)->first();
        if ($student) {
            $student->{$stageCol} = $stageStatus;
            $student->remarks = $fullRemark;
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
                    'stage_name' => $customStageName ?: (strtoupper($stageCol) . ' (' . ucfirst($stageStatus) . ')'),
                    'stage_calltime' => $callTime ?: null,
                    'stage_remark' => $fullRemark,
                    'stage_callperson' => $callPerson ?: $student->name,
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
