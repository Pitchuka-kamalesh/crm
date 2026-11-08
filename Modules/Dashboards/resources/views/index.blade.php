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

        /* Sleek 8-Step Bubble Track */
        .stepper-nav-bar {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 0.25rem;
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

            <!-- Center Navigation Links -->
            <ul class="dash-nav-links">
                <li><a href="{{ route('dashboards.index', ['step' => 1]) }}" class="dash-nav-link {{ $activeStep === 1 ? 'active' : '' }}">Fee Payment</a></li>
                <li><a href="{{ route('dashboards.index', ['step' => 2]) }}" class="dash-nav-link {{ $activeStep === 2 ? 'active' : '' }}">Profile</a></li>
                <li><a href="{{ route('dashboards.index', ['step' => 3]) }}" class="dash-nav-link {{ $activeStep === 3 ? 'active' : '' }}">Academic</a></li>
                <li><a href="{{ route('dashboards.index', ['step' => 4]) }}" class="dash-nav-link {{ $activeStep === 4 ? 'active' : '' }}">Documents</a></li>
                <li><a href="{{ route('dashboards.index', ['step' => 6]) }}" class="dash-nav-link {{ $activeStep === 6 ? 'active' : '' }}">Allotment</a></li>
                <li><a href="{{ route('dashboards.index', ['step' => 8]) }}" class="dash-nav-link {{ $activeStep === 8 ? 'active' : '' }}">Digital ID</a></li>
            </ul>

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

        <!-- 8-STEP BUBBLE PROGRESS TRACKER -->
        <section class="stepper-card">
            <div class="stepper-header">
                <div class="stepper-title-box">
                    <h2>Admission Onboarding</h2>
                    <span class="stepper-step-indicator">Step {{ $activeStep }} of 8</span>
                </div>
                <div class="stepper-progress-box">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                    <span class="progress-percent-label">{{ $progressPercent }}% Complete</span>
                </div>
            </div>

            <!-- 8 Stages Bubble Bar -->
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
                        <div class="step-panel-kicker">Stage 1 of 8 &bull; Seat Confirmation</div>
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
                                $feeBreakdown = \App\Services\CouponService::calculateFeeBreakdown($studentAppliNo, 25000.00);
                            @endphp
                            <div style="margin-bottom: 0.65rem; text-align: left;">
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: #0f172a; margin: 0;">Seat Confirmation Fee Payment</h4>
                                <p style="font-size: 0.76rem; color: #64748b; margin: 0.1rem 0 0 0;">Pay securely via the official PayU gateway to lock your provisional seat.</p>
                            </div>

                            <!-- PayU Fee Summary Box with Concessions -->
                            <div style="max-width: 480px; margin: 0 auto 0.75rem auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 0.75rem 1rem; text-align: left;">
                                <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #475569; margin-bottom: 0.3rem;">
                                    <span>Base Admission Fee:</span>
                                    <strong style="color: #0f172a;">₹ 25,000.00</strong>
                                </div>

                                @if($feeBreakdown['def_discount'] > 0)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: #059669; margin-bottom: 0.25rem;">
                                        <span>Early Bird Concession ({{ $feeBreakdown['def_coupon'] }}):</span>
                                        <strong style="color: #059669;">- ₹ {{ number_format($feeBreakdown['def_discount'], 2) }}</strong>
                                    </div>
                                @endif

                                @if($feeBreakdown['per_discount'] > 0)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: #059669; margin-bottom: 0.25rem;">
                                        <span>Incharge Personal Concession ({{ $feeBreakdown['per_coupon'] }}):</span>
                                        <strong style="color: #059669;">- ₹ {{ number_format($feeBreakdown['per_discount'], 2) }}</strong>
                                    </div>
                                @endif

                                @if($feeBreakdown['total_discount'] > 0)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.76rem; color: #047857; margin-bottom: 0.3rem; background: #ecfdf5; padding: 3px 6px; border-radius: 6px;">
                                        <span style="font-weight: 600;">Total Concession Deducted:</span>
                                        <strong style="color: #047857;">- ₹ {{ number_format($feeBreakdown['total_discount'], 2) }}</strong>
                                    </div>
                                @endif

                                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: #64748b; margin-bottom: 0.25rem;">
                                    <span>Payment Gateway:</span>
                                    <span style="font-weight: 600; color: #059669;">PayU Secure Hosted</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 0.82rem; color: #64748b; padding-top: 0.35rem; border-top: 1px dashed #cbd5e1; align-items: center;">
                                    <span style="font-weight: 700; font-size: 0.86rem; color: #0f172a;">Total Payable:</span>
                                    <span style="font-size: 1.15rem; font-weight: 800; color: #059669;">₹ {{ number_format($feeBreakdown['net_payable'], 2) }}</span>
                                </div>
                            </div>

                            <form action="{{ route('payu.initiate') }}" method="POST" style="display: inline-block;">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $feeBreakdown['net_payable'] }}">
                                <button type="submit" class="btn-step-next" style="display: inline-flex; align-items: center; gap: 0.45rem; margin: 0 auto; background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 8px 22px; font-size: 0.86rem; border-radius: 8px; border: none; font-weight: 700; cursor: pointer; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);">
                                    <span>💳 Pay ₹{{ number_format($feeBreakdown['net_payable']) }} via PayU Gateway</span>
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
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
                            <span>Step 2: Personal Profile &rarr;</span>
                        </a>
                    </div>
                </div>

            <!-- ================= STAGE 2: PERSONAL PROFILE ================= -->
            @elseif($activeStep === 2)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 2 of 8 &bull; Candidate Information</div>
                        <h3>Personal Profile</h3>
                        <p>Verify and update your personal details, contact coordinates, and residential background.</p>
                    </div>
                    @if($stages[2]['status'] === 'completed')
                        <span class="step-header-tag tag-success">✓ Completed</span>
                    @else
                        <span class="step-header-tag tag-pending">In Progress</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="2">

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Candidate Demographics</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Full Name as per SSC <span class="required-star">*</span></label>
                                <input type="text" name="name" class="field-input" value="{{ $crmStudent?->name ?: $displayName }}" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Date of Birth <span class="required-star">*</span></label>
                                <input type="date" name="dob" class="field-input" value="2008-05-14" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Gender <span class="required-star">*</span></label>
                                <select name="gender" class="field-select">
                                    <option value="Male" selected>Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Blood Group</label>
                                <select name="blood_group" class="field-select">
                                    <option value="O+">O Positive (O+)</option>
                                    <option value="A+">A Positive (A+)</option>
                                    <option value="B+" selected>B Positive (B+)</option>
                                    <option value="AB+">AB Positive (AB+)</option>
                                    <option value="O-">O Negative (O-)</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Aadhaar Card Number <span class="required-star">*</span></label>
                                <input type="text" name="aadhaar_no" class="field-input" value="7845 9210 4432" maxlength="14" placeholder="XXXX XXXX XXXX" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Social Category / Community</label>
                                <select name="category" class="field-select">
                                    <option value="OC" selected>Open Category (OC)</option>
                                    <option value="OBC">Other Backward Class (OBC)</option>
                                    <option value="BC-A">BC-A</option>
                                    <option value="BC-B">BC-B</option>
                                    <option value="SC">Scheduled Caste (SC)</option>
                                    <option value="ST">Scheduled Tribe (ST)</option>
                                    <option value="EWS">Economically Weaker Section (EWS)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Communication &amp; Permanent Address</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Mobile Number</label>
                                <input type="text" name="phone" class="field-input" value="{{ $user?->phnno }}" readonly>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Personal Email ID <span class="required-star">*</span></label>
                                <input type="email" name="email" class="field-input" value="{{ $crmStudent?->emailid ?: $user?->email }}" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Accommodation Preference</label>
                                <select name="hostel_required" class="field-select">
                                    <option value="0" {{ ($crmStudent?->ableto == 0) ? 'selected' : '' }}>Day Scholar (University Bus Route)</option>
                                    <option value="1" {{ ($crmStudent?->ableto == 1) ? 'selected' : '' }}>Campus Hostel (AC / Attached Bath)</option>
                                    <option value="2">Campus Hostel (Non-AC)</option>
                                </select>
                            </div>
                            <div class="field-group" style="grid-column: span 2;">
                                <label class="field-label">Door No / Street Address <span class="required-star">*</span></label>
                                <input type="text" name="address" class="field-input" value="Flat 402, Sri Sai Nilayam, Brodipet 4th Line" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Pin Code <span class="required-star">*</span></label>
                                <input type="text" name="pincode" class="field-input" value="522002" maxlength="6" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">City / Town <span class="required-star">*</span></label>
                                <input type="text" name="city" class="field-input" value="Guntur" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">State</label>
                                <input type="text" name="state" class="field-input" value="Andhra Pradesh" readonly>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Country</label>
                                <input type="text" name="country" class="field-input" value="India" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 1]) }}" class="btn-step-prev">
                            &larr; Stage 1: Fee Payment
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Proceed to Stage 3 &rarr;</span>
                        </button>
                    </div>
                </form>

            <!-- ================= STAGE 3: ACADEMIC RECORDS ================= -->
            @elseif($activeStep === 3)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 3 of 8 &bull; Education Background</div>
                        <h3>Academic Records</h3>
                        <p>Enter your 10th Standard (SSC) and 12th Standard (Intermediate / Diploma) educational qualifications.</p>
                    </div>
                    @if($stages[3]['status'] === 'completed')
                        <span class="step-header-tag tag-success">✓ Completed</span>
                    @else
                        <span class="step-header-tag tag-pending">In Progress</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="3">

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Class 10th (Secondary School Certificate - SSC)</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Board of Examination <span class="required-star">*</span></label>
                                <select name="ssc_board" class="field-select">
                                    <option value="BSEAP" selected>Board of Secondary Education, AP (BSEAP)</option>
                                    <option value="CBSE">Central Board of Secondary Education (CBSE)</option>
                                    <option value="ICSE">Indian Certificate of Secondary Education (ICSE)</option>
                                    <option value="OTHER">Other State Board</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">School Name <span class="required-star">*</span></label>
                                <input type="text" name="ssc_school" class="field-input" value="Sri Chaitanya English Medium High School" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">SSC Hall Ticket Number <span class="required-star">*</span></label>
                                <input type="text" name="ssc_hallticket" class="field-input" value="2407189021" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Year of Passing <span class="required-star">*</span></label>
                                <select name="ssc_year" class="field-select">
                                    <option value="2024" selected>2024</option>
                                    <option value="2023">2023</option>
                                    <option value="2022">2022</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Grade / CGPA / Total Marks <span class="required-star">*</span></label>
                                <input type="text" name="ssc_marks" class="field-input" value="9.8 / 10.0 CGPA" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">School District / State</label>
                                <input type="text" name="ssc_location" class="field-input" value="Guntur, Andhra Pradesh">
                            </div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Class 12th (Intermediate / +2 / Diploma)</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Qualifying Examination <span class="required-star">*</span></label>
                                <select name="inter_board" class="field-select">
                                    <option value="BIEAP" selected>Board of Intermediate Education, AP (BIEAP)</option>
                                    <option value="TSBIE">Telangana Board of Intermediate (TSBIE)</option>
                                    <option value="CBSE_XII">CBSE Class XII</option>
                                    <option value="ISC">ISC (Class XII)</option>
                                    <option value="DIPLOMA">Polytechnic Diploma</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Junior College Name <span class="required-star">*</span></label>
                                <input type="text" name="inter_college" class="field-input" value="Narayana Junior College, Ring Road Campus" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Intermediate Hall Ticket No <span class="required-star">*</span></label>
                                <input type="text" name="inter_hallticket" class="field-input" value="2601248931" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Major Stream / Group <span class="required-star">*</span></label>
                                <select name="inter_group" class="field-select">
                                    <option value="MPC" selected>MPC (Mathematics, Physics, Chemistry)</option>
                                    <option value="BiPC">BiPC (Biology, Physics, Chemistry)</option>
                                    <option value="MEC">MEC (Mathematics, Economics, Commerce)</option>
                                    <option value="OTHER">Other Vocational Stream</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Marks Obtained / Percentage <span class="required-star">*</span></label>
                                <input type="text" name="inter_marks" class="field-input" value="962 / 1000 (96.2%)" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Year of Completion <span class="required-star">*</span></label>
                                <select name="inter_year" class="field-select">
                                    <option value="2026" selected>2026 (Appearing / Passed)</option>
                                    <option value="2025">2025</option>
                                    <option value="2024">2024</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-step-prev">
                            &larr; Stage 2: Personal Profile
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Proceed to Stage 4 &rarr;</span>
                        </button>
                    </div>
                </form>

            <!-- ================= STAGE 4: CERTIFICATES & KYC ================= -->
            @elseif($activeStep === 4)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 4 of 8 &bull; Document Verification</div>
                        <h3>Certificates &amp; Document KYC</h3>
                        <p>Upload your academic certificates and official identity proof for document scrutiny.</p>
                    </div>
                    @if($stages[4]['status'] === 'completed')
                        <span class="step-header-tag tag-success">✓ Completed</span>
                    @else
                        <span class="step-header-tag tag-pending">In Progress</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="4">

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
                            &larr; Stage 3: Academic Records
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Proceed to Stage 5 &rarr;</span>
                        </button>
                    </div>
                </form>

            <!-- ================= STAGE 5: FAMILY & PARENT DETAILS ================= -->
            @elseif($activeStep === 5)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 5 of 8 &bull; Family Information</div>
                        <h3>Parent &amp; Guardian Details</h3>
                        <p>Provide contact details and occupational records of parents or local guardian.</p>
                    </div>
                    @if($stages[5]['status'] === 'completed')
                        <span class="step-header-tag tag-success">✓ Completed</span>
                    @else
                        <span class="step-header-tag tag-pending">In Progress</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="5">

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Father's Details</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Father's Full Name <span class="required-star">*</span></label>
                                <input type="text" name="father_name" class="field-input" value="Guduru Venkateswara Rao" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Father's Occupation <span class="required-star">*</span></label>
                                <select name="father_occupation" class="field-select">
                                    <option value="Business" selected>Business / Self-Employed</option>
                                    <option value="Government Service">Government Service</option>
                                    <option value="Private Sector">Private Sector Corporate</option>
                                    <option value="Agriculture">Agriculture / Farming</option>
                                    <option value="Professional">Doctor / Lawyer / CA / Professor</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Father's Mobile Number <span class="required-star">*</span></label>
                                <input type="tel" name="father_phone" class="field-input" value="9848123456" required>
                            </div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Mother's Details</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Mother's Full Name <span class="required-star">*</span></label>
                                <input type="text" name="mother_name" class="field-input" value="Guduru Lakshmi" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Mother's Occupation</label>
                                <select name="mother_occupation" class="field-select">
                                    <option value="Homemaker" selected>Homemaker</option>
                                    <option value="Government Service">Government Service</option>
                                    <option value="Teaching / Professor">Teaching / Academician</option>
                                    <option value="Private Sector">Private Sector Corporate</option>
                                    <option value="Business">Business</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Mother's Mobile Number</label>
                                <input type="tel" name="mother_phone" class="field-input" value="9848654321">
                            </div>
                        </div>
                    </div>

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Annual Household Income &amp; Emergency Contact</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Annual Family Income <span class="required-star">*</span></label>
                                <select name="annual_income" class="field-select">
                                    <option value="Upto 2 Lakhs">Below ₹ 2,00,000</option>
                                    <option value="2 to 5 Lakhs">₹ 2,00,000 to ₹ 5,00,000</option>
                                    <option value="5 to 8 Lakhs" selected>₹ 5,00,000 to ₹ 8,00,000</option>
                                    <option value="Above 8 Lakhs">Above ₹ 8,00,000</option>
                                </select>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Emergency Contact Person <span class="required-star">*</span></label>
                                <input type="text" name="emergency_contact_person" class="field-input" value="Guduru Venkateswara Rao (Father)" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Emergency Mobile Number <span class="required-star">*</span></label>
                                <input type="tel" name="emergency_phone" class="field-input" value="9848123456" required>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 4]) }}" class="btn-step-prev">
                            &larr; Stage 4: Certificates &amp; KYC
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Save &amp; Proceed to Stage 6 &rarr;</span>
                        </button>
                    </div>
                </form>

            <!-- ================= STAGE 6: SEAT ALLOTMENT & BRANCH ================= -->
            @elseif($activeStep === 6)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 6 of 8 &bull; Academic Allotment</div>
                        <h3>Seat Allotment &amp; Program Confirmation</h3>
                        <p>Official university allotment order confirming your allotted department and academic campus.</p>
                    </div>
                    @if($stages[6]['status'] === 'completed')
                        <span class="step-header-tag tag-success">✓ Confirmed</span>
                    @else
                        <span class="step-header-tag tag-pending">Active Allotment</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="6">

                    <div class="section-box">
                        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
                            <div class="section-box-title" style="margin-bottom:0;">
                                <span>Confirmed Allotment Details</span>
                            </div>
                            <span class="meta-badge meta-badge-success">✓ Provisional Seat Reserved</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Allotted Campus</label>
                                <input type="text" class="field-input" value="Vadlamudi Main Campus (VDM), Guntur" readonly>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Academic Program</label>
                                <input type="text" class="field-input" value="{{ $programName ?: 'B.Tech - Bachelor of Technology' }}" readonly>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Allotted Department / Branch</label>
                                <input type="text" class="field-input" value="{{ $branchName ?: 'Information Technology' }}" style="font-weight:700; color:#1e3a8a;" readonly>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Academic Curriculum Regulation</label>
                                <input type="text" class="field-input" value="R25 (Outcome-Based AI-Embedded Curriculum)" readonly>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Admission Category</label>
                                <input type="text" class="field-input" value="Category-A (Merit / Convenor Quota)" readonly>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Commencement / Induction Date</label>
                                <input type="text" class="field-input" value="July 15, 2026 (Wednesday)" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="section-box" style="background:#f8fafc;">
                        <h4 style="font-size:0.88rem; font-weight:700; color:#1e3a8a; margin-bottom:0.3rem;">Branch Sliding &amp; Upgradation Policy</h4>
                        <p style="font-size:0.82rem; color:#475569; line-height:1.5; margin:0;">
                            Students holding a confirmed seat in <strong>Information Technology</strong> are eligible for automated merit-sliding into CSE (Core) or CSE (Artificial Intelligence &amp; Machine Learning) during internal counseling rounds based on intermediate merit ranks.
                        </p>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 5]) }}" class="btn-step-prev">
                            &larr; Stage 5: Family Details
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Confirm &amp; Proceed to Stage 7 &rarr;</span>
                        </button>
                    </div>
                </form>

            <!-- ================= STAGE 7: TUITION STRUCTURE ================= -->
            @elseif($activeStep === 7)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 7 of 8 &bull; Financial Structure</div>
                        <h3>Tuition Structure &amp; Fee Breakdown</h3>
                        <p>Complete academic tuition fee schedule, applicable scholarships, and payment installments.</p>
                    </div>
                    @if($stages[7]['status'] === 'completed')
                        <span class="step-header-tag tag-success">✓ Completed</span>
                    @else
                        <span class="step-header-tag tag-pending">In Progress</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="7">

                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Academic Year 2026-27 Tuition Breakdown</span>
                        </div>
                        <table class="fee-table">
                            <thead>
                                <tr>
                                    <th>Fee Component</th>
                                    <th>Description / Eligibility</th>
                                    <th style="text-align:right;">Amount (INR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Base Annual Tuition Fee</strong></td>
                                    <td>Undergraduate B.Tech (Information Technology)</td>
                                    <td style="text-align:right; font-weight:700;">₹ 2,20,000.00</td>
                                </tr>
                                <tr>
                                    <td style="color:#059669;"><strong>V-SAT / Intermediate Merit Concession</strong></td>
                                    <td style="color:#059669;">Applicable for &gt;95% aggregate in Intermediate Board</td>
                                    <td style="text-align:right; color:#059669; font-weight:700;">- ₹ 35,000.00</td>
                                </tr>
                                <tr>
                                    <td style="color:#059669;"><strong>Early Bird Registration Waiver</strong></td>
                                    <td style="color:#059669;">Coupon code <code>27EARLYB100</code> auto-applied</td>
                                    <td style="text-align:right; color:#059669; font-weight:700;">- ₹ 5,000.00</td>
                                </tr>
                                <tr>
                                    <td style="color:#2563eb;"><strong>Stage 1 Seat Confirmation Fee Paid</strong></td>
                                    <td style="color:#2563eb;">Credited towards 1st Semester tuition installment</td>
                                    <td style="text-align:right; color:#2563eb; font-weight:700;">- ₹ 20,000.00</td>
                                </tr>
                                <tr>
                                    <td style="font-size:0.95rem; font-weight:700; color:#0f172a;">Remaining Net Annual Tuition Payable</td>
                                    <td style="font-size:0.82rem; color:#64748b;">Payable in 2 equal semester installments</td>
                                    <td style="text-align:right; font-size:1.15rem; font-weight:800; color:#059669;">₹ 1,60,000.00</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="form-grid-2">
                        <div class="section-box">
                            <h4 style="font-size:0.88rem; font-weight:700; color:#0f172a; margin-bottom:0.4rem;">Semester 1 Due</h4>
                            <p style="font-size:0.8rem; color:#64748b; margin-bottom:0.5rem;">Due at the time of campus orientation and physical document verification.</p>
                            <div style="font-size:1.1rem; font-weight:800; color:#2563eb;">₹ 80,000.00</div>
                        </div>

                        <div class="section-box">
                            <h4 style="font-size:0.88rem; font-weight:700; color:#0f172a; margin-bottom:0.4rem;">Semester 2 Due</h4>
                            <p style="font-size:0.8rem; color:#64748b; margin-bottom:0.5rem;">Due on or before November 30, 2026 before even semester commencement.</p>
                            <div style="font-size:1.1rem; font-weight:800; color:#2563eb;">₹ 80,000.00</div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 6]) }}" class="btn-step-prev">
                            &larr; Stage 6: Seat Allotment
                        </a>
                        <button type="submit" class="btn-step-next">
                            <span>Acknowledge &amp; Proceed to Stage 8 &rarr;</span>
                        </button>
                    </div>
                </form>

            <!-- ================= STAGE 8: PROVISIONAL ID & PASS ================= -->
            @elseif($activeStep === 8)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 8 of 8 &bull; Admission Pass &amp; ID</div>
                        <h3>Provisional ID &amp; Digital Admission Pass</h3>
                        <p>Congratulations! Your admission onboarding has been successfully verified.</p>
                    </div>
                    <span class="step-header-tag tag-success">✓ Allotment Active</span>
                </div>

                <div class="step-panel-body">
                    <!-- DIGITAL ID CARD PREVIEW -->
                    <div class="id-card-preview">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:1rem;">
                            <div>
                                <div style="font-size:0.68rem; font-weight:800; letter-spacing:0.06em; color:#93c5fd; text-transform:uppercase;">VFSTR &bull; Deemed to be University</div>
                                <div style="font-size:1.05rem; font-weight:800; color:#ffffff;">VIGNAN UNIVERSITY</div>
                                <div style="font-size:0.68rem; color:#cbd5e1;">NAAC A+ Accredited &bull; Vadlamudi, Guntur</div>
                            </div>
                            <img src="/images/vignan-shield-clean.png" alt="Vignan Logo" style="width:38px; height:38px; object-fit:contain;">
                        </div>

                        <div style="display:flex; gap:1.25rem; align-items:center; margin:1.25rem 0; background:rgba(255,255,255,0.06); padding:1rem; border-radius:12px; border:1px solid rgba(255,255,255,0.1);">
                            <div style="width:72px; height:72px; border-radius:10px; background:linear-gradient(135deg, #2563eb, #38bdf8); color:#ffffff; font-size:1.85rem; font-weight:800; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.2);">
                                {{ strtoupper(substr($displayName, 0, 1)) }}
                            </div>
                            <div style="display:flex; flex-direction:column; gap:0.2rem;">
                                <div style="font-size:1.05rem; font-weight:800; color:#ffffff;">{{ $displayName }}</div>
                                <div style="font-size:0.78rem; color:#7dd3fc; font-weight:700;">Appli No: {{ $appNo }}</div>
                                <div style="font-size:0.75rem; color:#e2e8f0;">{{ $programName ?: 'B.Tech' }} ({{ $branchName ?: 'Information Technology' }})</div>
                                <div style="font-size:0.72rem; color:#94a3b8;">Academic Session: 2026 - 2030</div>
                            </div>
                        </div>

                        <div style="display:flex; justify-content:space-between; align-items:center; font-size:0.75rem; color:#94a3b8; border-top:1px dashed rgba(255,255,255,0.2); padding-top:0.75rem;">
                            <span>Regulation: <strong>R25</strong></span>
                            <span>Campus: <strong>VDM Main</strong></span>
                            <span style="color:#6ee7b7; font-weight:700;">Status: ADMITTED ✓</span>
                        </div>
                    </div>

                    <!-- DOWNLOADS -->
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Download Official Signed Credentials</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="download-doc-card">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.2rem;">
                                    <div style="font-size: 1.5rem; margin-bottom: 0.15rem;">📜</div>
                                    <h4 class="kyc-doc-title" style="font-size: 0.86rem; margin: 0;">Provisional Allotment Letter</h4>
                                    <p class="kyc-doc-sub" style="font-size: 0.74rem; margin: 0.15rem 0 0 0;">Dean Admissions Signed Letter (PDF)</p>
                                </div>
                                <button type="button" onclick="alert('Downloading Provisional Admission Order PDF...')" class="btn-doc-download">
                                    <span>Download Letter ↓</span>
                                </button>
                            </div>

                            <div class="download-doc-card">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.2rem;">
                                    <div style="font-size: 1.5rem; margin-bottom: 0.15rem;">🪪</div>
                                    <h4 class="kyc-doc-title" style="font-size: 0.86rem; margin: 0;">Digital Student ID Card</h4>
                                    <p class="kyc-doc-sub" style="font-size: 0.74rem; margin: 0.15rem 0 0 0;">High-Resolution Printable Pass</p>
                                </div>
                                <button type="button" onclick="alert('Printing Digital Student ID Card...')" class="btn-doc-download">
                                    <span>Print ID Card 🖨</span>
                                </button>
                            </div>

                            <div class="download-doc-card">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.2rem;">
                                    <div style="font-size: 1.5rem; margin-bottom: 0.15rem;">🎒</div>
                                    <h4 class="kyc-doc-title" style="font-size: 0.86rem; margin: 0;">Campus Induction Booklet</h4>
                                    <p class="kyc-doc-sub" style="font-size: 0.74rem; margin: 0.15rem 0 0 0;">Hostel, Bus &amp; Academic Guide</p>
                                </div>
                                <button type="button" onclick="alert('Downloading Campus Induction Booklet...')" class="btn-doc-download">
                                    <span>Download Guide ↓</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 7]) }}" class="btn-step-prev">
                            &larr; Stage 7: Tuition Structure
                        </a>
                        <button type="button" onclick="window.print()" class="btn-step-next">
                            <span>Print Admission Dossier</span>
                        </button>
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
