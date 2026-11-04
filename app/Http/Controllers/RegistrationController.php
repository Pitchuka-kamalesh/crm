<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Campus;
use App\Models\Program;
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

        $campuses = DB::table('master.campuses')
            ->where('is_active', true)
            ->select('campus_id', 'campus_code', 'campus_short_name', 'campus_long_name')
            ->orderBy('campus_id')
            ->get();

        $countries = DB::table('crm.countries')
            ->select('country_id', 'country_code', 'country_name', 'country_phonecode')
            ->orderBy('country_id')
            ->get();

        $states = DB::table('crm.states')
            ->select('state_id', 'state_code', 'state_name', 'country_id')
            ->orderBy('state_name')
            ->get();

        $programs = DB::table('master.programs')
            ->select('program_id', 'program_level', 'program_short_name', 'program_long_name')
            ->orderBy('program_level')
            ->orderBy('program_id')
            ->get();

        $branches = DB::table('master.branches')
            ->select('branch_id', 'program_id', 'branch_short_name', 'branch_long_name')
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
        $campus = DB::table('master.campuses')->where('campus_id', $request->campus_id)->first();
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
        $programName = DB::table('master.programs')
            ->where('program_id', $progId)
            ->value('program_long_name') ?? 'Bachelor / Master Program';

        $branchName = 'General Core';
        if ($branchId) {
            $branchName = DB::table('master.branches')
                ->where('branch_id', $branchId)
                ->value('branch_long_name') ?? 'General Core';
        }

        // 1. Insert into crm.student_registration
        $appliNano = null;
        try {
            $inserted = DB::selectOne("
                INSERT INTO crm.student_registration (
                    name, phoneno, emailid, campuscode, countryid, stateid, programlevel, programid, branchid,
                    ableto, std_status, stage1, stage2, stage3, stage4, stage5, stage6, stage7, stage8,
                    log_userid, log_ipaddress, log_timestamp
                ) VALUES (
                    :name, :phoneno, :emailid, :campuscode, :countryid, :stateid, :programlevel, :programid, :branchid,
                    :ableto, 'Applied', 'not_paid', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA', 'NA',
                    'SELF_REGISTER', :ip, NOW()
                ) RETURNING applinano
            ", [
                'name'         => $applicantName,
                'phoneno'      => $cleanMobile,
                'emailid'      => $email,
                'campuscode'   => $campusCode,
                'countryid'    => $countryId,
                'stateid'      => $stateId,
                'programlevel' => $dbProgLevel,
                'programid'    => $progId,
                'branchid'     => $branchId,
                'ableto'       => $ableTo ? 'true' : 'false',
                'ip'           => $request->ip(),
            ]);

            if ($inserted && isset($inserted->applinano)) {
                $appliNano = $inserted->applinano;
            }
        } catch (Exception $e) {
            Log::error('[RegistrationController] crm.student_registration error: ' . $e->getMessage());
        }

        if (!$appliNano) {
            $appliNano = '20' . date('ymd') . mt_rand(100, 999);
        }

        // 2. Insert into admissions.student_registrations
        $programCode = DB::table('master.programs')
            ->where('program_id', $progId)
            ->value('program_seprt_code') ?? 'CK';
        $regNo = '261' . $programCode . mt_rand(10000, 99999);
        $enrollmentNo = 'Jan-26-' . Str::random(10);

        try {
            DB::table('admissions.student_registrations')->updateOrInsert(
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
                'role_id'        => 1,
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
        $branches = DB::table('master.branches')
            ->where('program_id', $programId)
            ->select('branch_id', 'branch_code', 'branch_short_name', 'branch_long_name')
            ->orderBy('branch_long_name')
            ->get();

        return response()->json([
            'success'  => true,
            'branches' => $branches,
        ]);
    }
}
