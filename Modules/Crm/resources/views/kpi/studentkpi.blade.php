<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student KPI &amp; Conversion Analytics - VFSTR Admissions CRM</title>
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

        /* KPI Responsive Layout & Grid Styles */
        .kpi-metrics-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .kpi-charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(440px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.75rem;
        }

        @media (max-width: 1200px) {
            .kpi-metrics-grid {
                grid-template-columns: repeat(3, 1fr) !important;
            }
        }

        @media (max-width: 768px) {
            .kpi-charts-grid {
                grid-template-columns: 1fr !important;
                gap: 1rem !important;
            }
            .kpi-charts-grid > div {
                padding: 1rem !important;
            }
            .card-header-bar {
                padding: 0.85rem 1rem !important;
                gap: 0.65rem !important;
            }
            .card-title {
                font-size: 1rem !important;
            }
            .kpi-content-padding {
                padding: 0.85rem 0.65rem !important;
            }
            #kpiFilterForm {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            #kpiFilterForm > div {
                width: 100% !important;
                min-width: 100% !important;
            }
            #kpiResetBtn {
                width: 100% !important;
                text-align: center !important;
            }
        }

        @media (max-width: 640px) {
            .kpi-metrics-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 0.5rem !important;
            }
            .kpi-metrics-grid > div {
                padding: 0.65rem 0.75rem !important;
            }
            .kpi-metrics-grid > div > div:first-child {
                font-size: 0.65rem !important;
            }
            .kpi-metrics-grid > div > div:nth-child(2) {
                font-size: 1.25rem !important;
            }
            .kpi-metrics-grid > div > div:nth-child(3) {
                font-size: 0.65rem !important;
            }
        }
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
                <div class="topbar-page-breadcrumb">
                    <span class="breadcrumb-item breadcrumb-desktop-only">KPI</span>
                    <span class="breadcrumb-separator breadcrumb-desktop-only">/</span>
                    <span class="breadcrumb-active-tag">
                        <span class="breadcrumb-dot"></span>
                        <span class="breadcrumb-lead-full">Student KPI &bull; Conversion Analytics</span>
                        <span class="breadcrumb-lead-mobile">Student KPI</span>
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

            <!-- TAB: STUDENT KPI & CONVERSION ANALYTICS -->
            <div class="content-card">
                <div class="card-header-bar" style="padding: 1.15rem 1.5rem; border-bottom: 1px solid #e2e8f0; background: #ffffff; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                    <h2 class="card-title" style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--theme-accent, #059669);"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                        <span>Student KPI &amp; Conversion Analytics</span>
                    </h2>
                    <button type="button" 
                            id="exportStudentKpiTopBtn"
                            onclick="exportStudentKpiExcel()" 
                            class="btn-theme-export"
                            style="height: 36px; display: inline-flex; align-items: center; gap: 0.45rem; padding: 0 1.15rem; background: var(--theme-accent, #059669); color: #ffffff; border: none; border-radius: 8px; font-size: 0.825rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: all 0.2s; box-shadow: 0 1px 3px var(--theme-accent-ring, rgba(5, 150, 105, 0.25));">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        <span>Export Excel</span>
                    </button>
                </div>

                <div class="kpi-content-padding" style="padding: 1.5rem;">
                    <!-- 1. TOP EXECUTIVE METRIC CARDS (COMPACT SINGLE ROW, NO ICONS) -->
                    <div class="kpi-metrics-grid">
                        <!-- Card 1: Total Registered -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 0.85rem; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Total Applicants</div>
                            <div style="font-size: 1.35rem; font-weight: 800; color: #1e293b; margin: 0.15rem 0 0.1rem 0;">{{ $totalApplicants }}</div>
                            <div style="font-size: 0.68rem; color: #0284c7; font-weight: 600; white-space: nowrap;">100% Pipeline Inflow</div>
                        </div>

                        <!-- Card 2: Stage 1 Personal Profile -->
                        <div style="border: 1px solid #bfdbfe; border-radius: 10px; padding: 0.75rem 0.85rem; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Stage 1 Personal Profile</div>
                            <div style="font-size: 1.35rem; font-weight: 800; color: #2563eb; margin: 0.15rem 0 0.1rem 0;">{{ $kpiStage1Profile }}</div>
                            <div style="font-size: 0.68rem; color: #2563eb; font-weight: 600; white-space: nowrap;">{{ round(($kpiStage1Profile / $kpiTotal) * 100, 1) }}% Completed</div>
                        </div>

                        <!-- Card 3: Stage 2 Academic Info -->
                        <div style="border: 1px solid #ddd6fe; border-radius: 10px; padding: 0.75rem 0.85rem; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Stage 2 Academic Info</div>
                            <div style="font-size: 1.35rem; font-weight: 800; color: #7c3aed; margin: 0.15rem 0 0.1rem 0;">{{ $kpiStage2Academic }}</div>
                            <div style="font-size: 0.68rem; color: #7c3aed; font-weight: 600; white-space: nowrap;">{{ round(($kpiStage2Academic / $kpiTotal) * 100, 1) }}% Completed</div>
                        </div>

                        <!-- Card 4: Stage 3 Document Upload -->
                        <div style="border: 1px solid #fed7aa; border-radius: 10px; padding: 0.75rem 0.85rem; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Stage 3 Document Upload</div>
                            <div style="font-size: 1.35rem; font-weight: 800; color: #ea580c; margin: 0.15rem 0 0.1rem 0;">{{ $kpiStage3Docs }}</div>
                            <div style="font-size: 0.68rem; color: #ea580c; font-weight: 600; white-space: nowrap;">{{ round(($kpiStage3Docs / $kpiTotal) * 100, 1) }}% Uploaded</div>
                        </div>

                        <!-- Card 5: Stage 4 Fee Payment -->
                        <div style="border: 1px solid #a7f3d0; border-radius: 10px; padding: 0.75rem 0.85rem; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Stage 4 Fee Payment</div>
                            <div style="font-size: 1.35rem; font-weight: 800; color: #059669; margin: 0.15rem 0 0.1rem 0;">{{ $kpiStage4Fee }}</div>
                            <div style="font-size: 0.68rem; color: #059669; font-weight: 600; white-space: nowrap;">{{ round(($kpiStage4Fee / $kpiTotal) * 100, 1) }}% Fee Paid</div>
                        </div>

                        <!-- Card 6: Stage 5 Exam Slot Booking -->
                        <div style="border: 1px solid #99f6e4; border-radius: 10px; padding: 0.75rem 0.85rem; background: #ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.68rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.03em; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Stage 5 Slot Booking</div>
                            <div style="font-size: 1.35rem; font-weight: 800; color: #0d9488; margin: 0.15rem 0 0.1rem 0;">{{ $kpiStage5Slot }}</div>
                            <div style="font-size: 0.68rem; color: #0d9488; font-weight: 600; white-space: nowrap;">{{ round(($kpiStage5Slot / $kpiTotal) * 100, 1) }}% Slots Booked</div>
                        </div>
                    </div>

                    <!-- 2. VISUAL CHARTS (5-STAGE CONVERSION FUNNEL & TOP BRANCH DISTRIBUTION) -->
                    <div class="kpi-charts-grid">
                        <!-- Chart 1: 5-Stage Conversion Funnel -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.5rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">5-Stage Student Conversion Funnel</div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Progression from registration through exam slot booking</div>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 0.25rem 0.65rem; border-radius: 20px; border: 1px solid #dbeafe;">Full Funnel</span>
                            </div>
                            <div style="position: relative; flex-grow: 1; min-height: 280px; max-height: 320px;">
                                <canvas id="studentFunnelChart"></canvas>
                            </div>
                        </div>

                        <!-- Chart 2: Top Branch Distribution -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.5rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Top Branch Intake Distribution</div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Applicant preference across academic engineering branches</div>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; color: #7c3aed; background: #f5f3ff; padding: 0.25rem 0.65rem; border-radius: 20px; border: 1px solid #ddd6fe;">Branch Demand</span>
                            </div>
                            <div style="position: relative; flex-grow: 1; min-height: 280px; max-height: 320px;">
                                <canvas id="branchDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 3. LIVE SEARCH & STAGE FILTER BAR (NO BRANCH DROPDOWN, REAL-TIME FILTERING) -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 1.5rem; padding: 1rem 1.25rem;">
                        <form id="kpiFilterForm" method="GET" action="{{ route('kpi.studentkpi') }}" onsubmit="event.preventDefault(); window.handleKpiLiveFilter(true);" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                            <input type="hidden" name="tab" value="studentkpi">
                            
                            <!-- Search query (Live debounced) -->
                            <div style="flex: 1; min-width: 260px; position: relative;">
                                <input type="text" 
                                       id="kpiLiveSearch" 
                                       name="q" 
                                       value="{{ $search }}" 
                                       placeholder="Search by name, application #, phone, email..." 
                                       autocomplete="off"
                                       oninput="window.handleKpiLiveFilter()"
                                       style="width: 100%; padding: 0.55rem 2rem 0.55rem 2.25rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; transition: all 0.2s;">
                                <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8;" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <button type="button" 
                                        id="kpiClearSearchBtn" 
                                        onclick="window.clearKpiSearch()" 
                                        style="display: {{ $search ? 'block' : 'none' }}; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; font-size: 1.15rem; cursor: pointer; line-height: 1; padding: 0;">&times;</button>
                            </div>

                            <!-- Stage Filter (Live onchange) -->
                            <div style="min-width: 220px;">
                                <select id="kpiStageSelect" 
                                        name="stage" 
                                        onchange="window.handleKpiLiveFilter(true)"
                                        style="width: 100%; padding: 0.55rem 0.85rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; outline: none;">
                                    <option value="">All Funnel Stages</option>
                                    <option value="stage1" {{ $filterStage === 'stage1' ? 'selected' : '' }}>Stage 1: Personal Profile</option>
                                    <option value="stage2" {{ $filterStage === 'stage2' ? 'selected' : '' }}>Stage 2: Academic Info</option>
                                    <option value="stage3" {{ $filterStage === 'stage3' ? 'selected' : '' }}>Stage 3: Document Upload</option>
                                    <option value="stage4" {{ $filterStage === 'stage4' ? 'selected' : '' }}>Stage 4: Fee Payment</option>
                                    <option value="stage5" {{ $filterStage === 'stage5' ? 'selected' : '' }}>Stage 5: Exam Slot Booking</option>
                                </select>
                            </div>

                            <!-- Live Filter Active Spinner / Indicator -->
                            <div id="kpiLiveIndicator" style="display: none; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: #0284c7; font-weight: 600;">
                                <span style="display: inline-block; width: 7px; height: 7px; background: #0284c7; border-radius: 50%;"></span>
                                <span>Updating...</span>
                            </div>

                            <!-- Reset Button -->
                            <button type="button" 
                                    id="kpiResetBtn" 
                                    onclick="window.resetKpiFilters()" 
                                    style="padding: 0.55rem 1rem; font-size: 0.85rem; font-weight: 600; color: #64748b; background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 8px; cursor: pointer; display: {{ ($search || $filterStage) ? 'inline-block' : 'none' }};">
                                Reset
                            </button>
                        </form>
                    </div>

                    <!-- 4. EAGER-LOADED STUDENT REGISTRATIONS TABLE (LIVE-REPLACED VIA AJAX) -->
                    <div id="studentTableContainer" style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                            <div>
                                <span style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Verified Student Records</span>
                                <span id="studentTableCountText" style="font-size: 0.75rem; color: #64748b; margin-left: 0.5rem;">(Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} records)</span>
                            </div>
                            <button type="button" 
                                    id="exportStudentKpiTableBtn"
                                    onclick="exportStudentKpiExcel()" 
                                    class="btn-theme-export"
                                    style="height: 32px; display: inline-flex; align-items: center; gap: 0.4rem; padding: 0 0.95rem; background: var(--theme-accent, #059669); color: #ffffff; border: none; border-radius: 7px; font-size: 0.785rem; font-weight: 700; cursor: pointer; white-space: nowrap; transition: all 0.2s; box-shadow: 0 1px 2px var(--theme-accent-ring, rgba(5, 150, 105, 0.2));">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                <span>Export Excel</span>
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="crm-table" id="studentRecordsTable">
                                <thead>
                                    <tr>
                                        <th style="width: 35%;">Application Number</th>
                                        <th style="width: 35%;">Mobile Number</th>
                                        <th style="width: 30%;">Current Step</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $st)
                                        @php
                                            $appNo = (string)$st->applinano;
                                            $isStage1 = in_array(strtolower(trim((string)$st->stage1)), ['completed', 'approved', 'done']) || $st->personalInfo;
                                            $isStage2 = in_array(strtolower(trim((string)$st->stage2)), ['completed', 'approved', 'done']) || $st->academicDetails;
                                            $isStage3 = in_array(strtolower(trim((string)$st->stage3)), ['completed', 'approved', 'done']) || $st->uploads;
                                            $isStage4 = in_array(strtolower(trim((string)$st->stage4)), ['paid', 'completed']) || ($st->stage1 === 'paid') || ($st->payDetail && ($st->payDetail->txnstatus === 'success' || $st->payDetail->paidamount > 0)) || $st->receipt || $verifiedPaidAppNos->contains($appNo);
                                            $isStage5 = in_array(strtolower(trim((string)$st->stage5)), ['completed', 'approved', 'done']) || $st->examSchedule;

                                            if ($isStage5) {
                                                $stageTitle = 'Stage 5: Exam Slot Booked';
                                                $badgeColor = '#059669';
                                                $badgeBg = '#ecfdf5';
                                                $badgeBorder = '#a7f3d0';
                                            } elseif ($isStage4) {
                                                $stageTitle = 'Stage 5: Exam Slot Booking';
                                                $badgeColor = '#0d9488';
                                                $badgeBg = '#f0fdfa';
                                                $badgeBorder = '#99f6e4';
                                            } elseif ($isStage3) {
                                                $stageTitle = 'Stage 4: Fee Payment';
                                                $badgeColor = '#b45309';
                                                $badgeBg = '#fef3c7';
                                                $badgeBorder = '#fde68a';
                                            } elseif ($isStage2) {
                                                $stageTitle = 'Stage 3: Document Upload';
                                                $badgeColor = '#ea580c';
                                                $badgeBg = '#fff7ed';
                                                $badgeBorder = '#fed7aa';
                                            } elseif ($isStage1) {
                                                $stageTitle = 'Stage 2: Academic Info';
                                                $badgeColor = '#7c3aed';
                                                $badgeBg = '#f5f3ff';
                                                $badgeBorder = '#ddd6fe';
                                            } else {
                                                $stageTitle = 'Stage 1: Personal Profile';
                                                $badgeColor = '#2563eb';
                                                $badgeBg = '#eff6ff';
                                                $badgeBorder = '#bfdbfe';
                                            }
                                        @endphp
                                        <tr class="student-row"
                                            data-stage1="{{ $isStage1 ? '1' : '0' }}"
                                            data-stage2="{{ $isStage2 ? '1' : '0' }}"
                                            data-stage3="{{ $isStage3 ? '1' : '0' }}"
                                            data-stage4="{{ $isStage4 ? '1' : '0' }}"
                                            data-stage5="{{ $isStage5 ? '1' : '0' }}">
                                            <!-- Column 1: Application Number -->
                                            <td>
                                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 700; color: #0284c7;">
                                                    {{ $st->applinano }}
                                                </span>
                                            </td>

                                            <!-- Column 2: Mobile Number -->
                                            <td>
                                                <span style="font-weight: 600; color: #1e293b; font-size: 0.85rem; font-family: 'JetBrains Mono', monospace;">
                                                    {{ $st->phoneno ?: '—' }}
                                                </span>
                                            </td>

                                            <!-- Column 3: Current Step -->
                                            <td>
                                                <span style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 4px 10px; border-radius: 9999px; font-size: 0.775rem; font-weight: 700; color: {{ $badgeColor }}; background: {{ $badgeBg }}; border: 1px solid {{ $badgeBorder }};">
                                                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: {{ $badgeColor }};"></span>
                                                    <span>{{ $stageTitle }}</span>
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" style="text-align: center; padding: 2.5rem 1.5rem; color: #94a3b8;">
                                                <div style="font-size: 1.5rem; margin-bottom: 0.35rem;">🔍</div>
                                                <div style="font-size: 0.9rem; font-weight: 600; color: #64748b;">No matching student registrations found</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                    <tr id="kpiClientNoMatchesRow" style="display: none;">
                                        <td colspan="3" style="text-align: center; padding: 2.5rem 1.5rem; color: #94a3b8;">
                                            <div style="font-size: 1.5rem; margin-bottom: 0.35rem;">🔍</div>
                                            <div style="font-size: 0.9rem; font-weight: 600; color: #64748b;">No matching student registrations found</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer -->
                        @if($students->hasPages())
                            <div class="table-pagination-area">
                                <div style="font-size: 0.825rem; color: #64748b;">
                                    Showing <strong style="color: #1e293b;">{{ $students->firstItem() ?? 0 }}</strong> to <strong style="color: #1e293b;">{{ $students->lastItem() ?? 0 }}</strong> of <strong style="color: #1e293b;">{{ number_format($students->total()) }}</strong> records ({{ $students->perPage() }} per page)
                                </div>
                                <div class="pagination-wrapper">
                                    {{ $students->links('pagination::bootstrap-4') }}
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
    // 1. Chart.js Funnel Progression Chart
    const funnelCtx = document.getElementById('studentFunnelChart');
    if (funnelCtx) {
        new Chart(funnelCtx, {
            type: 'bar',
            data: {
                labels: [
                    'Total Registered',
                    'Stage 1: Personal Profile',
                    'Stage 2: Academic Info',
                    'Stage 3: Document Upload',
                    'Stage 4: Fee Payment',
                    'Stage 5: Exam Slot Booking'
                ],
                datasets: [{
                    label: 'Candidates Completed',
                    data: [
                        {{ $totalApplicants }},
                        {{ $kpiStage1Profile }},
                        {{ $kpiStage2Academic }},
                        {{ $kpiStage3Docs }},
                        {{ $kpiStage4Fee }},
                        {{ $kpiStage5Slot }}
                    ],
                    backgroundColor: [
                        '#0284c7',
                        '#2563eb',
                        '#7c3aed',
                        '#ea580c',
                        '#059669',
                        '#0d9488'
                    ],
                    borderRadius: 6,
                    borderWidth: 0,
                    barPercentage: 0.6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const total = {{ $kpiTotal }};
                                const val = context.parsed.y;
                                const pct = ((val / total) * 100).toFixed(1);
                                return ` ${val} candidates (${pct}%)`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 2 },
                        grid: { color: '#f1f5f9' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: 'Plus Jakarta Sans' } }
                    }
                }
            }
        });
    }

    // 2. Chart.js Top Branch Distribution Doughnut Chart
    const branchCtx = document.getElementById('branchDistributionChart');
    if (branchCtx) {
        const branchLabels = {!! json_encode($branchDistribution->keys()) !!};
        const branchCounts = {!! json_encode($branchDistribution->values()) !!};

        new Chart(branchCtx, {
            type: 'doughnut',
            data: {
                labels: branchLabels.length ? branchLabels : ['General Engineering'],
                datasets: [{
                    data: branchCounts.length ? branchCounts : [1],
                    backgroundColor: [
                        '#0284c7', '#059669', '#7c3aed', '#ea580c',
                        '#e11d48', '#d97706', '#0891b2', '#4b5563'
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
                            padding: 12
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }

    // Initialize AJAX pagination listener
    attachPaginationAjax();
});

let kpiLiveTimer = null;
window.handleKpiLiveFilter = function (immediate) {
    const searchInput = document.getElementById('kpiLiveSearch');
    const stageSelect = document.getElementById('kpiStageSelect');
    const resetBtn = document.getElementById('kpiResetBtn');
    const clearBtn = document.getElementById('kpiClearSearchBtn');
    const indicator = document.getElementById('kpiLiveIndicator');
    const tableContainer = document.getElementById('studentTableContainer');

    const q = searchInput ? searchInput.value.trim() : '';
    const stage = stageSelect ? stageSelect.value : '';

    if (clearBtn) clearBtn.style.display = q ? 'block' : 'none';
    if (resetBtn) resetBtn.style.display = (q || stage) ? 'inline-block' : 'none';

    // 1. Instant client-side filtering on current rows
    const rows = document.querySelectorAll('#studentRecordsTable tbody tr.student-row');
    const noMatchesRow = document.getElementById('kpiClientNoMatchesRow');
    let visibleCount = 0;
    rows.forEach(row => {
        const text = (row.textContent || '').toLowerCase();
        const matchesQ = !q || text.includes(q.toLowerCase());
        const matchesStage = !stage || (row.getAttribute('data-' + stage) === '1');
        if (matchesQ && matchesStage) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    if (noMatchesRow) {
        noMatchesRow.style.display = (visibleCount === 0 && rows.length > 0) ? '' : 'none';
    }

    // 2. Debounced background fetch across all database pages
    if (indicator) indicator.style.display = 'inline-flex';
    clearTimeout(kpiLiveTimer);

    const delay = immediate ? 0 : 250;
    kpiLiveTimer = setTimeout(() => {
        const url = new URL(window.location.origin + window.location.pathname);
        url.searchParams.set('tab', 'studentkpi');
        if (q) url.searchParams.set('q', q);
        if (stage) url.searchParams.set('stage', stage);

        window.history.replaceState({}, '', url.toString());

        fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.getElementById('studentTableContainer');
            if (newTable && tableContainer) {
                tableContainer.innerHTML = newTable.innerHTML;
                attachPaginationAjax();
            }
        })
        .catch(err => console.error('Live filter fetch error:', err))
        .finally(() => {
            if (indicator) indicator.style.display = 'none';
        });
    }, delay);
};

window.clearKpiSearch = function () {
    const searchInput = document.getElementById('kpiLiveSearch');
    if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
        window.handleKpiLiveFilter(true);
    }
};

window.resetKpiFilters = function () {
    const searchInput = document.getElementById('kpiLiveSearch');
    const stageSelect = document.getElementById('kpiStageSelect');
    if (searchInput) searchInput.value = '';
    if (stageSelect) stageSelect.value = '';
    window.handleKpiLiveFilter(true);
};

window.exportStudentKpiExcel = function () {
    const searchInput = document.getElementById('kpiLiveSearch');
    const stageSelect = document.getElementById('kpiStageSelect');
    const q = searchInput ? searchInput.value.trim() : '';
    const stage = stageSelect ? stageSelect.value : '';

    const url = new URL('{{ route("kpi.studentkpi.export") }}', window.location.origin);
    if (q) url.searchParams.set('q', q);
    if (stage) url.searchParams.set('stage', stage);

    // Fetch the export blob directly so the current page is never redirected
    fetch(url.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if (!res.ok) throw new Error('Export network error: ' + res.status);
        return res.blob();
    })
    .then(blob => {
        const downloadUrl = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = downloadUrl;
        a.download = 'Student_KPI_Report_' + new Date().toISOString().slice(0, 10) + '.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(downloadUrl);
    })
    .catch(err => {
        console.warn('Backend export failed, falling back to instant client-side CSV generator:', err);
        fallbackClientExport();
    });
};

function fallbackClientExport() {
    const headers = ['Application Number', 'Mobile Number', 'Current Step'];
    const rows = [];
    document.querySelectorAll('#studentRecordsTable tbody tr.student-row').forEach(row => {
        if (row.style.display !== 'none') {
            const cells = row.querySelectorAll('td');
            if (cells.length >= 3) {
                const appNo = (cells[0].innerText || '').trim();
                const phone = (cells[1].innerText || '').trim();
                const step = (cells[2].innerText || '').replace(/^[•●✓]\s*/, '').trim();
                rows.push([appNo, phone, step]);
            }
        }
    });

    let csvContent = "\uFEFF";
    csvContent += headers.map(h => `"${h.replace(/"/g, '""')}"`).join(",") + "\r\n";
    rows.forEach(r => {
        csvContent += r.map(c => `"${(c ?? '').toString().replace(/"/g, '""')}"`).join(",") + "\r\n";
    });

    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const downloadUrl = URL.createObjectURL(blob);
    link.href = downloadUrl;
    link.download = 'Student_KPI_Report_' + new Date().toISOString().slice(0, 10) + '.csv';
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(downloadUrl);
}

function attachPaginationAjax() {
    const paginationLinks = document.querySelectorAll('#studentTableContainer .pagination a');
    const indicator = document.getElementById('kpiLiveIndicator');
    const tableContainer = document.getElementById('studentTableContainer');

    paginationLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            if (!href) return;

            if (indicator) indicator.style.display = 'inline-flex';
            window.history.replaceState({}, '', href);

            fetch(href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newTable = doc.getElementById('studentTableContainer');
                if (newTable && tableContainer) {
                    tableContainer.innerHTML = newTable.innerHTML;
                    attachPaginationAjax();
                    tableContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            })
            .catch(err => console.error('Pagination fetch error:', err))
            .finally(() => {
                if (indicator) indicator.style.display = 'none';
            });
        });
    });
}
</script>
</body>
</html>
