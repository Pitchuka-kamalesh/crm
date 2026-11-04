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
        $prefilledPhone = $request->query('phone') ?? session('verified_phone', '');
        if ($prefilledPhone) {
            $prefilledPhone = preg_replace('/\D/', '', $prefilledPhone);
            if (strlen($prefilledPhone) > 10) {
                $prefilledPhone = substr($prefilledPhone, -10);
            }
        }

        $campuses = DB::table('master.campuses')
            ->where('is_active', 1)
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

        return view('auth.register', compact('campuses', 'programs', 'branches', 'prefilledPhone'));
    }

    /**
     * Store new student registration into admissions.student_registrations & master.login.
     */
    public function store(
        Request $request,
        WhatsAppService $whatsAppService,
        SmsService $smsService
    ): RedirectResponse {
        $request->validate([
            'applicant_name' => ['required', 'string', 'max:150'],
            'email'          => ['required', 'email', 'max:150'],
            'mobile'         => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'password'       => ['required', 'string', 'min:8', 'confirmed'],
            'campus_id'      => ['required'],
            'program_level'  => ['required', 'string', 'in:UG,PG,Doctoral,PhD'],
            'progid'         => ['required', 'integer'],
            'branch_id'      => ['nullable', 'integer'],
            'state'          => ['nullable', 'string', 'max:100'],
        ]);

        $cleanMobile = preg_replace('/\D/', '', $request->input('mobile'));
        if (strlen($cleanMobile) > 10) {
            $cleanMobile = substr($cleanMobile, -10);
        }

        // Generate unique 11-digit application number (e.g. 202601 + 5 digits)
        do {
            $applicationNo = '202601' . mt_rand(10000, 99999);
        } while (DB::table('admissions.student_registrations')->where('application_no', $applicationNo)->exists());

        // Generate register number
        $programCode = DB::table('master.programs')
            ->where('program_id', $request->progid)
            ->value('program_seprt_code') ?? 'CK';
        $regNo = '261' . $programCode . mt_rand(10000, 99999);
        $enrollmentNo = 'Jan-26-' . Str::random(10);

        $programName = DB::table('master.programs')
            ->where('program_id', $request->progid)
            ->value('program_long_name') ?? 'Bachelor / Master Program';

        $branchName = 'General Core';
        if ($request->branch_id) {
            $branchName = DB::table('master.branches')
                ->where('branch_id', $request->branch_id)
                ->value('branch_long_name') ?? 'General';
        }

        // 1. Insert or update in admissions.student_registrations
        $existingStudent = StudentRegistration::where('mobile', $cleanMobile)->first();

        if ($existingStudent) {
            $applicationNo = $existingStudent->application_no ?: $applicationNo;
            $regNo = $existingStudent->register_no ?: $regNo;
            $enrollmentNo = $existingStudent->enrollment_no ?: $enrollmentNo;

            DB::table('admissions.student_registrations')
                ->where('application_no', $existingStudent->application_no)
                ->update([
                    'student_name'   => trim($request->input('applicant_name')),
                    'personal_email' => trim($request->input('email')),
                    'ofc_mail'       => trim($request->input('email')),
                    'regulation'     => 'R25',
                    'progid'         => (int)$request->progid,
                    'electiveid'     => (int)($request->branch_id ?: 1),
                    'std_type'       => 'Regular',
                    'std_status'     => 'Applied',
                    'log_ipaddress'  => $request->ip(),
                    'log_timestamp'  => now(),
                ]);
        } else {
            DB::table('admissions.student_registrations')->insert([
                'application_no' => $applicationNo,
                'enrollment_no'  => $enrollmentNo,
                'register_no'    => $regNo,
                'student_name'   => trim($request->input('applicant_name')),
                'personal_email' => trim($request->input('email')),
                'ofc_mail'       => trim($request->input('email')),
                'mobile'         => $cleanMobile,
                'regulation'     => 'R25',
                'progid'         => (int)$request->progid,
                'electiveid'     => (int)($request->branch_id ?: 1),
                'admyear'        => 2026,
                'adm_batch_code' => 'JANUARY_2026',
                'std_type'       => 'Regular',
                'std_status'     => 'Applied',
                'lock_status'    => 0,
                'log_userid'     => 'SELF_REGISTER',
                'log_ipaddress'  => $request->ip(),
                'log_timestamp'  => now(),
            ]);
        }

        // 2. Create account in master.login
        $user = User::updateOrCreate(
            ['usercode' => $applicationNo],
            [
                'phnno'          => $cleanMobile,
                'personalmail'   => trim($request->input('email')),
                'officialmail'   => trim($request->input('email')),
                'user_type'      => 1, // Student Candidate
                'role_id'        => 1,
                'campus_id'      => (string)$request->campus_id,
                'is_active'      => true,
                'pwd'            => bcrypt($request->password),
                'log_ipaddress'  => $request->ip(),
                'log_timestamp'  => now(),
            ]
        );

        // Also ensure usercode alias with register_no if different
        if ($regNo && $regNo !== $applicationNo) {
            try {
                User::firstOrCreate(
                    ['usercode' => $regNo],
                    [
                        'phnno'          => $cleanMobile,
                        'personalmail'   => trim($request->input('email')),
                        'officialmail'   => trim($request->input('email')),
                        'user_type'      => 1,
                        'role_id'        => 1,
                        'campus_id'      => (string)$request->campus_id,
                        'is_active'      => true,
                        'pwd'            => bcrypt($request->password),
                        'log_ipaddress'  => $request->ip(),
                        'log_timestamp'  => now(),
                    ]
                );
            } catch (Exception $e) {
                // optional alias
            }
        }

        // 3. Dispatch SmartPing WhatsApp Admission notification (crmfirstinfo)
        try {
            $whatsAppService->sendAdmissionInfo(
                phone: $cleanMobile,
                candidateName: trim($request->input('applicant_name')),
                applicationId: $applicationNo,
                programName: $programName,
                contactInfo: 'admissions@vignan.ac.in / 0863-2344700',
                user: trim($request->input('applicant_name')),
                registerNo: $applicationNo,
                ip: $request->ip()
            );
        } catch (Exception $e) {
            Log::warning('[RegistrationController] WhatsApp confirmation notice skipped: ' . $e->getMessage());
        }

        // 4. Dispatch Jio DLT SMS (1777179354065677511)
        try {
            $smsService->sendAdmissionSms(
                phone: $cleanMobile,
                name: trim($request->input('applicant_name')),
                program: $programName,
                branch: $branchName,
                campus: 'Vignan Main Campus (Vadlamudi)',
                applicationNo: $applicationNo
            );
        } catch (Exception $e) {
            Log::info('[RegistrationController] SMS notice skipped: ' . $e->getMessage());
        }

        // 5. Authenticate candidate and redirect to individual portal dashboard
        Auth::login($user);
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }
        session()->forget('verified_phone');

        return redirect()->route('dashboards.index')
            ->with('status', "Congratulations {$user->name}! Your Admission Application (#{$applicationNo}) has been submitted successfully.");
    }

    /**
     * API to fetch branches for a program.
     */
    public function getBranches(int $programId): JsonResponse
    {
        $branches = DB::table('master.branches')
            ->where('program_id', $programId)
            ->select('branch_id', 'branch_short_name', 'branch_long_name')
            ->orderBy('branch_long_name')
            ->get();

        return response()->json([
            'success' => true,
            'branches' => $branches,
        ]);
    }
}

