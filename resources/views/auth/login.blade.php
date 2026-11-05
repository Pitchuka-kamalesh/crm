<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admission Portal | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-primary: #2563eb;
            --brand-primary-hover: #1d4ed8;
            --brand-accent: #38bdf8;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
        }

        html, body {
            height: 100vh;
            max-height: 100vh;
            overflow: hidden;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: linear-gradient(135deg, rgba(6, 14, 28, 0.76) 0%, rgba(10, 22, 42, 0.65) 45%, rgba(13, 27, 51, 0.78) 100%),
                        url('/images/vignan-campus-real.jpg') center/cover no-repeat fixed;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 1.25rem 2.5rem;
            line-height: 1.5;
            position: relative;
        }

        /* Top Header */
        .top-header {
            max-width: 1320px;
            width: 100%;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            position: relative;
            z-index: 20;
        }

        .badge-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.4rem 1.1rem;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: #bae6fd;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.25);
        }

        .logo-img-mini {
            width: 22px;
            height: 22px;
            object-fit: contain;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.76rem;
            color: #e2e8f0;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            padding: 0.35rem 0.9rem;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            box-shadow: 0 0 10px #10b981;
        }

        /* Main Container */
        .main-container {
            max-width: 1320px;
            width: 100%;
            margin: auto;
            display: grid;
            grid-template-columns: 1.18fr 0.82fr;
            gap: 3.5rem;
            align-items: center;
            position: relative;
            z-index: 10;
        }

        /* Left Side: Hero & Interactive Bubbles */
        .hero-section {
            display: flex;
            flex-direction: column;
            gap: 1.75rem;
        }

        .hero-title-area {
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
        }

        .tag-label {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.28rem 0.8rem;
            background: rgba(37, 99, 235, 0.25);
            border: 1px solid rgba(56, 189, 248, 0.35);
            border-radius: 8px;
            font-size: 0.74rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #38bdf8;
            width: fit-content;
            backdrop-filter: blur(8px);
        }

        .hero-title {
            font-size: 2.9rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            line-height: 1.15;
            color: #ffffff;
            text-shadow: 0 2px 12px rgba(0, 0, 0, 0.4);
        }

        .hero-title-gradient {
            background: linear-gradient(90deg, #38bdf8 0%, #60a5fa 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 35px rgba(56, 189, 248, 0.4);
        }

        .hero-desc {
            font-size: 0.95rem;
            color: #e2e8f0;
            max-width: 580px;
            line-height: 1.6;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
        }

        /* Interactive Floating Bubbles Section */
        .bubbles-cluster-wrap {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .bubbles-cluster-title {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .bubbles-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.85rem;
        }

        /* Individual Feature Bubble */
        .feature-bubble {
            position: relative;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
            padding: 0.55rem 1.1rem 0.55rem 0.65rem;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .feature-bubble:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(56, 189, 248, 0.6);
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.35), 0 0 20px rgba(56, 189, 248, 0.3);
            z-index: 30;
        }

        .bubble-icon {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        .bubble-icon svg {
            width: 17px;
            height: 17px;
        }

        .bubble-blue { background: #2563eb; color: #ffffff; }
        .bubble-cyan { background: #0284c7; color: #ffffff; }
        .bubble-emerald { background: #059669; color: #ffffff; }
        .bubble-amber { background: #d97706; color: #ffffff; }
        .bubble-purple { background: #7c3aed; color: #ffffff; }

        .bubble-label {
            font-size: 0.85rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.01em;
            white-space: nowrap;
        }

        .bubble-badge-indicator {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #38bdf8;
            box-shadow: 0 0 6px #38bdf8;
        }

        /* Hover Tooltip Card Popover */
        .bubble-popover {
            position: absolute;
            bottom: calc(100% + 12px);
            left: 50%;
            transform: translateX(-50%) translateY(8px);
            width: 290px;
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(56, 189, 248, 0.4);
            border-radius: 16px;
            padding: 1rem 1.15rem;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.6), 0 0 25px rgba(37, 99, 235, 0.25);
            pointer-events: none;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            z-index: 50;
        }

        .bubble-popover::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            border-width: 7px;
            border-style: solid;
            border-color: rgba(15, 23, 42, 0.95) transparent transparent transparent;
        }

        .feature-bubble:hover .bubble-popover {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        .popover-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.35rem;
        }

        .popover-title {
            font-size: 0.88rem;
            font-weight: 800;
            color: #ffffff;
        }

        .popover-badge {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            white-space: nowrap;
        }

        .popover-badge-blue { background: rgba(37, 99, 235, 0.3); color: #93c5fd; border: 1px solid rgba(147, 197, 253, 0.3); }
        .popover-badge-cyan { background: rgba(14, 165, 233, 0.3); color: #7dd3fc; border: 1px solid rgba(125, 211, 252, 0.3); }
        .popover-badge-emerald { background: rgba(16, 185, 129, 0.3); color: #6ee7b7; border: 1px solid rgba(110, 231, 183, 0.3); }
        .popover-badge-amber { background: rgba(245, 158, 11, 0.3); color: #fde68a; border: 1px solid rgba(253, 230, 138, 0.3); }
        .popover-badge-purple { background: rgba(168, 85, 247, 0.3); color: #e9d5ff; border: 1px solid rgba(233, 213, 255, 0.3); }

        .popover-text {
            font-size: 0.78rem;
            color: #cbd5e1;
            line-height: 1.45;
        }

        /* Right Side: Clean White Login Card */
        .login-card-wrapper {
            display: flex;
            justify-content: flex-end;
        }

        .login-card {
            background: #ffffff;
            border-radius: 1.75rem;
            padding: 2rem 2.25rem 1.5rem 2.25rem;
            width: 100%;
            max-width: 430px;
            box-shadow: 0 25px 60px -12px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(255, 255, 255, 0.15);
            color: var(--text-dark);
            position: relative;
        }

        .card-logo-area {
            text-align: center;
            margin-bottom: 1.1rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
        }

        .crest-icon-box {
            width: 62px;
            height: 62px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.25rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 7px;
        }

        .crest-icon-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .crest-subtext {
            font-size: 0.7rem;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .card-heading {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }

        .card-heading span {
            color: #2563eb;
        }

        .card-subtitle {
            font-size: 0.78rem;
            color: #64748b;
            font-weight: 500;
        }

        /* Form Controls */
        .form-group {
            margin-bottom: 1.1rem;
            display: flex;
            flex-direction: column;
            gap: 0.4rem;
        }

        .form-label {
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-prefix {
            position: absolute;
            left: 14px;
            display: flex;
            align-items: center;
            font-size: 0.84rem;
            font-weight: 700;
            color: #64748b;
            pointer-events: none;
            padding-right: 8px;
            border-right: 1px solid #e2e8f0;
        }

        .input-control {
            width: 100%;
            padding: 0.82rem 1rem;
            font-size: 0.9rem;
            color: #0f172a;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .input-control:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .input-with-prefix {
            padding-left: 4.25rem;
        }

        .btn-link {
            font-size: 0.76rem;
            font-weight: 700;
            color: #2563eb;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-link:hover {
            text-decoration: underline;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            padding: 0.85rem 1.25rem;
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            transition: all 0.2s ease;
            font-family: inherit;
            margin-top: 1rem;
        }

        .btn-submit:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px -4px rgba(37, 99, 235, 0.5);
            background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit svg {
            width: 18px;
            height: 18px;
            transition: transform 0.2s ease;
        }

        .btn-submit:hover svg {
            transform: translateX(3px);
        }

        /* Checkbox row */
        .remember-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.78rem;
            color: #475569;
            margin-top: 0.25rem;
            cursor: pointer;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #2563eb;
            cursor: pointer;
        }

        /* OTP Notification Box */
        .otp-banner {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 12px;
            padding: 0.75rem 0.85rem;
            margin-bottom: 1rem;
            font-size: 0.8rem;
            color: #1e3a8a;
        }

        .otp-banner-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.2rem;
            font-weight: 700;
        }

        .otp-code-pill {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            border: 1px solid #93c5fd;
            border-radius: 8px;
            padding: 0.3rem 0.6rem;
            margin-top: 0.4rem;
            font-family: monospace;
            font-weight: 700;
            font-size: 0.95rem;
            color: #1d4ed8;
        }

        .otp-autofill-btn {
            font-size: 0.75rem;
            color: #2563eb;
            background: none;
            border: none;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
        }

        /* Error Banner */
        .error-banner {
            background: #fff1f2;
            border: 1px solid #fecdd3;
            color: #9f1239;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        /* Success Banner */
        .status-banner {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }

        /* Card Footer */
        .card-footer {
            margin-top: 1.1rem;
            padding-top: 0.85rem;
            border-top: 1px solid #f1f5f9;
            text-align: center;
            font-size: 0.72rem;
            color: #94a3b8;
            line-height: 1.5;
        }

        .card-footer-power {
            color: #64748b;
            font-weight: 600;
        }

        .hidden {
            display: none !important;
        }

        /* Step Multi-Transition Container */
        .step-container {
            position: relative;
            width: 100%;
        }

        .step-pane {
            width: 100%;
            transition: opacity 0.28s ease, transform 0.28s ease;
        }

        .step-pane.step-active {
            display: block;
            opacity: 1;
            transform: translateX(0);
        }

        .step-pane.step-hidden {
            display: none;
            opacity: 0;
            transform: translateX(18px);
        }

        /* Phone Summary Pill in Step 2 */
        .phone-summary-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.65rem 0.95rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.15rem;
        }

        .summary-left {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .summary-label {
            font-size: 0.68rem;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .summary-phone {
            font-size: 0.94rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.02em;
        }

        .btn-edit-phone {
            background: rgba(37, 99, 235, 0.08);
            color: #2563eb;
            border: 1px solid rgba(37, 99, 235, 0.2);
            font-size: 0.74rem;
            font-weight: 700;
            border-radius: 8px;
            padding: 0.3rem 0.65rem;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .btn-edit-phone:hover {
            background: #2563eb;
            color: #ffffff;
        }

        .field-hint {
            font-size: 0.74rem;
            color: #64748b;
            margin-top: 0.35rem;
            line-height: 1.4;
        }

        .otp-input-field {
            font-family: monospace !important;
            font-size: 1.25rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.25em !important;
            text-align: center;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            html, body {
                height: auto;
                max-height: none;
                min-height: 100vh;
                overflow-y: auto;
            }
            body {
                padding: 1.5rem 1rem;
                justify-content: center;
            }
            .main-container {
                grid-template-columns: 1fr;
                gap: 2rem;
                margin: auto 0;
            }
            .hero-title {
                font-size: 2.1rem;
            }
            .login-card-wrapper {
                justify-content: center;
            }
            .login-card {
                max-width: 440px;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem 0.75rem;
            }
            .login-card {
                padding: 1.75rem 1.25rem;
                border-radius: 1.25rem;
            }
            .hero-title {
                font-size: 1.65rem;
            }
            .hero-desc {
                font-size: 0.84rem;
            }
            .crest-icon-box {
                width: 54px;
                height: 54px;
            }
            .card-heading {
                font-size: 1.2rem;
            }
            .bubbles-grid {
                gap: 0.45rem;
            }
            .feature-bubble {
                padding: 0.4rem 0.75rem 0.4rem 0.45rem;
                gap: 0.45rem;
            }
            .bubble-label {
                font-size: 0.76rem;
            }
            .bubble-icon {
                width: 26px;
                height: 26px;
            }
            .bubble-icon svg {
                width: 13px;
                height: 13px;
            }
            .btn-submit {
                padding: 0.85rem 1rem;
                font-size: 0.88rem;
            }
        }
    </style>
</head>
<body>

    <!-- Main Container -->
    <main class="main-container">

        <!-- Left Hero Section: Exclusively Student Admission Journey -->
        <section class="hero-section">
            <div class="hero-title-area">
                <div class="tag-label">
                    <span>&bull;</span> Official Student Admission Portal
                </div>
                <h1 class="hero-title">
                    Vignan University <br><span class="hero-title-gradient">Student Admission Portal</span>
                </h1>
                <p class="hero-desc">
                    Welcome prospective and enrolled candidates! Track your admission application, verify certificates, check program allotment, and complete enrollment onboarding.
                </p>
            </div>

            <!-- 5 Interactive Admission Bubbles (Hover reveals full details) -->
            <div class="bubbles-cluster-wrap">
                <div class="bubbles-cluster-title">
                    <span>&bull;</span> Admission Process &amp; Student Services (Hover to view details):
                </div>

                <div class="bubbles-grid">

                    <!-- Bubble 1: Online Application -->
                    <div class="feature-bubble">
                        <div class="bubble-icon bubble-blue">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <span class="bubble-label">Online Application</span>
                        <span class="bubble-badge-indicator"></span>

                        <!-- Hover Popover -->
                        <div class="bubble-popover">
                            <div class="popover-header">
                                <span class="popover-title">Online Application</span>
                                <span class="popover-badge popover-badge-blue">&bull; Batch 2026-27</span>
                            </div>
                            <p class="popover-text">Track application status, chosen program tracks (MBA, MCA, BBA, BCA), personal profile, and category reservation.</p>
                        </div>
                    </div>

                    <!-- Bubble 2: Document Verification -->
                    <div class="feature-bubble">
                        <div class="bubble-icon bubble-cyan">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="bubble-label">Document Verification</span>
                        <span class="bubble-badge-indicator"></span>

                        <!-- Hover Popover -->
                        <div class="bubble-popover">
                            <div class="popover-header">
                                <span class="popover-title">Certificate &amp; KYC</span>
                                <span class="popover-badge popover-badge-cyan">&bull; Online KYC</span>
                            </div>
                            <p class="popover-text">Upload and verify educational certificates, 10th/12th memos, Transfer Certificate, Migration, and Aadhaar card.</p>
                        </div>
                    </div>

                    <!-- Bubble 3: Fee Payment & Receipts -->
                    <div class="feature-bubble">
                        <div class="bubble-icon bubble-emerald">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <span class="bubble-label">Fee &amp; Receipts</span>
                        <span class="bubble-badge-indicator"></span>

                        <!-- Hover Popover -->
                        <div class="bubble-popover">
                            <div class="popover-header">
                                <span class="popover-title">Fee Payment</span>
                                <span class="popover-badge popover-badge-emerald">&bull; Instant Receipt</span>
                            </div>
                            <p class="popover-text">View tuition fee breakdown, scholarship concessions, online installment options, and download official receipts.</p>
                        </div>
                    </div>

                    <!-- Bubble 4: Branch & Program Allocation -->
                    <div class="feature-bubble">
                        <div class="bubble-icon bubble-amber">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="bubble-label">Seat Allotment</span>
                        <span class="bubble-badge-indicator"></span>

                        <!-- Hover Popover -->
                        <div class="bubble-popover">
                            <div class="popover-header">
                                <span class="popover-title">Branch &amp; Seat Allotment</span>
                                <span class="popover-badge popover-badge-amber">&bull; Merit Order</span>
                            </div>
                            <p class="popover-text">Check allocated degree program, academic regulation (R25), campus induction dates, and elective specializations.</p>
                        </div>
                    </div>

                    <!-- Bubble 5: Provisional ID & Enrollment -->
                    <div class="feature-bubble">
                        <div class="bubble-icon bubble-purple">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                        </div>
                        <span class="bubble-label">Provisional ID</span>
                        <span class="bubble-badge-indicator"></span>

                        <!-- Hover Popover -->
                        <div class="bubble-popover">
                            <div class="popover-header">
                                <span class="popover-title">Provisional ID &amp; Order</span>
                                <span class="popover-badge popover-badge-purple">&bull; Official Letter</span>
                            </div>
                            <p class="popover-text">Download official admission allotment order, student registration number, and digital campus onboarding pass.</p>
                        </div>
                    </div>

                </div>
            </div>

        </section>

        <!-- Right Side: Clean White Login Card (Student Admission Access) -->
        <div class="login-card-wrapper">
            <div class="login-card">
                
                <!-- Card Header with Official Vignan Logo -->
                <div class="card-logo-area">
                    <div class="crest-icon-box">
                        <img src="/images/vignan-logo.png" alt="Vignan University Crest">
                    </div>
                    <div class="crest-subtext">&bull; VFSTR &bull; VIGNAN UNIVERSITY</div>
                    <h2 class="card-heading">Student Admission <span>Portal</span></h2>
                    <p class="card-subtitle">Enter your registered mobile number to check admission status &amp; proceed.</p>
                </div>

                <!-- Status & Error Messages -->
                @if(session('status'))
                    <div class="status-banner">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-banner">
                        @foreach($errors->all() as $error)
                            <div>&bull; {{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <!-- 2-Step Interactive Admission Login Form -->
                <form action="{{ route('login.post') }}" method="POST" id="login-form">
                    @csrf
                    <input type="hidden" name="login_type" value="otp">

                    <!-- STEP 1 PANE: ENTER REGISTERED MOBILE NUMBER -->
                    <div id="step-phone-pane" class="step-pane step-active">
                        <div class="form-group">
                            <label for="phone" class="form-label">Registered Mobile Number</label>
                            <div class="input-group">
                                <span class="input-prefix">+91</span>
                                <input type="tel" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ old('phone') }}" 
                                       required 
                                       maxlength="10"
                                       inputmode="numeric"
                                       pattern="[0-9]{10}"
                                       autocomplete="tel"
                                       placeholder="Enter 10-digit mobile number" 
                                       oninput="this.value = this.value.replace(/\D/g, '').slice(0, 10)"
                                       onpaste="setTimeout(() => { this.value = this.value.replace(/\D/g, '').slice(0, 10); }, 0)"
                                       onkeydown="if(event.key === 'Enter'){ event.preventDefault(); submitPhoneStep(); }"
                                       class="input-control input-with-prefix">
                            </div>
                            <p class="field-hint">Enter your 10-digit number. We'll send an OTP to verify your identity.</p>
                        </div>

                        <button type="button" id="btn-get-otp" class="btn-submit" onclick="submitPhoneStep()" style="margin-top: 1.25rem;">
                            <span id="btn-get-otp-text">Get OTP</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>
                    </div>

                    <!-- STEP 2 PANE: VERIFY OTP (Opens after mobile number is submitted!) -->
                    <div id="step-otp-pane" class="step-pane step-hidden">

                        <!-- Clean Status Confirmation (No mobile number shown) -->
                        <div id="otp-sent-status" class="status-banner" style="margin-bottom: 1.15rem; padding: 0.7rem 0.9rem; font-size: 0.8rem;">
                            <span id="otp-sent-message">✔ Verification OTP sent to your WhatsApp / SMS.</span>
                        </div>

                        <!-- OTP Field -->
                        <div class="form-group">
                            <div class="form-label" style="display:flex; justify-content:space-between; align-items:center;">
                                <span>One-Time Password (OTP)</span>
                                <button type="button" id="btn-resend-otp" class="btn-link" onclick="submitPhoneStep(true)" style="font-size:0.75rem; color:#2563eb; background:none; border:none; cursor:pointer; font-weight:700;">
                                    <span id="resend-text">Resend in 60s</span>
                                </button>
                            </div>
                            <div class="input-group">
                                <input type="text" 
                                       name="otp" 
                                       id="otp" 
                                       maxlength="6"
                                       inputmode="numeric"
                                       autocomplete="one-time-code"
                                       placeholder="Enter 6-digit OTP" 
                                       class="input-control otp-input-field"
                                       oninput="this.value = this.value.replace(/\D/g, '').slice(0, 6)"
                                       onkeydown="if(event.key === 'Enter'){ event.preventDefault(); document.getElementById('btn-submit-login').click(); }">
                            </div>
                            <p class="field-hint">Please enter the 6-digit verification code sent to your phone.</p>
                        </div>

                        <!-- Keep me signed in -->
                        <label class="remember-row">
                            <input type="checkbox" name="remember" checked>
                            <span>Keep me signed in</span>
                        </label>

                        <!-- Final Submit -->
                        <button type="submit" id="btn-submit-login" class="btn-submit" onclick="return validateOtpBeforeSubmit(event)">
                            <span id="submit-btn-text">Access Admission Portal</span>
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                            </svg>
                        </button>

                        <div style="margin-top: 1rem; text-align: center;">
                            <button type="button" onclick="goToStepPhone()" style="background: none; border: none; font-size: 0.78rem; color: #64748b; cursor: pointer; text-decoration: underline; font-family: inherit;">
                                &larr; Re-enter mobile number
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Clean Minimal Card Footer -->
                <div class="card-footer">
                    <div>&copy; 2026 Student Admission Portal</div>
                    <div class="card-footer-power">VFSTR Deemed to be University</div>
                </div>

            </div>
        </div>

    </main>

    <!-- Interactive Scripts for 2-Step Login -->
    <script>
        let countdownTimer = null;
        let resendSecondsRemaining = 0;

        const phoneInputEl = document.getElementById('phone');
        const otpInputEl = document.getElementById('otp');
        const phonePaneEl = document.getElementById('step-phone-pane');
        const otpPaneEl = document.getElementById('step-otp-pane');
        const btnGetOtp = document.getElementById('btn-get-otp');
        const btnGetOtpText = document.getElementById('btn-get-otp-text');
        const resendBtn = document.getElementById('btn-resend-otp');
        const resendText = document.getElementById('resend-text');

        // Strictly restrict inputs to numeric only
        phoneInputEl.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 10);
        });
        otpInputEl.addEventListener('input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });

        function submitPhoneStep(isResend = false) {
            const cleanPhone = phoneInputEl.value.replace(/\D/g, '').slice(0, 10);

            if (cleanPhone.length !== 10) {
                alert('Please enter a valid 10-digit registered mobile number.');
                phoneInputEl.focus();
                return;
            }

            if (isResend) {
                if (resendSecondsRemaining > 0) return;
                resendText.textContent = 'Sending...';
                resendBtn.disabled = true;
            } else {
                btnGetOtpText.textContent = 'Sending OTP...';
                btnGetOtp.disabled = true;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('{{ route("login.send-otp") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ phone: cleanPhone })
            })
            .then(async res => {
                const data = await res.json().catch(() => null);

                if (!res.ok) {
                    resetButtons();
                    if (res.status === 419) {
                        alert('Your session expired. Refreshing page...');
                        window.location.reload();
                        return;
                    }
                    const msg = (data && data.message) ? data.message : ('Error (' + res.status + '): Please check your mobile number.');
                    alert(msg);
                    return;
                }

                if (data && data.success) {
                    if (data.dev_otp) {
                        console.log('%c[DEV OTP CODE] ' + data.dev_otp, 'background: #2563eb; color: #fff; font-weight: bold; font-size: 13px; padding: 3px 6px; border-radius: 4px;');
                    }
                    const msgText = document.getElementById('otp-sent-message');
                    if (msgText) {
                        msgText.textContent = '✔ Verification OTP sent to your WhatsApp / SMS.';
                    }

                    // Transition to Step 2 (OTP Screen)
                    goToStepOtp();

                    // Start 60s countdown
                    startResendCountdown(60);
                } else {
                    resetButtons();
                    alert((data && data.message) || 'Could not send OTP. Please check your mobile number.');
                }
            })
            .catch(err => {
                console.error('Send OTP error:', err);
                resetButtons();
                alert('Connection error. Please check your server and refresh the page (F5).');
            });
        }

        function goToStepOtp() {
            phonePaneEl.classList.remove('step-active');
            phonePaneEl.classList.add('step-hidden');

            otpPaneEl.classList.remove('step-hidden');
            otpPaneEl.classList.add('step-active');

            resetButtons();
            otpInputEl.value = '';
            setTimeout(() => { otpInputEl.focus(); }, 150);
        }

        function goToStepPhone() {
            otpPaneEl.classList.remove('step-active');
            otpPaneEl.classList.add('step-hidden');

            phonePaneEl.classList.remove('step-hidden');
            phonePaneEl.classList.add('step-active');

            resetButtons();
            setTimeout(() => { phoneInputEl.focus(); }, 150);
        }

        function resetButtons() {
            btnGetOtpText.textContent = 'Get OTP';
            btnGetOtp.disabled = false;
        }

        function startResendCountdown(seconds) {
            clearInterval(countdownTimer);
            resendSecondsRemaining = seconds;
            resendBtn.disabled = true;

            countdownTimer = setInterval(() => {
                resendSecondsRemaining--;
                resendText.textContent = `Resend in ${resendSecondsRemaining}s`;

                if (resendSecondsRemaining <= 0) {
                    clearInterval(countdownTimer);
                    resendBtn.disabled = false;
                    resendText.textContent = 'Resend OTP';
                }
            }, 1000);
        }

        function validateOtpBeforeSubmit(e) {
            const cleanPhone = phoneInputEl.value.replace(/\D/g, '').slice(0, 10);
            const cleanOtp = otpInputEl.value.replace(/\D/g, '').slice(0, 6);

            if (cleanPhone.length !== 10) {
                e.preventDefault();
                alert('Please enter a valid 10-digit mobile number.');
                goToStepPhone();
                return false;
            }

            if (!cleanOtp || cleanOtp.length < 4) {
                e.preventDefault();
                alert('Please enter the 6-digit OTP sent to your mobile.');
                otpInputEl.focus();
                return false;
            }

            document.getElementById('submit-btn-text').textContent = 'Authenticating...';
            return true;
        }

        // If returned back with errors or old phone, auto-open OTP step
        @if($errors->any() || old('phone'))
            const prevPhone = '{{ old("phone") }}';
            if (prevPhone && prevPhone.length === 10) {
                goToStepOtp(prevPhone);
            }
        @endif
    </script>
</body>
</html>
