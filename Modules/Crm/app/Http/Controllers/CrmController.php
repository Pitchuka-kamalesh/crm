<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Campus;
use App\Models\CommunicationLog;
use App\Models\CouponInfo;
use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\MenuAccessRole;
use App\Models\PayDetail;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentRegistration;
use App\Models\TelecallerLead;
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
            if ($request->is('*coupoungenration*')) {
                $activeTab = 'couponinfo';
            } else {
                $activeTab = match ($currentRoute) {
                    'crm.stddata', 'crm.enquirylist' => 'stddata',
                    'crm.stdsearch', 'crm.registerlist' => 'stdsearch',
                    'crm.couponinfo', 'crm.coupoungenration' => 'couponinfo',
                    'crm.employeelist' => 'employeelist',
                    'kpi.index', 'kpi.studentkpi' => 'kpi',
                    default => 'overview',
                };
            }
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
        // 1. Fetch from crm.student_registration (Core CRM table)
        try {
            $crmQuery = CrmStudentRegistration::with(['program', 'branch']);
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
            $allStudents = $crmQuery->latest('log_timestamp')->take(100)->get();
        } catch (\Throwable $e) {}

        // 2. If CRM table has no records matching filters, check admissions.student_registrations
        if ($allStudents->isEmpty()) {
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
            } catch (\Throwable $e) {}
        }

        // Distinct available statuses for Enquiry list filter dropdown
        $availableStatuses = collect(['Applied', 'Registered', 'Verified', 'Pending', 'Fee Paid', 'Callback']);
        try {
            $crmDbStatuses = CrmStudentRegistration::whereNotNull('std_status')->distinct()->pluck('std_status');
            $availableStatuses = $availableStatuses->merge($crmDbStatuses);
        } catch (\Throwable $e) {}
        try {
            $dbStatuses = StudentRegistration::whereNotNull('std_status')->distinct()->pluck('std_status');
            $availableStatuses = $availableStatuses->merge($dbStatuses);
        } catch (\Throwable $e) {}
        $availableStatuses = $availableStatuses->unique()->filter()->values();

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

        // Available employees for enquiry allotment & Telecallers with Role 4
        $employees = collect();
        $telecallers = collect();
        try {
            $telecallers = \App\Services\TelecallerService::getTelecallers();
            $employees = Employee::where('is_active', 1)->orderBy('full_name')->get();
            if ($employees->isEmpty()) {
                $employees = $telecallers->isNotEmpty() ? $telecallers : Employee::all();
            }
        } catch (\Throwable $e) {}

        // Database-backed lead assignments from crm.telecaller_leads via Eloquent
        $dbAllocations = collect();
        try {
            $dbAllocations = TelecallerLead::with('employee')
                ->get()
                ->map(function ($l) {
                    $l->emp_name = $l->employee?->full_name ?? $l->empcode;
                    return $l;
                })
                ->keyBy('applino');
        } catch (\Throwable $e) {}

        $unallocatedLeadsCount = max(0, $totalApplications - $dbAllocations->count());

        // Session-backed lead assignments fallback
        $leadAssignments = session()->get('lead_assignments', []);
        foreach ($dbAllocations as $appli => $alloc) {
            $leadAssignments[$appli] = [
                'employee' => $alloc->emp_name ?? $alloc->empcode,
                'empcode' => $alloc->empcode,
                'leadcount' => $alloc->leadcount ?? 1,
                'mode' => 'Assigned',
                'allotted_at' => $alloc->assigndatetime ? date('d M Y, h:i A', strtotime($alloc->assigndatetime)) : date('d M Y, h:i A'),
            ];
        }

        // Fetch coupon batches, personal coupons, and pay details via Eloquent
        $couponBatches = collect();
        $personalCoupons = collect();
        $payDetails = collect();
        try {
            $couponBatches = CouponInfo::where('coupontype', 'DEFAULT')->orderBy('id', 'asc')->get();
            $personalCoupons = CouponInfo::where('coupontype', 'PERSONAL')->get()->keyBy('applino');
            $payDetails = PayDetail::all()->keyBy('applino');
        } catch (\Throwable $e) {}

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
            'telecallers',
            'dbAllocations',
            'unallocatedLeadsCount',
            'leadAssignments',
            'couponBatches',
            'personalCoupons',
            'payDetails'
        ));
    }

    /**
     * Generate personal coupon for a student based on application number.
     * Extracts last 8 digits (e.g. 61106333 from 20261106333) and registers the discount.
     */
    public function generatePersonalCoupon(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'applino' => 'required|string',
            'discount_amount' => 'required|numeric|min:1|max:1200',
        ]);

        $applino = trim($request->input('applino'));
        $discount = (float) $request->input('discount_amount', 500.00);
        $user = Auth::user();
        $usercode = $user?->usercode ?? '03141';

        $res = \App\Services\CouponService::generatePersonalCoupon($applino, $discount, $usercode, $request->ip() ?? '127.0.0.1');

        return redirect()->route('crm.couponinfo', ['tab' => 'couponinfo'])
            ->with('status', $res['message']);
    }

    /**
     * Allot admission enquiries to a telecaller employee.
     * Persists into crm.telecaller_leads.
     */
    public function allotLeads(Request $request)
    {
        $employeeCode = trim($request->input('employee_code', ''));
        $employeeName = trim($request->input('employee_name', ''));
        $allotMode = trim($request->input('allot_mode', 'single'));
        $appliNo = trim($request->input('application_no', ''));
        $phoneNo = trim($request->input('phone_no', ''));
        $date = trim($request->input('allot_date', ''));
        $count = (int)$request->input('allot_count', 0);
        $stageName = trim($request->input('stage_name', 'Stage 1: Calling & Verification'));
        $stageRemark = trim($request->input('stage_remark', 'Lead assigned to telecaller by CRM Incharge'));

        // If employee_code not directly passed, lookup by name
        if (empty($employeeCode) && !empty($employeeName)) {
            $matchedEmp = Employee::where('full_name', $employeeName)->first();
            if ($matchedEmp) {
                $employeeCode = $matchedEmp->emp_code;
            } else {
                // Check if employeeName has code in parentheses e.g. "Ganesh kumar (03434)"
                if (preg_match('/\((\w+)\)/', $employeeName, $m)) {
                    $employeeCode = $m[1];
                }
            }
        }

        if (empty($employeeCode)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Please select a valid telecaller employee'], 422);
            }
            return redirect()->back()->with('error', 'Please select a valid telecaller employee');
        }

        $user = Auth::user();
        $crmId = $user ? $user->usercode : '02598';

        // Call TelecallerService
        $allocResult = \App\Services\TelecallerService::allocateLeads([
            'empcode' => $employeeCode,
            'crmid' => $crmId,
            'allot_mode' => $allotMode,
            'applino' => $appliNo,
            'phoneno' => $phoneNo,
            'allot_date' => $date,
            'allot_count' => $count,
            'stage_name' => $stageName,
            'stage_remark' => $stageRemark,
            'log_userid' => $crmId,
            'log_ipaddress' => $request->ip() ?: '127.0.0.1',
        ]);

        if (!$allocResult['success']) {
            if ($request->wantsJson()) {
                return response()->json($allocResult, 422);
            }
            return redirect()->back()->with('error', $allocResult['message']);
        }

        // Keep session updated for immediate reactive UI compatibility
        $assignments = session()->get('lead_assignments', []);
        $allottedAt = now()->format('d M Y, h:i A');
        $resolvedEmpName = $allocResult['employee_name'] ?? $employeeName;

        if (!empty($allocResult['applications'])) {
            foreach ($allocResult['applications'] as $app) {
                $assignments[$app] = [
                    'employee' => $resolvedEmpName,
                    'empcode' => $employeeCode,
                    'mode' => ucfirst($allotMode),
                    'allotted_at' => $allottedAt,
                ];
            }
        }

        session()->put('lead_assignments', $assignments);

        if ($request->wantsJson()) {
            return response()->json(array_merge($allocResult, [
                'employee' => $resolvedEmpName,
                'empcode' => $employeeCode,
                'mode' => $allotMode,
                'allotted_at' => $allottedAt,
                'assignments' => $assignments,
            ]));
        }

        return redirect()->back()->with('status', $allocResult['message']);
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
