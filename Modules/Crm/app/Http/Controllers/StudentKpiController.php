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

        // 2. Conversion funnel metrics across the 5 student onboarding stages:
        // Stage 1 (Personal Profile) -> Stage 2 (Academic Info) -> Stage 3 (Document Upload) -> Stage 4 (Fee Payment) -> Stage 5 (Exam Slot Booking)
        $totalApplicants = CrmStudentRegistration::count();
        $kpiTotal = max(1, $totalApplicants);

        // Stage 1: Personal Profile
        $kpiStage1Profile = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage1))'), ['completed', 'approved', 'done'])
              ->orWhereHas('personalInfo');
        })->count();

        // Stage 2: Academic Info
        $kpiStage2Academic = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage2))'), ['completed', 'approved', 'done'])
              ->orWhereHas('academicDetails');
        })->count();

        // Stage 3: Document Uploads
        $kpiStage3Docs = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage3))'), ['completed', 'approved', 'done'])
              ->orWhereHas('uploads');
        })->count();

        // Stage 4: Fee Payment
        $kpiStage4Fee = CrmStudentRegistration::where(function ($q) use ($verifiedPaidAppNos) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage4))'), ['paid', 'completed'])
              ->orWhere('stage1', 'paid')
              ->orWhereIn('applinano', $verifiedPaidAppNos);
        })->count();

        // Stage 5: Exam Slot Booking
        $kpiStage5Slot = CrmStudentRegistration::where(function ($q) {
            $q->whereIn(DB::raw('LOWER(TRIM(stage5))'), ['completed', 'approved', 'done'])
              ->orWhereHas('examSchedule');
        })->count();

        // Compatibility aliases for views & tests
        $kpiStage1Done = $kpiStage1Profile;
        $kpiStage2Done = $kpiStage2Academic;
        $kpiStage3Done = $kpiStage3Docs;
        $kpiStage4Done = $kpiStage4Fee;
        $kpiStage4Paid = $kpiStage4Fee;
        $kpiStage1Paid = $kpiStage4Fee;
        $kpiStage5Done = $kpiStage5Slot;

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
                'stage1' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage1))'), ['completed', 'approved', 'done'])->orWhereHas('personalInfo');
                }),
                'stage2' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage2))'), ['completed', 'approved', 'done'])->orWhereHas('academicDetails');
                }),
                'stage3' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage3))'), ['completed', 'approved', 'done'])->orWhereHas('uploads');
                }),
                'stage4' => $studentsQuery->where(function ($q) use ($verifiedPaidAppNos) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage4))'), ['paid', 'completed'])
                      ->orWhere('stage1', 'paid')
                      ->orWhereIn('applinano', $verifiedPaidAppNos);
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
            'kpiStage1Profile',
            'kpiStage2Academic',
            'kpiStage3Docs',
            'kpiStage4Fee',
            'kpiStage5Slot',
            'kpiStage1Done',
            'kpiStage2Done',
            'kpiStage3Done',
            'kpiStage4Done',
            'kpiStage4Paid',
            'kpiStage1Paid',
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

    /**
     * Export student records matching current filters as an Excel-compatible CSV.
     */
    public function export(Request $request)
    {
        $user = auth()->user();
        $roleId = $user ? (int)($user->role_id ?? 0) : 0;

        if ($roleId && RoleService::isTelecaller($roleId)) {
            return redirect()->route('kpi.telecallerkpi')->with('error', 'Telecallers only have access to Telecaller KPI.');
        }

        $pdPaid = PayDetail::where(function ($q) {
            $q->where('txnstatus', 'success')
              ->orWhere('paidamount', '>', 0);
        })->pluck('applino')->map(fn($v) => (string)$v);

        $rdPaid = ReceiptDetail::pluck('applino')->map(fn($v) => (string)$v);
        $verifiedPaidAppNos = $pdPaid->merge($rdPaid)->unique()->filter()->values();

        $search = trim($request->input('q', ''));
        $filterStage = $request->input('stage');

        $studentsQuery = CrmStudentRegistration::with([
            'personalInfo',
            'academicDetails',
            'uploads',
            'payDetail',
            'receipt',
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

        if ($filterStage) {
            match ($filterStage) {
                'stage1' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage1))'), ['completed', 'approved', 'done'])->orWhereHas('personalInfo');
                }),
                'stage2' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage2))'), ['completed', 'approved', 'done'])->orWhereHas('academicDetails');
                }),
                'stage3' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage3))'), ['completed', 'approved', 'done'])->orWhereHas('uploads');
                }),
                'stage4' => $studentsQuery->where(function ($q) use ($verifiedPaidAppNos) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage4))'), ['paid', 'completed'])
                      ->orWhere('stage1', 'paid')
                      ->orWhereIn('applinano', $verifiedPaidAppNos);
                }),
                'stage5' => $studentsQuery->where(function ($q) {
                    $q->whereIn(DB::raw('LOWER(TRIM(stage5))'), ['completed', 'approved', 'done'])->orWhereHas('examSchedule');
                }),
                default => null,
            };
        }

        $students = $studentsQuery->orderBy('applinano', 'asc')->get();

        $filename = 'Student_KPI_Report_' . date('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($students, $verifiedPaidAppNos) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM so Microsoft Excel automatically recognises UTF-8 encoding
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Write CSV Headers: Application Number, Candidate Name, Mobile Number, Current Stage
            fputcsv($handle, ['Application Number', 'Candidate Name', 'Mobile Number', 'Current Stage']);

            foreach ($students as $st) {
                $appNo = (string)$st->applinano;
                $isStage1 = in_array(strtolower(trim((string)$st->stage1)), ['completed', 'approved', 'done']) || $st->personalInfo;
                $isStage2 = in_array(strtolower(trim((string)$st->stage2)), ['completed', 'approved', 'done']) || $st->academicDetails;
                $isStage3 = in_array(strtolower(trim((string)$st->stage3)), ['completed', 'approved', 'done']) || $st->uploads;
                $isStage4 = in_array(strtolower(trim((string)$st->stage4)), ['paid', 'completed']) || ($st->stage1 === 'paid') || ($st->payDetail && ($st->payDetail->txnstatus === 'success' || $st->payDetail->paidamount > 0)) || $st->receipt || $verifiedPaidAppNos->contains($appNo);
                $isStage5 = in_array(strtolower(trim((string)$st->stage5)), ['completed', 'approved', 'done']) || $st->examSchedule;

                if ($isStage5) {
                    $currentStage = 'Stage 5: Exam Slot Booked';
                } elseif ($isStage4) {
                    $currentStage = 'Stage 5: Exam Slot Booking';
                } elseif ($isStage3) {
                    $currentStage = 'Stage 4: Fee Payment';
                } elseif ($isStage2) {
                    $currentStage = 'Stage 3: Document Upload';
                } elseif ($isStage1) {
                    $currentStage = 'Stage 2: Academic Info';
                } else {
                    $currentStage = 'Stage 1: Personal Profile';
                }

                fputcsv($handle, [
                    $st->applinano,
                    $st->name ?: '',
                    $st->phoneno ?: '',
                    $currentStage,
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ]);
    }
}
