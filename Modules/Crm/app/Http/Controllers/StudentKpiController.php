<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Crm\Models\CrmStudentRegistration;
use Modules\Crm\Models\Branch;
use Modules\Crm\Models\Campus;
use Modules\Crm\Models\Program;
use Modules\Finance\Models\PayDetail;
use Modules\Finance\Models\ReceiptDetail;
use Modules\Auth\Services\RoleService;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\MenuAccessRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StudentKpiController extends Controller
{
    /**
     * Display the Student KPI dashboard & Conversion Funnel Analytics.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : RoleService::defaultStaffRoleId();

        // Telecaller is restricted to Telecaller KPI only
        if (RoleService::isTelecaller($roleId)) {
            return redirect()->route('kpi.telecallerkpi')->with('error', 'Telecallers only have access to Telecaller KPI.');
        }

        $roleName = $user ? $user->role_label : 'CRM Official';
        $activeTab = 'studentkpi';

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

        // 1. Calculate verified fee payments from finance.paydetails and finance.receiptdetails
        $pdPaid = PayDetail::where(function ($q) {
            $q->where('txnstatus', 'success')
              ->orWhere('paidamount', '>', 0);
        })->pluck('applino')->map(fn($v) => (string)$v);

        $rdPaid = ReceiptDetail::pluck('applino')->map(fn($v) => (string)$v);
        $verifiedPaidAppNos = $pdPaid->merge($rdPaid)->unique()->filter()->values();

        // 2. Conversion funnel metrics across all 5 stages
        $totalApplicants = CrmStudentRegistration::count();
        $kpiTotal = max(1, $totalApplicants);

        // Stage 1: Fee Paid
        $kpiStage1Paid = CrmStudentRegistration::where(function ($q) use ($verifiedPaidAppNos) {
            $q->where('stage1', 'paid')
              ->orWhereIn('applinano', $verifiedPaidAppNos);
        })->count();

        // Stage 2: Personal Profile / Info
        $kpiStage2Done = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage2))'), ['completed', 'approved', 'done'])
              ->orWhereHas('personalInfo');
        })->count();

        // Stage 3: Academic Details
        $kpiStage3Done = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage3))'), ['completed', 'approved', 'done'])
              ->orWhereHas('academicDetails');
        })->count();

        // Stage 4: Document Uploads
        $kpiStage4Done = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage4))'), ['completed', 'approved', 'done'])
              ->orWhereHas('uploads');
        })->count();

        // Stage 5: Examination / Qualified
        $kpiStage5Done = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage5))'), ['completed', 'approved', 'done'])
              ->orWhereHas('examSchedule');
        })->count();

        // 3. Top branch distribution using Eloquent model with master.branches join
        $branchDistribution = CrmStudentRegistration::query()
            ->leftJoin('master.branches as b', 'crm.student_registration.branchid', '=', 'b.branch_id')
            ->select(
                DB::raw("COALESCE(b.branch_long_name, b.branch_short_name, 'General Engineering') as branch_name"),
                DB::raw('count(*) as count')
            )
            ->groupBy(DB::raw("COALESCE(b.branch_long_name, b.branch_short_name, 'General Engineering')"))
            ->orderByDesc('count')
            ->limit(8)
            ->pluck('count', 'branch_name');

        // 4. Detailed student registrations with eager-loaded relationships
        $search = trim($request->input('q', ''));
        $filterBranch = $request->input('branch_id');
        $filterStage = $request->input('stage');

        $studentsQuery = CrmStudentRegistration::with([
            'personalInfo',
            'academicDetails',
            'program',
            'branch',
            'payDetail',
            'receipt',
            'uploads',
            'examSchedule',
        ]);

        if ($search !== '') {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('applinano', 'like', "%{$search}%")
                  ->orWhere('phoneno', 'like', "%{$search}%")
                  ->orWhere('emailid', 'like', "%{$search}%");
            });
        }

        if ($filterBranch) {
            $studentsQuery->where('branchid', $filterBranch);
        }

        if ($filterStage) {
            match ($filterStage) {
                'stage1' => $studentsQuery->where(function ($q) use ($verifiedPaidAppNos) {
                    $q->where('stage1', 'paid')->orWhereIn('applinano', $verifiedPaidAppNos);
                }),
                'stage2' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage2))'), ['completed', 'approved', 'done'])->orWhereHas('personalInfo');
                }),
                'stage3' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage3))'), ['completed', 'approved', 'done'])->orWhereHas('academicDetails');
                }),
                'stage4' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage4))'), ['completed', 'approved', 'done'])->orWhereHas('uploads');
                }),
                'stage5' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage5))'), ['completed', 'approved', 'done'])->orWhereHas('examSchedule');
                }),
                default => null,
            };
        }

        $students = $studentsQuery->latest('log_timestamp')->paginate(20)->withQueryString();

        // Additional counts for dashboard header
        $totalPrograms = Program::count();
        $totalBranches = Branch::count();
        $totalCampuses = Campus::count();
        $allBranches = Branch::orderBy('branch_short_name')->get();

        return view('crm::kpi.studentkpi', compact(
            'user',
            'roleId',
            'roleName',
            'activeTab',
            'roleMenus',
            'availableRoles',
            'totalApplicants',
            'kpiTotal',
            'kpiStage1Paid',
            'kpiStage2Done',
            'kpiStage3Done',
            'kpiStage4Done',
            'kpiStage5Done',
            'branchDistribution',
            'verifiedPaidAppNos',
            'students',
            'totalPrograms',
            'totalBranches',
            'totalCampuses',
            'allBranches',
            'search',
            'filterBranch',
            'filterStage'
        ));
    }
}
