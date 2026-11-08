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

            <!-- ROLE-SPECIFIC METRIC CARDS (DASHBOARD ONLY) -->
            @if(($activeTab ?? 'overview') === 'overview' || empty($activeTab))
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
                    <div class="metric-card">
                        <div class="metric-info">
                            <span class="metric-label">Unallocated Leads</span>
                            <span class="metric-value">{{ max($totalApplications, 12) }}</span>
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
                    <!-- 3 LIVE FILTERS: PHONE NUMBER, STATUS, DATE -->
                    <div class="enquiry-filter-toolbar">
                        <div class="filter-inputs-row">
                            <!-- 1. Phone Number Filter -->
                            <div class="filter-control-group">
                                <label for="liveFilterPhone" class="filter-label">
                                    <span class="filter-icon">📞</span> Phone Number
                                </label>
                                <div class="input-with-icon">
                                    <input type="text" 
                                           id="liveFilterPhone" 
                                           class="filter-input" 
                                           placeholder="Filter by phone (e.g. 7780...)" 
                                           value="{{ $filterPhone ?? '' }}" 
                                           autocomplete="off">
                                    <button type="button" class="btn-clear-input" onclick="clearFilterInput('liveFilterPhone')" title="Clear phone">&times;</button>
                                </div>
                            </div>

                            <!-- 2. Status Filter -->
                            <div class="filter-control-group">
                                <label for="liveFilterStatus" class="filter-label">
                                    <span class="filter-icon">🏷️</span> Status
                                </label>
                                <select id="liveFilterStatus" class="filter-select">
                                    <option value="">All Statuses</option>
                                    @foreach($availableStatuses as $statusOption)
                                        <option value="{{ $statusOption }}" {{ strtolower($filterStatus ?? '') === strtolower($statusOption) ? 'selected' : '' }}>
                                            {{ $statusOption }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- 3. Date Filter -->
                            <div class="filter-control-group">
                                <label for="liveFilterDate" class="filter-label">
                                    <span class="filter-icon">📅</span> Enquiry Date
                                </label>
                                <div class="input-with-icon">
                                    <input type="date" 
                                           id="liveFilterDate" 
                                           class="filter-input" 
                                           value="{{ $filterDate ?? '' }}">
                                    <button type="button" class="btn-clear-input" onclick="clearFilterInput('liveFilterDate')" title="Clear date">&times;</button>
                                </div>
                            </div>

                            <!-- Actions: Reset Filter & Bulk Allot -->
                            <div class="filter-actions-group">
                                <button type="button" id="btnResetFilters" class="btn-filter-reset" title="Reset all 3 filters">
                                    <span style="font-size: 0.9rem;">↺</span> Reset Filters
                                </button>
                                <button type="button" class="btn-filter-allot" onclick="openAssignBox('', 'All Enquiries (Bulk)')" title="Allot Enquiries to Staff">
                                    <span>📤</span> Allot Enquiries
                                </button>
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
                                    <th>Status</th>
                                    <th>Enquiry Date</th>
                                    <th>Assign</th>
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
                                        $assignedInfo = $leadAssignments[$appliKey] ?? ($leadAssignments['_last_bulk'] ?? null);
                                        
                                        $badgeClass = match(strtolower($rawStatus)) {
                                            'applied' => 'badge-applied',
                                            'verified' => 'badge-verified',
                                            'paid', 'fee paid', 'registered' => 'badge-paid',
                                            'pending', 'not_paid' => 'badge-pending',
                                            default => 'badge-applied',
                                        };
                                    @endphp
                                    <tr class="enquiry-row" 
                                        data-phone="{{ $phone }}" 
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
                                            @php
                                                $branchLabel = $std->branch?->branch_short_name ?? $std->branch?->branch_long_name;
                                            @endphp
                                            @if(!empty($branchLabel))
                                                <div style="font-size: 0.72rem; color: #64748b;">{{ strtoupper($branchLabel) }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge-status {{ $badgeClass }}">{{ $rawStatus }}</span>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #0f172a; font-size: 0.8rem;">{{ $dateFormatted }}</div>
                                            @if($timeFormatted)
                                                <div style="font-size: 0.72rem; color: #64748b;">{{ $timeFormatted }}</div>
                                            @endif
                                        </td>
                                        <td class="assign-action-cell">
                                            <div class="assign-cell-content" data-appli="{{ $appliKey }}">
                                                @if($assignedInfo)
                                                    <div class="assigned-emp-pill">
                                                        <div class="assigned-emp-avatar">👤</div>
                                                        <div class="assigned-emp-details">
                                                            <span class="assigned-emp-name">{{ $assignedInfo['employee'] }}</span>
                                                            <span class="assigned-emp-meta">Allotted • {{ $assignedInfo['mode'] ?? 'Assigned' }}</span>
                                                        </div>
                                                        <button type="button" 
                                                                class="btn-change-emp" 
                                                                onclick="openAssignBox('{{ $appliKey }}', '{{ addslashes($candidateName) }}')" 
                                                                title="Change allotment">
                                                            Change
                                                        </button>
                                                    </div>
                                                @else
                                                    <button type="button" 
                                                            class="btn-assign" 
                                                            onclick="openAssignBox('{{ $appliKey }}', '{{ addslashes($candidateName) }}')"
                                                            title="Allot to Counselor or Telecaller">
                                                        <span>👤</span> Assign
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="emptyTableInitialRow">
                                        <td colspan="8" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                            No enquiry records found in the database.
                                        </td>
                                    </tr>
                                @endforelse

                                <!-- LIVE FILTER NO RESULTS PLACEHOLDER -->
                                <tr id="noFilterMatchesRow" style="display: none;">
                                    <td colspan="8" style="text-align: center; padding: 2.5rem 1rem; background: #fafafa;">
                                        <div style="font-size: 1.5rem; margin-bottom: 0.5rem;">🔍</div>
                                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">No enquiries match the selected filters</div>
                                        <div style="font-size: 0.82rem; color: #64748b; margin-bottom: 1rem;" id="noMatchesDetailText">Try adjusting or clearing your Phone, Status, or Date filter.</div>
                                        <button type="button" onclick="resetFilters()" class="btn-action-sm btn-verify" style="margin: 0 auto; display: inline-flex;">
                                            ↺ Reset All Filters
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
                            <span class="legend-item"><span class="legend-dot dot-pending"></span> In Progress / Pending</span>
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
                                        $isPaid = strtolower($std->std_status ?? '') === 'paid' || in_array(strtolower($std->std_status ?? ''), ['applied', 'verified', 'registered']);
                                        $isEnrolled = !empty($std->register_no) || in_array(strtolower($std->std_status ?? ''), ['applied', 'verified', 'registered']);
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
                                                <span class="tracking-pill pill-done">✓ Done</span>
                                                <div class="step-sub-text">Registration</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill pill-done">✓ Done</span>
                                                <div class="step-sub-text">Academic</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill pill-done">✓ Done</span>
                                                <div class="step-sub-text">KYC Verified</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill {{ $isPaid ? 'pill-done' : 'pill-pending' }}">{{ $isPaid ? '✓ Done' : '⏳ Pending' }}</span>
                                                <div class="step-sub-text">{{ $isPaid ? 'Fee Paid' : 'Fee Due' }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill pill-done">✓ Done</span>
                                                <div class="step-sub-text">{{ $std->program?->program_short_name ?? 'B.Tech' }}</div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="text-align: center;">
                                                <span class="tracking-pill {{ $isEnrolled ? 'pill-done' : 'pill-pending' }}">{{ $isEnrolled ? '✓ Done' : '⏳ Pending' }}</span>
                                                <div class="step-sub-text">Enrollment</div>
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
                            <div class="card-subtitle">Manage Default Early Bird Tiers (up to 800 members) and Generate Personal Coupons from Last 8 Digits</div>
                        </div>
                        <button type="button" class="btn-action-sm btn-verify" onclick="openPersonalCouponModal()" style="height: 40px; padding: 0 1.25rem; font-size: 0.85rem; font-weight: 700; background: #6366f1; color: #ffffff; border: none; border-radius: 8px; cursor: pointer;">
                            + 🎟️ Generate Personal Coupon (Last 8 Digits)
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
                            <span class="legend-item" style="color: #059669; font-weight: 700;">Default Coupon: 27EARLYB100 (-₹ 5,000)</span>
                            <span class="legend-item" style="color: #6366f1; font-weight: 700;">Personal: Last 8 Digits (-₹ 5,000)</span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table" id="couponInfoTable">
                            <thead>
                                <tr>
                                    <th>Application Number</th>
                                    <th>Payment Status</th>
                                    <th>Default Coupon</th>
                                    <th>Personal Coupon (Last 8 Digits)</th>
                                    <th>Fee Payable &amp; Paid</th>
                                    <th>Date Time</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allStudents as $idx => $std)
                                    @php
                                        $appliNo = $std->application_no ?? $std->applinano ?? ('20' . str_pad($idx + 10001, 8, '0', STR_PAD_LEFT));
                                        $last8 = strlen($appliNo) >= 8 ? substr($appliNo, -8) : $appliNo;
                                        $candidateName = $std->student_name ?? $std->name ?? 'Candidate';
                                        $phone = $std->mobile ?? $std->phoneno ?? '';
                                        $email = $std->personal_email ?? $std->emailid ?? ($phone ? $phone . '@student.vignan.ac.in' : 'student@vignan.ac.in');
                                        $programName = $std->program?->program_short_name ?? $std->program?->program_name ?? 'B.Tech';
                                        $regulation = $std->regulation ?? 'R25';
                                        $timestamp = $std->log_timestamp;
                                        $dateTimeFormatted = $timestamp ? \Carbon\Carbon::parse($timestamp)->format('d M Y, h:i A') : now()->format('d M Y, h:i A');

                                        // Lookup real payment details or calculate
                                        $payRecord = $payDetails->get($appliNo);
                                        $persCouponObj = $personalCoupons->get($appliNo) ?? $personalCoupons->firstWhere('couponsno', $last8);

                                        $defaultCoupon = $payRecord?->defcoupon ?? '27EARLYB100';
                                        $defaultDiscount = (float) ($payRecord?->defcouponamount ?? 5000.00);

                                        $personalCoupon = $payRecord?->percoupon ?? ($persCouponObj ? $persCouponObj->couponsno : null);
                                        $personalDiscount = (float) ($payRecord?->percouponamount ?? ($persCouponObj ? $persCouponObj->discountamount : 0.00));

                                        $baseFee = (float) ($payRecord?->feepayable ?? 25000.00);
                                        $totalDiscount = $defaultDiscount + $personalDiscount;
                                        $paidAmount = $payRecord?->paidamount ?? max(0, $baseFee - $totalDiscount);

                                        $isPaid = ($std->stage1 === 'paid' || ($payRecord && $payRecord->txnstatus === 'success'));
                                        $receiptNo = 'REC-2026-' . str_pad($idx + 101, 5, '0', STR_PAD_LEFT);
                                        $transactionId = $payRecord?->txncrmid ?? ('TXN-VU-' . strtoupper(substr(sha1($appliNo), 0, 10)));
                                        $bankRef = $payRecord?->txnpayuid ?? ('PAY_' . strtoupper(substr(md5($appliNo . 'vignan'), 0, 10)));

                                        $paymentPayload = [
                                            'appli_no' => $appliNo,
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
                                            <strong style="color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.88rem;">{{ $appliNo }}</strong>
                                            <div style="font-size: 0.74rem; color: #64748b; font-weight: 500;">{{ $candidateName }}</div>
                                            <div style="font-size: 0.70rem; color: #94a3b8;">Last 8: <code>{{ $last8 }}</code></div>
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
                                                        onclick="openPersonalCouponModal('{{ $appliNo }}', '{{ $last8 }}')"
                                                        style="background: #f1f5f9; color: #475569; border: 1px dashed #cbd5e1; padding: 3px 8px; border-radius: 6px; font-size: 0.72rem; font-weight: 600; cursor: pointer;">
                                                    + Assign ({{ $last8 }})
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
                                                        onclick='openPaymentWizard(@json($paymentPayload))' 
                                                        title="View Payment Breakdown &amp; Receipt Wizard">
                                                    <span>💳</span> Receipt
                                                </button>
                                                @if(!$personalCoupon)
                                                    <button type="button" 
                                                            class="btn-action-sm" 
                                                            onclick="openPersonalCouponModal('{{ $appliNo }}', '{{ $last8 }}')"
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
                                        Candidate Application Number *
                                    </label>
                                    <input type="text" 
                                           id="modalAppliNo" 
                                           name="applino" 
                                           class="form-input" 
                                           placeholder="e.g. 20261106333" 
                                           oninput="updateModalLast8(this.value)"
                                           required 
                                           style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-family: 'JetBrains Mono', monospace; font-size: 0.95rem;">
                                </div>

                                <div style="margin-bottom: 1.25rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px;">
                                    <div style="font-size: 0.78rem; color: #1e40af; font-weight: 600;">Personal Coupon Code (Auto-Derived Last 8 Digits):</div>
                                    <div id="modalCouponPreview" style="font-size: 1.15rem; font-weight: 800; color: #2563eb; font-family: 'JetBrains Mono', monospace; margin-top: 4px;">
                                        61106333
                                    </div>
                                    <div style="font-size: 0.70rem; color: #60a5fa; margin-top: 2px;">
                                        This coupon code will be tied to this candidate in <code>finance.couponinfo</code>.
                                    </div>
                                </div>

                                <div style="margin-bottom: 1.25rem;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #334155; margin-bottom: 0.4rem; text-transform: uppercase;">
                                        Concession / Discount Amount (₹) *
                                    </label>
                                    <input type="number" 
                                           name="discount_amount" 
                                           class="form-input" 
                                           placeholder="e.g. 5000" 
                                           value="5000" 
                                           min="100" 
                                           max="25000" 
                                           step="100" 
                                           required 
                                           style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 0.95rem; font-weight: 700; color: #059669;">
                                </div>

                                <div style="font-size: 0.78rem; color: #64748b; background: #f8fafc; padding: 10px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                                    ℹ️ When candidate pays at Stage 1, this personal discount (-₹5,000) will combine with the active early bird tier (<code>27EARLYB100</code>: -₹5,000) for a total discount of <strong>₹10,000</strong>!
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
                    function openPersonalCouponModal(appliNo = '', last8 = '') {
                        const modal = document.getElementById('personalCouponModal');
                        if (!modal) return;
                        modal.style.display = 'flex';
                        const input = document.getElementById('modalAppliNo');
                        if (input) {
                            input.value = appliNo || '20261106333';
                            updateModalLast8(input.value);
                        }
                    }

                    function closePersonalCouponModal() {
                        const modal = document.getElementById('personalCouponModal');
                        if (modal) modal.style.display = 'none';
                    }

                    function updateModalLast8(val) {
                        val = val.trim();
                        const last8 = val.length >= 8 ? val.slice(-8) : val;
                        const preview = document.getElementById('modalCouponPreview');
                        if (preview) {
                            preview.textContent = last8 || '--------';
                        }
                    }
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
                                        $phoneSecondary = $emp->phone_secondary ?: '0863-2344700';
                                        $empType = $emp->emp_type ?: 'PERMANENT';
                                        $campusName = 'VFSTR Vadlamudi Main Campus';
                                        
                                        $designation = match($empCode) {
                                            '03141' => 'Dean of Admissions',
                                            '03434' => 'Admissions Incharge & Operations Head',
                                            '02598' => 'Counselor & Staff Coordinator',
                                            default => 'Admissions Officer',
                                        };

                                        $department = match($empCode) {
                                            '03141' => 'Office of the Dean (Admissions)',
                                            '03434' => 'Management Studies & CRM Cell',
                                            '02598' => 'Training & Outreach Division',
                                            default => 'Admissions Directorate',
                                        };

                                        $roleTag = match($empCode) {
                                            '03141' => 'CRM DEAN',
                                            '03434' => 'CRM INCHARGE',
                                            '02598' => 'COUNSELOR',
                                            default => 'STAFF',
                                        };

                                        $roleTagClass = match($empCode) {
                                            '03141' => 'badge-role-dean',
                                            '03434' => 'badge-role-incharge',
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

            @else
                <!-- DEFAULT TAB: OVERVIEW DASHBOARD -->
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
                
                <!-- 1. Select Employee Dropdown -->
                <div class="form-group-custom">
                    <label for="assignEmployeeSelect" class="form-label-custom">
                        Select Employee <span class="required-asterisk">*</span>
                    </label>
                    <div class="select-wrapper-custom">
                        <select id="assignEmployeeSelect" class="form-control-custom" required>
                            <option value="">-- Choose Employee --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->full_name }}" data-code="{{ $emp->emp_code }}">
                                    {{ $emp->full_name }} ({{ $emp->emp_code }}{{ $emp->emp_type ? ' - ' . $emp->emp_type : '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- 2. Allot Mode Dropdown with the 4 required options -->
                <div class="form-group-custom">
                    <label for="assignAllotModeSelect" class="form-label-custom">
                        Allot Mode <span class="required-asterisk">*</span>
                    </label>
                    <div class="select-wrapper-custom">
                        <select id="assignAllotModeSelect" class="form-control-custom" onchange="onAllotModeChange(this.value)" required>
                            <option value="datewise">Date-wise (Target Date)</option>
                            <option value="first 50">First 50 Leads</option>
                            <option value="first 100">First 100 Leads</option>
                            <option value="custom">Custom Batch Count</option>
                        </select>
                    </div>
                    <!-- Dedicated Info Callout Box -->
                    <div class="mode-help-banner" id="modeHelpBanner">
                        <span class="banner-info-icon">ℹ️</span>
                        <span id="modeHelpText">Allots enquiries based on the selected date.</span>
                    </div>
                </div>

                <!-- Dynamic Extra Field: Datewise Input -->
                <div id="datewiseFieldGroup" class="form-group-custom" style="display: flex;">
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
<div id="paymentWizardBackdrop" class="modal-backdrop-custom" style="display: none;" onclick="closePaymentWizard(event)">
    <div class="payment-wizard-card" onclick="event.stopPropagation()">
        <!-- Wizard Header -->
        <div class="wizard-card-header">
            <div class="wizard-header-brand">
                <div class="wizard-brand-emblem">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <circle cx="12" cy="11" r="3"/>
                        <line x1="12" y1="8" x2="12" y2="14"/>
                        <line x1="9" y1="11" x2="15" y2="11"/>
                    </svg>
                </div>
                <div>
                    <div class="wizard-uni-name">VIGNAN'S FOUNDATION FOR SCIENCE, TECHNOLOGY &amp; RESEARCH</div>
                    <h3 class="wizard-title">Admission Fee Payment &amp; Coupon Receipt Wizard</h3>
                </div>
            </div>
            <button type="button" class="btn-modal-close no-print" onclick="closePaymentWizard()" title="Close dialog">&times;</button>
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
        <div class="wizard-card-body" id="printablePaymentArea">
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
                    <strong class="cell-val" id="wizStudentName">Hemanth Kumar Vucha</strong>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Application Number:</span>
                    <strong class="cell-val cell-code" id="wizAppliNo">20########</strong>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Mobile Number:</span>
                    <span class="cell-val" id="wizPhone">+91 7780579012</span>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Email ID:</span>
                    <span class="cell-val" id="wizEmail">7780579012@student.vignan.ac.in</span>
                </div>
                <div class="info-cell">
                    <span class="cell-label">Enrolled Program:</span>
                    <strong class="cell-val" id="wizProgram">B.Tech (CSE)</strong>
                </div>
                <div class="info-cell">
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
                        <td style="text-align: right; font-weight: 700;">₹ <span id="wizBaseFee">2,000.00</span></td>
                    </tr>
                    <tr class="discount-row">
                        <td>
                            <strong>Default Institutional Merit Waiver</strong>
                            <div class="breakdown-sub">VFSTR 2026 Open Campus Early Incentive</div>
                        </td>
                        <td><span class="coupon-tag-badge" id="wizDefaultCoupon">VIGNAN2026</span></td>
                        <td style="text-align: right; color: #059669; font-weight: 700;">- ₹ <span id="wizDefaultDiscount">500.00</span></td>
                    </tr>
                    <tr class="discount-row">
                        <td>
                            <strong>Personal / Referral Candidate Coupon</strong>
                            <div class="breakdown-sub">Special admission council referral redemption</div>
                        </td>
                        <td><span class="coupon-tag-badge badge-personal-tag" id="wizPersonalCoupon">PERS-A84F2B</span></td>
                        <td style="text-align: right; color: #059669; font-weight: 700;">- ₹ <span id="wizPersonalDiscount">300.00</span></td>
                    </tr>
                    <tr class="subtotal-row">
                        <td colspan="2">
                            <strong>Total Coupon Discount Applied</strong>
                        </td>
                        <td style="text-align: right; color: #059669; font-weight: 800;">- ₹ <span id="wizTotalDiscount">800.00</span></td>
                    </tr>
                    <tr class="net-total-row">
                        <td colspan="2">
                            <div class="net-title">NET AMOUNT PAID</div>
                            <div class="net-subtitle">Confirmed by Payment Gateway • No Additional Dues</div>
                        </td>
                        <td style="text-align: right;">
                            <div class="net-amount">₹ <span id="wizPaidAmount">1,200.00</span></div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Banking & Gateway Details -->
            <div class="wizard-gateway-strip">
                <div class="gateway-item">
                    <span class="gw-label">Payment Gateway:</span>
                    <strong class="gw-val" id="wizPaymentMode">Online Gateway (Razorpay / SBI e-Pay)</strong>
                </div>
                <div class="gateway-item">
                    <span class="gw-label">Bank Reference / UTR:</span>
                    <code class="gw-val" id="wizBankRef">PAY_OK_99418294</code>
                </div>
                <div class="gateway-item">
                    <span class="gw-label">Reconciliation:</span>
                    <span class="gw-val" style="color: #059669; font-weight: 700;">✓ Auto-Reconciled</span>
                </div>
            </div>

            <!-- Authentic Seal Footer -->
            <div class="wizard-seal-footer">
                <div class="seal-text-group">
                    <div class="seal-icon">🏛️</div>
                    <div>
                        <div class="seal-heading">Admissions Accounts &amp; Financial Division</div>
                        <div class="seal-sub">VFSTR Deemed to be University, Vadlamudi, Guntur, AP - 522213</div>
                        <div class="seal-note">This document is an electronically verified admission payment receipt. Valid without physical signature.</div>
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
        <div class="wizard-actions-footer no-print">
            <button type="button" class="btn-wizard-print" onclick="printPaymentWizard()" title="Print Payment Receipt">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                <span>Print</span>
            </button>
            <button type="button" class="btn-wizard-close" onclick="closePaymentWizard()" title="Close Wizard">
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
    const phoneInput = document.getElementById('liveFilterPhone');
    const statusInput = document.getElementById('liveFilterStatus');
    const dateInput = document.getElementById('liveFilterDate');
    const resetBtn = document.getElementById('btnResetFilters');
    const countBadge = document.getElementById('filterCountBadge');
    const noMatchRow = document.getElementById('noFilterMatchesRow');
    const noMatchesDetail = document.getElementById('noMatchesDetailText');
    const rows = Array.from(document.querySelectorAll('.enquiry-row'));

    function applyLiveFilters() {
        const phoneVal = (phoneInput?.value || '').trim().toLowerCase();
        const statusVal = (statusInput?.value || '').trim().toLowerCase();
        const dateVal = (dateInput?.value || '').trim();

        let visibleCount = 0;

        rows.forEach(function (row) {
            const rowPhone = (row.getAttribute('data-phone') || '').toLowerCase();
            const rowStatus = (row.getAttribute('data-status') || '').toLowerCase();
            const rowDate = row.getAttribute('data-date') || '';

            // 1. Phone match: empty or phone contains substring
            const matchPhone = !phoneVal || rowPhone.includes(phoneVal);

            // 2. Status match: empty or matches status
            const matchStatus = !statusVal || rowStatus === statusVal;

            // 3. Date match: empty or matches YYYY-MM-DD
            const matchDate = !dateVal || rowDate === dateVal;

            if (matchPhone && matchStatus && matchDate) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update counter badge
        if (countBadge) {
            if (phoneVal || statusVal || dateVal) {
                countBadge.innerHTML = `<strong>${visibleCount}</strong> of ${rows.length} enquiries matched`;
                countBadge.style.background = '#dbeafe';
                countBadge.style.color = '#1e40af';
            } else {
                countBadge.textContent = `Showing all ${rows.length} enquiries`;
                countBadge.style.background = '#f1f5f9';
                countBadge.style.color = '#475569';
            }
        }

        // Show/hide no matching rows message
        if (noMatchRow) {
            if (visibleCount === 0 && rows.length > 0) {
                noMatchRow.style.display = '';
                if (noMatchesDetail) {
                    const activeFilters = [];
                    if (phoneVal) activeFilters.push(`Phone: "${phoneVal}"`);
                    if (statusVal) activeFilters.push(`Status: "${statusVal}"`);
                    if (dateVal) activeFilters.push(`Date: "${dateVal}"`);
                    noMatchesDetail.textContent = `No records match filters: ${activeFilters.join(', ')}`;
                }
            } else {
                noMatchRow.style.display = 'none';
            }
        }
    }

    // Live event listeners
    if (phoneInput) {
        phoneInput.addEventListener('input', applyLiveFilters);
        phoneInput.addEventListener('keyup', applyLiveFilters);
    }
    if (statusInput) {
        statusInput.addEventListener('change', applyLiveFilters);
    }
    if (dateInput) {
        dateInput.addEventListener('input', applyLiveFilters);
        dateInput.addEventListener('change', applyLiveFilters);
    }

    // Reset handlers
    window.resetFilters = function () {
        if (phoneInput) phoneInput.value = '';
        if (statusInput) statusInput.value = '';
        if (dateInput) dateInput.value = '';
        applyLiveFilters();
        if (phoneInput) phoneInput.focus();
    };

    window.clearFilterInput = function (inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.value = '';
            applyLiveFilters();
            input.focus();
        }
    };

    if (resetBtn) {
        resetBtn.addEventListener('click', window.resetFilters);
    }

    // Trigger on page load in case pre-filled values exist
    if (rows.length > 0) {
        applyLiveFilters();
    }

    // Live search for Register List (Student Tracking)
    const registerSearchInput = document.getElementById('registerListSearchInput');
    const registerRows = Array.from(document.querySelectorAll('.register-track-row'));
    if (registerSearchInput && registerRows.length > 0) {
        registerSearchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            registerRows.forEach(row => {
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();
                row.style.display = !query || searchData.includes(query) ? '' : 'none';
            });
        });
    }

    // Live search for Coupon Info (Payment & Coupon Tracking)
    const couponSearchInput = document.getElementById('couponListSearchInput');
    const couponRows = Array.from(document.querySelectorAll('.coupon-track-row'));
    if (couponSearchInput && couponRows.length > 0) {
        couponSearchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            couponRows.forEach(row => {
                const searchData = (row.getAttribute('data-search') || '').toLowerCase();
                row.style.display = !query || searchData.includes(query) ? '' : 'none';
            });
        });
    }

    // =========================================================================
    // PAYMENT & COUPON RECEIPT WIZARD MODAL
    // =========================================================================
    window.openPaymentWizard = function (data) {
        if (!data) return;
        const modal = document.getElementById('paymentWizardBackdrop');
        if (!modal) return;

        const setTxt = (id, val) => {
            const el = document.getElementById(id);
            if (el) el.textContent = val || '';
        };

        setTxt('wizReceiptNo', data.receipt_no || 'REC-2026-00101');
        setTxt('wizTxnId', data.transaction_id || 'TXN-VU-00000000');
        setTxt('wizDateTime', data.payment_date || '');
        setTxt('wizStudentName', data.student_name || 'Candidate');
        setTxt('wizAppliNo', data.appli_no || '');
        setTxt('wizPhone', data.phone ? ('+91 ' + data.phone) : '+91 7780579012');
        setTxt('wizEmail', data.email || 'N/A');
        setTxt('wizProgram', (data.program || 'B.Tech') + ' (Engineering)');
        setTxt('wizRegulation', 'Academic Batch 2026-2027');
        setTxt('wizBaseFee', data.base_fee || '2,000.00');
        setTxt('wizDefaultCoupon', data.default_coupon || 'VIGNAN2026');
        setTxt('wizDefaultDiscount', data.default_discount || '500.00');
        setTxt('wizPersonalCoupon', data.personal_coupon || 'PERS-COUPON');
        setTxt('wizPersonalDiscount', data.personal_discount || '300.00');
        setTxt('wizTotalDiscount', data.total_discount || '800.00');
        setTxt('wizPaidAmount', data.paid_amount || '1,200.00');
        setTxt('wizPaymentMode', data.payment_mode || 'Online Gateway (Razorpay / SBI e-Pay)');
        setTxt('wizBankRef', data.bank_ref || 'PAY_OK_99418294');

        modal.style.display = 'flex';
    };

    window.closePaymentWizard = function (event) {
        const modal = document.getElementById('paymentWizardBackdrop');
        if (event && event.target !== modal) return;
        if (modal) modal.style.display = 'none';
    };

    window.printPaymentWizard = function () {
        window.print();
    };

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
            if (helpText) helpText.textContent = 'Allots enquiries received on the chosen date to the employee.';
        } else if (modeKey === 'first 50') {
            if (dateGroup) dateGroup.style.display = 'none';
            if (customGroup) customGroup.style.display = 'none';
            if (helpText) helpText.textContent = 'Allots the first 50 enquiries to the selected employee.';
        } else if (modeKey === 'first 100') {
            if (dateGroup) dateGroup.style.display = 'none';
            if (customGroup) customGroup.style.display = 'none';
            if (helpText) helpText.textContent = 'Allots the first 100 enquiries to the selected employee.';
        } else if (modeKey === 'custom') {
            if (dateGroup) dateGroup.style.display = 'none';
            if (customGroup) customGroup.style.display = 'flex';
            if (helpText) helpText.textContent = 'Allots the specified custom quantity of enquiries to the employee.';
        }
    };

    window.submitAllotment = function () {
        const empSelect = document.getElementById('assignEmployeeSelect');
        const empName = (empSelect?.value || '').trim();

        if (!empName) {
            alert('Please select an employee before clicking Allot.');
            empSelect?.focus();
            return;
        }

        const allotModeSelect = document.getElementById('assignAllotModeSelect');
        const allotMode = allotModeSelect?.value || 'datewise';
        const targetAppli = (document.getElementById('modalTargetAppliNoInput')?.value || '').trim();
        const dateVal = document.getElementById('allotDatewiseInput')?.value || '';
        const customCount = parseInt(document.getElementById('allotCustomCountInput')?.value || '25', 10);

        const allRows = Array.from(document.querySelectorAll('.enquiry-row'));
        let rowsToAllot = [];

        if (allotMode === 'datewise') {
            rowsToAllot = allRows.filter(r => (r.getAttribute('data-date') || '') === dateVal);
            if (rowsToAllot.length === 0 && targetAppli) {
                rowsToAllot = allRows.filter(r => {
                    const cell = r.querySelector('.assign-cell-content');
                    return cell && cell.getAttribute('data-appli') === targetAppli;
                });
            }
        } else if (allotMode === 'first 50') {
            rowsToAllot = allRows.slice(0, 50);
        } else if (allotMode === 'first 100') {
            rowsToAllot = allRows.slice(0, 100);
        } else if (allotMode === 'custom') {
            rowsToAllot = allRows.slice(0, Math.max(1, customCount));
        }

        // If target application specified and not included, ensure it is added
        if (targetAppli) {
            const hasTarget = rowsToAllot.some(r => {
                const cell = r.querySelector('.assign-cell-content');
                return cell && cell.getAttribute('data-appli') === targetAppli;
            });
            if (!hasTarget) {
                const targetRow = allRows.find(r => {
                    const cell = r.querySelector('.assign-cell-content');
                    return cell && cell.getAttribute('data-appli') === targetAppli;
                });
                if (targetRow) rowsToAllot.push(targetRow);
            }
        }

        if (rowsToAllot.length === 0 && allRows.length > 0) {
            rowsToAllot = [allRows[0]];
        }

        const modeDisplay = allotMode.charAt(0).toUpperCase() + allotMode.slice(1);
        let localStore = {};
        try {
            localStore = JSON.parse(localStorage.getItem('crm_allotted_enquiries') || '{}');
        } catch (e) {}

        rowsToAllot.forEach(row => {
            const cell = row.querySelector('.assign-cell-content');
            if (!cell) return;
            const appli = cell.getAttribute('data-appli') || targetAppli;
            const candidateName = row.querySelector('td:nth-child(2) div:first-child')?.textContent?.trim() || 'Candidate';

            // Display employee name very properly
            cell.innerHTML = `
                <div class="assigned-emp-pill">
                    <div class="assigned-emp-avatar">👤</div>
                    <div class="assigned-emp-details">
                        <span class="assigned-emp-name">${escapeHtml(empName)}</span>
                        <span class="assigned-emp-meta">Allotted • ${escapeHtml(modeDisplay)}</span>
                    </div>
                    <button type="button" 
                            class="btn-change-emp" 
                            onclick="openAssignBox('${escapeHtml(appli)}', '${escapeHtml(candidateName)}')" 
                            title="Change allotment">
                        Change
                    </button>
                </div>
            `;

            if (appli) {
                localStore[appli] = {
                    employee: empName,
                    mode: modeDisplay,
                    date: new Date().toLocaleDateString('en-GB')
                };
            }
        });

        try {
            localStorage.setItem('crm_allotted_enquiries', JSON.stringify(localStore));
        } catch (e) {}

        // Sync with session backend
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('/crm/allot-leads', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                employee_name: empName,
                allot_mode: allotMode,
                application_no: targetAppli,
                allot_date: dateVal,
                allot_count: customCount
            })
        }).catch(e => console.log('Session sync notice:', e));

        // Close modal
        closeAssignBox();

        // Show toast
        showToastNotification(`✓ Successfully allotted to ${empName} (${modeDisplay})!`);
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
