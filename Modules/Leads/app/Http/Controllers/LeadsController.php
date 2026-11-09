<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\MenuAccessRole;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentRegistration;
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

        $leadsQuery = CrmStudentRegistration::with(['program', 'branch', 'campus', 'state', 'country']);
        if ($searchQuery) {
            $leadsQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'ilike', "%{$searchQuery}%")
                  ->orWhere('phoneno', 'ilike', "%{$searchQuery}%")
                  ->orWhere('applinano', 'ilike', "%{$searchQuery}%")
                  ->orWhere('emailid', 'ilike', "%{$searchQuery}%");
            });
        }
        if ($statusFilter !== 'all') {
            $leadsQuery->where('std_status', $statusFilter);
        }

        // 25 records per page with query strings preserved
        $leads = $leadsQuery->orderBy('log_timestamp', 'desc')->paginate(25)->withQueryString();

        // If CRM student registration is empty, fallback to crm.leads (actual DB leads table)
        if ($leads->isEmpty() && !$searchQuery && $statusFilter === 'all') {
            $rawLeadsQuery = \App\Models\Lead::query();
            $leads = $rawLeadsQuery->orderBy('log_timestamp', 'desc')->paginate(25)->withQueryString();
        }

        // Telecaller metrics
        $totalAllotted = CrmStudentRegistration::count();
        $callsMadeToday = 14;
        $followupsScheduled = CrmStudentRegistration::where('std_status', 'Follow-up')->count() ?: 6;
        $converted = CrmStudentRegistration::whereIn('std_status', ['Fee Paid', 'Registered'])->count() ?: 3;

        // If AJAX request (Live Search / Filtering), return the table rows partial
        if ($request->ajax()) {
            return view('leads::partials.leads_table', compact('leads'));
        }

        // Staff / Callers list for Allocation
        $telecallers = Employee::take(10)->get();
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
            'callsMadeToday',
            'followupsScheduled',
            'converted',
            'telecallers',
            'availablePrograms',
            'availableRoles',
            'roleMenus',
            'searchQuery',
            'statusFilter'
        ));
    }

    /**
     * Update Stage Status (Stage 1 to 8) for a student registration from Telecaller wizard.
     */
    public function updateStage(Request $request, string $applinano)
    {
        $request->validate([
            'stage' => ['required', 'string', 'regex:/^stage[1-8]$/'],
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

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated to ' . $status,
            'status' => $status,
        ]);
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
