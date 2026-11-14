<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Campus;
use App\Models\CommunicationLog;
use App\Models\CouponInfo;
use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\MenuAccessRole;
use App\Models\PayDetail;
use App\Models\ReceiptDetail;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentRegistration;
use App\Models\TelecallerLead;
use App\Models\TelecallerLeadTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            if ($request->is('*coupoungenration*') || $request->is('*percoupongen*')) {
                $activeTab = 'couponinfo';
            } elseif ($request->is('*crmdatatracking*') || $request->is('*datatracking*')) {
                $activeTab = 'stdsearch';
            } elseif ($request->is('*telecallerkpi*')) {
                $activeTab = 'telecallerkpi';
            } elseif ($request->is('*leadskpi*')) {
                $activeTab = 'leadskpi';
            } elseif ($request->is('*studentkpi*')) {
                $activeTab = 'studentkpi';
            } else {
                $activeTab = match ($currentRoute) {
                    'crm.stddata', 'crm.enquirylist' => 'stddata',
                    'crm.stdsearch', 'crm.registerlist', 'crm.crmdatatracking' => 'stdsearch',
                    'crm.couponinfo', 'crm.coupoungenration', 'crm.percoupongen' => 'couponinfo',
                    'crm.employeelist' => 'employeelist',
                    'kpi.telecallerkpi' => 'telecallerkpi',
                    'kpi.leadskpi' => 'leadskpi',
                    'kpi.index', 'kpi.studentkpi' => 'studentkpi',
                    default => 'stddata', // Default to Student Data (Enquiries) instead of overview dashboard
                };
            }
        }
        if ($activeTab === 'kpi') {
            $activeTab = 'studentkpi';
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

        // Ensure student CRM IDs are synchronized with branch-wise unique numbers
        try {
            \App\Services\CrmIdService::syncAllStudentCrmIds();
        } catch (\Throwable $e) {}

        $allStudents = collect();
        // 1. Fetch from crm.student_registration (Core CRM table)
        try {
            $crmQuery = CrmStudentRegistration::with([
                'program', 'branch', 'campus', 'state', 'country',
                'personalInfo', 'academicDetails', 'payDetail'
            ]);
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

        // Available roles for UI role switcher (strictly user's assigned roles from DB)
        $availableRoles = collect();
        try {
            $userAssigned = \App\Services\RoleService::getAssignedRoles($user->usercode);
            $availableRoles = !empty($userAssigned)
                ? collect($userAssigned)->map(fn($r) => (object)['role_id' => (int)$r['role_id'], 'role_name' => $r['role_name']])
                : Role::where('role_id', $roleId)->get();
        } catch (\Throwable $e) {}

        // Role-based assigned menus from master.menu_access_roles (with crm fallback)
        $roleMenus = collect();
        try {
            $roleMenus = MenuAccessRole::forRole($roleId);
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
        $paidAppNos = collect();
        try {
            $couponBatches = CouponInfo::where('coupontype', 'DEFAULT')->orderBy('id', 'asc')->get();
            $personalCoupons = CouponInfo::where('coupontype', 'PERSONAL')->get()->keyBy('applino');
            $payDetails = PayDetail::all()->keyBy('applino');

            // Genuine verified payment records from paydetails (status success or paidamount > 0) and receiptdetails
            $pdPaid = PayDetail::where(function($q) {
                $q->where('txnstatus', 'success')
                  ->orWhere('paidamount', '>', 0);
            })->pluck('applino')->map(fn($v) => (string)$v);

            $rdPaid = ReceiptDetail::pluck('applino')->map(fn($v) => (string)$v);
            $paidAppNos = $pdPaid->merge($rdPaid)->unique()->filter()->values();
        } catch (\Throwable $e) {}

        // Genuine Student KPI & Funnel Metrics for 'kpi' tab
        $kpiTotal = max(1, $allStudents->count() ?: $totalApplications);
        $kpiStage1Paid = $allStudents->filter(function($s) use ($paidAppNos) {
            $st1 = strtolower(trim((string)($s->stage1 ?? '')));
            $appNo = (string)($s->applinano ?? $s->application_no ?? '');
            // Strictly genuine: status 'paid' or verified payment record
            return $st1 === 'paid' || ($appNo !== '' && $paidAppNos->contains($appNo));
        })->count();

        $kpiStage2Done = $allStudents->filter(function($s) {
            $st2 = strtolower(trim((string)($s->stage2 ?? '')));
            return in_array($st2, ['completed', 'approved', 'done'], true);
        })->count();

        $kpiStage3Done = $allStudents->filter(function($s) {
            $st3 = strtolower(trim((string)($s->stage3 ?? '')));
            return in_array($st3, ['completed', 'approved', 'done'], true);
        })->count();

        $kpiStage4Done = $allStudents->filter(function($s) {
            $st4 = strtolower(trim((string)($s->stage4 ?? '')));
            return in_array($st4, ['completed', 'approved', 'done'], true);
        })->count();

        $kpiStage5Done = $allStudents->filter(function($s) {
            $st5 = strtolower(trim((string)($s->stage5 ?? '')));
            return in_array($st5, ['completed', 'approved', 'done'], true);
        })->count();

        $kpiBranchDistribution = $allStudents->groupBy(function($s) {
            return $s->branch?->branch_long_name ?? $s->branch?->branch_short_name ?? 'General Engineering';
        })->map(fn($group) => $group->count())->sortDesc()->take(6);

        // Genuine Telecaller KPI Metrics
        $selectedTelecaller = (string)$request->query('telecaller', 'all');
        $totalTelecallerLeads = 0;
        $teleGaneshCount = 0;
        $teleUnassignedCount = 0;
        $teleTasksCount = 0;
        $teleDistinctContacted = 0;
        $telePendingCount = 0;
        $teleTaskStages = collect();
        try {
            $totalTelecallerLeads = TelecallerLead::count();
            $teleGaneshCount = TelecallerLead::where('empcode', '03434')->count();
            $teleUnassignedCount = TelecallerLead::where('empcode', '0')->count();
            $teleTasksCount = TelecallerLeadTask::count();
            $teleDistinctContacted = TelecallerLeadTask::distinct('phoneno')->count('phoneno');
            $telePendingCount = max(0, $totalTelecallerLeads - $teleDistinctContacted);
            $teleTaskStages = DB::table('crm.telecaller_leads_tasks')
                ->select('stage_name', DB::raw('count(*) as count'))
                ->whereNotNull('stage_name')
                ->groupBy('stage_name')
                ->orderBy('count', 'desc')
                ->get();
        } catch (\Throwable $e) {}

        $telecallerLeadsList = collect();
        try {
            $telecallerLeadsList = DB::table('crm.telecaller_leads as tl')
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
                )
                ->orderBy('tl.leadcount', 'asc')
                ->take(100)
                ->get()
                ->map(function($lead) {
                    $latestTask = DB::table('crm.telecaller_leads_tasks')
                        ->where('phoneno', (string)$lead->phoneno)
                        ->orWhere(function($q) use ($lead) {
                            if ($lead->applino && $lead->applino !== '0') {
                                $q->where('applino', (string)$lead->applino);
                            }
                        })
                        ->orderBy('leadtaskid', 'desc')
                        ->first();
                    $lead->latest_stage = $latestTask ? $latestTask->stage_name : 'Pending Call';
                    $lead->latest_calltime = $latestTask ? $latestTask->stage_calltime : null;
                    $lead->latest_duration = $latestTask ? $latestTask->stage_call_duration : null;
                    $lead->latest_remark = $latestTask ? $latestTask->stage_remark : 'No calls recorded yet';
                    $lead->call_person = $latestTask ? $latestTask->stage_callperson : null;
                    $lead->call_count = DB::table('crm.telecaller_leads_tasks')
                        ->where('phoneno', (string)$lead->phoneno)
                        ->count();
                    return $lead;
                });
        } catch (\Throwable $e) {}

        $telecallerStats = collect();
        $totalTelecallersCount = 0;
        try {
            $telecallerStats = Employee::whereIn('emp_code', function($q) {
                $q->select('emp_id')->from('master.roles_assigned')->where('roles_role_id', 4);
            })->orWhere('role_id', 4)
            ->orWhereIn('emp_code', function($q) {
                $q->select('empcode')->from('crm.telecaller_leads');
            })->orWhereIn('emp_code', function($q) {
                $q->select('empcode')->from('crm.leads')->whereNotNull('empcode')->where('empcode', '!=', '0');
            })->orderBy('full_name', 'asc')->get()->map(function($emp) use ($paidAppNos) {
                $leads = TelecallerLead::where('empcode', $emp->emp_code)->get();
                $leadsCount = $leads->count();
                $paidCount = $leads->filter(fn($l) => $paidAppNos->contains((string)$l->applino))->count();
                $tasksCount = TelecallerLeadTask::where('empcode', $emp->emp_code)->count();
                $contactedCount = TelecallerLeadTask::where('empcode', $emp->emp_code)->distinct('phoneno')->count('phoneno');
                $pendingCount = max(0, $leadsCount - $contactedCount);
                $stages = DB::table('crm.telecaller_leads_tasks')
                    ->where('empcode', $emp->emp_code)
                    ->select('stage_name', DB::raw('count(*) as count'))
                    ->whereNotNull('stage_name')
                    ->groupBy('stage_name')
                    ->orderBy('count', 'desc')
                    ->get();
                $convRate = $leadsCount > 0 ? round(($paidCount / $leadsCount) * 100, 1) : 0;
                return (object)[
                    'emp_code' => $emp->emp_code,
                    'name' => $emp->full_name,
                    'email' => $emp->ofc_mailid ?? $emp->personal_mailid ?? 'N/A',
                    'phone' => $emp->phone_primary ?? 'N/A',
                    'leads_count' => $leadsCount,
                    'tasks_count' => $tasksCount,
                    'contacted_count' => $contactedCount,
                    'pending_count' => $pendingCount,
                    'stages' => $stages,
                    'paid_count' => $paidCount,
                    'conversion_rate' => $convRate,
                    'status' => $leadsCount > 0 ? 'Active' : 'Standby',
                ];
            });
            $totalTelecallersCount = $telecallerStats->count();
        } catch (\Throwable $e) {}

        // Genuine Leads KPI Metrics
        $totalRawLeads = 0;
        $leadsNewCount = 0;
        $leadsRegCount = 0;
        $allocatedLeadsCount = 0;
        $unallocatedPoolCount = 0;
        try {
            $totalRawLeads = Lead::count();
            $leadsNewCount = Lead::where('phnostatus', 'New Lead')->count();
            $leadsRegCount = Lead::where('phnostatus', 'Registered')->count();
            $allocatedLeadsCount = Lead::where('empcode', '!=', '0')->whereNotNull('empcode')->count();
            $unallocatedPoolCount = Lead::where(function($q) {
                $q->where('empcode', '0')->orWhereNull('empcode');
            })->count();
        } catch (\Throwable $e) {}

        $leadsByDate = collect();
        $availableYears = collect();
        $availableMonths = collect();
        try {
            $leadsByDate = DB::table('crm.leads')
                ->select(DB::raw("CAST(enquirydate AS text) as enquirydate"), DB::raw('count(*) as count'))
                ->whereNotNull('enquirydate')
                ->groupBy('enquirydate')
                ->orderBy('enquirydate', 'asc')
                ->get()
                ->map(function($item) {
                    $dt = strtotime($item->enquirydate);
                    $item->year = date('Y', $dt);
                    $item->month = date('m', $dt);
                    $item->month_name = date('M', $dt);
                    $item->formatted_date = date('d M Y', $dt);
                    $item->short_date = date('d M', $dt);
                    return $item;
                });

            $availableYears = $leadsByDate->pluck('year')->unique()->values();
            $availableMonths = $leadsByDate->map(function($d) {
                return (object)[
                    'year' => $d->year,
                    'month' => $d->month,
                    'label' => date('F Y', strtotime($d->enquirydate)),
                    'short' => date('M Y', strtotime($d->enquirydate))
                ];
            })->unique('label')->values();
        } catch (\Throwable $e) {}

        $rawLeadsList = collect();
        try {
            $rawLeadsList = DB::table('crm.leads')
                ->select('phoneno', 'phnostatus', 'enquirydate', 'empcode', 'applino')
                ->orderBy('enquirydate', 'desc')
                ->orderByRaw('log_timestamp DESC NULLS LAST')
                ->take(100)
                ->get();
        } catch (\Throwable $e) {}

        $leadsBySource = collect();
        try {
            $leadsBySource = DB::table('crm.leads')
                ->select(DB::raw("CASE WHEN log_userid = '02598' THEN 'Bulk Upload' ELSE 'Portal / Direct' END as source_name"), DB::raw('count(*) as count'))
                ->groupBy(DB::raw("CASE WHEN log_userid = '02598' THEN 'Bulk Upload' ELSE 'Portal / Direct' END"))
                ->orderBy('count', 'desc')
                ->get();
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
            'payDetails',
            'paidAppNos',
            'kpiTotal',
            'kpiStage1Paid',
            'kpiStage2Done',
            'kpiStage3Done',
            'kpiStage4Done',
            'kpiStage5Done',
            'kpiBranchDistribution',
            'selectedTelecaller',
            'totalTelecallerLeads',
            'teleGaneshCount',
            'teleUnassignedCount',
            'teleTasksCount',
            'teleDistinctContacted',
            'telePendingCount',
            'teleTaskStages',
            'telecallerLeadsList',
            'telecallerStats',
            'totalTelecallersCount',
            'totalRawLeads',
            'leadsNewCount',
            'leadsRegCount',
            'allocatedLeadsCount',
            'unallocatedPoolCount',
            'leadsByDate',
            'availableYears',
            'availableMonths',
            'leadsBySource',
            'rawLeadsList'
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

        if ($request->is('*percoupongen*') || $request->input('redirect_to') === 'percoupongen') {
            return redirect()->route('crm.percoupongen')
                ->with('status', $res['message']);
        }

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
        $crmId = $user ? $user->usercode : 'SYSTEM';

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

    // Resource API stubs
    public function create() { return view('crm::create'); }
    public function store(Request $request) { return response()->json(['success' => true]); }
    public function show($id) { return view('crm::show'); }
    public function edit($id) { return view('crm::edit'); }
    public function update(Request $request, $id) { return response()->json(['success' => true]); }
    public function destroy($id) { return response()->json(['success' => true]); }
}
