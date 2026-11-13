<?php

namespace App\Http\Controllers;

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

        return view('application-form', [
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

        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return \Barryvdh\DomPDF\Facade\Pdf::loadView('application-form', [
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

        return view('application-form', [
            'student' => $student,
            'logo' => asset('images/vignan-logo.png'),
            'downloadUrl' => route('application.download', ['id' => $id]),
            'autoPrint' => true,
        ]);
    }

    /**
     * Gather candidate registration, personal, academic, and payment records via Eloquent.
     */
    private function buildStudentApplicationData(string $id): object
    {
        $user = Auth::user();
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

        $applino = $crmStudent?->applinano ?? $id;
        $phone = $crmStudent?->phoneno ?? $cleanPhone ?: 'N/A';

        $personalInfo = StudentPersonalInfo::where('applino', $applino)
            ->when($phone !== 'N/A', fn($q) => $q->orWhere('phoneno', $phone))
            ->first();

        $academicDetails = StudentAcademicDetail::where('applino', $applino)
            ->when($phone !== 'N/A', fn($q) => $q->orWhere('phoneno', $phone))
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

        $stateVal = $personalInfo?->state ?: ($crmStudent?->state?->state_name ?? 'Andhra Pradesh');
        if (is_numeric($stateVal)) {
            $resolvedState = State::where('state_id', (int) $stateVal)->value('state_name');
            $stateVal = $resolvedState ?: $stateVal;
        }

        $countryVal = $personalInfo?->country ?: ($crmStudent?->country?->country_name ?? 'India');
        if (is_numeric($countryVal)) {
            $resolvedCountry = Country::where('country_id', (int) $countryVal)->value('country_name');
            $countryVal = $resolvedCountry ?: 'India';
        }

        $isPaid = ($crmStudent?->stage1 === 'paid') || ($payDetail !== null) || ($receipt !== null);
        $uploadedDocs = \Modules\Dashboards\Http\Controllers\DashboardsController::getUploadedDocuments((string) $applino);
        $photoInfo = $uploadedDocs['photo'] ?? null;
        $photoPath = null;
        if ($photoInfo && in_array($photoInfo['ext'], ['jpg', 'jpeg', 'png'], true) && is_file($photoInfo['path'])) {
            $photoPath = 'data:' . $photoInfo['mime'] . ';base64,' . base64_encode((string) file_get_contents($photoInfo['path']));
        }

        return (object) [
            'id' => $applino,
            'student_id' => $personalInfo?->crmid ?: ($crmStudent?->crmid ?: $applino),
            'name' => $crmStudent?->name ?? $user?->name ?? 'Candidate',
            'email' => filter_var((string) ($crmStudent?->emailid ?? ''), FILTER_VALIDATE_EMAIL)
                ? $crmStudent->emailid
                : ($user?->personalmail ?: 'admissions@vignan.ac.in'),
            'phone' => $phone,
            'applied_at' => $crmStudent?->log_timestamp
                ? \Carbon\Carbon::parse($crmStudent->log_timestamp)->format('d/m/Y')
                : now()->format('d/m/Y'),
            'dob' => $personalInfo?->dob ?? '',
            'gender' => $personalInfo?->gender ?? 'Not Specified',
            'category' => $personalInfo?->caste_category ?? 'General / OC',
            'school_name' => $academicDetails?->tenth_school_name ?? ($academicDetails?->pu2_college_name ?? 'N/A'),
            'board' => $academicDetails?->tenth_study_board ?? ($academicDetails?->pu2_study_board ?? 'BSEAP'),
            'percentage' => $percentage,
            'passing_year' => $academicDetails?->tenth_passed_year ?? ($academicDetails?->pu2_passed_year ?? '2024'),
            'roll_number' => $academicDetails?->tenth_roll_no ?? ($academicDetails?->pu2_roll_no ?? 'N/A'),
            'street' => $personalInfo?->village ?? 'Main Road',
            'city' => $personalInfo?->city_mandal ?? 'Guntur',
            'state' => $stateVal,
            'pincode' => $personalInfo?->pincode ?? '522213',
            'country' => $countryVal,
            'payment_amount' => (float) ($payDetail?->paidamount ?? $receipt?->feepayable ?? ($isPaid ? 700.00 : 1200.00)),
            'payment_method' => $payDetail?->paytype ?? $receipt?->paytype ?? 'PayU Secure Hosted',
            'transaction_id' => $payDetail?->txnpayuid ?? $payDetail?->txncrmid ?? $receipt?->receiptno ?? 'PENDING',
            'payment_status' => $isPaid ? 'Completed' : 'Pending',
            'photo_path' => $photoPath,
            'documents' => [
                'marksheet10' => $uploadedDocs['marksheet10']['mime'] ?? null,
                'marksheet12' => $uploadedDocs['marksheet12']['mime'] ?? null,
                'aadhaar' => $uploadedDocs['aadhaar']['mime'] ?? null,
                'photo' => $uploadedDocs['photo']['mime'] ?? null,
                'tc' => $uploadedDocs['tc']['mime'] ?? null,
                'study_conduct' => $uploadedDocs['study_conduct']['mime'] ?? null,
            ],
        ];
    }
}

