<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $roleName }} Dashboard - VFSTR Admissions CRM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css">
</head>
<body>
<div class="app-wrapper">
    <!-- 1. LEFT SIDEBAR (Dynamic Per Role) -->
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
            </div>

            <div class="top-bar-right">
                @include('layouts.role-switcher')
            </div>
        </header>

        <!-- Main Dashboard Body -->
        <main class="main-body">
            @if(session('status'))
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 0.85rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.85rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                    <span>✓ {{ session('status') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 1.1rem; cursor: pointer; color: #60a5fa;">&times;</button>
                </div>
            @endif

            <!-- ROLE-SPECIFIC METRIC CARDS (DASHBOARD ONLY - HIDDEN BY DEFAULT) -->
            @if(($activeTab ?? '') === 'overview')
            <div class="metrics-grid">
                @if($roleId == 3)
                    <!-- CRM DEAN METRICS -->
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Total Applicants</span>
                            <span class="metric-value">{{ number_format($totalApplications) }}</span>
                            <span class="metric-sub">↑ 100% Target Pipeline</span>
                        </div>
                        <div class="metric-icon-box metric-blue">👥</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Degree Programs</span>
                            <span class="metric-value">{{ $totalPrograms }}</span>
                            <span class="metric-sub">Across All Campuses</span>
                        </div>
                        <div class="metric-icon-box metric-purple">🎓</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Academic Branches</span>
                            <span class="metric-value">{{ $totalBranches }}</span>
                            <span class="metric-sub">Active Specializations</span>
                        </div>
                        <div class="metric-icon-box metric-emerald">⚙️</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">SMS / WhatsApp Sent</span>
                            <span class="metric-value">{{ number_format($totalCommunications) }}</span>
                            <span class="metric-sub">Delivery Rate: 99.4%</span>
                        </div>
                        <div class="metric-icon-box metric-amber">💬</div>
                    </div>

                @elseif($roleId == 2)
                    <!-- CRM INCHARGE METRICS -->
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Applications Under Review</span>
                            <span class="metric-value">{{ number_format($totalApplications) }}</span>
                            <span class="metric-sub">Ready for Verification</span>
                        </div>
                        <div class="metric-icon-box metric-blue">📋</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Active Degree Programs</span>
                            <span class="metric-value">{{ $totalPrograms }}</span>
                            <span class="metric-sub">In 84 Engineering Branches</span>
                        </div>
                        <div class="metric-icon-box metric-purple">🏫</div>
                    </div>
                    <div class="metric-card" onclick="openAssignBox('', 'All Unallocated Leads')" style="cursor: pointer;" title="Click to allot unallocated leads to telecallers">
                        <div class="metric-info">
                            <span class="metric-label">Unallocated Leads</span>
                            <span class="metric-value" id="topMetricUnallocated">{{ $unallocatedLeadsCount ?? max(0, $totalApplications - ($dbAllocations?->count() ?? 0)) }}</span>
                            <span class="metric-sub">Ready for Telecallers</span>
                        </div>
                        <div class="metric-icon-box metric-emerald">📤</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Applicant Communications</span>
                            <span class="metric-value">{{ number_format($totalCommunications) }}</span>
                            <span class="metric-sub">OTP & Alerts Sent</span>
                        </div>
                        <div class="metric-icon-box metric-amber">💬</div>
                    </div>

                @else
                    <!-- EMPLOYEE / ADMIN METRICS -->
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Configured Campuses</span>
                            <span class="metric-value">{{ $totalCampuses }}</span>
                            <span class="metric-sub">Vadlamudi Main Campus</span>
                        </div>
                        <div class="metric-icon-box metric-blue">🏢</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Degree Programs</span>
                            <span class="metric-value">{{ $totalPrograms }}</span>
                            <span class="metric-sub">UG, PG, PhD Catalog</span>
                        </div>
                        <div class="metric-icon-box metric-purple">🎓</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Academic Branches</span>
                            <span class="metric-value">{{ $totalBranches }}</span>
                            <span class="metric-sub">Master Database</span>
                        </div>
                        <div class="metric-icon-box metric-emerald">⚙️</div>
                    </div>
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Gateway Dispatch Logs</span>
                            <span class="metric-value">{{ number_format($totalCommunications) }}</span>
                            <span class="metric-sub">System Status: Healthy</span>
                        </div>
                        <div class="metric-icon-box metric-amber">💬</div>
                    </div>
                @endif
            </div>
            @endif

            <!-- TABS ROUTING & CONTENT VIEWS -->
            @if(($activeTab ?? '') === 'stddata')
                <!-- TAB: ENQUIRY LIST -->
                <div class="content-card">
                    <!-- LIVE SINGLE SEARCH: APPLICATION NO, PHONE NUMBER, STUDENT NAME -->
                    <div class="enquiry-filter-toolbar">
                        <div class="filter-inputs-row">
                            <!-- Unified Search Filter -->
                            <div class="filter-control-group filter-control-search">
                                <label for="liveFilterSearch" class="filter-label">
                                    <span class="filter-icon">🔍</span> Search Enquiries
                                </label>
                                <div class="input-with-icon">
                                    <input type="text" 
                                           id="liveFilterSearch" 
                                           class="filter-input" 
                                           placeholder="Search application no, phone, name..." 
                                           value="{{ $searchQuery ?? $filterPhone ?? '' }}" 
                                           autocomplete="off"
                                           oninput="window.handleEnquiryLiveSearch(this.value)"
                                           onkeyup="if(event.key==='Escape'){ window.clearFilterInput('liveFilterSearch'); }">
                                    <button type="button" class="btn-clear-input" onclick="clearFilterInput('liveFilterSearch')" title="Clear search">&times;</button>
                                </div>
                            </div>
                        </div>

                        <!-- Live Status & Counter Badge -->
                        <div class="filter-meta-bar">
                            <div class="live-status-indicator">
                                <span class="live-pulse-dot"></span>
                                <span class="live-indicator-text">LIVE FILTERING</span>
                            </div>
                            <div id="filterCountBadge" class="filter-count-badge">
                                Showing {{ $allStudents->count() }} of {{ $allStudents->count() }} enquiries
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table" id="enquiriesTable">
                            <thead>
                                <tr>
                                    <th>Application No</th>
                                    <th>Student Name</th>
                                    <th>Phone / WhatsApp</th>
                                    <th>Email ID</th>
                                    <th>Program / Branch</th>
                                    <th>Enquiry Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allStudents as $std)
                                    @php
                                        $phone = $std->mobile ?? $std->phoneno ?? '';
                                        $rawStatus = $std->std_status ?? 'Registered';
                                        $timestamp = $std->log_timestamp;
                                        $dateYmd = $timestamp ? \Carbon\Carbon::parse($timestamp)->format('Y-m-d') : '';
                                        $dateFormatted = $timestamp ? \Carbon\Carbon::parse($timestamp)->format('d M Y') : 'N/A';
                                        $timeFormatted = $timestamp ? \Carbon\Carbon::parse($timestamp)->format('h:i A') : '';
                                        $appliKey = $std->application_no ?? $std->applinano ?? '';
                                        $candidateName = $std->student_name ?? $std->name ?? 'Candidate';
                                        $emailVal = $std->personal_email ?? $std->emailid ?? '';
                                        $programName = $std->program?->program_short_name ?? 'B.Tech';
                                        $branchLabel = $std->branch?->branch_long_name ?? $std->branch?->branch_short_name ?? '';
                                        $searchString = strtolower(trim("{$appliKey} " . ($std->register_no ?? '') . " {$candidateName} {$phone} {$emailVal} {$programName} {$branchLabel}"));
                                    @endphp
                                    <tr class="enquiry-row" 
                                        data-search="{{ $searchString }}"
                                        data-phone="{{ $phone }}" 
                                        data-appli="{{ strtolower($appliKey) }}"
                                        data-name="{{ strtolower($candidateName) }}"
                                        data-status="{{ strtolower($rawStatus) }}" 
                                        data-date="{{ $dateYmd }}">
                                        <td>
                                            <strong>{{ $std->application_no ?? $std->applinano ?? 'N/A' }}</strong>
                                            @if(!empty($std->register_no))
                                                 <div style="font-size: 0.72rem; color: #64748b;">Reg: {{ $std->register_no }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #0f172a;">{{ $candidateName }}</div>
                                            @if(!empty($std->gender))
                                                <div style="font-size: 0.72rem; color: #64748b;">Gender: {{ $std->gender }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #0f172a;">{{ $phone ?: 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.8rem; color: #475569;">{{ $std->personal_email ?? $std->emailid ?? 'N/A' }}</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #0f172a;">{{ $std->program?->program_short_name ?? 'B.Tech' }}</div>
                                            @if(!empty($branchLabel))
                                                <div style="font-size: 0.72rem; color: #64748b; font-weight: 500;">{{ $branchLabel }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #0f172a; font-size: 0.8rem;">{{ $dateFormatted }}</div>
                                            @if($timeFormatted)
                                                <div style="font-size: 0.72rem; color: #64748b;">{{ $timeFormatted }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyTableInitialRow">
                                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                            No enquiry records found in the database.
                                        </td>
                                    </tr>
                                @endforelse

                                <!-- LIVE FILTER NO RESULTS PLACEHOLDER -->
                                <tr id="noFilterMatchesRow" style="display: none;">
                                    <td colspan="6" style="text-align: center; padding: 2.5rem 1rem; background: #fafafa;">
                                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🔍</div>
                                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">No enquiries match the selected filters</div>
                                        <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 1rem;" id="noMatchesDetailText">No records match search.</div>
                                        <button type="button" onclick="window.clearFilterInput('liveFilterSearch')" class="btn-action-sm btn-verify" style="margin: 0 auto; display: inline-flex;">
                                            ↺ Clear Search
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Direct Dedicated Live Filter Script for Enquiry List -->
                    <script>
                        window.handleEnquiryLiveSearch = function(rawQuery) {
                            var input = document.getElementById('liveFilterSearch');
                            var query = (rawQuery !== undefined ? rawQuery : (input ? input.value : '')).trim().toLowerCase();
                            var countBadge = document.getElementById('filterCountBadge');
                            var noMatchRow = document.getElementById('noFilterMatchesRow');
                            var noMatchesDetail = document.getElementById('noMatchesDetailText');
                            var table = document.getElementById('enquiriesTable');
                            if (!table) return;

                            var rows = table.querySelectorAll('tbody tr.enquiry-row');
                            var totalCount = rows.length;
                            if (!totalCount) return;

                            var tokens = query ? query.split(/\s+/).filter(Boolean) : [];
                            var visibleCount = 0;

                            for (var i = 0; i < rows.length; i++) {
                                var row = rows[i];
                                if (!tokens.length) {
                                    row.style.display = '';
                                    visibleCount++;
                                    continue;
                                }

                                var dataSearch = row.getAttribute('data-search') || '';
                                var rowText = row.innerText || row.textContent || '';
                                var haystack = (dataSearch + ' ' + rowText).toLowerCase();

                                var matches = true;
                                for (var t = 0; t < tokens.length; t++) {
                                    if (haystack.indexOf(tokens[t]) === -1) {
                                        matches = false;
                                        break;
                                    }
                                }

                                if (matches) {
                                    row.style.display = '';
                                    visibleCount++;
                                } else {
                                    row.style.display = 'none';
                                }
                            }

                            if (countBadge) {
                                if (tokens.length) {
                                    countBadge.innerHTML = '<strong>' + visibleCount + '</strong> of ' + totalCount + ' enquiries matched';
                                    countBadge.style.background = '#dbeafe';
                                    countBadge.style.color = '#1e40af';
                                } else {
                                    countBadge.textContent = 'Showing ' + totalCount + ' of ' + totalCount + ' enquiries';
                                    countBadge.style.background = '#f1f5f9';
                                    countBadge.style.color = '#475569';
                                }
                            }

                            if (noMatchRow) {
                                if (visibleCount === 0 && totalCount > 0) {
                                    noMatchRow.style.display = '';
                                    if (noMatchesDetail) {
                                        noMatchesDetail.textContent = 'No records match search: "' + query + '"';
                                    }
                                } else {
                                    noMatchRow.style.display = 'none';
                                }
                            }
                        };

                        window.clearFilterInput = function(inputId) {
                            var input = document.getElementById(inputId || 'liveFilterSearch');
                            if (input) {
                                input.value = '';
                                window.handleEnquiryLiveSearch('');
                                input.focus();
                            }
                        };

                        window.resetFilters = function() {
                            window.clearFilterInput('liveFilterSearch');
                        };

                        // Immediately wire listeners
                        (function() {
                            var input = document.getElementById('liveFilterSearch');
                            if (input) {
                                input.addEventListener('input', function() {
                                    window.handleEnquiryLiveSearch(this.value);
                                });
                                input.addEventListener('keyup', function() {
                                    window.handleEnquiryLiveSearch(this.value);
                                });
                                if (input.value && input.value.trim()) {
                                    window.handleEnquiryLiveSearch(input.value);
                                }
                            }
                        })();
                    </script>
                </div>

            @elseif(($activeTab ?? '') === 'stdsearch')
                <!-- TAB: REGISTER LIST (STUDENT TRACKING) -->
                <div class="content-card">
                    <!-- Search Toolbar with Tracking Legend -->
                    <div style="padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <form method="GET" action="{{ route('crm.stdsearch') }}" style="display: flex; gap: 0.75rem; flex: 1; max-width: 580px;">
                            <input type="hidden" name="tab" value="stdsearch">
                            <input type="text" 
                                   id="registerListSearchInput" 
                                   name="q" 
                                   value="{{ $searchQuery ?? '' }}" 
                                   placeholder="Search by Application No, Student Name, or Mobile..." 
                                   style="flex: 1; height: 42px; padding: 0.55rem 1rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; outline: none;">
                            <button type="submit" class="btn-action-sm btn-verify" style="padding: 0 1.25rem; height: 42px; font-size: 0.84rem;">
                                🔍 Search
                            </button>
                        </form>

                        <div class="tracking-legend">
                            <span class="legend-item"><span class="legend-dot dot-done"></span> Completed</span>
                            <span class="legend-item"><span class="legend-dot dot-pending"></span> In Progress</span>
                            <span class="legend-item"><span class="legend-dot dot-danger"></span> Fee Due</span>
                            <span class="legend-item"><span class="legend-dot dot-awaiting"></span> Locked / Awaiting</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table" id="registerListTable">
                            <thead>
                                <tr>
                                    <th>Application Number</th>
                                    <th>Student Name</th>
                                    <th>Mobile Number</th>
                                    <th style="text-align: center;">Step 1</th>
                                    <th style="text-align: center;">Step 2</th>
                                    <th style="text-align: center;">Step 3</th>
                                    <th style="text-align: center;">Step 4</th>
                                    <th style="text-align: center;">Step 5</th>
                                    <th style="text-align: center;">Step 6</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allStudents as $std)
                                    @php
                                        $phone = $std->mobile ?? $std->phoneno ?? '';
                                        $appliNo = $std->application_no ?? $std->applinano ?? 'N/A';
                                        $name = $std->student_name ?? $std->name ?? 'Candidate';

                                        // Real stages from crm.student_registration
                                        $s1 = strtolower(trim((string)($std->stage1 ?? '')));
                                        $s2 = strtolower(trim((string)($std->stage2 ?? '')));
                                        $s3 = strtolower(trim((string)($std->stage3 ?? '')));
                                        $s4 = strtolower(trim((string)($std->stage4 ?? '')));
                                        $s5 = strtolower(trim((string)($std->stage5 ?? '')));
                                        $s6 = strtolower(trim((string)($std->stage6 ?? '')));

                                        // Step 1: Registration & Fee Payment
                                        if (in_array($s1, ['completed', 'paid', 'approved', 'done'])) {
                                            $st1Pill = 'pill-done'; $st1Icon = '✓'; $st1Text = 'Done'; $st1Sub = 'Fee Paid';
                                        } elseif (in_array($s1, ['in_progress', 'partial', 'pending_verification'])) {
                                            $st1Pill = 'pill-pending'; $st1Icon = '⏳'; $st1Text = 'In Progress'; $st1Sub = 'In Progress';
                                        } elseif (in_array($s1, ['not_paid', 'unpaid', 'due', 'failed'])) {
                                            $st1Pill = 'pill-danger'; $st1Icon = '❌'; $st1Text = 'Fee Due'; $st1Sub = 'Not Paid';
                                        } else {
                                            $st1Pill = 'pill-awaiting'; $st1Icon = '🔒'; $st1Text = 'Pending'; $st1Sub = 'Registration';
                                        }

                                        // Step 2: Personal Profile
                                        if (in_array($s2, ['completed', 'approved', 'done'])) {
                                            $st2Pill = 'pill-done'; $st2Icon = '✓'; $st2Text = 'Done'; $st2Sub = 'Personal Details';
                                        } elseif (in_array($s2, ['in_progress', 'partial', 'draft'])) {
                                            $st2Pill = 'pill-pending'; $st2Icon = '⏳'; $st2Text = 'In Progress'; $st2Sub = 'Personal Details';
                                        } elseif (in_array($s2, ['locked', 'na', 'not_applicable'])) {
                                            $st2Pill = 'pill-awaiting'; $st2Icon = '🔒'; $st2Text = 'Locked'; $st2Sub = 'Personal Details';
                                        } else {
                                            $st2Pill = 'pill-awaiting'; $st2Icon = '⏳'; $st2Text = 'Pending'; $st2Sub = 'Personal Details';
                                        }

                                        // Step 3: Academic Details
                                        if (in_array($s3, ['completed', 'approved', 'done'])) {
                                            $st3Pill = 'pill-done'; $st3Icon = '✓'; $st3Text = 'Done'; $st3Sub = 'Academic Info';
                                        } elseif (in_array($s3, ['in_progress', 'partial', 'draft'])) {
                                            $st3Pill = 'pill-pending'; $st3Icon = '⏳'; $st3Text = 'In Progress'; $st3Sub = 'Academic Info';
                                        } elseif (in_array($s3, ['locked', 'na', 'not_applicable'])) {
                                            $st3Pill = 'pill-awaiting'; $st3Icon = '🔒'; $st3Text = 'Locked'; $st3Sub = 'Academic Info';
                                        } else {
                                            $st3Pill = 'pill-awaiting'; $st3Icon = '⏳'; $st3Text = 'Pending'; $st3Sub = 'Academic Info';
                                        }

                                        // Step 4: Application Download
                                        if (in_array($s4, ['completed', 'approved', 'done', 'downloaded'])) {
                                            $st4Pill = 'pill-done'; $st4Icon = '✓'; $st4Text = 'Done'; $st4Sub = 'Application';
                                        } elseif (in_array($s4, ['in_progress', 'ready'])) {
                                            $st4Pill = 'pill-pending'; $st4Icon = '⏳'; $st4Text = 'Ready'; $st4Sub = 'Application';
                                        } elseif (in_array($s4, ['na', 'not_applicable'])) {
                                            $st4Pill = 'pill-awaiting'; $st4Icon = '—'; $st4Text = 'N/A'; $st4Sub = 'Application';
                                        } else {
                                            $st4Pill = 'pill-awaiting'; $st4Icon = '🔒'; $st4Text = 'Locked'; $st4Sub = 'Application';
                                        }

                                        // Step 5: VSAT Result / Entrance Exam
                                        if (in_array($s5, ['completed', 'approved', 'done', 'qualified'])) {
                                            $st5Pill = 'pill-done'; $st5Icon = '✓'; $st5Text = 'Done'; $st5Sub = 'VSAT Result';
                                        } elseif (in_array($s5, ['in_progress', 'scheduled', 'pending'])) {
                                            $st5Pill = 'pill-pending'; $st5Icon = '⏳'; $st5Text = 'In Progress'; $st5Sub = 'VSAT Result';
                                        } elseif (in_array($s5, ['na', 'not_applicable'])) {
                                            $st5Pill = 'pill-awaiting'; $st5Icon = '—'; $st5Text = 'N/A'; $st5Sub = 'VSAT Result';
                                        } else {
                                            $st5Pill = 'pill-awaiting'; $st5Icon = '🔒'; $st5Text = 'Locked'; $st5Sub = 'VSAT Result';
                                        }

                                        // Step 6: Enrollment Confirmation
                                        $isAdmitted = in_array($s6, ['completed', 'approved', 'done', 'admitted', 'enrolled'])
                                            || !empty($std->register_no)
                                            || in_array(strtolower($std->std_status ?? ''), ['admitted', 'enrolled']);

                                        if ($isAdmitted) {
                                            $st6Pill = 'pill-done'; $st6Icon = '✓'; $st6Text = 'Done'; $st6Sub = 'Enrollment';
                                        } elseif (in_array($s6, ['in_progress', 'seat_allotted', 'counseling'])) {
                                            $st6Pill = 'pill-pending'; $st6Icon = '⏳'; $st6Text = 'In Progress'; $st6Sub = 'Enrollment';
                                        } elseif (in_array($s6, ['na', 'not_applicable'])) {
                                            $st6Pill = 'pill-awaiting'; $st6Icon = '—'; $st6Text = 'N/A'; $st6Sub = 'Enrollment';
                                        } else {
                                            $st6Pill = 'pill-awaiting'; $st6Icon = '🔒'; $st6Text = 'Locked'; $st6Sub = 'Enrollment';
                                        }
                                    @endphp
                                    <tr class="register-track-row" 
                                        data-search="{{ strtolower($appliNo . ' ' . $name . ' ' . $phone) }}">
                                        <td>
                                            <strong style="color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem;">{{ $appliNo }}</strong>
                                        </td>
                                        <td>
                                            <div style="font-weight: 700; color: #0f172a;">{{ $name }}</div>
                                            @if(!empty($std->gender))
                                                <div style="font-size: 0.72rem; color: #64748b;">{{ $std->gender }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span style="font-family: 'JetBrains Mono', monospace; font-weight: 600; color: #0f172a;">+91 {{ $phone ?: 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill {{ $st1Pill }}">{{ $st1Icon }} {{ $st1Text }}</span>
                                                <div class="step-sub-text">{{ $st1Sub }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill {{ $st2Pill }}">{{ $st2Icon }} {{ $st2Text }}</span>
                                                <div class="step-sub-text">{{ $st2Sub }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill {{ $st3Pill }}">{{ $st3Icon }} {{ $st3Text }}</span>
                                                <div class="step-sub-text">{{ $st3Sub }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill {{ $st4Pill }}">{{ $st4Icon }} {{ $st4Text }}</span>
                                                <div class="step-sub-text">{{ $st4Sub }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill {{ $st5Pill }}">{{ $st5Icon }} {{ $st5Text }}</span>
                                                <div class="step-sub-text">{{ $st5Sub }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill {{ $st6Pill }}">{{ $st6Icon }} {{ $st6Text }}</span>
                                                <div class="step-sub-text">{{ $st6Sub }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                            No student records found in Register List.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <script>
                        (function() {
                            const input = document.getElementById('registerListSearchInput');
                            if (!input) return;
                            const table = document.getElementById('registerListTable');
                            if (!table) return;
                            const rows = Array.from(table.querySelectorAll('tbody tr.register-track-row'));
                            const filterRows = function(q) {
                                const term = (q || '').trim().toLowerCase();
                                rows.forEach(row => {
                                    const text = (row.getAttribute('data-search') || row.innerText || '').toLowerCase();
                                    row.style.display = !term || text.includes(term) ? '' : 'none';
                                });
                            };
                            input.addEventListener('input', function() { filterRows(this.value); });
                            input.addEventListener('keyup', function() { filterRows(this.value); });
                            if (input.value && input.value.trim()) {
                                filterRows(input.value);
                            }
                        })();
                    </script>
                </div>

            @elseif(($activeTab ?? '') === 'couponinfo')
                <!-- TAB: COUPON INFO (PAYMENT & COUPON TRACKING) -->

                <!-- 1. EARLY BIRD COUPON TIERS STATUS STRIP (finance.couponinfo) -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    @php
                        $tiers = [
                            ['code' => '27EARLYB100', 'name' => 'Tier 1 (First 100 Members)', 'range' => '0 - 100', 'total' => 100, 'discount' => 5000],
                            ['code' => '27EARLYB200', 'name' => 'Tier 2 (Next 200 Members)', 'range' => '100 - 300', 'total' => 200, 'discount' => 3000],
                            ['code' => '27EARLYB500', 'name' => 'Tier 3 (Next 500 Members)', 'range' => '300 - 800', 'total' => 500, 'discount' => 2000],
                            ['code' => '27EARLYB50',  'name' => 'Tier 4 (Reserve 50 Members)', 'range' => '800 - 850', 'total' => 50, 'discount' => 1000],
                        ];
                    @endphp

                    @foreach($tiers as $t)
                        @php
                            $batchObj = $couponBatches->firstWhere('couponsno', $t['code']);
                            $curr = $batchObj ? $batchObj->coupcurrcount : 0;
                            $tot = $batchObj ? $batchObj->coupcount : $t['total'];
                            $isComplete = ($curr >= $tot);
                            $isActive = !$isComplete && ($loop->first || ($tiers[$loop->index - 1] ?? null) && ($couponBatches->firstWhere('couponsno', $tiers[$loop->index - 1]['code'])->coupcurrcount ?? 0) >= ($couponBatches->firstWhere('couponsno', $tiers[$loop->index - 1]['code'])->coupcount ?? 1));
                        @endphp
                        <div style="background: #ffffff; border: 1.5px solid {{ $isActive ? '#10b981' : ($isComplete ? '#cbd5e1' : '#e2e8f0') }}; border-radius: 12px; padding: 1rem; position: relative; box-shadow: {{ $isActive ? '0 4px 12px rgba(16, 185, 129, 0.15)' : 'none' }};">
                            @if($isActive)
                                <span style="position: absolute; top: 8px; right: 8px; background: #10b981; color: #fff; font-size: 0.68rem; font-weight: 700; padding: 2px 6px; border-radius: 4px;">● ACTIVE</span>
                            @elseif($isComplete)
                                <span style="position: absolute; top: 8px; right: 8px; background: #94a3b8; color: #fff; font-size: 0.68rem; font-weight: 700; padding: 2px 6px; border-radius: 4px;">COMPLETED</span>
                            @else
                                <span style="position: absolute; top: 8px; right: 8px; background: #e2e8f0; color: #64748b; font-size: 0.68rem; font-weight: 700; padding: 2px 6px; border-radius: 4px;">UPCOMING</span>
                            @endif

                            <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">{{ $t['name'] }}</div>
                            <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a; margin: 4px 0; font-family: 'JetBrains Mono', monospace;">{{ $t['code'] }}</div>
                            <div style="font-size: 0.8rem; color: #059669; font-weight: 700; margin-bottom: 6px;">- ₹ {{ number_format($t['discount']) }} Concession</div>
                            
                            <!-- Progress Bar -->
                            <div style="height: 6px; background: #f1f5f9; border-radius: 999px; overflow: hidden; margin-top: 6px;">
                                <div style="height: 100%; width: {{ $tot > 0 ? min(100, round(($curr / $tot) * 100)) : 0 }}%; background: {{ $isComplete ? '#94a3b8' : '#10b981' }};"></div>
                            </div>
                            <div style="display: flex; justify-content: space-between; font-size: 0.72rem; color: #64748b; margin-top: 4px;">
                                <span>Claimed: <strong>{{ $curr }}</strong> / {{ $tot }}</span>
                                <span>Members: {{ $t['range'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 2. COUPON MANAGEMENT & APPLICANTS LEDGER -->
                <div class="content-card">
                    <div class="card-header-bar" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <div>
                            <div class="card-title">🎟️ Coupon Management &amp; Fee Concessions Ledger (finance.couponinfo)</div>
                            <div class="card-subtitle">Manage Default Early Bird Tiers (up to 800 members) and Personal Coupons (Derived from 8 Digits of Application No + P1 Suffix, e.g. 27000005P1)</div>
                        </div>
                        <button type="button" class="btn-action-sm btn-verify" onclick="openPersonalCouponModal()" style="height: 40px; padding: 0 1.25rem; font-size: 0.85rem; font-weight: 700; background: #6366f1; color: #ffffff; border: none; border-radius: 8px; cursor: pointer;">
                            + 🎟️ Generate Personal Coupon (e.g. 27000005P1)
                        </button>
                    </div>
                    <!-- Search Toolbar with Live Search -->
                    <div style="padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; gap: 0.75rem; flex: 1; max-width: 580px;">
                            <input type="text" 
                                   id="couponListSearchInput" 
                                   placeholder="Search by Application Number, Candidate Name, or Coupon Code..." 
                                   style="flex: 1; height: 42px; padding: 0.55rem 1rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; outline: none;">
                            <button type="button" class="btn-action-sm btn-verify" style="padding: 0 1.25rem; height: 42px; font-size: 0.84rem;">
                                🔍 Search
                            </button>
                        </div>

                        <div class="tracking-legend">
                            <span class="legend-item"><span class="legend-dot dot-done"></span> Paid &amp; Verified</span>
                            <span class="legend-item" style="color: #059669; font-weight: 700;">Default Coupon: 27EARLYB100 (-₹ 500)</span>
                            <span class="legend-item" style="color: #6366f1; font-weight: 700;">Personal: 261106333P1 (-₹ 500)</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table" id="couponInfoTable">
                            <thead>
                                <tr>
                                    <th>Application Number &amp; CRM ID</th>
                                    <th>Payment Status</th>
                                    <th>Default Coupon</th>
                                    <th>Personal Coupon (e.g. 261106333P1)</th>
                                    <th>Fee Payable &amp; Paid</th>
                                    <th>Date Time</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allStudents as $idx => $std)
                                    @php
                                        $appliNo = $std->application_no ?? $std->applinano ?? ('20' . str_pad($idx + 10001, 8, '0', STR_PAD_LEFT));
                                        $expectedPersonalCode = \App\Services\CouponService::formatPersonalCouponCode($appliNo);
                                        $last7 = strlen($appliNo) >= 7 ? substr($appliNo, -7) : $appliNo;
                                        $last8 = strlen($appliNo) >= 8 ? substr($appliNo, -8) : $appliNo;
                                        $candidateName = $std->student_name ?? $std->name ?? 'Candidate';
                                        $phone = $std->mobile ?? $std->phoneno ?? '';
                                        $email = $std->personal_email ?? $std->emailid ?? ($phone ? $phone . '@student.vignan.ac.in' : 'student@vignan.ac.in');
                                        $programName = $std->program?->program_short_name ?? $std->program?->program_name ?? $std->programlevel ?? $std->branch?->branch_short_name ?? 'B.Tech';
                                        $stdCrmId = $std->crmid ?: \App\Services\CrmIdService::getOrGenerateByAppli($appliNo);
                                        $regulation = $std->regulation ?? 'R25';
                                        $timestamp = $std->log_timestamp;
                                        $dateTimeFormatted = $timestamp ? \Carbon\Carbon::parse($timestamp)->format('d M Y, h:i A') : now()->format('d M Y, h:i A');

                                        // Lookup real payment details or calculate
                                        $payRecord = $payDetails->get($appliNo);
                                        $persCouponObj = $personalCoupons->get($appliNo)
                                            ?? $personalCoupons->firstWhere('couponsno', $expectedPersonalCode)
                                            ?? $personalCoupons->first(fn($c) => !empty($c->couponsno) && str_contains($c->couponsno, $last7))
                                            ?? $personalCoupons->firstWhere('couponsno', $last8);

                                        $defaultCoupon = $payRecord?->defcoupon ?? '27EARLYB100';
                                        $defaultDiscount = (float) ($payRecord?->defcouponamount ?? 500.00);

                                        $personalCoupon = $payRecord?->percoupon ?? ($persCouponObj ? $persCouponObj->couponsno : null);
                                        $personalDiscount = (float) ($payRecord?->percouponamount ?? ($persCouponObj ? $persCouponObj->discountamount : 0.00));

                                        $baseFee = (float) ($payRecord?->feepayable ?? 1200.00);
                                        $totalDiscount = min($baseFee, $defaultDiscount + $personalDiscount);
                                        $paidAmount = $payRecord?->paidamount ?? max(0, $baseFee - $totalDiscount);

                                        $isPaid = ($std->stage1 === 'paid' || ($payRecord && $payRecord->txnstatus === 'success'));
                                        $receiptNo = 'REC-2026-' . str_pad($idx + 101, 5, '0', STR_PAD_LEFT);
                                        $transactionId = $payRecord?->txncrmid ?? ('TXN-VU-' . strtoupper(substr(sha1($appliNo), 0, 10)));
                                        $bankRef = $payRecord?->txnpayuid ?? ('PAY_' . strtoupper(substr(md5($appliNo . 'vignan'), 0, 10)));

                                        $paymentPayload = [
                                            'appli_no' => $appliNo,
                                            'crm_id' => $stdCrmId,
                                            'student_name' => $candidateName,
                                            'phone' => $phone ?: '7780579012',
                                            'email' => $email,
                                            'program' => $programName,
                                            'regulation' => $regulation,
                                            'receipt_no' => $receiptNo,
                                            'transaction_id' => $transactionId,
                                            'bank_ref' => $bankRef,
                                            'payment_date' => $dateTimeFormatted,
                                            'base_fee' => number_format($baseFee, 2),
                                            'default_coupon' => $defaultCoupon,
                                            'default_discount' => number_format($defaultDiscount, 2),
                                            'personal_coupon' => $personalCoupon ?: 'None Assigned',
                                            'personal_discount' => number_format($personalDiscount, 2),
                                            'total_discount' => number_format($totalDiscount, 2),
                                            'paid_amount' => number_format($paidAmount, 2),
                                            'payment_mode' => $payRecord?->paytype ?? 'PayU Online Gateway',
                                            'status' => $isPaid ? 'PAID / SUCCESSFUL' : 'PENDING',
                                        ];
                                    @endphp
                                    <tr class="coupon-track-row" data-search="{{ strtolower($appliNo . ' ' . $candidateName . ' ' . $defaultCoupon . ' ' . ($personalCoupon ?? '')) }}">
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 6px; flex-wrap: wrap;">
                                                <strong style="color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem;">{{ $appliNo }}</strong>
                                                <span style="font-size: 0.68rem; font-weight: 700; background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; padding: 1px 6px; border-radius: 4px; font-family: 'JetBrains Mono', monospace;" title="Branch CRM ID">{{ $stdCrmId }}</span>
                                            </div>
                                            <div style="font-size: 0.74rem; color: #64748b; font-weight: 500; margin-top: 2px;">{{ $candidateName }} • {{ $programName }}</div>
                                            <div style="font-size: 0.70rem; color: #94a3b8;">Code: <code>{{ $expectedPersonalCode }}</code></div>
                                        </td>
                                        <td>
                                            @if($isPaid)
                                                 <span class="badge-status badge-paid">✓ Paid (PayU)</span>
                                            @else
                                                <span class="badge-status" style="background: #fef3c7; color: #d97706; border: 1px solid #fde68a;">Pending</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="coupon-tag-badge" style="background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid #a7f3d0;">{{ $defaultCoupon }}</span>
                                            <div style="font-size: 0.72rem; color: #059669; font-weight: 700; margin-top: 0.2rem;">- ₹ {{ number_format($defaultDiscount, 0) }}</div>
                                        </td>
                                        <td>
                                            @if($personalCoupon)
                                                <span class="coupon-tag-badge badge-personal-tag" style="background: rgba(99, 102, 241, 0.1); color: #6366f1; border: 1px solid #c7d2fe;">
                                                    {{ $personalCoupon }}
                                                </span>
                                                <div style="font-size: 0.72rem; color: #6366f1; font-weight: 700; margin-top: 0.2rem;">- ₹ {{ number_format($personalDiscount, 0) }}</div>
                                            @else
                                                <button type="button" 
                                                        onclick="openPersonalCouponModal('{{ $appliNo }}', '{{ $expectedPersonalCode }}')"
                                                        style="background: #f1f5f9; color: #475569; border: 1px dashed #cbd5e1; padding: 3px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 600; cursor: pointer;">
                                                    + Assign ({{ $expectedPersonalCode }})
                                                </button>
                                            @endif
                                        </td>
                                        <td>
                                            <div style="font-size: 0.78rem; color: #64748b;">Fee: ₹ {{ number_format($baseFee, 0) }}</div>
                                            <div style="font-size: 0.74rem; color: #059669; font-weight: 600;">Discounts: -₹ {{ number_format($totalDiscount, 0) }}</div>
                                            <strong style="color: #059669; font-family: 'JetBrains Mono', monospace; font-size: 0.92rem;">Net: ₹ {{ number_format($paidAmount, 2) }}</strong>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.82rem; color: #334155; font-weight: 600;">{{ $dateTimeFormatted }}</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; gap: 6px; justify-content: center;">
                                                <button type="button" 
                                                        class="btn-action-sm btn-verify" 
                                                        onclick="openPaymentWizard(this)" 
                                                        data-payload="{{ base64_encode(json_encode($paymentPayload)) }}"
                                                        title="View Official Payment Receipt"
                                                        style="height: 32px; padding: 0 12px; font-size: 0.82rem; font-weight: 700; background: #1e40af; color: #ffffff; border: none; border-radius: 6px; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; box-shadow: 0 2px 4px rgba(30, 64, 175, 0.2);">
                                                    <span>💳</span> Receipt
                                                </button>
                                                @if(!$personalCoupon)
                                                    <button type="button" 
                                                            class="btn-action-sm" 
                                                            onclick="openPersonalCouponModal('{{ $appliNo }}', '{{ $expectedPersonalCode }}', '{{ $std->crmid ?? \App\Services\CrmIdService::getOrGenerateByAppli($appliNo) }}', '{{ addslashes($candidateName) }}', '{{ $programName }}')"
                                                            style="background: #e0e7ff; color: #4338ca; border: 1px solid #c7d2fe;"
                                                            title="Generate Personal Coupon">
                                                        🎟️
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                            No coupon and payment records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 3. CRM INCHARGE PERSONAL COUPON GENERATION MODAL -->
                <div class="modal-backdrop" id="personalCouponModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
                    <div style="background: #ffffff; border-radius: 16px; max-width: 520px; width: 100%; box-shadow: 0 25px 50px rgba(0,0,0,0.25); border: 1px solid #e2e8f0; overflow: hidden;">
                        <div style="padding: 1.25rem 1.5rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
                            <div style="font-size: 1.05rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <span>🎟️</span> Generate Personal Coupon (CRM Incharge)
                            </div>
                            <button type="button" onclick="closePersonalCouponModal()" style="background: none; border: none; font-size: 1.4rem; color: #64748b; cursor: pointer;">&times;</button>
                        </div>

                        <form action="{{ route('crm.coupons.generate-personal') }}" method="POST">
                            @csrf
                            <div style="padding: 1.5rem;">
                                <div style="margin-bottom: 1.25rem;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem; text-transform: uppercase;">
                                        Candidate Application Number or CRM ID *
                                    </label>
                                    <input type="text" 
                                           id="modalAppliNo" 
                                           name="applino" 
                                           class="form-input" 
                                           placeholder="e.g. Enter Application Number or CRM ID" 
                                           oninput="updateModalCouponCode(this.value)"
                                           required 
                                           style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: 'JetBrains Mono', monospace; font-size: 0.95rem;">
                                    <div id="modalCandidateBadge" style="display: none; margin-top: 6px; font-size: 0.76rem; color: #047857; font-weight: 600; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 4px 10px; border-radius: 6px;"></div>
                                </div>

                                <div style="margin-bottom: 1.25rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.78rem; color: #1e40af; font-weight: 600;">Personal Coupon Code:</span>
                                        <span id="modalCrmIdTag" style="font-size: 0.72rem; font-weight: 700; background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-family: 'JetBrains Mono', monospace; border: 1px solid #bfdbfe;">CRM ID: V-SAT-0002</span>
                                    </div>
                                    <div id="modalCouponPreview" style="font-size: 1.18rem; font-weight: 800; color: #2563eb; font-family: 'JetBrains Mono', monospace; margin-top: 4px;">
                                        27000005P1
                                    </div>
                                    <div style="font-size: 0.70rem; color: #60a5fa; margin-top: 2px;">
                                        Generated dynamically with branch-wise CRM ID (B.Tech &rarr; V-SAT, M.Tech &rarr; MTECH, PhD &rarr; PHD).
                                    </div>
                                </div>

                                <div style="margin-bottom: 1.25rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                                        <label style="font-size: 0.8rem; font-weight: 700; color: #334155; text-transform: uppercase;">
                                            Concession / Discount Amount (₹) *
                                        </label>
                                        <span style="font-size: 0.72rem; color: #059669; font-weight: 700; background: #ecfdf5; padding: 2px 8px; border-radius: 6px;">
                                            Max Remaining: ₹700 (Fee ₹1,200 - EarlyBird ₹500)
                                        </span>
                                    </div>
                                    <input type="number" 
                                           id="modalDiscountAmount"
                                           name="discount_amount" 
                                           class="form-input" 
                                           placeholder="e.g. 500" 
                                           value="500" 
                                           min="50" 
                                           max="700" 
                                           step="50" 
                                           required 
                                           style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; font-weight: 700; color: #059669;">
                                </div>

                                <div style="font-size: 0.78rem; color: #64748b; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                                    ℹ️ Admission Fee: <strong>₹1,200</strong>. Active default coupon (<code>27EARLYB100</code>) provides <strong>-₹500</strong> concession. Remaining balance is <strong>₹700</strong>. The personal concession is cross-checked against this remaining amount so the student's net payable cannot be negative.
                                </div>
                            </div>

                            <div style="padding: 1rem 1.5rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 10px;">
                                <button type="button" onclick="closePersonalCouponModal()" style="padding: 8px 16px; border: 1px solid #cbd5e1; background: #fff; border-radius: 8px; cursor: pointer; font-weight: 600;">Cancel</button>
                                <button type="submit" style="padding: 8px 18px; background: #6366f1; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer;">
                                    Save &amp; Generate Coupon
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    const candidateCrmMap = {
                        @foreach($allStudents as $std)
                            @php
                                $aNo = $std->applinano ?? $std->application_no ?? '';
                                $cId = $std->crmid ?? \App\Services\CrmIdService::getOrGenerateByAppli($aNo);
                                $pCode = \App\Services\CouponService::formatPersonalCouponCode($aNo);
                                $cName = addslashes($std->name ?? $std->student_name ?? '');
                                $pName = addslashes($std->program?->program_short_name ?? $std->program?->program_name ?? $std->programlevel ?? $std->branch?->branch_short_name ?? 'B.Tech');
                            @endphp
                            '{{ $aNo }}': { applino: '{{ $aNo }}', crmid: '{{ $cId }}', name: '{{ $cName }}', prog: '{{ $pName }}', code: '{{ $pCode }}' },
                            '{{ $cId }}': { applino: '{{ $aNo }}', crmid: '{{ $cId }}', name: '{{ $cName }}', prog: '{{ $pName }}', code: '{{ $pCode }}' },
                        @endforeach
                    };

                    function formatPersonalCouponPreview(val) {
                        val = (val || '').trim();
                        if (!val) return '--------';
                        if (candidateCrmMap[val]) {
                            return candidateCrmMap[val].code;
                        }
                        let clean = val.replace(/\D/g, '');
                        let core = clean;
                        if (clean.startsWith('20') && clean.length === 10) {
                            core = clean.substring(2); // exactly 8 digits: e.g. 27000005
                        } else if (clean.startsWith('20') && clean.length > 10) {
                            core = clean.substring(2);
                        } else if (clean.length >= 8) {
                            core = clean.slice(-8);
                        } else if (clean.length === 7) {
                            core = '26' + clean;
                        }
                        return (core ? core + 'P1' : val).toUpperCase();
                    }

                    function openPersonalCouponModal(appliNo = '', previewCode = '', crmId = '', name = '', prog = '') {
                        const modal = document.getElementById('personalCouponModal');
                        if (!modal) return;
                        modal.style.display = 'flex';
                        const input = document.getElementById('modalAppliNo');
                        if (input) {
                            input.value = appliNo || '';
                            updateModalCouponCode(input.value);
                        }
                    }

                    function closePersonalCouponModal() {
                        const modal = document.getElementById('personalCouponModal');
                        if (modal) modal.style.display = 'none';
                    }

                    function updateModalCouponCode(val) {
                        val = (val || '').trim();
                        const preview = document.getElementById('modalCouponPreview');
                        const crmTag = document.getElementById('modalCrmIdTag');
                        const candBadge = document.getElementById('modalCandidateBadge');

                        let derivedCode = formatPersonalCouponPreview(val);
                        if (preview) preview.textContent = derivedCode;

                        const matched = candidateCrmMap[val];
                        if (matched) {
                            if (crmTag) crmTag.textContent = `CRM ID: ${matched.crmid}`;
                            if (candBadge) {
                                candBadge.style.display = 'block';
                                candBadge.innerHTML = `👤 <strong>${matched.name}</strong> • ${matched.prog} • App: <code>${matched.applino}</code>`;
                            }
                        } else {
                            if (crmTag) crmTag.textContent = val.includes('-') ? `CRM ID: ${val}` : 'CRM ID: V-SAT-XXXX';
                            if (candBadge) candBadge.style.display = 'none';
                        }
                    }

                    @if(request()->is('*percoupongen*') || request()->has('generate') || request()->has('openModal'))
                    document.addEventListener('DOMContentLoaded', function() {
                        openPersonalCouponModal('{{ request('applino', '') }}');
                    });
                    @endif

                    window.openPaymentWizard = function (source) {
                        let data = null;
                        try {
                            if (source && source.dataset && source.dataset.payload) {
                                data = JSON.parse(atob(source.dataset.payload));
                            } else if (typeof source === 'string') {
                                try {
                                    data = JSON.parse(source);
                                } catch (e) {
                                    const btn = document.querySelector(`button[data-payload*="${source}"]`);
                                    if (btn && btn.dataset && btn.dataset.payload) {
                                        data = JSON.parse(atob(btn.dataset.payload));
                                    }
                                }
                            } else if (typeof source === 'object') {
                                data = source;
                            }
                        } catch (err) {
                            console.error('Error reading receipt payload:', err);
                        }

                        if (!data) {
                            console.warn('Receipt data could not be parsed.');
                            return;
                        }

                        const modal = document.getElementById('paymentWizardBackdrop');
                        if (!modal) {
                            console.error('#paymentWizardBackdrop element not found in DOM.');
                            return;
                        }

                        const setTxt = (id, val) => {
                            const el = document.getElementById(id);
                            if (el) el.textContent = val || '';
                        };

                        setTxt('wizReceiptNo', data.receipt_no || 'REC-2026-00101');
                        setTxt('wizTxnId', data.transaction_id || 'TXN-VU-83A912F0');
                        setTxt('wizDateTime', data.payment_date || '');
                        setTxt('wizStudentName', data.student_name || 'Candidate');
                        setTxt('wizAppliNo', data.appli_no || '');
                        setTxt('wizCrmId', data.crm_id || 'V-SAT-0001');
                        setTxt('wizPhone', data.phone ? ('+91 ' + data.phone) : '+91 7780579012');
                        setTxt('wizEmail', data.email || 'student@vignan.ac.in');
                        setTxt('wizProgram', data.program || 'B.Tech');
                        setTxt('wizRegulation', 'Academic Batch 2026-2027');
                        setTxt('wizBaseFee', data.base_fee || '1,200.00');
                        setTxt('wizDefaultCoupon', data.default_coupon || '27EARLYB100');
                        setTxt('wizDefaultDiscount', data.default_discount || '500.00');
                        setTxt('wizPersonalCoupon', (data.personal_coupon && data.personal_coupon !== 'None Assigned') ? data.personal_coupon : 'None Assigned');
                        setTxt('wizPersonalDiscount', data.personal_discount || '0.00');
                        setTxt('wizTotalDiscount', data.total_discount || '500.00');
                        setTxt('wizPaidAmount', data.paid_amount || '700.00');
                        setTxt('wizPaymentMode', data.payment_mode || 'PayU Online Gateway');
                        setTxt('wizBankRef', data.bank_ref || 'PAY_OK_99418294');

                        const statusEl = document.getElementById('wizStatus');
                        if (statusEl) {
                            const isPaid = (data.status && data.status.toUpperCase().includes('PAID'));
                            statusEl.textContent = isPaid ? '✓ PAID / VERIFIED' : 'PENDING';
                            statusEl.className = isPaid ? 'badge-status badge-paid' : 'badge-status';
                            statusEl.style.background = isPaid ? '#ecfdf5' : '#fef3c7';
                            statusEl.style.color = isPaid ? '#047857' : '#d97706';
                            statusEl.style.border = isPaid ? '1px solid #a7f3d0' : '1px solid #fde68a';
                        }

                        modal.style.display = 'flex';
                    };

                    window.closePaymentWizard = function (event) {
                        const modal = document.getElementById('paymentWizardBackdrop');
                        if (event && event.target && event.target !== modal && !event.target.classList.contains('btn-modal-close') && !event.target.closest('.btn-wizard-close')) {
                            return;
                        }
                        if (modal) {
                            modal.style.display = 'none';
                        }
                    };

                    window.printPaymentWizard = function () {
                        window.print();
                    };
                </script>

            @elseif(($activeTab ?? '') === 'employeelist')
                <!-- TAB: EMPLOYEE LIST (STAFF DIRECTORY) -->
                <div class="content-card">
                    <!-- Metric Cards for Employees -->
                    <div class="employee-metrics-strip">
                        <div class="emp-metric-card">
                            <div class="emp-metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #059669;">👥</div>
                            <div class="emp-metric-data">
                                <span class="emp-metric-label">Total Staff</span>
                                <strong class="emp-metric-value">{{ $employees->count() }}</strong>
                                <span class="emp-metric-sub">Admissions &amp; CRM Cell</span>
                            </div>
                        </div>
                        <div class="emp-metric-card">
                            <div class="emp-metric-icon" style="background: rgba(59, 130, 246, 0.12); color: #2563eb;">🏛️</div>
                            <div class="emp-metric-data">
                                <span class="emp-metric-label">CRM Leadership</span>
                                <strong class="emp-metric-value">Dean &amp; Incharge</strong>
                                <span class="emp-metric-sub">Administrative Officers</span>
                            </div>
                        </div>
                        <div class="emp-metric-card">
                            <div class="emp-metric-icon" style="background: rgba(147, 51, 234, 0.12); color: #7c3aed;">💼</div>
                            <div class="emp-metric-data">
                                <span class="emp-metric-label">Staff Cadre</span>
                                <strong class="emp-metric-value">Permanent</strong>
                                <span class="emp-metric-sub">University Appointed</span>
                            </div>
                        </div>
                        <div class="emp-metric-card">
                            <div class="emp-metric-icon" style="background: rgba(16, 185, 129, 0.12); color: #10b981;">🛡️</div>
                            <div class="emp-metric-data">
                                <span class="emp-metric-label">System Access</span>
                                <strong class="emp-metric-value">100% Active</strong>
                                <span class="emp-metric-sub">Verified &amp; Authenticated</span>
                            </div>
                        </div>
                    </div>

                    <!-- Search Toolbar with Live Search -->
                    <div style="padding: 1.25rem 1.5rem; background: #ffffff; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <div style="display: flex; gap: 0.75rem; flex: 1; max-width: 580px;">
                            <input type="text" 
                                   id="employeeListSearchInput" 
                                   placeholder="Search by Employee Name, Code, Email, Phone, or Designation..." 
                                   style="flex: 1; height: 42px; padding: 0.55rem 1rem; border: 1.5px solid #cbd5e1; border-radius: 8px; font-size: 0.88rem; outline: none;">
                            <button type="button" class="btn-action-sm btn-verify" style="padding: 0 1.25rem; height: 42px; font-size: 0.84rem;">
                                🔍 Search
                            </button>
                        </div>

                        <div class="tracking-legend">
                            <span class="legend-item"><span class="legend-dot dot-done"></span> Active University Staff</span>
                            <span id="empCountBadge" style="font-weight: 700; color: #059669; font-size: 0.82rem;">Showing {{ $employees->count() }} employees</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table" id="employeesTable">
                            <thead>
                                <tr>
                                    <th>Emp Code</th>
                                    <th>Employee Name</th>
                                    <th>Official Email</th>
                                    <th>Phone Number</th>
                                    <th>Campus</th>
                                    <th style="text-align: center;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $emp)
                                    @php
                                        $empCode = $emp->emp_code;
                                        $fullName = $emp->full_name;
                                        $email = $emp->ofc_mailid ?: ($empCode . '@vignan.ac.in');
                                        $personalMail = $emp->personal_mailid ?: (strtolower(str_replace(' ', '', $fullName)) . '@gmail.com');
                                        $phone = $emp->phone_primary ?: 'N/A';
                                        $phoneSecondary = $emp->phone_secondary ?: 'N/A';
                                        $empType = $emp->emp_type ?: 'N/A';
                                        $campusName = $emp->campus_id ? ('Campus #' . $emp->campus_id) : 'N/A';

                                        $assignedRoleNames = collect(\App\Services\RoleService::getAssignedRoles($empCode))
                                            ->pluck('role_name')
                                            ->filter()
                                            ->values();
                                        $primaryRoleName = $assignedRoleNames->first() ?? 'Staff';

                                        $designation = $emp->designation_id ? ('Designation #' . $emp->designation_id) : $primaryRoleName;
                                        $department = $emp->department_id ? ('Department #' . $emp->department_id) : 'N/A';
                                        $roleTag = strtoupper($primaryRoleName);

                                        $roleTagClass = match (true) {
                                            str_contains(strtolower($primaryRoleName), 'dean') => 'badge-role-dean',
                                            str_contains(strtolower($primaryRoleName), 'incharge') => 'badge-role-incharge',
                                            default => 'badge-role-staff',
                                        };

                                        $initials = collect(explode(' ', $fullName))
                                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                                            ->take(2)
                                            ->join('');

                                        $joinDate = $emp->date_of_join ? \Carbon\Carbon::parse($emp->date_of_join)->format('d M Y') : '15 Jun 2018';
                                        $auditTime = $emp->log_timestamp ? \Carbon\Carbon::parse($emp->log_timestamp)->format('d M Y, h:i A') : '08 Oct 2026, 09:42 AM';

                                        $empPayload = [
                                            'emp_code' => $empCode,
                                            'full_name' => $fullName,
                                            'email' => $email,
                                            'personal_mail' => $personalMail,
                                            'phone' => $phone,
                                            'phone_secondary' => $phoneSecondary,
                                            'designation' => $designation,
                                            'department' => $department,
                                            'role_tag' => $roleTag,
                                            'emp_type' => $empType,
                                            'campus' => $campusName,
                                            'join_date' => $joinDate,
                                            'audit_time' => $auditTime,
                                            'ip_address' => $emp->log_ipaddress ?: '192.168.1.10',
                                            'initials' => $initials ?: 'VF',
                                        ];
                                    @endphp
                                    <tr class="employee-track-row" data-search="{{ strtolower($empCode . ' ' . $fullName . ' ' . $email . ' ' . $phone . ' ' . $designation . ' ' . $department) }}">
                                        <td>
                                            <code class="emp-code-badge">{{ $empCode }}</code>
                                        </td>
                                        <td>
                                            <div class="emp-name-wrapper">
                                                <div class="emp-avatar-circle">{{ $initials ?: 'VF' }}</div>
                                                <div>
                                                    <div class="emp-name-text">{{ $fullName }}</div>
                                                    <span class="badge-role-pill {{ $roleTagClass }}">{{ $roleTag }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="mailto:{{ $email }}" class="emp-email-link" title="Send email to {{ $fullName }}">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                    <polyline points="22,6 12,13 2,6"/>
                                                </svg>
                                                {{ $email }}
                                            </a>
                                        </td>
                                        <td>
                                            <span class="emp-phone-mono">
                                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                                </svg>
                                                +91 {{ $phone }}
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.8rem; color: #334155; font-weight: 600;">Vadlamudi</span>
                                        </td>
                                        <td style="text-align: center;">
                                            <button type="button" 
                                                    class="btn-action-sm btn-verify" 
                                                    onclick='openEmployeeProfile(@json($empPayload))' 
                                                    title="View Full Employee Profile &amp; Details">
                                                <span>👤</span> View Profile
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                            No employee records found in the database.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif(($activeTab ?? '') === 'programs' || ($activeTab ?? '') === 'branches' || ($activeTab ?? '') === 'campuses')
                <!-- TAB: ACADEMIC MASTER DATA (For Employee & Dean) -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">🎓 University Academic Programs (master.programs & master.branches)</div>
                            <div class="card-subtitle">Official VFSTR degree courses, duration, and branch specializations</div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>Program Code</th>
                                    <th>Program Title</th>
                                    <th>Level</th>
                                    <th>Duration</th>
                                    <th>Mode</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($programs as $p)
                                    <tr>
                                        <td><code>{{ $p->program_code }}</code></td>
                                        <td><strong>{{ $p->program_long_name }}</strong></td>
                                        <td><span class="badge-status badge-applied">{{ $p->program_level }}</span></td>
                                        <td>{{ $p->program_duration ?? '4 Years' }}</td>
                                        <td>{{ $p->program_mode ?? 'Regular' }}</td>
                                        <td><span class="badge-status badge-paid">Active</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            @elseif(($activeTab ?? '') === 'kpi' || ($activeTab ?? '') === 'studentkpi')
                <!-- TAB: KPI & CONVERSION ANALYTICS -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">📊 Key Performance Indicators & Conversion Analytics (master.menus: KPII0001)</div>
                            <div class="card-subtitle">Strategic intake benchmarks and admissions pipeline funnel</div>
                        </div>
                    </div>

                    <div style="padding: 1.5rem;">
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
                            <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; background: #ffffff;">
                                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Inquiry to Application Ratio</div>
                                <div style="font-size: 1.85rem; font-weight: 800; color: #2563eb; margin: 0.35rem 0;">84.2%</div>
                                <div style="font-size: 0.75rem; color: #10b981; font-weight: 600;">↑ 5.4% higher than last academic year</div>
                            </div>
                            <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; background: #ffffff;">
                                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Application to Verification Rate</div>
                                <div style="font-size: 1.85rem; font-weight: 800; color: #059669; margin: 0.35rem 0;">92.8%</div>
                                <div style="font-size: 0.75rem; color: #10b981; font-weight: 600;">Documents verified within 24 hours</div>
                            </div>
                            <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 1.25rem; background: #ffffff;">
                                <div style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Fee Paid / Enrollment Confirmation</div>
                                <div style="font-size: 1.85rem; font-weight: 800; color: #7c3aed; margin: 0.35rem 0;">76.5%</div>
                                <div style="font-size: 0.75rem; color: #64748b;">Seat reservation fee deposited</div>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif(($activeTab ?? '') === 'overview')
                <!-- TAB: OVERVIEW DASHBOARD (HIDDEN BY DEFAULT) -->
                <div class="content-card">
                    <div class="card-header-bar">
                        <div>
                            <div class="card-title">
                                @if($roleId == 3)
                                    🏛️ Executive Admissions Pipeline
                                @elseif($roleId == 2)
                                    👔 Daily Incharge Operations & Verification
                                @else
                                    💼 Administrative Registry
                                @endif
                            </div>
                            <div class="card-subtitle">Real-time candidate registrations and status</div>
                        </div>
                        <a href="{{ route('crm.stddata') }}" class="btn-action-sm btn-verify">View All Enquiries &rarr;</a>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>Application ID</th>
                                    <th>Candidate Name</th>
                                    <th>Mobile Number</th>
                                    <th>Email ID</th>
                                    <th>Campus</th>
                                    <th>Status</th>
                                    <th>Quick Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents as $student)
                                    <tr>
                                        <td><strong>{{ $student->application_no ?? $student->applinano ?? 'N/A' }}</strong></td>
                                        <td>{{ $student->student_name ?? $student->name ?? 'Candidate' }}</td>
                                        <td>📞 +91 {{ $student->mobile ?? $student->phoneno ?? 'N/A' }}</td>
                                        <td>{{ $student->personal_email ?? $student->emailid ?? 'N/A' }}</td>
                                        <td>Vadlamudi (Main)</td>
                                        <td><span class="badge-status badge-applied">{{ $student->std_status ?? 'Registered' }}</span></td>
                                        <td>
                                            <a href="tel:{{ $student->mobile ?? $student->phoneno }}" class="btn-action-sm btn-call">📞 Call</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                            No recent registrations found in database.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>

<!-- ==========================================================================
     ALLOTMENT MODAL BOX (Opens right on this page when clicking Assign)
     ========================================================================== -->
<div id="assignModalBackdrop" class="modal-backdrop-custom" style="display: none;" onclick="closeAssignBox(event)">
    <div class="assign-modal-card" onclick="event.stopPropagation()">
        <!-- Modal Header -->
        <div class="modal-card-header">
            <div class="modal-title-group">
                <div class="modal-icon-badge">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <polyline points="16 11 18 13 22 9"/>
                    </svg>
                </div>
                <div>
                    <h3 class="modal-main-title">Allot Admission Enquiry</h3>
                    <p class="modal-sub-title">Select employee and allotment mode to assign enquiries</p>
                </div>
            </div>
            <button type="button" class="btn-modal-close" onclick="closeAssignBox()" title="Close dialog">&times;</button>
        </div>

        <!-- Modal Body -->
        <div class="modal-card-body">
            <!-- Selected Enquiry Pill (Context) -->
            <div id="modalTargetEnquiryPill" class="target-enquiry-banner">
                <span class="banner-badge">Enquiry</span>
                <div class="enquiry-text-group">
                    <span id="modalTargetAppliNo" class="enquiry-target-value">20########</span>
                    <span id="modalTargetName" class="enquiry-candidate-name">(Hemanth Kumar Vucha)</span>
                </div>
            </div>

            <form id="assignEnquiryForm" onsubmit="event.preventDefault(); submitAllotment();">
                <input type="hidden" id="modalTargetAppliNoInput" value="">
                
                <!-- 1. Select Telecaller Dropdown (Filtered by Role 4) -->
                <div class="form-group-custom">
                    <label for="assignEmployeeSelect" class="form-label-custom">
                        Select Telecaller (Role: Tele Caller) <span class="required-asterisk">*</span>
                    </label>
                    <div class="select-wrapper-custom">
                        <select id="assignEmployeeSelect" class="form-control-custom" required>
                            <option value="">-- Choose Telecaller --</option>
                            @if(isset($telecallers) && $telecallers->isNotEmpty())
                                <optgroup label="📞 Telecallers (Role: Tele Caller)">
                                    @foreach($telecallers as $tc)
                                        <option value="{{ $tc->emp_code }}" data-code="{{ $tc->emp_code }}" data-name="{{ $tc->full_name }}">
                                            {{ $tc->full_name }} ({{ $tc->emp_code }}) - Tele Caller
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                            @php
                                $otherStaff = $employees->whereNotIn('emp_code', $telecallers?->pluck('emp_code') ?? collect());
                            @endphp
                            @if($otherStaff->isNotEmpty())
                                <optgroup label="👥 Other Admissions Staff">
                                    @foreach($otherStaff as $emp)
                                        <option value="{{ $emp->emp_code }}" data-code="{{ $emp->emp_code }}" data-name="{{ $emp->full_name }}">
                                            {{ $emp->full_name }} ({{ $emp->emp_code }}{{ $emp->emp_type ? ' - ' . $emp->emp_type : '' }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        </select>
                    </div>
                    <span class="input-hint" style="color: #059669; font-weight: 600;">Directly allocates leads to telecaller's queue in crm.telecaller_leads</span>
                </div>

                <!-- 2. Allot Mode Dropdown -->
                <div class="form-group-custom">
                    <label for="assignAllotModeSelect" class="form-label-custom">
                        Allot Mode <span class="required-asterisk">*</span>
                    </label>
                    <div class="select-wrapper-custom">
                        <select id="assignAllotModeSelect" class="form-control-custom" onchange="onAllotModeChange(this.value)" required>
                            <option value="unallocated">All Unallocated Leads ({{ $unallocatedLeadsCount ?? 0 }} Leads)</option>
                            <option value="first 50">First 50 Leads</option>
                            <option value="first 100">First 100 Leads</option>
                            <option value="custom">Custom Lead Count</option>
                            <option value="datewise">Date-wise (Target Date)</option>
                        </select>
                    </div>
                    <!-- Dedicated Info Callout Box -->
                    <div class="mode-help-banner" id="modeHelpBanner">
                        <span class="banner-info-icon">ℹ️</span>
                        <span id="modeHelpText">Allots unallocated candidate leads to the selected telecaller.</span>
                    </div>
                </div>

                <!-- 3. Assign Initial Step / Task to Telecaller (Stored in crm.telecaller_leads_tasks) -->
                <div class="form-group-custom">
                    <label for="assignStageSelect" class="form-label-custom">
                        Assign Initial Step / Task <span class="required-asterisk">*</span>
                    </label>
                    <div class="select-wrapper-custom">
                        <select id="assignStageSelect" class="form-control-custom" required>
                            <option value="Stage 1: Calling & Verification">Stage 1: Calling &amp; Verification</option>
                            <option value="Stage 1: Fee Payment Follow-up">Stage 1: Fee Payment Follow-up</option>
                            <option value="Stage 2: Personal Profile Completion">Stage 2: Personal Profile Completion</option>
                            <option value="Stage 3: Academic Details Entry">Stage 3: Academic Details Entry</option>
                            <option value="Stage 4: Document Verification">Stage 4: Document Verification</option>
                            <option value="Stage 5: Scholarship & Concession Grant">Stage 5: Scholarship &amp; Concession Grant</option>
                        </select>
                    </div>
                    <span class="input-hint">Persisted into <code>crm.telecaller_leads_tasks</code> for telecaller tracking.</span>
                </div>

                <!-- 4. Remarks Field -->
                <div class="form-group-custom">
                    <label for="assignRemarksInput" class="form-label-custom">
                        Telecaller Task Remarks
                    </label>
                    <input type="text" id="assignRemarksInput" class="form-control-custom" placeholder="e.g. Call candidate regarding provisional seat and early bird concession" value="Initial telecalling allocation for admission counseling">
                </div>

                <!-- Dynamic Extra Field: Datewise Input -->
                <div id="datewiseFieldGroup" class="form-group-custom" style="display: none;">
                    <label for="allotDatewiseInput" class="form-label-custom">
                        Target Enquiry Date:
                    </label>
                    <input type="date" id="allotDatewiseInput" class="form-control-custom" value="{{ date('Y-m-d') }}">
                    <span class="input-hint">All enquiries matching this date will be allotted to the selected employee.</span>
                </div>

                <!-- Dynamic Extra Field: Custom Count Input -->
                <div id="customCountFieldGroup" class="form-group-custom" style="display: none;">
                    <label for="allotCustomCountInput" class="form-label-custom">
                        Enter Number of Enquiries:
                    </label>
                    <input type="number" id="allotCustomCountInput" class="form-control-custom" min="1" max="1000" placeholder="e.g. 25" value="25">
                    <span class="input-hint">Specify the number of enquiries to allot in sequence.</span>
                </div>

                <!-- Modal Actions -->
                <div class="modal-card-footer">
                    <button type="button" class="btn-modal-cancel" onclick="closeAssignBox()">Cancel</button>
                    <button type="button" class="btn-modal-submit" id="btnSubmitAllotment" onclick="submitAllotment()">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Allot
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ==========================================================================
     PAYMENT & COUPON RECEIPT WIZARD MODAL (Opens when clicking Action in Coupon Info)
     ========================================================================== -->
<div id="paymentWizardBackdrop" class="modal-backdrop-custom" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem; box-sizing: border-box;" onclick="closePaymentWizard(event)">
    <div class="payment-wizard-card" onclick="event.stopPropagation()" style="width: 95%; max-width: 820px; max-height: 92vh; background: #ffffff; border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4); display: flex; flex-direction: column; overflow: hidden;">
        <!-- Wizard Header -->
        <div class="wizard-card-header" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 1.15rem 1.75rem; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="wizard-header-brand" style="display: flex; align-items: center; gap: 14px;">
                <div style="background: #ffffff; border-radius: 8px; padding: 4px 10px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                    <img src="/images/vignan-logo.png" alt="Vignan's University" style="height: 36px; object-fit: contain;">
                </div>
                <div>
                    <div class="wizard-uni-name" style="color: #94a3b8; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em;">VIGNAN'S FOUNDATION FOR SCIENCE, TECHNOLOGY &amp; RESEARCH</div>
                    <h3 class="wizard-title" style="color: #ffffff; font-size: 1.15rem; font-weight: 800; margin: 0; letter-spacing: -0.01em;">Admission Fee Payment &amp; Concession Receipt</h3>
                </div>
            </div>
            <button type="button" class="btn-modal-close no-print" onclick="closePaymentWizard()" title="Close dialog" style="color: #cbd5e1; font-size: 1.7rem; background: transparent; border: none; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <!-- Wizard Stepper Indicator -->
        <div class="wizard-stepper-bar no-print">
            <div class="wizard-step completed">
                <div class="step-circle">1</div>
                <div class="step-text">Application Fee</div>
            </div>
            <div class="wizard-step-line completed"></div>
            <div class="wizard-step completed">
                <div class="step-circle">2</div>
                <div class="step-text">Coupons Applied</div>
            </div>
            <div class="wizard-step-line completed"></div>
            <div class="wizard-step completed">
                <div class="step-circle">3</div>
                <div class="step-text">Gateway Payment</div>
            </div>
            <div class="wizard-step-line completed"></div>
            <div class="wizard-step completed">
                <div class="step-circle">✓</div>
                <div class="step-text">Verified Receipt</div>
            </div>
        </div>

        <!-- Wizard Body Content -->
        <div class="wizard-card-body" id="printablePaymentArea" style="overflow-y: auto; padding: 1.5rem 1.75rem; background: #ffffff;">
            <!-- Official University Letterhead Banner -->
            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 1rem; border-bottom: 2px solid #1e3a8a; margin-bottom: 1.25rem; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <img src="/images/vignan-logo.png" alt="Vignan Logo" style="height: 55px; max-width: 170px; object-fit: contain;">
                    <div style="border-left: 2px solid #e2e8f0; padding-left: 14px;">
                        <div style="font-size: 1.05rem; font-weight: 800; color: #1e3a8a; text-transform: uppercase; letter-spacing: 0.5px; line-height: 1.25;">
                            Vignan's Foundation for Science, Technology &amp; Research
                        </div>
                        <div style="font-size: 0.76rem; color: #475569; font-weight: 600; margin-top: 2px;">
                            (Deemed to be University estd. u/s 3 of UGC Act 1956) • NAAC 'A+' Accredited
                        </div>
                        <div style="font-size: 0.72rem; color: #64748b; font-weight: 500;">
                            Vadlamudi, Guntur - 522 213, Andhra Pradesh, India
                        </div>
                    </div>
                </div>
                <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end;">
                    <img src="/images/vignan-shield-logo.png" alt="VFSTR Shield" style="height: 50px; object-fit: contain;">
                    <span style="font-size: 0.68rem; font-weight: 800; color: #1e3a8a; background: #e0f2fe; padding: 3px 8px; border-radius: 4px; margin-top: 4px; letter-spacing: 0.05em; text-transform: uppercase; border: 1px solid #bfdbfe;">
                        Official E-Receipt
                    </span>
                </div>
            </div>

            <!-- Official Watermark / Badge Banner -->
            <div class="receipt-meta-banner">
                <div class="meta-item">
                    <span class="meta-label">RECEIPT NUMBER</span>
                    <strong class="meta-value" id="wizReceiptNo">REC-2026-00101</strong>
                </div>
                <div class="meta-item">
                    <span class="meta-label">TRANSACTION ID</span>
                    <strong class="meta-value" id="wizTxnId">TXN-VU-83A912F0</strong>
                </div>
                <div class="meta-item">
                    <span class="meta-label">PAYMENT DATE &amp; TIME</span>
                    <span class="meta-value" id="wizDateTime">04 Nov 2026, 03:09 PM</span>
                </div>
                <div class="meta-item">
                    <span class="meta-label">STATUS</span>
                    <span class="badge-status badge-paid" id="wizStatus">✓ PAID / VERIFIED</span>
                </div>
            </div>

            <!-- Student Profile Grid -->
            <div class="wizard-section-title">
                <span>👤</span> Candidate &amp; Program Information
            </div>
            <div class="wizard-grid-info">
                <div class="info-cell">
                    <span class="cell-label">Student Name:</span>
                    <strong class="cell-val" id="wizStudentName">Candidate</strong>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Application Number:</span>
                    <strong class="cell-val cell-code" id="wizAppliNo">20261106333</strong>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Branch CRM ID:</span>
                    <strong class="cell-val cell-code" id="wizCrmId" style="color: #1d4ed8; background: #eff6ff; padding: 2px 8px; border-radius: 4px; border: 1px solid #bfdbfe;">V-SAT-0001</strong>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Mobile Number:</span>
                    <span class="cell-val" id="wizPhone">+91 7780579012</span>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Email ID:</span>
                    <span class="cell-val" id="wizEmail">student@vignan.ac.in</span>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Enrolled Program:</span>
                    <strong class="cell-val" id="wizProgram">B.Tech</strong>
                </div>
                <div class="info-cell" style="grid-column: span 3;">
                    <span class="cell-label">Academic Batch:</span>
                    <span class="cell-val" id="wizRegulation">Academic Batch 2026-2027</span>
                </div>
            </div>

            <!-- Coupon & Fee Breakdown Table -->
            <div class="wizard-section-title" style="margin-top: 1.25rem;">
                <span>🏷️</span> Fee Assessment &amp; Coupon Deductions
            </div>
            <table class="wizard-breakdown-table">
                <thead>
                    <tr>
                        <th>Fee Particulars / Coupon Description</th>
                        <th>Coupon Code</th>
                        <th style="text-align: right;">Amount (INR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Standard Application &amp; Registration Fee</strong>
                            <div class="breakdown-sub">Official prospectus and entrance assessment processing</div>
                        </td>
                        <td><span class="breakdown-plain">—</span></td>
                        <td style="text-align: right; font-weight: 700;">₹ <span id="wizBaseFee">1,200.00</span></td>
                    </tr>
                    <tr class="discount-row">
                        <td>
                            <strong>Institutional Early Bird Waiver</strong>
                            <div class="breakdown-sub">VFSTR 2026 Open Campus Early Incentive</div>
                        </td>
                        <td><span class="coupon-tag-badge" id="wizDefaultCoupon">27EARLYB100</span></td>
                        <td style="text-align: right; color: #059669; font-weight: 700;">- ₹ <span id="wizDefaultDiscount">500.00</span></td>
                    </tr>
                    <tr class="discount-row">
                        <td>
                            <strong>Personal Concession Coupon</strong>
                            <div class="breakdown-sub">Special admission council referral redemption</div>
                        </td>
                        <td><span class="coupon-tag-badge badge-personal-tag" id="wizPersonalCoupon">261106333P1</span></td>
                        <td style="text-align: right; color: #059669; font-weight: 700;">- ₹ <span id="wizPersonalDiscount">600.00</span></td>
                    </tr>
                    <tr class="subtotal-row">
                        <td colspan="2">
                            <strong>Total Concession Applied</strong>
                        </td>
                        <td style="text-align: right; color: #059669; font-weight: 800;">- ₹ <span id="wizTotalDiscount">1,100.00</span></td>
                    </tr>
                    <tr class="net-total-row">
                        <td colspan="2">
                            <div class="net-title">NET AMOUNT PAID</div>
                            <div class="net-subtitle">Confirmed by Payment Gateway • No Additional Dues</div>
                        </td>
                        <td style="text-align: right;">
                            <div class="net-amount">₹ <span id="wizPaidAmount">100.00</span></div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Banking & Gateway Details -->
            <div class="wizard-gateway-strip">
                <div class="gateway-item">
                    <span class="gw-label">Payment Gateway:</span>
                    <strong class="gw-val" id="wizPaymentMode">PayU Online Gateway</strong>
                </div>
                <div class="gateway-item">
                    <span class="gw-label">Bank Reference / UTR:</span>
                    <code class="gw-val" id="wizBankRef">PAY_OK_99418294</code>
                </div>
                <div class="gateway-item">
                    <span class="gw-label">Reconciliation:</span>
                    <span class="gw-val" style="color: #059669; font-weight: 700;">✓ Auto-Reconciled &amp; Accounts Audited</span>
                </div>
            </div>

            <!-- Authentic Seal Footer -->
            <div class="wizard-seal-footer" style="margin-top: 1.25rem;">
                <div class="seal-text-group" style="display: flex; align-items: center; gap: 12px;">
                    <img src="/images/vignan-shield-logo.png" alt="VFSTR Seal" style="height: 44px; object-fit: contain;">
                    <div>
                        <div class="seal-heading" style="font-size: 0.82rem; font-weight: 700; color: #1e3a8a;">Admissions Accounts &amp; Financial Division</div>
                        <div class="seal-sub" style="font-size: 0.74rem; color: #475569;">VFSTR Deemed to be University, Vadlamudi, Guntur, AP - 522213</div>
                        <div class="seal-note" style="font-size: 0.70rem; color: #64748b; margin-top: 2px;">This document is an electronically verified admission payment receipt. Valid without physical signature under IT Act 2000.</div>
                    </div>
                </div>
                <div class="seal-stamp-box">
                    <div class="stamp-verified-circle">
                        <span>PAID</span>
                        <small>VFSTR CRM</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Down Wizard Actions: Print and Close (Strictly Required Options) -->
        <div class="wizard-actions-footer no-print" style="padding: 1rem 1.75rem; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; gap: 12px;">
            <button type="button" class="btn-wizard-print" onclick="printPaymentWizard()" title="Print Payment Receipt" style="display: inline-flex; align-items: center; gap: 8px; padding: 9px 18px; border-radius: 8px; background: #1e3a8a; color: #ffffff; border: none; font-weight: 700; cursor: pointer; font-size: 0.88rem;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                <span>Print Receipt</span>
            </button>
            <button type="button" class="btn-wizard-close" onclick="closePaymentWizard()" title="Close Wizard" style="display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px; background: #ffffff; color: #475569; border: 1px solid #cbd5e1; font-weight: 600; cursor: pointer; font-size: 0.88rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
                <span>Close</span>
            </button>
        </div>
    </div>
</div>

<!-- ==========================================================================
     EMPLOYEE PROFILE MODAL (Opens when clicking View Profile in Employee List)
     ========================================================================== -->
<div id="employeeProfileModalBackdrop" class="modal-backdrop-custom" style="display: none;" onclick="closeEmployeeProfile(event)">
    <div class="emp-profile-card" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="emp-profile-header">
            <div class="emp-profile-avatar-large" id="modalEmpAvatar">GK</div>
            <div class="emp-profile-header-info">
                <div class="emp-profile-code-strip">
                    <span class="emp-code-badge" id="modalEmpCode">03434</span>
                    <span class="badge-role-pill badge-role-incharge" id="modalEmpRole">CRM INCHARGE</span>
                    <span class="badge-status badge-verified">✓ Active</span>
                </div>
                <h3 class="emp-profile-name" id="modalEmpName">Ganesh kumar</h3>
                <div class="emp-profile-desig" id="modalEmpDesignation">Admissions Incharge &amp; Operations Head</div>
            </div>
            <button type="button" class="btn-modal-close" onclick="closeEmployeeProfile()" title="Close dialog">&times;</button>
        </div>

        <!-- Body -->
        <div class="emp-profile-body">
            <!-- Section 1: Contact Information -->
            <div class="profile-section-heading">
                <span>📞</span> Official &amp; Personal Contact
            </div>
            <div class="profile-details-grid">
                <div class="profile-detail-cell">
                    <span class="detail-label">Official Email:</span>
                    <strong class="detail-val" id="modalEmpEmail">mskv_mgt@vignan.ac.in</strong>
                </div>
                <div class="profile-detail-cell">
                    <span class="detail-label">Personal Email:</span>
                    <span class="detail-val" id="modalEmpPersonalMail">ganesh.mgmt@gmail.com</span>
                </div>
                <div class="profile-detail-cell">
                    <span class="detail-label">Primary Mobile:</span>
                    <strong class="detail-val cell-mono" id="modalEmpPhone">+91 9391364210</strong>
                </div>
                <div class="profile-detail-cell">
                    <span class="detail-label">Secondary / Office Ext:</span>
                    <span class="detail-val cell-mono" id="modalEmpPhoneSec">0863-2344700</span>
                </div>
            </div>

            <!-- Section 2: Department & Academic Posting -->
            <div class="profile-section-heading" style="margin-top: 1.25rem;">
                <span>🏛️</span> University Directorate &amp; Posting
            </div>
            <div class="profile-details-grid">
                <div class="profile-detail-cell">
                    <span class="detail-label">Assigned Department:</span>
                    <strong class="detail-val" id="modalEmpDepartment">Management Studies &amp; CRM Cell</strong>
                </div>
                <div class="profile-detail-cell">
                    <span class="detail-label">Campus Location:</span>
                    <span class="detail-val" id="modalEmpCampus">VFSTR Vadlamudi Main Campus</span>
                </div>
                <div class="profile-detail-cell">
                    <span class="detail-label">Cadre / Employment Type:</span>
                    <span class="badge-status badge-paid" id="modalEmpType">PERMANENT</span>
                </div>
                <div class="profile-detail-cell">
                    <span class="detail-label">Date of Joining:</span>
                    <span class="detail-val" id="modalEmpJoinDate">15 Jun 2018</span>
                </div>
            </div>

            <!-- Section 3: CRM System Privileges -->
            <div class="profile-section-heading" style="margin-top: 1.25rem;">
                <span>🛡️</span> CRM Module Privileges &amp; Responsibilities
            </div>
            <div class="profile-privileges-box">
                <div class="privilege-item">
                    <span class="priv-icon">✓</span>
                    <div>
                        <strong>Admission Enquiry Allotment:</strong> Authorized to assign incoming student leads to telecallers and counselors.
                    </div>
                </div>
                <div class="privilege-item">
                    <span class="priv-icon">✓</span>
                    <div>
                        <strong>Register List &amp; Stage Tracking:</strong> Full visibility across Step 1 to Step 6 admission progress.
                    </div>
                </div>
                <div class="privilege-item">
                    <span class="priv-icon">✓</span>
                    <div>
                        <strong>Coupon &amp; Fee Reconciliation:</strong> Access to verify candidate fee payments and coupon deductions.
                    </div>
                </div>
            </div>

            <!-- Section 4: Audit Trail -->
            <div class="profile-audit-banner">
                <div class="audit-item">
                    <span class="audit-lbl">System Usercode:</span>
                    <code id="modalEmpAuditCode">03434</code>
                </div>
                <div class="audit-item">
                    <span class="audit-lbl">Authorized Audit IP:</span>
                    <code id="modalEmpAuditIp">192.168.1.10</code>
                </div>
                <div class="audit-item">
                    <span class="audit-lbl">Last Sync Timestamp:</span>
                    <span id="modalEmpAuditTime">08 Oct 2026, 09:42 AM</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="emp-profile-footer">
            <button type="button" class="btn-modal-cancel" onclick="closeEmployeeProfile()" style="min-width: 110px;">
                Close
            </button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('liveFilterSearch');
    if (searchInput && window.handleEnquiryLiveSearch) {
        searchInput.addEventListener('input', function() {
            window.handleEnquiryLiveSearch(this.value);
        });
        searchInput.addEventListener('keyup', function() {
            window.handleEnquiryLiveSearch(this.value);
        });
        if (searchInput.value && searchInput.value.trim()) {
            window.handleEnquiryLiveSearch(searchInput.value);
        }
    }

    // Live search for Register List (Student Tracking)
    const registerSearchInput = document.getElementById('registerListSearchInput');
    const registerRows = Array.from(document.querySelectorAll('.register-track-row'));
    if (registerSearchInput && registerRows.length > 0) {
        const filterRegister = function () {
            const query = registerSearchInput.value.trim().toLowerCase();
            registerRows.forEach(row => {
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();
                row.style.display = !query || searchData.includes(query) ? '' : 'none';
            });
        };
        registerSearchInput.addEventListener('input', filterRegister);
        registerSearchInput.addEventListener('keyup', filterRegister);
        if (registerSearchInput.value && registerSearchInput.value.trim()) {
            filterRegister();
        }
    }

    // Live search for Coupon Info (Payment & Coupon Tracking)
    const couponSearchInput = document.getElementById('couponListSearchInput');
    const couponRows = Array.from(document.querySelectorAll('.coupon-track-row'));
    if (couponSearchInput && couponRows.length > 0) {
        const filterCoupons = function () {
            const query = couponSearchInput.value.trim().toLowerCase();
            couponRows.forEach(row => {
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();
                row.style.display = !query || searchData.includes(query) ? '' : 'none';
            });
        };
        couponSearchInput.addEventListener('input', filterCoupons);
        couponSearchInput.addEventListener('keyup', filterCoupons);
        if (couponSearchInput.value && couponSearchInput.value.trim()) {
            filterCoupons();
        }
    }

    // Live search for Employee Directory
    const empSearchInput = document.getElementById('employeeListSearchInput');
    const empRows = Array.from(document.querySelectorAll('.employee-track-row'));
    const empCountBadge = document.getElementById('empCountBadge');
    if (empSearchInput && empRows.length > 0) {
        empSearchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let visible = 0;
            empRows.forEach(row => {
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();
                const match = !query || searchData.includes(query);
                row.style.display = match ? '' : 'none';
                if (match) visible++;
            });
            if (empCountBadge) {
                empCountBadge.textContent = `Showing ${visible} of ${empRows.length} employees`;
            }
        });
    }

    // =========================================================================
    // EMPLOYEE PROFILE MODAL
    // =========================================================================
    window.openEmployeeProfile = function (data) {
        if (!data) return;
        const modal = document.getElementById('employeeProfileModalBackdrop');
        if (!modal) return;

        const setTxt = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val || '';
        };

        setTxt('modalEmpAvatar', data.initials || 'VF');
        setTxt('modalEmpCode', data.emp_code || '');
        setTxt('modalEmpRole', data.role_tag || 'STAFF');
        setTxt('modalEmpName', data.full_name || 'Employee');
        setTxt('modalEmpDesignation', data.designation || 'Admissions Officer');
        setTxt('modalEmpEmail', data.email || 'N/A');
        setTxt('modalEmpPersonalMail', data.personal_mail || 'N/A');
        setTxt('modalEmpPhone', data.phone ? ('+91 ' + data.phone) : 'N/A');
        setTxt('modalEmpPhoneSec', data.phone_secondary || '0863-2344700');
        setTxt('modalEmpDepartment', data.department || 'Admissions Directorate');
        setTxt('modalEmpCampus', data.campus || 'VFSTR Vadlamudi Main Campus');
        setTxt('modalEmpType', data.emp_type || 'PERMANENT');
        setTxt('modalEmpJoinDate', data.join_date || 'N/A');
        setTxt('modalEmpAuditCode', data.emp_code || '');
        setTxt('modalEmpAuditIp', data.ip_address || '192.168.1.10');
        setTxt('modalEmpAuditTime', data.audit_time || 'N/A');

        // Style the role pill dynamically
        const rolePill = document.getElementById('modalEmpRole');
        if (rolePill) {
            rolePill.className = 'badge-role-pill';
            if (data.role_tag === 'CRM DEAN') {
                rolePill.classList.add('badge-role-dean');
            } else if (data.role_tag === 'CRM INCHARGE') {
                rolePill.classList.add('badge-role-incharge');
            } else {
                rolePill.classList.add('badge-role-staff');
            }
        }

        modal.style.display = 'flex';
    };

    window.closeEmployeeProfile = function (event) {
        const modal = document.getElementById('employeeProfileModalBackdrop');
        if (event && event.target !== modal) return;
        if (modal) modal.style.display = 'none';
    };

    // =========================================================================
    // ALLOTMENT MODAL & ACTIONS
    // =========================================================================
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    window.openAssignBox = function (appliNo, studentName) {
        const modal = document.getElementById('assignModalBackdrop');
        const targetAppliInput = document.getElementById('modalTargetAppliNoInput');
        const targetAppliText = document.getElementById('modalTargetAppliNo');
        const targetNameText = document.getElementById('modalTargetName');
        const pill = document.getElementById('modalTargetEnquiryPill');

        if (targetAppliInput) targetAppliInput.value = appliNo || '';

        if (appliNo) {
            if (targetAppliText) targetAppliText.textContent = appliNo;
            if (targetNameText) targetNameText.textContent = studentName ? `(${studentName})` : '';
            if (pill) pill.style.display = 'flex';
        } else {
            if (targetAppliText) targetAppliText.textContent = 'All Available Enquiries';
            if (targetNameText) targetNameText.textContent = '(Bulk Allotment)';
            if (pill) pill.style.display = 'flex';
        }

        if (modal) {
            modal.style.display = 'flex';
        }
    };

    window.closeAssignBox = function (event) {
        const modal = document.getElementById('assignModalBackdrop');
        if (event && event.target !== modal) return;
        if (modal) modal.style.display = 'none';
    };

    window.onAllotModeChange = function (mode) {
        const modeKey = (mode || '').toLowerCase();
        const dateGroup = document.getElementById('datewiseFieldGroup');
        const customGroup = document.getElementById('customCountFieldGroup');
        const helpText = document.getElementById('modeHelpText');

        if (modeKey === 'datewise') {
            if (dateGroup) dateGroup.style.display = 'flex';
            if (customGroup) customGroup.style.display = 'none';
            if (helpText) helpText.textContent = 'Allots enquiries received on the chosen date to the telecaller.';
        } else if (modeKey === 'unallocated') {
            if (dateGroup) dateGroup.style.display = 'none';
            if (customGroup) customGroup.style.display = 'none';
            if (helpText) helpText.textContent = 'Allots all pending unallocated student leads to the selected telecaller.';
        } else if (modeKey === 'first 50') {
            if (dateGroup) dateGroup.style.display = 'none';
            if (customGroup) customGroup.style.display = 'none';
            if (helpText) helpText.textContent = 'Allots the first 50 enquiries to the selected telecaller in crm.telecaller_leads.';
        } else if (modeKey === 'first 100') {
            if (dateGroup) dateGroup.style.display = 'none';
            if (customGroup) customGroup.style.display = 'none';
            if (helpText) helpText.textContent = 'Allots the first 100 enquiries to the selected telecaller in crm.telecaller_leads.';
        } else if (modeKey === 'custom') {
            if (dateGroup) dateGroup.style.display = 'none';
            if (customGroup) customGroup.style.display = 'flex';
            if (helpText) helpText.textContent = 'Allots the specified custom quantity of enquiries to the telecaller.';
        }
    };

    window.submitAllotment = function () {
        const empSelect = document.getElementById('assignEmployeeSelect');
        const empOption = empSelect?.options[empSelect.selectedIndex];
        const empCode = (empOption?.getAttribute('data-code') || empSelect?.value || '').trim();
        const empName = (empOption?.getAttribute('data-name') || empOption?.textContent?.trim() || empCode).trim();

        if (!empCode) {
            alert('Please select a telecaller from the dropdown before clicking Allot.');
            empSelect?.focus();
            return;
        }

        const allotModeSelect = document.getElementById('assignAllotModeSelect');
        const allotMode = allotModeSelect?.value || 'unallocated';
        const targetAppli = (document.getElementById('modalTargetAppliNoInput')?.value || '').trim();
        const dateVal = document.getElementById('allotDatewiseInput')?.value || '';
        const customCount = parseInt(document.getElementById('allotCustomCountInput')?.value || '25', 10);
        const stageSelect = document.getElementById('assignStageSelect');
        const stageName = stageSelect?.value || 'Stage 1: Calling & Verification';
        const remarksInput = document.getElementById('assignRemarksInput');
        const stageRemark = (remarksInput?.value || '').trim();

        const btnSubmit = document.getElementById('btnSubmitAllotment');
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = 'Allotting...';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('/crm/allot-leads', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                employee_code: empCode,
                employee_name: empName,
                allot_mode: targetAppli ? 'single' : allotMode,
                application_no: targetAppli,
                allot_date: dateVal,
                allot_count: customCount,
                stage_name: stageName,
                stage_remark: stageRemark
            })
        })
        .then(res => res.json())
        .then(data => {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Allot`;
            }

            if (!data.success) {
                alert(data.message || 'Error allotting leads.');
                return;
            }

            // Close modal
            closeAssignBox();

            // Update matched row cells
            const apps = data.applications || (targetAppli ? [targetAppli] : []);
            const resolvedEmp = data.employee_name || empName;
            const modeDisplay = (data.mode || allotMode).toUpperCase();

            apps.forEach(appli => {
                const cell = document.querySelector(`.assign-cell-content[data-appli="${appli}"]`);
                if (cell) {
                    cell.innerHTML = `
                        <div class="assigned-emp-pill">
                            <div class="assigned-emp-avatar">👤</div>
                            <div class="assigned-emp-details">
                                <span class="assigned-emp-name">${escapeHtml(resolvedEmp)}</span>
                                <span class="assigned-emp-meta">Telecaller • ${escapeHtml(modeDisplay)}</span>
                            </div>
                            <button type="button" 
                                    class="btn-change-emp" 
                                    onclick="openAssignBox('${escapeHtml(appli)}', '')" 
                                    title="Change allotment">
                                Change
                            </button>
                        </div>
                    `;
                }
            });

            // Update top metric for unallocated
            const topMetric = document.getElementById('topMetricUnallocated');
            if (topMetric && data.count) {
                const current = parseInt(topMetric.textContent.replace(/,/g, '') || '0', 10);
                topMetric.textContent = Math.max(0, current - data.count);
            }

            // Show toast notification
            showToastNotification(`✓ ${data.message}`);
        })
        .catch(err => {
            if (btnSubmit) {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> Allot`;
            }
            console.error('Allotment error:', err);
            alert('Failed to connect to server. Please try again.');
        });
    };

    function showToastNotification(msg) {
        const old = document.getElementById('crmToastAlert');
        if (old) old.remove();

        const toast = document.createElement('div');
        toast.id = 'crmToastAlert';
        toast.style.cssText = 'position: fixed; top: 20px; right: 24px; z-index: 9999; background: #065f46; color: #ffffff; padding: 0.85rem 1.4rem; border-radius: 10px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 0.65rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.25); border: 1.5px solid #10b981; animation: fadeInModal 0.2s ease-out;';
        toast.innerHTML = `<span>✓</span> <span>${escapeHtml(msg)}</span>`;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.4s ease';
            setTimeout(() => toast.remove(), 400);
        }, 4000);
    }

    // Restore any existing allotments from localStorage
    try {
        const saved = JSON.parse(localStorage.getItem('crm_allotted_enquiries') || '{}');
        document.querySelectorAll('.assign-cell-content').forEach(cell => {
            const appli = cell.getAttribute('data-appli');
            if (appli && saved[appli]) {
                const item = saved[appli];
                const row = cell.closest('tr');
                const candidateName = row ? row.querySelector('td:nth-child(2) div:first-child')?.textContent?.trim() || 'Candidate' : 'Candidate';
                cell.innerHTML = `
                    <div class="assigned-emp-pill">
                        <div class="assigned-emp-avatar">👤</div>
                        <div class="assigned-emp-details">
                            <span class="assigned-emp-name">${escapeHtml(item.employee)}</span>
                            <span class="assigned-emp-meta">Allotted • ${escapeHtml(item.mode || 'Assigned')}</span>
                        </div>
                        <button type="button" 
                                class="btn-change-emp" 
                                onclick="openAssignBox('${escapeHtml(appli)}', '${escapeHtml(candidateName)}')" 
                                title="Change allotment">
                            Change
                        </button>
                    </div>
                `;
            }
        });
    } catch (e) {}
});
</script>
</body>
</html>
