<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telecaller KPI &amp; Outreach Analytics - VFSTR Admissions CRM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css?v={{ file_exists(public_path('css/crm-dashboard.css')) ? filemtime(public_path('css/crm-dashboard.css')) : time() }}">

    <style>
        /* Standalone Guarantee for Pagination & SVG chevrons (immune to browser CSS cache) */
        .table-pagination-area, .pagination-wrapper { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }
        .pagination, ul.pagination { display: inline-flex !important; gap: 4px !important; list-style: none !important; margin: 0 !important; padding: 0 !important; align-items: center !important; }
        .pagination .page-item { list-style: none !important; margin: 0 !important; padding: 0 !important; }
        .pagination .page-item .page-link, .pagination .page-item span.page-link, .pagination .page-item a.page-link {
            display: inline-flex !important; align-items: center !important; justify-content: center !important;
            min-width: 32px !important; height: 32px !important; padding: 0 8px !important; font-size: 0.8rem !important;
            font-weight: 600 !important; color: #475569 !important; background: #ffffff !important; border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important; text-decoration: none !important; line-height: 1 !important;
        }
        .pagination .page-item .page-link:hover, .pagination .page-item a.page-link:hover { background: #f1f5f9 !important; border-color: #94a3b8 !important; color: #0f172a !important; }
        .pagination .page-item.active .page-link, .pagination .page-item.active span.page-link { background: #0284c7 !important; color: #ffffff !important; border-color: #0284c7 !important; font-weight: 700 !important; }
        .pagination .page-item.disabled .page-link, .pagination .page-item.disabled span.page-link { color: #94a3b8 !important; background: #f8fafc !important; border-color: #e2e8f0 !important; cursor: not-allowed !important; pointer-events: none !important; }
        .pagination svg, .pagination-wrapper svg, nav svg, svg.w-5, svg.h-5 { width: 14px !important; height: 14px !important; max-width: 14px !important; max-height: 14px !important; display: inline-block !important; vertical-align: middle !important; }
        .pagination-wrapper nav > div:first-child:not(:only-child) { display: none !important; }
    </style>

    <!-- Chart.js for High-Impact Analytics -->
    <script src="/js/chart.min.js"></script>
    <script>if (typeof Chart === 'undefined') { document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"><\/script>'); }</script>
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

            <!-- TAB: TELECALLER KPI & OUTREACH ANALYTICS -->
            <div class="content-card">
                <div class="card-header-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; background: #ffffff;">
                    <div>
                        <div class="card-title" style="font-size: 1.15rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                            <span>🎧 Telecaller KPI &amp; Outreach Analytics</span>
                        </div>
                        <div style="font-size: 0.8rem; color: #64748b; margin-top: 0.35rem;">
                            Counselor outreach milestones, call disposition breakdown, and fee conversion leaderboard
                        </div>
                    </div>

                    <!-- Header Metadata Badges -->
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                        <span style="font-size: 0.825rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 0.35rem 0.85rem; border-radius: 20px; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <span>📅</span> Intake Cycle 2026–27
                        </span>
                        <span style="font-size: 0.825rem; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.35rem 0.85rem; border-radius: 20px; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <span style="display: inline-block; width: 7px; height: 7px; background: #10b981; border-radius: 50%;"></span>
                            {{ $totalAllocatedLeads }} Allotted Leads
                        </span>
                    </div>
                </div>

                <div style="padding: 1.5rem;">
                    <!-- 1. TOP EXECUTIVE METRIC CARDS -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
                        <!-- Card 1: Total Allocated Leads -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Allotted Leads</div>
                                <div style="font-size: 1.65rem; font-weight: 800; color: #1e293b; margin: 0.2rem 0;">{{ $totalAllocatedLeads }}</div>
                                <div style="font-size: 0.75rem; color: #0284c7; font-weight: 600;">Active in Calling Pool</div>
                            </div>
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">📋</div>
                        </div>

                        <!-- Card 2: Completed Outreach Calls -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Completed Calls</div>
                                <div style="font-size: 1.65rem; font-weight: 800; color: #2563eb; margin: 0.2rem 0;">{{ $totalOutreachCalls }}</div>
                                <div style="font-size: 0.75rem; color: #2563eb; font-weight: 600;">Logged Outreach Tasks</div>
                            </div>
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">📞</div>
                        </div>

                        <!-- Card 3: Fee Paid Conversions -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Fee Paid Conversions</div>
                                <div style="font-size: 1.65rem; font-weight: 800; color: #059669; margin: 0.2rem 0;">{{ $totalTelecallerConversions }}</div>
                                <div style="font-size: 0.75rem; color: #059669; font-weight: 600;">{{ $overallConversionRate }}% Conversion Rate</div>
                            </div>
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">💳</div>
                        </div>

                        <!-- Card 4: Active Counselors or My Profile -->
                        @if(!empty($isTelecaller))
                            <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                <div>
                                    <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">My Counselor ID</div>
                                    <div style="font-size: 1.35rem; font-weight: 800; color: #7c3aed; margin: 0.2rem 0; font-family: 'JetBrains Mono', monospace;">{{ $user->usercode ?: 'Active' }}</div>
                                    <div style="font-size: 0.75rem; color: #059669; font-weight: 600;">● Assigned Counselor</div>
                                </div>
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: #f5f3ff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">🎧</div>
                            </div>
                        @else
                            <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                                <div>
                                    <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Active Telecallers</div>
                                    <div style="font-size: 1.65rem; font-weight: 800; color: #7c3aed; margin: 0.2rem 0;">{{ $telecallers->count() }}</div>
                                    <div style="font-size: 0.75rem; color: #7c3aed; font-weight: 600;">Assigned Role ID 4</div>
                                </div>
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: #f5f3ff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">🎧</div>
                            </div>
                        @endif
                    </div>

                    <!-- 2. VISUAL CHARTS (COUNSELOR COMPARISON & CALL DISPOSITION) -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(460px, 1fr)); gap: 1.5rem; margin-bottom: 1.75rem;">
                        <!-- Chart 1: Counselor Performance Comparison (Allocated vs Calls vs Paid) -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.5rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">
                                        {{ !empty($isTelecaller) ? 'My Outreach & Conversion Progress' : 'Counselor Performance Comparison' }}
                                    </div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                                        {{ !empty($isTelecaller) ? 'Personal metrics: Allocated leads vs completed outreach vs fee conversions' : 'Allocated leads vs completed outreach vs fee conversions' }}
                                    </div>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 0.25rem 0.65rem; border-radius: 20px; border: 1px solid #dbeafe;">Counselors</span>
                            </div>
                            <div style="position: relative; flex-grow: 1; min-height: 280px; max-height: 320px;">
                                <canvas id="counselorComparisonChart"></canvas>
                            </div>
                        </div>

                        <!-- Chart 2: Call Disposition Stage Breakdown -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.5rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Call Disposition Stage Breakdown</div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Distribution of outreach call task stages &amp; candidate statuses</div>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; color: #059669; background: #ecfdf5; padding: 0.25rem 0.65rem; border-radius: 20px; border: 1px solid #d1fae5;">Call Status</span>
                            </div>
                            <div style="position: relative; flex-grow: 1; min-height: 280px; max-height: 320px;">
                                <canvas id="dispositionChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 3. EXECUTIVE TELECALLER PERFORMANCE LEADERBOARD / MY PERFORMANCE SUMMARY -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 1.75rem; overflow: hidden;">
                        <div style="padding: 1.15rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                            <div>
                                <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">
                                    {{ !empty($isTelecaller) ? '👤 My Outreach & Conversion Performance' : '🏆 Executive Telecaller Performance Leaderboard' }}
                                </div>
                                <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                                    {{ !empty($isTelecaller) ? 'Personal performance metrics for your allotted leads' : 'Counselors ranked by verified fee-paid conversions and outreach efficiency' }}
                                </div>
                            </div>
                            <span style="font-size: 0.75rem; font-weight: 700; color: #475569; background: #f8fafc; border: 1px solid #e2e8f0; padding: 0.35rem 0.75rem; border-radius: 6px;">
                                Role ID 4 Verified
                            </span>
                        </div>

                        <div class="table-responsive">
                            <table class="crm-table">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Counselor Name &amp; ID</th>
                                        <th>Contact Details</th>
                                        <th>Allocated Leads</th>
                                        <th>Outreach Completed</th>
                                        <th>Pending Follow-ups</th>
                                        <th>Fee Paid Conversions</th>
                                        <th>Conversion Rate</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($telecallerLeaderboard as $idx => $t)
                                        <tr>
                                            <td>
                                                @if($idx === 0)
                                                    <span style="font-size: 1.1rem;">🥇</span>
                                                @elseif($idx === 1)
                                                    <span style="font-size: 1.1rem;">🥈</span>
                                                @elseif($idx === 2)
                                                    <span style="font-size: 1.1rem;">🥉</span>
                                                @else
                                                    <span style="font-size: 0.85rem; font-weight: 700; color: #64748b;">#{{ $idx + 1 }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="font-weight: 700; color: #1e293b; font-size: 0.875rem;">{{ $t->name }}</div>
                                                <div style="font-size: 0.75rem; color: #0284c7; font-family: 'JetBrains Mono', monospace;">ID: {{ $t->emp_code }}</div>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.78rem; color: #475569;">📞 {{ $t->phone }}</div>
                                                <div style="font-size: 0.72rem; color: #94a3b8;">✉️ {{ $t->email }}</div>
                                            </td>
                                            <td>
                                                <span style="font-weight: 700; color: #1e293b; font-size: 0.95rem;">{{ $t->allocated_leads }}</span>
                                            </td>
                                            <td>
                                                <span style="font-weight: 700; color: #2563eb; font-size: 0.95rem;">{{ $t->completed_calls }}</span>
                                            </td>
                                            <td>
                                                <span style="font-weight: 700; color: {{ $t->pending_followups > 0 ? '#ea580c' : '#64748b' }}; font-size: 0.95rem;">
                                                    {{ $t->pending_followups }}
                                                </span>
                                            </td>
                                            <td>
                                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.825rem; font-weight: 800; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 10px; border-radius: 6px;">
                                                    💳 {{ $t->paid_conversions }}
                                                </span>
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <div style="flex: 1; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; min-width: 50px;">
                                                        <div style="width: {{ min(100, $t->conversion_rate) }}%; height: 100%; background: #10b981; border-radius: 3px;"></div>
                                                    </div>
                                                    <span style="font-size: 0.8rem; font-weight: 800; color: #059669;">{{ $t->conversion_rate }}%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 4px; {{ $t->status === 'Active' ? 'background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;' : 'background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0;' }}">
                                                    {{ $t->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                                No active telecallers found in master.roles_assigned for role ID 4.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 4. INTERACTIVE SEARCH & EMPLOYEE FILTER BAR -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 1.5rem; padding: 1.15rem 1.5rem;">
                        <form method="GET" action="{{ route('kpi.telecallerkpi') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                            <input type="hidden" name="tab" value="telecallerkpi">
                            
                            <!-- Search query -->
                            <div style="flex: 1; min-width: 240px; position: relative;">
                                <input type="text" name="q" value="{{ $search }}" placeholder="Search by student name, application #, phone, CRM ID..." style="width: 100%; padding: 0.5rem 0.85rem 0.5rem 2.2rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc;">
                                <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8;" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </div>

                            @if(empty($isTelecaller))
                            <!-- Employee Filter Dropdown (Admins / Incharges only) -->
                            <div style="min-width: 240px;">
                                <select name="employee" style="width: 100%; padding: 0.5rem 0.85rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff;">
                                    <option value="">All Telecallers / Counselors</option>
                                    @foreach($telecallers as $emp)
                                        <option value="{{ $emp->emp_code }}" {{ (string)$filterEmp === (string)$emp->emp_code ? 'selected' : '' }}>
                                            {{ $emp->full_name }} ({{ $emp->emp_code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            @endif

                            <button type="submit" style="padding: 0.5rem 1.25rem; font-size: 0.85rem; font-weight: 700; color: #ffffff; background: #0284c7; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                                Filter Records
                            </button>

                            @if($search || (!empty($filterEmp) && empty($isTelecaller)))
                                <a href="{{ route('kpi.telecallerkpi', ['tab' => 'telecallerkpi']) }}" style="padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 600; color: #64748b; background: #f1f5f9; border-radius: 8px; text-decoration: none;">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- 5. DETAILED CANDIDATE OUTREACH RECORDS TABLE -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">
                                    {{ !empty($isTelecaller) ? 'My Allotted Leads & Call Task Logs' : 'Candidate Outreach & Call Task Logs' }}
                                </span>
                                <span style="font-size: 0.75rem; color: #64748b; margin-left: 0.5rem;">(Showing {{ $leadsTable->firstItem() ?? 0 }} to {{ $leadsTable->lastItem() ?? 0 }} of {{ $leadsTable->total() }} records)</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="crm-table">
                                <thead>
                                    <tr>
                                        <th>Application #</th>
                                        <th>Student Details</th>
                                        <th>Branch / Program</th>
                                        <th>Assigned Counselor</th>
                                        <th>Outreach Task Stage</th>
                                        <th>Call Duration</th>
                                        <th>Remark</th>
                                        <th>Fee Payment</th>
                                        <th>Allotted On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($leadsTable as $lead)
                                        @php
                                            $appNo = (string)$lead->applino;
                                            $latestTask = $lead->tasks->first();
                                            $isPaid = $verifiedPaidAppNos->contains($appNo);
                                        @endphp
                                        <tr>
                                            <td>
                                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.825rem; font-weight: 700; color: #0284c7;">
                                                    {{ $lead->applino }}
                                                </span>
                                                @if($lead->crmid)
                                                    <div style="font-size: 0.7rem; color: #64748b; font-family: 'JetBrains Mono', monospace;">CRM: {{ $lead->crmid }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="font-weight: 700; color: #1e293b; font-size: 0.875rem;">
                                                    {{ $lead->student?->name ?: 'Candidate' }}
                                                </div>
                                                <div style="font-size: 0.75rem; color: #64748b;">
                                                    📞 {{ $lead->phoneno ?: ($lead->student?->phoneno ?: 'N/A') }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: #334155; font-size: 0.825rem;">
                                                    {{ $lead->student?->branch?->branch_short_name ?: ($lead->student?->branch?->branch_long_name ?: 'General Engineering') }}
                                                </div>
                                                <div style="font-size: 0.72rem; color: #64748b;">
                                                    {{ $lead->student?->program?->program_short_name ?: 'B.Tech' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 700; color: #0284c7; font-size: 0.825rem;">
                                                    {{ $lead->employee?->full_name ?: $lead->empcode }}
                                                </div>
                                                <div style="font-size: 0.7rem; color: #64748b; font-family: 'JetBrains Mono', monospace;">ID: {{ $lead->empcode }}</div>
                                            </td>
                                            <td>
                                                @if($latestTask && $latestTask->stage_name)
                                                    <span style="font-size: 0.75rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 2px 8px; border-radius: 6px;">
                                                        {{ $latestTask->stage_name }}
                                                    </span>
                                                @else
                                                    <span style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px;">
                                                        Pending Call
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span style="font-size: 0.8rem; font-family: 'JetBrains Mono', monospace; color: #334155;">
                                                    {{ $latestTask?->stage_call_duration ?: '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span style="font-size: 0.78rem; color: #475569; max-width: 220px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $latestTask?->stage_remark }}">
                                                    {{ $latestTask?->stage_remark ?: '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($isPaid)
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 6px;">
                                                        ✓ Paid
                                                    </span>
                                                @else
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px;">
                                                        Unpaid
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span style="font-size: 0.75rem; color: #64748b;">
                                                    {{ $lead->assigndatetime ? date('d M Y, h:i A', strtotime($lead->assigndatetime)) : '—' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" style="text-align: center; padding: 2.5rem 1.5rem; color: #94a3b8;">
                                                <div style="font-size: 1.5rem; margin-bottom: 0.35rem;">🔍</div>
                                                <div style="font-size: 0.9rem; font-weight: 600; color: #64748b;">No matching candidate outreach records found</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        @if($leadsTable->hasPages())
                            <div class="table-pagination-area">
                                <div style="font-size: 0.825rem; color: #64748b;">
                                    Showing <strong style="color: #1e293b;">{{ $leadsTable->firstItem() ?? 0 }}</strong> to <strong style="color: #1e293b;">{{ $leadsTable->lastItem() ?? 0 }}</strong> of <strong style="color: #1e293b;">{{ number_format($leadsTable->total()) }}</strong> records ({{ $leadsTable->perPage() }} per page)
                                </div>
                                <div class="pagination-wrapper">
                                    {{ $leadsTable->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Chart.js Counselor Performance Comparison
    const compCtx = document.getElementById('counselorComparisonChart');
    if (compCtx) {
        const empLabels = {!! json_encode($telecallerLeaderboard->pluck('name')) !!};
        const allocatedData = {!! json_encode($telecallerLeaderboard->pluck('allocated_leads')) !!};
        const callsData = {!! json_encode($telecallerLeaderboard->pluck('completed_calls')) !!};
        const paidData = {!! json_encode($telecallerLeaderboard->pluck('paid_conversions')) !!};

        new Chart(compCtx, {
            type: 'bar',
            data: {
                labels: empLabels.length ? empLabels : ['Counselor Pool'],
                datasets: [
                    {
                        label: 'Allotted Leads',
                        data: allocatedData.length ? allocatedData : [0],
                        backgroundColor: '#0284c7',
                        borderRadius: 4
                    },
                    {
                        label: 'Calls Completed',
                        data: callsData.length ? callsData : [0],
                        backgroundColor: '#2563eb',
                        borderRadius: 4
                    },
                    {
                        label: 'Fee Conversions',
                        data: paidData.length ? paidData : [0],
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { font: { size: 11, family: 'Plus Jakarta Sans' }, boxWidth: 12 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: { stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: 'Plus Jakarta Sans' } }
                    }
                }
            }
        });
    }

    // 2. Chart.js Call Disposition Stage Breakdown Chart
    const dispCtx = document.getElementById('dispositionChart');
    if (dispCtx) {
        const stageLabels = {!! json_encode($stageBreakdown->keys()) !!};
        const stageCounts = {!! json_encode($stageBreakdown->values()) !!};

        new Chart(dispCtx, {
            type: 'doughnut',
            data: {
                labels: stageLabels.length ? stageLabels : ['No Calls Logged'],
                datasets: [{
                    data: stageCounts.length ? stageCounts : [1],
                    backgroundColor: [
                        '#10b981', '#0284c7', '#7c3aed', '#f59e0b',
                        '#ec4899', '#6366f1', '#14b8a6', '#64748b'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11, family: 'Plus Jakarta Sans' },
                            padding: 10
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
});

function toggleSidebarMenu() {
    const sidebar = document.querySelector('.app-sidebar');
    if (sidebar) sidebar.classList.toggle('active');
}
</script>
</body>
</html>
