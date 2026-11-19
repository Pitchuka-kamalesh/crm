<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Admission Onboarding Portal | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-shield-clean.png">

    <!-- Immediate Theme Preloader (Prevents Theme Flash & Syncs Palette across all menus) -->
    <script>
        (function(){
            var color = localStorage.getItem('crm_theme_color') || 'maroon';
            if (color === 'red') color = 'maroon';
            var mode = localStorage.getItem('crm_theme_mode') || 'light';
            document.documentElement.setAttribute('data-theme-color', color);
            document.documentElement.setAttribute('data-theme-mode', mode);
        })();
    </script>

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

        /* Dynamic Color Themes from Palette (Purple, Blue, Green, Maroon) */
        [data-theme-color="purple"] {
            --brand-primary: #5b21b6;
            --brand-accent: #7c3aed;
            --brand-sky: #8b5cf6;
        }

        [data-theme-color="blue"] {
            --brand-primary: #1e3a8a;
            --brand-accent: #2563eb;
            --brand-sky: #0ea5e9;
        }

        [data-theme-color="green"] {
            --brand-primary: #065f46;
            --brand-accent: #059669;
            --brand-sky: #10b981;
        }

        [data-theme-color="red"],
        [data-theme-color="maroon"] {
            --brand-primary: #4a000e;
            --brand-accent: #670014;
            --brand-sky: #9b112c;
        }

        /* Nav Theme Controls Capsule */
        .nav-theme-controls-capsule {
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 9999px;
            padding: 4px 8px 4px 10px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
            margin-right: 0.75rem;
        }

        .nav-theme-color-palette {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .nav-theme-color-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: none;
            padding: 0;
            cursor: pointer;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .nav-theme-color-dot:hover {
            transform: scale(1.12);
        }

        .nav-theme-color-dot.dot-maroon {
            background: #670014;
            --dot-color: #670014;
        }

        .nav-theme-color-dot.dot-blue {
            background: #2563eb;
            --dot-color: #2563eb;
        }

        .nav-theme-color-dot.dot-green {
            background: #059669;
            --dot-color: #059669;
        }

        .nav-theme-color-dot.dot-purple {
            background: #7c3aed;
            --dot-color: #7c3aed;
        }

        .nav-theme-color-dot .theme-dot-check {
            display: none;
            color: #ffffff;
            font-size: 10px;
            font-weight: 900;
            line-height: 1;
        }

        .nav-theme-color-dot.is-active {
            box-shadow: 0 0 0 2px #f1f5f9, 0 0 0 4px var(--dot-color);
        }

        .nav-theme-color-dot.is-active .theme-dot-check {
            display: flex;
            align-items: center;
            justify-content: center;
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
            padding: 0 1.5rem;
            height: 66px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-cluster {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
        }

        .brand-logo-img {
            width: 40px;
            height: 40px;
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
            white-space: nowrap;
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
            white-space: nowrap;
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
            border-top: 1px solid #e2e8f0;
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
            gap: 1rem;
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
            .dash-footer {
                padding: 1.15rem 1rem;
                text-align: center;
                margin-top: 1.25rem;
            }
            .footer-inner {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                gap: 0.45rem;
                font-size: 0.75rem;
                line-height: 1.5;
            }
            .footer-inner > div {
                text-align: center;
            }
        }

        /* Dedicated Stage Form Wrapper for consistent mobile padding */
        .stage-form-wrapper {
            padding: 1.5rem 2rem;
            width: 100%;
            box-sizing: border-box;
        }

        .embedded-a4-form-wrapper {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 10px;
        }

        .mobile-a4-scroll-hint {
            display: none;
        }

        .btn-label-mobile {
            display: none;
        }
        .btn-label-desktop {
            display: inline;
        }

        .stage-action-row {
            margin-top: 1.5rem;
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            align-items: center;
        }

        @media (max-width: 640px) {
            .stage-form-wrapper {
                padding: 0.65rem 0.5rem !important;
            }
            .dash-container {
                padding: 0.5rem 0.5rem 2rem !important;
                gap: 0.5rem;
            }
            .dash-nav {
                position: sticky;
                top: 0;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
            }
            .nav-inner {
                padding: 0.6rem 0.75rem !important;
                height: auto !important;
                min-height: 60px !important;
            }
            .brand-cluster {
                gap: 0.45rem !important;
            }
            .brand-logo-img {
                width: 34px !important;
                height: 34px !important;
                flex-shrink: 0 !important;
            }
            .brand-name-main {
                font-size: 0.98rem !important;
                font-weight: 800 !important;
                white-space: nowrap !important;
            }
            .brand-tag {
                font-size: 0.62rem !important;
                padding: 0.1rem 0.4rem !important;
                white-space: nowrap !important;
            }
            .brand-accred-sub {
                display: none !important;
            }
            .user-nav-cluster {
                gap: 0.4rem !important;
            }
            .user-name-title {
                max-width: 90px !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                font-size: 0.8rem !important;
                font-weight: 700 !important;
            }
            .user-meta-sub {
                font-size: 0.64rem !important;
                font-weight: 600 !important;
                white-space: nowrap !important;
            }
            .user-avatar-circle {
                width: 32px !important;
                height: 32px !important;
                font-size: 0.8rem !important;
                flex-shrink: 0 !important;
            }
            .btn-nav-action {
                padding: 0.3rem 0.45rem !important;
                font-size: 0.72rem !important;
                flex-shrink: 0 !important;
            }
            .btn-signout svg {
                width: 15px !important;
                height: 15px !important;
            }
            .btn-signout span {
                display: none;
            }

            .welcome-hero {
                flex-direction: column;
                align-items: flex-start;
                padding: 0.85rem 1rem !important;
                gap: 0.55rem !important;
                border-radius: 12px !important;
            }
            .welcome-hero-left {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 0.3rem;
            }
            .welcome-heading {
                font-size: 1.1rem !important;
                line-height: 1.3 !important;
                margin: 0;
            }
            .welcome-subtitle {
                font-size: 0.76rem !important;
                line-height: 1.4 !important;
                color: #475569 !important;
            }
            .meta-badges-cluster {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 0.4rem;
            }
            .meta-badge {
                font-size: 0.7rem !important;
                padding: 0.22rem 0.6rem !important;
                border-radius: 9999px;
            }

            .stepper-card {
                padding: 0.75rem 0.85rem !important;
                border-radius: 12px !important;
            }
            .stepper-header {
                display: flex !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.45rem !important;
                margin-bottom: 0.65rem !important;
            }
            .stepper-title-box {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                width: 100% !important;
            }
            .stepper-title-box h2 {
                font-size: 0.88rem !important;
                font-weight: 700;
                color: #0f172a;
                margin: 0;
            }
            .stepper-step-indicator {
                font-size: 0.68rem !important;
                padding: 0.12rem 0.5rem !important;
                font-weight: 600;
            }
            .stepper-progress-box {
                display: flex !important;
                align-items: center !important;
                justify-content: space-between !important;
                width: 100% !important;
                gap: 0.65rem !important;
            }
            .progress-bar-track {
                flex: 1 !important;
                width: auto !important;
                height: 6px !important;
                background: #f1f5f9;
                border-radius: 9999px;
            }
            .progress-percent-label {
                font-size: 0.72rem !important;
                font-weight: 700 !important;
                color: #475569 !important;
                white-space: nowrap !important;
            }
            .stepper-nav-bar {
                display: flex !important;
                flex-wrap: nowrap !important;
                overflow-x: auto !important;
                -webkit-overflow-scrolling: touch !important;
                scrollbar-width: none !important;
                -ms-overflow-style: none !important;
                gap: 0.5rem !important;
                padding: 0.5rem 0.2rem 0.25rem !important;
                border-top: 1px solid #f1f5f9;
            }
            .stepper-nav-bar::-webkit-scrollbar {
                display: none !important;
                width: 0 !important;
                height: 0 !important;
            }
            .stepper-nav-bar .step-nav-btn {
                min-width: 82px !important;
                max-width: 95px !important;
                padding: 0.35rem 0.15rem !important;
                flex: 0 0 auto !important;
            }
            .step-circle {
                width: 30px !important;
                height: 30px !important;
                font-size: 0.75rem !important;
            }
            .step-nav-name {
                font-size: 0.7rem !important;
                line-height: 1.2 !important;
                white-space: normal !important;
                word-break: normal !important;
                text-overflow: clip !important;
                max-width: 100% !important;
                text-align: center !important;
                margin-top: 0.25rem !important;
            }
            .step-nav-status-sub {
                font-size: 0.62rem !important;
                margin-top: 0.1rem !important;
            }

            .step-panel-card {
                border-radius: 10px;
            }
            .step-panel-header {
                padding: 0.75rem 0.85rem !important;
                flex-direction: column;
                align-items: flex-start;
                gap: 0.45rem;
            }
            .step-panel-header h3 {
                font-size: 0.98rem !important;
            }
            .step-panel-body {
                padding: 0.85rem 0.75rem !important;
            }

            .mockup-card {
                padding: 1rem 0.85rem 1.35rem !important;
                border-radius: 14px !important;
            }
            .mockup-card-title {
                font-size: 0.98rem !important;
            }
            .mockup-subbox {
                padding: 0.75rem 0.65rem !important;
                border-radius: 10px !important;
                gap: 0.6rem;
            }
            .mockup-subbox-title {
                font-size: 0.88rem !important;
            }

            .mockup-input, .mockup-select, .field-input, .field-select, .field-textarea {
                font-size: 16px !important; /* Prevents auto zoom in iOS Safari */
                padding: 0.55rem 0.75rem !important;
            }

            .mockup-radio-grid {
                grid-template-columns: 1fr !important;
                gap: 0.4rem;
            }
            .mockup-radio-row {
                flex-wrap: wrap;
                gap: 0.4rem 0.75rem;
            }

            .kyc-docs-grid {
                grid-template-columns: 1fr !important;
                gap: 0.75rem;
            }
            .kyc-doc-card, .download-doc-card {
                padding: 0.85rem 0.75rem !important;
                width: 100%;
                box-sizing: border-box;
            }

            .step-footer-actions {
                flex-direction: column-reverse;
                align-items: stretch;
                gap: 0.65rem;
                padding-top: 0.85rem;
            }
            .step-footer-actions .btn-step-prev,
            .step-footer-actions .btn-step-next,
            .step-footer-actions .btn-save-step,
            .step-footer-actions button[type="submit"] {
                width: 100%;
                justify-content: center;
                min-height: 44px;
                text-align: center;
                box-sizing: border-box;
            }
            #payuSubmitForm {
                width: 100%;
                display: block;
            }
            #btnPayUGateway {
                width: 100% !important;
                justify-content: center !important;
                min-height: 44px !important;
                box-sizing: border-box;
            }

            .fee-table {
                font-size: 0.8rem;
            }
            .fee-table th, .fee-table td {
                padding: 0.65rem 0.5rem;
            }

            .embedded-a4-form {
                min-width: 680px;
            }

            .mobile-a4-scroll-hint {
                display: flex !important;
                align-items: center;
                justify-content: center;
                gap: 0.4rem;
                background: #e0f2fe;
                color: #0369a1;
                font-size: 0.74rem;
                font-weight: 700;
                padding: 6px 10px;
                border-radius: 8px;
                margin-bottom: 0.75rem;
                border: 1px solid #bae6fd;
                text-align: center;
            }

            .stage6-header-actions {
                width: 100%;
                display: grid !important;
                grid-template-columns: 1fr 1fr;
                gap: 0.5rem;
                margin-top: 0.5rem;
            }
            .stage6-header-actions a {
                width: 100%;
                justify-content: center !important;
                text-align: center !important;
                padding: 0.55rem 0.5rem !important;
                font-size: 0.78rem !important;
                border-radius: 8px !important;
                box-sizing: border-box !important;
            }

            .stage6-panel-body {
                padding: 0.75rem 0.5rem !important;
            }

            .embedded-a4-attachment {
                border-radius: 8px !important;
                margin-top: 0.85rem !important;
            }
            .attachment-header {
                padding: 10px 12px !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 8px !important;
            }
            .attachment-actions {
                width: 100% !important;
                display: flex !important;
                gap: 6px !important;
                flex-wrap: wrap !important;
            }
            .attachment-actions a, .attachment-actions span {
                font-size: 10px !important;
                padding: 4px 8px !important;
                flex: 1 !important;
                justify-content: center !important;
                text-align: center !important;
            }
            .embedded-a4-attachment iframe {
                height: 420px !important;
            }

            /* Stage 7 Mobile Refinements */
            .stage7-banner-box {
                padding: 0.95rem 0.85rem !important;
                border-radius: 10px !important;
            }
            .stage7-banner-header {
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.65rem !important;
                margin-bottom: 0.85rem !important;
            }
            .stage7-banner-header h4 {
                font-size: 0.92rem !important;
                line-height: 1.25 !important;
            }
            .stage7-banner-header span {
                font-size: 0.74rem !important;
            }
            .stage7-banner-header .meta-badge {
                align-self: flex-start !important;
                font-size: 0.76rem !important;
                padding: 0.35rem 0.75rem !important;
            }
            .stage7-metric-card {
                padding: 0.75rem 0.85rem !important;
            }
            .stage7-notice-box {
                padding: 0.75rem 0.85rem !important;
                font-size: 0.78rem !important;
                line-height: 1.45 !important;
            }

            .dash-footer {
                padding: 1rem 0.85rem !important;
                margin-top: 1rem !important;
                background: #ffffff !important;
                border-top: 1px solid #e2e8f0 !important;
            }
            .footer-inner {
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                gap: 0.4rem !important;
                font-size: 0.72rem !important;
                line-height: 1.45 !important;
            }
            .footer-inner > div {
                text-align: center !important;
            }

            .btn-label-desktop {
                display: none !important;
            }
            .btn-label-mobile {
                display: inline !important;
            }

            .stage-action-row {
                margin-top: 1.5rem !important;
                margin-bottom: 0.25rem !important;
                width: 100% !important;
                display: flex !important;
                flex-direction: column-reverse !important;
                gap: 0.75rem !important;
            }
            .stage-action-row .btn-step-prev,
            .stage-action-row .btn-step-next,
            .stage-action-row button,
            .stage-action-row a {
                width: 100% !important;
                justify-content: center !important;
                text-align: center !important;
                min-height: 48px !important;
                padding: 0.75rem 1rem !important;
                font-size: 0.92rem !important;
                font-weight: 700 !important;
                border-radius: 10px !important;
                box-sizing: border-box !important;
                display: flex !important;
                align-items: center !important;
                line-height: 1.25 !important;
            }
            .stage-action-row .btn-step-next {
                background: #2563eb !important;
                color: #ffffff !important;
                box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25) !important;
            }
            .stage-action-row .btn-step-next:hover {
                background: #1d4ed8 !important;
            }
            .stage-action-row .btn-step-prev {
                background: #f8fafc !important;
                color: #475569 !important;
                border: 1.5px solid #cbd5e1 !important;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
            }
            .stage-action-row .btn-step-prev:hover {
                background: #f1f5f9 !important;
                color: #0f172a !important;
                border-color: #94a3b8 !important;
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
            .mockup-field-row-3 {
                grid-template-columns: 1fr !important;
            }
            .mockup-field-row-4 {
                grid-template-columns: 1fr;
            }
            .mockup-subgrid-3 {
                grid-template-columns: 1fr !important;
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

        .mockup-select {
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%232563eb' stroke-width='2.2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 0.85rem;
            padding-right: 2rem !important;
        }

        .mockup-select option {
            background: #ffffff;
            color: #0f172a;
            padding: 0.5rem 0.75rem;
            font-size: 0.88rem;
        }

        /* ========================================================
           CUSTOM DROPDOWN UI (Floating Styled Dropdown Card)
           ======================================================== */
        .custom-select-container {
            position: relative;
            width: 100%;
        }

        .custom-select-container.is-open {
            z-index: 1001;
        }

        .custom-select-trigger {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.4rem;
            padding: 0.6rem 0.85rem;
            min-height: 40px;
            border: 1.5px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            color: #1e293b;
            font-family: inherit;
            background-color: #eef3f7;
            cursor: pointer;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            user-select: none;
            text-align: left;
            box-sizing: border-box;
        }

        .custom-select-trigger:hover {
            border-color: #94a3b8;
            background-color: #ffffff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.06);
        }

        .custom-select-container.is-open .custom-select-trigger,
        .custom-select-trigger:focus {
            outline: none;
            border-color: #0f2744;
            background-color: #ffffff;
            box-shadow: 0 0 0 3px rgba(15, 39, 68, 0.1);
        }

        .custom-select-trigger:disabled,
        fieldset[disabled] .custom-select-trigger {
            background: #f1f5f9 !important;
            color: #64748b !important;
            border-color: #cbd5e1 !important;
            cursor: not-allowed !important;
            opacity: 0.85;
            box-shadow: none !important;
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
            width: 0.85rem;
            height: 0.85rem;
            flex-shrink: 0;
            color: #2563eb;
            transition: transform 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-select-container.is-open .custom-select-arrow {
            transform: rotate(180deg);
        }

        .custom-select-dropdown {
            position: absolute;
            top: calc(100% + 4px);
            left: 0;
            width: 100%;
            max-width: 100%;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 12px;
            box-shadow: 0 16px 36px -4px rgba(15, 23, 42, 0.16), 0 6px 14px -3px rgba(15, 23, 42, 0.08);
            padding: 0.35rem;
            max-height: 240px;
            overflow-y: auto;
            z-index: 1002;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-6px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
            box-sizing: border-box;
        }

        .custom-select-container.is-open .custom-select-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

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

        .custom-select-option {
            padding: 0.55rem 0.75rem;
            font-size: 0.84rem;
            font-weight: 500;
            color: #1e293b;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.15s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
        }

        .custom-select-option:hover {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: 600;
        }

        .custom-select-option.is-selected {
            background-color: #2563eb;
            color: #ffffff;
            font-weight: 700;
        }

        .custom-select-option.is-placeholder {
            color: #94a3b8;
            font-style: italic;
            border-bottom: 1px dashed #e2e8f0;
            margin-bottom: 0.25rem;
        }

        fieldset[disabled] .mockup-input,
        fieldset[disabled] .mockup-select,
        fieldset[disabled] textarea,
        .mockup-input:disabled,
        .mockup-select:disabled {
            background: #f1f5f9 !important;
            color: #1e293b !important;
            border-color: #cbd5e1 !important;
            cursor: not-allowed !important;
            opacity: 0.92;
        }
        fieldset[disabled] input[type="radio"],
        fieldset[disabled] input[type="checkbox"] {
            cursor: not-allowed !important;
        }
        fieldset[disabled] label {
            cursor: default !important;
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
                    <div style="display:flex; align-items:center; gap:0.45rem; flex-wrap:nowrap;">
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
                        <span class="user-meta-sub" style="white-space:nowrap;">ID: {{ $appNo }}</span>
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
                    $isPaid = (!empty($isPaid) || $crmStudent?->stage4 === 'paid' || $crmStudent?->stage1 === 'paid' || !empty($isStage1Paid));
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

        @if(isset($errors) && $errors->any() && (int)($activeStep ?? 1) !== 2)
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
                             style="opacity: 0.65; cursor: not-allowed; pointer-events: none;"
                             title="Step {{ $num }}: {{ $stg['title'] }} (Locked until previous steps are completed)">
                            <div class="step-circle" style="background: #f8fafc; color: #64748b; border: 1.5px solid #cbd5e1;">
                                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: #64748b;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <span class="step-nav-name" style="color: #64748b;">{{ $stg['title'] }}</span>
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

            <!-- ================= STAGE 1: PERSONAL PROFILE ================= -->
            @if($activeStep === 1)
                <div class="stage-form-wrapper">
                    <!-- CARD 1: 2. PERSONAL DETAILS (FULL WIDTH) -->
                    <div class="mockup-card card-active-step" style="width: 100%; box-sizing: border-box; border-radius: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <h3 class="mockup-card-title">1. {{ $stages[1]['title'] ?? 'Personal Profile' }}</h3>
                            @if(($stages[1]['status'] ?? '') === 'completed')
                                <span class="step-header-tag tag-success" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">✓ Completed</span>
                            @else
                                <span class="step-header-tag tag-pending" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">In Progress</span>
                            @endif
                        </div>

                        @if($isPaid)
                            <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 0.85rem 1.15rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                                <span style="font-size: 1.25rem;">🔒</span>
                                <div>
                                    <div style="font-weight: 700; color: #1e40af; font-size: 0.88rem;">Personal Profile Fixed (Read-Only)</div>
                                    <div style="color: #3b82f6; font-size: 0.78rem;">Your personal details are locked because admission fee payment has been completed. Details can only be viewed.</div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('dashboards.save-step') }}" method="POST">
                            @csrf
                            <input type="hidden" name="step" value="1">
                            <fieldset @if($isPaid) disabled @endif style="border: none; margin: 0; padding: 0;">

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
                                    <input type="tel" name="student_number" class="mockup-input" value="{{ substr(preg_replace('/\D/', '', (string)($crmStudent?->phoneno ?: ($personalInfo?->phoneno ?: ($user?->phnno ?: '')))), -10) }}" placeholder="Enter 10-digit Student Number" maxlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" required>
                                </div>
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Parent Phone Number: <span style="color:#ef4444;">*</span></label>
                                    <input type="tel" name="parent_phone_no" class="mockup-input" value="{{ substr(preg_replace('/\D/', '', (string)($personalInfo?->parentphono ?: '')), -10) }}" placeholder="Enter 10-digit Parent Phone Number" maxlength="10" pattern="[0-9]{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10)" required>
                                </div>
                            </div>

                            <!-- Row 2: Date of Birth, Gender, Category (3 Columns) -->
                            <div class="mockup-field-row-3" style="margin-top: 1rem; align-items: start;">
                                <div class="mockup-field-group">
                                    <label class="mockup-label">Date of Birth: <span style="color:#ef4444;">*</span></label>
                                    <input type="date" name="dob" id="studentDobInput" class="mockup-input" value="{{ $personalInfo?->dob ? date('Y-m-d', strtotime($personalInfo->dob)) : '' }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" onchange="if(this.value > '{{ \Carbon\Carbon::now()->format('Y-m-d') }}') { this.value = ''; alert('Date of Birth cannot be a future date.'); }" required>
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
                                            <input type="checkbox" name="referee_by[]" value="via @google search(by own)" {{ (in_array('via @google search(by own)', $savedRefs) || in_array('via @by own', $savedRefs)) ? 'checked' : '' }}>
                                            <span>via @google search(by own)</span>
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
                                                @php
                                                    $isCountrySel = ($personalInfo?->country == $c->country_name)
                                                        || (!$personalInfo && ($c->country_name === 'India' || $c->country_id == 1 || strtoupper($c->country_code ?? '') === 'IN'));
                                                @endphp
                                                <option value="{{ $c->country_id }}" data-name="{{ $c->country_name }}" {{ $isCountrySel ? 'selected' : '' }}>
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
                                                @php
                                                    $isStateSel = ($personalInfo?->state == $s->state_name)
                                                        || (!$personalInfo && ($s->state_name === 'Andhra Pradesh' || $s->state_id == 1));
                                                @endphp
                                                <option value="{{ $s->state_id }}" data-country="{{ $s->country_id }}" data-name="{{ $s->state_name }}" {{ $isStateSel ? 'selected' : '' }}>
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
                                                @php
                                                    $isDistSel = ($personalInfo?->district == $d->DISTRICT_NAME)
                                                        || (!$personalInfo && ($d->DISTRICT_NAME === 'Guntur' || $d->DISTRICT_ID == 1));
                                                @endphp
                                                <option value="{{ $d->DISTRICT_ID }}" data-state="{{ $d->STATE_ID }}" data-name="{{ $d->DISTRICT_NAME }}" {{ $isDistSel ? 'selected' : '' }}>
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
                                        <input type="text" name="pincode" class="mockup-input" value="{{ $personalInfo?->pincode }}" placeholder="Enter 6-digit Pincode" maxlength="6" pattern="[0-9]{6}" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)" required>
                                    </div>
                                </div>
                            </div>
                            </fieldset>

                            <div class="stage-action-row">
                                @if($isPaid)
                                    <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-step-next" style="background: #2563eb; text-decoration: none;">
                                        <span class="btn-label-desktop">Next: Stage 2 - {{ $stages[2]['title'] ?? 'Academic Info' }} &rarr;</span>
                                        <span class="btn-label-mobile">Next: Stage 2 &rarr;</span>
                                    </a>
                                @else
                                    <button type="submit" class="btn-step-next" style="background: #2563eb;">
                                        <span class="btn-label-desktop">Save Personal Info &amp; Proceed to Stage 2: {{ $stages[2]['title'] ?? 'Academic Info' }} &rarr;</span>
                                        <span class="btn-label-mobile">Save &amp; Proceed to Stage 2 &rarr;</span>
                                    </button>
                                @endif
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

                            function togglePg1DocFields(val) {
                                const cmm = document.getElementById('pg1_cmm_container');
                                const pc = document.getElementById('pg1_pc_container');
                                if (!cmm || !pc) return;
                                if (val === 'CMM') {
                                    cmm.style.display = 'grid';
                                    pc.style.display = 'none';
                                } else if (val === 'PC_OD') {
                                    cmm.style.display = 'none';
                                    pc.style.display = 'grid';
                                } else {
                                    cmm.style.display = 'grid';
                                    pc.style.display = 'grid';
                                }
                            }

                            function togglePg2DocFields(val) {
                                const cmm = document.getElementById('pg2_cmm_container');
                                const pc = document.getElementById('pg2_pc_container');
                                if (!cmm || !pc) return;
                                if (val === 'CMM') {
                                    cmm.style.display = 'grid';
                                    pc.style.display = 'none';
                                } else if (val === 'PC_OD') {
                                    cmm.style.display = 'none';
                                    pc.style.display = 'grid';
                                } else if (val === 'BOTH') {
                                    cmm.style.display = 'grid';
                                    pc.style.display = 'grid';
                                } else {
                                    cmm.style.display = 'none';
                                    pc.style.display = 'none';
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

            <!-- ================= STAGE 2: ACADEMIC DETAILS ================= -->
            @elseif($activeStep === 2)
                <div class="stage-form-wrapper">
                    <!-- CARD 2: 3. EDUCATION DETAILS (FULL WIDTH) -->
                    <div class="mockup-card card-active-step" style="width: 100%; box-sizing: border-box; border-radius: 16px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
                            <h3 class="mockup-card-title" id="step2-card-title" style="margin: 0;">2. {{ $stages[2]['title'] ?? 'Education Details' }}</h3>
                            @if(($stages[2]['status'] ?? '') === 'completed')
                                <span class="step-header-tag tag-success" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">✓ Completed</span>
                            @else
                                <span class="step-header-tag tag-pending" style="font-size: 0.72rem; padding: 0.25rem 0.6rem;">In Progress</span>
                            @endif
                        </div>

                        @if($isPaid)
                            <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 0.85rem 1.15rem; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                                <span style="font-size: 1.25rem;">🔒</span>
                                <div>
                                    <div style="font-weight: 700; color: #1e40af; font-size: 0.88rem;">Academic Details Fixed (Read-Only)</div>
                                    <div style="color: #3b82f6; font-size: 0.78rem;">Your academic details are locked because admission fee payment has been completed. Details can only be viewed.</div>
                                </div>
                            </div>
                        @endif

                        <form id="step2AcademicForm" action="{{ route('dashboards.save-step') }}" method="POST" enctype="multipart/form-data" onsubmit="return validateStep2Form(this);">
                            @csrf
                            <input type="hidden" name="step" value="2">
                            <input type="hidden" name="degree_level" id="degree_level_input" value="{{ $degreeLevel }}">
                            <input type="hidden" name="eval_type" id="eval_type" value="{{ $academicDetails?->tenth_marks_type ?? 'Marks' }}">
                            <fieldset @if($isPaid) disabled @endif style="border: none; margin: 0; padding: 0;">

                            <!-- ================= SUBBOX 1: 10th SCHOOL / SSC INFO (OPTIONAL) ================= -->
                            <div class="mockup-subbox" id="sec-10th-details">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <h4 class="mockup-subbox-title">10th / Secondary School Details</h4>
                                </div>

                                <!-- Row 1: College Name / Institute Name & Board (2 Columns) -->
                                <div class="mockup-field-row-2">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">College Name / Institute Name:</label>
                                        <input type="text" name="school_co_name" id="tenth_school_name" class="mockup-input" value="{{ $academicDetails?->tenth_school_name }}" placeholder="School / Institute Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Board:</label>
                                        <input type="text" name="boad_of_study" id="tenth_study_board" class="mockup-input" value="{{ $academicDetails?->tenth_study_board }}" placeholder="Board (e.g. BSEAP, CBSE, ICSE)">
                                    </div>
                                </div>

                                <!-- Row 2: City, State, District (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">City:</label>
                                        <input type="text" name="tenth_school_city" id="tenth_school_city" class="mockup-input" value="{{ $academicDetails?->tenth_school_city }}" placeholder="Enter City">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">State:</label>
                                        <select name="tenth_school_state" id="geo-tenth-state-select" class="mockup-select" onchange="onTenthStateChange(this.value)">
                                            <option value="">-- Select State --</option>
                                            @foreach($states as $s)
                                                @php
                                                    $curState = $academicDetails?->tenth_school_state;
                                                    $isStateSel = !empty($curState) && ($curState == $s->state_name || $curState == $s->state_id);
                                                @endphp
                                                <option value="{{ $s->state_id }}" data-id="{{ $s->state_id }}" data-name="{{ $s->state_name }}" {{ $isStateSel ? 'selected' : '' }}>
                                                    {{ $s->state_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">District:</label>
                                        <select name="tenth_school_district" id="geo-tenth-district-select" class="mockup-select">
                                            <option value="">-- Select District --</option>
                                            @foreach($districts as $d)
                                                @php
                                                    $curDist = $academicDetails?->tenth_school_district;
                                                    $isDistSel = !empty($curDist) && ($curDist == $d->DISTRICT_NAME || $curDist == $d->DISTRICT_ID);
                                                @endphp
                                                <option value="{{ $d->DISTRICT_ID }}" data-state="{{ $d->STATE_ID }}" data-name="{{ $d->DISTRICT_NAME }}" {{ $isDistSel ? 'selected' : '' }}>
                                                    {{ $d->DISTRICT_NAME }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 3: Year, Hall Ticket number/Registration number, Current Status (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Year:</label>
                                        <input type="number" name="passing_year" id="tenth_passed_year" class="mockup-input" value="{{ $academicDetails?->tenth_passed_year }}" placeholder="Passing Year (e.g. 2024)" min="2000" max="2030">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Hall Ticket number:</label>
                                        <input type="text" name="roll_no" id="tenth_roll_no" class="mockup-input" value="{{ $academicDetails?->tenth_roll_no }}" placeholder="Enter 10th Hall Ticket / Reg No">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Current Status:</label>
                                        <select name="tenth_current_status" id="tenth_current_status" class="mockup-select">
                                            <option value="Completed" {{ ($academicDetails?->tenth_current_status === 'Completed' || !$academicDetails?->tenth_current_status) ? 'selected' : '' }}>Completed / Passed</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 4: Marks Type, Total Marks (Conducted), Marks Earned, Percentage (%) (4 Columns) -->
                                <div class="mockup-field-row-4" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Type:</label>
                                        <select name="tenth_marks_type" id="tenth_marks_type" class="mockup-select" onchange="onTenthMarksTypeChange(this.value)">
                                            <option value="Marks" {{ ($academicDetails?->tenth_marks_type === 'Marks' || $academicDetails?->tenth_marks_type === 'marks' || !$academicDetails) ? 'selected' : '' }}>Marks</option>
                                            <option value="CGPA" {{ ($academicDetails?->tenth_marks_type === 'CGPA') ? 'selected' : '' }}>CGPA</option>
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Total Marks (Conducted):</label>
                                        <input type="number" step="0.01" name="tenth_marks_conducted" id="tenth_marks_conducted" class="mockup-input" value="{{ $academicDetails?->tenth_marks_conducted }}" placeholder="e.g. 600" oninput="calculateTenthPct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Earned:</label>
                                        <input type="number" step="0.01" name="tenth_marks_earned" id="tenth_marks_earned" class="mockup-input" value="{{ $academicDetails?->tenth_marks_earned ?? $academicDetails?->tenth_marks }}" placeholder="e.g. 580" oninput="calculateTenthPct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Percentage (%):</label>
                                        <input type="number" step="0.01" name="tenth_percentage" id="tenth_percentage" class="mockup-input" value="{{ $academicDetails?->tenth_percentage }}" placeholder="e.g. 96.67" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #0f172a; font-weight: 700;">
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUBBOX 2: 12th / INTERMEDIATE INFO ================= -->
                            <div class="mockup-subbox" id="sec-inter-details" style="margin-top: 1rem;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <h4 class="mockup-subbox-title">12th / Intermediate Details <span style="font-size: 0.76rem; font-weight: 700; color: #2563eb; margin-left: 0.35rem;">(option 1st year/2nd year for UG; default 2nd year for PG &amp; Ph.D)</span></h4>
                                </div>
                                <input type="hidden" name="course_choice" value="{{ $academicDetails?->pu2_study_board ?? '' }}">
                                <input type="hidden" name="undertaking_60_pct" id="undertaking_60_pct_input" value="{{ $academicDetails?->undertaking_60_pct ?? '' }}">

                                <!-- Row 1: College Name / Institute Name & Board (2 Columns) -->
                                <div class="mockup-field-row-2">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">College Name / Institute Name: <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_college" id="inter_college" class="mockup-input" value="{{ $academicDetails?->pu2_college_name }}" placeholder="Enter College / Institute Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Board: <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="inter_board_of_study" id="inter_board_of_study" class="mockup-input" value="{{ $academicDetails?->pu2_study_board ?? $academicDetails?->pu2_board_of_study }}" placeholder="Enter Board (e.g. BIEAP, CBSE, TSBIE)">
                                    </div>
                                </div>

                                <!-- Row 2: City, State, District (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">City: <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="city" id="inter_city" class="mockup-input" value="{{ $academicDetails?->city ?? $academicDetails?->pu2_college_city }}" placeholder="Enter College City">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">State: <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <select name="jr_state" id="geo-inter-state-select" class="mockup-select" onchange="onInterStateChange(this.value)">
                                            <option value="">-- Select State --</option>
                                            @foreach($states as $s)
                                                @php
                                                    $curState = $academicDetails?->pu2_college_state;
                                                    $isStateSel = !empty($curState) ? ($curState == $s->state_name || $curState == $s->state_id) : ($s->state_name === 'Andhra Pradesh');
                                                @endphp
                                                <option value="{{ $s->state_id }}" data-id="{{ $s->state_id }}" data-name="{{ $s->state_name }}" {{ $isStateSel ? 'selected' : '' }}>
                                                    {{ $s->state_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">District: <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <select name="jr_district" id="geo-inter-district-select" class="mockup-select">
                                            <option value="">-- Select District --</option>
                                            @foreach($districts as $d)
                                                @php
                                                    $curDist = $academicDetails?->pu2_college_district;
                                                    $isDistSel = !empty($curDist) && ($curDist == $d->DISTRICT_NAME || $curDist == $d->DISTRICT_ID);
                                                @endphp
                                                <option value="{{ $d->DISTRICT_ID }}" data-state="{{ $d->STATE_ID }}" data-name="{{ $d->DISTRICT_NAME }}" {{ $isDistSel ? 'selected' : '' }}>
                                                    {{ $d->DISTRICT_NAME }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 3: Year, Hall Ticket number/Registration number, Current Status (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Year: <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <input type="number" name="inter_year" id="inter_year" class="mockup-input" value="{{ $academicDetails?->pu2_passed_year }}" placeholder="e.g. 2026" min="2000" max="2030">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Hall Ticket number:</label>
                                        <input type="text" name="inter_hallticket" id="inter_hallticket" class="mockup-input" value="{{ $academicDetails?->pu2_roll_no }}" placeholder="Enter Hall Ticket number">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Current Status: <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <div class="mockup-radio-row" style="margin-top: 0.45rem;">
                                            @php
                                                $isPgOrDoc = in_array($degreeLevel ?? '', ['mtech', 'phd']);
                                                $savedStatus = $academicDetails?->pu2_current_status;
                                                if (!empty($savedStatus)) {
                                                    $isSecondYr = in_array(strtolower(trim($savedStatus)), ['second year', 'second']);
                                                } else {
                                                    // Default: Second Year for PG/Doctorate, First Year for UG
                                                    $isSecondYr = $isPgOrDoc;
                                                }
                                            @endphp
                                            <label class="mockup-radio-label" id="radio-wrapper-first-year" style="{{ $isPgOrDoc ? 'display: none !important;' : '' }}">
                                                <input type="radio" name="subset_choice" id="radio-subset-first-year" value="First Year" {{ (!$isPgOrDoc && !$isSecondYr) ? 'checked' : '' }} {{ $isPgOrDoc ? 'disabled' : '' }}>
                                                <span>1st Year</span>
                                            </label>
                                            <label class="mockup-radio-label" id="radio-wrapper-second-year">
                                                <input type="radio" name="subset_choice" id="radio-subset-second-year" value="Second Year" {{ ($isPgOrDoc || $isSecondYr) ? 'checked' : '' }}>
                                                <span>2nd Year</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Row 4: Marks Type, Total Marks (Conducted), Marks Earned, Percentage (%) (4 Columns) -->
                                <div class="mockup-field-row-4" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Type: <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <select name="pu2_marks_type" id="pu2_marks_type" class="mockup-select" onchange="onPu2MarksTypeChange(this.value)">
                                            <option value="Marks" {{ ($academicDetails?->pu2_marks_type === 'Marks' || !$academicDetails) ? 'selected' : '' }}>Marks</option>
                                            <option value="CGPA" {{ ($academicDetails?->pu2_marks_type === 'CGPA') ? 'selected' : '' }}>CGPA</option>
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Total Marks (Conducted): <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="pu2_marks_conducted" id="pu2_marks_conducted" class="mockup-input" value="{{ $academicDetails?->pu2_marks_conducted }}" placeholder="e.g. 1000" oninput="calculatePu2Pct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Earned: <span class="req-inter" style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="pu2_marks_earned" id="pu2_marks_earned" class="mockup-input" value="{{ $academicDetails?->pu2_marks_earned ?? $academicDetails?->pu2_marks }}" placeholder="e.g. 962" oninput="calculatePu2Pct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Percentage (%): <span class="req-inter" style="color:#ef4444;">*</span> <span id="span-pu2-cutoff" style="font-size:0.75rem; font-weight:700; color:#dc2626; {{ in_array($degreeLevel ?? '', ['mtech', 'phd']) ? 'display:none;' : '' }}">(&ge; 60%)</span></label>
                                        <input type="number" step="0.01" name="pu2_percentage" id="pu2_percentage" class="mockup-input" value="{{ $academicDetails?->pu2_percentage }}" placeholder="e.g. 96.20" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #0f172a; font-weight: 700;">
                                        <div id="pu2_pct_warning" style="display: none; font-size: 0.72rem; color: #dc2626; font-weight: 700; margin-top: 3px;">⚠️ 12th percentage must be at least 60% to proceed.</div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUBBOX 3: UNDERGRADUATE (B.TECH / DEGREE) ================= -->
                            <div class="mockup-subbox" id="sec-ug-details" style="display: {{ in_array($degreeLevel ?? '', ['mtech', 'phd']) ? 'block' : 'none' }}; margin-top: 1rem; border-color: #93c5fd; background: #f0f7ff;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <h4 class="mockup-subbox-title" style="color: #1e3a8a;">Undergraduate (UG / B.Tech / Degree) Details</h4>
                                </div>

                                <!-- Row 1: College Name / Institute Name & Board (2 Columns) -->
                                <div class="mockup-field-row-2">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">College Name / Institute Name: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_college_name" id="ug_college_name" class="mockup-input" value="{{ $academicDetails?->ug_college_name }}" placeholder="UG College / Institute Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Board: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_university_board" id="ug_university_board" class="mockup-input" value="{{ $academicDetails?->ug_university_board }}" placeholder="University / Board Name">
                                    </div>
                                </div>

                                <!-- Row 2: City, State, District (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">City: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_college_city" id="ug_college_city" class="mockup-input" value="{{ $academicDetails?->ug_college_city }}" placeholder="Enter City">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">State: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <select name="ug_college_state" id="geo-ug-state-select" class="mockup-select" onchange="onUgStateChange(this.value)">
                                            <option value="">-- Select State --</option>
                                            @foreach($states as $s)
                                                @php
                                                    $curState = $academicDetails?->ug_college_state;
                                                    $isStateSel = !empty($curState) ? ($curState == $s->state_name || $curState == $s->state_id) : ($s->state_name === 'Andhra Pradesh');
                                                @endphp
                                                <option value="{{ $s->state_id }}" data-id="{{ $s->state_id }}" data-name="{{ $s->state_name }}" {{ $isStateSel ? 'selected' : '' }}>
                                                    {{ $s->state_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">District: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <select name="ug_college_district" id="geo-ug-district-select" class="mockup-select">
                                            <option value="">-- Select District --</option>
                                            @foreach($districts as $d)
                                                @php
                                                    $curDist = $academicDetails?->ug_college_district;
                                                    $isDistSel = !empty($curDist) && ($curDist == $d->DISTRICT_NAME || $curDist == $d->DISTRICT_ID);
                                                @endphp
                                                <option value="{{ $d->DISTRICT_ID }}" data-state="{{ $d->STATE_ID }}" data-name="{{ $d->DISTRICT_NAME }}" {{ $isDistSel ? 'selected' : '' }}>
                                                    {{ $d->DISTRICT_NAME }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 3: Year, Hall Ticket number/Registration number, Current Status (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Year: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <input type="number" name="ug_passed_year" id="ug_passed_year" class="mockup-input" value="{{ $academicDetails?->ug_passed_year }}" placeholder="Passing Year (e.g. 2024)" min="2000" max="2030">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Registration number: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="ug_roll_no" id="ug_roll_no" class="mockup-input" value="{{ $academicDetails?->ug_roll_no }}" placeholder="Enter UG Roll No / Reg No">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Current Status: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <select name="ug_current_status" id="ug_current_status" class="mockup-select">
                                            <option value="Completed" {{ ($academicDetails?->ug_current_status === 'Completed' || !$academicDetails) ? 'selected' : '' }}>Completed</option>
                                            <option value="Final Year" id="opt-ug-final-year" {{ ($academicDetails?->ug_current_status === 'Final Year' || $academicDetails?->ug_current_status === 'Final Year Appeared') ? 'selected' : '' }}>Final Year</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 4: Marks Type, Total Marks (Conducted), Marks Earned, Percentage (%) (4 Columns) -->
                                <div class="mockup-field-row-4" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Type: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <select name="ug_marks_type" id="ug_marks_type" class="mockup-select" onchange="onUgMarksTypeChange(this.value)">
                                            <option value="Marks" {{ ($academicDetails?->ug_marks_type === 'Marks' || !$academicDetails) ? 'selected' : '' }}>Marks</option>
                                            <option value="CGPA" {{ ($academicDetails?->ug_marks_type === 'CGPA') ? 'selected' : '' }}>CGPA</option>
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Total Marks (Conducted): <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="ug_marks_conducted" id="ug_marks_conducted" class="mockup-input" value="{{ $academicDetails?->ug_marks_conducted }}" placeholder="e.g. 1000" oninput="calculateUgPct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Earned: <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="ug_marks_earned" id="ug_marks_earned" class="mockup-input" value="{{ $academicDetails?->ug_marks_earned ?? $academicDetails?->ug_marks }}" placeholder="e.g. 850" oninput="calculateUgPct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Percentage (%): <span class="req-ug" style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="ug_percentage" id="ug_percentage" class="mockup-input" value="{{ $academicDetails?->ug_percentage }}" placeholder="e.g. 85.00" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #0f172a; font-weight: 700;">
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUBBOX 4: POSTGRADUATE 1 (PHD ONLY) ================= -->
                            <div class="mockup-subbox" id="sec-pg-details" style="display: {{ ($degreeLevel ?? '') === 'phd' ? 'block' : 'none' }}; margin-top: 1rem; border-color: #d8b4fe; background: #faf5ff;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <h4 class="mockup-subbox-title" style="color: #6b21a8;">
                                        Postgraduate 1 (PG1) Details <span style="color:#ef4444;">*</span>
                                    </h4>
                                </div>

                                <!-- Row 1: College Name / Institute Name & Board (2 Columns) -->
                                <div class="mockup-field-row-2">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">College Name / Institute Name: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_college_name" id="pg_college_name" class="mockup-input" value="{{ $academicDetails?->pg_college_name }}" placeholder="PG 1 College / University Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Board: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_university_board" id="pg_university_board" class="mockup-input" value="{{ $academicDetails?->pg_university_board }}" placeholder="University / Board Name">
                                    </div>
                                </div>

                                <!-- Row 2: City, State, District (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">City: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_college_city" id="pg_college_city" class="mockup-input" value="{{ $academicDetails?->pg_college_city }}" placeholder="Enter City">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">State: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <select name="pg_college_state" id="geo-pg-state-select" class="mockup-select" onchange="onPgStateChange(this.value)">
                                            <option value="">-- Select State --</option>
                                            @foreach($states as $s)
                                                @php
                                                    $curState = $academicDetails?->pg_college_state;
                                                    $isStateSel = !empty($curState) ? ($curState == $s->state_name || $curState == $s->state_id) : ($s->state_name === 'Andhra Pradesh');
                                                @endphp
                                                <option value="{{ $s->state_id }}" data-id="{{ $s->state_id }}" data-name="{{ $s->state_name }}" {{ $isStateSel ? 'selected' : '' }}>
                                                    {{ $s->state_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">District: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <select name="pg_college_district" id="geo-pg-district-select" class="mockup-select">
                                            <option value="">-- Select District --</option>
                                            @foreach($districts as $d)
                                                @php
                                                    $curDist = $academicDetails?->pg_college_district;
                                                    $isDistSel = !empty($curDist) && ($curDist == $d->DISTRICT_NAME || $curDist == $d->DISTRICT_ID);
                                                @endphp
                                                <option value="{{ $d->DISTRICT_ID }}" data-state="{{ $d->STATE_ID }}" data-name="{{ $d->DISTRICT_NAME }}" {{ $isDistSel ? 'selected' : '' }}>
                                                    {{ $d->DISTRICT_NAME }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 3: Year, Hall Ticket number/Registration number, Current Status (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Year: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <input type="number" name="pg_passed_year" id="pg_passed_year" class="mockup-input" value="{{ $academicDetails?->pg_passed_year }}" placeholder="Passing Year (e.g. 2024)" min="2000" max="2030">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Registration number: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <input type="text" name="pg_roll_no" id="pg_roll_no" class="mockup-input" value="{{ $academicDetails?->pg_roll_no }}" placeholder="PG 1 Roll NO / Reg No">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Current Status: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <select name="pg_current_status" id="pg_current_status" class="mockup-select">
                                            <option value="Completed" {{ ($academicDetails?->pg_current_status === 'Completed' || !$academicDetails) ? 'selected' : '' }}>Completed</option>
                                            <option value="Pursuing" {{ ($academicDetails?->pg_current_status === 'Pursuing') ? 'selected' : '' }}>Pursuing</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 4: Marks Type, Total Marks (Conducted), Marks Earned, Percentage (%) (4 Columns) -->
                                <div class="mockup-field-row-4" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Type: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <select name="pg_marks_type" id="pg_marks_type" class="mockup-select" onchange="onPgMarksTypeChange(this.value)">
                                            <option value="Marks" {{ ($academicDetails?->pg_marks_type === 'Marks' || !$academicDetails) ? 'selected' : '' }}>Marks</option>
                                            <option value="CGPA" {{ ($academicDetails?->pg_marks_type === 'CGPA') ? 'selected' : '' }}>CGPA</option>
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Total Marks (Conducted): <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="pg_marks_conducted" id="pg_marks_conducted" class="mockup-input" value="{{ $academicDetails?->pg_marks_conducted }}" placeholder="e.g. 1000" oninput="calculatePgPct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Earned: <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="pg_marks_earned" id="pg_marks_earned" class="mockup-input" value="{{ $academicDetails?->pg_marks_earned ?? $academicDetails?->pg_marks }}" placeholder="e.g. 880" oninput="calculatePgPct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Percentage (%): <span class="req-pg" style="color:#ef4444;">*</span></label>
                                        <input type="number" step="0.01" name="pg_percentage" id="pg_percentage" class="mockup-input" value="{{ $academicDetails?->pg_percentage }}" placeholder="e.g. 88.00" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #0f172a; font-weight: 700;">
                                    </div>
                                </div>

                                <!-- PG 1 Certificate / Memo Type: (pg1(cmm), pg1 (pc/od))* (any one only mandatory) -->
                                @php
                                    $pg1Doc = $academicDetails?->pg1_doc_type ?? 'BOTH';
                                @endphp
                                <div style="margin-top: 0.95rem; background: #ffffff; border: 1.5px solid #d8b4fe; border-radius: 8px; padding: 0.85rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                                        <label class="mockup-label" style="font-weight: 800; color: #581c87; margin-bottom: 0;">
                                            Proof of PG 1 Degree / Certificate: <span style="color:#ef4444;">*</span>
                                            <span style="font-size: 0.75rem; font-weight: 700; color: #7e22ce;">(pg1(cmm), pg1 (pc/od))* &bull; (any one only mandatory)</span>
                                        </label>
                                        <span class="mockup-pill-badge" style="background: #f3e8ff; color: #6b21a8; font-size: 0.72rem; border: 1px solid #d8b4fe;">
                                            Any one mandatory *
                                        </span>
                                    </div>

                                    <div class="mockup-radio-row" style="gap: 1.25rem; margin-bottom: 0.65rem;">
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="pg1_doc_type" value="CMM" {{ $pg1Doc === 'CMM' ? 'checked' : '' }} onchange="togglePg1DocFields(this.value)">
                                            <span>PG 1 Consolidated Marks Memo (CMM)</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="pg1_doc_type" value="PC_OD" {{ $pg1Doc === 'PC_OD' ? 'checked' : '' }} onchange="togglePg1DocFields(this.value)">
                                            <span>PG 1 Provisional Certificate / Original Degree (PC/OD)</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="pg1_doc_type" value="BOTH" {{ ($pg1Doc === 'BOTH' || !$academicDetails?->pg1_doc_type) ? 'checked' : '' }} onchange="togglePg1DocFields(this.value)">
                                            <span>Both (CMM &amp; PC/OD)</span>
                                        </label>
                                    </div>

                                    <div id="pg1_cmm_container" class="mockup-subgrid-3" style="display: {{ in_array($pg1Doc, ['CMM', 'BOTH']) ? 'grid' : 'none' }}; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; margin-top: 0.45rem; background: #faf5ff; padding: 0.65rem; border-radius: 6px; border: 1px dashed #d8b4fe;">
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 1 CMM Memo / Serial No:</label>
                                            <input type="text" name="pg1_cmm_memo_no" class="mockup-input" value="{{ $academicDetails?->pg1_cmm_memo_no }}" placeholder="Enter CMM Memo / Serial No">
                                        </div>
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 1 CMM Marks / CGPA:</label>
                                            <input type="text" name="pg1_cmm_cgpa" class="mockup-input" value="{{ $academicDetails?->pg1_cmm_cgpa }}" placeholder="e.g. 8.95">
                                        </div>
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 1 CMM Issue Year:</label>
                                            <input type="number" name="pg1_cmm_year" class="mockup-input" value="{{ $academicDetails?->pg1_cmm_year }}" placeholder="e.g. 2024" min="2000" max="2030">
                                        </div>
                                    </div>

                                    <div id="pg1_pc_container" class="mockup-subgrid-3" style="display: {{ in_array($pg1Doc, ['PC_OD', 'BOTH']) ? 'grid' : 'none' }}; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; margin-top: 0.45rem; background: #faf5ff; padding: 0.65rem; border-radius: 6px; border: 1px dashed #d8b4fe;">
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 1 PC / OD Certificate No:</label>
                                            <input type="text" name="pg1_pc_od_no" class="mockup-input" value="{{ $academicDetails?->pg1_pc_od_no }}" placeholder="Enter PC / OD Number">
                                        </div>
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 1 PC / OD Issue Year:</label>
                                            <input type="number" name="pg1_pc_od_year" class="mockup-input" value="{{ $academicDetails?->pg1_pc_od_year }}" placeholder="e.g. 2024" min="2000" max="2030">
                                        </div>
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 1 PC / OD Certificate Status:</label>
                                            <select name="pg1_pc_od_status" class="mockup-select">
                                                <option value="Provisional" {{ ($academicDetails?->pg1_pc_od_status === 'Provisional' || !$academicDetails) ? 'selected' : '' }}>Provisional Certificate (PC)</option>
                                                <option value="Original Degree" {{ ($academicDetails?->pg1_pc_od_status === 'Original Degree') ? 'selected' : '' }}>Original Degree (OD)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= SUBBOX 5: POSTGRADUATE 2 (PHD ONLY) ================= -->
                            <div class="mockup-subbox" id="sec-pg2-details" style="display: {{ ($degreeLevel ?? '') === 'phd' ? 'block' : 'none' }}; margin-top: 1rem; border-color: #d8b4fe; background: #faf5ff;">
                                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem; flex-wrap: wrap; gap: 0.5rem;">
                                    <h4 class="mockup-subbox-title" style="color: #6b21a8;">Postgraduate 2 (PG2) Details</h4>
                                </div>

                                <!-- Row 1: College Name / Institute Name & Board (2 Columns) -->
                                <div class="mockup-field-row-2">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">College Name / Institute Name:</label>
                                        <input type="text" name="pg2_college_name" id="pg2_college_name" class="mockup-input" value="{{ $academicDetails?->pg2_college_name }}" placeholder="PG 2 College / University Name">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Board:</label>
                                        <input type="text" name="pg2_university_board" id="pg2_university_board" class="mockup-input" value="{{ $academicDetails?->pg2_university_board }}" placeholder="University / Board Name">
                                    </div>
                                </div>

                                <!-- Row 2: City, State, District (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">City:</label>
                                        <input type="text" name="pg2_college_city" id="pg2_college_city" class="mockup-input" value="{{ $academicDetails?->pg2_college_city }}" placeholder="Enter City">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">State:</label>
                                        <select name="pg2_college_state" id="geo-pg2-state-select" class="mockup-select" onchange="onPg2StateChange(this.value)">
                                            <option value="">-- Select State --</option>
                                            @foreach($states as $s)
                                                @php
                                                    $curState = $academicDetails?->pg2_college_state;
                                                    $isStateSel = !empty($curState) ? ($curState == $s->state_name || $curState == $s->state_id) : ($s->state_name === 'Andhra Pradesh');
                                                @endphp
                                                <option value="{{ $s->state_id }}" data-id="{{ $s->state_id }}" data-name="{{ $s->state_name }}" {{ $isStateSel ? 'selected' : '' }}>
                                                    {{ $s->state_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">District:</label>
                                        <select name="pg2_college_district" id="geo-pg2-district-select" class="mockup-select">
                                            <option value="">-- Select District --</option>
                                            @foreach($districts as $d)
                                                @php
                                                    $curDist = $academicDetails?->pg2_college_district;
                                                    $isDistSel = !empty($curDist) && ($curDist == $d->DISTRICT_NAME || $curDist == $d->DISTRICT_ID);
                                                @endphp
                                                <option value="{{ $d->DISTRICT_ID }}" data-state="{{ $d->STATE_ID }}" data-name="{{ $d->DISTRICT_NAME }}" {{ $isDistSel ? 'selected' : '' }}>
                                                    {{ $d->DISTRICT_NAME }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 3: Year, Hall Ticket number/Registration number, Current Status (3 Columns) -->
                                <div class="mockup-field-row-3" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Year:</label>
                                        <input type="number" name="pg2_passed_year" id="pg2_passed_year" class="mockup-input" value="{{ $academicDetails?->pg2_passed_year }}" placeholder="Passing Year (e.g. 2026)" min="2000" max="2030">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Registration number:</label>
                                        <input type="text" name="pg2_roll_no" id="pg2_roll_no" class="mockup-input" value="{{ $academicDetails?->pg2_roll_no }}" placeholder="PG 2 Roll Number / Reg No">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Current Status:</label>
                                        <select name="pg2_current_status" id="pg2_current_status" class="mockup-select">
                                            <option value="Completed" {{ ($academicDetails?->pg2_current_status === 'Completed' || !$academicDetails) ? 'selected' : '' }}>Completed</option>
                                            <option value="Pursuing" {{ ($academicDetails?->pg2_current_status === 'Pursuing') ? 'selected' : '' }}>Pursuing</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Row 4: Marks Type, Total Marks (Conducted), Marks Earned, Percentage (%) (4 Columns) -->
                                <div class="mockup-field-row-4" style="margin-top: 0.85rem; align-items: start;">
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Type:</label>
                                        <select name="pg2_marks_type" id="pg2_marks_type" class="mockup-select" onchange="onPg2MarksTypeChange(this.value)">
                                            <option value="Marks" {{ ($academicDetails?->pg2_marks_type === 'Marks' || !$academicDetails) ? 'selected' : '' }}>Marks</option>
                                            <option value="CGPA" {{ ($academicDetails?->pg2_marks_type === 'CGPA') ? 'selected' : '' }}>CGPA</option>
                                        </select>
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Total Marks (Conducted):</label>
                                        <input type="number" step="0.01" name="pg2_marks_conducted" id="pg2_marks_conducted" class="mockup-input" value="{{ $academicDetails?->pg2_marks_conducted }}" placeholder="e.g. 1000" oninput="calculatePg2Pct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Marks Earned:</label>
                                        <input type="number" step="0.01" name="pg2_marks_earned" id="pg2_marks_earned" class="mockup-input" value="{{ $academicDetails?->pg2_marks_earned ?? $academicDetails?->pg2_marks }}" placeholder="e.g. 880" oninput="calculatePg2Pct()">
                                    </div>
                                    <div class="mockup-field-group">
                                        <label class="mockup-label">Percentage (%):</label>
                                        <input type="number" step="0.01" name="pg2_percentage" id="pg2_percentage" class="mockup-input" value="{{ $academicDetails?->pg2_percentage }}" placeholder="e.g. 88.00" readonly style="background-color: #f1f5f9; cursor: not-allowed; color: #0f172a; font-weight: 700;">
                                    </div>
                                </div>

                                <!-- PG 2 Certificate / Memo Type: (pg2(cmm), pg2(pc/od)) (Optional) -->
                                @php
                                    $pg2Doc = $academicDetails?->pg2_doc_type ?? 'NONE';
                                @endphp
                                <div style="margin-top: 0.95rem; background: #ffffff; border: 1.5px solid #d8b4fe; border-radius: 8px; padding: 0.85rem;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                                        <label class="mockup-label" style="font-weight: 800; color: #581c87; margin-bottom: 0;">
                                            Proof of PG 2 Degree / Certificate:
                                            <span style="font-size: 0.75rem; font-weight: 600; color: #7e22ce;">(pg2(cmm), pg2(pc/od))</span>
                                        </label>
                                    </div>

                                    <div class="mockup-radio-row" style="gap: 1.25rem; margin-bottom: 0.65rem;">
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="pg2_doc_type" value="NONE" {{ ($pg2Doc === 'NONE' || !$academicDetails?->pg2_doc_type) ? 'checked' : '' }} onchange="togglePg2DocFields(this.value)">
                                            <span>Not Applicable</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="pg2_doc_type" value="CMM" {{ $pg2Doc === 'CMM' ? 'checked' : '' }} onchange="togglePg2DocFields(this.value)">
                                            <span>PG 2 Consolidated Marks Memo (CMM)</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="pg2_doc_type" value="PC_OD" {{ $pg2Doc === 'PC_OD' ? 'checked' : '' }} onchange="togglePg2DocFields(this.value)">
                                            <span>PG 2 Provisional Certificate / Original Degree (PC/OD)</span>
                                        </label>
                                        <label class="mockup-radio-label">
                                            <input type="radio" name="pg2_doc_type" value="BOTH" {{ $pg2Doc === 'BOTH' ? 'checked' : '' }} onchange="togglePg2DocFields(this.value)">
                                            <span>Both (CMM &amp; PC/OD)</span>
                                        </label>
                                    </div>

                                    <div id="pg2_cmm_container" class="mockup-subgrid-3" style="display: {{ in_array($pg2Doc, ['CMM', 'BOTH']) ? 'grid' : 'none' }}; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; margin-top: 0.45rem; background: #faf5ff; padding: 0.65rem; border-radius: 6px; border: 1px dashed #d8b4fe;">
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 2 CMM Memo / Serial No:</label>
                                            <input type="text" name="pg2_cmm_memo_no" class="mockup-input" value="{{ $academicDetails?->pg2_cmm_memo_no }}" placeholder="Enter CMM Memo / Serial No">
                                        </div>
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 2 CMM Marks / CGPA:</label>
                                            <input type="text" name="pg2_cmm_cgpa" class="mockup-input" value="{{ $academicDetails?->pg2_cmm_cgpa }}" placeholder="e.g. 9.10">
                                        </div>
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 2 CMM Issue Year:</label>
                                            <input type="number" name="pg2_cmm_year" class="mockup-input" value="{{ $academicDetails?->pg2_cmm_year }}" placeholder="e.g. 2026" min="2000" max="2030">
                                        </div>
                                    </div>

                                    <div id="pg2_pc_container" class="mockup-subgrid-3" style="display: {{ in_array($pg2Doc, ['PC_OD', 'BOTH']) ? 'grid' : 'none' }}; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; margin-top: 0.45rem; background: #faf5ff; padding: 0.65rem; border-radius: 6px; border: 1px dashed #d8b4fe;">
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 2 PC / OD Certificate No:</label>
                                            <input type="text" name="pg2_pc_od_no" class="mockup-input" value="{{ $academicDetails?->pg2_pc_od_no }}" placeholder="Enter PC / OD Number">
                                        </div>
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 2 PC / OD Issue Year:</label>
                                            <input type="number" name="pg2_pc_od_year" class="mockup-input" value="{{ $academicDetails?->pg2_pc_od_year }}" placeholder="e.g. 2026" min="2000" max="2030">
                                        </div>
                                        <div class="mockup-field-group">
                                            <label class="mockup-label">PG 2 PC / OD Certificate Status:</label>
                                            <select name="pg2_pc_od_status" class="mockup-select">
                                                <option value="Provisional" {{ ($academicDetails?->pg2_pc_od_status === 'Provisional' || !$academicDetails) ? 'selected' : '' }}>Provisional Certificate (PC)</option>
                                                <option value="Original Degree" {{ ($academicDetails?->pg2_pc_od_status === 'Original Degree') ? 'selected' : '' }}>Original Degree (OD)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </fieldset>

                            <div class="stage-action-row" style="justify-content: space-between;">
                                <a href="{{ route('dashboards.index', ['step' => 1]) }}" class="btn-step-prev" style="text-decoration: none;">
                                    &larr; Stage 1: {{ $stages[1]['title'] ?? 'Personal Profile' }}
                                </a>
                                @if($isPaid)
                                    <a href="{{ route('dashboards.index', ['step' => 3]) }}" class="btn-step-next" style="background: #2563eb; text-decoration: none;">
                                        <span class="btn-label-desktop">Next: Stage 3 - {{ $stages[3]['title'] ?? 'Document upload' }} &rarr;</span>
                                        <span class="btn-label-mobile">Next: Stage 3 &rarr;</span>
                                    </a>
                                @else
                                    <button type="submit" class="btn-step-next" style="background: #2563eb;">
                                        <span class="btn-label-desktop">Save Academic Info &amp; Proceed to Stage 3: {{ $stages[3]['title'] ?? 'Document upload' }} &rarr;</span>
                                        <span class="btn-label-mobile">Save &amp; Proceed to Stage 3 &rarr;</span>
                                    </button>
                                @endif
                            </div>
                        </form>

                        <!-- First Year Under 60% Undertaking Modal -->
                        <div id="firstYearUndertakingModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem;">
                            <div style="background: #ffffff; border-radius: 16px; max-width: 480px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden;">
                                <div style="background: #eff6ff; border-bottom: 1px solid #dbeafe; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #2563eb; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                                        ℹ️
                                    </div>
                                    <div>
                                        <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #1e3a8a;">Academic Undertaking</h3>
                                        <p style="margin: 0.1rem 0 0; font-size: 0.76rem; color: #3b82f6;">First Year Candidate Acknowledgment</p>
                                    </div>
                                </div>
                                <div style="padding: 1.25rem 1.35rem;">
                                    <p style="font-size: 0.86rem; color: #334155; line-height: 1.5; margin: 0 0 1rem;">
                                        As per university norms, candidate must obtain minimum <strong>60% or above</strong> in 12th / Intermediate. Since you are currently in <strong>First Year</strong>, you can proceed by confirming your undertaking below:
                                    </p>
                                    <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1rem;">
                                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; font-size: 0.9rem; font-weight: 700; color: #0f172a;">
                                            <input type="checkbox" id="undertakingModalCheckbox" style="width: 19px; height: 19px; accent-color: #2563eb; cursor: pointer;">
                                            <span>i will get 60%</span>
                                        </label>
                                    </div>
                                    <div id="undertakingModalError" style="display: none; color: #dc2626; font-size: 0.78rem; font-weight: 600; margin-bottom: 0.85rem;">
                                        ⚠️ Please tick "i will get 60%" to proceed.
                                    </div>
                                    <div style="display: flex; justify-content: flex-end; gap: 0.65rem;">
                                        <button type="button" onclick="closeUndertakingModal()" style="padding: 0.5rem 1rem; border-radius: 8px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; font-weight: 600; font-size: 0.84rem; cursor: pointer;">
                                            Cancel
                                        </button>
                                        <button type="button" onclick="confirmUndertakingAndProceed()" style="padding: 0.5rem 1.25rem; border-radius: 8px; border: none; background: #2563eb; color: #ffffff; font-weight: 700; font-size: 0.84rem; cursor: pointer;">
                                            Proceed &rarr;
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Second Year Under 60% Not Eligible Modal -->
                        <div id="secondYearNotEligibleModal" style="display: none; position: fixed; inset: 0; z-index: 99999; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 1rem;">
                            <div style="background: #ffffff; border-radius: 16px; max-width: 480px; width: 100%; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.15), 0 10px 10px -5px rgba(0, 0, 0, 0.04); overflow: hidden;">
                                <div style="background: #fef2f2; border-bottom: 1px solid #fee2e2; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 0.75rem;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #ef4444; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.15rem;">
                                        ⚠️
                                    </div>
                                    <div>
                                        <h3 style="margin: 0; font-size: 1rem; font-weight: 700; color: #991b1b;">Not Eligible</h3>
                                        <p style="margin: 0.1rem 0 0; font-size: 0.76rem; color: #dc2626;">Eligibility Notice</p>
                                    </div>
                                </div>
                                <div style="padding: 1.25rem 1.35rem;">
                                    <p style="font-size: 0.9rem; color: #334155; line-height: 1.5; margin: 0 0 1.25rem;">
                                        You are not eligible according to university norms. Minimum 60% is required in 12th / Intermediate.
                                    </p>
                                    <div style="display: flex; justify-content: flex-end;">
                                        <button type="button" onclick="closeNotEligibleModal()" style="padding: 0.5rem 1.25rem; border-radius: 8px; border: none; background: #dc2626; color: #ffffff; font-weight: 700; font-size: 0.84rem; cursor: pointer;">
                                            OK
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <script>
                            const allStatesData = @json($states);
                            const allDistrictsData = @json($districts);

                            function populateDistrictDropdown(stateId, distSelectId, currentSelectedDist) {
                                const distSelect = document.getElementById(distSelectId);
                                if (!distSelect) return;
                                const previousValue = distSelect.value;
                                distSelect.innerHTML = '<option value="">-- Select District --</option>';

                                let numericStateId = stateId;
                                if (isNaN(stateId) && typeof allStatesData !== 'undefined') {
                                    const foundState = allStatesData.find(s => s.state_name === stateId);
                                    if (foundState) numericStateId = foundState.state_id;
                                }

                                let foundSelected = false;
                                if (typeof allDistrictsData !== 'undefined' && Array.isArray(allDistrictsData)) {
                                    allDistrictsData.forEach(function(d) {
                                        const dStateId = d.STATE_ID ?? d.state_id;
                                        const dDistId = d.DISTRICT_ID ?? d.district_id;
                                        const dName = d.DISTRICT_NAME ?? d.district_name;

                                        if (!numericStateId || dStateId == numericStateId) {
                                            const opt = document.createElement('option');
                                            opt.value = dDistId;
                                            opt.textContent = dName;
                                            opt.setAttribute('data-name', dName);
                                            if (dDistId == currentSelectedDist || dName == currentSelectedDist || dDistId == previousValue || dName == previousValue) {
                                                opt.selected = true;
                                                foundSelected = true;
                                            }
                                            distSelect.appendChild(opt);
                                        }
                                    });
                                }
                                if (!foundSelected && distSelect.options.length > 1) {
                                    distSelect.options[1].selected = true;
                                }
                            }

                            function onTenthStateChange(stateId) {
                                populateDistrictDropdown(stateId, 'geo-tenth-district-select', '{{ $academicDetails?->tenth_school_district }}');
                            }

                            function onInterStateChange(stateId) {
                                populateDistrictDropdown(stateId, 'geo-inter-district-select', '{{ $academicDetails?->pu2_college_district ?: "Guntur" }}');
                            }

                            function onUgStateChange(stateId) {
                                populateDistrictDropdown(stateId, 'geo-ug-district-select', '{{ $academicDetails?->ug_college_district }}');
                            }

                            function onPgStateChange(stateId) {
                                populateDistrictDropdown(stateId, 'geo-pg-district-select', '{{ $academicDetails?->pg_college_district }}');
                            }

                            function onPg2StateChange(stateId) {
                                populateDistrictDropdown(stateId, 'geo-pg2-district-select', '{{ $academicDetails?->pg2_college_district }}');
                            }

                            const interFields = ['inter_college', 'inter_board_of_study', 'inter_city', 'geo-inter-state-select', 'geo-inter-district-select', 'inter_year', 'pu2_marks_conducted', 'pu2_marks_earned'];
                            const ugFields = ['ug_college_name', 'ug_university_board', 'ug_college_city', 'geo-ug-state-select', 'geo-ug-district-select', 'ug_passed_year', 'ug_roll_no', 'ug_marks_conducted', 'ug_marks_earned'];
                            const pg1Fields = ['pg_college_name', 'pg_university_board', 'pg_college_city', 'geo-pg-state-select', 'geo-pg-district-select', 'pg_passed_year', 'pg_roll_no', 'pg_marks_conducted', 'pg_marks_earned'];

                            function setFieldsRequired(fieldIds, isReq) {
                                fieldIds.forEach(function(id) {
                                    const el = document.getElementById(id);
                                    if (el) {
                                        if (isReq) {
                                            el.setAttribute('required', 'required');
                                            el.required = true;
                                        } else {
                                            el.removeAttribute('required');
                                            el.required = false;
                                            if (typeof el.setCustomValidity === 'function') {
                                                el.setCustomValidity('');
                                            }
                                        }
                                    }
                                });
                            }

                            function setDegreeLevel(level) {
                                const hiddenInput = document.getElementById('degree_level_input');
                                if (hiddenInput) hiddenInput.value = level;

                                const ugSec = document.getElementById('sec-ug-details');
                                const pgSec = document.getElementById('sec-pg-details');
                                const pg2Sec = document.getElementById('sec-pg2-details');
                                const firstYearRadioWrapper = document.getElementById('radio-wrapper-first-year');
                                const firstYearRadio = document.getElementById('radio-subset-first-year');
                                const secondYearRadio = document.getElementById('radio-subset-second-year');
                                const optUgFinalYear = document.getElementById('opt-ug-final-year');
                                const spanCutoff = document.getElementById('span-pu2-cutoff');


                                if (level === 'mtech') {
                                    // PG: 10th, Intermediate (default 2nd year), UG (Completed / Final Year)
                                    if (ugSec) ugSec.style.display = 'block';
                                    if (pgSec) pgSec.style.display = 'none';
                                    if (pg2Sec) pg2Sec.style.display = 'none';
                                    if (firstYearRadioWrapper) firstYearRadioWrapper.style.setProperty('display', 'none', 'important');
                                    if (firstYearRadio) {
                                        firstYearRadio.disabled = true;
                                        firstYearRadio.checked = false;
                                    }
                                    if (secondYearRadio) {
                                        secondYearRadio.checked = true;
                                    }
                                    if (optUgFinalYear) optUgFinalYear.style.display = '';
                                    if (spanCutoff) spanCutoff.style.display = 'none';

                                    setFieldsRequired(interFields, true);
                                    setFieldsRequired(ugFields, true);
                                    setFieldsRequired(pg1Fields, false);
                                } else if (level === 'phd') {
                                    // PhD: 10th, Intermediate (default 2nd year), UG (Completed), PG1 (mandatory), PG2 (optional)
                                    if (ugSec) ugSec.style.display = 'block';
                                    if (pgSec) pgSec.style.display = 'block';
                                    if (pg2Sec) pg2Sec.style.display = 'block';
                                    if (firstYearRadioWrapper) firstYearRadioWrapper.style.setProperty('display', 'none', 'important');
                                    if (firstYearRadio) {
                                        firstYearRadio.disabled = true;
                                        firstYearRadio.checked = false;
                                    }
                                    if (secondYearRadio) {
                                        secondYearRadio.checked = true;
                                    }
                                    if (optUgFinalYear) optUgFinalYear.style.display = 'none';
                                    const ugStatusSel = document.getElementById('ug_current_status');
                                    if (ugStatusSel && ugStatusSel.value === 'Final Year') ugStatusSel.value = 'Completed';
                                    if (spanCutoff) spanCutoff.style.display = 'none';

                                    setFieldsRequired(interFields, false);
                                    setFieldsRequired(ugFields, true);
                                    setFieldsRequired(pg1Fields, true);
                                } else {
                                    // UG (btech): 10th, Intermediate (1st year / 2nd year option). UG, PG1, PG2 hidden.
                                    if (ugSec) ugSec.style.display = 'none';
                                    if (pgSec) pgSec.style.display = 'none';
                                    if (pg2Sec) pg2Sec.style.display = 'none';
                                    if (firstYearRadioWrapper) firstYearRadioWrapper.style.removeProperty('display');
                                    if (firstYearRadio) {
                                        firstYearRadio.disabled = false;
                                    }
                                    if (spanCutoff) spanCutoff.style.display = '';

                                    setFieldsRequired(interFields, true);
                                    setFieldsRequired(ugFields, false);
                                    setFieldsRequired(pg1Fields, false);
                                }
                            }

                            function calculateTenthPct() {
                                const conductedIn = document.getElementById('tenth_marks_conducted');
                                const earnedIn = document.getElementById('tenth_marks_earned');
                                const conducted = parseFloat(conductedIn?.value || 0);
                                const earned = parseFloat(earnedIn?.value || 0);
                                const type = document.getElementById('tenth_marks_type')?.value || 'Marks';
                                const pctInput = document.getElementById('tenth_percentage');
                                if (earnedIn && conducted > 0 && earned > conducted) {
                                    earnedIn.setCustomValidity('Marks Earned cannot exceed Total Marks Conducted.');
                                    if (typeof earnedIn.reportValidity === 'function') earnedIn.reportValidity();
                                } else if (earnedIn) {
                                    earnedIn.setCustomValidity('');
                                }
                                if (!pctInput) return;

                                let pct = 0;
                                if (type === 'CGPA') {
                                    if (earned > 0) pct = earned * 9.5;
                                } else {
                                    if (conducted > 0 && earned > 0) pct = (earned / conducted) * 100;
                                }
                                pctInput.value = pct > 0 ? pct.toFixed(2) : '';
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
                                const conductedIn = document.getElementById('pu2_marks_conducted');
                                const earnedIn = document.getElementById('pu2_marks_earned');
                                const conducted = parseFloat(conductedIn?.value || 0);
                                const earned = parseFloat(earnedIn?.value || 0);
                                const type = document.getElementById('pu2_marks_type')?.value || 'Marks';
                                const pctInput = document.getElementById('pu2_percentage');
                                const warning = document.getElementById('pu2_pct_warning');
                                if (earnedIn && conducted > 0 && earned > conducted) {
                                    earnedIn.setCustomValidity('Marks Earned cannot exceed Total Marks Conducted.');
                                    if (typeof earnedIn.reportValidity === 'function') earnedIn.reportValidity();
                                } else if (earnedIn) {
                                    earnedIn.setCustomValidity('');
                                }
                                if (!pctInput) return;

                                let pct = 0;
                                if (type === 'CGPA') {
                                    if (earned > 0) pct = earned * 9.5;
                                } else {
                                    if (conducted > 0 && earned > 0) pct = (earned / conducted) * 100;
                                }
                                pctInput.value = pct > 0 ? pct.toFixed(2) : '';
                                if (warning) {
                                    const degreeLevel = document.getElementById('degree_level_input')?.value || '{{ $degreeLevel }}';
                                    const isPgOrDoc = (degreeLevel === 'mtech' || degreeLevel === 'phd');
                                    warning.style.display = (!isPgOrDoc && pct > 0 && pct < 60) ? 'block' : 'none';
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

                            function calculateUgPct() {
                                const conductedIn = document.getElementById('ug_marks_conducted');
                                const earnedIn = document.getElementById('ug_marks_earned');
                                const conducted = parseFloat(conductedIn?.value || 0);
                                const earned = parseFloat(earnedIn?.value || 0);
                                const type = document.getElementById('ug_marks_type')?.value || 'Marks';
                                const pctInput = document.getElementById('ug_percentage');
                                if (earnedIn && conducted > 0 && earned > conducted) {
                                    earnedIn.setCustomValidity('Marks Earned cannot exceed Total Marks Conducted.');
                                    if (typeof earnedIn.reportValidity === 'function') earnedIn.reportValidity();
                                } else if (earnedIn) {
                                    earnedIn.setCustomValidity('');
                                }
                                if (!pctInput) return;

                                let pct = 0;
                                if (type === 'CGPA') {
                                    if (earned > 0) pct = earned * 9.5;
                                } else {
                                    if (conducted > 0 && earned > 0) pct = (earned / conducted) * 100;
                                }
                                pctInput.value = pct > 0 ? pct.toFixed(2) : '';
                            }

                            function onUgMarksTypeChange(val) {
                                const cond = document.getElementById('ug_marks_conducted');
                                if (val === 'CGPA') {
                                    if (cond) cond.value = '10';
                                } else {
                                    if (cond && (cond.value === '10' || cond.value === '10.0')) cond.value = '1000';
                                }
                                calculateUgPct();
                            }

                            function calculatePgPct() {
                                const conductedIn = document.getElementById('pg_marks_conducted');
                                const earnedIn = document.getElementById('pg_marks_earned');
                                const conducted = parseFloat(conductedIn?.value || 0);
                                const earned = parseFloat(earnedIn?.value || 0);
                                const type = document.getElementById('pg_marks_type')?.value || 'Marks';
                                const pctInput = document.getElementById('pg_percentage');
                                if (earnedIn && conducted > 0 && earned > conducted) {
                                    earnedIn.setCustomValidity('Marks Earned cannot exceed Total Marks Conducted.');
                                    if (typeof earnedIn.reportValidity === 'function') earnedIn.reportValidity();
                                } else if (earnedIn) {
                                    earnedIn.setCustomValidity('');
                                }
                                if (!pctInput) return;

                                let pct = 0;
                                if (type === 'CGPA') {
                                    if (earned > 0) pct = earned * 9.5;
                                } else {
                                    if (conducted > 0 && earned > 0) pct = (earned / conducted) * 100;
                                }
                                pctInput.value = pct > 0 ? pct.toFixed(2) : '';
                            }

                            function onPgMarksTypeChange(val) {
                                const cond = document.getElementById('pg_marks_conducted');
                                if (val === 'CGPA') {
                                    if (cond) cond.value = '10';
                                } else {
                                    if (cond && (cond.value === '10' || cond.value === '10.0')) cond.value = '1000';
                                }
                                calculatePgPct();
                            }

                            function calculatePg2Pct() {
                                const conductedIn = document.getElementById('pg2_marks_conducted');
                                const earnedIn = document.getElementById('pg2_marks_earned');
                                const conducted = parseFloat(conductedIn?.value || 0);
                                const earned = parseFloat(earnedIn?.value || 0);
                                const type = document.getElementById('pg2_marks_type')?.value || 'Marks';
                                const pctInput = document.getElementById('pg2_percentage');
                                if (earnedIn && conducted > 0 && earned > conducted) {
                                    earnedIn.setCustomValidity('Marks Earned cannot exceed Total Marks Conducted.');
                                    if (typeof earnedIn.reportValidity === 'function') earnedIn.reportValidity();
                                } else if (earnedIn) {
                                    earnedIn.setCustomValidity('');
                                }
                                if (!pctInput) return;

                                let pct = 0;
                                if (type === 'CGPA') {
                                    if (earned > 0) pct = earned * 9.5;
                                } else {
                                    if (conducted > 0 && earned > 0) pct = (earned / conducted) * 100;
                                }
                                pctInput.value = pct > 0 ? pct.toFixed(2) : '';
                            }

                            function onPg2MarksTypeChange(val) {
                                const cond = document.getElementById('pg2_marks_conducted');
                                if (val === 'CGPA') {
                                    if (cond) cond.value = '10';
                                } else {
                                    if (cond && (cond.value === '10' || cond.value === '10.0')) cond.value = '1000';
                                }
                                calculatePg2Pct();
                            }

                            function getSelectedSubsetChoice() {
                                const radios = document.getElementsByName('subset_choice');
                                for (let i = 0; i < radios.length; i++) {
                                    if (radios[i].checked && !radios[i].disabled) return radios[i].value;
                                }
                                const degreeLevel = document.getElementById('degree_level_input')?.value || '{{ $degreeLevel }}';
                                if (degreeLevel === 'mtech' || degreeLevel === 'phd') {
                                    return 'Second Year';
                                }
                                return 'First Year';
                            }

                            function openUndertakingModal() {
                                const modal = document.getElementById('firstYearUndertakingModal');
                                if (modal) {
                                    modal.style.display = 'flex';
                                    const cb = document.getElementById('undertakingModalCheckbox');
                                    if (cb) cb.checked = false;
                                    const err = document.getElementById('undertakingModalError');
                                    if (err) err.style.display = 'none';
                                }
                            }

                            function closeUndertakingModal() {
                                const modal = document.getElementById('firstYearUndertakingModal');
                                if (modal) modal.style.display = 'none';
                            }

                            function openNotEligibleModal() {
                                const modal = document.getElementById('secondYearNotEligibleModal');
                                if (modal) modal.style.display = 'flex';
                            }

                            function closeNotEligibleModal() {
                                const modal = document.getElementById('secondYearNotEligibleModal');
                                if (modal) modal.style.display = 'none';
                            }

                            function confirmUndertakingAndProceed() {
                                const cb = document.getElementById('undertakingModalCheckbox');
                                const err = document.getElementById('undertakingModalError');
                                if (!cb || !cb.checked) {
                                    if (err) err.style.display = 'block';
                                    return;
                                }
                                if (err) err.style.display = 'none';
                                const hiddenIn = document.getElementById('undertaking_60_pct_input');
                                if (hiddenIn) hiddenIn.value = 'yes';
                                closeUndertakingModal();
                                const form = document.getElementById('step2AcademicForm');
                                if (form) form.submit();
                            }

                            function getActiveRequiredFields() {
                                const degreeLevel = document.getElementById('degree_level_input')?.value || '{{ $degreeLevel }}';
                                const isPhd = (degreeLevel === 'phd');
                                const isPg = (degreeLevel === 'mtech');
                                const isUg = (!isPhd && !isPg);

                                let fields = [];
                                if (isUg || isPg) {
                                    fields = fields.concat(interFields);
                                }
                                if (isPg || isPhd) {
                                    fields = fields.concat(ugFields);
                                }
                                if (isPhd) {
                                    fields = fields.concat(pg1Fields);
                                }
                                return fields;
                            }

                            function validateStep2Form(form) {
                                calculatePu2Pct();
                                calculateUgPct();
                                calculatePgPct();
                                calculatePg2Pct();

                                const degreeLevel = document.getElementById('degree_level_input')?.value || '{{ $degreeLevel }}';
                                const isPhd = (degreeLevel === 'phd');
                                const isPg = (degreeLevel === 'mtech');
                                const isUg = (!isPhd && !isPg);

                                // Check all active required fields in order: if any empty, scroll, focus, and reportValidity
                                const fieldsToCheck = getActiveRequiredFields();
                                for (let i = 0; i < fieldsToCheck.length; i++) {
                                    const el = document.getElementById(fieldsToCheck[i]);
                                    if (!el) continue;
                                    const val = el.value ? el.value.trim() : '';
                                    if (val === '' || val.startsWith('-- Select')) {
                                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                        el.focus();
                                        if (typeof el.reportValidity === 'function') {
                                            el.setCustomValidity('Please fill out this field.');
                                            el.reportValidity();
                                            el.oninput = function() { el.setCustomValidity(''); };
                                            el.onchange = function() { el.setCustomValidity(''); };
                                        }
                                        return false;
                                    }
                                }

                                // Validate Marks Earned <= Total Marks Conducted for all qualifications
                                const marksChecks = [
                                    { earned: 'tenth_marks_earned', cond: 'tenth_marks_conducted', name: '10th' },
                                    { earned: 'pu2_marks_earned', cond: 'pu2_marks_conducted', name: '12th / Intermediate' },
                                    { earned: 'ug_marks_earned', cond: 'ug_marks_conducted', name: 'UG' },
                                    { earned: 'pg_marks_earned', cond: 'pg_marks_conducted', name: 'PG 1' },
                                    { earned: 'pg2_marks_earned', cond: 'pg2_marks_conducted', name: 'PG 2' },
                                ];
                                for (let m = 0; m < marksChecks.length; m++) {
                                    const eEl = document.getElementById(marksChecks[m].earned);
                                    const cEl = document.getElementById(marksChecks[m].cond);
                                    if (eEl && cEl && eEl.value !== '' && cEl.value !== '') {
                                        const eVal = parseFloat(eEl.value);
                                        const cVal = parseFloat(cEl.value);
                                        if (cVal > 0 && eVal > cVal) {
                                            eEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            eEl.focus();
                                            if (typeof eEl.reportValidity === 'function') {
                                                eEl.setCustomValidity('Marks Earned (' + eVal + ') cannot be greater than Total Marks Conducted (' + cVal + ') in ' + marksChecks[m].name + '.');
                                                eEl.reportValidity();
                                                eEl.oninput = function() { eEl.setCustomValidity(''); };
                                            }
                                            return false;
                                        }
                                    }
                                }

                                if (isPg || isPhd) {
                                    return true;
                                }

                                const pu2Pct = parseFloat(document.getElementById('pu2_percentage')?.value || 0);
                                const subsetChoice = getSelectedSubsetChoice();
                                const isSecondYear = (subsetChoice.toLowerCase() === 'second year');

                                if (isSecondYear) {
                                    if (pu2Pct < 60) {
                                        openNotEligibleModal();
                                        const earnedInput = document.getElementById('pu2_marks_earned');
                                        if (earnedInput) {
                                            earnedInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            earnedInput.focus();
                                        }
                                        return false;
                                    }
                                } else {
                                    // First Year
                                    if (pu2Pct < 60) {
                                        const undertakingVal = document.getElementById('undertaking_60_pct_input')?.value;
                                        if (undertakingVal !== 'yes') {
                                            openUndertakingModal();
                                            return false;
                                        }
                                    }
                                }
                                return true;
                            }

                            function togglePg1DocFields(val) {
                                const cmm = document.getElementById('pg1_cmm_container');
                                const pc = document.getElementById('pg1_pc_container');
                                if (!cmm || !pc) return;
                                if (val === 'CMM') {
                                    cmm.style.display = 'grid';
                                    pc.style.display = 'none';
                                } else if (val === 'PC_OD') {
                                    cmm.style.display = 'none';
                                    pc.style.display = 'grid';
                                } else {
                                    cmm.style.display = 'grid';
                                    pc.style.display = 'grid';
                                }
                            }

                            function togglePg2DocFields(val) {
                                const cmm = document.getElementById('pg2_cmm_container');
                                const pc = document.getElementById('pg2_pc_container');
                                if (!cmm || !pc) return;
                                if (val === 'CMM') {
                                    cmm.style.display = 'grid';
                                    pc.style.display = 'none';
                                } else if (val === 'PC_OD') {
                                    cmm.style.display = 'none';
                                    pc.style.display = 'grid';
                                } else if (val === 'BOTH') {
                                    cmm.style.display = 'grid';
                                    pc.style.display = 'grid';
                                } else {
                                    cmm.style.display = 'none';
                                    pc.style.display = 'none';
                                }
                            }

                            document.addEventListener('DOMContentLoaded', function() {
                                setDegreeLevel('{{ $degreeLevel }}');
                                if (!document.getElementById('tenth_percentage')?.value) {
                                    calculateTenthPct();
                                }
                                if (!document.getElementById('pu2_percentage')?.value) {
                                    calculatePu2Pct();
                                }
                                if (!document.getElementById('ug_percentage')?.value) {
                                    calculateUgPct();
                                }
                                if (!document.getElementById('pg_percentage')?.value) {
                                    calculatePgPct();
                                }
                                if (!document.getElementById('pg2_percentage')?.value) {
                                    calculatePg2Pct();
                                }

                                const tenthState = document.getElementById('geo-tenth-state-select');
                                if (tenthState && tenthState.value) {
                                    onTenthStateChange(tenthState.value);
                                }
                                const interStateSelect = document.getElementById('geo-inter-state-select');
                                if (interStateSelect && interStateSelect.value) {
                                    onInterStateChange(interStateSelect.value);
                                }
                                const ugState = document.getElementById('geo-ug-state-select');
                                if (ugState && ugState.value) {
                                    onUgStateChange(ugState.value);
                                }
                                const pgState = document.getElementById('geo-pg-state-select');
                                if (pgState && pgState.value) {
                                    onPgStateChange(pgState.value);
                                }
                                const pg2State = document.getElementById('geo-pg2-state-select');
                                if (pg2State && pg2State.value) {
                                    onPg2StateChange(pg2State.value);
                                }

                                @if(isset($errors) && $errors->any())
                                    @php
                                        $firstErrorField = array_key_first($errors->toArray());
                                        $fieldMap = [
                                            'inter_college' => 'inter_college',
                                            'inter_board_of_study' => 'inter_board_of_study',
                                            'city' => 'inter_city',
                                            'jr_state' => 'geo-inter-state-select',
                                            'jr_district' => 'geo-inter-district-select',
                                            'inter_year' => 'inter_year',
                                            'pu2_marks_conducted' => 'pu2_marks_conducted',
                                            'pu2_marks_earned' => 'pu2_marks_earned',
                                            'pu2_percentage' => 'pu2_percentage',
                                            'ug_college_name' => 'ug_college_name',
                                            'ug_university_board' => 'ug_university_board',
                                            'ug_college_city' => 'ug_college_city',
                                            'ug_college_state' => 'geo-ug-state-select',
                                            'ug_college_district' => 'geo-ug-district-select',
                                            'ug_passed_year' => 'ug_passed_year',
                                            'ug_roll_no' => 'ug_roll_no',
                                            'ug_marks_conducted' => 'ug_marks_conducted',
                                            'ug_marks_earned' => 'ug_marks_earned',
                                            'pg_college_name' => 'pg_college_name',
                                            'pg_university_board' => 'pg_university_board',
                                            'pg_college_city' => 'pg_college_city',
                                            'pg_college_state' => 'geo-pg-state-select',
                                            'pg_college_district' => 'geo-pg-district-select',
                                            'pg_passed_year' => 'pg_passed_year',
                                            'pg_roll_no' => 'pg_roll_no',
                                            'pg_marks_conducted' => 'pg_marks_conducted',
                                            'pg_marks_earned' => 'pg_marks_earned',
                                        ];
                                        $targetId = $fieldMap[$firstErrorField] ?? $firstErrorField;
                                    @endphp
                                    setTimeout(function() {
                                        const errEl = document.getElementById('{{ $targetId }}');
                                        if (errEl) {
                                            errEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            errEl.focus();
                                            if (typeof errEl.reportValidity === 'function') {
                                                errEl.setCustomValidity('{{ addslashes($errors->first()) }}');
                                                errEl.reportValidity();
                                                errEl.oninput = function() { errEl.setCustomValidity(''); };
                                                errEl.onchange = function() { errEl.setCustomValidity(''); };
                                            }
                                        }
                                    }, 250);
                                @endif
                            });
                        </script>
                    </div>
                </div>

            <!-- ================= STAGE 3: APPLICATION UPLOAD (CRMM0006) ================= -->
            @elseif($activeStep === 3)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 3 of {{ count($stages) }} &bull; Document Verification</div>
                        <h3>{{ $stages[3]['title'] ?? 'Document upload' }}</h3>
                        <p>Upload and submit required academic certificates, identity documents, and photograph.</p>
                    </div>
                    @if(($stages[3]['status'] ?? '') === 'completed')
                        <span class="step-header-tag tag-success">✓ Uploaded</span>
                    @else
                        <span class="step-header-tag tag-pending">In Progress</span>
                    @endif
                </div>

                @if($isPaid)
                    <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 10px; padding: 0.85rem 1.15rem; margin: 1rem 1.5rem 0; display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.25rem;">🔒</span>
                        <div>
                            <div style="font-weight: 700; color: #1e40af; font-size: 0.88rem;">Documents Fixed (Read-Only)</div>
                            <div style="color: #3b82f6; font-size: 0.78rem;">Your uploaded documents are locked because admission fee payment has been completed. Details can only be viewed.</div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('dashboards.save-step') }}" method="POST" enctype="multipart/form-data" class="step-panel-body" id="stage3-upload-form">
                    @csrf
                    <input type="hidden" name="step" value="3">
                    <input type="hidden" name="degree_level" id="stage3_degree_level_input" value="{{ $degreeLevel ?? 'btech' }}">

                    @php
                        $docDefinitions = [
                            'marksheet10' => [
                                'icon' => '📜',
                                'title' => '10th SSC Marks Memo',
                                'sub' => 'Class X original certificate or DigiLocker attested copy',
                                'levels' => 'btech mtech phd',
                                'is_common' => false,
                                'is_required' => false,
                            ],
                            'marksheet12' => [
                                'icon' => '📑',
                                'title' => '12th Intermediate / Diploma Memo',
                                'sub' => 'Short memo or internet copy with principal attestation',
                                'levels' => 'btech mtech phd',
                                'is_common' => false,
                                'is_required' => false,
                            ],
                            'btech' => [
                                'icon' => '🎓',
                                'title' => 'B.Tech / UG Degree Memo',
                                'sub' => 'Consolidated Marks Memo (CMM) or Provisional Certificate',
                                'levels' => 'mtech phd',
                                'is_common' => false,
                                'is_required' => false,
                            ],
                            'mtech' => [
                                'icon' => '📚',
                                'title' => 'M.Tech / PG Memo',
                                'sub' => 'Postgraduate Marks Memo or Degree Certificate',
                                'levels' => 'mtech',
                                'is_common' => false,
                                'is_required' => false,
                            ],
                            'pg1_cmm' => [
                                'icon' => '📜',
                                'title' => 'PG 1 Consolidated Marks Memo (CMM)',
                                'sub' => 'Consolidated Marks Memo for PG 1 (At least one mandatory with PC/OD)*',
                                'levels' => 'phd',
                                'is_common' => false,
                                'is_required' => false,
                                'is_pg1_group' => true,
                            ],
                            'pg1_pc' => [
                                'icon' => '🎓',
                                'title' => 'PG 1 Provisional Certificate / Original Degree (PC/OD)',
                                'sub' => 'Provisional Certificate / Original Degree for PG 1 (At least one mandatory with CMM)*',
                                'levels' => 'phd',
                                'is_common' => false,
                                'is_required' => false,
                                'is_pg1_group' => true,
                            ],
                            'pg2_cmm' => [
                                'icon' => '📜',
                                'title' => 'PG 2 Consolidated Marks Memo (CMM)',
                                'sub' => 'Consolidated Marks Memo for PG 2 (Optional)',
                                'levels' => 'phd',
                                'is_common' => false,
                                'is_required' => false,
                            ],
                            'pg2_pc' => [
                                'icon' => '🎓',
                                'title' => 'PG 2 Provisional Certificate / Original Degree (PC/OD)',
                                'sub' => 'Provisional Certificate / Original Degree for PG 2 (Optional)',
                                'levels' => 'phd',
                                'is_common' => false,
                                'is_required' => false,
                            ],
                            'pg' => [
                                'icon' => '🔬',
                                'title' => 'Doctoral / Advanced PG Memo',
                                'sub' => 'Doctoral coursework or post-master qualification certificate',
                                'levels' => 'phd',
                                'is_common' => false,
                                'is_required' => false,
                            ],
                            'experience' => [
                                'icon' => '💼',
                                'title' => 'Work Experience Letter / Certificate',
                                'sub' => 'Service certificate or work experience letter (Optional)',
                                'levels' => 'btech mtech phd',
                                'is_common' => false,
                                'is_required' => false,
                            ],
                            'aadhaar' => [
                                'icon' => '🪪',
                                'title' => 'Candidate Aadhaar Card',
                                'sub' => 'Government proof of identity & native address',
                                'levels' => 'btech mtech phd',
                                'is_common' => true,
                                'is_required' => false,
                            ],
                            'photo' => [
                                'icon' => '🖼️',
                                'title' => 'Passport Size Photograph',
                                'sub' => 'Recent studio color photo with white background',
                                'levels' => 'btech mtech phd',
                                'is_common' => true,
                                'is_required' => false,
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
                                            @if(!empty($docMeta['is_pg1_group']))
                                                <span style="font-size: 0.68rem; font-weight: 700; background: #faf5ff; color: #7c3aed; padding: 0.15rem 0.5rem; border-radius: 9999px; border: 1px solid #d8b4fe;">
                                                    Any 1 Mandatory *
                                                </span>
                                            @endif
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
                                            @if($docMeta['is_required'] || !empty($docMeta['is_pg1_group']))
                                                <span style="color: #dc2626;" title="Mandatory field">*</span>
                                            @endif
                                        </h4>
                                        <p class="kyc-doc-sub">{{ $docMeta['sub'] }}</p>
                                    </div>
                                    @if($docFile)
                                        <div style="font-size:0.8rem; color:#059669; font-weight:700; display:flex; align-items:center; justify-content:space-between; gap:0.5rem; margin-bottom:0.4rem; background: #ecfdf5; padding: 0.4rem 0.6rem; border-radius: 6px; border: 1px solid #a7f3d0;">
                                            <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">📄 {{ $docFile['filename'] }}</span>
                                            <a href="{{ $docFile['url'] }}" target="_blank" style="color:#1d4ed8; text-decoration:underline; flex-shrink:0;">View / Download</a>
                                        </div>
                                    @endif
                                    @if(!$isPaid)
                                        <div id="file-feedback-{{ $docKey }}" style="display: none; font-size: 0.74rem; font-weight: 700; color: #15803d; margin-bottom: 0.35rem;"></div>
                                        <input type="file"
                                               name="docs[{{ $docKey }}]"
                                               id="file-input-{{ $docKey }}"
                                               accept=".pdf,.jpg,.jpeg,.png"
                                               class="field-input doc-file-input"
                                               style="padding:0.4rem; font-size:0.75rem;"
                                               onchange="handleDocFileChange(this, '{{ $docKey }}')">
                                    @elseif(!$docFile)
                                        <div style="font-size: 0.76rem; color: #94a3b8; font-style: italic; margin-top: 0.25rem;">
                                            No document uploaded
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-step-prev">
                            &larr; Stage 2: {{ $stages[2]['title'] ?? 'Academic Info' }}
                        </a>
                        @if($isPaid)
                            <a href="{{ route('dashboards.index', ['step' => 4]) }}" class="btn-step-next" style="background: #2563eb; text-decoration: none;">
                                <span class="btn-label-desktop">Next: Stage 4 - {{ $stages[4]['title'] ?? 'Fee Payment' }} &rarr;</span>
                                <span class="btn-label-mobile">Next: Stage 4 &rarr;</span>
                            </a>
                        @else
                            <button type="submit" class="btn-step-next" style="background: #2563eb;">
                                <span class="btn-label-desktop">Save Documents &amp; Proceed to Stage 4: {{ $stages[4]['title'] ?? 'Fee Payment' }} &rarr;</span>
                                <span class="btn-label-mobile">Save &amp; Proceed to Stage 4 &rarr;</span>
                            </button>
                        @endif
                    </div>

                    <script>
                        function setStage3DegreeLevel(level) {
                            const hiddenInput = document.getElementById('stage3_degree_level_input');
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
                            setStage3DegreeLevel('{{ $degreeLevel ?? 'btech' }}');

                            const form = document.getElementById('stage3-upload-form');
                            if (form) {
                                form.addEventListener('submit', function(e) {
                                    const lvl = document.getElementById('stage3_degree_level_input')?.value || '{{ $degreeLevel ?? "btech" }}';
                                    if (lvl === 'phd') {
                                        const hasCmm = Boolean({{ !empty($uploadedDocs['pg1_cmm']) ? 'true' : 'false' }}) || (document.getElementById('file-input-pg1_cmm')?.files?.length > 0);
                                        const hasPc = Boolean({{ !empty($uploadedDocs['pg1_pc']) ? 'true' : 'false' }}) || (document.getElementById('file-input-pg1_pc')?.files?.length > 0);

                                        if (!hasCmm && !hasPc) {
                                            e.preventDefault();
                                            const cmmCard = document.getElementById('doc-card-pg1_cmm');
                                            if (cmmCard) cmmCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
                                            const input = document.getElementById('file-input-pg1_cmm');
                                            if (input) {
                                                input.focus();
                                                if (typeof input.reportValidity === 'function') {
                                                    input.setCustomValidity('Please upload at least one document for PG 1: either Consolidated Marks Memo (CMM) or Provisional Certificate / Original Degree (PC/OD).');
                                                    input.reportValidity();
                                                    input.onchange = function() { input.setCustomValidity(''); };
                                                }
                                            }
                                            return false;
                                        }
                                    }
                                });
                            }
                        });
                    </script>
                </form>

            <!-- ================= STAGE 4: FEE PAYMENT ================= -->
            @elseif($activeStep === 4)
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage 4 of {{ count($stages) }} &bull; Seat Confirmation</div>
                        <h3>
                            <span>{{ $stages[4]['title'] ?? 'Fee Payment' }}</span>
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
                                    Your provisional seat confirmation is successfully recorded in the university admissions ledger. You may now download your accounts-verified fee receipt below and proceed to @if(($degreeLevel ?? '') === 'phd') application form preview & download. @else exam slot booking. @endif
                                </p>
                                <div style="display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap; align-items: center;">
                                    <a href="{{ route('payu.receipt') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #0f172a; color: #ffffff; padding: 0.6rem 1.35rem; border-radius: 8px; font-size: 0.84rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(15, 23, 42, 0.2);">
                                        <span>📥 Download / View Official Receipt</span>
                                        <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </a>
                                    <a href="{{ route('dashboards.index', ['step' => 5]) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: #2563eb; color: #ffffff; padding: 0.6rem 1.35rem; border-radius: 8px; font-size: 0.84rem; font-weight: 700; text-decoration: none; box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);">
                                        <span>Proceed to Step 5: {{ $stages[5]['title'] ?? (($degreeLevel ?? '') === 'phd' ? 'Application Download' : 'Exam Slot Booking') }} &rarr;</span>
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
                                <p style="font-size: 0.76rem; color: #64748b; margin: 0.1rem 0 0 0;">Pay securely to lock your provisional registration and instantly receive your verified admission receipt.</p>
                            </div>

                            <!-- Admission Fee Summary Box with Concessions -->
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
                                    <span>Payment Mode:</span>
                                    <span style="font-weight: 600; color: #059669;">{{ $feeBreakdown['net_payable'] <= 0 ? 'Coupon 100% Concession' : 'Online Admission Settlement' }}</span>
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
                                        <span>💳 Pay ₹<span id="btnPayAmountText">{{ number_format($feeBreakdown['net_payable']) }}</span> & Issue Receipt</span>
                                    @endif
                                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- DOWNLOAD CARDS -->


                    <div class="step-footer-actions" style="padding-top: 1rem; margin-top: 0.25rem;">
                        <a href="{{ route('dashboards.index', ['step' => 3]) }}" class="btn-step-prev" style="text-decoration: none;">
                            &larr; Stage 3: {{ $stages[3]['title'] ?? 'Document upload' }}
                        </a>
                        <div style="display: flex; align-items: center; gap: 0.45rem;">
                            <span style="font-size: 0.78rem; color: #64748b; font-weight: 500;">Status:</span>
                            @if($isPaid)
                                <span class="step-header-tag tag-success">✓ Fee Paid</span>
                            @else
                                <span class="step-header-tag tag-pending">Payment Pending</span>
                            @endif
                        </div>
                        @if($isPaid)
                            <a href="{{ route('dashboards.index', ['step' => 5]) }}" class="btn-step-next" style="text-decoration: none; background: #2563eb; color: #ffffff; padding: 0.55rem 1.25rem; font-weight: 700; border-radius: 8px; display: inline-flex; align-items: center; gap: 0.45rem;">
                                <span>Proceed to Step 5: {{ $stages[5]['title'] ?? (($degreeLevel ?? '') === 'phd' ? 'Application Download' : 'Exam Slot Booking') }} &rarr;</span>
                            </a>
                        @else
                            <button type="button" disabled class="btn-step-prev" style="opacity: 0.5; cursor: not-allowed; background: #f1f5f9; color: #94a3b8; border: 1px solid #cbd5e1; display: inline-flex; align-items: center; gap: 0.45rem;" title="Please complete your Admission Fee Payment to unlock Step 5">
                                <span>🔒 Step 5: {{ $stages[5]['title'] ?? (($degreeLevel ?? '') === 'phd' ? 'Application Download' : 'Exam Slot Booking') }} (Locked until Fee Payment)</span>
                            </button>
                        @endif
                    </div>
                </div>

            <!-- ================= STAGE 5: EXAM SLOT BOOKING (master.menus: EXAM00002) ================= -->
            @elseif($activeStep === 5 && ($degreeLevel ?? '') !== 'phd')
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

                    $currentSlot = $examSchedule?->exam_slot ?? 'Slot 1 11:00 AM to 1:00 PM';
                    $hasBookedSlot = !empty($examSchedule?->examdate) && !empty($examSchedule?->exam_slot);

                    // Cutoff: 1 day before booked exam date at 5:00 PM
                    // (e.g. if student has exam tomorrow, they can edit up to today evening 5:00 PM; afterwards locked)
                    $editDeadline = $existingDate
                        ? \Carbon\Carbon::parse($existingDate)->subDay()->setTime(17, 0, 0)
                        : null;

                    $isSlotLocked = $hasBookedSlot && ($editDeadline ? $now->gt($editDeadline) : false);
                    $canEditSlot = !$hasBookedSlot || !$isSlotLocked;
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
                                        📅 Booked Date: <strong>{{ \Carbon\Carbon::parse($examSchedule->examdate)->format('d M Y') }}</strong> &bull; Selected Slot: <strong>{{ str_replace(['S1 ', 'S2 '], ['Slot 1 ', 'Slot 2 '], $examSchedule->exam_slot) }}</strong>
                                    </div>
                                    <div style="font-size: 0.76rem; color: #64748b; margin-top: 2px;">
                                        Examination Center: <strong>{{ $campusName ?: 'Vignan Main Campus (Vadlamudi)' }}</strong> &bull; Status: Confirmed
                                    </div>
                                </div>
                            </div>
                            <div style="font-size: 0.78rem; font-weight: 700; background: #ffffff; padding: 5px 12px; border-radius: 8px; {{ $isSlotLocked ? 'color: #b45309; border: 1px solid #fde68a;' : 'color: #15803d; border: 1px solid #bbf7d0;' }}">
                                @if($isSlotLocked)
                                    🔒 Exam slot is confirmed &amp; locked
                                @else
                                    ✏️ Rescheduling open until {{ $editDeadline ? $editDeadline->format('d M Y, 5:00 PM') : '' }}
                                @endif
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
                                <label class="field-label">
                                    Exam Date <span class="required-star">*</span>
                                    @if($isSlotLocked)
                                        <span style="font-size: 0.72rem; font-weight: 700; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; padding: 2px 7px; border-radius: 9999px; margin-left: 6px;">
                                            🔒 Date Locked
                                        </span>
                                    @elseif($hasBookedSlot)
                                        <span style="font-size: 0.72rem; font-weight: 700; color: #15803d; background: #dcfce7; border: 1px solid #bbf7d0; padding: 2px 7px; border-radius: 9999px; margin-left: 6px;">
                                            ✏️ Reschedule Allowed
                                        </span>
                                    @endif
                                </label>
                                <input type="date"
                                       id="stage5ExamDateInput"
                                       name="examdate"
                                       class="field-input"
                                       min="{{ $minExamDate }}"
                                       value="{{ $defaultDate }}"
                                       {{ $isSlotLocked ? 'readonly style=background-color:#f1f5f9;cursor:not-allowed;pointer-events:none;color:#475569;' : '' }}
                                       required>
                                @if($isSlotLocked)
                                    <input type="hidden" name="examdate" value="{{ $defaultDate }}">
                                    <div style="margin-top: 6px; font-size: 0.76rem; color: #b45309; background: #fffbeb; border: 1px solid #fde68a; border-radius: 6px; padding: 6px 10px; display: flex; align-items: center; gap: 6px;">
                                        <span>🔒</span>
                                        <span>Your exam date is now locked. Cutoff deadline was {{ $editDeadline ? $editDeadline->format('d M Y, 5:00 PM') : '' }}.</span>
                                    </div>
                                @elseif($hasBookedSlot)
                                    <div style="margin-top: 6px; font-size: 0.76rem; color: #15803d; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; padding: 6px 10px; display: flex; align-items: center; gap: 6px;">
                                        <span>⏰</span>
                                        <span>You can edit your exam date &amp; slot any number of times before <strong>{{ $editDeadline ? $editDeadline->format('d M Y, 5:00 PM') : '' }}</strong>.</span>
                                    </div>
                                @else
                                    <div style="margin-top: 6px; font-size: 0.76rem; color: #1e40af; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 6px 10px; display: flex; align-items: center; gap: 6px;">
                                        <span>⏰</span>
                                        <span><strong>5:00 PM Daily Cutoff:</strong> {{ $cutoffMessage }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Slot Selection (S1 11 AM - 1 PM or S2 2 PM - 4 PM) -->
                            <div class="field-group">
                                <label class="field-label">
                                    Select Slot <span class="required-star">*</span>
                                    @if($isSlotLocked)
                                        <span style="font-size: 0.72rem; font-weight: 700; color: #b45309; background: #fef3c7; border: 1px solid #fde68a; padding: 2px 7px; border-radius: 9999px; margin-left: 6px;">
                                            🔒 Slot Locked
                                        </span>
                                    @endif
                                </label>
                                <select id="stage5ExamSlotSelect" name="exam_slot" class="mockup-select" {{ $isSlotLocked ? 'disabled style=background-color:#f1f5f9;cursor:not-allowed;pointer-events:none;color:#475569;' : '' }} required>
                                    <option value="Slot 1 11:00 AM to 1:00 PM" {{ (str_contains($currentSlot, '11') || str_contains($currentSlot, 'Slot 1') || str_contains($currentSlot, 'S1')) && !str_contains($currentSlot, 'Slot 2') && !str_contains($currentSlot, 'S2') ? 'selected' : '' }}>
                                        Slot 1 11:00 AM to 1:00 PM (Morning Session)
                                    </option>
                                    <option value="Slot 2 2:00 PM to 4:00 PM" {{ (str_contains($currentSlot, '2:00') || str_contains($currentSlot, 'Slot 2') || str_contains($currentSlot, 'S2')) ? 'selected' : '' }}>
                                        Slot 2 2:00 PM to 4:00 PM (Afternoon Session)
                                    </option>
                                </select>
                                @if($isSlotLocked)
                                    <input type="hidden" name="exam_slot" value="{{ $currentSlot }}">
                                @endif
                                <div style="margin-top: 6px; font-size: 0.76rem; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 6px; padding: 6px 10px; display: flex; align-items: center; gap: 6px;">
                                    <span>✓</span>
                                    <span id="slotLiveStatus">
                                        @php
                                            $displaySlot = str_replace(['S1 ', 'S2 '], ['Slot 1 ', 'Slot 2 '], $currentSlot);
                                        @endphp
                                        @if($isSlotLocked)
                                            Confirmed slot: <strong>{{ $displaySlot }}</strong> (Standard 2-hour CBT entrance test session). Slot selection is locked.
                                        @else
                                            Current slot: <strong>{{ $displaySlot }}</strong> (Standard 2-hour CBT entrance test session).
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => 4]) }}" class="btn-step-prev">
                            <span class="btn-label-desktop">&larr; Stage 4: {{ $stages[4]['title'] ?? 'Fee Payment' }}</span>
                            <span class="btn-label-mobile">&larr; Stage 4: Fee Payment</span>
                        </a>
                        <button type="submit" class="btn-step-next" style="background: #2563eb;">
                            @if($isSlotLocked)
                                <span class="btn-label-desktop">Proceed to Stage 6: Application Download &rarr;</span>
                                <span class="btn-label-mobile">Proceed to Stage 6 &rarr;</span>
                            @elseif($hasBookedSlot)
                                <span class="btn-label-desktop">Update Slot &amp; Proceed to Stage 6: {{ $stages[6]['title'] ?? 'Application Download' }} &rarr;</span>
                                <span class="btn-label-mobile">Update &amp; Proceed to Stage 6 &rarr;</span>
                            @else
                                <span class="btn-label-desktop">Confirm Slot &amp; Proceed to Stage 6: {{ $stages[6]['title'] ?? 'Application Download' }} &rarr;</span>
                                <span class="btn-label-mobile">Confirm &amp; Proceed to Stage 6 &rarr;</span>
                            @endif
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
                                    if (this.value.includes('Slot 1') || this.value.includes('S1')) {
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
            @elseif(($activeStep === 6 && ($degreeLevel ?? '') !== 'phd') || ($activeStep === 5 && ($degreeLevel ?? '') === 'phd'))
                @php
                    $appStudent = $applicationData ?? \App\Http\Controllers\ApplicationController::buildStudentApplicationData((string) ($appNo ?: ($user?->usercode ?? '0')));
                    $student = $appStudent;
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
                    $progUpper = strtoupper(trim((string)($appStudent->program ?? '')));
                    $levelUpper = strtoupper(trim((string)($appStudent->degree_level ?? '')));
                    $crmUpper = strtoupper(trim((string)($appStudent->student_id ?? '')));
                    $isPhd = ($degreeLevel ?? '') === 'phd'
                        || $levelUpper === 'PHD'
                        || str_contains($progUpper, 'PHD')
                        || str_contains($progUpper, 'PH.D')
                        || str_contains($progUpper, 'DOCTOR')
                        || str_starts_with($crmUpper, 'PHD');
                @endphp

                <div class="step-panel-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <div class="step-panel-kicker">Stage {{ $activeStep }} of {{ count($stages) }} &bull; Official Candidate Dossier</div>
                        <h3>Application Form Preview &amp; Download</h3>
                        <p>Review your verified application details and download the official PDF form.</p>
                    </div>
                    <div class="stage6-header-actions" style="display: flex; gap: 0.65rem; align-items: center; flex-wrap: wrap;">
                        <a href="{{ route('application.show', ['id' => $appStudent->id, 'print' => 1]) }}" target="_blank" class="btn-step-prev" style="text-decoration: none; padding: 0.55rem 1rem; font-size: 0.82rem; font-weight: 600; background: #ffffff; border: 1.5px solid #cbd5e1; display: inline-flex; align-items: center; gap: 0.4rem;" title="Open and Print Application Form">
                            <span>🖨️ Print Form</span>
                        </a>
                        <a href="{{ route('application.download', ['id' => $appStudent->id]) }}" download class="btn-step-next" style="text-decoration: none; padding: 0.55rem 1.25rem; font-size: 0.84rem; background: #16a34a; font-weight: 700; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <span>📥 Download PDF</span>
                        </a>
                    </div>
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body stage6-panel-body" style="padding: 1.25rem; background: #f1f5f9;">
                    @csrf
                    <input type="hidden" name="step" value="{{ $activeStep }}">

                    <div class="mobile-a4-scroll-hint">
                        <span>↔️ Swipe horizontally to view full official A4 sheet</span>
                    </div>

                    <!-- EMBEDDED A4 ADMISSION APPLICATION FORM -->
                    <div class="embedded-a4-form-wrapper">
                    <div class="embedded-a4-form" style="max-width: 860px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 10px 30px rgba(15, 42, 74, 0.15); border: 1px solid #cbd5e1; font-family: 'Source Sans 3', Helvetica, Arial, sans-serif; font-size: 13px; color: #1B2430;">

                        @php
                            $uploadedKeys = array_keys((array)($student->uploaded_files ?? []));
                            $hasTenth = in_array('marksheet10', $uploadedKeys);
                            $hasInter = in_array('marksheet12', $uploadedKeys);
                            $hasUg = in_array('btech', $uploadedKeys) || in_array('ug_cmm', $uploadedKeys);
                            $hasPg = in_array('pg1_cmm', $uploadedKeys) || in_array('pg1_pc', $uploadedKeys) || in_array('pg', $uploadedKeys) || in_array('mtech', $uploadedKeys);
                            $hasExp = in_array('experience', $uploadedKeys);
                            $hasId = in_array('aadhaar', $uploadedKeys) || in_array('passport', $uploadedKeys);
                            $hasPhoto = in_array('photo', $uploadedKeys) || !empty($student->photo_path);

                            $progUpper = strtoupper(trim((string)($student->program ?? '')));
                            $levelUpper = strtoupper(trim((string)($student->degree_level ?? '')));
                            $crmUpper = strtoupper(trim((string)($student->student_id ?? '')));
                            $isPhd = $levelUpper === 'PHD'
                                || $levelUpper === 'DOCTORAL'
                                || str_contains($progUpper, 'PHD')
                                || str_contains($progUpper, 'PH.D')
                                || str_contains($progUpper, 'DOCTOR')
                                || str_starts_with($crmUpper, 'PHD');

                            $isPg = !$isPhd && (
                                $levelUpper === 'MTECH'
                                || $levelUpper === 'PG'
                                || str_contains($progUpper, 'M.TECH')
                                || str_contains($progUpper, 'MTECH')
                                || str_contains($progUpper, 'MASTER')
                                || str_contains($progUpper, 'MBA')
                                || str_contains($progUpper, 'MCA')
                            );

                            $isUg = !$isPhd && !$isPg;
                        @endphp
                            <div class="phd-form-wrap">
                                <style>
                                    .phd-form-wrap {
                                        --bg: #f3f2ef; --paper: #fff; --ink: #1d1d1f; --mute: #5d5d63; --line: #cfcdc7;
                                        --bar: #4a4a4f; --bar-ink: #fff; --accent: #8c1c2e; --accent-ink: #fff; --field: #fafaf8; --ok: #1f6b3a;
                                        background: var(--paper); border: 1px solid var(--line); padding: 24px 28px;
                                        font: 400 14px/1.5 "Source Sans 3", system-ui, -apple-system, sans-serif; color: var(--ink); border-radius: 4px;
                                    }
                                    .phd-form-wrap header {
                                        display: grid; grid-template-columns: auto 1fr auto; gap: 18px; align-items: center;
                                        border-bottom: 2px solid var(--ink); padding-bottom: 14px;
                                    }
                                    .phd-form-wrap .logo-box { display: flex; align-items: center; justify-content: center; }
                                    .phd-form-wrap .logo-box img { height: 56px; display: block; }
                                    .phd-form-wrap .brand { font: 700 20px/1.1 "Source Serif 4", Georgia, serif; margin: 0; color: var(--ink); }
                                    .phd-form-wrap .brand small { display: block; font: 400 12px "Source Sans 3", sans-serif; color: var(--mute); margin-top: 4px; }
                                    .phd-form-wrap h1 { font: 700 18px/1.2 "Source Serif 4", Georgia, serif; margin: 12px 0 2px; color: var(--accent); }
                                    .phd-form-wrap .meta { display: flex; flex-wrap: wrap; gap: 10px 18px; margin-top: 8px; }
                                    .phd-form-wrap .meta label { display: flex; gap: 6px; align-items: center; font-weight: 600; font-size: 13px; }
                                    .phd-form-wrap .photo {
                                        width: 115px; height: 145px; border: 1px dashed var(--mute); display: grid; place-items: center;
                                        text-align: center; font-size: 11px; color: var(--mute); overflow: hidden; position: relative;
                                        background: var(--field); border-radius: 4px;
                                    }
                                    .phd-form-wrap .photo img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
                                    .phd-form-wrap .note {
                                        font-size: 12.5px; color: var(--mute); margin: 12px 0; background: var(--field);
                                        border-left: 3px solid var(--accent); padding: 6px 10px;
                                    }
                                    .phd-form-wrap h2 {
                                        background: var(--bar); color: var(--bar-ink); font: 700 14.5px "Source Sans 3", sans-serif;
                                        margin: 20px 0 10px; padding: 7px 12px; display: flex; gap: 10px; border-radius: 3px;
                                    }
                                    .phd-form-wrap .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 16px; }
                                    .phd-form-wrap .full { grid-column: 1 / -1; }
                                    .phd-form-wrap .f { display: flex; flex-direction: column; gap: 4px; }
                                    .phd-form-wrap .f > span, .phd-form-wrap .lbl { font-weight: 600; font-size: 13px; color: var(--ink); }
                                    .phd-form-wrap .req::after { content: " *"; color: var(--accent); }
                                    .phd-form-wrap input[type=text], .phd-form-wrap input[type=tel], .phd-form-wrap input[type=email],
                                    .phd-form-wrap input[type=date], .phd-form-wrap input[type=number], .phd-form-wrap select, .phd-form-wrap textarea {
                                        width: 100%; font: inherit; color: inherit; background: var(--field); border: 1px solid var(--line);
                                        padding: 7px 10px; border-radius: 3px; font-size: 13px; box-sizing: border-box;
                                    }
                                    .phd-form-wrap textarea { resize: vertical; min-height: 56px; }
                                    .phd-form-wrap .opts { display: flex; flex-wrap: wrap; gap: 6px 18px; padding: 6px 0; }
                                    .phd-form-wrap .chk { display: flex; gap: 6px; align-items: center; font-size: 12.5px; color: var(--ink); cursor: default; }
                                    .phd-form-wrap .chk input { accent-color: var(--accent); width: 16px; height: 16px; margin: 0; }
                                    .phd-form-wrap .tw { overflow-x: auto; border: 1px solid var(--line); border-radius: 3px; }
                                    .phd-form-wrap table { border-collapse: collapse; width: 100%; min-width: 640px; }
                                    .phd-form-wrap th, .phd-form-wrap td { border: 1px solid var(--line); padding: 6px; text-align: left; vertical-align: middle; font-size: 12.5px; }
                                    .phd-form-wrap th { background: var(--field); font-size: 12px; padding: 7px 6px; font-weight: 700; }
                                    .phd-form-wrap td input, .phd-form-wrap td select { border: 0; background: transparent; padding: 4px; width: 100%; font-size: 12.5px; }
                                    .phd-form-wrap td.rl { font-weight: 600; font-size: 12.5px; white-space: nowrap; padding: 6px 8px; background: var(--field); }
                                    .phd-form-wrap td output { display: block; padding: 4px; font-weight: 700; color: var(--ok); }
                                    .phd-form-wrap .decl { border: 1px solid var(--line); padding: 12px; font-size: 13px; background: var(--field); border-radius: 3px; line-height: 1.5; }
                                    .phd-form-wrap .sign { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; margin-top: 12px; }
                                    .phd-form-wrap .rules { margin-top: 20px; }
                                    .phd-form-wrap .rules .in { font-size: 12.5px; line-height: 1.55; }
                                    .phd-form-wrap .rules h3 { font-size: 13px; margin: 12px 0 4px; font-weight: 700; }
                                    .phd-form-wrap .rules ul, .phd-form-wrap .rules ol { margin: 0; padding-left: 22px; }
                                    .phd-form-wrap .rules li { margin: 3px 0; }
                                    .phd-form-wrap .glance { display: grid; grid-template-columns: repeat(7, 1fr); gap: 6px; margin: 8px 0 12px; text-align: center; }
                                    .phd-form-wrap .glance div { border: 1px solid var(--line); padding: 6px 2px; background: var(--field); font-size: 11px; border-radius: 3px; }
                                    .phd-form-wrap .glance b { display: block; font-size: 15px; color: var(--ink); }
                                    .phd-form-wrap .office { margin-top: 20px; border: 2px dashed var(--line); padding: 10px 14px 14px; border-radius: 4px; background: #fff; }
                                    .phd-form-wrap .office h2 { margin: 6px -14px 12px -14px; border-radius: 0; }
                                    .phd-form-wrap .office .big td { font-weight: 700; background: var(--field); }
                                    @media(max-width: 640px) {
                                        .phd-form-wrap .grid { grid-template-columns: 1fr; }
                                        .phd-form-wrap header { grid-template-columns: 1fr; }
                                        .phd-form-wrap .sign { grid-template-columns: 1fr; }
                                    }
                                </style>

                                <header style="display: grid; grid-template-columns: 1fr auto; gap: 16px; align-items: start; border-bottom: 2px solid var(--ink); padding-bottom: 12px;">
                                    <div>
                                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 4px;">
                                            <img src="{{ $formLogo }}" alt="Vignan's Logo" style="height: 48px; object-fit: contain;">
                                            <div>
                                                <p class="brand" style="margin: 0; font-size: 19px; line-height: 1.15;">
                                                    Vignan’s Foundation for Science, Technology &amp; Research
                                                    <small style="display: block; font-size: 11.5px; color: var(--mute); margin-top: 2px;">(Deemed to be University)</small>
                                                </p>
                                            </div>
                                        </div>
                                        <h1 style="margin: 6px 0 2px 0; font-size: 16px; color: var(--accent); font-weight: 700;">Admission Information Form</h1>
                                        <div style="font-size: 12.5px; font-weight: 700; color: var(--ink); margin-bottom: 6px;">
                                            {{ $isPhd ? 'Doctor of Philosophy (Ph.D.) Programme' : ($student->program ?: 'Undergraduate / Postgraduate Programme') }}
                                        </div>
                                        <div class="meta" style="margin-top: 4px; display: flex; flex-wrap: wrap; gap: 8px 16px;">
                                            <label style="font-size: 12px; font-weight: 600;">Academic year <input type="text" id="ay" value="2026–2027" size="10" readonly style="padding: 2px 6px; font-size: 12px; font-weight: 600;"></label>
                                            <label style="font-size: 12px; font-weight: 600;">Application No. <input type="text" id="appno" value="{{ $student->id }}" size="10" readonly style="padding: 2px 6px; font-size: 12px; font-weight: 600;"></label>
                                            <label style="font-size: 12px; font-weight: 600;">CRM ID <input type="text" value="{{ $student->student_id }}" size="10" readonly style="padding: 2px 6px; font-size: 12px; font-weight: 600;"></label>
                                        </div>
                                    </div>
                                    <label class="photo" id="ph" style="margin: 0; align-self: start;">
                                        @if(!empty($student->photo_path))
                                            <img src="{{ $student->photo_path }}" alt="Candidate Photo">
                                        @else
                                            <span>Passport-size<br>colour photograph</span>
                                        @endif
                                    </label>
                                </header>

                                <h2>1 &nbsp;Personal details</h2>
                                <div class="grid">
                                    <label class="f full"><span class="req">Name of the candidate</span><input type="text" name="name" required style="text-transform:uppercase" value="{{ strtoupper($student->name ?: '-') }}" readonly></label>
                                    <label class="f full"><span>Father’s / Guardian’s name</span><input type="text" name="father" style="text-transform:uppercase" value="{{ strtoupper($student->father_name ?: ($student->parent_name ?: '-')) }}" readonly></label>
                                    <div class="f">
                                        <span class="lbl req">Gender</span>
                                        <div class="opts" data-group="gender">
                                            <label class="chk"><input type="radio" name="gender" value="Male" {{ strtolower($student->gender ?? '') === 'male' ? 'checked' : '' }} disabled><span>Male</span></label>
                                            <label class="chk"><input type="radio" name="gender" value="Female" {{ strtolower($student->gender ?? '') === 'female' ? 'checked' : '' }} disabled><span>Female</span></label>
                                            <label class="chk"><input type="radio" name="gender" value="Other" {{ !in_array(strtolower($student->gender ?? ''), ['male','female']) && !empty($student->gender) && $student->gender !== '-' ? 'checked' : '' }} disabled><span>Other</span></label>
                                        </div>
                                    </div>
                                    <label class="f"><span class="req">Date of birth</span><input type="text" name="dob" value="{{ !empty($student->dob) && $student->dob !== '-' ? \Carbon\Carbon::parse($student->dob)->format('d M Y') : '-' }}" readonly></label>
                                    <div class="f full">
                                        <span class="lbl">Category</span>
                                        <div class="opts">
                                            <label class="chk"><input type="radio" name="cat" value="General" {{ in_array(strtoupper($student->category ?? ''), ['GENERAL', 'OC', 'OPEN']) ? 'checked' : '' }} disabled><span>General</span></label>
                                            <label class="chk"><input type="radio" name="cat" value="OBC" {{ str_contains(strtoupper($student->category ?? ''), 'OBC') || str_contains(strtoupper($student->category ?? ''), 'BC') ? 'checked' : '' }} disabled><span>OBC</span></label>
                                            <label class="chk"><input type="radio" name="cat" value="SC" {{ strtoupper($student->category ?? '') === 'SC' ? 'checked' : '' }} disabled><span>SC</span></label>
                                            <label class="chk"><input type="radio" name="cat" value="ST" {{ strtoupper($student->category ?? '') === 'ST' ? 'checked' : '' }} disabled><span>ST</span></label>
                                            <label class="chk"><input type="radio" name="cat" value="PwD" {{ in_array(strtoupper($student->category ?? ''), ['PWD', 'PH', 'OTHER']) ? 'checked' : '' }} disabled><span>PwD / Other</span></label>
                                        </div>
                                    </div>
                                    <label class="f"><span class="req">Mobile no.</span><input type="tel" name="mobile" value="{{ $student->phone ?: '-' }}" readonly></label>
                                    <label class="f"><span class="req">Parent’s mobile</span><input type="tel" name="pmobile" value="{{ $student->father_phone ?: ($student->parent_phone ?: '-') }}" readonly></label>
                                    <label class="f full"><span class="req">Email ID</span><input type="email" name="email" value="{{ $student->email ?: '-' }}" readonly></label>
                                    <label class="f full"><span>Address for communication</span><textarea name="addr" readonly>{{ $student->address ?: '-' }}</textarea></label>
                                    <label class="f"><span>City / District</span><input type="text" name="city" value="{{ trim(($student->city !== '-' ? $student->city : '') . ($student->district !== '-' ? ', ' . $student->district : '')) ?: '-' }}" readonly></label>
                                    <label class="f"><span>State / PIN code</span><input type="text" name="state" value="{{ trim(($student->state !== '-' ? $student->state : '') . ($student->pincode !== '-' ? ' - ' . $student->pincode : '')) ?: '-' }}" readonly></label>
                                </div>

                                <h2>2 &nbsp;Programme details</h2>
                                <div class="grid">
                                    <label class="f full"><span class="req">{{ $isPhd ? 'Name of the specialization in Ph.D. programme' : 'Programme & Specialization / Branch' }}</span><input type="text" name="spec" value="{{ $student->branch !== '-' ? $student->branch : ($student->program !== '-' ? $student->program : '-') }}" readonly></label>
                                    <label class="f"><span>VSAT ID</span><input type="text" name="vsat" value="{{ $student->student_id ?: '-' }}" readonly></label>
                                    <label class="f"><span>{{ $isPhd ? 'Research area' : 'Allotted Program' }}</span><input type="text" name="area" value="{{ $student->program !== '-' ? $student->program : ($student->branch !== '-' ? $student->branch : '-') }}" readonly></label>
                                    <div class="f full">
                                        <span class="lbl req">Campus preference</span>
                                        <div class="opts">
                                            <label class="chk"><input type="radio" name="campus" value="VFSTR, Guntur" {{ !str_contains(strtolower($student->campus ?? ''), 'hyderabad') ? 'checked' : '' }} disabled><span>VFSTR, Guntur</span></label>
                                            <label class="chk"><input type="radio" name="campus" value="Off-Campus, Hyderabad" {{ str_contains(strtolower($student->campus ?? ''), 'hyderabad') ? 'checked' : '' }} disabled><span>Off-Campus, Hyderabad</span></label>
                                        </div>
                                    </div>
                                </div>

                                <h2>3 &nbsp;Academic qualifications</h2>
                                <div class="lbl" style="margin-bottom:6px">School education</div>
                                <div class="tw">
                                    <table id="school">
                                        <thead>
                                            <tr><th>Examination</th><th>Board / School</th><th>Year of passing</th><th>Total marks</th><th>Marks obtained</th><th>Percentage</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="rl">SSC</td>
                                                <td><input type="text" value="{{ $student->academic->ssc_board !== '-' ? $student->academic->ssc_board : ($student->academic->ssc_school !== '-' ? $student->academic->ssc_school : '-') }}" readonly></td>
                                                <td><input type="text" value="{{ $student->academic->ssc_year ?: '-' }}" readonly></td>
                                                <td><input type="text" value="{{ $student->academic->ssc_marks_conducted ?: '-' }}" readonly></td>
                                                <td><input type="text" value="{{ $student->academic->ssc_marks_earned ?: '-' }}" readonly></td>
                                                <td><output>{{ $student->academic->ssc_percentage ?: '-' }}</output></td>
                                            </tr>
                                            <tr>
                                                <td class="rl">Intermediate</td>
                                                <td><input type="text" value="{{ $student->academic->inter_board !== '-' ? $student->academic->inter_board : ($student->academic->inter_college !== '-' ? $student->academic->inter_college : '-') }}" readonly></td>
                                                <td><input type="text" value="{{ $student->academic->inter_year ?: '-' }}" readonly></td>
                                                <td><input type="text" value="{{ $student->academic->inter_marks_conducted ?: '-' }}" readonly></td>
                                                <td><input type="text" value="{{ $student->academic->inter_marks_earned ?: '-' }}" readonly></td>
                                                <td><output>{{ $student->academic->inter_percentage ?: '-' }}</output></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                @if(!$isUg)
                                    <div class="lbl" style="margin:16px 0 6px">{{ $isPhd ? 'UG & PG programme details' : 'UG programme details' }}</div>
                                    <div class="tw">
                                        <table id="deg">
                                            <thead>
                                                <tr><th>Programme</th><th>Programme with specialization</th><th>College / University</th><th>Year</th><th>Division / grade points</th><th>% of marks</th><th>Mode of study</th></tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="rl">UG programme<br><small style="font-weight:400;color:var(--mute)">(weightage: 20%)</small></td>
                                                    <td><input type="text" value="B.Tech / Degree" readonly></td>
                                                    <td><input type="text" value="{{ $student->academic->ug_college !== '-' ? $student->academic->ug_college : ($student->academic->ug_board !== '-' ? $student->academic->ug_board : '-') }}" readonly></td>
                                                    <td><input type="text" value="{{ $student->academic->ug_year ?: '-' }}" readonly></td>
                                                    <td><input type="text" value="{{ $student->academic->ug_status ?: '-' }}" readonly></td>
                                                    <td><input type="text" value="{{ $student->academic->ug_percentage ?: '-' }}" readonly></td>
                                                    <td><select disabled><option selected>Regular</option><option>Full-time</option><option>Part-time</option><option>Distance</option><option>Online</option></select></td>
                                                </tr>
                                                @if($isPhd)
                                                    <tr>
                                                        <td class="rl">PG programme<br><small style="font-weight:400;color:var(--mute)">(weightage: 30%)</small></td>
                                                        <td><input type="text" value="M.Tech / Master’s" readonly></td>
                                                        <td><input type="text" value="{{ $student->academic->pg_college !== '-' ? $student->academic->pg_college : ($student->academic->pg_board !== '-' ? $student->academic->pg_board : '-') }}" readonly></td>
                                                        <td><input type="text" value="{{ $student->academic->pg_year ?: '-' }}" readonly></td>
                                                        <td><input type="text" value="{{ $student->academic->pg_status ?: '-' }}" readonly></td>
                                                        <td><input type="text" value="{{ $student->academic->pg_percentage ?: '-' }}" readonly></td>
                                                        <td><select disabled><option selected>Regular</option><option>Full-time</option><option>Part-time</option><option>Distance</option><option>Online</option></select></td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                <h2>4 &nbsp;Enclosures checklist</h2>
                                <p class="note" style="margin-top:0">Tick the documents enclosed with this form.</p>
                                <div class="grid" id="enc">
                                    <label class="chk"><input type="checkbox" name="enc" value="SSC / 10th marks memo" {{ $hasTenth ? 'checked' : '' }} disabled><span>SSC / 10th marks memo</span></label>
                                    <label class="chk"><input type="checkbox" name="enc" value="Intermediate / 10+2 marks memo" {{ $hasInter ? 'checked' : '' }} disabled><span>Intermediate / 10+2 marks memo</span></label>
                                    @if(!$isUg)
                                        <label class="chk"><input type="checkbox" name="enc" value="UG degree certificate & marks memos" {{ $hasUg ? 'checked' : '' }} disabled><span>UG degree certificate &amp; marks memos</span></label>
                                        @if($isPhd)
                                            <label class="chk"><input type="checkbox" name="enc" value="PG degree certificate & marks memos" {{ $hasPg ? 'checked' : '' }} disabled><span>PG degree certificate &amp; marks memos</span></label>
                                            <label class="chk"><input type="checkbox" name="enc" value="GATE / CSIR / NET / CAT score card (if any)" disabled><span>GATE / CSIR / NET / CAT score card (if any)</span></label>
                                            <label class="chk"><input type="checkbox" name="enc" value="Experience certificate(s) (if any)" {{ $hasExp ? 'checked' : '' }} disabled><span>Experience certificate(s) (if any)</span></label>
                                            <label class="chk"><input type="checkbox" name="enc" value="Publication proofs (if any)" disabled><span>Publication proofs (if any)</span></label>
                                        @endif
                                    @endif
                                    <label class="chk"><input type="checkbox" name="enc" value="Caste / category certificate (if applicable)" {{ ($student->category !== 'General' && $student->category !== 'OC' && $student->category !== '-') ? 'checked' : '' }} disabled><span>Caste / category certificate (if applicable)</span></label>
                                    <label class="chk"><input type="checkbox" name="enc" value="Photo ID proof (Aadhaar / Passport etc.)" {{ $hasId ? 'checked' : '' }} disabled><span>Photo ID proof (Aadhaar / Passport etc.)</span></label>
                                    <label class="chk"><input type="checkbox" name="enc" value="Passport-size photographs" {{ $hasPhoto ? 'checked' : '' }} disabled><span>Passport-size photographs</span></label>
                                </div>

                                <h2>5 &nbsp;Declaration by the candidate</h2>
                                <div class="decl">
                                    I hereby declare that all the information furnished in this form is true and correct to the best of my knowledge and belief. I understand that any false information or suppression of facts will lead to the cancellation of my candidature / admission. I have read the eligibility criteria and admission procedure given at the end of this form and agree to abide by the rules and regulations of VFSTR.
                                    <label class="chk" style="margin-top:10px"><input type="checkbox" id="agree" checked disabled><span><b>I agree to the declaration</b></span></label>
                                </div>
                                <div class="sign">
                                    <label class="f"><span>Place</span><input type="text" name="place" value="{{ $student->city !== '-' ? $student->city : ($student->district !== '-' ? $student->district : 'Guntur') }}" readonly></label>
                                    <label class="f"><span>Date</span><input type="text" name="sdate" value="{{ $student->applied_at ?: now()->format('d/m/Y') }}" readonly></label>
                                    <label class="f"><span>Signature of the candidate (type full name)</span><input type="text" name="sign" value="{{ $student->name }}" readonly></label>
                                </div>

                                @if(!$isPhd)
                                    <section class="office" aria-label="For office use only" style="margin-top: 20px;">
                                        <h2>6 &nbsp;For office use only – Admission &amp; Verification</h2>
                                        <div class="tw">
                                            <table>
                                                <thead>
                                                    <tr><th>Admission status</th><th>Fee payment status</th><th>Allotted programme &amp; branch</th><th>Campus &amp; quota</th></tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td style="color: var(--ok); font-weight: 700;">Provisional Admission Confirmed</td>
                                                        <td style="color: var(--ok); font-weight: 700;">Verified &amp; Accounted</td>
                                                        <td>{{ $student->program ?: '-' }} ({{ $student->branch ?: '-' }})</td>
                                                        <td>{{ $isHydCampus ? 'Off-Campus, Hyderabad' : 'VFSTR, Guntur' }} &bull; {{ $student->category ?: 'General' }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="grid" style="margin-top: 14px;">
                                            <label class="f"><span>Scrutiny Officer</span><input type="text" value="Verified &amp; Approved" readonly></label>
                                            <label class="f"><span>Finance / Accounts Officer</span><input type="text" value="Fee Receipt Verified" readonly></label>
                                            <label class="f"><span>Dean – Admissions</span><input type="text" value="Authorized Signatory" readonly></label>
                                        </div>
                                    </section>
                                @endif

                                @if($isPhd)
                                <section class="rules" aria-label="Eligibility criteria">
                                    <h2>Eligibility criteria</h2>
                                    <div class="in">
                                        <h3>Engineering streams</h3><ul><li>Full-time scholar: first class in both B.Tech and M.Tech, with scholarship.</li><li>Part-time scholar: as per UGC rules, subject to written test and interview by VFSTR.</li></ul>
                                        <h3>General qualification norms</h3><ul><li>Minimum 55% in post-graduation as per UGC rules, relaxable by 5% for SC / ST / OBC candidates.</li><li>Minimum 60% aggregate in the qualifying degree; deserving cases may be referred to RMC.</li><li>Engineering: PG degree in a relevant engineering discipline. Management: Master’s in business management. Sciences / humanities: relevant PG degree.</li></ul>
                                        <h3>Law</h3><ul><li>LL.B. from a Bar Council of India recognised institution and regular full-time LL.M. with minimum 55%. UGC NET / SET qualified candidates preferred.</li></ul>
                                        <h3>Pharmaceutical sciences</h3><ul><li>First class in B.Pharmacy and M.Pharmacy or Pharm.D. GPAT qualified candidates preferred.</li></ul>
                                    </div>
                                </section>

                                <section class="rules" aria-label="Admission procedure">
                                    <h2>Admission procedure</h2>
                                    <div class="in">
                                        <p>Selection into the Ph.D. programme is evaluated on the academic performance (% marks in B.Tech / UG and M.Tech / PG) and the institution last attended for Bachelor’s and Master’s degrees (maximum 20% and 30% marks respectively), previous experience (each year 1 mark, up to 5 marks), publications (2 marks per journal paper and 1 mark per conference paper, up to 5 marks), written test (15% marks) and interview (25% marks).</p>
                                        <h3>Selection weightage at a glance</h3>
                                        <div class="glance"><div><b>20%</b>UG degree</div><div><b>30%</b>PG degree</div><div><b>5%</b>Experience</div><div><b>5%</b>Publications</div><div><b>15%</b>Written test</div><div><b>25%</b>Interview</div><div><b>100%</b>Total</div></div>
                                        <ol><li>Eligible candidates have to appear for a written test and interview.</li><li>All candidates are required to qualify in a written test conducted by VFSTR. Candidates who have qualified in GATE / CSIR / NET / CAT are exempted from the written test, but they should appear for the interview.</li><li>Candidates recommended for selection by the concerned department are to be approved by the Dean (R&amp;D) and Chairman, URB respectively. The approved selection list shall be sent to Dean (Admission) for further action.</li><li>Candidates will be admitted into the Ph.D. programme after payment of prescribed fees and completion of all the admission procedures.</li><li>Admitted candidates will report to the concerned department; a committee appointed by HoD will allot a Research Supervisor for each candidate on mutual consent.</li></ol>
                                    </div>
                                </section>

                                <section class="office" aria-label="For office use only">
                                    <h2>For office use only – Department verification</h2>
                                    <div class="tw"><table>
                                        <thead><tr><th>Component</th><th>Basis of scoring</th><th>Max. marks</th><th>Marks awarded</th></tr></thead>
                                        <tbody>
                                        <tr><td class="rl">UG degree details</td><td>Academic performance (% marks) &amp; institution last attended</td><td>20</td><td><input type="number" class="osum" min="0" max="20" placeholder="____" readonly></td></tr>
                                        <tr><td class="rl">PG degree details</td><td>Academic performance (% marks) &amp; institution last attended</td><td>30</td><td><input type="number" class="osum" min="0" max="30" placeholder="____" readonly></td></tr>
                                        <tr><td class="rl">Experience</td><td>1 mark for each year of experience</td><td>5</td><td><input type="number" class="osum" min="0" max="5" placeholder="____" readonly></td></tr>
                                        <tr><td class="rl">Publications</td><td>2 marks / journal paper &amp; 1 mark / conference paper</td><td>5</td><td><input type="number" class="osum" min="0" max="5" placeholder="____" readonly></td></tr>
                                        <tr class="big"><td colspan="2">Total marks out of 60</td><td>60</td><td><output id="o60">0</output></td></tr>
                                        <tr><td class="rl">Written test</td><td>Exempted for GATE / CSIR / NET / CAT qualified candidates</td><td>15</td><td><input type="number" class="osum" min="0" max="15" placeholder="____" readonly></td></tr>
                                        <tr><td class="rl">Interview</td><td>Conducted by the department</td><td>25</td><td><input type="number" class="osum" min="0" max="25" placeholder="____" readonly></td></tr>
                                        <tr class="big"><td colspan="2">Grand total (selection score)</td><td>100</td><td><output id="o100">0</output></td></tr>
                                        </tbody></table></div>
                                    <div class="opts" style="margin-top:10px">
                                        <label class="chk"><input type="radio" name="sel" value="Selected" disabled><span>Selected</span></label>
                                        <label class="chk"><input type="radio" name="sel" value="Not selected" disabled><span>Not selected</span></label>
                                        <span class="lbl">Research assistantship:</span>
                                        <label class="chk"><input type="checkbox" disabled><span>ERA</span></label><label class="chk"><input type="checkbox" disabled><span>TRA</span></label><label class="chk"><input type="checkbox" disabled><span>CAP</span></label><label class="chk"><input type="checkbox" disabled><span>CRA</span></label>
                                        <label class="chk"><input type="radio" name="trk" value="Industry" disabled><span>Industry</span></label><label class="chk"><input type="radio" name="trk" value="Academics" disabled><span>Academics</span></label>
                                    </div>
                                    <label class="f" style="margin-top:10px"><span>Remarks (not selected candidates)</span><textarea readonly placeholder="Department remarks..."></textarea></label>
                                    <div class="grid" style="margin-top:10px">
                                        <label class="f"><span>Name of the faculty member (department coordinator)</span><input type="text" readonly></label>
                                        <label class="f"><span>Designation</span><input type="text" readonly></label>
                                        <label class="f"><span>Signature</span><input type="text" readonly></label>
                                        <label class="f"><span>Date</span><input type="text" readonly></label>
                                    </div>
                                    <p class="note" style="margin-top:8px">Selection committee: BoR (Chairman) · Head of the Department (Member) · School Dean (Member) · Dean R&amp;D (Member)</p>
                                </section>
                                @endif
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
                                <div class="attachment-header" style="background: linear-gradient(135deg, #0F2A4A 0%, #1e3a8a 100%); padding: 12px 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                    <div class="attachment-info" style="display: flex; align-items: center; gap: 12px;">
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

                                    <div class="attachment-actions" style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap;">
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
                            <p style="margin: 0; color: #64748b; font-size: 0.85rem;">
                                No document attachments found for this candidate.
                            </p>
                        </div>
                    @endif

                    {{-- 2. COMPACT SUMMARY CHECKLIST OF ALL SLOTS (REQUIRED & OPTIONAL) --}}
                    <div style="max-width: 820px; margin: 1.5rem auto 0 auto; background: #ffffff; border-radius: 10px; border: 1px solid #cbd5e1; overflow: hidden; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);">
                        <div style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0; padding: 10px 16px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                            <div style="font-size: 11px; font-weight: 800; color: #0F2A4A; text-transform: uppercase; letter-spacing: 0.4px;">
                                Candidate Document Dossier Checklist
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

                    @php
                        $isPhd = (($degreeLevel ?? '') === 'phd');
                        $appPrevStep = $isPhd ? 4 : 5;
                        $appNextStep = $isPhd ? 6 : 7;
                    @endphp
                    <div class="step-footer-actions" style="margin-top: 1.5rem;">
                        <a href="{{ route('dashboards.index', ['step' => $appPrevStep]) }}" class="btn-step-prev">
                            <span class="btn-label-desktop">&larr; Stage {{ $appPrevStep }}: {{ $stages[$appPrevStep]['title'] ?? ($isPhd ? 'Fee Payment' : 'Exam Slot Booking') }}</span>
                            <span class="btn-label-mobile">&larr; Stage {{ $appPrevStep }}: {{ $stages[$appPrevStep]['title'] ?? ($isPhd ? 'Fee Payment' : 'Exam Slot') }}</span>
                        </a>
                        @if(isset($stages[$appNextStep]))
                            <button type="submit" class="btn-step-next" style="background: #2563eb;">
                                <span class="btn-label-desktop">Proceed to Stage {{ $appNextStep }}: {{ $stages[$appNextStep]['title'] }} &rarr;</span>
                                <span class="btn-label-mobile">Proceed to Stage {{ $appNextStep }} &rarr;</span>
                            </button>
                        @else
                            <button type="submit" class="btn-step-next" style="background: #16a34a;">
                                <span class="btn-label-desktop">✓ Complete Onboarding &amp; Confirm Submission</span>
                                <span class="btn-label-mobile">✓ Confirm Submission</span>
                            </button>
                        @endif
                    </div>
                </form>

            <!-- ================= STAGE 7: V-SAT RESULTS (master.menus: CRMM0007) ================= -->
            @elseif((($degreeLevel ?? '') !== 'phd' && $activeStep === 7 && isset($stages[7])) || (($degreeLevel ?? '') === 'phd' && $activeStep === 6 && isset($stages[6])))
                @php
                    $isPhd = (($degreeLevel ?? '') === 'phd');
                    $vsatStepNum = $isPhd ? 6 : 7;
                    $vsatPrevStep = $isPhd ? 5 : 6;
                    $isStage7Done = ($stages[$vsatStepNum]['status'] ?? 'pending') === 'completed';
                    $examDateFormatted = $examSchedule?->examdate ? \Carbon\Carbon::parse($examSchedule->examdate)->format('d M Y') : 'Booked in Stage 5';
                    $slotFormatted = $examSchedule?->exam_slot ?? 'Session Scheduled';
                @endphp
                <div class="step-panel-header">
                    <div>
                        <div class="step-panel-kicker">Stage {{ $vsatStepNum }} of {{ count($stages) }} &bull; V-SAT 2026 Examination</div>
                        <h3>{{ $stages[$vsatStepNum]['title'] ?? 'V-SAT Results' }}</h3>
                        <p>{{ $stages[$vsatStepNum]['subtitle'] ?? 'Official V-SAT examination scorecard and admission merit records.' }}</p>
                    </div>
                    @if($isStage7Done)
                        <span class="step-header-tag tag-success">✓ Onboarding Completed</span>
                    @else
                        <span class="step-header-tag tag-pending">Results &amp; Verification</span>
                    @endif
                </div>

                <form action="{{ route('dashboards.save-step') }}" method="POST" class="step-panel-body">
                    @csrf
                    <input type="hidden" name="step" value="{{ $vsatStepNum }}">

                    <!-- Candidate Examination Record from DB -->
                    <div class="section-box stage7-banner-box" style="background: linear-gradient(135deg, #eff6ff 0%, #f0fdf4 100%); border: 1.5px solid #93c5fd; border-radius: 12px; padding: 1.25rem;">
                        <div class="stage7-banner-header" style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; margin-bottom:1.25rem;">
                            <div style="display:flex; align-items:center; gap:0.75rem;">
                                <div style="width:44px; height:44px; border-radius:10px; background:#2563eb; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.35rem; flex-shrink:0;">
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
                            <div class="stage7-metric-card" style="background:#fff; border-radius:10px; padding:0.9rem; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase;">Registered Program</div>
                                <div style="font-size:1.15rem; font-weight:800; color:#2563eb; margin-top:0.25rem;">
                                    {{ $program?->program_short_name ?: ($crmStudent?->programlevel ?: 'Undergraduate') }}
                                </div>
                                <span style="font-size:0.75rem; color:#16a34a; font-weight:700;">{{ $branchName ?: 'General Allotment' }}</span>
                            </div>
                            <div class="stage7-metric-card" style="background:#fff; border-radius:10px; padding:0.9rem; border:1px solid #e2e8f0; text-align:center;">
                                <div style="font-size:0.75rem; font-weight:700; color:#64748b; text-transform:uppercase;">Scheduled Slot</div>
                                <div style="font-size:1.05rem; font-weight:800; color:#0f172a; margin-top:0.25rem;">
                                    {{ $examDateFormatted }}
                                </div>
                                <span style="font-size:0.75rem; color:#475569;">{{ $slotFormatted }}</span>
                            </div>
                            <div class="stage7-metric-card" style="background:#fff; border-radius:10px; padding:0.9rem; border:1px solid #e2e8f0; text-align:center;">
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
                        <div class="stage7-notice-box" style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #16a34a; border-radius: 8px; padding: 0.9rem 1.1rem; margin-top: 1rem; font-size: 0.84rem; color: #334155; line-height: 1.55;">
                            <strong style="color: #14532d; font-weight: 800; display: block; margin-bottom: 0.3rem;">University Examination &amp; Counseling Notice:</strong>
                            <p style="margin: 0;">
                                Candidate details for Application <strong>#{{ $appNo }}</strong> have been verified and confirmed across all onboarding stages in accordance with university admissions policy. Official V-SAT entrance scores, rank percentiles, and merit scholarship category allocations are generated and published following examination slot completion.
                            </p>
                        </div>
                    </div>

                    <div class="step-footer-actions">
                        <a href="{{ route('dashboards.index', ['step' => $vsatPrevStep]) }}" class="btn-step-prev">
                            <span class="btn-label-desktop">&larr; Stage {{ $vsatPrevStep }}: {{ $stages[$vsatPrevStep]['title'] ?? 'Application Download' }}</span>
                            <span class="btn-label-mobile">&larr; Stage {{ $vsatPrevStep }}: {{ $stages[$vsatPrevStep]['title'] ?? 'Application Download' }}</span>
                        </a>
                        <button type="submit" class="btn-step-next" style="background: #15803d;">
                            <span class="btn-label-desktop">✓ Complete Onboarding &amp; Finalize Registration</span>
                            <span class="btn-label-mobile">✓ Finalize Registration</span>
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

    <!-- Modern Custom Dropdown UI Engine for Dashboard Selects -->
    <script>
        function initCustomSelects(selector) {
            selector = selector || '.mockup-select';
            document.querySelectorAll(selector).forEach(function(selectEl) {
                if (selectEl.dataset.customInit === 'true') {
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
                trigger.innerHTML = '<span class="custom-select-value"></span><svg class="custom-select-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>';
                container.appendChild(trigger);

                // Dropdown Menu Card
                const dropdown = document.createElement('div');
                dropdown.className = 'custom-select-dropdown';
                container.appendChild(dropdown);

                // Handle disabled state
                if (selectEl.disabled || selectEl.closest('fieldset[disabled]')) {
                    trigger.disabled = true;
                    trigger.style.opacity = '0.7';
                    trigger.style.cursor = 'not-allowed';
                }

                // Toggle menu on click
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    if (selectEl.disabled || selectEl.closest('fieldset[disabled]')) return;
                    const isOpen = container.classList.contains('is-open');
                    document.querySelectorAll('.custom-select-container.is-open').forEach(function(c) {
                        c.classList.remove('is-open');
                    });
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
                selectEl.addEventListener('change', function() {
                    updateTriggerLabel(selectEl, container);
                    syncCustomSelect(selectEl);
                });

                // Observe DOM option changes (e.g. dynamic state/district filtering)
                const observer = new MutationObserver(function() {
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
        }

        function syncCustomSelect(selectEl) {
            const container = selectEl.closest('.custom-select-container');
            if (!container) return;
            const dropdown = container.querySelector('.custom-select-dropdown');
            if (!dropdown) return;

            dropdown.innerHTML = '';
            Array.from(selectEl.options).forEach(function(opt) {
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

                item.addEventListener('click', function(e) {
                    e.stopPropagation();
                    selectEl.value = opt.value;
                    container.classList.remove('is-open');
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

        // Close dropdown when clicking outside or pressing Escape
        document.addEventListener('click', function() {
            document.querySelectorAll('.custom-select-container.is-open').forEach(function(c) {
                c.classList.remove('is-open');
            });
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.custom-select-container.is-open').forEach(function(c) {
                    c.classList.remove('is-open');
                });
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            initCustomSelects('.mockup-select');
        });
    </script>

    @if(!$hasAcceptedTerms)
        <!-- ================= TERMS & CONDITIONS POPUP MODAL ================= -->
        <style>
            .terms-modal-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.72);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.25rem;
                animation: termsFadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            }

            @keyframes termsFadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            .terms-modal-card {
                background: #ffffff;
                width: 100%;
                max-width: 590px;
                border-radius: 20px;
                box-shadow: 0 25px 60px -12px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
                overflow: hidden;
                border: 1px solid #e2e8f0;
                display: flex;
                flex-direction: column;
                animation: termsSlideUp 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            }

            @keyframes termsSlideUp {
                from {
                    opacity: 0;
                    transform: translateY(18px) scale(0.98);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            /* Header */
            .terms-modal-header {
                background: linear-gradient(135deg, #091e3a 0%, #1e3a8a 100%);
                color: #ffffff;
                padding: 1.35rem 1.65rem;
                display: flex;
                align-items: center;
                gap: 1rem;
                position: relative;
                overflow: hidden;
            }

            .terms-modal-header::after {
                content: '';
                position: absolute;
                top: -30px;
                right: -30px;
                width: 120px;
                height: 120px;
                background: radial-gradient(circle, rgba(59, 130, 246, 0.3) 0%, transparent 70%);
                pointer-events: none;
            }

            .terms-header-icon-box {
                width: 46px;
                height: 46px;
                border-radius: 12px;
                background: rgba(255, 255, 255, 0.12);
                border: 1px solid rgba(255, 255, 255, 0.22);
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                color: #93c5fd;
            }

            .terms-header-title {
                margin: 0;
                font-size: 1.15rem;
                font-weight: 800;
                color: #ffffff;
                letter-spacing: -0.2px;
                line-height: 1.25;
            }

            .terms-header-subtitle {
                margin: 0.25rem 0 0 0;
                font-size: 0.78rem;
                color: #93c5fd;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 0.45rem;
            }

            .terms-header-subtitle-dot {
                width: 4px;
                height: 4px;
                border-radius: 50%;
                background: #60a5fa;
            }

            /* Body */
            .terms-modal-body {
                padding: 1.45rem 1.65rem 1.25rem;
                color: #334155;
                font-size: 0.88rem;
                line-height: 1.6;
                max-height: 65vh;
                overflow-y: auto;
            }

            .terms-intro-text {
                font-weight: 700;
                color: #0f172a;
                margin-top: 0;
                margin-bottom: 1rem;
                font-size: 0.88rem;
                display: flex;
                align-items: center;
                gap: 0.45rem;
            }

            .terms-cards-list {
                display: flex;
                flex-direction: column;
                gap: 0.85rem;
            }

            .terms-card-row {
                display: flex;
                gap: 0.85rem;
                align-items: flex-start;
                padding: 0.85rem 1rem;
                border-radius: 12px;
                transition: all 0.2s ease;
                border: 1px solid transparent;
            }

            /* 1. Authenticity Row - Blue */
            .terms-card-row.row-blue {
                background: #f8fafc;
                border-color: #e2e8f0;
                border-left: 4px solid #2563eb;
            }
            .terms-card-row.row-blue:hover {
                background: #f0f7ff;
                border-color: #bfdbfe;
                border-left-color: #1d4ed8;
            }

            /* 2. Code of Conduct - Green */
            .terms-card-row.row-green {
                background: #f8fafc;
                border-color: #e2e8f0;
                border-left: 4px solid #16a34a;
            }
            .terms-card-row.row-green:hover {
                background: #f0fdf4;
                border-color: #bbf7d0;
                border-left-color: #15803d;
            }

            /* 3. Forfeiture Policy - Amber */
            .terms-card-row.row-amber {
                background: #f8fafc;
                border-color: #e2e8f0;
                border-left: 4px solid #d97706;
            }
            .terms-card-row.row-amber:hover {
                background: #fffbeb;
                border-color: #fde68a;
                border-left-color: #b45309;
            }

            .terms-badge-pill {
                width: 26px;
                height: 26px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.78rem;
                font-weight: 800;
                flex-shrink: 0;
                margin-top: 1px;
            }

            .terms-badge-pill.badge-blue {
                background: #eff6ff;
                color: #1d4ed8;
                border: 1.5px solid #bfdbfe;
            }

            .terms-badge-pill.badge-green {
                background: #f0fdf4;
                color: #15803d;
                border: 1.5px solid #bbf7d0;
            }

            .terms-badge-pill.badge-amber {
                background: #fffbeb;
                color: #b45309;
                border: 1.5px solid #fde68a;
            }

            .terms-row-content {
                flex: 1;
                font-size: 0.84rem;
                color: #334155;
                line-height: 1.55;
            }

            /* Checkbox Agreement Card */
            .terms-agreement-box {
                margin-top: 1.15rem;
                padding: 0.95rem 1.15rem;
                background: #f8fafc;
                border: 1.5px solid #cbd5e1;
                border-radius: 12px;
                transition: all 0.2s ease;
                cursor: pointer;
            }

            .terms-agreement-box:hover {
                background: #eff6ff;
                border-color: #93c5fd;
            }

            .terms-agreement-box.is-agreed {
                background: #eff6ff;
                border-color: #2563eb;
                box-shadow: 0 2px 8px rgba(37, 99, 235, 0.12);
            }

            .terms-checkbox-label {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                cursor: pointer;
                user-select: none;
                margin: 0;
            }

            .terms-checkbox-input {
                width: 19px;
                height: 19px;
                accent-color: #2563eb;
                cursor: pointer;
                flex-shrink: 0;
            }

            .terms-checkbox-text {
                font-size: 0.86rem;
                font-weight: 700;
                color: #1e3a8a;
                line-height: 1.4;
            }

            /* Footer */
            .terms-modal-footer {
                padding: 1rem 1.65rem;
                background: #f8fafc;
                border-top: 1px solid #e2e8f0;
                display: flex;
                justify-content: flex-end;
                align-items: center;
                gap: 0.75rem;
            }

            .btn-terms-submit {
                display: inline-flex;
                align-items: center;
                gap: 0.55rem;
                background: #cbd5e1;
                color: #64748b;
                padding: 0.7rem 1.65rem;
                border-radius: 10px;
                font-size: 0.88rem;
                font-weight: 700;
                border: none;
                cursor: not-allowed;
                transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: none;
            }

            .btn-terms-submit.is-active {
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                color: #ffffff;
                cursor: pointer;
                box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
                transform: translateY(0);
            }

            .btn-terms-submit.is-active:hover {
                background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
                box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
                transform: translateY(-1px);
            }

            .btn-terms-submit.is-active:active {
                transform: translateY(0);
            }

            /* Mobile View Optimization */
            @media (max-width: 640px) {
                .terms-modal-overlay {
                    padding: 0.65rem;
                    align-items: center;
                    justify-content: center;
                }

                .terms-modal-card {
                    width: 100%;
                    max-width: 100%;
                    border-radius: 16px;
                    max-height: 94vh;
                }

                .terms-modal-header {
                    padding: 0.9rem 1.15rem;
                    gap: 0.75rem;
                }

                .terms-header-icon-box {
                    width: 38px;
                    height: 38px;
                    border-radius: 10px;
                }

                .terms-header-icon-box svg {
                    width: 20px;
                    height: 20px;
                }

                .terms-header-title {
                    font-size: 1.02rem;
                    line-height: 1.2;
                }

                .terms-header-subtitle {
                    font-size: 0.72rem;
                    margin-top: 0.15rem;
                }

                .terms-modal-body {
                    padding: 0.85rem 1.15rem 0.75rem;
                    font-size: 0.80rem;
                    max-height: 58vh;
                }

                .terms-intro-text {
                    font-size: 0.78rem;
                    margin-bottom: 0.65rem;
                    line-height: 1.35;
                }

                .terms-cards-list {
                    gap: 0.5rem;
                }

                .terms-card-row {
                    padding: 0.6rem 0.75rem;
                    gap: 0.65rem;
                    border-radius: 9px;
                }

                .terms-badge-pill {
                    width: 22px;
                    height: 22px;
                    font-size: 0.72rem;
                    border-radius: 6px;
                }

                .terms-row-content {
                    font-size: 0.78rem;
                    line-height: 1.45;
                }

                .terms-agreement-box {
                    margin-top: 0.7rem;
                    padding: 0.7rem 0.8rem;
                    border-radius: 10px;
                }

                .terms-checkbox-label {
                    gap: 0.6rem;
                }

                .terms-checkbox-input {
                    width: 18px;
                    height: 18px;
                }

                .terms-checkbox-text {
                    font-size: 0.78rem;
                    line-height: 1.35;
                }

                .terms-modal-footer {
                    padding: 0.75rem 1.15rem;
                    background: #f8fafc;
                }

                .btn-terms-submit {
                    width: 100%;
                    justify-content: center;
                    padding: 0.75rem 1.25rem;
                    font-size: 0.92rem;
                    min-height: 44px;
                    border-radius: 10px;
                }
            }
        </style>

        <div id="termsConditionsModalOverlay" class="terms-modal-overlay">
            <div class="terms-modal-card">

                <!-- Modal Header -->
                <div class="terms-modal-header">
                    <div class="terms-header-icon-box">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <path d="M9 15l2 2 4-4"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="terms-header-title">Terms &amp; Conditions Agreement</h3>
                        <p class="terms-header-subtitle">
                            <span>VFSTR Vignan University</span>
                            <span class="terms-header-subtitle-dot"></span>
                            <span>Admissions Onboarding</span>
                        </p>
                    </div>
                </div>

                <!-- Modal Body with 3 Terms Lines -->
                <div class="terms-modal-body">
                    <p class="terms-intro-text">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Please review and confirm your acceptance of the admission terms before proceeding:</span>
                    </p>

                    <div class="terms-cards-list">
                        <!-- 1. Personal & Academic Details Authenticity -->
                        <div class="terms-card-row row-blue">
                            <div class="terms-badge-pill badge-blue">1</div>
                            <div class="terms-row-content">
                                I confirm that all personal, contact, academic, and category details provided by me in this application are accurate and authentic to the best of my knowledge.
                            </div>
                        </div>

                        <!-- 2. Academic & Conduct Compliance -->
                        <div class="terms-card-row row-green">
                            <div class="terms-badge-pill badge-green">2</div>
                            <div class="terms-row-content">
                                I agree to abide by all the academic rules, codes of conduct, fee regulations, and attendance policies of VFSTR Vignan University for the academic year 2026–27.
                            </div>
                        </div>

                        <!-- 3. Legal Undertaking & Forfeiture Policy -->
                        <div class="terms-card-row row-amber">
                            <div class="terms-badge-pill badge-amber">3</div>
                            <div class="terms-row-content">
                                I understand that providing any false, incomplete, or misleading information will result in immediate cancellation of my admission and forfeiture of fees.
                            </div>
                        </div>
                    </div>

                    <!-- Checkbox (Unticked by default) -->
                    <div id="termsAgreementBox" class="terms-agreement-box" onclick="toggleCheckboxFromBox(event)">
                        <label class="terms-checkbox-label">
                            <input type="checkbox" id="termsAgreementCheckbox" onchange="toggleTermsSubmitBtn(this)" class="terms-checkbox-input">
                            <span class="terms-checkbox-text">I have read, understood, and accept all the terms and conditions stated above.</span>
                        </label>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="terms-modal-footer">
                    <button type="button" id="btnAcceptTermsModal" class="btn-terms-submit" onclick="submitTermsAgreement()" disabled>
                        <span>I Agree &amp; Continue</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <script>
            function toggleCheckboxFromBox(e) {
                if (e.target && e.target.id === 'termsAgreementCheckbox') {
                    return; // native checkbox handles itself
                }
                const chk = document.getElementById('termsAgreementCheckbox');
                if (chk) {
                    chk.checked = !chk.checked;
                    toggleTermsSubmitBtn(chk);
                }
            }

            function toggleTermsSubmitBtn(chk) {
                const btn = document.getElementById('btnAcceptTermsModal');
                const box = document.getElementById('termsAgreementBox');
                if (!btn) return;
                if (chk.checked) {
                    btn.disabled = false;
                    btn.classList.add('is-active');
                    if (box) box.classList.add('is-agreed');
                } else {
                    btn.disabled = true;
                    btn.classList.remove('is-active');
                    if (box) box.classList.remove('is-agreed');
                }
            }

            function submitTermsAgreement() {
                const chk = document.getElementById('termsAgreementCheckbox');
                if (chk && !chk.checked) {
                    alert('Please tick the checkbox to agree to the terms and conditions.');
                    return;
                }
                const btn = document.getElementById('btnAcceptTermsModal');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.remove('is-active');
                    btn.innerHTML = '<span>Saving agreement...</span>';
                }

                fetch('{{ route("dashboards.accept-terms") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ terms: 'yes' })
                })
                .then(res => res.json())
                .then(data => {
                    const overlay = document.getElementById('termsConditionsModalOverlay');
                    if (overlay) {
                        overlay.style.transition = 'opacity 0.25s ease-out';
                        overlay.style.opacity = '0';
                        setTimeout(() => { overlay.style.display = 'none'; }, 250);
                    }
                })
                .catch(err => {
                    const overlay = document.getElementById('termsConditionsModalOverlay');
                    if (overlay) overlay.style.display = 'none';
                });
            }
        </script>
    @endif
    <script>
        function setAppThemeColor(color) {
            localStorage.setItem('crm_theme_color', color);
            document.documentElement.setAttribute('data-theme-color', color);
            document.querySelectorAll('.nav-theme-color-dot').forEach(function(dot) {
                var dotColor = dot.getAttribute('data-color');
                if (dotColor === color || (color === 'maroon' && dotColor === 'red')) {
                    dot.classList.add('is-active');
                } else {
                    dot.classList.remove('is-active');
                }
            });
        }

        (function() {
            var c = localStorage.getItem('crm_theme_color') || 'maroon';
            if (c === 'red') c = 'maroon';
            var d = document.querySelector('.nav-theme-color-dot[data-color="' + c + '"]');
            if (d) d.classList.add('is-active');
        })();
    </script>
</body>
</html>
