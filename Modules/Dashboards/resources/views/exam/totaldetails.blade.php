<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Details - All Registered Students Directory | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css">

    <style>
        .total-details-toolbar {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 8px !important;
            width: 100% !important;
        }
        .total-details-filters-group {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            align-items: center !important;
            gap: 6px !important;
            flex: 1 1 auto !important;
        }
        .filter-select-custom {
            width: auto !important;
            min-width: 115px !important;
            max-width: 135px !important;
            flex: 0 0 auto !important;
            display: inline-block !important;
            padding: 0.35rem 0.5rem !important;
            font-size: 0.76rem !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            color: #1e293b !important;
            font-weight: 500 !important;
            outline: none !important;
            height: 32px !important;
            line-height: 1.2 !important;
            cursor: pointer !important;
            box-sizing: border-box !important;
        }
        .filter-select-custom:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.1) !important;
        }
        .search-input-custom {
            width: 175px !important;
            min-width: 150px !important;
            max-width: 200px !important;
            flex: 0 0 auto !important;
            display: inline-block !important;
            height: 32px !important;
            padding: 0.35rem 0.6rem 0.35rem 1.85rem !important;
            font-size: 0.78rem !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            color: #1e293b !important;
            outline: none !important;
            box-sizing: border-box !important;
        }
        .search-input-custom:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.1) !important;
        }
        .badge-paid {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 2px 7px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-pending {
            background: #fffbeb;
            color: #b45309;
            border: 1px solid #fde68a;
            padding: 2px 7px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-slot-booked {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 2px 7px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .badge-slot-none {
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #e2e8f0;
            padding: 2px 7px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.7rem;
        }
        .btn-excel {
            background: #059669;
            color: #ffffff !important;
            border: none;
            border-radius: 6px;
            padding: 0.45rem 0.9rem;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
            transition: all 0.15s ease;
            cursor: pointer;
        }
        .btn-excel:hover {
            background: #047857;
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(5, 150, 105, 0.28);
        }
        .student-table th {
            background: #f8fafc;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            padding: 9px 10px;
            border-bottom: 2px solid #e2e8f0;
            white-space: nowrap;
        }
        .student-table td {
            padding: 8px 10px;
            font-size: 0.78rem;
            color: #1e293b;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .student-table tr:hover td {
            background: #f8fafc;
        }
        .mono-code {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: #0f172a;
        }
        /* Dossier Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .modal-overlay.active {
            display: flex;
        }
        .dossier-card {
            background: #ffffff;
            border-radius: 12px;
            max-width: 900px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
        .dossier-header {
            padding: 1rem 1.25rem;
            background: linear-gradient(135deg, #0F2A4A 0%, #1a365d 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .dossier-section {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid #e2e8f0;
        }
        .dossier-section:last-child {
            border-bottom: none;
        }
        .dossier-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem 1rem;
            margin-top: 0.5rem;
        }
        .dossier-item-label {
            font-size: 0.68rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 0.03em;
        }
        .dossier-item-value {
            font-size: 0.82rem;
            font-weight: 600;
            color: #0f172a;
            margin-top: 1px;
            word-break: break-word;
        }
    </style>
</head>
<body>
<div class="app-wrapper">
    <!-- 1. LEFT SIDEBAR -->
    @include('layouts.crm-sidebar')

    <!-- 2. MAIN CONTENT AREA -->
    <div class="app-main">
        <!-- Top Navigation Header -->
        <header class="top-bar no-print">
            <div class="top-bar-left">
                <button type="button" class="topbar-toggle-sidebar-btn" onclick="toggleSidebarMenu()" title="Open Navigation Menu" aria-label="Toggle Sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                </button>
                <div class="topbar-page-breadcrumb">
                    <span class="breadcrumb-item breadcrumb-desktop-only">Exams</span>
                    <span class="breadcrumb-separator breadcrumb-desktop-only">/</span>
                    <span class="breadcrumb-item breadcrumb-active">TotalDetails (All Registered Students)</span>
                </div>
            </div>

            <div class="top-bar-right">
                <span class="badge" style="background:#0F2A4A; color:#fff; font-weight:700; padding:4px 10px; border-radius:6px; font-size:0.75rem;">
                    {{ $roleName }}
                </span>
                <span class="badge" style="background:#059669; color:#fff; font-weight:700; padding:4px 10px; border-radius:6px; font-size:0.75rem;">
                    Academic Year 2027
                </span>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="main-body">
            <!-- 1. Compact Single-Line Metrics Summary (No logos, slim one-line) -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:0.6rem 1.1rem; margin-bottom:0.75rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:16px; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
                <div style="display:flex; align-items:baseline; gap:8px;">
                    <span style="font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:0.02em;">Total Registered:</span>
                    <span style="font-size:1.2rem; font-weight:800; color:#0f172a;">{{ number_format($totalRegistered) }}</span>
                </div>
                <div style="width:1px; height:22px; background:#e2e8f0;"></div>
                <div style="display:flex; align-items:baseline; gap:8px;">
                    <span style="font-size:0.75rem; font-weight:700; color:#059669; text-transform:uppercase; letter-spacing:0.02em;">Application Fee Paid:</span>
                    <span style="font-size:1.2rem; font-weight:800; color:#065f46;">{{ number_format($totalPaid) }}</span>
                </div>
                <div style="width:1px; height:22px; background:#e2e8f0;"></div>
                <div style="display:flex; align-items:baseline; gap:8px;">
                    <span style="font-size:0.75rem; font-weight:700; color:#7c3aed; text-transform:uppercase; letter-spacing:0.02em;">Exam Slots Booked:</span>
                    <span style="font-size:1.2rem; font-weight:800; color:#5b21b6;">{{ number_format($totalExamBooked) }}</span>
                </div>
                <div style="width:1px; height:22px; background:#e2e8f0;"></div>
                <div style="display:flex; align-items:baseline; gap:8px;">
                    <span style="font-size:0.75rem; font-weight:700; color:#b45309; text-transform:uppercase; letter-spacing:0.02em;">Fee Pending:</span>
                    <span style="font-size:1.2rem; font-weight:800; color:#92400e;">{{ number_format($totalPending) }}</span>
                </div>
            </div>

            <!-- 2. Horizontal Filters & Actions Toolbar -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; padding:0.6rem 0.9rem; margin-bottom:0.75rem; box-shadow:0 1px 2px rgba(0,0,0,0.02);">
                <form method="GET" action="{{ route('exam.totaldetails') }}" id="filterForm" style="margin:0; width:100%;">
                    <div class="total-details-toolbar">
                        <!-- Horizontal Inputs Group -->
                        <div class="total-details-filters-group">
                            <!-- Search Field -->
                            <div style="position:relative; display:inline-flex; align-items:center;">
                                <svg style="position:absolute; left:8px; pointer-events:none; color:#94a3b8;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                    <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="App No, CRM ID, Name, Ph..." class="search-input-custom">
                            </div>

                            <!-- Program Level Filter -->
                            <select name="programlevel" class="filter-select-custom" onchange="this.form.submit()">
                                <option value="">All Program Levels</option>
                                <option value="BTECH" {{ strcasecmp(request('programlevel'), 'BTECH') === 0 ? 'selected' : '' }}>B.Tech</option>
                                <option value="MTECH" {{ strcasecmp(request('programlevel'), 'MTECH') === 0 ? 'selected' : '' }}>M.Tech</option>
                                <option value="MBA" {{ strcasecmp(request('programlevel'), 'MBA') === 0 ? 'selected' : '' }}>MBA</option>
                                <option value="MCA" {{ strcasecmp(request('programlevel'), 'MCA') === 0 ? 'selected' : '' }}>MCA</option>
                                <option value="BBA" {{ strcasecmp(request('programlevel'), 'BBA') === 0 ? 'selected' : '' }}>BBA</option>
                                <option value="BPHARM" {{ strcasecmp(request('programlevel'), 'BPHARM') === 0 ? 'selected' : '' }}>B.Pharmacy</option>
                                <option value="PHD" {{ strcasecmp(request('programlevel'), 'PHD') === 0 ? 'selected' : '' }}>Ph.D</option>
                            </select>

                            <!-- Inter Group Filter -->
                            <select name="intergroup" class="filter-select-custom" onchange="this.form.submit()">
                                <option value="">All Inter Groups</option>
                                @foreach($distinctGroups as $grp)
                                    <option value="{{ $grp }}" {{ request('intergroup') === $grp ? 'selected' : '' }}>{{ $grp }}</option>
                                @endforeach
                            </select>

                            <!-- Fee Status Filter -->
                            <select name="feestatus" class="filter-select-custom" onchange="this.form.submit()">
                                <option value="">All Fee Status</option>
                                <option value="paid" {{ request('feestatus') === 'paid' ? 'selected' : '' }}>Fee Paid</option>
                                <option value="pending" {{ request('feestatus') === 'pending' ? 'selected' : '' }}>Fee Pending</option>
                            </select>

                            <!-- Exam Slot Filter -->
                            <select name="slotstatus" class="filter-select-custom" onchange="this.form.submit()">
                                <option value="">All Slot Status</option>
                                <option value="booked" {{ request('slotstatus') === 'booked' ? 'selected' : '' }}>Slot Booked</option>
                                <option value="not_booked" {{ request('slotstatus') === 'not_booked' ? 'selected' : '' }}>Not Booked</option>
                            </select>

                            <!-- Submit / Filter Button -->
                            <button type="submit" class="btn" style="background:#1e293b; color:#fff; padding:0.35rem 0.75rem; font-size:0.76rem; font-weight:700; border-radius:6px; border:none; cursor:pointer; height:32px; display:inline-flex; align-items:center;">
                                Filter
                            </button>

                            @if(request()->anyFilled(['search', 'programlevel', 'intergroup', 'feestatus', 'slotstatus']))
                                <a href="{{ route('exam.totaldetails') }}" class="btn" style="background:#f1f5f9; color:#64748b; padding:0.35rem 0.65rem; font-size:0.76rem; font-weight:600; border-radius:6px; text-decoration:none; height:32px; display:inline-flex; align-items:center; border:1px solid #cbd5e1;">
                                    ✕ Reset
                                </a>
                            @endif

                            <!-- Export to Excel (Download Icon Only) -->
                            @php
                                $exportUrl = route('exam.totaldetails.export', request()->query());
                            @endphp
                            <a href="{{ $exportUrl }}" class="btn-excel" style="height:32px; width:36px; padding:0; display:inline-flex; align-items:center; justify-content:center; border-radius:6px; box-sizing:border-box;" title="Export to Excel">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- 3. Student Table Card -->
            <div style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.03);">
                <div style="padding:0.75rem 1rem; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; background:#fafafa;">
                    <div style="font-weight:700; font-size:0.85rem; color:#0f172a;">
                        Registered Students Directory (Showing {{ $students->firstItem() ?? 0 }} - {{ $students->lastItem() ?? 0 }} of {{ $students->total() }})
                    </div>
                    <div style="font-size:0.75rem; color:#64748b;">
                        Click <strong>View Dossier</strong> on any student to view complete profile & academic breakdown
                    </div>
                </div>

                <div style="overflow-x:auto;">
                    <table class="student-table" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr>
                                <th style="text-align:center; width:45px;">S.No</th>
                                <th>App No</th>
                                <th>CRM ID</th>
                                <th>Candidate Name</th>
                                <th>Phone No</th>
                                <th>Parent Mobile</th>
                                <th>Program & Branch</th>
                                <th>Inter Group</th>
                                <th>Fee Status</th>
                                <th>Exam Slot</th>
                                <th>Registered On</th>
                                <th style="text-align:center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $idx => $s)
                                @php
                                    $p = $s->personalInfo;
                                    $a = $s->academicDetails;
                                    $pay = $s->payDetail;
                                    $exam = $s->examSchedule;

                                    $isPaid = ($s->stage4 === 'paid' || $s->stage1 === 'paid' || $s->std_status === 'paid' || ($pay && $pay->txnstatus === 'success'));
                                    $isPhd = strtoupper((string)$s->programlevel) === 'PHD';

                                    // Prepack JSON data for dossier modal
                                    $dossierData = [
                                        'app_no' => $s->applinano,
                                        'crm_id' => $s->crmid,
                                        'name' => $s->name,
                                        'phone' => $s->phoneno,
                                        'email' => $s->emailid,
                                        'program_level' => $s->programlevel,
                                        'program_name' => $s->program?->program_short_name ?: ($s->program?->program_long_name ?: ($s->programlevel ?: 'N/A')),
                                        'branch_name' => $s->branch?->branch_short_name ?: ($s->branch?->branch_long_name ?: 'N/A'),
                                        'campus' => $s->campus?->campus_name ?? ($s->campuscode ?? 'N/A'),
                                        'registered_on' => $s->log_timestamp ? \Carbon\Carbon::parse($s->log_timestamp)->format('d/m/Y h:i A') : 'N/A',
                                        'reg_ip' => $s->log_ipaddress ?? 'N/A',
                                        // Personal
                                        'dob' => $p?->dob ?? 'N/A',
                                        'gender' => $p?->gender ?? 'N/A',
                                        'parent_name' => $p?->parent_name ?? 'N/A',
                                        'parent_phone' => $p?->parentphono ?? 'N/A',
                                        'caste' => $p?->caste_category ?? 'N/A',
                                        'address' => $p?->address ?? 'N/A',
                                        'village' => $p?->village ?? 'N/A',
                                        'city' => $p?->city_mandal ?? 'N/A',
                                        'district' => $p?->district ?? 'N/A',
                                        'state' => $p?->state ?? ($s->state?->state_name ?? 'N/A'),
                                        'country' => $p?->country ?? ($s->country?->country_name ?? 'India'),
                                        'pincode' => $p?->pincode ?? 'N/A',
                                        'referred_by' => $p?->referedby ?? 'Direct',
                                        'emp_code' => $p?->referedempcode ?? 'N/A',
                                        // 10th
                                        'tenth_school' => $a?->tenth_school_name ?? 'N/A',
                                        'tenth_board' => $a?->tenth_study_board ?? 'N/A',
                                        'tenth_roll' => $a?->tenth_roll_no ?? 'N/A',
                                        'tenth_year' => $a?->tenth_passed_year ?? 'N/A',
                                        'tenth_pct' => $a?->tenth_percentage ? ($a->tenth_percentage . '%') : 'N/A',
                                        'tenth_status' => $a?->tenth_current_status ?? 'N/A',
                                        // 12th
                                        'pu2_college' => $a?->pu2_college_name ?? 'N/A',
                                        'pu2_board' => $a?->pu2_study_board ?? 'N/A',
                                        'pu2_group' => $a?->pu2_group ?? 'N/A',
                                        'pu2_roll' => $a?->pu2_roll_no ?? 'N/A',
                                        'pu2_year' => $a?->pu2_passed_year ?? 'N/A',
                                        'pu2_pct' => $a?->pu2_percentage ? ($a->pu2_percentage . '%') : 'N/A',
                                        'pu2_status' => $a?->pu2_current_status ?? 'N/A',
                                        // UG / PG
                                        'ug_college' => $a?->ug_college_name ?? '',
                                        'ug_board' => $a?->ug_university_board ?? '',
                                        'ug_pct' => $a?->ug_percentage ?? '',
                                        'pg_college' => $a?->pg_college_name ?? '',
                                        'pg_pct' => $a?->pg_percentage ?? '',
                                        // Fee
                                        'fee_status' => $isPaid ? 'PAID' : 'PENDING',
                                        'fee_amount' => $pay?->amount ?? ($isPaid ? '1200' : '0'),
                                        'fee_txnid' => $pay?->txnid ?? 'N/A',
                                        'fee_mode' => $pay?->mode ?? $pay?->payment_mode ?? 'N/A',
                                        'fee_date' => $pay?->txndatetime ? \Carbon\Carbon::parse($pay->txndatetime)->format('d/m/Y h:i A') : 'N/A',
                                        // Exam
                                        'exam_status' => $exam ? 'BOOKED' : ($isPhd ? 'EXEMPTED (PHD)' : 'NOT BOOKED'),
                                        'exam_date' => $exam?->examdate ? \Carbon\Carbon::parse($exam->examdate)->format('d/m/Y') : 'N/A',
                                        'exam_slot' => $exam?->exam_slot ?? 'N/A',
                                        'exam_booked_at' => $exam?->logdatetime ? \Carbon\Carbon::parse($exam->logdatetime)->format('d/m/Y h:i A') : 'N/A',
                                    ];
                                @endphp
                                <tr>
                                    <td style="text-align:center; color:#64748b; font-weight:600;">
                                        {{ $students->firstItem() + $idx }}
                                    </td>
                                    <td>
                                        <span class="mono-code" style="color:#1e40af;">#{{ $s->applinano }}</span>
                                    </td>
                                    <td>
                                        <span class="mono-code" style="color:#0f172a;">{{ $s->crmid ?: '-' }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight:700; color:#0f172a;">{{ $s->name }}</div>
                                        <div style="font-size:0.7rem; color:#64748b;">{{ $s->emailid ?: 'No email' }}</div>
                                    </td>
                                    <td>
                                        <a href="tel:{{ $s->phoneno }}" style="color:#2563eb; text-decoration:none; font-weight:600;">
                                            {{ $s->phoneno }}
                                        </a>
                                    </td>
                                    <td>
                                        <span style="color:#475569; font-weight:500;">
                                            {{ $p?->parentphono ?: '-' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight:700; color:#1e293b;">
                                            {{ $s->program?->program_short_name ?: ($s->program?->program_long_name ?: ($s->programlevel ?: 'General')) }}
                                        </div>
                                        <div style="font-size:0.7rem; color:#64748b;">
                                            {{ $s->branch?->branch_short_name ?: ($s->branch?->branch_long_name ?: '-') }}
                                        </div>
                                    </td>
                                    <td>
                                        @if(!empty($a?->pu2_group))
                                            <span style="background:#f1f5f9; color:#0f172a; padding:2px 6px; border-radius:4px; font-weight:700; font-size:0.72rem; border:1px solid #cbd5e1;">
                                                {{ $a->pu2_group }}
                                            </span>
                                        @else
                                            <span style="color:#94a3b8;">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isPaid)
                                            <span class="badge-paid">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg>
                                                PAID
                                            </span>
                                        @else
                                            <span class="badge-pending">
                                                PENDING
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($exam)
                                            <div class="badge-slot-booked">
                                                📅 {{ \Carbon\Carbon::parse($exam->examdate)->format('d M') }} ({{ $exam->exam_slot }})
                                            </div>
                                        @elseif($isPhd)
                                            <span style="color:#94a3b8; font-size:0.72rem; font-weight:600;">N/A (PhD)</span>
                                        @else
                                            <span class="badge-slot-none">Not Booked</span>
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap; color:#64748b; font-size:0.74rem;">
                                        {{ $s->log_timestamp ? \Carbon\Carbon::parse($s->log_timestamp)->format('d/m/Y') : '-' }}
                                    </td>
                                    <td style="text-align:center; white-space:nowrap;">
                                        <button type="button" 
                                                class="btn" 
                                                onclick='openDossierModal(@json($dossierData))'
                                                style="background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; border-radius:6px; padding:3px 8px; font-size:0.72rem; font-weight:700; cursor:pointer;"
                                                title="View Everything about this Student">
                                            🔍 View Dossier
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" style="text-align:center; padding:2.5rem 1rem; color:#64748b;">
                                        <div style="font-size:2rem; margin-bottom:0.5rem;">📂</div>
                                        <div style="font-size:0.95rem; font-weight:700; color:#0f172a;">No student records found</div>
                                        <div style="font-size:0.8rem; margin-top:2px;">Try adjusting your search criteria or resetting filters</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination footer -->
                @if($students->hasPages())
                    <div style="padding:0.75rem 1rem; border-top:1px solid #e2e8f0; background:#ffffff; display:flex; justify-content:flex-end;">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

<!-- 4. Complete Student Dossier Modal -->
<div class="modal-overlay" id="dossierModal" onclick="closeDossierModal(event)">
    <div class="dossier-card" onclick="event.stopPropagation()">
        <div class="dossier-header">
            <div>
                <h3 style="margin:0; font-size:1.05rem; font-weight:800; display:flex; align-items:center; gap:8px;">
                    <span>🎓 Student Dossier:</span>
                    <span id="m_name" style="color:#60a5fa;"></span>
                    <span id="m_app_badge" class="mono-code" style="background:rgba(255,255,255,0.15); padding:2px 8px; border-radius:6px; font-size:0.78rem;"></span>
                </h3>
                <div style="font-size:0.74rem; opacity:0.85; margin-top:3px;">
                    VFSTR V-SAT 2027 Admissions Master Profile
                </div>
            </div>
            <button type="button" onclick="closeDossierModal()" style="background:transparent; border:none; color:#fff; font-size:1.5rem; cursor:pointer; line-height:1;">
                &times;
            </button>
        </div>

        <!-- Section 1: Academic & Application Enrolment -->
        <div class="dossier-section">
            <h4 style="margin:0 0 0.5rem 0; font-size:0.82rem; font-weight:800; color:#0F2A4A; display:flex; align-items:center; gap:6px;">
                <span>📋</span> APPLICATION &amp; COURSE ALLOCATION
            </h4>
            <div class="dossier-grid">
                <div>
                    <div class="dossier-item-label">Application Number</div>
                    <div class="dossier-item-value mono-code" id="m_app_no"></div>
                </div>
                <div>
                    <div class="dossier-item-label">CRM ID</div>
                    <div class="dossier-item-value mono-code" id="m_crm_id"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Program Level</div>
                    <div class="dossier-item-value" id="m_program_level"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Allocated Program</div>
                    <div class="dossier-item-value" id="m_program_name"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Branch</div>
                    <div class="dossier-item-value" id="m_branch_name"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Campus</div>
                    <div class="dossier-item-value" id="m_campus"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Registered Timestamp</div>
                    <div class="dossier-item-value" id="m_registered_on"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Registration IP</div>
                    <div class="dossier-item-value mono-code" id="m_reg_ip"></div>
                </div>
            </div>
        </div>

        <!-- Section 2: Personal Profile & Contact Details -->
        <div class="dossier-section" style="background:#fafafa;">
            <h4 style="margin:0 0 0.5rem 0; font-size:0.82rem; font-weight:800; color:#0F2A4A; display:flex; align-items:center; gap:6px;">
                <span>👤</span> PERSONAL &amp; CONTACT INFORMATION
            </h4>
            <div class="dossier-grid">
                <div>
                    <div class="dossier-item-label">Candidate Mobile</div>
                    <div class="dossier-item-value" id="m_phone"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Email Address</div>
                    <div class="dossier-item-value" id="m_email"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Date of Birth</div>
                    <div class="dossier-item-value" id="m_dob"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Gender</div>
                    <div class="dossier-item-value" id="m_gender"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Father / Parent Name</div>
                    <div class="dossier-item-value" id="m_parent_name"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Parent Mobile</div>
                    <div class="dossier-item-value" id="m_parent_phone"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Caste / Category</div>
                    <div class="dossier-item-value" id="m_caste"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Door No / Address</div>
                    <div class="dossier-item-value" id="m_address"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Village / Town</div>
                    <div class="dossier-item-value" id="m_village"></div>
                </div>
                <div>
                    <div class="dossier-item-label">City / Mandal</div>
                    <div class="dossier-item-value" id="m_city"></div>
                </div>
                <div>
                    <div class="dossier-item-label">District</div>
                    <div class="dossier-item-value" id="m_district"></div>
                </div>
                <div>
                    <div class="dossier-item-label">State &amp; Country</div>
                    <div class="dossier-item-value" id="m_state_country"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Pin Code</div>
                    <div class="dossier-item-value mono-code" id="m_pincode"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Referred By</div>
                    <div class="dossier-item-value" id="m_referred_by"></div>
                </div>
            </div>
        </div>

        <!-- Section 3: Academic Background (10th & 12th / Inter) -->
        <div class="dossier-section">
            <h4 style="margin:0 0 0.5rem 0; font-size:0.82rem; font-weight:800; color:#0F2A4A; display:flex; align-items:center; gap:6px;">
                <span>🎓</span> ACADEMIC CREDENTIALS (10TH &amp; INTERMEDIATE)
            </h4>
            <div class="dossier-grid">
                <div>
                    <div class="dossier-item-label">10th / SSC School</div>
                    <div class="dossier-item-value" id="m_tenth_school"></div>
                </div>
                <div>
                    <div class="dossier-item-label">10th Board</div>
                    <div class="dossier-item-value" id="m_tenth_board"></div>
                </div>
                <div>
                    <div class="dossier-item-label">10th Roll No</div>
                    <div class="dossier-item-value mono-code" id="m_tenth_roll"></div>
                </div>
                <div>
                    <div class="dossier-item-label">10th Passing Year &amp; Marks</div>
                    <div class="dossier-item-value" id="m_tenth_marks"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Inter / 12th College</div>
                    <div class="dossier-item-value" id="m_pu2_college"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Inter Study Board</div>
                    <div class="dossier-item-value" id="m_pu2_board"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Inter Group (Stream)</div>
                    <div class="dossier-item-value" style="font-weight:700; color:#2563eb;" id="m_pu2_group"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Inter Roll No &amp; Marks</div>
                    <div class="dossier-item-value mono-code" id="m_pu2_marks"></div>
                </div>
            </div>
        </div>

        <!-- Section 4: Fee Payment & Entrance Slot Schedule -->
        <div class="dossier-section" style="background:#fafafa;">
            <h4 style="margin:0 0 0.5rem 0; font-size:0.82rem; font-weight:800; color:#0F2A4A; display:flex; align-items:center; gap:6px;">
                <span>💳</span> APPLICATION FEE &amp; ENTRANCE EXAM SLOT
            </h4>
            <div class="dossier-grid">
                <div>
                    <div class="dossier-item-label">Application Fee Status</div>
                    <div class="dossier-item-value" id="m_fee_status"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Fee Amount Paid</div>
                    <div class="dossier-item-value" id="m_fee_amount"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Transaction ID</div>
                    <div class="dossier-item-value mono-code" id="m_fee_txnid"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Payment Date &amp; Mode</div>
                    <div class="dossier-item-value" id="m_fee_date_mode"></div>
                </div>
                <div>
                    <div class="dossier-item-label">V-SAT Exam Slot Status</div>
                    <div class="dossier-item-value" id="m_exam_status"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Scheduled Exam Date</div>
                    <div class="dossier-item-value" id="m_exam_date"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Slot Timing</div>
                    <div class="dossier-item-value" id="m_exam_slot"></div>
                </div>
                <div>
                    <div class="dossier-item-label">Slot Booked At</div>
                    <div class="dossier-item-value" id="m_exam_booked_at"></div>
                </div>
            </div>
        </div>

        <div style="padding:0.75rem 1.25rem; background:#f1f5f9; display:flex; justify-content:flex-end;">
            <button type="button" onclick="closeDossierModal()" class="btn" style="background:#0F2A4A; color:#fff; padding:0.45rem 1.2rem; font-weight:700; border-radius:6px; border:none; cursor:pointer;">
                Close Dossier
            </button>
        </div>
    </div>
</div>

<script>
    function openDossierModal(data) {
        document.getElementById('m_name').textContent = data.name || 'Candidate';
        document.getElementById('m_app_badge').textContent = '#' + (data.app_no || '');
        document.getElementById('m_app_no').textContent = data.app_no || '-';
        document.getElementById('m_crm_id').textContent = data.crm_id || '-';
        document.getElementById('m_program_level').textContent = data.program_level || '-';
        document.getElementById('m_program_name').textContent = data.program_name || '-';
        document.getElementById('m_branch_name').textContent = data.branch_name || '-';
        document.getElementById('m_campus').textContent = data.campus || '-';
        document.getElementById('m_registered_on').textContent = data.registered_on || '-';
        document.getElementById('m_reg_ip').textContent = data.reg_ip || '-';

        document.getElementById('m_phone').textContent = data.phone || '-';
        document.getElementById('m_email').textContent = data.email || '-';
        document.getElementById('m_dob').textContent = data.dob || '-';
        document.getElementById('m_gender').textContent = data.gender || '-';
        document.getElementById('m_parent_name').textContent = data.parent_name || '-';
        document.getElementById('m_parent_phone').textContent = data.parent_phone || '-';
        document.getElementById('m_caste').textContent = data.caste || '-';
        document.getElementById('m_address').textContent = data.address || '-';
        document.getElementById('m_village').textContent = data.village || '-';
        document.getElementById('m_city').textContent = data.city || '-';
        document.getElementById('m_district').textContent = data.district || '-';
        document.getElementById('m_state_country').textContent = (data.state || '') + (data.country ? (', ' + data.country) : '');
        document.getElementById('m_pincode').textContent = data.pincode || '-';
        document.getElementById('m_referred_by').textContent = (data.referred_by || 'Direct') + (data.emp_code && data.emp_code !== 'N/A' ? (' (Emp: ' + data.emp_code + ')') : '');

        document.getElementById('m_tenth_school').textContent = data.tenth_school || '-';
        document.getElementById('m_tenth_board').textContent = data.tenth_board || '-';
        document.getElementById('m_tenth_roll').textContent = data.tenth_roll || '-';
        document.getElementById('m_tenth_marks').textContent = (data.tenth_year ? ('Year: ' + data.tenth_year + ' | ') : '') + (data.tenth_pct || '-');

        document.getElementById('m_pu2_college').textContent = data.pu2_college || '-';
        document.getElementById('m_pu2_board').textContent = data.pu2_board || '-';
        document.getElementById('m_pu2_group').textContent = data.pu2_group || '-';
        document.getElementById('m_pu2_marks').textContent = (data.pu2_roll ? ('Roll: ' + data.pu2_roll + ' | ') : '') + (data.pu2_pct || '-');

        document.getElementById('m_fee_status').textContent = data.fee_status;
        document.getElementById('m_fee_amount').textContent = '₹' + data.fee_amount;
        document.getElementById('m_fee_txnid').textContent = data.fee_txnid || '-';
        document.getElementById('m_fee_date_mode').textContent = (data.fee_date || '-') + (data.fee_mode && data.fee_mode !== 'N/A' ? (' via ' + data.fee_mode) : '');

        document.getElementById('m_exam_status').textContent = data.exam_status;
        document.getElementById('m_exam_date').textContent = data.exam_date || '-';
        document.getElementById('m_exam_slot').textContent = data.exam_slot || '-';
        document.getElementById('m_exam_booked_at').textContent = data.exam_booked_at || '-';

        document.getElementById('dossierModal').classList.add('active');
    }

    function closeDossierModal(e) {
        if (!e || e.target === document.getElementById('dossierModal')) {
            document.getElementById('dossierModal').classList.remove('active');
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDossierModal();
        }
    });
</script>
</body>
</html>
