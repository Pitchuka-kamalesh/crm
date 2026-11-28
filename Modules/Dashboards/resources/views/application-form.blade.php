{{--
    resources/views/application-form.blade.php
    Official Vignan University Admission Information Form.
    A4 single-page layout with balanced spacing for B.Tech candidates.
--}}
@php
    if (empty($logo) || !str_starts_with((string)$logo, 'data:image')) {
        $logoCandidates = [
            public_path('images/vignan_logo.jpeg'),
            base_path('public/images/vignan_logo.jpeg'),
            __DIR__ . '/../../../../../public/images/vignan_logo.jpeg',
            public_path('images/vignan-banner-logo.jpg'),
            base_path('public/images/vignan-banner-logo.jpg'),
            __DIR__ . '/../../../../../public/images/vignan-banner-logo.jpg',
        ];
        foreach ($logoCandidates as $cand) {
            if (!empty($cand) && is_file($cand)) {
                $ext = strtolower(pathinfo($cand, PATHINFO_EXTENSION));
                $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                $cnt = @file_get_contents($cand);
                if (!empty($cnt)) {
                    $logo = 'data:' . $mime . ';base64,' . base64_encode($cnt);
                    break;
                }
            }
        }
    }
    $logo = $logo ?? asset('images/vignan_logo.jpeg');

    $progUpper = strtoupper(trim((string)($student->program ?? '')));
    $levelUpper = strtoupper(trim((string)($student->degree_level ?? '')));
    $crmUpper = strtoupper(trim((string)($student->student_id ?? '')));
    $isPhd = $levelUpper === 'PHD'
        || $levelUpper === 'DOCTORAL'
        || str_contains($progUpper, 'PHD')
        || str_contains($progUpper, 'PH.D')
        || str_contains($progUpper, 'DOCTOR')
        || str_starts_with($crmUpper, 'PHD');

    $isPg = !$isPhd && (
        $levelUpper === 'MTECH'
        || $levelUpper === 'PG'
        || str_contains($progUpper, 'M.TECH')
        || str_contains($progUpper, 'MTECH')
        || str_contains($progUpper, 'MASTER')
        || str_contains($progUpper, 'MBA')
        || str_contains($progUpper, 'MCA')
    );

    $isBtech = !$isPg && !$isPhd;

    $dob = 'N/A';
    if (!empty($student->dob) && $student->dob !== '-') {
        try {
            $dob = \Carbon\Carbon::parse($student->dob)->format('d M Y');
        } catch (\Throwable $e) {
            $dob = (string) $student->dob;
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Application Form - {{ $student->name ?? 'Candidate' }}</title>
<style>
    @page {
        size: A4 portrait;
        margin: {{ $isBtech ? '7mm 8mm 6mm 8mm' : '5mm 6mm 4mm 6mm' }};
    }
    *, *::before, *::after {
        box-sizing: border-box;
    }
    html, body {
        margin: 0;
        padding: 0;
        width: 100%;
        background: {{ !empty($isPdf) ? '#ffffff' : '#f1f5f9' }};
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #1B2430;
        font-size: {{ $isBtech ? '10.5px' : '10px' }};
        line-height: 1.35;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .page-wrap {
        width: 100%;
        max-width: 200mm;
        margin: {{ !empty($isPdf) ? '0 auto' : '10px auto' }};
        background: #ffffff;
        border: {{ !empty($isPdf) ? 'none' : '1px solid #cbd5e1' }};
        box-shadow: {{ !empty($isPdf) ? 'none' : '0 8px 24px rgba(15,23,42,0.08)' }};
        border-radius: {{ !empty($isPdf) ? '0' : '6px' }};
        overflow: hidden;
        page-break-inside: avoid;
        page-break-after: avoid;
    }
    table {
        border-collapse: collapse;
        width: 100%;
    }
    .sec-card {
        background: #ffffff;
        border: 1px solid #E3E8EF;
        border-radius: 8px;
        padding: {{ $isBtech ? '8px 12px' : '6px 9px' }};
        box-sizing: border-box;
    }
    .sec-title {
        margin: 0 0 {{ $isBtech ? '6px' : '4px' }};
        padding: 1px 0 2px 7px;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #0F2A4A;
        border-left: 3.5px solid #1E7BD8;
        border-bottom: 1px solid #E3E8EF;
        line-height: 1.25;
    }
    .check-icon {
        font-family: 'DejaVu Sans', sans-serif;
        font-weight: bold;
    }
    @media print {
        @page {
            size: A4 portrait;
            margin: {{ $isBtech ? '7mm 8mm 6mm 8mm' : '5mm 6mm 4mm 6mm' }};
        }
        body {
            background: #ffffff !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .toolbar {
            display: none !important;
        }
        .page-wrap {
            margin: 0 !important;
            padding: 0 !important;
            max-width: 100% !important;
            border: none !important;
            box-shadow: none !important;
            border-radius: 0 !important;
            page-break-after: avoid !important;
            page-break-inside: avoid !important;
        }
    }
</style>
</head>
<body>

@if(empty($isPdf))
<div class="toolbar" style="background:#0F2A4A; padding:8px 16px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 8px rgba(0,0,0,0.15);">
    <div style="color:#ffffff; font-weight:800; font-size:12.5px; display:flex; align-items:center;">
        Application Form &bull; {{ $student->name }} (ID: {{ $student->student_id }})
    </div>
    <div style="display:flex; gap:8px;">
        <a href="{{ request('from') ?: (url()->previous() ?: route('crm.stdsearch', ['tab' => 'stdsearch'])) }}" style="background:#334155; color:#ffffff; text-decoration:none; padding:5px 14px; border-radius:6px; font-weight:700; font-size:11px; display:inline-flex; align-items:center; border:1px solid rgba(255,255,255,0.25);">
            Back
        </a>
        <button type="button" onclick="window.print()" style="background:#ffffff; color:#0F2A4A; border:none; padding:5px 14px; border-radius:6px; font-weight:700; font-size:11px; cursor:pointer;">
            Print Form
        </button>
        <a href="{{ $downloadUrl ?? route('application.download', $student->id) }}" style="background:#2563eb; color:#ffffff; text-decoration:none; padding:5px 14px; border-radius:6px; font-weight:700; font-size:11px; display:inline-flex; align-items:center;">
            Download PDF
        </a>
    </div>
</div>
@endif

<div class="page-wrap">

    {{-- Form Header Banner --}}
    <table style="width: 100%; border-collapse: collapse; background: #1DA1F2; border-bottom: 3.5px solid #2E5A97;">
        <tr>
            <td style="padding: {{ $isBtech ? '12px 18px' : '10px 16px' }}; vertical-align: middle; width: 50%;">
                <div style="background: #ffffff; border-radius: 6px; padding: 4px 10px; display: inline-block;">
                    <img src="{{ $logo }}" alt="Vignan's Logo" style="height: {{ $isBtech ? '48px' : '44px' }}; max-width: 200px; object-fit: contain; display: block;">
                </div>
                <div style="color: #F0F9FF; font-size: 8.5px; padding-top: 5px; letter-spacing: 0.2px;">
                    Application date: {{ ($student->applied_at ?? null) ?? now()->format('d/m/Y') }} &nbsp;|&nbsp; admissions.vignan.ac.in
                </div>
            </td>
            <td style="padding: {{ $isBtech ? '12px 18px' : '10px 16px' }}; vertical-align: middle; width: 50%; text-align: right;">
                <div style="color: #ffffff; font-size: {{ $isBtech ? '18px' : '17px' }}; font-weight: 800; letter-spacing: 0.5px;">APPLICATION FORM</div>
                <div style="color: #F0F9FF; font-size: 9.5px; line-height: 1.4; margin-top: 3px;">
                    University Admission Application<br>
                    Please read all details carefully before signing
                </div>
            </td>
        </tr>
    </table>

    <div style="padding: 0 10px 6px 10px;">

        {{-- Profile Strip --}}
        <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 8px; margin-top: -10px; overflow: hidden; position: relative; z-index: 2;">
            <table style="width: 100%; border-collapse: collapse; border: none; background: transparent;">
                <tr>
                    <td style="padding: {{ $isBtech ? '8px 12px' : '6px 10px' }}; width: 56px; vertical-align: middle;">
                        @if(!empty($student->photo_path))
                            <div style="width: 48px; height: 60px; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; background: #ffffff;">
                                <img src="{{ $student->photo_path }}" alt="Photo" style="width: 48px; height: 60px; object-fit: cover; display: block;">
                            </div>
                        @else
                            <div style="width: 48px; height: 60px; border: 1px solid #cbd5e1; background: #F8FAFC; text-align: center; font-size: 6.5px; color: #64748b; line-height: 1.3; padding-top: 15px; font-weight: 700; border-radius: 4px;">
                                AFFIX<br>PASSPORT<br>PHOTO
                            </div>
                        @endif
                    </td>
                    <td style="padding: {{ $isBtech ? '8px 12px' : '6px 10px' }}; vertical-align: middle;">
                        <div style="font-size: {{ $isBtech ? '14.5px' : '13.5px' }}; font-weight: 800; color: #0f172a; text-transform: uppercase; line-height: 1.2;">{{ $student->name }}</div>
                        <div style="color: #64748b; font-size: {{ $isBtech ? '10px' : '9.5px' }}; line-height: 1.45; margin-top: 3px;">
                            {{ $student->email }} &nbsp;|&nbsp; {{ $student->phone }}<br>
                            Applicant ID: <strong style="color: #0F2A4A;">{{ $student->student_id }}</strong> (App #: {{ $student->id }})
                        </div>
                    </td>
                    <td style="padding: {{ $isBtech ? '8px 12px' : '6px 10px' }}; vertical-align: middle; text-align: right;">
                        @if(!empty($student->is_paid))
                            <span style="display: inline-block; font-size: 8.5px; font-weight: 800; padding: 4px 10px; border-radius: 6px; color: #12875B; background: #E2F5EC; border: 1px solid #a7f3d0;">
                                PAYMENT {{ strtoupper($student->payment_status ?? 'COMPLETED') }}
                            </span>
                        @else
                            <span style="display: inline-block; font-size: 8.5px; font-weight: 800; padding: 4px 10px; border-radius: 6px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a;">
                                PAYMENT PENDING
                            </span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        {{-- Allotment & Examination Schedule Bar --}}
        <table style="width: 100%; border-collapse: collapse; background: #ffffff; border: 1px solid #E3E8EF; border-radius: 8px; margin-top: {{ $isBtech ? '8px' : '5px' }};">
            <tr>
                <td style="padding: {{ $isBtech ? '7px 10px' : '5px 8px' }}; width: 33.33%; vertical-align: top;">
                    <div style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Allotted Program</div>
                    <div style="font-size: {{ $isBtech ? '11.5px' : '11px' }}; font-weight: 800; color: #0F2A4A; margin-top: 1px;">
                        {{ $student->program ?: 'B.Tech - Bachelor of Technology' }}
                    </div>
                    @if(!empty($student->branch))
                        <div style="font-size: 9.5px; color: #1E7BD8; font-weight: 700;">{{ $student->branch }}</div>
                    @endif
                </td>
                <td style="padding: {{ $isBtech ? '7px 10px' : '5px 8px' }}; width: 33.33%; vertical-align: top; border-left: 1px solid #E3E8EF;">
                    <div style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Allotted Campus</div>
                    <div style="font-size: {{ $isBtech ? '11.5px' : '11px' }}; font-weight: 800; color: #0F2A4A; margin-top: 1px;">
                        {{ $student->campus ?: 'Vignan Main Campus (Vadlamudi)' }}
                    </div>
                    <div style="font-size: 9.5px; color: #12875B; font-weight: 700;">Provisional Allotment Confirmed</div>
                </td>
                <td style="padding: {{ $isBtech ? '7px 10px' : '5px 8px' }}; width: 33.33%; vertical-align: top; border-left: 1px solid #E3E8EF;">
                    @if(($student->degree_level ?? '') !== 'phd')
                        <div>
                            <div style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Booked Entrance Exam Slot</div>
                            <div style="font-size: {{ $isBtech ? '11.5px' : '11px' }}; font-weight: 800; color: #0F2A4A; margin-top: 1px;">
                                {{ $student->exam_date ?: 'Stage 5 Scheduled' }}
                            </div>
                            <div style="font-size: 9.5px; color: #B25E09; font-weight: 700;">{{ $student->exam_slot ?: 'Session Assigned' }}</div>
                        </div>
                    @else
                        <div>
                            <div style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase;">Entrance Examination</div>
                            <div style="font-size: {{ $isBtech ? '11.5px' : '11px' }}; font-weight: 800; color: #0F2A4A; margin-top: 1px;">
                                Exempted / Direct Ph.D
                            </div>
                            <div style="font-size: 9.5px; color: #12875B; font-weight: 700;">Departmental Evaluation</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- 2-Column: Personal Information & Address Information --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: {{ $isBtech ? '8px' : '5px' }};">
            <tr>
                <td style="width: 50%; padding-right: 4px; vertical-align: top;">
                    {{-- 1. PERSONAL & PARENT INFORMATION --}}
                    <div class="sec-card">
                        <div class="sec-title">1. Candidate &amp; Parent Details</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: {{ $isBtech ? '10.5px' : '10px' }};">
                            <tr><td style="width: 38%; color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Candidate Name</td><td style="font-size: {{ $isBtech ? '12px' : '11.5px' }}; font-weight: 800; text-transform: uppercase; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->name ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Father / Parent</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->parent_name ?: ($student->father_name ?: 'N/A') }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Parent Contact</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->parent_phone ?: ($student->father_phone ?: 'N/A') }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Candidate Phone</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->phone ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Email ID</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }}; word-break: break-all;">{{ $student->email ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Date of Birth</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $dob }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Gender</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->gender ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Category</td><td style="font-weight: 700; color: #0f172a; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->category ?: 'General' }}</td></tr>
                        </table>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 4px; vertical-align: top;">
                    {{-- 2. ADDRESS INFORMATION --}}
                    <div class="sec-card">
                        <div class="sec-title">2. Permanent &amp; Contact Address</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: {{ $isBtech ? '10.5px' : '10px' }};">
                            <tr><td style="width: 38%; color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Door / Street</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->street ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Mandal / City</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->city ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">District</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->district ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">State</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->state ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Country</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->country ?: 'India' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">PIN Code</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->pincode ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Admission Ref</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">#{{ $student->id }}</td></tr>
                            <tr><td style="color: #64748b; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">Student CRM ID</td><td style="font-weight: 700; color: #0f172a; padding: {{ $isBtech ? '3px 0' : '2px 0' }};">{{ $student->student_id ?: 'N/A' }}</td></tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- 3. QUALIFYING ACADEMIC RECORDS (FULL WIDTH TABLE) --}}
        <div class="sec-card" style="margin-top: {{ $isBtech ? '8px' : '5px' }};">
            <div class="sec-title">
                3. Qualifying Academic Qualifications (10th SSC &amp; 12th Intermediate{{ !empty($student->academic->ug_college) && $student->academic->ug_college !== '-' ? ', Undergraduate' : '' }}{{ !empty($student->academic->pg_college) && $student->academic->pg_college !== '-' ? ' &amp; Postgraduate' : '' }})
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: {{ $isBtech ? '10.5px' : '10px' }};">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1.5px solid #E2E8F0; text-align: left; color: #475569; font-weight: 800; font-size: 9.5px;">
                        <th style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; width: 17%;">Standard / Level</th>
                        <th style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; width: 26%;">School / College Name</th>
                        <th style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; width: 18%;">Board / University</th>
                        <th style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; width: 13%; text-align: center; font-weight: 800; color: #0F2A4A;">Group / Stream</th>
                        <th style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; width: 8%; text-align: center;">Year</th>
                        <th style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; width: 10%;">Roll / Hall Ticket</th>
                        <th style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; width: 8%; text-align: right;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- 10th Standard --}}
                    <tr style="border-bottom: 1px solid #E3E8EF;">
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; font-weight: 800; color: #0F2A4A;">
                            <span style="display: inline-block; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; border-radius: 4px; padding: 2px 6px; font-size: 9px;">
                                10th Standard (SSC)
                            </span>
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; font-weight: 700; color: #1e293b;">
                            {{ (!empty($student->academic->ssc_school) && $student->academic->ssc_school !== '-') ? $student->academic->ssc_school : '-' }}
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; color: #475569;">
                            {{ (!empty($student->academic->ssc_board) && $student->academic->ssc_board !== '-') ? $student->academic->ssc_board : '-' }}
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; text-align: center; color: #64748b; font-weight: 700;">-</td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; text-align: center; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->ssc_year) && $student->academic->ssc_year !== '-') ? $student->academic->ssc_year : '-' }}
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->ssc_roll) && $student->academic->ssc_roll !== '-') ? $student->academic->ssc_roll : '-' }}
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; text-align: right; font-weight: 800; color: #16a34a;">
                            {{ (!empty($student->academic->ssc_percentage) && $student->academic->ssc_percentage !== 'N/A' && $student->academic->ssc_percentage !== '-') ? $student->academic->ssc_percentage : '-' }}
                        </td>
                    </tr>

                    {{-- 12th / Intermediate --}}
                    <tr style="{{ ((!empty($student->academic->ug_college) && $student->academic->ug_college !== '-') || ($student->degree_level ?? '') === 'mtech' || ($student->degree_level ?? '') === 'phd') ? 'border-bottom: 1px solid #E3E8EF;' : '' }}">
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; font-weight: 800; color: #0F2A4A;">
                            <span style="display: inline-block; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 4px; padding: 2px 6px; font-size: 9px;">
                                12th / Inter (10+2)
                            </span>
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; font-weight: 700; color: #1e293b;">
                            {{ (!empty($student->academic->inter_college) && $student->academic->inter_college !== '-') ? $student->academic->inter_college : '-' }}
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; color: #475569;">
                            {{ (!empty($student->academic->inter_board) && $student->academic->inter_board !== '-') ? $student->academic->inter_board : '-' }}
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; text-align: center; font-weight: 800; color: #0F2A4A;">
                            {{ (!empty($student->academic->inter_group) && $student->academic->inter_group !== '-') ? $student->academic->inter_group : '-' }}
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; text-align: center; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->inter_year) && $student->academic->inter_year !== '-') ? $student->academic->inter_year : '-' }}
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->inter_roll) && $student->academic->inter_roll !== '-') ? $student->academic->inter_roll : '-' }}
                        </td>
                        <td style="padding: {{ $isBtech ? '5px 7px' : '3px 5px' }}; text-align: right; font-weight: 800; color: #16a34a;">
                            {{ (!empty($student->academic->inter_percentage) && $student->academic->inter_percentage !== 'N/A' && $student->academic->inter_percentage !== '-') ? $student->academic->inter_percentage : '-' }}
                        </td>
                    </tr>

                    {{-- Undergraduate (UG / Degree / B.Tech) --}}
                    @if((!empty($student->academic->ug_college) && $student->academic->ug_college !== '-') || ($student->degree_level ?? '') === 'mtech' || ($student->degree_level ?? '') === 'phd')
                    <tr style="{{ ((!empty($student->academic->pg_college) && $student->academic->pg_college !== '-') || ($student->degree_level ?? '') === 'phd') ? 'border-bottom: 1px solid #E3E8EF;' : '' }}">
                        <td style="padding: 4px 6px; font-weight: 800; color: #0F2A4A;">
                            <span style="display: inline-block; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; border-radius: 3px; padding: 1px 5px; font-size: 8.5px;">
                                Undergraduate (UG)
                            </span>
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #1e293b;">
                            {{ (!empty($student->academic->ug_college) && $student->academic->ug_college !== '-') ? $student->academic->ug_college : '-' }}
                        </td>
                        <td style="padding: 4px 6px; color: #475569;">
                            {{ (!empty($student->academic->ug_board) && $student->academic->ug_board !== '-') ? $student->academic->ug_board : '-' }}
                        </td>
                        <td style="padding: 4px 6px; text-align: center; color: #64748b; font-weight: 700;">-</td>
                        <td style="padding: 4px 6px; text-align: center; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->ug_year) && $student->academic->ug_year !== '-') ? $student->academic->ug_year : '-' }}
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->ug_roll) && $student->academic->ug_roll !== '-') ? $student->academic->ug_roll : '-' }}
                        </td>
                        <td style="padding: 4px 6px; text-align: right; font-weight: 800; color: #16a34a;">
                            {{ (!empty($student->academic->ug_percentage) && $student->academic->ug_percentage !== 'N/A' && $student->academic->ug_percentage !== '-') ? $student->academic->ug_percentage : '-' }}
                        </td>
                    </tr>
                    @endif

                    {{-- Postgraduate 1 (PG 1 / Master's / M.Tech) --}}
                    @if((!empty($student->academic->pg_college) && $student->academic->pg_college !== '-') || ($student->degree_level ?? '') === 'phd')
                    <tr style="{{ ((!empty($student->academic->pg2_college) && $student->academic->pg2_college !== '-') || ($student->degree_level ?? '') === 'phd') ? 'border-bottom: 1px solid #E3E8EF;' : '' }}">
                        <td style="padding: 4px 6px; font-weight: 800; color: #0F2A4A;">
                            <span style="display: inline-block; background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; border-radius: 3px; padding: 1px 5px; font-size: 8.5px;">
                                Postgraduate 1
                            </span>
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #1e293b;">
                            {{ (!empty($student->academic->pg_college) && $student->academic->pg_college !== '-') ? $student->academic->pg_college : '-' }}
                        </td>
                        <td style="padding: 4px 6px; color: #475569;">
                            {{ (!empty($student->academic->pg_board) && $student->academic->pg_board !== '-') ? $student->academic->pg_board : '-' }}
                        </td>
                        <td style="padding: 4px 6px; text-align: center; color: #64748b; font-weight: 700;">-</td>
                        <td style="padding: 4px 6px; text-align: center; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->pg_year) && $student->academic->pg_year !== '-') ? $student->academic->pg_year : '-' }}
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->pg_roll) && $student->academic->pg_roll !== '-') ? $student->academic->pg_roll : '-' }}
                        </td>
                        <td style="padding: 4px 6px; text-align: right; font-weight: 800; color: #16a34a;">
                            {{ (!empty($student->academic->pg_percentage) && $student->academic->pg_percentage !== 'N/A' && $student->academic->pg_percentage !== '-') ? $student->academic->pg_percentage : '-' }}
                        </td>
                    </tr>
                    @endif

                    {{-- Postgraduate 2 (PG 2 / Ph.D) --}}
                    @if((!empty($student->academic->pg2_college) && $student->academic->pg2_college !== '-') || ($student->degree_level ?? '') === 'phd')
                    <tr>
                        <td style="padding: 4px 6px; font-weight: 800; color: #0F2A4A;">
                            <span style="display: inline-block; background: #faf5ff; color: #7e22ce; border: 1px solid #e9d5ff; border-radius: 3px; padding: 1px 5px; font-size: 8.5px;">
                                Postgraduate 2
                            </span>
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #1e293b;">
                            {{ (!empty($student->academic->pg2_college) && $student->academic->pg2_college !== '-') ? $student->academic->pg2_college : '-' }}
                        </td>
                        <td style="padding: 4px 6px; color: #475569;">
                            {{ (!empty($student->academic->pg2_board) && $student->academic->pg2_board !== '-') ? $student->academic->pg2_board : '-' }}
                        </td>
                        <td style="padding: 4px 6px; text-align: center; color: #64748b; font-weight: 700;">-</td>
                        <td style="padding: 4px 6px; text-align: center; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->pg2_year) && $student->academic->pg2_year !== '-') ? $student->academic->pg2_year : '-' }}
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->pg2_roll) && $student->academic->pg2_roll !== '-') ? $student->academic->pg2_roll : '-' }}
                        </td>
                        <td style="padding: 4px 6px; text-align: right; font-weight: 800; color: #16a34a;">
                            {{ (!empty($student->academic->pg2_percentage) && $student->academic->pg2_percentage !== 'N/A' && $student->academic->pg2_percentage !== '-') ? $student->academic->pg2_percentage : '-' }}
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- 2-Column: 4. Payment Information & 5. Verification Checklist --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: {{ $isBtech ? '8px' : '5px' }};">
            <tr>
                <td style="width: 50%; padding-right: 4px; vertical-align: top;">
                    {{-- 4. PAYMENT INFORMATION --}}
                    <div class="sec-card">
                        <div class="sec-title">4. Application Fee</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: {{ $isBtech ? '10.5px' : '10px' }};">
                            <tr><td style="width: 38%; color: #6B778A; padding: {{ $isBtech ? '3.5px 0' : '2px 0' }};">Paid Amount</td><td style="font-weight: 800; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3.5px 0' : '2px 0' }}; color: #15803d; font-size: {{ $isBtech ? '12px' : '11.5px' }};">{{ (!empty($student->payment_amount) && $student->payment_amount > 0) ? ('INR ' . number_format((float) $student->payment_amount)) : '-' }}</td></tr>
                            <tr><td style="color: #6B778A; padding: {{ $isBtech ? '3.5px 0' : '2px 0' }};">Payment Gateway</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3.5px 0' : '2px 0' }};">{{ (!empty($student->payment_method) && $student->payment_method !== '-') ? $student->payment_method : '-' }}</td></tr>
                            <tr><td style="color: #6B778A; padding: {{ $isBtech ? '3.5px 0' : '2px 0' }};">Transaction Ref</td><td style="font-weight: 700; color: #0f172a; border-bottom: 1px solid #E3E8EF; padding: {{ $isBtech ? '3.5px 0' : '2px 0' }}; font-size: 9.5px; word-break: break-all;">{{ (!empty($student->transaction_id) && $student->transaction_id !== '-') ? $student->transaction_id : '-' }}</td></tr>
                            <tr><td style="color: #6B778A; padding: {{ $isBtech ? '3.5px 0' : '2px 0' }};">Accounts Status</td>
                                <td style="font-weight: 700; padding: {{ $isBtech ? '3.5px 0' : '2px 0' }};">
                                    @if(!empty($student->is_paid))
                                        <span style="display: inline-block; font-size: 8.5px; font-weight: 800; padding: 2.5px 8px; border-radius: 6px; color: #12875B; background: #E2F5EC; border: 1px solid #a7f3d0;">
                                            <span class="check-icon">&#10003;</span> {{ strtoupper($student->payment_status ?? 'COMPLETED') }} &amp; VERIFIED
                                        </span>
                                    @else
                                        <span style="display: inline-block; font-size: 8.5px; font-weight: 800; padding: 2.5px 8px; border-radius: 6px; color: #b45309; background: #fef3c7; border: 1px solid #fde68a;">
                                            PAYMENT PENDING
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 4px; vertical-align: top;">
                    {{-- 5. DOCUMENTS UPLOAD SUMMARY --}}
                    <div class="sec-card">
                        <div class="sec-title">5. Document Verification Status</div>
                        @php
                            $onlyUploadedDocs = [];
                            $allDocsMeta = [
                                'marksheet10'   => '10th Marksheet',
                                'marksheet12'   => '12th Marksheet',
                                'btech'         => 'B.Tech / UG Memo',
                                'mtech'         => 'M.Tech / PG Memo',
                                'pg1_cmm'       => 'PG 1 CMM Memo',
                                'pg1_pc'        => 'PG 1 PC / OD',
                                'pg2_cmm'       => 'PG 2 CMM Memo',
                                'pg2_pc'        => 'PG 2 PC / OD',
                                'experience'    => 'Work Experience',
                                'aadhaar'       => 'Aadhaar Card',
                                'photo'         => 'Photo',
                                'tc'            => 'Transfer Cert.',
                                'study_conduct' => 'Study & Conduct',
                            ];
                            $stUploadedFiles = (array)($student->uploaded_files ?? []);
                            $stDocuments = (array)($student->documents ?? []);

                            foreach($stUploadedFiles as $uKey => $uData) {
                                if (!empty($uData['path']) || !empty($uData['url']) || !empty($uData['filename'])) {
                                    $uExt = strtoupper($uData['ext'] ?? pathinfo($uData['filename'] ?? '', PATHINFO_EXTENSION) ?: 'VERIFIED');
                                    $onlyUploadedDocs[$uKey] = [
                                        'label' => $uData['title'] ?? ($allDocsMeta[$uKey] ?? ucwords(str_replace('_', ' ', $uKey))),
                                        'ext' => $uExt,
                                    ];
                                }
                            }
                            foreach($stDocuments as $dKey => $dExt) {
                                if (!empty($dExt) && !isset($onlyUploadedDocs[$dKey])) {
                                    $cleanDExt = strtoupper(str_replace(['image/', 'application/'], '', $dExt));
                                    $onlyUploadedDocs[$dKey] = [
                                        'label' => $allDocsMeta[$dKey] ?? ucwords(str_replace('_', ' ', $dKey)),
                                        'ext' => $cleanDExt ?: 'VERIFIED',
                                    ];
                                }
                            }
                        @endphp
                        @if(empty($onlyUploadedDocs))
                            <div style="padding: {{ $isBtech ? '16px 8px' : '8px' }}; font-size: 9.5px; color: #64748b; font-style: italic; text-align: center;">
                                No documents uploaded yet.
                            </div>
                        @else
                            <table style="width: 100%; border-collapse: collapse;">
                                @foreach(array_chunk($onlyUploadedDocs, 2, true) as $dRow)
                                    <tr>
                                        @foreach($dRow as $dKey => $dInfo)
                                            <td style="width: 50%; padding: 2px;">
                                                <div style="border-radius: 4px; padding: {{ $isBtech ? '3px 7px' : '2px 6px' }}; font-size: 9px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; background: #E2F5EC; color: #1B2430; border: 1px solid #a7f3d0;">
                                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $dInfo['label'] }}</span>
                                                    <small style="color: #12875B; font-weight: 800; margin-left: 4px;"><span class="check-icon">&#10003;</span> {{ $dInfo['ext'] }}</small>
                                                </div>
                                            </td>
                                        @endforeach
                                        @if(count($dRow) === 1)
                                            <td style="width: 50%; padding: 2px;"></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Instructions & Declaration (Matches Portal Preview) --}}
        <div style="background: #FFF8EA; border: 1px solid #F2DDB0; border-left: 3.5px solid #B25E09; border-radius: 6px; padding: {{ $isBtech ? '8px 12px' : '5px 9px' }}; margin-top: {{ $isBtech ? '9px' : '5px' }}; font-size: {{ $isBtech ? '8.5px' : '8px' }}; line-height: 1.45; color: #1B2430;">
            <strong style="color: #B25E09; letter-spacing: 0.3px;">INSTRUCTIONS &amp; DECLARATION</strong>
            <ul style="margin: 3px 0 0; padding-left: 14px;">
                <li>This is a submitted application form for your admission to Vignan University.</li>
                <li>For more details, phone: +91 863 2344700, 7799 427 427, Website: https://vignan.ac.in/admissions. For all information, discussions, and clarifications, please contact the Admissions Office at Vignan University.</li>
                <li>I hereby declare that all entries made in this application are authentic and true to the best of my knowledge.</li>
            </ul>
        </div>

        {{-- Signatures --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: {{ $isBtech ? '22px' : '10px' }};">
            <tr>
                <td style="width: 33.33%; text-align: center; padding: 0 10px; color: #6B778A; font-size: 9px;">
                    <div style="border-top: 1px solid #6B778A; padding-top: 4px; font-weight: 700;">Signature of Parent / Guardian</div>
                </td>
                <td style="width: 33.33%; text-align: center; padding: 0 10px; color: #6B778A; font-size: 9px;">
                    <div style="border-top: 1px solid #6B778A; padding-top: 4px; font-weight: 700;">Date: {{ date('d/m/Y') }}</div>
                </td>
                <td style="width: 33.33%; text-align: center; padding: 0 10px; color: #6B778A; font-size: 9px;">
                    <div style="border-top: 1px solid #6B778A; padding-top: 4px; font-weight: 700;">Signature of Candidate</div>
                </td>
            </tr>
        </table>

        {{-- For Office Use Only --}}
        <div style="border: 1px solid #cbd5e1; background: #f8fafc; border-radius: 6px; padding: {{ $isBtech ? '6px 10px' : '4px 8px' }}; margin-top: {{ $isBtech ? '10px' : '5px' }}; font-size: {{ $isBtech ? '8.5px' : '8px' }}; color: #64748b;">
            <strong style="font-size: 8px; letter-spacing: 0.3px; display: block; margin-bottom: 3px; color: #0f172a;">FOR OFFICE USE ONLY &bull; UNIVERSITY ADMISSIONS DIRECTORATE</strong>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 33.33%;">Application No.: <strong>{{ $student->id }}</strong></td>
                    <td style="width: 33.33%;">Verification Officer: ________________</td>
                    <td style="width: 33.33%;">Approval Seal &amp; Date: _________</td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <div style="text-align: center; font-size: 8px; color: #6B778A; margin-top: {{ $isBtech ? '8px' : '4px' }};">
            Vignan's Foundation for Science, Technology &amp; Research (Deemed to be University) &nbsp;|&nbsp; https://admissions.vignan.ac.in
        </div>

    </div>
</div>

@if (!empty($autoPrint) || request()->has('print'))
<script>
    window.addEventListener('load', function() {
        setTimeout(function() {
            window.print();
        }, 300);
    });
</script>
@endif
</body>
</html>
