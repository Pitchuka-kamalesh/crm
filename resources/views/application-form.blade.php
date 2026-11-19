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
<div class="page" style="background:#ffffff; color:#1d1d1f; font-family:'Source Sans 3', Helvetica, Arial, sans-serif; font-size:12px; padding:20px 24px; box-sizing:border-box;">
    <style>
        :root {
            --bg: #f3f2ef; --paper: #fff; --ink: #1d1d1f; --mute: #5d5d63; --line: #cfcdc7;
            --bar: #4a4a4f; --bar-ink: #fff; --accent: #8c1c2e; --accent-ink: #fff; --field: #fafaf8; --ok: #1f6b3a;
        }
        .phd-form-wrap {
            background: #ffffff;
            font: 400 13px/1.5 "Source Sans 3", system-ui, -apple-system, sans-serif;
            color: #1d1d1f;
        }
        .phd-form-wrap header {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 16px;
            align-items: center;
            border-bottom: 2px solid #1d1d1f;
            padding-bottom: 12px;
        }
        .phd-form-wrap .logo-box { display: inline-block; }
        .phd-form-wrap .logo-box img { height: 52px; display: block; }
        .phd-form-wrap .brand { font: 700 19px/1.2 "Source Serif 4", Georgia, serif; margin: 0; color: #1d1d1f; }
        .phd-form-wrap .brand small { display: block; font: 400 11.5px "Source Sans 3", sans-serif; color: #5d5d63; margin-top: 2px; }
        .phd-form-wrap h1 { font: 700 17px/1.2 "Source Serif 4", Georgia, serif; margin: 10px 0 2px; color: #8c1c2e; }
        .phd-form-wrap .meta { display: flex; flex-wrap: wrap; gap: 8px 16px; margin-top: 6px; }
        .phd-form-wrap .meta label { display: flex; gap: 6px; align-items: center; font-weight: 600; font-size: 12px; }
        .phd-form-wrap .photo {
            width: 110px; height: 140px; border: 1px dashed #5d5d63; display: grid; place-items: center;
            text-align: center; font-size: 11px; color: #5d5d63; overflow: hidden; position: relative;
            background: #fafaf8; border-radius: 4px;
        }
        .phd-form-wrap .photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
        .phd-form-wrap .note {
            font-size: 12px; color: #5d5d63; margin: 10px 0; background: #fafaf8;
            border-left: 3px solid #8c1c2e; padding: 6px 10px;
        }
        .phd-form-wrap h2 {
            background: #4a4a4f; color: #ffffff; font: 700 13.5px "Source Sans 3", sans-serif;
            margin: 16px 0 8px; padding: 6px 10px; display: flex; gap: 10px; border-radius: 3px;
        }
        .phd-form-wrap .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px 14px; }
        .phd-form-wrap .full { grid-column: 1 / -1; }
        .phd-form-wrap .f { display: flex; flex-direction: column; gap: 3px; }
        .phd-form-wrap .f > span, .phd-form-wrap .lbl { font-weight: 600; font-size: 12px; color: #1d1d1f; }
        .phd-form-wrap .req::after { content: " *"; color: #8c1c2e; }
        .phd-form-wrap input[type=text], .phd-form-wrap input[type=tel], .phd-form-wrap input[type=email],
        .phd-form-wrap input[type=date], .phd-form-wrap input[type=number], .phd-form-wrap select, .phd-form-wrap textarea {
            width: 100%; font: inherit; color: inherit; background: #fafaf8; border: 1px solid #cfcdc7;
            padding: 6px 8px; border-radius: 3px; font-size: 12px; box-sizing: border-box;
        }
        .phd-form-wrap textarea { resize: vertical; min-height: 50px; }
        .phd-form-wrap .opts { display: flex; flex-wrap: wrap; gap: 6px 16px; padding: 4px 0; }
        .phd-form-wrap .chk { display: flex; gap: 6px; align-items: center; font-size: 12px; color: #1d1d1f; cursor: default; }
        .phd-form-wrap .chk input { accent-color: #8c1c2e; width: 15px; height: 15px; margin: 0; }
        .phd-form-wrap .tw { overflow-x: auto; border: 1px solid #cfcdc7; border-radius: 3px; }
        .phd-form-wrap table { border-collapse: collapse; width: 100%; }
        .phd-form-wrap th, .phd-form-wrap td { border: 1px solid #cfcdc7; padding: 5px 6px; text-align: left; vertical-align: middle; font-size: 11.5px; }
        .phd-form-wrap th { background: #fafaf8; font-size: 11.5px; padding: 6px; font-weight: 700; }
        .phd-form-wrap td input, .phd-form-wrap td select { border: 0; background: transparent; padding: 3px; width: 100%; font-size: 11.5px; }
        .phd-form-wrap td.rl { font-weight: 600; font-size: 11.5px; white-space: nowrap; padding: 5px 8px; background: #fafaf8; }
        .phd-form-wrap td output { display: block; padding: 3px; font-weight: 700; color: #1f6b3a; }
        .phd-form-wrap .decl { border: 1px solid #cfcdc7; padding: 10px 12px; font-size: 12px; background: #fafaf8; border-radius: 3px; line-height: 1.5; }
        .phd-form-wrap .sign { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-top: 10px; }
        .phd-form-wrap .rules { margin-top: 16px; }
        .phd-form-wrap .rules .in { font-size: 11.5px; line-height: 1.5; }
        .phd-form-wrap .rules h3 { font-size: 12px; margin: 10px 0 3px; font-weight: 700; }
        .phd-form-wrap .rules ul, .phd-form-wrap .rules ol { margin: 0; padding-left: 20px; }
        .phd-form-wrap .rules li { margin: 2px 0; }
        .phd-form-wrap .glance { display: grid; grid-template-columns: repeat(7, 1fr); gap: 5px; margin: 8px 0 10px; text-align: center; }
        .phd-form-wrap .glance div { border: 1px solid #cfcdc7; padding: 5px 2px; background: #fafaf8; font-size: 10.5px; border-radius: 3px; }
        .phd-form-wrap .glance b { display: block; font-size: 14px; color: #1d1d1f; }
        .phd-form-wrap .office { margin-top: 16px; border: 2px dashed #cfcdc7; padding: 8px 12px 12px; border-radius: 4px; background: #fff; }
        .phd-form-wrap .office h2 { margin: 4px -12px 10px -12px; border-radius: 0; }
        .phd-form-wrap .office .big td { font-weight: 700; background: #fafaf8; }
        @media print {
            .page { box-shadow: none; margin: 0; padding: 10mm; }
        }
    </style>

    <div class="phd-form-wrap">
        <header>
            <div class="logo-box">
                <img src="{{ $logo }}" alt="Vignan's Logo">
            </div>
            <div>
                <p class="brand">Vignan’s Foundation for Science, Technology &amp; Research<small>(Deemed to be University)</small></p>
                <h1>Admission Information Form</h1>
                <div>{{ $isPhd ? 'Doctor of Philosophy (Ph.D.) Programme' : ($student->program ?: 'Undergraduate / Postgraduate Programme') }}</div>
                <div class="meta">
                    <label>Academic year <input type="text" id="ay" value="2026–2027" size="10" readonly></label>
                    <label>Application No. <input type="text" id="appno" value="{{ $student->id }}" size="10" readonly></label>
                    <label>CRM ID <input type="text" value="{{ $student->student_id }}" size="10" readonly></label>
                </div>
            </div>
            <label class="photo" id="ph">
                @if(!empty($student->photo_path))
                    <img src="{{ $student->photo_path }}" alt="Candidate Photo">
                @else
                    <span>Passport-size<br>colour photograph</span>
                @endif
            </label>
        </header>

        <p class="note">Please fill in BLOCK LETTERS. Fields marked * are mandatory. Attach self-attested copies of all certificates listed in Section 4.</p>

        <h2>1 &nbsp;Personal details</h2>
        <div class="grid">
            <label class="f full"><span class="req">Name of the candidate</span><input type="text" name="name" required style="text-transform:uppercase" value="{{ strtoupper($student->name ?: '-') }}" readonly></label>
            <label class="f full"><span>Father’s / Guardian’s name</span><input type="text" name="father" style="text-transform:uppercase" value="{{ strtoupper($student->father_name ?: ($student->parent_name ?: '-')) }}" readonly></label>
            <div class="f">
                <span class="lbl req">Gender</span>
                <div class="opts" data-group="gender">
                    <label class="chk"><input type="radio" name="gender" value="Male" {{ strtolower($student->gender ?? '') === 'male' ? 'checked' : '' }} disabled><span>Male</span></label>
                    <label class="chk"><input type="radio" name="gender" value="Female" {{ strtolower($student->gender ?? '') === 'female' ? 'checked' : '' }} disabled><span>Female</span></label>
                    <label class="chk"><input type="radio" name="gender" value="Other" {{ !in_array(strtolower($student->gender ?? ''), ['male','female']) && !empty($student->gender) && $student->gender !== '-' ? 'checked' : '' }} disabled><span>Other</span></label>
                </div>
            </div>
            <label class="f"><span class="req">Date of birth</span><input type="text" name="dob" value="{{ !empty($student->dob) && $student->dob !== '-' ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : '-' }}" readonly></label>
            <div class="f full">
                <span class="lbl">Category</span>
                <div class="opts">
                    <label class="chk"><input type="radio" name="cat" value="General" {{ in_array(strtoupper($student->category ?? ''), ['GENERAL', 'OC', 'OPEN']) ? 'checked' : '' }} disabled><span>General</span></label>
                    <label class="chk"><input type="radio" name="cat" value="OBC" {{ str_contains(strtoupper($student->category ?? ''), 'OBC') || str_contains(strtoupper($student->category ?? ''), 'BC') ? 'checked' : '' }} disabled><span>OBC</span></label>
                    <label class="chk"><input type="radio" name="cat" value="SC" {{ strtoupper($student->category ?? '') === 'SC' ? 'checked' : '' }} disabled><span>SC</span></label>
                    <label class="chk"><input type="radio" name="cat" value="ST" {{ strtoupper($student->category ?? '') === 'ST' ? 'checked' : '' }} disabled><span>ST</span></label>
                    <label class="chk"><input type="radio" name="cat" value="PwD" {{ in_array(strtoupper($student->category ?? ''), ['PWD', 'PH', 'OTHER']) ? 'checked' : '' }} disabled><span>PwD / Other</span></label>
                </div>
            </div>
            <label class="f"><span class="req">Mobile no.</span><input type="tel" name="mobile" value="{{ $student->phone ?: '-' }}" readonly></label>
            <label class="f"><span class="req">Parent’s mobile</span><input type="tel" name="pmobile" value="{{ $student->father_phone ?: ($student->parent_phone ?: '-') }}" readonly></label>
            <label class="f full"><span class="req">Email ID</span><input type="email" name="email" value="{{ $student->email ?: '-' }}" readonly></label>
            <label class="f full"><span>Address for communication</span><textarea name="addr" readonly>{{ $student->address ?: '-' }}</textarea></label>
            <label class="f"><span>City / District</span><input type="text" name="city" value="{{ trim(($student->city !== '-' ? $student->city : '') . ($student->district !== '-' ? ', ' . $student->district : '')) ?: '-' }}" readonly></label>
            <label class="f"><span>State / PIN code</span><input type="text" name="state" value="{{ trim(($student->state !== '-' ? $student->state : '') . ($student->pincode !== '-' ? ' - ' . $student->pincode : '')) ?: '-' }}" readonly></label>
        </div>

        <h2>2 &nbsp;Programme details</h2>
        <div class="grid">
            <label class="f full"><span class="req">{{ $isPhd ? 'Name of the specialization in Ph.D. programme' : 'Programme & Specialization / Branch' }}</span><input type="text" name="spec" value="{{ $student->branch !== '-' ? $student->branch : ($student->program !== '-' ? $student->program : '-') }}" readonly></label>
            <label class="f"><span>VSAT ID</span><input type="text" name="vsat" value="{{ $student->student_id ?: '-' }}" readonly></label>
            <label class="f"><span>{{ $isPhd ? 'Research area' : 'Allotted Program' }}</span><input type="text" name="area" value="{{ $student->branch !== '-' ? $student->branch : ($student->program !== '-' ? $student->program : '-') }}" readonly></label>
            <div class="f full">
                <span class="lbl req">Campus preference</span>
                <div class="opts">
                    <label class="chk"><input type="radio" name="campus" value="VFSTR, Guntur" {{ !str_contains(strtolower($student->campus ?? ''), 'hyderabad') ? 'checked' : '' }} disabled><span>VFSTR, Guntur</span></label>
                    <label class="chk"><input type="radio" name="campus" value="Off-Campus, Hyderabad" {{ str_contains(strtolower($student->campus ?? ''), 'hyderabad') ? 'checked' : '' }} disabled><span>Off-Campus, Hyderabad</span></label>
                </div>
            </div>
        </div>

        <h2>3 &nbsp;Academic qualifications</h2>
        <div class="lbl" style="margin-bottom:6px">School education</div>
        <div class="tw">
            <table id="school">
                <thead>
                    <tr><th>Examination</th><th>Board / School</th><th>Year of passing</th><th>Total marks</th><th>Marks obtained</th><th>Percentage</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="rl">SSC</td>
                        <td><input type="text" value="{{ $student->academic->ssc_board !== '-' ? $student->academic->ssc_board : ($student->academic->ssc_school !== '-' ? $student->academic->ssc_school : '-') }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->ssc_year ?: '-' }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->ssc_marks_conducted ?: '-' }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->ssc_marks_earned ?: '-' }}" readonly></td>
                        <td><output>{{ $student->academic->ssc_percentage ?: '-' }}</output></td>
                    </tr>
                    <tr>
                        <td class="rl">Intermediate</td>
                        <td><input type="text" value="{{ $student->academic->inter_board !== '-' ? $student->academic->inter_board : ($student->academic->inter_college !== '-' ? $student->academic->inter_college : '-') }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->inter_year ?: '-' }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->inter_marks_conducted ?: '-' }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->inter_marks_earned ?: '-' }}" readonly></td>
                        <td><output>{{ $student->academic->inter_percentage ?: '-' }}</output></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="lbl" style="margin:16px 0 6px">UG &amp; PG programme details</div>
        <div class="tw">
            <table id="deg">
                <thead>
                    <tr><th>Programme</th><th>Programme with specialization</th><th>College / University</th><th>Year</th><th>Division / grade points</th><th>% of marks</th><th>Mode of study</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="rl">UG programme<br><small style="font-weight:400;color:#5d5d63">(weightage: 20%)</small></td>
                        <td><input type="text" value="B.Tech / Degree" readonly></td>
                        <td><input type="text" value="{{ $student->academic->ug_college !== '-' ? $student->academic->ug_college : ($student->academic->ug_board !== '-' ? $student->academic->ug_board : '-') }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->ug_year ?: '-' }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->ug_status ?: '-' }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->ug_percentage ?: '-' }}" readonly></td>
                        <td><select disabled><option selected>Regular</option><option>Full-time</option><option>Part-time</option><option>Distance</option><option>Online</option></select></td>
                    </tr>
                    <tr>
                        <td class="rl">PG programme<br><small style="font-weight:400;color:#5d5d63">(weightage: 30%)</small></td>
                        <td><input type="text" value="M.Tech / Master’s" readonly></td>
                        <td><input type="text" value="{{ $student->academic->pg_college !== '-' ? $student->academic->pg_college : ($student->academic->pg_board !== '-' ? $student->academic->pg_board : '-') }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->pg_year ?: '-' }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->pg_status ?: '-' }}" readonly></td>
                        <td><input type="text" value="{{ $student->academic->pg_percentage ?: '-' }}" readonly></td>
                        <td><select disabled><option selected>Regular</option><option>Full-time</option><option>Part-time</option><option>Distance</option><option>Online</option></select></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h2>4 &nbsp;Enclosures checklist</h2>
        <p class="note" style="margin-top:0">Tick the documents enclosed with this form.</p>
        <div class="grid" id="enc">
            <label class="chk"><input type="checkbox" name="enc" value="SSC / 10th marks memo" {{ $hasTenth ? 'checked' : '' }} disabled><span>SSC / 10th marks memo</span></label>
            <label class="chk"><input type="checkbox" name="enc" value="Intermediate / 10+2 marks memo" {{ $hasInter ? 'checked' : '' }} disabled><span>Intermediate / 10+2 marks memo</span></label>
            <label class="chk"><input type="checkbox" name="enc" value="UG degree certificate & marks memos" {{ $hasUg ? 'checked' : '' }} disabled><span>UG degree certificate &amp; marks memos</span></label>
            <label class="chk"><input type="checkbox" name="enc" value="PG degree certificate & marks memos" {{ $hasPg ? 'checked' : '' }} disabled><span>PG degree certificate &amp; marks memos</span></label>
            <label class="chk"><input type="checkbox" name="enc" value="GATE / CSIR / NET / CAT score card (if any)" disabled><span>GATE / CSIR / NET / CAT score card (if any)</span></label>
            <label class="chk"><input type="checkbox" name="enc" value="Experience certificate(s) (if any)" {{ $hasExp ? 'checked' : '' }} disabled><span>Experience certificate(s) (if any)</span></label>
            <label class="chk"><input type="checkbox" name="enc" value="Publication proofs (if any)" disabled><span>Publication proofs (if any)</span></label>
            <label class="chk"><input type="checkbox" name="enc" value="Caste / category certificate (if applicable)" {{ ($student->category !== 'General' && $student->category !== 'OC' && $student->category !== '-') ? 'checked' : '' }} disabled><span>Caste / category certificate (if applicable)</span></label>
            <label class="chk"><input type="checkbox" name="enc" value="Photo ID proof (Aadhaar / Passport etc.)" {{ $hasId ? 'checked' : '' }} disabled><span>Photo ID proof (Aadhaar / Passport etc.)</span></label>
            <label class="chk"><input type="checkbox" name="enc" value="Passport-size photographs" {{ $hasPhoto ? 'checked' : '' }} disabled><span>Passport-size photographs</span></label>
        </div>

        <h2>5 &nbsp;Declaration by the candidate</h2>
        <div class="decl">
            I hereby declare that all the information furnished in this form is true and correct to the best of my knowledge and belief. I understand that any false information or suppression of facts will lead to the cancellation of my candidature / admission. I have read the eligibility criteria and admission procedure given at the end of this form and agree to abide by the rules and regulations of VFSTR.
            <label class="chk" style="margin-top:10px"><input type="checkbox" id="agree" checked disabled><span><b>I agree to the declaration</b></span></label>
        </div>
        <div class="sign">
            <label class="f"><span>Place</span><input type="text" name="place" value="{{ $student->city !== '-' ? $student->city : ($student->district !== '-' ? $student->district : 'Guntur') }}" readonly></label>
            <label class="f"><span>Date</span><input type="text" name="sdate" value="{{ $student->applied_at ?: now()->format('d/m/Y') }}" readonly></label>
            <label class="f"><span>Signature of the candidate (type full name)</span><input type="text" name="sign" value="{{ $student->name }}" readonly></label>
        </div>

        <section class="rules" aria-label="Eligibility criteria">
            <h2>Eligibility criteria</h2>
            <div class="in">
                <h3>Engineering streams</h3><ul><li>Full-time scholar: first class in both B.Tech and M.Tech, with scholarship.</li><li>Part-time scholar: as per UGC rules, subject to written test and interview by VFSTR.</li></ul>
                <h3>General qualification norms</h3><ul><li>Minimum 55% in post-graduation as per UGC rules, relaxable by 5% for SC / ST / OBC candidates.</li><li>Minimum 60% aggregate in the qualifying degree; deserving cases may be referred to RMC.</li><li>Engineering: PG degree in a relevant engineering discipline. Management: Master’s in business management. Sciences / humanities: relevant PG degree.</li></ul>
                <h3>Law</h3><ul><li>LL.B. from a Bar Council of India recognised institution and regular full-time LL.M. with minimum 55%. UGC NET / SET qualified candidates preferred.</li></ul>
                <h3>Pharmaceutical sciences</h3><ul><li>First class in B.Pharmacy and M.Pharmacy or Pharm.D. GPAT qualified candidates preferred.</li></ul>
            </div>
        </section>

        <section class="rules" aria-label="Admission procedure">
            <h2>Admission procedure</h2>
            <div class="in">
                <p>Selection into the Ph.D. programme is evaluated on the academic performance (% marks in B.Tech / UG and M.Tech / PG) and the institution last attended for Bachelor’s and Master’s degrees (maximum 20% and 30% marks respectively), previous experience (each year 1 mark, up to 5 marks), publications (2 marks per journal paper and 1 mark per conference paper, up to 5 marks), written test (15% marks) and interview (25% marks).</p>
                <h3>Selection weightage at a glance</h3>
                <div class="glance"><div><b>20%</b>UG degree</div><div><b>30%</b>PG degree</div><div><b>5%</b>Experience</div><div><b>5%</b>Publications</div><div><b>15%</b>Written test</div><div><b>25%</b>Interview</div><div><b>100%</b>Total</div></div>
                <ol><li>Eligible candidates have to appear for a written test and interview.</li><li>All candidates are required to qualify in a written test conducted by VFSTR. Candidates who have qualified in GATE / CSIR / NET / CAT are exempted from the written test, but they should appear for the interview.</li><li>Candidates recommended for selection by the concerned department are to be approved by the Dean (R&amp;D) and Chairman, URB respectively. The approved selection list shall be sent to Dean (Admission) for further action.</li><li>Candidates will be admitted into the Ph.D. programme after payment of prescribed fees and completion of all the admission procedures.</li><li>Admitted candidates will report to the concerned department; a committee appointed by HoD will allot a Research Supervisor for each candidate on mutual consent.</li></ol>
            </div>
        </section>

        <section class="office" aria-label="For office use only">
            <h2>For office use only – Department verification</h2>
            <div class="tw"><table>
                <thead><tr><th>Component</th><th>Basis of scoring</th><th>Max. marks</th><th>Marks awarded</th></tr></thead>
                <tbody>
                <tr><td class="rl">UG degree details</td><td>Academic performance (% marks) &amp; institution last attended</td><td>20</td><td><input type="number" class="osum" min="0" max="20" placeholder="____" readonly></td></tr>
                <tr><td class="rl">PG degree details</td><td>Academic performance (% marks) &amp; institution last attended</td><td>30</td><td><input type="number" class="osum" min="0" max="30" placeholder="____" readonly></td></tr>
                <tr><td class="rl">Experience</td><td>1 mark for each year of experience</td><td>5</td><td><input type="number" class="osum" min="0" max="5" placeholder="____" readonly></td></tr>
                <tr><td class="rl">Publications</td><td>2 marks / journal paper &amp; 1 mark / conference paper</td><td>5</td><td><input type="number" class="osum" min="0" max="5" placeholder="____" readonly></td></tr>
                <tr class="big"><td colspan="2">Total marks out of 60</td><td>60</td><td><output id="o60">0</output></td></tr>
                <tr><td class="rl">Written test</td><td>Exempted for GATE / CSIR / NET / CAT qualified candidates</td><td>15</td><td><input type="number" class="osum" min="0" max="15" placeholder="____" readonly></td></tr>
                <tr><td class="rl">Interview</td><td>Conducted by the department</td><td>25</td><td><input type="number" class="osum" min="0" max="25" placeholder="____" readonly></td></tr>
                <tr class="big"><td colspan="2">Grand total (selection score)</td><td>100</td><td><output id="o100">0</output></td></tr>
                </tbody></table></div>
            <div class="opts" style="margin-top:10px">
                <label class="chk"><input type="radio" name="sel" value="Selected" disabled><span>Selected</span></label>
                <label class="chk"><input type="radio" name="sel" value="Not selected" disabled><span>Not selected</span></label>
                <span class="lbl">Research assistantship:</span>
                <label class="chk"><input type="checkbox" disabled><span>ERA</span></label><label class="chk"><input type="checkbox" disabled><span>TRA</span></label><label class="chk"><input type="checkbox" disabled><span>CAP</span></label><label class="chk"><input type="checkbox" disabled><span>CRA</span></label>
                <label class="chk"><input type="radio" name="trk" value="Industry" disabled><span>Industry</span></label><label class="chk"><input type="radio" name="trk" value="Academics" disabled><span>Academics</span></label>
            </div>
            <label class="f" style="margin-top:10px"><span>Remarks (not selected candidates)</span><textarea readonly placeholder="Department remarks..."></textarea></label>
            <div class="grid" style="margin-top:10px">
                <label class="f"><span>Name of the faculty member (department coordinator)</span><input type="text" readonly></label>
                <label class="f"><span>Designation</span><input type="text" readonly></label>
                <label class="f"><span>Signature</span><input type="text" readonly></label>
                <label class="f"><span>Date</span><input type="text" readonly></label>
            </div>
            <p class="note" style="margin-top:8px">Selection committee: BoR (Chairman) · Head of the Department (Member) · School Dean (Member) · Dean R&amp;D (Member)</p>
        </section>

        <div class="footer" style="position:relative; margin-top:14px; text-align:center; font-size:9px; color:#5d5d63;">
            Vignan’s Foundation for Science, Technology &amp; Research (Deemed to be University) &nbsp;|&nbsp; https://admissions.vignan.ac.in
        </div>
    </div>
</div>

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

