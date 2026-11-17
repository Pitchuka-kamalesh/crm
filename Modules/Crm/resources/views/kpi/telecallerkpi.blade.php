<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telecaller KPI &amp; Outreach Performance - VFSTR Admissions CRM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700;800&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css?v={{ file_exists(public_path('css/crm-dashboard.css')) ? filemtime(public_path('css/crm-dashboard.css')) : time() }}">

    <!-- Chart.js for High-Impact Analytics -->
    <script src="/js/chart.min.js"></script>
    <script>if (typeof Chart === 'undefined') { document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"><\/script>'); }</script>

    <style>
        .telecaller-filter-wrapper:hover {
            border-color: #94a3b8 !important;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08) !important;
        }
        .telecaller-filter-wrapper:focus-within {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.18) !important;
        }
        .kpi-metric-card:hover {
            border-color: #cbd5e1 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.06) !important;
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .crm-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.85rem;
        }
        .crm-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 0.72rem;
            letter-spacing: 0.04em;
            white-space: nowrap;
        }
        .crm-table td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }
        .crm-table tbody tr:hover {
            background-color: #f8fafc;
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

            <!-- TAB: TELECALLER KPI & OUTREACH PERFORMANCE -->
            <div class="content-card">
                <div class="card-header-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 1.15rem 1.5rem; border-bottom: 1px solid #e2e8f0; background: #ffffff;">
                    <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                        <div class="card-title" style="font-size: 1.15rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; margin: 0; white-space: nowrap;">
                            <span>Telecaller KPI &amp; Outreach Performance</span>
                        </div>

                        @if(empty($isTelecaller))
                        <!-- Telecaller Selection Dropdown Filter -->
                        <div class="telecaller-filter-wrapper" style="position: relative; display: inline-flex; align-items: center; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 9px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1); overflow: hidden;">
                            <div style="display: inline-flex; align-items: center; gap: 7px; padding: 0 13px; height: 38px; background: #f8fafc; border-right: 1.5px solid #cbd5e1; font-size: 0.74rem; font-weight: 800; color: #334155; text-transform: uppercase; letter-spacing: 0.4px; user-select: none; white-space: nowrap;">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span>Telecallers:</span>
                                <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 20px; padding: 0 6px; background: #2563eb; color: #ffffff; font-weight: 800; font-size: 0.72rem; border-radius: 999px; box-shadow: 0 1px 2px rgba(37, 99, 235, 0.25);">
                                    {{ count($telecallerStats ?? []) }}
                                </span>
                            </div>
                            <div style="position: relative; display: inline-flex; align-items: center;">
                                <select id="telecallerDropdownFilter" onchange="handleTelecallerChange(this.value)" style="height: 38px; min-width: 250px; padding: 0 36px 0 13px; font-size: 0.85rem; font-weight: 700; color: #0f172a; background: #ffffff; border: none; outline: none; cursor: pointer; -webkit-appearance: none; -moz-appearance: none; appearance: none; font-family: inherit;">
                                    <option value="all" {{ ($selectedTelecaller ?? 'all') === 'all' ? 'selected' : '' }}>
                                        All Telecallers
                                    </option>
                                    @foreach(($telecallerStats ?? []) as $tOpt)
                                        <option value="{{ $tOpt->emp_code }}" {{ ($selectedTelecaller ?? '') === $tOpt->emp_code ? 'selected' : '' }}>
                                            {{ $tOpt->name }} (Emp: {{ $tOpt->emp_code }})
                                        </option>
                                    @endforeach
                                </select>
                                <svg style="position: absolute; right: 12px; pointer-events: none; color: #64748b;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="6 9 12 15 18 9"></polyline>
                                </svg>
                            </div>
                        </div>
                        @endif

                        <!-- Export CSV Button (Beside Telecaller Dropdown) -->
                        <button type="button" onclick="exportTelecallerKpiCsv()" style="height: 38px; display: inline-flex; align-items: center; gap: 0.45rem; padding: 0 1.15rem; background: #0284c7; color: #ffffff; border: none; border-radius: 8px; font-size: 0.825rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: all 0.2s; box-shadow: 0 1px 3px rgba(2, 132, 199, 0.25);" onmouseover="this.style.background='#0369a1'" onmouseout="this.style.background='#0284c7'">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>Export CSV</span>
                        </button>
                    </div>
                </div>

                <div style="padding: 1.5rem;">
                    <!-- 1. TOP EXECUTIVE METRIC CARDS (COMPACT SINGLE-ROW BOXES) -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 0.85rem; margin-bottom: 1.5rem;">
                        <!-- Card 1: Allotted Leads -->
                        <div class="kpi-metric-card" style="border: 1px solid #e2e8f0; border-radius: 9px; padding: 0.65rem 1.15rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                            <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap;">Total Allotted Leads</span>
                            <strong id="kpiCardAllottedValue" style="font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1; font-family: 'JetBrains Mono', monospace;">{{ number_format($totalTelecallerLeads) }}</strong>
                        </div>

                        <!-- Card 2: Calls Logged -->
                        <div class="kpi-metric-card" style="border: 1px solid #e2e8f0; border-radius: 9px; padding: 0.65rem 1.15rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                            <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap;">Call Tasks Logged</span>
                            <strong id="kpiCardTasksValue" style="font-size: 1.35rem; font-weight: 800; color: #2563eb; line-height: 1; font-family: 'JetBrains Mono', monospace;">{{ number_format($teleTasksCount) }}</strong>
                        </div>

                        <!-- Card 3: Leads Contacted -->
                        <div class="kpi-metric-card" style="border: 1px solid #e2e8f0; border-radius: 9px; padding: 0.65rem 1.15rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                            <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap;">Leads Contacted</span>
                            <strong id="kpiCardContactedValue" style="font-size: 1.35rem; font-weight: 800; color: #059669; line-height: 1; font-family: 'JetBrains Mono', monospace;">{{ number_format($teleDistinctContacted) }}</strong>
                        </div>

                        <!-- Card 4: Pending Outreach -->
                        <div class="kpi-metric-card" style="border: 1px solid #e2e8f0; border-radius: 9px; padding: 0.65rem 1.15rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                            <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap;">Pending Outreach</span>
                            <strong id="kpiCardPendingValue" style="font-size: 1.35rem; font-weight: 800; color: #d97706; line-height: 1; font-family: 'JetBrains Mono', monospace;">{{ number_format($telePendingCount) }}</strong>
                        </div>
                    </div>

                    <!-- 2. TELECALLER ALLOCATION & CALL STAGES BREAKDOWN -->
                    <div style="display: grid; grid-template-columns: 1.15fr 0.85fr; gap: 1.5rem; margin-bottom: 1.75rem;">
                        <!-- 1. Telecallers Step Completion Table (Replaces old Logged Call Stages Breakdown) -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #ffffff; display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Telecaller Step Completion Summary</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">Allocated leads by onboarding steps completed vs not completed</div>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; color: #047857; background: #ecfdf5; padding: 0.2rem 0.6rem; border-radius: 20px;">Telecaller Summary</span>
                            </div>
                            <div class="table-responsive" style="max-height: 290px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <table class="crm-table" style="width: 100%; border-collapse: collapse; font-size: 0.82rem; margin: 0;">
                                    <thead>
                                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; position: sticky; top: 0; z-index: 2;">
                                            <th style="padding: 0.6rem 0.75rem; text-align: left; font-weight: 700; color: #475569; font-size: 0.72rem; text-transform: uppercase;">Telecaller</th>
                                            <th style="padding: 0.6rem 0.75rem; text-align: center; font-weight: 700; color: #475569; font-size: 0.72rem; text-transform: uppercase;">Allocated</th>
                                            <th style="padding: 0.6rem 0.75rem; text-align: center; font-weight: 700; color: #15803d; font-size: 0.72rem; text-transform: uppercase;">All Steps Completed</th>
                                            <th style="padding: 0.6rem 0.75rem; text-align: center; font-weight: 700; color: #b45309; font-size: 0.72rem; text-transform: uppercase;">Not Completed</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($telecallerStats ?? []) as $tStat)
                                            <tr class="telecaller-stat-row" data-empcode="{{ $tStat->emp_code }}" style="border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: background 0.15s ease;" onclick="selectTelecaller('{{ $tStat->emp_code }}')" title="Click to filter by {{ $tStat->name }}">
                                                <td style="padding: 0.65rem 0.75rem;">
                                                    <div style="font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                                                        <span style="font-size: 0.95rem;">👤</span>
                                                        <span>{{ $tStat->name }}</span>
                                                    </div>
                                                    <div style="font-size: 0.7rem; color: #64748b; font-family: 'JetBrains Mono', monospace; margin-top: 1px;">Emp: {{ $tStat->emp_code }}</div>
                                                </td>
                                                <td style="padding: 0.65rem 0.75rem; text-align: center;">
                                                    <span style="display: inline-block; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: #0f172a; background: #f1f5f9; padding: 2px 8px; border-radius: 6px; font-size: 0.85rem;">
                                                        {{ number_format($tStat->leads_count) }}
                                                    </span>
                                                </td>
                                                <td style="padding: 0.65rem 0.75rem; text-align: center;">
                                                    <span style="display: inline-flex; align-items: center; gap: 4px; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: #15803d; background: #dcfce7; border: 1px solid #86efac; padding: 2px 8px; border-radius: 6px; font-size: 0.85rem;">
                                                        ✓ {{ number_format($tStat->allocated_completed) }}
                                                    </span>
                                                </td>
                                                <td style="padding: 0.65rem 0.75rem; text-align: center;">
                                                    <span style="display: inline-flex; align-items: center; gap: 4px; font-weight: 800; font-family: 'JetBrains Mono', monospace; color: #b45309; background: #fef3c7; border: 1px solid #fcd34d; padding: 2px 8px; border-radius: 6px; font-size: 0.85rem;">
                                                        ⏳ {{ number_format($tStat->allocated_not_completed) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" style="text-align: center; padding: 1.5rem; color: #94a3b8; font-size: 0.85rem;">
                                                    No telecaller data available.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if(isset($telecallerStats) && $telecallerStats->isNotEmpty())
                                        <tfoot style="border-top: 2px solid #e2e8f0; background: #f8fafc; position: sticky; bottom: 0;">
                                            <tr style="font-weight: 800;">
                                                <td style="padding: 0.6rem 0.75rem; color: #334155; font-size: 0.75rem; text-transform: uppercase;">Total</td>
                                                <td style="padding: 0.6rem 0.75rem; text-align: center; font-family: 'JetBrains Mono', monospace; color: #0f172a; font-size: 0.85rem;">{{ number_format($totalTelecallerLeads) }}</td>
                                                <td style="padding: 0.6rem 0.75rem; text-align: center; font-family: 'JetBrains Mono', monospace; color: #15803d; font-size: 0.85rem;">✓ {{ number_format($totalAllStepsCompleted) }}</td>
                                                <td style="padding: 0.6rem 0.75rem; text-align: center; font-family: 'JetBrains Mono', monospace; color: #b45309; font-size: 0.85rem;">⏳ {{ number_format($totalAllStepsNotCompleted) }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>

                        <!-- 2. Logged Call Stages Breakdown (Visual Graph) -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #ffffff; display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Logged Call Stages Breakdown</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">Authentic stage categories from telecaller lead tasks</div>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 0.2rem 0.6rem; border-radius: 20px;">Task Stages</span>
                            </div>
                            <div style="position: relative; flex-grow: 1; min-height: 250px; max-height: 290px; display: flex; align-items: center; justify-content: center;">
                                <canvas id="callStagesChart"></canvas>
                                <div id="callStagesEmpty" style="display: none; text-align: center; padding: 2rem; color: #94a3b8; font-size: 0.85rem;">
                                    No call stages recorded in database for this selection.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. COUNSELOR LEAD OUTREACH DIRECTORY TABLE (GENUINE CALL TASKS & REMARKS) -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #ffffff;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 1rem 1.25rem; background: #ffffff; border-bottom: 1px solid #e2e8f0;">
                            <div>
                                <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Counselor Lead Outreach Directory</div>
                                <div style="font-size: 0.75rem; color: #64748b;">Actual allotted leads and logged calling task stages from PostgreSQL</div>
                            </div>
                            <div style="position: relative;">
                                <input type="text" id="telecallerSearchInput" placeholder="Search phone, counselor, stage, remark..." onkeyup="filterTelecallerTable()" style="padding: 0.45rem 0.85rem 0.45rem 2rem; font-size: 0.825rem; border: 1px solid #cbd5e1; border-radius: 8px; width: 310px; outline: none; background: #ffffff;">
                                <svg style="position: absolute; left: 0.65rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="crm-table" id="telecallerTable">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Lead Contact</th>
                                        <th>Application No</th>
                                        <th>Assigned Counselor</th>
                                        <th>Allocation Date</th>
                                        <th>Latest Call Stage</th>
                                        <th>Calls Made</th>
                                        <th>Latest Counselor Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($telecallerLeadsList as $index => $tLead)
                                        <tr class="telecaller-row" data-empcode="{{ $tLead->empcode }}">
                                            <td style="color: #64748b; font-weight: 600;">{{ $index + 1 }}</td>
                                            <td>
                                                <span style="font-weight: 700; color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.86rem;">{{ $tLead->phoneno }}</span>
                                            </td>
                                            <td>
                                                @if($tLead->applino && $tLead->applino !== '0')
                                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; color: #1e40af;">{{ $tLead->applino }}</span>
                                                @else
                                                    <span style="font-size: 0.78rem; color: #94a3b8; font-style: italic;">Not Registered</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: #1e293b;">{{ $tLead->emp_name ?? 'Counselor' }}</div>
                                                <div style="font-size: 0.72rem; color: #0284c7; font-weight: 600;">Emp: {{ $tLead->empcode }}</div>
                                            </td>
                                            <td>
                                                <div style="font-size: 0.8rem; color: #334155; font-weight: 600;">{{ $tLead->assigndatetime ? date('d M Y, h:i A', strtotime($tLead->assigndatetime)) : 'N/A' }}</div>
                                            </td>
                                            <td>
                                                @if($tLead->call_count > 0)
                                                    <span style="display: inline-block; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0;">
                                                        {{ $tLead->latest_stage }}
                                                    </span>
                                                @else
                                                    <span style="display: inline-block; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; background: #fef3c7; color: #92400e; border: 1px solid #fde68a;">
                                                        Pending Call
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">{{ $tLead->call_count }} call(s)</span>
                                            </td>
                                            <td style="max-width: 280px;">
                                                <span style="font-size: 0.78rem; color: #475569; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $tLead->latest_remark }}">
                                                    {{ $tLead->latest_remark }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" style="text-align: center; padding: 2rem; color: #64748b;">No telecaller allocated leads found.</td>
                                        </tr>
                                    @endforelse
                                    <tr id="telecallerNoRowsFound" style="display: none;">
                                        <td colspan="8" style="text-align: center; padding: 2.5rem; color: #64748b; font-weight: 600;">No leads allocated to this telecaller yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
const telecallerData = {
    all: {
        emp_code: 'all',
        name: 'All Telecallers',
        allotted: {{ $totalTelecallerLeads }},
        tasks: {{ $teleTasksCount }},
        contacted: {{ $teleDistinctContacted }},
        pending: {{ $telePendingCount }},
        stages: {!! json_encode($teleTaskStages) !!}
    },
    @foreach(($telecallerStats ?? []) as $tStat)
    '{{ $tStat->emp_code }}': {
        emp_code: '{{ $tStat->emp_code }}',
        name: '{{ addslashes($tStat->name) }}',
        allotted: {{ $tStat->leads_count }},
        tasks: {{ $tStat->tasks_count }},
        contacted: {{ $tStat->contacted_count }},
        pending: {{ $tStat->pending_count }},
        stages: {!! json_encode($tStat->stages) !!}
    },
    @endforeach
};

function selectTelecaller(empCode) {
    const dropdown = document.getElementById('telecallerDropdownFilter');
    if (dropdown) {
        const targetVal = (dropdown.value === empCode) ? 'all' : empCode;
        dropdown.value = targetVal;
        handleTelecallerChange(targetVal);
    }
}

function handleTelecallerChange(empCode) {
    const data = telecallerData[empCode] || telecallerData['all'];

    // 1. Update Top 4 Metric Cards
    const cardAllottedVal = document.getElementById('kpiCardAllottedValue');
    const cardTasksVal = document.getElementById('kpiCardTasksValue');
    const cardContactedVal = document.getElementById('kpiCardContactedValue');
    const cardPendingVal = document.getElementById('kpiCardPendingValue');

    if (cardAllottedVal) cardAllottedVal.textContent = Number(data.allotted).toLocaleString();
    if (cardTasksVal) cardTasksVal.textContent = Number(data.tasks).toLocaleString();
    if (cardContactedVal) cardContactedVal.textContent = Number(data.contacted).toLocaleString();
    if (cardPendingVal) cardPendingVal.textContent = Number(data.pending).toLocaleString();

    // 2. Update Logged Call Stages Chart
    updateCallStagesChart(data.stages, data.tasks);

    // 3. Highlight active row in Telecaller Step Completion Table
    document.querySelectorAll('.telecaller-stat-row').forEach(row => {
        if (empCode !== 'all' && row.getAttribute('data-empcode') === empCode) {
            row.style.background = '#f0fdf4';
            row.style.boxShadow = 'inset 3px 0 0 #10b981';
        } else {
            row.style.background = '';
            row.style.boxShadow = '';
        }
    });

    // 4. Filter Directory Table
    filterTelecallerTable();

    // 5. Update URL without page reload
    try {
        const url = new URL(window.location.href);
        if (empCode !== 'all') {
            url.searchParams.set('telecaller', empCode);
        } else {
            url.searchParams.delete('telecaller');
        }
        window.history.replaceState(null, '', url.toString());
    } catch (e) {}
}

let callStagesChart = null;

function updateCallStagesChart(stages, totalTasks) {
    const canvas = document.getElementById('callStagesChart');
    const emptyEl = document.getElementById('callStagesEmpty');
    if (!canvas) return;

    const validStages = (stages || []).filter(s => Number(s.count || 0) > 0);

    if (validStages.length === 0) {
        canvas.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'block';
        if (callStagesChart) {
            callStagesChart.destroy();
            callStagesChart = null;
        }
        return;
    }

    canvas.style.display = 'block';
    if (emptyEl) emptyEl.style.display = 'none';

    const labels = validStages.map(s => s.stage_name);
    const counts = validStages.map(s => Number(s.count));
    const total = counts.reduce((a, b) => a + b, 0);

    const palette = [
        '#0284c7', // Sky Blue
        '#059669', // Emerald
        '#7c3aed', // Purple
        '#ea580c', // Orange
        '#e11d48', // Rose
        '#d97706', // Amber
        '#0891b2', // Cyan
        '#4f46e5'  // Indigo
    ];

    if (callStagesChart) {
        callStagesChart.data.labels = labels;
        callStagesChart.data.datasets[0].data = counts;
        callStagesChart.data.datasets[0].backgroundColor = palette.slice(0, labels.length);
        callStagesChart.update();
        return;
    }

    callStagesChart = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: counts,
                backgroundColor: palette.slice(0, labels.length),
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
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
                        padding: 12,
                        font: { size: 11, family: 'Plus Jakarta Sans', weight: '600' },
                        color: '#334155',
                        generateLabels: function(chart) {
                            const data = chart.data;
                            if (data.labels.length && data.datasets.length) {
                                return data.labels.map((label, i) => {
                                    const val = data.datasets[0].data[i];
                                    return {
                                        text: `${label} (${val})`,
                                        fillStyle: data.datasets[0].backgroundColor[i],
                                        strokeStyle: '#ffffff',
                                        lineWidth: 1,
                                        hidden: false,
                                        index: i
                                    };
                                });
                            }
                            return [];
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const val = context.parsed;
                            return ` ${context.label}: ${val} calls`;
                        }
                    }
                }
            },
            cutout: '62%'
        }
    });
}

function filterTelecallerTable() {
    const dropdown = document.getElementById('telecallerDropdownFilter');
    const selectedEmp = dropdown ? dropdown.value : 'all';
    const input = document.getElementById('telecallerSearchInput');
    const filter = (input ? input.value : '').toLowerCase().trim();
    const rows = document.querySelectorAll('#telecallerTable tbody tr.telecaller-row');
    const noRows = document.getElementById('telecallerNoRowsFound');
    
    let visibleCount = 0;
    rows.forEach(row => {
        const rowEmp = row.getAttribute('data-empcode');
        const matchesEmp = (selectedEmp === 'all' || rowEmp === selectedEmp);
        const text = row.innerText.toLowerCase();
        const matchesText = !filter || text.includes(filter);

        if (matchesEmp && matchesText) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    if (noRows) {
        noRows.style.display = (visibleCount === 0) ? '' : 'none';
        if (visibleCount === 0 && selectedEmp !== 'all') {
            const name = (telecallerData[selectedEmp] && telecallerData[selectedEmp].name) 
                ? telecallerData[selectedEmp].name 
                : 'this telecaller';
            noRows.querySelector('td').textContent = 'No leads allocated to ' + name + ' yet.';
        } else if (visibleCount === 0) {
            noRows.querySelector('td').textContent = 'No matching allocated leads found.';
        }
    }
}

function exportTelecallerKpiCsv() {
    const dropdown = document.getElementById('telecallerDropdownFilter');
    const selectedEmp = dropdown ? dropdown.value : 'all';

    let suffix = 'All';
    let counselorName = 'All Telecallers';
    if (selectedEmp !== 'all' && telecallerData[selectedEmp]) {
        counselorName = telecallerData[selectedEmp].name || ('Emp_' + selectedEmp);
        suffix = counselorName.replace(/[^a-zA-Z0-9_-]/g, '_') + '_' + selectedEmp;
    }

    const headers = ['#', 'Lead Phone', 'Application No', 'Assigned Counselor', 'Counselor Emp Code', 'Allocation Date', 'Call Stage', 'Calls Made', 'Latest Remark'];
    const rows = [];
    let count = 0;

    document.querySelectorAll('#telecallerTable tbody tr.telecaller-row').forEach(row => {
        const rowEmp = row.getAttribute('data-empcode');
        // When a specific telecaller is selected, only export that specific telecaller's leads
        if (selectedEmp === 'all' || rowEmp === selectedEmp) {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 8) {
                count++;
                const phone = cells[1].innerText.replace(/\n.*/, '').trim();
                const applino = cells[2].innerText.trim();
                const counselor = cells[3].innerText.replace(/\n.*/, '').trim();
                const empCode = (cells[3].innerText.match(/Emp:\s*(\S+)/) || [])[1] || rowEmp;
                const allocDate = cells[4].innerText.trim();
                const stage = cells[5].innerText.trim();
                const callsCount = cells[6].innerText.trim();
                const remark = cells[7].innerText.trim();
                rows.push([count, phone, applino, counselor, empCode, allocDate, stage, callsCount, remark]);
            }
        }
    });

    if (rows.length === 0) {
        alert('No allocated leads found for ' + counselorName + ' to export.');
        return;
    }

    const filename = 'Telecaller_KPI_Report_' + suffix + '_' + new Date().toISOString().slice(0, 10) + '.csv';
    downloadCsv(filename, headers, rows);
}

function downloadCsv(filename, headers, rows) {
    let csvContent = "\uFEFF";
    csvContent += headers.map(h => `"${h.replace(/"/g, '""')}"`).join(",") + "\r\n";
    rows.forEach(row => {
        csvContent += row.map(cell => `"${(cell ?? '').toString().replace(/"/g, '""')}"`).join(",") + "\r\n";
    });
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('telecallerDropdownFilter');
    const initialEmp = (dropdown && dropdown.value) ? dropdown.value : 'all';
    
    // Initialize chart with initial selection data
    const initialData = telecallerData[initialEmp] || telecallerData['all'];
    updateCallStagesChart(initialData.stages, initialData.tasks);

    if (initialEmp !== 'all') {
        handleTelecallerChange(initialEmp);
    }
});
</script>
</body>
</html>
