<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admission Onboarding Portal | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-shield-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-primary: #1e3a8a;
            --brand-accent: #2563eb;
            --brand-sky: #0ea5e9;
            --brand-emerald: #10b981;
            --brand-amber: #f59e0b;
            --brand-purple: #8b5cf6;
            --bg-page: #f8fafc;
            --card-bg: #ffffff;
            --text-dark: #0f172a;
            --text-slate: #334155;
            --text-muted: #64748b;
            --border-color: #e2e8f0;
            --border-hover: #cbd5e1;
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
        .dash-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .nav-inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-cluster {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
        }

        .brand-logo-img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.08));
        }

        .brand-title-wrap {
            display: flex;
            flex-direction: column;
        }

        .brand-main-title {
            font-size: 0.98rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-tag {
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            text-transform: uppercase;
        }

        .brand-sub-title {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 500;
        }

        .user-nav-cluster {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info-text {
            text-align: right;
            display: flex;
            flex-direction: column;
        }

        .user-name-title {
            font-size: 0.88rem;
            font-weight: 800;
            color: #0f172a;
        }

        .user-meta-sub {
            font-size: 0.72rem;
            color: #64748b;
            font-weight: 500;
        }

        .user-avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #ffffff;
            font-weight: 800;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }

        .btn-nav-action {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.9rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-crm-link {
            background: #eff6ff;
            color: #2563eb;
            border-color: #bfdbfe;
        }
        .btn-crm-link:hover {
            background: #dbeafe;
        }

        .btn-signout {
            background: #fef2f2;
            color: #dc2626;
            border-color: #fecaca;
        }
        .btn-signout:hover {
            background: #fee2e2;
        }

        /* Container */
        .dash-container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 1.75rem 1.5rem 3.5rem;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        /* Hero Banner */
        .welcome-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #1e3a8a 100%);
            border-radius: 1.5rem;
            padding: 2rem 2.25rem;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.35);
        }

        .welcome-hero::after {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 260px;
            height: 260px;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .welcome-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 600;
            color: #7dd3fc;
            margin-bottom: 0.75rem;
        }

        .welcome-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 8px #10b981;
        }

        .welcome-heading {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.4rem;
        }

        .welcome-heading-grad {
            background: linear-gradient(90deg, #7dd3fc, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-subtitle {
            font-size: 0.92rem;
            color: #cbd5e1;
            max-width: 820px;
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }

        .meta-badges-cluster {
            display: flex;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .meta-badge {
            font-size: 0.75rem;
            padding: 0.35rem 0.85rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .meta-badge-success {
            background: rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.4);
            color: #6ee7b7;
            font-weight: 700;
        }

        /* 8-STEP PROGRESS TRACKER (CORE FEATURE) */
        .stepper-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            padding: 1.5rem;
            box-shadow: 0 4px 16px -2px rgba(0, 0, 0, 0.04);
        }

        .stepper-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .stepper-title-box h2 {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stepper-title-box p {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 0.2rem;
        }

        .stepper-progress-box {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .progress-bar-track {
            width: 180px;
            height: 10px;
            background: #e2e8f0;
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #10b981);
            border-radius: 9999px;
            transition: width 0.4s ease;
        }

        .progress-percent-label {
            font-size: 0.82rem;
            font-weight: 800;
            color: #2563eb;
        }

        /* Stepper 8-Item Bar */
        .stepper-nav-bar {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 0.5rem;
            border-top: 1px solid #f1f5f9;
            padding-top: 1.25rem;
            overflow-x: auto;
        }

        .step-nav-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.85rem;
            padding: 0.75rem 0.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            color: var(--text-slate);
            transition: all 0.2s ease;
            position: relative;
            min-width: 110px;
        }

        .step-nav-btn:hover {
            background: #eff6ff;
            border-color: #bfdbfe;
            transform: translateY(-2px);
        }

        .step-nav-btn.is-active {
            background: #eff6ff;
            border-color: #2563eb;
            color: #1d4ed8;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
        }

        .step-nav-btn.is-completed {
            background: #f0fdf4;
            border-color: #86efac;
            color: #166534;
        }

        .step-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: 800;
            margin-bottom: 0.35rem;
            background: #e2e8f0;
            color: #475569;
            transition: all 0.2s ease;
        }

        .step-nav-btn.is-active .step-circle {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        .step-nav-btn.is-completed .step-circle {
            background: #10b981;
            color: #ffffff;
        }

        .step-nav-name {
            font-size: 0.74rem;
            font-weight: 700;
            line-height: 1.25;
            margin-bottom: 0.15rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .step-nav-badge {
            font-size: 0.62rem;
            font-weight: 700;
            padding: 0.1rem 0.4rem;
            border-radius: 9999px;
            text-transform: uppercase;
        }

        .badge-done { background: #dcfce7; color: #15803d; }
        .badge-active { background: #dbeafe; color: #1d4ed8; }
        .badge-pending { background: #f1f5f9; color: #64748b; }

        /* STEP CONTENT CONTAINER */
        .step-panel-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .step-panel-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .step-panel-header h3 {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .step-panel-header p {
            font-size: 0.85rem;
            color: #bfdbfe;
            margin-top: 0.25rem;
        }

        .step-header-tag {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            padding: 0.35rem 0.85rem;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .step-panel-body {
            padding: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        /* Form Controls */
        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .form-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .field-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .required-star {
            color: #ef4444;
        }

        .field-input, .field-select, .field-textarea {
            width: 100%;
            padding: 0.75rem 0.95rem;
            border: 1px solid #cbd5e1;
            border-radius: 0.65rem;
            font-size: 0.88rem;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .field-input:focus, .field-select:focus, .field-textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .field-input[readonly], .field-input:disabled {
            background: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }

        .section-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.25rem 1.5rem;
        }

        .section-box-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Buttons & Footer Actions */
        .step-footer-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1.5rem;
            border-top: 1px solid #e2e8f0;
            margin-top: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-step-prev {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.7rem 1.35rem;
            border-radius: 0.75rem;
            font-size: 0.88rem;
            font-weight: 700;
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-step-prev:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .btn-step-next {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.75rem;
            border-radius: 0.75rem;
            font-size: 0.9rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e3a8a, #2563eb);
            color: #ffffff;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
        }

        .btn-step-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }

        /* Document KYC Cards */
        .kyc-docs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }

        .kyc-doc-card {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 1.25rem;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.75rem;
            transition: all 0.2s ease;
        }

        .kyc-doc-card:hover {
            border-color: #2563eb;
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.08);
        }

        .kyc-doc-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .kyc-doc-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .kyc-doc-title {
            font-size: 0.92rem;
            font-weight: 800;
            color: #0f172a;
        }

        .kyc-doc-sub {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* Fee Summary Table */
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.88rem;
        }

        .fee-table th {
            background: #f8fafc;
            padding: 0.85rem 1rem;
            text-align: left;
            font-weight: 800;
            color: #334155;
            border-bottom: 1px solid #e2e8f0;
        }

        .fee-table td {
            padding: 0.95rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .fee-table tr:last-child td {
            border-bottom: none;
            font-weight: 800;
            font-size: 1rem;
            background: #f8fafc;
        }

        /* Alert notifications */
        .alert-box {
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.88rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-error {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
        }

        /* Footer */
        .dash-footer {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1320px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #64748b;
        }

        .footer-inner a {
            color: #2563eb;
            text-decoration: none;
        }

        @media (max-width: 1024px) {
            .stepper-nav-bar {
                grid-template-columns: repeat(4, 1fr);
            }
            .form-grid-3, .form-grid-4, .kyc-docs-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .stepper-nav-bar {
                grid-template-columns: repeat(2, 1fr);
            }
            .form-grid-3, .form-grid-2, .form-grid-4, .kyc-docs-grid {
                grid-template-columns: 1fr;
            }
            .welcome-heading {
                font-size: 1.5rem;
            }
            .nav-inner {
                padding: 0 1rem;
            }
            .dash-container {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>

    <!-- TOP NAVIGATION -->
    <nav class="dash-nav">
        <div class="nav-inner">
            <a href="{{ route('dashboards.index') }}" class="brand-cluster">
                <img src="/images/vignan-shield-logo.png" alt="VFSTR Vignan University" class="brand-logo-img">
                <div class="brand-title-wrap">
                    <div class="brand-main-title">
                        <span>VFSTR &bull; VIGNAN UNIVERSITY</span>
                        <span class="brand-tag">Student Admission Portal</span>
                    </div>
                    <span class="brand-sub-title">8-Stage Academic Enrollment &amp; Onboarding Administration</span>
                </div>
            </a>

            <div class="user-nav-cluster">
                <div class="user-info-text">
                    <span class="user-name-title">{{ $displayName }}</span>
                    <span class="user-meta-sub">{{ $roleName }} &bull; {{ $appNo }} &bull; +91 {{ $user?->phnno }}</span>
                </div>

                <div class="user-avatar-circle">
                    {{ strtoupper(substr($displayName, 0, 1)) }}
                </div>

                <a href="{{ route('crm.index') }}" class="btn-nav-action btn-crm-link" title="CRM Executive Portal">
                    <span>👑 CRM Portal</span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-nav-action btn-signout">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTAINER -->
    <main class="dash-container">

        <!-- WELCOME HERO BANNER -->
        <section class="welcome-hero">
            <div class="welcome-pill">
                <span class="welcome-dot"></span>
                <span>Admissions Batch 2026 - 2027 &bull; Student Candidate Onboarding</span>
            </div>

            <h1 class="welcome-heading">
                Welcome, <span class="welcome-heading-grad">{{ $displayName }}</span>!
            </h1>

            <p class="welcome-subtitle">
                Your 8-Stage Admission Onboarding Process for <strong>{{ $programName }}</strong> ({{ $branchName }}, Regulation {{ $regulation }}) is active. Complete each step below to finalize your academic enrollment and allotment order.
            </p>

            <div class="meta-badges-cluster">
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Application No:</strong> {{ $appNo }}
                </span>
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Register No:</strong> {{ $regNo }}
                </span>
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Allocated Program:</strong> {{ $programName }}
                </span>
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Campus:</strong> {{ $campusName }}
                </span>
                <span class="meta-badge meta-badge-success">
                    &bull; Admission Confirmed
                </span>
            </div>
        </section>

        <!-- FLASH NOTIFICATIONS -->
        @if(session('status'))
            <div class="alert-box alert-success">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert-box alert-error">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <div>
                    <strong>Please check the following errors:</strong>
                    <ul style="margin-left: 1.25rem; margin-top: 0.25rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <!-- 8-STEP PROGRESS TRACKER (LANDING PAGE CORE) -->
        <section class="stepper-card">
            <div class="stepper-header">
                <div class="stepper-title-box">
                    <h2>
                        <span>8-Step Admission Onboarding Process</span>
                    </h2>
                    <p>Click on any step below to view, complete, or update your candidate enrollment details</p>
                </div>
                <div class="stepper-progress-box">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                    <span class="progress-percent-label">{{ $progressPercent }}% Complete ({{ $completedCount }}/8)</span>
                </div>
            </div>

            <!-- 8 Steps Bar -->
            <div class="stepper-nav-bar">
                @php
                    $stepIcons = [
                        1 => '📝',
                        2 => '👤',
                        3 => '👨‍👩‍👦',
                        4 => '📍',
                        5 => '🎓',
                        6 => '📄',
                        7 => '🏛️',
                        8 => '💳',
                    ];
                @endphp

                @foreach($stages as $num => $stg)
                    @php
                        $isActive = ($activeStep === $num);
                        $isDone = ($stg['status'] === 'completed');
                    @endphp
                    <a href="{{ route('dashboards.index', ['step' => $num]) }}"
                       class="step-nav-btn {{ $isActive ? 'is-active' : '' }} {{ $isDone ? 'is-completed' : '' }}"
                       title="Step {{ $num }}: {{ $stg['title'] }}">
                        <div class="step-circle">
                            @if($isDone) ✓ @else {{ $num }} @endif
                        </div>
                        <span class="step-nav-name">{{ $stepIcons[$num] }} Step {{ $num }}</span>
                        <span class="step-nav-sub" style="font-size:0.68rem; color:#64748b; margin-bottom:0.25rem;">{{ $stg['title'] }}</span>
                        <span class="step-nav-badge {{ $isDone ? 'badge-done' : ($isActive ? 'badge-active' : 'badge-pending') }}">
                            @if($isDone) Completed @elseif($isActive) Current @else Pending @endif
                        </span>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- STEP CONTENT PANEL -->
        <section class="step-panel-card">

            <!-- ================= STEP 1: BASIC REGISTRATION DETAILS ================= -->
            @if($activeStep === 1)
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Step 1 of 8 &bull; Initial Admission Capture
                        </div>
                        <h3>
                            <span>📝 Step 1: Basic Registration &amp; Program Selection</span>
                        </h3>
                        <p>Verified candidate registration details in Vignan University CRM database.</p>
                    </div>
                    <span class="step-header-tag">✓ Completed on Registration</span>
                </div>

                <div class="step-panel-body">
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>📌 Registered Candidate Summary</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Candidate Name</label>
                                <input type="text" value="{{ $displayName }}" readonly class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Primary Mobile</label>
                                <input type="text" value="+91 {{ $user?->phnno }}" readonly class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Personal Email</label>
                                <input type="text" value="{{ $student?->personal_email ?: ($crmStudent?->emailid ?: $user?->personalmail) }}" readonly class="field-input">
                            </div>
                        </div>

                        <div class="form-grid-3" style="margin-top: 1.25rem;">
                            <div class="field-group">
                                <label class="field-label">Allocated Campus</label>
                                <input type="text" value="{{ $campusName }}" readonly class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Allocated Program</label>
                                <input type="text" value="{{ $programName }}" readonly class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Core Specialization Branch</label>
                                <input type="text" value="{{ $branchName }}" readonly class="field-input">
                            </div>
                        </div>

                        <div class="form-grid-3" style="margin-top: 1.25rem;">
                            <div class="field-group">
                                <label class="field-label">Registration No</label>
                                <input type="text" value="{{ $regNo }}" readonly class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Application Nano No</label>
                                <input type="text" value="{{ $appNo }}" readonly class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Admission Batch</label>
                                <input type="text" value="Batch 2026 - 2027 (Regulation {{ $regulation }})" readonly class="field-input">
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <div style="font-size:0.85rem; color:#64748b;">
                            Step 1 status: <strong style="color:#10b981;">✓ Successfully Verified in Database</strong>
                        </div>
                        <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-step-next">
                            <span>Proceed to Step 2: Personal Profile</span>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

            <!-- ================= STEP 2: PERSONAL PROFILE & IDENTITY ================= -->
            @elseif($activeStep === 2)
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Step 2 of 8 &bull; Candidate Identification
                        </div>
                        <h3>
                            <span>👤 Step 2: Personal Profile &amp; Identity</span>
                        </h3>
                        <p>Provide your official personal details, government identity numbers, and demographic records.</p>
                    </div>
                    <span class="step-header-tag">Step 2: Profile</span>
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="2">

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>🆔 Personal &amp; Demographic Information</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Applicant's Full Name (As per SSC)</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="student_name" value="{{ old('student_name', $student?->student_name ?? $displayName) }}" required class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">
                                    <span>Date of Birth</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="date" name="dob" value="{{ old('dob', $student?->dob ? date('Y-m-d', strtotime($student->dob)) : '2008-05-15') }}" required class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">
                                    <span>Gender</span>
                                    <span class="required-star">*</span>
                                </label>
                                <select name="gender" class="field-select" required>
                                    <option value="Male" {{ old('gender', $student?->gender ?? 'Male') === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $student?->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $student?->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-3" style="margin-top: 1.25rem;">
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Aadhaar Card Number</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="aadhar_no" value="{{ old('aadhar_no', $student?->aadhar_no ?? '987654321012') }}" placeholder="12-digit Aadhaar number" maxlength="14" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">
                                    <span>Academic Bank of Credits (ABC ID)</span>
                                </label>
                                <input type="text" name="abc_id" value="{{ old('abc_id', $student?->abc_id ?? 'ABC-2026-90218') }}" placeholder="e.g. 12-digit ABC ID" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">Blood Group</label>
                                <select name="blood_group" class="field-select">
                                    <option value="O+">O Positive (O+)</option>
                                    <option value="A+">A Positive (A+)</option>
                                    <option value="B+">B Positive (B+)</option>
                                    <option value="AB+">AB Positive (AB+)</option>
                                    <option value="O-">O Negative (O-)</option>
                                    <option value="A-">A Negative (A-)</option>
                                    <option value="B-">B Negative (B-)</option>
                                    <option value="AB-">AB Negative (AB-)</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-3" style="margin-top: 1.25rem;">
                            <div class="field-group">
                                <label class="field-label">Community / Category</label>
                                <select name="category" class="field-select">
                                    <option value="OC">OC / General Category</option>
                                    <option value="OBC">OBC (Other Backward Classes)</option>
                                    <option value="EWS">EWS (Economically Weaker Section)</option>
                                    <option value="SC">SC (Scheduled Caste)</option>
                                    <option value="ST">ST (Scheduled Tribe)</option>
                                </select>
                            </div>

                            <div class="field-group">
                                <label class="field-label">Religion</label>
                                <input type="text" name="religion" value="Hindu" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">Nationality</label>
                                <input type="text" name="nationality" value="Indian" class="field-input">
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 1]) }}" class="btn-step-prev">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Back to Step 1</span>
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Continue to Step 3: Parent Details</span>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

            <!-- ================= STEP 3: PARENT & FAMILY DETAILS ================= -->
            @elseif($activeStep === 3)
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Step 3 of 8 &bull; Family Background
                        </div>
                        <h3>
                            <span>👨‍👩‍👦 Step 3: Parent &amp; Guardian Information</span>
                        </h3>
                        <p>Fill in parent contact information, occupation, and annual family income details.</p>
                    </div>
                    <span class="step-header-tag">Step 3: Parents</span>
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="3">

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>👨 Father / Guardian Information</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Father's Full Name</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="father_name" value="N. Venkateswara Rao" required class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Father's Mobile Number</label>
                                <input type="text" name="father_mobile" value="+91 9440123456" class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Father's Occupation</label>
                                <input type="text" name="father_occ" value="Business / Agriculture" class="field-input">
                            </div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>👩 Mother Information</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Mother's Full Name</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="mother_name" value="N. Lakshmi Devi" required class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Mother's Mobile Number</label>
                                <input type="text" name="mother_mobile" value="+91 9440654321" class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Mother's Occupation</label>
                                <input type="text" name="mother_occ" value="Home Maker" class="field-input">
                            </div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>💰 Annual Family Income &amp; Emergency Contact</span>
                        </div>
                        <div class="form-grid-2">
                            <div class="field-group">
                                <label class="field-label">Annual Family Income</label>
                                <select name="annual_income" class="field-select">
                                    <option value="1_to_2_lakhs">₹1,00,000 - ₹2,50,000</option>
                                    <option value="2_to_5_lakhs" selected>₹2,50,000 - ₹5,00,000</option>
                                    <option value="5_to_8_lakhs">₹5,00,000 - ₹8,00,000</option>
                                    <option value="above_8_lakhs">Above ₹8,00,000</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Emergency Guardian Contact</label>
                                <input type="text" name="emergency_phone" value="+91 {{ $user?->phnno }}" class="field-input">
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-step-prev">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Back to Step 2</span>
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Continue to Step 4: Address Details</span>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

            <!-- ================= STEP 4: ADDRESS & COMMUNICATION ================= -->
            @elseif($activeStep === 4)
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Step 4 of 8 &bull; Residence &amp; Location
                        </div>
                        <h3>
                            <span>📍 Step 4: Communication &amp; Permanent Address</span>
                        </h3>
                        <p>Provide postal communication and domicile address records for dispatching university credentials.</p>
                    </div>
                    <span class="step-header-tag">Step 4: Address</span>
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="4">

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>🏡 Permanent Residential Address</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Door / Flat / House No</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="door_no" value="D.No: 4-12/A" required class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Street / Colony / Landmark</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="street" value="Vignan Nagar, Main Road" required class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Village / Town / City</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="city" value="Vadlamudi" required class="field-input">
                            </div>
                        </div>

                        <div class="form-grid-3" style="margin-top: 1.25rem;">
                            <div class="field-group">
                                <label class="field-label">
                                    <span>District</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="district" value="Guntur" required class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">
                                    <span>State</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="state" value="Andhra Pradesh" required class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Postal Pincode</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="pincode" value="522213" maxlength="6" required class="field-input">
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 3]) }}" class="btn-step-prev">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Back to Step 3</span>
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Continue to Step 5: Academic Records</span>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

            <!-- ================= STEP 5: ACADEMIC QUALIFICATIONS ================= -->
            @elseif($activeStep === 5)
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Step 5 of 8 &bull; Prior Education
                        </div>
                        <h3>
                            <span>🎓 Step 5: Academic Qualifications (10th &amp; Intermediate / +2)</span>
                        </h3>
                        <p>Fill and verify your 10th Standard (SSC) and Intermediate (10+2) academic examination marks.</p>
                    </div>
                    <span class="step-header-tag">Step 5: Academics</span>
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="5">

                    <!-- SECTION 1: 10TH STANDARD (SSC) -->
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>📘 10th Standard (SSC / Matriculation) Details</span>
                            @if($edu10th)
                                <span style="margin-left:auto; font-size:0.72rem; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; padding:0.15rem 0.6rem; border-radius:9999px;">✓ Saved</span>
                            @endif
                        </div>

                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Board / Authority Name</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="ssc_board" value="{{ old('ssc_board', $edu10th?->board_university ?? 'State Board of Secondary Education, AP (BSEAP)') }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">
                                    <span>School / Institution Name</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="ssc_school" value="{{ old('ssc_school', $edu10th?->institution_name ?? 'Z.P. High School') }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">
                                    <span>SSC Hall Ticket / Roll Number</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="ssc_hallticket" value="{{ old('ssc_hallticket', $edu10th?->hall_ticket_no ?? '2401827491') }}" class="field-input">
                            </div>
                        </div>

                        <div class="form-grid-4" style="margin-top: 1.25rem;">
                            <div class="field-group">
                                <label class="field-label">Year of Passing</label>
                                <input type="number" name="ssc_year" min="2010" max="2026" value="{{ old('ssc_year', $edu10th?->year_of_passing ?? 2024) }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">Marks Obtained</label>
                                <input type="number" step="0.01" name="ssc_marks" value="{{ old('ssc_marks', $edu10th?->marks_obtained ?? 540) }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">Max Marks</label>
                                <input type="number" step="0.01" name="ssc_max_marks" value="{{ old('ssc_max_marks', $edu10th?->max_marks ?? 600) }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">CGPA / Grade Points</label>
                                <input type="number" step="0.01" name="ssc_cgpa" value="{{ old('ssc_cgpa', $edu10th?->cgpa ?? 9.5) }}" class="field-input">
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: INTERMEDIATE (10+2) -->
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>📗 Intermediate / 12th (10+2) / Polytechnic Records</span>
                            @if($eduInter)
                                <span style="margin-left:auto; font-size:0.72rem; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; padding:0.15rem 0.6rem; border-radius:9999px;">✓ Saved</span>
                            @endif
                        </div>

                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Board / Council Name</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="inter_board" value="{{ old('inter_board', $eduInter?->board_university ?? 'Board of Intermediate Education, AP (BIEAP)') }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">
                                    <span>Junior College / Institution Name</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="inter_college" value="{{ old('inter_college', $eduInter?->institution_name ?? 'Vignan Junior College') }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">
                                    <span>Stream / Group</span>
                                    <span class="required-star">*</span>
                                </label>
                                <select name="inter_stream" class="field-select">
                                    <option value="MPC" {{ old('inter_stream', $eduInter?->study_mode ?? 'MPC') === 'MPC' ? 'selected' : '' }}>MPC (Maths, Physics, Chemistry)</option>
                                    <option value="BiPC" {{ old('inter_stream', $eduInter?->study_mode) === 'BiPC' ? 'selected' : '' }}>BiPC (Biology, Physics, Chemistry)</option>
                                    <option value="MEC" {{ old('inter_stream', $eduInter?->study_mode) === 'MEC' ? 'selected' : '' }}>MEC (Maths, Economics, Commerce)</option>
                                    <option value="Diploma" {{ old('inter_stream', $eduInter?->study_mode) === 'Diploma' ? 'selected' : '' }}>Polytechnic Diploma</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-grid-4" style="margin-top: 1.25rem;">
                            <div class="field-group">
                                <label class="field-label">
                                    <span>Inter Hall Ticket Number</span>
                                    <span class="required-star">*</span>
                                </label>
                                <input type="text" name="inter_hallticket" value="{{ old('inter_hallticket', $eduInter?->hall_ticket_no ?? '2601928374') }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">Year of Passing</label>
                                <input type="number" name="inter_year" min="2010" max="2026" value="{{ old('inter_year', $eduInter?->year_of_passing ?? 2026) }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">Marks Obtained</label>
                                <input type="number" step="0.01" name="inter_marks" value="{{ old('inter_marks', $eduInter?->marks_obtained ?? 950) }}" class="field-input">
                            </div>

                            <div class="field-group">
                                <label class="field-label">Total / Max Marks</label>
                                <input type="number" step="0.01" name="inter_max_marks" value="{{ old('inter_max_marks', $eduInter?->max_marks ?? 1000) }}" class="field-input">
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 4]) }}" class="btn-step-prev">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Back to Step 4</span>
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Continue to Step 6: Document KYC</span>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

            <!-- ================= STEP 6: DOCUMENT KYC UPLOAD ================= -->
            @elseif($activeStep === 6)
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Step 6 of 8 &bull; Certificate Verification
                        </div>
                        <h3>
                            <span>📄 Step 6: Document &amp; Certificate KYC Upload</span>
                        </h3>
                        <p>Upload scans of your official academic marksheets and government identification for verification.</p>
                    </div>
                    <span class="step-header-tag">Step 6: KYC Upload</span>
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="6">

                    <div class="kyc-docs-grid">
                        <div class="kyc-doc-card">
                            <div class="kyc-doc-top">
                                <div class="kyc-doc-icon">📷</div>
                                <span class="step-nav-badge badge-done">✓ Uploaded</span>
                            </div>
                            <div>
                                <h4 class="kyc-doc-title">Passport Size Photograph</h4>
                                <p class="kyc-doc-sub">Format: JPG, PNG (Max 2MB)</p>
                            </div>
                            <input type="file" class="field-input" style="padding:0.4rem; font-size:0.75rem;">
                        </div>

                        <div class="kyc-doc-card">
                            <div class="kyc-doc-top">
                                <div class="kyc-doc-icon">📜</div>
                                <span class="step-nav-badge badge-done">✓ Uploaded</span>
                            </div>
                            <div>
                                <h4 class="kyc-doc-title">10th Standard (SSC) Memo</h4>
                                <p class="kyc-doc-sub">Official Board Marks Certificate</p>
                            </div>
                            <input type="file" class="field-input" style="padding:0.4rem; font-size:0.75rem;">
                        </div>

                        <div class="kyc-doc-card">
                            <div class="kyc-doc-top">
                                <div class="kyc-doc-icon">📑</div>
                                <span class="step-nav-badge badge-done">✓ Uploaded</span>
                            </div>
                            <div>
                                <h4 class="kyc-doc-title">Intermediate (10+2) Memo</h4>
                                <p class="kyc-doc-sub">Board Marks / Hall Ticket Copy</p>
                            </div>
                            <input type="file" class="field-input" style="padding:0.4rem; font-size:0.75rem;">
                        </div>

                        <div class="kyc-doc-card">
                            <div class="kyc-doc-top">
                                <div class="kyc-doc-icon">📋</div>
                                <span class="step-nav-badge badge-done">✓ Uploaded</span>
                            </div>
                            <div>
                                <h4 class="kyc-doc-title">Transfer Certificate (TC)</h4>
                                <p class="kyc-doc-sub">Issued by Junior College / School</p>
                            </div>
                            <input type="file" class="field-input" style="padding:0.4rem; font-size:0.75rem;">
                        </div>

                        <div class="kyc-doc-card">
                            <div class="kyc-doc-top">
                                <div class="kyc-doc-icon">💳</div>
                                <span class="step-nav-badge badge-done">✓ Uploaded</span>
                            </div>
                            <div>
                                <h4 class="kyc-doc-title">Aadhaar Card Copy</h4>
                                <p class="kyc-doc-sub">Front &amp; Back Identity Verification</p>
                            </div>
                            <input type="file" class="field-input" style="padding:0.4rem; font-size:0.75rem;">
                        </div>

                        <div class="kyc-doc-card">
                            <div class="kyc-doc-top">
                                <div class="kyc-doc-icon">🏅</div>
                                <span class="step-nav-badge badge-active">Optional</span>
                            </div>
                            <div>
                                <h4 class="kyc-doc-title">Study &amp; Conduct Certificate</h4>
                                <p class="kyc-doc-sub">From Class 6th to Intermediate</p>
                            </div>
                            <input type="file" class="field-input" style="padding:0.4rem; font-size:0.75rem;">
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 5]) }}" class="btn-step-prev">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Back to Step 5</span>
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Continue to Step 7: Campus Life</span>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

            <!-- ================= STEP 7: CAMPUS LIFE & SPECIALIZATION ================= -->
            @elseif($activeStep === 7)
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Step 7 of 8 &bull; Campus Options &amp; Branch Track
                        </div>
                        <h3>
                            <span>🏛️ Step 7: Campus Life &amp; Specialization Preferences</span>
                        </h3>
                        <p>Configure your hostel accommodation, university bus routes, and elective specialization tracks.</p>
                    </div>
                    <span class="step-header-tag">Step 7: Campus Options</span>
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="7">

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>🎓 Allocated Program &amp; Core Branch</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Degree Program</label>
                                <input type="text" value="{{ $programName }}" readonly class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Allocated Core Branch</label>
                                <input type="text" value="{{ $branchName }}" readonly class="field-input">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Elective Minor Specialization</label>
                                <select name="specialization_track" class="field-select">
                                    <option value="AI_ML">Artificial Intelligence &amp; Precision Farming</option>
                                    <option value="IOT_AG">Smart IoT &amp; Farm Automation</option>
                                    <option value="CORE" selected>Advanced Core Agriculture Honors</option>
                                    <option value="DATA_SCI">Agricultural Data Science</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>🏠 On-Campus Student Accommodation</span>
                        </div>
                        <div class="form-grid-2">
                            <div class="field-group">
                                <label class="field-label">Hostel Accommodation Choice</label>
                                <select name="hostel_type" class="field-select">
                                    <option value="AC">On-Campus AC Student Hostel (Twin Sharing)</option>
                                    <option value="NON_AC" selected>On-Campus Non-AC Student Hostel (Triple Sharing)</option>
                                    <option value="DAY_SCHOLAR">Day Scholar (Living at home / local residence)</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Dietary Preference</label>
                                <select name="mess_type" class="field-select">
                                    <option value="SOUTH_VEG">South Indian Vegetarian</option>
                                    <option value="SOUTH_NON_VEG" selected>South Indian Non-Vegetarian</option>
                                    <option value="NORTH_INDIAN">North Indian Special Mess</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>🚌 University Transportation</span>
                        </div>
                        <div class="form-grid-2">
                            <div class="field-group">
                                <label class="field-label">University Shuttle Bus Pass</label>
                                <select name="bus_required" class="field-select">
                                    <option value="NO" selected>Not Required (Hosteler)</option>
                                    <option value="GUNTUR">Guntur City Route (Pass: ₹28,000/yr)</option>
                                    <option value="VIJAYAWADA">Vijayawada Route (Pass: ₹36,000/yr)</option>
                                    <option value="TENALI">Tenali Route (Pass: ₹24,000/yr)</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Campus Facility Access</label>
                                <input type="text" value="Central Library, Sports Complex, Gym &amp; Wi-Fi Included" readonly class="field-input">
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 6]) }}" class="btn-step-prev">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Back to Step 6</span>
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Continue to Step 8: Fee &amp; Allotment</span>
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </form>

            <!-- ================= STEP 8: FEE PAYMENT & PROVISIONAL ALLOTMENT ================= -->
            @elseif($activeStep === 8)
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Step 8 of 8 &bull; Final Admission Stage
                        </div>
                        <h3>
                            <span>💳 Step 8: Admission Fee &amp; Provisional Allotment Order</span>
                        </h3>
                        <p>Review your tuition fee breakdown, download your official Allotment Order and Fee Payment Receipt.</p>
                    </div>
                    <span class="step-header-tag" style="background:#10b981; border-color:#86efac; color:#ffffff;">✓ Allotment Confirmed</span>
                </div>

                <div class="step-panel-body">
                    <!-- FEE TABLE SUMMARY -->
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>🧾 Academic Year 2026-27 Fee Breakdown</span>
                        </div>
                        <table class="fee-table">
                            <thead>
                                <tr>
                                    <th>Fee Description</th>
                                    <th>Category</th>
                                    <th>Amount (INR)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Application &amp; Processing Fee</td>
                                    <td>One-Time</td>
                                    <td>₹ 1,200</td>
                                    <td><span class="step-nav-badge badge-done">Paid ✓</span></td>
                                </tr>
                                <tr>
                                    <td>Admission &amp; Seat Confirmation Deposit</td>
                                    <td>One-Time</td>
                                    <td>₹ 25,000</td>
                                    <td><span class="step-nav-badge badge-done">Paid ✓</span></td>
                                </tr>
                                <tr>
                                    <td>Semester I Tuition &amp; Academic Fee</td>
                                    <td>Semester I</td>
                                    <td>₹ 50,000</td>
                                    <td><span class="step-nav-badge badge-done">Paid ✓</span></td>
                                </tr>
                                <tr>
                                    <td>Merit Scholarship Concession (VSAT 2026)</td>
                                    <td>Waiver</td>
                                    <td style="color:#10b981;">- ₹ 15,000</td>
                                    <td><span class="step-nav-badge badge-done">Applied ✓</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Total Net Fee Paid</strong></td>
                                    <td><strong>Receipt #VU-2026-{{ substr($regNo, -5) }}</strong></td>
                                    <td><strong style="color:#2563eb;">₹ 61,200</strong></td>
                                    <td><span class="step-nav-badge badge-done">Confirmed ✓</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- DOWNLOAD CARDS -->
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>📥 Official Admission Documents Download</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="kyc-doc-card" style="text-align:center; padding:1.5rem;">
                                <div style="font-size:2rem; margin-bottom:0.5rem;">📜</div>
                                <h4 class="kyc-doc-title">Provisional Allotment Order</h4>
                                <p class="kyc-doc-sub">Signed Dean Allotment Letter</p>
                                <a href="javascript:alert('Downloading Provisional Allotment Order (PDF)...')" class="btn-step-next" style="margin-top:0.75rem; justify-content:center; text-decoration:none;">
                                    <span>Download PDF</span>
                                </a>
                            </div>

                            <div class="kyc-doc-card" style="text-align:center; padding:1.5rem;">
                                <div style="font-size:2rem; margin-bottom:0.5rem;">🧾</div>
                                <h4 class="kyc-doc-title">Official Fee Payment Receipt</h4>
                                <p class="kyc-doc-sub">Vignan Accounts Verified Receipt</p>
                                <a href="javascript:alert('Downloading Official Fee Payment Receipt (PDF)...')" class="btn-step-next" style="margin-top:0.75rem; justify-content:center; text-decoration:none;">
                                    <span>Download Receipt</span>
                                </a>
                            </div>

                            <div class="kyc-doc-card" style="text-align:center; padding:1.5rem;">
                                <div style="font-size:2rem; margin-bottom:0.5rem;">📅</div>
                                <h4 class="kyc-doc-title">Campus Induction Schedule</h4>
                                <p class="kyc-doc-sub">Orientation &amp; Hostel Move-in Guide</p>
                                <a href="javascript:alert('Downloading Orientation & Campus Induction Schedule (PDF)...')" class="btn-step-next" style="margin-top:0.75rem; justify-content:center; text-decoration:none;">
                                    <span>Download Guide</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 7]) }}" class="btn-step-prev">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>Back to Step 7</span>
                        </a>
                        <form action="{{ route('dashboards.save-step') }}" method="POST">
                            @csrf
                            <input type="hidden" name="step" value="8">
                            <button type="submit" class="btn-step-next" style="background: linear-gradient(135deg, #059669, #10b981);">
                                <span>✓ All Steps Completed - Finalize Admission</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endif

        </section>

    </main>

    <!-- FOOTER -->
    <footer class="dash-footer">
        <div class="footer-inner">
            <div>
                &copy; 2026 VFSTR &bull; Vignan's Foundation for Science, Technology &amp; Research (Deemed to be University). All rights reserved.
            </div>
            <div>
                Student Admission Portal &bull; <a href="https://vignan.ac.in" target="_blank">Official University Site</a>
            </div>
        </div>
    </footer>

</body>
</html>
