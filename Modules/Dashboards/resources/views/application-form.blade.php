{{--
    resources/views/application-form.blade.php
    Official Vignan University Admission Information Form.
    Pixel-perfect, A4-optimized 1-page layout for browser printing and DomPDF engine.
--}}
@php
    $logo = $logo ?? (is_file(public_path('images/vignan-banner-logo.jpg'))
        ? ('data:image/jpeg;base64,' . base64_encode((string) file_get_contents(public_path('images/vignan-banner-logo.jpg'))))
        : asset('images/vignan-banner-logo.jpg'));

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
        margin: 6mm 8mm 6mm 8mm;
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
        color: #0f172a;
        font-size: 10px;
        line-height: 1.35;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .page-wrap {
        width: 100%;
        max-width: 194mm;
        margin: {{ !empty($isPdf) ? '0 auto' : '14px auto' }};
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
        border: 1px solid #E2E8F0;
        border-radius: 6px;
        padding: 8px 10px;
        box-sizing: border-box;
    }
    .sec-title {
        margin: 0 0 5px;
        padding: 0 0 4px 6px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: 0.3px;
        text-transform: uppercase;
        color: #0F2A4A;
        border-left: 3px solid #1E7BD8;
        border-bottom: 1px solid #E2E8F0;
    }
    @media print {
        @page {
            size: A4 portrait;
            margin: 5mm 6mm;
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
    <div style="color:#ffffff; font-weight:800; font-size:12.5px; display:flex; align-items:center; gap:8px;">
        <span>📄</span> Application Form &bull; {{ $student->name }} (ID: {{ $student->student_id }})
    </div>
    <div style="display:flex; gap:8px;">
        <button type="button" onclick="window.print()" style="background:#ffffff; color:#0F2A4A; border:none; padding:5px 14px; border-radius:6px; font-weight:700; font-size:11px; cursor:pointer;">
            🖨️ Print Form
        </button>
        <a href="{{ $downloadUrl ?? route('application.download', $student->id) }}" style="background:#2563eb; color:#ffffff; text-decoration:none; padding:5px 14px; border-radius:6px; font-weight:700; font-size:11px; display:inline-flex; align-items:center; gap:5px;">
            📥 Download PDF
        </a>
    </div>
</div>
@endif

<div class="page-wrap">

    {{-- Form Header Banner --}}
    <table style="width: 100%; border-collapse: collapse; background: #0F2A4A; border-bottom: 3px solid #1E7BD8;">
        <tr>
            <td style="padding: 10px 18px; vertical-align: middle; width: 50%;">
                <div style="background: #ffffff; border-radius: 6px; padding: 4px 10px; display: inline-block;">
                    <img src="{{ $logo }}" alt="Vignan's Logo" style="height: 44px; width: auto; max-width: 195px; display: block;">
                </div>
                <div style="color: #A9BCD6; font-size: 8px; padding-top: 4px; letter-spacing: 0.2px;">
                    Application date: {{ ($student->applied_at ?? null) ?? now()->format('d/m/Y') }} &nbsp;|&nbsp; admissions.vignan.ac.in
                </div>
            </td>
            <td style="padding: 10px 18px; vertical-align: middle; width: 50%; text-align: right;">
                <div style="color: #ffffff; font-size: 17px; font-weight: 800; letter-spacing: 0.5px;">APPLICATION FORM</div>
                <div style="color: #A9BCD6; font-size: 9.5px; line-height: 1.35; margin-top: 2px;">
                    University Admission Application<br>
                    Please read all details carefully before signing
                </div>
            </td>
        </tr>
    </table>

    <div style="padding: 0 16px 8px 16px;">

        {{-- Profile Strip --}}
        <div style="background: #ffffff; border: 1px solid #E2E8F0; border-radius: 8px; margin-top: -10px; overflow: hidden; box-shadow: 0 2px 4px rgba(0,0,0,0.03); position: relative; z-index: 2;">
            <table style="width: 100%; border-collapse: collapse; border: none; background: transparent;">
                <tr>
                    <td style="padding: 6px 12px; width: 58px; vertical-align: middle;">
                        @if(!empty($student->photo_path))
                            <div style="width: 48px; height: 58px; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; background: #ffffff;">
                                <img src="{{ $student->photo_path }}" alt="Photo" style="width: 48px; height: 58px; object-fit: cover; display: block;">
                            </div>
                        @else
                            <div style="width: 48px; height: 58px; border: 1px solid #cbd5e1; background: #F8FAFC; text-align: center; font-size: 7px; color: #64748b; line-height: 1.3; padding-top: 14px; font-weight: 700; border-radius: 4px;">
                                AFFIX<br>PHOTO
                            </div>
                        @endif
                    </td>
                    <td style="padding: 6px 12px; vertical-align: middle;">
                        <div style="font-size: 15px; font-weight: 800; color: #0F2A4A; line-height: 1.2;">{{ $student->name }}</div>
                        <div style="color: #64748b; font-size: 9.5px; line-height: 1.45; margin-top: 2px;">
                            {{ $student->email }} &nbsp;|&nbsp; {{ $student->phone }}<br>
                            Applicant ID: <strong style="color: #0F2A4A;">{{ $student->student_id }}</strong> (App #: {{ $student->id }})
                        </div>
                    </td>
                    <td style="padding: 6px 12px; vertical-align: middle; text-align: right;">
                        <span style="display: inline-block; font-size: 8px; font-weight: 800; padding: 3px 8px; border-radius: 8px; color: #12875B; background: #E2F5EC; border: 1px solid #a7f3d0;">
                            PAYMENT {{ strtoupper($student->payment_status ?? 'COMPLETED') }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Allotment & Examination Schedule Bar --}}
        <table style="width: 100%; border-collapse: collapse; background: #ffffff; border: 1px solid #E2E8F0; border-radius: 6px; margin-top: 6px;">
            <tr>
                <td style="padding: 6px 10px; width: 33.33%; vertical-align: top;">
                    <div style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">Allotted Program</div>
                    <div style="font-size: 10.5px; font-weight: 800; color: #0F2A4A; margin-top: 1px;">
                        {{ $student->program ?: 'B.Tech - Bachelor of Technology' }}
                    </div>
                    @if(!empty($student->branch))
                        <div style="font-size: 9px; color: #1E7BD8; font-weight: 700;">{{ $student->branch }}</div>
                    @endif
                </td>
                <td style="padding: 6px 10px; width: 33.33%; vertical-align: top; border-left: 1px solid #E2E8F0;">
                    <div style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">Allotted Campus</div>
                    <div style="font-size: 10.5px; font-weight: 800; color: #0F2A4A; margin-top: 1px;">
                        {{ $student->campus ?: 'Vignan Main Campus (Vadlamudi)' }}
                    </div>
                    <div style="font-size: 9px; color: #12875B; font-weight: 700;">Allotment Confirmed</div>
                </td>
                <td style="padding: 6px 10px; width: 33.33%; vertical-align: top; border-left: 1px solid #E2E8F0;">
                    @if(($student->degree_level ?? '') !== 'phd')
                        <div>
                            <div style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">Booked Entrance Exam Slot</div>
                            <div style="font-size: 10.5px; font-weight: 800; color: #0F2A4A; margin-top: 1px;">
                                📅 {{ $student->exam_date ?: 'Stage 5 Scheduled' }}
                            </div>
                            <div style="font-size: 9px; color: #B25E09; font-weight: 700;">⏰ {{ $student->exam_slot ?: 'Session Assigned' }}</div>
                        </div>
                    @else
                        <div>
                            <div style="font-size: 8px; font-weight: 700; color: #64748b; text-transform: uppercase;">Entrance Examination</div>
                            <div style="font-size: 10.5px; font-weight: 800; color: #0F2A4A; margin-top: 1px;">
                                Exempted / Direct Ph.D
                            </div>
                            <div style="font-size: 9px; color: #12875B; font-weight: 700;">Departmental Evaluation</div>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- 2-Column: Personal Information & Address Information --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: 6px;">
            <tr>
                <td style="width: 50%; padding-right: 4px; vertical-align: top;">
                    {{-- 1. PERSONAL & PARENT INFORMATION --}}
                    <div class="sec-card">
                        <div class="sec-title">1. Candidate &amp; Parent Details</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 9.5px;">
                            <tr><td style="width: 38%; color: #64748b; padding: 2px 0;">Candidate Name</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->name ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Father / Parent</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->parent_name ?: ($student->father_name ?: 'N/A') }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Parent Contact</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->parent_phone ?: ($student->father_phone ?: 'N/A') }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Candidate Phone</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->phone ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Email ID</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0; word-break: break-all;">{{ $student->email ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Date of Birth</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $dob }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Gender</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->gender ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Category</td><td style="font-weight: 700; padding: 2px 0;">{{ $student->category ?: 'General' }}</td></tr>
                        </table>
                    </div>
                </td>
                <td style="width: 50%; padding-left: 4px; vertical-align: top;">
                    {{-- 2. ADDRESS INFORMATION --}}
                    <div class="sec-card">
                        <div class="sec-title">2. Permanent &amp; Contact Address</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 9.5px;">
                            <tr><td style="width: 38%; color: #64748b; padding: 2px 0;">Door / Street</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->street ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Mandal / City</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->city ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">District</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->district ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">State</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->state ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Country</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->country ?: 'India' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">PIN Code</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->pincode ?: 'N/A' }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Admission Ref</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">#{{ $student->id }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Student CRM ID</td><td style="font-weight: 700; padding: 2px 0;">{{ $student->student_id ?: 'N/A' }}</td></tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- 3. QUALIFYING ACADEMIC RECORDS (FULL WIDTH TABLE) --}}
        <div class="sec-card" style="margin-top: 6px;">
            <div class="sec-title">
                3. Qualifying Academic Qualifications (10th SSC &amp; 12th Intermediate{{ !empty($student->academic->ug_college) ? ', Undergraduate' : '' }}{{ !empty($student->academic->pg_college) ? ' &amp; Postgraduate' : '' }})
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 9.5px;">
                <thead>
                    <tr style="background: #F8FAFC; border-bottom: 1px solid #CBD5E1; text-align: left; color: #475569; font-weight: 800;">
                        <th style="padding: 4px 6px; width: 17%;">Standard / Level</th>
                        <th style="padding: 4px 6px; width: 27%;">School / College Name</th>
                        <th style="padding: 4px 6px; width: 18%;">Board / University</th>
                        <th style="padding: 4px 6px; width: 13%; text-align: center; font-weight: 800; color: #0F2A4A;">Group / Stream</th>
                        <th style="padding: 4px 6px; width: 8%; text-align: center;">Year</th>
                        <th style="padding: 4px 6px; width: 9%;">Roll / Hall Ticket</th>
                        <th style="padding: 4px 6px; width: 8%; text-align: right;">Percentage</th>
                    </tr>


                </thead>
                <tbody>
                    {{-- 10th Standard --}}
                    <tr style="border-bottom: 1px solid #E2E8F0;">
                        <td style="padding: 4px 6px; font-weight: 800; color: #0F2A4A;">
                            <span style="display: inline-block; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; border-radius: 3px; padding: 1px 5px; font-size: 8.5px;">
                                10th Standard (SSC)
                            </span>
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #1e293b;">
                            {{ (!empty($student->academic->ssc_school) && $student->academic->ssc_school !== '-') ? $student->academic->ssc_school : '-' }}
                        </td>
                        <td style="padding: 4px 6px; color: #475569;">
                            {{ (!empty($student->academic->ssc_board) && $student->academic->ssc_board !== '-') ? $student->academic->ssc_board : '-' }}
                        </td>

                        <td style="padding: 4px 6px; text-align: center; color: #64748b; font-weight: 700;">-</td>

                        <td style="padding: 4px 6px; text-align: center; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->ssc_year) && $student->academic->ssc_year !== '-') ? $student->academic->ssc_year : '-' }}
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->ssc_roll) && $student->academic->ssc_roll !== '-') ? $student->academic->ssc_roll : '-' }}
                        </td>
                        <td style="padding: 4px 6px; text-align: right; font-weight: 800; color: #16a34a;">
                            {{ (!empty($student->academic->ssc_percentage) && $student->academic->ssc_percentage !== 'N/A' && $student->academic->ssc_percentage !== '-') ? $student->academic->ssc_percentage : '-' }}
                        </td>
                    </tr>
                    {{-- 12th / Intermediate --}}
                    <tr style="{{ (!empty($student->academic->ug_college) && $student->academic->ug_college !== '-' || ($student->degree_level ?? '') === 'mtech' || ($student->degree_level ?? '') === 'phd') ? 'border-bottom: 1px solid #E2E8F0;' : '' }}">
                        <td style="padding: 4px 6px; font-weight: 800; color: #0F2A4A;">
                            <span style="display: inline-block; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 3px; padding: 1px 5px; font-size: 8.5px;">
                                12th / Inter (10+2)
                            </span>
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #1e293b;">
                            {{ (!empty($student->academic->inter_college) && $student->academic->inter_college !== '-') ? $student->academic->inter_college : '-' }}
                        </td>
                        <td style="padding: 4px 6px; color: #475569;">
                            {{ (!empty($student->academic->inter_board) && $student->academic->inter_board !== '-') ? $student->academic->inter_board : '-' }}
                        </td>
                        <td style="padding: 4px 6px; text-align: center; font-weight: 800; color: #0F2A4A;">
                            {{ (!empty($student->academic->inter_group) && $student->academic->inter_group !== '-') ? $student->academic->inter_group : '-' }}
                        </td>

                        <td style="padding: 4px 6px; text-align: center; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->inter_year) && $student->academic->inter_year !== '-') ? $student->academic->inter_year : '-' }}
                        </td>
                        <td style="padding: 4px 6px; font-weight: 700; color: #0F2A4A;">
                            {{ (!empty($student->academic->inter_roll) && $student->academic->inter_roll !== '-') ? $student->academic->inter_roll : '-' }}
                        </td>
                        <td style="padding: 4px 6px; text-align: right; font-weight: 800; color: #16a34a;">
                            {{ (!empty($student->academic->inter_percentage) && $student->academic->inter_percentage !== 'N/A' && $student->academic->inter_percentage !== '-') ? $student->academic->inter_percentage : '-' }}
                        </td>
                    </tr>

                    {{-- Undergraduate (UG / Degree / B.Tech) --}}
                    @if((!empty($student->academic->ug_college) && $student->academic->ug_college !== '-') || ($student->degree_level ?? '') === 'mtech' || ($student->degree_level ?? '') === 'phd')
                    <tr style="{{ ((!empty($student->academic->pg_college) && $student->academic->pg_college !== '-') || ($student->degree_level ?? '') === 'phd') ? 'border-bottom: 1px solid #E2E8F0;' : '' }}">
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
                    <tr style="{{ ((!empty($student->academic->pg2_college) && $student->academic->pg2_college !== '-') || ($student->degree_level ?? '') === 'phd') ? 'border-bottom: 1px solid #E2E8F0;' : '' }}">
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
        <table style="width: 100%; border-collapse: collapse; margin-top: 6px;">
            <tr>
                <td style="width: 50%; padding-right: 4px; vertical-align: top;">
                    {{-- 4. PAYMENT INFORMATION --}}
                    <div class="sec-card">
                        <div class="sec-title">4. Application Fee</div>
                        <table style="width: 100%; border-collapse: collapse; font-size: 9.5px;">
                            <tr><td style="width: 38%; color: #64748b; padding: 2px 0;">Paid Amount</td><td style="font-weight: 800; border-bottom: 1px solid #E2E8F0; padding: 2px 0; color: #15803d; font-size: 10.5px;">INR {{ number_format((float) $student->payment_amount) }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Payment Gateway</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0;">{{ $student->payment_method }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Transaction Ref</td><td style="font-weight: 700; border-bottom: 1px solid #E2E8F0; padding: 2px 0; font-size: 8.5px; word-break: break-all;">{{ $student->transaction_id }}</td></tr>
                            <tr><td style="color: #64748b; padding: 2px 0;">Accounts Status</td>
                                <td style="font-weight: 700; padding: 2px 0;">
                                    <span style="display: inline-block; font-size: 8px; font-weight: 800; padding: 2px 6px; border-radius: 6px; color: #12875B; background: #E2F5EC; border: 1px solid #a7f3d0;">
                                        ✓ {{ strtoupper($student->payment_status ?? 'COMPLETED') }} &amp; VERIFIED
                                    </span>
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
                                'btech'         => 'B.Tech / UG Degree Memo',
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
                            <div style="padding: 6px; font-size: 8.5px; color: #64748b; font-style: italic; text-align: center;">
                                No documents uploaded yet.
                            </div>
                        @else
                            <table style="width: 100%; border-collapse: collapse;">
                                @foreach(array_chunk($onlyUploadedDocs, 2, true) as $dRow)
                                    <tr>
                                        @foreach($dRow as $dKey => $dInfo)
                                            <td style="width: 50%; padding: 1.5px;">
                                                <div style="border-radius: 4px; padding: 3px 6px; font-size: 8.5px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; background: #E2F5EC; color: #1B2430; border: 1px solid #a7f3d0;">
                                                    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $dInfo['label'] }}</span>
                                                    <small style="color: #12875B; font-weight: 800; margin-left: 4px;">✓ {{ $dInfo['ext'] }}</small>
                                                </div>
                                            </td>
                                        @endforeach
                                        @if(count($dRow) === 1)
                                            <td style="width: 50%; padding: 1.5px;"></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </td>
            </tr>
        </table>

        {{-- Instructions & Declaration --}}
        <div style="background: #FFF8EA; border: 1px solid #F2DDB0; border-left: 3px solid #B25E09; border-radius: 6px; padding: 5px 10px; margin-top: 6px; font-size: 8px; line-height: 1.35; color: #1B2430;">
            <strong style="color: #B25E09; letter-spacing: 0.3px;">INSTRUCTIONS &amp; DECLARATION</strong>
            <ul style="margin: 2px 0 0; padding-left: 14px;">
                <li>Original certificates must be presented at the time of campus verification.</li>
                <li>Tuition fee, merit scholarships, and entrance test criteria are governed by university admissions norms.</li>
                <li>I hereby declare that all entries made in this application are authentic and true to the best of my knowledge.</li>
            </ul>
        </div>

        {{-- Signatures --}}
        <table style="width: 100%; border-collapse: collapse; margin-top: 14px;">
            <tr>
                <td style="width: 33.33%; text-align: center; padding: 0 10px; color: #64748b; font-size: 9px;">
                    <div style="border-top: 1px solid #64748b; padding-top: 4px; font-weight: 700;">Signature of Parent / Guardian</div>
                </td>
                <td style="width: 33.33%; text-align: center; padding: 0 10px; color: #64748b; font-size: 9px;">
                    <div style="border-top: 1px solid #64748b; padding-top: 4px; font-weight: 700;">Date: {{ date('d/m/Y') }}</div>
                </td>
                <td style="width: 33.33%; text-align: center; padding: 0 10px; color: #64748b; font-size: 9px;">
                    <div style="border-top: 1px solid #64748b; padding-top: 4px; font-weight: 700;">Signature of Candidate</div>
                </td>
            </tr>
        </table>

        {{-- For Office Use Only --}}
        <div style="border: 1px solid #CBD5E1; background: #F8FAFC; border-radius: 6px; padding: 5px 10px; margin-top: 8px; font-size: 8px; color: #64748b;">
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
        <div style="text-align: center; font-size: 7.5px; color: #64748b; margin-top: 6px;">
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
