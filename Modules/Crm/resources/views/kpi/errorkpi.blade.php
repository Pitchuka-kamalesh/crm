<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error &amp; Logs Monitoring KPI &bull; VFSTR Admissions CRM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css?v={{ file_exists(public_path('css/crm-dashboard.css')) ? filemtime(public_path('css/crm-dashboard.css')) : time() }}">

    <style>
        :root {
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        /* KPI Cards Grid */
        .err-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .err-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.15rem 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            position: relative;
            overflow: hidden;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .err-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0,0,0,0.06);
        }

        .err-card-border-red { border-top: 4px solid #dc2626; }
        .err-card-border-amber { border-top: 4px solid #d97706; }
        .err-card-border-blue { border-top: 4px solid #0284c7; }
        .err-card-border-orange { border-top: 4px solid #ea580c; }
        .err-card-border-purple { border-top: 4px solid #7c3aed; }
        .err-card-border-slate { border-top: 4px solid #475569; }

        .err-card-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .err-card-num {
            font-size: 2rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
            margin-bottom: 0.35rem;
            font-feature-settings: "tnum";
        }

        .err-card-sub {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 500;
        }

        /* Role KPI Grid */
        .role-kpi-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
        }

        .role-kpi-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.65rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .role-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 0.85rem;
        }

        .role-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            text-decoration: none;
            display: block;
            color: inherit;
            transition: all 0.15s ease;
        }

        .role-box:hover, .role-box.active {
            background: #ffffff;
            border-color: #991b1b;
            box-shadow: 0 4px 10px rgba(153, 27, 27, 0.08);
            transform: translateY(-1px);
        }

        .role-box-name {
            font-size: 0.75rem;
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.35rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .role-box-count {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
        }

        .role-box-pct {
            font-size: 0.7rem;
            color: #64748b;
            font-weight: 600;
        }

        /* Filter Toolbar */
        .filter-toolbar {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
            justify-content: space-between;
        }

        .filter-group {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .filter-select, .filter-input {
            padding: 0.45rem 0.75rem;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            outline: none;
            font-family: var(--font-sans);
        }

        .filter-select:focus, .filter-input:focus {
            border-color: #991b1b;
            background: #ffffff;
            box-shadow: 0 0 0 2px rgba(153, 27, 27, 0.1);
        }

        .pill-btn {
            padding: 0.4rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 9999px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #475569;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.15s ease;
        }

        .pill-btn:hover, .pill-btn.active {
            background: #991b1b;
            color: #ffffff;
            border-color: #991b1b;
        }

        /* Table Design */
        .table-container {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.02);
            margin-bottom: 2rem;
        }

        .table-header-bar {
            padding: 1rem 1.25rem;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
        }

        .custom-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.68rem;
            letter-spacing: 0.05em;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
        }

        .custom-table td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }

        .custom-table tr:hover td {
            background: #fcfcfd;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.72rem;
            font-weight: 800;
            font-family: var(--font-mono);
        }

        .badge-500 { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-403 { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .badge-404 { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-400 { background: #ffedd5; color: #c2410c; border: 1px solid #fed7aa; }
        .badge-503 { background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; }

        .badge-role {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .role-1 { background: #e0f2fe; color: #0284c7; }
        .role-2 { background: #fee2e2; color: #991b1b; }
        .role-3 { background: #fef3c7; color: #b45309; }
        .role-4 { background: #dcfce7; color: #15803d; }
        .role-5 { background: #f3e8ff; color: #7e22ce; }
        .role-guest { background: #f1f5f9; color: #64748b; }

        .btn-inspect {
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 700;
            border-radius: 6px;
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-inspect:hover {
            background: #0f172a;
            color: #ffffff;
            border-color: #0f172a;
        }

        /* Modal Inspector */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(2px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal-box {
            background: #ffffff;
            border-radius: 16px;
            max-width: 780px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1), 0 10px 10px -5px rgba(0,0,0,0.04);
            padding: 1.75rem;
            position: relative;
        }

        .trace-box {
            background: #0f172a;
            color: #f8fafc;
            padding: 1rem;
            border-radius: 8px;
            font-family: var(--font-mono);
            font-size: 0.75rem;
            line-height: 1.5;
            max-height: 320px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
            margin-top: 0.75rem;
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
                <button type="button" class="topbar-toggle-sidebar-btn" onclick="toggleSidebarMenu()" title="Open Navigation Menu">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="18" x2="20" y2="18"/></svg>
                </button>
                <div class="topbar-page-breadcrumb">
                    <span class="breadcrumb-item breadcrumb-desktop-only">KPI</span>
                    <span class="breadcrumb-separator breadcrumb-desktop-only">/</span>
                    <span class="breadcrumb-active-tag">
                        <span class="breadcrumb-dot" style="background: #dc2626;"></span>
                        <span class="breadcrumb-lead-full">Error &amp; Logs Monitoring KPI &bull; Pin-to-Pin Diagnostic Hub</span>
                        <span class="breadcrumb-lead-mobile">Error KPI</span>
                    </span>
                </div>
            </div>

            <div class="top-bar-right">
                <a href="{{ route('kpi.errors.export', request()->query()) }}" class="pill-btn" title="Export current logs view to CSV">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export CSV
                </a>
                <form action="{{ route('kpi.errors.clear') }}" method="POST" onsubmit="return confirm('Clear all captured error logs?');" style="display:inline;">
                    @csrf
                    <button type="submit" class="pill-btn" style="color: #991b1b; border-color: #fca5a5;">
                        Clear Logs
                    </button>
                </form>
                <a href="{{ route('kpi.errors') }}" class="pill-btn active">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Live Refresh
                </a>
            </div>
        </header>

        <!-- Main Body Content -->
        <main class="page-body">
            @if(session('status'))
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1rem; font-size: 0.85rem; font-weight: 600;">
                {{ session('status') }}
            </div>
            @endif

            <!-- 1. HIGH-LEVEL ERROR KPI METRICS -->
            <div class="err-kpi-grid">
                <div class="err-card err-card-border-slate">
                    <div class="err-card-label">
                        <span>Total Errors</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    </div>
                    <div class="err-card-num">{{ number_format($metrics['total']) }}</div>
                    <div class="err-card-sub">Today: <strong style="color: #dc2626;">{{ $metrics['today'] }}</strong> new errors</div>
                </div>

                <div class="err-card err-card-border-red">
                    <div class="err-card-label">
                        <span>500 Server Errors</span>
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background: #dc2626; box-shadow: 0 0 6px rgba(220,38,38,0.8);"></span>
                    </div>
                    <div class="err-card-num" style="color: #991b1b;">{{ number_format($metrics['count500']) }}</div>
                    <div class="err-card-sub">Fatal &bull; Uncaught exceptions</div>
                </div>

                <div class="err-card err-card-border-amber">
                    <div class="err-card-label">
                        <span>403 Access Denied</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="err-card-num" style="color: #b45309;">{{ number_format($metrics['count403']) }}</div>
                    <div class="err-card-sub">Role permission rejections</div>
                </div>

                <div class="err-card err-card-border-blue">
                    <div class="err-card-label">
                        <span>404 Not Found</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <div class="err-card-num" style="color: #0369a1;">{{ number_format($metrics['count404']) }}</div>
                    <div class="err-card-sub">Missing routes &bull; Bad URLs</div>
                </div>

                <div class="err-card err-card-border-orange">
                    <div class="err-card-label">
                        <span>400 / 419 Client</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#ea580c" stroke-width="2"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    </div>
                    <div class="err-card-num" style="color: #c2410c;">{{ number_format($metrics['count400']) }}</div>
                    <div class="err-card-sub">CSRF expiry &bull; Bad payloads</div>
                </div>

                <div class="err-card err-card-border-purple">
                    <div class="err-card-label">
                        <span>Impacted Roles</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7c3aed" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="err-card-num" style="color: #7e22ce;">{{ $metrics['impactedRoles'] }}</div>
                    <div class="err-card-sub">Active roles affected</div>
                </div>
            </div>

            <!-- 2. CORE USER REQUIREMENT: ERROR KPI BY ROLE -->
            <div class="role-kpi-container">
                <div class="role-kpi-title">
                    <span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#991b1b" stroke-width="2.5" style="vertical-align: middle; margin-right: 6px;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                        Error Distribution by Role (Role-Based Diagnostic KPI)
                    </span>
                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 500;">
                        Click any role card below to filter the pin-to-pin error log
                    </span>
                </div>

                <div class="role-grid">
                    @foreach($metrics['roleCounts'] as $key => $rInfo)
                        @php
                            $isActive = (string)$currentRole === (string)$key;
                            $pct = $metrics['total'] > 0 ? round(($rInfo['count'] / $metrics['total']) * 100, 1) : 0;
                        @endphp
                        <a href="{{ route('kpi.errors', array_merge(request()->query(), ['role' => $key])) }}" 
                           class="role-box {{ $isActive ? 'active' : '' }}">
                            <div class="role-box-name">
                                <span>{{ $rInfo['label'] }}</span>
                                @if($rInfo['count'] > 0)
                                <span style="font-size: 0.65rem; background: #fee2e2; color: #991b1b; padding: 1px 5px; border-radius: 4px;">{{ $pct }}%</span>
                                @endif
                            </div>
                            <div class="role-box-count">{{ number_format($rInfo['count']) }}</div>
                            <div class="role-box-pct">{{ $rInfo['count'] == 1 ? '1 error logged' : $rInfo['count'] . ' errors logged' }}</div>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- 3. FILTER & SEARCH TOOLBAR -->
            <form method="GET" action="{{ route('kpi.errors') }}" class="filter-toolbar">
                <div class="filter-group">
                    <span style="font-size: 0.78rem; font-weight: 700; color: #64748b;">Time:</span>
                    <a href="{{ route('kpi.errors', array_merge(request()->except('time'), ['time' => 'all'])) }}" class="pill-btn {{ $timeFilter === 'all' ? 'active' : '' }}">All Time</a>
                    <a href="{{ route('kpi.errors', array_merge(request()->except('time'), ['time' => 'today'])) }}" class="pill-btn {{ $timeFilter === 'today' ? 'active' : '' }}">Today</a>
                    <a href="{{ route('kpi.errors', array_merge(request()->except('time'), ['time' => 'yesterday'])) }}" class="pill-btn {{ $timeFilter === 'yesterday' ? 'active' : '' }}">Yesterday</a>
                    <a href="{{ route('kpi.errors', array_merge(request()->except('time'), ['time' => 'week'])) }}" class="pill-btn {{ $timeFilter === 'week' ? 'active' : '' }}">Past 7 Days</a>
                </div>

                <div class="filter-group">
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Status Codes</option>
                        <option value="500" {{ $currentStatus == '500' ? 'selected' : '' }}>500 &bull; Server Errors</option>
                        <option value="403" {{ $currentStatus == '403' ? 'selected' : '' }}>403 &bull; Access Denied</option>
                        <option value="404" {{ $currentStatus == '404' ? 'selected' : '' }}>404 &bull; Not Found</option>
                        <option value="400" {{ $currentStatus == '400' ? 'selected' : '' }}>400 &bull; Bad Request</option>
                        <option value="419" {{ $currentStatus == '419' ? 'selected' : '' }}>419 &bull; Page Expired</option>
                        <option value="503" {{ $currentStatus == '503' ? 'selected' : '' }}>503 &bull; Maintenance</option>
                    </select>

                    <select name="role" class="filter-select" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <option value="1" {{ $currentRole == '1' ? 'selected' : '' }}>Role 1 &bull; Staff / Employee</option>
                        <option value="2" {{ $currentRole == '2' ? 'selected' : '' }}>Role 2 &bull; CRM Incharge</option>
                        <option value="3" {{ $currentRole == '3' ? 'selected' : '' }}>Role 3 &bull; CRM Dean</option>
                        <option value="4" {{ $currentRole == '4' ? 'selected' : '' }}>Role 4 &bull; Telecaller</option>
                        <option value="5" {{ $currentRole == '5' ? 'selected' : '' }}>Role 5 &bull; Student / Candidate</option>
                        <option value="guest" {{ $currentRole == 'guest' ? 'selected' : '' }}>Guest / Public</option>
                    </select>

                    <input type="text" name="search" value="{{ request('search') }}" class="filter-input" placeholder="Search problem, URL, user..." style="min-width: 220px;">
                    
                    <button type="submit" class="pill-btn active" style="padding: 0.45rem 1rem;">Filter</button>
                    @if(request()->hasAny(['status', 'role', 'search', 'time']))
                    <a href="{{ route('kpi.errors') }}" class="pill-btn" style="color: #64748b;">Reset</a>
                    @endif
                </div>
            </form>

            <!-- 4. PIN TO PIN DETAILED LOGS MONITORING TABLE -->
            <div class="table-container">
                <div class="table-header-bar">
                    <div>
                        <strong style="font-size: 0.95rem; color: #0f172a;">Live Error Incident Records (master.system_error_logs)</strong>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">
                            Showing {{ $errors->firstItem() ?? 0 }} to {{ $errors->lastItem() ?? 0 }} of {{ $errors->total() }} recorded incidents
                        </div>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th style="width: 140px;">Time (IST)</th>
                                <th style="width: 90px;">Status</th>
                                <th style="width: 160px;">Role &amp; User</th>
                                <th style="width: 180px;">Endpoint / Method</th>
                                <th>What Was The Problem? (Root Cause &amp; Details)</th>
                                <th style="width: 180px;">File Location</th>
                                <th style="width: 90px; text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($errors as $err)
                            <tr>
                                <td>
                                    <div style="font-weight: 700; color: #0f172a; font-family: var(--font-mono); font-size: 0.75rem;">
                                        {{ \Carbon\Carbon::parse($err->created_at)->timezone('Asia/Kolkata')->format('d M, h:i A') }}
                                    </div>
                                    <div style="font-size: 0.68rem; color: #94a3b8;">
                                        {{ \Carbon\Carbon::parse($err->created_at)->diffForHumans() }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-status badge-{{ in_array($err->status_code, [500, 403, 404, 400, 503]) ? $err->status_code : '500' }}">
                                        {{ $err->status_code }}
                                    </span>
                                </td>
                                <td>
                                    <div class="badge-role role-{{ $err->role_id ?: 'guest' }}">
                                        {{ $err->role_name ?: 'Guest' }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: #475569; margin-top: 3px; font-family: var(--font-mono);">
                                        {{ $err->usercode ?: 'Unauthenticated' }}
                                    </div>
                                </td>
                                <td>
                                    <span style="font-weight: 700; font-size: 0.68rem; background: #f1f5f9; padding: 2px 4px; border-radius: 3px;">{{ $err->http_method }}</span>
                                    <span style="font-family: var(--font-mono); font-size: 0.72rem; color: #334155; word-break: break-all;">
                                        {{ parse_url($err->route_url, PHP_URL_PATH) ?: $err->route_url }}
                                    </span>
                                </td>
                                <td>
                                    <div style="font-weight: 700; color: #991b1b; font-size: 0.8rem; line-height: 1.3;">
                                        {{ $err->error_message }}
                                    </div>
                                    @if($err->exception_class && $err->exception_class !== 'LoggedException')
                                    <div style="font-size: 0.68rem; color: #64748b; font-family: var(--font-mono); margin-top: 2px;">
                                        Exception: {{ $err->exception_class }}
                                    </div>
                                    @endif
                                </td>
                                <td>
                                    @if($err->file_path && $err->file_path !== 'laravel.log')
                                    <div style="font-family: var(--font-mono); font-size: 0.72rem; color: #334155;">
                                        {{ basename($err->file_path) }}:{{ $err->line_number }}
                                    </div>
                                    <div style="font-size: 0.65rem; color: #94a3b8; word-break: break-all;" title="{{ $err->file_path }}">
                                        {{ substr($err->file_path, 0, 30) }}...
                                    </div>
                                    @else
                                    <span style="font-size: 0.72rem; color: #94a3b8;">laravel.log</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn-inspect" onclick="openInspectorModal({{ json_encode($err) }})">
                                        Inspect
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 3rem 1rem; color: #64748b;">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" style="margin-bottom: 0.5rem;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    <div style="font-weight: 700; font-size: 1rem; color: #0f172a;">Zero Errors Recorded!</div>
                                    <div style="font-size: 0.8rem; color: #94a3b8; margin-top: 2px;">No errors matched the selected filter criteria. System running clean.</div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($errors->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                    {{ $errors->links() }}
                </div>
                @endif
            </div>

            <!-- 5. RAW LARAVEL.LOG TAIL VIEWER -->
            <div class="table-container">
                <div class="table-header-bar">
                    <div>
                        <strong style="font-size: 0.95rem; color: #0f172a;">Live `laravel.log` Stream Tail (Last 80 Records)</strong>
                        <div style="font-size: 0.75rem; color: #64748b; margin-top: 2px;">Direct realtime log buffer from storage/logs/laravel.log</div>
                    </div>
                </div>
                <div class="trace-box" style="margin: 0; border-radius: 0; max-height: 380px;">
@foreach($rawLogLines as $line)
@php
    $color = '#94a3b8';
    if (str_contains($line, '.ERROR:') || str_contains($line, 'FATAL') || str_contains($line, 'Exception')) $color = '#f87171';
    elseif (str_contains($line, '.WARNING:')) $color = '#fbbf24';
    elseif (str_contains($line, '.INFO:')) $color = '#34d399';
@endphp
<div style="color: {{ $color }}; padding: 2px 0; border-bottom: 1px solid rgba(255,255,255,0.05);">{{ $line }}</div>
@endforeach
                </div>
            </div>
        </main>
    </div>
</div>

<!-- 6. INSPECTOR MODAL -->
<div class="modal-overlay" id="inspectorModal" onclick="if(event.target === this) closeInspectorModal();">
    <div class="modal-box">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; border-bottom: 1px solid #e2e8f0; padding-bottom: 0.75rem;">
            <div>
                <span id="modalStatusBadge" class="badge-status badge-500">500</span>
                <span id="modalRoleBadge" class="badge-role role-2" style="margin-left: 6px;">CRM Incharge</span>
                <h3 id="modalTitle" style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-top: 6px;">Incident Diagnostics</h3>
            </div>
            <button type="button" onclick="closeInspectorModal()" style="background: none; border: none; font-size: 1.4rem; cursor: pointer; color: #64748b;">&times;</button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; font-size: 0.8rem; margin-bottom: 1rem;">
            <div><strong>Timestamp:</strong> <span id="modalTime" style="color: #475569;"></span></div>
            <div><strong>User Code:</strong> <span id="modalUser" style="color: #475569;"></span></div>
            <div><strong>HTTP Method / Route:</strong> <span id="modalRoute" style="font-family: var(--font-mono); color: #0284c7;"></span></div>
            <div><strong>Client IP:</strong> <span id="modalIp" style="font-family: var(--font-mono); color: #475569;"></span></div>
            <div style="grid-column: span 2;"><strong>File &amp; Line:</strong> <span id="modalFile" style="font-family: var(--font-mono); color: #991b1b;"></span></div>
        </div>

        <div style="margin-top: 1rem;">
            <strong style="font-size: 0.85rem; color: #0f172a;">Exact Error Message / Root Cause:</strong>
            <div id="modalMessage" style="background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; padding: 0.75rem 1rem; border-radius: 8px; font-weight: 700; margin-top: 4px; font-size: 0.85rem; word-break: break-all;"></div>
        </div>

        <div style="margin-top: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <strong style="font-size: 0.85rem; color: #0f172a;">Detailed Stack Trace:</strong>
                <button type="button" class="btn-inspect" onclick="copyTrace()">Copy Trace</button>
            </div>
            <div id="modalTrace" class="trace-box"></div>
        </div>
    </div>
</div>

<script>
    function openInspectorModal(err) {
        document.getElementById('modalStatusBadge').innerText = err.status_code || 500;
        document.getElementById('modalStatusBadge').className = 'badge-status badge-' + (err.status_code || 500);
        document.getElementById('modalRoleBadge').innerText = err.role_name || 'Guest';
        document.getElementById('modalRoleBadge').className = 'badge-role role-' + (err.role_id || 'guest');
        document.getElementById('modalTime').innerText = err.created_at;
        document.getElementById('modalUser').innerText = err.usercode || 'Unauthenticated / Guest';
        document.getElementById('modalRoute').innerText = (err.http_method || 'GET') + ' ' + (err.route_url || '/');
        document.getElementById('modalIp').innerText = err.ip_address || '127.0.0.1';
        document.getElementById('modalFile').innerText = (err.file_path || 'Unknown') + (err.line_number ? (':' + err.line_number) : '');
        document.getElementById('modalMessage').innerText = err.error_message || 'No message provided';
        document.getElementById('modalTrace').innerText = err.stack_trace || 'No stack trace captured.';
        document.getElementById('inspectorModal').classList.add('open');
    }

    function closeInspectorModal() {
        document.getElementById('inspectorModal').classList.remove('open');
    }

    function copyTrace() {
        var text = document.getElementById('modalTrace').innerText;
        navigator.clipboard.writeText(text).then(function() {
            alert('Stack trace copied to clipboard!');
        });
    }
</script>
</body>
</html>
