{{--
    resources/views/application-form.blade.php
    A4 application form. Works in the browser (print/Save PDF) and with barryvdh/laravel-dompdf.
--}}
@php
    $logo = $logo ?? asset('images/vignan-logo.png');

    $docs = [
        'marksheet10'   => '10th Marksheet',
        'marksheet12'   => '12th Marksheet',
        'aadhaar'       => 'Aadhaar Card',
        'photo'         => 'Photo',
        'tc'            => 'Transfer Cert.',
        'study_conduct' => 'Study & Conduct',
    ];
    $uploaded = (array) ($student->documents ?? []);

    $dob = !empty($student->dob) ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : 'N/A';
    $paid = strtolower($student->payment_status ?? '') === 'completed';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Application Form - {{ $student->name }}</title>
<style>
    @page { size: A4 portrait; margin: 0; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Helvetica, Arial, sans-serif; color: #1B2430; font-size: 11px; background: #F4F7FB; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .page { width: 210mm; min-height: 297mm; margin: 0 auto; background: #F4F7FB; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 42, 74, 0.12); -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    table { border-collapse: collapse; border-spacing: 0; }
    td { vertical-align: top; }

    /* header */
    .header { width: 100%; background: #0F2A4A; border-bottom: 4px solid #1E7BD8; }
    .header td { padding: 22px 12mm 18px 12mm; }
    .logo-box { background: #fff; border-radius: 8px; padding: 8px 10px; display: inline-block; }
    .logo-box img { height: 46px; display: block; }
    .title { color: #fff; font-size: 22px; font-weight: bold; text-align: right; letter-spacing: .5px; }
    .subtitle { color: #A9BCD6; font-size: 11px; text-align: right; line-height: 1.6; }
    .meta { color: #A9BCD6; font-size: 9px; padding-top: 8px; }

    .wrap { padding: 0 12mm; }

    /* profile strip */
    .profile { width: 100%; background: #fff; border: 1px solid #E3E8EF; border-radius: 10px; margin-top: -16px; }
    .profile td { padding: 10px 12px; vertical-align: middle; }
    .photo { width: 58px; height: 72px; border: 1px dashed #6B778A; background: #F4F7FB; text-align: center;
             font-size: 7px; color: #6B778A; line-height: 1.5; padding-top: 22px; }
    .photo-filled { width: 58px; height: 72px; border: 1px solid #cbd5e1; background: #fff; padding: 0; overflow: hidden; }
    .photo-filled img { display: block; width: 58px; height: 72px; object-fit: cover; }
    .pname { font-size: 18px; font-weight: bold; }
    .pmeta { color: #6B778A; font-size: 11px; line-height: 1.7; }

    .pill { display: inline-block; font-size: 8.5px; font-weight: bold; padding: 4px 10px; border-radius: 10px; margin-bottom: 4px; }
    .ok   { color: #12875B; background: #E2F5EC; }
    .warn { color: #B25E09; background: #FDF0DC; }

    /* cards */
    .grid { width: 100%; margin-top: 12px; }
    .grid > tbody > tr > td { width: 50%; }
    .card { background: #fff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px; }
    .gap-r { padding-right: 7px; } .gap-l { padding-left: 7px; }
    .card h3 { margin: 0 0 8px; padding: 0 0 8px 8px; font-size: 12px; letter-spacing: .4px; text-transform: uppercase;
               color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF; }
    .fields { width: 100%; }
    .fields td { padding: 5px 0; font-size: 11px; }
    .fields td.l { width: 38%; color: #6B778A; font-size: 10px; }
    .fields td.v { font-weight: bold; border-bottom: 1px solid #E3E8EF; word-break: break-all; }

    /* documents */
    .docs { width: 100%; }
    .docs td { width: 33.33%; padding: 3px; }
    .chip { border-radius: 7px; padding: 6px 8px; font-size: 10px; font-weight: bold; }
    .chip small { float: right; font-weight: normal; font-size: 9px; }
    .chip.ok   { color: #1B2430; background: #E2F5EC; } .chip.ok small   { color: #12875B; }
    .chip.warn { color: #1B2430; background: #FDF0DC; } .chip.warn small { color: #B25E09; }

    /* note */
    .note { background: #FFF8EA; border: 1px solid #F2DDB0; border-left: 4px solid #B25E09; border-radius: 10px;
            padding: 10px 14px; margin-top: 12px; font-size: 10px; line-height: 1.6; }
    .note b { color: #B25E09; font-size: 10px; letter-spacing: .4px; }
    .note ul { margin: 4px 0 0; padding-left: 14px; }

    /* signatures + office */
    .sign { width: 100%; margin-top: 38px; }
    .sign td { width: 33.33%; text-align: center; padding: 0 12px; color: #6B778A; font-size: 10px; }
    .sign .line { border-top: 1px solid #6B778A; padding-top: 5px; }
    .office { border: 1px dashed #6B778A; background: #fff; border-radius: 8px; padding: 8px 12px; margin-top: 18px;
              font-size: 10px; color: #6B778A; }
    .office b { font-size: 8.5px; letter-spacing: .4px; display: block; margin-bottom: 8px; }
    .office table { width: 100%; } .office td { width: 33.33%; }

    .footer { position: absolute; bottom: 8mm; left: 0; right: 0; text-align: center; font-size: 8.5px; color: #6B778A; }

    /* attached document page styling */
    .page-attachment { width: 210mm; min-height: 297mm; margin: 20px auto 0 auto; background: #ffffff; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 42, 74, 0.12); page-break-before: always; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* download toolbar (hidden in the PDF and when printing) */
    .toolbar { width: 210mm; margin: 0 auto; padding: 14px 0; display: flex; justify-content: space-between; align-items: center; }
    .btn { display: inline-block; background: #1E7BD8; color: #fff; text-decoration: none; font-weight: bold; font-size: 13px;
           padding: 10px 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(30,123,216,.35); cursor: pointer; border: none; }
    .btn:hover { background: #0F2A4A; }
    .btn-back { background: #fff; color: #0F2A4A; border: 1px solid #cbd5e1; box-shadow: none; }
    .btn-back:hover { background: #e2e8f0; color: #0F2A4A; }
    .btn svg { vertical-align: -3px; margin-right: 6px; }
    @media print {
        body { background: #fff; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .page { box-shadow: none; width: 100%; margin: 0; padding: 0; page-break-after: always; }
        .page-attachment { box-shadow: none; width: 100%; margin: 0; padding: 0; page-break-before: always; page-break-after: always; }
        .toolbar { display: none !important; }
    }
</style>
</head>
<body>
@if (empty($isPdf))
    <div class="toolbar">
        <a class="btn btn-back" href="{{ route('dashboards.index', ['step' => 5]) }}">
            &larr; Back to Portal
        </a>
        <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-back" onclick="window.print()">Print A4</button>
            <a class="btn" download href="{{ $downloadUrl ?? route('application.download', $student->id) }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/></svg>Download PDF
            </a>
        </div>
    </div>
@endif
@php
    $progUpper = strtoupper(trim((string)($student->program ?? '')));
    $levelUpper = strtoupper(trim((string)($student->degree_level ?? '')));
    $crmUpper = strtoupper(trim((string)($student->student_id ?? '')));
    $isPhd = $levelUpper === 'PHD' || str_contains($progUpper, 'PHD') || str_contains($progUpper, 'PH.D') || str_contains($progUpper, 'DOCTOR') || str_starts_with($crmUpper, 'PHD');
@endphp

@if($isPhd)
<div class="page" style="background:#ffffff; color:#1d1d1f; font-family:'Source Sans 3', Helvetica, Arial, sans-serif; font-size:10px; padding:20px 24px; box-sizing:border-box;">

    {{-- Ph.D. Header with Vignan Logo & Metadata (Matches Ph.D. Admission Information Form.html) --}}
    <table style="width:100%; border-bottom:2px solid #1d1d1f; padding-bottom:12px; margin-bottom:12px;">
        <tr>
            <td style="vertical-align:top; width:75%;">
                <div style="margin-bottom:6px;">
                    <span class="logo-box" style="padding:4px 8px; border-radius:6px; background:#ffffff; display:inline-block; border:1px solid #e2e8f0;">
                        <img src="{{ $logo }}" alt="Vignan's Logo" style="height:44px; display:block;">
                    </span>
                </div>
                <div style="font-size:16px; font-weight:bold; color:#0F2A4A; line-height:1.2;">
                    Vignan’s Foundation for Science, Technology &amp; Research
                </div>
                <div style="font-size:10px; color:#5d5d63; margin-top:2px;">(Deemed to be University)</div>
                <div style="font-size:17px; font-weight:bold; color:#8c1c2e; margin:6px 0 2px 0; letter-spacing:0.3px;">
                    Admission Information Form
                </div>
                <div style="font-size:11px; font-weight:bold; color:#3a3a40;">Doctor of Philosophy (Ph.D.) Programme</div>
                <div style="font-size:9.5px; color:#475569; margin-top:6px;">
                    Academic year: <b style="color:#0F2A4A;">2026–2027</b> &nbsp;|&nbsp;
                    Application No.: <b style="color:#0F2A4A;">{{ $student->id }}</b> &nbsp;|&nbsp;
                    CRM ID: <b style="color:#0F2A4A;">{{ $student->student_id }}</b>
                </div>
            </td>
            <td style="width:25%; text-align:right; vertical-align:top;">
                <div style="width:105px; height:130px; border:1px dashed #5d5d63; background:#fafaf8; border-radius:4px; text-align:center; box-sizing:border-box; overflow:hidden; display:inline-block; vertical-align:top;">
                    @if(!empty($student->photo_path))
                        <img src="{{ $student->photo_path }}" alt="Candidate Photo" style="width:105px; height:130px; object-fit:cover; display:block;">
                    @else
                        <div style="font-size:8px; color:#5d5d63; font-weight:bold; line-height:1.4; padding-top:42px;">
                            Passport-size<br>colour photograph
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div style="font-size:9px; color:#5d5d63; margin-bottom:12px; background:#fafaf8; border-left:3px solid #8c1c2e; padding:5px 8px;">
        Please fill in BLOCK LETTERS. Fields marked * are mandatory. Attach self-attested copies of all certificates listed in Section 4.
    </div>

    {{-- Section 1: Personal details --}}
    <div style="background:#4a4a4f; color:#ffffff; font-size:11px; font-weight:bold; padding:5px 8px; margin:10px 0 6px; border-radius:2px;">
        1 &nbsp; Personal details
    </div>
    <table style="width:100%; border-collapse:collapse; margin-bottom:10px; font-size:9.5px;">
        <tr>
            <td style="width:20%; border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Name of candidate *</td>
            <td style="width:30%; border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ strtoupper($student->name ?: '-') }}</td>
            <td style="width:20%; border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Father’s / Guardian’s name</td>
            <td style="width:30%; border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ strtoupper($student->father_name ?: '-') }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Gender *</td>
            <td style="border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ $student->gender ?: '-' }}</td>
            <td style="border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Date of birth *</td>
            <td style="border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ !empty($student->dob) && $student->dob !== '-' ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : '-' }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Category</td>
            <td style="border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ $student->category ?: '-' }}</td>
            <td style="border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Mobile no. *</td>
            <td style="border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ $student->phone ?: '-' }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Parent’s mobile *</td>
            <td style="border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ $student->father_phone ?: '-' }}</td>
            <td style="border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Email ID *</td>
            <td style="border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ $student->email ?: '-' }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Address for communication</td>
            <td style="border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;" colspan="3">
                {{ $student->address ?: '-' }}, {{ $student->city ?: '-' }}, {{ $student->district ?: '-' }}, {{ $student->state ?: '-' }} - {{ $student->pincode ?: '-' }}
            </td>
        </tr>
    </table>

    {{-- Section 2: Programme details --}}
    <div style="background:#4a4a4f; color:#ffffff; font-size:11px; font-weight:bold; padding:5px 8px; margin:10px 0 6px; border-radius:2px;">
        2 &nbsp; Programme details
    </div>
    <table style="width:100%; border-collapse:collapse; margin-bottom:10px; font-size:9.5px;">
        <tr>
            <td style="width:25%; border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Ph.D. Specialization *</td>
            <td style="width:35%; border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ $student->branch !== '-' ? $student->branch : $student->program }}</td>
            <td style="width:18%; border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">VSAT ID / CRM ID</td>
            <td style="width:22%; border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ $student->student_id }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Research Area</td>
            <td style="border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ $student->branch !== '-' ? $student->branch : $student->program }}</td>
            <td style="border:1px solid #cfcdc7; background:#fafaf8; padding:5px 8px; font-weight:bold; color:#475569;">Campus preference *</td>
            <td style="border:1px solid #cfcdc7; padding:5px 8px; font-weight:bold; color:#1d1d1f;">{{ $student->campus }}</td>
        </tr>
    </table>

    {{-- Section 3: Academic qualifications --}}
    <div style="background:#4a4a4f; color:#ffffff; font-size:11px; font-weight:bold; padding:5px 8px; margin:10px 0 6px; border-radius:2px;">
        3 &nbsp; Academic qualifications
    </div>
    <div style="font-size:9.5px; font-weight:bold; color:#3a3a40; margin:4px 0;">School education</div>
    <table style="width:100%; border-collapse:collapse; margin-bottom:8px; font-size:9px;">
        <thead>
            <tr style="background:#fafaf8; color:#1d1d1f;">
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:left; width:22%;">Examination</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:left; width:34%;">Board / School</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:center; width:11%;">Year of passing</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:center; width:11%;">Total marks</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:center; width:11%;">Marks obtained</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:center; width:11%;">Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border:1px solid #cfcdc7; padding:5px; font-weight:bold;">SSC / 10th Standard</td>
                <td style="border:1px solid #cfcdc7; padding:5px;">{{ $student->academic->ssc_board !== '-' ? $student->academic->ssc_board : ($student->academic->ssc_school !== '-' ? $student->academic->ssc_school : '-') }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->ssc_year ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->ssc_marks_conducted ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->ssc_marks_earned ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center; font-weight:bold; color:#12875B;">{{ $student->academic->ssc_percentage ?: '-' }}</td>
            </tr>
            <tr>
                <td style="border:1px solid #cfcdc7; padding:5px; font-weight:bold;">Intermediate / 10+2 / Diploma</td>
                <td style="border:1px solid #cfcdc7; padding:5px;">{{ $student->academic->inter_board !== '-' ? $student->academic->inter_board : ($student->academic->inter_college !== '-' ? $student->academic->inter_college : '-') }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->inter_year ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->inter_marks_conducted ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->inter_marks_earned ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center; font-weight:bold; color:#12875B;">{{ $student->academic->inter_percentage ?: '-' }}</td>
            </tr>
        </tbody>
    </table>

    <div style="font-size:9.5px; font-weight:bold; color:#3a3a40; margin:6px 0 4px;">UG &amp; PG programme details</div>
    <table style="width:100%; border-collapse:collapse; margin-bottom:10px; font-size:9px;">
        <thead>
            <tr style="background:#fafaf8; color:#1d1d1f;">
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:left; width:22%;">Programme</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:left; width:20%;">Programme with specialization</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:left; width:24%;">College / University</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:center; width:9%;">Year</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:center; width:12%;">Division / Grade</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:center; width:13%;">% of marks</th>
                <th style="border:1px solid #cfcdc7; padding:5px; text-align:center; width:10%;">Mode of study</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border:1px solid #cfcdc7; padding:5px; font-weight:bold;">
                    UG programme<br><small style="color:#5d5d63; font-weight:normal;">(weightage: 20%)</small>
                </td>
                <td style="border:1px solid #cfcdc7; padding:5px;">B.Tech / Degree</td>
                <td style="border:1px solid #cfcdc7; padding:5px;">{{ $student->academic->ug_college !== '-' ? $student->academic->ug_college : ($student->academic->ug_board !== '-' ? $student->academic->ug_board : '-') }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->ug_year ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->ug_status ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center; font-weight:bold; color:#12875B;">{{ $student->academic->ug_percentage ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">Regular</td>
            </tr>
            <tr>
                <td style="border:1px solid #cfcdc7; padding:5px; font-weight:bold;">
                    PG programme<br><small style="color:#5d5d63; font-weight:normal;">(weightage: 30%)</small>
                </td>
                <td style="border:1px solid #cfcdc7; padding:5px;">M.Tech / Master’s</td>
                <td style="border:1px solid #cfcdc7; padding:5px;">{{ $student->academic->pg_college !== '-' ? $student->academic->pg_college : ($student->academic->pg_board !== '-' ? $student->academic->pg_board : '-') }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->pg_year ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">{{ $student->academic->pg_status ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center; font-weight:bold; color:#12875B;">{{ $student->academic->pg_percentage ?: '-' }}</td>
                <td style="border:1px solid #cfcdc7; padding:5px; text-align:center;">Regular</td>
            </tr>
        </tbody>
    </table>

    {{-- Section 4: Enclosures checklist --}}
    @php
        $uploadedKeys = array_keys((array)($student->uploaded_files ?? []));
        $hasTenth = in_array('marksheet10', $uploadedKeys);
        $hasInter = in_array('marksheet12', $uploadedKeys);
        $hasUg = in_array('btech', $uploadedKeys) || in_array('ug_cmm', $uploadedKeys);
        $hasPg = in_array('pg1_cmm', $uploadedKeys) || in_array('pg1_pc', $uploadedKeys) || in_array('pg', $uploadedKeys) || in_array('mtech', $uploadedKeys);
        $hasExp = in_array('experience', $uploadedKeys);
        $hasId = in_array('aadhaar', $uploadedKeys) || in_array('passport', $uploadedKeys);
        $hasPhoto = in_array('photo', $uploadedKeys) || !empty($student->photo_path);
    @endphp
    <div style="background:#4a4a4f; color:#ffffff; font-size:11px; font-weight:bold; padding:5px 8px; margin:10px 0 6px; border-radius:2px;">
        4 &nbsp; Enclosures checklist
    </div>
    <table style="width:100%; border-collapse:collapse; margin-bottom:10px; font-size:9px;">
        <tr>
            <td style="width:50%; border:1px solid #cfcdc7; padding:4px 8px;">
                <b style="color:{{ $hasTenth ? '#12875B' : '#5d5d63' }};">[{{ $hasTenth ? '✓' : ' ' }}]</b> &nbsp; SSC / 10th marks memo
            </td>
            <td style="width:50%; border:1px solid #cfcdc7; padding:4px 8px;">
                <b style="color:{{ $hasInter ? '#12875B' : '#5d5d63' }};">[{{ $hasInter ? '✓' : ' ' }}]</b> &nbsp; Intermediate / 10+2 marks memo
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #cfcdc7; padding:4px 8px;">
                <b style="color:{{ $hasUg ? '#12875B' : '#5d5d63' }};">[{{ $hasUg ? '✓' : ' ' }}]</b> &nbsp; UG degree certificate &amp; marks memos
            </td>
            <td style="border:1px solid #cfcdc7; padding:4px 8px;">
                <b style="color:{{ $hasPg ? '#12875B' : '#5d5d63' }};">[{{ $hasPg ? '✓' : ' ' }}]</b> &nbsp; PG degree certificate &amp; marks memos
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #cfcdc7; padding:4px 8px;">
                <b>[ ]</b> &nbsp; GATE / CSIR / NET / CAT score card (if any)
            </td>
            <td style="border:1px solid #cfcdc7; padding:4px 8px;">
                <b style="color:{{ $hasExp ? '#12875B' : '#5d5d63' }};">[{{ $hasExp ? '✓' : ' ' }}]</b> &nbsp; Experience certificate(s) (if any)
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #cfcdc7; padding:4px 8px;">
                <b>[ ]</b> &nbsp; Publication proofs (if any)
            </td>
            <td style="border:1px solid #cfcdc7; padding:4px 8px;">
                <b style="color:{{ ($student->category !== 'General' && $student->category !== 'OC' && $student->category !== '-') ? '#12875B' : '#5d5d63' }};">[{{ ($student->category !== 'General' && $student->category !== 'OC' && $student->category !== '-') ? '✓' : ' ' }}]</b> &nbsp; Caste / category certificate (if applicable)
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #cfcdc7; padding:4px 8px;">
                <b style="color:{{ $hasId ? '#12875B' : '#5d5d63' }};">[{{ $hasId ? '✓' : ' ' }}]</b> &nbsp; Photo ID proof (Aadhaar / Passport etc.)
            </td>
            <td style="border:1px solid #cfcdc7; padding:4px 8px;">
                <b style="color:{{ $hasPhoto ? '#12875B' : '#5d5d63' }};">[{{ $hasPhoto ? '✓' : ' ' }}]</b> &nbsp; Passport-size photographs
            </td>
        </tr>
    </table>

    {{-- Section 5: Declaration by the candidate --}}
    <div style="background:#4a4a4f; color:#ffffff; font-size:11px; font-weight:bold; padding:5px 8px; margin:10px 0 6px; border-radius:2px;">
        5 &nbsp; Declaration by the candidate
    </div>
    <div style="border:1px solid #cfcdc7; background:#fafaf8; padding:8px 10px; font-size:9px; line-height:1.5; color:#1d1d1f; border-radius:3px;">
        I hereby declare that all the information furnished in this form is true and correct to the best of my knowledge and belief. I understand that any false information or suppression of facts will lead to the cancellation of my candidature / admission. I have read the eligibility criteria and admission procedure given at the end of this form and agree to abide by the rules and regulations of VFSTR.<br>
        <span style="font-weight:bold; color:#1f6b3a; display:inline-block; margin-top:4px;">☑ I agree to the declaration</span>
    </div>

    <table style="width:100%; margin-top:14px; font-size:9.5px;">
        <tr>
            <td style="width:33%;">Place: <b style="color:#0F2A4A;">{{ $student->city !== '-' ? $student->city : ($student->district !== '-' ? $student->district : 'Guntur') }}</b></td>
            <td style="width:34%; text-align:center;">Date: <b style="color:#0F2A4A;">{{ $student->applied_at ?: now()->format('d/m/Y') }}</b></td>
            <td style="width:33%; text-align:right;">Signature: <b style="color:#0F2A4A;">{{ $student->name }}</b></td>
        </tr>
    </table>

    {{-- Selection weightage at a glance --}}
    <div style="margin-top:14px; border:1px solid #cfcdc7; border-radius:4px; padding:8px 10px; background:#ffffff;">
        <div style="font-size:10px; font-weight:bold; color:#8c1c2e; margin-bottom:6px;">Selection Weightage at a Glance</div>
        <table style="width:100%; border-collapse:collapse; text-align:center; font-size:8.5px;">
            <tr>
                <td style="border:1px solid #cfcdc7; padding:4px; background:#fafaf8;"><b style="font-size:11px; color:#0F2A4A; display:block;">20%</b>UG degree</td>
                <td style="border:1px solid #cfcdc7; padding:4px; background:#fafaf8;"><b style="font-size:11px; color:#0F2A4A; display:block;">30%</b>PG degree</td>
                <td style="border:1px solid #cfcdc7; padding:4px; background:#fafaf8;"><b style="font-size:11px; color:#0F2A4A; display:block;">5%</b>Experience</td>
                <td style="border:1px solid #cfcdc7; padding:4px; background:#fafaf8;"><b style="font-size:11px; color:#0F2A4A; display:block;">5%</b>Publications</td>
                <td style="border:1px solid #cfcdc7; padding:4px; background:#fafaf8;"><b style="font-size:11px; color:#0F2A4A; display:block;">15%</b>Written test</td>
                <td style="border:1px solid #cfcdc7; padding:4px; background:#fafaf8;"><b style="font-size:11px; color:#0F2A4A; display:block;">25%</b>Interview</td>
                <td style="border:1px solid #cfcdc7; padding:4px; background:#fafaf8;"><b style="font-size:11px; color:#12875B; display:block;">100%</b>Total</td>
            </tr>
        </table>
    </div>

    {{-- For Office Use Only --}}
    <div style="margin-top:14px; border:1px dashed #5d5d63; border-radius:4px; padding:10px 12px; background:#ffffff;">
        <div style="background:#4a4a4f; color:#ffffff; font-size:10px; font-weight:bold; padding:4px 8px; margin:-10px -12px 8px -12px; border-radius:3px 3px 0 0;">
            For office use only – Department verification
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:8.5px;">
            <thead>
                <tr style="background:#fafaf8;">
                    <th style="border:1px solid #cfcdc7; padding:4px; text-align:left;">Component</th>
                    <th style="border:1px solid #cfcdc7; padding:4px; text-align:left;">Basis of scoring</th>
                    <th style="border:1px solid #cfcdc7; padding:4px; text-align:center; width:12%;">Max. marks</th>
                    <th style="border:1px solid #cfcdc7; padding:4px; text-align:center; width:16%;">Marks awarded</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="border:1px solid #cfcdc7; padding:4px; font-weight:bold;">UG degree details</td>
                    <td style="border:1px solid #cfcdc7; padding:4px;">Academic performance (% marks) &amp; institution last attended</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">20</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">____</td>
                </tr>
                <tr>
                    <td style="border:1px solid #cfcdc7; padding:4px; font-weight:bold;">PG degree details</td>
                    <td style="border:1px solid #cfcdc7; padding:4px;">Academic performance (% marks) &amp; institution last attended</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">30</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">____</td>
                </tr>
                <tr>
                    <td style="border:1px solid #cfcdc7; padding:4px; font-weight:bold;">Experience</td>
                    <td style="border:1px solid #cfcdc7; padding:4px;">1 mark for each year of experience</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">5</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">____</td>
                </tr>
                <tr>
                    <td style="border:1px solid #cfcdc7; padding:4px; font-weight:bold;">Publications</td>
                    <td style="border:1px solid #cfcdc7; padding:4px;">2 marks / journal paper &amp; 1 mark / conference paper</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">5</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">____</td>
                </tr>
                <tr style="background:#f8fafc; font-weight:bold;">
                    <td style="border:1px solid #cfcdc7; padding:4px;" colspan="2">Total marks out of 60</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">60</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">____</td>
                </tr>
                <tr>
                    <td style="border:1px solid #cfcdc7; padding:4px; font-weight:bold;">Written test</td>
                    <td style="border:1px solid #cfcdc7; padding:4px;">Exempted for GATE / CSIR / NET / CAT qualified candidates</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">15</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">____</td>
                </tr>
                <tr>
                    <td style="border:1px solid #cfcdc7; padding:4px; font-weight:bold;">Interview</td>
                    <td style="border:1px solid #cfcdc7; padding:4px;">Conducted by the department</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">25</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">____</td>
                </tr>
                <tr style="background:#f8fafc; font-weight:bold; color:#0F2A4A;">
                    <td style="border:1px solid #cfcdc7; padding:4px;" colspan="2">Grand total (selection score)</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">100</td>
                    <td style="border:1px solid #cfcdc7; padding:4px; text-align:center;">____</td>
                </tr>
            </tbody>
        </table>
        <div style="font-size:8px; color:#5d5d63; margin-top:6px; text-align:center;">
            Selection committee: BoR (Chairman) &bull; Head of the Department (Member) &bull; School Dean (Member) &bull; Dean R&amp;D (Member)
        </div>
    </div>

    <div class="footer" style="position:relative; margin-top:14px; text-align:center; font-size:8px; color:#6B778A;">
        Vignan’s Foundation for Science, Technology &amp; Research (Deemed to be University) &nbsp;|&nbsp; https://admissions.vignan.ac.in
    </div>

</div>
@else
<div class="page">

    {{-- Header --}}
    <table class="header">
        <tr>
            <td style="width:50%">
                <span class="logo-box"><img src="{{ $logo }}" alt="Vignan's"></span>
                <div class="meta">Application date: {{ $student->applied_at ?? now()->format('d/m/Y') }} &nbsp;|&nbsp; admissions.vignan.ac.in</div>
            </td>
            <td style="width:50%">
                <div class="title">APPLICATION FORM</div>
                <div class="subtitle">University Admission Application<br>Please read all details carefully before signing</div>
            </td>
        </tr>
    </table>

    <div class="wrap">

        {{-- Profile strip --}}
        <table class="profile">
            <tr>
                <td style="width:70px">
                    @if (!empty($student->photo_path))
                        <div class="photo-filled"><img src="{{ $student->photo_path }}" width="58" height="72" style="display:block; width:58px; height:72px; object-fit:cover;" alt=""></div>
                    @else
                        <div class="photo">AFFIX<br>PASSPORT<br>PHOTO</div>
                    @endif
                </td>
                <td>
                    <div class="pname">{{ $student->name }}</div>
                    <div class="pmeta">
                        {{ $student->email }} &nbsp;|&nbsp; {{ $student->phone }}<br>
                        Applicant ID: {{ !empty($student->student_id) ? $student->student_id : '____________________' }} (App #: {{ $student->id }})
                    </div>
                </td>
                <td style="text-align:right">
                    <span class="pill {{ $paid ? 'ok' : 'warn' }}">PAYMENT {{ strtoupper($student->payment_status ?? 'PENDING') }}</span><br>
                    <span class="pill warn">PROVISIONAL SEAT ALLOTMENT</span>
                </td>
            </tr>
        </table>

        {{-- Cards --}}
        <table class="grid">
            <tr>
                <td class="gap-r">
                    <div class="card">
                        <h3>1. Candidate &amp; Parent Details</h3>
                        <table class="fields">
                            <tr><td class="l">Full name</td><td class="v">{{ $student->name }}</td></tr>
                            <tr><td class="l">Father / Parent</td><td class="v">{{ $student->parent_name ?: ($student->father_name ?: 'N/A') }}</td></tr>
                            <tr><td class="l">Parent Contact</td><td class="v">{{ $student->parent_phone ?: ($student->father_phone ?: 'N/A') }}</td></tr>
                            <tr><td class="l">Candidate Phone</td><td class="v">{{ $student->phone }}</td></tr>
                            <tr><td class="l">Email ID</td><td class="v">{{ $student->email }}</td></tr>
                            <tr><td class="l">Date of birth</td><td class="v">{{ $dob }}</td></tr>
                            <tr><td class="l">Gender</td><td class="v">{{ $student->gender ?: 'N/A' }}</td></tr>
                            <tr><td class="l">Category</td><td class="v">{{ $student->category ?: 'General' }}</td></tr>
                        </table>
                    </div>
                </td>
                <td class="gap-l">
                    <div class="card">
                        <h3>2. Address Information</h3>
                        <table class="fields">
                            <tr><td class="l">Door / Street</td><td class="v">{{ $student->street ?: 'N/A' }}</td></tr>
                            <tr><td class="l">City / Mandal</td><td class="v">{{ $student->city ?: 'N/A' }}</td></tr>
                            <tr><td class="l">District</td><td class="v">{{ $student->district ?: 'N/A' }}</td></tr>
                            <tr><td class="l">State</td><td class="v">{{ $student->state ?: 'N/A' }}</td></tr>
                            <tr><td class="l">Country</td><td class="v">{{ $student->country ?: 'India' }}</td></tr>
                            <tr><td class="l">Pincode</td><td class="v">{{ $student->pincode ?: 'N/A' }}</td></tr>
                            <tr><td class="l">Exam Slot</td><td class="v">{{ $student->exam_date ?: 'Stage 5' }} ({{ $student->exam_slot ?: 'Assigned' }})</td></tr>
                            <tr><td class="l">Program</td><td class="v">{{ $student->program ?: 'Undergraduate' }}</td></tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Educational Information (10th SSC, 12th Inter, UG & PG) --}}
        <div class="card" style="margin-top:12px">
            <h3>3. Qualifying Academic Records (10th SSC &amp; 12th Intermediate{{ !empty($student->academic->ug_college) ? ', Undergraduate' : '' }}{{ !empty($student->academic->pg_college) ? ' &amp; Postgraduate' : '' }})</h3>
            <table class="fields" style="width:100%; border-collapse:collapse;">
                <tr style="background:#F8FAFC; font-weight:bold; color:#475569;">
                    <td style="padding:4px 6px; width:18%;">Standard</td>
                    <td style="padding:4px 6px; width:30%;">School / College</td>
                    <td style="padding:4px 6px; width:22%;">Board</td>
                    <td style="padding:4px 6px; width:10%;">Year</td>
                    <td style="padding:4px 6px; width:10%;">Roll No</td>
                    <td style="padding:4px 6px; width:10%; text-align:right;">% Marks</td>
                </tr>
                <tr>
                    <td style="padding:4px 6px; font-weight:bold;">10th Standard (SSC)</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->ssc_school) && $student->academic->ssc_school !== '-') ? $student->academic->ssc_school : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->ssc_board) && $student->academic->ssc_board !== '-') ? $student->academic->ssc_board : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->ssc_year) && $student->academic->ssc_year !== '-') ? $student->academic->ssc_year : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->ssc_roll) && $student->academic->ssc_roll !== '-') ? $student->academic->ssc_roll : '-' }}</td>
                    <td style="padding:4px 6px; text-align:right; font-weight:bold; color:#12875B;">{{ (!empty($student->academic->ssc_percentage) && $student->academic->ssc_percentage !== 'N/A' && $student->academic->ssc_percentage !== '-') ? $student->academic->ssc_percentage : '-' }}</td>
                </tr>
                <tr>
                    <td style="padding:4px 6px; font-weight:bold;">12th / Inter (10+2)</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->inter_college) && $student->academic->inter_college !== '-') ? $student->academic->inter_college : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->inter_board) && $student->academic->inter_board !== '-') ? $student->academic->inter_board : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->inter_year) && $student->academic->inter_year !== '-') ? $student->academic->inter_year : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->inter_roll) && $student->academic->inter_roll !== '-') ? $student->academic->inter_roll : '-' }}</td>
                    <td style="padding:4px 6px; text-align:right; font-weight:bold; color:#12875B;">{{ (!empty($student->academic->inter_percentage) && $student->academic->inter_percentage !== 'N/A' && $student->academic->inter_percentage !== '-') ? $student->academic->inter_percentage : '-' }}</td>
                </tr>
                @if((!empty($student->academic->ug_college) && $student->academic->ug_college !== '-') || ($student->degree_level ?? '') === 'mtech' || ($student->degree_level ?? '') === 'phd')
                <tr>
                    <td style="padding:4px 6px; font-weight:bold;">Undergraduate (UG)</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->ug_college) && $student->academic->ug_college !== '-') ? $student->academic->ug_college : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->ug_board) && $student->academic->ug_board !== '-') ? $student->academic->ug_board : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->ug_year) && $student->academic->ug_year !== '-') ? $student->academic->ug_year : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->ug_roll) && $student->academic->ug_roll !== '-') ? $student->academic->ug_roll : '-' }}</td>
                    <td style="padding:4px 6px; text-align:right; font-weight:bold; color:#12875B;">{{ (!empty($student->academic->ug_percentage) && $student->academic->ug_percentage !== 'N/A' && $student->academic->ug_percentage !== '-') ? $student->academic->ug_percentage : '-' }}</td>
                </tr>
                @endif
                @if((!empty($student->academic->pg_college) && $student->academic->pg_college !== '-') || ($student->degree_level ?? '') === 'phd')
                <tr>
                    <td style="padding:4px 6px; font-weight:bold;">Postgraduate 1</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->pg_college) && $student->academic->pg_college !== '-') ? $student->academic->pg_college : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->pg_board) && $student->academic->pg_board !== '-') ? $student->academic->pg_board : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->pg_year) && $student->academic->pg_year !== '-') ? $student->academic->pg_year : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->pg_roll) && $student->academic->pg_roll !== '-') ? $student->academic->pg_roll : '-' }}</td>
                    <td style="padding:4px 6px; text-align:right; font-weight:bold; color:#12875B;">{{ (!empty($student->academic->pg_percentage) && $student->academic->pg_percentage !== 'N/A' && $student->academic->pg_percentage !== '-') ? $student->academic->pg_percentage : '-' }}</td>
                </tr>
                @endif
                @if((!empty($student->academic->pg2_college) && $student->academic->pg2_college !== '-') || ($student->degree_level ?? '') === 'phd')
                <tr>
                    <td style="padding:4px 6px; font-weight:bold;">Postgraduate 2</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->pg2_college) && $student->academic->pg2_college !== '-') ? $student->academic->pg2_college : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->pg2_board) && $student->academic->pg2_board !== '-') ? $student->academic->pg2_board : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->pg2_year) && $student->academic->pg2_year !== '-') ? $student->academic->pg2_year : '-' }}</td>
                    <td style="padding:4px 6px;">{{ (!empty($student->academic->pg2_roll) && $student->academic->pg2_roll !== '-') ? $student->academic->pg2_roll : '-' }}</td>
                    <td style="padding:4px 6px; text-align:right; font-weight:bold; color:#12875B;">{{ (!empty($student->academic->pg2_percentage) && $student->academic->pg2_percentage !== 'N/A' && $student->academic->pg2_percentage !== '-') ? $student->academic->pg2_percentage : '-' }}</td>
                </tr>
                @endif
            </table>
        </div>

        <table class="grid" style="margin-top:12px;">
            <tr>
                <td class="gap-r">
                    <div class="card">
                        <h3>4. Payment Information</h3>
                        <table class="fields">
                            <tr><td class="l">Amount</td><td class="v">INR {{ number_format((float) $student->payment_amount) }}</td></tr>
                            <tr><td class="l">Method</td><td class="v">{{ $student->payment_method }}</td></tr>
                            <tr><td class="l">Transaction ID</td><td class="v" style="font-size:9px">{{ $student->transaction_id }}</td></tr>
                            <tr><td class="l">Status</td>
                                <td class="v"><span class="pill {{ $paid ? 'ok' : 'warn' }}" style="margin:0">{{ strtoupper($student->payment_status ?? 'PENDING') }}</span></td></tr>
                        </table>
                    </div>
                </td>
                <td class="gap-l">
                    <div class="card">
                        <h3>5. Documents Verification</h3>
                        <table class="docs">
                            @foreach (array_chunk($docs, 2, true) as $row)
                                <tr>
                                    @foreach ($row as $key => $label)
                                        @php
                                            $mime = $uploaded[$key] ?? null;
                                            $has  = !empty($mime);
                                            $type = $has ? strtoupper(str_replace(['image/', 'application/'], '', $mime)) : 'Pending';
                                        @endphp
                                        <td><div class="chip {{ $has ? 'ok' : 'warn' }}">{{ $label }} <small>{{ $type }}</small></div></td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Instructions --}}
        <div class="note">
            <b>INSTRUCTIONS</b>
            <ul>
                <li>This is a provisional seat allotment. Original documents must be submitted at the time of joining.</li>
                <li>Tuition fee and scholarship eligibility are subject to university norms.</li>
                <li>All information is valid as of the approval date.</li>
            </ul>
        </div>

        {{-- Signatures --}}
        <table class="sign">
            <tr>
                <td><div class="line">Signature of Parent</div></td>
                <td><div class="line">Date</div></td>
                <td><div class="line">Signature of Student</div></td>
            </tr>
        </table>

        {{-- Office use --}}
        <div class="office">
            <b>FOR OFFICE USE ONLY</b>
            <table><tr>
                <td>Application No.: {{ $student->id }}</td>
                <td>Verified by: ____________</td>
                <td>Date: ____________</td>
            </tr></table>
        </div>
    </div>

    <div class="footer">Vignan's Foundation for Science, Technology &amp; Research (Deemed to be University) &nbsp;|&nbsp; https://admissions.vignan.ac.in</div>
</div>
@endif

{{-- ATTACHED APPLICATION DOCUMENTS (CONSECUTIVE PAGES IN DOSSIER) --}}
@php
    $attachedFiles = (array) ($student->uploaded_files ?? []);
    $attachIdx = 0;
    $totalAttached = count($attachedFiles);
@endphp

@if($totalAttached > 0)
    @foreach($attachedFiles as $docKey => $doc)
        @php
            $attachIdx++;
            $docExt = strtoupper($doc['ext'] ?? pathinfo($doc['filename'] ?? '', PATHINFO_EXTENSION) ?: 'DOC');
            $cleanExt = strtolower($docExt);
            $isImg = !empty($doc['is_image']) || in_array($cleanExt, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg']);
            
            // Robustly resolve the local filesystem path for the document (avoids double public_path prepending)
            $absCandidates = [
                $doc['abs_path'] ?? null,
                $doc['path'] ?? null,
                !empty($doc['path']) ? public_path($doc['path']) : null,
                !empty($doc['rel_path']) ? public_path($doc['rel_path']) : null,
                !empty($doc['filename']) ? public_path('uploads/documents/' . ($student->id ?? '') . '/' . $doc['filename']) : null,
            ];
            $realPath = null;
            foreach ($absCandidates as $cand) {
                if (!empty($cand) && is_file($cand)) {
                    $realPath = $cand;
                    break;
                }
            }

            $fUrl = $doc['url'] ?? '';
            $imgSrc = null;
            if ($isImg) {
                if ($realPath && is_file($realPath)) {
                    $mime = $doc['mime'] ?? ('image/' . ($cleanExt === 'jpg' ? 'jpeg' : $cleanExt));
                    $imgSrc = 'data:' . $mime . ';base64,' . base64_encode((string) file_get_contents($realPath));
                } else {
                    $imgSrc = $fUrl;
                }
            }
        @endphp

        <div class="page-attachment">
            {{-- Header --}}
            <table class="header">
                <tr>
                    <td style="width:50%">
                        <span class="logo-box"><img src="{{ $logo }}" alt="Vignan's"></span>
                        <div class="meta">
                            Application No: <b>{{ $student->id }}</b> &nbsp;|&nbsp; CRM ID: <b>{{ $student->student_id }}</b>
                        </div>
                    </td>
                    <td style="width:50%">
                        <div class="title" style="font-size:18px;">ATTACHED DOSSIER DOCUMENT</div>
                        <div class="subtitle">
                            Attachment {{ $attachIdx }} of {{ $totalAttached }} &bull; {{ $doc['title'] ?? ucwords(str_replace('_', ' ', $docKey)) }}
                        </div>
                    </td>
                </tr>
            </table>

            <div class="wrap" style="padding:16px 12mm;">
                {{-- Meta details card --}}
                <div class="card" style="margin-top:10px;">
                    <h3>Attachment Details &bull; {{ $docExt }}</h3>
                    <table class="fields">
                        <tr>
                            <td class="l">Document Title</td>
                            <td class="v">{{ $doc['title'] ?? 'Uploaded Document' }}</td>
                            <td class="l" style="padding-left:16px;">File Name</td>
                            <td class="v">{{ $doc['filename'] }}</td>
                        </tr>
                        <tr>
                            <td class="l">Candidate Name</td>
                            <td class="v">{{ $student->name }}</td>
                            <td class="l" style="padding-left:16px;">Verification Status</td>
                            <td class="v" style="color:#12875B;">VERIFIED &amp; ATTACHED</td>
                        </tr>
                    </table>
                </div>

                {{-- Document image / preview box --}}
                <div style="margin-top:14px; background:#ffffff; border:1px solid #E3E8EF; border-radius:10px; padding:14px; text-align:center;">
                    @if($isImg && $imgSrc)
                        <img src="{{ $imgSrc }}" alt="{{ $doc['title'] }}" style="max-width:100%; max-height:200mm; object-fit:contain; border-radius:4px; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    @else
                        <div style="padding:48px 20px; color:#475569;">
                            <div style="font-size:36px; margin-bottom:10px;">📄</div>
                            <div style="font-size:14px; font-weight:bold; color:#0F2A4A;">{{ $doc['filename'] }}</div>
                            <div style="font-size:11px; color:#64748b; margin-top:6px;">
                                Attached {{ $docExt }} Document &bull; Verified and filed in University Admissions Dossier
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Verification footer block --}}
                <div class="office" style="margin-top:20px;">
                    <b>ADMISSION VERIFICATION RECORD</b>
                    <table><tr>
                        <td>Document: {{ $doc['title'] }}</td>
                        <td>Verified by: ____________</td>
                        <td>Seal / Date: ____________</td>
                    </tr></table>
                </div>
            </div>

            <div class="footer">Vignan's Foundation for Science, Technology &amp; Research &nbsp;|&nbsp; Application Dossier Attachment &bull; Page {{ $attachIdx + 1 }}</div>
        </div>
    @endforeach
@endif
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

