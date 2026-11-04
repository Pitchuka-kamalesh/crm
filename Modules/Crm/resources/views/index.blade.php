<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $roleName }} Portal - CRM Dashboard | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            --brand-navy: #0b1e42;
            --brand-sky: #0ea5e9;
            --brand-emerald: #059669;
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
        .crm-nav {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .nav-inner {
            max-width: 1360px;
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
            text-decoration: none;
            color: inherit;
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
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-tag {
            font-size: 0.65rem;
            font-weight: 800;
            padding: 0.15rem 0.55rem;
            border-radius: 9999px;
            background: #dbeafe;
            color: #1d4ed8;
            text-transform: uppercase;
            letter-spacing: 0.02em;
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

        .role-badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .role-switcher-form {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .role-select-box {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.65rem;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #0f172a;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-nav-action {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid var(--border-color);
            padding: 0.45rem 0.85rem;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            text-decoration: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-nav-action:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .btn-signout:hover {
            background: #fee2e2;
            color: #b91c1c;
            border-color: #fca5a5;
        }

        /* Container */
        .crm-container {
            max-width: 1360px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
            flex: 1;
        }

        /* Hero Banner */
        .crm-hero {
            background: linear-gradient(135deg, #091a35 0%, #0e274d 50%, #1e1b4b 100%);
            border-radius: 1.5rem;
            padding: 2.25rem;
            color: #ffffff;
            box-shadow: 0 15px 35px -10px rgba(11, 30, 66, 0.4);
            position: relative;
            overflow: hidden;
        }

        .crm-hero-top {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.85rem;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 9999px;
            font-size: 0.74rem;
            font-weight: 700;
            color: #7dd3fc;
            margin-bottom: 0.85rem;
        }

        .dot-live {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 10px #10b981;
        }

        .crm-hero-title {
            font-size: 2.1rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .crm-hero-highlight {
            background: linear-gradient(90deg, #93c5fd, #67e8f9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .crm-hero-sub {
            font-size: 0.95rem;
            color: #cbd5e1;
            max-width: 820px;
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }

        .meta-pills-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .meta-pill {
            font-size: 0.75rem;
            padding: 0.35rem 0.8rem;
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #e2e8f0;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        /* 4 Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 1.15rem;
            padding: 1.35rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            box-shadow: 0 8px 20px -4px rgba(0, 0, 0, 0.06);
            transform: translateY(-2px);
        }

        .stat-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .stat-icon.blue { background: #eff6ff; color: #2563eb; }
        .stat-icon.green { background: #ecfdf5; color: #059669; }
        .stat-icon.amber { background: #fffbeb; color: #d97706; }
        .stat-icon.purple { background: #f5f3ff; color: #7c3aed; }

        .stat-value {
            font-size: 1.9rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }

        .stat-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #64748b;
            margin-top: 0.35rem;
        }

        /* Modules Quick Actions */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.15rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #0f172a;
        }

        .section-sub {
            font-size: 0.82rem;
            color: #64748b;
            margin-top: 0.15rem;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.25rem;
        }

        .action-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 1.15rem;
            padding: 1.4rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
            text-decoration: none;
            color: inherit;
        }

        .action-card:hover {
            border-color: #93c5fd;
            box-shadow: 0 10px 25px -5px rgba(37, 99, 235, 0.1);
            transform: translateY(-2px);
        }

        .action-card-header {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            margin-bottom: 0.75rem;
        }

        .action-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }

        .action-title {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
        }

        .action-card-desc {
            font-size: 0.84rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .action-card-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: #2563eb;
        }

        /* Recent Applications Table */
        .table-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 1.15rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .table-header-box {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fafafa;
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.86rem;
        }

        .custom-table th {
            padding: 0.85rem 1.5rem;
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            border-bottom: 1px solid var(--border-color);
        }

        .custom-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .custom-table tr:hover td {
            background: #f8fafc;
        }

        .code-pill {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: #2563eb;
            background: #eff6ff;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            font-size: 0.8rem;
        }

        .badge-verified {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }

        /* Footer */
        .crm-footer {
            background: #ffffff;
            border-top: 1px solid var(--border-color);
            padding: 1.25rem 0;
            margin-top: auto;
        }

        .footer-inner {
            max-width: 1360px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.78rem;
            color: #64748b;
        }

        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .actions-grid { grid-template-columns: repeat(1, 1fr); }
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <nav class="crm-nav">
        <div class="nav-inner">
            <a href="{{ route('crm.index') }}" class="brand-cluster">
                <div class="brand-icon-box">
                    <img src="/images/vignan-logo.png" alt="VFSTR Vignan University">
                </div>
                <div class="brand-title-wrap">
                    <div class="brand-main-title">
                        <span>VFSTR &bull; VIGNAN UNIVERSITY</span>
                        <span class="brand-tag">CRM Portal</span>
                    </div>
                    <span class="brand-sub-title">Admissions &bull; Leads &bull; Academic Enrollment Administration</span>
                </div>
            </a>

            <div class="user-nav-cluster">
                <span class="role-badge-pill">
                    <span>👑</span>
                    <span>{{ $roleName }}</span>
                </span>

                <!-- Fast Role Switcher (Session-Only, Zero DB Modification) -->
                <form action="{{ route('role.switch') }}" method="POST" class="role-switcher-form">
                    @csrf
                    <label for="role_switch_select" style="font-size:0.72rem; font-weight:700; color:#64748b;">View:</label>
                    <select name="role_id" id="role_switch_select" class="role-select-box" onchange="this.form.submit()">
                        @foreach($availableRoles as $r)
                            <option value="{{ $r->role_id }}" {{ $roleId == $r->role_id ? 'selected' : '' }}>
                                {{ $r->role_name }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('sms.index') }}" class="btn-nav-action" title="View SMS & WhatsApp Communication Tracker">
                    <span>💬 SMS Tracker</span>
                </a>

                <a href="{{ route('dashboards.index') }}" class="btn-nav-action" title="View Student Candidate Dashboard">
                    <span>🎓 Student View</span>
                </a>

                <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-nav-action btn-signout">
                        <span>Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="crm-container">

        @if(session('status'))
            <div style="background: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 0.9rem 1.25rem; border-radius: 12px; font-size: 0.88rem; font-weight: 600;">
                ✔ {{ session('status') }}
            </div>
        @endif

        <!-- Executive Hero Banner -->
        <section class="crm-hero">
            <div class="crm-hero-top">
                <span class="dot-live"></span>
                <span>Session 2026 - 2027 &bull; {{ $roleName }} Dashboard</span>
            </div>

            <h1 class="crm-hero-title">
                Welcome, <span class="crm-hero-highlight">{{ $user?->name ?? 'Administrator' }}</span>!
            </h1>

            <p class="crm-hero-sub">
                Centralized CRM &amp; Admissions Command Portal. Oversee candidate applications, lead pipelines, tele-calling distribution, and communication outreach across Vignan Campuses.
            </p>

            <div class="meta-pills-row">
                <span class="meta-pill">
                    <strong>Logged in as:</strong> {{ $user?->usercode ?? '02598' }} &bull; {{ $roleName }}
                </span>
                <span class="meta-pill">
                    <strong>Primary Contact:</strong> +91 {{ $user?->phnno ?? '9912996788' }}
                </span>
                <span class="meta-pill" style="background: rgba(16, 185, 129, 0.2); border-color: rgba(16, 185, 129, 0.4); color: #6ee7b7;">
                    &bull; Master Role Access Active
                </span>
            </div>
        </section>

        <!-- 4 Key Stat Cards -->
        <section class="stats-grid">
            
            <div class="stat-card">
                <div class="stat-top-row">
                    <span class="stat-label">Total Candidate Applications</span>
                    <div class="stat-icon blue">📋</div>
                </div>
                <div class="stat-value">{{ number_format($totalApplications) }}</div>
                <p style="font-size: 0.74rem; color: #10b981; font-weight: 600; margin-top: 0.4rem;">✔ Live candidate registrations</p>
            </div>

            <div class="stat-card">
                <div class="stat-top-row">
                    <span class="stat-label">SMS &amp; WhatsApp Tracker</span>
                    <div class="stat-icon green">💬</div>
                </div>
                <div class="stat-value">{{ number_format($totalCommunications) }}</div>
                <p style="font-size: 0.74rem; color: #059669; font-weight: 600; margin-top: 0.4rem;">✔ SmartPing &amp; JioCX dispatches</p>
            </div>

            <div class="stat-card">
                <div class="stat-top-row">
                    <span class="stat-label">Approved Programs</span>
                    <div class="stat-icon amber">🎓</div>
                </div>
                <div class="stat-value">{{ number_format($totalPrograms) }}</div>
                <p style="font-size: 0.74rem; color: #d97706; font-weight: 600; margin-top: 0.4rem;">✔ UG, PG, &amp; Doctoral degrees</p>
            </div>

            <div class="stat-card">
                <div class="stat-top-row">
                    <span class="stat-label">Specialized Branches</span>
                    <div class="stat-icon purple">🏛</div>
                </div>
                <div class="stat-value">{{ number_format($totalBranches) }}</div>
                <p style="font-size: 0.74rem; color: #7c3aed; font-weight: 600; margin-top: 0.4rem;">✔ Engineering &amp; Management</p>
            </div>

        </section>

        <!-- Quick Access to Role Menus -->
        <section>
            <div class="section-header">
                <div>
                    <h2 class="section-title">{{ $roleName }} Quick Access Modules</h2>
                    <p class="section-sub">Direct access to menus assigned to your role in the system</p>
                </div>
            </div>

            <div class="actions-grid">

                <!-- 1. Student Data -->
                <a href="#recent-applications" class="action-card">
                    <div>
                        <div class="action-card-header">
                            <div class="action-icon" style="background:#eff6ff; color:#2563eb;">📋</div>
                            <div>
                                <h3 class="action-title">Student Data</h3>
                                <span style="font-size:0.7rem; color:#64748b; font-weight:600;">Menu Ref: crm/stddata</span>
                            </div>
                        </div>
                        <p class="action-card-desc">Review registered student application records, verified mobile credentials, campus preferences, and qualification statuses.</p>
                    </div>
                    <div class="action-card-link">
                        <span>View Registered Students</span>
                        <span>&rarr;</span>
                    </div>
                </a>

                <!-- 2. Leads Allocation -->
                <a href="{{ route('leads.index') }}" class="action-card">
                    <div>
                        <div class="action-card-header">
                            <div class="action-icon" style="background:#ecfdf5; color:#059669;">👥</div>
                            <div>
                                <h3 class="action-title">Leads Allocation</h3>
                                <span style="font-size:0.7rem; color:#64748b; font-weight:600;">Menu Ref: leads/leadsallocation</span>
                            </div>
                        </div>
                        <p class="action-card-desc">Manage prospective student leads, assign tele-callers, monitor call statuses, and track enrollment conversions.</p>
                    </div>
                    <div class="action-card-link">
                        <span>Open Leads Module</span>
                        <span>&rarr;</span>
                    </div>
                </a>

                <!-- 3. SMS & WhatsApp Tracker -->
                <a href="{{ route('sms.index') }}" class="action-card">
                    <div>
                        <div class="action-card-header">
                            <div class="action-icon" style="background:#f0fdf4; color:#16a34a;">💬</div>
                            <div>
                                <h3 class="action-title">SMS &amp; WhatsApp Hub</h3>
                                <span style="font-size:0.7rem; color:#64748b; font-weight:600;">Audit Gateway</span>
                            </div>
                        </div>
                        <p class="action-card-desc">Monitor live SMS and WhatsApp OTP transmissions, delivery statuses, trace IDs, and trigger direct test notifications.</p>
                    </div>
                    <div class="action-card-link">
                        <span>Inspect Dispatch Logs</span>
                        <span>&rarr;</span>
                    </div>
                </a>

                <!-- 4. Fee & Finance Details -->
                <a href="{{ route('finance.index') }}" class="action-card">
                    <div>
                        <div class="action-card-header">
                            <div class="action-icon" style="background:#fffbeb; color:#d97706;">💰</div>
                            <div>
                                <h3 class="action-title">Finance &amp; Paid Details</h3>
                                <span style="font-size:0.7rem; color:#64748b; font-weight:600;">Menu Ref: finance/paiddetails</span>
                            </div>
                        </div>
                        <p class="action-card-desc">Audit seat confirmation fee collections, transaction references, receipt records, and candidate payment statuses.</p>
                    </div>
                    <div class="action-card-link">
                        <span>Review Payments</span>
                        <span>&rarr;</span>
                    </div>
                </a>

                <!-- 5. Academic Programs & Branches -->
                <div class="action-card">
                    <div>
                        <div class="action-card-header">
                            <div class="action-icon" style="background:#f5f3ff; color:#7c3aed;">🏛</div>
                            <div>
                                <h3 class="action-title">Academic Courses</h3>
                                <span style="font-size:0.7rem; color:#64748b; font-weight:600;">UG &bull; PG &bull; Doctoral</span>
                            </div>
                        </div>
                        <p class="action-card-desc">{{ $totalPrograms }} official university degree programs and {{ $totalBranches }} engineering &amp; management specializations configured.</p>
                    </div>
                    <div class="action-card-link" style="color:#7c3aed;">
                        <span>{{ $totalBranches }} Active Branches</span>
                    </div>
                </div>

                <!-- 6. Student Admission Portal -->
                <a href="{{ route('dashboards.index') }}" class="action-card">
                    <div>
                        <div class="action-card-header">
                            <div class="action-icon" style="background:#eff6ff; color:#1d4ed8;">🎓</div>
                            <div>
                                <h3 class="action-title">Student Portal View</h3>
                                <span style="font-size:0.7rem; color:#64748b; font-weight:600;">Candidate Dashboard</span>
                            </div>
                        </div>
                        <p class="action-card-desc">Switch to the student onboarding perspective to verify seat allotments, certificate uploads, and academic info forms.</p>
                    </div>
                    <div class="action-card-link">
                        <span>Preview Student View</span>
                        <span>&rarr;</span>
                    </div>
                </a>

            </div>
        </section>

        <!-- Live Candidate Registrations Table -->
        <section id="recent-applications">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Recent Student Registrations (Live Database)</h2>
                    <p class="section-sub">Applicant records registered through the admission portal</p>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header-box">
                    <span style="font-weight: 700; color: #0f172a;">Candidate Records</span>
                    <span style="font-size: 0.75rem; color: #64748b; font-weight: 600;">Showing up to 10 latest entries</span>
                </div>

                <div style="overflow-x: auto;">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Reg No / App No</th>
                                <th>Candidate Name</th>
                                <th>Mobile Number</th>
                                <th>Email ID</th>
                                <th>Campus</th>
                                <th>Timestamp</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentStudents as $std)
                                <tr>
                                    <td>
                                        <span class="code-pill">{{ $std->applinano ?? $std->register_no ?? $std->application_no ?? '—' }}</span>
                                    </td>
                                    <td style="font-weight: 700; color: #0f172a;">
                                        {{ $std->name ?? $std->student_name ?? 'Prospective Student' }}
                                    </td>
                                    <td>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.82rem;">
                                            +91 {{ $std->phoneno ?? $std->mobile ?? '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $std->emailid ?? $std->personal_email ?? '—' }}
                                    </td>
                                    <td>
                                        <span style="font-weight: 600; color: #475569;">
                                            Campus {{ $std->campuscode ?? $std->campus_id ?? '1' }}
                                        </span>
                                    </td>
                                    <td style="font-size: 0.8rem; color: #64748b;">
                                        {{ $std->log_timestamp ?? 'Recently' }}
                                    </td>
                                    <td>
                                        <span class="badge-verified">✔ Registered</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                        No registration records currently found in database.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="crm-footer">
        <div class="footer-inner">
            <span>&copy; 2026 VFSTR Vignan University &bull; CRM &amp; Admissions Administration System</span>
            <span>Logged in as: <strong>{{ $user?->name }}</strong> ({{ $roleName }})</span>
        </div>
    </footer>

</body>
</html>
