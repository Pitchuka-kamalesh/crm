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

                @if($user && ($user->user_type == 2 || in_array((int)$user->role_id, [1, 2, 3, 4])))
                    <a href="{{ route('crm.index') }}" class="btn-nav-action btn-crm-link" title="CRM Executive Portal">
                        <span>👑 CRM Portal</span>
                    </a>
                @endif

                <a href="{{ route('logout') }}" class="btn-nav-action btn-signout" style="text-decoration:none;">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Sign Out</span>
                </a>
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
                Your 8-Stage Admission Onboarding Process @if($programName)for <strong>{{ $programName }}</strong> @if($branchName)({{ $branchName }})@endif @endif is active. Complete each step below to finalize your admission.
            </p>

            <div class="meta-badges-cluster">
                @if($appNo)
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Application No:</strong> {{ $appNo }}
                </span>
                @endif
                @if($regNo)
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Register No:</strong> {{ $regNo }}
                </span>
                @endif
                @if($programName)
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Program:</strong> {{ $programName }}
                </span>
                @endif
                @if($campusName)
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Campus:</strong> {{ $campusName }}
                </span>
                @endif
                <span class="meta-badge {{ ($crmStudent?->stage1 === 'paid') ? 'meta-badge-success' : 'meta-badge-warning' }}">
                    &bull; {{ ($crmStudent?->stage1 === 'paid') ? 'Fee Paid' : ($crmStudent?->std_status ?: 'Fee Pending') }}
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
                        <span>8-Stage Admission Onboarding Process</span>
                    </h2>
                    <p>Click on any stage below to view and proceed through your admission process</p>
                </div>
                <div class="stepper-progress-box">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                    <span class="progress-percent-label">{{ $progressPercent }}% Complete ({{ $completedCount }}/8)</span>
                </div>
            </div>

            <!-- 8 Stages Bar -->
            <div class="stepper-nav-bar">
                @foreach($stages as $num => $stg)
                    @php
                        $isActive = ($activeStep === $num);
                        $isDone = ($stg['status'] === 'completed');
                    @endphp
                    <a href="{{ route('dashboards.index', ['step' => $num]) }}"
                       class="step-nav-btn {{ $isActive ? 'is-active' : '' }} {{ $isDone ? 'is-completed' : '' }}"
                       title="Stage {{ $num }}">
                        <div class="step-circle">
                            @if($isDone) ✓ @else {{ $num }} @endif
                        </div>
                        <span class="step-nav-name">Stage {{ $num }}</span>
                        <span class="step-nav-badge {{ $isDone ? 'badge-done' : 'badge-pending' }}">
                            {{ $isDone ? 'Completed' : 'Not Completed' }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>

        <!-- STEP CONTENT PANEL -->
        <section class="step-panel-card">

            <!-- ================= STAGE 1: FEE PAYMENT ================= -->
            @if($activeStep === 1)
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Stage 1 of 8 &bull; Fee Payment
                        </div>
                        <h3>
                            <span>Stage 1: Fee Payment</span>
                        </h3>
                        <p>Complete your initial admission fee payment to proceed with the remaining onboarding stages.</p>
                    </div>
                    @if($crmStudent?->stage1 === 'paid')
                        <span class="step-header-tag" style="background:#10b981; border-color:#86efac; color:#ffffff;">✓ Completed</span>
                    @else
                        <span class="step-header-tag" style="background:rgba(255,255,255,0.15); border-color:rgba(255,255,255,0.25); color:#ffffff;">Not Completed</span>
                    @endif
                </div>

                <div class="step-panel-body">
                    <!-- FEE PAYMENT STATUS & ACTION -->
                    <div class="section-box" style="text-align: center; padding: 2rem 1.5rem;">
                        @if($crmStudent?->stage1 === 'paid')
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">✅</div>
                            <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Admission Fee Paid &amp; Confirmed</h3>
                            <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 1.25rem;">Your stage 1 admission fee payment is verified in the database. You can download your official documents below.</p>
                            <span class="step-nav-badge badge-done" style="font-size: 0.85rem; padding: 0.35rem 0.85rem;">Status: Completed ✓</span>
                        @else
                            @php
                                $studentAppliNo = $crmStudent?->applinano ?? $user?->usercode ?? '20261106333';
                                $feeBreakdown = \App\Services\CouponService::calculateFeeBreakdown($studentAppliNo, 25000.00);
                            @endphp
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">💳</div>
                            <h3 style="font-size: 1.25rem; font-weight: 800; color: #0f172a; margin-bottom: 0.25rem;">Admission Fee Payment (Stage 1)</h3>
                            <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 1.25rem;">Complete your initial Stage 1 seat confirmation fee payment securely via the PayU Gateway.</p>

                            <!-- PayU Fee Summary Box with Concessions -->
                            <div style="max-width: 440px; margin: 0 auto 1.5rem auto; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; text-align: left;">
                                <div style="display: flex; justify-content: space-between; font-size: 0.88rem; color: #475569; margin-bottom: 0.5rem;">
                                    <span>Base Admission Fee:</span>
                                    <strong style="color: #0f172a;">₹ 25,000.00</strong>
                                </div>

                                @if($feeBreakdown['def_discount'] > 0)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.84rem; color: #059669; margin-bottom: 0.4rem;">
                                        <span>Early Bird Concession (<code>{{ $feeBreakdown['def_coupon'] }}</code>):</span>
                                        <strong style="color: #059669;">- ₹ {{ number_format($feeBreakdown['def_discount'], 2) }}</strong>
                                    </div>
                                @endif

                                @if($feeBreakdown['per_discount'] > 0)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.84rem; color: #059669; margin-bottom: 0.4rem;">
                                        <span>Incharge Personal Concession (<code>{{ $feeBreakdown['per_coupon'] }}</code>):</span>
                                        <strong style="color: #059669;">- ₹ {{ number_format($feeBreakdown['per_discount'], 2) }}</strong>
                                    </div>
                                @endif

                                @if($feeBreakdown['total_discount'] > 0)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.82rem; color: #047857; margin-bottom: 0.5rem; background: rgba(16, 185, 129, 0.08); padding: 4px 8px; border-radius: 6px;">
                                        <span style="font-weight: 700;">Total Concession Deducted:</span>
                                        <strong style="color: #047857;">- ₹ {{ number_format($feeBreakdown['total_discount'], 2) }}</strong>
                                    </div>
                                @endif

                                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; color: #64748b; margin-bottom: 0.5rem;">
                                    <span>Payment Gateway:</span>
                                    <span style="font-weight: 700; color: #10b981;">PayU Secure Hosted</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; color: #64748b; padding-top: 0.5rem; border-top: 1px dashed #cbd5e1; align-items: center;">
                                    <span style="font-weight: 800; font-size: 0.92rem; color: #0f172a;">Net Fee Payable:</span>
                                    <span style="font-size: 1.25rem; font-weight: 800; color: #059669;">₹ {{ number_format($feeBreakdown['net_payable'], 2) }}</span>
                                </div>
                            </div>

                            <form action="{{ route('payu.initiate') }}" method="POST" style="display: inline-block;">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $feeBreakdown['net_payable'] }}">
                                <button type="submit" class="btn-step-next" style="display: inline-flex; margin: 0 auto; background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 12px 28px; font-size: 0.95rem;">
                                    <span>💳 Pay ₹{{ number_format($feeBreakdown['net_payable']) }} via PayU Gateway</span>
                                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- DOWNLOAD CARDS -->
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>📥 Official Admission Documents</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="kyc-doc-card" style="text-align:center; padding:1.5rem;">
                                <div style="font-size:2rem; margin-bottom:0.5rem;">📜</div>
                                <h4 class="kyc-doc-title">Provisional Allotment Order</h4>
                                <p class="kyc-doc-sub">Signed Dean Allotment Letter</p>
                                @if($crmStudent?->stage1 === 'paid')
                                    <a href="javascript:alert('Downloading Provisional Allotment Order (PDF)...')" class="btn-step-next" style="margin-top:0.75rem; justify-content:center; text-decoration:none;">
                                        <span>Download PDF</span>
                                    </a>
                                @else
                                    <div style="font-size:0.75rem; color:#94a3b8; margin-top:0.75rem; padding:0.5rem; background:#f8fafc; border-radius:6px;">Available after fee payment</div>
                                @endif
                            </div>

                            <div class="kyc-doc-card" style="text-align:center; padding:1.5rem;">
                                <div style="font-size:2rem; margin-bottom:0.5rem;">🧾</div>
                                <h4 class="kyc-doc-title">Official Fee Payment Receipt</h4>
                                <p class="kyc-doc-sub">Vignan Accounts Verified Receipt</p>
                                @if($crmStudent?->stage1 === 'paid')
                                    <a href="javascript:alert('Downloading Official Fee Payment Receipt (PDF)...')" class="btn-step-next" style="margin-top:0.75rem; justify-content:center; text-decoration:none;">
                                        <span>Download Receipt</span>
                                    </a>
                                @else
                                    <div style="font-size:0.75rem; color:#94a3b8; margin-top:0.75rem; padding:0.5rem; background:#f8fafc; border-radius:6px;">Available after fee payment</div>
                                @endif
                            </div>

                            <div class="kyc-doc-card" style="text-align:center; padding:1.5rem;">
                                <div style="font-size:2rem; margin-bottom:0.5rem;">📅</div>
                                <h4 class="kyc-doc-title">Campus Induction Schedule</h4>
                                <p class="kyc-doc-sub">Orientation &amp; Move-in Guide</p>
                                <a href="javascript:alert('Downloading Orientation & Campus Induction Schedule (PDF)...')" class="btn-step-next" style="margin-top:0.75rem; justify-content:center; text-decoration:none;">
                                    <span>Download Guide</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <div style="font-size:0.85rem; color:#64748b;">
                            Stage 1 Status: <strong style="color: {{ $crmStudent?->stage1 === 'paid' ? '#10b981' : '#f59e0b' }};">{{ $crmStudent?->stage1 === 'paid' ? 'Completed' : 'Not Completed' }}</strong>
                        </div>
                        <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-step-next" style="text-decoration:none;">
                            <span>Stage 2 &rarr;</span>
                        </a>
                    </div>
                </div>

            <!-- ================= STAGES 2 to 8 ================= -->
            @else
                <div class="step-panel-header">
                    <div>
                        <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#bfdbfe; margin-bottom:0.2rem;">
                            Stage {{ $activeStep }} of 8
                        </div>
                        <h3>
                            <span>Stage {{ $activeStep }}</span>
                        </h3>
                        <p>Stage {{ $activeStep }} onboarding overview.</p>
                    </div>
                    @if($stages[$activeStep]['status'] === 'completed')
                        <span class="step-header-tag" style="background:#10b981; border-color:#86efac; color:#ffffff;">✓ Completed</span>
                    @else
                        <span class="step-header-tag" style="background:rgba(255,255,255,0.15); border-color:rgba(255,255,255,0.25); color:#ffffff;">Not Completed</span>
                    @endif
                </div>

                <div class="step-panel-body">
                    <div class="section-box" style="text-align: center; padding: 2.5rem 1.5rem;">
                        <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">
                            {{ $stages[$activeStep]['status'] === 'completed' ? '✅' : '⏳' }}
                        </div>
                        <h3 style="font-size: 1.25rem; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;">
                            Stage {{ $activeStep }} Status: {{ $stages[$activeStep]['status'] === 'completed' ? 'Completed' : 'Not Completed' }}
                        </h3>
                        <p style="font-size: 0.88rem; color: #64748b; margin-bottom: 1.5rem;">
                            @if($stages[$activeStep]['status'] === 'completed')
                                This stage has been completed and recorded.
                            @else
                                This stage is currently not completed.
                            @endif
                        </p>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => $activeStep - 1]) }}" class="btn-step-prev">
                            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            <span>&larr; Stage {{ $activeStep - 1 }}</span>
                        </a>
                        @if($activeStep < 8)
                            <a href="{{ route('dashboards.index', ['step' => $activeStep + 1]) }}" class="btn-step-next" style="text-decoration:none;">
                                <span>Stage {{ $activeStep + 1 }} &rarr;</span>
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
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

    <!-- Silent Autosave on Blur / Change (No intimate message as requested) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '{{ csrf_token() }}';
            const forms = document.querySelectorAll('.step-panel-body');

            forms.forEach(function(form) {
                form.querySelectorAll('input:not([type="hidden"]):not([type="file"]), select, textarea').forEach(function(el) {
                    const autoSave = function() {
                        const fieldName = el.name;
                        if (!fieldName || fieldName === '_token' || fieldName === 'step') return;
                        const fieldValue = el.value;

                        fetch('{{ route("dashboards.autosave") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                field: fieldName,
                                value: fieldValue
                            })
                        }).catch(() => {}); // Intentionally silent: no toast or notification message
                    };

                    el.addEventListener('blur', autoSave);
                    el.addEventListener('change', autoSave);
                });
            });
        });
    </script>
</body>
</html>
