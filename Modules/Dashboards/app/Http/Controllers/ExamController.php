<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Campus;
use App\Models\CrmStudentRegistration;
use App\Models\ExamSchedule;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExamController extends Controller
{
    /**
     * Display the Exams Slot Data management table (matching master.menus: EXAM00003).
     */
    public function slotData(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? (int) $user->getActiveRoleId() : 2;
        $roleName = $user ? $user->role_label : 'CRM Official';

        // Load all exam slot bookings from finance.exam_schedule with student associations
        $schedules = collect();
        try {
            $schedules = ExamSchedule::with(['student.program', 'student.branch', 'student.academicDetails', 'personalInfo'])
                ->orderBy('logdatetime', 'desc')
                ->get();
        } catch (\Throwable $e) {
            // Fallback via Eloquent Model
            try {
                $schedules = ExamSchedule::orderBy('logdatetime', 'desc')->get();
            } catch (\Throwable $e2) {}
        }


        // Eager-load academicDetails for pu2_group extraction
            $schedules = ExamSchedule::with(['student.program', 'student.branch', 'student.academicDetails', 'personalInfo'])
                ->orderBy('logdatetime', 'desc')
                ->get();

            $distinctGroups = $schedules->map(function ($s) {
                return trim((string)($s->student?->academicDetails?->pu2_group ?? ''));
            })->filter()->unique()->sort()->values();




        // Summary Metric Counters
        $totalBookings = $schedules->count();
        $slot1Count = $schedules->filter(function ($s) {
            $slot = strtoupper((string) $s->exam_slot);
            return str_contains($slot, 'SLOT 1') || str_contains($slot, 'S1') || str_contains($slot, '11');
        })->count();

        $slot2Count = $schedules->filter(function ($s) {
            $slot = strtoupper((string) $s->exam_slot);
            return str_contains($slot, 'SLOT 2') || str_contains($slot, 'S2') || str_contains($slot, '2:00') || str_contains($slot, '2 PM');
        })->count();

        $confirmedCount = $schedules->filter(function ($s) {
            return (int) $s->exam_status === 1 || empty($s->exam_status);
        })->count();

        $todayDate = Carbon::today()->format('Y-m-d');
        $todayBookings = $schedules->filter(function ($s) use ($todayDate) {
            return !empty($s->examdate) && Carbon::parse($s->examdate)->format('Y-m-d') === $todayDate;
        })->count();

        $distinctDates = $schedules->pluck('examdate')->filter()->unique()->values();

        $distinctYears = $schedules->map(function ($s) {
            return !empty($s->examdate) ? Carbon::parse($s->examdate)->format('Y') : null;
        })->filter()->unique()->sort()->values();
        if ($distinctYears->isEmpty()) {
            $distinctYears = collect([Carbon::now()->format('Y')]);
        }

        $monthsList = collect([
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December',
        ]);

        $distinctGroups = $schedules->map(function ($s) {
            return trim((string)($s->student?->academicDetails?->pu2_group ?? ''));
        })->filter()->unique()->sort()->values();

        $viewName = view()->exists('dashboards::exam.examslotdata') ? 'dashboards::exam.examslotdata' : 'exam.examslotdata';

        return view($viewName, compact(
            'user',
            'roleId',
            'roleName',
            'schedules',
            'totalBookings',
            'slot1Count',
            'slot2Count',
            'confirmedCount',
            'todayBookings',
            'distinctDates',
            'distinctYears',
            'monthsList',
            'distinctGroups'
        ));
    }

    /**
     * Display all registered students table with comprehensive details & filters (EXAM00004).
     */
    public function totalDetails(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? (int) $user->getActiveRoleId() : 2;
        $roleName = $user ? $user->role_label : 'CRM Official';

        $query = CrmStudentRegistration::with([
            'personalInfo',
            'academicDetails',
            'payDetail',
            'examSchedule',
            'program',
            'branch',
            'campus',
            'country',
            'state'
        ]);

        // Search Filter (App No, CRM ID, Name, Phone, Email)
        if ($search = trim((string)$request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('applinano', 'ilike', "%{$search}%")
                  ->orWhere('crmid', 'ilike', "%{$search}%")
                  ->orWhere('name', 'ilike', "%{$search}%")
                  ->orWhere('phoneno', 'like', "%{$search}%")
                  ->orWhere('emailid', 'ilike', "%{$search}%");
            });
        }

        // Program Level Filter
        if ($programLevel = trim((string)$request->input('programlevel'))) {
            $query->where('programlevel', 'ilike', $programLevel);
        }

        // Program ID Filter
        if ($programId = $request->input('programid')) {
            $query->where('programid', $programId);
        }

        // Branch ID Filter
        if ($branchId = $request->input('branchid')) {
            $query->where('branchid', $branchId);
        }

        // Fee Status Filter
        if ($feeStatus = $request->input('feestatus')) {
            if ($feeStatus === 'paid') {
                $query->where(function ($q) {
                    $q->where('stage4', 'paid')
                      ->orWhere('stage1', 'paid')
                      ->orWhere('std_status', 'paid')
                      ->orWhereHas('payDetail', function ($pq) {
                          $pq->where('txnstatus', 'success');
                      });
                });
            } elseif ($feeStatus === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('stage4')
                      ->orWhere('stage4', '!=', 'paid');
                })->where(function ($q) {
                    $q->whereNull('stage1')
                      ->orWhere('stage1', '!=', 'paid');
                })->where(function ($q) {
                    $q->whereNull('std_status')
                      ->orWhere('std_status', '!=', 'paid');
                });
            }
        }

        // Exam Slot Filter
        if ($slotFilter = $request->input('slotstatus')) {
            if ($slotFilter === 'booked') {
                $query->whereHas('examSchedule');
            } elseif ($slotFilter === 'not_booked') {
                $query->whereDoesntHave('examSchedule');
            }
        }

        // Inter Group Filter
        if ($interGroup = trim((string)$request->input('intergroup'))) {
            $query->whereHas('academicDetails', function ($aq) use ($interGroup) {
                $aq->where('pu2_group', $interGroup);
            });
        }

        // Sort order
        $sortField = $request->input('sort', 'log_timestamp');
        $sortOrder = $request->input('order', 'desc');
        if (!in_array($sortField, ['applinano', 'crmid', 'name', 'phoneno', 'log_timestamp', 'programlevel'])) {
            $sortField = 'log_timestamp';
        }
        $query->orderBy($sortField, strtolower($sortOrder) === 'asc' ? 'asc' : 'desc');

        // Pagination
        $perPage = (int) $request->input('per_page', 25);
        if ($perPage < 10 || $perPage > 200) {
            $perPage = 25;
        }
        $students = $query->paginate($perPage)->withQueryString();

        // Overall summary statistics
        $totalRegistered = CrmStudentRegistration::count();
        $totalPaid = CrmStudentRegistration::where(function ($q) {
            $q->where('stage4', 'paid')
              ->orWhere('stage1', 'paid')
              ->orWhere('std_status', 'paid');
        })->count();
        $totalExamBooked = ExamSchedule::count();
        $totalPending = max(0, $totalRegistered - $totalPaid);

        // Distinct filter options
        $programs = Program::orderBy('program_short_name')->get();
        $distinctGroups = DB::table('crm.student_academic_details')
            ->whereNotNull('pu2_group')
            ->where('pu2_group', '!=', '')
            ->distinct()
            ->pluck('pu2_group')
            ->sort()
            ->values();

        $viewName = view()->exists('dashboards::exam.totaldetails') ? 'dashboards::exam.totaldetails' : 'exam.totaldetails';

        return view($viewName, compact(
            'user',
            'roleId',
            'roleName',
            'students',
            'totalRegistered',
            'totalPaid',
            'totalExamBooked',
            'totalPending',
            'programs',
            'distinctGroups'
        ));
    }

    /**
     * Export every single detail of all registered students to Excel/CSV format (EXAM00004).
     */
    public function exportTotalDetails(Request $request)
    {
        $query = CrmStudentRegistration::with([
            'personalInfo',
            'academicDetails',
            'payDetail',
            'examSchedule',
            'program',
            'branch',
            'campus',
            'country',
            'state'
        ]);

        // Apply same filters if provided during export
        if ($search = trim((string)$request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('applinano', 'ilike', "%{$search}%")
                  ->orWhere('crmid', 'ilike', "%{$search}%")
                  ->orWhere('name', 'ilike', "%{$search}%")
                  ->orWhere('phoneno', 'like', "%{$search}%")
                  ->orWhere('emailid', 'ilike', "%{$search}%");
            });
        }
        if ($programLevel = trim((string)$request->input('programlevel'))) {
            $query->where('programlevel', 'ilike', $programLevel);
        }
        if ($programId = $request->input('programid')) {
            $query->where('programid', $programId);
        }
        if ($branchId = $request->input('branchid')) {
            $query->where('branchid', $branchId);
        }
        if ($feeStatus = $request->input('feestatus')) {
            if ($feeStatus === 'paid') {
                $query->where(function ($q) {
                    $q->where('stage4', 'paid')->orWhere('stage1', 'paid')->orWhere('std_status', 'paid');
                });
            } elseif ($feeStatus === 'pending') {
                $query->where(function ($q) {
                    $q->whereNull('stage4')->orWhere('stage4', '!=', 'paid');
                })->where(function ($q) {
                    $q->whereNull('stage1')->orWhere('stage1', '!=', 'paid');
                });
            }
        }
        if ($slotFilter = $request->input('slotstatus')) {
            if ($slotFilter === 'booked') {
                $query->whereHas('examSchedule');
            } elseif ($slotFilter === 'not_booked') {
                $query->whereDoesntHave('examSchedule');
            }
        }
        if ($interGroup = trim((string)$request->input('intergroup'))) {
            $query->whereHas('academicDetails', function ($aq) use ($interGroup) {
                $aq->where('pu2_group', $interGroup);
            });
        }

        $query->orderBy('applinano', 'asc');

        $fileName = 'VSAT_TotalStudentDetails_' . date('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM so Excel opens Indian fonts/characters without distortion
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // CSV Columns Header List
            fputcsv($handle, [
                // 1. Core Registration & Identity
                'S.No',
                'Application No',
                'CRM ID',
                'Candidate Name',
                'Mobile Number',
                'Email Address',
                'Campus Code',
                'Campus Name',
                'Program Level',
                'Program Name',
                'Branch Name',
                'Able To',
                'Student Status',
                'Registered Date',
                'Registered Time',
                'Registration IP',

                // 2. Personal & Communication Profile
                'Date of Birth',
                'Gender',
                'Parent / Father Name',
                'Parent Mobile No',
                'Caste Category',
                'Address / Door No',
                'Village',
                'City / Mandal',
                'District',
                'State',
                'Country',
                'Pin Code',
                'Referred By',
                'Referred Emp Code',
                'Terms Accepted',

                // 3. 10th / SSC Academic Details
                '10th School Name',
                '10th Study Board',
                '10th School City',
                '10th School District',
                '10th School State',
                '10th Roll No',
                '10th Passing Year',
                '10th Marks Type',
                '10th Max Marks',
                '10th Marks Earned',
                '10th Percentage / CGPA',
                '10th Result Status',

                // 4. 12th / Inter / Diploma Academic Details
                'Inter / 12th College Name',
                'Inter Campus Location',
                'Inter Study Board',
                'Inter Board of Study',
                'Inter Group (pu2_group)',
                'Inter College City',
                'Inter College District',
                'Inter College State',
                'Inter Roll No',
                'Inter Passing Year',
                'Inter Marks Type',
                'Inter Max Marks',
                'Inter Marks Earned',
                'Inter Percentage',
                'Inter Result Status',
                '60% Undertaking',

                // 5. UG / Degree Details
                'UG College Name',
                'UG University / Board',
                'UG College City',
                'UG Roll No',
                'UG Passing Year',
                'UG Percentage',
                'UG Result Status',

                // 6. PG / Post-Graduate Details
                'PG College Name',
                'PG University / Board',
                'PG Roll No',
                'PG Passing Year',
                'PG Percentage',
                'PG Result Status',

                // 7. Fee Payment Information
                'Fee Status',
                'Fee Amount',
                'Payment Txn ID',
                'Payment Mode',
                'Payment Date',
                'Payment Status',

                // 8. Entrance Exam Slot Schedule
                'Exam Slot Status',
                'Exam Date',
                'Exam Slot Timing',
                'Slot Booked At',
                'Slot Booking IP',

                // 9. Onboarding Journey Stages
                'Stage 1 (Payment)',
                'Stage 2 (Personal Info)',
                'Stage 3 (Academic Info)',
                'Stage 4 (Documents Upload)',
                'Stage 5 (Slot Booking)',
                'Stage 6 (Application PDF)',
                'Stage 7 (Scorecard)',
                'Stage 8',
                'Remarks / Notes'
            ]);

            $sNo = 1;

            $query->chunk(300, function ($records) use ($handle, &$sNo) {
                foreach ($records as $s) {
                    $p = $s->personalInfo;
                    $a = $s->academicDetails;
                    $pay = $s->payDetail;
                    $exam = $s->examSchedule;

                    // Fee status resolution
                    $isPaid = ($s->stage4 === 'paid' || $s->stage1 === 'paid' || $s->std_status === 'paid' || ($pay && $pay->txnstatus === 'success'));
                    $feeStatusText = $isPaid ? 'PAID' : 'PENDING';
                    $feeAmount = $pay?->amount ?? ($isPaid ? '1200' : '0');
                    $txnId = $pay?->txnid ?? $pay?->bank_ref_num ?? '';
                    $payMode = $pay?->mode ?? $pay?->payment_mode ?? '';
                    $payDate = $pay?->txndatetime ? Carbon::parse($pay->txndatetime)->format('d/m/Y h:i A') : '';

                    // Exam schedule resolution
                    $examStatusText = $exam ? 'BOOKED' : (strtoupper((string)$s->programlevel) === 'PHD' ? 'EXEMPTED (PHD)' : 'NOT BOOKED');
                    $examDate = $exam?->examdate ? Carbon::parse($exam->examdate)->format('d/m/Y') : '';
                    $examSlot = $exam?->exam_slot ?? '';
                    $examBookedAt = $exam?->logdatetime ? Carbon::parse($exam->logdatetime)->format('d/m/Y h:i A') : '';
                    $examIp = $exam?->ipaddress ?? '';

                    // Registration Timestamp split
                    $regDate = $s->log_timestamp ? Carbon::parse($s->log_timestamp)->format('d/m/Y') : '';
                    $regTime = $s->log_timestamp ? Carbon::parse($s->log_timestamp)->format('h:i A') : '';

                    fputcsv($handle, [
                        // Core
                        $sNo++,
                        $s->applinano ?? '',
                        $s->crmid ?? '',
                        $s->name ?? '',
                        $s->phoneno ?? '',
                        $s->emailid ?? '',
                        $s->campuscode ?? '',
                        $s->campus?->campus_name ?? '',
                        $s->programlevel ?? '',
                        $s->program?->program_short_name ?? ($s->program?->program_long_name ?? ($s->programlevel ?? '')),
                        $s->branch?->branch_short_name ?? ($s->branch?->branch_long_name ?? ''),
                        $s->ableto ?? '',
                        $s->std_status ?? '',
                        $regDate,
                        $regTime,
                        $s->log_ipaddress ?? '',

                        // Personal
                        $p?->dob ?? '',
                        $p?->gender ?? '',
                        $p?->parent_name ?? '',
                        $p?->parentphono ?? '',
                        $p?->caste_category ?? '',
                        $p?->address ?? '',
                        $p?->village ?? '',
                        $p?->city_mandal ?? '',
                        $p?->district ?? '',
                        $p?->state ?? ($s->state?->state_name ?? ''),
                        $p?->country ?? ($s->country?->country_name ?? ''),
                        $p?->pincode ?? '',
                        $p?->referedby ?? '',
                        $p?->referedempcode ?? '',
                        $p?->terms_accepted ? 'YES' : 'NO',

                        // 10th
                        $a?->tenth_school_name ?? '',
                        $a?->tenth_study_board ?? '',
                        $a?->tenth_school_city ?? '',
                        $a?->tenth_school_district ?? '',
                        $a?->tenth_school_state ?? '',
                        $a?->tenth_roll_no ?? '',
                        $a?->tenth_passed_year ?? '',
                        $a?->tenth_marks_type ?? '',
                        $a?->tenth_marks_conducted ?? '',
                        $a?->tenth_marks_earned ?? '',
                        $a?->tenth_percentage ?? '',
                        $a?->tenth_current_status ?? '',

                        // 12th
                        $a?->pu2_college_name ?? '',
                        $a?->pu2_campus_location ?? '',
                        $a?->pu2_study_board ?? '',
                        $a?->pu2_board_of_study ?? '',
                        $a?->pu2_group ?? '',
                        $a?->pu2_college_city ?? '',
                        $a?->pu2_college_district ?? '',
                        $a?->pu2_college_state ?? '',
                        $a?->pu2_roll_no ?? '',
                        $a?->pu2_passed_year ?? '',
                        $a?->pu2_marks_type ?? '',
                        $a?->pu2_marks_conducted ?? '',
                        $a?->pu2_marks_earned ?? '',
                        $a?->pu2_percentage ?? '',
                        $a?->pu2_current_status ?? '',
                        $a?->undertaking_60_pct ? 'YES' : 'NO',

                        // UG
                        $a?->ug_college_name ?? '',
                        $a?->ug_university_board ?? '',
                        $a?->ug_college_city ?? '',
                        $a?->ug_roll_no ?? '',
                        $a?->ug_passed_year ?? '',
                        $a?->ug_percentage ?? '',
                        $a?->ug_current_status ?? '',

                        // PG
                        $a?->pg_college_name ?? '',
                        $a?->pg_university_board ?? '',
                        $a?->pg_roll_no ?? '',
                        $a?->pg_passed_year ?? '',
                        $a?->pg_percentage ?? '',
                        $a?->pg_current_status ?? '',

                        // Payment
                        $feeStatusText,
                        $feeAmount,
                        $txnId,
                        $payMode,
                        $payDate,
                        $pay?->txnstatus ?? ($isPaid ? 'success' : 'pending'),

                        // Exam Slot
                        $examStatusText,
                        $examDate,
                        $examSlot,
                        $examBookedAt,
                        $examIp,

                        // Stages
                        $s->stage1 ?? '',
                        $s->stage2 ?? '',
                        $s->stage3 ?? '',
                        $s->stage4 ?? '',
                        $s->stage5 ?? '',
                        $s->stage6 ?? '',
                        $s->stage7 ?? '',
                        $s->stage8 ?? '',
                        $s->remarks ?? ''
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}


