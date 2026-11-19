<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads KPI &amp; Acquisition Analytics - VFSTR Admissions CRM</title>
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
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .kpi-charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(440px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.75rem;
        }

        @media (max-width: 991px) {
            .kpi-metrics-grid {
                grid-template-columns: repeat(2, 1fr) !important;
                gap: 0.65rem !important;
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
            #leadsFilterForm {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            #leadsFilterForm > div {
                width: 100% !important;
                min-width: 100% !important;
            }
            #leadsResetBtn {
                width: 100% !important;
                text-align: center !important;
            }
        }

        @media (max-width: 480px) {
            .kpi-metrics-grid {
                grid-template-columns: 1fr !important;
                gap: 0.5rem !important;
            }
            .kpi-metrics-grid > div {
                padding: 0.65rem 0.75rem !important;
            }
            .kpi-metrics-grid > div > div:first-child {
                font-size: 0.68rem !important;
            }
            .kpi-metrics-grid > div > div:nth-child(2) {
                font-size: 1.35rem !important;
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
                        <span class="breadcrumb-lead-full">Leads KPI &bull; Acquisition Analytics</span>
                        <span class="breadcrumb-lead-mobile">Leads KPI</span>
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

            <!-- TAB: LEADS KPI & ACQUISITION ANALYTICS -->
            <div class="content-card">
                <div class="card-header-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 1rem 1.5rem; border-bottom: 1px solid #e2e8f0; background: #ffffff;">
                    <div class="card-title" style="font-size: 1.15rem; font-weight: 700; color: #1e293b; margin: 0;">
                        Leads KPI &amp; Acquisition Analytics
                    </div>
                </div>

                <div class="kpi-content-padding" style="padding: 1.5rem;">
                    <!-- 1. TOP EXECUTIVE METRIC CARDS (NO ICONS, COMPACT SINGLE LINE ROW) -->
                    <div class="kpi-metrics-grid">
                        <!-- Card 1: Total Raw Lead Volume -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Total Lead Volume</div>
                            <div style="font-size: 1.5rem; font-weight: 800; color: #1e293b; margin-top: 0.25rem;">{{ number_format($totalRawLeads) }}</div>
                        </div>

                        <!-- Card 2: Allocated Leads -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Allocated Leads</div>
                            <div style="font-size: 1.5rem; font-weight: 800; color: #059669; margin-top: 0.25rem;">{{ number_format($allocatedCount) }}</div>
                        </div>

                        <!-- Card 3: Unallocated Pool -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Unallocated Pool</div>
                            <div style="font-size: 1.5rem; font-weight: 800; color: #ea580c; margin-top: 0.25rem;">{{ number_format($unallocatedCount) }}</div>
                        </div>

                        <!-- Card 4: Source Breakdown Overview -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.85rem 1rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
                            <div style="font-size: 0.72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;">Bulk Uploads (Bulk Excel (02598))</div>
                            <div style="font-size: 1.5rem; font-weight: 800; color: #7c3aed; margin-top: 0.25rem;">{{ number_format($bulkUploadCount) }}</div>
                        </div>
                    </div>

                    <!-- 2. VISUAL CHARTS (CHRONOLOGICAL INFLOW TRENDS & BULK VS NORMAL BREAKDOWN) -->
                    <div class="kpi-charts-grid">
                        <!-- Chart 1: Chronological Lead Inflow Trends (Chart.js Area Graph) -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.5rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Chronological Lead Inflow Velocity</div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Daily acquisition trends plotted by enquirydate</div>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; color: #0284c7; background: #eff6ff; padding: 0.25rem 0.65rem; border-radius: 20px; border: 1px solid #bfdbfe;">Area Trend</span>
                            </div>
                            <div style="position: relative; flex-grow: 1; min-height: 280px; max-height: 320px;">
                                <canvas id="leadInflowChart"></canvas>
                            </div>
                        </div>

                        <!-- Chart 2: Source Breakdown (Bulk Upload vs Normal Registrations) -->
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem 1.5rem; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); display: flex; flex-direction: column;">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem;">
                                <div>
                                    <div style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Lead Acquisition Channels</div>
                                    <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Bulk Upload vs Normal Registrations</div>
                                </div>
                                <span style="font-size: 0.72rem; font-weight: 700; color: #0284c7; background: #eff6ff; padding: 0.25rem 0.65rem; border-radius: 20px; border: 1px solid #bfdbfe;">Bulk vs Normal</span>
                            </div>
                            <div style="position: relative; flex-grow: 1; min-height: 280px; max-height: 320px;">
                                <canvas id="sourceDistributionChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- 3. LIVE INTERACTIVE FILTERING TOOLBAR (NO ALL SOURCES DROPDOWN, LIVE DEBOUNCED) -->
                    <div style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); margin-bottom: 1.5rem; padding: 1rem 1.25rem;">
                        <form id="leadsFilterForm" method="GET" action="{{ route('kpi.leadskpi') }}" onsubmit="event.preventDefault(); window.handleLeadsLiveFilter(true);" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                            <input type="hidden" name="tab" value="leadskpi">

                            <!-- Search query (Live debounced) -->
                            <div style="flex: 1; min-width: 240px; position: relative;">
                                <input type="text" 
                                       id="leadsLiveSearch" 
                                       name="q" 
                                       value="{{ $search }}" 
                                       placeholder="Search by phone, application #, staff ID, student..." 
                                       autocomplete="off"
                                       oninput="window.handleLeadsLiveFilter()"
                                       style="width: 100%; padding: 0.55rem 2rem 0.55rem 2.25rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; outline: none; background: #f8fafc; transition: all 0.2s;">
                                <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8;" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                <button type="button" 
                                        id="leadsClearSearchBtn" 
                                        onclick="window.clearLeadsSearch()" 
                                        style="display: {{ $search ? 'block' : 'none' }}; position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; color: #94a3b8; font-size: 1.15rem; cursor: pointer; line-height: 1; padding: 0;">&times;</button>
                            </div>

                            <!-- Year Filter Dropdown (Live onchange) -->
                            <div style="min-width: 130px;">
                                <select id="leadsYearSelect" 
                                        name="year" 
                                        onchange="window.handleLeadsLiveFilter(true)"
                                        style="width: 100%; padding: 0.55rem 0.85rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; outline: none;">
                                    <option value="">All Years</option>
                                    @foreach($availableYears as $y)
                                        <option value="{{ $y }}" {{ (string)$filterYear === (string)$y ? 'selected' : '' }}>Year {{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Month Filter Dropdown (Live onchange) -->
                            <div style="min-width: 140px;">
                                <select id="leadsMonthSelect" 
                                        name="month" 
                                        onchange="window.handleLeadsLiveFilter(true)"
                                        style="width: 100%; padding: 0.55rem 0.85rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; outline: none;">
                                    <option value="">All Months</option>
                                    @foreach($availableMonths as $num => $name)
                                        <option value="{{ $num }}" {{ (string)$filterMonth === (string)$num ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Allocation Status Dropdown (Live onchange) -->
                            <div style="min-width: 150px;">
                                <select id="leadsStatusSelect" 
                                        name="status" 
                                        onchange="window.handleLeadsLiveFilter(true)"
                                        style="width: 100%; padding: 0.55rem 0.85rem; font-size: 0.85rem; border: 1px solid #cbd5e1; border-radius: 8px; background: #ffffff; cursor: pointer; outline: none;">
                                    <option value="all" {{ $filterStatus === 'all' ? 'selected' : '' }}>All Statuses</option>
                                    <option value="allocated" {{ $filterStatus === 'allocated' ? 'selected' : '' }}>Allocated</option>
                                    <option value="unallocated" {{ $filterStatus === 'unallocated' ? 'selected' : '' }}>Unallocated</option>
                                </select>
                            </div>

                            <!-- Live Filter Active Spinner / Indicator -->
                            <div id="leadsLiveIndicator" style="display: none; align-items: center; gap: 0.35rem; font-size: 0.75rem; color: #0284c7; font-weight: 600;">
                                <span style="display: inline-block; width: 7px; height: 7px; background: #0284c7; border-radius: 50%;"></span>
                                <span>Updating...</span>
                            </div>

                            <!-- Reset Button (Always Visible) -->
                            <button type="button" 
                                    id="leadsResetBtn" 
                                    onclick="window.resetLeadsFilters()" 
                                    style="padding: 0.55rem 1.15rem; font-size: 0.85rem; font-weight: 600; color: #475569; background: #f1f5f9; border: 1px solid #cbd5e1; border-radius: 8px; cursor: pointer; transition: all 0.2s;"
                                    onmouseover="this.style.background='#e2e8f0'"
                                    onmouseout="this.style.background='#f1f5f9'">
                                Reset
                            </button>
                        </form>
                    </div>

                    <!-- 4. PAGINATED LEADS PREVIEW TABLE (CONTAINER REPLACED VIA LIVE AJAX) -->
                    <div id="leadsTableContainer" style="border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff; box-shadow: 0 1px 3px rgba(0,0,0,0.02); overflow: hidden;">
                        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <span style="font-size: 0.95rem; font-weight: 700; color: #1e293b;">Raw Inquiries Preview Directory</span>
                                <span id="leadsTableCountText" style="font-size: 0.75rem; color: #64748b; margin-left: 0.5rem;">(Showing {{ $leads->firstItem() ?? 0 }} to {{ $leads->lastItem() ?? 0 }} of {{ number_format($leads->total()) }} leads)</span>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="crm-table" id="leadsRecordsTable">
                                <thead>
                                    <tr>
                                        <th>Phone Number</th>
                                        <th>Application #</th>
                                        <th>Enquiry Date</th>
                                        <th>Source Channel (log_userid)</th>
                                        <th>Allocation Status</th>
                                        <th>Assigned Counselor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr id="leadsClientNoMatchesRow" style="display: none;">
                                        <td colspan="6" style="text-align: center; padding: 2.5rem 1.5rem; color: #94a3b8;">
                                            <div style="font-size: 1.5rem; margin-bottom: 0.35rem;">🔍</div>
                                            <div style="font-size: 0.9rem; font-weight: 600; color: #64748b;">No raw inquiries matching the active live filter criteria</div>
                                        </td>
                                    </tr>
                                    @forelse($leads as $lead)
                                        @php
                                            $isAllocated = !empty($lead->empcode) && $lead->empcode !== '0';
                                            $isBulk = trim((string)$lead->log_userid) === '02598';
                                            $isPortal = in_array(strtoupper(trim((string)$lead->log_userid)), ['PORTAL', 'SELF_REGISTER']);
                                            $leadYear = $lead->enquirydate ? date('Y', strtotime($lead->enquirydate)) : '';
                                            $leadMonth = $lead->enquirydate ? (int)date('n', strtotime($lead->enquirydate)) : '';
                                        @endphp
                                        <tr class="lead-row" 
                                            data-year="{{ $leadYear }}" 
                                            data-month="{{ $leadMonth }}" 
                                            data-status="{{ $isAllocated ? 'allocated' : 'unallocated' }}">
                                            <td>
                                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 700; color: #1e293b;">
                                                    {{ $lead->phoneno }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($lead->applino)
                                                    <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.825rem; font-weight: 600; color: #0284c7;">
                                                        {{ $lead->applino }}
                                                    </span>
                                                @else
                                                    <span style="color: #94a3b8; font-size: 0.75rem;">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span style="font-size: 0.8rem; color: #334155; font-weight: 600;">
                                                    {{ $lead->enquirydate ? date('d M Y', strtotime($lead->enquirydate)) : '—' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($isBulk)
                                                    <span style="font-size: 0.75rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; padding: 2px 8px; border-radius: 6px;">
                                                        Bulk Excel (02598)
                                                    </span>
                                                @elseif($isPortal)
                                                    <span style="font-size: 0.75rem; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 6px;">
                                                        Direct Portal
                                                    </span>
                                                @else
                                                    <span style="font-size: 0.75rem; font-weight: 700; color: #6d28d9; background: #f5f3ff; border: 1px solid #ddd6fe; padding: 2px 8px; border-radius: 6px;">
                                                        {{ $lead->log_userid ?: 'Direct Staff' }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isAllocated)
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 2px 8px; border-radius: 6px;">
                                                        ✓ Allocated
                                                    </span>
                                                @else
                                                    <span style="display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; font-weight: 600; color: #ea580c; background: #fff7ed; border: 1px solid #fed7aa; padding: 2px 8px; border-radius: 6px;">
                                                        Unallocated
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($isAllocated)
                                                    <div style="font-weight: 700; color: #0284c7; font-size: 0.825rem;">
                                                        {{ $lead->employee?->full_name ?: $lead->empcode }}
                                                    </div>
                                                    <div style="font-size: 0.7rem; color: #64748b; font-family: 'JetBrains Mono', monospace;">ID: {{ $lead->empcode }}</div>
                                                @else
                                                    <span style="color: #94a3b8; font-size: 0.78rem;">In Calling Pool</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 2.5rem 1.5rem; color: #94a3b8;">
                                                <div style="font-size: 1.5rem; margin-bottom: 0.35rem;">🔍</div>
                                                <div style="font-size: 0.9rem; font-weight: 600; color: #64748b;">No raw inquiries matching the selected filter criteria</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination Footer (Single Instance) -->
                        @if($leads->hasPages())
                            <div class="table-pagination-area">
                                <div style="font-size: 0.825rem; color: #64748b;">
                                    Showing <strong style="color: #1e293b;">{{ $leads->firstItem() ?? 0 }}</strong> to <strong style="color: #1e293b;">{{ $leads->lastItem() ?? 0 }}</strong> of <strong style="color: #1e293b;">{{ number_format($leads->total()) }}</strong> records ({{ $leads->perPage() }} per page)
                                </div>
                                <div class="pagination-wrapper">
                                    {{ $leads->links('pagination::bootstrap-4') }}
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
let leadInflowChartInstance = null;
let sourceDistributionChartInstance = null;

function renderLeadsCharts() {
    if (typeof Chart === 'undefined') {
        setTimeout(renderLeadsCharts, 60);
        return;
    }

    // 1. Chart.js Chronological Lead Inflow Velocity (Area Graph)
    const inflowCanvas = document.getElementById('leadInflowChart');
    if (inflowCanvas) {
        if (leadInflowChartInstance) {
            leadInflowChartInstance.destroy();
            leadInflowChartInstance = null;
        }
        const labels = {!! json_encode($trendLabels) !!};
        const counts = {!! json_encode($trendCounts) !!};

        leadInflowChartInstance = new Chart(inflowCanvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Raw Enquiries Inflow',
                    data: counts,
                    borderColor: '#0284c7',
                    backgroundColor: 'rgba(2, 132, 199, 0.12)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#0284c7',
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                                return ` ${context.parsed.y.toLocaleString()} enquiries received`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function (value) {
                                return value >= 1000 ? (value / 1000) + 'k' : value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11, family: 'Plus Jakarta Sans' } }
                    }
                }
            }
        });
    }

    // 2. Chart.js Inflow Channel Distribution (Bulk Upload vs Normal Registrations)
    const sourceCanvas = document.getElementById('sourceDistributionChart');
    if (sourceCanvas) {
        if (sourceDistributionChartInstance) {
            sourceDistributionChartInstance.destroy();
            sourceDistributionChartInstance = null;
        }
        const bulkCount = {{ (int)$bulkUploadCount }};
        const normalCount = {{ (int)$normalLeadsCount }};
        const total = {{ (int)$totalRawLeads }};

        const bulkPct = total > 0 ? ((bulkCount / total) * 100).toFixed(2) : '0';
        const normalPct = total > 0 ? ((normalCount / total) * 100).toFixed(2) : '0';

        sourceDistributionChartInstance = new Chart(sourceCanvas, {
            type: 'doughnut',
            data: {
                labels: [
                    `Bulk Upload (${bulkCount.toLocaleString()} • ${bulkPct}%)`,
                    `Normal Registration (${normalCount.toLocaleString()} • ${normalPct}%)`
                ],
                datasets: [{
                    data: [bulkCount, normalCount],
                    backgroundColor: ['#0284c7', '#10b981'],
                    hoverBackgroundColor: ['#0369a1', '#059669'],
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
                            boxWidth: 14,
                            boxHeight: 14,
                            font: { size: 12, family: 'Plus Jakarta Sans', weight: '600' },
                            padding: 16,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                const val = context.parsed;
                                const pct = total > 0 ? ((val / total) * 100).toFixed(2) : 0;
                                return ` ${context.label.split(' (')[0]}: ${val.toLocaleString()} leads (${pct}%)`;
                            }
                        }
                    }
                },
                cutout: '65%'
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    renderLeadsCharts();
    attachLeadsPaginationAjax();
});

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    renderLeadsCharts();
    attachLeadsPaginationAjax();
}

let leadsLiveTimer = null;
window.handleLeadsLiveFilter = function (immediate) {
    const searchInput = document.getElementById('leadsLiveSearch');
    const yearSelect = document.getElementById('leadsYearSelect');
    const monthSelect = document.getElementById('leadsMonthSelect');
    const statusSelect = document.getElementById('leadsStatusSelect');
    const resetBtn = document.getElementById('leadsResetBtn');
    const clearBtn = document.getElementById('leadsClearSearchBtn');
    const indicator = document.getElementById('leadsLiveIndicator');
    const tableContainer = document.getElementById('leadsTableContainer');

    const q = searchInput ? searchInput.value.trim() : '';
    const year = yearSelect ? yearSelect.value : '';
    const month = monthSelect ? monthSelect.value : '';
    const status = statusSelect ? statusSelect.value : 'all';

    if (clearBtn) clearBtn.style.display = q ? 'block' : 'none';

    // 1. Instant client-side filtering on current rows
    const rows = document.querySelectorAll('#leadsRecordsTable tbody tr.lead-row');
    const noMatchesRow = document.getElementById('leadsClientNoMatchesRow');
    let visibleCount = 0;
    rows.forEach(row => {
        const text = (row.textContent || '').toLowerCase();
        const matchesQ = !q || text.includes(q.toLowerCase());
        const rowYear = row.getAttribute('data-year') || '';
        const rowMonth = row.getAttribute('data-month') || '';
        const rowStatus = row.getAttribute('data-status') || '';

        const matchesYear = !year || (rowYear === year);
        const matchesMonth = !month || (rowMonth === month);
        const matchesStatus = (!status || status === 'all') || (rowStatus === status);

        if (matchesQ && matchesYear && matchesMonth && matchesStatus) {
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
    clearTimeout(leadsLiveTimer);

    const delay = immediate ? 0 : 250;
    leadsLiveTimer = setTimeout(() => {
        const url = new URL(window.location.origin + window.location.pathname);
        url.searchParams.set('tab', 'leadskpi');
        if (q) url.searchParams.set('q', q);
        if (year) url.searchParams.set('year', year);
        if (month) url.searchParams.set('month', month);
        if (status && status !== 'all') url.searchParams.set('status', status);

        window.history.replaceState({}, '', url.toString());

        fetch(url.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.getElementById('leadsTableContainer');
            if (newTable && tableContainer) {
                tableContainer.innerHTML = newTable.innerHTML;
                attachLeadsPaginationAjax();
            }
        })
        .catch(err => console.error('Leads live filter fetch error:', err))
        .finally(() => {
            if (indicator) indicator.style.display = 'none';
        });
    }, delay);
};

window.clearLeadsSearch = function () {
    const searchInput = document.getElementById('leadsLiveSearch');
    if (searchInput) {
        searchInput.value = '';
        searchInput.focus();
        window.handleLeadsLiveFilter(true);
    }
};

window.resetLeadsFilters = function () {
    const searchInput = document.getElementById('leadsLiveSearch');
    const yearSelect = document.getElementById('leadsYearSelect');
    const monthSelect = document.getElementById('leadsMonthSelect');
    const statusSelect = document.getElementById('leadsStatusSelect');
    if (searchInput) searchInput.value = '';
    if (yearSelect) yearSelect.value = '';
    if (monthSelect) monthSelect.value = '';
    if (statusSelect) statusSelect.value = 'all';
    window.handleLeadsLiveFilter(true);
};

function attachLeadsPaginationAjax() {
    const paginationLinks = document.querySelectorAll('#leadsTableContainer .pagination a');
    const indicator = document.getElementById('leadsLiveIndicator');
    const tableContainer = document.getElementById('leadsTableContainer');

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
                const newTable = doc.getElementById('leadsTableContainer');
                if (newTable && tableContainer) {
                    tableContainer.innerHTML = newTable.innerHTML;
                    attachLeadsPaginationAjax();
                    tableContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            })
            .catch(err => console.error('Leads pagination fetch error:', err))
            .finally(() => {
                if (indicator) indicator.style.display = 'none';
            });
        });
    });
}
</script>
</body>
</html>
