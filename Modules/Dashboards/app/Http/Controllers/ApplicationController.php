<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Country;
use App\Models\CrmStudentRegistration;
use App\Models\PayDetail;
use App\Models\ReceiptDetail;
use App\Models\State;
use App\Models\StudentAcademicDetail;
use App\Models\StudentPersonalInfo;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApplicationController extends Controller
{
    /**
     * Resolve the base64 data URI for the university logo.
     */
    public static function getLogoBase64(): string
    {
        $candidates = [
            public_path('images/vignan_logo.jpeg'),
            base_path('public/images/vignan_logo.jpeg'),
            __DIR__ . '/../../../../../public/images/vignan_logo.jpeg',
            public_path('images/vignan-banner-logo.jpg'),
            base_path('public/images/vignan-banner-logo.jpg'),
            __DIR__ . '/../../../../../public/images/vignan-banner-logo.jpg',
        ];

        foreach ($candidates as $filePath) {
            if (!empty($filePath) && is_file($filePath)) {
                $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                $content = @file_get_contents($filePath);
                if (!empty($content)) {
                    return 'data:' . $mime . ';base64,' . base64_encode($content);
                }
            }
        }

        return asset('images/vignan_logo.jpeg');
    }

    /**
     * Show the A4 Admission Application Form in browser with Download PDF toolbar.
     */
    public function show(string $id): View
    {
        $student = $this->buildStudentApplicationData($id);
        $viewName = view()->exists('dashboards::application-form') ? 'dashboards::application-form' : 'application-form';

        $logoBase64 = self::getLogoBase64();

        return view($viewName, [
            'student' => $student,
            'logo' => $logoBase64,
            'downloadUrl' => route('application.download', ['id' => $id]),
            'autoPrint' => request()->boolean('print'),
        ]);
    }

    /**
     * Download or trigger browser A4 PDF print for the Application Form.
     */
    public function download(string $id): Response|View
    {
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        $student = $this->buildStudentApplicationData($id);
        $viewName = view()->exists('dashboards::application-form') ? 'dashboards::application-form' : 'application-form';

        if (class_exists(\Dompdf\Dompdf::class)) {
            try {
                $logoBase64 = self::getLogoBase64();

                $html = view($viewName, [
                    'student' => $student,
                    'logo' => $logoBase64,
                    'isPdf' => true,
                ])->render();

                $tempDir = storage_path('app/temp');
                if (!is_dir($tempDir)) {
                    @mkdir($tempDir, 0777, true);
                }

                $options = new \Dompdf\Options();
                $options->set('isHtml5ParserEnabled', true);
                $options->set('isRemoteEnabled', true);
                $options->set('isFontSubsettingEnabled', true);
                $options->set('defaultFont', 'Helvetica');
                $options->set('dpi', 96);
                $options->set('tempDir', $tempDir);
                $options->set('chroot', [public_path(), base_path(), $tempDir, storage_path()]);

                $dompdf = new \Dompdf\Dompdf($options);
                $dompdf->loadHtml($html);
                $dompdf->setPaper('a4', 'portrait');

                $dompdf->render();

                $renderedPdf = $dompdf->output();

                $filename = 'Application_Form_' . ($student->id ?: 'VU') . '_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $student->name ?: 'Candidate') . '.pdf';

                return response($renderedPdf, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('DomPDF generation error: ' . $e->getMessage());
            }
        }

        return view($viewName, [
            'student' => $student,
            'logo' => self::getLogoBase64(),
            'downloadUrl' => route('application.download', ['id' => $id]),
            'autoPrint' => false,
        ]);
    }

    /**
     * Gather candidate registration, personal, academic, and payment records via Eloquent.
     */
    public static function buildStudentApplicationData(string $id): object
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', (string) ($user?->phnno ?? ''));
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $crmStudent = CrmStudentRegistration::with(['program', 'branch', 'state', 'country'])
            ->where('applinano', $id)
            ->first();

        if (!$crmStudent && $cleanPhone) {
            $crmStudent = CrmStudentRegistration::with(['program', 'branch', 'state', 'country'])
                ->where('phoneno', $cleanPhone)
                ->first();
        }

        $applino = $crmStudent?->applinano ?? (!empty($id) && $id !== '0' ? $id : ($usercode ?: ''));
        $phone = $crmStudent?->phoneno ?? ($cleanPhone ?: '');

        // Fetch real-time Personal Info with exact applino priority (avoids picking up duplicate phone records)
        $personalInfo = null;
        if (!empty($applino)) {
            $personalInfo = StudentPersonalInfo::where('applino', $applino)->first();
        }
        if (!$personalInfo && !empty($phone) && $phone !== 'N/A') {
            $personalInfo = StudentPersonalInfo::where('phoneno', $phone)->orderBy('applino', 'desc')->first();
        }

        // Fetch real-time Academic Details with exact applino priority
        $academicDetails = null;
        if (!empty($applino)) {
            $academicDetails = StudentAcademicDetail::where('applino', $applino)->first();
        }
        if (!$academicDetails && !empty($phone) && $phone !== 'N/A') {
            $academicDetails = StudentAcademicDetail::where('phoneno', $phone)->orderBy('applino', 'desc')->first();
        }

        // Fetch real-time Payment details
        $payDetail = null;
        if (!empty($applino)) {
            $payDetail = PayDetail::where('applino', $applino)
                ->where('txnstatus', 'success')
                ->orderBy('feeid', 'desc')
                ->first();
        }
        if (!$payDetail && !empty($phone) && $phone !== 'N/A') {
            $payDetail = PayDetail::where('applino', 'like', "%{$phone}%")
                ->where('txnstatus', 'success')
                ->orderBy('feeid', 'desc')
                ->first();
        }

        // Fetch real-time Receipt details
        $receipt = null;
        if (!empty($applino)) {
            $receipt = ReceiptDetail::where('applino', $applino)
                ->orderBy('id', 'desc')
                ->first();
        }
        if (!$receipt && !empty($phone) && $phone !== 'N/A') {
            $receipt = ReceiptDetail::where('applino', 'like', "%{$phone}%")
                ->orderBy('id', 'desc')
                ->first();
        }

        $percentage = 0.0;
        if ($academicDetails) {
            if ($academicDetails->tenth_marks_type === 'CGPA' && (float) $academicDetails->tenth_cgpa > 0) {
                $percentage = (float) $academicDetails->tenth_cgpa * 9.5;
            } elseif ((float) $academicDetails->tenth_marks > 0) {
                $rawMarks = (float) $academicDetails->tenth_marks;
                $percentage = $rawMarks > 100 ? ($rawMarks / 600) * 100 : $rawMarks;
            }
        }

        $stateVal = $personalInfo?->state ?: ($crmStudent?->state?->state_name ?? '');
        if (is_numeric($stateVal)) {
            $resolvedState = State::where('state_id', (int) $stateVal)->value('state_name');
            $stateVal = $resolvedState ?: $stateVal;
        }

        $countryVal = $personalInfo?->country ?: ($crmStudent?->country?->country_name ?? 'India');
        if (is_numeric($countryVal)) {
            $resolvedCountry = Country::where('country_id', (int) $countryVal)->value('country_name');
            $countryVal = $resolvedCountry ?: 'India';
        }

        $parentName = $personalInfo?->parent_name ?: ($personalInfo?->father_name ?? '');
        $parentPhone = $personalInfo?->parentphono ?: ($personalInfo?->father_phone ?? '');
        $motherName = $personalInfo?->mother_name ?? '';
        
        $districtVal = $personalInfo?->district ?: ($crmStudent?->district ?? '');
        if (is_numeric($districtVal)) {
            $resolvedDistrict = District::cachedName((int) $districtVal);
            $districtVal = $resolvedDistrict ?: $districtVal;
        }

        $villageVal = $personalInfo?->village ?: ($crmStudent?->village ?? ($crmStudent?->doorno ?? ''));
        $cityVal = $personalInfo?->city_mandal ?: ($crmStudent?->mandal ?? '');
        $pincodeVal = $personalInfo?->pincode ?: ($crmStudent?->pincode ?? '');

        $programObj = $crmStudent?->program;
        $programName = $programObj ? ($programObj->program_short_name . ' - ' . $programObj->program_long_name) : ($crmStudent?->programlevel ?: '');
        $branchObj = $crmStudent?->branch;
        $branchName = $branchObj ? $branchObj->branch_long_name : '';
        $campusCode = $crmStudent?->campuscode;
        $campusObj = $campusCode ? \App\Models\Campus::where('campus_code', $campusCode)->first() : null;
        $campusName = $campusObj ? $campusObj->campus_short_name : ($campusCode ?: 'Vignan Main Campus');

        $examSchedule = null;
        try {
            if (!empty($applino)) {
                $examSchedule = \App\Models\ExamSchedule::where('applino', $applino)->first();
            }
            if (!$examSchedule && !empty($phone) && $phone !== 'N/A') {
                $examSchedule = \App\Models\ExamSchedule::where('phoneno', $phone)->first();
            }
        } catch (\Throwable $e) {}

        $tenthPercentage = (float)($academicDetails?->tenth_percentage ?? 0);
        if ($tenthPercentage <= 0) {
            if ($academicDetails?->tenth_marks_type === 'CGPA' && (float) $academicDetails->tenth_cgpa > 0) {
                $tenthPercentage = (float) $academicDetails->tenth_cgpa * 9.5;
            } elseif ((float) $academicDetails?->tenth_marks_earned > 0 && (float) $academicDetails?->tenth_marks_conducted > 0) {
                $tenthPercentage = ((float) $academicDetails->tenth_marks_earned / (float) $academicDetails->tenth_marks_conducted) * 100;
            } elseif ((float) $academicDetails?->tenth_marks > 0) {
                $tenthPercentage = (float) $academicDetails->tenth_marks > 100 ? ((float) $academicDetails->tenth_marks / 600) * 100 : (float) $academicDetails->tenth_marks;
            }
        }

        $interPercentage = (float)($academicDetails?->pu2_percentage ?? 0);
        if ($interPercentage <= 0) {
            if ((float) $academicDetails?->pu2_marks_earned > 0 && (float) $academicDetails?->pu2_marks_conducted > 0) {
                $interPercentage = ((float) $academicDetails->pu2_marks_earned / (float) $academicDetails->pu2_marks_conducted) * 100;
            } elseif ((float) $academicDetails?->pu2_marks > 0) {
                $interPercentage = (float) $academicDetails->pu2_marks > 100 ? ((float) $academicDetails->pu2_marks / 1000) * 100 : (float) $academicDetails->pu2_marks;
            }
        }

        $ugPercentage = (float)($academicDetails?->ug_percentage ?? 0);
        if ($ugPercentage <= 0) {
            if ((float) $academicDetails?->ug_marks_earned > 0 && (float) $academicDetails?->ug_marks_conducted > 0) {
                $ugPercentage = ((float) $academicDetails->ug_marks_earned / (float) $academicDetails->ug_marks_conducted) * 100;
            } elseif ((float) $academicDetails?->ug_marks > 0) {
                $rawUg = (float) $academicDetails->ug_marks;
                $ugPercentage = $rawUg <= 10 ? ($rawUg * 9.5) : ($rawUg > 100 ? ($rawUg / 1000) * 100 : $rawUg);
            }
        }

        $pgPercentage = (float)($academicDetails?->pg_percentage ?? 0);
        if ($pgPercentage <= 0) {
            if ((float) $academicDetails?->pg_marks_earned > 0 && (float) $academicDetails?->pg_marks_conducted > 0) {
                $pgPercentage = ((float) $academicDetails->pg_marks_earned / (float) $academicDetails->pg_marks_conducted) * 100;
            } elseif ((float) $academicDetails?->pg_marks > 0) {
                $rawPg = (float) $academicDetails->pg_marks;
                $pgPercentage = $rawPg <= 10 ? ($rawPg * 9.5) : ($rawPg > 100 ? ($rawPg / 1000) * 100 : $rawPg);
            }
        }

        $pg2Percentage = (float)($academicDetails?->pg2_percentage ?? 0);
        if ($pg2Percentage <= 0) {
            if ((float) $academicDetails?->pg2_marks_earned > 0 && (float) $academicDetails?->pg2_marks_conducted > 0) {
                $pg2Percentage = ((float) $academicDetails->pg2_marks_earned / (float) $academicDetails->pg2_marks_conducted) * 100;
            } elseif ((float) $academicDetails?->pg2_marks > 0) {
                $rawPg2 = (float) $academicDetails->pg2_marks;
                $pg2Percentage = $rawPg2 <= 10 ? ($rawPg2 * 9.5) : ($rawPg2 > 100 ? ($rawPg2 / 1000) * 100 : $rawPg2);
            }
        }

        // Determine Degree Level (UG / M.Tech / PhD)
        $progLevel = strtoupper(trim((string)($crmStudent?->programlevel ?? '')));
        $progShort = strtoupper($programObj?->program_short_name ?? '');
        $progLong = strtoupper($programObj?->program_long_name ?? '');

        if (
            $progLevel === 'PHD' ||
            $progLevel === 'DOCTORAL' ||
            str_contains($progShort, 'PHD') ||
            str_contains($progShort, 'PH.D') ||
            str_contains($progLong, 'DOCTOR') ||
            !empty($academicDetails?->pg2_college_name) ||
            !empty($academicDetails?->pg_college_name)
        ) {
            $degreeLevel = 'phd';
        } elseif (
            $progLevel === 'PG' ||
            str_contains($progShort, 'M.TECH') ||
            str_contains($progShort, 'MTECH') ||
            str_contains($progLong, 'MASTER') ||
            in_array($progShort, ['MCA', 'MBA', 'M.PHARM', 'M.SC', 'M.A']) ||
            !empty($academicDetails?->ug_college_name)
        ) {
            $degreeLevel = 'mtech';
        } else {
            $degreeLevel = 'btech';
        }

        $isPaid = ($crmStudent?->stage4 === 'paid') || ($crmStudent?->stage1 === 'paid') || ($payDetail !== null) || ($receipt !== null);
        $uploadedDocs = \Modules\Dashboards\Http\Controllers\DashboardsController::getUploadedDocuments((string) $applino);
        $photoInfo = $uploadedDocs['photo'] ?? null;
        $photoPath = null;
        $absPhotoPath = null;

        if ($photoInfo) {
            $candidates = [
                $photoInfo['path'] ?? '',
                $photoInfo['abs_path'] ?? '',
                !empty($photoInfo['path']) ? public_path($photoInfo['path']) : '',
                !empty($photoInfo['filename']) ? public_path('uploads/documents/' . $applino . '/' . $photoInfo['filename']) : '',
            ];
            foreach ($candidates as $cand) {
                if (!empty($cand) && is_file($cand)) {
                    $absPhotoPath = $cand;
                    break;
                }
            }
        }

        // Direct directory scan fallback if photo wasn't indexed yet
        if (!$absPhotoPath && !empty($applino)) {
            $cleanApp = preg_replace('/[^A-Za-z0-9_-]/', '', (string)$applino);
            $appDir = public_path('uploads/documents/' . $cleanApp);
            if (is_dir($appDir)) {
                $photoFiles = glob($appDir . DIRECTORY_SEPARATOR . 'photo_*');
                if (!empty($photoFiles) && is_file($photoFiles[0])) {
                    $absPhotoPath = $photoFiles[0];
                }
            }
        }

        if ($absPhotoPath && is_file($absPhotoPath)) {
            $mime = mime_content_type($absPhotoPath) ?: ($photoInfo['mime'] ?? 'image/jpeg');
            $photoPath = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($absPhotoPath));
        } elseif ($photoInfo && !empty($photoInfo['url'])) {
            $photoPath = $photoInfo['url'];
        }

        $docKeys = ['marksheet10', 'marksheet12', 'aadhaar', 'photo', 'tc', 'study_conduct'];
        $docsState = [];
        foreach ($docKeys as $dk) {
            if (!empty($uploadedDocs[$dk])) {
                $ext = strtoupper($uploadedDocs[$dk]['ext'] ?? 'PDF');
                $docsState[$dk] = $ext;
            } else {
                $docsState[$dk] = null;
            }
        }

        $studentCrmId = $crmStudent?->crmid ?: ($personalInfo?->crmid ?: (!empty($applino) ? ('V-SAT-' . substr($applino, -4)) : ''));

        $paidAmountVal = 0.00;
        if ($payDetail !== null) {
            $paidAmountVal = (float) $payDetail->paidamount;
        } elseif ($receipt !== null) {
            $paidAmountVal = (float) $receipt->feepayable;
        } elseif ($isPaid) {
            $paidAmountVal = 0.00;
        }

        return (object) [
            'id' => $applino,
            'student_id' => $studentCrmId ?: '-',
            'name' => $crmStudent?->name ?? $user?->name ?? 'Candidate',
            'parent_name' => $parentName ?: '-',
            'father_name' => $parentName ?: '-',
            'parent_phone' => $parentPhone ?: '-',
            'father_phone' => $parentPhone ?: '-',
            'mother_name' => $motherName ?: '-',
            'email' => filter_var((string) ($crmStudent?->emailid ?? ''), FILTER_VALIDATE_EMAIL)
                ? $crmStudent->emailid
                : ($user?->personalmail ?: ($user?->email ?: '-')),
            'phone' => $phone ?: '-',
            'applied_at' => $crmStudent?->log_timestamp
                ? \Carbon\Carbon::parse($crmStudent->log_timestamp)->format('d/m/Y')
                : now()->format('d/m/Y'),
            'dob' => $personalInfo?->dob ?: '-',
            'gender' => $personalInfo?->gender ?: '-',
            'category' => $personalInfo?->caste_category ?: ($personalInfo?->caste ?: '-'),
            'program' => $programName ?: '-',
            'branch' => $branchName ?: '-',
            'campus' => $campusName ?: 'VFSTR, Guntur',
            'school_name' => $academicDetails?->tenth_school_name ?: '-',
            'board' => $academicDetails?->tenth_study_board ?: '-',
            'percentage' => $tenthPercentage ?: $percentage,
            'passing_year' => $academicDetails?->tenth_passed_year ?: '-',
            'roll_number' => $academicDetails?->tenth_roll_no ?: '-',
            'academic' => (object) [
                'ssc_board' => !empty($academicDetails?->tenth_study_board) ? $academicDetails->tenth_study_board : '-',
                'ssc_school' => !empty($academicDetails?->tenth_school_name) ? $academicDetails->tenth_school_name : '-',
                'ssc_year' => !empty($academicDetails?->tenth_passed_year) ? $academicDetails->tenth_passed_year : '-',
                'ssc_roll' => !empty($academicDetails?->tenth_roll_no) ? $academicDetails->tenth_roll_no : '-',
                'ssc_marks_conducted' => !empty($academicDetails?->tenth_marks_conducted) ? $academicDetails->tenth_marks_conducted : '-',
                'ssc_marks_earned' => !empty($academicDetails?->tenth_marks_earned) ? $academicDetails->tenth_marks_earned : '-',
                'ssc_percentage' => ($tenthPercentage && $tenthPercentage > 0) ? number_format($tenthPercentage, 2) . ' %' : '-',
                
                'inter_college' => !empty($academicDetails?->pu2_college_name) ? ($academicDetails->pu2_college_name . (!empty($academicDetails?->pu2_campus_location) ? ' (' . $academicDetails->pu2_campus_location . ')' : '')) : '-',
                'inter_campus_location' => $academicDetails?->pu2_campus_location ?? '-',
                'inter_board' => !empty($academicDetails?->pu2_study_board) ? $academicDetails->pu2_study_board : '-',
                'inter_group' => !empty($academicDetails?->pu2_group) ? $academicDetails->pu2_group : '-',
                                
                'inter_year' => !empty($academicDetails?->pu2_passed_year) ? $academicDetails->pu2_passed_year : '-',
                'inter_roll' => !empty($academicDetails?->pu2_roll_no) ? $academicDetails->pu2_roll_no : '-',
                'inter_marks_conducted' => !empty($academicDetails?->pu2_marks_conducted) ? $academicDetails->pu2_marks_conducted : '-',
                'inter_marks_earned' => !empty($academicDetails?->pu2_marks_earned) ? $academicDetails->pu2_marks_earned : '-',
                'inter_percentage' => ($interPercentage && $interPercentage > 0) ? number_format($interPercentage, 2) . ' %' : '-',

                // Undergraduate (UG / Degree / B.Tech)
                'ug_college' => !empty($academicDetails?->ug_college_name) ? $academicDetails->ug_college_name : '-',
                'ug_board' => !empty($academicDetails?->ug_university_board) ? $academicDetails->ug_university_board : '-',
                'ug_year' => !empty($academicDetails?->ug_passed_year) ? $academicDetails->ug_passed_year : '-',
                'ug_roll' => !empty($academicDetails?->ug_roll_no) ? $academicDetails->ug_roll_no : '-',
                'ug_marks_conducted' => !empty($academicDetails?->ug_marks_conducted) ? $academicDetails->ug_marks_conducted : '-',
                'ug_marks_earned' => !empty($academicDetails?->ug_marks_earned) ? $academicDetails->ug_marks_earned : '-',
                'ug_percentage' => ($ugPercentage && $ugPercentage > 0) ? number_format($ugPercentage, 2) . ' %' : ($academicDetails?->ug_cgpa ? $academicDetails->ug_cgpa . ' CGPA' : '-'),
                'ug_status' => !empty($academicDetails?->ug_current_status) ? $academicDetails->ug_current_status : '-',

                // Postgraduate 1 (PG 1 / Master's / M.Tech)
                'pg_college' => !empty($academicDetails?->pg_college_name) ? $academicDetails->pg_college_name : '-',
                'pg_board' => !empty($academicDetails?->pg_university_board) ? $academicDetails->pg_university_board : '-',
                'pg_year' => !empty($academicDetails?->pg_passed_year) ? $academicDetails->pg_passed_year : '-',
                'pg_roll' => !empty($academicDetails?->pg_roll_no) ? $academicDetails->pg_roll_no : '-',
                'pg_marks_conducted' => !empty($academicDetails?->pg_marks_conducted) ? $academicDetails->pg_marks_conducted : '-',
                'pg_marks_earned' => !empty($academicDetails?->pg_marks_earned) ? $academicDetails->pg_marks_earned : '-',
                'pg_percentage' => ($pgPercentage && $pgPercentage > 0) ? number_format($pgPercentage, 2) . ' %' : ($academicDetails?->pg_cgpa ? $academicDetails->pg_cgpa . ' CGPA' : '-'),
                'pg_status' => !empty($academicDetails?->pg_current_status) ? $academicDetails->pg_current_status : '-',
                'pg_study_type' => !empty($academicDetails?->pg_study_type) ? $academicDetails->pg_study_type : 'Regular',
                'pg1_doc_type' => !empty($academicDetails?->pg1_doc_type) ? $academicDetails->pg1_doc_type : 'BOTH',

                // Postgraduate 2 (PG 2 / Ph.D)
                'pg2_college' => !empty($academicDetails?->pg2_college_name) ? $academicDetails->pg2_college_name : '-',
                'pg2_board' => !empty($academicDetails?->pg2_university_board) ? $academicDetails->pg2_university_board : '-',
                'pg2_year' => !empty($academicDetails?->pg2_passed_year) ? $academicDetails->pg2_passed_year : '-',
                'pg2_roll' => !empty($academicDetails?->pg2_roll_no) ? $academicDetails->pg2_roll_no : '-',
                'pg2_marks_conducted' => !empty($academicDetails?->pg2_marks_conducted) ? $academicDetails->pg2_marks_conducted : '-',
                'pg2_marks_earned' => !empty($academicDetails?->pg2_marks_earned) ? $academicDetails->pg2_marks_earned : '-',
                'pg2_percentage' => ($pg2Percentage && $pg2Percentage > 0) ? number_format($pg2Percentage, 2) . ' %' : ($academicDetails?->pg2_cgpa ? $academicDetails->pg2_cgpa . ' CGPA' : '-'),
                'pg2_status' => !empty($academicDetails?->pg2_current_status) ? $academicDetails->pg2_current_status : '-',
                'pg2_study_type' => !empty($academicDetails?->pg2_study_type) ? $academicDetails->pg2_study_type : 'Regular',
                'pg2_doc_type' => !empty($academicDetails?->pg2_doc_type) ? $academicDetails->pg2_doc_type : 'BOTH',
            ],
            'academic_year' => \Modules\Crm\Models\CrmStudentRegistration::getAcademicYearInfo()['academic_year_full'],
            'academic_year_short' => \Modules\Crm\Models\CrmStudentRegistration::getAcademicYearInfo()['academic_year_short'],
            'degree_level' => $degreeLevel,
            'street' => $villageVal ?: ($personalInfo?->village ?: ''),
            'address' => $villageVal ?: ($personalInfo?->village ?: ''),
            'city' => $cityVal ?: ($personalInfo?->city_mandal ?: ''),
            'district' => $districtVal,
            'state' => $stateVal ?: '',
            'pincode' => $pincodeVal ?: ($personalInfo?->pincode ?: ''),
            'country' => $countryVal ?: '',
            'exam_date' => $examSchedule?->examdate ? \Carbon\Carbon::parse($examSchedule->examdate)->format('d M Y') : 'Booked in Stage 5',
            'exam_slot' => $examSchedule?->exam_slot ?? 'Session Scheduled',
            'payment_amount' => $paidAmountVal,
            'payment_method' => $payDetail?->paytype ?? $receipt?->paytype ?? ($isPaid ? 'Online Settlement' : 'Unpaid'),
            'transaction_id' => $payDetail?->txnpayuid ?? $payDetail?->txncrmid ?? $receipt?->receiptno ?? ($isPaid ? ('TXN_' . $applino) : 'PENDING'),
            'payment_status' => $isPaid ? 'Completed' : 'Pending',
            'photo_path' => $photoPath,
            'documents' => $docsState,
            'uploaded_files' => $uploadedDocs,
        ];
    }
}

