<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Campus;
use App\Models\CrmStudentRegistration;
use App\Models\Program;
use App\Models\StudentAcademicDetail;
use App\Models\StudentEducationInfo;
use App\Models\StudentPersonalInfo;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardsController extends Controller
{
    /**
     * Display the 8-Stage Student Admission & Onboarding Landing Page.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        // Active step (1 through 5, default 1)
        $activeStep = (int) $request->query('step', 1);
        if ($activeStep < 1 || $activeStep > 5) {
            $activeStep = 1;
        }

        // Resolve CRM student record from crm.student_registration
        $crmStudent = null;
        try {
            $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
                ->when($cleanPhone, function ($q) use ($cleanPhone) {
                    $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
                })
                ->first();
        } catch (\Throwable $e) {}

        $appNo = $crmStudent?->applinano ?: ($usercode ?: '');
        $regNo = ($crmStudent?->std_status === 'Admitted' || $crmStudent?->stage1 === 'paid') ? ($crmStudent?->applinano ?: '') : '';
        $displayName = trim((string) ($crmStudent?->name ?: ($user?->name ?: 'Applicant')));

        // Always display Candidate / Student role
        $roleName = ($user?->role_id === 5 || $user?->user_type === 1) ? 'Student Candidate' : ($user?->role_label ?: 'Student Candidate');

        // Resolve Program and Branch dynamically from DB (empty if not filled)
        $progId = $crmStudent?->programid;
        $program = $progId ? Program::where('program_id', $progId)->first() : null;
        $programName = $program ? ($program->program_short_name . ' - ' . $program->program_long_name) : ($crmStudent?->programlevel ?: '');

        $branchId = $crmStudent?->branchid;
        $branch = $branchId ? Branch::where('branch_id', $branchId)->first() : null;
        $branchName = $branch ? $branch->branch_long_name : '';

        $campusCode = $crmStudent?->campuscode;
        $campus = $campusCode ? Campus::where('campus_code', $campusCode)->first() : null;
        $campusName = $campus ? $campus->campus_short_name : '';

        $student = $crmStudent;
        $regulation = 'R25';

        // Fetch Personal Info and Academic Details from CRM tables
        $personalInfo = null;
        $academicDetails = null;
        if ($crmStudent) {
            try {
                $personalInfo = StudentPersonalInfo::where('applino', $crmStudent->applinano)
                    ->where('phoneno', $crmStudent->phoneno)
                    ->first();
                $academicDetails = StudentAcademicDetail::where('applino', $crmStudent->applinano)
                    ->where('phoneno', $crmStudent->phoneno)
                    ->first();
            } catch (\Throwable $e) {
                Log::warning('Could not load student personal/academic details: ' . $e->getMessage());
            }
        }

        // Fetch 10th and Inter educational info
        $edu10th = null;
        $eduInter = null;

        // Uploaded KYC documents
        $documents = collect();

        // 5-Stage Metadata definitions (strictly aligned with master.menus)
        $stageMeta = [
            1 => [
                'title' => 'Fee Payment',
                'subtitle' => 'Seat Confirmation & Fee Payment',
                'icon' => '💳',
            ],
            2 => [
                'title' => 'Personal Profile',
                'subtitle' => 'Candidate Bio & Contact Information',
                'icon' => '👤',
            ],
            3 => [
                'title' => 'Academic Info',
                'subtitle' => '10th SSC & Intermediate Details',
                'icon' => '🎓',
            ],
            4 => [
                'title' => 'Application Download',
                'subtitle' => 'Application PDF & Documents Download',
                'icon' => '📄',
            ],
            5 => [
                'title' => 'VSAT Results',
                'subtitle' => 'VSAT Scorecard & Merit Rank Details',
                'icon' => '📊',
            ],
        ];

        // Dynamically populate stage titles from master.menus table so DB updates reflect immediately
        try {
            $dbStageMenus = DB::table('master.menus')
                ->whereIn('menu_id', [109, 110, 111, 112, 113])
                ->orWhere('controller_ref', 'like', 'dashboards?step=%')
                ->get();

            $stepMap = [109 => 1, 110 => 2, 111 => 3, 112 => 4, 113 => 5];
            foreach ($dbStageMenus as $m) {
                $stepNum = null;
                if (!empty($m->controller_ref) && preg_match('/step\D*(\d+)/i', $m->controller_ref, $matches)) {
                    $stepNum = (int) $matches[1];
                } elseif (isset($stepMap[$m->menu_id])) {
                    $stepNum = $stepMap[$m->menu_id];
                }
                if ($stepNum && isset($stageMeta[$stepNum]) && !empty($m->menu_name)) {
                    $stageMeta[$stepNum]['title'] = trim((string) $m->menu_name);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Could not load dynamic stage menus from master.menus: ' . $e->getMessage());
        }

        // Calculate stage statuses strictly from crmStudent DB columns
        $stages = [];
        for ($i = 1; $i <= 5; $i++) {
            $col = 'stage' . $i;
            $val = $crmStudent ? (string)$crmStudent->{$col} : '';
            // For stage 1, 'paid' is completed; for stages 2-5, 'completed' or 'paid' is completed
            $isCompleted = ($i === 1) ? ($val === 'paid') : ($val === 'completed' || $val === 'paid');

            $stages[$i] = [
                'title' => $stageMeta[$i]['title'],
                'subtitle' => $stageMeta[$i]['subtitle'],
                'icon' => $stageMeta[$i]['icon'],
                'status' => $isCompleted ? 'completed' : 'pending',
                'db_field' => $col,
                'db_value' => $val ?: ($i === 1 ? 'not_paid' : 'NA'),
            ];
        }

        // All stages are accessible on click as requested
        $maxAllowedStep = 5;

        // Overall progress percentage
        $completedCount = 0;
        foreach ($stages as $stg) {
            if ($stg['status'] === 'completed') {
                $completedCount++;
            }
        }
        $progressPercent = round(($completedCount / 5) * 100);

        return view('dashboards::index', compact(
            'user',
            'student',
            'crmStudent',
            'personalInfo',
            'academicDetails',
            'edu10th',
            'eduInter',
            'documents',
            'appNo',
            'regNo',
            'displayName',
            'roleName',
            'programName',
            'branchName',
            'campusName',
            'regulation',
            'activeStep',
            'maxAllowedStep',
            'stages',
            'completedCount',
            'progressPercent'
        ));
    }

    /**
     * Unified handler to save candidate details for any of the 8 steps.
     */
    public function saveStep(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $crmStudent = null;
        try {
            $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
                ->when($cleanPhone, function ($q) use ($cleanPhone) {
                    $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
                })
                ->first();
        } catch (\Throwable $e) {}

        $appNo = $crmStudent?->applinano ?? ($usercode ?: ('VU2026-' . ($cleanPhone ? substr($cleanPhone, -6) : 'APP')));
        $regNo = $crmStudent?->applinano ?? ($usercode ?: ('261FA' . ($cleanPhone ? substr($cleanPhone, -5) : '00001')));

        $step = (int) $request->input('step', 1);
        $nextStep = min(5, $step + 1);

        try {
            if ($step === 1) {
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage1' => 'paid',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 2;
                $msg = 'Stage 1: Admission Fee Payment confirmed!';
            } elseif ($step === 2) {
                if ($crmStudent) {
                    $personalCrmId = 'CRMP_' . ($crmStudent->applinano ?: 'APP') . '_' . time();
                    $existingInfo = StudentPersonalInfo::where('applino', $crmStudent->applinano)
                        ->where('phoneno', $crmStudent->phoneno)
                        ->first();

                    $parentName = $request->input('parent_name') ?? $request->input('father_name');
                    $parentPhone = $request->input('parentphono') ?? $request->input('far_ph_ne_no') ?? $request->input('father_phone');
                    $dobVal = $request->input('dob') ?? $request->input('DOB');
                    $refBy = $request->input('referedby') ?? $request->input('referee_by');
                    $refEmp = $request->input('referedempcode') ?? $request->input('referee_empcode');
                    $genderVal = $request->input('gender') ?? $request->input('Gender') ?? 'Male';
                    $catVal = $request->input('caste_category') ?? $request->input('category') ?? 'OC';
                    $countryVal = $request->input('country') ?? $request->input('street_country') ?? 'India';
                    $stateVal = $request->input('state') ?? 'Andhra Pradesh';
                    $districtVal = $request->input('district') ?? $request->input('distric_t') ?? 'Guntur';
                    $cityVal = $request->input('city_mandal') ?? $request->input('city') ?? 'Guntur';
                    $villageVal = $request->input('village') ?? $request->input('address') ?? '';
                    $pincodeVal = $request->input('pincode') ?? $request->input('pin_code') ?? '522002';

                    StudentPersonalInfo::updateOrCreate(
                        [
                            'applino' => $crmStudent->applinano,
                            'phoneno' => $crmStudent->phoneno,
                        ],
                        [
                            'crmid' => $existingInfo?->crmid ?? $personalCrmId,
                            'dob' => $dobVal ? date('Y-m-d', strtotime($dobVal)) : null,
                            'gender' => $genderVal,
                            'parent_name' => $parentName,
                            'parentphono' => $parentPhone,
                            'caste_category' => $catVal,
                            'referedby' => $refBy,
                            'referedempcode' => $refEmp,
                            'country' => $countryVal,
                            'state' => $stateVal,
                            'district' => $districtVal,
                            'city_mandal' => $cityVal,
                            'village' => $villageVal,
                            'pincode' => $pincodeVal,
                            'log_userid' => $user?->usercode ?? $user?->name ?? 'STUDENT',
                            'log_ipaddress' => $request->ip(),
                            'log_timestamp' => now(),
                        ]
                    );

                    $crmUpdates = [
                        'stage2' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ];
                    if ($request->filled('name')) {
                        $crmUpdates['name'] = $request->input('name');
                    }
                    if ($request->filled('email')) {
                        $crmUpdates['emailid'] = $request->input('email');
                    }
                    if ($request->has('hostel_required')) {
                        $crmUpdates['ableto'] = (bool) $request->input('hostel_required');
                    }
                    $crmStudent->update($crmUpdates);
                }
                $nextStep = 3;
                $msg = 'Stage 2: Personal Profile saved successfully!';
            } elseif ($step === 3) {
                if ($crmStudent) {
                    $academicCrmId = 'CRMA_' . ($crmStudent->applinano ?: 'APP') . '_' . time();
                    $existingAcademic = StudentAcademicDetail::where('applino', $crmStudent->applinano)
                        ->where('phoneno', $crmStudent->phoneno)
                        ->first();

                    $tenthSchool = $request->input('tenth_school_name') ?? $request->input('school_co_name') ?? $request->input('ssc_school');
                    $tenthBoard = $request->input('tenth_study_board') ?? $request->input('boad_of_study') ?? $request->input('ssc_board');
                    $tenthRoll = $request->input('tenth_roll_no') ?? $request->input('roll_no') ?? $request->input('ssc_hallticket');
                    $tenthYear = (int) ($request->input('tenth_passed_year') ?? $request->input('passing_year') ?? $request->input('ssc_year') ?? 2024);

                    $evalType = $request->input('eval_type') ?? $request->input('tenth_marks_type') ?? 'marks';
                    $sscMarks = $request->input('tenth_marks') ?? $request->input('marks_value') ?? $request->input('ssc_marks');
                    $tenthMarksNum = null;
                    $tenthCgpaNum = null;

                    if ($evalType === 'CGPA' || $request->filled('cgpa_value')) {
                        $cgpaInput = $request->input('cgpa_value') ?? $sscMarks;
                        if (is_numeric($cgpaInput)) {
                            $tenthCgpaNum = (float) $cgpaInput;
                        } elseif (preg_match('/([\d\.]+)/', (string)$cgpaInput, $m)) {
                            $tenthCgpaNum = (float) $m[1];
                        }
                    } else {
                        if (is_numeric($sscMarks)) {
                            $tenthMarksNum = (float) $sscMarks;
                        } elseif (preg_match('/([\d\.]+)/', (string)$sscMarks, $m)) {
                            $tenthMarksNum = (float) $m[1];
                        }
                    }

                    $courseChoice = $request->input('course_choice') ?? $request->input('pu2_study_board') ?? 'Inter';
                    $subsetChoice = $request->input('subset_choice') ?? $request->input('pu2_current_status') ?? 'First Year';

                    // Handle document upload if present
                    if ($request->hasFile('upload_docs')) {
                        try {
                            $file = $request->file('upload_docs');
                            $fileName = 'DOC_' . ($crmStudent->applinano ?: 'APP') . '_' . time() . '.' . $file->getClientOriginalExtension();
                            $file->move(public_path('uploads/documents'), $fileName);
                        } catch (\Throwable $e) {
                            Log::warning('Document upload note: ' . $e->getMessage());
                        }
                    }

                    StudentAcademicDetail::updateOrCreate(
                        [
                            'applino' => $crmStudent->applinano,
                            'phoneno' => $crmStudent->phoneno,
                        ],
                        [
                            'crmid' => $existingAcademic?->crmid ?? $academicCrmId,
                            'progmid' => $crmStudent->programid,
                            'branchid' => $crmStudent->branchid,
                            'tenth_school_name' => $tenthSchool,
                            'tenth_study_board' => $tenthBoard,
                            'tenth_school_city' => $request->input('tenth_school_city') ?? $request->input('ssc_location') ?? 'Guntur',
                            'tenth_school_district' => $request->input('tenth_school_district') ?? $request->input('ssc_location') ?? 'Guntur',
                            'tenth_roll_no' => $tenthRoll,
                            'tenth_passed_year' => $tenthYear,
                            'tenth_marks_type' => $evalType,
                            'tenth_marks' => $tenthMarksNum,
                            'tenth_cgpa' => $tenthCgpaNum ?: ($evalType === 'CGPA' ? 9.80 : null),
                            'pu2_college_name' => $request->input('inter_college') ?? $request->input('pu2_college_name') ?? 'Narayana Junior College',
                            'pu2_study_board' => $courseChoice,
                            'pu2_college_city' => $request->input('inter_location') ?? $request->input('pu2_college_city'),
                            'pu2_roll_no' => $request->input('inter_hallticket') ?? $request->input('pu2_roll_no'),
                            'pu2_passed_year' => (int) ($request->input('inter_year') ?? $request->input('pu2_passed_year') ?? 2026),
                            'pu2_marks_type' => 'Marks',
                            'pu2_marks' => is_numeric($request->input('inter_marks')) ? (float)$request->input('inter_marks') : 962.00,
                            'pu2_cgpa' => is_numeric($request->input('inter_cgpa')) ? (float)$request->input('inter_cgpa') : null,
                            'pu2_current_status' => $subsetChoice,
                            'log_userid' => $user?->usercode ?? $user?->name ?? 'STUDENT',
                            'log_datetime' => now(),
                            'log_ipaddress' => $request->ip(),
                        ]
                    );

                    $crmStudent->update([
                        'stage3' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 4;
                $msg = 'Stage 3: Academic Records saved successfully!';
            } elseif ($step >= 4 && $step <= 5) {
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage' . $step => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = min(5, $step + 1);
                $msg = 'Stage ' . $step . ' saved successfully!';
            } else {
                $msg = 'Stage updated successfully!';
            }

            return redirect()->route('dashboards.index', ['step' => $nextStep])
                ->with('status', $msg);

        } catch (Exception $e) {
            Log::error('[DashboardsController] saveStep error: ' . $e->getMessage());
            return redirect()->route('dashboards.index', ['step' => $step])
                ->withErrors(['error' => 'Failed to save stage details: ' . $e->getMessage()]);
        }
    }

    /**
     * Backward-compatible alias for existing form submissions.
     */
    public function saveEducation(Request $request): RedirectResponse
    {
        $request->merge(['step' => 6]);
        return $this->saveStep($request);
    }

    /**
     * Silent autosave on blur / change for any student dashboard input.
     */
    public function autosave(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $crmStudent = null;
        try {
            $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
                ->when($cleanPhone, function ($q) use ($cleanPhone) {
                    $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
                })
                ->first();
        } catch (\Throwable $e) {}

        $fieldName = (string)$request->input('field', '');
        $fieldValue = $request->input('value');
        $data = $request->except(['_token', 'field', 'value']);
        if (!empty($fieldName)) {
            $data[$fieldName] = $fieldValue;
        }

        try {
            if ($crmStudent) {
                $crmUpdates = [];
                if (isset($data['student_name']) || isset($data['name'])) {
                    $crmUpdates['name'] = $data['student_name'] ?? $data['name'];
                }
                if (isset($data['personal_email']) || isset($data['email'])) {
                    $crmUpdates['emailid'] = $data['personal_email'] ?? $data['email'];
                }
                if (isset($data['hostel_required'])) {
                    $crmUpdates['ableto'] = (bool)$data['hostel_required'];
                }
                if (!empty($crmUpdates)) {
                    $crmUpdates['log_ipaddress'] = $request->ip();
                    $crmUpdates['log_timestamp'] = now();
                    $crmStudent->update($crmUpdates);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[DashboardsController] autosave error: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
}
