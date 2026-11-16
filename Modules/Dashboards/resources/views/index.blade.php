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

        /* Sleek Step Bubble Track - Always 1 Single Row */
        .stepper-nav-bar {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: minmax(0, 1fr);
            grid-template-columns: repeat({{ count($stages) ?? 6 }}, minmax(0, 1fr));
            gap: 0.5rem;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.65rem;
            width: 100%;
        }

        .step-nav-btn {
            background: transparent;
            border: none;
            border-radius: 10px;
            padding: 0.4rem 0.15rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
            color: #64748b;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            min-width: 0;
            width: 100%;
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
                display: grid;
                grid-auto-flow: column;
                grid-auto-columns: minmax(0, 1fr);
                grid-template-columns: repeat({{ count($stages) ?? 6 }}, minmax(0, 1fr));
            }
            .form-grid-3, .form-grid-4, .kyc-docs-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 768px) {
            .stepper-nav-bar {
                display: flex;
                flex-wrap: nowrap;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                gap: 0.5rem;
                padding-bottom: 0.35rem;
            }
            .stepper-nav-bar .step-nav-btn {
                min-width: 90px;
                flex: 0 0 auto;
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

        .mockup-field-row-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem 1rem;
        }

        @media (max-width: 900px) {
            .mockup-field-row-3 {
                grid-template-columns: 1fr;
            }
            .mockup-field-row-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 640px) {
            .mockup-field-row-2 {
                grid-template-columns: 1fr;
            }
            .mockup-field-row-4 {
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
                @php
                    $isPaid = ($crmStudent?->stage1 === 'paid' || !empty($isStage1Paid));
                @endphp
                @if($appNo)
                <span class="meta-badge">
                    <strong>App ID:</strong> #{{ $appNo }}
                </span>
                @endif
                <span class="meta-badge {{ $isPaid ? 'meta-badge-success' : 'meta-badge-warning' }}">
                    &bull; {{ $isPaid ? 'Seat Confirmed' : 'Admission Fee Pending' }}
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

        @if(session('warning'))
            <div class="alert-box alert-warning" style="background: #fffbeb; border: 1px solid #fde68a; color: #92400e; padding: 0.85rem 1.15rem; border-radius: 10px; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.65rem; font-size: 0.84rem; font-weight: 600;">
                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('warning') }}</span>
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
                    <span class="stepper-step-indicator">Step {{ $activeStep }} of {{ count($stages) }}</span>
                </div>
                <div class="stepper-progress-box">
                    <div class="progress-bar-track">
                        <div class="progress-bar-fill" style="width: {{ $progressPercent }}%;"></div>
                    </div>
                    <span class="progress-percent-label">{{ $progressPercent }}% Complete</span>
                </div>
            </div>

            <!-- 5 Stages Indication Bar with Strict Sequential Locking -->
            <div class="stepper-nav-bar">
                @foreach($stages as $num => $stg)
                    @php
                        $isActive = ($activeStep === $num);
                        $isDone = ($stg['status'] === 'completed');
                        $isLocked = ($num > $maxAllowedStep);
                    @endphp
                    @if($isLocked)
                        <div class="step-nav-btn is-locked"
                             style="opacity: 0.45; cursor: not-allowed; pointer-events: none;"
                             title="Step {{ $num }}: {{ $stg['title'] }} (Locked until previous steps are completed)">
                            <div class="step-circle" style="background: #f1f5f9; color: #94a3b8; border-color: #cbd5e1;">
                                🔒
                            </div>
                            <span class="step-nav-name" style="color: #94a3b8;">{{ $stg['title'] }}</span>
                            <span class="step-nav-status-sub" style="color: #94a3b8;">Locked</span>
                        </div>
                    @else
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
                            @if($isDone)
                                <span class="step-nav-status-sub done">Completed</span>
                            @elseif($isActive)
                                <span class="step-nav-status-sub">In Progress</span>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>
        </section>

        <!-- STEP CONTENT PANEL -->
        <section class="step-panel-card">

            <!-- ================= STAGE 1: FEE PAYMENT ================= -->
            @if($activeStep === 1)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 1 of {{ count($stages) }} &bull; Seat Confirmation</div>
                        <h3>
                            <span>Seat Confirmation &amp; Admission Fee</span>
                        </h3>
                    </div>
                    @if($isPaid)
                        <span class="step-header-tag tag-success">✓ Fee Paid</span>
                    @else
                        <span class="step-header-tag tag-pending">Payment Pending</span>
                    @endif
                </div>

                <div class="step-panel-body">
                    <!-- FEE PAYMENT STATUS & ACTION -->
                    <div class="section-box" style="text-align: center; padding: 1rem 1.25rem;">
                        @if($isPaid)
                            <div style="background: linear-gradient(135deg, #f0fdf4 0%, #eff6ff 100%); border: 1.5px solid #86efac; border-radius: 14px; padding: 1.5rem 1.75rem; max-width: 640px; margin: 0 auto; box-shadow: 0 4px 16px rgba(16, 185, 129, 0.12); text-align: center;">
                                <div style="display: flex; align-items: center; justify-content: center; gap: 0.85rem; margin-bottom: 0.75rem;">
                                    <div style="width: 42px; height: 42px; border-radius: 50%; background: #16a34a; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; font-weight: 800;">
                                        ✓
                                    </div>
                                    <img src="/images/vignan-logo.png" alt="Vignan Logo" style="height: 38px; width: auto; object-fit: contain;">
                                </div>
                                <h3 style="font-size: 1.18rem; font-weight: 800; color: #0f172a; margin-bottom: 0.3rem;">
                                    Admission Registration Fee Paid &amp; Verified
                                </h3>
                                <p style="font-size: 0.82rem; color: #475569; margin-bottom: 1.25rem; line-height: 1.45;">
                                    Your provisional seat confirmation is successfully recorded in the university admissions ledger. You may now download your accounts-verified fee receipt below and proceed to fill your personal profile.
                                </p>
                                <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                                    <a href="{{ route('payu.receipt') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #0f172a; color: #ffffff; padding: 0.6rem 1.35rem; border-radius: 8px; font-size: 0.84rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);">
                                        <span>📥 Download / View Official Receipt</span>
                                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <a href="{{ route('dashboards.index', ['step' => 2]) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #2563eb; color: #ffffff; padding: 0.6rem 1.35rem; border-radius: 8px; font-size: 0.84rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                                        <span>Proceed to Step 2: Personal Profile &rarr;</span>
                                    </a>
                                </div>
                            </div>
                        @else
                            @php
                                $studentAppliNo = $crmStudent?->applinano ?? $user?->usercode ?? (string) session('student_applino', '');
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

                                @if($feeBreakdown['per_discount'] > 0)
                                    <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: #059669; margin-bottom: 0.25rem;">
                                        <span>Personal Concession ({{ $feeBreakdown['per_coupon'] }}):</span>
                                        <strong style="color: #059669;">- ₹ {{ number_format($feeBreakdown['per_discount'], 2) }}</strong>
                                    </div>
                                @endif

                                <div style="display: flex; justify-content: space-between; font-size: 0.76rem; color: #047857; margin-bottom: 0.3rem; background: #ecfdf5; padding: 3px 6px; border-radius: 6px;">
                                    <span style="font-weight: 600;">Total Concession Deducted:</span>
                                    <strong style="color: #047857;">- ₹ <span id="dispTotalConcessionVal">{{ number_format($feeBreakdown['total_discount'], 2) }}</span></strong>
                                </div>

                                <div style="display: flex; justify-content: space-between; font-size: 0.78rem; color: #64748b; margin-bottom: 0.25rem;">
                                    <span>Payment Gateway:</span>
                                    <span style="font-weight: 600; color: #059669;">{{ $feeBreakdown['net_payable'] <= 0 ? 'Coupon 100% Concession' : 'PayU Secure Hosted' }}</span>
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
                                    @if($feeBreakdown['net_payable'] <= 0)
                                        <span>✓ Confirm ₹0 Fee (100% Coupon Applied)</span>
                                    @else
                                        <span>💳 Pay ₹<span id="btnPayAmountText">{{ number_format($feeBreakdown['net_payable']) }}</span> via PayU Gateway</span>
                                    @endif
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
                                @if($isPaid)
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
                                @if($isPaid)
                                    <a href="{{ route('payu.receipt') }}" class="btn-doc-download">
                                        <span>Open Official Receipt &rarr;</span>
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
                            @if($isPaid)
                                <span class="step-header-tag tag-success">✓ Fee Paid</span>
                            @else
                                <span class="step-header-tag tag-pending">Payment Pending</span>
                            @endif
                        </div>
                        @if($isPaid)
                            <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-step-next" style="text-decoration: none; background: #2563eb; color: #ffffff; padding: 0.55rem 1.25rem; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 0.45rem;">
                                <span>Proceed to Step 2: {{ $stages[2]['title'] }} &rarr;</span>
                            </a>
                        @else
                            <button type="button" disabled class="btn-step-prev" style="opacity: 0.5; cursor: not-allowed; background: #f1f5f9; color: #94a3b8; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; gap: 0.45rem;" title="Please complete your Admission Fee Payment to unlock Step 2">
                                <span>🔒 Step 2: {{ $stages[2]['title'] }} (Locked until Fee Payment)</span>
                            </button>
                        @endif
                    </div>
                </div>

            <!-- ================= STAGE 2: PERSONAL INFO (SEPARATE STEP 2) ================= -->
            @elseif($activeStep === 2)
                <div style="padding: 1.5rem 2rem; width: 100%; box-sizing: border-box;">
                    <!-- CARD 1: 2. PERSONAL DETAILS (FULL WIDTH) -->
                    <div class="mockup-card card-active-step" style="width: 100%; box-sizing: border-box; border-radius: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <h3 class="mockup-card-title">2. Personal Details</h3>
                            @if($stages[2]['status'] === 'completed')
                                <span class="step-header-tag tag-success" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">✓ Completed</span>
                            @else
                                <span class="step-header-tag tag-pending" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">In Progress</span>
                            @endif
                        </div>

                        <form action="{{ route('dashboards.save-step') }}" method="POST">
                            @csrf
                            <input type="hidden" name="step" value="2">

                            <!-- Row 1: Candidate Name & Parent Name (2 Columns) -->
                            <div class="mockup-field-row-2">
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Candidate Name: <span style="color:#ef4444;">*</span></label>
                                    <input type="text" name="candidate_name" class="mockup-input" value="{{ $crmStudent?->name ?? $user?->name }}" placeholder="Enter Candidate Name" required>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Parent Name: <span style="color:#ef4444;">*</span></label>
                                    <input type="text" name="parent_name" class="mockup-input" value="{{ $personalInfo?->parent_name }}" placeholder="Enter Parent Name" required>
                                </div>
                            </div>

                            <!-- Row 2: Student Number & Parent Phone Number (2 Columns) -->
                            <div class="mockup-field-row-2" style="margin-top: 1rem;">
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Student Number: <span style="color:#ef4444;">*</span></label>
                                    <input type="tel" name="student_number" class="mockup-input" value="{{ substr(preg_replace('/\D/', '', (string)($crmStudent?->phoneno ?: ($personalInfo?->phoneno ?: ($user?->phnno ?: '')))), -10) }}" placeholder="Enter 10-digit Student Number" maxlength="10" pattern="[0-9]{10}" required>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Parent Phone Number:</label>
                                    <input type="tel" name="parent_phone_no" class="mockup-input" value="{{ substr(preg_replace('/\D/', '', (string)($personalInfo?->parentphono ?: '')), -10) }}" placeholder="Enter 10-digit Parent Phone Number" maxlength="10" pattern="[0-9]{10}">
                                </div>
                            </div>

                            <!-- Row 2: Date of Birth, Gender, Category (3 Columns) -->
                            <div class="mockup-field-row-3" style="margin-top: 1rem; align-items: start;">
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Date of Birth: <span style="color:#ef4444;">*</span></label>
                                    <input type="date" name="dob" class="mockup-input" value="{{ $personalInfo?->dob ? date('Y-m-d', strtotime($personalInfo->dob)) : '' }}" required>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Gender: <span style="color:#ef4444;">*</span></label>
                                    <select name="gender" class="mockup-select" required>
                                        <option value="Male" {{ ($personalInfo?->gender === 'Male' || !$personalInfo) ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ ($personalInfo?->gender === 'Female') ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ ($personalInfo?->gender === 'Other') ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Category: <span style="color:#ef4444;">*</span></label>
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
                            </div>

                            <!-- Row 3: Referee By -->
                            @php
                                $savedRefs = array_map('trim', explode(',', $personalInfo?->referedby ?? 'via advertisement'));
                            @endphp
                            <div style="margin-top: 1rem;">
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Referee By:</label>
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

                            <!-- Address Box (Order by: Country, State, District, City / Mandal / Town, D.No / Colony / Street / Village, Pincode) -->
                            <div class="mockup-subbox" style="margin-top: 1.25rem;">
                                <h4 class="mockup-subbox-title">Address</h4>
                                <div class="mockup-field-row-3">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Country: <span style="color:#ef4444;">*</span></label>
                                        <select name="country" id="geo-country-select" class="mockup-select" onchange="onCountryChange(this.value)" required>
                                            @foreach($countries as $c)
                                                <option value="{{ $c->country_id }}" data-name="{{ $c->country_name }}" {{ ($personalInfo?->country == $c->country_name || (!$personalInfo && $c->country_code === 'IN')) ? 'selected' : '' }}>
                                                    {{ $c->country_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">State: <span style="color:#ef4444;">*</span></label>
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
                                        <label class="mockup-label">District: <span style="color:#ef4444;">*</span></label>
                                        <select name="district" id="geo-district-select" class="mockup-select" required>
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
                                        <label class="mockup-label">City / Mandal / Town: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="city_mandal" class="mockup-input" value="{{ $personalInfo?->city_mandal }}" placeholder="Enter City / Mandal / Town" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">D.No / Colony / Street / Village: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="village" class="mockup-input" value="{{ $personalInfo?->village }}" placeholder="Enter D.No / Colony / Street / Village" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Pincode: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pincode" class="mockup-input" value="{{ $personalInfo?->pincode }}" placeholder="Enter 6-digit Pincode" maxlength="6" pattern="[0-9]{6}" required>
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
                                let foundSelected = false;
                                allDistrictsData.forEach(function(d) {
                                    if (!stateId || d.STATE_ID == stateId) {
                                        const opt = document.createElement('option');
                                        opt.value = d.DISTRICT_ID;
                                        opt.textContent = d.DISTRICT_NAME;
                                        if (d.DISTRICT_ID == currentSelectedDist || d.DISTRICT_NAME === '{{ $personalInfo?->district ?: "Guntur" }}') {
                                            opt.selected = true;
                                            foundSelected = true;
                                        }
                                        distSelect.appendChild(opt);
                                    }
                                });
                                if (!foundSelected && distSelect.options.length > 1) {
                                    distSelect.options[1].selected = true;
                                }
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
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                            <h3 class="mockup-card-title" id="step3-card-title" style="margin: 0;">3. Education Details</h3>
                            @if($stages[3]['status'] === 'completed')
                                <span class="step-header-tag tag-success" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">✓ Completed</span>
                            @else
                                <span class="step-header-tag tag-pending" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">In Progress</span>
                            @endif
                        </div>

                        <form action="{{ route('dashboards.save-step') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateStep3Form(this);">
                            @csrf
                            <input type="hidden" name="step" value="3">
                            <input type="hidden" name="degree_level" id="degree_level_input" value="{{ $degreeLevel }}">
                            <input type="hidden" name="eval_type" id="eval_type" value="{{ $academicDetails?->tenth_marks_type ?? 'Marks' }}">

                            <!-- ================= SUBBOX 1: 10th SCHOOL / SSC INFO ================= -->
                            <div class="mockup-subbox" id="sec-10th-details">
                                <h4 class="mockup-subbox-title">10th School / SSC Details</h4>

                                <div class="mockup-field-row-2">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">School / College Name: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="school_co_name" class="mockup-input" value="{{ $academicDetails?->tenth_school_name }}" placeholder="School / College Name" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Board of Study: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="boad_of_study" class="mockup-input" value="{{ $academicDetails?->tenth_study_board }}" placeholder="Board of Study (e.g. BSEAP, CBSE, ICSE)" required>
                                    </div>
                                </div>

                                <div class="mockup-field-row-2" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Roll No / Hall Ticket No: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="roll_no" class="mockup-input" value="{{ $academicDetails?->tenth_roll_no }}" placeholder="Enter 10th Roll / Hall Ticket No" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Passing Year: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="passing_year" class="mockup-input" value="{{ $academicDetails?->tenth_passed_year }}" placeholder="Passing Year (e.g. 2024)" min="2000" max="2030" required>
                                    </div>
                                </div>

                                <!-- EXACT DATABASE FIELDS: tenth_marks_type, tenth_marks_conducted, tenth_marks_earned, tenth_percentage -->
                                <div class="mockup-field-row-4" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Type: <span style="color:#ef4444;">*</span></label>
                                        <select name="tenth_marks_type" id="tenth_marks_type" class="mockup-select" onchange="onTenthMarksTypeChange(this.value)">
                                            <option value="Marks" {{ ($academicDetails?->tenth_marks_type === 'Marks' || $academicDetails?->tenth_marks_type === 'marks' || !$academicDetails) ? 'selected' : '' }}>Marks</option>
                                            <option value="CGPA" {{ ($academicDetails?->tenth_marks_type === 'CGPA') ? 'selected' : '' }}>CGPA</option>
                                            <option value=">= 60%" {{ ($academicDetails?->tenth_marks_type === '>= 60%') ? 'selected' : '' }}>Grade (>= 60%)</option>
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Conducted: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="tenth_marks_conducted" id="tenth_marks_conducted" class="mockup-input" value="{{ $academicDetails?->tenth_marks_conducted ?? 600 }}" placeholder="e.g. 600" oninput="calculateTenthPct()" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Earned: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="tenth_marks_earned" id="tenth_marks_earned" class="mockup-input" value="{{ $academicDetails?->tenth_marks_earned ?? $academicDetails?->tenth_marks }}" placeholder="e.g. 580" oninput="calculateTenthPct()" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Percentage (%): <span style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="tenth_percentage" id="tenth_percentage" class="mockup-input" value="{{ $academicDetails?->tenth_percentage }}" placeholder="e.g. 96.67" required>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUBBOX 2: 12th / INTERMEDIATE / DIPLOMA INFO ================= -->
                            <div class="mockup-subbox" id="sec-inter-details" style="margin-top: 1rem;">
                                <h4 class="mockup-subbox-title">12th / Intermediate / Diploma Details</h4>

                                <div class="mockup-field-row-3">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Course Choice: <span style="color:#ef4444;">*</span></label>
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
                                        <label class="mockup-label">Current Status: <span style="color:#ef4444;">*</span></label>
                                        <div class="mockup-radio-row" style="margin-top: 0.35rem;">
                                            <label class="mockup-radio-label">
                                                <input type="radio" name="subset_choice" value="First Year" {{ ($academicDetails?->pu2_current_status !== 'Second year') ? 'checked' : '' }}>
                                                <span>First Year</span>
                                            </label>
                                            <label class="mockup-radio-label">
                                                <input type="radio" name="subset_choice" value="Second year" {{ ($academicDetails?->pu2_current_status === 'Second year') ? 'checked' : '' }}>
                                                <span>Second Year</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Passing Year: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="inter_year" class="mockup-input" value="{{ $academicDetails?->pu2_passed_year ?? 2026 }}" placeholder="2026" min="2000" max="2030" required>
                                    </div>
                                </div>

                                <div class="mockup-field-row-2" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Junior College / Institute Name: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_college" class="mockup-input" value="{{ $academicDetails?->pu2_college_name }}" placeholder="Junior College / Institute Name" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Hall Ticket / Roll No: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_hallticket" class="mockup-input" value="{{ $academicDetails?->pu2_roll_no }}" placeholder="Enter Inter / Diploma Roll No" required>
                                    </div>
                                </div>

                                <!-- EXACT DATABASE FIELDS: pu2_marks_type, pu2_marks_conducted, pu2_marks_earned, pu2_percentage -->
                                <div class="mockup-field-row-4" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Type: <span style="color:#ef4444;">*</span></label>
                                        <select name="pu2_marks_type" id="pu2_marks_type" class="mockup-select" onchange="onPu2MarksTypeChange(this.value)">
                                            <option value="Marks" {{ ($academicDetails?->pu2_marks_type === 'Marks' || !$academicDetails) ? 'selected' : '' }}>Marks</option>
                                            <option value="CGPA" {{ ($academicDetails?->pu2_marks_type === 'CGPA') ? 'selected' : '' }}>CGPA</option>
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Conducted: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="pu2_marks_conducted" id="pu2_marks_conducted" class="mockup-input" value="{{ $academicDetails?->pu2_marks_conducted ?? 1000 }}" placeholder="e.g. 1000" oninput="calculatePu2Pct()" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Earned: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="pu2_marks_earned" id="pu2_marks_earned" class="mockup-input" value="{{ $academicDetails?->pu2_marks_earned ?? $academicDetails?->pu2_marks }}" placeholder="e.g. 962" oninput="calculatePu2Pct()" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Percentage (%): <span style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="pu2_percentage" id="pu2_percentage" class="mockup-input" value="{{ $academicDetails?->pu2_percentage }}" placeholder="e.g. 96.20" required>
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
                                        <input type="text" name="ug_college_name" class="mockup-input" value="{{ $academicDetails?->ug_college_name }}" placeholder="UG College / Institute Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">University / Board: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_university_board" class="mockup-input" value="{{ $academicDetails?->ug_university_board }}" placeholder="University / Board Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">UG Registration / Roll NO: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_roll_no" class="mockup-input" value="{{ $academicDetails?->ug_roll_no }}" placeholder="UG Registration / Roll Number">
                                    </div>
                                </div>

                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Passing Year: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="ug_passed_year" class="mockup-input" value="{{ $academicDetails?->ug_passed_year }}" placeholder="Passing Year (e.g. 2024)" min="2000" max="2030">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Aggregate CGPA / %: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_cgpa" class="mockup-input" value="{{ $academicDetails?->ug_cgpa }}" placeholder="e.g. 8.75 CGPA or %">
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
                                        <input type="text" name="pg_college_name" class="mockup-input" value="{{ $academicDetails?->pg_college_name }}" placeholder="PG College / University Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">University / Board: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_university_board" class="mockup-input" value="{{ $academicDetails?->pg_university_board }}" placeholder="University / Board Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">PG Roll NO / Reg No: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_roll_no" class="mockup-input" value="{{ $academicDetails?->pg_roll_no }}" placeholder="PG Roll Number / Reg No">
                                    </div>
                                </div>

                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Passing Year: <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="pg_passed_year" class="mockup-input" value="{{ $academicDetails?->pg_passed_year }}" placeholder="Passing Year (e.g. 2026)" min="2000" max="2030">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">PG CGPA / %: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_cgpa" class="mockup-input" value="{{ $academicDetails?->pg_cgpa }}" placeholder="e.g. 9.10 CGPA or %">
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
                                const hiddenInput = document.getElementById('degree_level_input');
                                if (hiddenInput) hiddenInput.value = level;

                                const ugSec = document.getElementById('sec-ug-details');
                                const pgSec = document.getElementById('sec-pg-details');

                                if (level === 'btech') {
                                    if (ugSec) ugSec.style.display = 'none';
                                    if (pgSec) pgSec.style.display = 'none';
                                } else if (level === 'mtech') {
                                    if (ugSec) ugSec.style.display = 'block';
                                    if (pgSec) pgSec.style.display = 'none';
                                } else if (level === 'phd') {
                                    if (ugSec) ugSec.style.display = 'block';
                                    if (pgSec) pgSec.style.display = 'block';
                                }
                            }

                            function calculateTenthPct() {
                                const conducted = parseFloat(document.getElementById('tenth_marks_conducted')?.value || 0);
                                const earned = parseFloat(document.getElementById('tenth_marks_earned')?.value || 0);
                                const type = document.getElementById('tenth_marks_type')?.value || 'Marks';
                                const pctInput = document.getElementById('tenth_percentage');
                                if (!pctInput) return;

                                if (type === 'CGPA') {
                                    if (earned > 0) {
                                        pctInput.value = (earned * 9.5).toFixed(2);
                                    }
                                } else {
                                    if (conducted > 0 && earned > 0) {
                                        pctInput.value = ((earned / conducted) * 100).toFixed(2);
                                    }
                                }
                            }

                            function onTenthMarksTypeChange(val) {
                                const evalIn = document.getElementById('eval_type');
                                if (evalIn) evalIn.value = val;
                                const conductedIn = document.getElementById('tenth_marks_conducted');
                                if (val === 'CGPA') {
                                    if (conductedIn) conductedIn.value = '10';
                                } else {
                                    if (conductedIn && (conductedIn.value === '10' || conductedIn.value === '10.0')) conductedIn.value = '600';
                                }
                                calculateTenthPct();
                            }

                            function calculatePu2Pct() {
                                const conducted = parseFloat(document.getElementById('pu2_marks_conducted')?.value || 0);
                                const earned = parseFloat(document.getElementById('pu2_marks_earned')?.value || 0);
                                const type = document.getElementById('pu2_marks_type')?.value || 'Marks';
                                const pctInput = document.getElementById('pu2_percentage');
                                if (!pctInput) return;

                                if (type === 'CGPA') {
                                    if (earned > 0) {
                                        pctInput.value = (earned * 9.5).toFixed(2);
                                    }
                                } else {
                                    if (conducted > 0 && earned > 0) {
                                        pctInput.value = ((earned / conducted) * 100).toFixed(2);
                                    }
                                }
                            }

                            function onPu2MarksTypeChange(val) {
                                const conductedIn = document.getElementById('pu2_marks_conducted');
                                if (val === 'CGPA') {
                                    if (conductedIn) conductedIn.value = '10';
                                } else {
                                    if (conductedIn && (conductedIn.value === '10' || conductedIn.value === '10.0')) conductedIn.value = '1000';
                                }
                                calculatePu2Pct();
                            }

                            function validateStep3Form(form) {
                                return true;
                            }

                            document.addEventListener('DOMContentLoaded', function() {
                                setDegreeLevel('{{ $degreeLevel }}');
                                if (!document.getElementById('tenth_percentage')?.value) {
                                    calculateTenthPct();
                                }
                                if (!document.getElementById('pu2_percentage')?.value) {
                                    calculatePu2Pct();
                                }
                            });
                        </script>
                    </div>
                </div>

            <!-- ================= STAGE 4: APPLICATION UPLOAD (master.menus: CRMM0006) ================= -->
            @elseif($activeStep === 4)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 4 of {{ count($stages) }} &bull; Document Verification</div>
                        <h3>{{ $stages[4]['title'] }}</h3>
                        <p>Upload and submit required academic certificates, identity documents, and photograph.</p>
                    </div>
                    @if($stages[4]['status'] === 'completed')
                        <span class="step-header-tag tag-success">✓ Uploaded</span>
                    @else
                        <span class="step-header-tag tag-pending">In Progress</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" enctype="multipart/form-data" class="step-panel-body" id="stage4-upload-form">
                    @csrf
                    <input type="hidden" name="step" value="4">
                    <input type="hidden" name="degree_level" id="stage4_degree_level_input" value="{{ $degreeLevel ?? 'btech' }}">

                    @php
                        $docDefinitions = [
                            'marksheet10' => [
                                'icon' => '📜',
                                'title' => '10th SSC Marks Memo',
                                'sub' => 'Class X original certificate or DigiLocker attested copy',
                                'levels' => 'btech mtech phd',
                                'is_common' => false,
                                'is_required' => true,
                            ],
                            'marksheet12' => [
                                'icon' => '📑',
                                'title' => '12th Intermediate / Diploma Memo',
                                'sub' => 'Short memo or internet copy with principal attestation',
                                'levels' => 'btech mtech phd',
                                'is_common' => false,
                                'is_required' => true,
                            ],
                            'btech' => [
                                'icon' => '🎓',
                                'title' => 'B.Tech / UG Degree Memo',
                                'sub' => 'Consolidated Marks Memo (CMM) or Provisional Certificate',
                                'levels' => 'mtech phd',
                                'is_common' => false,
                                'is_required' => true,
                            ],
                            'mtech' => [
                                'icon' => '📚',
                                'title' => 'M.Tech / PG Memo',
                                'sub' => 'Postgraduate Marks Memo or Degree Certificate',
                                'levels' => 'phd',
                                'is_common' => false,
                                'is_required' => true,
                            ],
                            'pg' => [
                                'icon' => '🔬',
                                'title' => 'Doctoral / Advanced PG Memo',
                                'sub' => 'Doctoral coursework or post-master qualification certificate',
                                'levels' => 'phd',
                                'is_common' => false,
                                'is_required' => true,
                            ],
                            'aadhaar' => [
                                'icon' => '🪪',
                                'title' => 'Candidate Aadhaar Card',
                                'sub' => 'Government proof of identity & native address',
                                'levels' => 'btech mtech phd',
                                'is_common' => true,
                                'is_required' => true,
                            ],
                            'photo' => [
                                'icon' => '🖼️',
                                'title' => 'Passport Size Photograph',
                                'sub' => 'Recent studio color photo with white background',
                                'levels' => 'btech mtech phd',
                                'is_common' => true,
                                'is_required' => true,
                            ],
                            'tc' => [
                                'icon' => '🏛️',
                                'title' => 'Transfer Certificate (TC)',
                                'sub' => 'Original TC from last attended Junior College / School',
                                'levels' => 'btech mtech phd',
                                'is_common' => true,
                                'is_required' => false,
                            ],
                            'study_conduct' => [
                                'icon' => '📋',
                                'title' => 'Study & Conduct Certificate',
                                'sub' => 'Bonafide certificate covering Class VI to Qualifying Exam',
                                'levels' => 'btech mtech phd',
                                'is_common' => true,
                                'is_required' => false,
                            ],
                        ];
                    @endphp

                    <div class="section-box">
                        <div class="section-box-title" style="display: flex; align-items: center; justify-content: space-between;">
                            <span>Uploaded &amp; Required Documents</span>
                            <span style="font-size: 0.72rem; color: #64748b; font-weight: 500;">Supported: PDF, JPG, PNG (Max 5MB each)</span>
                        </div>
                        <div class="kyc-docs-grid">
                            @foreach($docDefinitions as $docKey => $docMeta)
                                @php
                                    $docFile = $uploadedDocs[$docKey] ?? null;
                                    $cardLevels = explode(' ', $docMeta['levels']);
                                    $isLevelMatch = in_array($degreeLevel ?? 'btech', $cardLevels);
                                @endphp
                                <div class="kyc-doc-card doc-stream-item"
                                     data-doc-key="{{ $docKey }}"
                                     data-levels="{{ $docMeta['levels'] }}"
                                     data-required="{{ $docMeta['is_required'] ? '1' : '0' }}"
                                     id="doc-card-{{ $docKey }}"
                                     style="{{ $isLevelMatch ? 'display: flex;' : 'display: none;' }} flex-direction: column; justify-content: space-between; min-height: 155px;">
                                    <div class="kyc-doc-top">
                                        <div class="kyc-doc-icon">{{ $docMeta['icon'] }}</div>
                                        <div style="display: flex; gap: 0.35rem; align-items: center; flex-wrap: wrap;">
                                            @if($docMeta['is_common'])
                                                <span style="font-size: 0.68rem; font-weight: 700; background: #eff6ff; color: #1d4ed8; padding: 0.15rem 0.5rem; border-radius: 9999px; border: 1px solid #bfdbfe;">
                                                    Common
                                                </span>
                                            @endif
                                            @if($docFile)
                                                <span class="kyc-badge kyc-badge-verified" id="badge-{{ $docKey }}">✓ Uploaded</span>
                                            @else
                                                <span class="kyc-badge kyc-badge-pending" id="badge-{{ $docKey }}">⏳ Pending</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="kyc-doc-title">
                                            {{ $docMeta['title'] }}
                                            @if($docMeta['is_required'])
                                                <span style="color: #dc2626;" title="Mandatory field">*</span>
                                            @endif
                                        </h4>
                                        <p class="kyc-doc-sub">{{ $docMeta['sub'] }}</p>
                                    </div>
                                    @if($docFile)
                                        <div style="font-size:0.78rem; color:#059669; font-weight:700; display:flex; align-items:center; justify-content:space-between; gap:0.5rem; margin-bottom:0.4rem;">
                                            <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">File: {{ $docFile['filename'] }}</span>
                                            <a href="{{ $docFile['url'] }}" target="_blank" style="color:#1d4ed8; text-decoration:underline; flex-shrink:0;">View</a>
                                        </div>
                                    @endif
                                    <div id="file-feedback-{{ $docKey }}" style="display: none; font-size: 0.74rem; font-weight: 700; color: #15803d; margin-bottom: 0.35rem;"></div>
                                    <input type="file"
                                           name="docs[{{ $docKey }}]"
                                           id="file-input-{{ $docKey }}"
                                           accept=".pdf,.jpg,.jpeg,.png"
                                           class="field-input doc-file-input"
                                           style="padding:0.4rem; font-size:0.75rem;"
                                           onchange="handleDocFileChange(this, '{{ $docKey }}')">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 3]) }}" class="btn-step-prev">
                            &larr; Stage 3: {{ $stages[3]['title'] }}
                        </a>
                        <button type="submit" class="btn-step-next" style="background: #2563eb;">
                            <span>Save Documents &amp; Proceed to Stage 5: {{ $stages[5]['title'] }} &rarr;</span>
                        </button>
                    </div>

                    <script>
                        function setStage4DegreeLevel(level) {
                            const hiddenInput = document.getElementById('stage4_degree_level_input');
                            if (hiddenInput) hiddenInput.value = level;

                            // Filter cards: B.Tech shows only 10th, Inter, Aadhaar, Photo, TC, Conduct
                            document.querySelectorAll('.doc-stream-item').forEach(card => {
                                const levels = (card.getAttribute('data-levels') || '').split(' ');
                                if (levels.includes(level)) {
                                    card.style.display = 'flex';
                                } else {
                                    card.style.display = 'none';
                                }
                            });
                        }

                        function handleDocFileChange(input, key) {
                            const feedback = document.getElementById('file-feedback-' + key);
                            const badge = document.getElementById('badge-' + key);
                            if (!input.files || input.files.length === 0) {
                                if (feedback) feedback.style.display = 'none';
                                return;
                            }

                            const file = input.files[0];
                            const allowed = ['pdf', 'jpg', 'jpeg', 'png'];
                            const ext = file.name.split('.').pop().toLowerCase();
                            const maxSize = 5 * 1024 * 1024; // 5MB

                            if (!allowed.includes(ext)) {
                                alert('Invalid file format. Please upload a PDF, JPG, JPEG, or PNG file.');
                                input.value = '';
                                if (feedback) feedback.style.display = 'none';
                                return;
                            }

                            if (file.size > maxSize) {
                                alert('File size exceeds the 5MB limit. Please upload a smaller file.');
                                input.value = '';
                                if (feedback) feedback.style.display = 'none';
                                return;
                            }

                            if (feedback) {
                                feedback.style.display = 'block';
                                feedback.textContent = '✓ Selected: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                            }
                            if (badge) {
                                badge.className = 'kyc-badge kyc-badge-verified';
                                badge.style.background = '#dcfce7';
                                badge.style.color = '#15803d';
                                badge.textContent = '✓ Ready to Save';
                            }
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            setStage4DegreeLevel('{{ $degreeLevel ?? 'btech' }}');
                        });
                    </script>
                </form>

            <!-- ================= STAGE 5: EXAM SLOT BOOKING (master.menus: EXAM00002) ================= -->
            @elseif($activeStep === 5)
                @php
                    $now = \Carbon\Carbon::now();
                    $cutoffHour = 17; // 5:00 PM daily cutoff
                    $isBefore5PM = $now->hour < $cutoffHour;

                    // If before 5 PM: earliest available is tomorrow (14th -> 15th).
                    // If after 5 PM cutoff: earliest available is day after tomorrow (16th).
                    $minExamDate = $isBefore5PM 
                        ? $now->copy()->addDay()->format('Y-m-d') 
                        : $now->copy()->addDays(2)->format('Y-m-d');

                    $minExamDateFormatted = \Carbon\Carbon::parse($minExamDate)->format('d M Y');
                    $cutoffMessage = $isBefore5PM
                        ? 'Bookings today before 5:00 PM are scheduled for tomorrow (' . $minExamDateFormatted . ').'
                        : 'Bookings after 5:00 PM cutoff are scheduled for day after tomorrow (' . $minExamDateFormatted . ').';

                    $existingDate = $examSchedule?->examdate ? \Carbon\Carbon::parse($examSchedule->examdate)->format('Y-m-d') : null;
                    $defaultDate = ($existingDate && $existingDate >= $minExamDate) 
                        ? $existingDate 
                        : $minExamDate;

                    $currentSlot = $examSchedule?->exam_slot ?? 'S1 11:00 AM to 1:00 PM';
                    $hasBookedSlot = !empty($examSchedule?->examdate) && !empty($examSchedule?->exam_slot);
                @endphp

                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 5 of {{ count($stages) }} &bull; Entrance Examination Scheduling</div>
                        <h3>{{ $stages[5]['title'] ?? 'Exam Slot Booking' }}</h3>
                        <p>{{ $stages[5]['subtitle'] ?? 'Select your preferred date and time slot for the V-SAT entrance examination.' }}</p>
                    </div>
                    @if($hasBookedSlot)
                        <span class="step-header-tag tag-success">✓ Slot Booked</span>
                    @else
                        <span class="step-header-tag tag-pending">Booking Pending</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="5">

                    <!-- Confirmation Banner: Display when a time slot is booked -->
                    @if($hasBookedSlot)
                        <div style="background: #f0fdf4; border: 1.5px solid #86efac; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                <div style="width: 44px; height: 44px; border-radius: 10px; background: #22c55e; color: #ffffff; display: flex; align-items: center; justify-content: center; font-size: 1.35rem; flex-shrink: 0;">
                                    ✓
                                </div>
                                <div>
                                    <div style="font-size: 0.96rem; font-weight: 800; color: #166534; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                                        <span>This time slot has been booked</span>
                                        <span style="font-size: 0.72rem; font-weight: 700; background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; padding: 2px 8px; border-radius: 9999px;">
                                            Confirmed in Database
                                        </span>
                                    </div>
                                    <div style="font-size: 0.84rem; color: #334155; margin-top: 3px;">
                                        📅 Booked Date: <strong>{{ \Carbon\Carbon::parse($examSchedule->examdate)->format('d M Y') }}</strong> &bull; Selected Slot: <strong>{{ $examSchedule->exam_slot }}</strong>
                                    </div>
                                    <div style="font-size: 0.76rem; color: #64748b; margin-top: 2px;">
                                        Examination Center: <strong>{{ $campusName ?: 'Vignan Main Campus (Vadlamudi)' }}</strong> &bull; Status: Confirmed
                                    </div>
                                </div>
                            </div>
                            <div style="font-size: 0.78rem; color: #15803d; font-weight: 700; background: #ffffff; border: 1px solid #bbf7d0; padding: 5px 12px; border-radius: 8px;">
                                To change slot, select date/slot below &amp; click Confirm
                            </div>
                        </div>
                    @endif

                    <!-- Slot Booking Selection -->
                    <div class="section-box">
                        <div class="section-box-title">
                            <span>Select Exam Date &amp; Time Slot</span>
                        </div>

                        <div class="form-grid-2">
                            <!-- Date Selection with 5 PM Cutoff Enforcement -->
                            <div class="field-group">
                                <label class="field-label">Exam Date <span class="required-star">*</span></label>
                                <input type="date"
                                       id="stage5ExamDateInput"
                                       name="examdate"
                                       class="field-input"
                                       min="{{ $minExamDate }}"
                                       value="{{ $defaultDate }}"
                                       required>
                                <div style="margin-top: 6px; font-size: 0.76rem; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 6px 10px; display: flex; align-items: center; gap: 6px;">
                                    <span>⏰</span>
                                    <span><strong>5:00 PM Daily Cutoff:</strong> {{ $cutoffMessage }}</span>
                                </div>
                            </div>

                            <!-- Slot Selection (S1 11 AM - 1 PM or S2 2 PM - 4 PM) -->
                            <div class="field-group">
                                <label class="field-label">Select Slot <span class="required-star">*</span></label>
                                <select id="stage5ExamSlotSelect" name="exam_slot" class="field-input" required>
                                    <option value="S1 11:00 AM to 1:00 PM" {{ str_contains($currentSlot, 'S1') || str_contains($currentSlot, '11') ? 'selected' : '' }}>
                                        S1 11:00 AM to 1:00 PM (Morning Session)
                                    </option>
                                    <option value="S2 2:00 PM to 4:00 PM" {{ str_contains($currentSlot, 'S2') || str_contains($currentSlot, '2') ? 'selected' : '' }}>
                                        S2 2:00 PM to 4:00 PM (Afternoon Session)
                                    </option>
                                </select>
                                <div style="margin-top: 6px; font-size: 0.76rem; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 6px 10px; display: flex; align-items: center; gap: 6px;">
                                    <span>✓</span>
                                    <span id="slotLiveStatus">Standard 2-hour CBT entrance test session.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 4]) }}" class="btn-step-prev">
                            &larr; Stage 4: {{ $stages[4]['title'] ?? 'Document upload' }}
                        </a>
                        <button type="submit" class="btn-step-next" style="background: #2563eb;">
                            <span>Confirm Slot &amp; Proceed to Stage 6: {{ $stages[6]['title'] ?? 'Application Download' }} &rarr;</span>
                        </button>
                    </div>

                    <!-- Client-Side Real-Time 5 PM Cutoff Validator -->
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const dateInput = document.getElementById('stage5ExamDateInput');
                            const slotSelect = document.getElementById('stage5ExamSlotSelect');
                            const liveStatus = document.getElementById('slotLiveStatus');

                            const minAllowed = '{{ $minExamDate }}';

                            if (dateInput) {
                                dateInput.addEventListener('change', function() {
                                    if (this.value < minAllowed) {
                                        alert('Per university policy, exam dates must be scheduled with advance notice (before 5:00 PM for tomorrow, or after 5:00 PM for day after tomorrow). Earliest available date is ' + minAllowed);
                                        this.value = minAllowed;
                                    }
                                });
                            }

                            if (slotSelect && liveStatus) {
                                slotSelect.addEventListener('change', function() {
                                    if (this.value.includes('S1')) {
                                        liveStatus.textContent = 'Morning session selected: 11:00 AM to 1:00 PM. Please report 30 minutes prior.';
                                    } else {
                                        liveStatus.textContent = 'Afternoon session selected: 2:00 PM to 4:00 PM. Please report 30 minutes prior.';
                                    }
                                });
                            }
                        });
                    </script>
                </form>

            <!-- ================= STAGE 6: APPLICATION DOWNLOAD (master.menus: CRMM0010) ================= -->
            @elseif($activeStep === 6)
                @php
                    $appStudent = $applicationData ?? \App\Http\Controllers\ApplicationController::buildStudentApplicationData((string) ($appNo ?: ($user?->usercode ?? '0')));
                    $formLogo = asset('images/vignan-logo.png');
                    $paid = strtolower($appStudent->payment_status ?? '') === 'completed';
                    $dobFormatted = !empty($appStudent->dob) ? \Carbon\Carbon::parse($appStudent->dob)->format('d M Y') : 'N/A';
                    $formDocs = [
                        'marksheet10'   => '10th Marksheet',
                        'marksheet12'   => '12th Marksheet',
                        'aadhaar'       => 'Aadhaar Card',
                        'photo'         => 'Photo',
                        'tc'            => 'Transfer Cert.',
                        'study_conduct' => 'Study & Conduct',
                    ];
                    $formUploaded = (array) ($appStudent->documents ?? []);
                @endphp

                <div class="step-panel-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div class="step-panel-kicker">Stage 6 of {{ count($stages) }} &bull; Official Candidate Dossier</div>
                        <h3>Application Form Preview &amp; Download</h3>
                        <p>Review your verified application details and download the official PDF form.</p>
                    </div>
                    <div style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
                        <a href="{{ route('application.download', ['id' => $appStudent->id]) }}" target="_blank" class="btn-step-prev" style="text-decoration: none; padding: 0.55rem 1rem; font-size: 0.82rem; font-weight: 600; background: #ffffff; border: 1.5px solid #cbd5e1; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <span>🖨️ Print Form</span>
                        </a>
                        <a href="{{ route('application.download', ['id' => $appStudent->id]) }}" class="btn-step-next" style="text-decoration: none; padding: 0.55rem 1.25rem; font-size: 0.84rem; background: #16a34a; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <span>📥 Download Application PDF</span>
                        </a>
                    </div>
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body" style="padding: 1.25rem; background: #f1f5f9;">
                    @csrf
                    <input type="hidden" name="step" value="6">

                    <!-- EMBEDDED A4 ADMISSION APPLICATION FORM -->
                    <div class="embedded-a4-form" style="max-width: 820px; margin: 0 auto; background: #F4F7FB; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 42, 74, 0.15); border: 1px solid #cbd5e1; font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #1B2430;">

                        <!-- Form Header Banner -->
                        <table style="width: 100%; border-collapse: collapse; background: #0F2A4A; border-bottom: 4px solid #1E7BD8;">
                            <tr>
                                <td style="padding: 18px 24px; vertical-align: top; width: 50%;">
                                    <div style="background: #ffffff; border-radius: 8px; padding: 6px 10px; display: inline-block;">
                                        <img src="{{ $formLogo }}" alt="Vignan's Logo" style="height: 42px; display: block;">
                                    </div>
                                    <div style="color: #A9BCD6; font-size: 9px; padding-top: 8px;">
                                        Application date: {{ $appStudent->applied_at ?? now()->format('d/m/Y') }} &nbsp;|&nbsp; admissions.vignan.ac.in
                                    </div>
                                </td>
                                <td style="padding: 18px 24px; vertical-align: top; width: 50%; text-align: right;">
                                    <div style="color: #ffffff; font-size: 20px; font-weight: 800; letter-spacing: 0.5px;">APPLICATION FORM</div>
                                    <div style="color: #A9BCD6; font-size: 11px; line-height: 1.5; margin-top: 4px;">
                                        University Admission Application<br>
                                        Please read all details carefully before signing
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <div style="padding: 0 24px 20px 24px;">

                            <!-- Profile Strip -->
                            <table style="width: 100%; border-collapse: collapse; background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; margin-top: -14px; box-shadow: 0 2px 6px rgba(0,0,0,0.04);">
                                <tr>
                                    <td style="padding: 10px 14px; width: 68px; vertical-align: middle;">
                                        @if(!empty($appStudent->photo_path))
                                            <div style="width: 58px; height: 72px; border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; background: #ffffff;">
                                                <img src="{{ $appStudent->photo_path }}" alt="Photo" style="width: 58px; height: 72px; object-fit: cover; display: block;">
                                            </div>
                                        @else
                                            <div style="width: 58px; height: 72px; border: 1px dashed #6B778A; background: #F4F7FB; text-align: center; font-size: 7.5px; color: #6B778A; line-height: 1.4; padding-top: 20px; font-weight: 700; border-radius: 4px;">
                                                AFFIX<br>PASSPORT<br>PHOTO
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 10px 14px; vertical-align: middle;">
                                        <div style="font-size: 17px; font-weight: 800; color: #0F2A4A; line-height: 1.2;">{{ $appStudent->name }}</div>
                                        <div style="color: #6B778A; font-size: 11px; line-height: 1.6; margin-top: 3px;">
                                            {{ $appStudent->email }} &nbsp;|&nbsp; {{ $appStudent->phone }}<br>
                                            Applicant ID: <strong style="color: #0F2A4A;">{{ $appStudent->student_id }}</strong> (App #: {{ $appStudent->id }})
                                        </div>
                                    </td>
                                    <td style="padding: 10px 14px; vertical-align: middle; text-align: right;">
                                        <span style="display: inline-block; font-size: 8.5px; font-weight: 800; padding: 4px 10px; border-radius: 10px; margin-bottom: 4px; color: #12875B; background: #E2F5EC; border: 1px solid #a7f3d0;">
                                            PAYMENT {{ strtoupper($appStudent->payment_status ?? 'COMPLETED') }}
                                        </span><br>
                                        <span style="display: inline-block; font-size: 8.5px; font-weight: 800; padding: 4px 10px; border-radius: 10px; color: #B25E09; background: #FDF0DC; border: 1px solid #fde68a;">
                                            PROVISIONAL SEAT ALLOTMENT
                                        </span>
                                    </td>
                                </tr>
                            </table>

                            <!-- Allotment & Examination Schedule Bar -->
                            <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 10px 14px; margin-top: 10px; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.03);">
                                <div>
                                    <div style="font-size: 9px; font-weight: 700; color: #6B778A; text-transform: uppercase;">Allotted Program</div>
                                    <div style="font-size: 11.5px; font-weight: 800; color: #0F2A4A; margin-top: 2px;">
                                        {{ $appStudent->program ?: ($programName ?: 'B.Tech - Bachelor of Technology') }}
                                    </div>
                                    @if(!empty($appStudent->branch) || !empty($branchName))
                                        <div style="font-size: 10px; color: #1E7BD8; font-weight: 700;">{{ $appStudent->branch ?: $branchName }}</div>
                                    @endif
                                </div>
                                <div>
                                    <div style="font-size: 9px; font-weight: 700; color: #6B778A; text-transform: uppercase;">Allotted Campus</div>
                                    <div style="font-size: 11.5px; font-weight: 800; color: #0F2A4A; margin-top: 2px;">
                                        {{ $appStudent->campus ?: ($campusName ?: 'Vignan Main Campus (Vadlamudi)') }}
                                    </div>
                                    <div style="font-size: 10px; color: #12875B; font-weight: 700;">Provisional Allotment Confirmed</div>
                                </div>
                                <div>
                                    <div style="font-size: 9px; font-weight: 700; color: #6B778A; text-transform: uppercase;">Booked Entrance Exam Slot</div>
                                    <div style="font-size: 11.5px; font-weight: 800; color: #0F2A4A; margin-top: 2px;">
                                        📅 {{ $appStudent->exam_date ?: 'Stage 5 Scheduled' }}
                                    </div>
                                    <div style="font-size: 10px; color: #B25E09; font-weight: 700;">⏰ {{ $appStudent->exam_slot ?: 'Session Assigned' }}</div>
                                </div>
                            </div>

                            <!-- 2-Column: Personal Information & Address Information -->
                            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                <tr>
                                    <td style="width: 50%; padding-right: 6px; vertical-align: top;">
                                        <!-- 1. PERSONAL & PARENT INFORMATION -->
                                        <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px; height: 100%; box-sizing: border-box;">
                                            <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                                1. Candidate &amp; Parent Details
                                            </h4>
                                            <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                                                <tr><td style="width: 38%; color: #6B778A; padding: 3.5px 0;">Candidate Name</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->name ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Father / Parent</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->parent_name ?: ($appStudent->father_name ?: 'N/A') }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Parent Contact</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->parent_phone ?: ($appStudent->father_phone ?: 'N/A') }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Candidate Phone</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->phone ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Email ID</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0; word-break: break-all;">{{ $appStudent->email ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Date of Birth</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $dobFormatted }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Gender</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->gender ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Category</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->category ?: 'General' }}</td></tr>
                                            </table>
                                        </div>
                                    </td>
                                    <td style="width: 50%; padding-left: 6px; vertical-align: top;">
                                        <!-- 2. ADDRESS INFORMATION -->
                                        <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px; height: 100%; box-sizing: border-box;">
                                            <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                                2. Permanent &amp; Contact Address
                                            </h4>
                                            <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                                                <tr><td style="width: 38%; color: #6B778A; padding: 3.5px 0;">Door / Street</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->street ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Mandal / City</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->city ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">District</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->district ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">State</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->state ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Country</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->country ?: 'India' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">PIN Code</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->pincode ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Admission Ref</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">#{{ $appStudent->id }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 3.5px 0;">Student CRM ID</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 3.5px 0;">{{ $appStudent->student_id ?: 'N/A' }}</td></tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- 3. QUALIFYING ACADEMIC RECORDS (FULL WIDTH TABLE) -->
                            <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px; margin-top: 10px;">
                                <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                    3. Qualifying Academic Qualifications (10th SSC &amp; 12th Intermediate)
                                </h4>
                                <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                                    <thead>
                                        <tr style="background: #F8FAFC; border-bottom: 2px solid #E2E8F0; text-align: left; color: #475569; font-weight: 800;">
                                            <th style="padding: 6px 8px; width: 18%;">Standard / Level</th>
                                            <th style="padding: 6px 8px; width: 28%;">School / College Name</th>
                                            <th style="padding: 6px 8px; width: 20%;">Board / University</th>
                                            <th style="padding: 6px 8px; width: 10%; text-align: center;">Year</th>
                                            <th style="padding: 6px 8px; width: 12%;">Roll / Hall Ticket</th>
                                            <th style="padding: 6px 8px; width: 12%; text-align: right;">Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 10th Standard -->
                                        <tr style="border-bottom: 1px solid #E3E8EF;">
                                            <td style="padding: 6px 8px; font-weight: 800; color: #0F2A4A;">
                                                <span style="display: inline-block; background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; border-radius: 4px; padding: 2px 6px; font-size: 9.5px;">
                                                    10th Standard (SSC)
                                                </span>
                                            </td>
                                            <td style="padding: 6px 8px; font-weight: 700; color: #1e293b;">
                                                {{ $appStudent->academic->ssc_school ?: ($appStudent->school_name ?: 'Not Provided') }}
                                            </td>
                                            <td style="padding: 6px 8px; color: #475569;">
                                                {{ $appStudent->academic->ssc_board ?: ($appStudent->board ?: 'State Board') }}
                                            </td>
                                            <td style="padding: 6px 8px; text-align: center; font-weight: 700; color: #0F2A4A;">
                                                {{ $appStudent->academic->ssc_year ?: ($appStudent->passing_year ?: 'N/A') }}
                                            </td>
                                            <td style="padding: 6px 8px; font-weight: 700; color: #0F2A4A;">
                                                {{ $appStudent->academic->ssc_roll ?: ($appStudent->roll_number ?: 'N/A') }}
                                            </td>
                                            <td style="padding: 6px 8px; text-align: right; font-weight: 800; color: #16a34a;">
                                                {{ $appStudent->academic->ssc_percentage !== 'N/A' ? $appStudent->academic->ssc_percentage : ((float)$appStudent->percentage > 0 ? number_format((float)$appStudent->percentage, 2) . ' %' : 'N/A') }}
                                            </td>
                                        </tr>
                                        <!-- 12th / Intermediate -->
                                        <tr>
                                            <td style="padding: 6px 8px; font-weight: 800; color: #0F2A4A;">
                                                <span style="display: inline-block; background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; border-radius: 4px; padding: 2px 6px; font-size: 9.5px;">
                                                    12th / Inter (10+2)
                                                </span>
                                            </td>
                                            <td style="padding: 6px 8px; font-weight: 700; color: #1e293b;">
                                                {{ $appStudent->academic->inter_college ?: 'Junior College Records' }}
                                            </td>
                                            <td style="padding: 6px 8px; color: #475569;">
                                                {{ $appStudent->academic->inter_board ?: 'Board of Intermediate Education' }}
                                            </td>
                                            <td style="padding: 6px 8px; text-align: center; font-weight: 700; color: #0F2A4A;">
                                                {{ $appStudent->academic->inter_year ?: 'N/A' }}
                                            </td>
                                            <td style="padding: 6px 8px; font-weight: 700; color: #0F2A4A;">
                                                {{ $appStudent->academic->inter_roll ?: 'N/A' }}
                                            </td>
                                            <td style="padding: 6px 8px; text-align: right; font-weight: 800; color: #16a34a;">
                                                {{ $appStudent->academic->inter_percentage ?: 'N/A' }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- 2-Column: 4. Payment Information & 5. Verification Checklist -->
                            <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
                                <tr>
                                    <td style="width: 50%; padding-right: 6px; vertical-align: top;">
                                        <!-- 4. PAYMENT INFORMATION -->
                                        <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px; height: 100%; box-sizing: border-box;">
                                            <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                                4. Admission Registration Fee
                                            </h4>
                                            <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                                                <tr><td style="width: 38%; color: #6B778A; padding: 4px 0;">Paid Amount</td><td style="font-weight: 800; border-bottom: 1px solid #E3E8EF; padding: 4px 0; color: #15803d; font-size: 12px;">INR {{ number_format((float) $appStudent->payment_amount) }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Payment Gateway</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->payment_method }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Transaction Ref</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0; font-size: 9.5px; word-break: break-all;">{{ $appStudent->transaction_id }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Accounts Status</td>
                                                    <td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">
                                                        <span style="display: inline-block; font-size: 8.5px; font-weight: 800; padding: 2px 8px; border-radius: 8px; color: #12875B; background: #E2F5EC; border: 1px solid #a7f3d0;">
                                                            ✓ {{ strtoupper($appStudent->payment_status ?? 'COMPLETED') }} &amp; VERIFIED
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                    <td style="width: 50%; padding-left: 6px; vertical-align: top;">
                                        <!-- 5. DOCUMENTS UPLOAD SUMMARY -->
                                        <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px; height: 100%; box-sizing: border-box;">
                                            <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                                5. Document Verification Status
                                            </h4>
                                            <table style="width: 100%; border-collapse: collapse;">
                                                @foreach(array_chunk($formDocs, 2, true) as $dRow)
                                                    <tr>
                                                        @foreach($dRow as $dKey => $dLabel)
                                                            @php
                                                                $dExt = $formUploaded[$dKey] ?? null;
                                                                $isUploaded = !empty($dExt) || !empty($uploadedDocs[$dKey]);
                                                                $dType = $isUploaded ? ($dExt ?: 'VERIFIED') : 'Pending';
                                                            @endphp
                                                            <td style="width: 50%; padding: 2.5px;">
                                                                <div style="border-radius: 6px; padding: 5px 7px; font-size: 9.5px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; background: {{ $isUploaded ? '#E2F5EC' : '#FDF0DC' }}; color: #1B2430; border: 1px solid {{ $isUploaded ? '#a7f3d0' : '#fde68a' }};">
                                                                    <span>{{ $dLabel }}</span>
                                                                    <small style="color: {{ $isUploaded ? '#12875B' : '#B25E09' }}; font-weight: 800;">{{ $isUploaded ? '✓ ' . $dType : 'Pending' }}</small>
                                                                </div>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Instructions -->
                            <div style="background: #FFF8EA; border: 1px solid #F2DDB0; border-left: 4px solid #B25E09; border-radius: 10px; padding: 9px 14px; margin-top: 10px; font-size: 9.5px; line-height: 1.5; color: #1B2430;">
                                <strong style="color: #B25E09; letter-spacing: 0.4px;">INSTRUCTIONS &amp; DECLARATION</strong>
                                <ul style="margin: 3px 0 0; padding-left: 14px;">
                                    <li>This is a provisional seat allotment. Original certificates must be presented at the time of campus verification.</li>
                                    <li>Tuition fee, merit scholarships, and entrance test criteria are governed by university admissions norms.</li>
                                    <li>I hereby declare that all entries made in this application are authentic and true to the best of my knowledge.</li>
                                </ul>
                            </div>

                            <!-- Signatures -->
                            <table style="width: 100%; border-collapse: collapse; margin-top: 24px;">
                                <tr>
                                    <td style="width: 33.33%; text-align: center; padding: 0 12px; color: #6B778A; font-size: 10px;">
                                        <div style="border-top: 1px solid #6B778A; padding-top: 5px; font-weight: 700;">Signature of Parent / Guardian</div>
                                    </td>
                                    <td style="width: 33.33%; text-align: center; padding: 0 12px; color: #6B778A; font-size: 10px;">
                                        <div style="border-top: 1px solid #6B778A; padding-top: 5px; font-weight: 700;">Date: {{ date('d/m/Y') }}</div>
                                    </td>
                                    <td style="width: 33.33%; text-align: center; padding: 0 12px; color: #6B778A; font-size: 10px;">
                                        <div style="border-top: 1px solid #6B778A; padding-top: 5px; font-weight: 700;">Signature of Candidate</div>
                                    </td>
                                </tr>
                            </table>

                            <!-- For Office Use Only -->
                            <div style="border: 1px dashed #6B778A; background: #ffffff; border-radius: 8px; padding: 7px 12px; margin-top: 14px; font-size: 9.5px; color: #6B778A;">
                                <strong style="font-size: 8.5px; letter-spacing: 0.4px; display: block; margin-bottom: 6px;">FOR OFFICE USE ONLY &bull; UNIVERSITY ADMISSIONS DIRECTORATE</strong>
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 33.33%;">Application No.: <strong>{{ $appStudent->id }}</strong></td>
                                        <td style="width: 33.33%;">Verification Officer: ________________</td>
                                        <td style="width: 33.33%;">Approval Seal &amp; Date: _________</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Footer -->
                            <div style="text-align: center; font-size: 8.5px; color: #6B778A; margin-top: 12px;">
                                Vignan's Foundation for Science, Technology &amp; Research (Deemed to be University) &nbsp;|&nbsp; https://admissions.vignan.ac.in
                            </div>

                        </div>
                    </div>

                    <!-- ================= ATTACHED APPLICATION DOCUMENTS & UPLOADED CERTIFICATES ================= -->
                    @php
                        $standardSlots = [
                            'marksheet10'   => ['title' => '10th Standard Marksheet / SSC Certificate', 'required' => true, 'desc' => 'Proof of date of birth and secondary academic marks'],
                            'marksheet12'   => ['title' => '12th Standard / Intermediate Marksheet', 'required' => true, 'desc' => 'Proof of qualifying intermediate (10+2) marks memo'],
                            'aadhaar'       => ['title' => 'Aadhaar Card / Government Identity Proof', 'required' => true, 'desc' => 'Official government photo identification certificate'],
                            'photo'         => ['title' => 'Candidate Passport Photograph', 'required' => true, 'desc' => 'Recent color passport size photograph with plain background'],
                            'tc'            => ['title' => 'Transfer Certificate (T.C.)', 'required' => false, 'desc' => 'Transfer certificate issued by previous junior college / school'],
                            'study_conduct' => ['title' => 'Study & Conduct Certificate', 'required' => false, 'desc' => 'Bonafide / conduct certificate from last attended institution'],
                            'btech'         => ['title' => 'Undergraduate (UG) Degree Certificate', 'required' => false, 'desc' => 'Bachelor degree marks memo or provisional certificate'],
                        ];

                        // Merge any extra files found on disk / database
                        foreach ($uploadedDocs as $uK => $uVal) {
                            if (!isset($standardSlots[$uK])) {
                                $standardSlots[$uK] = [
                                    'title' => $uVal['title'] ?? ('Uploaded Document: ' . $uVal['filename']),
                                    'required' => false,
                                    'desc' => 'Candidate uploaded document attachment',
                                ];
                            }
                        }

                        // Collect only uploaded documents for full inline rendering
                        $actualUploadedList = [];
                        foreach ($uploadedDocs as $uK => $uVal) {
                            if (!empty($uVal['path']) || !empty($uVal['url'])) {
                                $actualUploadedList[$uK] = array_merge(
                                    $standardSlots[$uK] ?? ['title' => ucwords(str_replace('_', ' ', $uK)), 'required' => false, 'desc' => 'Uploaded document'],
                                    $uVal
                                );
                            }
                        }
                        $uploadedCount = count($actualUploadedList);
                        $docIndex = 0;
                    @endphp

                    {{-- SECTION DIVIDER HEADER --}}
                    <div style="max-width: 820px; margin: 2rem auto 0 auto; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; padding: 0.5rem 0.25rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <span style="font-size: 1.25rem;">📎</span>
                            <div>
                                <h4 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #0f172a; letter-spacing: 0.2px;">
                                    Attached Application Documents ({{ $uploadedCount }})
                                </h4>
                                <div style="font-size: 0.76rem; color: #64748b; margin-top: 2px;">
                                    Physical files attached to Application #{{ $appStudent->id }} for candidate {{ $appStudent->name }}
                                </div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <span style="display: inline-block; font-size: 0.74rem; font-weight: 700; padding: 4px 10px; border-radius: 9999px; background: #16a34a; color: #ffffff;">
                                ✓ {{ $uploadedCount }} File(s) Attached
                            </span>
                            <a href="{{ route('dashboards.index', ['step' => 4]) }}" style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.74rem; font-weight: 700; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 4px 10px; text-decoration: none;">
                                <span>📤 Add / Update Documents (Step 4)</span>
                            </a>
                        </div>
                    </div>

                    {{-- 1. FULL INLINE ATTACHED DOCUMENTS (RENDERED DIRECTLY UNDER THE MAIN FORM) --}}
                    @if($uploadedCount > 0)
                        @foreach($actualUploadedList as $slotKey => $docItem)
                            @php
                                $docIndex++;
                                $cleanExt = strtolower($docItem['ext'] ?? pathinfo($docItem['filename'] ?? '', PATHINFO_EXTENSION) ?: 'file');
                                $fileExt = strtoupper($cleanExt);
                                $fileName = $docItem['filename'] ?? basename((string)($docItem['path'] ?? ''));
                                $fileUrl = $docItem['url'] ?? asset($docItem['path'] ?? '');
                                $isImage = in_array($cleanExt, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'svg']) || !empty($docItem['is_image']);
                                $isPdf = ($cleanExt === 'pdf');
                            @endphp

                            <div class="embedded-a4-attachment" style="max-width: 820px; margin: 1.25rem auto 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 42, 74, 0.12); border: 1.5px solid #cbd5e1; font-family: Helvetica, Arial, sans-serif;">
                                <!-- Document Header -->
                                <div style="background: linear-gradient(135deg, #0F2A4A 0%, #1e3a8a 100%); padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                    <div style="display: flex; align-items: center; gap: 12px;">
                                        <div style="width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,0.15); display: flex; align-items: center; justify-content: center; font-size: 1.15rem; color: #ffffff;">
                                            @if($isImage) 🖼️ @elseif($isPdf) 📄 @else 📎 @endif
                                        </div>
                                        <div>
                                            <div style="font-size: 9px; font-weight: 800; color: #93c5fd; text-transform: uppercase; letter-spacing: 0.5px;">
                                                ATTACHED APPLICATION DOCUMENT &bull; ATTACHMENT {{ $docIndex }} OF {{ $uploadedCount }}
                                            </div>
                                            <h4 style="margin: 2px 0 0 0; color: #ffffff; font-size: 15px; font-weight: 800;">
                                                {{ $docItem['title'] }}
                                            </h4>
                                            <div style="color: #cbd5e1; font-size: 11px; margin-top: 2px;">
                                                File: <strong style="color: #ffffff;">{{ $fileName }}</strong> ({{ $fileExt }}) &bull; Application #{{ $appStudent->id }} &bull; CRM ID: {{ $appStudent->student_id }}
                                            </div>
                                        </div>
                                    </div>

                                    <div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
                                        <span style="font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; background: #22c55e; color: #ffffff; display: inline-flex; align-items: center; gap: 4px;">
                                            <span>✓ Verified &amp; Uploaded</span>
                                        </span>
                                        <a href="{{ $fileUrl }}" target="_blank" style="text-decoration: none; font-size: 11px; font-weight: 700; color: #0F2A4A; background: #ffffff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s ease;">
                                            <span>🔍 View Fullscreen</span>
                                        </a>
                                        <a href="{{ $fileUrl }}" download="{{ $fileName }}" style="text-decoration: none; font-size: 11px; font-weight: 700; color: #ffffff; background: #16a34a; border: 1px solid #15803d; border-radius: 6px; padding: 5px 12px; display: inline-flex; align-items: center; gap: 4px; transition: all 0.15s ease;">
                                            <span>📥 Download</span>
                                        </a>
                                    </div>
                                </div>

                                <!-- Document Body Preview Container -->
                                <div style="padding: 16px; background: #f8fafc; text-align: center;">
                                    @if($isImage)
                                        <div style="background: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0; display: inline-block; max-width: 100%; box-shadow: 0 4px 15px rgba(0,0,0,0.06);">
                                            <img src="{{ $fileUrl }}" alt="{{ $docItem['title'] }}" style="max-width: 100%; height: auto; max-height: 850px; display: block; margin: 0 auto; border-radius: 4px; border: 1px solid #cbd5e1;">
                                        </div>
                                    @elseif($isPdf)
                                        <div style="background: #ffffff; border-radius: 8px; border: 1px solid #cbd5e1; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.06);">
                                            <iframe src="{{ $fileUrl }}#toolbar=0" style="width: 100%; height: 800px; border: none; display: block;" title="{{ $docItem['title'] }}">
                                                <div style="padding: 2rem; text-align: center; color: #64748b;">
                                                    <p>PDF preview is not supported by your browser directly.</p>
                                                    <a href="{{ $fileUrl }}" target="_blank" style="color: #2563eb; font-weight: 700;">Click here to view PDF</a>
                                                </div>
                                            </iframe>
                                        </div>
                                    @else
                                        <div style="padding: 40px 20px; background: #ffffff; border-radius: 8px; border: 1.5px dashed #cbd5e1;">
                                            <div style="font-size: 3rem;">📄</div>
                                            <h5 style="margin: 10px 0 5px; font-size: 16px; color: #0f172a;">{{ $fileName }}</h5>
                                            <p style="font-size: 12px; color: #64748b; margin: 0 0 15px;">Document format: {{ $fileExt }} &bull; Archived with candidate admission dossier</p>
                                            <a href="{{ $fileUrl }}" target="_blank" style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-size: 13px; font-weight: 700; color: #ffffff; background: #1e40af; border-radius: 6px; padding: 8px 18px;">
                                                <span>Open Document &rarr;</span>
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                <!-- Document Verification Footer -->
                                <div style="background: #ffffff; border-top: 1px solid #e2e8f0; padding: 10px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; font-size: 10.5px; color: #64748b;">
                                    <div>
                                        Applicant: <strong>{{ $appStudent->name }}</strong> (#{{ $appStudent->id }}) &nbsp;|&nbsp; CRM ID: <strong>{{ $appStudent->student_id }}</strong>
                                    </div>
                                    <div style="text-align: right;">
                                        Vignan's Foundation for Science, Technology &amp; Research &nbsp;|&nbsp; Document Verification Ledger
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div style="max-width: 820px; margin: 1.25rem auto 0 auto; background: #ffffff; border-radius: 10px; padding: 2rem; border: 1.5px dashed #cbd5e1; text-align: center;">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">📂</div>
                            <h4 style="margin: 0 0 0.5rem 0; color: #0f172a; font-weight: 800;">No Documents Attached Yet</h4>
                            <p style="margin: 0 0 1rem 0; color: #64748b; font-size: 0.85rem;">
                                Please upload your 10th marksheet, 12th marksheet, Aadhaar card, and photo in Stage 4 so they appear here under your application form.
                            </p>
                            <a href="{{ route('dashboards.index', ['step' => 4]) }}" style="text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem; background: #2563eb; color: #ffffff; font-weight: 700; font-size: 0.84rem; padding: 0.55rem 1.2rem; border-radius: 6px;">
                                <span>📤 Go to Stage 4: Document Upload &rarr;</span>
                            </a>
                        </div>
                    @endif

                    {{-- 2. COMPACT SUMMARY CHECKLIST OF ALL SLOTS (REQUIRED & OPTIONAL) --}}
                    <div style="max-width: 820px; margin: 1.5rem auto 0 auto; background: #ffffff; border-radius: 10px; border: 1px solid #cbd5e1; overflow: hidden; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);">
                        <div style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; padding: 10px 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                            <div style="font-size: 11px; font-weight: 800; color: #0F2A4A; text-transform: uppercase; letter-spacing: 0.4px;">
                                📋 Candidate Document Dossier Checklist
                            </div>
                            <div style="font-size: 10.5px; color: #64748b;">
                                {{ $uploadedCount }} attached &bull; {{ count($standardSlots) - $uploadedCount }} pending / optional
                            </div>
                        </div>
                        <div style="padding: 10px 16px; display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 8px;">
                            @foreach($standardSlots as $sKey => $sMeta)
                                @php
                                    $hasThis = !empty($uploadedDocs[$sKey]);
                                    $thisFname = $hasThis ? ($uploadedDocs[$sKey]['filename'] ?? '') : '';
                                    $thisUrl = $hasThis ? ($uploadedDocs[$sKey]['url'] ?? '#') : '#';
                                @endphp
                                <div style="display: flex; align-items: center; justify-content: space-between; padding: 6px 10px; border-radius: 6px; background: {{ $hasThis ? '#f0fdf4' : '#f8fafc' }}; border: 1px solid {{ $hasThis ? '#86efac' : '#e2e8f0' }}; font-size: 11px;">
                                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; padding-right: 6px;">
                                        <div style="font-weight: 700; color: #0f172a;">{{ $sMeta['title'] }}</div>
                                        @if($hasThis)
                                            <div style="font-size: 9.5px; color: #166534; overflow: hidden; text-overflow: ellipsis;">📎 {{ $thisFname }}</div>
                                        @else
                                            <div style="font-size: 9.5px; color: #94a3b8;">{{ $sMeta['required'] ? 'Required document' : 'Optional document' }}</div>
                                        @endif
                                    </div>
                                    <div style="flex-shrink: 0;">
                                        @if($hasThis)
                                            <a href="{{ $thisUrl }}" target="_blank" style="text-decoration: none; font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 4px; background: #22c55e; color: #ffffff;">
                                                View
                                            </a>
                                        @else
                                            <span style="font-size: 9.5px; font-weight: 600; padding: 2px 6px; border-radius: 4px; background: #e2e8f0; color: #64748b;">
                                                Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="step-footer-actions" style="margin-top: 1.5rem;">
                        <a href="{{ route('dashboards.index', ['step' => 5]) }}" class="btn-step-prev">
                            &larr; Stage 5: {{ $stages[5]['title'] ?? 'Exam Slot Booking' }}
                        </a>
                        @if(isset($stages[7]))
                            <button type="submit" class="btn-step-next" style="background: #2563eb;">
                                <span>Proceed to Stage 7: {{ $stages[7]['title'] }} &rarr;</span>
                            </button>
                        @else
                            <button type="submit" class="btn-step-next" style="background: #16a34a;">
                                <span>✓ Complete Onboarding &amp; Confirm Submission</span>
                            </button>
                        @endif
                    </div>
                </form>

            <!-- ================= STAGE 7: V-SAT RESULTS (master.menus: CRMM0007) ================= -->
            @elseif($activeStep === 7 && isset($stages[7]))
                @php
                    $isStage7Done = ($stages[7]['status'] ?? 'pending') === 'completed';
                    $examDateFormatted = $examSchedule?->examdate ? \Carbon\Carbon::parse($examSchedule->examdate)->format('d M Y') : 'Booked in Stage 5';
                    $slotFormatted = $examSchedule?->exam_slot ?? 'Session Scheduled';
                @endphp
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 7 of {{ count($stages) }} &bull; V-SAT 2026 Examination</div>
                        <h3>{{ $stages[7]['title'] ?? 'V-SAT Results' }}</h3>
                        <p>{{ $stages[7]['subtitle'] ?? 'Official V-SAT examination scorecard and admission merit records.' }}</p>
                    </div>
                    @if($isStage7Done)
                        <span class="step-header-tag tag-success">✓ Onboarding Completed</span>
                    @else
                        <span class="step-header-tag tag-pending">Results &amp; Verification</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="7">

                    <!-- Candidate Examination Record from DB -->
                    <div class="section-box" style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1.5px solid #93c5fd; border-radius: 12px; padding: 1.25rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.25rem;">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <div style="width:44px; height:44px; border-radius:10px; background:#2563eb; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.35rem;">
                                    🏆
                                </div>
                                <div>
                                    <h4 style="margin:0; font-size:1rem; font-weight:800; color:#1e3a8a;">V-SAT 2026 National Entrance Examination</h4>
                                    <span style="font-size:0.8rem; color:#475569;">Application No: <strong>#{{ $appNo }}</strong> &bull; Candidate: <strong>{{ $displayName }}</strong></span>
                                </div>
                            </div>
                            <span class="meta-badge {{ $isStage7Done ? 'meta-badge-success' : 'meta-badge-info' }}" style="font-size:0.85rem; padding:0.4rem 0.85rem;">
                                {{ $crmStudent?->std_status ?: 'Provisional Admission Registered' }}
                            </span>
                        </div>

                        <!-- Dynamic Metrics Grid from Database -->
                        <div class="form-grid-3">
                            <div style="background:#fff; border-radius:10px; padding:0.9rem; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase;">Registered Program</div>
                                <div style="font-size:1.15rem; font-weight:800; color:#2563eb; margin-top:0.25rem;">
                                    {{ $program?->program_short_name ?: ($crmStudent?->programlevel ?: 'Undergraduate') }}
                                </div>
                                <span style="font-size:0.75rem; color:#16a34a; font-weight:700;">{{ $branchName ?: 'General Allotment' }}</span>
                            </div>
                            <div style="background:#fff; border-radius:10px; padding:0.9rem; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase;">Scheduled Slot</div>
                                <div style="font-size:1.05rem; font-weight:800; color:#0f172a; margin-top:0.25rem;">
                                    {{ $examDateFormatted }}
                                </div>
                                <span style="font-size:0.75rem; color:#475569;">{{ $slotFormatted }}</span>
                            </div>
                            <div style="background:#fff; border-radius:10px; padding:0.9rem; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase;">Admission Status</div>
                                <div style="font-size:1.15rem; font-weight:800; color:#16a34a; margin-top:0.25rem;">
                                    {{ ($crmStudent?->stage1 === 'paid') ? 'Fee Verified' : 'In Progress' }}
                                </div>
                                <span style="font-size:0.75rem; color:#15803d; font-weight:600;">Stage 7 of {{ count($stages) }} Verified</span>
                            </div>
                        </div>
                    </div>

                    <!-- Admission Dossier Summary -->
                    <div class="section-box" style="margin-top: 1.25rem;">
                        <div class="section-box-title">
                            <span>Candidate Registration Dossier Summary</span>
                        </div>
                        <div class="form-grid-3">
                            <div class="field-group">
                                <label class="field-label">Candidate Name</label>
                                <input type="text" class="field-input" value="{{ $displayName }}" readonly style="font-weight:700; background:#f8fafc;">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Registered Phone Number</label>
                                <input type="text" class="field-input" value="{{ $cleanPhone ?: ($crmStudent?->phoneno ?? 'N/A') }}" readonly style="font-weight:700; background:#f8fafc;">
                            </div>
                            <div class="field-group">
                                <label class="field-label">Selected Campus</label>
                                <input type="text" class="field-input" value="{{ $campusName ?: ($crmStudent?->campuscode ?? 'Vignan Main Campus (Vadlamudi)') }}" readonly style="font-weight:700; background:#f8fafc;">
                            </div>
                        </div>

                        <!-- University Notice regarding V-SAT merit rankings -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #16a34a; border-radius: 8px; padding: 0.9rem 1.1rem; margin-top: 1rem; font-size: 0.84rem; color: #334155; line-height: 1.55;">
                            <strong style="color: #14532d; font-weight: 800; display: block; margin-bottom: 0.3rem;">University Examination &amp; Counseling Notice:</strong>
                            <p style="margin: 0;">
                                Candidate details for Application <strong>#{{ $appNo }}</strong> have been verified and confirmed across all onboarding stages in accordance with university admissions policy. Official V-SAT entrance scores, rank percentiles, and merit scholarship category allocations are generated and published following examination slot completion.
                            </p>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 6]) }}" class="btn-step-prev">
                            &larr; Stage 6: {{ $stages[6]['title'] ?? 'Application Download' }}
                        </a>
                        <button type="submit" class="btn-step-next" style="background: #15803d;">
                            <span>✓ Complete Onboarding &amp; Finalize Registration</span>
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
