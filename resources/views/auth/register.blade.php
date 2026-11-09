<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form | VFSTR Vignan's University Admissions</title>
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
            --brand-primary: #1e3a8a;
            --brand-blue: #2563eb;
            --brand-blue-hover: #1d4ed8;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-light: #cbd5e1;
            --bg-body: #f8fafc;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #ffffff;
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            line-height: 1.5;
        }

        /* Top Header Bar */
        .top-navbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03);
        }

        .nav-inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0.65rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo-area {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
        }

        .brand-logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
        }

        .nav-menu-links {
            display: flex;
            align-items: center;
            gap: 1.75rem;
            list-style: none;
        }

        .nav-item-link {
            font-size: 0.9rem;
            font-weight: 600;
            color: #334155;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .nav-item-link:hover,
        .nav-item-link.active {
            color: #2563eb;
        }

        .btn-apply-nav {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: #0f2b5c;
            color: #ffffff;
            padding: 0.55rem 1.25rem;
            border-radius: 9999px;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-apply-nav:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        /* Main Registration Split Section */
        .page-wrapper {
            max-width: 1320px;
            width: 100%;
            margin: 0 auto;
            padding: 3rem 1.5rem 4rem 1.5rem;
            display: grid;
            grid-template-columns: 1.05fr 0.95fr;
            gap: 3.5rem;
            align-items: start;
            flex: 1;
        }

        /* Left Hero Column */
        .hero-left {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            padding-top: 2rem;
        }

        .hero-headline {
            font-size: 3.2rem;
            font-weight: 900;
            color: #0b1e42;
            line-height: 1.15;
            letter-spacing: -0.025em;
        }

        .hero-headline span {
            color: #1e3a8a;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: #475569;
            line-height: 1.6;
            max-width: 520px;
        }

        .accreditation-row {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-top: 0.5rem;
        }

        .accred-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 0.9rem;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 700;
            color: #1e293b;
        }

        /* Right Form Card (Matching Screenshot exactly) */
        .registration-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 2.25rem 2.25rem 2rem 2.25rem;
            box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.02);
        }

        .card-header-title {
            text-align: center;
            font-size: 1.35rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        .reg-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .form-row-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .form-row-2col .select-field {
            font-size: 0.84rem;
            padding: 0.76rem 2.25rem 0.76rem 0.85rem;
        }

        @media (max-width: 600px) {
            .form-row-2col {
                grid-template-columns: 1fr;
            }
        }

        .form-row-3col {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 0.75rem;
        }

        @media (max-width: 600px) {
            .form-row-3col {
                grid-template-columns: 1fr;
            }
        }

        .form-row-4col {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.55rem;
        }

        .form-row-4col .select-field {
            font-size: 0.80rem;
            font-weight: 600;
            padding: 0.72rem 1.65rem 0.72rem 0.65rem;
            background-position: right 0.45rem center;
            background-size: 0.82rem;
            letter-spacing: -0.01em;
        }

        @media (max-width: 900px) {
            .form-row-4col {
                grid-template-columns: 1fr 1fr;
                gap: 0.75rem;
            }
            .form-row-4col .select-field {
                font-size: 0.85rem;
                padding: 0.76rem 2.25rem 0.76rem 0.85rem;
                background-position: right 0.75rem center;
                background-size: 0.95rem;
            }
        }

        @media (max-width: 480px) {
            .form-row-4col {
                grid-template-columns: 1fr;
            }
        }

        .input-text-field {
            width: 100%;
            padding: 0.76rem 0.95rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 500;
            color: #0f172a;
            font-family: inherit;
            background: #f8fafc;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-text-field:hover {
            border-color: #94a3b8;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
        }

        .input-text-field:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15), 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .input-text-field::placeholder {
            color: #94a3b8;
            font-size: 0.86rem;
            font-weight: 400;
        }

        .phone-group-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .phone-flag-box {
            position: absolute;
            left: 12px;
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.86rem;
            font-weight: 700;
            color: #475569;
            pointer-events: none;
            padding-right: 8px;
            border-right: 1px solid #cbd5e1;
        }

        .phone-with-flag {
            padding-left: 4.25rem;
        }

        .select-field {
            width: 100%;
            padding: 0.76rem 2.25rem 0.76rem 0.85rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 600;
            color: #0f172a;
            font-family: inherit;
            background-color: #f8fafc;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 0.95rem;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .select-field:hover {
            border-color: #94a3b8;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
        }

        .select-field:focus {
            outline: none;
            border-color: #2563eb;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15), 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .select-field option {
            font-size: 0.88rem;
            font-weight: 500;
            color: #0f172a;
            background-color: #ffffff;
            padding: 10px 14px;
        }

        .select-field option:hover,
        .select-field option:focus,
        .select-field option:checked {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: 700;
        }

        /* ========================================================
           MODERN CUSTOM DROPDOWN UI (Replaces Raw Native OS Menu)
           ======================================================== */
        .custom-select-container {
            position: relative;
            width: 100%;
        }

        .custom-select-container.is-open {
            z-index: 999;
        }

        .custom-select-trigger {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            padding: 0.76rem 0.95rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 600;
            color: #0f172a;
            font-family: inherit;
            background-color: #f8fafc;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            text-align: left;
        }

        .custom-select-trigger:hover {
            border-color: #94a3b8;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
        }

        .custom-select-container.is-open .custom-select-trigger,
        .custom-select-trigger:focus {
            outline: none;
            border-color: #2563eb;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15), 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .custom-select-trigger.field-error {
            border: 2px solid #ef4444 !important;
            background: #fff5f5 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
        }

        .custom-select-value {
            flex: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .custom-select-value.is-placeholder {
            color: #64748b;
            font-weight: 500;
        }

        .custom-select-arrow {
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
            color: #2563eb;
            transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-select-container.is-open .custom-select-arrow {
            transform: rotate(180deg);
        }

        /* 4-column compact triggers */
        .form-row-4col .custom-select-trigger {
            font-size: 0.80rem;
            font-weight: 600;
            padding: 0.72rem 0.6rem;
            letter-spacing: -0.01em;
        }

        .form-row-4col .custom-select-arrow {
            width: 0.85rem;
            height: 0.85rem;
        }

        @media (max-width: 900px) {
            .form-row-4col .custom-select-trigger {
                font-size: 0.85rem;
                padding: 0.76rem 0.95rem;
            }
            .form-row-4col .custom-select-arrow {
                width: 1rem;
                height: 1rem;
            }
        }

        /* Floating Custom Dropdown Menu Card */
        .custom-select-dropdown {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            min-width: 100%;
            width: max-content;
            max-width: min(380px, 92vw);
            background: #ffffff;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.14), 0 6px 14px -3px rgba(15, 23, 42, 0.07);
            padding: 0.4rem;
            max-height: 240px;
            overflow-y: auto;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
        }

        .form-row-2col .custom-select-dropdown {
            width: 100%;
            max-width: 100%;
        }

        .custom-select-container.is-open .custom-select-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        /* Sleek Modern Scrollbar */
        .custom-select-dropdown::-webkit-scrollbar {
            width: 6px;
        }

        .custom-select-dropdown::-webkit-scrollbar-track {
            background: #f8fafc;
            border-radius: 6px;
        }

        .custom-select-dropdown::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 6px;
        }

        .custom-select-dropdown::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Custom Dropdown Option Items */
        .custom-select-option {
            padding: 0.65rem 0.85rem;
            font-size: 0.86rem;
            font-weight: 500;
            color: #1e293b;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            line-height: 1.35;
        }

        .custom-select-option:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
            transform: translateX(2px);
        }

        .custom-select-option.is-selected {
            background-color: #2563eb;
            color: #ffffff !important;
            font-weight: 700;
        }

        .custom-select-option.is-placeholder {
            color: #94a3b8;
            font-style: italic;
            border-bottom: 1px dashed #e2e8f0;
            margin-bottom: 0.25rem;
            border-radius: 6px 6px 0 0;
        }

        .custom-select-option.is-placeholder:hover {
            background-color: #f8fafc;
            color: #64748b;
            transform: none;
        }

        /* Choose Program Level Tabs */
        .level-section {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
            margin-top: 0.15rem;
        }

        .level-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: #334155;
        }

        .level-pills-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.4rem;
            background: #f1f5f9;
            padding: 0.35rem;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
        }

        .level-pill-btn {
            padding: 0.55rem 0.5rem;
            border: none;
            background: transparent;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .level-pill-btn.pill-active {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        /* Captcha Card */
        .captcha-card {
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 0.65rem 0.95rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.25rem;
        }

        .captcha-checkbox-label {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-size: 0.84rem;
            font-weight: 600;
            color: #1e293b;
            cursor: pointer;
        }

        .captcha-checkbox-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #2563eb;
        }

        .captcha-badge-secure {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-size: 0.62rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Register Submit Button */
        .btn-register-submit {
            width: 100%;
            padding: 0.9rem 1.5rem;
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.96rem;
            font-weight: 800;
            cursor: pointer;
            font-family: inherit;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.4);
            transition: all 0.2s ease;
            margin-top: 0.5rem;
        }

        .btn-register-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px -4px rgba(37, 99, 235, 0.5);
            background: linear-gradient(135deg, #1e40af 0%, #172554 100%);
        }

        .login-back-link {
            text-align: center;
            font-size: 0.82rem;
            color: #64748b;
            margin-top: 0.85rem;
        }

        .login-back-link a {
            color: #2563eb;
            font-weight: 700;
            text-decoration: none;
        }

        .login-back-link a:hover {
            text-decoration: underline;
        }

        /* Banner alerts */
        .status-alert {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 1rem;
        }

        .error-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.82rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 960px) {
            .page-wrapper {
                grid-template-columns: 1fr;
                gap: 2rem;
                padding-top: 1.5rem;
            }
            .hero-headline {
                font-size: 2.2rem;
            }
            .watermark-bg {
                display: none;
            }
        }

        /* Background Watermark (Ultra-crisp Vector, Zero Pixelation) */
        .watermark-bg {
            position: fixed;
            top: 48%;
            left: 20%;
            transform: translate(-50%, -50%);
            width: min(540px, 46vw);
            height: min(620px, 55vh);
            background-image: url('/images/vignan-shield-vector.svg');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.055;
            pointer-events: none;
            z-index: 0;
            transition: opacity 0.3s ease, filter 0.3s ease;
        }

        /* Hero Headline with Side Logo (Parallely Matched to Full 3 Rows) */
        .hero-headline-wrap {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .hero-side-logo {
            height: 176px;
            width: auto;
            max-width: 160px;
            object-fit: contain;
            flex-shrink: 0;
            image-rendering: -webkit-optimize-contrast;
            filter: drop-shadow(0 6px 20px rgba(37, 99, 235, 0.25));
            transition: all 0.3s ease;
        }

        @media (max-width: 900px) {
            .hero-headline-wrap {
                gap: 1.15rem;
            }
            .hero-side-logo {
                height: 130px;
                max-width: 120px;
            }
        }

        @media (max-width: 600px) {
            .hero-headline-wrap {
                gap: 0.85rem;
            }
            .hero-side-logo {
                height: 95px;
                max-width: 85px;
            }
        }

        /* Form Validation Styles */
        .field-error {
            border: 2px solid #ef4444 !important;
            background: #fff5f5 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15) !important;
        }

        .field-error-msg {
            color: #dc2626;
            font-size: 0.74rem;
            font-weight: 700;
            margin-top: 0.25rem;
            display: none;
        }

        .field-error-msg.visible {
            display: block;
        }

        .validation-alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 0.7rem 0.95rem;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 600;
            margin-bottom: 0.85rem;
            display: none;
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <header class="top-navbar">
        <div class="nav-inner">
            <a href="/" class="brand-logo-area">
                <img src="/images/vignan-logo.png" alt="Vignan's University" class="brand-logo-img">
                <div style="display:flex;flex-direction:column;">
                    <span style="font-size:0.95rem;font-weight:900;color:#0b1e42;letter-spacing:-0.01em;">VIGNAN'S</span>
                    <span style="font-size:0.65rem;font-weight:700;color:#2563eb;letter-spacing:0.04em;">DEEMED TO BE UNIVERSITY &bull; NAAC A+</span>
                </div>
            </a>

            <div style="display:flex;align-items:center;gap:0.75rem;">
                <a href="{{ route('login') }}" class="btn-apply-nav">
                    <span>Sign In Portal &rarr;</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Background Watermark Layer (Ultra-sharp Vector, Zero Pixelation) -->
    <div class="watermark-bg" aria-hidden="true"></div>

    <!-- Page Content -->
    <main class="page-wrapper" style="position:relative; z-index:1;">

        <!-- Left Hero Area -->
        <section class="hero-left">
            <div class="hero-headline-wrap">
                <img src="/images/vignan-shield-clean.png" alt="Vignan's University" class="hero-side-logo">
                <h1 class="hero-headline">
                    Empower as <br>leader at <br><span>Vignan's University</span>
                </h1>
            </div>

            <p class="hero-subtitle">
                Join a legacy of academic excellence, cutting-edge research, state-of-the-art innovation centers, and top-tier placements across multinational leaders.
            </p>

            <div class="accreditation-row">
                <div class="accred-badge">
                    <span style="color:#059669;">✔</span> NAAC A+ Accredited
                </div>
                <div class="accred-badge">
                    <span style="color:#2563eb;">✔</span> NIRF Ranked Top 70
                </div>
                <div class="accred-badge">
                    <span style="color:#d97706;">✔</span> UGC Category-1 Graded
                </div>
                <div class="accred-badge">
                    <span style="color:#7c3aed;">✔</span> ABET &amp; NBA Certified
                </div>
            </div>
        </section>

        <!-- Right Side: Registration Form -->
        <section class="registration-card">
            <h2 class="card-header-title">Registration Form</h2>

            <div class="validation-alert" id="clientValidationAlert"></div>

            @if(session('status'))
                <div class="status-alert">
                    &bull; {{ session('status') }}
                </div>
            @endif

            @if($errors->any())
                <div class="error-alert">
                    @foreach($errors->all() as $err)
                        <div>&bull; {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register.post') }}" method="POST" class="reg-form" id="admissionRegisterForm" novalidate>
                @csrf
                <input type="hidden" name="program_level" id="input_program_level" value="UG">
                <input type="hidden" name="mobile" value="{{ $prefilledPhone }}">

                <!-- 1. Ph no: 9912996788 (from wireframe) -->
                <div style="padding-bottom: 0.65rem; border-bottom: 2px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between;">
                    <span style="font-size: 1.05rem; font-weight: 800; color: #0b1e42;">
                        Ph no: <span style="color: #2563eb; font-family: monospace;">{{ $prefilledPhone }}</span>
                    </span>
                    <span style="font-size: 0.72rem; font-weight: 700; color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.2rem 0.65rem; border-radius: 9999px;">
                        ✔ Verified
                    </span>
                </div>

                <!-- 2. select campus | select country | State | District (dynamically populated from DB) -->
                <div class="form-row-4col">
                    <select name="campus_id" required class="select-field">
                        <option value="">select campus</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->campus_id }}" {{ old('campus_id', 1) == $campus->campus_id ? 'selected' : '' }}>
                                {{ $campus->campus_short_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="country_id" id="country_select" required class="select-field" onchange="filterStates(this.value)">
                        <option value="">select country</option>
                        @foreach($countries as $country)
                            <option value="{{ $country->country_id }}" {{ old('country_id', 1) == $country->country_id ? 'selected' : '' }}>
                                {{ $country->country_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="state_id" id="state_select" required class="select-field" onchange="filterDistricts(this.value)">
                        <option value="">State</option>
                        @foreach($states as $st)
                            <option value="{{ $st->state_id }}" data-country="{{ $st->country_id }}" {{ old('state_id', 1) == $st->state_id ? 'selected' : '' }}>
                                {{ $st->state_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="district_id" id="district_select" required class="select-field">
                        <option value="">select District</option>
                        @foreach($districts as $dst)
                            <option value="{{ $dst->DISTRICT_ID }}" data-state="{{ $dst->STATE_ID }}" {{ old('district_id', 1) == $dst->DISTRICT_ID ? 'selected' : '' }}>
                                {{ $dst->DISTRICT_NAME }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 3. Undergrade | PG | Doctoral (from wireframe) -->
                <div class="level-section">
                    <div class="level-pills-row">
                        <button type="button" class="level-pill-btn pill-active" onclick="selectProgramLevel('UG', this)">Undergrade</button>
                        <button type="button" class="level-pill-btn" onclick="selectProgramLevel('PG', this)">PG</button>
                        <button type="button" class="level-pill-btn" onclick="selectProgramLevel('Doctoral', this)">Doctoral</button>
                    </div>
                </div>

                <!-- 4. select program | select Branch (from wireframe) -->
                <div class="form-row-2col">
                    <select name="progid" id="course_select" required class="select-field" onchange="updateBranches(this.value)">
                        <option value="">select program</option>
                        @foreach($programs as $prog)
                            <option value="{{ $prog->program_id }}" data-level="{{ $prog->program_level }}" {{ old('progid') == $prog->program_id ? 'selected' : '' }}>
                                {{ $prog->program_short_name }} - {{ $prog->program_long_name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="branch_id" id="branch_select" required class="select-field">
                        <option value="">select Branch</option>
                        @php
                            $initProgId = old('progid');
                            $initBranches = $initProgId ? $branches->where('program_id', $initProgId)->where('lock_status', 0)->unique('branch_long_name') : collect();
                        @endphp
                        @foreach($initBranches as $branch)
                            <option value="{{ $branch->branch_id }}" data-prog="{{ $branch->program_id }}" {{ old('branch_id') == $branch->branch_id ? 'selected' : '' }}>
                                {{ $branch->branch_long_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- 5. Name as per SSC -->
                <div>
                    <input type="text" 
                           name="applicant_name" 
                           value="{{ old('applicant_name') }}" 
                           required 
                           placeholder="Name as per SSC" 
                           class="input-text-field">
                </div>

                <!-- Email ID -->
                <div>
                    <input type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required 
                           placeholder="Email ID" 
                           class="input-text-field">
                </div>

                <!-- 6. Consent & Authorization -->
                <div style="display: flex; align-items: center; gap: 0.65rem; padding: 0.35rem 0.2rem;">
                    <input type="checkbox" name="ableto" id="ableto" value="1" checked required style="width: 18px; height: 18px; accent-color: #2563eb; cursor: pointer;">
                    <label for="ableto" style="font-size: 0.88rem; font-weight: 600; color: #1e293b; cursor: pointer;">
                        I authorize Vignan University to contact me &amp; use my data
                    </label>
                </div>

                <!-- 7. Submit Register Button -->
                <button type="submit" class="btn-register-submit" id="btnSubmit">
                    <span>Register Now &rarr;</span>
                </button>

                <div class="login-back-link">
                    Already registered? <a href="{{ route('login') }}">Sign In to Student Portal</a>
                </div>
            </form>
        </section>

    </main>

    <!-- Dynamic Course, Branch, State & District Scripts -->
    <script>
        const allPrograms  = @json($programs);
        const allBranches  = @json($branches);
        const allStates    = @json($states);
        const allDistricts = @json($districts);

        function selectProgramLevel(level, btnEl, preserveVal = false) {
            document.querySelectorAll('.level-pill-btn').forEach(b => b.classList.remove('pill-active'));
            if (btnEl) btnEl.classList.add('pill-active');
            document.getElementById('input_program_level').value = level;

            const courseSelect = document.getElementById('course_select');
            const previousCourseVal = preserveVal ? courseSelect.value : '';
            courseSelect.innerHTML = '<option value="">select program</option>';

            const filterLevel = (level === 'Doctoral') ? ['PhD', 'Doctoral'] : [level];
            const filtered = allPrograms.filter(p => filterLevel.includes(p.program_level));

            filtered.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.program_id;
                opt.textContent = `${p.program_short_name} - ${p.program_long_name}`;
                courseSelect.appendChild(opt);
            });

            // Do NOT auto-select the first program by default! Leave on "select program"
            if (previousCourseVal && filtered.some(p => String(p.program_id) === String(previousCourseVal))) {
                courseSelect.value = previousCourseVal;
                updateBranches(previousCourseVal, preserveVal);
            } else {
                courseSelect.value = '';
                updateBranches('', false);
            }
        }

        function updateBranches(progId, preserveVal = false) {
            const branchSelect = document.getElementById('branch_select');
            if (!branchSelect) return;
            const previousVal = preserveVal ? branchSelect.value : '';
            branchSelect.innerHTML = '<option value="">select Branch</option>';

            if (!progId) {
                branchSelect.value = '';
                return;
            }

            const filtered = allBranches.filter(b => String(b.program_id) === String(progId) && Number(b.lock_status ?? 0) === 0);
            const seen = new Set();
            if (filtered.length > 0) {
                filtered.forEach(b => {
                    const nameKey = (b.branch_long_name || '').trim().toLowerCase();
                    if (nameKey && !seen.has(nameKey)) {
                        seen.add(nameKey);
                        const opt = document.createElement('option');
                        opt.value = b.branch_id;
                        opt.textContent = b.branch_long_name;
                        branchSelect.appendChild(opt);
                    }
                });
                // Keep "select Branch" prompt selected! Do NOT auto-select index 1
                if (previousVal && filtered.some(b => String(b.branch_id) === String(previousVal))) {
                    branchSelect.value = previousVal;
                } else {
                    branchSelect.value = '';
                }
            } else {
                const opt = document.createElement('option');
                opt.value = 1;
                opt.textContent = 'General / Core Track';
                branchSelect.appendChild(opt);
                branchSelect.value = '';
            }
        }

        function filterDistricts(stateId) {
            const districtSelect = document.getElementById('district_select');
            if (!districtSelect) return;
            const previousVal = districtSelect.value;
            districtSelect.innerHTML = '<option value="">select District</option>';

            const filtered = allDistricts.filter(d => String(d.STATE_ID) === String(stateId));
            filtered.forEach(d => {
                const opt = document.createElement('option');
                opt.value = d.DISTRICT_ID;
                opt.textContent = d.DISTRICT_NAME;
                districtSelect.appendChild(opt);
            });

            if (filtered.some(d => String(d.DISTRICT_ID) === String(previousVal))) {
                districtSelect.value = previousVal;
            } else if (filtered.length > 0) {
                districtSelect.value = filtered[0].DISTRICT_ID;
            }
        }

        function filterStates(countryId) {
            const stateSelect = document.getElementById('state_select');
            if (!stateSelect) return;
            const previousVal = stateSelect.value;
            stateSelect.innerHTML = '<option value="">State</option>';

            const filtered = allStates.filter(s => String(s.country_id) === String(countryId));
            filtered.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.state_id;
                opt.textContent = s.state_name;
                stateSelect.appendChild(opt);
            });

            if (filtered.some(s => String(s.state_id) === String(previousVal))) {
                stateSelect.value = previousVal;
            } else if (filtered.length > 0) {
                stateSelect.value = filtered[0].state_id;
            }

            filterDistricts(stateSelect.value);
        }

        // Initialize with default UG level, Country, State & Branch
        document.addEventListener('DOMContentLoaded', () => {
            const countryEl = document.getElementById('country_select');
            if (countryEl && countryEl.value) {
                filterStates(countryEl.value);
            } else {
                const stateEl = document.getElementById('state_select');
                if (stateEl && stateEl.value) {
                    filterDistricts(stateEl.value);
                }
            }

            const currentLevel = document.getElementById('input_program_level')?.value || 'UG';
            const activeBtn = Array.from(document.querySelectorAll('.level-pill-btn'))
                .find(b => b.textContent.toLowerCase().includes(currentLevel.toLowerCase()) || (currentLevel === 'UG' && b.textContent.toLowerCase().includes('undergrade')));
            const oldProgId = '{{ old("progid", "") }}';
            if (activeBtn) {
                selectProgramLevel(currentLevel, activeBtn, Boolean(oldProgId));
            } else {
                const courseSelect = document.getElementById('course_select');
                if (courseSelect && courseSelect.value) {
                    updateBranches(courseSelect.value, Boolean(oldProgId));
                }
            }

            // Initialize Modern Custom Dropdown UI
            initCustomSelects();

            // Silent Auto-save on blur / change (No intimate message as requested)
            const form = document.getElementById('admissionRegisterForm');
            if (form) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const mobileVal = '{{ $prefilledPhone }}';

                const silentAutosave = function(fieldEl) {
                    const fieldName = fieldEl.name;
                    if (!fieldName || fieldName === '_token') return;
                    const fieldValue = fieldEl.value;
                    if (fieldValue === undefined || fieldValue === null || fieldValue === '') return;

                    fetch('{{ route("register.autosave") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            mobile: mobileVal,
                            field: fieldName,
                            value: fieldValue
                        })
                    }).catch(() => {}); // Intentionally silent: no alerts, toasts, or popup messages
                };

                form.querySelectorAll('input:not([type="hidden"]), select, textarea').forEach(function(el) {
                    el.addEventListener('blur', function() { silentAutosave(this); });
                    el.addEventListener('change', function() { silentAutosave(this); });
                });

                function showClientError(msg, fieldEl) {
                    const alertBox = document.getElementById('clientValidationAlert');
                    if (alertBox) {
                        alertBox.textContent = '⚠️ ' + msg;
                        alertBox.style.display = 'block';
                        alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                    if (fieldEl) {
                        fieldEl.classList.add('field-error');
                        const container = fieldEl.closest('.custom-select-container');
                        if (container) {
                            const trigger = container.querySelector('.custom-select-trigger');
                            if (trigger) trigger.classList.add('field-error');
                            trigger.focus();
                        } else {
                            fieldEl.focus();
                        }
                    }
                }

                function clearClientErrors() {
                    const alertBox = document.getElementById('clientValidationAlert');
                    if (alertBox) {
                        alertBox.style.display = 'none';
                        alertBox.textContent = '';
                    }
                    form.querySelectorAll('.field-error').forEach(el => el.classList.remove('field-error'));
                    document.querySelectorAll('.custom-select-trigger.field-error').forEach(el => el.classList.remove('field-error'));
                }

                // Auto-clear validation errors on input/change
                form.querySelectorAll('input, select').forEach(el => {
                    el.addEventListener('input', function() {
                        this.classList.remove('field-error');
                        const alertBox = document.getElementById('clientValidationAlert');
                        if (alertBox) alertBox.style.display = 'none';
                    });
                    el.addEventListener('change', function() {
                        this.classList.remove('field-error');
                        const alertBox = document.getElementById('clientValidationAlert');
                        if (alertBox) alertBox.style.display = 'none';
                    });
                });

                // Form validation & submission control
                let isSubmitting = false;
                form.addEventListener('submit', function(e) {
                    clearClientErrors();

                    const campus = form.querySelector('[name="campus_id"]');
                    if (!campus || !campus.value) {
                        e.preventDefault();
                        showClientError('Please select a campus to continue.', campus);
                        return false;
                    }

                    const country = document.getElementById('country_select');
                    if (!country || !country.value) {
                        e.preventDefault();
                        showClientError('Please select a country.', country);
                        return false;
                    }

                    const state = document.getElementById('state_select');
                    if (!state || !state.value) {
                        e.preventDefault();
                        showClientError('Please select a state.', state);
                        return false;
                    }

                    const district = document.getElementById('district_select');
                    if (!district || !district.value) {
                        e.preventDefault();
                        showClientError('Please select a district.', district);
                        return false;
                    }

                    const course = document.getElementById('course_select');
                    if (!course || !course.value) {
                        e.preventDefault();
                        showClientError('Please select an academic program.', course);
                        return false;
                    }

                    const branch = document.getElementById('branch_select');
                    if (branch && branch.options.length > 1 && !branch.value) {
                        e.preventDefault();
                        showClientError('Please select a branch or specialization.', branch);
                        return false;
                    }

                    const applicantName = form.querySelector('[name="applicant_name"]');
                    if (!applicantName || !applicantName.value.trim() || applicantName.value.trim().length < 2) {
                        e.preventDefault();
                        showClientError('Please enter your full name as per SSC (minimum 2 characters).', applicantName);
                        return false;
                    }

                    const email = form.querySelector('[name="email"]');
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!email || !email.value.trim() || !emailRegex.test(email.value.trim())) {
                        e.preventDefault();
                        showClientError('Please enter a valid email address (e.g., student@example.com).', email);
                        return false;
                    }

                    const consent = document.getElementById('ableto');
                    if (consent && !consent.checked) {
                        e.preventDefault();
                        showClientError('Please check the authorization box to proceed.', consent);
                        return false;
                    }

                    if (isSubmitting) {
                        e.preventDefault();
                        return false;
                    }
                    isSubmitting = true;
                    const btn = document.getElementById('btnSubmit');
                    if (btn) {
                        btn.disabled = true;
                        btn.style.opacity = '0.7';
                        btn.innerHTML = '<span>Saving Registration...</span>';
                    }
                });
            }
        });

        // ========================================================
        // Modern Custom Dropdown UI Engine
        // ========================================================
        function initCustomSelects() {
            document.querySelectorAll('select.select-field').forEach(selectEl => {
                if (selectEl.dataset.customInit) {
                    syncCustomSelect(selectEl);
                    return;
                }
                selectEl.dataset.customInit = 'true';

                // Hide native select accessibility-friendly
                selectEl.style.position = 'absolute';
                selectEl.style.width = '1px';
                selectEl.style.height = '1px';
                selectEl.style.opacity = '0';
                selectEl.style.pointerEvents = 'none';

                // Container
                const container = document.createElement('div');
                container.className = 'custom-select-container';
                selectEl.parentNode.insertBefore(container, selectEl);
                container.appendChild(selectEl);

                // Trigger Button
                const trigger = document.createElement('button');
                trigger.type = 'button';
                trigger.className = 'custom-select-trigger';
                trigger.innerHTML = `
                    <span class="custom-select-value"></span>
                    <svg class="custom-select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                `;
                container.appendChild(trigger);

                // Dropdown Menu Card
                const dropdown = document.createElement('div');
                dropdown.className = 'custom-select-dropdown';
                container.appendChild(dropdown);

                // Toggle menu on click
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = container.classList.contains('is-open');
                    document.querySelectorAll('.custom-select-container.is-open').forEach(c => c.classList.remove('is-open'));
                    if (!isOpen) {
                        container.classList.add('is-open');
                        const selectedItem = dropdown.querySelector('.is-selected');
                        if (selectedItem) {
                            selectedItem.scrollIntoView({ block: 'nearest' });
                        }
                    }
                });

                // Populate initial options
                syncCustomSelect(selectEl);

                // Watch for value changes
                selectEl.addEventListener('change', () => {
                    updateTriggerLabel(selectEl, container);
                    syncCustomSelect(selectEl);
                });

                // Observe DOM option changes (e.g. program level or state filtering)
                const observer = new MutationObserver(() => {
                    syncCustomSelect(selectEl);
                });
                observer.observe(selectEl, { childList: true });
            });
        }

        function updateTriggerLabel(selectEl, container) {
            const trigger = container.querySelector('.custom-select-trigger');
            const valueSpan = container.querySelector('.custom-select-value');
            if (!trigger || !valueSpan) return;

            const selectedOption = selectEl.options[selectEl.selectedIndex];
            if (selectedOption && selectEl.value !== '') {
                valueSpan.textContent = selectedOption.textContent.trim();
                valueSpan.classList.remove('is-placeholder');
            } else {
                valueSpan.textContent = (selectEl.options[0]?.textContent || 'Select...').trim();
                valueSpan.classList.add('is-placeholder');
            }

            if (selectEl.classList.contains('field-error')) {
                trigger.classList.add('field-error');
            } else {
                trigger.classList.remove('field-error');
            }
        }

        function syncCustomSelect(selectEl) {
            const container = selectEl.closest('.custom-select-container');
            if (!container) return;
            const dropdown = container.querySelector('.custom-select-dropdown');
            if (!dropdown) return;

            dropdown.innerHTML = '';
            Array.from(selectEl.options).forEach((opt) => {
                const item = document.createElement('div');
                item.className = 'custom-select-option';
                item.dataset.value = opt.value;
                item.textContent = opt.textContent;

                if (opt.value === '') {
                    item.classList.add('is-placeholder');
                }
                if (String(opt.value) === String(selectEl.value) && selectEl.value !== '') {
                    item.classList.add('is-selected');
                }

                item.addEventListener('click', (e) => {
                    e.stopPropagation();
                    selectEl.value = opt.value;
                    container.classList.remove('is-open');
                    selectEl.classList.remove('field-error');
                    updateTriggerLabel(selectEl, container);

                    // Dispatch events
                    selectEl.dispatchEvent(new Event('change', { bubbles: true }));
                    selectEl.dispatchEvent(new Event('input', { bubbles: true }));

                    // Trigger inline onchange if defined
                    if (typeof selectEl.onchange === 'function') {
                        selectEl.onchange();
                    }
                });

                dropdown.appendChild(item);
            });

            updateTriggerLabel(selectEl, container);
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.custom-select-container.is-open').forEach(c => c.classList.remove('is-open'));
        });

        // Close dropdown on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.custom-select-container.is-open').forEach(c => c.classList.remove('is-open'));
            }
        });

        // Synchronize side logo height to precisely match the 3 rows of headline text
        function syncLogoHeight() {
            const headline = document.querySelector('.hero-headline');
            const logo = document.querySelector('.hero-side-logo');
            if (headline && logo) {
                const h = headline.offsetHeight;
                if (h > 60) {
                    logo.style.height = h + 'px';
                }
            }
        }
        window.addEventListener('resize', syncLogoHeight);
        window.addEventListener('load', syncLogoHeight);
        document.addEventListener('DOMContentLoaded', syncLogoHeight);
        setTimeout(syncLogoHeight, 50);
        setTimeout(syncLogoHeight, 300);
    </script>
</body>
</html>

