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
            padding: 0.45rem 0.75rem;
            font-size: 0.82rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #ffffff;
            color: #1e293b;
            outline: none;
            cursor: pointer;
        }
        .filter-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37,99,235,0.1);
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
        <header class="top-bar">
            <div class="top-bar-left">
                <button type="button" class="topbar-toggle-sidebar-btn" onclick="toggleSidebarMenu()" title="Open Navigation Menu" aria-label="Toggle Sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                </button>
                <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📅 Exams Slot Data Ledger</span>
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
        <main class="main-body" style="padding: 1.5rem 2rem;">
            <!-- Metrics Bar -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <!-- Card 1: Total Booked Slots -->
                <div class="metric-card" style="padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div class="metric-label" style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">TOTAL BOOKINGS</div>
                        <div class="metric-value" style="font-size: 1.65rem; font-weight: 800; color: #0f172a; margin: 0.2rem 0;">{{ $totalBookings }}</div>
                        <div style="font-size: 0.74rem; font-weight: 600; color: #059669;">Registered Candidates</div>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">📅</div>
                </div>

                <!-- Card 2: Slot 1 (Morning) -->
                <div class="metric-card" style="padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div class="metric-label" style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">SLOT 1 (11 AM - 1 PM)</div>
                        <div class="metric-value" style="font-size: 1.65rem; font-weight: 800; color: #1d4ed8; margin: 0.2rem 0;">{{ $slot1Count }}</div>
                        <div style="font-size: 0.74rem; font-weight: 600; color: #2563eb;">Morning Session</div>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #dbeafe; color: #1e40af; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">🌅</div>
                </div>

                <!-- Card 3: Slot 2 (Afternoon) -->
                <div class="metric-card" style="padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div class="metric-label" style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">SLOT 2 (2 PM - 4 PM)</div>
                        <div class="metric-value" style="font-size: 1.65rem; font-weight: 800; color: #7e22ce; margin: 0.2rem 0;">{{ $slot2Count }}</div>
                        <div style="font-size: 0.74rem; font-weight: 600; color: #9333ea;">Afternoon Session</div>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #f3e8ff; color: #7e22ce; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">☀️</div>
                </div>

                <!-- Card 4: Confirmed & Active -->
                <div class="metric-card" style="padding: 1rem 1.25rem; border-radius: 12px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; align-items: center; justify-content: space-between;">
                    <div>
                        <div class="metric-label" style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">CONFIRMED STATUS</div>
                        <div class="metric-value" style="font-size: 1.65rem; font-weight: 800; color: #047857; margin: 0.2rem 0;">{{ $confirmedCount }}</div>
                        <div style="font-size: 0.74rem; font-weight: 600; color: #059669;">Verified Allotments</div>
                    </div>
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.35rem;">✓</div>
                </div>
            </div>

            <!-- Content Card: Exam Schedules Table -->
            <div class="content-card" style="background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.03); overflow: hidden;">
                <!-- Header Toolbar & Filters -->
                <div class="card-header-bar" style="padding: 1.15rem 1.5rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 0.5rem;">
                            <span>Exam Slot Registrations</span>
                            <span class="filter-count-badge" id="examCountDisplay" style="font-size: 0.78rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 2px 9px; border-radius: 12px;">
                                Total: {{ $totalBookings }}
                            </span>
                        </h3>
                        <p style="margin: 3px 0 0; font-size: 0.78rem; color: #64748b;">
                            Direct examination booking ledger from database table <code style="font-family: monospace; background: #f1f5f9; padding: 1px 4px; border-radius: 4px;">finance.exam_schedule</code>
                        </p>
                    </div>

                    <!-- Search & Filter Controls -->
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                        <!-- Slot Filter -->
                        <select id="slotFilterSelect" class="filter-select" onchange="filterExamTable()">
                            <option value="">All Slots</option>
                            <option value="S1">S1 11:00 AM to 1:00 PM</option>
                            <option value="S2">S2 2:00 PM to 4:00 PM</option>
                        </select>

                        <!-- Date Filter -->
                        <select id="dateFilterSelect" class="filter-select" onchange="filterExamTable()">
                            <option value="">All Dates</option>
                            @foreach($distinctDates as $dDate)
                                @php
                                    $dVal = \Carbon\Carbon::parse($dDate)->format('Y-m-d');
                                    $dLabel = \Carbon\Carbon::parse($dDate)->format('d M Y');
                                @endphp
                                <option value="{{ $dVal }}">{{ $dLabel }}</option>
                            @endforeach
                        </select>

                        <!-- Live Text Search -->
                        <div style="position: relative;">
                            <input type="text"
                                   id="examSearchInput"
                                   placeholder="Search candidate, app no, phone..."
                                   onkeyup="filterExamTable()"
                                   style="padding: 0.45rem 0.85rem 0.45rem 2rem; font-size: 0.82rem; border: 1px solid #cbd5e1; border-radius: 8px; width: 230px; outline: none; background: #ffffff;">
                            <svg style="position: absolute; left: 0.65rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>

                        <!-- Print Action -->
                        <button type="button" onclick="window.print()" class="btn-header-action" style="padding: 0.45rem 0.85rem; font-size: 0.82rem;" title="Print Table">
                            <span>🖨️ Print</span>
                        </button>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="table-responsive" style="overflow-x: auto;">
                    <table class="crm-table" id="examScheduleTable" style="width: 100%; border-collapse: collapse; font-size: 0.84rem;">
                        <thead>
                            <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left; color: #475569; font-weight: 700; font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.03em;">
                                <th style="padding: 0.85rem 1rem; width: 45px; text-align: center;">#</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap;">Application #</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap;">Candidate Name</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap;">Phone No</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap;">CRM ID</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap;">Program &amp; Branch</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap;">Scheduled Date</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap;">Exam Slot / Timing</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap; text-align: center;">Status</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap;">Booking Details</th>
                                <th style="padding: 0.85rem 1rem; white-space: nowrap; text-align: center;">Action</th>
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
                                    
                                    $dateFormatted = !empty($row->examdate) ? \Carbon\Carbon::parse($row->examdate)->format('d M Y') : 'N/A';
                                    $dateRaw = !empty($row->examdate) ? \Carbon\Carbon::parse($row->examdate)->format('Y-m-d') : '';
                                    
                                    $slotStr = (string) $row->exam_slot;
                                    $isS1 = str_contains(strtoupper($slotStr), 'S1') || str_contains($slotStr, '11');
                                    $isS2 = str_contains(strtoupper($slotStr), 'S2') || str_contains($slotStr, '2:00') || str_contains($slotStr, '2 PM');
                                    
                                    $isConfirmed = (int) $row->exam_status === 1 || empty($row->exam_status);
                                    
                                    $logTimeFormatted = !empty($row->logdatetime) ? \Carbon\Carbon::parse($row->logdatetime)->format('d M Y, h:i A') : 'Recorded';
                                @endphp
                                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.15s ease;"
                                    data-name="{{ strtolower($candidateName) }}"
                                    data-appli="{{ strtolower((string)$row->applino) }}"
                                    data-phone="{{ strtolower((string)$row->phoneno) }}"
                                    data-crmid="{{ strtolower((string)$row->crmid) }}"
                                    data-date="{{ $dateRaw }}"
                                    data-slot="{{ $isS1 ? 'S1' : ($isS2 ? 'S2' : 'OTHER') }}">
                                    
                                    <!-- Index -->
                                    <td style="padding: 0.85rem 1rem; text-align: center; color: #94a3b8; font-weight: 600;">
                                        {{ $index + 1 }}
                                    </td>

                                    <!-- Application No -->
                                    <td style="padding: 0.85rem 1rem; font-weight: 800; color: #0f172a; white-space: nowrap;">
                                        <a href="{{ route('application.show', ['id' => $row->applino]) }}" target="_blank" style="color: #2563eb; text-decoration: none;" title="Open Candidate Dossier">
                                            #{{ $row->applino }}
                                        </a>
                                    </td>

                                    <!-- Candidate Name -->
                                    <td style="padding: 0.85rem 1rem; font-weight: 700; color: #1e293b; white-space: nowrap;">
                                        {{ $candidateName }}
                                    </td>

                                    <!-- Phone Number -->
                                    <td style="padding: 0.85rem 1rem; color: #334155; font-family: 'JetBrains Mono', monospace; font-size: 0.82rem; white-space: nowrap;">
                                        {{ $row->phoneno ?: 'N/A' }}
                                    </td>

                                    <!-- CRM ID -->
                                    <td style="padding: 0.85rem 1rem; white-space: nowrap;">
                                        <span style="display: inline-block; background: #f8fafc; border: 1px solid #e2e8f0; color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;">
                                            {{ $row->crmid ?: ('V-SAT-' . substr((string)$row->applino, -4)) }}
                                        </span>
                                    </td>

                                    <!-- Program & Branch -->
                                    <td style="padding: 0.85rem 1rem; white-space: nowrap;">
                                        <div style="font-weight: 700; color: #0f172a;">{{ $progTitle }}</div>
                                        @if($branchTitle)
                                            <div style="font-size: 0.74rem; color: #64748b; margin-top: 1px;">{{ $branchTitle }}</div>
                                        @endif
                                    </td>

                                    <!-- Exam Date -->
                                    <td style="padding: 0.85rem 1rem; white-space: nowrap;">
                                        <div style="display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700; color: #0f172a;">
                                            <span>📅</span>
                                            <span>{{ $dateFormatted }}</span>
                                        </div>
                                    </td>

                                    <!-- Exam Slot -->
                                    <td style="padding: 0.85rem 1rem; white-space: nowrap;">
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
                                    <td style="padding: 0.85rem 1rem; text-align: center; white-space: nowrap;">
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
                                    <td style="padding: 0.85rem 1rem; white-space: nowrap; font-size: 0.76rem; color: #64748b;">
                                        <div>{{ $logTimeFormatted }}</div>
                                        <div style="font-family: monospace; color: #94a3b8; font-size: 0.72rem;">IP: {{ $row->ipaddress ?: '127.0.0.1' }}</div>
                                    </td>

                                    <!-- Action -->
                                    <td style="padding: 0.85rem 1rem; text-align: center; white-space: nowrap;">
                                        <a href="{{ route('application.show', ['id' => $row->applino]) }}" target="_blank" class="btn-table-action" style="display: inline-flex; align-items: center; gap: 0.3rem; font-size: 0.74rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 4px 10px; border-radius: 6px; text-decoration: none;">
                                            <span>👁️ View Form</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" style="text-align: center; padding: 3rem 1rem; color: #64748b;">
                                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">📅</div>
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

<!-- Live Filter Script -->
<script>
    function filterExamTable() {
        const query = (document.getElementById('examSearchInput').value || '').toLowerCase().trim();
        const slotFilter = document.getElementById('slotFilterSelect').value;
        const dateFilter = document.getElementById('dateFilterSelect').value;

        const table = document.getElementById('examScheduleTable');
        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(function(row) {
            // Check if it's the empty placeholder row
            if (row.children.length === 1 && row.children[0].colSpan > 1) {
                return;
            }

            const name = row.getAttribute('data-name') || '';
            const appli = row.getAttribute('data-appli') || '';
            const phone = row.getAttribute('data-phone') || '';
            const crmid = row.getAttribute('data-crmid') || '';
            const date = row.getAttribute('data-date') || '';
            const slot = row.getAttribute('data-slot') || '';

            let matchesQuery = !query || name.includes(query) || appli.includes(query) || phone.includes(query) || crmid.includes(query);
            let matchesSlot = !slotFilter || slot === slotFilter;
            let matchesDate = !dateFilter || date === dateFilter;

            if (matchesQuery && matchesSlot && matchesDate) {
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
</script>
</body>
</html>

