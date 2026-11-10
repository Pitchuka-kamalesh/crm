<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admission Onboarding Portal | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-shield-clean.png">

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
            position: relative;
        }

        /* Subtle brand watermark background layer */
        .watermark-bg {
            position: fixed;
            bottom: -60px;
            right: -60px;
            width: 520px;
            height: 520px;
            background-image: url('/images/vignan-shield-vector.svg');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.035;
            pointer-events: none;
            z-index: 0;
        }

        /* Top Nav */
        .dash-nav {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
        }

        .nav-inner {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0 1.25rem;
            height: 58px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-cluster {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            text-decoration: none;
        }

        .brand-logo-img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            transition: transform 0.2s ease;
        }

        .brand-cluster:hover .brand-logo-img {
            transform: scale(1.04);
        }

        .brand-title-wrap {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }

        .brand-name-main {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.015em;
        }

        .brand-accred-sub {
            font-size: 0.64rem;
            font-weight: 500;
            color: #64748b;
            letter-spacing: 0.03em;
            margin-top: 0.1rem;
        }

        .brand-tag {
            font-size: 0.62rem;
            font-weight: 600;
            padding: 0.1rem 0.45rem;
            border-radius: 6px;
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #dbeafe;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        /* Center Nav Links - Clean Understated Style */
        .dash-nav-links {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            list-style: none;
            background: transparent;
            padding: 0;
            border: none;
        }

        .dash-nav-link {
            font-size: 0.8rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            padding: 0.38rem 0.75rem;
            border-radius: 8px;
            transition: all 0.15s ease;
        }

        .dash-nav-link:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        .dash-nav-link.active {
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 700;
        }

        .user-nav-cluster {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .user-info-text {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 0.05rem;
        }

        .user-name-title {
            font-size: 0.82rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.2;
        }

        .user-meta-sub {
            font-size: 0.68rem;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 0.3rem;
        }

        .user-avatar-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #eff6ff;
            color: #1d4ed8;
            font-weight: 700;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #bfdbfe;
        }

        .btn-nav-action {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            font-size: 0.74rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
        }

        .btn-crm-link {
            background: #eff6ff;
            color: #2563eb;
            border: 1px solid #bfdbfe;
        }
        .btn-crm-link:hover {
            background: #dbeafe;
        }

        .btn-signout {
            background: transparent;
            color: #64748b;
            border: 1px solid transparent;
        }
        .btn-signout:hover {
            background: #fef2f2;
            color: #dc2626;
        }

        @media (max-width: 960px) {
            .dash-nav-links {
                display: none;
            }
        }

        /* Main Container */
        .dash-container {
            max-width: 1320px;
            margin: 0 auto;
            padding: 0.75rem 1.25rem 2rem;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 0.65rem;
            position: relative;
            z-index: 1;
        }

        /* Hero Banner (Minimalist Clean Info Card) */
        .welcome-hero {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1.25rem;
            color: #0f172a;
            position: relative;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.65rem;
        }

        .welcome-hero-left {
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }

        .welcome-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.15rem 0.55rem;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 9999px;
            font-size: 0.68rem;
            font-weight: 600;
            color: #2563eb;
        }

        .welcome-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #10b981;
            box-shadow: 0 0 4px #10b981;
        }

        .welcome-heading {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.015em;
            margin: 0;
            line-height: 1.25;
            color: #0f172a;
        }

        .welcome-heading-grad {
            color: #1e40af;
        }

        .welcome-subtitle {
            font-size: 0.78rem;
            color: #64748b;
            margin: 0;
            line-height: 1.35;
        }

        .meta-badges-cluster {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.45rem;
        }

        .meta-badge {
            font-size: 0.72rem;
            padding: 0.22rem 0.65rem;
            border-radius: 9999px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            white-space: nowrap;
            font-weight: 500;
        }

        .meta-badge strong {
            color: #0f172a;
            font-weight: 600;
        }

        .meta-badge-success {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
            font-weight: 600;
        }

        .meta-badge-warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
            font-weight: 600;
        }

        /* MINIMALIST BUBBLE STEPPER */
        .stepper-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.75rem 1.15rem 0.85rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            position: relative;
        }

        .stepper-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .stepper-title-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stepper-title-box h2 {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.01em;
        }

        .stepper-step-indicator {
            font-size: 0.72rem;
            font-weight: 600;
            color: #2563eb;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            padding: 0.12rem 0.5rem;
            border-radius: 9999px;
        }

        .stepper-progress-box {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .progress-bar-track {
            width: 100px;
            height: 6px;
            background: #f1f5f9;
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            background: #2563eb;
            border-radius: 9999px;
            transition: width 0.3s ease;
        }

        .progress-percent-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #64748b;
        }

        /* Sleek 5-Step Bubble Track */
        .stepper-nav-bar {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.5rem;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.65rem;
            overflow-x: auto;
        }

        .step-nav-btn {
            background: transparent;
            border: none;
            border-radius: 10px;
            padding: 0.4rem 0.2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            color: #64748b;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            min-width: 85px;
        }

        .step-nav-btn:hover {
            color: #0f172a;
        }

        .step-nav-btn.is-active {
            color: #1d4ed8;
        }

        .step-nav-btn.is-completed {
            color: #047857;
        }

        /* Bubble Node */
        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.78rem;
            font-weight: 700;
            margin-bottom: 0.35rem;
            background: #ffffff;
            color: #64748b;
            border: 1.5px solid #cbd5e1;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .step-nav-btn:hover .step-circle {
            transform: translateY(-2px);
            border-color: #93c5fd;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.12);
            color: #1e40af;
        }

        .step-nav-btn.is-active .step-circle {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2), 0 4px 10px rgba(29, 78, 216, 0.25);
            transform: translateY(-2px);
        }

        .step-nav-btn.is-completed .step-circle {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border-color: #10b981;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.25);
        }

        .step-nav-name {
            font-size: 0.72rem;
            font-weight: 600;
            line-height: 1.25;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .step-nav-btn.is-active .step-nav-name {
            font-weight: 700;
            color: #1d4ed8;
        }

        .step-nav-status-sub {
            font-size: 0.62rem;
            font-weight: 600;
            color: #2563eb;
            margin-top: 0.15rem;
        }

        .step-nav-status-sub.done {
            color: #10b981;
        }

        /* STEP CONTENT CONTAINER */
        .step-panel-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
            overflow: hidden;
        }

        .step-panel-header {
            padding: 0.85rem 1.4rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .step-panel-kicker {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 0.1rem;
        }

        .step-panel-header h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.01em;
            display: flex;
            align-items: center;
            gap: 0.45rem;
            margin: 0;
        }

        .step-panel-header p {
            font-size: 0.78rem;
            color: #64748b;
            margin: 0.15rem 0 0 0;
        }

        .step-header-tag {
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .step-header-tag.tag-pending {
            background: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
        }

        .step-header-tag.tag-success {
            background: #dcfce7;
            color: #166534;
            border-color: #bbf7d0;
        }

        .step-panel-body {
            padding: 1.15rem 1.35rem;
            display: flex;
            flex-direction: column;
            gap: 1.15rem;
        }

        /* Form Controls - Minimalist Refined */
        .form-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.85rem 1rem;
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.85rem 1rem;
        }

        .form-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem 1rem;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .field-label {
            font-size: 0.78rem;
            font-weight: 600;
            color: #475569;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .required-star {
            color: #ef4444;
        }

        .field-input, .field-select, .field-textarea {
            width: 100%;
            padding: 0.55rem 0.85rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.84rem;
            font-weight: 500;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            transition: all 0.15s ease;
        }

        .field-input:hover, .field-select:hover, .field-textarea:hover {
            border-color: #94a3b8;
        }

        .field-input:focus, .field-select:focus, .field-textarea:focus {
            outline: none;
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .field-input[readonly], .field-input:disabled {
            background: #f8fafc;
            color: #64748b;
            cursor: not-allowed;
            border-color: #e2e8f0;
            box-shadow: none;
        }

        .section-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 1.1rem 1.3rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .section-box-title {
            font-size: 0.88rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Buttons & Footer Actions */
        .step-footer-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
            margin-top: 0.25rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .btn-step-prev {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1.15rem;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-step-prev:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #94a3b8;
        }

        .btn-step-next {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.55rem 1.35rem;
            border-radius: 8px;
            font-size: 0.84rem;
            font-weight: 600;
            background: #2563eb;
            color: #ffffff;
            border: none;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.2);
            transition: all 0.15s ease;
            text-decoration: none;
        }

        .btn-step-next:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.25);
            color: #ffffff;
        }

        /* Document KYC Cards */
        .kyc-docs-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }

        .kyc-doc-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.1rem 1rem;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.75rem;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
        }

        .kyc-doc-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }

        .download-doc-card {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 1.15rem 1rem;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            gap: 0.85rem;
            transition: all 0.15s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
            min-height: 155px;
            box-sizing: border-box;
        }

        .download-doc-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.04);
            transform: translateY(-1px);
        }

        .doc-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.74rem;
            font-weight: 500;
            color: #94a3b8;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 0.45rem 0.75rem;
            border-radius: 6px;
            width: 100%;
            min-height: 34px;
            box-sizing: border-box;
            text-align: center;
        }

        .btn-doc-download {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            font-size: 0.76rem;
            font-weight: 600;
            color: #2563eb;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            padding: 0.45rem 0.75rem;
            border-radius: 6px;
            text-decoration: none;
            width: 100%;
            min-height: 34px;
            box-sizing: border-box;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .btn-doc-download:hover {
            background: #dbeafe;
            color: #1d4ed8;
            border-color: #93c5fd;
        }

        .kyc-doc-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
        }

        .kyc-doc-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
        }

        .kyc-doc-title {
            font-size: 0.92rem;
            font-weight: 800;
            color: #0f172a;
            margin-top: 0.25rem;
        }

        .kyc-doc-sub {
            font-size: 0.76rem;
            color: #64748b;
            margin-top: 0.15rem;
        }

        .kyc-badge {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.55rem;
            border-radius: 9999px;
            text-transform: uppercase;
        }
        .kyc-badge-verified { background: #dcfce7; color: #166534; }
        .kyc-badge-pending { background: #fef3c7; color: #92400e; }

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

        /* Digital ID Card Preview */
        .id-card-preview {
            max-width: 480px;
            margin: 0 auto;
            border-radius: 16px;
            background: linear-gradient(135deg, #1e3a8a 0%, #1e293b 100%);
            color: #ffffff;
            box-shadow: 0 12px 30px rgba(30, 58, 138, 0.25);
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            overflow: hidden;
        }

        .id-card-preview::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.25) 0%, transparent 70%);
            border-radius: 50%;
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
            position: relative;
            z-index: 1;
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
            .welcome-hero {
                padding: 1rem 1.25rem;
            }
            .welcome-heading {
                font-size: 1.25rem;
            }
            .nav-inner {
                padding: 0 1rem;
            }
            .dash-container {
                padding: 1rem;
            }
        }

        /* ================= EXACT PERSONAL APPLICATION & MOCKUP STYLING ================= */
        .personal-app-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.35rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .personal-app-title {
            font-size: 1.85rem;
            font-weight: 800;
            color: #0f2744;
            margin: 0;
            letter-spacing: -0.02em;
        }

        .btn-pdf-download {
            background: #0f2744;
            color: #ffffff;
            padding: 0.6rem 1.25rem;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: 0 2px 6px rgba(15, 39, 68, 0.25);
            transition: all 0.2s ease;
            cursor: pointer;
            border: none;
        }

        .btn-pdf-download:hover {
            background: #1e3a8a;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15, 39, 68, 0.35);
        }

        .degree-pill-btn {
            background: #ffffff;
            color: #334155;
            border: 1.5px solid #cbd5e1;
            border-radius: 20px;
            padding: 0.4rem 0.95rem;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: inherit;
        }

        .degree-pill-btn:hover {
            border-color: #2563eb;
            color: #1e40af;
            background: #eff6ff;
        }

        .degree-pill-btn.active {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }

        .mockup-cards-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            align-items: start;
        }

        @media (max-width: 992px) {
            .degree-pill-btn {
            background: #ffffff;
            color: #334155;
            border: 1.5px solid #cbd5e1;
            border-radius: 20px;
            padding: 0.4rem 0.95rem;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-family: inherit;
        }

        .degree-pill-btn:hover {
            border-color: #2563eb;
            color: #1e40af;
            background: #eff6ff;
        }

        .degree-pill-btn.active {
            background: #2563eb;
            color: #ffffff;
            border-color: #2563eb;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.25);
        }

        .mockup-cards-grid {
                grid-template-columns: 1fr;
            }
        }

        .mockup-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 1.5rem 1.65rem;
            box-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            border: 1px solid #e2e8f0;
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .mockup-card.card-active-step {
            border-color: #3b82f6;
            box-shadow: 0 6px 24px rgba(59, 130, 246, 0.12);
        }

        .mockup-card-title {
            font-size: 1.12rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 0.5rem 0;
            line-height: 1.35;
        }

        .mockup-field-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem 1rem;
        }

        .mockup-field-row-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.85rem 1.15rem;
        }

        @media (max-width: 900px) {
            .mockup-field-row-3 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .mockup-field-row-2 {
                grid-template-columns: 1fr;
            }
        }

        .mockup-field-group {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .mockup-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #0f172a;
        }

        .mockup-input, .mockup-select {
            width: 100%;
            background: #eef3f7;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            padding: 0.6rem 0.85rem;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1e293b;
            font-family: inherit;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }

        .mockup-input:focus, .mockup-select:focus {
            outline: none;
            border-color: #0f2744;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(15, 39, 68, 0.1);
        }

        .mockup-subbox {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 14px;
            padding: 1.15rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .mockup-subbox-title {
            font-size: 0.95rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0 0 0.35rem 0;
        }

        .mockup-radio-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.45rem 1rem;
            margin: 0.25rem 0 0.5rem;
        }

        .mockup-radio-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.65rem 1.25rem;
            margin: 0.35rem 0 0.5rem;
        }

        .mockup-radio-label {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.82rem;
            font-weight: 500;
            color: #1e293b;
            cursor: pointer;
        }

        .mockup-radio-label input[type="radio"] {
            accent-color: #0f2744;
            width: 15px;
            height: 15px;
            cursor: pointer;
            margin: 0;
        }

        .mockup-pill-badge {
            background: #475569;
            color: #ffffff;
            border-radius: 20px;
            padding: 0.3rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
            align-self: flex-start;
        }

        .mockup-file-wrap {
            display: flex;
            align-items: center;
            background: #eef3f7;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
        }

        .mockup-file-text {
            flex: 1;
            padding: 0.55rem 0.85rem;
            font-size: 0.82rem;
            color: #64748b;
        }

        .mockup-browse-btn {
            background: #3f7b82;
            color: #ffffff;
            padding: 0.6rem 1.2rem;
            font-size: 0.84rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: background 0.15s ease;
            text-align: center;
        }

        .mockup-browse-btn:hover {
            background: #2e5f65;
        }
    </style>
</head>
<body>

    <!-- Subtle Watermark Background Layer -->
    <div class="watermark-bg" aria-hidden="true"></div>

    <!-- TOP NAVIGATION -->
    <nav class="dash-nav">
        <div class="nav-inner">
            <a href="{{ route('dashboards.index') }}" class="brand-cluster">
                <img src="/images/vignan-shield-clean.png" alt="Vignan's University" class="brand-logo-img">
                <div class="brand-title-wrap">
                    <div style="display:flex; align-items:center; gap:0.45rem;">
                        <span class="brand-name-main">VIGNAN'S</span>
                        <span class="brand-tag">Student Portal</span>
                    </div>
                    <span class="brand-accred-sub">DEEMED TO BE UNIVERSITY &bull; NAAC A+</span>
                </div>
            </a>


            <div class="user-nav-cluster">
                <div class="user-info-text">
                    <span class="user-name-title">{{ $displayName }}</span>
                    @if($appNo)
                        <span class="user-meta-sub">ID: {{ $appNo }}</span>
                    @endif
                </div>

                <div class="user-avatar-circle" title="{{ $displayName }}">
                    {{ strtoupper(substr($displayName, 0, 1)) }}
                </div>

                @if($user && ($user->user_type == 2 || in_array((int)$user->role_id, [1, 2, 3, 4])))
                    <a href="{{ route('crm.index') }}" class="btn-nav-action btn-crm-link" title="CRM Executive Portal">
                        <span>👑 CRM</span>
                    </a>
                @endif

                <a href="{{ route('logout') }}" class="btn-nav-action btn-signout" title="Sign Out of Portal">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Sign Out</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTAINER -->
    <main class="dash-container">

        <!-- WELCOME HERO BANNER (MINIMALIST INFO BAR) -->
        <section class="welcome-hero">
            <div class="welcome-hero-left">
                <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                    <h1 class="welcome-heading">
                        Welcome, <span class="welcome-heading-grad">{{ $displayName }}</span>!
                    </h1>
                    <span class="welcome-pill">
                        <span class="welcome-dot"></span>
                        <span>Academic Year 2026–27</span>
                    </span>
                </div>
                <p class="welcome-subtitle">
                    {{ $programName ?: 'B.Tech - Bachelor of Technology' }} @if($branchName)({{ $branchName }})@endif &bull; {{ $campusName ?: 'Vadlamudi Campus' }}
                </p>
            </div>

            <div class="meta-badges-cluster">
                @if($appNo)
                <span class="meta-badge">
                    <strong>App ID:</strong> #{{ $appNo }}
                </span>
                @endif
                <span class="meta-badge {{ ($crmStudent?->stage1 === 'paid') ? 'meta-badge-success' : 'meta-badge-warning' }}">
                    &bull; {{ ($crmStudent?->stage1 === 'paid') ? 'Seat Confirmed' : 'Admission Fee Pending' }}
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


        <!-- STEP PROGRESS INDICATION TRACKER -->
        <section class="stepper-card">
            <div class="stepper-header">
                <div class="stepper-title-box">
                    <h2>Admission Onboarding</h2>
                    <span class="stepper-step-indicator">Step {{ $activeStep }} of 5</span>
                </div>
                <div class="stepper-progress-box">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                    <span class="progress-percent-label">{{ $progressPercent }}% Complete</span>
                </div>
            </div>

            <!-- 8 Stages Indication Bar -->
            <div class="stepper-nav-bar">
                @foreach($stages as $num => $stg)
                    @php
                        $isActive = ($activeStep === $num);
                        $isDone = ($stg['status'] === 'completed');
                    @endphp
                    <a href="{{ route('dashboards.index', ['step' => $num]) }}"
                       class="step-nav-btn {{ $isActive ? 'is-active' : '' }} {{ $isDone ? 'is-completed' : '' }}"
                       title="Step {{ $num }}: {{ $stg['title'] }}">
                        <div class="step-circle">
                            @if($isDone)
                                ✓
                            @else
                                {{ $num }}
                            @endif
                        </div>
                        <span class="step-nav-name">{{ $stg['title'] }}</span>
                        @if($isActive)
                            <span class="step-nav-status-sub">In Progress</span>
                        @elseif($isDone)
                            <span class="step-nav-status-sub done">Completed</span>
                        @endif
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
                        <div class="step-panel-kicker">Stage 1 of 5 &bull; Seat Confirmation</div>
                        <h3>
                            <span>Seat Confirmation &amp; Admission Fee</span>
                        </h3>
                    </div>
                    @if($crmStudent?->stage1 === 'paid')
                        <span class="step-header-tag tag-success">✓ Fee Paid</span>
                    @else
                        <span class="step-header-tag tag-pending">Payment Pending</span>
                    @endif
                </div>

                <div class="step-panel-body">
                    <!-- FEE PAYMENT STATUS & ACTION -->
                    <div class="section-box" style="text-align: center; padding: 1rem 1.25rem;">
                        @if($crmStudent?->stage1 === 'paid')
                            <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">✅</div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 0.2rem;">Admission Fee Confirmed</h3>
                            <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.75rem;">Your seat confirmation fee is verified in the database. Download your official documents below.</p>
                            <span class="step-header-tag tag-success" style="display: inline-block;">Status: Completed ✓</span>
                        @else
                            @php
                                $studentAppliNo = $crmStudent?->applinano ?? $user?->usercode ?? '20261106333';
                                $feeBreakdown = \App\Services\CouponService::calculateFeeBreakdown($studentAppliNo, \App\Services\CouponService::DEFAULT_ADMISSION_FEE);
                            @endphp
                            <div style="margin-bottom: 0.65rem; text-align: left;">
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 0;">Admission Registration Fee Payment</h4>
                                <p style="font-size: 0.76rem; color: #64748b; margin: 0.1rem 0 0 0;">Pay securely via the official PayU gateway to lock your provisional registration.</p>
                            </div>

                            <!-- PayU Fee Summary Box with Concessions -->
                            <div style="max-width: 480px; margin: 0 auto 0.75rem auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 1rem; text-align: left;">
                                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #475569; margin-bottom: 0.3rem;">
                                    <span>Base Admission Fee:</span>
                                    <strong style="color: #0f172a;">₹ {{ number_format($feeBreakdown['base_fee'], 2) }}</strong>
                                </div>

                                @if($feeBreakdown['def_discount'] > 0)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: #059669; margin-bottom: 0.25rem;">
                                        <span>Early Bird Concession ({{ $feeBreakdown['def_coupon'] }}):</span>
                                        <strong style="color: #059669;">- ₹ {{ number_format($feeBreakdown['def_discount'], 2) }}</strong>
                                    </div>
                                @endif

                                @php
                                    $defaultAllocated = \App\Services\CouponService::getPersonalCouponForStudent($studentAppliNo);
                                    $hasPersonalCoupon = $feeBreakdown['per_discount'] > 0;
                                    $suggestedPersonalCode = $defaultAllocated?->couponsno ?? \App\Services\CouponService::formatPersonalCouponCode($studentAppliNo);
                                @endphp

                                <!-- Personal Coupon Row & Button -->
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; margin: 5px 0; border-top: 1px dashed #e2e8f0; border-bottom: 1px dashed #e2e8f0;">
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 5px;">
                                            <span style="font-size: 0.8rem; font-weight: 700; color: #1e293b;">🎟️ Personal Coupon</span>
                                            @if($defaultAllocated && $defaultAllocated->coupstatus === 'ACTIVE' && !$hasPersonalCoupon)
                                                <span style="font-size: 0.68rem; color: #059669; font-weight: 700; background: #ecfdf5; padding: 1px 5px; border-radius: 4px; border: 1px solid #a7f3d0;">
                                                    Available
                                                </span>
                                            @endif
                                        </div>
                                        <div style="font-size: 0.7rem; color: #64748b;" id="personalCouponSubtext">
                                            @if($hasPersonalCoupon)
                                                Applied: <code>{{ $feeBreakdown['per_coupon'] }}</code>
                                            @elseif($defaultAllocated && $defaultAllocated->coupstatus === 'ACTIVE')
                                                Approved code: <code>{{ $suggestedPersonalCode }}</code>
                                            @else
                                                Special counselor concession
                                            @endif
                                        </div>
                                    </div>

                                    <div id="appliedPersonalCouponCol" style="{{ $hasPersonalCoupon ? 'display: flex;' : 'display: none;' }} align-items: center; gap: 6px;">
                                        <strong style="color: #059669; font-size: 0.82rem;">- ₹ <span id="dispPersonalDiscountVal">{{ number_format($feeBreakdown['per_discount'], 2) }}</span></strong>
                                        <button type="button" onclick="handleRemovePersonalCoupon()" style="background: #fee2e2; border: 1px solid #fecaca; color: #dc2626; border-radius: 4px; padding: 2px 7px; font-size: 0.68rem; font-weight: 700; cursor: pointer;" title="Remove Personal Coupon">✕ Remove</button>
                                    </div>

                                    <div id="btnPromptCol" style="{{ $hasPersonalCoupon ? 'display: none;' : 'display: block;' }}">
                                        <button type="button" 
                                                id="btnShowCouponInput"
                                                onclick="togglePersonalCouponBox()" 
                                                style="background: #2563eb; color: #ffffff; border: none; border-radius: 6px; padding: 4px 10px; font-size: 0.75rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 1px 3px rgba(37, 99, 235, 0.25);">
                                            <span>+</span> Apply Coupon
                                        </button>
                                    </div>
                                </div>

                                <!-- Personal Coupon Input & Apply Box -->
                                <div id="couponEntryBox" style="{{ (!$hasPersonalCoupon && $defaultAllocated && $defaultAllocated->coupstatus === 'ACTIVE') ? 'display: block;' : 'display: none;' }} margin: 0.4rem 0 0.6rem 0; padding: 0.5rem; background: #f8fafc; border: 1px dashed #93c5fd; border-radius: 8px;">
                                    <div style="font-size: 0.72rem; color: #1e40af; font-weight: 600; margin-bottom: 4px;">
                                        Enter Personal Coupon Code:
                                    </div>
                                    <div style="display: flex; gap: 6px; align-items: center;">
                                        <input type="text" 
                                               id="personalCouponInput" 
                                               placeholder="e.g. {{ $suggestedPersonalCode }}" 
                                               value="{{ $suggestedPersonalCode }}" 
                                               style="flex: 1; height: 32px; padding: 0 10px; border: 1.5px solid #93c5fd; border-radius: 6px; font-size: 0.82rem; font-family: 'JetBrains Mono', monospace; text-transform: uppercase;">
                                        <button type="button" 
                                                id="btnSubmitPersonalCoupon"
                                                onclick="submitPersonalCouponAjax('{{ $studentAppliNo }}')" 
                                                style="height: 32px; background: #4f46e5; color: #ffffff; border: none; padding: 0 14px; border-radius: 6px; font-size: 0.78rem; font-weight: 700; cursor: pointer;">
                                            Apply
                                        </button>
                                        <button type="button" 
                                                onclick="togglePersonalCouponBox()" 
                                                style="height: 32px; background: #e2e8f0; color: #475569; border: none; padding: 0 8px; border-radius: 6px; font-size: 0.74rem; cursor: pointer;" title="Close">
                                            ✕
                                        </button>
                                    </div>
                                    <div id="couponAjaxNotice" style="font-size: 0.72rem; margin-top: 4px; display: none;"></div>
                                </div>

                                <div style="display: flex; justify-content: space-between; font-size: 0.76rem; color: #047857; margin-bottom: 0.3rem; background: #ecfdf5; padding: 3px 6px; border-radius: 6px;">
                                    <span style="font-weight: 600;">Total Concession Deducted:</span>
                                    <strong style="color: #047857;">- ₹ <span id="dispTotalConcessionVal">{{ number_format($feeBreakdown['total_discount'], 2) }}</span></strong>
                                </div>

                                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: #64748b; margin-bottom: 0.25rem;">
                                    <span>Payment Gateway:</span>
                                    <span style="font-weight: 600; color: #059669;">PayU Secure Hosted</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; color: #64748b; padding-top: 0.35rem; border-top: 1px dashed #cbd5e1; align-items: center;">
                                    <span style="font-weight: 700; font-size: 0.86rem; color: #0f172a;">Total Payable:</span>
                                    <span style="font-size: 1.15rem; font-weight: 800; color: #059669;">₹ <span id="dispTotalPayableVal">{{ number_format($feeBreakdown['net_payable'], 2) }}</span></span>
                                </div>
                            </div>

                            <form id="payuSubmitForm" action="{{ route('payu.initiate') }}" method="POST" style="display: inline-block;">
                                @csrf
                                <input type="hidden" id="payuAmountInput" name="amount" value="{{ $feeBreakdown['net_payable'] }}">
                                <button type="submit" id="btnPayUGateway" class="btn-step-next" style="display: inline-flex; align-items: center; gap: 0.45rem; margin: 0 auto; background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 8px 22px; font-size: 0.86rem; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);">
                                    <span>💳 Pay ₹<span id="btnPayAmountText">{{ number_format($feeBreakdown['net_payable']) }}</span> via PayU Gateway</span>
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>

                            <script>
                                function togglePersonalCouponBox() {
                                    const box = document.getElementById('couponEntryBox');
                                    if (box) {
                                        box.style.display = (box.style.display === 'none' || !box.style.display) ? 'block' : 'none';
                                    }
                                }

                                function submitPersonalCouponAjax(applino) {
                                    const input = document.getElementById('personalCouponInput');
                                    const notice = document.getElementById('couponAjaxNotice');
                                    const btn = document.getElementById('btnSubmitPersonalCoupon');
                                    const code = (input ? input.value : '').trim();

                                    if (!code) {
                                        if (notice) {
                                            notice.style.display = 'block';
                                            notice.style.color = '#dc2626';
                                            notice.textContent = 'Please enter a personal coupon code.';
                                        }
                                        return;
                                    }

                                    if (btn) btn.disabled = true;
                                    if (notice) {
                                        notice.style.display = 'block';
                                        notice.style.color = '#2563eb';
                                        notice.textContent = 'Verifying personal coupon and remaining balance...';
                                    }

                                    fetch('{{ route("dashboards.apply-coupon") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ coupon_code: code, applino: applino })
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        if (btn) btn.disabled = false;
                                        if (data.success) {
                                            if (notice) {
                                                notice.style.color = '#059669';
                                                notice.textContent = '✓ ' + data.message;
                                            }
                                            const appliedCol = document.getElementById('appliedPersonalCouponCol');
                                            const btnCol = document.getElementById('btnPromptCol');
                                            const entryBox = document.getElementById('couponEntryBox');
                                            const subtext = document.getElementById('personalCouponSubtext');
                                            const valSpan = document.getElementById('dispPersonalDiscountVal');
                                            const totConcessionSpan = document.getElementById('dispTotalConcessionVal');
                                            const totPayableSpan = document.getElementById('dispTotalPayableVal');
                                            const payInput = document.getElementById('payuAmountInput');
                                            const btnPayText = document.getElementById('btnPayAmountText');

                                            if (valSpan) valSpan.textContent = Number(data.discount).toFixed(2);
                                            if (subtext) subtext.innerHTML = `Applied: <code>${data.code}</code>`;
                                            if (appliedCol) appliedCol.style.display = 'flex';
                                            if (btnCol) btnCol.style.display = 'none';
                                            if (entryBox) entryBox.style.display = 'none';

                                            if (totConcessionSpan) totConcessionSpan.textContent = Number(data.total_discount).toFixed(2);
                                            if (totPayableSpan) totPayableSpan.textContent = Number(data.net_payable).toFixed(2);
                                            if (payInput) payInput.value = data.net_payable;
                                            if (btnPayText) btnPayText.textContent = Math.round(data.net_payable);
                                        } else {
                                            if (notice) {
                                                notice.style.color = '#dc2626';
                                                notice.textContent = '✕ ' + data.message;
                                            }
                                        }
                                    })
                                    .catch(err => {
                                        if (btn) btn.disabled = false;
                                        if (notice) {
                                            notice.style.color = '#dc2626';
                                            notice.textContent = 'Network error while applying coupon. Please try again.';
                                        }
                                    });
                                }

                                function handleRemovePersonalCoupon() {
                                    fetch('{{ route("dashboards.remove-coupon") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        }
                                    })
                                    .then(r => r.json())
                                    .then(data => {
                                        location.reload();
                                    })
                                    .catch(e => location.reload());
                                }
                            </script>
                        @endif
                    </div>

                    <!-- DOWNLOAD CARDS -->
                    <div class="section-box" style="padding: 1rem 1.25rem;">
                        <div class="section-box-title" style="margin-bottom: 0.75rem;">
                            <span>Admission Documents</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="download-doc-card">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.2rem;">
                                    <div style="font-size: 1.5rem; margin-bottom: 0.15rem;">📜</div>
                                    <h4 class="kyc-doc-title" style="font-size: 0.86rem; margin: 0;">Provisional Allotment Order</h4>
                                    <p class="kyc-doc-sub" style="font-size: 0.74rem; margin: 0.15rem 0 0 0;">Dean Allotment Letter</p>
                                </div>
                                @if($crmStudent?->stage1 === 'paid')
                                    <a href="javascript:alert('Downloading Provisional Allotment Order (PDF)...')" class="btn-doc-download">
                                        <span>Download PDF ↓</span>
                                    </a>
                                @else
                                    <span class="doc-status-pill">Unlocks after fee payment</span>
                                @endif
                            </div>

                            <div class="download-doc-card">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.2rem;">
                                    <div style="font-size: 1.5rem; margin-bottom: 0.15rem;">🧾</div>
                                    <h4 class="kyc-doc-title" style="font-size: 0.86rem; margin: 0;">Official Fee Payment Receipt</h4>
                                    <p class="kyc-doc-sub" style="font-size: 0.74rem; margin: 0.15rem 0 0 0;">Accounts Verified Receipt</p>
                                </div>
                                @if($crmStudent?->stage1 === 'paid')
                                    <a href="javascript:alert('Downloading Official Fee Payment Receipt (PDF)...')" class="btn-doc-download">
                                        <span>Download Receipt ↓</span>
                                    </a>
                                @else
                                    <span class="doc-status-pill">Unlocks after fee payment</span>
                                @endif
                            </div>

                            <div class="download-doc-card">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.2rem;">
                                    <div style="font-size: 1.5rem; margin-bottom: 0.15rem;">📅</div>
                                    <h4 class="kyc-doc-title" style="font-size: 0.86rem; margin: 0;">Campus Induction Schedule</h4>
                                    <p class="kyc-doc-sub" style="font-size: 0.74rem; margin: 0.15rem 0 0 0;">Orientation &amp; Move-in Guide</p>
                                </div>
                                <a href="javascript:alert('Downloading Orientation & Campus Induction Schedule (PDF)...')" class="btn-doc-download">
                                    <span>Download Guide ↓</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions" style="padding-top: 1rem; margin-top: 0.25rem;">
                        <div style="display: flex; align-items: center; gap: 0.45rem;">
                            <span style="font-size: 0.78rem; color: #64748b; font-weight: 500;">Status:</span>
                            @if($crmStudent?->stage1 === 'paid')
                                <span class="step-header-tag tag-success">✓ Fee Paid</span>
                            @else
                                <span class="step-header-tag tag-pending">Payment Pending</span>
                            @endif
                        </div>
                        <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-step-prev" style="text-decoration: none;">
                            <span>Step 2: {{ $stages[2]['title'] }} &rarr;</span>
                        </a>
                    </div>
                </div>

            <!-- ================= STAGE 2: PERSONAL INFO (SEPARATE STEP 2) ================= -->
            @elseif($activeStep === 2)
                <div style="padding: 1.5rem 2rem; width: 100%; box-sizing: border-box;">
                    <!-- CARD 1: 2. PERSONAL INFO (FULL WIDTH) -->
                    <div class="mockup-card card-active-step" style="width: 100%; box-sizing: border-box; border-radius: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.25rem;">
                            <h3 class="mockup-card-title">2. personal info : Fathers Name</h3>
                            @if($stages[2]['status'] === 'completed')
                                <span class="step-header-tag tag-success" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">✓ Completed</span>
                            @else
                                <span class="step-header-tag tag-pending" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">In Progress</span>
                            @endif
                        </div>

                        <form action="{{ route('dashboards.save-step') }}" method="POST">
                            @csrf
                            <input type="hidden" name="step" value="2">

                            <!-- Row 1: Father Name, Phone No, DOB (3 Columns) -->
                            <div class="mockup-field-row-3">
                                <div class="mockup-field-group">
                                    <label class="mockup-label">father name: <span style="color:#ef4444;">*</span></label>
                                    <input type="text" name="father_name" class="mockup-input" value="{{ $personalInfo?->parent_name ?: '' }}" placeholder="Enter Father's Name" required>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Phone no: <span style="color:#ef4444;">*</span></label>
                                    <input type="tel" name="phone_no" class="mockup-input" value="{{ $personalInfo?->parentphono ?: ($crmStudent?->phoneno ?: $user?->phnno) }}" placeholder="Enter 10-digit Phone Number" maxlength="10" pattern="[0-9]{10}" required>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">DOB: <span style="color:#ef4444;">*</span></label>
                                    <input type="date" name="dob" class="mockup-input" value="{{ $personalInfo?->dob ? date('Y-m-d', strtotime($personalInfo->dob)) : '2008-05-14' }}" required>
                                </div>
                            </div>

                            <!-- Row 2: Gender, Category, Referee By (3 Columns) -->
                            <div class="mockup-field-row-3" style="margin-top: 1rem; align-items: start;">
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Gender: <span style="color:#ef4444;">*</span></label>
                                    <select name="gender" class="mockup-select" required>
                                        <option value="Male" {{ ($personalInfo?->gender === 'Male' || !$personalInfo) ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ ($personalInfo?->gender === 'Female') ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ ($personalInfo?->gender === 'Other') ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">category: <span style="color:#ef4444;">*</span></label>
                                    <div class="mockup-radio-row" style="margin-top: 0.5rem;">
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="category" value="OC" {{ ($personalInfo?->caste_category === 'OC' || !$personalInfo) ? 'checked' : '' }}>
                                            <span>OC</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="category" value="BC" {{ ($personalInfo?->caste_category === 'BC' || $personalInfo?->caste_category === 'OBC') ? 'checked' : '' }}>
                                            <span>BC</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="category" value="SC" {{ ($personalInfo?->caste_category === 'SC') ? 'checked' : '' }}>
                                            <span>SC</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="category" value="ST" {{ ($personalInfo?->caste_category === 'ST') ? 'checked' : '' }}>
                                            <span>ST</span>
                                        </label>
                                    </div>
                                </div>
                                @php
                                    $savedRefs = array_map('trim', explode(',', $personalInfo?->referedby ?? 'via advertisement'));
                                @endphp
                                <div class="mockup-field-group">
                                    <label class="mockup-label">referee by:</label>
                                    <div class="mockup-radio-grid" style="margin-top: 0.35rem;">
                                        <label class="mockup-radio-label">
                                            <input type="checkbox" name="referee_by[]" value="via advertisement" {{ in_array('via advertisement', $savedRefs) ? 'checked' : '' }}>
                                            <span>via advertisement</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="checkbox" name="referee_by[]" value="via @by own" {{ in_array('via @by own', $savedRefs) ? 'checked' : '' }}>
                                            <span>via @by own</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="checkbox" name="referee_by[]" value="via Emp vtstr" {{ in_array('via Emp vtstr', $savedRefs) ? 'checked' : '' }} onchange="toggleEmpCode()">
                                            <span>via Emp vtstr</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="checkbox" name="referee_by[]" value="via Conseller / Telecaller" {{ in_array('via Conseller / Telecaller', $savedRefs) ? 'checked' : '' }} onchange="toggleEmpCode()">
                                            <span>via Conseller / Telecaller</span>
                                        </label>
                                    </div>
                                    <div id="ref-empcode-wrap" style="display: {{ (in_array('via Emp vtstr', $savedRefs) || in_array('via Conseller / Telecaller', $savedRefs)) ? 'block' : 'none' }}; margin-top: 0.45rem;">
                                        <input type="text" name="referee_empcode" class="mockup-input" value="{{ $personalInfo?->referedempcode }}" placeholder="Enter Employee / Counselor Code (optional)">
                                    </div>
                                </div>
                            </div>

                            <!-- Address Box populated from crm DB tables (3 Columns) -->
                            <div class="mockup-subbox" style="margin-top: 1.25rem;">
                                <h4 class="mockup-subbox-title">Address</h4>
                                <div class="mockup-field-row-3">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">street/country: <span style="color:#ef4444;">*</span></label>
                                        <select name="street_country" id="geo-country-select" class="mockup-select" onchange="onCountryChange(this.value)" required>
                                            @foreach($countries as $c)
                                                <option value="{{ $c->country_id }}" data-name="{{ $c->country_name }}" {{ ($personalInfo?->country == $c->country_name || (!$personalInfo && $c->country_code === 'IN')) ? 'selected' : '' }}>
                                                    {{ $c->country_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">state: <span style="color:#ef4444;">*</span></label>
                                        <select name="state" id="geo-state-select" class="mockup-select" onchange="onStateChange(this.value)" required>
                                            <option value="">-- Select State --</option>
                                            @foreach($states as $s)
                                                <option value="{{ $s->state_id }}" data-country="{{ $s->country_id }}" data-name="{{ $s->state_name }}" {{ ($personalInfo?->state == $s->state_name || (!$personalInfo && $s->state_name === 'Andhra Pradesh')) ? 'selected' : '' }}>
                                                    {{ $s->state_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">distric t: <span style="color:#ef4444;">*</span></label>
                                        <select name="distric_t" id="geo-district-select" class="mockup-select" required>
                                            <option value="">-- Select District --</option>
                                            @foreach($districts as $d)
                                                <option value="{{ $d->DISTRICT_ID }}" data-state="{{ $d->STATE_ID }}" data-name="{{ $d->DISTRICT_NAME }}" {{ ($personalInfo?->district == $d->DISTRICT_NAME || (!$personalInfo && $d->DISTRICT_NAME === 'Guntur')) ? 'selected' : '' }}>
                                                    {{ $d->DISTRICT_NAME }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">city / mandal: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="city_mandal" class="mockup-input" value="{{ $personalInfo?->city_mandal ?: 'Guntur' }}" placeholder="City / Mandal" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">village : <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="village" class="mockup-input" value="{{ $personalInfo?->village ?: 'Flat 402, Sri Sai Nilayam, Brodipet' }}" placeholder="Village / Street Address" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">pin code: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pin_code" class="mockup-input" value="{{ $personalInfo?->pincode ?: '522002' }}" placeholder="6-digit Pin Code" maxlength="6" pattern="[0-9]{6}" required>
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; justify-content: space-between; align-items: center;">
                                <a href="{{ route('dashboards.index', ['step' => 1]) }}" class="btn-step-prev" style="text-decoration: none;">
                                    &larr; Stage 1: {{ $stages[1]['title'] }}
                                </a>
                                <button type="submit" class="btn-step-next" style="background: #2563eb;">
                                    <span>Save Personal Info &amp; Proceed to Stage 3 &rarr;</span>
                                </button>
                            </div>
                        </form>

                        <script>
                            function toggleEmpCode() {
                                const wrap = document.getElementById('ref-empcode-wrap');
                                const emp = document.querySelector('input[value="via Emp vtstr"]')?.checked;
                                const cou = document.querySelector('input[value="via Conseller / Telecaller"]')?.checked;
                                if (wrap) wrap.style.display = (emp || cou) ? 'block' : 'none';
                            }

                            const allStatesData = @json($states);
                            const allDistrictsData = @json($districts);

                            function onCountryChange(countryId) {
                                const stateSelect = document.getElementById('geo-state-select');
                                if (!stateSelect) return;
                                const currentSelectedState = stateSelect.value;
                                stateSelect.innerHTML = '<option value="">-- Select State --</option>';
                                allStatesData.forEach(function(s) {
                                    if (!countryId || s.country_id == countryId) {
                                        const opt = document.createElement('option');
                                        opt.value = s.state_id;
                                        opt.textContent = s.state_name;
                                        if (s.state_id == currentSelectedState) opt.selected = true;
                                        stateSelect.appendChild(opt);
                                    }
                                });
                                onStateChange(stateSelect.value);
                            }

                            function onStateChange(stateId) {
                                const distSelect = document.getElementById('geo-district-select');
                                if (!distSelect) return;
                                const currentSelectedDist = distSelect.value;
                                distSelect.innerHTML = '<option value="">-- Select District --</option>';
                                allDistrictsData.forEach(function(d) {
                                    if (!stateId || d.STATE_ID == stateId) {
                                        const opt = document.createElement('option');
                                        opt.value = d.DISTRICT_ID;
                                        opt.textContent = d.DISTRICT_NAME;
                                        if (d.DISTRICT_ID == currentSelectedDist) opt.selected = true;
                                        distSelect.appendChild(opt);
                                    }
                                });
                            }

                            document.addEventListener('DOMContentLoaded', function() {
                                const stateSelect = document.getElementById('geo-state-select');
                                if (stateSelect && stateSelect.value) {
                                    onStateChange(stateSelect.value);
                                }
                            });
                        </script>
                    </div>
                </div>

            <!-- ================= STAGE 3: ACADEMIC DETAILS (SEPARATE STEP 3) ================= -->
            @elseif($activeStep === 3)
                <div style="padding: 1.5rem 2rem; width: 100%; box-sizing: border-box;">
                    <!-- CARD 2: 3. EDUCATION DETAILS (FULL WIDTH) -->
                    <div class="mockup-card card-active-step" style="width: 100%; box-sizing: border-box; border-radius: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                            <h3 class="mockup-card-title" style="margin: 0;">3. education details; B-tech; m-tech; phd; 10th+Inter+ug+pg</h3>
                            @if($stages[3]['status'] === 'completed')
                                <span class="step-header-tag tag-success" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">✓ Completed</span>
                            @else
                                <span class="step-header-tag tag-pending" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">In Progress</span>
                            @endif
                        </div>

                        <!-- Degree Stream Selector: B.Tech (10th + Inter) | M.Tech (10th + Inter + B.Tech) | PhD (10th + Inter + UG + PG) -->
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.15rem; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 0.75rem 1rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                <span style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">Degree Stream:</span>
                                <button type="button" class="degree-pill-btn {{ $degreeLevel === 'btech' ? 'active' : '' }}" id="pill-btech" onclick="setDegreeLevel('btech')">
                                    🎓 B.Tech &bull; 10th + Inter
                                </button>
                                <button type="button" class="degree-pill-btn {{ $degreeLevel === 'mtech' ? 'active' : '' }}" id="pill-mtech" onclick="setDegreeLevel('mtech')">
                                    🎓 M.Tech &bull; 10th + Inter + B.Tech (UG)
                                </button>
                                <button type="button" class="degree-pill-btn {{ $degreeLevel === 'phd' ? 'active' : '' }}" id="pill-phd" onclick="setDegreeLevel('phd')">
                                    🎓 Ph.D &bull; 10th + Inter + UG + PG
                                </button>
                                <input type="hidden" name="degree_level" id="degree_level_input" value="{{ $degreeLevel }}">
                            </div>
                            <span id="degree-badge-indicator" class="mockup-pill-badge" style="font-size: 0.76rem; padding: 0.25rem 0.7rem; background: #0f2744; color: #fff;">
                                {{ $degreeLevel === 'phd' ? '10th + Inter + UG + PG' : ($degreeLevel === 'mtech' ? '10th + Inter + B.Tech (UG)' : '10th + Inter') }}
                            </span>
                        </div>

                        <form action="{{ route('dashboards.save-step') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateStep3Form(this);">
                            @csrf
                            <input type="hidden" name="step" value="3">

                            <!-- ================= SUBBOX 1: 10th SCHOOL / SSC INFO ================= -->
                            <div class="mockup-subbox" id="sec-10th-details">
                                <h4 class="mockup-subbox-title">school/ college 10 th info</h4>

                                <div class="mockup-field-row-2">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">school/ co name : <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="school_co_name" class="mockup-input" value="{{ $academicDetails?->tenth_school_name ?: 'Sri Chaitanya English Medium High School' }}" placeholder="School / College Name" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Boad of study : <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="boad_of_study" class="mockup-input" value="{{ $academicDetails?->tenth_study_board ?: 'BSEAP' }}" placeholder="Board of Study" required>
                                    </div>
                                </div>

                                <div class="mockup-field-row-3" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Roll NO : <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="roll_no" class="mockup-input" value="{{ $academicDetails?->tenth_roll_no ?: '2407189021' }}" placeholder="Roll / Hall Ticket No" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">passing year : <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="passing_year" class="mockup-input" value="{{ $academicDetails?->tenth_passed_year ?: 2024 }}" placeholder="2024" min="2000" max="2030" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Evaluation Type / Marks: <span style="color:#ef4444;">*</span></label>
                                        <div class="mockup-radio-row" style="margin-top: 0.35rem;">
                                            <label class="mockup-radio-label">
                                                <input type="radio" name="eval_type" value="marks" {{ ($academicDetails?->tenth_marks_type !== 'CGPA') ? 'checked' : '' }} onchange="onEvalTypeChange('marks')">
                                                <span>marks</span>
                                            </label>
                                            <label class="mockup-radio-label">
                                                <input type="radio" name="eval_type" value="CGPA" {{ ($academicDetails?->tenth_marks_type === 'CGPA') ? 'checked' : '' }} onchange="onEvalTypeChange('CGPA')">
                                                <span>CGPA</span>
                                            </label>
                                        </div>
                                        <div id="cgpa-input-box" style="display: {{ ($academicDetails?->tenth_marks_type === 'CGPA') ? 'block' : 'none' }}; margin-top: 0.35rem;">
                                            <input type="number" id="eval_cgpa_field" name="cgpa_value" class="mockup-input" style="padding: 0.4rem 0.65rem;" step="0.01" min="1.0" max="10.0" value="{{ $academicDetails?->tenth_cgpa ?: '9.80' }}" placeholder="e.g. 9.80" oninput="evaluateScholarshipFromInput()">
                                        </div>
                                        <div id="marks-input-box" style="display: {{ ($academicDetails?->tenth_marks_type !== 'CGPA') ? 'block' : 'none' }}; margin-top: 0.35rem;">
                                            <input type="number" id="eval_marks_field" name="marks_value" class="mockup-input" style="padding: 0.4rem 0.65rem;" step="0.1" min="0" max="1000" value="{{ $academicDetails?->tenth_marks ?: '590' }}" placeholder="e.g. 590 or 85%" oninput="evaluateScholarshipFromInput()">
                                        </div>
                                    </div>
                                </div>

                                <!-- DYNAMIC SCHOLARSHIP ELIGIBILITY & CONCESSION CONTAINER (POPS UP WHEN >= 60%) -->
                                <div id="scholarship-details-box" style="display: none; margin-top: 1rem; background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%); border: 1.5px solid #86efac; border-radius: 14px; padding: 1.25rem 1.35rem; box-shadow: 0 4px 14px rgba(34, 197, 94, 0.08);">
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 0.85rem; border-bottom: 1px dashed #86efac; padding-bottom: 0.75rem;">
                                        <div style="display: flex; align-items: center; gap: 0.65rem;">
                                            <span style="font-size: 1.5rem;">🎉</span>
                                            <div>
                                                <h4 style="margin: 0; font-size: 0.98rem; font-weight: 800; color: #14532d;">Eligible for VFSTR Merit Scholarship!</h4>
                                                <p style="margin: 0.15rem 0 0 0; font-size: 0.76rem; color: #166534; font-weight: 600;">Candidate aggregate score is &ge; 60% — Tuition Fee Concession Tier unlocked!</p>
                                            </div>
                                        </div>
                                        <span id="scholarship-status-badge" style="background: #16a34a; color: #ffffff; font-size: 0.74rem; font-weight: 700; padding: 0.35rem 0.85rem; border-radius: 20px; letter-spacing: 0.02em;">
                                            ✓ Scholarship Eligible
                                        </span>
                                    </div>

                                    <div class="mockup-field-row-2" style="align-items: center;">
                                        <div class="mockup-field-group">
                                            <label class="mockup-label" style="color: #14532d;">Applicable Concession Tier:</label>
                                            <div id="scholarship-tier-display" style="background: #ffffff; border: 1.5px solid #86efac; border-radius: 10px; padding: 0.65rem 0.95rem; font-size: 0.86rem; font-weight: 800; color: #15803d; display: flex; align-items: center; gap: 0.5rem; min-height: 42px;">
                                                <span>🏆 Tier 1: 50% Tuition Fee Concession</span>
                                            </div>
                                        </div>

                                        <div class="mockup-field-group">
                                            <label class="mockup-label" style="color: #14532d;">Scholarship Concession Status:</label>
                                            <div style="background: #e0f2fe; border: 1.5px solid #7dd3fc; border-radius: 10px; padding: 0.65rem 0.95rem; font-size: 0.84rem; font-weight: 700; color: #0369a1; display: flex; align-items: center; gap: 0.5rem; min-height: 42px;">
                                                <span>Auto-adjusted in Semester Fee</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="margin-top: 0.85rem; padding-top: 0.65rem; border-top: 1px solid rgba(134, 239, 172, 0.4); font-size: 0.76rem; color: #166534; display: flex; flex-wrap: wrap; gap: 1.5rem;">
                                        <span><strong>Tier 1 (&ge; 80%):</strong> 50% Fee Concession</span>
                                        <span><strong>Tier 2 (70% - 79.9%):</strong> 35% Fee Concession</span>
                                        <span><strong>Tier 3 (60% - 69.9%):</strong> 25% Fee Concession</span>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUBBOX 2: 12th / INTERMEDIATE / DIPLOMA INFO ================= -->
                            <div class="mockup-subbox" id="sec-inter-details" style="margin-top: 1rem;">
                                <h4 class="mockup-subbox-title">12th / Intermediate / Diploma Details</h4>

                                <div class="mockup-field-row-3">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Course choice: <span style="color:#ef4444;">*</span></label>
                                        <div class="mockup-radio-row" style="margin-top: 0.35rem;">
                                            <label class="mockup-radio-label">
                                                <input type="radio" name="course_choice" value="Inter" {{ ($academicDetails?->pu2_study_board !== 'Diploma') ? 'checked' : '' }}>
                                                <span>Inter</span>
                                            </label>
                                            <label class="mockup-radio-label">
                                                <input type="radio" name="course_choice" value="Diploma" {{ ($academicDetails?->pu2_study_board === 'Diploma') ? 'checked' : '' }}>
                                                <span>Diploma</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Subset choice: <span style="color:#ef4444;">*</span></label>
                                        <div class="mockup-radio-row" style="margin-top: 0.35rem;">
                                            <label class="mockup-radio-label">
                                                <input type="radio" name="subset_choice" value="First Year" {{ ($academicDetails?->pu2_current_status !== 'Second year') ? 'checked' : '' }}>
                                                <span>First Year</span>
                                            </label>
                                            <label class="mockup-radio-label">
                                                <input type="radio" name="subset_choice" value="Second year" {{ ($academicDetails?->pu2_current_status === 'Second year') ? 'checked' : '' }}>
                                                <span>Second year</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Passing Year: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="inter_year" class="mockup-input" value="{{ $academicDetails?->pu2_passed_year ?: 2026 }}" placeholder="2026" min="2000" max="2030">
                                    </div>
                                </div>

                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Junior College / Institute Name: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_college" class="mockup-input" value="{{ $academicDetails?->pu2_college_name ?: 'Narayana Junior College' }}" placeholder="Junior College Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Hall Ticket / Roll No: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_hallticket" class="mockup-input" value="{{ $academicDetails?->pu2_roll_no ?: '2607189021' }}" placeholder="Inter Roll No">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Inter Total Marks / CGPA: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_marks" class="mockup-input" value="{{ $academicDetails?->pu2_marks ?: '962' }}" placeholder="e.g. 962 / 1000">
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUBBOX 3: UNDERGRADUATE (B.TECH / DEGREE) (M.TECH & PHD) ================= -->
                            <div class="mockup-subbox" id="sec-ug-details" style="display: {{ ($degreeLevel === 'mtech' || $degreeLevel === 'phd') ? 'block' : 'none' }}; margin-top: 1rem; border-color: #93c5fd; background: #f0f7ff;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                                    <h4 class="mockup-subbox-title" style="color: #1e3a8a;">Undergraduate (B.Tech / Degree) Details</h4>
                                    <span class="mockup-pill-badge" style="background: #2563eb; color: #fff; font-size: 0.72rem;">Required for M.Tech &amp; Ph.D</span>
                                </div>

                                <div class="mockup-field-row-3">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">UG College / Institute Name: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_college_name" class="mockup-input" value="{{ $academicDetails?->ug_college_name ?: "Vignan's Lara Institute of Technology & Science" }}" placeholder="UG College Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">University / Board: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_university_board" class="mockup-input" value="{{ $academicDetails?->ug_university_board ?: 'JNTUK / Andhra University' }}" placeholder="University Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">UG Registration / Roll NO: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_roll_no" class="mockup-input" value="{{ $academicDetails?->ug_roll_no ?: '201FA04001' }}" placeholder="UG Roll Number">
                                    </div>
                                </div>

                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Passing Year: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="ug_passed_year" class="mockup-input" value="{{ $academicDetails?->ug_passed_year ?: 2024 }}" placeholder="2024" min="2000" max="2030">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Aggregate CGPA / %: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_cgpa" class="mockup-input" value="{{ $academicDetails?->ug_cgpa ?: '8.75' }}" placeholder="e.g. 8.75 CGPA">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Degree Status: <span style="color:#ef4444;">*</span></label>
                                        <select name="ug_current_status" class="mockup-select">
                                            <option value="Completed" {{ ($academicDetails?->ug_current_status === 'Completed' || !$academicDetails) ? 'selected' : '' }}>Completed (Graduated)</option>
                                            <option value="Final Year Appeared" {{ ($academicDetails?->ug_current_status === 'Final Year Appeared') ? 'selected' : '' }}>Final Year Appeared / Awaiting Results</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUBBOX 4: POSTGRADUATE (M.TECH / PG) (PHD ONLY) ================= -->
                            <div class="mockup-subbox" id="sec-pg-details" style="display: {{ $degreeLevel === 'phd' ? 'block' : 'none' }}; margin-top: 1rem; border-color: #d8b4fe; background: #faf5ff;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                                    <h4 class="mockup-subbox-title" style="color: #6b21a8;">Postgraduate (M.Tech / PG) Details</h4>
                                    <span class="mockup-pill-badge" style="background: #9333ea; color: #fff; font-size: 0.72rem;">Required for Ph.D</span>
                                </div>

                                <div class="mockup-field-row-3">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">PG College / University Name: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_college_name" class="mockup-input" value="{{ $academicDetails?->pg_college_name ?: 'VFSTR Deemed to be University' }}" placeholder="PG College Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">University / Board: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_university_board" class="mockup-input" value="{{ $academicDetails?->pg_university_board ?: 'VFSTR' }}" placeholder="University Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">PG Roll NO / Reg No: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_roll_no" class="mockup-input" value="{{ $academicDetails?->pg_roll_no ?: '241FK01001' }}" placeholder="PG Roll Number">
                                    </div>
                                </div>

                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Passing Year: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="pg_passed_year" class="mockup-input" value="{{ $academicDetails?->pg_passed_year ?: 2026 }}" placeholder="2026" min="2000" max="2030">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">PG CGPA / %: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_cgpa" class="mockup-input" value="{{ $academicDetails?->pg_cgpa ?: '9.10' }}" placeholder="e.g. 9.10 CGPA">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">PG Status: <span style="color:#ef4444;">*</span></label>
                                        <select name="pg_current_status" class="mockup-select">
                                            <option value="Completed" {{ ($academicDetails?->pg_current_status === 'Completed' || !$academicDetails) ? 'selected' : '' }}>Completed</option>
                                            <option value="Pursuing" {{ ($academicDetails?->pg_current_status === 'Pursuing') ? 'selected' : '' }}>Pursuing / Final Semester</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUBBOX 5: UPLOAD DOCS ================= -->
                            <div class="mockup-subbox" style="margin-top: 1rem;">
                                <h4 class="mockup-subbox-title">Upload Academic Documents</h4>
                                <div class="mockup-field-row-2" style="align-items: center;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Upload Certificates / Marks Memo: (Max 5MB PDF/JPG)</label>
                                        <div class="mockup-file-wrap" style="margin-top: 0.35rem;">
                                            <input type="file" id="upload-docs-file" name="upload_docs" accept=".pdf,.jpg,.jpeg,.png" style="display: none;" onchange="document.getElementById('upload-docs-label-text').textContent = this.files[0] ? this.files[0].name : 'upload docs:';">
                                            <span id="upload-docs-label-text" class="mockup-file-text">upload docs:</span>
                                            <button type="button" class="mockup-browse-btn" onclick="document.getElementById('upload-docs-file').click();">Browse</button>
                                        </div>
                                    </div>
                                    <div style="font-size: 0.78rem; color: #64748b; line-height: 1.4; padding-top: 1.15rem;">
                                        Attach 10th Marks Memo, Intermediate / Diploma Certificates, and UG Degree Memo if applicable.
                                    </div>
                                </div>
                            </div>

                            <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem; justify-content: space-between; align-items: center;">
                                <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-step-prev" style="text-decoration: none;">
                                    &larr; Stage 2: {{ $stages[2]['title'] }}
                                </a>
                                <button type="submit" class="btn-step-next" style="background: #0f2744;">
                                    <span>Save Academic Info &amp; Proceed to Stage 4 &rarr;</span>
                                </button>
                            </div>
                        </form>

                        <script>
                            function setDegreeLevel(level) {
                                document.querySelectorAll('.degree-pill-btn').forEach(function(b) {
                                    b.classList.remove('active');
                                });
                                const activeBtn = document.getElementById('pill-' + level);
                                if (activeBtn) activeBtn.classList.add('active');

                                const hiddenInput = document.getElementById('degree_level_input');
                                if (hiddenInput) hiddenInput.value = level;

                                const ugSec = document.getElementById('sec-ug-details');
                                const pgSec = document.getElementById('sec-pg-details');
                                const indicator = document.getElementById('degree-badge-indicator');

                                if (level === 'btech') {
                                    if (ugSec) ugSec.style.display = 'none';
                                    if (pgSec) pgSec.style.display = 'none';
                                    if (indicator) indicator.textContent = '10th + Inter';
                                } else if (level === 'mtech') {
                                    if (ugSec) ugSec.style.display = 'block';
                                    if (pgSec) pgSec.style.display = 'none';
                                    if (indicator) indicator.textContent = '10th + Inter + B.Tech (UG)';
                                } else if (level === 'phd') {
                                    if (ugSec) ugSec.style.display = 'block';
                                    if (pgSec) pgSec.style.display = 'block';
                                    if (indicator) indicator.textContent = '10th + Inter + UG + PG';
                                }
                            }

                            function onEvalTypeChange(type) {
                                const cgpaIn = document.getElementById('cgpa-input-box');
                                const marksIn = document.getElementById('marks-input-box');

                                if (type === 'CGPA') {
                                    if (cgpaIn) cgpaIn.style.display = 'block';
                                    if (marksIn) marksIn.style.display = 'none';
                                } else {
                                    if (cgpaIn) cgpaIn.style.display = 'none';
                                    if (marksIn) marksIn.style.display = 'block';
                                }
                                evaluateScholarshipFromInput();
                            }

                            function evaluateScholarshipFromInput() {
                                const isCgpa = document.querySelector('input[name="eval_type"][value="CGPA"]')?.checked;
                                let percentage = 0;

                                if (isCgpa) {
                                    const cgpa = parseFloat(document.getElementById('eval_cgpa_field')?.value || 0);
                                    percentage = cgpa * 9.5;
                                } else {
                                    const marks = parseFloat(document.getElementById('eval_marks_field')?.value || 0);
                                    if (marks > 100) {
                                        percentage = (marks / 600) * 100;
                                    } else {
                                        percentage = marks;
                                    }
                                }

                                const sBox = document.getElementById('scholarship-details-box');
                                const display = document.getElementById('scholarship-tier-display');
                                const badge = document.getElementById('scholarship-status-badge');

                                if (percentage >= 60) {
                                    if (sBox) sBox.style.display = 'block';
                                    if (percentage >= 80) {
                                        if (display) display.innerHTML = '<span>🏆 Tier 1: 50% Tuition Fee Concession (' + Math.round(percentage) + '%)</span>';
                                        if (badge) {
                                            badge.style.background = '#16a34a';
                                            badge.textContent = '✓ Eligible: 50% Concession';
                                        }
                                    } else if (percentage >= 70) {
                                        if (display) display.innerHTML = '<span>⭐ Tier 2: 35% Tuition Fee Concession (' + Math.round(percentage) + '%)</span>';
                                        if (badge) {
                                            badge.style.background = '#2563eb';
                                            badge.textContent = '✓ Eligible: 35% Concession';
                                        }
                                    } else {
                                        if (display) display.innerHTML = '<span>⭐ Tier 3: 25% Tuition Fee Concession (' + Math.round(percentage) + '%)</span>';
                                        if (badge) {
                                            badge.style.background = '#d97706';
                                            badge.textContent = '✓ Eligible: 25% Concession';
                                        }
                                    }
                                } else {
                                    if (sBox) sBox.style.display = 'none';
                                }
                            }

                            function validateStep3Form(form) {
                                return true;
                            }

                            document.addEventListener('DOMContentLoaded', function() {
                                const activeEval = document.querySelector('input[name="eval_type"]:checked')?.value || 'marks';
                                onEvalTypeChange(activeEval);
                            });
                        </script>
                    </div>
                </div>

            <!-- ================= STAGE 4: APPLICATION DOWNLOAD ================= -->
            @elseif($activeStep === 4)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 4 of 5 &bull; Application &amp; Documents</div>
                        <h3>{{ $stages[4]['title'] }}</h3>
                        <p>Download your official candidate admission application form and verify submitted academic documents.</p>
                    </div>
                    @if($stages[4]['status'] === 'completed')
                        <span class="step-header-tag tag-success">✓ Available</span>
                    @else
                        <span class="step-header-tag tag-pending">In Progress</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="4">

                    <!-- Official Application Form Download Capsule -->
                    <div class="section-box" style="background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%); border: 1.5px solid #86efac; border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <div style="width: 48px; height: 48px; border-radius: 10px; background: #16a34a; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #fff;">
                                    📄
                                </div>
                                <div>
                                    <h4 style="margin: 0; font-size: 1rem; font-weight: 700; color: #0f172a;">Official Candidate Admission Application Form</h4>
                                    <p style="margin: 0.2rem 0 0; font-size: 0.82rem; color: #475569;">Application No: <strong>#{{ $appNo }}</strong> &bull; Generated from Admission Master Database</p>
                                </div>
                            </div>
                            <a href="javascript:void(0)" onclick="window.print()" class="btn-step-next" style="text-decoration: none; padding: 0.6rem 1.25rem; font-size: 0.85rem; background: #16a34a;">
                                <span>📥 Download Application PDF</span>
                            </a>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Uploaded &amp; Required Documents</span>
                        </div>
                        <div class="kyc-docs-grid">
                            <div class="kyc-doc-card">
                                <div class="kyc-doc-top">
                                    <div class="kyc-doc-icon">📜</div>
                                    <span class="kyc-badge kyc-badge-verified">✓ Verified</span>
                                </div>
                                <div>
                                    <h4 class="kyc-doc-title">10th SSC Marks Memo</h4>
                                    <p class="kyc-doc-sub">Class X original certificate or DigiLocker attested copy</p>
                                </div>
                                <div style="font-size:0.78rem; color:#059669; font-weight:700;">File: SSC_Marks_Memo_Verified.pdf</div>
                            </div>

                            <div class="kyc-doc-card">
                                <div class="kyc-doc-top">
                                    <div class="kyc-doc-icon">📑</div>
                                    <span class="kyc-badge kyc-badge-verified">✓ Verified</span>
                                </div>
                                <div>
                                    <h4 class="kyc-doc-title">12th Intermediate Memo</h4>
                                    <p class="kyc-doc-sub">Short memo or internet copy with principal attestation</p>
                                </div>
                                <div style="font-size:0.78rem; color:#059669; font-weight:700;">File: Inter_Plus2_Memo_Verified.pdf</div>
                            </div>

                            <div class="kyc-doc-card">
                                <div class="kyc-doc-top">
                                    <div class="kyc-doc-icon">🪪</div>
                                    <span class="kyc-badge kyc-badge-verified">✓ Verified</span>
                                </div>
                                <div>
                                    <h4 class="kyc-doc-title">Candidate Aadhaar Card</h4>
                                    <p class="kyc-doc-sub">Government proof of identity &amp; native address</p>
                                </div>
                                <div style="font-size:0.78rem; color:#059669; font-weight:700;">File: Aadhaar_Front_Back.pdf</div>
                            </div>

                            <div class="kyc-doc-card">
                                <div class="kyc-doc-top">
                                    <div class="kyc-doc-icon">🖼️</div>
                                    <span class="kyc-badge kyc-badge-verified">✓ Verified</span>
                                </div>
                                <div>
                                    <h4 class="kyc-doc-title">Passport Size Photograph</h4>
                                    <p class="kyc-doc-sub">Recent studio color photo with white background</p>
                                </div>
                                <div style="font-size:0.78rem; color:#059669; font-weight:700;">File: Candidate_Passport_Photo.jpg</div>
                            </div>

                            <div class="kyc-doc-card">
                                <div class="kyc-doc-top">
                                    <div class="kyc-doc-icon">🏛️</div>
                                    <span class="kyc-badge kyc-badge-pending">⏳ Pending</span>
                                </div>
                                <div>
                                    <h4 class="kyc-doc-title">Transfer Certificate (TC)</h4>
                                    <p class="kyc-doc-sub">Original TC from last attended Junior College / School</p>
                                </div>
                                <input type="file" class="field-input" style="padding:0.4rem; font-size:0.75rem;">
                            </div>

                            <div class="kyc-doc-card">
                                <div class="kyc-doc-top">
                                    <div class="kyc-doc-icon">📋</div>
                                    <span class="kyc-badge kyc-badge-pending">⏳ Pending</span>
                                </div>
                                <div>
                                    <h4 class="kyc-doc-title">Study &amp; Conduct Certificate</h4>
                                    <p class="kyc-doc-sub">Bonafide certificate covering Class VI to Intermediate</p>
                                </div>
                                <input type="file" class="field-input" style="padding:0.4rem; font-size:0.75rem;">
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 3]) }}" class="btn-step-prev">
                            &larr; Stage 3: {{ $stages[3]['title'] }}
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Proceed to Stage 5: {{ $stages[5]['title'] }} &rarr;</span>
                        </button>
                    </div>
                </form>

            <!-- ================= STAGE 5: VSAT RESULTS ================= -->
            @elseif($activeStep === 5)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 5 of 5 &bull; V-SAT 2026 Examination</div>
                        <h3>{{ $stages[5]['title'] }} &amp; Merit Scorecard</h3>
                        <p>Official V-SAT (Vignan Scholastic Aptitude Test) scorecard, rank percentile, and merit scholarship eligibility.</p>
                    </div>
                    @if($stages[5]['status'] === 'completed')
                        <span class="step-header-tag tag-success">✓ Verified</span>
                    @else
                        <span class="step-header-tag tag-success">✓ Merit Qualified</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="5">

                    <!-- Official VSAT Merit Card -->
                    <div class="section-box" style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1.5px solid #93c5fd; border-radius: 12px; padding: 1.25rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.25rem;">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <div style="width:44px; height:44px; border-radius:10px; background:#2563eb; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.35rem;">
                                    🏆
                                </div>
                                <div>
                                    <h4 style="margin:0; font-size:1rem; font-weight:800; color:#1e3a8a;">V-SAT 2026 National Entrance Examination</h4>
                                    <span style="font-size:0.8rem; color:#475569;">Hall Ticket: <strong>VSAT26-{{ $appNo ?: '2027000005' }}</strong> &bull; Exam Mode: Online Computer Based</span>
                                </div>
                            </div>
                            <span class="meta-badge meta-badge-success" style="font-size:0.85rem; padding:0.4rem 0.85rem;">✓ Qualified for Counseling</span>
                        </div>

                        <div class="form-grid-3">
                            <div style="background:#fff; border-radius:10px; padding:0.9rem; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase;">All India Rank (AIR)</div>
                                <div style="font-size:1.6rem; font-weight:900; color:#2563eb; margin-top:0.2rem;">1,420</div>
                                <span style="font-size:0.75rem; color:#16a34a; font-weight:700;">Top 3.2% Nationally</span>
                            </div>
                            <div style="background:#fff; border-radius:10px; padding:0.9rem; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase;">Percentile Score</div>
                                <div style="font-size:1.6rem; font-weight:900; color:#0f172a; margin-top:0.2rem;">96.85 %</div>
                                <span style="font-size:0.75rem; color:#475569;">Total Marks: 94 / 120</span>
                            </div>
                            <div style="background:#fff; border-radius:10px; padding:0.9rem; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase;">Scholarship Granted</div>
                                <div style="font-size:1.4rem; font-weight:900; color:#16a34a; margin-top:0.2rem;">25% Fee Waiver</div>
                                <span style="font-size:0.75rem; color:#15803d; font-weight:600;">Category-A Merit Slab</span>
                            </div>
                        </div>
                    </div>

                    <!-- Subject-wise Breakdown -->
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Subject-wise Mark Distribution</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Mathematics / Biology</label>
                                <input type="text" class="field-input" value="42 / 50 Marks (84%)" readonly style="font-weight:700;">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Physics</label>
                                <input type="text" class="field-input" value="26 / 35 Marks (74.3%)" readonly style="font-weight:700;">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Chemistry &amp; English</label>
                                <input type="text" class="field-input" value="26 / 35 Marks (74.3%)" readonly style="font-weight:700;">
                            </div>
                        </div>
                    </div>

                    <!-- Parent / Emergency Contact Record (Preserved) -->
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Parent &amp; Emergency Contact Records</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Father's Name <span class="required-star">*</span></label>
                                <input type="text" name="father_name" class="field-input" value="Guduru Venkateswara Rao" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Father's Contact Number <span class="required-star">*</span></label>
                                <input type="tel" name="father_phone" class="field-input" value="9848123456" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Mother's Name <span class="required-star">*</span></label>
                                <input type="text" name="mother_name" class="field-input" value="Guduru Lakshmi" required>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 4]) }}" class="btn-step-prev">
                            &larr; Stage 4: {{ $stages[4]['title'] }}
                        </a>
                        <button type="submit" class="btn-step-next" style="background: #15803d;">
                            <span>✓ Complete &amp; Save Application</span>
                        </button>
                    </div>
                </form>



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
