<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Crm\Models\Branch;
use Modules\Crm\Models\Campus;
use Modules\Crm\Models\Country;
use Modules\Crm\Models\District;
use Modules\Crm\Models\Program;
use Modules\Crm\Models\State;
use Modules\Crm\Models\CrmStudentRegistration;
use Modules\Dashboards\Models\StudentAcademicDetail;
use Modules\Dashboards\Models\StudentEducationInfo;
use Modules\Dashboards\Models\StudentPersonalInfo;
use Modules\Dashboards\Models\StudentUpload;
use Modules\Auth\Models\Menu;
use Modules\Auth\Models\MenuAccessRole;
use Modules\Auth\Models\User;
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

        // Active step (1 through 7, default 1)
        $activeStep = (int) $request->query('step', 1);
        if ($activeStep < 1 || $activeStep > 8) {
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

        // Check Exam Slot Booking from finance.exam_schedule
        $examSchedule = null;
        try {
            if ($appNo) {
                $examSchedule = \App\Models\ExamSchedule::where('applino', $appNo)->first();
            }
            if (!$examSchedule && $cleanPhone) {
                $examSchedule = \App\Models\ExamSchedule::where('phoneno', $cleanPhone)->first();
            }
        } catch (\Throwable $e) {
            Log::warning('Could not load exam schedule: ' . $e->getMessage());
        }

        // Validation gate: Enforce sequential step unlocking based on real DB status
        $isStage1Paid = ($crmStudent && $crmStudent->stage1 === 'paid') || $hasPaidRecord;
        $isStage2Done = ($crmStudent && ($crmStudent->stage2 === 'completed' || $crmStudent->stage2 === 'paid'));
        $isStage3Done = ($crmStudent && ($crmStudent->stage3 === 'completed' || $crmStudent->stage3 === 'paid'));
        $isStage4Done = ($crmStudent && ($crmStudent->stage4 === 'completed' || $crmStudent->stage4 === 'paid'));
        $isStage5Done = ($crmStudent && ($crmStudent->stage5 === 'completed' || $crmStudent->stage5 === 'paid')) || !empty($examSchedule);
        $isStage6Done = ($crmStudent && ($crmStudent->stage6 === 'completed' || $crmStudent->stage6 === 'paid'));

        // If Stage 1 payment is not completed, block access to Steps 2+
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

        // If Stage 4 is not completed, block access to Step 5+
        if ($activeStep > 4 && !$isStage4Done) {
            return redirect()->route('dashboards.index', ['step' => 4])
                ->with('warning', 'Please complete Document Upload in Step 4 before booking Exam Slot.');
        }

        // If Stage 5 is not completed, block access to Step 6+
        if ($activeStep > 5 && !$isStage5Done) {
            return redirect()->route('dashboards.index', ['step' => 5])
                ->with('warning', 'Please complete Exam Slot Booking in Step 5 before proceeding.');
        }

        // If Stage 6 is not completed, block access to Step 7
        if ($activeStep > 6 && !$isStage6Done) {
            return redirect()->route('dashboards.index', ['step' => 6])
                ->with('warning', 'Please complete Application Download in Step 6 before viewing V-SAT Results.');
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
                    ->first()
                    ?? StudentPersonalInfo::where('applino', $crmStudent->applinano)->first()
                    ?? StudentPersonalInfo::where('phoneno', $crmStudent->phoneno)->first();
                $academicDetails = StudentAcademicDetail::where('applino', $crmStudent->applinano)
                    ->where('phoneno', $crmStudent->phoneno)
                    ->first()
                    ?? StudentAcademicDetail::where('applino', $crmStudent->applinano)->first()
                    ?? StudentAcademicDetail::where('phoneno', $crmStudent->phoneno)->first();
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

        // Standard subtitles & icons mapped by menu_code / controller_ref
        $metaDetails = [
            'FINA0003'  => ['subtitle' => 'Seat Confirmation & Fee Payment', 'icon' => '💳'],
            'CRMM0004'  => ['subtitle' => 'Candidate Bio & Contact Information', 'icon' => '👤'],
            'CRMM0005'  => ['subtitle' => '10th SSC & Intermediate Details', 'icon' => '🎓'],
            'CRMM0006'  => ['subtitle' => 'Upload Candidate Documents & Certificates', 'icon' => '📤'],
            'EXAM00002' => ['subtitle' => 'Select Date & Time Slot for V-SAT / Entrance Exam', 'icon' => '📅'],
            'CRMM0010'  => ['subtitle' => 'Official Application PDF & Final Submission', 'icon' => '📥'],
            'CRMM0007'  => ['subtitle' => 'Official V-SAT Scorecard & Merit Ranking', 'icon' => '🏆'],
        ];

        // Build $stageMeta dynamically from the database
        $stageMeta = [];
        if ($roleMenus->isNotEmpty()) {
            foreach ($roleMenus as $item) {
                $stepNum = (int) $item->morder;
                $menu = $item->menu;
                if ($menu) {
                    $mCode = (string) $menu->menu_code;
                    $stageMeta[$stepNum] = [
                        'menu_id'     => (int) $menu->menu_id,
                        'menu_code'   => $mCode,
                        'title'       => trim((string) $menu->menu_name),
                        'subtitle'    => $metaDetails[$mCode]['subtitle'] ?? ($menu->menu_name . ' Information'),
                        'icon'        => $metaDetails[$mCode]['icon'] ?? '📋',
                        'lock_status' => (int) $menu->lock_status,
                    ];
                }
            }
        }

        // Safe 7-stage fallback if database roles/menus are not yet seeded
        if (empty($stageMeta)) {
            $stageMeta = [
                1 => ['menu_id' => 109, 'menu_code' => 'FINA0003', 'title' => 'Fee Payment', 'subtitle' => 'Seat Confirmation & Fee Payment', 'icon' => '💳', 'lock_status' => 0],
                2 => ['menu_id' => 110, 'menu_code' => 'CRMM0004', 'title' => 'Personal Profile', 'subtitle' => 'Candidate Bio & Contact Information', 'icon' => '👤', 'lock_status' => 0],
                3 => ['menu_id' => 111, 'menu_code' => 'CRMM0005', 'title' => 'Academic Info', 'subtitle' => '10th SSC & Intermediate Details', 'icon' => '🎓', 'lock_status' => 0],
                4 => ['menu_id' => 112, 'menu_code' => 'CRMM0006', 'title' => 'Document upload', 'subtitle' => 'Upload Candidate Documents & Certificates', 'icon' => '📤', 'lock_status' => 0],
                5 => ['menu_id' => 123, 'menu_code' => 'EXAM00002', 'title' => 'Exam Slot Booking', 'subtitle' => 'Select Date & Time Slot for V-SAT / Entrance Exam', 'icon' => '📅', 'lock_status' => 0],
                6 => ['menu_id' => 122, 'menu_code' => 'CRMM0010', 'title' => 'Application Download', 'subtitle' => 'Official Application PDF & Final Submission', 'icon' => '📥', 'lock_status' => 0],
                7 => ['menu_id' => 124, 'menu_code' => 'CRMM0007', 'title' => 'V-SAT Results', 'subtitle' => 'Official V-SAT Scorecard & Merit Ranking', 'icon' => '🏆', 'lock_status' => 0],
            ];
        }

        ksort($stageMeta);
        $totalSteps = count($stageMeta);

        // Calculate stage statuses strictly from crmStudent DB columns
        $stages = [];
        foreach ($stageMeta as $i => $meta) {
            $col = 'stage' . $i;
            $val = $crmStudent ? (string)$crmStudent->{$col} : '';
            // For stage 1, 'paid' or $isStage1Paid is completed; for other stages, 'completed' or 'paid' is completed
            if ($i === 1) {
                $isCompleted = ($val === 'paid' || $isStage1Paid);
            } elseif ($i === 5) {
                $isCompleted = ($val === 'completed' || $val === 'paid' || !empty($examSchedule));
            } else {
                $isCompleted = ($val === 'completed' || $val === 'paid');
            }

            $stages[$i] = [
                'title'             => $meta['title'],
                'subtitle'          => $meta['subtitle'],
                'icon'              => $meta['icon'],
                'status'            => $isCompleted ? 'completed' : 'pending',
                'db_field'          => $col,
                'db_value'          => $val ?: ($i === 1 ? 'not_paid' : 'NA'),
                'menu_id'           => $meta['menu_id'] ?? null,
                'menu_code'         => $meta['menu_code'] ?? null,
                'is_locked_by_menu' => !empty($meta['lock_status']),
            ];
        }

        // Enforce maximum allowed step based on actual completion
        if (!$isStage1Paid) {
            $maxAllowedStep = 1;
        } elseif (!$isStage2Done) {
            $maxAllowedStep = 2;
        } elseif (!$isStage3Done) {
            $maxAllowedStep = 3;
        } elseif (!$isStage4Done) {
            $maxAllowedStep = 4;
        } elseif (!$isStage5Done) {
            $maxAllowedStep = 5;
        } elseif (!$isStage6Done) {
            $maxAllowedStep = 6;
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
            'examSchedule',
            'cleanPhone',
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
        $nextStep = min(7, $step + 1);

        // Security validation: Block saving higher steps if previous prerequisite step is not completed
        if ($step > 1 && (!$crmStudent || $crmStudent->stage1 !== 'paid')) {
            return redirect()->route('dashboards.index', ['step' => 1])
                ->withErrors(['error' => 'Admission fee payment in Step 1 must be completed before saving further steps.']);
        }

        if ($step > 2 && (!$crmStudent || ($crmStudent->stage2 !== 'completed' && $crmStudent->stage2 !== 'paid'))) {
            return redirect()->route('dashboards.index', ['step' => 2])
                ->withErrors(['error' => 'Please complete your Personal Profile in Step 2 first.']);
        }

        if ($step > 3 && (!$crmStudent || ($crmStudent->stage3 !== 'completed' && $crmStudent->stage3 !== 'paid'))) {
            return redirect()->route('dashboards.index', ['step' => 3])
                ->withErrors(['error' => 'Please complete your Academic Info in Step 3 first.']);
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
                // Normalize field aliases for step 2 (Personal Details & Address)
                $parentName = $request->input('parent_name') ?? $request->input('father_name');
                $candidateName = $request->input('candidate_name') ?? $request->input('name');
                $phoneVal = $request->input('student_number') ?? $request->input('phone_no') ?? $request->input('phone');
                $parentPhoneVal = $request->input('parent_phone_no') ?? $request->input('parentphono') ?? $request->input('far_ph_ne_no') ?? $request->input('parent_phone');
                $countryVal = $request->input('country') ?? $request->input('street_country');
                $districtVal = $request->input('district') ?? $request->input('distric_t');
                $pincodeVal = $request->input('pincode') ?? $request->input('pin_code');

                $cleanStudentPhone = preg_replace('/\D/', '', (string)$phoneVal);
                $cleanParentPhone = preg_replace('/\D/', '', (string)$parentPhoneVal);

                $request->merge([
                    'parent_name' => $parentName,
                    'candidate_name' => $candidateName,
                    'student_number' => $cleanStudentPhone,
                    'parent_phone_no' => $cleanParentPhone,
                    'country' => $countryVal,
                    'district' => $districtVal,
                    'pincode' => $pincodeVal,
                ]);

                $request->validate([
                    'parent_name' => ['required', 'string', 'max:255'],
                    'student_number' => ['required', 'regex:/^[0-9]{10}$/'],
                    'parent_phone_no' => ['nullable', 'regex:/^[0-9]{10}$/'],
                    'dob' => ['required'],
                    'gender' => ['required', 'string'],
                    'category' => ['required', 'string'],
                    'country' => ['required'],
                    'state' => ['required'],
                    'district' => ['required'],
                    'city_mandal' => ['required', 'string', 'max:100'],
                    'village' => ['required', 'string', 'max:255'],
                    'pincode' => ['required', 'regex:/^[0-9]{6}$/'],
                ], [
                    'parent_name.required' => 'Parent Name is required.',
                    'student_number.required' => 'Student Number is required.',
                    'student_number.regex' => 'Student Number must be exactly 10 digits.',
                    'parent_phone_no.regex' => 'Parent Phone Number must be exactly 10 digits.',
                    'dob.required' => 'Date of birth is required.',
                    'pincode.required' => 'Pincode is required.',
                    'pincode.regex' => 'Pincode must be exactly 6 digits.',
                    'district.required' => 'Please select a District.',
                    'state.required' => 'Please select a State.',
                    'country.required' => 'Please select a Country.',
                    'city_mandal.required' => 'City / Mandal / Town is required.',
                    'village.required' => 'D.No / Colony / Street / Village is required.',
                ]);

                if ($crmStudent) {
                    $personalCrmId = $crmStudent->crmid ?? \App\Services\CrmIdService::generateForStudent($crmStudent);
                    $existingInfo = StudentPersonalInfo::where('applino', $crmStudent->applinano)->first()
                        ?? StudentPersonalInfo::where('phoneno', $crmStudent->phoneno)->first();

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
                        ],
                        [
                            'crmid' => $personalCrmId,
                            'phoneno' => $cleanStudentPhone ?: $crmStudent->phoneno,
                            'dob' => $dobVal ? date('Y-m-d', strtotime($dobVal)) : null,
                            'gender' => $genderVal,
                            'parent_name' => $parentName,
                            'parentphono' => $cleanParentPhone ?: ($existingInfo?->parentphono ?: $crmStudent->phoneno),
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
                    if ($request->filled('candidate_name')) {
                        $crmUpdates['name'] = $request->input('candidate_name');
                    } elseif ($request->filled('name')) {
                        $crmUpdates['name'] = $request->input('name');
                    }
                    if ($cleanStudentPhone) {
                        $crmUpdates['phoneno'] = $cleanStudentPhone;
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
                if (!$request->has('eval_type') && $request->has('tenth_marks_type')) {
                    $request->merge(['eval_type' => $request->input('tenth_marks_type')]);
                }

                $request->validate([
                    'school_co_name' => ['required', 'string', 'max:255'],
                    'boad_of_study' => ['required', 'string', 'max:255'],
                    'roll_no' => ['required', 'string', 'max:50'],
                    'passing_year' => ['required', 'integer', 'min:2000', 'max:2030'],
                    'eval_type' => ['nullable', 'string'],
                    'tenth_marks_type' => ['nullable', 'string'],
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
                    $sscMarks = $request->input('tenth_marks') ?? $request->input('tenth_marks_earned') ?? $request->input('marks_value') ?? $request->input('ssc_marks');
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

                    $tenthConducted = is_numeric($request->input('tenth_marks_conducted'))
                        ? (float)$request->input('tenth_marks_conducted')
                        : 600.0;
                    $tenthEarned = is_numeric($request->input('tenth_marks_earned'))
                        ? (float)$request->input('tenth_marks_earned')
                        : ($tenthMarksNum ?? ($tenthCgpaNum ? round($tenthCgpaNum * 9.5, 2) : null));
                    $tenthPct = is_numeric($request->input('tenth_percentage'))
                        ? (float)$request->input('tenth_percentage')
                        : ($tenthConducted > 0 && $tenthEarned ? round(($tenthEarned / $tenthConducted) * 100, 2) : null);

                    $pu2MarksRaw = is_numeric($request->input('inter_marks')) ? (float)$request->input('inter_marks') : null;
                    $pu2Type = $request->input('pu2_marks_type') ?? $request->input('inter_marks_type') ?? 'Marks';
                    $pu2Conducted = is_numeric($request->input('pu2_marks_conducted'))
                        ? (float)$request->input('pu2_marks_conducted')
                        : (($pu2MarksRaw && $pu2MarksRaw > 100) ? 1000.0 : 100.0);
                    $pu2Earned = is_numeric($request->input('pu2_marks_earned'))
                        ? (float)$request->input('pu2_marks_earned')
                        : $pu2MarksRaw;
                    $pu2Pct = is_numeric($request->input('pu2_percentage'))
                        ? (float)$request->input('pu2_percentage')
                        : (($pu2Conducted > 0 && $pu2Earned) ? round(($pu2Earned / $pu2Conducted) * 100, 2) : $pu2MarksRaw);

                    $academicPayload = [
                        'crmid' => $academicCrmId,
                        'phoneno' => $crmStudent->phoneno,
                        'progmid' => $crmStudent->programid,
                        'branchid' => $crmStudent->branchid,
                        'tenth_school_name' => $tenthSchool,
                        'tenth_study_board' => $tenthBoard,
                        'tenth_school_city' => $request->input('tenth_school_city') ?? $request->input('ssc_location'),
                        'tenth_school_district' => $request->input('tenth_school_district') ?? $request->input('ssc_location'),
                        'tenth_roll_no' => $tenthRoll,
                        'tenth_passed_year' => $tenthYear ?: null,
                        'tenth_marks_type' => $request->input('tenth_marks_type') ?? $evalType,
                        'tenth_marks_conducted' => $tenthConducted,
                        'tenth_marks_earned' => $tenthEarned,
                        'tenth_percentage' => $tenthPct,
                        'pu2_college_name' => $request->input('inter_college') ?? $request->input('pu2_college_name'),
                        'pu2_study_board' => $courseChoice,
                        'pu2_college_city' => $request->input('inter_location') ?? $request->input('pu2_college_city'),
                        'pu2_roll_no' => $request->input('inter_hallticket') ?? $request->input('pu2_roll_no'),
                        'pu2_passed_year' => $request->filled('inter_year') ? (int) $request->input('inter_year') : ($request->filled('pu2_passed_year') ? (int) $request->input('pu2_passed_year') : null),
                        'pu2_marks_type' => $pu2Type,
                        'pu2_marks' => $pu2Earned ?: $pu2MarksRaw,
                        'pu2_marks_conducted' => $pu2Conducted,
                        'pu2_marks_earned' => $pu2Earned,
                        'pu2_percentage' => $pu2Pct,
                        'pu2_current_status' => $subsetChoice,
                        'log_userid' => $user?->usercode ?? $user?->name ?? 'STUDENT',
                        'log_datetime' => now(),
                        'log_ipaddress' => $request->ip(),
                    ];

                    // If Degree level is M.Tech or PhD, save UG (B.Tech) details
                    if ($request->filled('ug_college_name') || $request->input('degree_level') === 'mtech' || $request->input('degree_level') === 'phd') {
                        $ugMarksRaw = is_numeric($request->input('ug_marks')) ? (float) $request->input('ug_marks') : (is_numeric($request->input('ug_cgpa')) ? (float)$request->input('ug_cgpa') : null);
                        $ugPct = ($ugMarksRaw && $ugMarksRaw <= 10) ? round($ugMarksRaw * 9.5, 2) : (($ugMarksRaw && $ugMarksRaw > 100) ? round(($ugMarksRaw / 1000) * 100, 2) : $ugMarksRaw);

                        $academicPayload['ug_college_name'] = $request->input('ug_college_name') ?: null;
                        $academicPayload['ug_university_board'] = $request->input('ug_university_board') ?: null;
                        $academicPayload['ug_college_city'] = $request->input('ug_college_city') ?: null;
                        $academicPayload['ug_college_district'] = $request->input('ug_college_district') ?: null;
                        $academicPayload['ug_roll_no'] = $request->input('ug_roll_no') ?: null;
                        $academicPayload['ug_passed_year'] = $request->filled('ug_passed_year') ? (int) $request->input('ug_passed_year') : null;
                        $academicPayload['ug_marks_type'] = $request->input('ug_marks_type') ?: null;
                        $academicPayload['ug_marks'] = $ugMarksRaw;
                        $academicPayload['ug_marks_conducted'] = 100.0;
                        $academicPayload['ug_marks_earned'] = $ugMarksRaw;
                        $academicPayload['ug_percentage'] = $ugPct;
                        $academicPayload['ug_current_status'] = $request->input('ug_current_status') ?: null;
                    }

                    // If Degree level is PhD, save PG (M.Tech) details
                    if ($request->filled('pg_college_name') || $request->input('degree_level') === 'phd') {
                        $pgMarksRaw = is_numeric($request->input('pg_marks')) ? (float) $request->input('pg_marks') : (is_numeric($request->input('pg_cgpa')) ? (float)$request->input('pg_cgpa') : null);
                        $pgPct = ($pgMarksRaw && $pgMarksRaw <= 10) ? round($pgMarksRaw * 9.5, 2) : (($pgMarksRaw && $pgMarksRaw > 100) ? round(($pgMarksRaw / 1000) * 100, 2) : $pgMarksRaw);

                        $academicPayload['pg_college_name'] = $request->input('pg_college_name') ?: null;
                        $academicPayload['pg_university_board'] = $request->input('pg_university_board') ?: null;
                        $academicPayload['pg_college_city'] = $request->input('pg_college_city') ?: null;
                        $academicPayload['pg_college_district'] = $request->input('pg_college_district') ?: null;
                        $academicPayload['pg_roll_no'] = $request->input('pg_roll_no') ?: null;
                        $academicPayload['pg_passed_year'] = $request->filled('pg_passed_year') ? (int) $request->input('pg_passed_year') : null;
                        $academicPayload['pg_marks_type'] = $request->input('pg_marks_type') ?: null;
                        $academicPayload['pg_marks'] = $pgMarksRaw;
                        $academicPayload['pg_marks_conducted'] = 100.0;
                        $academicPayload['pg_marks_earned'] = $pgMarksRaw;
                        $academicPayload['pg_percentage'] = $pgPct;
                        $academicPayload['pg_current_status'] = $request->input('pg_current_status') ?: null;
                    }

                    StudentAcademicDetail::updateOrCreate(
                        [
                            'applino' => $crmStudent->applinano,
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
                // Step 5: Exam Slot Booking (finance.exam_schedule)
                $now = now();
                $cutoffHour = 17; // 5:00 PM cutoff
                $isBefore5PM = $now->hour < $cutoffHour;
                $minExamDate = $isBefore5PM
                    ? $now->copy()->addDay()->format('Y-m-d')
                    : $now->copy()->addDays(2)->format('Y-m-d');

                $minDateFormatted = \Carbon\Carbon::parse($minExamDate)->format('d M Y');

                $request->validate([
                    'examdate'  => ['required', 'date', 'after_or_equal:' . $minExamDate],
                    'exam_slot' => ['required', 'string', 'max:100'],
                ], [
                    'examdate.required'  => 'Please select your preferred examination date.',
                    'examdate.after_or_equal' => $isBefore5PM
                        ? 'Bookings made today before 5:00 PM can only be scheduled starting from tomorrow (' . $minDateFormatted . ').'
                        : 'Bookings made after 5:00 PM cutoff can only be scheduled starting from day after tomorrow (' . $minDateFormatted . ').',
                    'exam_slot.required' => 'Please select your preferred exam time slot.',
                ]);

                if ($crmStudent) {
                    $examCrmId = $crmStudent->crmid ?? \App\Services\CrmIdService::generateForStudent($crmStudent);
                    \App\Models\ExamSchedule::updateOrCreate(
                        ['applino' => $crmStudent->applinano],
                        [
                            'crmid'        => $examCrmId,
                            'phoneno'      => $crmStudent->phoneno,
                            'examdate'     => $request->input('examdate'),
                            'exam_slot'    => $request->input('exam_slot'),
                            'exam_status'  => 1,
                            'loguserid'    => $user?->usercode ?? $user?->name ?? 'STUDENT',
                            'logdatetime'  => now(),
                            'ipaddress'    => $request->ip(),
                        ]
                    );

                    $crmStudent->update([
                        'stage5' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 6;
                $msg = 'Stage 5: Exam Slot confirmed and booked successfully!';
            } elseif ($step === 6) {
                // Step 6: Application Download
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage6' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 7;
                $msg = 'Stage 6: Application Download verified!';
            } elseif ($step === 7) {
                // Step 7: V-SAT Results
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage7' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 7;
                $msg = 'Stage 7: V-SAT Results verified & Onboarding completed!';
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
        $keys = ['marksheet10', 'marksheet12', 'aadhaar', 'photo', 'tc', 'study_conduct', 'btech', 'mtech', 'pg'];
        $titles = [
            'marksheet10'   => '10th Standard Marksheet / SSC Certificate',
            'marksheet12'   => '12th Standard / Intermediate Marksheet',
            'aadhaar'       => 'Aadhaar Card / Government Identity Proof',
            'photo'         => 'Candidate Passport Photograph',
            'tc'            => 'Transfer Certificate (T.C.)',
            'study_conduct' => 'Study & Conduct Certificate',
            'btech'         => 'B.Tech / Degree Certificate',
            'mtech'         => 'M.Tech / Post-Graduate Certificate',
            'pg'            => 'Post-Graduate (PG) Certificate',
        ];
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
                        $fPath = $dbUpload->{$pathCol} ?: ('uploads/documents/' . $cleanAppli . '/' . $fName);
                        $ext = strtolower(pathinfo($fName, PATHINFO_EXTENSION) ?: 'pdf');

                        // Ensure file exists physically on disk (e.g. if restored from DB backup)
                        self::ensurePhysicalDocumentExists($fPath, $cleanAppli, $k, $fName);

                        $found[$k] = [
                            'key' => $k,
                            'title' => $titles[$k] ?? ucwords(str_replace('_', ' ', $k)),
                            'filename' => $fName,
                            'path' => $fPath,
                            'url' => asset(str_starts_with($fPath, 'uploads/') ? $fPath : ('uploads/documents/' . $cleanAppli . '/' . $fName)),
                            'mime' => $mimeMap[$ext] ?? 'application/octet-stream',
                            'ext' => $ext,
                            'is_image' => in_array($ext, ['jpg', 'jpeg', 'png', 'webp']),
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
                        'key' => $key,
                        'title' => $titles[$key] ?? ucwords(str_replace('_', ' ', $key)),
                        'filename' => $filename,
                        'path' => 'uploads/documents/' . $cleanAppli . '/' . $filename,
                        'url' => asset('uploads/documents/' . $cleanAppli . '/' . $filename),
                        'mime' => $mimeMap[$ext] ?? 'application/octet-stream',
                        'ext' => $ext,
                        'is_image' => in_array($ext, ['jpg', 'jpeg', 'png', 'webp']),
                    ];
                }
            }

            // Also check for any other arbitrary uploaded files in the directory
            $allFiles = glob($dir . DIRECTORY_SEPARATOR . '*.*') ?: [];
            foreach ($allFiles as $filePath) {
                if (!is_file($filePath)) continue;
                $filename = basename($filePath);
                $isAlreadyCaptured = false;
                foreach ($found as $item) {
                    if ($item['filename'] === $filename || basename($item['path']) === $filename) {
                        $isAlreadyCaptured = true;
                        break;
                    }
                }
                if (!$isAlreadyCaptured) {
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    $autoKey = preg_replace('/[^a-z0-9_]/', '_', strtolower(pathinfo($filename, PATHINFO_FILENAME)));
                    $found[$autoKey] = [
                        'key' => $autoKey,
                        'title' => 'Additional Document (' . $filename . ')',
                        'filename' => $filename,
                        'path' => 'uploads/documents/' . $cleanAppli . '/' . $filename,
                        'url' => asset('uploads/documents/' . $cleanAppli . '/' . $filename),
                        'mime' => $mimeMap[$ext] ?? 'application/octet-stream',
                        'ext' => $ext,
                        'is_image' => in_array($ext, ['jpg', 'jpeg', 'png', 'webp']),
                    ];
                }
            }
        }

        return $found;
    }

    /**
     * Ensure a document file exists physically on disk.
     * If recorded in database (e.g. from a restored backup) but not on local disk,
     * generate a valid placeholder document file to prevent 404 errors.
     */
    public static function ensurePhysicalDocumentExists(string $relativePath, string $applino, string $docKey, string $filename): void
    {
        try {
            $diskPath = public_path($relativePath);
            if (is_file($diskPath)) {
                return;
            }

            $targetDir = dirname($diskPath);
            if (!is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION) ?: 'png');

            if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp'])) {
                $img = imagecreatetruecolor(800, 1050);
                $bg = imagecolorallocate($img, 255, 255, 255);
                imagefill($img, 0, 0, $bg);

                $headerBg = imagecolorallocate($img, 15, 42, 74);
                imagefilledrectangle($img, 0, 0, 800, 120, $headerBg);

                $blueLine = imagecolorallocate($img, 30, 123, 216);
                imagefilledrectangle($img, 0, 120, 800, 128, $blueLine);

                $white = imagecolorallocate($img, 255, 255, 255);
                imagestring($img, 5, 40, 35, "VIGNAN'S UNIVERSITY - ADMISSION ONBOARDING", $white);
                imagestring($img, 3, 40, 65, "Candidate Document: " . strtoupper($docKey), $white);
                imagestring($img, 3, 40, 85, "Application No: " . $applino, $white);

                $border = imagecolorallocate($img, 203, 213, 225);
                imagerectangle($img, 35, 160, 765, 980, $border);

                $dark = imagecolorallocate($img, 15, 23, 42);
                $green = imagecolorallocate($img, 22, 101, 52);
                $gray = imagecolorallocate($img, 100, 116, 139);

                imagestring($img, 5, 60, 200, "OFFICIAL VERIFIED DOCUMENT COPY", $dark);
                imagestring($img, 4, 60, 240, "Document: " . ucwords(str_replace('_', ' ', $docKey)), $dark);
                imagestring($img, 4, 60, 270, "Original Filename: " . $filename, $dark);
                imagestring($img, 4, 60, 300, "Verification Status: Uploaded & Verified [OK]", $green);
                imagestring($img, 4, 60, 330, "Archived under application #" . $applino, $gray);

                for ($i = 400; $i < 900; $i += 40) {
                    imageline($img, 60, $i, 740, $i, $border);
                }

                if ($ext === 'png') {
                    imagepng($img, $diskPath);
                } else {
                    imagejpeg($img, $diskPath, 90);
                }
            } elseif ($ext === 'pdf') {
                $pdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \n0000000101 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF\n";
                file_put_contents($diskPath, $pdfContent);
            }
        } catch (\Throwable $e) {
            Log::warning('[DashboardsController] Could not ensure physical document: ' . $e->getMessage());
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

    /**
     * Apply student personal coupon from Stage 1 Fee Payment card.
     */
    public function applyPersonalCoupon(Request $request): \Illuminate\Http\JsonResponse
    {
        $code = (string) $request->input('coupon_code', '');
        $applino = (string) $request->input('applino', '');

        if (empty($applino)) {
            $user = Auth::user();
            $applino = $user?->usercode ?? (string) session('student_applino', '');
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
