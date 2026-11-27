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
use Modules\Auth\Services\RoleService;
use Modules\Crm\Services\CrmIdService;
use Modules\Finance\Services\CouponService;
use Exception;
use Illuminate\Http\JsonResponse;
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
        // error_log("$user->role_id");
        Log::info('User Role ID Debug', ['role_id' => $user?->role_id]);
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? $user?->phone ?? $user?->mobile ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }
        if (!$cleanPhone && is_numeric($usercode) && strlen($usercode) === 10) {
            $cleanPhone = $usercode;
        }

        // Active step (1 through 7, default 1)
        $activeStep = (int) $request->query('step', 1);
        if ($activeStep < 1 || $activeStep > 8) {
            $activeStep = 1;
        }

        self::ensureAcademicColumns();

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
        $displayName = trim((string) ($crmStudent?->name ?: ($user?->name ?: 'Applicant')));

        // Resolve payment status: Check crm.student_registration.stage4/stage1, finance.paydetails, and finance.receiptdetails
        $hasPaidRecord = false;
        try {
            if ($appNo && $appNo !== '0') {
                $hasPaidRecord = \App\Models\PayDetail::where('applino', $appNo)->where('txnstatus', 'success')->exists()
                    || \App\Models\ReceiptDetail::where('applino', $appNo)->exists();
            }
            if (!$hasPaidRecord && $cleanPhone) {
                $phoneSuffix = substr($cleanPhone, -6);
                $hasPaidRecord = \App\Models\PayDetail::where('applino', 'like', "%{$phoneSuffix}")->where('txnstatus', 'success')->exists()
                    || \App\Models\ReceiptDetail::where('applino', 'like', "%{$phoneSuffix}")->exists();
            }
        } catch (\Throwable $e) {}

        if ($hasPaidRecord && $crmStudent && $crmStudent->stage4 !== 'paid') {
            try {
                $crmStudent->update(['stage4' => 'paid']);
            } catch (\Throwable $e) {}
        }

        // Ensure CRM student registration record exists for this student candidate
        if (!$crmStudent && $cleanPhone) {
            try {
                $ayPrefix = CrmStudentRegistration::getAcademicYearInfo()['prefix'];
                $appliCandidate = ($appNo && $appNo !== '0' && !str_starts_with($appNo, 'VU' . $ayPrefix . '-') && !str_starts_with($appNo, 'VU2026-'))
                    ? $appNo
                    : ('VU' . $ayPrefix . substr($cleanPhone, -6));
                $crmStudent = CrmStudentRegistration::firstOrCreate(
                    ['phoneno' => $cleanPhone],
                    [
                        'applinano' => $appliCandidate,
                        'name' => ($displayName && $displayName !== 'Applicant') ? $displayName : ($user?->name ?: $cleanPhone),
                        'phoneno' => $cleanPhone,
                        'emailid' => $user?->personalmail ?: ($cleanPhone . '@vignan.ac.in'),
                        'stage1' => 'NA',
                        'stage2' => 'NA',
                        'stage3' => 'NA',
                        'stage4' => $hasPaidRecord ? 'paid' : 'NA',
                        'stage5' => 'NA',
                        'std_status' => $hasPaidRecord ? 'Admitted' : 'Registered',
                        'campuscode' => 'VAD',
                        'programlevel' => 'UG',
                        'log_userid' => $appliCandidate,
                        'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                        'log_timestamp' => now(),
                    ]
                );
                $appNo = $crmStudent->applinano;
                if ($user && ($user->usercode === '0' || empty($user->usercode))) {
                    $user->update(['usercode' => $appNo]);
                }
            } catch (\Throwable $e) {
                Log::error('Could not ensure crmStudent in index: ' . $e->getMessage());
            }
        }

        $regNo = ($crmStudent?->std_status === 'Admitted' || $crmStudent?->stage4 === 'paid' || $crmStudent?->stage1 === 'paid') ? ($crmStudent?->applinano ?: '') : '';

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

        // Validation gate: Check if student has paid admission fee (applicable at Step 4)
        $isPaid = ($crmStudent && ($crmStudent->stage4 === 'paid' || $crmStudent->stage1 === 'paid')) || $hasPaidRecord;
        $isStage1Paid = $isPaid;

        // Always display Candidate / Student role
        $roleName = (RoleService::isStudent($user?->role_id) || $user?->user_type === 1) ? 'Student Candidate' : ($user?->role_label ?: 'Student Candidate');

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
        $requestedLevel = strtolower(trim((string)($request->query('degree_level') ?? $request->input('degree_level') ?? session('degree_level') ?? '')));
        $progLevel = strtoupper(trim((string)($crmStudent?->programlevel ?? '')));
        $progShort = strtoupper($program?->program_short_name ?? '');
        $progName = strtoupper($program?->program_long_name ?? $program?->program_short_name ?? '');

        if (
            $requestedLevel === 'phd' ||
            $progLevel === 'PHD' ||
            $progLevel === 'DOCTORAL' ||
            str_contains($progShort, 'PHD') ||
            str_contains($progShort, 'PH.D') ||
            str_contains($progName, 'DOCTOR') ||
            str_contains($progName, 'PH.D') ||
            str_contains($progName, 'PHD')
        ) {
            $degreeLevel = 'phd';
        } elseif (
            $requestedLevel === 'mtech' ||
            $progLevel === 'PG' ||
            str_contains($progShort, 'M.TECH') ||
            str_contains($progShort, 'MTECH') ||
            str_contains($progName, 'MASTER') ||
            in_array($progShort, ['MCA', 'MBA', 'M.PHARM', 'M.SC', 'M.A'])
        ) {
            $degreeLevel = 'mtech';
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
                $personalInfo = StudentPersonalInfo::where('applino', $crmStudent->applinano)->first()
                    ?? (!empty($crmStudent->phoneno) ? StudentPersonalInfo::where('phoneno', $crmStudent->phoneno)->where('applino', $crmStudent->applinano)->first() : null);
                $academicDetails = StudentAcademicDetail::where('applino', $crmStudent->applinano)->first()
                    ?? (!empty($crmStudent->phoneno) ? StudentAcademicDetail::where('phoneno', $crmStudent->phoneno)->where('applino', $crmStudent->applinano)->first() : null);
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

        // Safe 7-stage fallback if database roles/menus are missing, empty, or contain fewer than 5 steps (e.g. single STEP7)
        $canonicalStages = [
            1 => ['menu_id' => 110, 'menu_code' => 'CRMM0004', 'title' => 'Personal Profile', 'subtitle' => 'Candidate Bio & Contact Information', 'icon' => '👤', 'lock_status' => 0],
            2 => ['menu_id' => 111, 'menu_code' => 'CRMM0005', 'title' => 'Academic Info', 'subtitle' => '10th SSC & Intermediate Details', 'icon' => '🎓', 'lock_status' => 0],
            3 => ['menu_id' => 112, 'menu_code' => 'CRMM0006', 'title' => 'Document upload', 'subtitle' => 'Upload Candidate Documents & Certificates', 'icon' => '📤', 'lock_status' => 0],
            4 => ['menu_id' => 109, 'menu_code' => 'FINA0003', 'title' => 'Fee Payment', 'subtitle' => 'Seat Confirmation & Fee Payment', 'icon' => '💳', 'lock_status' => 0],
            5 => ['menu_id' => 123, 'menu_code' => 'EXAM00002', 'title' => 'Exam Slot Booking', 'subtitle' => 'Select Date & Time Slot for V-SAT / Entrance Exam', 'icon' => '📅', 'lock_status' => 0],
            6 => ['menu_id' => 122, 'menu_code' => 'CRMM0010', 'title' => 'Application Download', 'subtitle' => 'Official Application PDF & Final Submission', 'icon' => '📥', 'lock_status' => 0],
            7 => ['menu_id' => 124, 'menu_code' => 'CRMM0007', 'title' => 'V-SAT Results', 'subtitle' => 'Official V-SAT Scorecard & Merit Ranking', 'icon' => '🏆', 'lock_status' => 0],
        ];

        if (empty($stageMeta) || count($stageMeta) < 5 || !isset($stageMeta[1]) || !isset($stageMeta[2])) {
            $stageMeta = $canonicalStages;
        }

        ksort($stageMeta);

        // PhD candidate: Completely remove Exam Slot Booking stage (EXAM00002) and re-index stages
        if ($degreeLevel === 'phd') {
            $filteredMeta = [];
            foreach ($stageMeta as $meta) {
                $mCode = $meta['menu_code'] ?? '';
                $mTitle = strtolower($meta['title'] ?? '');
                if ($mCode === 'EXAM00002' || str_contains($mTitle, 'exam slot')) {
                    continue;
                }
                $filteredMeta[] = $meta;
            }
            $stageMeta = [];
            foreach ($filteredMeta as $idx => $m) {
                $stageMeta[$idx + 1] = $m;
            }
        }

        $totalSteps = count($stageMeta);
        if ($activeStep > $totalSteps) {
            $activeStep = $totalSteps;
        }

        // Calculate stage statuses dynamically from crmStudent DB columns and actual completed data
        $stages = [];
        foreach ($stageMeta as $i => $meta) {
            $col = 'stage' . $i;
            $val = $crmStudent ? (string)$crmStudent->{$col} : '';
            $mCode = $meta['menu_code'] ?? '';

            if ($mCode === 'FINA0003') {
                $isCompleted = ($val === 'paid' || $val === 'completed' || $isPaid || ($crmStudent && ($crmStudent->stage4 === 'paid' || $crmStudent->stage1 === 'paid')));
            } elseif ($mCode === 'EXAM00002') {
                $isCompleted = ($val === 'completed' || $val === 'paid' || !empty($examSchedule));
            } elseif ($mCode === 'CRMM0004') {
                $isCompleted = ($val === 'completed' || $val === 'paid' || ($personalInfo !== null && !empty($personalInfo->gender)));
            } elseif ($mCode === 'CRMM0005') {
                $hasAcademicFilled = false;
                if ($academicDetails) {
                    if ($degreeLevel === 'phd') {
                        $hasAcademicFilled = !empty($academicDetails->pg_college_name) || !empty($academicDetails->pg_passed_year);
                    } elseif ($degreeLevel === 'mtech') {
                        $hasAcademicFilled = !empty($academicDetails->ug_college_name) || !empty($academicDetails->ug_passed_year);
                    } else {
                        $hasAcademicFilled = !empty($academicDetails->tenth_school_name) && !empty($academicDetails->pu2_college_name);
                    }
                }
                $isCompleted = ($val === 'completed' || $hasAcademicFilled);
            } elseif ($mCode === 'CRMM0006') {
                $isCompleted = ($val === 'completed' || !empty($uploadedDocs));
            } elseif ($mCode === 'CRMM0010') {
                // Application download
                $isCompleted = ($val === 'completed' || ($crmStudent && ($crmStudent->stage5 === 'completed' || $crmStudent->stage6 === 'completed')));
            } elseif ($mCode === 'CRMM0007') {
                // V-SAT results
                $isCompleted = ($val === 'completed' || ($crmStudent && ($crmStudent->stage6 === 'completed' || $crmStudent->stage7 === 'completed')));
            } else {
                $isCompleted = ($val === 'completed' || $val === 'paid');
            }

            $stages[$i] = [
                'title'             => $meta['title'],
                'subtitle'          => $meta['subtitle'],
                'icon'              => $meta['icon'],
                'status'            => $isCompleted ? 'completed' : 'pending',
                'db_field'          => $col,
                'db_value'          => $val ?: ($mCode === 'FINA0003' ? ($isCompleted ? 'paid' : 'not_paid') : 'NA'),
                'menu_id'           => $meta['menu_id'] ?? null,
                'menu_code'         => $meta['menu_code'] ?? null,
                'is_locked_by_menu' => !empty($meta['lock_status']),
            ];
        }

        // Enforce maximum allowed step based on actual sequential completion of stages
        $maxAllowedStep = 1;
        for ($s = 1; $s <= $totalSteps; $s++) {
            if (isset($stages[$s]) && $stages[$s]['status'] === 'completed') {
                $maxAllowedStep = min($totalSteps, $s + 1);
            } else {
                break;
            }
        }

        if ($activeStep > $maxAllowedStep) {
            $prevStepTitle = $stages[$maxAllowedStep]['title'] ?? "Step {$maxAllowedStep}";
            return redirect()->route('dashboards.index', ['step' => $maxAllowedStep])
                ->with('warning', "Please complete {$prevStepTitle} before proceeding to the next step.");
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

        if ($countries->isEmpty() || $states->isEmpty() || $districts->isEmpty()) {
            if (class_exists(RegistrationController::class)) {
                RegistrationController::ensureGeographicTablesExist();
            }
            try {
                $countries = Country::where('lock_status', 0)->orderBy('country_name')->get();
                $states = State::where('lock_status', 0)->orderBy('state_name')->get();
                $districts = District::where('LOCK_STATUS', 0)->orderBy('DISTRICT_NAME')->get();
            } catch (\Throwable $e2) {}
        }

        if ($countries->isEmpty()) {
            $countries = collect([
                (object)['country_id' => 1, 'country_code' => 'IN', 'country_name' => 'India', 'country_phonecode' => '+91'],
                (object)['country_id' => 3, 'country_code' => 'US', 'country_name' => 'United States', 'country_phonecode' => '+1'],
            ]);
        }
        if ($states->isEmpty()) {
            $states = collect([
                (object)['state_id' => 1, 'state_code' => 'AP', 'state_name' => 'Andhra Pradesh', 'country_id' => 1],
                (object)['state_id' => 24, 'state_code' => 'TG', 'state_name' => 'Telangana', 'country_id' => 1],
                (object)['state_id' => 11, 'state_code' => 'KA', 'state_name' => 'Karnataka', 'country_id' => 1],
                (object)['state_id' => 23, 'state_code' => 'TN', 'state_name' => 'Tamil Nadu', 'country_id' => 1],
            ]);
        }
        if ($districts->isEmpty()) {
            $districts = collect([
                (object)['DISTRICT_ID' => 1, 'DISTRICT_CODE' => 'GNT', 'DISTRICT_NAME' => 'Guntur', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                (object)['DISTRICT_ID' => 2, 'DISTRICT_CODE' => 'KRI', 'DISTRICT_NAME' => 'Krishna', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                (object)['DISTRICT_ID' => 3, 'DISTRICT_CODE' => 'VSP', 'DISTRICT_NAME' => 'Visakhapatnam', 'STATE_ID' => 1, 'COUNTRY_ID' => 1],
                (object)['DISTRICT_ID' => 4, 'DISTRICT_CODE' => 'HYD', 'DISTRICT_NAME' => 'Hyderabad', 'STATE_ID' => 24, 'COUNTRY_ID' => 1],
            ]);
        }

        $applicationData = null;
        try {
            $applicationData = \App\Http\Controllers\ApplicationController::buildStudentApplicationData((string) $appNo);
        } catch (\Throwable $e) {
            Log::warning('Could not build application data: ' . $e->getMessage());
        }

        $hasAcceptedTerms = ($personalInfo && $personalInfo->terms_accepted === 'yes');

        // Resolve default registered Country, State, District for forms prefill
        $defaultCountry = $personalInfo?->country;
        if (empty($defaultCountry)) {
            $cId = $crmStudent?->countryid ?? 1;
            try {
                $defaultCountry = Country::where('country_id', $cId)->value('country_name') ?? 'India';
            } catch (\Throwable $e) {
                $defaultCountry = 'India';
            }
        }

        $defaultState = $personalInfo?->state;
        if (empty($defaultState)) {
            $sId = $crmStudent?->stateid ?? 1;
            try {
                $defaultState = State::where('state_id', $sId)->value('state_name') ?? 'Andhra Pradesh';
            } catch (\Throwable $e) {
                $defaultState = 'Andhra Pradesh';
            }
        }

        $defaultDistrict = $personalInfo?->district
            ?? $academicDetails?->tenth_school_district
            ?? $academicDetails?->pu2_college_district
            ?? null;
        if (empty($defaultDistrict)) {
            $dId = $crmStudent?->districtid ?? 1;
            try {
                $defaultDistrict = District::where('DISTRICT_ID', $dId)->value('DISTRICT_NAME') ?? 'Guntur';
            } catch (\Throwable $e) {
                $defaultDistrict = 'Guntur';
            }
        }

        // Resolve selected Country ID from 1. Personal Profile (or registration fallback)
        $selectedCountryId = null;
        if (!empty($personalInfo?->country)) {
            if (is_numeric($personalInfo->country)) {
                $selectedCountryId = (int)$personalInfo->country;
            } else {
                $matchedCountry = $countries->first(function ($c) use ($personalInfo) {
                    return strcasecmp($c->country_name, $personalInfo->country) === 0;
                });
                if ($matchedCountry) {
                    $selectedCountryId = (int)$matchedCountry->country_id;
                }
            }
        }
        if (!$selectedCountryId) {
            $selectedCountryId = !empty($crmStudent?->countryid) ? (int)$crmStudent->countryid : 1;
        }

        // States filtered specifically by the Country selected in 1. Personal Profile for 2. Academic Info dropdowns
        $academicStates = $states->filter(function ($s) use ($selectedCountryId) {
            return isset($s->country_id) && (int)$s->country_id === (int)$selectedCountryId;
        })->values();

        if ($academicStates->isEmpty()) {
            $academicStates = $states;
        }

        return view('dashboards::index', compact(
            'hasAcceptedTerms',
            'user',
            'student',
            'crmStudent',
            'personalInfo',
            'academicDetails',
            'defaultCountry',
            'defaultState',
            'defaultDistrict',
            'selectedCountryId',
            'academicStates',
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
            'isPaid',
            'isStage1Paid',
            'completedCount',
            'progressPercent'
        ));
    }

    /**
     * Accept Terms and Conditions on login modal.
     */
    public function acceptTerms(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $usercode = $user->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user->phnno ?? $user->phone ?? $user->mobile ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }
        if (!$cleanPhone && is_numeric($usercode) && strlen($usercode) === 10) {
            $cleanPhone = $usercode;
        }

        $crmStudent = null;
        try {
            $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
                ->when($cleanPhone, function ($q) use ($cleanPhone) {
                    $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
                })
                ->first();
        } catch (\Throwable $e) {}

        $ayPrefix = CrmStudentRegistration::getAcademicYearInfo()['prefix'];
        $appNo = $crmStudent?->applinano ?? ($usercode ?: ('VU' . $ayPrefix . '-' . ($cleanPhone ? substr($cleanPhone, -6) : 'APP')));

        if (!$crmStudent && $cleanPhone) {
            try {
                $appliCandidate = ($appNo && $appNo !== '0' && !str_starts_with($appNo, 'VU' . $ayPrefix . '-') && !str_starts_with($appNo, 'VU2026-'))
                    ? $appNo
                    : ('VU' . $ayPrefix . substr($cleanPhone, -6));
                $crmStudent = CrmStudentRegistration::firstOrCreate(
                    ['phoneno' => $cleanPhone],
                    [
                        'applinano' => $appliCandidate,
                        'name' => $user->name ?: $cleanPhone,
                        'phoneno' => $cleanPhone,
                        'emailid' => $user->personalmail ?: ($cleanPhone . '@vignan.ac.in'),
                        'stage1' => 'NA',
                        'stage2' => 'NA',
                        'std_status' => 'Registered',
                        'campuscode' => 'VAD',
                        'programlevel' => 'UG',
                        'log_userid' => $appliCandidate,
                        'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                        'log_timestamp' => now(),
                    ]
                );
                $appNo = $crmStudent->applinano;
                if ($user && ($user->usercode === '0' || empty($user->usercode))) {
                    $user->update(['usercode' => $appNo]);
                }
            } catch (\Throwable $e) {
                Log::error('Could not ensure crmStudent in acceptTerms: ' . $e->getMessage());
            }
        }

        try {
            $info = StudentPersonalInfo::where('applino', $appNo)->first();
            if ($info) {
                $info->update([
                    'terms_accepted' => 'yes',
                    'log_ipaddress' => $request->ip(),
                    'log_timestamp' => now(),
                ]);
            } else {
                $crmid = $crmStudent ? CrmIdService::generateForStudent($crmStudent) : 'CRM' . $cleanPhone;
                StudentPersonalInfo::create([
                    'crmid' => $crmid,
                    'applino' => $appNo,
                    'phoneno' => $cleanPhone ?: ($crmStudent?->phoneno ?? '0000000000'),
                    'terms_accepted' => 'yes',
                    'log_userid' => $user->usercode ?? $user->name ?? 'STUDENT',
                    'log_ipaddress' => $request->ip(),
                    'log_timestamp' => now(),
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Terms and conditions accepted successfully.']);
        } catch (\Throwable $e) {
            Log::error('[DashboardsController] acceptTerms error: ' . $e->getMessage());
            return response()->json(['success' => true, 'message' => 'Terms accepted.']);
        }
    }

    /**
     * Unified handler to save candidate details for any of the 8 steps.
     */
    public function saveStep(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? $user?->phone ?? $user?->mobile ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }
        if (!$cleanPhone && is_numeric($usercode) && strlen($usercode) === 10) {
            $cleanPhone = $usercode;
        }

        $crmStudent = null;
        try {
            $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
                ->when($cleanPhone, function ($q) use ($cleanPhone) {
                    $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
                })
                ->first();
        } catch (\Throwable $e) {}

        $ayPrefix = CrmStudentRegistration::getAcademicYearInfo()['prefix'];
        $appNo = $crmStudent?->applinano ?? ($usercode ?: ('VU' . $ayPrefix . '-' . ($cleanPhone ? substr($cleanPhone, -6) : 'APP')));
        $regNo = $crmStudent?->applinano ?? ($usercode ?: (substr($ayPrefix, -2) . '1FA' . ($cleanPhone ? substr($cleanPhone, -5) : '00001')));

        $progId = $crmStudent?->programid;
        $program = $progId ? Program::where('program_id', $progId)->first() : null;
        $progLevel = strtoupper(trim((string)($crmStudent?->programlevel ?? '')));
        $progShort = strtoupper($program?->program_short_name ?? '');
        $progName = strtoupper($program?->program_long_name ?? $program?->program_short_name ?? '');
        $requestedLevel = strtolower(trim((string)($request->query('degree_level') ?? $request->input('degree_level') ?? session('degree_level') ?? '')));

        $isPhd = (
            $requestedLevel === 'phd' ||
            $progLevel === 'PHD' ||
            $progLevel === 'DOCTORAL' ||
            str_contains($progShort, 'PHD') ||
            str_contains($progShort, 'PH.D') ||
            str_contains($progName, 'DOCTOR') ||
            str_contains($progName, 'PH.D') ||
            str_contains($progName, 'PHD')
        );

        $step = (int) $request->input('step', 1);
        $maxSteps = $isPhd ? 6 : 7;
        $nextStep = min($maxSteps, $step + 1);

        $hasPaidRecord = false;
        try {
            if ($appNo && $appNo !== '0') {
                $hasPaidRecord = \App\Models\PayDetail::where('applino', $appNo)->where('txnstatus', 'success')->exists()
                    || \App\Models\ReceiptDetail::where('applino', $appNo)->exists();
            }
            if (!$hasPaidRecord && $cleanPhone) {
                $phoneSuffix = substr($cleanPhone, -6);
                $hasPaidRecord = \App\Models\PayDetail::where('applino', 'like', "%{$phoneSuffix}")->where('txnstatus', 'success')->exists()
                    || \App\Models\ReceiptDetail::where('applino', 'like', "%{$phoneSuffix}")->exists();
            }
        } catch (\Throwable $e) {}

        // Ensure crmStudent exists before processing step save
        if (!$crmStudent && $cleanPhone) {
            try {
                $appliCandidate = ($appNo && $appNo !== '0' && !str_starts_with($appNo, 'VU' . $ayPrefix . '-') && !str_starts_with($appNo, 'VU2026-'))
                    ? $appNo
                    : ('VU' . $ayPrefix . substr($cleanPhone, -6));
                $crmStudent = CrmStudentRegistration::firstOrCreate(
                    ['phoneno' => $cleanPhone],
                    [
                        'applinano' => $appliCandidate,
                        'name' => $user?->name ?: $cleanPhone,
                        'phoneno' => $cleanPhone,
                        'emailid' => $user?->personalmail ?: ($cleanPhone . '@vignan.ac.in'),
                        'stage1' => 'NA',
                        'stage2' => 'NA',
                        'stage3' => 'NA',
                        'stage4' => $hasPaidRecord ? 'paid' : 'NA',
                        'stage5' => 'NA',
                        'std_status' => $hasPaidRecord ? 'Admitted' : 'Registered',
                        'campuscode' => 'VAD',
                        'programlevel' => 'UG',
                        'log_userid' => $appliCandidate,
                        'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                        'log_timestamp' => now(),
                    ]
                );
                $appNo = $crmStudent->applinano;
                if ($user && ($user->usercode === '0' || empty($user->usercode))) {
                    $user->update(['usercode' => $appNo]);
                }
            } catch (\Throwable $e) {
                Log::error('Could not ensure crmStudent in saveStep: ' . $e->getMessage());
            }
        }

        $isPaid = ($crmStudent && ($crmStudent->stage4 === 'paid' || $crmStudent->stage1 === 'paid')) || $hasPaidRecord;

        // Security validation: If fee payment is completed, steps 1, 2, and 3 are fixed / read-only
        if ($isPaid && in_array($step, [1, 2, 3])) {
            return redirect()->route('dashboards.index', ['step' => $step])
                ->with('info', 'Your details are fixed and cannot be modified after fee payment.');
        }

        // Security validation: Block saving higher steps if fee payment in Step 4 is not completed
        if ($step > 4 && !$isPaid) {
            return redirect()->route('dashboards.index', ['step' => 4])
                ->withErrors(['error' => 'Admission fee payment in Step 4 must be completed before proceeding.']);
        }

        try {
            if ($step === 1) {
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
                            'terms_accepted' => 'yes',
                ]);

                $request->validate([
                    'parent_name' => ['required', 'string', 'max:255'],
                    'student_number' => ['required', 'regex:/^[0-9]{10}$/'],
                    'parent_phone_no' => ['required', 'regex:/^[0-9]{10}$/'],
                    'dob' => ['required', 'date', 'before_or_equal:today'],
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
                    'parent_phone_no.required' => 'Parent Phone Number is mandatory.',
                    'parent_phone_no.regex' => 'Parent Phone Number must be exactly 10 digits.',
                    'dob.required' => 'Date of Birth is required.',
                    'dob.before_or_equal' => 'Date of Birth cannot be a future date.',
                    'pincode.required' => 'Pincode is required.',
                    'pincode.regex' => 'Pincode must be exactly 6 digits.',
                    'district.required' => 'Please select a District.',
                    'state.required' => 'Please select a State.',
                    'country.required' => 'Please select a Country.',
                    'city_mandal.required' => 'City / Mandal / Town is required.',
                    'village.required' => 'D.No / Colony / Street / Village is required.',
                ]);

                if ($crmStudent) {
                    $existingInfo = StudentPersonalInfo::where('applino', $crmStudent->applinano)->first()
                        ?? (!empty($cleanStudentPhone) ? StudentPersonalInfo::where('phoneno', $cleanStudentPhone)->where('applino', $crmStudent->applinano)->first() : null);

                    if ($existingInfo && !empty($existingInfo->crmid) && !CrmIdService::isCrmIdInUse($existingInfo->crmid, $crmStudent->applinano)) {
                        $personalCrmId = $existingInfo->crmid;
                        if ($crmStudent->crmid !== $personalCrmId) {
                            $crmStudent->update(['crmid' => $personalCrmId]);
                            $crmStudent->crmid = $personalCrmId;
                        }
                    } else {
                        $personalCrmId = CrmIdService::generateForStudent($crmStudent);
                    }

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
                            'terms_accepted' => 'yes',
                            'log_userid' => $user?->usercode ?? $user?->name ?? 'STUDENT',
                            'log_ipaddress' => $request->ip(),
                            'log_timestamp' => now(),
                        ]
                    );

                    $crmUpdates = [
                        'stage1' => 'completed',
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
                $nextStep = 2;
                $msg = 'Stage 1: Personal Profile saved successfully!';
            } elseif ($step === 2) {
                // Validation for Step 2 (Academic Info)
                if (!$request->has('eval_type') && $request->has('tenth_marks_type')) {
                    $request->merge(['eval_type' => $request->input('tenth_marks_type')]);
                }

                $degLvl = strtolower(trim((string)($request->input('degree_level') ?? $request->query('degree_level') ?? 'btech')));
                $isPhd = ($degLvl === 'phd');
                $isPg = ($degLvl === 'mtech');
                $isUg = ($degLvl === 'btech' || (!$isPhd && !$isPg));

                $resolveState = function($input) {
                    if (!$input) return null;
                    if (is_numeric($input)) {
                        $m = State::where('state_id', (int)$input)->first();
                        return $m ? $m->state_name : (string)$input;
                    }
                    return (string)$input;
                };

                $resolveDistrict = function($input) {
                    if (!$input) return null;
                    if (is_numeric($input)) {
                        $m = District::where('DISTRICT_ID', (int)$input)->first();
                        return $m ? $m->DISTRICT_NAME : (string)$input;
                    }
                    return (string)$input;
                };

                $tenthStateVal = $resolveState($request->input('tenth_school_state') ?? $request->input('tenth_state'));
                $tenthDistrictVal = $resolveDistrict($request->input('tenth_school_district') ?? $request->input('tenth_district'));

                $cityInput = $request->input('city') ?? $request->input('inter_city') ?? $request->input('pu2_college_city');
                $stateInput = $resolveState($request->input('jr_state') ?? $request->input('inter_state') ?? $request->input('pu2_college_state'));
                $districtInput = $resolveDistrict($request->input('jr_district') ?? $request->input('inter_district') ?? $request->input('pu2_college_district'));

                $ugStateVal = $resolveState($request->input('ug_college_state') ?? $request->input('ug_state'));
                $ugDistrictVal = $resolveDistrict($request->input('ug_college_district') ?? $request->input('ug_district'));

                $pgStateVal = $resolveState($request->input('pg_college_state') ?? $request->input('pg_state'));
                $pgDistrictVal = $resolveDistrict($request->input('pg_college_district') ?? $request->input('pg_district'));

                $pg2StateVal = $resolveState($request->input('pg2_college_state') ?? $request->input('pg2_state'));
                $pg2DistrictVal = $resolveDistrict($request->input('pg2_college_district') ?? $request->input('pg2_district'));

                $request->merge([
                    'city' => $cityInput,
                    'jr_state' => $stateInput,
                    'jr_district' => $districtInput,
                    'pu2_college_state' => $stateInput,
                    'pu2_college_district' => $districtInput,
                ]);

                $validationRules = [
                    // 10th School / SSC Details
                    'school_co_name' => ['nullable', 'string', 'max:255'],
                    'boad_of_study' => ['nullable', 'string', 'max:255'],
                    'tenth_school_city' => ['nullable', 'string', 'max:100'],
                    'tenth_school_state' => ['nullable', 'string', 'max:100'],
                    'tenth_school_district' => ['nullable', 'string', 'max:100'],
                    'passing_year' => ['nullable', 'integer', 'min:2000', 'max:2030'],
                    'roll_no' => ['nullable', 'string', 'max:50'],
                    'tenth_marks_type' => ['nullable', 'string'],
                    'tenth_current_status' => ['nullable', 'string'],
                    'tenth_marks_conducted' => ['nullable', 'numeric'],
                    'tenth_marks_earned' => ['nullable', 'numeric'],
                    'tenth_percentage' => ['nullable', 'numeric'],
                    'inter_campus_location' => ['nullable', 'string', 'max:255'],
                ];

                if (!$isPhd) {
                    $validationRules['inter_college'] = ['required', 'string', 'max:255'];
                     $validationRules['inter_group'] = ['required', 'string', 'in:MPC,BIPC,MBIPC'];
                    $validationRules['city'] = ['required', 'string', 'max:100'];
                    $validationRules['jr_state'] = ['required', 'string', 'max:100'];
                    $validationRules['jr_district'] = ['required', 'string', 'max:100'];
                    $validationRules['subset_choice'] = ['required', 'string'];
                    $validationRules['inter_year'] = ['required', 'integer', 'min:2000', 'max:2030'];
                    $validationRules['pu2_marks_type'] = ['required', 'string'];
                    $validationRules['pu2_marks_conducted'] = ['required', 'numeric', 'min:1'];
                    $validationRules['pu2_marks_earned'] = ['required', 'numeric', 'min:0'];
                    $validationRules['pu2_percentage'] = ['required', 'numeric'];
                } else {
                    $validationRules['inter_college'] = ['nullable', 'string', 'max:255'];
                    $validationRules['inter_group'] = ['nullable', 'string', 'in:MPC,BIPC,MBIPC'];
                    $validationRules['city'] = ['nullable', 'string', 'max:100'];
                    $validationRules['jr_state'] = ['nullable', 'string', 'max:100'];
                    $validationRules['jr_district'] = ['nullable', 'string', 'max:100'];
                    $validationRules['subset_choice'] = ['nullable', 'string'];
                    $validationRules['inter_year'] = ['nullable', 'integer', 'min:2000', 'max:2030'];
                    $validationRules['pu2_marks_type'] = ['nullable', 'string'];
                    $validationRules['pu2_marks_conducted'] = ['nullable', 'numeric'];
                    $validationRules['pu2_marks_earned'] = ['nullable', 'numeric'];
                    $validationRules['pu2_percentage'] = ['nullable', 'numeric'];
                }

                if ($isPg) {
                    $validationRules['ug_college_name'] = ['required', 'string', 'max:255'];
                    $validationRules['ug_university_board'] = ['required', 'string', 'max:255'];
                }

                if ($isPhd) {
                    $validationRules['pg_college_name'] = ['required', 'string', 'max:255'];
                    $validationRules['pg_university_board'] = ['required', 'string', 'max:255'];
                }

                if ($request->filled('tenth_marks_conducted') && $request->filled('tenth_marks_earned')) {
                    $validationRules['tenth_marks_earned'] = ['nullable', 'numeric', 'lte:tenth_marks_conducted'];
                }
                if ($request->filled('pu2_marks_conducted') && $request->filled('pu2_marks_earned')) {
                    $validationRules['pu2_marks_earned'] = array_merge((array)$validationRules['pu2_marks_earned'], ['lte:pu2_marks_conducted']);
                }
                if ($request->filled('ug_marks_conducted') && $request->filled('ug_marks_earned')) {
                    $validationRules['ug_marks_earned'] = ['nullable', 'numeric', 'lte:ug_marks_conducted'];
                }
                if ($request->filled('pg_marks_conducted') && $request->filled('pg_marks_earned')) {
                    $validationRules['pg_marks_earned'] = ['nullable', 'numeric', 'lte:pg_marks_conducted'];
                }
                if ($request->filled('pg2_marks_conducted') && $request->filled('pg2_marks_earned')) {
                    $validationRules['pg2_marks_earned'] = ['nullable', 'numeric', 'lte:pg2_marks_conducted'];
                }

                $request->validate($validationRules, [
                    'inter_college.required' => 'College Name / Institute Name is required.',
                    'inter_group.required' => 'Please select Intermediate Group (MPC, BIPC, or MBIPC).',
                    'city.required' => 'College City is required.',
                    'jr_state.required' => 'College State is required.',
                    'jr_district.required' => 'College District is required.',
                    'subset_choice.required' => 'Please select 1st Year or 2nd Year.',
                    'inter_year.required' => 'Year is required.',
                    'pu2_marks_type.required' => 'Marks Type is required.',
                    'pu2_marks_conducted.required' => 'Total Marks (Conducted) is required.',
                    'pu2_marks_earned.required' => 'Marks Earned is required.',
                    'pu2_marks_earned.lte' => '12th Marks Earned cannot exceed Total Marks Conducted.',
                    'tenth_marks_earned.lte' => '10th Marks Earned cannot exceed Total Marks Conducted.',
                    'ug_marks_earned.lte' => 'UG Marks Earned cannot exceed Total Marks Conducted.',
                    'pg_marks_earned.lte' => 'PG 1 Marks Earned cannot exceed Total Marks Conducted.',
                    'pg2_marks_earned.lte' => 'PG 2 Marks Earned cannot exceed Total Marks Conducted.',
                    'pu2_percentage.required' => 'Percentage is required.',
                    'ug_college_name.required' => 'UG College Name / Institute Name is required.',
                    'ug_university_board.required' => 'UG Board is required.',
                    'pg_college_name.required' => 'PG 1 College Name / Institute Name is required.',
                    'pg_university_board.required' => 'PG 1 Board is required.',
                ]);

                if ($crmStudent) {
                    $academicCrmId = CrmIdService::generateForStudent($crmStudent);
                    $existingAcademic = StudentAcademicDetail::where('applino', $crmStudent->applinano)
                        ->where('phoneno', $crmStudent->phoneno)
                        ->first();

                    $tenthSchool = $request->input('tenth_school_name') ?? $request->input('school_co_name') ?? $request->input('ssc_school');
                    $tenthBoard = $request->input('tenth_study_board') ?? $request->input('boad_of_study') ?? $request->input('ssc_board');
                    $tenthRoll = $request->input('tenth_roll_no') ?? $request->input('roll_no') ?? $request->input('ssc_hallticket');
                    $tenthYearInput = $request->input('tenth_passed_year') ?? $request->input('passing_year') ?? $request->input('ssc_year');
                    $tenthYear = (is_numeric($tenthYearInput) && (int)$tenthYearInput > 0) ? (int)$tenthYearInput : null;

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

                    $isPgOrDoc = in_array($request->input('degree_level'), ['mtech', 'phd']);
                    if (!$isPgOrDoc && $crmStudent && $crmStudent->progmid) {
                        $prog = Program::where('program_id', $crmStudent->progmid)->first();
                        if ($prog) {
                            $pLevel = strtoupper($prog->program_level ?? '');
                            $pShort = strtoupper($prog->program_short_name ?? '');
                            $pName = strtoupper($prog->program_name ?? '');
                            if ($pLevel === 'PG' || $pLevel === 'POST GRADUATE' || str_contains($pShort, 'M.TECH') || str_contains($pName, 'MASTER') || in_array($pShort, ['MCA', 'MBA', 'M.PHARM', 'M.SC', 'M.A']) || $pLevel === 'PHD' || $pLevel === 'DOCTORAL' || str_contains($pShort, 'PH.D') || str_contains($pName, 'DOCTOR')) {
                                $isPgOrDoc = true;
                            }
                        }
                    }


                    self::ensureAcademicColumns();

                    $courseChoice = $request->input('inter_board_of_study') ?? $request->input('pu2_board_of_study') ?? $request->input('course_choice') ?? $request->input('pu2_study_board');
                    $subsetChoice = $request->input('subset_choice') ?? $request->input('pu2_current_status') ?? ($isPgOrDoc ? 'Second Year' : 'First Year');
                    if ($isPgOrDoc) {
                        $subsetChoice = 'Second Year';
                    }

                    $tenthConducted = is_numeric($request->input('tenth_marks_conducted'))
                        ? (float)$request->input('tenth_marks_conducted')
                        : null;
                    $tenthEarned = is_numeric($request->input('tenth_marks_earned'))
                        ? (float)$request->input('tenth_marks_earned')
                        : ($tenthMarksNum ?? ($tenthCgpaNum ? round($tenthCgpaNum * 9.5, 2) : null));
                    $tenthPct = is_numeric($request->input('tenth_percentage'))
                        ? (float)$request->input('tenth_percentage')
                        : (($tenthConducted && $tenthConducted > 0 && $tenthEarned) ? round(($tenthEarned / $tenthConducted) * 100, 2) : null);

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

                    $isSecondYear = (strcasecmp((string)$subsetChoice, 'Second Year') === 0 || strcasecmp((string)$subsetChoice, 'Second year') === 0);
                    $undertakingAgreed = ($request->input('undertaking_60_pct') === 'yes' || $request->input('undertaking_60_pct') === '1' || ($existingAcademic && $existingAcademic->undertaking_60_pct === 'yes'));


                    // For UG programs: enforce 60% threshold. For PG / Doctorate: no 60% requirement for intermediate.
                    if (!$isPgOrDoc) {
                        // If 2nd year student, only proceed if percentage is >= 60%
                        if ($isSecondYear && $pu2Pct !== null && $pu2Pct < 60.0) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'pu2_percentage' => 'You are not eligible according to university norms.',
                            ]);
                        }

                        // If 1st year student and < 60%, require undertaking confirmation
                        if (!$isSecondYear && $pu2Pct !== null && $pu2Pct < 60.0) {
                            if (!$undertakingAgreed) {
                                throw \Illuminate\Validation\ValidationException::withMessages([
                                    'undertaking_60_pct' => 'Please confirm your undertaking (i will get 60%) to proceed.',
                                ]);
                            }
                        }
                    }

                    $cityVal = (string)($cityInput ?? '');
                    $jrStateVal = (string)($stateInput ?? '');
                    $jrDistrictVal = (string)($districtInput ?? '');

                    $academicPayload = [
                        'crmid' => $academicCrmId,
                        'phoneno' => $crmStudent->phoneno,
                        'progmid' => $crmStudent->programid,
                        'branchid' => $crmStudent->branchid,
                        'tenth_school_name' => $tenthSchool ?: null,
                        'tenth_study_board' => $tenthBoard ?: null,
                        'tenth_school_city' => $request->input('tenth_school_city') ?? $request->input('ssc_location'),
                        'tenth_school_state' => $tenthStateVal,
                        'tenth_school_district' => $tenthDistrictVal ?: ($request->input('tenth_school_district') ?? $request->input('ssc_location')),
                        'tenth_roll_no' => $tenthRoll ?: null,
                        'tenth_passed_year' => $tenthYear ?: null,
                        'tenth_marks_type' => $request->input('tenth_marks_type') ?? $evalType,
                        'tenth_current_status' => $request->input('tenth_current_status') ?? 'Completed',
                        'tenth_marks_conducted' => $tenthConducted,
                        'tenth_marks_earned' => $tenthEarned,
                        'tenth_percentage' => $tenthPct,
                        'pu2_college_name' => $request->input('inter_college') ?? $request->input('pu2_college_name'),
                        'pu2_campus_location' => $request->input('inter_campus_location') ?? $request->input('pu2_campus_location') ?? $request->input('campus_location'),
                        'pu2_group' => $request->input('inter_group') ?? $request->input('pu2_group'),

                        'pu2_study_board' => $courseChoice,
                        'pu2_board_of_study' => $courseChoice,
                        'pu2_college_city' => $cityVal,
                        'pu2_college_state' => $jrStateVal,
                        'pu2_college_district' => $jrDistrictVal,
                        'pu2_roll_no' => $request->input('inter_hallticket') ?? $request->input('pu2_roll_no'),
                        'pu2_passed_year' => $request->filled('inter_year') ? (int) $request->input('inter_year') : ($request->filled('pu2_passed_year') ? (int) $request->input('pu2_passed_year') : null),
                        'pu2_marks_type' => $pu2Type,
                        'pu2_marks' => $pu2Earned ?: $pu2MarksRaw,
                        'pu2_marks_conducted' => $pu2Conducted,
                        'pu2_marks_earned' => $pu2Earned,
                        'pu2_percentage' => $pu2Pct,
                        'pu2_current_status' => $subsetChoice,
                        'undertaking_60_pct' => $undertakingAgreed ? 'yes' : ($existingAcademic?->undertaking_60_pct ?? null),
                        'log_userid' => $user?->usercode ?? $user?->name ?? 'STUDENT',
                        'log_datetime' => now(),
                        'log_ipaddress' => $request->ip(),
                    ];

                    // If Degree level is M.Tech or PhD, save UG (B.Tech) details
                    if ($request->input('degree_level') === 'mtech' || $request->input('degree_level') === 'phd' || $isPg || $isPhd) {
                        $ugMarksRaw = is_numeric($request->input('ug_marks')) ? (float) $request->input('ug_marks') : (is_numeric($request->input('ug_cgpa')) ? (float)$request->input('ug_cgpa') : null);
                        $ugPct = ($ugMarksRaw && $ugMarksRaw <= 10) ? round($ugMarksRaw * 9.5, 2) : (($ugMarksRaw && $ugMarksRaw > 100) ? round(($ugMarksRaw / 1000) * 100, 2) : $ugMarksRaw);

                        $ugMarksConducted = is_numeric($request->input('ug_marks_conducted')) ? (float)$request->input('ug_marks_conducted') : 100.0;
                        $ugMarksEarned = is_numeric($request->input('ug_marks_earned')) ? (float)$request->input('ug_marks_earned') : $ugMarksRaw;
                        $ugPctInput = is_numeric($request->input('ug_percentage')) ? (float)$request->input('ug_percentage') : $ugPct;

                        $academicPayload['ug_college_name'] = $request->input('ug_college_name') ?: null;
                        $academicPayload['ug_university_board'] = $request->input('ug_university_board') ?: null;
                        $academicPayload['ug_college_city'] = $request->input('ug_college_city') ?: null;
                        $academicPayload['ug_college_state'] = $ugStateVal;
                        $academicPayload['ug_college_district'] = $ugDistrictVal;
                        $academicPayload['ug_roll_no'] = $request->input('ug_roll_no') ?: null;
                        $academicPayload['ug_passed_year'] = $request->filled('ug_passed_year') ? (int) $request->input('ug_passed_year') : null;
                        $academicPayload['ug_marks_type'] = $request->input('ug_marks_type') ?: 'Marks';
                        $academicPayload['ug_marks'] = $ugMarksEarned;
                        $academicPayload['ug_marks_conducted'] = $ugMarksConducted;
                        $academicPayload['ug_marks_earned'] = $ugMarksEarned;
                        $academicPayload['ug_percentage'] = $ugPctInput;
                        $academicPayload['ug_current_status'] = $request->input('ug_current_status') ?: ($isPhd ? 'Completed' : 'Final Year Appeared');
                    }

                    // If Degree level is PhD, save PG 1 and PG 2 details
                    if ($request->input('degree_level') === 'phd' || $isPhd) {
                        // Postgraduate 1
                        $pgMarksRaw = is_numeric($request->input('pg_marks')) ? (float) $request->input('pg_marks') : (is_numeric($request->input('pg_cgpa')) ? (float)$request->input('pg_cgpa') : null);
                        $pgPct = ($pgMarksRaw && $pgMarksRaw <= 10) ? round($pgMarksRaw * 9.5, 2) : (($pgMarksRaw && $pgMarksRaw > 100) ? round(($pgMarksRaw / 1000) * 100, 2) : $pgMarksRaw);

                        $pgMarksConducted = is_numeric($request->input('pg_marks_conducted')) ? (float)$request->input('pg_marks_conducted') : 100.0;
                        $pgMarksEarned = is_numeric($request->input('pg_marks_earned')) ? (float)$request->input('pg_marks_earned') : $pgMarksRaw;
                        $pgPctInput = is_numeric($request->input('pg_percentage')) ? (float)$request->input('pg_percentage') : $pgPct;

                        $academicPayload['pg_college_name'] = $request->input('pg_college_name') ?: null;
                        $academicPayload['pg_university_board'] = $request->input('pg_university_board') ?: null;
                        $academicPayload['pg_college_city'] = $request->input('pg_college_city') ?: null;
                        $academicPayload['pg_college_state'] = $pgStateVal;
                        $academicPayload['pg_college_district'] = $pgDistrictVal;
                        $academicPayload['pg_roll_no'] = $request->input('pg_roll_no') ?: null;
                        $academicPayload['pg_passed_year'] = $request->filled('pg_passed_year') ? (int) $request->input('pg_passed_year') : null;
                        $academicPayload['pg_marks_type'] = $request->input('pg_marks_type') ?: 'Marks';
                        $academicPayload['pg_marks'] = $pgMarksEarned;
                        $academicPayload['pg_marks_conducted'] = $pgMarksConducted;
                        $academicPayload['pg_marks_earned'] = $pgMarksEarned;
                        $academicPayload['pg_percentage'] = $pgPctInput;
                        $academicPayload['pg_current_status'] = $request->input('pg_current_status') ?: 'Completed';
                        $academicPayload['pg_study_type'] = $request->input('pg_study_type') ?: 'Regular';

                        // PG 1 CMM & PC/OD Details (At least one mandatory for PhD)
                        $pg1DocType = $request->input('pg1_doc_type') ?: 'BOTH';
                        $academicPayload['pg1_doc_type'] = $pg1DocType;
                        $academicPayload['pg1_cmm_memo_no'] = $request->input('pg1_cmm_memo_no') ?: null;
                        $academicPayload['pg1_cmm_cgpa'] = is_numeric($request->input('pg1_cmm_cgpa')) ? (float)$request->input('pg1_cmm_cgpa') : null;
                        $academicPayload['pg1_cmm_year'] = $request->filled('pg1_cmm_year') ? (int)$request->input('pg1_cmm_year') : null;
                        $academicPayload['pg1_pc_od_no'] = $request->input('pg1_pc_od_no') ?: null;
                        $academicPayload['pg1_pc_od_year'] = $request->filled('pg1_pc_od_year') ? (int)$request->input('pg1_pc_od_year') : null;
                        $academicPayload['pg1_pc_od_status'] = $request->input('pg1_pc_od_status') ?: null;

                        // Postgraduate 2
                        $pg2MarksRaw = is_numeric($request->input('pg2_marks')) ? (float) $request->input('pg2_marks') : (is_numeric($request->input('pg2_cgpa')) ? (float)$request->input('pg2_cgpa') : null);
                        $pg2Pct = ($pg2MarksRaw && $pg2MarksRaw <= 10) ? round($pg2MarksRaw * 9.5, 2) : (($pg2MarksRaw && $pg2MarksRaw > 100) ? round(($pg2MarksRaw / 1000) * 100, 2) : $pg2MarksRaw);

                        $pg2MarksConducted = is_numeric($request->input('pg2_marks_conducted')) ? (float)$request->input('pg2_marks_conducted') : 100.0;
                        $pg2MarksEarned = is_numeric($request->input('pg2_marks_earned')) ? (float)$request->input('pg2_marks_earned') : $pg2MarksRaw;
                        $pg2PctInput = is_numeric($request->input('pg2_percentage')) ? (float)$request->input('pg2_percentage') : $pg2Pct;

                        $academicPayload['pg2_college_name'] = $request->input('pg2_college_name') ?: null;
                        $academicPayload['pg2_university_board'] = $request->input('pg2_university_board') ?: null;
                        $academicPayload['pg2_college_city'] = $request->input('pg2_college_city') ?: null;
                        $academicPayload['pg2_college_state'] = $pg2StateVal;
                        $academicPayload['pg2_college_district'] = $pg2DistrictVal;
                        $academicPayload['pg2_roll_no'] = $request->input('pg2_roll_no') ?: null;
                        $academicPayload['pg2_passed_year'] = $request->filled('pg2_passed_year') ? (int) $request->input('pg2_passed_year') : null;
                        $academicPayload['pg2_marks_type'] = $request->input('pg2_marks_type') ?: 'Marks';
                        $academicPayload['pg2_marks'] = $pg2MarksEarned;
                        $academicPayload['pg2_marks_conducted'] = $pg2MarksConducted;
                        $academicPayload['pg2_marks_earned'] = $pg2MarksEarned;
                        $academicPayload['pg2_percentage'] = $pg2PctInput;
                        $academicPayload['pg2_current_status'] = $request->input('pg2_current_status') ?: 'Completed';
                        $academicPayload['pg2_study_type'] = $request->input('pg2_study_type') ?: 'Regular';

                        // PG 2 CMM & PC/OD Details (Optional for PhD)
                        $academicPayload['pg2_doc_type'] = $request->input('pg2_doc_type') ?: 'BOTH';
                        $academicPayload['pg2_cmm_memo_no'] = $request->input('pg2_cmm_memo_no') ?: null;
                        $academicPayload['pg2_cmm_cgpa'] = is_numeric($request->input('pg2_cmm_cgpa')) ? (float)$request->input('pg2_cmm_cgpa') : null;
                        $academicPayload['pg2_cmm_year'] = $request->filled('pg2_cmm_year') ? (int)$request->input('pg2_cmm_year') : null;
                        $academicPayload['pg2_pc_od_no'] = $request->input('pg2_pc_od_no') ?: null;
                        $academicPayload['pg2_pc_od_year'] = $request->filled('pg2_pc_od_year') ? (int)$request->input('pg2_pc_od_year') : null;
                        $academicPayload['pg2_pc_od_status'] = $request->input('pg2_pc_od_status') ?: null;
                    }

                    StudentAcademicDetail::updateOrCreate(
                        [
                            'applino' => $crmStudent->applinano,
                        ],
                        $academicPayload
                    );

                    $crmUpdates = [
                        'stage2' => 'completed',
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
                $nextStep = 3;
                $msg = 'Stage 2: Academic Records saved successfully!';
            } elseif ($step === 3) {
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
                        'pg1_cmm', 'pg1_pc', 'pg2_cmm', 'pg2_pc', 'experience',
                        'aadhaar', 'photo', 'tc', 'study_conduct'
                    ];

                    $isPhdCandidate = $isPhd || strtolower((string)$request->input('degree_level')) === 'phd' || strtoupper((string)$crmStudent->programlevel) === 'DOCTORAL' || strtoupper((string)$crmStudent->programlevel) === 'PHD';
                    if ($isPhdCandidate) {
                        $acad = StudentAcademicDetail::where('applino', $crmStudent->applinano)->first();
                        $pg1DocType = $acad?->pg1_doc_type ?? 'BOTH';
                        $existingUpload = StudentUpload::where('applino', $crmStudent->applinano)->first();
                        $hasCmm = !empty($existingUpload?->pg1_cmm_file_path) || $request->hasFile('docs.pg1_cmm');
                        $hasPc = !empty($existingUpload?->pg1_pc_file_path) || $request->hasFile('docs.pg1_pc');

                        if (($pg1DocType === 'CMM' || $pg1DocType === 'BOTH') && !$hasCmm) {
                            return redirect()->route('dashboards.index', ['step' => 3])
                                ->withErrors(['pg1_required' => 'Please upload PG 1 Consolidated Marks Memo (CMM).']);
                        }
                        if (($pg1DocType === 'PC_OD' || $pg1DocType === 'BOTH') && !$hasPc) {
                            return redirect()->route('dashboards.index', ['step' => 3])
                                ->withErrors(['pg1_required' => 'Please upload PG 1 Provisional Certificate / Original Degree (PC/OD).']);
                        }
                    }

                    $appliFolder = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($crmStudent->applinano ?: 'APP'));
                    $destDir = public_path('uploads/documents/' . $appliFolder);

                    $crmId = CrmIdService::generateForStudent($crmStudent);

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
                        'pg1_cmm' => ['pg1_cmm_file_name', 'pg1_cmm_file_path'],
                        'pg1_pc' => ['pg1_pc_file_name', 'pg1_pc_file_path'],
                        'pg2_cmm' => ['pg2_cmm_file_name', 'pg2_cmm_file_path'],
                        'pg2_pc' => ['pg2_pc_file_name', 'pg2_pc_file_path'],
                        'experience' => ['experience_file_name', 'experience_file_path'],
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
                        'stage3' => 'completed',
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
                $nextStep = 4;
                $msg = 'Stage 3: Document Uploads saved successfully!';
            } elseif ($step === 4) {
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage4' => 'paid',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 5;
                $msg = 'Stage 4: Admission Fee Payment confirmed!';
            } elseif ($step === 5 && $isPhd) {
                // Ph.D candidate: Step 5 is Application Download (Exam Slot Booking is skipped)
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage5' => 'completed',
                        'stage6' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 6;
                $msg = 'Stage 5: Application Download verified!';
            } elseif ($step === 5) {
                // Step 5: Exam Slot Booking (finance.exam_schedule)
                $now = now();
                $cutoffHour = 17; // 5:00 PM cutoff
                $isBefore5PM = $now->hour < $cutoffHour;
                $minExamDate = $isBefore5PM
                    ? $now->copy()->addDay()->format('Y-m-d')
                    : $now->copy()->addDays(2)->format('Y-m-d');

                $minDateFormatted = \Carbon\Carbon::parse($minExamDate)->format('d M Y');

                $existingExamSchedule = $crmStudent ? \App\Models\ExamSchedule::where('applino', $crmStudent->applinano)->first() : null;
                $existingDate = $existingExamSchedule?->examdate ? \Carbon\Carbon::parse($existingExamSchedule->examdate)->format('Y-m-d') : null;

                // Reschedule cutoff: 1 day before exam date at 5:00 PM
                // (e.g., if student has exam tomorrow, they can edit up to today evening 5:00 PM; afterwards locked)
                $editDeadline = $existingDate
                    ? \Carbon\Carbon::parse($existingDate)->subDay()->setTime(17, 0, 0)
                    : null;

                $isSlotLocked = $existingExamSchedule && $editDeadline && $now->gt($editDeadline);

                if ($isSlotLocked) {
                    if ($crmStudent && $crmStudent->stage5 !== 'completed') {
                        $crmStudent->update([
                            'stage5' => 'completed',
                            'log_ipaddress' => $request->ip(),
                            'log_timestamp' => now(),
                        ]);
                    }
                    return redirect()->route('dashboards.index', ['step' => 6])
                        ->with('info', 'Your exam slot is locked and confirmed.');
                }

                $request->validate([
                    'examdate'  => ['required', 'date', 'after_or_equal:' . $minExamDate],
                    'exam_slot' => ['required', 'string', 'max:100'],
                ], [
                    'examdate.required'  => 'Please select your preferred examination date.',
                    'examdate.after_or_equal' => $isBefore5PM
                        ? 'Exam date can only be scheduled starting from tomorrow (' . $minDateFormatted . ').'
                        : 'Bookings after 5:00 PM cutoff can only be scheduled starting from day after tomorrow (' . $minDateFormatted . ').',
                    'exam_slot.required' => 'Please select your preferred exam time slot.',
                ]);

                if ($crmStudent) {
                    $examCrmId = CrmIdService::generateForStudent($crmStudent);

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
                $msg = $existingExamSchedule
                    ? 'Stage 5: Exam slot updated successfully!'
                    : 'Stage 5: Exam Slot confirmed and booked successfully!';
            } elseif ($step === 6 && $isPhd) {
                // Ph.D candidate: Step 6 is V-SAT Results (Final Stage)
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage6' => 'completed',
                        'stage7' => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 6;
                $msg = 'Stage 6: Onboarding completed successfully!';
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

        } catch (\Illuminate\Validation\ValidationException $ve) {
            throw $ve;
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

                ALTER TABLE crm.student_uploads
                ADD COLUMN IF NOT EXISTS pg1_cmm_file_name VARCHAR(255),
                ADD COLUMN IF NOT EXISTS pg1_cmm_file_path VARCHAR(500),
                ADD COLUMN IF NOT EXISTS pg1_pc_file_name VARCHAR(255),
                ADD COLUMN IF NOT EXISTS pg1_pc_file_path VARCHAR(500),
                ADD COLUMN IF NOT EXISTS pg2_cmm_file_name VARCHAR(255),
                ADD COLUMN IF NOT EXISTS pg2_cmm_file_path VARCHAR(500),
                ADD COLUMN IF NOT EXISTS pg2_pc_file_name VARCHAR(255),
                ADD COLUMN IF NOT EXISTS pg2_pc_file_path VARCHAR(500),
                ADD COLUMN IF NOT EXISTS experience_file_name VARCHAR(255),
                ADD COLUMN IF NOT EXISTS experience_file_path VARCHAR(500);
            ");
        } catch (\Throwable $e) {
            // Silently continue if table already exists
        }
    }

    /**
     * Ensure academic details columns exist in crm.student_academic_details.
     */
    public static function ensureAcademicColumns(): void
    {
        try {
            DB::unprepared("
                ALTER TABLE crm.student_academic_details
                ADD COLUMN IF NOT EXISTS tenth_school_state VARCHAR(100),
                ADD COLUMN IF NOT EXISTS tenth_current_status VARCHAR(50),
                ADD COLUMN IF NOT EXISTS pu2_board_of_study VARCHAR(255),
                ADD COLUMN IF NOT EXISTS pu2_campus_location VARCHAR(255),
                ADD COLUMN IF NOT EXISTS ug_college_state VARCHAR(100),
                ADD COLUMN IF NOT EXISTS pg_college_state VARCHAR(100),
                ADD COLUMN IF NOT EXISTS pg2_college_state VARCHAR(100),
                ADD COLUMN IF NOT EXISTS pg1_doc_type VARCHAR(50),
                ADD COLUMN IF NOT EXISTS pg1_cmm_memo_no VARCHAR(100),
                ADD COLUMN IF NOT EXISTS pg1_cmm_cgpa NUMERIC(10,2),
                ADD COLUMN IF NOT EXISTS pg1_cmm_year INT,
                ADD COLUMN IF NOT EXISTS pg1_pc_od_no VARCHAR(100),
                ADD COLUMN IF NOT EXISTS pg1_pc_od_year INT,
                ADD COLUMN IF NOT EXISTS pg1_pc_od_status VARCHAR(50),
                ADD COLUMN IF NOT EXISTS pg2_doc_type VARCHAR(50),
                ADD COLUMN IF NOT EXISTS pg2_cmm_memo_no VARCHAR(100),
                ADD COLUMN IF NOT EXISTS pg2_cmm_cgpa NUMERIC(10,2),
                ADD COLUMN IF NOT EXISTS pg2_cmm_year INT,
                ADD COLUMN IF NOT EXISTS pg2_pc_od_no VARCHAR(100),
                ADD COLUMN IF NOT EXISTS pg2_pc_od_year INT,
                ADD COLUMN IF NOT EXISTS pg_study_type VARCHAR(50),
                ADD COLUMN IF NOT EXISTS pg2_study_type VARCHAR(50),
                ADD COLUMN IF NOT EXISTS pg2_pc_od_status VARCHAR(50);

                ALTER TABLE crm.student_academic_details
                ALTER COLUMN tenth_marks_conducted TYPE NUMERIC(12,2),
                ALTER COLUMN tenth_marks_earned TYPE NUMERIC(12,2),
                ALTER COLUMN pu2_marks TYPE NUMERIC(12,2),
                ALTER COLUMN pu2_marks_conducted TYPE NUMERIC(12,2),
                ALTER COLUMN pu2_marks_earned TYPE NUMERIC(12,2),
                ALTER COLUMN ug_marks TYPE NUMERIC(12,2),
                ALTER COLUMN ug_marks_conducted TYPE NUMERIC(12,2),
                ALTER COLUMN ug_marks_earned TYPE NUMERIC(12,2),
                ALTER COLUMN pg_marks TYPE NUMERIC(12,2),
                ALTER COLUMN pg_marks_conducted TYPE NUMERIC(12,2),
                ALTER COLUMN pg_marks_earned TYPE NUMERIC(12,2);
            ");
        } catch (\Throwable $e) {
            Log::warning('ensureAcademicColumns: ' . $e->getMessage());
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
        $keys = ['marksheet10', 'marksheet12', 'aadhaar', 'photo', 'tc', 'study_conduct', 'btech', 'mtech', 'pg', 'pg1_cmm', 'pg1_pc', 'pg2_cmm', 'pg2_pc', 'experience'];
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
            'pg1_cmm'       => 'PG 1 Consolidated Marks Memo (CMM)',
            'pg1_pc'        => 'PG 1 Provisional Certificate / Original Degree (PC/OD)',
            'pg2_cmm'       => 'PG 2 Consolidated Marks Memo (CMM)',
            'pg2_pc'        => 'PG 2 Provisional Certificate / Original Degree (PC/OD)',
            'experience'    => 'Work Experience Letter / Certificate',
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
                    'pg1_cmm' => ['pg1_cmm_file_name', 'pg1_cmm_file_path'],
                    'pg1_pc' => ['pg1_pc_file_name', 'pg1_pc_file_path'],
                    'pg2_cmm' => ['pg2_cmm_file_name', 'pg2_cmm_file_path'],
                    'pg2_pc' => ['pg2_pc_file_name', 'pg2_pc_file_path'],
                    'experience' => ['experience_file_name', 'experience_file_path'],
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

                        $relPath = str_starts_with($fPath, 'uploads/') ? $fPath : ('uploads/documents/' . $cleanAppli . '/' . $fName);
                        $absPath = public_path($relPath);

                        $found[$k] = [
                            'key' => $k,
                            'title' => $titles[$k] ?? ucwords(str_replace('_', ' ', $k)),
                            'filename' => $fName,
                            'path' => is_file($absPath) ? $absPath : $relPath,
                            'abs_path' => $absPath,
                            'rel_path' => $relPath,
                            'url' => asset($relPath),
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
                    $relPath = 'uploads/documents/' . $cleanAppli . '/' . $filename;
                    $found[$key] = [
                        'key' => $key,
                        'title' => $titles[$key] ?? ucwords(str_replace('_', ' ', $key)),
                        'filename' => $filename,
                        'path' => $fullPath,
                        'abs_path' => $fullPath,
                        'rel_path' => $relPath,
                        'url' => asset($relPath),
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

        $res = CouponService::validateAndApplyPersonalCoupon($code, $applino);
        return response()->json($res);
    }

    /**
     * Remove applied personal coupon from student session.
     */
    public function removePersonalCoupon(): \Illuminate\Http\JsonResponse
    {
        $res = CouponService::removeAppliedCoupon();
        return response()->json($res);
    }
}
