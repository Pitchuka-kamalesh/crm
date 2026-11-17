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

            <!-- TAB: STUDENT KPI & CONVERSION ANALYTICS -->
            <div class="content-card">
                <div class="card-header-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 1.25rem 1.5rem; border-bottom: 1px solid #e2e8f0; background: #ffffff;">
                    <div>
                        <div class="card-title" style="font-size: 1.15rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                            <span>🎓 Student KPI &amp; Conversion Analytics</span>
                        </div>
                        <!-- Executive Stage Progress Tracker with Genuine Percentages & Authentic Counts -->
                        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; margin-top: 0.65rem;">
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; background: #f8fafc; border: 1px solid #e2e8f0; padding: 3px 9px; border-radius: 6px;">
                                <span style="font-weight: 700; color: #475569;">Total Enquiries:</span>
                                <strong style="color: #0284c7;">100%</strong>
                                <span style="color: #94a3b8; font-size: 0.7rem;">({{ $totalApplicants }})</span>
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; background: #eff6ff; border: 1px solid #bfdbfe; padding: 3px 9px; border-radius: 6px;">
                                <span style="font-weight: 700; color: #1e40af;">Stage 1 Personal Profile:</span>
                                <strong style="color: #2563eb;">{{ round(($kpiStage1Profile / $kpiTotal) * 100, 1) }}%</strong>
                                <span style="color: #3b82f6; font-size: 0.7rem;">({{ $kpiStage1Profile }})</span>
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; background: #f5f3ff; border: 1px solid #ddd6fe; padding: 3px 9px; border-radius: 6px;">
                                <span style="font-weight: 700; color: #6d28d9;">Stage 2 Academic Info:</span>
                                <strong style="color: #7c3aed;">{{ round(($kpiStage2Academic / $kpiTotal) * 100, 1) }}%</strong>
                                <span style="color: #8b5cf6; font-size: 0.7rem;">({{ $kpiStage2Academic }})</span>
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; background: #fff7ed; border: 1px solid #fed7aa; padding: 3px 9px; border-radius: 6px;">
                                <span style="font-weight: 700; color: #c2410c;">Stage 3 Document Upload:</span>
                                <strong style="color: #ea580c;">{{ round(($kpiStage3Docs / $kpiTotal) * 100, 1) }}%</strong>
                                <span style="color: #f97316; font-size: 0.7rem;">({{ $kpiStage3Docs }})</span>
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 3px 9px; border-radius: 6px;">
                                <span style="font-weight: 700; color: #047857;">Stage 4 Fee Payment:</span>
                                <strong style="color: #059669;">{{ round(($kpiStage4Fee / $kpiTotal) * 100, 1) }}%</strong>
                                <span style="color: #059669; font-size: 0.7rem;">({{ $kpiStage4Fee }})</span>
                            </span>
                            <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.75rem; background: #f0fdfa; border: 1px solid #99f6e4; padding: 3px 9px; border-radius: 6px;">
                                <span style="font-weight: 700; color: #0f766e;">Stage 5 Exam Slot Booking:</span>
                                <strong style="color: #0d9488;">{{ round(($kpiStage5Slot / $kpiTotal) * 100, 1) }}%</strong>
                                <span style="color: #14b8a6; font-size: 0.7rem;">({{ $kpiStage5Slot }})</span>
                            </span>
                        </div>
                    </div>

                    <!-- Header Metadata Badges -->
                    <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                        <span style="font-size: 0.825rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 0.35rem 0.85rem; border-radius: 20px; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <span>📅</span> Intake Cycle 2026–27
                        </span>
                        <span style="font-size: 0.825rem; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.35rem 0.85rem; border-radius: 20px; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <span style="display: inline-block; width: 7px; height: 7px; background: #10b981; border-radius: 50%;"></span>
                            {{ $totalApplicants }} Total Applicants
                        </span>
                    </div>
                </div>

                <div style="padding: 1.5rem;">
                    <!-- 1. TOP EXECUTIVE METRIC CARDS -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 1rem; margin-bottom: 1.75rem;">
                        <!-- Card 1: Total Registered -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Total Applicants</div>
                                <div style="font-size: 1.65rem; font-weight: 800; color: #1e293b; margin: 0.2rem 0;">{{ $totalApplicants }}</div>
                                <div style="font-size: 0.75rem; color: #0284c7; font-weight: 600;">100% Pipeline Inflow</div>
                            </div>
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">👥</div>
                        </div>

                        <!-- Card 2: Stage 1 Personal Profile -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Stage 1 Personal Profile</div>
                                <div style="font-size: 1.65rem; font-weight: 800; color: #2563eb; margin: 0.2rem 0;">{{ $kpiStage1Profile }}</div>
                                <div style="font-size: 0.75rem; color: #2563eb; font-weight: 600;">{{ round(($kpiStage1Profile / $kpiTotal) * 100, 1) }}% Completed</div>
                            </div>
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #eff6ff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">👤</div>
                        </div>

                        <!-- Card 3: Stage 2 Academic Info -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Stage 2 Academic Info</div>
                                <div style="font-size: 1.65rem; font-weight: 800; color: #7c3aed; margin: 0.2rem 0;">{{ $kpiStage2Academic }}</div>
                                <div style="font-size: 0.75rem; color: #7c3aed; font-weight: 600;">{{ round(($kpiStage2Academic / $kpiTotal) * 100, 1) }}% Completed</div>
                            </div>
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #f5f3ff; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">🎓</div>
                        </div>

                        <!-- Card 4: Stage 3 Document Upload -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Stage 3 Document Upload</div>
                                <div style="font-size: 1.65rem; font-weight: 800; color: #ea580c; margin: 0.2rem 0;">{{ $kpiStage3Docs }}</div>
                                <div style="font-size: 0.75rem; color: #ea580c; font-weight: 600;">{{ round(($kpiStage3Docs / $kpiTotal) * 100, 1) }}% Uploaded</div>
                            </div>
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #fff7ed; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">📤</div>
                        </div>

                        <!-- Card 5: Stage 4 Fee Payment -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Stage 4 Fee Payment</div>
                                <div style="font-size: 1.65rem; font-weight: 800; color: #059669; margin: 0.2rem 0;">{{ $kpiStage4Fee }}</div>
                                <div style="font-size: 0.75rem; color: #059669; font-weight: 600;">{{ round(($kpiStage4Fee / $kpiTotal) * 100, 1) }}% Fee Paid</div>
                            </div>
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #ecfdf5; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">💳</div>
                        </div>

                        <!-- Card 6: Stage 5 Exam Slot Booking -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem 1.25rem; background: #ffffff; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div>
                                <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Stage 5 Exam Slot Booking</div>
                                <div style="font-size: 1.65rem; font-weight: 800; color: #0d9488; margin: 0.2rem 0;">{{ $kpiStage5Slot }}</div>
                                <div style="font-size: 0.75rem; color: #0d9488; font-weight: 600;">{{ round(($kpiStage5Slot / $kpiTotal) * 100, 1) }}% Slots Booked</div>
                            </div>
                            <div style="width: 44px; height: 44px; border-radius: 10px; background: #f0fdfa; display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">📅</div>
                        </div>
                    </div>

                    <!-- 2. VISUAL CHARTS (5-STAGE CONVERSION FUNNEL & TOP BRANCH DISTRIBUTION) -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(460px, 1fr)); gap: 1.5rem; margin-bottom: 1.75rem;">
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

                    <!-- 3. INTERACTIVE SEARCH & FILTER BAR -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 1.5rem; padding: 1.15rem 1.5rem;">
                        <form method="GET" action="{{ route('kpi.studentkpi') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                            <input type="hidden" name="tab" value="studentkpi">
                            
                            <!-- Search query -->
                            <div style="flex: 1; min-width: 240px; position: relative;">
                                <input type="text" name="q" value="{{ $search }}" placeholder="Search by name, application #, phone, email..." style="width: 100%; padding: 0.5rem 0.85rem 0.5rem 2.2rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc;">
                                <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8;" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            </div>

                            <!-- Stage Filter -->
                            <div style="min-width: 180px;">
                                <select name="stage" style="width: 100%; padding: 0.5rem 0.85rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff;">
                                    <option value="">All Funnel Stages</option>
                                    <option value="stage1" {{ $filterStage === 'stage1' ? 'selected' : '' }}>Stage 1: Personal Profile</option>
                                    <option value="stage2" {{ $filterStage === 'stage2' ? 'selected' : '' }}>Stage 2: Academic Info</option>
                                    <option value="stage3" {{ $filterStage === 'stage3' ? 'selected' : '' }}>Stage 3: Document Upload</option>
                                    <option value="stage4" {{ $filterStage === 'stage4' ? 'selected' : '' }}>Stage 4: Fee Payment</option>
                                    <option value="stage5" {{ $filterStage === 'stage5' ? 'selected' : '' }}>Stage 5: Exam Slot Booking</option>
                                </select>
                            </div>

                            <!-- Branch Filter -->
                            <div style="min-width: 180px;">
                                <select name="branch_id" style="width: 100%; padding: 0.5rem 0.85rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff;">
                                    <option value="">All Branches</option>
                                    @foreach($allBranches as $br)
                                        <option value="{{ $br->branch_id }}" {{ (string)$filterBranch === (string)$br->branch_id ? 'selected' : '' }}>
                                            {{ $br->branch_short_name ?: $br->branch_long_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <button type="submit" style="padding: 0.5rem 1.25rem; font-size: 0.85rem; font-weight: 700; color: #ffffff; background: #0284c7; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                                Filter Records
                            </button>

                            @if($search || $filterStage || $filterBranch)
                                <a href="{{ route('kpi.studentkpi', ['tab' => 'studentkpi']) }}" style="padding: 0.5rem 1rem; font-size: 0.85rem; font-weight: 600; color: #64748b; background: #f1f5f9; border-radius: 8px; text-decoration: none;">
                                    Reset
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- 4. EAGER-LOADED STUDENT REGISTRATIONS TABLE -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Verified Student Records</span>
                                <span style="font-size: 0.75rem; color: #64748b; margin-left: 0.5rem;">(Showing {{ $students->firstItem() ?? 0 }} to {{ $students->lastItem() ?? 0 }} of {{ $students->total() }} records)</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="crm-table">
                                <thead>
                                    <tr>
                                        <th>Application #</th>
                                        <th>Student Details</th>
                                        <th>Program &amp; Branch</th>
                                        <th>Stage 1 (Profile)</th>
                                        <th>Stage 2 (Academic)</th>
                                        <th>Stage 3 (Docs)</th>
                                        <th>Stage 4 (Fee)</th>
                                        <th>Stage 5 (Slot Booking)</th>
                                        <th>Overall Progress</th>
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

                                            $completedCount = ($isStage1 ? 1 : 0) + ($isStage2 ? 1 : 0) + ($isStage3 ? 1 : 0) + ($isStage4 ? 1 : 0) + ($isStage5 ? 1 : 0);
                                            $progressPct = round(($completedCount / 5) * 100);
                                        @endphp
                                        <tr>
                                            <td>
                                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.825rem; font-weight: 700; color: #0284c7;">
                                                    {{ $st->applinano }}
                                                </span>
                                                @if($st->crmid)
                                                    <div style="font-size: 0.7rem; color: #64748b; font-family: 'JetBrains Mono', monospace;">CRM: {{ $st->crmid }}</div>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="font-weight: 700; color: #1e293b; font-size: 0.875rem;">{{ $st->name ?: 'Candidate' }}</div>
                                                <div style="font-size: 0.75rem; color: #64748b; display: flex; align-items: center; gap: 0.35rem; margin-top: 2px;">
                                                    <span>📞 {{ $st->phoneno }}</span>
                                                    @if($st->emailid)
                                                        <span>• ✉️ {{ $st->emailid }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div style="font-weight: 600; color: #334155; font-size: 0.825rem;">
                                                    {{ $st->branch?->branch_short_name ?: ($st->branch?->branch_long_name ?: 'General Engineering') }}
                                                </div>
                                                <div style="font-size: 0.72rem; color: #64748b;">
                                                    {{ $st->program?->program_short_name ?: ($st->program?->program_name ?: 'B.Tech') }}
                                                </div>
                                            </td>
                                            <td>
                                                @if($isStage1)
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 2px 8px; border-radius: 6px;">
                                                        ✓ Done
                                                    </span>
                                                @else
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px;">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isStage2)
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; color: #6d28d9; background: #f5f3ff; border: 1px solid #ddd6fe; padding: 2px 8px; border-radius: 6px;">
                                                        ✓ Done
                                                    </span>
                                                @else
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px;">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isStage3)
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; color: #c2410c; background: #fff7ed; border: 1px solid #fed7aa; padding: 2px 8px; border-radius: 6px;">
                                                        ✓ Uploaded
                                                    </span>
                                                @else
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px;">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isStage4)
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 6px;">
                                                        ✓ Paid
                                                    </span>
                                                @else
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px;">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isStage5)
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; color: #0f766e; background: #f0fdfa; border: 1px solid #99f6e4; padding: 2px 8px; border-radius: 6px;">
                                                        ✓ Booked
                                                    </span>
                                                @else
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 600; color: #94a3b8; background: #f8fafc; border: 1px solid #e2e8f0; padding: 2px 8px; border-radius: 6px;">
                                                        Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                    <div style="flex: 1; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden; min-width: 60px;">
                                                        <div style="width: {{ $progressPct }}%; height: 100%; background: {{ $progressPct >= 80 ? '#10b981' : ($progressPct >= 50 ? '#3b82f6' : '#f59e0b') }}; border-radius: 3px;"></div>
                                                    </div>
                                                    <span style="font-size: 0.75rem; font-weight: 700; color: #334155;">{{ $progressPct }}%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" style="text-align: center; padding: 2.5rem 1.5rem; color: #94a3b8;">
                                                <div style="font-size: 1.5rem; margin-bottom: 0.35rem;">🔍</div>
                                                <div style="font-size: 0.9rem; font-weight: 600; color: #64748b;">No matching student registrations found</div>
                                            </td>
                                        </tr>
                                    @endforelse
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
});

function toggleSidebarMenu() {
    const sidebar = document.querySelector('.app-sidebar');
    if (sidebar) sidebar.classList.toggle('active');
}
</script>
</body>
</html>
