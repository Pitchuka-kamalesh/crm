<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS & WhatsApp Gateway Tracker | Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-primary: #2563eb;
            --brand-primary-hover: #1d4ed8;
            --brand-whatsapp: #059669;
            --brand-whatsapp-dark: #047857;
            --bg-page: #f8fafc;
            --text-dark: #0f172a;
            --text-slate: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        /* Top Nav */
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .nav-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .brand-logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-titles {
            display: flex;
            flex-direction: column;
        }

        .brand-main {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
        }

        .brand-tag {
            font-size: 0.72rem;
            font-weight: 700;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.5rem 1rem;
            background: #f1f5f9;
            color: #334155;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-nav:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Main Container */
        .main-content {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem 3rem 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        /* Banner alerts */
        .alert-banner {
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            font-size: 0.86rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* Page Header */
        .page-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
        }

        .page-subtitle {
            font-size: 0.86rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
        }

        /* Metrics Grid */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.15rem;
        }

        .metric-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.25rem 1.35rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.05);
        }

        .metric-label {
            font-size: 0.76rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .metric-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }

        .metric-sub {
            font-size: 0.74rem;
            color: #64748b;
        }

        /* Gateway Dispatcher Cards (Side-by-side) */
        .dispatchers-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        @media (max-width: 900px) {
            .dispatchers-grid {
                grid-template-columns: 1fr;
            }
        }

        .dispatch-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.4rem 1.6rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .dispatch-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .dispatch-title-box {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .dispatch-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 1.1rem;
        }

        .icon-wa { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .icon-sms { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }

        .dispatch-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
        }

        .dispatch-meta {
            font-size: 0.74rem;
            color: #64748b;
        }

        .badge-live {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
            font-size: 0.68rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            text-transform: uppercase;
        }

        .dispatch-form {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .form-group-field {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .form-field-label {
            font-size: 0.76rem;
            font-weight: 700;
            color: #334155;
        }

        .form-control-input {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.85rem;
            font-family: inherit;
            color: #0f172a;
            background: #f8fafc;
            transition: all 0.2s ease;
        }

        .form-control-input:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .btn-dispatch {
            padding: 0.75rem 1.25rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #ffffff;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-family: inherit;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .btn-dispatch-wa {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }
        .btn-dispatch-wa:hover {
            background: linear-gradient(135deg, #047857 0%, #065f46 100%);
            transform: translateY(-1px);
        }

        .btn-dispatch-sms {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }
        .btn-dispatch-sms:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            transform: translateY(-1px);
        }

        /* Tracker Logs Table Card */
        .table-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .table-card-header {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            background: #ffffff;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.65rem;
        }

        .filter-select {
            padding: 0.5rem 0.85rem;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.8rem;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
        }

        .filter-search {
            padding: 0.5rem 0.85rem;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.8rem;
            font-family: inherit;
            width: 220px;
        }

        .btn-filter {
            padding: 0.5rem 1rem;
            background: #2563eb;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-reset {
            padding: 0.5rem 0.85rem;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 0.8rem;
            font-weight: 700;
            text-decoration: none;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            text-align: left;
        }

        .data-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-size: 0.7rem;
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .data-table td {
            padding: 0.95rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: #1e293b;
        }

        .data-table tr:hover td {
            background: #f8fafc;
        }

        /* Badges */
        .badge-channel {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-wa {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-sms-ch {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        .status-pill-table {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.22rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .status-submitted {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .status-failed {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .trace-id-box {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: #2563eb;
            background: #eff6ff;
            padding: 0.2rem 0.45rem;
            border-radius: 6px;
            border: 1px solid #dbeafe;
            display: inline-block;
            max-width: 180px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .mono-phone {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            color: #0f172a;
        }

        .msg-excerpt {
            max-width: 280px;
            font-size: 0.78rem;
            color: #475569;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table-pagination-area {
            padding: 1rem 1.5rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
        }

        /* Modal dialog for raw JSON view */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
            padding: 1.5rem;
        }

        .modal-card {
            background: #ffffff;
            border-radius: 16px;
            width: 100%;
            max-width: 640px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .modal-header {
            padding: 1.15rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: #64748b;
        }

        .modal-body {
            padding: 1.25rem 1.5rem;
            max-height: 480px;
            overflow-y: auto;
        }

        .json-pre {
            background: #0f172a;
            color: #38bdf8;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            padding: 1rem;
            border-radius: 10px;
            overflow-x: auto;
            white-space: pre-wrap;
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <header class="top-navbar">
        <div class="nav-inner">
            <a href="{{ route('sms.index') }}" class="brand-link">
                <div class="brand-logo-box">
                    <img src="/images/vignan-logo.png" alt="Vignan University">
                </div>
                <div class="brand-titles">
                    <span class="brand-main">Vignan University</span>
                    <span class="brand-tag">Communication &amp; SMS Tracker</span>
                </div>
            </a>

            <div class="nav-actions">
                <a href="{{ route('dashboards.index') }}" class="btn-nav">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Back to Portal</span>
                </a>
            </div>
        </div>
    </header>

    <main class="main-content">

        <!-- Alerts -->
        @if(session('status'))
            <div class="alert-banner alert-success">
                <span>&bull; {{ session('status') }}</span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;font-weight:700;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-banner alert-danger">
                <span>&bull; {{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;font-weight:700;">&times;</button>
            </div>
        @endif

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">SMS &amp; WhatsApp Communication Tracker</h1>
                <p class="page-subtitle">Unified monitoring for SmartPing WhatsApp Gateway and JioCX DLT SMS logs, delivery receipts, and live dispatches.</p>
            </div>
            <div style="display:flex;gap:0.75rem;">
                <button type="button" class="btn-nav" onclick="window.location.reload()">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Refresh Logs</span>
                </button>
            </div>
        </div>

        <!-- KPI Metrics -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-label">
                    <span>Total Dispatched</span>
                    <span style="font-size:1.1rem;">📊</span>
                </div>
                <div class="metric-value">{{ number_format($totalLogs) }}</div>
                <div class="metric-sub">Across all gateways &amp; campaigns</div>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <span>WhatsApp (SmartPing)</span>
                    <span style="font-size:1.1rem;color:#10b981;">💬</span>
                </div>
                <div class="metric-value" style="color:#059669;">{{ number_format($totalWhatsapp) }}</div>
                <div class="metric-sub">OTP (vimsgridonetimepp) &amp; Admission</div>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <span>Jio DLT SMS</span>
                    <span style="font-size:1.1rem;color:#2563eb;">📱</span>
                </div>
                <div class="metric-value" style="color:#2563eb;">{{ number_format($totalSms) }}</div>
                <div class="metric-sub">Template 1777179354065677511 &amp; OTP</div>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <span>Delivered / Submitted</span>
                    <span style="font-size:1.1rem;color:#10b981;">✅</span>
                </div>
                <div class="metric-value" style="color:#10b981;">{{ number_format($totalSuccess) }}</div>
                <div class="metric-sub">Successful gateway deliveries</div>
            </div>

            <div class="metric-card">
                <div class="metric-label">
                    <span>Failed / Rejected</span>
                    <span style="font-size:1.1rem;color:#ef4444;">⚠️</span>
                </div>
                <div class="metric-value" style="color:#ef4444;">{{ number_format($totalFailed) }}</div>
                <div class="metric-sub">Delivery or network errors</div>
            </div>
        </div>

        <!-- Test Dispatchers -->
        <div class="dispatchers-grid">

            <!-- Card 1: SmartPing WhatsApp Gateway -->
            <div class="dispatch-card">
                <div class="dispatch-header">
                    <div class="dispatch-title-box">
                        <div class="dispatch-icon icon-wa">WA</div>
                        <div>
                            <h3 class="dispatch-title">SmartPing WhatsApp Gateway</h3>
                            <div class="dispatch-meta">Campaigns: vimsgridonetimepp / crmfirstinfo</div>
                        </div>
                    </div>
                    <span class="badge-live">Active JWT</span>
                </div>

                <form action="{{ route('sms.test-whatsapp') }}" method="POST" class="dispatch-form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group-field">
                            <label class="form-field-label">Recipient Mobile (10-digits)</label>
                            <input type="tel" name="phone" required maxlength="10" placeholder="e.g. 8309607291" class="form-control-input" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                        </div>
                        <div class="form-group-field">
                            <label class="form-field-label">Campaign Template</label>
                            <select name="campaign_type" class="form-control-input" id="wa-campaign-selector" onchange="toggleWaFields(this.value)">
                                <option value="otp">OTP Verification (vimsgridonetimepp)</option>
                                <option value="admission">Admission Details (crmfirstinfo)</option>
                            </select>
                        </div>
                    </div>

                    <div id="wa-admission-fields" style="display:none;gap:0.75rem;" class="form-row">
                        <div class="form-group-field">
                            <label class="form-field-label">Student Name</label>
                            <input type="text" name="test_name" placeholder="e.g. Ramesh Kethakana" class="form-control-input">
                        </div>
                        <div class="form-group-field">
                            <label class="form-field-label">Program Name</label>
                            <input type="text" name="test_program" placeholder="e.g. MBA / MCA" class="form-control-input">
                        </div>
                    </div>

                    <button type="submit" class="btn-dispatch btn-dispatch-wa">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Send WhatsApp via SmartPing</span>
                    </button>
                </form>
            </div>

            <!-- Card 2: Jio DLT SMS Gateway -->
            <div class="dispatch-card">
                <div class="dispatch-header">
                    <div class="dispatch-title-box">
                        <div class="dispatch-icon icon-sms">SMS</div>
                        <div>
                            <h3 class="dispatch-title">JioCX DLT SMS Gateway</h3>
                            <div class="dispatch-meta">Entity: 1101385640000011255 | Sender: VIGNAN</div>
                        </div>
                    </div>
                    <span class="badge-live">DLT Ready</span>
                </div>

                <form action="{{ route('sms.test-sms') }}" method="POST" class="dispatch-form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group-field">
                            <label class="form-field-label">Recipient Mobile (10-digits)</label>
                            <input type="tel" name="phone" required maxlength="10" placeholder="e.g. 8309607291" class="form-control-input" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                        </div>
                        <div class="form-group-field">
                            <label class="form-field-label">SMS Template</label>
                            <select name="sms_type" class="form-control-input" id="sms-template-selector" onchange="toggleSmsFields(this.value)">
                                <option value="admission">Admission Notice (1777179354065677511)</option>
                                <option value="otp">Login OTP (1777179265526294215)</option>
                            </select>
                        </div>
                    </div>

                    <div id="sms-admission-fields" style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div class="form-group-field">
                            <label class="form-field-label">Student Name</label>
                            <input type="text" name="student_name" placeholder="Candidate Name" class="form-control-input">
                        </div>
                        <div class="form-group-field">
                            <label class="form-field-label">Branch / Program</label>
                            <input type="text" name="program_name" placeholder="e.g. Computer Science" class="form-control-input">
                        </div>
                    </div>

                    <button type="submit" class="btn-dispatch btn-dispatch-sms">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Dispatch SMS via JioCX</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- Communication Tracker Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div>
                    <h2 style="font-size:1.15rem;font-weight:800;color:#0f172a;">Live Gateway Dispatch Logs (master.communication_logs)</h2>
                    <p style="font-size:0.76rem;color:#64748b;">Includes real-time delivery status, SmartPing trace IDs, and payload records.</p>
                </div>

                <!-- Filter Controls -->
                <form action="{{ route('sms.index') }}" method="GET" class="filter-form">
                    <select name="channel" class="filter-select" onchange="this.form.submit()">
                        <option value="all" {{ ($channel ?? '') === 'all' ? 'selected' : '' }}>All Channels</option>
                        <option value="whatsapp" {{ ($channel ?? '') === 'whatsapp' ? 'selected' : '' }}>WhatsApp Only</option>
                        <option value="sms" {{ ($channel ?? '') === 'sms' ? 'selected' : '' }}>SMS Only</option>
                    </select>

                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="all" {{ ($status ?? '') === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="SUBMITTED" {{ ($status ?? '') === 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED</option>
                        <option value="DELIVERED" {{ ($status ?? '') === 'DELIVERED' ? 'selected' : '' }}>DELIVERED</option>
                        <option value="FAILED" {{ ($status ?? '') === 'FAILED' ? 'selected' : '' }}>FAILED</option>
                    </select>

                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search phone, reg no, trace ID..." class="filter-search">

                    <button type="submit" class="btn-filter">Filter</button>
                    @if(!empty($channel) || !empty($status) || !empty($search))
                        <a href="{{ route('sms.index') }}" class="btn-reset">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID &amp; Time</th>
                            <th>Channel</th>
                            <th>Recipient Mobile</th>
                            <th>User / Reg No</th>
                            <th>Campaign / Template</th>
                            <th>Message Preview</th>
                            <th>Trace ID</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    <div style="font-weight:700;color:#0f172a;">#{{ $log->id }}</div>
                                    <div style="font-size:0.72rem;color:#94a3b8;">{{ $log->created_at?->format('d M Y, h:i A') ?? 'Just now' }}</div>
                                </td>
                                <td>
                                    @if($log->channel === 'whatsapp')
                                        <span class="badge-channel badge-wa">
                                            <span>💬</span> WhatsApp
                                        </span>
                                    @else
                                        <span class="badge-channel badge-sms-ch">
                                            <span>📱</span> SMS
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="mono-phone">+91 {{ $log->wapptophone }}</span>
                                </td>
                                <td>
                                    <div style="font-weight:700;color:#0f172a;">{{ $log->wauser ?: 'Prospective Student' }}</div>
                                    <div style="font-size:0.72rem;color:#64748b;">{{ $log->waregisterno ?: 'N/A' }}</div>
                                </td>
                                <td>
                                    <span style="font-weight:600;color:#334155;">{{ $log->template_name ?: 'vimsgridonetimepp' }}</span>
                                </td>
                                <td>
                                    <div class="msg-excerpt" title="{{ $log->wapptext }}">{{ $log->wapptext }}</div>
                                </td>
                                <td>
                                    @if($log->jiotraceid)
                                        <span class="trace-id-box" title="{{ $log->jiotraceid }}">{{ $log->jiotraceid }}</span>
                                    @else
                                        <span style="color:#94a3b8;font-size:0.75rem;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if(in_array($log->status, ['SUBMITTED', 'DELIVERED', 'SENT']))
                                        <span class="status-pill-table status-submitted">&bull; {{ $log->status }}</span>
                                    @else
                                        <span class="status-pill-table status-failed">&bull; {{ $log->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn-nav" style="padding:0.3rem 0.6rem;font-size:0.74rem;" onclick="viewPayload({{ json_encode($log->response_payload ?? ['status' => $log->status]) }})">
                                        View JSON
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align:center;padding:2.5rem;color:#94a3b8;">
                                    No communication logs recorded yet. Send a test WhatsApp or SMS above or trigger an OTP from the login page!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-pagination-area">
                <div style="font-size:0.78rem;color:#64748b;">
                    Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} records
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>

    </main>

    <!-- Modal for JSON Inspection -->
    <div id="jsonModal" class="modal-overlay" onclick="closeModal(event)">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 class="modal-title">Gateway API Response Payload</h3>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <pre id="jsonContent" class="json-pre"></pre>
            </div>
        </div>
    </div>

    <script>
        function toggleWaFields(val) {
            const fields = document.getElementById('wa-admission-fields');
            fields.style.display = (val === 'admission') ? 'grid' : 'none';
        }

        function toggleSmsFields(val) {
            const fields = document.getElementById('sms-admission-fields');
            fields.style.display = (val === 'admission') ? 'grid' : 'none';
        }

        function viewPayload(payload) {
            const modal = document.getElementById('jsonModal');
            const content = document.getElementById('jsonContent');
            content.textContent = JSON.stringify(payload, null, 2);
            modal.style.display = 'flex';
        }

        function closeModal(e) {
            document.getElementById('jsonModal').style.display = 'none';
        }
    </script>
</body>
</html>
