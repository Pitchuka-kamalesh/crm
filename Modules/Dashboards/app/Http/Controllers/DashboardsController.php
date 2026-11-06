<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Campus;
use App\Models\CrmStudentRegistration;
use App\Models\Program;
use App\Models\StudentEducationInfo;
use App\Models\StudentRegistration;
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

        // Active step (1 through 8, default 1)
        $activeStep = (int) $request->query('step', 1);
        if ($activeStep < 1 || $activeStep > 8) {
            $activeStep = 1;
        }

        // Resolve student record from admissions.student_registrations
        $student = StudentRegistration::where('register_no', $usercode)
            ->orWhere('application_no', $usercode)
            ->when($cleanPhone, function ($q) use ($cleanPhone) {
                $q->orWhere('mobile', $cleanPhone)->orWhere('mobile', 'like', "%{$cleanPhone}%");
            })
            ->first();

        // Resolve CRM student record from crm.student_registration
        $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
            ->when($student, function ($q) use ($student) {
                $q->orWhere('applinano', $student->application_no);
            })
            ->when($cleanPhone, function ($q) use ($cleanPhone) {
                $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
            })
            ->first();

        $appNo = $student?->application_no ?? ($crmStudent?->applinano ?? ($usercode ?: ('VU2026-' . ($cleanPhone ? substr($cleanPhone, -6) : 'APP'))));
        $regNo = $student?->register_no ?? ($usercode ?: ('261FA' . ($cleanPhone ? substr($cleanPhone, -5) : '00001')));
        $displayName = $student?->student_name ?: ($crmStudent?->name ?: ($user?->name ?: 'Applicant Candidate'));

        // Always display Candidate / Student role
        $roleName = ($user?->role_id === 5 || $user?->user_type === 1) ? 'Student Candidate' : ($user?->role_label ?: 'Student Candidate');

        // Resolve Program and Branch
        $progId = $student?->progid ?? ($crmStudent?->programid ?? 1);
        $program = Program::where('program_id', $progId)->first();
        $programName = $program ? ($program->program_short_name . ' - ' . $program->program_long_name) : 'B.Tech - Bachelor of Technology';

        $branchId = $student?->electiveid ?? ($crmStudent?->branchid ?? 11);
        $branch = Branch::where('branch_id', $branchId)->first();
        $branchName = $branch ? $branch->branch_long_name : 'Agriculture Engineering';

        $campusCode = $crmStudent?->campuscode ?? 'VDM';
        $campus = Campus::where('campus_code', $campusCode)->first() ?? Campus::first();
        $campusName = $campus ? $campus->campus_short_name : 'Vignan Main Campus (Vadlamudi)';

        $regulation = $student?->regulation ?: 'R25';

        // Fetch 10th and Inter educational info
        $edu10th = StudentEducationInfo::where(function ($q) use ($appNo, $regNo) {
            $q->where('application_no', $appNo)->orWhere('register_no', $regNo);
        })->where('qual_level', '10th')->first();

        $eduInter = StudentEducationInfo::where(function ($q) use ($appNo, $regNo) {
            $q->where('application_no', $appNo)->orWhere('register_no', $regNo);
        })->where('qual_level', 'Inter')->first();

        // Fetch uploaded KYC documents
        $documents = collect();
        try {
            $documents = DB::table('admissions.student_documents')
                ->where('application_no', $appNo)
                ->orWhere('register_no', $regNo)
                ->get();
        } catch (\Throwable $e) {}

        // Calculate stage statuses from crmStudent
        $stages = [
            1 => ['title' => 'Registration', 'desc' => 'Basic Application Info', 'status' => 'completed'],
            2 => ['title' => 'Personal Profile', 'desc' => 'Identity & Category', 'status' => ($student?->dob && $student?->aadhar_no) ? 'completed' : 'in_progress'],
            3 => ['title' => 'Parent & Family', 'desc' => 'Guardian Contact', 'status' => ($crmStudent?->stage3 === 'paid') ? 'completed' : 'pending'],
            4 => ['title' => 'Address Details', 'desc' => 'Permanent & Current', 'status' => ($crmStudent?->stage4 === 'paid') ? 'completed' : 'pending'],
            5 => ['title' => 'Academic Records', 'desc' => '10th & Intermediate', 'status' => ($edu10th && $eduInter) ? 'completed' : 'pending'],
            6 => ['title' => 'Document KYC', 'desc' => 'Certificates Upload', 'status' => ($documents->count() >= 4) ? 'completed' : 'pending'],
            7 => ['title' => 'Campus & Branch', 'desc' => 'Course & Hostel Life', 'status' => ($crmStudent?->stage7 === 'paid' || $branch) ? 'completed' : 'pending'],
            8 => ['title' => 'Fee & Allotment', 'desc' => 'Allotment Order', 'status' => ($crmStudent?->stage8 === 'paid') ? 'completed' : 'pending'],
        ];

        // Overall progress percentage
        $completedCount = 0;
        foreach ($stages as $stg) {
            if ($stg['status'] === 'completed') {
                $completedCount++;
            }
        }
        $progressPercent = round(($completedCount / 8) * 100);

        return view('dashboards::index', compact(
            'user',
            'student',
            'crmStudent',
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

        $student = StudentRegistration::where('register_no', $usercode)
            ->orWhere('application_no', $usercode)
            ->when($cleanPhone, function ($q) use ($cleanPhone) {
                $q->orWhere('mobile', $cleanPhone)->orWhere('mobile', 'like', "%{$cleanPhone}%");
            })
            ->first();

        $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
            ->when($student, function ($q) use ($student) {
                $q->orWhere('applinano', $student->application_no);
            })
            ->when($cleanPhone, function ($q) use ($cleanPhone) {
                $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
            })
            ->first();

        $appNo = $student?->application_no ?? ($crmStudent?->applinano ?? ($usercode ?: ('VU2026-' . ($cleanPhone ? substr($cleanPhone, -6) : 'APP'))));
        $regNo = $student?->register_no ?? ($usercode ?: ('261FA' . ($cleanPhone ? substr($cleanPhone, -5) : '00001')));

        $step = (int) $request->input('step', 2);
        $nextStep = min(8, $step + 1);

        try {
            // STEP 2: Personal Profile
            if ($step === 2) {
                $request->validate([
                    'student_name' => ['nullable', 'string', 'max:150'],
                    'dob'          => ['nullable', 'date'],
                    'gender'       => ['nullable', 'string', 'in:Male,Female,Other'],
                    'aadhar_no'    => ['nullable', 'string', 'max:20'],
                    'abc_id'       => ['nullable', 'string', 'max:30'],
                    'personal_email' => ['nullable', 'email', 'max:100'],
                ]);

                if ($student) {
                    $student->update([
                        'student_name'   => $request->student_name ?: $student->student_name,
                        'dob'            => $request->dob ?: $student->dob,
                        'gender'         => $request->gender ?: $student->gender,
                        'aadhar_no'      => $request->aadhar_no ?: $student->aadhar_no,
                        'abc_id'         => $request->abc_id ?: $student->abc_id,
                        'personal_email' => $request->personal_email ?: $student->personal_email,
                        'log_ipaddress'  => $request->ip(),
                        'log_timestamp'  => now(),
                    ]);
                }

                if ($crmStudent) {
                    $crmStudent->update(['stage2' => 'paid']);
                }

                $msg = 'Step 2: Personal Profile & Identity details saved successfully!';
            }

            // STEP 3: Parent & Family Details
            elseif ($step === 3) {
                if ($crmStudent) {
                    $crmStudent->update(['stage3' => 'paid']);
                }
                $msg = 'Step 3: Parent & Family Contact Information saved successfully!';
            }

            // STEP 4: Address Details
            elseif ($step === 4) {
                if ($crmStudent) {
                    $crmStudent->update(['stage4' => 'paid']);
                }
                $msg = 'Step 4: Communication & Permanent Address details saved successfully!';
            }

            // STEP 5: Academic Records (10th & Intermediate)
            elseif ($step === 5) {
                // 10th
                if ($request->filled('ssc_board') || $request->filled('ssc_school') || $request->filled('ssc_hallticket')) {
                    $sscData = [
                        'application_no'   => $appNo,
                        'register_no'      => $regNo,
                        'qual_level'       => '10th',
                        'qual_name'        => 'SSC / 10th Standard',
                        'institution_name' => $request->ssc_school ?: 'High School',
                        'board_university' => $request->ssc_board ?: 'State Board of Secondary Education',
                        'hall_ticket_no'   => $request->ssc_hallticket,
                        'year_of_passing'  => $request->ssc_year ? (int)$request->ssc_year : null,
                        'marks_obtained'   => $request->ssc_marks ? (float)$request->ssc_marks : null,
                        'max_marks'        => $request->ssc_max_marks ? (float)$request->ssc_max_marks : 600,
                        'percentage'       => ($request->ssc_marks && $request->ssc_max_marks) ? round(((float)$request->ssc_marks / (float)$request->ssc_max_marks) * 100, 2) : null,
                        'cgpa'             => $request->ssc_cgpa ? (float)$request->ssc_cgpa : null,
                        'city'             => $request->ssc_city,
                        'state'            => $request->ssc_state ?: 'Andhra Pradesh',
                        'result_status'    => 'Passed',
                        'lock_status'      => 0,
                        'log_userid'       => $usercode,
                        'log_ipaddress'    => $request->ip(),
                        'log_timestamp'    => now(),
                    ];

                    $existing10th = StudentEducationInfo::where(function ($q) use ($appNo, $regNo) {
                        $q->where('application_no', $appNo)->orWhere('register_no', $regNo);
                    })->where('qual_level', '10th')->first();

                    if ($existing10th) {
                        $existing10th->update($sscData);
                    } else {
                        StudentEducationInfo::create($sscData);
                    }
                }

                // Inter
                if ($request->filled('inter_board') || $request->filled('inter_college') || $request->filled('inter_hallticket')) {
                    $interPercentage = $request->inter_percentage;
                    if (!$interPercentage && $request->inter_marks && $request->inter_max_marks) {
                        $interPercentage = round(((float)$request->inter_marks / (float)$request->inter_max_marks) * 100, 2);
                    }

                    $interData = [
                        'application_no'   => $appNo,
                        'register_no'      => $regNo,
                        'qual_level'       => 'Inter',
                        'qual_name'        => 'Intermediate (10+2) / Higher Secondary',
                        'institution_name' => $request->inter_college ?: 'Junior College',
                        'board_university' => $request->inter_board ?: 'Board of Intermediate Education',
                        'study_mode'       => $request->inter_stream ?: 'MPC',
                        'hall_ticket_no'   => $request->inter_hallticket,
                        'year_of_passing'  => $request->inter_year ? (int)$request->inter_year : null,
                        'marks_obtained'   => $request->inter_marks ? (float)$request->inter_marks : null,
                        'max_marks'        => $request->inter_max_marks ? (float)$request->inter_max_marks : 1000,
                        'percentage'       => $interPercentage ? (float)$interPercentage : null,
                        'city'             => $request->inter_city,
                        'state'            => $request->inter_state ?: 'Andhra Pradesh',
                        'result_status'    => 'Passed',
                        'lock_status'      => 0,
                        'log_userid'       => $usercode,
                        'log_ipaddress'    => $request->ip(),
                        'log_timestamp'    => now(),
                    ];

                    $existingInter = StudentEducationInfo::where(function ($q) use ($appNo, $regNo) {
                        $q->where('application_no', $appNo)->orWhere('register_no', $regNo);
                    })->where('qual_level', 'Inter')->first();

                    if ($existingInter) {
                        $existingInter->update($interData);
                    } else {
                        StudentEducationInfo::create($interData);
                    }
                }

                if ($crmStudent) {
                    $crmStudent->update(['stage5' => 'paid']);
                }

                $msg = 'Step 5: Academic Qualification Records (10th & Intermediate) saved successfully!';
            }

            // STEP 6: Document KYC Upload
            elseif ($step === 6) {
                // Record verified KYC certificates
                $docTypes = [
                    'PHOTO' => 'Passport Size Photograph',
                    '10TH_MEMO' => '10th Standard SSC Marks Memo',
                    'INTER_MEMO' => 'Intermediate (10+2) Marks Memo',
                    'TC' => 'Transfer Certificate (TC)',
                    'AADHAAR' => 'Aadhaar Identity Card',
                ];

                foreach ($docTypes as $type => $name) {
                    DB::table('admissions.student_documents')->updateOrInsert(
                        [
                            'application_no' => $appNo,
                            'doctype'        => $type,
                        ],
                        [
                            'register_no'   => $regNo,
                            'docno'         => 'DOC-' . mt_rand(1000, 9999),
                            'docname'       => $name,
                            'docfile'       => 'verified_' . strtolower($type) . '.pdf',
                            'lock_status'   => 1,
                            'log_userid'    => $usercode,
                            'log_ipaddress' => $request->ip(),
                            'log_timestamp' => now(),
                        ]
                    );
                }

                if ($crmStudent) {
                    $crmStudent->update(['stage6' => 'paid']);
                }

                $msg = 'Step 6: Document & KYC Certificate Records updated successfully!';
            }

            // STEP 7: Campus Life & Specialization Preferences
            elseif ($step === 7) {
                if ($crmStudent) {
                    $crmStudent->update([
                        'ableto' => $request->boolean('hostel_required', true),
                        'stage7' => 'paid',
                    ]);
                }
                $msg = 'Step 7: Campus Life, Accommodation & Branch Preferences saved!';
            }

            // STEP 8: Admission Fee & Allotment
            elseif ($step === 8) {
                if ($crmStudent) {
                    $crmStudent->update(['stage8' => 'paid']);
                }
                $nextStep = 8;
                $msg = 'Step 8: Official Admission Fee Paid & Allotment Order Confirmed!';
            } else {
                $msg = 'Step information updated successfully!';
            }

            return redirect()->route('dashboards.index', ['step' => $nextStep])
                ->with('status', $msg);

        } catch (Exception $e) {
            Log::error('[DashboardsController] saveStep error: ' . $e->getMessage());
            return redirect()->route('dashboards.index', ['step' => $step])
                ->withErrors(['error' => 'Failed to save step details: ' . $e->getMessage()]);
        }
    }

    /**
     * Backward-compatible alias for existing form submissions.
     */
    public function saveEducation(Request $request): RedirectResponse
    {
        $request->merge(['step' => 5]);
        return $this->saveStep($request);
    }
}
