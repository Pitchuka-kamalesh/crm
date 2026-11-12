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
    @page { size: A4; margin: 0; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Helvetica, Arial, sans-serif; color: #1B2430; font-size: 11px; background: #F4F7FB; }
    .page { width: 210mm; height: 297mm; margin: 0 auto; background: #F4F7FB; position: relative; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 42, 74, 0.12); }
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

    /* download toolbar (hidden in the PDF and when printing) */
    .toolbar { width: 210mm; margin: 0 auto; padding: 14px 0; display: flex; justify-content: space-between; align-items: center; }
    .btn { display: inline-block; background: #1E7BD8; color: #fff; text-decoration: none; font-weight: bold; font-size: 13px;
           padding: 10px 20px; border-radius: 8px; box-shadow: 0 2px 6px rgba(30,123,216,.35); cursor: pointer; border: none; }
    .btn:hover { background: #0F2A4A; }
    .btn-back { background: #fff; color: #0F2A4A; border: 1px solid #cbd5e1; box-shadow: none; }
    .btn-back:hover { background: #e2e8f0; color: #0F2A4A; }
    .btn svg { vertical-align: -3px; margin-right: 6px; }
    @media print {
        body { background: #fff; }
        .page { box-shadow: none; }
        .toolbar { display: none; }
    }
</style>
</head>
<body>
@if (empty($isPdf))
    <div class="toolbar">
        <a class="btn btn-back" href="{{ route('dashboards.index', ['step' => 4]) }}">
            &larr; Back to Portal
        </a>
        <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-back" onclick="window.print()">Print A4</button>
            <a class="btn" href="{{ $downloadUrl ?? route('application.download', $student->id) }}">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/></svg>Download PDF
            </a>
        </div>
    </div>
@endif
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
                        <div class="photo-filled"><img src="{{ $student->photo_path }}" width="58" height="72" style="display:block; width:58px; height:72px;" alt="Photo"></div>
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
                        <h3>1. Personal Information</h3>
                        <table class="fields">
                            <tr><td class="l">Full name</td><td class="v">{{ $student->name }}</td></tr>
                            <tr><td class="l">Email</td><td class="v">{{ $student->email }}</td></tr>
                            <tr><td class="l">Phone</td><td class="v">{{ $student->phone }}</td></tr>
                            <tr><td class="l">Date of birth</td><td class="v">{{ $dob }}</td></tr>
                            <tr><td class="l">Gender</td><td class="v">{{ $student->gender }}</td></tr>
                            <tr><td class="l">Category</td><td class="v">{{ $student->category }}</td></tr>
                        </table>
                    </div>
                </td>
                <td class="gap-l">
                    <div class="card">
                        <h3>2. Educational Information</h3>
                        <table class="fields">
                            <tr><td class="l">School</td><td class="v">{{ $student->school_name }}</td></tr>
                            <tr><td class="l">Board</td><td class="v">{{ $student->board }}</td></tr>
                            <tr><td class="l">Percentage</td><td class="v">{{ number_format((float) $student->percentage, 2) }} %</td></tr>
                            <tr><td class="l">Passing year</td><td class="v">{{ $student->passing_year }}</td></tr>
                            <tr><td class="l">Roll number</td><td class="v">{{ $student->roll_number }}</td></tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr><td colspan="2" style="height:12px"></td></tr>
            <tr>
                <td class="gap-r">
                    <div class="card">
                        <h3>3. Address Information</h3>
                        <table class="fields">
                            <tr><td class="l">Street</td><td class="v">{{ $student->street }}</td></tr>
                            <tr><td class="l">City</td><td class="v">{{ $student->city }}</td></tr>
                            <tr><td class="l">State</td><td class="v">{{ $student->state }}</td></tr>
                            <tr><td class="l">Pincode</td><td class="v">{{ $student->pincode }}</td></tr>
                            <tr><td class="l">Country</td><td class="v">{{ $student->country }}</td></tr>
                        </table>
                    </div>
                </td>
                <td class="gap-l">
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
            </tr>
        </table>

        {{-- Documents --}}
        <div class="card" style="margin-top:12px">
            <h3>5. Documents Uploaded</h3>
            <table class="docs">
                @foreach (array_chunk($docs, 3, true) as $row)
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
@if (!empty($autoPrint))
<script>
    window.addEventListener('load', function() {
        setTimeout(function() { window.print(); }, 350);
    });
</script>
@endif
</body>
</html>

