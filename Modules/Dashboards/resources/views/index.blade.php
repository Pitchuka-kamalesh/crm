<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Admission Portal | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

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
            --brand-blue: #1d4ed8;
            --brand-primary: #2563eb;
            --brand-sky: #0ea5e9;
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
        .dash-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .nav-inner {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-cluster {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .brand-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
        }

        .brand-icon-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-title-wrap {
            display: flex;
            flex-direction: column;
        }

        .brand-main-title {
            font-size: 0.95rem;
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
            background: #dbeafe;
            color: #1d4ed8;
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
            font-size: 0.85rem;
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
            background: linear-gradient(135deg, #2563eb, #4f46e5);
            color: #ffffff;
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }

        .btn-signout {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid var(--border-color);
            padding: 0.45rem 0.9rem;
            border-radius: 9px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-signout:hover {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }

        .btn-signout svg {
            width: 15px;
            height: 15px;
        }

        /* Dashboard Container */
        .dash-container {
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            flex: 1;
        }

        /* Banner */
        .welcome-hero {
            background: linear-gradient(135deg, #060d1a 0%, #0d1b33 50%, #1e1b4b 100%);
            border-radius: 1.5rem;
            padding: 2.25rem;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px -10px rgba(15, 23, 42, 0.4);
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
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #10b981;
        }

        .welcome-heading {
            font-size: 2rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .welcome-heading-grad {
            background: linear-gradient(90deg, #7dd3fc, #bfdbfe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .welcome-subtitle {
            font-size: 0.92rem;
            color: #cbd5e1;
            max-width: 750px;
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
            padding: 0.3rem 0.75rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }

        .meta-badge-success {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        /* 4 Stats Cards */
        .stats-quad {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            padding: 1.35rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .stat-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .stat-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stat-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon svg {
            width: 20px;
            height: 20px;
        }

        .stat-value-row {
            display: flex;
            align-items: baseline;
            gap: 0.5rem;
            margin-bottom: 0.35rem;
        }

        .stat-val-num {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .stat-val-pill {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.1rem 0.5rem;
            border-radius: 9999px;
        }

        .stat-footer-text {
            font-size: 0.75rem;
            color: #64748b;
        }

        /* 6 Admission Portal Modules */
        .modules-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
        }

        .modules-header-title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f172a;
        }

        .modules-header-sub {
            font-size: 0.8rem;
            color: #64748b;
        }

        .modules-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }

        .module-item-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 1.25rem;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.25s ease;
            position: relative;
        }

        .module-item-card:hover {
            border-color: #2563eb;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }

        .module-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .module-icon-badge {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .module-icon-badge svg {
            width: 24px;
            height: 24px;
        }

        .module-status-chip {
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
        }

        .module-card-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 0.4rem;
        }

        .module-card-desc {
            font-size: 0.82rem;
            color: #64748b;
            line-height: 1.55;
            margin-bottom: 1.25rem;
        }

        .module-action-link {
            border-top: 1px solid #f1f5f9;
            padding-top: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            font-weight: 700;
            color: #2563eb;
            text-decoration: none;
        }

        .module-action-link svg {
            width: 16px;
            height: 16px;
            transition: transform 0.2s ease;
        }

        .module-item-card:hover .module-action-link svg {
            transform: translateX(4px);
        }

        /* Dash Footer */
        .dash-footer {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 1.25rem 1.5rem;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1280px;
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

        .footer-inner a:hover {
            text-decoration: underline;
        }

        /* Color classes */
        .c-blue { background: #eff6ff; color: #2563eb; }
        .c-sky { background: #f0f9ff; color: #0284c7; }
        .c-amber { background: #fffbeb; color: #d97706; }
        .c-emerald { background: #ecfdf5; color: #059669; }
        .c-purple { background: #faf5ff; color: #7c3aed; }
        .c-indigo { background: #eef2ff; color: #4f46e5; }

        /* Profile & Academic Form Styles */
        .academic-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1.25rem;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-top: 1.5rem;
            margin-bottom: 2rem;
        }

        .academic-card-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .academic-form-body {
            padding: 2rem;
        }

        .section-divider-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 1.25rem;
            padding-bottom: 0.65rem;
            border-bottom: 2px solid #f1f5f9;
        }

        .section-icon-badge {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 700;
        }

        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.75rem;
        }

        .form-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.75rem;
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
            gap: 0.35rem;
        }

        .field-label .required-star {
            color: #e11d48;
        }

        .field-input, .field-select {
            width: 100%;
            padding: 0.7rem 0.9rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 9px;
            font-size: 0.88rem;
            color: #0f172a;
            font-family: inherit;
            background: #ffffff;
            transition: all 0.2s ease;
        }

        .field-input:focus, .field-select:focus {
            border-color: #2563eb;
            outline: none;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .field-input:read-only, .field-input:disabled {
            background: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
            border-color: #e2e8f0;
        }

        .btn-save-academic {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border: none;
            padding: 0.85rem 2.2rem;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-save-academic:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        }

        @media (max-width: 900px) {
            .form-grid-3, .form-grid-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 600px) {
            .form-grid-2, .form-grid-3, .form-grid-4 {
                grid-template-columns: 1fr;
            }
            .academic-form-body {
                padding: 1.25rem;
            }
        }

        @media (max-width: 1024px) {
            .stats-quad {
                grid-template-columns: repeat(2, 1fr);
            }
            .modules-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .stats-quad, .modules-grid {
                grid-template-columns: 1fr;
            }
            .welcome-heading {
                font-size: 1.6rem;
            }
            .nav-inner {
                padding: 0 1rem;
            }
            .dash-container {
                padding: 1.25rem 1rem;
            }
            .footer-inner {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    @php
        $user = Auth::user();
        $student = $user?->student ?? \App\Models\StudentRegistration::where('register_no', $user?->usercode)->orWhere('application_no', $user?->usercode)->first();
        $employee = $user?->employee;
        $displayName = $employee?->full_name ?? $student?->student_name ?? $user?->name ?? $user?->usercode;
        $roleName = $user?->role_label ?? ($user?->user_type === 2 ? 'Employee' : 'Student Candidate');
        $appNo = $student?->application_no ?? ($employee ? 'EMP-' . $employee->emp_code : '2026-APP-' . ($user?->usercode ?? '001'));
        $regNo = $student?->register_no ?? $employee?->emp_code ?? $user?->usercode;
        $programCode = $student?->program?->program_short_name ?? ($student?->progid == 1 ? 'MBA' : ($student?->progid == 2 ? 'MCA' : ($student?->progid == 3 ? 'BBA' : ($student?->progid == 4 ? 'BCA' : ($employee ? 'Academic Affairs' : 'Degree')))));
        $regulation = $student?->regulation ?? 'R25';
    @endphp

    <!-- Top Navigation -->
    <nav class="dash-nav">
        <div class="nav-inner">
            <div class="brand-cluster">
                <div class="brand-icon-box">
                    <img src="/images/vignan-logo.png" alt="VFSTR Vignan University">
                </div>
                <div class="brand-title-wrap">
                    <div class="brand-main-title">
                        <span>VFSTR &bull; VIGNAN UNIVERSITY</span>
                        <span class="brand-tag">Student Admission Portal</span>
                    </div>
                    <span class="brand-sub-title">Admissions &amp; Academic Enrollment Administration</span>
                </div>
            </div>

            <div class="user-nav-cluster">
                <div class="user-info-text">
                    <span class="user-name-title">{{ $displayName }}</span>
                    <span class="user-meta-sub">{{ $roleName }} &bull; {{ $appNo }} &bull; +91 {{ $user?->phnno }}</span>
                </div>

                <div class="user-avatar-circle">
                    {{ strtoupper(substr($displayName, 0, 1)) }}
                </div>

                <a href="{{ route('crm.index') }}" class="btn-signout" style="background:#eff6ff;color:#2563eb;border-color:#bfdbfe;text-decoration:none;" title="CRM Executive Portal">
                    <span>👑 CRM Portal</span>
                </a>

                <a href="{{ route('sms.index') }}" class="btn-signout" style="background:#eff6ff;color:#2563eb;border-color:#bfdbfe;text-decoration:none;" title="SMS and WhatsApp Tracker">
                    <span>💬 SMS Tracker</span>
                </a>

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-signout">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="dash-container">

        <!-- Welcome Banner (Admissions Focus) -->
        <section class="welcome-hero">
            <div class="welcome-pill">
                <span class="welcome-dot"></span>
                <span>Admissions Batch 2026 - 2027 &bull; {{ $roleName }} Portal</span>
            </div>

            <h1 class="welcome-heading">
                Welcome, <span class="welcome-heading-grad">{{ $displayName }}</span>!
            </h1>

            <p class="welcome-subtitle">
                Your admission onboarding portal is active for <strong>{{ $programCode }} (Regulation {{ $regulation }})</strong>. Check your application status, verified certificates, seat allotment order, and complete admission formalities below.
            </p>

            <div class="meta-badges-cluster">
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Application No:</strong> {{ $appNo }}
                </span>
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Register No:</strong> {{ $regNo }}
                </span>
                <span class="meta-badge">
                    <strong style="color:#ffffff;">Allocated Program:</strong> {{ $programCode }}
                </span>
                <span class="meta-badge meta-badge-success">
                    &bull; Admission Confirmed
                </span>
            </div>
        </section>

        <!-- 4 Key Admission Milestone Cards -->
        <section class="stats-quad">
            
            <!-- 1. Application Status -->
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-label">Application Status</span>
                    <div class="stat-icon c-blue">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value-row">
                    <span class="stat-val-num">Verified</span>
                    <span class="stat-val-pill c-blue">Approved</span>
                </div>
                <p class="stat-footer-text">Application No: {{ $appNo }}</p>
            </div>

            <!-- 2. Document Verification -->
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-label">Document KYC</span>
                    <div class="stat-icon c-emerald">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value-row">
                    <span class="stat-val-num">6 / 6</span>
                    <span class="stat-val-pill c-emerald">Complete</span>
                </div>
                <p class="stat-footer-text">10th, 12th, TC &amp; Aadhaar Verified</p>
            </div>

            <!-- 3. Admission Fee Payment -->
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-label">Admission Fee</span>
                    <div class="stat-icon c-sky">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value-row">
                    <span class="stat-val-num">Paid</span>
                    <span class="stat-val-pill c-sky">Receipt Issued</span>
                </div>
                <p class="stat-footer-text">Semester I Tuition &amp; Seat Fee</p>
            </div>

            <!-- 4. Provisional Enrollment -->
            <div class="stat-card">
                <div class="stat-top">
                    <span class="stat-label">Enrollment Allotment</span>
                    <div class="stat-icon c-purple">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                        </svg>
                    </div>
                </div>
                <div class="stat-value-row">
                    <span class="stat-val-num">{{ $programCode }}</span>
                    <span class="stat-val-pill c-purple">{{ $regulation }}</span>
                </div>
                <p class="stat-footer-text">Campus Induction: Vadlamudi</p>
            </div>

        </section>

        <!-- Candidate Admission & Academic Profile Form (Basic, 10th & Intermediate) -->
        <section class="academic-card">
            <!-- Header -->
            <div class="academic-card-header">
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; font-weight:700; color:#93c5fd; display:flex; align-items:center; gap:0.4rem;">
                        <span>●</span> Official Admission Application
                    </div>
                    <h2 style="font-size:1.35rem; font-weight:800; margin-top:0.25rem; letter-spacing:-0.01em;">
                        Candidate Profile &amp; Academic Qualification Details
                    </h2>
                    <p style="font-size:0.85rem; color:#dbeafe; margin-top:0.2rem;">
                        Fill and verify your Basic Registration, 10th Standard (SSC), and Intermediate (10+2) details in the university database.
                    </p>
                </div>
                <div style="display:flex; align-items:center; gap:0.6rem; flex-wrap:wrap;">
                    <span style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(255,255,255,0.15); backdrop-filter:blur(8px); padding:0.4rem 0.9rem; border-radius:9999px; font-size:0.78rem; font-weight:600;">
                        <span>Application:</span> <strong>{{ $appNo }}</strong>
                    </span>
                    <span style="display:inline-flex; align-items:center; gap:0.4rem; background:rgba(255,255,255,0.15); backdrop-filter:blur(8px); padding:0.4rem 0.9rem; border-radius:9999px; font-size:0.78rem; font-weight:600;">
                        <span>Register No:</span> <strong>{{ $regNo }}</strong>
                    </span>
                </div>
            </div>

            @if(session('status'))
                <div style="margin: 1.25rem 2rem 0; padding: 0.85rem 1.25rem; background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; border-radius: 10px; font-size: 0.88rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <svg style="width: 20px; height: 20px; flex-shrink: 0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div style="margin: 1.25rem 2rem 0; padding: 0.85rem 1.25rem; background: #fff1f2; border: 1px solid #fecdd3; color: #9f1239; border-radius: 10px; font-size: 0.85rem;">
                    <strong>Please check the following errors:</strong>
                    <ul style="margin-left: 1.25rem; margin-top: 0.35rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('dashboards.save-education') }}" method="POST" class="academic-form-body">
                @csrf

                <!-- SECTION 1: BASIC CANDIDATE DETAILS -->
                <div class="section-divider-title">
                    <span class="section-icon-badge c-blue">1</span>
                    <span>Basic Registration Details</span>
                </div>

                <div class="form-grid-3">
                    <div class="field-group">
                        <label class="field-label">
                            <span>Applicant's Full Name (As per SSC)</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="text" name="student_name" value="{{ old('student_name', $student?->student_name ?? $user?->name) }}" required placeholder="e.g. John Doe" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Date of Birth</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="date" name="dob" value="{{ old('dob', $student?->dob ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d') : '') }}" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Gender</span>
                            <span class="required-star">*</span>
                        </label>
                        <select name="gender" class="field-select">
                            <option value="Male" {{ old('gender', $student?->gender) === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender', $student?->gender) === 'Female' ? 'selected' : '' }}>Female</option>
                            <option value="Other" {{ old('gender', $student?->gender) === 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Aadhaar Number (12 Digits)</span>
                        </label>
                        <input type="text" name="aadhar_no" maxlength="12" value="{{ old('aadhar_no', $student?->aadhar_no) }}" placeholder="e.g. 123456789012" oninput="this.value=this.value.replace(/\D/g,'').slice(0,12)" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Academic Bank of Credits (ABC ID)</span>
                        </label>
                        <input type="text" name="abc_id" maxlength="30" value="{{ old('abc_id', $student?->abc_id) }}" placeholder="e.g. 123-456-789-012" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Personal Email Address</span>
                        </label>
                        <input type="email" name="personal_email" value="{{ old('personal_email', $student?->personal_email ?? $user?->personalmail) }}" placeholder="e.g. applicant@gmail.com" class="field-input">
                    </div>
                </div>

                <!-- SECTION 2: 10TH STANDARD (SSC) DETAILS -->
                <div class="section-divider-title" style="margin-top: 1.5rem;">
                    <span class="section-icon-badge c-emerald">2</span>
                    <span>10th Standard (SSC) Academic Records</span>
                    @if($edu10th)
                        <span style="margin-left:auto; font-size:0.75rem; background:#ecfdf5; color:#059669; border:1px solid #a7f3d0; padding:0.2rem 0.6rem; border-radius:9999px; font-weight:700;">
                            ✓ Saved in DB
                        </span>
                    @endif
                </div>

                <div class="form-grid-3">
                    <div class="field-group">
                        <label class="field-label">
                            <span>Board / Council Name</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="text" name="ssc_board" value="{{ old('ssc_board', $edu10th?->board_university ?? 'Board of Secondary Education, Andhra Pradesh (BSEAP)') }}" placeholder="e.g. BSEAP / CBSE / ICSE" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>School / Institution Name</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="text" name="ssc_school" value="{{ old('ssc_school', $edu10th?->institution_name) }}" placeholder="e.g. ZPHS / St. Joseph High School" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>10th Hall Ticket / Roll Number</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="text" name="ssc_hallticket" value="{{ old('ssc_hallticket', $edu10th?->hall_ticket_no) }}" placeholder="e.g. 2314567890" class="field-input">
                    </div>
                </div>

                <div class="form-grid-4">
                    <div class="field-group">
                        <label class="field-label">
                            <span>Year of Passing</span>
                        </label>
                        <input type="number" name="ssc_year" min="2010" max="2026" value="{{ old('ssc_year', $edu10th?->year_of_passing ?? 2024) }}" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Marks Obtained</span>
                        </label>
                        <input type="number" step="0.01" name="ssc_marks" value="{{ old('ssc_marks', $edu10th?->marks_obtained) }}" placeholder="e.g. 540" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Total / Max Marks</span>
                        </label>
                        <input type="number" step="0.01" name="ssc_max_marks" value="{{ old('ssc_max_marks', $edu10th?->max_marks ?? 600) }}" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>CGPA / Grade Points</span>
                        </label>
                        <input type="number" step="0.01" name="ssc_cgpa" value="{{ old('ssc_cgpa', $edu10th?->cgpa) }}" placeholder="e.g. 9.5" class="field-input">
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="field-group">
                        <label class="field-label">
                            <span>City / District</span>
                        </label>
                        <input type="text" name="ssc_city" value="{{ old('ssc_city', $edu10th?->city) }}" placeholder="e.g. Guntur / Vijayawada" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>State</span>
                        </label>
                        <input type="text" name="ssc_state" value="{{ old('ssc_state', $edu10th?->state ?? 'Andhra Pradesh') }}" placeholder="e.g. Andhra Pradesh" class="field-input">
                    </div>
                </div>

                <!-- SECTION 3: INTERMEDIATE / 12TH (10+2) DETAILS -->
                <div class="section-divider-title" style="margin-top: 1.5rem;">
                    <span class="section-icon-badge c-sky">3</span>
                    <span>Intermediate / 12th (10+2) Academic Records</span>
                    @if($eduInter)
                        <span style="margin-left:auto; font-size:0.75rem; background:#f0f9ff; color:#0284c7; border:1px solid #bae6fd; padding:0.2rem 0.6rem; border-radius:9999px; font-weight:700;">
                            ✓ Saved in DB
                        </span>
                    @endif
                </div>

                <div class="form-grid-3">
                    <div class="field-group">
                        <label class="field-label">
                            <span>Board / Council Name</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="text" name="inter_board" value="{{ old('inter_board', $eduInter?->board_university ?? 'Board of Intermediate Education, Andhra Pradesh (BIEAP)') }}" placeholder="e.g. BIEAP / TSBIE / CBSE / ISC" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Junior College / Institution Name</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="text" name="inter_college" value="{{ old('inter_college', $eduInter?->institution_name) }}" placeholder="e.g. Vignan Junior College" class="field-input">
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
                            <option value="CEC" {{ old('inter_stream', $eduInter?->study_mode) === 'CEC' ? 'selected' : '' }}>CEC (Civics, Economics, Commerce)</option>
                            <option value="Diploma" {{ old('inter_stream', $eduInter?->study_mode) === 'Diploma' ? 'selected' : '' }}>Polytechnic Diploma</option>
                            <option value="Other" {{ old('inter_stream', $eduInter?->study_mode) === 'Other' ? 'selected' : '' }}>Other / Vocational</option>
                        </select>
                    </div>
                </div>

                <div class="form-grid-4">
                    <div class="field-group">
                        <label class="field-label">
                            <span>Hall Ticket / Roll Number</span>
                            <span class="required-star">*</span>
                        </label>
                        <input type="text" name="inter_hallticket" value="{{ old('inter_hallticket', $eduInter?->hall_ticket_no) }}" placeholder="e.g. 2501234567" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Year of Passing</span>
                        </label>
                        <input type="number" name="inter_year" min="2010" max="2026" value="{{ old('inter_year', $eduInter?->year_of_passing ?? 2026) }}" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Marks Obtained</span>
                        </label>
                        <input type="number" step="0.01" name="inter_marks" value="{{ old('inter_marks', $eduInter?->marks_obtained) }}" placeholder="e.g. 950" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>Total / Max Marks</span>
                        </label>
                        <input type="number" step="0.01" name="inter_max_marks" value="{{ old('inter_max_marks', $eduInter?->max_marks ?? 1000) }}" class="field-input">
                    </div>
                </div>

                <div class="form-grid-3">
                    <div class="field-group">
                        <label class="field-label">
                            <span>Percentage (%)</span>
                        </label>
                        <input type="number" step="0.01" name="inter_percentage" value="{{ old('inter_percentage', $eduInter?->percentage) }}" placeholder="e.g. 95.00" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>City / District</span>
                        </label>
                        <input type="text" name="inter_city" value="{{ old('inter_city', $eduInter?->city) }}" placeholder="e.g. Guntur" class="field-input">
                    </div>

                    <div class="field-group">
                        <label class="field-label">
                            <span>State</span>
                        </label>
                        <input type="text" name="inter_state" value="{{ old('inter_state', $eduInter?->state ?? 'Andhra Pradesh') }}" placeholder="e.g. Andhra Pradesh" class="field-input">
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; align-items:center; gap:1rem; margin-top:2rem; padding-top:1.5rem; border-top:1px solid #e2e8f0;">
                    <button type="submit" class="btn-save-academic">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:20px;height:20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                        </svg>
                        <span>Save &amp; Update Admission Details</span>
                    </button>
                </div>
            </form>
        </section>

        <!-- 6 Student Admission Services Modules -->
        <section>
            <div class="modules-header">
                <div>
                    <h2 class="modules-header-title">Student Admission &amp; Onboarding Services</h2>
                    <p class="modules-header-sub">Manage your application, documents, fee payments, and allotment</p>
                </div>
            </div>

            <div class="modules-grid">
                
                <!-- 1. Admission Application Details -->
                <div class="module-item-card">
                    <div>
                        <div class="module-top-row">
                            <div class="module-icon-badge c-blue">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </div>
                            <span class="module-status-chip c-blue">Verified</span>
                        </div>
                        <h3 class="module-card-title">Admission Application</h3>
                        <p class="module-card-desc">Review your registered candidate profile, parent/guardian details, address, qualification records, and quota category.</p>
                    </div>
                    <a href="javascript:void(0)" class="module-action-link">
                        <span>View Application Form</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

                <!-- 2. Document & Certificate KYC -->
                <div class="module-item-card">
                    <div>
                        <div class="module-top-row">
                            <div class="module-icon-badge c-emerald">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="module-status-chip c-emerald">Approved</span>
                        </div>
                        <h3 class="module-card-title">Certificate &amp; KYC Upload</h3>
                        <p class="module-card-desc">Check certificate verification: 10th memo, 12th/Intermediate marks, Transfer Certificate (TC), Migration, and Aadhaar.</p>
                    </div>
                    <a href="javascript:void(0)" class="module-action-link">
                        <span>Check Verified Documents</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

                <!-- 3. Fee Breakdown & Receipts -->
                <div class="module-item-card">
                    <div>
                        <div class="module-top-row">
                            <div class="module-icon-badge c-sky">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <span class="module-status-chip c-sky">Paid</span>
                        </div>
                        <h3 class="module-card-title">Fee Payment &amp; Receipts</h3>
                        <p class="module-card-desc">Download official admission fee receipt, view scholarship fee concession, upcoming semester dues, and payment history.</p>
                    </div>
                    <a href="javascript:void(0)" class="module-action-link">
                        <span>Download Fee Receipts</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

                <!-- 4. Branch & Program Allocation -->
                <div class="module-item-card">
                    <div>
                        <div class="module-top-row">
                            <div class="module-icon-badge c-amber">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <span class="module-status-chip c-amber">Allotted</span>
                        </div>
                        <h3 class="module-card-title">Program &amp; Course Tracks</h3>
                        <p class="module-card-desc">Allocated degree: {{ $programCode }} (Duration: 2-3 Years). Check semester syllabus, regulation handbook, and elective choices.</p>
                    </div>
                    <a href="javascript:void(0)" class="module-action-link">
                        <span>View Program Curriculum</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

                <!-- 5. Hostel & Campus Transport -->
                <div class="module-item-card">
                    <div>
                        <div class="module-top-row">
                            <div class="module-icon-badge c-purple">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                                </svg>
                            </div>
                            <span class="module-status-chip c-purple">Open Booking</span>
                        </div>
                        <h3 class="module-card-title">Hostel &amp; Bus Transport</h3>
                        <p class="module-card-desc">Apply for on-campus AC/Non-AC student hostel accommodation, dietary preferences, and university shuttle bus passes.</p>
                    </div>
                    <a href="javascript:void(0)" class="module-action-link">
                        <span>Book Accommodation</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

                <!-- 6. Provisional Allotment Order & Helpdesk -->
                <div class="module-item-card">
                    <div>
                        <div class="module-top-row">
                            <div class="module-icon-badge c-indigo">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <span class="module-status-chip c-indigo">Downloadable</span>
                        </div>
                        <h3 class="module-card-title">Allotment Order &amp; Helpdesk</h3>
                        <p class="module-card-desc">Download your signed Provisional Admission Allotment Order, student induction schedule, and contact admission counselors.</p>
                    </div>
                    <a href="javascript:void(0)" class="module-action-link">
                        <span>Download Allotment Letter</span>
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </a>
                </div>

            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="dash-footer">
        <div class="footer-inner">
            <div>
                &copy; 2026 VFSTR &bull; Vignan's Foundation for Science, Technology &amp; Research. All rights reserved.
            </div>
            <div>
                Student Admission Portal &bull; <a href="https://vignan.ac.in" target="_blank">Official University Site</a>
            </div>
        </div>
    </footer>

</body>
</html>
