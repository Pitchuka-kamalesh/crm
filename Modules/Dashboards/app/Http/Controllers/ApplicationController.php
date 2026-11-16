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
     * Show the A4 Admission Application Form in browser with Download PDF toolbar.
     */
    public function show(string $id): View
    {
        $student = $this->buildStudentApplicationData($id);
        $viewName = view()->exists('dashboards::application-form') ? 'dashboards::application-form' : 'application-form';

        return view($viewName, [
            'student' => $student,
            'logo' => asset('images/vignan-logo.png'),
            'downloadUrl' => route('application.download', ['id' => $id]),
        ]);
    }

    /**
     * Download or trigger browser A4 PDF print for the Application Form.
     */
    public function download(string $id): Response|View
    {
        $student = $this->buildStudentApplicationData($id);
        $viewName = view()->exists('dashboards::application-form') ? 'dashboards::application-form' : 'application-form';

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView($viewName, [
                'student' => $student,
                'logo' => public_path('images/vignan-logo.png'),
                'isPdf' => true,
            ])
                ->setOption([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'chroot' => public_path(),
                ])
                ->setPaper('a4', 'portrait')
                ->download('Application_Form_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $student->name) . '.pdf');
        }

        return view($viewName, [
            'student' => $student,
            'logo' => asset('images/vignan-logo.png'),
            'downloadUrl' => route('application.download', ['id' => $id]),
            'autoPrint' => true,
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

        $personalInfo = StudentPersonalInfo::where('applino', $applino)
            ->when(!empty($phone) && $phone !== 'N/A', fn($q) => $q->orWhere('phoneno', $phone))
            ->first();

        $academicDetails = StudentAcademicDetail::where('applino', $applino)
            ->when(!empty($phone) && $phone !== 'N/A', fn($q) => $q->orWhere('phoneno', $phone))
            ->first();

        $payDetail = PayDetail::where('applino', $applino)
            ->where('txnstatus', 'success')
            ->orderBy('feeid', 'desc')
            ->first();

        $receipt = ReceiptDetail::where('applino', $applino)
            ->orderBy('id', 'desc')
            ->first();

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
            $resolvedDistrict = District::where('DISTRICT_ID', (int) $districtVal)->value('DISTRICT_NAME');
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
            $examSchedule = \App\Models\ExamSchedule::query()
                ->where('applino', $applino)
                ->orWhere('phoneno', $phone)
                ->first();
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

        $isPaid = ($crmStudent?->stage1 === 'paid') || ($payDetail !== null) || ($receipt !== null);
        $uploadedDocs = \Modules\Dashboards\Http\Controllers\DashboardsController::getUploadedDocuments((string) $applino);
        $photoInfo = $uploadedDocs['photo'] ?? null;
        $photoPath = null;
        if ($photoInfo && !empty($photoInfo['path']) && is_file($photoInfo['path'])) {
            $photoPath = 'data:' . ($photoInfo['mime'] ?? 'image/jpeg') . ';base64,' . base64_encode((string) file_get_contents($photoInfo['path']));
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

        $studentCrmId = $personalInfo?->crmid ?: ($crmStudent?->crmid ?: (!empty($applino) ? ('V-SAT-' . substr($applino, -4)) : ''));

        return (object) [
            'id' => $applino,
            'student_id' => $studentCrmId,
            'name' => $crmStudent?->name ?? $user?->name ?? 'Candidate',
            'parent_name' => $parentName,
            'father_name' => $parentName,
            'parent_phone' => $parentPhone,
            'father_phone' => $parentPhone,
            'mother_name' => $motherName,
            'email' => filter_var((string) ($crmStudent?->emailid ?? ''), FILTER_VALIDATE_EMAIL)
                ? $crmStudent->emailid
                : ($user?->personalmail ?: ($user?->email ?: '')),
            'phone' => $phone,
            'applied_at' => $crmStudent?->log_timestamp
                ? \Carbon\Carbon::parse($crmStudent->log_timestamp)->format('d/m/Y')
                : now()->format('d/m/Y'),
            'dob' => $personalInfo?->dob ?: '',
            'gender' => $personalInfo?->gender ?: '',
            'category' => $personalInfo?->caste_category ?: '',
            'program' => $programName,
            'branch' => $branchName,
            'campus' => $campusName,
            'school_name' => $academicDetails?->tenth_school_name ?: ($academicDetails?->pu2_college_name ?: ''),
            'board' => $academicDetails?->tenth_study_board ?: ($academicDetails?->pu2_study_board ?: ''),
            'percentage' => $tenthPercentage ?: $percentage,
            'passing_year' => $academicDetails?->tenth_passed_year ?: ($academicDetails?->pu2_passed_year ?: ''),
            'roll_number' => $academicDetails?->tenth_roll_no ?: ($academicDetails?->pu2_roll_no ?: ''),
            'academic' => (object) [
                'ssc_board' => $academicDetails?->tenth_study_board ?: 'State Board',
                'ssc_school' => $academicDetails?->tenth_school_name ?: '',
                'ssc_year' => $academicDetails?->tenth_passed_year ?: '',
                'ssc_roll' => $academicDetails?->tenth_roll_no ?: '',
                'ssc_marks_conducted' => $academicDetails?->tenth_marks_conducted ?: '',
                'ssc_marks_earned' => $academicDetails?->tenth_marks_earned ?: '',
                'ssc_percentage' => $tenthPercentage > 0 ? number_format($tenthPercentage, 2) . ' %' : 'N/A',
                'inter_college' => $academicDetails?->pu2_college_name ?: '',
                'inter_board' => $academicDetails?->pu2_study_board ?: 'Board of Intermediate',
                'inter_year' => $academicDetails?->pu2_passed_year ?: '',
                'inter_roll' => $academicDetails?->pu2_roll_no ?: '',
                'inter_marks_conducted' => $academicDetails?->pu2_marks_conducted ?: '',
                'inter_marks_earned' => $academicDetails?->pu2_marks_earned ?: '',
                'inter_percentage' => $interPercentage > 0 ? number_format($interPercentage, 2) . ' %' : 'N/A',
            ],
            'street' => $villageVal ?: ($personalInfo?->village ?: ''),
            'address' => $villageVal ?: ($personalInfo?->village ?: ''),
            'city' => $cityVal ?: ($personalInfo?->city_mandal ?: ''),
            'district' => $districtVal,
            'state' => $stateVal ?: '',
            'pincode' => $pincodeVal ?: ($personalInfo?->pincode ?: ''),
            'country' => $countryVal ?: '',
            'exam_date' => $examSchedule?->examdate ? \Carbon\Carbon::parse($examSchedule->examdate)->format('d M Y') : 'Booked in Stage 5',
            'exam_slot' => $examSchedule?->exam_slot ?? 'Session Scheduled',
            'payment_amount' => (float) ($payDetail?->paidamount ?? $receipt?->feepayable ?? ($isPaid ? 200.00 : 700.00)),
            'payment_method' => $payDetail?->paytype ?? $receipt?->paytype ?? 'PayU (Online)',
            'transaction_id' => $payDetail?->txnpayuid ?? $payDetail?->txncrmid ?? $receipt?->receiptno ?? ($isPaid ? ('TXN_' . $applino) : 'PENDING'),
            'payment_status' => $isPaid ? 'Completed' : 'Pending',
            'photo_path' => $photoPath,
            'documents' => $docsState,
            'uploaded_files' => $uploadedDocs,
        ];
    }
}

