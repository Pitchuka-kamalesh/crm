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
                            @if($isActive)
                                <span class="step-nav-status-sub">In Progress</span>
                            @elseif($isDone)
                                <span class="step-nav-status-sub done">Completed</span>
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
                                    <input type="text" name="father_name" class="mockup-input" value="{{ $personalInfo?->parent_name }}" placeholder="Enter Father's Name" required>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Phone no: <span style="color:#ef4444;">*</span></label>
                                    <input type="tel" name="phone_no" class="mockup-input" value="{{ substr(preg_replace('/\D/', '', (string)($personalInfo?->parentphono ?: ($crmStudent?->phoneno ?: ($user?->phnno ?: '')))), -10) }}" placeholder="Enter 10-digit Phone Number" maxlength="10" pattern="[0-9]{10}" required>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">DOB: <span style="color:#ef4444;">*</span></label>
                                    <input type="date" name="dob" class="mockup-input" value="{{ $personalInfo?->dob ? date('Y-m-d', strtotime($personalInfo->dob)) : '' }}" required>
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
                                        <input type="text" name="city_mandal" class="mockup-input" value="{{ $personalInfo?->city_mandal }}" placeholder="Enter City / Mandal" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">village : <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="village" class="mockup-input" value="{{ $personalInfo?->village }}" placeholder="Enter Village / Street Address" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">pin code: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pin_code" class="mockup-input" value="{{ $personalInfo?->pincode }}" placeholder="Enter 6-digit Pin Code" maxlength="6" pattern="[0-9]{6}" required>
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
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                            <h3 class="mockup-card-title" id="step3-card-title" style="margin: 0;">3. Education Details: {{ $degreeLevel === 'phd' ? 'Ph.D (10th + Inter + UG + PG)' : ($degreeLevel === 'mtech' ? 'M.Tech (10th + Inter + UG)' : 'B.Tech (10th + Inter)') }}</h3>
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
                                        <input type="text" name="school_co_name" class="mockup-input" value="{{ $academicDetails?->tenth_school_name }}" placeholder="School / College Name" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Boad of study : <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="boad_of_study" class="mockup-input" value="{{ $academicDetails?->tenth_study_board }}" placeholder="Board of Study (e.g. BSEAP, CBSE, ICSE)" required>
                                    </div>
                                </div>

                                <div class="mockup-field-row-3" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Roll NO : <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="roll_no" class="mockup-input" value="{{ $academicDetails?->tenth_roll_no }}" placeholder="Enter 10th Roll / Hall Ticket No" required>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">passing year : <span style="color:#ef4444;">*</span></label>
                                        <input type="number" name="passing_year" class="mockup-input" value="{{ $academicDetails?->tenth_passed_year }}" placeholder="Passing Year (e.g. 2024)" min="2000" max="2030" required>
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
                                            <input type="number" id="eval_cgpa_field" name="cgpa_value" class="mockup-input" style="padding: 0.4rem 0.65rem;" step="0.01" min="1.0" max="10.0" value="{{ $academicDetails?->tenth_cgpa }}" placeholder="Enter CGPA (e.g. 9.80)" oninput="evaluateScholarshipFromInput()">
                                        </div>
                                        <div id="marks-input-box" style="display: {{ ($academicDetails?->tenth_marks_type !== 'CGPA') ? 'block' : 'none' }}; margin-top: 0.35rem;">
                                            <input type="number" id="eval_marks_field" name="marks_value" class="mockup-input" style="padding: 0.4rem 0.65rem;" step="0.1" min="0" max="1000" value="{{ $academicDetails?->tenth_marks }}" placeholder="Enter Marks (e.g. 590 or 85%)" oninput="evaluateScholarshipFromInput()">
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
                                        <input type="number" name="inter_year" class="mockup-input" value="{{ $academicDetails?->pu2_passed_year ?? '' }}" placeholder="2026" min="2000" max="2030">
                                    </div>
                                </div>

                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Junior College / Institute Name: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_college" class="mockup-input" value="{{ $academicDetails?->pu2_college_name }}" placeholder="Junior College / Institute Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Hall Ticket / Roll No: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_hallticket" class="mockup-input" value="{{ $academicDetails?->pu2_roll_no }}" placeholder="Enter Inter / Diploma Roll No">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Inter Total Marks / CGPA: <span style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_marks" class="mockup-input" value="{{ $academicDetails?->pu2_marks }}" placeholder="Total Marks / CGPA (e.g. 962 or 9.5)">
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
                                const titleEl = document.getElementById('step3-card-title');

                                if (level === 'btech') {
                                    if (ugSec) ugSec.style.display = 'none';
                                    if (pgSec) pgSec.style.display = 'none';
                                    if (indicator) indicator.textContent = '10th + Inter';
                                    if (titleEl) titleEl.textContent = '3. Education Details: B.Tech (10th + Inter)';
                                } else if (level === 'mtech') {
                                    if (ugSec) ugSec.style.display = 'block';
                                    if (pgSec) pgSec.style.display = 'none';
                                    if (indicator) indicator.textContent = '10th + Inter + B.Tech (UG)';
                                    if (titleEl) titleEl.textContent = '3. Education Details: M.Tech (10th + Inter + UG)';
                                } else if (level === 'phd') {
                                    if (ugSec) ugSec.style.display = 'block';
                                    if (pgSec) pgSec.style.display = 'block';
                                    if (indicator) indicator.textContent = '10th + Inter + UG + PG';
                                    if (titleEl) titleEl.textContent = '3. Education Details: Ph.D (10th + Inter + UG + PG)';
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
                                setDegreeLevel('{{ $degreeLevel }}');
                                const activeEval = document.querySelector('input[name="eval_type"]:checked')?.value || 'marks';
                                onEvalTypeChange(activeEval);
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

                    <!-- Degree Stream Filter Selector for Stage 4 Documents -->
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.15rem; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 0.75rem 1rem;">
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-size: 0.82rem; font-weight: 700; color: #1e293b;">Degree Stream:</span>
                            <button type="button" class="degree-pill-btn {{ ($degreeLevel ?? 'btech') === 'btech' ? 'active' : '' }}" id="stage4-pill-btech" onclick="setStage4DegreeLevel('btech')">
                                🎓 B.Tech &bull; 10th + Inter
                            </button>
                            <button type="button" class="degree-pill-btn {{ ($degreeLevel ?? 'btech') === 'mtech' ? 'active' : '' }}" id="stage4-pill-mtech" onclick="setStage4DegreeLevel('mtech')">
                                🎓 M.Tech &bull; 10th + Inter + B.Tech (UG)
                            </button>
                            <button type="button" class="degree-pill-btn {{ ($degreeLevel ?? 'btech') === 'phd' ? 'active' : '' }}" id="stage4-pill-phd" onclick="setStage4DegreeLevel('phd')">
                                🎓 Ph.D &bull; 10th + Inter + UG + PG
                            </button>
                            <input type="hidden" name="degree_level" id="stage4_degree_level_input" value="{{ $degreeLevel ?? 'btech' }}">
                        </div>
                        <span id="stage4-degree-badge-indicator" class="mockup-pill-badge" style="font-size: 0.76rem; padding: 0.25rem 0.7rem; background: #0f2744; color: #fff;">
                            {{ ($degreeLevel ?? 'btech') === 'phd' ? 'Ph.D: 10th + Inter + UG + PG' : (($degreeLevel ?? 'btech') === 'mtech' ? 'M.Tech: 10th + Inter + B.Tech (UG)' : 'B.Tech: 10th + Inter') }}
                        </span>
                    </div>

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
                                @endphp
                                <div class="kyc-doc-card doc-stream-item"
                                     data-doc-key="{{ $docKey }}"
                                     data-levels="{{ $docMeta['levels'] }}"
                                     data-required="{{ $docMeta['is_required'] ? '1' : '0' }}"
                                     id="doc-card-{{ $docKey }}">
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
                            document.querySelectorAll('[id^="stage4-pill-"]').forEach(btn => {
                                btn.classList.remove('active');
                            });
                            const activeBtn = document.getElementById('stage4-pill-' + level);
                            if (activeBtn) activeBtn.classList.add('active');

                            const hiddenInput = document.getElementById('stage4_degree_level_input');
                            if (hiddenInput) hiddenInput.value = level;

                            const badge = document.getElementById('stage4-degree-badge-indicator');
                            if (badge) {
                                if (level === 'btech') badge.textContent = 'B.Tech: 10th + Inter';
                                else if (level === 'mtech') badge.textContent = 'M.Tech: 10th + Inter + B.Tech (UG)';
                                else if (level === 'phd') badge.textContent = 'Ph.D: 10th + Inter + UG + PG';
                            }

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

            <!-- ================= STAGE 5: APPLICATION DOWNLOAD (master.menus: CRMM0010) ================= -->
            @elseif($activeStep === 5)
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
                        <div class="step-panel-kicker">Stage 5 of {{ count($stages) }} &bull; Official Candidate Dossier</div>
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
                    <input type="hidden" name="step" value="5">

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

                            <!-- 2x2 Information Grid -->
                            <table style="width: 100%; border-collapse: collapse; margin-top: 12px;">
                                <tr>
                                    <td style="width: 50%; padding-right: 6px; vertical-align: top;">
                                        <!-- 1. PERSONAL INFORMATION -->
                                        <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px;">
                                            <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                                1. Personal Information
                                            </h4>
                                            <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                                                <tr><td style="width: 38%; color: #6B778A; padding: 4px 0;">Full name</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->name ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Email</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0; word-break: break-all;">{{ $appStudent->email ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Phone</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->phone ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Date of birth</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $dobFormatted }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Gender</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->gender ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Category</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->category ?: 'N/A' }}</td></tr>
                                            </table>
                                        </div>
                                    </td>
                                    <td style="width: 50%; padding-left: 6px; vertical-align: top;">
                                        <!-- 2. EDUCATIONAL INFORMATION -->
                                        <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px;">
                                            <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                                2. Educational Information
                                            </h4>
                                            <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                                                <tr><td style="width: 38%; color: #6B778A; padding: 4px 0;">School</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->school_name ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Board</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->board ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Percentage</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0; color: #16a34a;">{{ (float)$appStudent->percentage > 0 ? number_format((float) $appStudent->percentage, 2) . ' %' : 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Passing year</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->passing_year ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Roll number</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->roll_number ?: 'N/A' }}</td></tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                                <tr><td colspan="2" style="height: 10px;"></td></tr>
                                <tr>
                                    <td style="width: 50%; padding-right: 6px; vertical-align: top;">
                                        <!-- 3. ADDRESS INFORMATION -->
                                        <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px;">
                                            <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                                3. Address Information
                                            </h4>
                                            <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                                                <tr><td style="width: 38%; color: #6B778A; padding: 4px 0;">Street</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->street ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">City</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->city ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">State</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->state ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Pincode</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->pincode ?: 'N/A' }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Country</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->country ?: 'N/A' }}</td></tr>
                                            </table>
                                        </div>
                                    </td>
                                    <td style="width: 50%; padding-left: 6px; vertical-align: top;">
                                        <!-- 4. PAYMENT INFORMATION -->
                                        <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px;">
                                            <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                                4. Payment Information
                                            </h4>
                                            <table style="width: 100%; border-collapse: collapse; font-size: 10.5px;">
                                                <tr><td style="width: 38%; color: #6B778A; padding: 4px 0;">Amount</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">INR {{ number_format((float) $appStudent->payment_amount) }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Method</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">{{ $appStudent->payment_method }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Transaction ID</td><td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0; font-size: 9.5px; word-break: break-all;">{{ $appStudent->transaction_id }}</td></tr>
                                                <tr><td style="color: #6B778A; padding: 4px 0;">Status</td>
                                                    <td style="font-weight: 700; border-bottom: 1px solid #E3E8EF; padding: 4px 0;">
                                                        <span style="display: inline-block; font-size: 8.5px; font-weight: 800; padding: 2px 8px; border-radius: 8px; color: #12875B; background: #E2F5EC; border: 1px solid #a7f3d0;">
                                                            {{ strtoupper($appStudent->payment_status ?? 'COMPLETED') }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- 5. DOCUMENTS UPLOADED -->
                            <div style="background: #ffffff; border: 1px solid #E3E8EF; border-radius: 10px; padding: 12px 14px; margin-top: 10px;">
                                <h4 style="margin: 0 0 8px; padding: 0 0 6px 8px; font-size: 11.5px; font-weight: 800; letter-spacing: 0.4px; text-transform: uppercase; color: #0F2A4A; border-left: 4px solid #1E7BD8; border-bottom: 1px solid #E3E8EF;">
                                    5. Documents Uploaded
                                </h4>
                                <table style="width: 100%; border-collapse: collapse;">
                                    @foreach(array_chunk($formDocs, 3, true) as $dRow)
                                        <tr>
                                            @foreach($dRow as $dKey => $dLabel)
                                                @php
                                                    $dExt = $formUploaded[$dKey] ?? null;
                                                    $isUploaded = !empty($dExt);
                                                    $dType = $isUploaded ? $dExt : 'Pending';
                                                @endphp
                                                <td style="width: 33.33%; padding: 3px;">
                                                    <div style="border-radius: 7px; padding: 6px 8px; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: space-between; background: {{ $isUploaded ? '#E2F5EC' : '#FDF0DC' }}; color: #1B2430; border: 1px solid {{ $isUploaded ? '#a7f3d0' : '#fde68a' }};">
                                                        <span>{{ $dLabel }}</span>
                                                        <small style="color: {{ $isUploaded ? '#12875B' : '#B25E09' }}; font-weight: 800;">{{ $dType }}</small>
                                                    </div>
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </table>
                            </div>

                            <!-- Instructions -->
                            <div style="background: #FFF8EA; border: 1px solid #F2DDB0; border-left: 4px solid #B25E09; border-radius: 10px; padding: 9px 14px; margin-top: 10px; font-size: 9.5px; line-height: 1.5; color: #1B2430;">
                                <strong style="color: #B25E09; letter-spacing: 0.4px;">INSTRUCTIONS</strong>
                                <ul style="margin: 3px 0 0; padding-left: 14px;">
                                    <li>This is a provisional seat allotment. Original documents must be submitted at the time of joining.</li>
                                    <li>Tuition fee and scholarship eligibility are subject to university norms.</li>
                                    <li>All information is valid as of the approval date.</li>
                                </ul>
                            </div>

                            <!-- Signatures -->
                            <table style="width: 100%; border-collapse: collapse; margin-top: 28px;">
                                <tr>
                                    <td style="width: 33.33%; text-align: center; padding: 0 12px; color: #6B778A; font-size: 10px;">
                                        <div style="border-top: 1px solid #6B778A; padding-top: 5px;">Signature of Parent</div>
                                    </td>
                                    <td style="width: 33.33%; text-align: center; padding: 0 12px; color: #6B778A; font-size: 10px;">
                                        <div style="border-top: 1px solid #6B778A; padding-top: 5px;">Date</div>
                                    </td>
                                    <td style="width: 33.33%; text-align: center; padding: 0 12px; color: #6B778A; font-size: 10px;">
                                        <div style="border-top: 1px solid #6B778A; padding-top: 5px;">Signature of Student</div>
                                    </td>
                                </tr>
                            </table>

                            <!-- For Office Use Only -->
                            <div style="border: 1px dashed #6B778A; background: #ffffff; border-radius: 8px; padding: 7px 12px; margin-top: 16px; font-size: 9.5px; color: #6B778A;">
                                <strong style="font-size: 8.5px; letter-spacing: 0.4px; display: block; margin-bottom: 6px;">FOR OFFICE USE ONLY</strong>
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 33.33%;">Application No.: <strong>{{ $appStudent->id }}</strong></td>
                                        <td style="width: 33.33%;">Verified by: ______________</td>
                                        <td style="width: 33.33%;">Date: _________</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- Footer -->
                            <div style="text-align: center; font-size: 8.5px; color: #6B778A; margin-top: 14px;">
                                Vignan's Foundation for Science, Technology &amp; Research (Deemed to be University) &nbsp;|&nbsp; https://admissions.vignan.ac.in
                            </div>

                        </div>
                    </div>

                    <div class="step-footer-actions" style="margin-top: 1.5rem;">
                        <a href="{{ route('dashboards.index', ['step' => 4]) }}" class="btn-step-prev">
                            &larr; Stage 4: {{ $stages[4]['title'] ?? 'Application upload' }}
                        </a>
                        @if(isset($stages[6]))
                            <button type="submit" class="btn-step-next" style="background: #2563eb;">
                                <span>Proceed to Stage 6: {{ $stages[6]['title'] }} &rarr;</span>
                            </button>
                        @else
                            <button type="submit" class="btn-step-next" style="background: #16a34a;">
                                <span>✓ Complete Onboarding &amp; Confirm Submission</span>
                            </button>
                        @endif
                    </div>
                </form>

            <!-- ================= STAGE 6: OPTIONAL SUBSEQUENT STAGE ================= -->
            @elseif($activeStep === 6 && isset($stages[6]))
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 6 of {{ count($stages) }} &bull; V-SAT 2026 Examination</div>
                        <h3>{{ $stages[6]['title'] ?? 'VSAT Result' }} &amp; Merit Scorecard</h3>
                        <p>Official V-SAT (Vignan Scholastic Aptitude Test) scorecard, rank percentile, and merit scholarship eligibility.</p>
                    </div>
                    @if(($stages[6]['status'] ?? 'pending') === 'completed')
                        <span class="step-header-tag tag-success">✓ Verified</span>
                    @else
                        <span class="step-header-tag tag-success">✓ Merit Qualified</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="6">

                    <!-- Official VSAT Merit Card -->
                    <div class="section-box" style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1.5px solid #93c5fd; border-radius: 12px; padding: 1.25rem;">
                        <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.25rem;">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <div style="width:44px; height:44px; border-radius:10px; background:#2563eb; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.35rem;">
                                    🏆
                                </div>
                                <div>
                                    <h4 style="margin:0; font-size:1rem; font-weight:800; color:#1e3a8a;">V-SAT 2026 National Entrance Examination</h4>
                                    <span style="font-size:0.8rem; color:#475569;">Hall Ticket: <strong>VSAT26-{{ $appNo ?: '' }}</strong> &bull; Exam Mode: Online Computer Based</span>
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
                                <input type="text" name="father_name" class="field-input" value="{{ $personalInfo?->parent_name }}" placeholder="Enter Father's Name" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Father's Contact Number <span class="required-star">*</span></label>
                                <input type="tel" name="father_phone" class="field-input" value="{{ $personalInfo?->parentphono }}" placeholder="Enter Father's 10-digit Phone" required>
                            </div>
                            <div class="field-group">
                                <label class="field-label">Mother's Name</label>
                                <input type="text" name="mother_name" class="field-input" value="{{ $personalInfo?->mother_name ?? '' }}" placeholder="Enter Mother's Name">
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 5]) }}" class="btn-step-prev">
                            &larr; Stage 5: {{ $stages[5]['title'] }}
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
