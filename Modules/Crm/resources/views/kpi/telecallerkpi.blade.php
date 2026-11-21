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
        .telecaller-charts-grid {
            display: grid;
            grid-template-columns: 1.15fr 0.95fr 0.9fr;
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }
        @media (max-width: 1200px) {
            .telecaller-charts-grid {
                grid-template-columns: 1fr !important;
            }
        }
        .telecaller-metrics-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1.5rem;
        }
        .telecaller-filter-wrapper:hover {
            border-color: #94a3b8 !important;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08) !important;
        }
        .telecaller-filter-wrapper:focus-within {
            border-color: var(--theme-accent, #2563eb) !important;
            box-shadow: 0 0 0 3px var(--theme-accent-ring, rgba(37, 99, 235, 0.18)) !important;
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
            min-width: 840px;
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
            white-space: nowrap;
        }
        .crm-table tbody tr:hover {
            background-color: #f8fafc;
        }

        .call-activity-filters {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 0.5rem;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.45rem 0.65rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            width: 100%;
            box-sizing: border-box;
        }
        .call-activity-filter-item {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            min-width: 0;
            width: 100%;
        }
        .call-activity-filter-item label {
            flex-shrink: 0;
            font-size: 0.68rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
        }
        .call-activity-filter-item select {
            flex: 1;
            min-width: 0;
            width: 100%;
            font-size: 0.75rem;
            font-weight: 600;
            color: #1e293b;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 0.25rem 0.45rem;
            outline: none;
            cursor: pointer;
            box-sizing: border-box;
        }
        .table-responsive {
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
        .crm-table {
            width: 100% !important;
            min-width: 860px !important;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.85rem;
        }
        .table-scroll-hint {
            display: none;
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 600;
            padding: 0.3rem 0.75rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        @media (max-width: 991px) {
            .telecaller-metrics-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 0.65rem !important;
            }
        }

        @media (max-width: 768px) {
            .table-scroll-hint {
                display: flex !important;
                align-items: center;
                gap: 6px;
            }
            .card-header-bar {
                padding: 0.85rem 1rem !important;
                gap: 0.65rem !important;
            }
            .card-header-bar > div {
                width: 100% !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.65rem !important;
            }
            .card-title {
                font-size: 1rem !important;
                white-space: normal !important;
            }
            .telecaller-filter-wrapper {
                width: 100% !important;
                min-width: 100% !important;
            }
            .telecaller-filter-wrapper select {
                min-width: 0 !important;
                width: 100% !important;
            }
            .btn-theme-export {
                width: 100% !important;
                justify-content: center !important;
            }
            .kpi-content-padding {
                padding: 0.85rem 0.65rem !important;
            }
            .call-activity-filters {
                display: grid !important;
                grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
                gap: 0.35rem !important;
                padding: 0.45rem 0.5rem !important;
            }
            .call-activity-filter-item {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 2px !important;
                width: 100% !important;
            }
            .call-activity-filter-item select {
                width: 100% !important;
                padding: 0.3rem 0.35rem !important;
                font-size: 0.74rem !important;
            }
            #telecallerSearchInput {
                width: 100% !important;
            }
        }

        @media (max-width: 480px) {
            .telecaller-metrics-grid {
                grid-template-columns: 1fr !important;
                gap: 0.5rem !important;
            }
            .kpi-metric-card {
                padding: 0.55rem 0.85rem !important;
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
        <header class="top-bar">
            <div class="top-bar-left">
                <button type="button" class="topbar-toggle-sidebar-btn" onclick="toggleSidebarMenu()" title="Open Navigation Menu" aria-label="Toggle Sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                </button>
                <div class="topbar-page-breadcrumb">
                    <span class="breadcrumb-item breadcrumb-desktop-only">KPI</span>
                    <span class="breadcrumb-separator breadcrumb-desktop-only">/</span>
                    <span class="breadcrumb-active-tag">
                        <span class="breadcrumb-dot"></span>
                        <span class="breadcrumb-lead-full">Telecaller KPI &bull; Outreach Performance</span>
                        <span class="breadcrumb-lead-mobile">Telecaller KPI</span>
                    </span>
                </div>
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
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" style="color: var(--theme-accent, #2563eb);" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                                <span>Telecallers:</span>
                                <span style="display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 20px; padding: 0 6px; background: var(--theme-accent, #2563eb); color: #ffffff; font-weight: 800; font-size: 0.72rem; border-radius: 999px; box-shadow: 0 1px 2px var(--theme-accent-ring, rgba(37, 99, 235, 0.25));">
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
                        <button type="button" class="btn-theme-export" onclick="exportTelecallerKpiCsv()" style="height: 38px; display: inline-flex; align-items: center; gap: 0.45rem; padding: 0 1.15rem; background: var(--theme-accent, #0284c7); color: #ffffff; border: none; border-radius: 8px; font-size: 0.825rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: all 0.2s; box-shadow: 0 1px 3px var(--theme-accent-ring, rgba(2, 132, 199, 0.25));">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            <span>Export CSV</span>
                        </button>
                    </div>
                </div>

                <div class="kpi-content-padding" style="padding: 1.5rem;">
                    <!-- 1. TOP EXECUTIVE METRIC CARDS (COMPACT SINGLE-ROW BOXES) -->
                    <div class="telecaller-metrics-grid">
                        <!-- Card 1: Allotted Leads -->
                        <div class="kpi-metric-card" style="border: 1px solid #e2e8f0; border-radius: 9px; padding: 0.65rem 1.15rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                            <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap;">Total Allotted Leads</span>
                            <strong id="kpiCardAllottedValue" style="font-size: 1.35rem; font-weight: 800; color: #0f172a; line-height: 1; font-family: 'JetBrains Mono', monospace;">{{ number_format($totalTelecallerLeads) }}</strong>
                        </div>

                        <!-- Card 2: Calls Logged -->
                        <div class="kpi-metric-card" style="border: 1px solid #e2e8f0; border-radius: 9px; padding: 0.65rem 1.15rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03); transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);">
                            <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap;">Call Tasks Logged</span>
                            <strong id="kpiCardTasksValue" style="font-size: 1.35rem; font-weight: 800; color: var(--theme-accent, #2563eb); line-height: 1; font-family: 'JetBrains Mono', monospace;">{{ number_format($teleTasksCount) }}</strong>
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

                    <!-- 2. TELECALLER ANALYTICS & VISUAL GRAPHS -->
                    <div class="telecaller-charts-grid">
                        <!-- Graph 1: Telecaller Outreach & Step Completion (Grouped Bar Chart with Table View Toggle) -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #ffffff; display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Telecaller Summary &amp; Step Completion</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">Workload comparison of allotted leads, completed steps, and incompleted leads</div>
                                </div>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="display: inline-flex; background: #f1f5f9; padding: 2px; border-radius: 6px; border: 1px solid #e2e8f0;">
                                        <button type="button" id="btnShowBarChart" onclick="toggleTelecallerView('chart')" style="border: none; background: #ffffff; color: #0f172a; font-weight: 700; font-size: 0.72rem; padding: 0.2rem 0.6rem; border-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); cursor: pointer;">
                                            📊 Chart
                                        </button>
                                        <button type="button" id="btnShowTable" onclick="toggleTelecallerView('table')" style="border: none; background: transparent; color: #64748b; font-weight: 600; font-size: 0.72rem; padding: 0.2rem 0.6rem; border-radius: 4px; cursor: pointer;">
                                            📋 Table
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- View A: Bar Chart Container -->
                            <div id="telecallerChartView" style="position: relative; flex-grow: 1; min-height: 260px; max-height: 290px; display: flex; align-items: center; justify-content: center;">
                                <canvas id="telecallerBarChart"></canvas>
                                <div id="telecallerBarEmpty" style="display: none; text-align: center; padding: 2.5rem 1.5rem; color: #94a3b8;">
                                    <div style="font-size: 0.95rem; font-weight: 600; color: #64748b;">No records found</div>
                                </div>
                            </div>

                            <!-- View B: Table Container (Toggleable) -->
                            <div id="telecallerTableView" style="display: none; max-height: 290px; overflow-y: auto; border: 1px solid #e2e8f0; border-radius: 8px;">
                                <table class="crm-table" style="width: 100%; border-collapse: collapse; font-size: 0.82rem; margin: 0;">
                                    <thead>
                                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; position: sticky; top: 0; z-index: 2;">
                                            <th style="padding: 0.6rem 0.75rem; text-align: left; font-weight: 700; color: #475569; font-size: 0.72rem; text-transform: uppercase;">Telecaller</th>
                                            <th style="padding: 0.6rem 0.75rem; text-align: center; font-weight: 700; color: #475569; font-size: 0.72rem; text-transform: uppercase;">Allocated</th>
                                            <th style="padding: 0.6rem 0.75rem; text-align: center; font-weight: 700; color: #15803d; font-size: 0.72rem; text-transform: uppercase;">Completed</th>
                                            <th style="padding: 0.6rem 0.75rem; text-align: center; font-weight: 700; color: #b45309; font-size: 0.72rem; text-transform: uppercase;">Incompleted</th>
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

                        <!-- Graph 2: Allotted Leads by Stage (Stage Distribution - Exact Counts, No Percentage) -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #ffffff; display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Logged Call Stages Breakdown</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">Allotted leads by current call stage</div>
                                </div>
                                <span id="leadStagesAllottedBadge" style="font-size: 0.72rem; font-weight: 700; color: #0284c7; background: #e0f2fe; padding: 0.2rem 0.65rem; border-radius: 20px;">{{ number_format($totalTelecallerLeads) }} Allotted Leads</span>
                            </div>

                            <!-- Donut Chart of Stages -->
                            <div style="position: relative; width: 100%; height: 170px; display: flex; align-items: center; justify-content: center;">
                                <canvas id="leadStagesDonutChart" style="width: 100%; height: 170px;"></canvas>
                                <div id="leadStagesEmpty" style="display: none; width: 100%; text-align: center; padding: 2rem 1rem; color: #94a3b8;">
                                    <div style="font-size: 0.9rem; font-weight: 600; color: #64748b;">No allotted leads found</div>
                                </div>
                            </div>

                            <!-- Stage Items List Below Donut (Proper, Clear, No Percentage) -->
                            <div id="leadStagesList" style="margin-top: 0.75rem; border-top: 1px solid #f1f5f9; padding-top: 0.6rem; display: flex; flex-direction: column; gap: 0.4rem; max-height: 130px; overflow-y: auto;">
                                <!-- Populated dynamically by updateLeadStagesChart -->
                            </div>
                        </div>

                        <!-- Graph 3: Calls Made Activity Over Time (Year, Month, Date Dropdowns) -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #ffffff; display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Calls Made Activity</div>
                                    <div style="font-size: 0.75rem; color: #64748b;">Calls logged by Year, Month, and Date</div>
                                </div>
                                <span id="callsMadeCountBadge" style="font-size: 0.72rem; font-weight: 800; color: #0284c7; background: #e0f2fe; padding: 0.2rem 0.65rem; border-radius: 20px; font-family: 'JetBrains Mono', monospace;">0 Calls Made</span>
                            </div>

                            <!-- Year, Month, Date Filter Dropdowns -->
                            <div class="call-activity-filters">
                                <!-- Year Dropdown -->
                                <div class="call-activity-filter-item">
                                    <label for="callYearFilter" style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Year:</label>
                                    <select id="callYearFilter" onchange="onCallFilterChange('year')">
                                        <option value="all">All Years</option>
                                    </select>
                                </div>

                                <!-- Month Dropdown -->
                                <div class="call-activity-filter-item">
                                    <label for="callMonthFilter" style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Month:</label>
                                    <select id="callMonthFilter" onchange="onCallFilterChange('month')">
                                        <option value="all">All Months</option>
                                        <option value="1">Jan</option>
                                        <option value="2">Feb</option>
                                        <option value="3">Mar</option>
                                        <option value="4">Apr</option>
                                        <option value="5">May</option>
                                        <option value="6">Jun</option>
                                        <option value="7">Jul</option>
                                        <option value="8">Aug</option>
                                        <option value="9">Sep</option>
                                        <option value="10">Oct</option>
                                        <option value="11" selected>Nov</option>
                                        <option value="12">Dec</option>
                                    </select>
                                </div>

                                <!-- Date Dropdown -->
                                <div class="call-activity-filter-item">
                                    <label for="callDateFilter" style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Date:</label>
                                    <select id="callDateFilter" onchange="onCallFilterChange('date')">
                                        <option value="all">All Dates</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Calls Made Visual Graph Container -->
                            <div style="position: relative; flex-grow: 1; min-height: 200px; max-height: 240px; display: flex; align-items: center; justify-content: center; width: 100%;">
                                <div id="callsMadeChartWrap" style="width: 100%; height: 200px; display: flex; align-items: center; justify-content: center;">
                                    <canvas id="callsMadeBarChart"></canvas>
                                </div>
                                <div id="callsMadeEmpty" style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 2rem 1rem; margin: auto;">
                                    <div style="font-size: 2.3rem; line-height: 1; margin-bottom: 0.5rem; opacity: 0.85;">📞</div>
                                    <div style="font-size: 1.05rem; font-weight: 700; color: #334155;">No calls made</div>
                                    <div style="font-size: 0.76rem; color: #94a3b8; margin-top: 0.25rem;">No call records found for the selected filter</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. COUNSELOR LEAD OUTREACH DIRECTORY TABLE (GENUINE CALL TASKS & REMARKS) -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; background: #ffffff;">
                        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; padding: 1rem 1.25rem; background: #ffffff; border-bottom: 1px solid #e2e8f0;">
                            <div>
                                <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Counselor Lead Outreach Directory</div>
                                <div style="font-size: 0.75rem; color: #64748b;">Actual allotted leads and logged calling task stages from PostgreSQL</div>
                            </div>
                            <div style="position: relative; width: 100%; max-width: 320px;">
                                <input type="text" id="telecallerSearchInput" placeholder="Search phone, counselor, stage, remark..." onkeyup="filterTelecallerTable()" style="padding: 0.45rem 0.85rem 0.45rem 2rem; font-size: 0.825rem; border: 1px solid #cbd5e1; border-radius: 8px; width: 100%; outline: none; background: #ffffff;">
                                <svg style="position: absolute; left: 0.65rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                        </div>

                        <div class="table-scroll-hint">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="color: #2563eb;">
                                <polyline points="9 18 15 12 9 6"></polyline>
                            </svg>
                            <span>Scroll horizontally to view all counselor outreach columns</span>
                        </div>
                        <div class="table-responsive">
                            <table class="crm-table" id="telecallerTable">
                                <thead>
                                    <tr>
                                        <th style="white-space: nowrap;">#</th>
                                        <th style="white-space: nowrap;">Lead Contact</th>
                                        <th style="white-space: nowrap;">Application No</th>
                                        <th style="white-space: nowrap;">Assigned Counselor</th>
                                        <th style="white-space: nowrap;">Allocation Date</th>
                                        <th style="white-space: nowrap;">Latest Call Stage</th>
                                        <th style="white-space: nowrap;">Calls Made</th>
                                        <th style="white-space: nowrap;">Latest Counselor Remark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($telecallerLeadsList as $index => $tLead)
                                        <tr class="telecaller-row" data-empcode="{{ $tLead->empcode }}">
                                            <td style="color: #64748b; font-weight: 600; white-space: nowrap;">{{ $index + 1 }}</td>
                                            <td style="white-space: nowrap;">
                                                <span style="font-weight: 700; color: #0f172a; font-family: 'JetBrains Mono', monospace; font-size: 0.86rem; white-space: nowrap;">{{ $tLead->phoneno }}</span>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                @if($tLead->applino && $tLead->applino !== '0')
                                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; color: #1e40af; white-space: nowrap;">{{ $tLead->applino }}</span>
                                                @else
                                                    <span style="font-size: 0.78rem; color: #94a3b8; font-style: italic; white-space: nowrap;">Not Registered</span>
                                                @endif
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <div style="font-weight: 600; color: #1e293b; white-space: nowrap;">{{ $tLead->emp_name ?? 'Counselor' }}</div>
                                                <div style="font-size: 0.72rem; color: #0284c7; font-weight: 600; white-space: nowrap;">Emp: {{ $tLead->empcode }}</div>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <div style="font-size: 0.8rem; color: #334155; font-weight: 600; white-space: nowrap;">{{ $tLead->assigndatetime ? date('d M Y, h:i A', strtotime($tLead->assigndatetime)) : 'N/A' }}</div>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                @if($tLead->call_count > 0)
                                                    <span style="display: inline-block; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; white-space: nowrap;">
                                                        {{ $tLead->latest_stage }}
                                                    </span>
                                                @else
                                                    <span style="display: inline-block; padding: 0.2rem 0.65rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700; background: #fef3c7; color: #92400e; border: 1px solid #fde68a; white-space: nowrap;">
                                                        Pending Call
                                                    </span>
                                                @endif
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <span style="font-size: 0.82rem; font-weight: 700; color: #1e293b; white-space: nowrap;">{{ $tLead->call_count }} call(s)</span>
                                            </td>
                                            <td style="max-width: 280px; white-space: nowrap;">
                                                <span style="font-size: 0.78rem; color: #475569; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $tLead->latest_remark }}">
                                                    {{ $tLead->latest_remark }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" style="text-align: center; padding: 2.5rem; color: #64748b; font-weight: 600;">No records found</td>
                                        </tr>
                                    @endforelse
                                    <tr id="telecallerNoRowsFound" style="display: none;">
                                        <td colspan="8" style="text-align: center; padding: 2.5rem; color: #64748b; font-weight: 600;">No records found</td>
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
        completed: {{ $totalAllStepsCompleted }},
        not_completed: {{ $totalAllStepsNotCompleted }},
        stages: {!! json_encode($teleTaskStages) !!},
        lead_stages: {!! json_encode($globalLeadStages ?? []) !!}
    },
    @foreach(($telecallerStats ?? []) as $tStat)
    '{{ $tStat->emp_code }}': {
        emp_code: '{{ $tStat->emp_code }}',
        name: '{{ addslashes($tStat->name) }}',
        allotted: {{ $tStat->leads_count }},
        tasks: {{ $tStat->tasks_count }},
        contacted: {{ $tStat->contacted_count }},
        pending: {{ $tStat->pending_count }},
        completed: {{ $tStat->allocated_completed ?? 0 }},
        not_completed: {{ $tStat->allocated_not_completed ?? 0 }},
        stages: {!! json_encode($tStat->stages) !!},
        lead_stages: {!! json_encode($tStat->lead_stages ?? []) !!}
    },
    @endforeach
};

const allTelecallerStats = [
    @foreach(($telecallerStats ?? []) as $tStat)
    {
        emp_code: '{{ $tStat->emp_code }}',
        name: '{{ addslashes($tStat->name) }}',
        allotted: {{ (int)$tStat->leads_count }},
        tasks: {{ (int)$tStat->tasks_count }},
        contacted: {{ (int)$tStat->contacted_count }},
        pending: {{ (int)$tStat->pending_count }},
        completed: {{ (int)($tStat->allocated_completed ?? 0) }},
        not_completed: {{ (int)($tStat->allocated_not_completed ?? 0) }}
    },
    @endforeach
];

const allTasksData = {!! json_encode($allTasksData ?? []) !!};

let telecallerBarChart = null;
let leadStagesDonutChart = null;
let callsMadeBarChart = null;

function toggleTelecallerView(view) {
    const chartView = document.getElementById('telecallerChartView');
    const tableView = document.getElementById('telecallerTableView');
    const btnChart = document.getElementById('btnShowBarChart');
    const btnTable = document.getElementById('btnShowTable');

    if (!chartView || !tableView) return;

    if (view === 'table') {
        chartView.style.display = 'none';
        tableView.style.display = 'block';
        if (btnChart) {
            btnChart.style.background = 'transparent';
            btnChart.style.color = '#64748b';
            btnChart.style.fontWeight = '600';
            btnChart.style.boxShadow = 'none';
        }
        if (btnTable) {
            btnTable.style.background = '#ffffff';
            btnTable.style.color = '#0f172a';
            btnTable.style.fontWeight = '700';
            btnTable.style.boxShadow = '0 1px 2px rgba(0,0,0,0.05)';
        }
    } else {
        tableView.style.display = 'none';
        chartView.style.display = 'flex';
        if (btnChart) {
            btnChart.style.background = '#ffffff';
            btnChart.style.color = '#0f172a';
            btnChart.style.fontWeight = '700';
            btnChart.style.boxShadow = '0 1px 2px rgba(0,0,0,0.05)';
        }
        if (btnTable) {
            btnTable.style.background = 'transparent';
            btnTable.style.color = '#64748b';
            btnTable.style.fontWeight = '600';
            btnTable.style.boxShadow = 'none';
        }
        if (telecallerBarChart) {
            telecallerBarChart.resize();
        }
    }
}

function updateTelecallerBarChart(empCode) {
    const canvas = document.getElementById('telecallerBarChart');
    const emptyEl = document.getElementById('telecallerBarEmpty');
    if (!canvas) return;

    if (empCode === 'all') {
        // Show comparison of telecallers with leads (or all if none)
        let activeList = allTelecallerStats.filter(s => s.allotted > 0);
        if (activeList.length === 0) activeList = allTelecallerStats.slice(0, 8);

        if (activeList.length === 0) {
            canvas.style.display = 'none';
            if (emptyEl) emptyEl.style.display = 'block';
            if (telecallerBarChart) {
                telecallerBarChart.destroy();
                telecallerBarChart = null;
            }
            return;
        }

        canvas.style.display = 'block';
        if (emptyEl) emptyEl.style.display = 'none';

        const labels = activeList.map(s => s.name);
        const allottedData = activeList.map(s => s.allotted);
        const completedData = activeList.map(s => s.completed);
        const incompletedData = activeList.map(s => s.not_completed);

        const chartConfig = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Allotted Leads',
                        data: allottedData,
                        backgroundColor: '#3b82f6',
                        borderRadius: 6,
                        maxBarThickness: 28
                    },
                    {
                        label: 'Completed Steps',
                        data: completedData,
                        backgroundColor: '#10b981',
                        borderRadius: 6,
                        maxBarThickness: 28
                    },
                    {
                        label: 'Incompleted Leads',
                        data: incompletedData,
                        backgroundColor: '#f59e0b',
                        borderRadius: 6,
                        maxBarThickness: 28
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            padding: 10,
                            font: { size: 11, family: 'Plus Jakarta Sans', weight: '600' },
                            color: '#334155'
                        }
                    },
                    tooltip: {
                        padding: 10,
                        callbacks: {
                            label: function(ctx) {
                                return ` ${ctx.dataset.label}: ${ctx.parsed.y} leads`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, family: 'Plus Jakarta Sans', weight: '600' },
                            color: '#475569'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 10, family: 'JetBrains Mono' },
                            color: '#64748b'
                        },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        };

        if (telecallerBarChart) {
            telecallerBarChart.destroy();
        }
        telecallerBarChart = new Chart(canvas, chartConfig);
    } else {
        // Single Counselor View: Breakdown of metrics for the selected telecaller
        const data = telecallerData[empCode] || telecallerData['all'];
        if (!data || Number(data.allotted) === 0) {
            canvas.style.display = 'none';
            if (emptyEl) {
                emptyEl.style.display = 'block';
                emptyEl.querySelector('div').textContent = 'No leads allotted to ' + (data ? data.name : 'this telecaller');
            }
            if (telecallerBarChart) {
                telecallerBarChart.destroy();
                telecallerBarChart = null;
            }
            return;
        }

        canvas.style.display = 'block';
        if (emptyEl) emptyEl.style.display = 'none';

        const labels = ['Allotted Leads', 'Completed Steps', 'Incompleted Leads'];
        const values = [
            Number(data.allotted || 0),
            Number(data.completed || 0),
            Number(data.not_completed || 0)
        ];
        const barColors = ['#3b82f6', '#10b981', '#f59e0b'];

        const chartConfig = {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: data.name,
                    data: values,
                    backgroundColor: barColors,
                    borderRadius: 6,
                    maxBarThickness: 38
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        padding: 10,
                        callbacks: {
                            label: function(ctx) {
                                return ` ${ctx.label}: ${ctx.parsed.y} leads`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            font: { size: 11, family: 'Plus Jakarta Sans', weight: '600' },
                            color: '#475569'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 10, family: 'JetBrains Mono' },
                            color: '#64748b'
                        },
                        grid: { color: '#f1f5f9' }
                    }
                }
            }
        };

        if (telecallerBarChart) {
            telecallerBarChart.destroy();
        }
        telecallerBarChart = new Chart(canvas, chartConfig);
    }
}

function updateLeadStagesChart(data) {
    const canvas = document.getElementById('leadStagesDonutChart');
    const emptyEl = document.getElementById('leadStagesEmpty');
    const listEl = document.getElementById('leadStagesList');
    const badgeEl = document.getElementById('leadStagesAllottedBadge');
    if (!canvas) return;

    const allotted = Number(data.allotted || 0);

    if (badgeEl) {
        badgeEl.textContent = `${allotted.toLocaleString()} Allotted Lead${allotted === 1 ? '' : 's'}`;
    }

    // Lead stages array from controller: exact allotted leads grouped by their current stage
    let stageItems = Array.isArray(data.lead_stages) ? [...data.lead_stages] : [];

    // Fallback if lead_stages is empty but allotted > 0
    if (stageItems.length === 0 && allotted > 0) {
        const pending = Number(data.pending || allotted);
        if (pending > 0) stageItems.push({ stage_name: 'Pending Call', count: pending });
        if (Array.isArray(data.stages)) {
            data.stages.forEach(st => {
                if (Number(st.count || 0) > 0) {
                    stageItems.push({ stage_name: st.stage_name, count: Number(st.count) });
                }
            });
        }
    }

    if (allotted === 0 || stageItems.length === 0) {
        canvas.style.display = 'none';
        if (emptyEl) emptyEl.style.display = 'block';
        if (listEl) listEl.innerHTML = '';
        if (leadStagesDonutChart) {
            leadStagesDonutChart.destroy();
            leadStagesDonutChart = null;
        }
        return;
    }

    canvas.style.display = 'block';
    if (emptyEl) emptyEl.style.display = 'none';

    // Palette for distinct stages
    const palette = ['#f59e0b', '#0284c7', '#10b981', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b', '#3b82f6'];
    const colorMap = {
        'pending call': '#f59e0b',
        'pending first call': '#f59e0b',
        'telecaller stage': '#0284c7',
        'register form completed': '#10b981',
        'academic details completed': '#8b5cf6',
        'exam slot booked': '#ec4899',
    };

    const chartLabels = [];
    const chartCounts = [];
    const chartColors = [];

    stageItems.forEach((item, idx) => {
        const name = item.stage_name || 'Stage Logged';
        const cnt = Number(item.count || 0);
        const lower = name.toLowerCase().trim();
        const color = colorMap[lower] || palette[idx % palette.length];

        chartLabels.push(name);
        chartCounts.push(cnt);
        chartColors.push(color);
        item.assignedColor = color;
        item.displayName = name;
        item.cleanCount = cnt;
    });

    // Populate stage items list below donut (proper, clear, exact counts, NO percentages)
    if (listEl) {
        listEl.innerHTML = stageItems.map(item => `
            <div style="display: flex; align-items: flex-start; justify-content: space-between; padding: 0.35rem 0.5rem; font-size: 0.8rem; border-radius: 7px; background: #f8fafc; border: 1px solid #f1f5f9;">
                <div style="display: flex; align-items: flex-start; gap: 8px; min-width: 0;">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: ${item.assignedColor}; flex-shrink: 0; margin-top: 3px;"></span>
                    <span style="font-weight: 600; color: #334155; line-height: 1.35; word-break: break-word;">${item.displayName}</span>
                </div>
                <span style="font-weight: 700; font-family: 'JetBrains Mono', monospace; color: #0f172a; background: #ffffff; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px; font-size: 0.76rem; flex-shrink: 0; margin-left: 8px; white-space: nowrap;">
                    ${item.cleanCount} lead${item.cleanCount === 1 ? '' : 's'}
                </span>
            </div>
        `).join('');
    }

    const chartData = {
        labels: chartLabels,
        datasets: [{
            data: chartCounts,
            backgroundColor: chartColors,
            borderWidth: 2,
            borderColor: '#ffffff',
            hoverOffset: 4
        }]
    };

    if (leadStagesDonutChart) {
        leadStagesDonutChart.destroy();
        leadStagesDonutChart = null;
    }

    leadStagesDonutChart = new Chart(canvas, {
        type: 'doughnut',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    display: false // Display full list clearly below donut to prevent truncation and text mixing
                },
                tooltip: {
                    padding: 9,
                    boxPadding: 4,
                    callbacks: {
                        title: function(items) {
                            return items[0]?.label || '';
                        },
                        label: function(ctx) {
                            const val = ctx.parsed;
                            return ` ${val} lead${val === 1 ? '' : 's'}`;
                        }
                    }
                }
            }
        }
    });
}

function initCallsMadeFilters() {
    const yearSelect = document.getElementById('callYearFilter');
    const monthSelect = document.getElementById('callMonthFilter');
    if (!yearSelect) return;

    // Collect available years from tasks data
    const years = Array.from(new Set(allTasksData.map(t => Number(t.year)))).filter(Boolean).sort((a,b) => b - a);
    const currentYear = new Date().getFullYear();
    if (!years.includes(currentYear)) years.unshift(currentYear);

    yearSelect.innerHTML = '<option value="all">All Years</option>' + 
        years.map(y => `<option value="${y}" ${y === currentYear ? 'selected' : ''}>${y}</option>`).join('');

    // If monthSelect is present, check if November or latest active month should be selected
    if (monthSelect) {
        const activeMonths = Array.from(new Set(allTasksData.map(t => Number(t.month)))).filter(Boolean);
        if (activeMonths.length > 0 && !activeMonths.includes(Number(monthSelect.value))) {
            monthSelect.value = String(activeMonths[0]);
        }
    }

    updateCallDateDropdown();
}

function updateCallDateDropdown() {
    const dateSelect = document.getElementById('callDateFilter');
    if (!dateSelect) return;

    const currentVal = dateSelect.value;
    const yearVal = document.getElementById('callYearFilter')?.value || 'all';
    const monthVal = document.getElementById('callMonthFilter')?.value || 'all';
    const dropdown = document.getElementById('telecallerDropdownFilter');
    const selectedEmp = dropdown ? dropdown.value : 'all';

    let filtered = allTasksData;
    if (selectedEmp !== 'all') filtered = filtered.filter(t => t.empcode === selectedEmp);
    if (yearVal !== 'all') filtered = filtered.filter(t => t.year === Number(yearVal));
    if (monthVal !== 'all') filtered = filtered.filter(t => t.month === Number(monthVal));

    const distinctDays = Array.from(new Set(filtered.map(t => Number(t.day)))).filter(Boolean).sort((a,b) => a - b);

    let optionsHtml = '<option value="all">All Dates</option>';
    distinctDays.forEach(d => {
        optionsHtml += `<option value="${d}">${d}</option>`;
    });
    dateSelect.innerHTML = optionsHtml;

    if (distinctDays.includes(Number(currentVal))) {
        dateSelect.value = currentVal;
    } else {
        dateSelect.value = 'all';
    }
}

function onCallFilterChange(changedType) {
    if (changedType !== 'date') {
        updateCallDateDropdown();
    }
    renderCallsMadeChart();
}

function renderCallsMadeChart() {
    const canvas = document.getElementById('callsMadeBarChart');
    const chartWrap = document.getElementById('callsMadeChartWrap');
    const emptyEl = document.getElementById('callsMadeEmpty');
    const badge = document.getElementById('callsMadeCountBadge');
    if (!canvas) return;

    const dropdown = document.getElementById('telecallerDropdownFilter');
    const selectedEmp = dropdown ? dropdown.value : 'all';

    const yearVal = document.getElementById('callYearFilter')?.value || 'all';
    const monthVal = document.getElementById('callMonthFilter')?.value || 'all';
    const dayVal = document.getElementById('callDateFilter')?.value || 'all';

    // Filter tasks by Telecaller, Year, Month, Day
    const filtered = allTasksData.filter(t => {
        const matchEmp = (selectedEmp === 'all' || t.empcode === selectedEmp);
        const matchYear = (yearVal === 'all' || t.year === Number(yearVal));
        const matchMonth = (monthVal === 'all' || t.month === Number(monthVal));
        const matchDay = (dayVal === 'all' || t.day === Number(dayVal));
        return matchEmp && matchYear && matchMonth && matchDay;
    });

    if (badge) {
        badge.textContent = `${filtered.length} Call${filtered.length === 1 ? '' : 's'} Made`;
    }

    if (filtered.length === 0) {
        if (chartWrap) chartWrap.style.display = 'none';
        canvas.style.display = 'none';
        if (emptyEl) {
            emptyEl.style.display = 'flex';
        }
        if (callsMadeBarChart) {
            callsMadeBarChart.destroy();
            callsMadeBarChart = null;
        }
        return;
    }

    if (emptyEl) emptyEl.style.display = 'none';
    if (chartWrap) chartWrap.style.display = 'flex';
    canvas.style.display = 'block';

    let labels = [];
    let dataCounts = [];
    let datasetLabel = 'Calls Made';

    if (dayVal === 'all') {
        // Group by Date (e.g. 12 Nov, 13 Nov, 14 Nov, 18 Nov)
        const dateGroups = {};
        const sorted = [...filtered].sort((a,b) => a.date_str.localeCompare(b.date_str));
        sorted.forEach(t => {
            const key = t.date_label || t.date_str;
            dateGroups[key] = (dateGroups[key] || 0) + 1;
        });
        labels = Object.keys(dateGroups);
        dataCounts = Object.values(dateGroups);
        datasetLabel = 'Calls on Date';
    } else {
        // Specific day selected: group by stage of calls made on that day
        const stageGroups = {};
        filtered.forEach(t => {
            const key = t.stage || 'Call Logged';
            stageGroups[key] = (stageGroups[key] || 0) + 1;
        });
        labels = Object.keys(stageGroups);
        dataCounts = Object.values(stageGroups);
        datasetLabel = `Calls on Day ${dayVal}`;
    }

    const chartConfig = {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: datasetLabel,
                data: dataCounts,
                backgroundColor: '#0284c7',
                borderRadius: 6,
                maxBarThickness: 34
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    padding: 10,
                    callbacks: {
                        label: function(ctx) {
                            return ` ${ctx.label}: ${ctx.parsed.y} call(s)`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { size: 10, family: 'Plus Jakarta Sans', weight: '600' },
                        color: '#475569'
                    }
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        font: { size: 10, family: 'JetBrains Mono' },
                        color: '#64748b'
                    },
                    grid: { color: '#f1f5f9' }
                }
            }
        }
    };

    if (callsMadeBarChart) {
        callsMadeBarChart.destroy();
    }
    callsMadeBarChart = new Chart(canvas, chartConfig);
}

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

    // 2. Update All 3 Graphs
    updateTelecallerBarChart(empCode);
    updateLeadStagesChart(data);
    updateCallDateDropdown();
    renderCallsMadeChart();

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
    
    // Initialize Year, Month, Date dropdown filters
    initCallsMadeFilters();

    // Initialize all 3 graphs with initial selection data
    const initialData = telecallerData[initialEmp] || telecallerData['all'];
    updateTelecallerBarChart(initialEmp);
    updateLeadStagesChart(initialData);
    renderCallsMadeChart();

    if (initialEmp !== 'all') {
        handleTelecallerChange(initialEmp);
    }
});
</script>
</body>
</html>
