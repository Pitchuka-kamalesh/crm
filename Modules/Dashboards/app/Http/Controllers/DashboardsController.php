<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Campus;
use App\Models\Country;
use App\Models\CrmStudentRegistration;
use App\Models\District;
use App\Models\Menu;
use App\Models\Program;
use App\Models\State;
use App\Models\StudentAcademicDetail;
use App\Models\StudentEducationInfo;
use App\Models\StudentPersonalInfo;
use App\Models\StudentUpload;
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
     * Display the 6-Stage Student Admission & Onboarding Landing Page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        // Active step (1 through 6, default 1)
        $activeStep = (int) $request->query('step', 1);
        if ($activeStep < 1 || $activeStep > 6) {
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

        // Resolve payment status: Check crm.student_registration.stage1, finance.paydetails, and finance.receiptdetails
        $hasPaidRecord = false;
        try {
            if ($appNo) {
                $hasPaidRecord = \App\Models\PayDetail::where('applino', $appNo)->where('txnstatus', 'success')->exists()
                    || \App\Models\ReceiptDetail::where('applino', $appNo)->exists();
            }
        } catch (\Throwable $e) {}

        if ($hasPaidRecord && $crmStudent && $crmStudent->stage1 !== 'paid') {
            try {
                $crmStudent->update(['stage1' => 'paid']);
            } catch (\Throwable $e) {}
        }

        // Validation gate: Enforce sequential step unlocking based on real DB status
        $isStage1Paid = ($crmStudent && $crmStudent->stage1 === 'paid') || $hasPaidRecord;
        $isStage2Done = ($crmStudent && ($crmStudent->stage2 === 'completed' || $crmStudent->stage2 === 'paid'));
        $isStage3Done = ($crmStudent && ($crmStudent->stage3 === 'completed' || $crmStudent->stage3 === 'paid'));

        // If Stage 1 payment is not completed, block access to Steps 2, 3, 4, 5
        if ($activeStep > 1 && !$isStage1Paid) {
            return redirect()->route('dashboards.index', ['step' => 1])
                ->with('warning', 'Please complete the Admission Fee Payment in Step 1 to unlock your Personal Profile and further onboarding steps.');
        }

        // If Stage 2 is not completed, block access to Step 3+
        if ($activeStep > 2 && !$isStage2Done) {
            return redirect()->route('dashboards.index', ['step' => 2])
                ->with('warning', 'Please complete your Personal Profile in Step 2 before proceeding to Academic Info.');
        }

        // If Stage 3 is not completed, block access to Step 4+
        if ($activeStep > 3 && !$isStage3Done) {
            return redirect()->route('dashboards.index', ['step' => 3])
                ->with('warning', 'Please complete your Academic Info in Step 3 before proceeding.');
        }

        // Always display Candidate / Student role
        $roleName = (\App\Services\RoleService::isStudent($user?->role_id) || $user?->user_type === 1) ? 'Student Candidate' : ($user?->role_label ?: 'Student Candidate');

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

        // Determine Degree Level for Step 3 based on registration (UG -> B.Tech, PG -> M.Tech, Doctoral -> PhD)
        $progLevel = strtoupper(trim((string)($crmStudent?->programlevel ?? '')));
        $progShort = strtoupper($program?->program_short_name ?? '');
        $progName = strtoupper($program?->program_name ?? '');

        if (
            $progLevel === 'PG' ||
            str_contains($progShort, 'M.TECH') ||
            str_contains($progShort, 'MTECH') ||
            str_contains($progName, 'MASTER') ||
            in_array($progShort, ['MCA', 'MBA', 'M.PHARM', 'M.SC', 'M.A'])
        ) {
            $degreeLevel = 'mtech';
        } elseif (
            $progLevel === 'PHD' ||
            $progLevel === 'DOCTORAL' ||
            str_contains($progShort, 'PHD') ||
            str_contains($progShort, 'PH.D') ||
            str_contains($progName, 'DOCTOR')
        ) {
            $degreeLevel = 'phd';
        } else {
            $degreeLevel = 'btech';
        }

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

        // Uploaded KYC documents from disk (public/uploads/documents/{appNo})
        $documents = collect();
        $uploadedDocs = self::getUploadedDocuments((string) $appNo);

        // Role-based assigned menus from master.menu_access_roles (with crm fallback)
        $roleId = $user ? (int) $user->getActiveRoleId() : 5;
        $roleMenus = collect();
        try {
            $roleMenus = MenuAccessRole::forRole($roleId);
        } catch (\Throwable $e) {}

        // Default 6-Stage Metadata definitions (aligned with master.menus)
        $stageMeta = [
            1 => [
                'menu_id' => 109,
                'menu_code' => 'FINA0003',
                'title' => 'Fee Payment',
                'subtitle' => 'Seat Confirmation & Fee Payment',
                'icon' => '💳',
            ],
            2 => [
                'menu_id' => 110,
                'menu_code' => 'CRMM0004',
                'title' => 'Personal Profile',
                'subtitle' => 'Candidate Bio & Contact Information',
                'icon' => '👤',
            ],
            3 => [
                'menu_id' => 111,
                'menu_code' => 'CRMM0005',
                'title' => 'Academic Info',
                'subtitle' => '10th SSC & Intermediate Details',
                'icon' => '🎓',
            ],
            4 => [
                'menu_id' => 112,
                'menu_code' => 'CRMM0006',
                'title' => 'Application upload',
                'subtitle' => 'Upload Candidate Documents & Certificates',
                'icon' => '📤',
            ],
            5 => [
                'menu_id' => 122,
                'menu_code' => 'CRMM0010',
                'title' => 'Application Download',
                'subtitle' => 'Official Application PDF & Final Submission',
                'icon' => '📥',
            ],
            6 => [
                'menu_id' => 113,
                'menu_code' => 'CRMM0007',
                'title' => 'VSAT Result',
                'subtitle' => 'VSAT Scorecard & Merit Rank Details',
                'icon' => '📊',
            ],
        ];

        // Dynamically populate stage titles, icons and active status from master.menus table
        try {
            $dbStageMenus = Menu::whereIn('menu_id', [109, 110, 111, 112, 113, 122])
                ->orWhere('controller_ref', 'like', 'step%')
                ->orWhere('controller_ref', 'like', 'dashboards?step=%')
                ->get();

            $stepMap = [109 => 1, 110 => 2, 111 => 3, 112 => 4, 122 => 5, 113 => 6];
            foreach ($dbStageMenus as $m) {
                $stepNum = null;
                if (!empty($m->controller_ref) && preg_match('/step\D*(\d+)/i', $m->controller_ref, $matches)) {
                    $stepNum = (int) $matches[1];
                } elseif (isset($stepMap[$m->menu_id])) {
                    $stepNum = $stepMap[$m->menu_id];
                }
                if ($stepNum && isset($stageMeta[$stepNum])) {
                    if (!empty($m->menu_name)) {
                        $stageMeta[$stepNum]['title'] = trim((string) $m->menu_name);
                    }
                    if (!empty($m->icon_name)) {
                        $stageMeta[$stepNum]['icon_name'] = $m->icon_name;
                    }
                    $stageMeta[$stepNum]['menu_id'] = (int) $m->menu_id;
                    $stageMeta[$stepNum]['menu_code'] = $m->menu_code;
                    $stageMeta[$stepNum]['lock_status'] = (int) $m->lock_status;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Could not load dynamic stage menus from master.menus: ' . $e->getMessage());
        }

        // Determine total active steps (defaults to 6 steps if menu 122 exists)
        $totalSteps = isset($stageMeta[6]) ? 6 : 5;

        // Calculate stage statuses strictly from crmStudent DB columns
        $stages = [];
        for ($i = 1; $i <= $totalSteps; $i++) {
            $col = 'stage' . $i;
            $val = $crmStudent ? (string)$crmStudent->{$col} : '';
            // For stage 1, 'paid' or $isStage1Paid is completed; for stages 2-6, 'completed' or 'paid' is completed
            $isCompleted = ($i === 1) ? ($val === 'paid' || $isStage1Paid) : ($val === 'completed' || $val === 'paid');

            $stages[$i] = [
                'title' => $stageMeta[$i]['title'],
                'subtitle' => $stageMeta[$i]['subtitle'],
                'icon' => $stageMeta[$i]['icon'],
                'status' => $isCompleted ? 'completed' : 'pending',
                'db_field' => $col,
                'db_value' => $val ?: ($i === 1 ? 'not_paid' : 'NA'),
                'menu_id' => $stageMeta[$i]['menu_id'] ?? null,
                'menu_code' => $stageMeta[$i]['menu_code'] ?? null,
                'is_locked_by_menu' => !empty($stageMeta[$i]['lock_status']),
            ];
        }

        // Enforce maximum allowed step based on actual completion
        if (!$isStage1Paid) {
            $maxAllowedStep = 1;
        } elseif (!$isStage2Done) {
            $maxAllowedStep = 2;
        } elseif (!$isStage3Done) {
            $maxAllowedStep = 3;
        } else {
            $maxAllowedStep = $totalSteps;
        }

        // Overall progress percentage
        $completedCount = 0;
        foreach ($stages as $stg) {
            if ($stg['status'] === 'completed') {
                $completedCount++;
            }
        }
        $progressPercent = round(($completedCount / $totalSteps) * 100);

        // Load Country, State, District options from crm tables via Eloquent
        $countries = collect();
        $states = collect();
        $districts = collect();
        try {
            $countries = Country::where('lock_status', 0)->orderBy('country_name')->get();
            $states = State::where('lock_status', 0)->orderBy('state_name')->get();
            $districts = District::where('LOCK_STATUS', 0)->orderBy('DISTRICT_NAME')->get();
        } catch (\Throwable $e) {
            Log::warning('Could not load geo data from crm schema: ' . $e->getMessage());
        }

        $applicationData = null;
        try {
            $applicationData = \App\Http\Controllers\ApplicationController::buildStudentApplicationData((string) $appNo);
        } catch (\Throwable $e) {
            Log::warning('Could not build application data: ' . $e->getMessage());
        }

        return view('dashboards::index', compact(
            'user',
            'student',
            'crmStudent',
            'personalInfo',
            'academicDetails',
            'applicationData',
            'countries',
            'states',
            'districts',
            'edu10th',
            'eduInter',
            'documents',
            'uploadedDocs',
            'appNo',
            'regNo',
            'displayName',
            'roleName',
            'programName',
            'program',
            'degreeLevel',
            'branchName',
            'campusName',
            'regulation',
            'activeStep',
            'maxAllowedStep',
            'stages',
            'totalSteps',
            'roleMenus',
            'isStage1Paid',
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

        // Security validation: Block saving higher steps if previous prerequisite step is not completed
        if ($step > 1 && (!$crmStudent || $crmStudent->stage1 !== 'paid')) {
            return redirect()->route('dashboards.index', ['step' => 1])
                ->withErrors(['error' => 'Admission fee payment in Step 1 must be completed before saving further steps.']);
        }

        if ($step > 2 && (!$crmStudent || ($crmStudent->stage2 !== 'completed' && $crmStudent->stage2 !== 'paid'))) {
            return redirect()->route('dashboards.index', ['step' => 2])
                ->withErrors(['error' => 'Please complete your Personal Profile in Step 2 first.']);
        }

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
                // Normalize phone and pincode before validation
                $rawPhone = (string) ($request->input('phone_no') ?? '');
                $cleanPhoneInput = substr(preg_replace('/\D/', '', $rawPhone), -10);
                if (!$cleanPhoneInput && $crmStudent?->phoneno) {
                    $cleanPhoneInput = substr(preg_replace('/\D/', '', $crmStudent->phoneno), -10);
                }

                $rawPin = (string) ($request->input('pin_code') ?? '');
                $cleanPinInput = preg_replace('/\D/', '', $rawPin);

                if ($cleanPhoneInput) {
                    $request->merge(['phone_no' => $cleanPhoneInput]);
                }
                if ($cleanPinInput) {
                    $request->merge(['pin_code' => $cleanPinInput]);
                }

                // Validation for Step 2
                $request->validate([
                    'father_name' => ['required', 'string', 'max:255'],
                    'phone_no' => ['required', 'regex:/^[0-9]{10}$/'],
                    'dob' => ['required'],
                    'gender' => ['required', 'string'],
                    'category' => ['required', 'string'],
                    'street_country' => ['required'],
                    'state' => ['required'],
                    'distric_t' => ['required'],
                    'city_mandal' => ['required', 'string', 'max:100'],
                    'village' => ['required', 'string', 'max:255'],
                    'pin_code' => ['required', 'regex:/^[0-9]{6}$/'],
                ], [
                    'father_name.required' => 'Father name is required.',
                    'phone_no.required' => 'Phone number is required.',
                    'phone_no.regex' => 'Phone number must be exactly 10 digits.',
                    'dob.required' => 'Date of birth is required.',
                    'pin_code.required' => 'Pin code is required.',
                    'pin_code.regex' => 'Pin code must be exactly 6 digits.',
                    'distric_t.required' => 'Please select a District.',
                    'state.required' => 'Please select a State.',
                    'city_mandal.required' => 'City / Mandal is required.',
                    'village.required' => 'Village / Street address is required.',
                ]);

                if ($crmStudent) {
                    $personalCrmId = $crmStudent->crmid ?? \App\Services\CrmIdService::generateForStudent($crmStudent);
                    $existingInfo = StudentPersonalInfo::where('applino', $crmStudent->applinano)
                        ->where('phoneno', $crmStudent->phoneno)
                        ->first();

                    $parentName = $request->input('parent_name') ?? $request->input('father_name');
                    $rawPhone = $request->input('phone_no') ?? $request->input('far_ph_ne_no') ?? $request->input('phone') ?? $crmStudent->phoneno;
                    $cleanPhoneVal = preg_replace('/\D/', '', (string)$rawPhone);

                    $dobVal = $request->input('dob') ?? $request->input('DOB');

                    // Handle referee_by checkboxes (array or string)
                    $refByInput = $request->input('referee_by');
                    if (is_array($refByInput)) {
                        $refBy = implode(', ', array_filter($refByInput));
                    } else {
                        $refBy = (string)($refByInput ?? $request->input('referedby') ?? 'via advertisement');
                    }
                    $refEmp = $request->input('referedempcode') ?? $request->input('referee_empcode');

                    $genderVal = $request->input('gender') ?? $request->input('Gender') ?? 'Male';
                    $catVal = $request->input('caste_category') ?? $request->input('category') ?? 'OC';

                    // Resolve Country, State, District from DB values
                    $countryInput = $request->input('street_country') ?? $request->input('country') ?? 'India';
                    $stateInput = $request->input('state') ?? 'Andhra Pradesh';
                    $districtInput = $request->input('distric_t') ?? $request->input('district') ?? 'Guntur';

                    if (is_numeric($countryInput)) {
                        $cObj = Country::where('country_id', (int)$countryInput)->first();
                        $countryVal = $cObj?->country_name ?? 'India';
                    } else {
                        $countryVal = (string)$countryInput;
                    }

                    if (is_numeric($stateInput)) {
                        $sObj = State::where('state_id', (int)$stateInput)->first();
                        $stateVal = $sObj?->state_name ?? 'Andhra Pradesh';
                    } else {
                        $stateVal = (string)$stateInput;
                    }

                    if (is_numeric($districtInput)) {
                        $dObj = District::where('DISTRICT_ID', (int)$districtInput)->first();
                        $districtVal = $dObj?->DISTRICT_NAME ?? 'Guntur';
                    } else {
                        $districtVal = (string)$districtInput;
                    }

                    $cityVal = $request->input('city_mandal') ?? $request->input('city') ?? 'Guntur';
                    $villageVal = $request->input('village') ?? $request->input('address') ?? '';
                    $pincodeVal = $request->input('pincode') ?? $request->input('pin_code') ?? '522002';

                    StudentPersonalInfo::updateOrCreate(
                        [
                            'applino' => $crmStudent->applinano,
                            'phoneno' => $crmStudent->phoneno,
                        ],
                        [
                            'crmid' => $personalCrmId,
                            'dob' => $dobVal ? date('Y-m-d', strtotime($dobVal)) : null,
                            'gender' => $genderVal,
                            'parent_name' => $parentName,
                            'parentphono' => $cleanPhoneVal ?: $crmStudent->phoneno,
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
                // Validation for Step 3
                $request->validate([
                    'school_co_name' => ['required', 'string', 'max:255'],
                    'boad_of_study' => ['required', 'string', 'max:255'],
                    'roll_no' => ['required', 'string', 'max:50'],
                    'passing_year' => ['required', 'integer', 'min:2000', 'max:2030'],
                    'eval_type' => ['required', 'string'],
                ], [
                    'school_co_name.required' => 'School / College name is required.',
                    'boad_of_study.required' => 'Board of study is required.',
                    'roll_no.required' => 'Roll / Hall ticket number is required.',
                    'passing_year.required' => 'Passing year is required.',
                ]);

                if ($crmStudent) {
                    $academicCrmId = $crmStudent->crmid ?? \App\Services\CrmIdService::generateForStudent($crmStudent);
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
                        $tenthCgpaNum = is_numeric($cgpaInput) ? (float)$cgpaInput : null;
                        $tenthMarksNum = $tenthCgpaNum ? round($tenthCgpaNum * 9.5, 2) : null;
                        $evalType = 'CGPA';
                    } else {
                        $tenthMarksNum = is_numeric($sscMarks) ? (float)$sscMarks : null;
                        if ($tenthMarksNum !== null) {
                            if ($tenthMarksNum > 100) {
                                $tenthCgpaNum = round(($tenthMarksNum / 600) * 10, 2);
                            } else {
                                $tenthCgpaNum = round($tenthMarksNum / 9.5, 2);
                            }
                        }
                        $evalType = 'marks';
                    }

                    $courseChoice = $request->input('course_choice') ?? $request->input('pu2_study_board') ?? 'Inter';
                    $subsetChoice = $request->input('subset_choice') ?? $request->input('pu2_current_status') ?? 'First Year';

                    $academicPayload = [
                        'crmid' => $academicCrmId,
                        'progmid' => $crmStudent->programid,
                        'branchid' => $crmStudent->branchid,
                        'tenth_school_name' => $tenthSchool,
                        'tenth_study_board' => $tenthBoard,
                        'tenth_school_city' => $request->input('tenth_school_city') ?? $request->input('ssc_location'),
                        'tenth_school_district' => $request->input('tenth_school_district') ?? $request->input('ssc_location'),
                        'tenth_roll_no' => $tenthRoll,
                        'tenth_passed_year' => $tenthYear ?: null,
                        'tenth_marks_type' => $evalType,
                        'tenth_marks' => $tenthMarksNum,
                        'tenth_cgpa' => $tenthCgpaNum,
                        'pu2_college_name' => $request->input('inter_college') ?? $request->input('pu2_college_name'),
                        'pu2_study_board' => $courseChoice,
                        'pu2_college_city' => $request->input('inter_location') ?? $request->input('pu2_college_city'),
                        'pu2_roll_no' => $request->input('inter_hallticket') ?? $request->input('pu2_roll_no'),
                        'pu2_passed_year' => $request->filled('inter_year') ? (int) $request->input('inter_year') : ($request->filled('pu2_passed_year') ? (int) $request->input('pu2_passed_year') : null),
                        'pu2_marks_type' => $request->input('inter_marks_type') ?: 'Marks',
                        'pu2_marks' => is_numeric($request->input('inter_marks')) ? (float)$request->input('inter_marks') : null,
                        'pu2_cgpa' => is_numeric($request->input('inter_cgpa')) ? (float)$request->input('inter_cgpa') : null,
                        'pu2_current_status' => $subsetChoice,
                        'log_userid' => $user?->usercode ?? $user?->name ?? 'STUDENT',
                        'log_datetime' => now(),
                        'log_ipaddress' => $request->ip(),
                    ];

                    // If Degree level is M.Tech or PhD, save UG (B.Tech) details
                    if ($request->filled('ug_college_name') || $request->input('degree_level') === 'mtech' || $request->input('degree_level') === 'phd') {
                        $academicPayload['ug_college_name'] = $request->input('ug_college_name') ?: null;
                        $academicPayload['ug_university_board'] = $request->input('ug_university_board') ?: null;
                        $academicPayload['ug_college_city'] = $request->input('ug_college_city') ?: null;
                        $academicPayload['ug_college_district'] = $request->input('ug_college_district') ?: null;
                        $academicPayload['ug_roll_no'] = $request->input('ug_roll_no') ?: null;
                        $academicPayload['ug_passed_year'] = $request->filled('ug_passed_year') ? (int) $request->input('ug_passed_year') : null;
                        $academicPayload['ug_marks_type'] = $request->input('ug_marks_type') ?: null;
                        $academicPayload['ug_marks'] = is_numeric($request->input('ug_marks')) ? (float) $request->input('ug_marks') : null;
                        $academicPayload['ug_cgpa'] = is_numeric($request->input('ug_cgpa')) ? (float) $request->input('ug_cgpa') : null;
                        $academicPayload['ug_current_status'] = $request->input('ug_current_status') ?: null;
                    }

                    // If Degree level is PhD, save PG (M.Tech) details
                    if ($request->filled('pg_college_name') || $request->input('degree_level') === 'phd') {
                        $academicPayload['pg_college_name'] = $request->input('pg_college_name') ?: null;
                        $academicPayload['pg_university_board'] = $request->input('pg_university_board') ?: null;
                        $academicPayload['pg_college_city'] = $request->input('pg_college_city') ?: null;
                        $academicPayload['pg_college_district'] = $request->input('pg_college_district') ?: null;
                        $academicPayload['pg_roll_no'] = $request->input('pg_roll_no') ?: null;
                        $academicPayload['pg_passed_year'] = $request->filled('pg_passed_year') ? (int) $request->input('pg_passed_year') : null;
                        $academicPayload['pg_marks_type'] = $request->input('pg_marks_type') ?: null;
                        $academicPayload['pg_marks'] = is_numeric($request->input('pg_marks')) ? (float) $request->input('pg_marks') : null;
                        $academicPayload['pg_cgpa'] = is_numeric($request->input('pg_cgpa')) ? (float) $request->input('pg_cgpa') : null;
                        $academicPayload['pg_current_status'] = $request->input('pg_current_status') ?: null;
                    }

                    StudentAcademicDetail::updateOrCreate(
                        [
                            'applino' => $crmStudent->applinano,
                            'phoneno' => $crmStudent->phoneno,
                        ],
                        $academicPayload
                    );

                    $crmStudent->update([
                        'stage3' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 4;
                $msg = 'Stage 3: Academic Records saved successfully!';
            } elseif ($step === 4) {
                self::ensureStudentUploadsTable();

                $request->validate([
                    'docs' => ['nullable', 'array'],
                    'docs.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
                ], [
                    'docs.*.mimes' => 'Documents must be PDF, JPG, JPEG, or PNG files.',
                    'docs.*.max' => 'Each uploaded document must be 5MB or smaller.',
                ]);

                if ($crmStudent) {
                    $allowedDocKeys = [
                        'marksheet10', 'marksheet12', 'btech', 'mtech', 'pg',
                        'aadhaar', 'photo', 'tc', 'study_conduct'
                    ];
                    $appliFolder = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($crmStudent->applinano ?: 'APP'));
                    $destDir = public_path('uploads/documents/' . $appliFolder);

                    $crmId = $crmStudent->crmid ?? \App\Services\CrmIdService::generateForStudent($crmStudent);

                    $uploadPayload = [
                        'crmid' => $crmId,
                        'applino' => $crmStudent->applinano,
                        'phoneno' => $crmStudent->phoneno,
                        'log_userid' => $user?->usercode ?? $user?->name ?? 'STUDENT',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ];

                    $fieldMapping = [
                        'marksheet10' => ['tenth_file_name', 'tenth_file_path'],
                        'marksheet12' => ['inter_file_name', 'inter_file_path'],
                        'btech' => ['btech_file_name', 'btech_file_path'],
                        'mtech' => ['mtech_file_name', 'mtech_file_path'],
                        'pg' => ['pg_file_name', 'pg_file_path'],
                        'aadhaar' => ['aadhar_file_name', 'aadhar_file_path'],
                        'photo' => ['passport_file_name', 'passport_file_path'],
                        'tc' => ['transfer_file_name', 'transfer_file_path'],
                        'study_conduct' => ['study_conduct_file_name', 'study_conduct_file_path'],
                    ];

                    if ($request->hasFile('docs')) {
                        if (!is_dir($destDir)) {
                            mkdir($destDir, 0755, true);
                        }
                        foreach ($request->file('docs') as $docKey => $file) {
                            if (!$file || !in_array((string) $docKey, $allowedDocKeys, true)) {
                                continue;
                            }
                            foreach (glob($destDir . DIRECTORY_SEPARATOR . $docKey . '_*') ?: [] as $oldFile) {
                                @unlink($oldFile);
                            }
                            $ext = strtolower($file->getClientOriginalExtension() ?: 'pdf');
                            $origName = $file->getClientOriginalName();
                            $base = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($origName, PATHINFO_FILENAME));
                            $base = trim((string) $base, '_') ?: $docKey;
                            $fileName = $docKey . '_' . substr($base, 0, 40) . '.' . $ext;
                            $file->move($destDir, $fileName);

                            $relativeFilePath = 'uploads/documents/' . $appliFolder . '/' . $fileName;

                            if (isset($fieldMapping[$docKey])) {
                                [$nameCol, $pathCol] = $fieldMapping[$docKey];
                                $uploadPayload[$nameCol] = $origName;
                                $uploadPayload[$pathCol] = $relativeFilePath;
                            }
                        }
                    }

                    try {
                        StudentUpload::updateOrCreate(
                            [
                                'applino' => $crmStudent->applinano,
                                'phoneno' => $crmStudent->phoneno,
                            ],
                            $uploadPayload
                        );
                    } catch (\Throwable $e) {
                        Log::error('[DashboardsController] StudentUpload save error: ' . $e->getMessage());
                    }

                    $crmUpdates = [
                        'stage4' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ];
                    if ($request->filled('degree_level')) {
                        $lvl = strtolower(trim((string)$request->input('degree_level')));
                        if ($lvl === 'mtech') {
                            $crmUpdates['programlevel'] = 'PG';
                        } elseif ($lvl === 'phd') {
                            $crmUpdates['programlevel'] = 'Doctoral';
                        } elseif ($lvl === 'btech') {
                            $crmUpdates['programlevel'] = 'UG';
                        }
                    }
                    $crmStudent->update($crmUpdates);
                }
                $nextStep = 5;
                $msg = 'Stage 4 documents saved successfully!';
            } elseif ($step === 5) {
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage5' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 6;
                $msg = 'Stage 5: Application Download completed!';
            } elseif ($step === 6) {
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage6' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 6;
                $msg = 'Stage 6: VSAT Result verified!';
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
     * Ensure the crm.student_uploads table exists in the database.
     */
    public static function ensureStudentUploadsTable(): void
    {
        try {
            DB::unprepared("
                CREATE TABLE IF NOT EXISTS crm.student_uploads (
                    id BIGSERIAL PRIMARY KEY,
                    crmid VARCHAR(50),
                    applino VARCHAR(50) NOT NULL,
                    phoneno VARCHAR(20) NOT NULL,

                    tenth_file_name VARCHAR(255),
                    tenth_file_path VARCHAR(500),

                    inter_file_name VARCHAR(255),
                    inter_file_path VARCHAR(500),

                    btech_file_name VARCHAR(255),
                    btech_file_path VARCHAR(500),

                    mtech_file_name VARCHAR(255),
                    mtech_file_path VARCHAR(500),

                    pg_file_name VARCHAR(255),
                    pg_file_path VARCHAR(500),

                    aadhar_file_name VARCHAR(255),
                    aadhar_file_path VARCHAR(500),

                    passport_file_name VARCHAR(255),
                    passport_file_path VARCHAR(500),

                    transfer_file_name VARCHAR(255),
                    transfer_file_path VARCHAR(500),

                    study_conduct_file_name VARCHAR(255),
                    study_conduct_file_path VARCHAR(500),

                    log_userid VARCHAR(50),
                    log_ipaddress VARCHAR(255),
                    log_timestamp TIMESTAMP DEFAULT now(),

                    CONSTRAINT uq_student_uploads_applino_phone UNIQUE (applino, phoneno)
                );

                CREATE INDEX IF NOT EXISTS idx_student_uploads_crmid ON crm.student_uploads (crmid);
                CREATE INDEX IF NOT EXISTS idx_student_uploads_applino ON crm.student_uploads (applino);
            ");
        } catch (\Throwable $e) {
            // Silently continue if table already exists
        }
    }

    /**
     * Scan uploaded documents for an application number from DB (crm.student_uploads) and public/uploads/documents/{applino}.
     *
     * @return array<string, array{filename: string, path: string, url: string, mime: string, ext: string}>
     */
    public static function getUploadedDocuments(string $applino): array
    {
        self::ensureStudentUploadsTable();

        $cleanAppli = preg_replace('/[^A-Za-z0-9_-]/', '', $applino);
        if ($cleanAppli === '' || $cleanAppli === '0') {
            return [];
        }

        $dir = public_path('uploads/documents/' . $cleanAppli);
        $keys = ['marksheet10', 'marksheet12', 'btech', 'mtech', 'pg', 'aadhaar', 'photo', 'tc', 'study_conduct'];
        $mimeMap = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];
        $found = [];

        // 1. Check from database (crm.student_uploads)
        try {
            $dbUpload = StudentUpload::where('applino', $cleanAppli)->first();
            if ($dbUpload) {
                $fieldMap = [
                    'marksheet10' => ['tenth_file_name', 'tenth_file_path'],
                    'marksheet12' => ['inter_file_name', 'inter_file_path'],
                    'btech' => ['btech_file_name', 'btech_file_path'],
                    'mtech' => ['mtech_file_name', 'mtech_file_path'],
                    'pg' => ['pg_file_name', 'pg_file_path'],
                    'aadhaar' => ['aadhar_file_name', 'aadhar_file_path'],
                    'photo' => ['passport_file_name', 'passport_file_path'],
                    'tc' => ['transfer_file_name', 'transfer_file_path'],
                    'study_conduct' => ['study_conduct_file_name', 'study_conduct_file_path'],
                ];
                foreach ($fieldMap as $k => [$nameCol, $pathCol]) {
                    if (!empty($dbUpload->{$nameCol}) || !empty($dbUpload->{$pathCol})) {
                        $fName = $dbUpload->{$nameCol} ?: basename((string)$dbUpload->{$pathCol});
                        $fPath = $dbUpload->{$pathCol} ?: ($dir . DIRECTORY_SEPARATOR . $fName);
                        $ext = strtolower(pathinfo($fName, PATHINFO_EXTENSION) ?: 'pdf');
                        $found[$k] = [
                            'filename' => $fName,
                            'path' => $fPath,
                            'url' => asset(str_starts_with($fPath, 'uploads/') ? $fPath : ('uploads/documents/' . $cleanAppli . '/' . $fName)),
                            'mime' => $mimeMap[$ext] ?? 'application/octet-stream',
                            'ext' => $ext,
                        ];
                    }
                }
            }
        } catch (\Throwable $e) {}

        // 2. Also check physical disk directory
        if (is_dir($dir)) {
            foreach ($keys as $key) {
                if (isset($found[$key])) {
                    continue;
                }
                $matches = glob($dir . DIRECTORY_SEPARATOR . $key . '_*');
                if (!empty($matches) && is_file($matches[0])) {
                    $fullPath = $matches[0];
                    $filename = basename($fullPath);
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $found[$key] = [
                        'filename' => $filename,
                        'path' => $fullPath,
                        'url' => asset('uploads/documents/' . $cleanAppli . '/' . $filename),
                        'mime' => $mimeMap[$ext] ?? 'application/octet-stream',
                        'ext' => $ext,
                    ];
                }
            }
        }

        return $found;
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

    /**
     * Apply student personal coupon from Stage 1 Fee Payment card.
     */
    public function applyPersonalCoupon(Request $request): \Illuminate\Http\JsonResponse
    {
        $code = (string) $request->input('coupon_code', '');
        $applino = (string) $request->input('applino', '');

        if (empty($applino)) {
            $user = Auth::user();
            $applino = $user?->usercode ?? '2027000005';
        }

        $res = \App\Services\CouponService::validateAndApplyPersonalCoupon($code, $applino);
        return response()->json($res);
    }

    /**
     * Remove applied personal coupon from student session.
     */
    public function removePersonalCoupon(): \Illuminate\Http\JsonResponse
    {
        $res = \App\Services\CouponService::removeAppliedCoupon();
        return response()->json($res);
    }
}
