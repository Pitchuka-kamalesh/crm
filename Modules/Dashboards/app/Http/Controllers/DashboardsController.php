<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Employee;
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
     * Display the candidate portal dashboard with admission & academic details.
     */
    public function index(): View
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        // Resolve student record
        $student = StudentRegistration::where('register_no', $usercode)
            ->orWhere('application_no', $usercode)
            ->when($cleanPhone, function ($q) use ($cleanPhone) {
                $q->orWhere('mobile', $cleanPhone)->orWhere('mobile', 'like', "%{$cleanPhone}%");
            })
            ->first();

        $appNo = $student?->application_no ?? ($usercode ?: ('VU2026-' . ($cleanPhone ? substr($cleanPhone, -6) : 'APP')));
        $regNo = $student?->register_no ?? ($usercode ?: ('261FA' . ($cleanPhone ? substr($cleanPhone, -5) : '00001')));
        $displayName = $student?->student_name ?: ($user?->name ?: 'Applicant Candidate');
        $roleName = $user?->role_label ?: 'Student Candidate';
        $programCode = 'B.Tech - Computer Science & Engineering';
        $regulation = $student?->regulation ?: 'R25';

        // Fetch 10th and Inter educational info
        $edu10th = StudentEducationInfo::where(function ($q) use ($appNo, $regNo) {
            $q->where('application_no', $appNo)->orWhere('register_no', $regNo);
        })->where('qual_level', '10th')->first();

        $eduInter = StudentEducationInfo::where(function ($q) use ($appNo, $regNo) {
            $q->where('application_no', $appNo)->orWhere('register_no', $regNo);
        })->where('qual_level', 'Inter')->first();

        return view('dashboards::index', compact(
            'user',
            'student',
            'edu10th',
            'eduInter',
            'appNo',
            'regNo',
            'displayName',
            'roleName',
            'programCode',
            'regulation'
        ));
    }

    /**
     * Save/Update candidate's basic registration details and 10th + Inter academic info.
     */
    public function saveEducation(Request $request): RedirectResponse
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

        $appNo = $student?->application_no ?? ($usercode ?: ('VU2026-' . ($cleanPhone ? substr($cleanPhone, -6) : 'APP')));
        $regNo = $student?->register_no ?? ($usercode ?: ('261FA' . ($cleanPhone ? substr($cleanPhone, -5) : '00001')));

        $request->validate([
            // Basic details
            'student_name'   => ['nullable', 'string', 'max:150'],
            'dob'            => ['nullable', 'date'],
            'gender'         => ['nullable', 'string', 'in:Male,Female,Other'],
            'aadhar_no'      => ['nullable', 'string', 'max:20'],
            'abc_id'         => ['nullable', 'string', 'max:30'],
            'personal_email' => ['nullable', 'email', 'max:100'],

            // 10th details
            'ssc_board'       => ['nullable', 'string', 'max:100'],
            'ssc_school'      => ['nullable', 'string', 'max:150'],
            'ssc_hallticket'  => ['nullable', 'string', 'max:50'],
            'ssc_year'        => ['nullable', 'integer'],
            'ssc_marks'       => ['nullable', 'numeric'],
            'ssc_max_marks'   => ['nullable', 'numeric'],
            'ssc_cgpa'        => ['nullable', 'numeric'],
            'ssc_city'        => ['nullable', 'string', 'max:100'],
            'ssc_state'       => ['nullable', 'string', 'max:100'],

            // Inter details
            'inter_board'      => ['nullable', 'string', 'max:100'],
            'inter_college'    => ['nullable', 'string', 'max:150'],
            'inter_stream'     => ['nullable', 'string', 'max:50'],
            'inter_hallticket' => ['nullable', 'string', 'max:50'],
            'inter_year'       => ['nullable', 'integer'],
            'inter_marks'      => ['nullable', 'numeric'],
            'inter_max_marks'  => ['nullable', 'numeric'],
            'inter_percentage' => ['nullable', 'numeric'],
            'inter_city'       => ['nullable', 'string', 'max:100'],
            'inter_state'      => ['nullable', 'string', 'max:100'],
        ]);

        // 1. Update basic details in admissions.student_registrations
        if ($student) {
            $studentUpdate = [
                'log_ipaddress' => $request->ip(),
                'log_timestamp' => now(),
            ];
            if ($request->filled('student_name')) $studentUpdate['student_name'] = $request->student_name;
            if ($request->filled('dob')) $studentUpdate['dob'] = $request->dob;
            if ($request->filled('gender')) $studentUpdate['gender'] = $request->gender;
            if ($request->filled('aadhar_no')) $studentUpdate['aadhar_no'] = $request->aadhar_no;
            if ($request->filled('abc_id')) $studentUpdate['abc_id'] = $request->abc_id;
            if ($request->filled('personal_email')) $studentUpdate['personal_email'] = $request->personal_email;

            $student->update($studentUpdate);
        } else {
            // Create student registration record
            $student = StudentRegistration::create([
                'application_no' => $appNo,
                'register_no'    => $regNo,
                'student_name'   => $request->student_name ?: ('Candidate ' . ($cleanPhone ? substr($cleanPhone, -4) : 'User')),
                'dob'            => $request->dob ?: '2008-01-01',
                'gender'         => $request->gender ?: 'Male',
                'mobile'         => $cleanPhone ?: $user?->phnno,
                'personal_email' => $request->personal_email ?: $user?->personalmail,
                'aadhar_no'      => $request->aadhar_no,
                'abc_id'         => $request->abc_id,
                'regulation'     => 'R25',
                'admyear'        => 2026,
                'adm_batch_code' => '2026-27',
                'lock_status'    => 0,
                'log_userid'     => $usercode,
                'log_ipaddress'  => $request->ip(),
                'log_timestamp'  => now(),
            ]);
        }

        // 2. Save/Update 10th SSC Education Info
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

        // 3. Save/Update Inter / 12th Education Info
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

        return redirect()->route('dashboards.index')
            ->with('status', 'Admission profile & educational records (10th & Intermediate) saved successfully to database!');
    }
}
