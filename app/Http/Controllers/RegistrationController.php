<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Campus;
use App\Models\Country;
use App\Models\CrmStudentRegistration;
use App\Models\Lead;
use App\Models\Program;
use App\Models\State;
use App\Models\StudentRegistration;
use App\Models\User;
use App\Services\SmsService;
use App\Services\WhatsAppService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Show the Official Vignan Admission Registration Form.
     */
    public function show(Request $request): View
    {
        $prefilledPhone = $request->query('phone') ?? session('verified_phone', '9912996788');
        if ($prefilledPhone) {
            $prefilledPhone = preg_replace('/\D/', '', $prefilledPhone);
            if (strlen($prefilledPhone) > 10) {
                $prefilledPhone = substr($prefilledPhone, -10);
            }
        }

        $campuses = Campus::where('is_active', true)
            ->select('campus_id', 'campus_code', 'campus_short_name', 'campus_long_name')
            ->orderBy('campus_id')
            ->get();

        $countries = Country::select('country_id', 'country_code', 'country_name', 'country_phonecode')
            ->orderBy('country_id')
            ->get();

        $states = State::select('state_id', 'state_code', 'state_name', 'country_id')
            ->orderBy('state_name')
            ->get();

        $programs = Program::select('program_id', 'program_level', 'program_short_name', 'program_long_name')
            ->orderBy('program_level')
            ->orderBy('program_id')
            ->get();

        $branches = Branch::select('branch_id', 'program_id', 'branch_short_name', 'branch_long_name')
            ->orderBy('branch_long_name')
            ->get();

        return view('auth.register', compact('campuses', 'countries', 'states', 'programs', 'branches', 'prefilledPhone'));
    }

    /**
     * Store new student registration into crm.student_registration & admissions.
     */
    public function store(
        Request $request,
        WhatsAppService $whatsAppService,
        SmsService $smsService
    ): RedirectResponse {
        $request->validate([
            'applicant_name' => ['nullable', 'string', 'max:150'],
            'name'           => ['nullable', 'string', 'max:150'],
            'email'          => ['nullable', 'email', 'max:150'],
            'mobile'         => ['required', 'string'],
            'campus_id'      => ['required'],
            'country_id'     => ['nullable', 'integer'],
            'state_id'       => ['nullable'],
            'program_level'  => ['required', 'string', 'in:UG,PG,Doctoral,PhD'],
            'progid'         => ['required', 'integer'],
            'branch_id'      => ['nullable', 'integer'],
            'ableto'         => ['nullable'],
        ]);

        $applicantName = trim($request->input('applicant_name') ?: $request->input('name') ?: 'Applicant');
        $cleanMobile = preg_replace('/\D/', '', $request->input('mobile') ?: $request->input('phone') ?: '');
        if (strlen($cleanMobile) > 10) {
            $cleanMobile = substr($cleanMobile, -10);
        }
        $email = trim($request->input('email') ?: ($cleanMobile . '@student.vignan.ac.in'));
        $defaultPassword = 'password';

        // Campus info
        $campus = Campus::where('campus_id', $request->campus_id)->first();
        $campusCode = $campus ? $campus->campus_code : 'VDM';
        $campusName = $campus ? $campus->campus_short_name : 'Vignan Main Campus (Vadlamudi)';

        $countryId = (int) $request->input('country_id', 1);
        $stateId = (int) $request->input('state_id', 1);

        $progLevel = $request->input('program_level', 'UG');
        $dbProgLevel = ($progLevel === 'Doctoral') ? 'PhD' : $progLevel;
        $progId = (int) $request->progid;
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $ableTo = $request->boolean('ableto', true);

        // Program and branch names for notifications
        $programName = Program::where('program_id', $progId)
            ->value('program_long_name') ?? 'Bachelor / Master Program';

        $branchName = 'General Core';
        if ($branchId) {
            $branchName = Branch::where('branch_id', $branchId)
                ->value('branch_long_name') ?? 'General Core';
        }

        // 1. Insert into crm.student_registration via Eloquent
        $appliNano = null;
        try {
            $crmRecord = CrmStudentRegistration::create([
                'name'         => $applicantName,
                'phoneno'      => $cleanMobile,
                'emailid'      => $email,
                'campuscode'   => $campusCode,
                'countryid'    => $countryId,
                'stateid'      => $stateId,
                'programlevel' => $dbProgLevel,
                'programid'    => $progId,
                'branchid'     => $branchId,
                'ableto'       => $ableTo,
                'std_status'   => 'Applied',
                'stage1'       => 'not_paid',
                'stage2'       => 'NA',
                'stage3'       => 'NA',
                'stage4'       => 'NA',
                'stage5'       => 'NA',
                'stage6'       => 'NA',
                'stage7'       => 'NA',
                'stage8'       => 'NA',
                'log_userid'   => 'SELF_REGISTER',
                'log_ipaddress'=> $request->ip(),
                'log_timestamp'=> now(),
            ]);

            if ($crmRecord && !empty($crmRecord->applinano)) {
                $appliNano = $crmRecord->applinano;
            }
        } catch (Exception $e) {
            Log::error('[RegistrationController] CrmStudentRegistration error: ' . $e->getMessage());
        }

        if (!$appliNano) {
            $appliNano = '20' . date('ymd') . mt_rand(100, 999);
        }

        // 2. Insert into admissions.student_registrations via Eloquent
        $programCode = Program::where('program_id', $progId)
            ->value('program_seprt_code') ?? 'CK';
        $regNo = '261' . $programCode . mt_rand(10000, 99999);
        $enrollmentNo = 'Jan-26-' . Str::random(10);

        try {
            StudentRegistration::updateOrCreate(
                ['application_no' => $appliNano],
                [
                    'enrollment_no'  => $enrollmentNo,
                    'register_no'    => $regNo,
                    'student_name'   => $applicantName,
                    'personal_email' => $email,
                    'ofc_mail'       => $email,
                    'mobile'         => $cleanMobile,
                    'regulation'     => 'R25',
                    'progid'         => $progId,
                    'electiveid'     => $branchId ?: 1,
                    'admyear'        => 2026,
                    'adm_batch_code' => 'JANUARY_2026',
                    'std_type'       => 'Regular',
                    'std_status'     => 'Applied',
                    'lock_status'    => 0,
                    'log_userid'     => 'SELF_REGISTER',
                    'log_ipaddress'  => $request->ip(),
                    'log_timestamp'  => now(),
                ]
            );
        } catch (Exception $e) {
            Log::warning('[RegistrationController] admissions notice: ' . $e->getMessage());
        }

        // 3. Create or update user account in master.login
        $user = User::updateOrCreate(
            ['usercode' => $appliNano],
            [
                'phnno'          => $cleanMobile,
                'personalmail'   => $email,
                'officialmail'   => $email,
                'user_type'      => 1, // Student Candidate
                'role_id'        => 5, // Student role
                'campus_id'      => (string) $request->campus_id,
                'is_active'      => true,
                'pwd'            => bcrypt($defaultPassword),
                'log_ipaddress'  => $request->ip(),
                'log_timestamp'  => now(),
            ]
        );

        // 4. Dispatch WhatsApp & SMS (if configured)
        try {
            $whatsAppService->sendAdmissionInfo(
                phone: $cleanMobile,
                candidateName: $applicantName,
                applicationId: $appliNano,
                programName: $programName,
                contactInfo: 'admissions@vignan.ac.in / 0863-2344700',
                user: $applicantName,
                registerNo: $appliNano,
                ip: $request->ip()
            );
        } catch (Exception $e) {
            Log::info('[RegistrationController] WhatsApp notice: ' . $e->getMessage());
        }

        try {
            $smsService->sendAdmissionSms(
                phone: $cleanMobile,
                name: $applicantName,
                program: $programName,
                branch: $branchName,
                campus: $campusName,
                applicationNo: $appliNano
            );
        } catch (Exception $e) {
            Log::info('[RegistrationController] SMS notice: ' . $e->getMessage());
        }

        // 5. Authenticate candidate & redirect to dashboard
        Auth::login($user);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        session()->forget('verified_phone');

        return redirect()->route('dashboards.index')
            ->with('status', "Congratulations {$applicantName}! Your Application (#{$appliNano}) has been saved successfully.");
    }

    /**
     * API to fetch branches for a program.
     */
    public function getBranches(int $programId): JsonResponse
    {
        $branches = Branch::where('program_id', $programId)
            ->select('branch_id', 'branch_code', 'branch_short_name', 'branch_long_name')
            ->orderBy('branch_long_name')
            ->get();

        return response()->json([
            'success'  => true,
            'branches' => $branches,
        ]);
    }

    /**
     * Automatically save/update student field on blur/change without user notification.
     */
    public function autosave(Request $request): JsonResponse
    {
        $cleanMobile = preg_replace('/\D/', '', $request->input('mobile') ?: (Auth::user()?->phnno ?? session('verified_phone', '')));
        if (str_starts_with($cleanMobile, '91') && strlen($cleanMobile) > 10) {
            $cleanMobile = substr($cleanMobile, -10);
        }
        if (strlen($cleanMobile) > 10) {
            $cleanMobile = substr($cleanMobile, -10);
        }

        if (empty($cleanMobile) || strlen($cleanMobile) !== 10) {
            return response()->json(['success' => false, 'message' => 'Valid mobile required'], 422);
        }

        $fieldName = (string) $request->input('field', '');
        $fieldValue = $request->input('value');

        $data = $request->except(['_token', 'mobile', 'field', 'value']);
        if (!empty($fieldName)) {
            $data[$fieldName] = $fieldValue;
        }

        // 1. Detect or create in crm.leads
        try {
            Lead::firstOrCreate(
                ['phoneno' => (int)$cleanMobile],
                [
                    'phnostatus' => 'Registration in progress',
                    'enquirydate' => now()->toDateString(),
                    'log_userid' => 'AUTOSAVE',
                    'log_ipaddress' => $request->ip(),
                    'log_timestamp' => now(),
                ]
            );
        } catch (\Throwable $e) {}

        // 2. Find or create in crm.student_registration
        try {
            $crmStudent = CrmStudentRegistration::where('phoneno', $cleanMobile)->first();
            if (!$crmStudent) {
                $appliNano = '20' . date('ymd') . mt_rand(100, 999);
                $crmStudent = CrmStudentRegistration::create([
                    'applinano'    => $appliNano,
                    'phoneno'      => $cleanMobile,
                    'name'         => $data['applicant_name'] ?? ($data['name'] ?? 'Candidate ' . substr($cleanMobile, -4)),
                    'emailid'      => $data['email'] ?? ($cleanMobile . '@student.vignan.ac.in'),
                    'campuscode'   => $data['campus_code'] ?? 'VDM',
                    'countryid'    => isset($data['country_id']) ? (int)$data['country_id'] : 1,
                    'stateid'      => isset($data['state_id']) ? (int)$data['state_id'] : 1,
                    'programlevel' => $data['program_level'] ?? 'UG',
                    'programid'    => isset($data['progid']) ? (int)$data['progid'] : 1,
                    'branchid'     => isset($data['branch_id']) ? (int)$data['branch_id'] : 11,
                    'std_status'   => 'Applied',
                    'stage1'       => 'not_paid',
                    'log_userid'   => 'AUTOSAVE',
                    'log_ipaddress'=> $request->ip(),
                    'log_timestamp'=> now(),
                ]);
            } else {
                $crmUpdates = [];
                if (isset($data['applicant_name']) || isset($data['name'])) {
                    $crmUpdates['name'] = trim($data['applicant_name'] ?? $data['name']);
                }
                if (isset($data['email'])) {
                    $crmUpdates['emailid'] = trim($data['email']);
                }
                if (isset($data['campus_id'])) {
                    $cmp = Campus::where('campus_id', $data['campus_id'])->first();
                    if ($cmp) $crmUpdates['campuscode'] = $cmp->campus_code;
                }
                if (isset($data['country_id'])) {
                    $crmUpdates['countryid'] = (int)$data['country_id'];
                }
                if (isset($data['state_id'])) {
                    $crmUpdates['stateid'] = (int)$data['state_id'];
                }
                if (isset($data['program_level'])) {
                    $crmUpdates['programlevel'] = ($data['program_level'] === 'Doctoral') ? 'PhD' : $data['program_level'];
                }
                if (isset($data['progid'])) {
                    $crmUpdates['programid'] = (int)$data['progid'];
                }
                if (isset($data['branch_id'])) {
                    $crmUpdates['branchid'] = (int)$data['branch_id'];
                }
                if (!empty($crmUpdates)) {
                    $crmUpdates['log_ipaddress'] = $request->ip();
                    $crmUpdates['log_timestamp'] = now();
                    $crmStudent->update($crmUpdates);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[Autosave] crm.student_registration error: ' . $e->getMessage());
        }

        // 3. Update admissions.student_registrations
        try {
            $admStudent = StudentRegistration::where('mobile', $cleanMobile)->first();
            if (!$admStudent && isset($crmStudent) && $crmStudent) {
                $programCode = Program::where('program_id', $crmStudent->programid)->value('program_seprt_code') ?? 'CK';
                $regNo = '261' . $programCode . mt_rand(10000, 99999);
                $admStudent = StudentRegistration::create([
                    'application_no' => $crmStudent->applinano,
                    'register_no'    => $regNo,
                    'enrollment_no'  => 'Jan-26-' . Str::random(10),
                    'student_name'   => $crmStudent->name,
                    'personal_email' => $crmStudent->emailid,
                    'ofc_mail'       => $crmStudent->emailid,
                    'mobile'         => $cleanMobile,
                    'regulation'     => 'R25',
                    'progid'         => $crmStudent->programid ?: 1,
                    'electiveid'     => $crmStudent->branchid ?: 11,
                    'admyear'        => 2026,
                    'adm_batch_code' => 'JANUARY_2026',
                    'std_type'       => 'Regular',
                    'std_status'     => 'Applied',
                    'log_userid'     => 'AUTOSAVE',
                    'log_ipaddress'  => $request->ip(),
                    'log_timestamp'  => now(),
                ]);
            } elseif ($admStudent) {
                $admUpdates = [];
                if (isset($data['applicant_name']) || isset($data['name'])) {
                    $admUpdates['student_name'] = trim($data['applicant_name'] ?? $data['name']);
                }
                if (isset($data['email'])) {
                    $admUpdates['personal_email'] = trim($data['email']);
                    $admUpdates['ofc_mail'] = trim($data['email']);
                }
                if (isset($data['progid'])) {
                    $admUpdates['progid'] = (int)$data['progid'];
                }
                if (isset($data['branch_id'])) {
                    $admUpdates['electiveid'] = (int)$data['branch_id'];
                }
                if (!empty($admUpdates)) {
                    $admUpdates['log_ipaddress'] = $request->ip();
                    $admUpdates['log_timestamp'] = now();
                    $admStudent->update($admUpdates);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[Autosave] admissions.student_registrations error: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
}
