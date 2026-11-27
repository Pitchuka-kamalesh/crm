<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Error') &bull; VFSTR Admissions CRM</title>
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #991b1b;
            --primary-hover: #7f1d1d;
            --primary-light: #fee2e2;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --card-bg: #ffffff;
            --page-bg: #f8fafc;
            --font-sans: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            --font-mono: 'JetBrains Mono', monospace;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background: var(--page-bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
            line-height: 1.5;
        }

        @keyframes popIn {
            0% {
                opacity: 0;
                transform: scale(0.97) translateY(8px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .error-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.03);
            max-width: 620px;
            width: 100%;
            padding: 2.25rem 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: popIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .error-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #991b1b, #dc2626, #f97316);
        }

        .brand-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 1.75rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #f1f5f9;
            width: 100%;
        }

        .brand-left {
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .brand-logo {
            width: 36px;
            height: 36px;
            object-fit: contain;
        }

        .brand-name {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: #0f172a;
            line-height: 1.2;
        }

        .brand-sub {
            font-size: 0.68rem;
            color: #64748b;
        }

        /* User Header Actions Widget (Matching CRM Topbar) */
        .user-header-actions-group {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            padding: 3px 6px;
            border-radius: 9999px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        }

        .user-profile-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            text-decoration: none;
        }

        .user-avatar-red {
            width: 28px;
            height: 28px;
            background: #991b1b;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.82rem;
            line-height: 1;
            box-shadow: 0 2px 5px rgba(153, 27, 27, 0.3);
            flex-shrink: 0;
        }

        .user-text-stack {
            display: flex;
            flex-direction: column;
            text-align: left;
            line-height: 1.15;
            max-width: 140px;
        }

        .user-name-bold {
            font-size: 0.76rem;
            font-weight: 800;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role-sub {
            font-size: 0.65rem;
            font-weight: 600;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 1px;
        }

        .topbar-quick-logout-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            color: #64748b;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.15s ease-in-out;
            flex-shrink: 0;
        }

        .topbar-quick-logout-btn:hover {
            background: #fee2e2;
            border-color: #fca5a5;
            color: #dc2626;
            transform: scale(1.05);
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.18);
        }

        .btn-login-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 700;
            background: #f1f5f9;
            color: #334155;
            text-decoration: none;
            border: 1px solid #cbd5e1;
            transition: all 0.15s ease;
        }

        .btn-login-pill:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 14px;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 700;
            margin-bottom: 1rem;
            letter-spacing: 0.02em;
        }

        .status-pill.error-500 { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .status-pill.error-403 { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .status-pill.error-404 { background: #e0f2fe; color: #075985; border: 1px solid #bae6fd; }
        .status-pill.error-400 { background: #ffedd5; color: #9a3412; border: 1px solid #fed7aa; }
        .status-pill.error-503 { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }

        .error-code {
            font-size: 4.75rem;
            font-weight: 900;
            line-height: 1;
            letter-spacing: -0.05em;
            color: #0f172a;
            margin-bottom: 0.5rem;
            font-feature-settings: "tnum";
        }

        .error-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }

        .error-desc {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 1.75rem;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .actions-group {
            display: flex;
            gap: 0.75rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            font-size: 0.88rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: #991b1b;
            color: #ffffff;
            box-shadow: 0 2px 4px rgba(153, 27, 27, 0.2);
        }

        .btn-primary:hover {
            background: #7f1d1d;
            color: #ffffff;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #ffffff;
            color: #334155;
            border-color: #cbd5e1;
        }

        .btn-secondary:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .btn-logout {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }

        .btn-logout:hover {
            background: #ffe4e6;
            color: #be123c;
            border-color: #fda4af;
            transform: translateY(-1px);
        }

        .details-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 1rem;
            text-align: left;
            margin-top: 1.5rem;
            font-size: 0.8rem;
        }

        .details-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
            font-weight: 600;
            color: #475569;
            user-select: none;
        }

        .details-content {
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px dashed #cbd5e1;
            font-family: var(--font-mono);
            color: #334155;
            word-break: break-all;
            display: none;
        }

        .details-content.open {
            display: block;
        }

        .meta-tag {
            display: inline-block;
            background: #e2e8f0;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.75rem;
            margin-right: 4px;
        }

        .footer-note {
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: #94a3b8;
        }

        @media (max-width: 540px) {
            .brand-header {
                flex-direction: column;
                gap: 12px;
                text-align: center;
            }
            .brand-left {
                justify-content: center;
            }
            .user-header-actions-group {
                justify-content: center;
            }
            .error-code {
                font-size: 3.8rem;
            }
            .actions-group {
                flex-direction: column;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

<div class="error-card">
    <div class="brand-header">
        <div class="brand-left">
            <img src="/images/vignan-shield-logo.png" alt="Vignan Logo" class="brand-logo" onerror="this.style.display='none'">
            <div>
                <div class="brand-name">Vignan Admission CRM</div>
                <div class="brand-sub">Deemed to be University &bull; Guntur, AP</div>
            </div>
        </div>

        @if(auth()->check())
            @php
                $currentUser = auth()->user();
                $userNameRaw = $currentUser?->name ?: ($currentUser?->usercode ?: 'User');
                $userInitial = strtoupper(substr(trim($userNameRaw), 0, 1));
                $userNameDisplay = strtoupper($userNameRaw);
                $activeRoleName = method_exists($currentUser, 'getActiveRoleName')
                    ? $currentUser->getActiveRoleName()
                    : ($currentUser->role_label ?? 'Employee');
            @endphp
            <!-- User Profile & Quick Logout Action Widget (Matches Topbar Header) -->
            <div class="user-header-actions-group" title="Signed in as {{ $userNameDisplay }} ({{ $activeRoleName }})">
                <div class="user-profile-pill">
                    <div class="user-avatar-red">{{ $userInitial }}</div>
                    <div class="user-text-stack">
                        <span class="user-name-bold">{{ $userNameDisplay }}</span>
                        <span class="user-role-sub">{{ $activeRoleName }}</span>
                    </div>
                </div>
                <a href="{{ route('logout') }}" class="topbar-quick-logout-btn" title="Sign Out of Portal" aria-label="Sign Out">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </a>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn-login-pill" title="Sign In to Portal">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                    <polyline points="10 17 15 12 10 7"/>
                    <line x1="15" y1="12" x2="3" y2="12"/>
                </svg>
                <span>Sign In</span>
            </a>
        @endif
    </div>

    @yield('content')

    <div class="footer-note">
        Incident logged at {{ now()->timezone('Asia/Kolkata')->format('d M Y, h:i:s A') }} IST &bull; Ref: {{ substr(hash('sha256', request()->fullUrl() . now()), 0, 10) }}
    </div>
</div>

<script>
    function toggleDetails() {
        var el = document.getElementById('detailsContent');
        if (el) {
            el.classList.toggle('open');
        }
    }
</script>

</body>
</html>
