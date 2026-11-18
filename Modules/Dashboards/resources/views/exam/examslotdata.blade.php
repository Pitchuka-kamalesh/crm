<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams Slot Data - V-SAT Examination Schedule | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css">

    <style>
        .slot-pill-s1 {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.76rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .slot-pill-s2 {
            background: #faf5ff;
            color: #7e22ce;
            border: 1px solid #e9d5ff;
            padding: 3px 8px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 0.76rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .status-badge-confirmed {
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            padding: 2px 8px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 0.72rem;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .filter-select {
            padding: 0.38rem 0.65rem;
            font-size: 0.78rem;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: #ffffff;
            color: #1e293b;
            outline: none;
            cursor: pointer;
            height: 34px;
            font-family: inherit;
        }
        .filter-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.1);
        }
        .filter-field-label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 2px;
            display: block;
        }
        .filter-item {
            display: flex;
            flex-direction: column;
        }
        .btn-print-action {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 1rem;
            background: #0f2a4a;
            color: #ffffff;
            border: 1px solid #0f2a4a;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            height: 34px;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(15,42,74,0.15);
        }
        .btn-print-action:hover {
            background: #1e40af;
            border-color: #1e40af;
        }
        .btn-reset-action {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.45rem 0.85rem;
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            height: 34px;
            transition: all 0.15s ease;
        }
        .btn-reset-action:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        /* Proper Print Stylesheet */
        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm 8mm;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
                font-size: 9.5px !important;
            }
            .app-wrapper {
                display: block !important;
            }
            .crm-sidebar, 
            .top-bar, 
            .card-header-bar, 
            .filter-panel, 
            .no-print,
            .topbar-toggle-sidebar-btn {
                display: none !important;
            }
            .app-main {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }
            .main-body {
                padding: 0 !important;
            }
            .content-card {
                border: none !important;
                box-shadow: none !important;
            }
            .print-header-banner {
                display: block !important;
                margin-bottom: 12px;
                border-bottom: 2px solid #0F2A4A;
                padding-bottom: 8px;
            }
            .metrics-compact-row {
                display: flex !important;
                gap: 8px !important;
                margin-bottom: 10px !important;
            }
            .metrics-compact-row > div {
                border: 1px solid #cbd5e1 !important;
                padding: 3px 6px !important;
                flex: 1 !important;
                height: auto !important;
            }
            .crm-table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 9px !important;
            }
            .crm-table th, .crm-table td {
                border: 1px solid #cbd5e1 !important;
                padding: 3.5px 5px !important;
                color: #0f172a !important;
            }
            .crm-table th {
                background: #f1f5f9 !important;
                font-weight: 800 !important;
            }
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
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                    <span>Exams Slot Data Ledger</span>
                    <span style="font-size: 0.74rem; font-weight: 600; color: #64748b; background: #f1f5f9; padding: 2px 8px; border-radius: 4px; border: 1px solid #e2e8f0;">
                        finance.exam_schedule
                    </span>
                </div>
            </div>

            <div class="top-bar-right">
                <a href="{{ route('crm.stddata') }}" class="btn-header-action" title="Go to CRM Enquiries">
                    <span>👥 Student Data</span>
                </a>
                @include('layouts.role-switcher')
            </div>
        </header>

        <!-- Main Body -->
        <main class="main-body" style="padding: 1.25rem 1.75rem;">

            <!-- Print Only Header Banner -->
            <div class="print-header-banner" style="display: none;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="vertical-align: middle; width: 55px;">
                            <img src="/images/vignan-logo.png" style="height: 38px; display: block;" alt="Vignan's">
                        </td>
                        <td style="vertical-align: middle; padding-left: 10px;">
                            <div style="font-size: 15px; font-weight: 800; color: #0F2A4A; line-height: 1.2;">
                                VIGNAN'S FOUNDATION FOR SCIENCE, TECHNOLOGY &amp; RESEARCH (Deemed to be University)
                            </div>
                            <div style="font-size: 10.5px; font-weight: 700; color: #475569; margin-top: 2px;">
                                V-SAT Entrance Examination Schedule &amp; Slot Ledger &bull; Confirmed Candidates
                            </div>
                        </td>
                        <td style="vertical-align: middle; text-align: right; font-size: 9px; color: #64748b;">
                            Printed: {{ now()->format('d M Y, h:i A') }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Metrics Bar: 4 compact 1-line boxes with NO logos -->
            <div class="metrics-compact-row" style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.65rem; margin-bottom: 0.85rem;">
                <!-- Card 1: Total Bookings -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.4rem 0.85rem; display: flex; align-items: center; justify-content: space-between; height: 38px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Total Bookings</span>
                    <span style="font-size: 1.1rem; font-weight: 800; color: #0f172a; font-family: 'JetBrains Mono', monospace;">{{ $totalBookings }}</span>
                </div>

                <!-- Card 2: Slot 1 (Morning) -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.4rem 0.85rem; display: flex; align-items: center; justify-content: space-between; height: 38px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Slot 1 (11 AM - 1 PM)</span>
                    <span style="font-size: 1.1rem; font-weight: 800; color: #1d4ed8; font-family: 'JetBrains Mono', monospace;">{{ $slot1Count }}</span>
                </div>

                <!-- Card 3: Slot 2 (Afternoon) -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.4rem 0.85rem; display: flex; align-items: center; justify-content: space-between; height: 38px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Slot 2 (2 PM - 4 PM)</span>
                    <span style="font-size: 1.1rem; font-weight: 800; color: #7e22ce; font-family: 'JetBrains Mono', monospace;">{{ $slot2Count }}</span>
                </div>

                <!-- Card 4: Confirmed Status -->
                <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 0.4rem 0.85rem; display: flex; align-items: center; justify-content: space-between; height: 38px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                    <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Confirmed Status</span>
                    <span style="font-size: 1.1rem; font-weight: 800; color: #047857; font-family: 'JetBrains Mono', monospace;">{{ $confirmedCount }}</span>
                </div>
            </div>

            <!-- Content Card: Exam Schedules Table -->
            <div class="content-card" style="background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.03); overflow: hidden;">
                
                <!-- Filter Panel: Year, Month, Dates (From/To), Slots (From/To), Search, Reset, Download Button -->
                <div class="card-header-bar filter-panel no-print" style="padding: 0.75rem 1.15rem; border-bottom: 1px solid #e2e8f0; background: #fafafa;">
                    <div style="display: flex; align-items: flex-end; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
                        
                        <!-- Filter Controls Left -->
                        <div style="display: flex; align-items: flex-end; gap: 0.35rem; flex-wrap: wrap;">
                            <!-- 1. Year Filter -->
                            <div class="filter-item">
                                <label class="filter-field-label">Year</label>
                                <select id="yearFilterSelect" class="filter-select" onchange="filterExamTable()" style="width: 88px; height: 32px; font-size: 0.78rem;">
                                    <option value="">All Years</option>
                                    @foreach($distinctYears as $yr)
                                        <option value="{{ $yr }}">{{ $yr }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 2. Month Filter -->
                            <div class="filter-item">
                                <label class="filter-field-label">Month</label>
                                <select id="monthFilterSelect" class="filter-select" onchange="filterExamTable()" style="width: 95px; height: 32px; font-size: 0.78rem;">
                                    <option value="">All Months</option>
                                    @foreach($monthsList as $num => $mName)
                                        <option value="{{ $num }}">{{ $mName }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 3. Slot 1 & Slot 2 Checkboxes -->
                            <div class="filter-item">
                                <label class="filter-field-label">Slots</label>
                                <div style="display: flex; align-items: center; gap: 0.45rem; height: 32px; background: #ffffff; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0 0.45rem;">
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 0.75rem; font-weight: 700; color: #1d4ed8; cursor: pointer; margin: 0;" title="11:00 AM - 1:00 PM">
                                        <input type="checkbox" id="chkSlot1" checked onchange="filterExamTable()" style="accent-color: #2563eb; width: 14px; height: 14px; cursor: pointer;">
                                        <span>Slot 1</span>
                                    </label>
                                    <label style="display: flex; align-items: center; gap: 4px; font-size: 0.75rem; font-weight: 700; color: #7e22ce; cursor: pointer; margin: 0;" title="2:00 PM - 4:00 PM">
                                        <input type="checkbox" id="chkSlot2" checked onchange="filterExamTable()" style="accent-color: #7e22ce; width: 14px; height: 14px; cursor: pointer;">
                                        <span>Slot 2</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Some Gap / Visual Separator -->
                            <div style="width: 1px; height: 24px; background: #cbd5e1; margin: 0 0.2rem; align-self: flex-end; margin-bottom: 4px;"></div>

                            <!-- 4. Start Date -->
                            <div class="filter-item">
                                <label class="filter-field-label">Start Date</label>
                                <input type="date" id="startDateInput" class="filter-select" onchange="filterExamTable()" style="width: 118px; height: 32px; font-size: 0.75rem; padding: 0 0.4rem;" title="Filter bookings from this date">
                            </div>

                            <!-- 5. End Date -->
                            <div class="filter-item">
                                <label class="filter-field-label">End Date</label>
                                <input type="date" id="endDateInput" class="filter-select" onchange="filterExamTable()" style="width: 118px; height: 32px; font-size: 0.75rem; padding: 0 0.4rem;" title="Filter bookings up to this date">
                            </div>

                            <!-- 6. Live Search Input -->
                            <div class="filter-item">
                                <label class="filter-field-label">Search</label>
                                <input type="text"
                                       id="examSearchInput"
                                       placeholder="Name, phone, app #..."
                                       onkeyup="filterExamTable()"
                                       class="filter-select"
                                       style="width: 140px; height: 32px; font-size: 0.78rem;">
                            </div>

                            <!-- 7. Reset Button -->
                            <div class="filter-item">
                                <button type="button" onclick="resetExamFilters()" class="btn-reset-action" style="height: 32px; padding: 0 0.65rem; font-size: 0.78rem;" title="Clear all filters">
                                    <span>↺ Reset</span>
                                </button>
                            </div>
                        </div>

                        <!-- Right Actions: Count badge & Download Button -->
                        <div style="display: flex; align-items: center; gap: 0.45rem;">
                            <span class="filter-count-badge" id="examCountDisplay" style="height: 32px; display: inline-flex; align-items: center; font-size: 0.78rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 0 10px; border-radius: 6px; white-space: nowrap;">
                                Total: {{ $totalBookings }}
                            </span>
                            <button type="button" onclick="downloadExamSlotCsv()" class="btn-print-action" style="height: 32px; padding: 0 0.85rem; font-size: 0.78rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;" title="Download filtered exam slot ledger data as CSV">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="7 10 12 15 17 10"/>
                                    <line x1="12" y1="15" x2="12" y2="3"/>
                                </svg>
                                <span>Download</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="crm-table" id="examScheduleTable" style="width: 100%; border-collapse: collapse; font-size: 0.83rem;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.03em;">
                                <th style="padding: 0.75rem 0.85rem; width: 45px; text-align: center;">#</th>
                                <th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Application #</th>
                                <th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Candidate Name</th>
                                <th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Phone No</th>
                                <th style="padding: 0.75rem 0.85rem; white-space: nowrap;">CRM ID</th>
                                <th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Program &amp; Branch</th>
                                <th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Scheduled Date</th>
                                <th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Exam Slot / Timing</th>
                                <th style="padding: 0.75rem 0.85rem; white-space: nowrap; text-align: center;">Status</th>
                                <th style="padding: 0.75rem 0.85rem; white-space: nowrap;">Booking Details</th>
                                <th class="no-print" style="padding: 0.75rem 0.85rem; white-space: nowrap; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($schedules as $index => $row)
                                @php
                                    $student = $row->student;
                                    $candidateName = $student?->name ?: 'Candidate #' . $row->applino;
                                    $progObj = $student?->program;
                                    $branchObj = $student?->branch;
                                    $progTitle = $progObj ? ($progObj->program_short_name ?: $progObj->program_name) : ($student?->programlevel ?: 'Undergraduate');
                                    $branchTitle = $branchObj ? ($branchObj->branch_short_name ?: $branchObj->branch_name) : '';
                                    
                                    $dt = !empty($row->examdate) ? \Carbon\Carbon::parse($row->examdate) : null;
                                    $dateFormatted = $dt ? $dt->format('d M Y') : 'N/A';
                                    $dateRaw = $dt ? $dt->format('Y-m-d') : '';
                                    $yearRaw = $dt ? $dt->format('Y') : '';
                                    $monthRaw = $dt ? $dt->format('m') : '';
                                    
                                    $slotStr = (string) $row->exam_slot;
                                    $isS1 = str_contains(strtoupper($slotStr), 'S1') || str_contains($slotStr, '11');
                                    $isS2 = str_contains(strtoupper($slotStr), 'S2') || str_contains($slotStr, '2:00') || str_contains($slotStr, '2 PM');
                                    $slotCode = $isS1 ? 'S1' : ($isS2 ? 'S2' : 'OTHER');
                                    
                                    $isConfirmed = (int) $row->exam_status === 1 || empty($row->exam_status);
                                    
                                    $logTimeFormatted = !empty($row->logdatetime) ? \Carbon\Carbon::parse($row->logdatetime)->format('d M Y, h:i A') : 'Recorded';
                                @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;"
                                    data-name="{{ strtolower($candidateName) }}"
                                    data-appli="{{ strtolower((string)$row->applino) }}"
                                    data-phone="{{ strtolower((string)$row->phoneno) }}"
                                    data-crmid="{{ strtolower((string)$row->crmid) }}"
                                    data-date="{{ $dateRaw }}"
                                    data-year="{{ $yearRaw }}"
                                    data-month="{{ $monthRaw }}"
                                    data-slot="{{ $slotCode }}">
                                    
                                    <!-- Index -->
                                    <td style="padding: 0.75rem 0.85rem; text-align: center; color: #94a3b8; font-weight: 600;">
                                        {{ $index + 1 }}
                                    </td>

                                    <!-- Application No -->
                                    <td style="padding: 0.75rem 0.85rem; font-weight: 800; color: #0f172a; white-space: nowrap;">
                                        <a href="{{ route('application.show', ['id' => $row->applino]) }}" target="_blank" style="color: #2563eb; text-decoration: none;" title="Open Candidate Dossier">
                                            #{{ $row->applino }}
                                        </a>
                                    </td>

                                    <!-- Candidate Name -->
                                    <td style="padding: 0.75rem 0.85rem; font-weight: 700; color: #1e293b; white-space: nowrap;">
                                        {{ $candidateName }}
                                    </td>

                                    <!-- Phone Number -->
                                    <td style="padding: 0.75rem 0.85rem; color: #334155; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; white-space: nowrap;">
                                        {{ $row->phoneno ?: 'N/A' }}
                                    </td>

                                    <!-- CRM ID -->
                                    <td style="padding: 0.75rem 0.85rem; white-space: nowrap;">
                                        <span style="display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.76rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;">
                                            {{ $row->crmid ?: ('V-SAT-' . substr((string)$row->applino, -4)) }}
                                        </span>
                                    </td>

                                    <!-- Program & Branch -->
                                    <td style="padding: 0.75rem 0.85rem; white-space: nowrap;">
                                        <div style="font-weight: 700; color: #0f172a;">{{ $progTitle }}</div>
                                        @if($branchTitle)
                                            <div style="font-size: 0.72rem; color: #64748b; margin-top: 1px;">{{ $branchTitle }}</div>
                                        @endif
                                    </td>

                                    <!-- Exam Date -->
                                    <td style="padding: 0.75rem 0.85rem; white-space: nowrap;">
                                        <div style="font-weight: 700; color: #0f172a;">
                                            {{ $dateFormatted }}
                                        </div>
                                    </td>

                                    <!-- Exam Slot -->
                                    <td style="padding: 0.75rem 0.85rem; white-space: nowrap;">
                                        @if($isS1)
                                            <span class="slot-pill-s1">
                                                <span>🌅</span>
                                                <span>{{ $row->exam_slot }}</span>
                                            </span>
                                        @elseif($isS2)
                                            <span class="slot-pill-s2">
                                                <span>☀️</span>
                                                <span>{{ $row->exam_slot }}</span>
                                            </span>
                                        @else
                                            <span style="background: #f1f5f9; color: #334155; padding: 3px 8px; border-radius: 6px; font-weight: 600; font-size: 0.76rem;">
                                                {{ $row->exam_slot ?: 'Assigned' }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td style="padding: 0.75rem 0.85rem; text-align: center; white-space: nowrap;">
                                        @if($isConfirmed)
                                            <span class="status-badge-confirmed">
                                                <span>✓</span> Confirmed
                                            </span>
                                        @else
                                            <span style="background: #fef3c7; color: #92400e; border: 1px solid #fde68a; padding: 2px 8px; border-radius: 9999px; font-size: 0.72rem; font-weight: 700;">
                                                Pending
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Booking Details (Timestamp + IP) -->
                                    <td style="padding: 0.75rem 0.85rem; white-space: nowrap; font-size: 0.76rem; color: #64748b;">
                                        <div>{{ $logTimeFormatted }}</div>
                                        <div style="font-family: monospace; color: #94a3b8; font-size: 0.72rem;">IP: {{ $row->ipaddress ?: '127.0.0.1' }}</div>
                                    </td>

                                    <!-- Action -->
                                    <td class="no-print" style="padding: 0.75rem 0.85rem; text-align: center; white-space: nowrap;">
                                        <a href="{{ route('application.show', ['id' => $row->applino]) }}" target="_blank" class="btn-table-action" style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.74rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 6px; text-decoration: none;">
                                            <span>👁️ View Form</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" style="text-align: center; padding: 3rem 1rem; color: #64748b;">
                                        <div style="font-size: 1rem; font-weight: 700; color: #1e293b;">No Exam Slot Bookings Found</div>
                                        <p style="font-size: 0.82rem; margin: 0.25rem 0 0;">Candidates who complete Stage 5 slot booking will automatically appear in this ledger.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Dynamic Filter & Print Script -->
<script>
    function filterExamTable() {
        const query = (document.getElementById('examSearchInput')?.value || '').toLowerCase().trim();
        const year = document.getElementById('yearFilterSelect')?.value || '';
        const month = document.getElementById('monthFilterSelect')?.value || '';
        const startDate = document.getElementById('startDateInput')?.value || '';
        const endDate = document.getElementById('endDateInput')?.value || '';
        const chkS1 = document.getElementById('chkSlot1')?.checked ?? false;
        const chkS2 = document.getElementById('chkSlot2')?.checked ?? false;

        const table = document.getElementById('examScheduleTable');
        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(function(row) {
            // Check if it's the empty placeholder row
            if (row.children.length === 1 && row.children[0].colSpan > 1) {
                return;
            }

            const rowName = row.getAttribute('data-name') || '';
            const rowAppli = row.getAttribute('data-appli') || '';
            const rowPhone = row.getAttribute('data-phone') || '';
            const rowCrmid = row.getAttribute('data-crmid') || '';
            const rowDate = row.getAttribute('data-date') || '';
            const rowYear = row.getAttribute('data-year') || '';
            const rowMonth = row.getAttribute('data-month') || '';
            const rowSlot = row.getAttribute('data-slot') || '';

            // Search query matches
            let matchesQuery = !query || 
                rowName.includes(query) || 
                rowAppli.includes(query) || 
                rowPhone.includes(query) || 
                rowCrmid.includes(query);

            // Year match
            let matchesYear = !year || rowYear === year;

            // Month match
            let matchesMonth = !month || rowMonth === month;

            // Date Range Match (Start Date & End Date)
            let matchesStartDate = !startDate || (rowDate && rowDate >= startDate);
            let matchesEndDate = !endDate || (rowDate && rowDate <= endDate);

            // Slot Checkboxes Match
            let matchesSlot = true;
            if (chkS1 && !chkS2) {
                matchesSlot = (rowSlot === 'S1');
            } else if (!chkS1 && chkS2) {
                matchesSlot = (rowSlot === 'S2');
            } else if (!chkS1 && !chkS2) {
                matchesSlot = false; // Neither slot checked
            } else {
                matchesSlot = true; // Both checked -> all slots
            }

            if (matchesQuery && matchesYear && matchesMonth && matchesStartDate && matchesEndDate && matchesSlot) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const countBadge = document.getElementById('examCountDisplay');
        if (countBadge) {
            countBadge.textContent = 'Showing: ' + visibleCount;
        }
    }

    function resetExamFilters() {
        const ids = ['examSearchInput', 'yearFilterSelect', 'monthFilterSelect', 'startDateInput', 'endDateInput'];
        ids.forEach(function(id) {
            const el = document.getElementById(id);
            if (el) el.value = '';
        });
        const chk1 = document.getElementById('chkSlot1');
        const chk2 = document.getElementById('chkSlot2');
        if (chk1) chk1.checked = true;
        if (chk2) chk2.checked = true;
        filterExamTable();
    }

    function downloadExamSlotCsv() {
        const table = document.getElementById('examScheduleTable');
        if (!table) return;

        const headers = ['#', 'Application No', 'Candidate Name', 'Phone No', 'CRM ID', 'Program & Branch', 'Scheduled Date', 'Exam Slot / Timing', 'Status', 'Booking Details'];
        const rows = [];
        
        const trs = table.querySelectorAll('tbody tr');
        let index = 1;
        trs.forEach(function(tr) {
            if (tr.style.display !== 'none' && !tr.querySelector('td[colspan]')) {
                const cells = tr.querySelectorAll('td');
                if (cells.length >= 10) {
                    const rowData = [
                        index++,
                        cells[1].innerText.trim(),
                        cells[2].innerText.trim(),
                        cells[3].innerText.trim(),
                        cells[4].innerText.trim(),
                        cells[5].innerText.trim(),
                        cells[6].innerText.trim(),
                        cells[7].innerText.trim(),
                        cells[8].innerText.trim().replace(/\s+/g, ' '),
                        cells[9].innerText.trim().replace(/\s+/g, ' ')
                    ];
                    rows.push(rowData);
                }
            }
        });

        if (rows.length === 0) {
            alert('No exam slot records available to download for the selected filters.');
            return;
        }

        let csvContent = "\uFEFF";
        csvContent += headers.map(h => `"${h.replace(/"/g, '""')}"`).join(",") + "\r\n";
        rows.forEach(r => {
            csvContent += r.map(c => `"${(c ?? '').toString().replace(/"/g, '""')}"`).join(",") + "\r\n";
        });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement("a");
        const today = new Date().toISOString().slice(0, 10);
        link.setAttribute("href", URL.createObjectURL(blob));
        link.setAttribute("download", `VSAT_Exam_Slot_Bookings_${today}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
</body>
</html>
