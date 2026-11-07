<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Campus;
use App\Models\Country;
use App\Models\CrmStudentRegistration;
use App\Models\Lead;
use App\Models\Program;
use App\Models\State;
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
        $rawMobile = (string)($request->input('mobile') ?: $request->input('phone') ?: '');
        $cleanMobile = preg_replace('/\D/', '', $rawMobile);
        if (str_starts_with($cleanMobile, '91') && strlen($cleanMobile) > 10) {
            $cleanMobile = substr($cleanMobile, -10);
        }
        if (strlen($cleanMobile) > 10) {
            $cleanMobile = substr($cleanMobile, -10);
        }

        if (empty($cleanMobile) || strlen($cleanMobile) !== 10) {
            return back()->withInput()->withErrors([
                'mobile' => 'Mobile number must contain exactly 10 digits.',
            ]);
        }

        if (!in_array($cleanMobile[0], ['6', '7', '8', '9'])) {
            return back()->withInput()->withErrors([
                'mobile' => 'Mobile number must start with 6, 7, 8, or 9.',
            ]);
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

        // 1. Check if student registration already exists for this phone number
        $crmRecord = null;
        try {
            $crmRecord = CrmStudentRegistration::where('phoneno', $cleanMobile)->first();
        } catch (\Throwable $e) {}

        $appliNano = null;
        if ($crmRecord && !empty($crmRecord->applinano)) {
            $appliNano = $crmRecord->applinano;
        } else {
            // Generate unique application number from official crm sequence
            try {
                $seqRow = DB::selectOne("SELECT nextval('crm.student_registration_applinano_seq'::regclass)::text as applinano");
                if ($seqRow && !empty($seqRow->applinano) && !str_contains($seqRow->applinano, '#')) {
                    $appliNano = $seqRow->applinano;
                }
            } catch (\Throwable $e) {}

            if (!$appliNano) {
                $appliNano = '20' . date('ymd') . mt_rand(100, 999);
            }
        }

        // 2. Insert or update crm.student_registration via Eloquent (guarantees single record per student)
        try {
            if ($crmRecord) {
                $crmRecord->update([
                    'name'         => $applicantName,
                    'emailid'      => $email,
                    'campuscode'   => $campusCode,
                    'countryid'    => $countryId,
                    'stateid'      => $stateId,
                    'programlevel' => $dbProgLevel,
                    'programid'    => $progId,
                    'branchid'     => $branchId,
                    'ableto'       => $ableTo,
                    'std_status'   => 'Applied',
                    'log_userid'   => 'SELF_REGISTER',
                    'log_ipaddress'=> $request->ip(),
                    'log_timestamp'=> now(),
                ]);
            } else {
                $crmRecord = CrmStudentRegistration::create([
                    'applinano'    => $appliNano,
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
            }
        } catch (Exception $e) {
            Log::error('[RegistrationController] CrmStudentRegistration error: ' . $e->getMessage());
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

        if (empty($cleanMobile) || strlen($cleanMobile) !== 10 || !in_array($cleanMobile[0], ['6', '7', '8', '9'])) {
            return response()->json(['success' => false, 'message' => 'Valid 10-digit mobile number starting with 6, 7, 8, or 9 required'], 422);
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

        // 2. Only update crm.student_registration if an official record already exists
        try {
            $crmStudent = CrmStudentRegistration::where('phoneno', $cleanMobile)->first();
            if ($crmStudent) {
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

        return response()->json(['success' => true]);
    }
}
