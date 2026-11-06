<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Campus;
use App\Models\CommunicationLog;
use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\MenuAccessRole;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CrmController extends Controller
{
    /**
     * Display the CRM Landing Dashboard for CRM DEAN, CRM Incharge, and Staff.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : 1;
        $roleName = $user ? $user->role_label : 'CRM Official';

        $currentRoute = $request->route() ? $request->route()->getName() : '';
        $activeTab = $request->query('tab');
        if (!$activeTab) {
            $activeTab = match ($currentRoute) {
                'crm.stddata', 'crm.enquirylist' => 'stddata',
                'crm.stdsearch', 'crm.registerlist' => 'stdsearch',
                'crm.couponinfo' => 'couponinfo',
                'crm.employeelist' => 'employeelist',
                'kpi.index', 'kpi.studentkpi' => 'kpi',
                default => 'overview',
            };
        }

        // Summary counts from Eloquent Models (Read-Only)
        $totalCrmRegistrations = 0;
        try {
            $totalCrmRegistrations = CrmStudentRegistration::count();
        } catch (\Throwable $e) {}

        $totalAdmRegistrations = 0;
        try {
            $totalAdmRegistrations = StudentRegistration::count();
        } catch (\Throwable $e) {}

        $totalApplications = max($totalCrmRegistrations, $totalAdmRegistrations);

        $totalCommunications = 0;
        try {
            $totalCommunications = CommunicationLog::count();
        } catch (\Throwable $e) {}

        $totalPrograms = 0;
        try {
            $totalPrograms = Program::count();
        } catch (\Throwable $e) {}

        $totalBranches = 0;
        try {
            $totalBranches = Branch::count();
        } catch (\Throwable $e) {}

        $totalCampuses = 0;
        try {
            $totalCampuses = Campus::count();
        } catch (\Throwable $e) {}

        // Recent student registrations
        $recentStudents = collect();
        try {
            $recentStudents = CrmStudentRegistration::latest('log_timestamp')
                ->take(10)
                ->get();
        } catch (\Throwable $e) {}

        if ($recentStudents->isEmpty()) {
            try {
                $recentStudents = StudentRegistration::latest('log_timestamp')
                    ->take(10)
                    ->get();
            } catch (\Throwable $e) {}
        }

        // Search and full student data for stddata & stdsearch views
        $searchQuery = trim($request->input('q', ''));
        $filterPhone = trim($request->input('phone', ''));
        $filterStatus = trim($request->input('status', ''));
        $filterDate = trim($request->input('date', ''));

        $allStudents = collect();
        try {
            $query = StudentRegistration::query();
            if ($searchQuery) {
                $query->where(function ($q) use ($searchQuery) {
                    $q->where('student_name', 'like', "%{$searchQuery}%")
                        ->orWhere('application_no', 'like', "%{$searchQuery}%")
                        ->orWhere('mobile', 'like', "%{$searchQuery}%")
                        ->orWhere('register_no', 'like', "%{$searchQuery}%");
                });
            }
            if ($filterPhone) {
                $query->where('mobile', 'like', "%{$filterPhone}%");
            }
            if ($filterStatus && strtolower($filterStatus) !== 'all') {
                $query->where('std_status', 'like', "%{$filterStatus}%");
            }
            if ($filterDate) {
                $query->whereDate('log_timestamp', $filterDate);
            }
            $allStudents = $query->latest('log_timestamp')->take(100)->get();

            if ($allStudents->isEmpty() && !$searchQuery && !$filterPhone && !$filterStatus && !$filterDate) {
                $allStudents = CrmStudentRegistration::latest('log_timestamp')->take(100)->get();
            } elseif ($allStudents->isEmpty()) {
                $crmQuery = CrmStudentRegistration::query();
                if ($searchQuery) {
                    $crmQuery->where(function ($q) use ($searchQuery) {
                        $q->where('name', 'like', "%{$searchQuery}%")
                            ->orWhere('applinano', 'like', "%{$searchQuery}%")
                            ->orWhere('phoneno', 'like', "%{$searchQuery}%");
                    });
                }
                if ($filterPhone) {
                    $crmQuery->where('phoneno', 'like', "%{$filterPhone}%");
                }
                if ($filterStatus && strtolower($filterStatus) !== 'all') {
                    $crmQuery->where('std_status', 'like', "%{$filterStatus}%");
                }
                if ($filterDate) {
                    $crmQuery->whereDate('log_timestamp', $filterDate);
                }
                $crmResults = $crmQuery->latest('log_timestamp')->take(100)->get();
                if ($crmResults->isNotEmpty()) {
                    $allStudents = $crmResults;
                }
            }
        } catch (\Throwable $e) {}

        // Distinct available statuses for Enquiry list filter dropdown
        $availableStatuses = collect(['Applied', 'Registered', 'Verified', 'Pending', 'Fee Paid', 'Callback']);
        try {
            $dbStatuses = StudentRegistration::whereNotNull('std_status')->distinct()->pluck('std_status');
            $crmDbStatuses = CrmStudentRegistration::whereNotNull('std_status')->distinct()->pluck('std_status');
            $availableStatuses = $availableStatuses->merge($dbStatuses)->merge($crmDbStatuses)->unique()->filter()->values();
        } catch (\Throwable $e) {}

        // Campuses and Programs for Employee / Dean views
        $campuses = collect();
        try {
            $campuses = Campus::all();
        } catch (\Throwable $e) {}

        $programs = collect();
        try {
            $programs = Program::orderBy('program_level')->get();
        } catch (\Throwable $e) {}

        $branches = collect();
        try {
            $branches = Branch::take(50)->get();
        } catch (\Throwable $e) {}

        // Available roles for UI role switcher
        $availableRoles = collect();
        try {
            $availableRoles = Role::orderBy('role_id')->get();
        } catch (\Throwable $e) {}

        // Role-based assigned menus from master.menu_access_roles
        $roleMenus = collect();
        try {
            $roleMenus = MenuAccessRole::where('role_id', $roleId)->with('menu')->orderBy('morder')->get();
        } catch (\Throwable $e) {}

        // Available employees for enquiry allotment
        $employees = collect();
        try {
            $employees = Employee::where('is_active', 1)->orderBy('full_name')->get();
            if ($employees->isEmpty()) {
                $employees = Employee::all();
            }
        } catch (\Throwable $e) {}

        // Session-backed lead assignments (Read-only DB safe)
        $leadAssignments = session()->get('lead_assignments', []);

        return view('crm::index', compact(
            'user',
            'roleId',
            'roleName',
            'activeTab',
            'totalApplications',
            'totalCommunications',
            'totalPrograms',
            'totalBranches',
            'totalCampuses',
            'recentStudents',
            'allStudents',
            'campuses',
            'programs',
            'branches',
            'availableRoles',
            'roleMenus',
            'searchQuery',
            'filterPhone',
            'filterStatus',
            'filterDate',
            'availableStatuses',
            'employees',
            'leadAssignments'
        ));
    }

    /**
     * Allot admission enquiries to an employee (Saved in session, read-only DB safe).
     */
    public function allotLeads(Request $request)
    {
        $employeeName = trim($request->input('employee_name', ''));
        $allotMode = trim($request->input('allot_mode', 'single'));
        $appliNo = trim($request->input('application_no', ''));
        $date = trim($request->input('allot_date', ''));
        $count = (int)$request->input('allot_count', 0);

        if (!$employeeName) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Please select an employee'], 422);
            }
            return redirect()->back()->with('error', 'Please select an employee');
        }

        $assignments = session()->get('lead_assignments', []);
        $allottedAt = now()->format('d M Y, h:i A');

        if ($allotMode === 'single' && $appliNo) {
            $assignments[$appliNo] = [
                'employee' => $employeeName,
                'mode' => 'Single',
                'allotted_at' => $allottedAt,
            ];
        } else {
            // Bulk / mode assignment
            $assignments['_last_bulk'] = [
                'employee' => $employeeName,
                'mode' => ucfirst($allotMode),
                'date' => $date,
                'count' => $count,
                'allotted_at' => $allottedAt,
            ];
            if ($appliNo) {
                $assignments[$appliNo] = [
                    'employee' => $employeeName,
                    'mode' => ucfirst($allotMode),
                    'allotted_at' => $allottedAt,
                ];
            }
        }

        session()->put('lead_assignments', $assignments);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'employee' => $employeeName,
                'mode' => $allotMode,
                'allotted_at' => $allottedAt,
                'assignments' => $assignments,
            ]);
        }

        return redirect()->back()->with('status', "Enquiries successfully allotted to {$employeeName}!");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crm::create');
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
        return view('crm::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('crm::edit');
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
