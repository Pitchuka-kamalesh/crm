<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Branch;
use App\Models\Campus;
use App\Models\Country;
use App\Models\CrmStudentRegistration;
use App\Models\District;
use App\Models\Lead;
use App\Models\LoginOtp;
use App\Models\Program;
use App\Models\State;
use App\Models\TelecallerLead;
use App\Models\TelecallerLeadTask;
use App\Models\User;
use Modules\Sms\Services\SmsService;
use Modules\Sms\Services\WhatsAppService;
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

        $campuses = collect();
        $countries = collect();
        $states = collect();
        $districts = collect();
        $programs = collect();
        $branches = collect();

        try {
            $campuses = Campus::where('is_active', true)
                ->select('campus_id', 'campus_code', 'campus_short_name', 'campus_long_name')
                ->orderBy('campus_id')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('[RegistrationController] Campuses query error: ' . $e->getMessage());
        }

        try {
            $countries = Country::select('country_id', 'country_code', 'country_name', 'country_phonecode')
                ->orderBy('country_id')
                ->get();
            $states = State::select('state_id', 'state_code', 'state_name', 'country_id')
                ->orderBy('state_name')
                ->get();
            $districts = District::cachedAll();
        } catch (\Throwable $e) {
            Log::warning('[RegistrationController] Geo tables missing, triggering auto-heal: ' . $e->getMessage());
            self::ensureGeographicTablesExist();
            try {
                $countries = Country::select('country_id', 'country_code', 'country_name', 'country_phonecode')
                    ->orderBy('country_id')
                    ->get();
                $states = State::select('state_id', 'state_code', 'state_name', 'country_id')
                    ->orderBy('state_name')
                    ->get();
                $districts = District::cachedAll();
            } catch (\Throwable $e2) {
                Log::warning('[RegistrationController] Geo query fallback: ' . $e2->getMessage());
            }
        }

        // Clean safety fallbacks if tables are still empty
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

        try {
            $programs = Program::select('program_id', 'program_level', 'program_short_name', 'program_long_name')
                ->orderBy('program_level')
                ->orderBy('program_id')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('[RegistrationController] Programs query error: ' . $e->getMessage());
        }

        try {
            $branches = Branch::where('lock_status', 0)
                ->select('branch_id', 'program_id', 'branch_short_name', 'branch_long_name', 'lock_status')
                ->orderBy('branch_long_name')
                ->get();
        } catch (\Throwable $e) {
            Log::warning('[RegistrationController] Branches query error: ' . $e->getMessage());
        }

        $viewName = view()->exists('dashboards::register') ? 'dashboards::register' : 'auth.register';
        return view($viewName, compact('campuses', 'countries', 'states', 'districts', 'programs', 'branches', 'prefilledPhone'));
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
            'district_id'    => ['nullable'],
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
        $districtId = $request->input('district_id');

        $countryName = 'India';
        if ($countryId) {
            $cModel = Country::where('country_id', $countryId)->first();
            if ($cModel) {
                $countryName = $cModel->country_name;
            }
        }

        $stateName = 'Andhra Pradesh';
        if ($stateId) {
            $sModel = State::where('state_id', $stateId)->first();
            if ($sModel) {
                $stateName = $sModel->state_name;
            }
        }

        $districtName = null;
        if (!empty($districtId)) {
            $dModel = District::cachedFind((int) $districtId);
            if ($dModel) {
                $districtName = $dModel->DISTRICT_NAME;
            } else {
                $districtName = District::cachedName((int) $districtId);
            }
        }

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
        if ($crmRecord && !empty($crmRecord->applinano) && $crmRecord->applinano !== '0') {
            // Check if this applinano is already taken in master.login by a different phone number
            $conflictUser = User::where('usercode', (string) $crmRecord->applinano)
                ->where('phnno', '!=', $cleanMobile)
                ->exists();
            if (!$conflictUser) {
                $appliNano = (string) $crmRecord->applinano;
            }
        }

        if (!$appliNano) {
            // Generate 10-digit application number starting from 2027000001
            $appliNano = CrmStudentRegistration::generateNextApplicationNumber();
        }

        // 2. Insert or update crm.student_registration via Eloquent (guarantees single record per student)
        try {
            if ($crmRecord) {
                $crmRecord->update([
                    'applinano'    => $appliNano,
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

        // 3. In master.login: use phone number as unique, and replace user code zero with generated code
        $user = User::where('phnno', $cleanMobile)->first();
        if ($user) {
            $user->update([
                'usercode'       => $appliNano, // Replaces '0' with the generated code!
                'personalmail'   => $email,
                'officialmail'   => $email,
                'user_type'      => 1, // Student Candidate
                'role_id'        => 5, // Student role
                'campus_id'      => (string) $request->campus_id,
                'is_active'      => true,
                'log_ipaddress'  => $request->ip(),
                'log_timestamp'  => now(),
            ]);
        } else {
            $user = User::create([
                'usercode'       => $appliNano,
                'phnno'          => $cleanMobile,
                'personalmail'   => $email,
                'officialmail'   => $email,
                'user_type'      => 1,
                'role_id'        => 5,
                'campus_id'      => (string) $request->campus_id,
                'is_active'      => true,
                'pwd'            => bcrypt($defaultPassword),
                'log_ipaddress'  => $request->ip(),
                'log_timestamp'  => now(),
            ]);
        }

        // Sync master.login_otp usercode if it was zero or unlinked
        try {
            LoginOtp::where('identifier', $cleanMobile)
                ->where(function ($q) {
                    $q->where('usercode', '0')
                      ->orWhereNull('usercode')
                      ->orWhere('usercode', '');
                })
                ->update(['usercode' => (string) $appliNano]);
        } catch (\Throwable $e) {
            Log::warning('[RegistrationController] OTP sync error: ' . $e->getMessage());
        }

        // 3b. Sync lead entry into crm.leads (preserve fixed log_timestamp from first entry)
        try {
            $phoneInt = (int) $cleanMobile;
            $applinoInt = is_numeric($appliNano) ? (int) $appliNano : null;
            $existingLead = Lead::where('phoneno', $phoneInt)->first();
            if ($existingLead) {
                $existingLead->update([
                    'applino'       => $applinoInt,
                    'phnostatus'    => 'Registered',
                    'log_userid'    => 'SELF_REGISTER',
                    'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                    // log_timestamp is fixed from when lead entered first time; do NOT overwrite!
                ]);
            } else {
                Lead::create([
                    'phoneno'       => $phoneInt,
                    'applino'       => $applinoInt,
                    'phnostatus'    => 'Registered',
                    'enquirydate'   => now()->toDateString(),
                    'empcode'       => '0',
                    'allocated_at'  => null,
                    'log_userid'    => 'SELF_REGISTER',
                    'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                    'log_timestamp' => now(), // Initial entry timestamp
                ]);
            }
        } catch (\Throwable $e) {
            Log::info('[RegistrationController] Lead sync notice: ' . $e->getMessage());
        }

        // 3c. Pre-populate Student Personal Info & Academic Details with selected Country, State, District
        try {
            $personalCrmId = $crmRecord?->crmid;
            if (!$personalCrmId || $personalCrmId === '0') {
                $personalCrmId = \App\Services\CrmIdService::generateForStudent($crmRecord);
                if ($crmRecord && $personalCrmId) {
                    $crmRecord->update(['crmid' => $personalCrmId]);
                }
            }

            // Sync Personal Info with registered Country, State, District
            $existingPersonal = \Modules\Dashboards\Models\StudentPersonalInfo::where('applino', (string) $appliNano)->first()
                ?? (!empty($cleanMobile) ? \Modules\Dashboards\Models\StudentPersonalInfo::where('phoneno', (string) $cleanMobile)->first() : null);

            if ($existingPersonal) {
                $personalUpdates = [];
                if (empty($existingPersonal->country)) {
                    $personalUpdates['country'] = $countryName;
                }
                if (empty($existingPersonal->state) || $existingPersonal->state === 'Andhra Pradesh') {
                    $personalUpdates['state'] = $stateName;
                }
                if ((empty($existingPersonal->district) || $existingPersonal->district === 'Guntur') && $districtName) {
                    $personalUpdates['district'] = $districtName;
                }
                if (!empty($personalUpdates)) {
                    $existingPersonal->update($personalUpdates);
                }
            } else {
                \Modules\Dashboards\Models\StudentPersonalInfo::create([
                    'crmid'          => $personalCrmId ?: '0',
                    'applino'        => (string) $appliNano,
                    'phoneno'        => (string) $cleanMobile,
                    'country'        => $countryName,
                    'state'          => $stateName,
                    'district'       => $districtName ?? '',
                    'terms_accepted' => 'no',
                    'log_userid'     => 'SELF_REGISTER',
                    'log_ipaddress'  => $request->ip() ?? '127.0.0.1',
                    'log_timestamp'  => now(),
                ]);
            }

            // Sync Academic Details with registered State and District for 10th & 12th
            $existingAcademic = \Modules\Dashboards\Models\StudentAcademicDetail::where('applino', (string) $appliNano)->first()
                ?? (!empty($cleanMobile) ? \Modules\Dashboards\Models\StudentAcademicDetail::where('phoneno', (string) $cleanMobile)->first() : null);

            if ($existingAcademic) {
                $academicUpdates = [];
                if (empty($existingAcademic->tenth_school_state) || $existingAcademic->tenth_school_state === 'Andhra Pradesh') {
                    $academicUpdates['tenth_school_state'] = $stateName;
                }
                if ((empty($existingAcademic->tenth_school_district) || $existingAcademic->tenth_school_district === 'Guntur') && $districtName) {
                    $academicUpdates['tenth_school_district'] = $districtName;
                }
                if (empty($existingAcademic->pu2_college_state) || $existingAcademic->pu2_college_state === 'Andhra Pradesh') {
                    $academicUpdates['pu2_college_state'] = $stateName;
                }
                if ((empty($existingAcademic->pu2_college_district) || $existingAcademic->pu2_college_district === 'Guntur') && $districtName) {
                    $academicUpdates['pu2_college_district'] = $districtName;
                }
                if (!empty($academicUpdates)) {
                    $existingAcademic->update($academicUpdates);
                }
            } else {
                \Modules\Dashboards\Models\StudentAcademicDetail::create([
                    'crmid'                 => $personalCrmId ?: '0',
                    'applino'               => (string) $appliNano,
                    'phoneno'               => (string) $cleanMobile,
                    'progmid'               => $progId,
                    'branchid'              => $branchId,
                    'tenth_school_state'    => $stateName,
                    'tenth_school_district' => $districtName ?? '',
                    'pu2_college_state'     => $stateName,
                    'pu2_college_district'  => $districtName ?? '',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('[RegistrationController] Prefill personal/academic error: ' . $e->getMessage());
        }

        // 3c. Fill / update application number in crm.telecaller_leads table
        try {
            $cleanMobileStr = (string) $cleanMobile;
            $appliNanoStr = (string) $appliNano;
            $personalCrmId = \App\Models\StudentPersonalInfo::query()
                ->where(function ($q) use ($appliNanoStr, $cleanMobileStr) {
                    $q->where('applino', $appliNanoStr)
                      ->orWhere('phoneno', $cleanMobileStr);
                })
                ->whereNotNull('crmid')
                ->where('crmid', '!=', '')
                ->where('crmid', '!=', '0')
                ->value('crmid');
            $studentCrmId = $personalCrmId ? (string) $personalCrmId : '0';

            // Check if there is an existing telecaller_leads record for this student's phone
            $existingTcLead = \Modules\Leads\Models\TelecallerLead::query()
                ->where('phoneno', $cleanMobileStr)
                ->first();

            if ($existingTcLead) {
                // If a record already exists with the new applino, ensure crmid is up-to-date and clean up any placeholder '0' row
                $hasNewAppli = \Modules\Leads\Models\TelecallerLead::query()
                    ->where('applino', $appliNanoStr)
                    ->where('phoneno', $cleanMobileStr)
                    ->exists();

                if ($hasNewAppli) {
                    \Modules\Leads\Models\TelecallerLead::query()
                        ->where('applino', $appliNanoStr)
                        ->where('phoneno', $cleanMobileStr)
                        ->update([
                            'crmid'         => $studentCrmId,
                            'log_timestamp' => now(),
                        ]);

                    // Remove placeholder row if present
                    \Modules\Leads\Models\TelecallerLead::query()
                        ->where('applino', '0')
                        ->where('phoneno', $cleanMobileStr)
                        ->delete();
                } else {
                    // Update the application number from '0' to newly generated $appliNano
                    \Modules\Leads\Models\TelecallerLead::query()
                        ->where('phoneno', $cleanMobileStr)
                        ->where(function ($q) {
                            $q->where('applino', '0')
                              ->orWhereNull('applino')
                              ->orWhere('applino', '');
                        })
                        ->update([
                            'applino'       => $appliNanoStr,
                            'crmid'         => $studentCrmId,
                            'log_timestamp' => now(),
                        ]);
                }

                // Sync tasks in crm.telecaller_leads_tasks
                \Modules\Leads\Models\TelecallerLeadTask::query()
                    ->where('phoneno', $cleanMobileStr)
                    ->where(function ($q) {
                        $q->where('applino', '0')
                          ->orWhereNull('applino')
                          ->orWhere('applino', '');
                    })
                    ->update([
                        'applino'       => $appliNanoStr,
                        'crmid'         => $studentCrmId,
                        'log_timestamp' => now(),
                    ]);
            }
        } catch (\Throwable $e) {
            Log::warning('[RegistrationController] Telecaller leads sync error: ' . $e->getMessage());
        }

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
            ->where('lock_status', 0)
            ->select('branch_id', 'branch_code', 'branch_short_name', 'branch_long_name')
            ->orderBy('branch_long_name')
            ->get();

        return response()->json([
            'success'  => true,
            'branches' => $branches,
        ]);
    }

    /**
     * API to dynamically fetch districts for a selected state from crm.district (cached for 24 hours).
     */
    public function getDistricts(int $stateId): JsonResponse
    {
        $districts = District::cachedByState($stateId);

        return response()->json([
            'success'   => true,
            'districts' => $districts,
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

    /**
     * Auto-heal helper to ensure crm.countries, crm.states, and crm.district tables exist.
     */
    public static function ensureGeographicTablesExist(): void
    {
        try {
            DB::statement('CREATE SCHEMA IF NOT EXISTS crm;');
            DB::statement('CREATE SCHEMA IF NOT EXISTS master;');
            DB::statement('CREATE SCHEMA IF NOT EXISTS finance;');
            DB::statement('CREATE SCHEMA IF NOT EXISTS admissions;');

            DB::unprepared("
                CREATE TABLE IF NOT EXISTS crm.countries (
                    country_id integer NOT NULL PRIMARY KEY,
                    country_code character varying(10) NOT NULL,
                    country_name character varying(100),
                    country_phonecode character varying(100),
                    lock_status integer DEFAULT 0,
                    log_userid character varying(20),
                    log_timestamp timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    log_ipaddress character varying(100)
                );

                CREATE TABLE IF NOT EXISTS crm.states (
                    state_id integer NOT NULL PRIMARY KEY,
                    state_code character varying(20),
                    state_name character varying(100),
                    country_id integer,
                    lock_status integer DEFAULT 0,
                    log_userid character varying(20),
                    log_timestamp timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    log_ipaddress character varying(100)
                );

                CREATE TABLE IF NOT EXISTS crm.district (
                    \"DISTRICT_ID\" integer NOT NULL PRIMARY KEY,
                    \"DISTRICT_CODE\" character varying(20) NOT NULL,
                    \"DISTRICT_NAME\" character varying(100),
                    \"STATE_ID\" integer NOT NULL,
                    \"COUNTRY_ID\" integer NOT NULL,
                    \"LOCK_STATUS\" integer DEFAULT 0,
                    \"LOG_USERID\" character varying(20),
                    \"LOG_TIMESTAMP\" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    \"LOG_IPADDRESS\" character varying(100)
                );

                CREATE SEQUENCE IF NOT EXISTS crm.states_state_id_seq OWNED BY crm.states.state_id;
                SELECT setval('crm.states_state_id_seq', COALESCE((SELECT MAX(state_id) FROM crm.states), 0) + 1, false);
                ALTER TABLE crm.states ALTER COLUMN state_id SET DEFAULT nextval('crm.states_state_id_seq');

                CREATE SEQUENCE IF NOT EXISTS crm.district_district_id_seq OWNED BY crm.district.\"DISTRICT_ID\";
                SELECT setval('crm.district_district_id_seq', COALESCE((SELECT MAX(\"DISTRICT_ID\") FROM crm.district), 0) + 1, false);
                ALTER TABLE crm.district ALTER COLUMN \"DISTRICT_ID\" SET DEFAULT nextval('crm.district_district_id_seq');

                -- Student Registration Core
                CREATE SEQUENCE IF NOT EXISTS crm.student_registration_applinano_seq START WITH 1;
                CREATE TABLE IF NOT EXISTS crm.student_registration (
                    applinano character varying(20) NOT NULL PRIMARY KEY,
                    name character varying(100) NOT NULL,
                    phoneno character varying(20) NOT NULL,
                    campuscode character varying(50),
                    emailid character varying(255),
                    countryid integer,
                    stateid integer,
                    districtid integer,
                    programlevel character varying(50),
                    programid integer,
                    branchid integer,
                    ableto boolean DEFAULT true,
                    crmid character varying(50),
                    stage1 character varying(20) DEFAULT 'not_paid',
                    stage2 character varying(20) DEFAULT 'NA',
                    stage3 character varying(20) DEFAULT 'NA',
                    stage4 character varying(20) DEFAULT 'NA',
                    stage5 character varying(20) DEFAULT 'NA',
                    stage6 character varying(20) DEFAULT 'NA',
                    stage7 character varying(20) DEFAULT 'NA',
                    stage8 character varying(20) DEFAULT 'NA',
                    std_status character varying(30),
                    log_userid character varying(50),
                    log_ipaddress character varying(255),
                    log_timestamp timestamp without time zone DEFAULT now()
                );

                -- Student Personal Info & Academic Details
                CREATE TABLE IF NOT EXISTS crm.student_personal_info (
                    crmid VARCHAR(50) PRIMARY KEY,
                    applino VARCHAR(20) NOT NULL,
                    phoneno VARCHAR(20) NOT NULL,
                    dob DATE,
                    gender VARCHAR(20),
                    parent_name VARCHAR(255),
                    parentphono VARCHAR(20),
                    caste_category VARCHAR(50),
                    referedby VARCHAR(255),
                    referedempcode VARCHAR(50),
                    country VARCHAR(100),
                    state VARCHAR(100),
                    district VARCHAR(100),
                    city_mandal VARCHAR(100),
                    village VARCHAR(100),
                    pincode VARCHAR(10),
                    terms_accepted VARCHAR(10) DEFAULT 'no',
                    log_userid VARCHAR(50),
                    log_ipaddress VARCHAR(255),
                    log_timestamp TIMESTAMP DEFAULT now()
                );

                CREATE TABLE IF NOT EXISTS crm.student_academic_details (
                    crmid VARCHAR(50) PRIMARY KEY,
                    applino VARCHAR(20) NOT NULL,
                    phoneno VARCHAR(20) NOT NULL,
                    tenth_board VARCHAR(100),
                    tenth_school_name VARCHAR(255),
                    tenth_school_state VARCHAR(100),
                    tenth_school_district VARCHAR(100),
                    tenth_passed_year VARCHAR(10),
                    tenth_grade_type VARCHAR(20),
                    tenth_grade_value VARCHAR(20),
                    pu2_board VARCHAR(100),
                    pu2_college_name VARCHAR(255),
                    pu2_college_state VARCHAR(100),
                    pu2_college_district VARCHAR(100),
                    pu2_passed_year VARCHAR(10),
                    pu2_grade_type VARCHAR(20),
                    pu2_grade_value VARCHAR(20),
                    ug_university VARCHAR(255),
                    ug_college_name VARCHAR(255),
                    ug_passed_year VARCHAR(10),
                    ug_grade_type VARCHAR(20),
                    ug_grade_value VARCHAR(20),
                    pg_university VARCHAR(255),
                    pg_college_name VARCHAR(255),
                    pg_passed_year VARCHAR(10),
                    pg_grade_type VARCHAR(20),
                    pg_grade_value VARCHAR(20),
                    log_userid VARCHAR(50),
                    log_ipaddress VARCHAR(255),
                    log_timestamp TIMESTAMP DEFAULT now()
                );

                -- CRM Leads
                CREATE TABLE IF NOT EXISTS crm.leads (
                    phoneno bigint NOT NULL PRIMARY KEY,
                    phnostatus character varying(255),
                    enquirydate date,
                    log_userid character varying(100),
                    log_ipaddress character varying(255),
                    log_timestamp timestamp without time zone,
                    applino bigint,
                    empcode character varying(50),
                    allocated_at timestamp without time zone
                );

                -- Finance Exam Schedule
                CREATE TABLE IF NOT EXISTS finance.exam_schedule (
                    applino VARCHAR(30) NOT NULL PRIMARY KEY,
                    crmid VARCHAR(50),
                    phoneno VARCHAR(20),
                    examdate DATE,
                    exam_slot VARCHAR(50),
                    exam_status INTEGER DEFAULT 0,
                    loguserid VARCHAR(50),
                    logdatetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    ipaddress VARCHAR(50)
                );

                -- Master Communication & SMS Logs
                CREATE TABLE IF NOT EXISTS master.communication_logs (
                    id bigserial PRIMARY KEY,
                    channel varchar(20) DEFAULT 'whatsapp',
                    wauser varchar(100),
                    waregisterno varchar(50),
                    wapptophone varchar(20),
                    jiotraceid varchar(100),
                    template_name varchar(100),
                    wapptext text,
                    var1 text,
                    var2 text,
                    status varchar(50) DEFAULT 'SUBMITTED',
                    ipaddr varchar(45),
                    response_payload jsonb,
                    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP
                );

                CREATE OR REPLACE VIEW master.wapp_logs AS SELECT * FROM master.communication_logs;
                CREATE OR REPLACE VIEW master.wapplogs AS SELECT * FROM master.communication_logs;

                CREATE TABLE IF NOT EXISTS master.smslogs (
                    \"smsId\" bigserial PRIMARY KEY,
                    smsuser varchar(100),
                    smsregisterno varchar(50),
                    smstophone varchar(20),
                    jiotraceid varchar(120),
                    smstext text,
                    datetime timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
                    ipaddr varchar(50),
                    status varchar(50),
                    var1 text,
                    var2 text,
                    var3 text,
                    var4 text,
                    var5 text
                );
            ");

            try {
                $count = DB::table('crm.countries')->count();
                if ($count === 0) {
                    $masterSql = database_path('sql/vignan_master_data.sql');
                    if (file_exists($masterSql)) {
                        DB::unprepared(file_get_contents($masterSql));
                    }
                }
            } catch (\Throwable $e) {}

            try {
                $districtCount = DB::table('crm.district')->count();
                if ($districtCount === 0) {
                    $intlSql = database_path('sql/seed_international_states_and_districts.sql');
                    if (file_exists($intlSql)) {
                        DB::unprepared(file_get_contents($intlSql));
                    }
                }
            } catch (\Throwable $e) {}
        } catch (\Throwable $e) {
            Log::error('[RegistrationController] Auto-heal geographic tables error: ' . $e->getMessage());
        }
    }
}
