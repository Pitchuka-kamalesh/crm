{{--
    resources/views/application-form.blade.php
    Official Vignan University Admission Information Form.
    Engineered for pixel-perfect browser printing and DomPDF download engine.
--}}
@php
    $logo = $logo ?? asset('images/vignan-logo.png');

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

    $isUg = !$isPhd && !$isPg;

    $uploadedKeys = array_keys((array)($student->uploaded_files ?? []));
    $hasTenth = in_array('marksheet10', $uploadedKeys);
    $hasInter = in_array('marksheet12', $uploadedKeys);
    $hasUg = in_array('btech', $uploadedKeys) || in_array('ug_cmm', $uploadedKeys);
    $hasPg = in_array('pg1_cmm', $uploadedKeys) || in_array('pg1_pc', $uploadedKeys) || in_array('pg', $uploadedKeys) || in_array('mtech', $uploadedKeys);
    $hasTc = in_array('tc', $uploadedKeys);
    $hasExp = in_array('experience', $uploadedKeys);
    $hasId = in_array('aadhaar', $uploadedKeys) || in_array('passport', $uploadedKeys);
    $hasPhoto = in_array('photo', $uploadedKeys) || !empty($student->photo_path);

    $isMale = strtolower($student->gender ?? '') === 'male';
    $isFemale = strtolower($student->gender ?? '') === 'female';
    $isOtherGender = !$isMale && !$isFemale && !empty($student->gender) && $student->gender !== '-';

    $catUpper = strtoupper($student->category ?? '');
    $isGen = in_array($catUpper, ['GENERAL', 'OC', 'OPEN']);
    $isObc = str_contains($catUpper, 'OBC') || str_contains($catUpper, 'BC');
    $isSc = ($catUpper === 'SC');
    $isSt = ($catUpper === 'ST');
    $isPwd = in_array($catUpper, ['PWD', 'PH', 'OTHER']);

    // Dynamic campuses from database table
    $campusesList = \App\Models\Campus::where('is_active', true)->orWhereNull('is_active')->orderBy('campus_id')->get();
    if ($campusesList->isEmpty()) {
        $campusesList = collect([
            (object)['campus_id' => 1, 'campus_short_name' => 'Vignan Main Campus', 'campus_code' => 'VDM']
        ]);
    }

    // Base64 PNG checkbox icons (100% valid and compatible with DomPDF and browser print)
    $checkboxChecked = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAaklEQVQ4jWNgoBAwMjAwMPTI6P0nR3PJk0uMTJS6gGIDWEhRXPz4IpzdK6tPmguwaSbaAFyaMQwofnwRRTEhzThdgG4IPoCRDnBpxmY71nSATSE2MRggGIj4NDMw4EgHhDSR5AKaG0AxAABgPSD4iCmrRgAAAABJRU5ErkJggg==';
    $checkboxUnchecked = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAP0lEQVQ4jWNgoBAwMjAwMKSUdP8nR/OcnlJGJkpdwILMmd1dQpSm1NIeOJtiF4waMGoAAwNaSkROYXRzAcUAAI+9CIZl5aTxAAAAAElFTkSuQmCC';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Admission Information Form - {{ $student->name }}</title>
<style>
    @page {
        size: A4 portrait;
        margin: 8mm 10mm 8mm 10mm;
    }
    * {
        box-sizing: border-box;
    }
    body {
        margin: 0;
        padding: 0;
        font-family: Helvetica, Arial, sans-serif;
        color: #1d1d1f;
        font-size: 11px;
        line-height: 1.35;
        background: {{ !empty($isPdf) ? '#ffffff' : '#f1f5f9' }};
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .page {
        width: 100%;
        margin: 0;
        padding: 0;
        background: #ffffff;
        position: relative;
    }
    .page-break {
        page-break-before: always;
        break-before: page;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
    }
    td, th {
        vertical-align: top;
    }
    .sec-head {
        background: #4a4a4f;
        color: #ffffff;
        font-size: 11.5px;
        font-weight: bold;
        padding: 4px 8px;
        margin: 9px 0 5px 0;
        border-radius: 2px;
        letter-spacing: 0.3px;
    }
    .lbl {
        font-size: 9.5px;
        font-weight: bold;
        color: #475569;
        margin-bottom: 2px;
    }
    .lbl-req {
        color: #8c1c2e;
    }
    .box-val {
        background: #fafaf8;
        border: 1px solid #cfcdc7;
        border-radius: 2px;
        padding: 4px 6px;
        font-size: 11px;
        font-weight: bold;
        color: #1d1d1f;
        min-height: 16px;
    }
    .tbl-data {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #cfcdc7;
    }
    .tbl-data th {
        background: #fafaf8;
        border: 1px solid #cfcdc7;
        padding: 4px 6px;
        font-size: 10px;
        font-weight: bold;
        text-align: left;
        color: #1d1d1f;
    }
    .tbl-data td {
        border: 1px solid #cfcdc7;
        padding: 4px 6px;
        font-size: 10.5px;
        color: #1d1d1f;
    }
    .tbl-data td.rl {
        background: #fafaf8;
        font-weight: bold;
        white-space: nowrap;
    }
    .page-footer {
        text-align: center;
        font-size: 8.5px;
        color: #64748b;
        border-top: 1px solid #e2e8f0;
        padding-top: 5px;
        margin-top: 10px;
    }
    .mini-header {
        border-bottom: 1px solid #cbd5e1;
        padding-bottom: 4px;
        margin-bottom: 8px;
        font-size: 9.5px;
        color: #64748b;
    }
    .icon-symbol {
        width: 12px;
        height: 12px;
        vertical-align: -1px;
        margin-right: 3px;
    }
    .toolbar {
        width: 100%;
        max-width: 210mm;
        margin: 0 auto;
        padding: 10px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .btn {
        display: inline-block;
        background: #1E7BD8;
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        font-size: 12px;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        border: none;
    }
    .btn:hover { background: #0F2A4A; }
    .btn-back { background: #fff; color: #0F2A4A; border: 1px solid #cbd5e1; }
    .btn-back:hover { background: #e2e8f0; }

@if(empty($isPdf))
    @media screen {
        body {
            padding: 16px 0;
        }
        .page {
            max-width: 210mm;
            margin: 0 auto 16px auto;
            padding: 8mm 10mm;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border-radius: 4px;
        }
    }
@endif

    @media print {
        body {
            background: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .page {
            box-shadow: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
            width: 100% !important;
        }
        .toolbar {
            display: none !important;
        }
    }
</style>
</head>
<body>

@if (empty($isPdf))
    <div class="toolbar">
        <a class="btn btn-back" href="{{ route('dashboards.index', ['step' => 6]) }}">
            &larr; Back to Portal
        </a>
        <div style="display:flex; gap:8px;">
            <button type="button" class="btn btn-back" onclick="window.print()">🖨️ Print A4</button>
            <a class="btn" download href="{{ $downloadUrl ?? route('application.download', $student->id) }}" style="background:#16a34a;">
                📥 Download PDF
            </a>
        </div>
    </div>
@endif

<!-- ========================================================================= -->
<!-- PAGE 1: HEADER + SECTION 1 (PERSONAL) + SECTION 2 (PROGRAMME)            -->
<!-- ========================================================================= -->
<div class="page">
    {{-- Header: Logo beside Vignan text, Details on left, Photo box on right --}}
    <table style="width: 100%; border-bottom: 2px solid #1d1d1f; padding-bottom: 8px; margin-bottom: 8px;">
        <tr>
            <td style="vertical-align: top;">
                {{-- Top University Name with Logo Beside It --}}
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px;">
                    <tr>
                        <td style="width: 44px; vertical-align: middle; padding-right: 8px;">
                            <img src="{{ $logo }}" alt="Logo" style="height: 42px; display: block;">
                        </td>
                        <td style="vertical-align: middle;">
                            <div style="font-size: 15.5px; font-weight: bold; color: #1d1d1f; line-height: 1.15;">
                                Vignan’s Foundation for Science, Technology &amp; Research
                            </div>
                            <div style="font-size: 9.5px; color: #5d5d63; margin-top: 1px;">
                                (Deemed to be University)
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- Form Title & Programme Name (Left Aligned) --}}
                <div style="font-size: 13.5px; font-weight: bold; color: #8c1c2e; margin-top: 4px; margin-bottom: 1px;">
                    Admission Information Form
                </div>
                <div style="font-size: 11px; font-weight: bold; color: #1d1d1f; margin-bottom: 5px;">
                    {{ $isPhd ? 'Doctor of Philosophy (Ph.D.) Programme' : ($student->program ?: 'Undergraduate / Postgraduate Programme') }}
                </div>

                {{-- Metadata Row (Academic year, Application No., CRM ID) --}}
                <table style="border-collapse: collapse;">
                    <tr>
                        <td style="font-size: 10.5px; font-weight: bold; padding-right: 14px;">
                            Academic year: <span style="display:inline-block; padding: 2px 6px; background: #fafaf8; border: 1px solid #cfcdc7; border-radius: 2px;">2026–2027</span>
                        </td>
                        <td style="font-size: 10.5px; font-weight: bold; padding-right: 14px;">
                            Application No.: <span style="display:inline-block; padding: 2px 6px; background: #fafaf8; border: 1px solid #cfcdc7; border-radius: 2px;">{{ $student->id ?: '-' }}</span>
                        </td>
                        <td style="font-size: 10.5px; font-weight: bold;">
                            CRM ID: <span style="display:inline-block; padding: 2px 6px; background: #fafaf8; border: 1px solid #cfcdc7; border-radius: 2px;">{{ $student->student_id ?: '-' }}</span>
                        </td>
                    </tr>
                </table>
            </td>

            {{-- Right Column: Passport Photograph --}}
            <td style="width: 95px; vertical-align: top; text-align: right;">
                <div style="width: 90px; height: 112px; border: 1px dashed #5d5d63; background: #fafaf8; border-radius: 3px; text-align: center; overflow: hidden; display: inline-block;">
                    @if(!empty($student->photo_path))
                        <img src="{{ $student->photo_path }}" alt="Photo" style="width: 90px; height: 112px; object-fit: cover; display: block;">
                    @else
                        <div style="padding-top: 36px; font-size: 8px; color: #64748b; line-height: 1.3;">
                            Passport-size<br>colour photograph
                        </div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    {{-- SECTION 1: PERSONAL DETAILS --}}
    <div class="sec-head">1 &nbsp;Personal details</div>
    <table style="width: 100%;">
        <tr>
            <td colspan="2" style="padding-bottom: 5px;">
                <div class="lbl">Name of the candidate <span class="lbl-req">*</span></div>
                <div class="box-val">{{ strtoupper($student->name ?: '-') }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom: 5px;">
                <div class="lbl">Father’s / Guardian’s name</div>
                <div class="box-val">{{ strtoupper($student->father_name ?: ($student->parent_name ?: '-')) }}</div>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding-right: 6px; padding-bottom: 5px;">
                <div class="lbl">Gender <span class="lbl-req">*</span></div>
                <div class="box-val" style="font-weight: normal;">
                    <img src="{{ $isMale ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol"> Male &nbsp;&nbsp;&nbsp;
                    <img src="{{ $isFemale ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol"> Female &nbsp;&nbsp;&nbsp;
                    <img src="{{ $isOtherGender ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol"> Other
                </div>
            </td>
            <td style="width: 50%; padding-left: 6px; padding-bottom: 5px;">
                <div class="lbl">Date of birth <span class="lbl-req">*</span></div>
                <div class="box-val">{{ !empty($student->dob) && $student->dob !== '-' ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : '-' }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom: 5px;">
                <div class="lbl">Category</div>
                <div class="box-val" style="font-weight: normal;">
                    <img src="{{ $isGen ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol"> General &nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="{{ $isObc ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol"> OBC &nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="{{ $isSc ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol"> SC &nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="{{ $isSt ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol"> ST &nbsp;&nbsp;&nbsp;&nbsp;
                    <img src="{{ $isPwd ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol"> PwD / Other
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding-right: 6px; padding-bottom: 5px;">
                <div class="lbl">Mobile no. <span class="lbl-req">*</span></div>
                <div class="box-val">{{ $student->phone ?: '-' }}</div>
            </td>
            <td style="width: 50%; padding-left: 6px; padding-bottom: 5px;">
                <div class="lbl">Parent’s mobile <span class="lbl-req">*</span></div>
                <div class="box-val">{{ $student->father_phone ?: ($student->parent_phone ?: '-') }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom: 5px;">
                <div class="lbl">Email ID <span class="lbl-req">*</span></div>
                <div class="box-val">{{ $student->email ?: '-' }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom: 5px;">
                <div class="lbl">Address for communication</div>
                <div class="box-val">{{ $student->address ?: '-' }}</div>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding-right: 6px; padding-bottom: 5px;">
                <div class="lbl">City / District</div>
                <div class="box-val">{{ trim(($student->city !== '-' ? $student->city : '') . ($student->district !== '-' ? ', ' . $student->district : '')) ?: '-' }}</div>
            </td>
            <td style="width: 50%; padding-left: 6px; padding-bottom: 5px;">
                <div class="lbl">State / PIN code</div>
                <div class="box-val">{{ trim(($student->state !== '-' ? $student->state : '') . ($student->pincode !== '-' ? ' - ' . $student->pincode : '')) ?: '-' }}</div>
            </td>
        </tr>
    </table>

    {{-- SECTION 2: PROGRAMME DETAILS --}}
    <div class="sec-head">2 &nbsp;Programme details</div>
    <table style="width: 100%;">
        <tr>
            <td colspan="2" style="padding-bottom: 5px;">
                <div class="lbl">{{ $isPhd ? 'Name of the specialization in Ph.D. programme' : 'Programme & Specialization / Branch' }} <span class="lbl-req">*</span></div>
                <div class="box-val">{{ $student->branch !== '-' ? $student->branch : ($student->program !== '-' ? $student->program : '-') }}</div>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; padding-right: 6px; padding-bottom: 5px;">
                <div class="lbl">VSAT ID</div>
                <div class="box-val">{{ $student->student_id ?: '-' }}</div>
            </td>
            <td style="width: 50%; padding-left: 6px; padding-bottom: 5px;">
                <div class="lbl">{{ $isPhd ? 'Research area' : 'Allotted Program' }}</div>
                <div class="box-val">{{ $student->program !== '-' ? $student->program : ($student->branch !== '-' ? $student->branch : '-') }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-bottom: 5px;">
                <div class="lbl">Campus preference <span class="lbl-req">*</span></div>
                <div class="box-val" style="font-weight: normal;">
                    @foreach($campusesList as $c)
                        @php
                            $cShort = $c->campus_short_name ?: $c->campus_code;
                            $isSelectedCampus = (string)($student->campus_id ?? '') === (string)$c->campus_id
                                || (!empty($student->campus) && (str_contains(strtolower($student->campus), strtolower($cShort)) || str_contains(strtolower($student->campus), strtolower($c->campus_code ?? ''))))
                                || ($loop->first && empty($student->campus));
                        @endphp
                        <img src="{{ $isSelectedCampus ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol"> {{ $cShort }} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    @endforeach
                </div>
            </td>
        </tr>
    </table>

    <div class="page-footer">
        Vignan’s Foundation for Science, Technology &amp; Research (Deemed to be University) &nbsp;&bull;&nbsp; Application Dossier &nbsp;&bull;&nbsp; Page 1 of {{ $isPhd ? 3 : 2 }}
    </div>
</div>

<!-- ========================================================================= -->
<!-- PAGE 2: ACADEMIC QUALIFICATIONS + ENCLOSURES + DECLARATION               -->
<!-- ========================================================================= -->
<div class="page page-break">
    <table class="mini-header" style="width: 100%;">
        <tr>
            <td>Vignan’s Foundation for Science, Technology &amp; Research</td>
            <td style="text-align: right;">Application No: <b>{{ $student->id }}</b> &nbsp;|&nbsp; Candidate: <b>{{ $student->name }}</b></td>
        </tr>
    </table>

    {{-- SECTION 3: ACADEMIC QUALIFICATIONS (HIERARCHY SENSITIVE) --}}
    <div class="sec-head">3 &nbsp;Academic qualifications</div>
    <div style="font-size: 10px; font-weight: bold; color: #1d1d1f; margin: 4px 0 3px 0;">School education</div>
    <table class="tbl-data">
        <thead>
            <tr>
                <th style="width: 22%;">Examination</th>
                <th style="width: 32%;">Board / School</th>
                <th style="width: 11%;">Year</th>
                <th style="width: 12%;">Total marks</th>
                <th style="width: 11%;">Marks obt.</th>
                <th style="width: 12%;">Percentage</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="rl">SSC / 10th</td>
                <td>{{ $student->academic->ssc_board !== '-' ? $student->academic->ssc_board : ($student->academic->ssc_school !== '-' ? $student->academic->ssc_school : '-') }}</td>
                <td>{{ $student->academic->ssc_year ?: '-' }}</td>
                <td>{{ $student->academic->ssc_marks_conducted ?: '-' }}</td>
                <td>{{ $student->academic->ssc_marks_earned ?: '-' }}</td>
                <td style="font-weight: bold;">{{ $student->academic->ssc_percentage ?: '-' }}</td>
            </tr>
            <tr>
                <td class="rl">Intermediate / 10+2</td>
                <td>{{ $student->academic->inter_board !== '-' ? $student->academic->inter_board : ($student->academic->inter_college !== '-' ? $student->academic->inter_college : '-') }}</td>
                <td>{{ $student->academic->inter_year ?: '-' }}</td>
                <td>{{ $student->academic->inter_marks_conducted ?: '-' }}</td>
                <td>{{ $student->academic->inter_marks_earned ?: '-' }}</td>
                <td style="font-weight: bold;">{{ $student->academic->inter_percentage ?: '-' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Only show Degree / PG details for applicants whose degree level requires them (PG and PhD applicants) --}}
    @if(!$isUg)
        <div style="font-size: 10px; font-weight: bold; color: #1d1d1f; margin: 8px 0 3px 0;">
            {{ $isPhd ? 'UG & PG programme details' : 'UG programme details' }}
        </div>
        <table class="tbl-data">
            <thead>
                <tr>
                    <th style="width: 18%;">Programme</th>
                    <th style="width: 22%;">Specialization</th>
                    <th style="width: 24%;">College / University</th>
                    <th style="width: 10%;">Year</th>
                    <th style="width: 10%;">Grade</th>
                    <th style="width: 8%;">%</th>
                    <th style="width: 8%;">Mode</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="rl">UG programme<br><small style="color: #64748b; font-weight: normal;">(weightage: 20%)</small></td>
                    <td>B.Tech / Degree</td>
                    <td>{{ $student->academic->ug_college !== '-' ? $student->academic->ug_college : ($student->academic->ug_board !== '-' ? $student->academic->ug_board : '-') }}</td>
                    <td>{{ $student->academic->ug_year ?: '-' }}</td>
                    <td>{{ $student->academic->ug_status ?: '-' }}</td>
                    <td style="font-weight: bold;">{{ $student->academic->ug_percentage ?: '-' }}</td>
                    <td>Regular</td>
                </tr>
                @if($isPhd)
                    <tr>
                        <td class="rl">PG programme<br><small style="color: #64748b; font-weight: normal;">(weightage: 30%)</small></td>
                        <td>M.Tech / Master’s</td>
                        <td>{{ $student->academic->pg_college !== '-' ? $student->academic->pg_college : ($student->academic->pg_board !== '-' ? $student->academic->pg_board : '-') }}</td>
                        <td>{{ $student->academic->pg_year ?: '-' }}</td>
                        <td>{{ $student->academic->pg_status ?: '-' }}</td>
                        <td style="font-weight: bold;">{{ $student->academic->pg_percentage ?: '-' }}</td>
                        <td>Regular</td>
                    </tr>
                @endif
            </tbody>
        </table>
    @endif

    {{-- SECTION 4: ENCLOSURES CHECKLIST (HIERARCHY SENSITIVE) --}}
    <div class="sec-head">4 &nbsp;Enclosures checklist</div>
    <div style="font-size: 9px; color: #64748b; margin-bottom: 4px;">Tick the documents enclosed with this form.</div>
    <table style="width: 100%;">
        <tr>
            <td style="width: 50%; padding: 2px 0;">
                <img src="{{ $hasTenth ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                <span>SSC / 10th marks memo</span>
            </td>
            <td style="width: 50%; padding: 2px 0;">
                <img src="{{ $hasInter ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                <span>Intermediate / 10+2 marks memo</span>
            </td>
        </tr>
        @if(!$isUg)
            <tr>
                <td style="width: 50%; padding: 2px 0;">
                    <img src="{{ $hasUg ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                    <span>UG degree certificate &amp; marks memos</span>
                </td>
                <td style="width: 50%; padding: 2px 0;">
                    @if($isPhd)
                        <img src="{{ $hasPg ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                        <span>PG degree certificate &amp; marks memos</span>
                    @else
                        <img src="{{ $hasTc ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                        <span>Transfer Certificate (T.C.)</span>
                    @endif
                </td>
            </tr>
        @endif
        @if($isPhd)
            <tr>
                <td style="width: 50%; padding: 2px 0;">
                    <img src="{{ $checkboxUnchecked }}" class="icon-symbol">
                    <span>GATE / CSIR / NET / CAT score card (if any)</span>
                </td>
                <td style="width: 50%; padding: 2px 0;">
                    <img src="{{ $hasExp ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                    <span>Experience certificate(s) (if any)</span>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 2px 0;">
                    <img src="{{ $checkboxUnchecked }}" class="icon-symbol">
                    <span>Publication proofs (if any)</span>
                </td>
                <td style="width: 50%; padding: 2px 0;">
                    <img src="{{ ($student->category !== 'General' && $student->category !== 'OC' && $student->category !== '-') ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                    <span>Caste / category certificate (if applicable)</span>
                </td>
            </tr>
        @else
            <tr>
                <td style="width: 50%; padding: 2px 0;">
                    <img src="{{ $hasTc ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                    <span>Transfer Certificate (T.C.)</span>
                </td>
                <td style="width: 50%; padding: 2px 0;">
                    <img src="{{ ($student->category !== 'General' && $student->category !== 'OC' && $student->category !== '-') ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                    <span>Caste / category certificate (if applicable)</span>
                </td>
            </tr>
        @endif
        <tr>
            <td style="width: 50%; padding: 2px 0;">
                <img src="{{ $hasId ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                <span>Photo ID proof (Aadhaar / Passport etc.)</span>
            </td>
            <td style="width: 50%; padding: 2px 0;">
                <img src="{{ $hasPhoto ? $checkboxChecked : $checkboxUnchecked }}" class="icon-symbol">
                <span>Passport-size photographs</span>
            </td>
        </tr>
    </table>

    {{-- SECTION 5: DECLARATION BY THE CANDIDATE --}}
    <div class="sec-head">5 &nbsp;Declaration by the candidate</div>
    <div style="background: #fafaf8; border: 1px solid #cfcdc7; padding: 6px 8px; font-size: 10px; line-height: 1.45; border-radius: 2px;">
        I hereby declare that all the information furnished in this form is true and correct to the best of my knowledge and belief. I understand that any false information or suppression of facts will lead to the cancellation of my candidature / admission. I have read the eligibility criteria and admission procedure given at the end of this form and agree to abide by the rules and regulations of VFSTR.
        <div style="margin-top: 5px; font-weight: bold;">
            <img src="{{ $checkboxChecked }}" class="icon-symbol"> <b>I agree to the declaration</b>
        </div>
    </div>

    <table style="width: 100%; margin-top: 10px;">
        <tr>
            <td style="width: 32%; padding-right: 6px;">
                <div class="lbl">Place</div>
                <div class="box-val">{{ $student->city !== '-' ? $student->city : ($student->district !== '-' ? $student->district : 'Guntur') }}</div>
            </td>
            <td style="width: 32%; padding-right: 6px;">
                <div class="lbl">Date</div>
                <div class="box-val">{{ $student->applied_at ?: now()->format('d/m/Y') }}</div>
            </td>
            <td style="width: 36%;">
                <div class="lbl">Signature of Candidate (Full Name)</div>
                <div class="box-val">{{ $student->name }}</div>
            </td>
        </tr>
    </table>

    {{-- SECTION 6: FOR OFFICE USE ONLY (ADMISSIONS & VERIFICATION) --}}
    @if(!$isPhd)
        <div class="sec-head" style="margin-top: 12px;">6 &nbsp;For office use only – Admission &amp; Verification</div>
        <table class="tbl-data" style="margin-top: 4px;">
            <tr>
                <td class="rl" style="width: 22%;">Admission Status</td>
                <td style="width: 28%; color: #15803d; font-weight: bold;">Provisional Admission Confirmed</td>
                <td class="rl" style="width: 22%;">Fee Payment Status</td>
                <td style="width: 28%; color: #15803d; font-weight: bold;">Verified &amp; Accounted</td>
            </tr>
            <tr>
                <td class="rl">Allotted Program</td>
                <td>{{ $student->program ?: '-' }}</td>
                <td class="rl">Branch / Stream</td>
                <td>{{ $student->branch ?: '-' }}</td>
            </tr>
            <tr>
                <td class="rl">Campus Allotted</td>
                <td>{{ $student->campus ?: ($campusesList->first()->campus_short_name ?? 'Vignan Main Campus') }}</td>
                <td class="rl">Quota / Category</td>
                <td>{{ $student->category ?: 'General' }}</td>
            </tr>
        </table>

        <table style="width: 100%; margin-top: 16px;">
            <tr>
                <td style="width: 33%; text-align: center;">
                    <div style="border-bottom: 1px solid #1d1d1f; height: 26px;"></div>
                    <div style="font-size: 9.5px; font-weight: bold; margin-top: 4px; color: #475569;">Scrutiny Officer</div>
                </td>
                <td style="width: 33%; text-align: center; padding: 0 12px;">
                    <div style="border-bottom: 1px solid #1d1d1f; height: 26px;"></div>
                    <div style="font-size: 9.5px; font-weight: bold; margin-top: 4px; color: #475569;">Finance / Accounts Officer</div>
                </td>
                <td style="width: 34%; text-align: center;">
                    <div style="border-bottom: 1px solid #1d1d1f; height: 26px;"></div>
                    <div style="font-size: 9.5px; font-weight: bold; margin-top: 4px; color: #475569;">Dean – Admissions</div>
                </td>
            </tr>
        </table>
    @endif

    <div class="page-footer">
        Vignan’s Foundation for Science, Technology &amp; Research (Deemed to be University) &nbsp;&bull;&nbsp; Application Dossier &nbsp;&bull;&nbsp; Page 2 of {{ $isPhd ? 3 : 2 }}
    </div>
</div>

@if($isPhd)
<!-- ========================================================================= -->
<!-- PAGE 3: ELIGIBILITY CRITERIA + ADMISSION PROCEDURE + OFFICE VERIFICATION  -->
<!-- ========================================================================= -->
<div class="page page-break">
    <table class="mini-header" style="width: 100%;">
        <tr>
            <td>Vignan’s Foundation for Science, Technology &amp; Research</td>
            <td style="text-align: right;">Application No: <b>{{ $student->id }}</b> &nbsp;|&nbsp; Candidate: <b>{{ $student->name }}</b></td>
        </tr>
    </table>

    <div class="sec-head">Eligibility criteria</div>
    <div style="font-size: 9.5px; line-height: 1.4; color: #1d1d1f; margin-bottom: 6px;">
        <b>Engineering streams:</b> Full-time scholar: first class in both B.Tech and M.Tech, with scholarship. Part-time scholar: as per UGC rules, subject to written test and interview by VFSTR.<br>
        <b>General qualification norms:</b> Minimum 55% in post-graduation as per UGC rules, relaxable by 5% for SC / ST / OBC candidates. Minimum 60% aggregate in the qualifying degree; deserving cases may be referred to RMC. Engineering: PG degree in a relevant engineering discipline. Management: Master’s in business management. Sciences / humanities: relevant PG degree.<br>
        <b>Law:</b> LL.B. from a Bar Council of India recognised institution and regular full-time LL.M. with minimum 55%. UGC NET / SET qualified candidates preferred.<br>
        <b>Pharmaceutical sciences:</b> First class in B.Pharmacy and M.Pharmacy or Pharm.D. GPAT qualified candidates preferred.
    </div>

    <div class="sec-head">Admission procedure</div>
    <div style="font-size: 9.5px; line-height: 1.4; color: #1d1d1f; margin-bottom: 4px;">
        Selection into the Ph.D. programme is evaluated on academic performance (% marks in B.Tech / UG and M.Tech / PG) and institution last attended for Bachelor’s and Master’s degrees (max 20% and 30% marks respectively), previous experience (each year 1 mark, up to 5 marks), publications (2 marks per journal paper and 1 mark per conference paper, up to 5 marks), written test (15% marks) and interview (25% marks).
    </div>

    <table class="tbl-data" style="text-align: center; margin-bottom: 6px;">
        <thead>
            <tr>
                <th style="text-align:center;">20%</th>
                <th style="text-align:center;">30%</th>
                <th style="text-align:center;">5%</th>
                <th style="text-align:center;">5%</th>
                <th style="text-align:center;">15%</th>
                <th style="text-align:center;">25%</th>
                <th style="text-align:center;">100%</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align:center; font-size:9.5px;">UG degree</td>
                <td style="text-align:center; font-size:9.5px;">PG degree</td>
                <td style="text-align:center; font-size:9.5px;">Experience</td>
                <td style="text-align:center; font-size:9.5px;">Publications</td>
                <td style="text-align:center; font-size:9.5px;">Written test</td>
                <td style="text-align:center; font-size:9.5px;">Interview</td>
                <td style="text-align:center; font-size:9.5px; font-weight:bold;">Total</td>
            </tr>
        </tbody>
    </table>

    <div class="sec-head">For office use only – Department verification</div>
    <table class="tbl-data">
        <thead>
            <tr>
                <th style="width: 25%;">Component</th>
                <th style="width: 50%;">Basis of scoring</th>
                <th style="width: 12%; text-align:center;">Max. marks</th>
                <th style="width: 13%; text-align:center;">Marks awarded</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="rl">UG degree details</td>
                <td>Academic performance (% marks) &amp; institution last attended</td>
                <td style="text-align:center;">20</td>
                <td style="text-align:center;">____</td>
            </tr>
            <tr>
                <td class="rl">PG degree details</td>
                <td>Academic performance (% marks) &amp; institution last attended</td>
                <td style="text-align:center;">30</td>
                <td style="text-align:center;">____</td>
            </tr>
            <tr>
                <td class="rl">Experience</td>
                <td>1 mark for each year of experience</td>
                <td style="text-align:center;">5</td>
                <td style="text-align:center;">____</td>
            </tr>
            <tr>
                <td class="rl">Publications</td>
                <td>2 marks / journal paper &amp; 1 mark / conference paper</td>
                <td style="text-align:center;">5</td>
                <td style="text-align:center;">____</td>
            </tr>
            <tr style="background:#fafaf8; font-weight:bold;">
                <td colspan="2" style="font-weight:bold;">Total marks out of 60</td>
                <td style="text-align:center;">60</td>
                <td style="text-align:center;">____</td>
            </tr>
            <tr>
                <td class="rl">Written test</td>
                <td>Exempted for GATE / CSIR / NET / CAT qualified candidates</td>
                <td style="text-align:center;">15</td>
                <td style="text-align:center;">____</td>
            </tr>
            <tr>
                <td class="rl">Interview</td>
                <td>Conducted by the department</td>
                <td style="text-align:center;">25</td>
                <td style="text-align:center;">____</td>
            </tr>
            <tr style="background:#fafaf8; font-weight:bold;">
                <td colspan="2" style="font-weight:bold;">Grand total (selection score)</td>
                <td style="text-align:center;">100</td>
                <td style="text-align:center;">____</td>
            </tr>
        </tbody>
    </table>

    <table style="width: 100%; margin-top: 6px;">
        <tr>
            <td style="width: 50%;">
                <img src="{{ $checkboxUnchecked }}" class="icon-symbol"> Selected &nbsp;&nbsp;&nbsp;
                <img src="{{ $checkboxUnchecked }}" class="icon-symbol"> Not selected
            </td>
            <td style="width: 50%;">
                <b>Research assistantship:</b> &nbsp;
                <img src="{{ $checkboxUnchecked }}" class="icon-symbol"> ERA &nbsp;
                <img src="{{ $checkboxUnchecked }}" class="icon-symbol"> TRA &nbsp;
                <img src="{{ $checkboxUnchecked }}" class="icon-symbol"> CAP &nbsp;
                <img src="{{ $checkboxUnchecked }}" class="icon-symbol"> CRA
            </td>
        </tr>
    </table>

    <div style="margin-top: 6px;">
        <div class="lbl">Remarks (not selected candidates)</div>
        <div style="border: 1px solid #cfcdc7; background: #fafaf8; height: 26px; border-radius: 2px;"></div>
    </div>

    <table style="width: 100%; margin-top: 8px;">
        <tr>
            <td style="width: 35%; padding-right: 6px;">
                <div class="lbl">Department Coordinator</div>
                <div class="box-val">&nbsp;</div>
            </td>
            <td style="width: 25%; padding-right: 6px;">
                <div class="lbl">Designation</div>
                <div class="box-val">&nbsp;</div>
            </td>
            <td style="width: 20%; padding-right: 6px;">
                <div class="lbl">Signature</div>
                <div class="box-val">&nbsp;</div>
            </td>
            <td style="width: 20%;">
                <div class="lbl">Date</div>
                <div class="box-val">&nbsp;</div>
            </td>
        </tr>
    </table>

    <div style="font-size: 8px; color: #64748b; margin-top: 5px;">
        Selection committee: BoR (Chairman) · Head of the Department (Member) · School Dean (Member) · Dean R&amp;D (Member)
    </div>

    <div class="page-footer">
        Vignan’s Foundation for Science, Technology &amp; Research (Deemed to be University) &nbsp;&bull;&nbsp; Application Dossier &nbsp;&bull;&nbsp; Page 3 of 3
    </div>
</div>
@endif

<!-- ========================================================================= -->
<!-- PAGES 4+: ATTACHED DOSSIER DOCUMENTS                                     -->
<!-- ========================================================================= -->
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

        <div class="page page-break">
            {{-- Document Header --}}
            <table style="width: 100%; border-bottom: 2px solid #0F2A4A; padding-bottom: 6px; margin-bottom: 10px;">
                <tr>
                    <td style="width: 50%;">
                        <div style="font-size: 14px; font-weight: bold; color: #0F2A4A;">ATTACHED DOSSIER DOCUMENT</div>
                        <div style="font-size: 9.5px; color: #64748b; margin-top: 2px;">
                            Attachment {{ $attachIdx }} of {{ $totalAttached }} &bull; {{ $doc['title'] ?? ucwords(str_replace('_', ' ', $docKey)) }}
                        </div>
                    </td>
                    <td style="width: 50%; text-align: right;">
                        <div style="font-size: 10px; color: #64748b;">
                            Application No: <b>{{ $student->id }}</b> &nbsp;|&nbsp; Candidate: <b>{{ $student->name }}</b>
                        </div>
                    </td>
                </tr>
            </table>

            {{-- Metadata Table --}}
            <table class="tbl-data" style="margin-bottom: 12px;">
                <tr>
                    <td class="rl" style="width: 20%;">Document Title</td>
                    <td style="width: 30%;">{{ $doc['title'] ?? 'Uploaded Document' }}</td>
                    <td class="rl" style="width: 20%;">File Name</td>
                    <td style="width: 30%;">{{ $doc['filename'] }}</td>
                </tr>
                <tr>
                    <td class="rl">Candidate Name</td>
                    <td>{{ $student->name }}</td>
                    <td class="rl">Verification Status</td>
                    <td style="color: #16a34a; font-weight: bold;">VERIFIED &amp; ATTACHED</td>
                </tr>
            </table>

            {{-- Document Image or Placeholder --}}
            <div style="text-align: center; border: 1px solid #cfcdc7; background: #fafaf8; border-radius: 4px; padding: 10px; min-height: 180mm;">
                @if($isImg && $imgSrc)
                    <img src="{{ $imgSrc }}" alt="{{ $doc['title'] }}" style="max-width: 100%; max-height: 180mm; object-fit: contain; display: inline-block;">
                @else
                    <div style="padding-top: 60mm; color: #64748b;">
                        <div style="font-size: 32px; margin-bottom: 8px;">📄</div>
                        <div style="font-size: 13px; font-weight: bold; color: #0F2A4A;">{{ $doc['filename'] }}</div>
                        <div style="font-size: 10px; color: #64748b; margin-top: 4px;">
                            Attached {{ $docExt }} Document &bull; Verified and filed in University Admissions Dossier
                        </div>
                    </div>
                @endif
            </div>

            <div class="page-footer">
                Vignan’s Foundation for Science, Technology &amp; Research &nbsp;&bull;&nbsp; Application Dossier Attachment &nbsp;&bull;&nbsp; Page {{ ($isPhd ? 3 : 2) + $attachIdx }}
            </div>
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
