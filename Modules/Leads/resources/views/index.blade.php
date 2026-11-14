<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $roleName }} - Leads & Admission Counseling Portal | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;600&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css">

    <style>
        :root {
            --brand-primary: #1e40af;
            --brand-accent: #3b82f6;
            --surface-bg: #f8fafc;
            --card-border: #e2e8f0;
            --text-heading: #0f172a;
            --text-body: #334155;
            --text-muted: #64748b;
        }

        /* Metric KPI Cards (Single Row - Small Compact Squares) */
        .telecaller-stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.85rem;
            margin-bottom: 1.25rem;
        }

        @media (max-width: 900px) {
            .telecaller-stats-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 500px) {
            .telecaller-stats-row {
                grid-template-columns: 1fr;
            }
        }

        .stat-card-modern {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 0.65rem;
            padding: 0.85rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .stat-card-modern:hover {
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .stat-meta {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .stat-title {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #64748b;
        }

        .stat-number {
            font-size: 1.35rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-caption {
            font-size: 0.68rem;
            color: #10b981;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.2rem;
            white-space: nowrap;
        }

        .stat-icon-disc {
            width: 36px;
            height: 36px;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            flex-shrink: 0;
        }

        .stat-icon-blue { background: #eff6ff; border-color: #dbeafe; color: #2563eb; }
        .stat-icon-green { background: #f0fdf4; border-color: #dcfce7; color: #16a34a; }
        .stat-icon-amber { background: #fffbeb; border-color: #fef3c7; color: #d97706; }
        .stat-icon-purple { background: #faf5ff; border-color: #f3e8ff; color: #9333ea; }

        .main-body {
            padding: 0.9rem 1.15rem !important;
        }

        /* Portal Table Card */
        .portal-card-shell {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .portal-card-topbar {
            padding: 0.65rem 0.95rem;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.65rem;
            background: #ffffff;
        }

        .filter-nav-pills {
            display: flex;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .filter-nav-pill {
            padding: 0.4rem 0.85rem;
            border-radius: 0.5rem;
            font-size: 0.78rem;
            font-weight: 600;
            color: #64748b;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        .filter-nav-pill:hover {
            color: #0f172a;
            background: #f1f5f9;
        }

        .filter-nav-pill.is-active {
            color: var(--theme-accent, #1d4ed8);
            background: var(--theme-accent-light, #eff6ff);
            border-color: var(--theme-accent, #bfdbfe);
            font-weight: 700;
        }

        /* Clean Table */
        .tele-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.84rem;
            text-align: left;
        }

        .tele-table th {
            background: #f8fafc;
            padding: 0.55rem 0.85rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px solid var(--card-border);
            white-space: nowrap;
        }

        .tele-table td {
            padding: 0.55rem 0.85rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: var(--text-body);
        }

        .tele-table tbody tr:hover {
            background: #f8fafc;
        }

        .candidate-title {
            font-weight: 700;
            color: var(--text-heading);
            font-size: 0.88rem;
        }

        .candidate-sub {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        /* Progress Pill */
        .stage-meter-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.28rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .stage-meter-badge.is-completed-full {
            background: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        /* Pagination Bar */
        .tele-pagination-bar {
            padding: 1rem 1.4rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--card-border);
            background: #ffffff;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .tele-pagination-meta {
            font-size: 0.8rem;
            color: #64748b;
        }

        .tele-pagination-links .pagination {
            display: flex;
            gap: 0.3rem;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
        }

        .tele-pagination-links .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: #334155;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 0.4rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .tele-pagination-links .page-item.active .page-link {
            background: var(--theme-accent, #2563eb) !important;
            color: #ffffff !important;
            border-color: var(--theme-accent, #2563eb) !important;
            font-weight: 700;
        }

        .tele-pagination-links .page-item.disabled .page-link {
            opacity: 0.5;
            cursor: not-allowed;
            background: #f8fafc;
        }

        .tele-pagination-links .page-item:not(.active):not(.disabled) .page-link:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        /* Loading indicator for live search */
        .live-search-spinner {
            display: none;
            position: absolute;
            right: 10px;
            width: 14px;
            height: 14px;
            border: 2px solid #e2e8f0;
            border-top: 2px solid #2563eb;
            border-radius: 50%;
            animation: spin 0.6s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ==========================================================================
           LEADS INCHARGE TELECALLER & STEP ALLOCATION COMMAND CARD (CLEAN MODERN UI)
           ========================================================================== */
        .allocation-command-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 0.65rem 0.95rem;
            margin-bottom: 0.65rem;
            box-shadow: 0 2px 8px -2px rgba(15, 23, 42, 0.04);
            transition: all 0.2s ease;
        }

        .incharge-header-wrap {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.15rem;
            gap: 1rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .incharge-brand-box {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            flex: 1;
        }

        .incharge-icon-emblem {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1.5px solid #a7f3d0;
            color: #059669;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(16, 185, 129, 0.12);
        }

        .incharge-title-text {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            letter-spacing: -0.01em;
        }

        .incharge-sub-text {
            font-size: 0.76rem;
            color: #64748b;
            margin: 0.2rem 0 0 0;
            line-height: 1.4;
        }

        .incharge-sub-text code {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            background: #f1f5f9;
            color: #475569;
            padding: 0.1rem 0.45rem;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .incharge-selection-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 1rem;
            border-radius: 9999px;
            font-size: 0.78rem;
            font-weight: 700;
            background: #f8fafc;
            color: #64748b;
            border: 1.5px solid #e2e8f0;
            white-space: nowrap;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }

        .incharge-selection-badge.has-selected {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #93c5fd;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.14);
        }

        .incharge-badge-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #94a3b8;
            display: inline-block;
            transition: all 0.2s ease;
        }

        .incharge-selection-badge.has-selected .incharge-badge-dot {
            background: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }

        /* 2-Row Form Grid */
        .incharge-controls-grid {
            display: grid;
            grid-template-columns: 1.35fr 1fr 1.35fr;
            gap: 1.15rem;
            margin-bottom: 1.15rem;
        }

        @media (max-width: 1024px) {
            .incharge-controls-grid {
                grid-template-columns: 1fr;
                gap: 0.85rem;
            }
        }

        .incharge-action-row {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 1.15rem;
            align-items: flex-end;
        }

        @media (max-width: 768px) {
            .incharge-action-row {
                grid-template-columns: 1fr;
                gap: 0.85rem;
            }
            .incharge-action-row button {
                width: 100%;
            }
        }

        .incharge-field-group {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .incharge-label {
            font-size: 0.77rem;
            font-weight: 700;
            color: #334155;
            letter-spacing: 0.01em;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .incharge-label .required-dot {
            color: #ef4444;
            font-weight: 800;
            margin-left: 2px;
        }

        .incharge-control-shell {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
        }

        .incharge-control-shell .input-icon-left {
            position: absolute;
            left: 12px;
            color: #94a3b8;
            pointer-events: none;
            display: flex;
            align-items: center;
        }

        .incharge-select,
        .incharge-input {
            width: 100%;
            height: 42px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            padding: 0 0.85rem 0 2.35rem;
            font-size: 0.84rem;
            font-weight: 600;
            color: #0f172a;
            outline: none;
            transition: all 0.2s ease;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .incharge-input-no-icon {
            padding-left: 0.85rem !important;
        }

        .incharge-select:hover,
        .incharge-input:hover {
            border-color: #94a3b8;
        }

        .incharge-select:focus,
        .incharge-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18);
            background: #ffffff;
        }

        .incharge-btn-submit {
            height: 42px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 0 1.6rem;
            font-size: 0.86rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.55rem;
            box-shadow: 0 3px 10px rgba(16, 185, 129, 0.28);
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .incharge-btn-submit:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 5px 14px rgba(16, 185, 129, 0.38);
            transform: translateY(-1px);
        }

        .incharge-btn-submit:active {
            transform: translateY(0);
        }

        /* Dark Mode Support for Incharge Card */
        [data-theme-mode="dark"] .allocation-command-card {
            background: #131d31;
            border-color: #1e293b;
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.35);
        }
        [data-theme-mode="dark"] .incharge-header-wrap {
            border-color: #1e293b;
        }
        [data-theme-mode="dark"] .incharge-title-text {
            color: #f8fafc;
        }
        [data-theme-mode="dark"] .incharge-sub-text {
            color: #94a3b8;
        }
        [data-theme-mode="dark"] .incharge-sub-text code {
            background: #1e293b;
            color: #cbd5e1;
            border-color: #334155;
        }
        [data-theme-mode="dark"] .incharge-label {
            color: #cbd5e1;
        }
        [data-theme-mode="dark"] .incharge-select,
        [data-theme-mode="dark"] .incharge-input {
            background: #1a253c;
            border-color: #334155;
            color: #f1f5f9;
        }
        [data-theme-mode="dark"] .incharge-select:focus,
        [data-theme-mode="dark"] .incharge-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.25);
        }
        [data-theme-mode="dark"] .incharge-selection-badge {
            background: #1e293b;
            border-color: #334155;
            color: #94a3b8;
        }

        /* Moderate Buttons with SVG Icons */
        .tele-btn-action {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.38rem 0.7rem;
            border-radius: 0.45rem;
            font-size: 0.78rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.15s ease;
        }

        .tele-btn-call {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }
        .tele-btn-call:hover {
            background: #10b981;
            color: #ffffff;
            border-color: #10b981;
        }

        .tele-btn-wapp {
            background: #f0fdf4;
            color: #15803d;
            border-color: #bbf7d0;
        }
        .tele-btn-wapp:hover {
            background: #22c55e;
            color: #ffffff;
            border-color: #22c55e;
        }

        .tele-btn-wizard {
            background: var(--theme-accent, #2563eb);
            color: #ffffff;
            border-color: var(--theme-accent-hover, #1d4ed8);
            box-shadow: 0 1px 2px var(--theme-accent-ring, rgba(37, 99, 235, 0.2));
        }
        .tele-btn-wizard:hover {
            background: var(--theme-accent-hover, #1d4ed8);
        }

        .tele-status-select {
            padding: 0.38rem 1.85rem 0.38rem 0.75rem;
            border: 1.5px solid var(--card-border);
            border-radius: 0.5rem;
            font-size: 0.78rem;
            font-weight: 600;
            background: #ffffff;
            color: var(--text-heading);
            cursor: pointer;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.65rem center;
            background-size: 13px 13px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            transition: all 0.15s ease;
        }
        .tele-status-select:hover {
            border-color: #cbd5e1;
            background-color: #f8fafc;
        }
        .tele-status-select:focus {
            border-color: var(--theme-accent, #2563eb);
            box-shadow: 0 0 0 3px var(--theme-accent-ring, rgba(37, 99, 235, 0.15));
            background-color: #ffffff;
        }
        .tele-status-select option {
            background: #ffffff;
            color: #0f172a;
            padding: 6px 10px;
        }

        /* ================= POPUP WIZARD MODAL ================= */
        .wizard-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .wizard-overlay.active {
            display: flex;
        }

        .wizard-modal-box {
            background: #ffffff;
            border-radius: 1rem;
            width: 100%;
            max-width: 860px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.2);
            border: 1px solid var(--card-border);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            max-height: 88vh;
            animation: slideModal 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .wizard-modal-header {
            padding: 1.25rem 1.75rem;
            background: #0f172a;
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .candidate-header-info {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .candidate-header-name {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .candidate-header-badge {
            font-size: 0.7rem;
            padding: 0.15rem 0.5rem;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
            color: #cbd5e1;
        }

        .candidate-header-sub {
            color: #94a3b8;
            font-size: 0.78rem;
            margin-top: 0.2rem;
            display: flex;
            gap: 1rem;
        }

        .btn-close-modal {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            width: 32px;
            height: 32px;
            border-radius: 0.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-close-modal:hover {
            background: rgba(239, 68, 68, 0.85);
            border-color: transparent;
        }

        /* 5 Stepper Steps Inside Modal */
        .stepper-nav-bar {
            padding: 1rem 1.75rem;
            background: #f8fafc;
            border-bottom: 1px solid var(--card-border);
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 0.65rem;
        }

        .stepper-tab-item {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 0.55rem;
            padding: 0.65rem 0.25rem;
            text-align: center;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
            transition: all 0.15s ease;
        }

        .stepper-tab-item:hover {
            border-color: #93c5fd;
            background: #f8fafc;
        }

        .stepper-tab-item.is-selected {
            border-color: #2563eb;
            background: #eff6ff;
            box-shadow: 0 1px 4px rgba(37, 99, 235, 0.12);
        }

        .stepper-tab-item.is-completed {
            background: #ffffff;
            border-color: #86efac;
        }

        .step-index-circle {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: #f1f5f9;
            color: #64748b;
            font-size: 0.7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.15s ease;
        }

        .stepper-tab-item.is-selected .step-index-circle {
            background: #2563eb;
            color: #ffffff;
        }

        .stepper-tab-item.is-completed .step-index-circle {
            background: #10b981;
            color: #ffffff;
        }

        .step-title-text {
            font-size: 0.68rem;
            font-weight: 600;
            color: #334155;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }

        .step-state-tag {
            font-size: 0.58rem;
            font-weight: 700;
            padding: 0.12rem 0.4rem;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .tag-done { background: #dcfce7; color: #15803d; }
        .tag-progress { background: #dbeafe; color: #1e40af; }
        .tag-locked { background: #f1f5f9; color: #64748b; }

        /* Assessment Card in Modal Body */
        .modal-body-content {
            padding: 1.5rem 1.75rem;
            overflow-y: auto;
            flex: 1;
            background: #ffffff;
        }

        .assessment-panel {
            border: 1px solid var(--card-border);
            border-radius: 0.75rem;
            padding: 1.35rem 1.5rem;
            background: #f8fafc;
        }

        .assessment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.15rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--card-border);
        }

        .stage-heading-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-heading);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .assessment-status-options {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.85rem;
            margin-top: 0.75rem;
        }

        @media (max-width: 600px) {
            .assessment-status-options {
                grid-template-columns: 1fr;
            }
        }

        .status-choice-card {
            background: #ffffff;
            border: 1.5px solid var(--card-border);
            border-radius: 0.65rem;
            padding: 0.95rem 1rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            cursor: pointer;
            transition: all 0.15s ease;
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
        }

        .status-choice-card:hover {
            border-color: #cbd5e1;
            background: #ffffff;
        }

        .status-choice-card.active-completed {
            border-color: #10b981;
            background: #f0fdf4;
            color: #166534;
            box-shadow: 0 1px 3px rgba(16, 185, 129, 0.15);
        }

        .status-choice-card.active-progress {
            border-color: #2563eb;
            background: #eff6ff;
            color: #1d4ed8;
            box-shadow: 0 1px 3px rgba(37, 99, 235, 0.15);
        }

        .status-choice-card.active-locked {
            border-color: #94a3b8;
            background: #f8fafc;
            color: #475569;
        }

        .remarks-field-box {
            margin-top: 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .remarks-textarea {
            width: 100%;
            padding: 0.65rem 0.85rem;
            border: 1.5px solid var(--card-border);
            border-radius: 0.55rem;
            font-size: 0.8rem;
            font-family: inherit;
            resize: vertical;
            outline: none;
            background: #ffffff;
        }
        .remarks-textarea:focus {
            border-color: #2563eb;
        }

        /* Modal Footer */
        .wizard-modal-footer {
            padding: 1rem 1.75rem;
            background: #ffffff;
            border-top: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Clean Toast Alert */
        #teleToast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #0f172a;
            color: #ffffff;
            padding: 0.75rem 1.25rem;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            font-size: 0.82rem;
            font-weight: 600;
            display: none;
            z-index: 999999;
            align-items: center;
            gap: 0.5rem;
            border-left: 4px solid #10b981;
        }

        @keyframes slideModal {
            from { transform: translateY(16px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .lead-detail-primary {
            font-weight: 600;
            color: #0f172a;
        }

        .btn-print-leads {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.42rem 0.8rem;
            background: #ffffff;
            color: #334155;
            border: 1.5px solid var(--card-border);
            border-radius: 0.45rem;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-print-leads:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        /* ================= DARK MODE OVERRIDES ================= */
        [data-theme-mode="dark"] .stat-card-modern {
            background: #131d31;
            border-color: #1e293b;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        }

        [data-theme-mode="dark"] .stat-card-modern:hover {
            border-color: #334155;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
        }

        [data-theme-mode="dark"] .stat-title {
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .stat-number {
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .stat-icon-disc {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme-mode="dark"] .portal-card-shell {
            background: #131d31;
            border-color: #1e293b;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        }

        [data-theme-mode="dark"] .portal-card-topbar {
            background: #131d31;
            border-color: #1e293b;
        }

        [data-theme-mode="dark"] .filter-nav-pill {
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .filter-nav-pill:hover {
            color: #f8fafc;
            background: rgba(255, 255, 255, 0.06);
        }

        [data-theme-mode="dark"] .filter-nav-pill.is-active {
            background: rgba(37, 99, 235, 0.2);
            border-color: rgba(37, 99, 235, 0.4);
            color: #60a5fa;
        }

        [data-theme-mode="dark"] #liveSearchInput {
            background: #0f172a;
            border-color: #334155 !important;
            color: #f8fafc;
        }

        [data-theme-mode="dark"] #liveSearchInput:focus {
            border-color: #3b82f6 !important;
        }

        [data-theme-mode="dark"] .btn-print-leads {
            background: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
            border-color: rgba(255, 255, 255, 0.12);
        }

        [data-theme-mode="dark"] .btn-print-leads:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.25);
        }

        [data-theme-mode="dark"] .tele-table th {
            background: #0f172a;
            border-color: #1e293b;
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .tele-table td {
            border-color: #1e293b;
            color: #cbd5e1;
        }

        [data-theme-mode="dark"] .tele-table tbody tr:hover {
            background: rgba(255, 255, 255, 0.03);
        }

        [data-theme-mode="dark"] .candidate-title {
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .candidate-sub {
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .lead-detail-primary {
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .tele-status-select {
            background-color: #0f172a;
            border-color: #334155;
            color: #f8fafc;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
        }

        [data-theme-mode="dark"] .tele-status-select option {
            background: #0f172a;
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .stage-meter-badge {
            background: rgba(37, 99, 235, 0.15);
            color: #93c5fd;
            border-color: rgba(37, 99, 235, 0.3);
        }

        [data-theme-mode="dark"] .stage-meter-badge.is-completed-full {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.3);
        }

        [data-theme-mode="dark"] .tele-pagination-bar {
            background: #131d31;
            border-color: #1e293b;
        }

        [data-theme-mode="dark"] .tele-pagination-meta {
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .tele-pagination-links .page-item .page-link {
            background: #0f172a;
            border-color: #1e293b;
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .tele-pagination-links .page-item:not(.active):not(.disabled) .page-link:hover {
            background: #1e293b;
            color: #f8fafc;
            border-color: #334155;
        }

        [data-theme-mode="dark"] .tele-pagination-links .page-item.disabled .page-link {
            background: #0b1120;
            color: #475569;
        }

        /* Wizard Modal Dark Mode */
        [data-theme-mode="dark"] .wizard-modal-box {
            background: #131d31;
            border-color: #1e293b;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        [data-theme-mode="dark"] .stepper-nav-bar {
            background: #0f172a;
            border-color: #1e293b;
        }

        [data-theme-mode="dark"] .stepper-tab-item {
            background: #131d31;
            border-color: #1e293b;
        }

        [data-theme-mode="dark"] .stepper-tab-item:hover {
            border-color: #334155;
            background: #1e293b;
        }

        [data-theme-mode="dark"] .stepper-tab-item.is-selected {
            border-color: #3b82f6;
            background: rgba(37, 99, 235, 0.2);
        }

        [data-theme-mode="dark"] .stepper-tab-item.is-completed {
            border-color: rgba(16, 185, 129, 0.4);
            background: #131d31;
        }

        [data-theme-mode="dark"] .step-index-circle {
            background: #1e293b;
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .step-title-text {
            color: #cbd5e1;
        }

        [data-theme-mode="dark"] .tag-locked {
            background: #1e293b;
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .modal-body-content {
            background: #131d31;
        }

        [data-theme-mode="dark"] .assessment-panel {
            background: #0f172a;
            border-color: #1e293b;
        }

        [data-theme-mode="dark"] .assessment-header {
            border-color: #1e293b;
        }

        [data-theme-mode="dark"] .stage-heading-title {
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .status-choice-card {
            background: #131d31;
            border-color: #1e293b;
            color: #cbd5e1;
        }

        [data-theme-mode="dark"] .status-choice-card:hover {
            border-color: #334155;
            background: #1e293b;
        }

        [data-theme-mode="dark"] .status-choice-card.active-completed {
            border-color: #10b981;
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
        }

        [data-theme-mode="dark"] .status-choice-card.active-progress {
            border-color: #3b82f6;
            background: rgba(37, 99, 235, 0.15);
            color: #93c5fd;
        }

        [data-theme-mode="dark"] .status-choice-card.active-locked {
            border-color: #475569;
            background: #1e293b;
            color: #cbd5e1;
        }

        [data-theme-mode="dark"] .remarks-textarea {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .remarks-textarea:focus {
            border-color: #3b82f6;
        }

        [data-theme-mode="dark"] .wizard-modal-footer {
            background: #0f172a;
            border-color: #1e293b;
        }

        /* Call Duration Roller Popover */
        .duration-input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .duration-roller-trigger {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            color: #475569;
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            cursor: pointer;
            padding: 0;
        }

        .duration-roller-trigger:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .duration-roller-popover {
            position: fixed;
            display: none;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.22);
            padding: 8px;
            z-index: 100100;
            user-select: none;
            width: 184px;
        }

        .duration-roller-popover.open {
            display: block;
        }

        .duration-roller-headers {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
            margin-bottom: 4px;
        }

        .duration-roller-hdr {
            text-align: center;
            font-size: 0.62rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: #64748b;
            text-transform: uppercase;
        }

        .duration-roller-cols {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 4px;
        }

        .duration-roller-col {
            height: 192px;
            overflow-y: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            scroll-snap-type: y proximity;
            border-radius: 4px;
        }

        .duration-roller-col::-webkit-scrollbar {
            display: none;
        }

        .duration-roller-item {
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.92rem;
            font-weight: 500;
            color: #0f172a;
            border-radius: 4px;
            cursor: pointer;
            scroll-snap-align: start;
            transition: background 0.1s ease;
        }

        .duration-roller-item:hover {
            background: #f1f5f9;
        }

        .duration-roller-item.is-selected {
            background: #d1d5db;
            color: #0f172a;
            font-weight: 700;
        }

        .duration-roller-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #e2e8f0;
            gap: 6px;
        }

        .duration-roller-done {
            background: #7c0e23;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 3px 10px;
            cursor: pointer;
        }

        [data-theme-mode="dark"] .duration-roller-popover {
            background: #1e293b;
            border-color: #334155;
        }

        [data-theme-mode="dark"] .duration-roller-item {
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .duration-roller-item:hover {
            background: #334155;
        }

        [data-theme-mode="dark"] .duration-roller-item.is-selected {
            background: #475569;
            color: #ffffff;
        }

        [data-theme-mode="dark"] .duration-roller-footer {
            border-color: #334155;
        }

        /* ================= PRINT REPORT STYLES ================= */
        .print-only-header {
            display: none;
        }

        @media print {
            .crm-sidebar,
            .top-bar,
            .theme-preset-bar,
            .filter-nav-pills,
            .btn-print-leads,
            #liveSearchInput,
            .tele-btn-action,
            .tele-status-select,
            .tele-pagination-bar,
            .wizard-overlay,
            #teleToast {
                display: none !important;
            }

            body, .app-wrapper, .main-body, .app-main {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .main-body {
                padding: 10mm !important;
            }

            .print-only-header {
                display: block !important;
                margin-bottom: 15px;
                padding-bottom: 10px;
                border-bottom: 2px solid #0f172a;
            }

            .print-header-top {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 0.5rem;
            }

            .print-org-info h2 {
                font-size: 13pt;
                font-weight: 800;
                margin: 0;
                color: #0f172a;
            }

            .print-org-info p {
                font-size: 8pt;
                margin: 2px 0 0;
                color: #475569;
            }

            .print-report-meta {
                background: #f8fafc;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                padding: 6px 12px;
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 6px;
                font-size: 8pt;
                color: #1e293b;
            }

            .telecaller-stats-row {
                display: grid !important;
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 6px !important;
                margin-bottom: 12px !important;
            }

            .stat-card-modern {
                border: 1px solid #cbd5e1 !important;
                background: #ffffff !important;
                box-shadow: none !important;
                padding: 6px 8px !important;
            }

            .stat-icon-disc {
                display: none !important;
            }

            .portal-card-shell {
                border: 1px solid #94a3b8 !important;
                box-shadow: none !important;
            }

            .portal-card-topbar {
                display: none !important;
            }

            .tele-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .tele-table th, .tele-table td {
                border: 1px solid #cbd5e1 !important;
                padding: 5px 6px !important;
                font-size: 7.5pt !important;
                color: #000000 !important;
            }

            .tele-table th {
                background: #f1f5f9 !important;
                font-weight: 700 !important;
            }

            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>
<div class="app-wrapper">
    <!-- 1. LEFT SIDEBAR -->
    @include('layouts.crm-sidebar')

    <!-- 2. MAIN CONTENT AREA -->
    <div class="app-main">
        <!-- Top Navigation Header -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button type="button" class="topbar-toggle-sidebar-btn" onclick="toggleSidebarMenu()" title="Open Navigation Menu" aria-label="Toggle Sidebar">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="3" y1="12" x2="21" y2="12"></line>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <line x1="3" y1="18" x2="21" y2="18"></line>
                    </svg>
                </button>
                <div class="topbar-page-breadcrumb">
                    <span class="breadcrumb-item">Leads</span>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-active-tag">
                        <span class="breadcrumb-dot"></span>
                        @if($activeTab === 'upload')
                            <span>Leads Upload</span>
                        @elseif($activeTab === 'input')
                            <span>Lead Input &bull; Complete Leads Directory</span>
                        @elseif($activeTab === 'allocation' || in_array($roleId, [2, 3]))
                            <span>Leads Allocation &bull; Unallocated Queue</span>
                        @else
                            <span>Allotted Leads (Tele Caller)</span>
                        @endif
                    </span>
                </div>
            </div>

            <div class="top-bar-right">
                @include('layouts.role-switcher')
            </div>
        </header>

        <!-- Main Body -->
        <main class="main-body">
            <!-- Official Print Header (Visible only when printed) -->
            <div class="print-only-header">
                <div class="print-header-top">
                    <img src="/images/vignan-logo.png" alt="VFSTR Logo" style="height:44px;">
                    <div class="print-org-info">
                        <h2>VIGNAN'S FOUNDATION FOR SCIENCE, TECHNOLOGY &amp; RESEARCH</h2>
                        <p>(Deemed to be University) &bull; Admissions Counseling Wing &bull; Allotted Leads Report</p>
                    </div>
                </div>
                <div class="print-report-meta">
                    <div><strong>Officer:</strong> {{ $roleName }}</div>
                    <div><strong>Date &amp; Time:</strong> {{ date('d M Y, h:i A') }}</div>
                    <div><strong>Total Allotted:</strong> {{ $totalAllotted }}</div>
                    <div><strong>Unallocated:</strong> {{ $unallocatedCount ?? 0 }}</div>
                </div>
            </div>

            @if($activeTab !== 'upload' && session('upload_success'))
                <div style="background: #ecfdf5; border: 1.5px solid #a7f3d0; color: #065f46; padding: 1rem 1.25rem; border-radius: 0.65rem; margin-bottom: 1.25rem; font-size: 0.88rem; font-weight: 700; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.1);">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 1.4rem;">✅</span>
                        <div>
                            <div>{{ session('upload_success') }}</div>
                            @if(session('upload_summary'))
                                @php $sum = session('upload_summary'); @endphp
                                <div style="font-size: 0.78rem; font-weight: 600; color: #047857; margin-top: 4px; display: flex; gap: 12px; flex-wrap: wrap;">
                                    <span>📊 Total Found: <strong>{{ $sum['total_rows'] }}</strong></span>
                                    <span>📥 Inserted New: <strong>{{ $sum['inserted_count'] }}</strong></span>
                                    <span>⚠️ Duplicates Skipped: <strong>{{ $sum['total_duplicates'] }}</strong></span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('leads.allocation', ['tab' => 'allocation']) }}" style="padding: 0.5rem 1.15rem; background: #059669; color: #ffffff; border-radius: 6px; font-size: 0.82rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; white-space: nowrap;">
                        Go to Leads Allocation &rarr;
                    </a>
                </div>
            @endif

            @if($activeTab !== 'upload' && session('upload_error'))
                <div style="background: #fef2f2; border: 1.5px solid #fecaca; color: #991b1b; padding: 0.95rem 1.25rem; border-radius: 0.65rem; margin-bottom: 1.25rem; font-size: 0.88rem; font-weight: 700; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 1.3rem;">❌</span>
                    <div>{{ session('upload_error') }}</div>
                </div>
            @endif

            @if(session('status'))
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 0.75rem 1.25rem; border-radius: 0.5rem; margin-bottom: 1.25rem; font-size: 0.82rem; font-weight: 600;">
                    ✓ {{ session('status') }}
                </div>
            @endif

            <!-- 4 METRIC STAT CARDS WITH SVG ICONS (Hidden on Allocation, Allotted, and Upload Tabs) -->
            @if($activeTab !== 'allocation' && $activeTab !== 'allotted' && $activeTab !== 'upload')
            <div class="telecaller-stats-row">
                <div class="stat-card-modern">
                    <div class="stat-meta">
                        <span class="stat-title">Allotted Leads</span>
                        <span class="stat-number" id="statAllottedCount">{{ $totalAllotted }}</span>
                        <span class="stat-caption">In Telecaller Queues</span>
                    </div>
                    <div class="stat-icon-disc stat-icon-blue">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                </div>

                <div class="stat-card-modern">
                    <div class="stat-meta">
                        <span class="stat-title">Unallocated Leads</span>
                        <span class="stat-number" id="statUnallocatedCount" style="color: #b91c1c;">{{ $unallocatedCount ?? 0 }}</span>
                        <span class="stat-caption">Ready for Assignment</span>
                    </div>
                    <div class="stat-icon-disc stat-icon-amber">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                </div>

                <div class="stat-card-modern">
                    <div class="stat-meta">
                        <span class="stat-title">Stage Tasks</span>
                        <span class="stat-number" id="statTasksCount">{{ $totalTasksCount ?? 0 }}</span>
                        <span class="stat-caption">Tasks in Log</span>
                    </div>
                    <div class="stat-icon-disc stat-icon-green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 11 12 14 22 4"></polyline>
                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                        </svg>
                    </div>
                </div>

                <div class="stat-card-modern">
                    <div class="stat-meta">
                        <span class="stat-title">Converted / Paid</span>
                        <span class="stat-number">{{ $converted }}</span>
                        <span class="stat-caption">Fee Paid &amp; Enrolled</span>
                    </div>
                    <div class="stat-icon-disc stat-icon-purple">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"></path>
                            <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"></path>
                            <path d="M4 22h16"></path>
                            <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"></path>
                            <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"></path>
                            <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

            <!-- LEADS INCHARGE ALLOCATION CONTROL PANEL (Shown on Leads Allocation tab) -->
            @if($activeTab === 'allocation')
                <div class="allocation-command-card" style="padding: 0.65rem 0.95rem; margin-bottom: 0.65rem;">
                    <!-- Single-line form: Select Telecaller, Allocation Target, Allocate button -->
                    <form id="leadInchargeAllotForm" onsubmit="event.preventDefault(); submitInchargeAllocation();" style="margin: 0; padding: 0;">
                        <div style="display: flex; align-items: flex-end; gap: 0.85rem; flex-wrap: wrap;">
                            <!-- 1. Select Telecaller -->
                            <div style="flex: 1; min-width: 250px;">
                                <label for="inchargeTelecallerSelect" class="incharge-label" style="font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem; display: block;">
                                    <span>Select Telecaller<span class="required-dot" style="color: #ef4444; margin-left: 2px;">*</span></span>
                                </label>
                                <div class="incharge-control-shell">
                                    <span class="input-icon-left">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                                        </svg>
                                    </span>
                                    <select id="inchargeTelecallerSelect" class="incharge-select" required>
                                        <option value="">-- Choose Telecaller --</option>
                                        @foreach($telecallers as $tc)
                                            <option value="{{ $tc->emp_code }}">
                                                {{ $tc->full_name }} ({{ $tc->emp_code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 2. Allocation Target with bucket sizes 100, 300, 500, 1000 -->
                            <div style="flex: 1; min-width: 250px;">
                                <label for="inchargeTargetSelect" class="incharge-label" style="font-size: 0.78rem; font-weight: 700; color: #334155; margin-bottom: 0.25rem; display: block;">
                                    <span>Allocation Target<span class="required-dot" style="color: #ef4444; margin-left: 2px;">*</span></span>
                                </label>
                                <div class="incharge-control-shell">
                                    <span class="input-icon-left">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                            <circle cx="9" cy="7" r="4"/>
                                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                        </svg>
                                    </span>
                                    <select id="inchargeTargetSelect" class="incharge-select" required>
                                        <option value="selected">Selected Checked Leads</option>
                                        <option value="100">Bucket: 100 Leads</option>
                                        <option value="300">Bucket: 300 Leads</option>
                                        <option value="500">Bucket: 500 Leads</option>
                                        <option value="1000">Bucket: 1000 Leads</option>
                                        <option value="unallocated">All Unallocated Leads</option>
                                    </select>
                                </div>
                            </div>

                            <!-- 3. Allocate Button -->
                            <div style="flex-shrink: 0;">
                                <button type="submit" id="btnInchargeAllot" class="incharge-btn-submit" style="height: 42px; margin: 0; padding: 0 1.5rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px;">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                        <polyline points="16 11 18 13 22 9"/>
                                    </svg>
                                    <span>Allocate to Telecaller</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @elseif($activeTab === 'input')
                <div class="allocation-command-card" style="padding: 0.85rem 1.15rem; margin-bottom: 0.65rem;">
                    <div class="incharge-header-wrap" style="margin-bottom: 0; padding-bottom: 0; border-bottom: none;">
                        <div class="incharge-brand-box">
                            <div class="incharge-icon-emblem">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="incharge-title-text">
                                    <span>Lead Input &bull; Complete Master Leads Directory</span>
                                </h3>
                                <p class="incharge-sub-text">
                                    Displays the entire leads dataset including allocated telecaller queues, branch CRM IDs, step progress, and latest task remarks.
                                </p>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap;">
                            <button type="button" onclick="openAddLeadModal()" class="incharge-btn-submit" style="height: 36px; padding: 0 1rem; font-size: 0.8rem;">
                                <span>+ Add Lead Input</span>
                            </button>
                            <a href="{{ route('leads.allocation', ['tab' => 'allocation']) }}" style="font-size: 0.78rem; font-weight: 700; color: #b91c1c; text-decoration: none; padding: 0.45rem 0.85rem; background: #fef2f2; border: 1px solid #fecaca; border-radius: 0.45rem;">
                                Unallocated Queue (<span id="bannerUnallocCount">{{ $unallocatedCount ?? 0 }}</span>) &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            @if($activeTab === 'upload')
                <!-- LEADS UPLOAD HEADER & ACTIONS BAR (SPACE SAVER) -->
                <div class="allocation-command-card" style="padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; gap: 1rem; flex-wrap: nowrap; overflow-x: auto;">
                        <!-- Left Group: Title & Action Buttons -->
                        <div style="display: flex; align-items: center; gap: 10px; flex-shrink: 0;">
                            <div style="font-size: 1.15rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px; white-space: nowrap;">
                                <span style="font-size: 1.25rem;">📥</span>
                                <span>Leads Upload</span>
                            </div>
                            <button type="button" 
                                    onclick="openLeadUploadModal()" 
                                    class="incharge-btn-submit" 
                                    id="btnHeaderBrowseComputer"
                                    style="height: 36px; padding: 0 1.15rem; font-size: 0.825rem; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; border-radius: 8px; cursor: pointer; background: #2563eb; color: #ffffff; border: none; box-shadow: 0 1px 3px rgba(37,99,235,0.25); white-space: nowrap; transition: all 0.15s ease;"
                                    onmouseover="this.style.background='#1d4ed8'"
                                    onmouseout="this.style.background='#2563eb'">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <span>Browse Computer</span>
                            </button>
                            <a href="{{ route('leads.downloadSample') }}" 
                               style="height: 36px; padding: 0 1.05rem; display: inline-flex; align-items: center; gap: 6px; background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 8px; color: #334155; font-size: 0.825rem; font-weight: 700; text-decoration: none; white-space: nowrap; box-shadow: 0 1px 2px rgba(0,0,0,0.03); transition: all 0.15s ease;"
                               onmouseover="this.style.borderColor='#94a3b8'; this.style.color='#0f172a'"
                               onmouseout="this.style.borderColor='#cbd5e1'; this.style.color='#334155'">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="3" x2="12" y2="15"></line>
                                </svg>
                                <span>Download Sample Template</span>
                            </a>
                        </div>

                        @php
                            $effectiveTotalLeads = isset($totalLeadsCount) && $totalLeadsCount > 0
                                ? $totalLeadsCount
                                : (int) \App\Models\Lead::count();

                            $effectiveNormalLeads = isset($normalLeadsCount)
                                ? $normalLeadsCount
                                : (int) \App\Models\Lead::where(function ($q) {
                                      $q->whereIn('log_userid', ['PORTAL', 'SELF_REGISTER', 'AUTOSAVE', 'MANUAL'])
                                        ->orWhereNotNull('applino')
                                        ->orWhere('phnostatus', '!=', 'New Lead');
                                  })->count();

                            $effectiveBulkLeads = isset($bulkLeadsCount)
                                ? $bulkLeadsCount
                                : max(0, $effectiveTotalLeads - $effectiveNormalLeads);
                        @endphp
                        <!-- Right Group: Metric Badges (Strictly 1 Single Line) -->
                        <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0; white-space: nowrap;">
                            <!-- 1. Total Leads -->
                            <div style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #2563eb;"></span>
                                <span style="font-size: 0.74rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.3px;">Total Leads Count:</span>
                                <strong id="headerTotalLeadsBadge" style="font-size: 0.94rem; font-weight: 800; color: #0f172a; font-family: 'JetBrains Mono', monospace;">
                                    {{ number_format($effectiveTotalLeads) }}
                                </strong>
                            </div>

                            <!-- 2. Bulk Upload -->
                            <div style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #f0fdf4; border: 1.5px solid #bbf7d0; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #16a34a;"></span>
                                <span style="font-size: 0.74rem; font-weight: 700; color: #166534; text-transform: uppercase; letter-spacing: 0.3px;">Bulk Upload:</span>
                                <strong id="headerBulkLeadsBadge" style="font-size: 0.94rem; font-weight: 800; color: #15803d; font-family: 'JetBrains Mono', monospace;">
                                    {{ number_format($effectiveBulkLeads) }}
                                </strong>
                            </div>

                            <!-- 3. Normal Leads -->
                            <div style="display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
                                <span style="display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #6366f1;"></span>
                                <span style="font-size: 0.74rem; font-weight: 700; color: #1e40af; text-transform: uppercase; letter-spacing: 0.3px;">Normal:</span>
                                <strong id="headerNormalLeadsBadge" style="font-size: 0.94rem; font-weight: 800; color: #1d4ed8; font-family: 'JetBrains Mono', monospace;">
                                    {{ number_format($effectiveNormalLeads) }}
                                </strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prominent Notifications for Upload (Session Alerts & AJAX Feedback) -->
                <div id="uploadNotificationArea">
                    @if(session('upload_success'))
                        <div class="upload-alert-box" style="background: #f0fdf4; border: 2px solid #22c55e; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 16px rgba(34,197,94,0.12);">
                            <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                                <div style="display: flex; align-items: flex-start; gap: 14px;">
                                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #dcfce7; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #16a34a; flex-shrink: 0; font-weight: 800;">
                                        ✓
                                    </div>
                                    <div>
                                        <div style="font-size: 1.15rem; font-weight: 800; color: #15803d;">
                                            File Uploaded Successfully!
                                        </div>
                                        <div style="font-size: 0.9rem; font-weight: 600; color: #166534; margin-top: 4px;">
                                            {{ session('upload_success') }}
                                        </div>
                                        @if(session('upload_summary'))
                                            @php $sum = session('upload_summary'); @endphp
                                            <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #bbf7d0; font-size: 0.82rem;">
                                                <span style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 6px; padding: 4px 10px; font-weight: 700; color: #0f172a;">
                                                    📊 Total in File: <span style="color: #2563eb;">{{ number_format($sum['total_rows'] ?? 0) }}</span>
                                                </span>
                                                <span style="background: #ffffff; border: 1px solid #86efac; border-radius: 6px; padding: 4px 10px; font-weight: 700; color: #15803d;">
                                                    🟢 New Leads Added: <span style="color: #16a34a;">{{ number_format($sum['inserted_count'] ?? 0) }}</span>
                                                </span>
                                                @if(($sum['total_duplicates'] ?? 0) > 0)
                                                    <span style="background: #ffffff; border: 1px solid #fde047; border-radius: 6px; padding: 4px 10px; font-weight: 700; color: #854d0e;">
                                                        ⚠️ Duplicates Skipped: <span style="color: #d97706;">{{ number_format($sum['total_duplicates']) }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('leads.allocation', ['tab' => 'allocation']) }}" style="padding: 0.65rem 1.25rem; background: #16a34a; color: #ffffff; border-radius: 8px; font-size: 0.84rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(22,163,74,0.3);">
                                    <span>Go to Leads Allocation &rarr;</span>
                                </a>
                            </div>
                        </div>
                    @endif

                    @if(session('upload_error'))
                        <div class="upload-alert-box" style="background: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #dc2626; flex-shrink: 0; font-weight: 800;">
                                    ✕
                                </div>
                                <div>
                                    <div style="font-size: 1.05rem; font-weight: 800; color: #991b1b;">
                                        Upload Failed / Error
                                    </div>
                                    <div style="font-size: 0.86rem; color: #b91c1c; font-weight: 600; margin-top: 4px;">
                                        {{ session('upload_error') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="upload-alert-box" style="background: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
                            <div style="display: flex; align-items: center; gap: 14px;">
                                <div style="width: 44px; height: 44px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #dc2626; flex-shrink: 0; font-weight: 800;">
                                    ✕
                                </div>
                                <div>
                                    <div style="font-size: 1.05rem; font-weight: 800; color: #991b1b;">
                                        Validation Error
                                    </div>
                                    <div style="font-size: 0.86rem; color: #b91c1c; font-weight: 600; margin-top: 4px;">
                                        @foreach($errors->all() as $err)
                                            <div>&bull; {{ $err }}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- ================= POPUP MODAL: LEADS UPLOAD ================= -->
                <div class="wizard-overlay" id="leadUploadModal" onclick="if(event.target.id==='leadUploadModal') closeLeadUploadModal()">
                    <div class="wizard-modal-box" style="max-width: 620px; border-radius: 1rem; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);">
                        <!-- Modal Header -->
                        <div class="wizard-modal-header" style="padding: 1.15rem 1.5rem; background: #0f172a; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <span style="font-size: 1.35rem;">📥</span>
                                <div>
                                    <div class="candidate-header-name" style="font-size: 1.1rem; font-weight: 800; color: #ffffff;">Leads Upload</div>
                                    <div class="candidate-header-sub" style="font-size: 0.78rem; color: #94a3b8; margin-top: 2px;">Select or drop Excel/CSV file to ingest leads into system</div>
                                </div>
                            </div>
                            <button type="button" class="btn-close-modal" onclick="closeLeadUploadModal()" title="Close">✕</button>
                        </div>

                        <!-- Modal Body Content -->
                        <div class="modal-body-content" style="padding: 1.5rem 1.75rem; background: #ffffff;">
                            <div id="modalUploadNotificationArea"></div>
                            <form action="{{ route('leads.uploadExcel') }}" method="POST" enctype="multipart/form-data" id="leadExcelUploadForm">
                                @csrf
                                <div style="max-width: 100%; margin: 0 auto;">
                                    <!-- Dropzone Box -->
                                    <div style="border: 2px dashed #93c5fd; background: #f8fafc; border-radius: 14px; padding: 2.25rem 1.75rem; text-align: center; cursor: pointer; transition: all 0.2s ease; position: relative;"
                                         id="uploadDropzone"
                                         onclick="document.getElementById('leadsFileInput').click()">
                                        <input type="file" 
                                               name="leads_file" 
                                               id="leadsFileInput" 
                                               accept=".xlsx, .xls, .csv, .txt, .XLSX, .XLS, .CSV, .TXT" 
                                               required 
                                               style="display: none;"
                                               onchange="handleLeadFileSelected(this)">
                                        
                                        <div style="width: 52px; height: 52px; margin: 0 auto 0.85rem auto; background: #eff6ff; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #2563eb;">
                                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                                <polyline points="17 8 12 3 7 8"></polyline>
                                                <line x1="12" y1="3" x2="12" y2="15"></line>
                                            </svg>
                                        </div>
                                        <div style="font-size: 1.05rem; font-weight: 700; color: #0f172a; margin-bottom: 0.35rem;" id="dropzoneMainText">
                                            Click to select Excel/CSV file or drag &amp; drop here
                                        </div>
                                        <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 1.25rem;" id="dropzoneSubText">
                                            Supports .xlsx, .xls, .csv &bull; Max file size 20MB
                                        </div>
                                        <button type="button" class="btn-action-sm btn-verify" style="padding: 0.55rem 1.5rem; font-size: 0.84rem; pointer-events: none;">
                                            Browse Computer
                                        </button>
                                    </div>

                                    <!-- Selected File Preview Badge -->
                                    <div id="fileSelectedBox" style="display: none; background: #ffffff; border: 1.5px solid #2563eb; border-radius: 10px; padding: 0.85rem 1.15rem; margin-top: 1rem; box-shadow: 0 4px 12px rgba(37,99,235,0.08);">
                                        <div style="display: flex; align-items: center; justify-content: space-between;">
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <span style="font-size: 1.6rem;">📄</span>
                                                <div style="text-align: left;">
                                                    <div style="font-weight: 800; color: #0f172a; font-size: 0.9rem;" id="previewFileName">filename.xlsx</div>
                                                    <div style="font-size: 0.74rem; color: #64748b;" id="previewFileSize">128 KB &bull; Ready to upload</div>
                                                </div>
                                            </div>
                                            <span style="font-size: 0.74rem; font-weight: 700; background: #ecfdf5; color: #059669; padding: 4px 10px; border-radius: 9999px; border: 1px solid #a7f3d0;">
                                                ✓ File Selected
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Upload Progress Status -->
                                    <div id="uploadProgressStatus" style="display: none; margin-top: 1rem; padding: 0.85rem 1rem; background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; color: #1e40af; font-size: 0.84rem; font-weight: 600; text-align: center;">
                                        ⏳ Uploading file and ingesting mobile numbers into <code>crm.leads</code>... Please wait.
                                    </div>

                                    <div style="margin-top: 1.5rem; display: flex; justify-content: flex-end; gap: 0.75rem; align-items: center;">
                                        <button type="button" onclick="closeLeadUploadModal()" class="tele-btn-action" style="background: #f8fafc; border: 1px solid var(--card-border); color: #475569; padding: 0.6rem 1.25rem; font-size: 0.84rem; border-radius: 8px; font-weight: 600;">
                                            Cancel
                                        </button>
                                        <button type="submit" id="btnSubmitUpload" class="incharge-btn-submit" style="min-width: 220px; height: 42px; font-size: 0.88rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);" disabled>
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                                <path d="M12 12v9"></path>
                                                <path d="m16 16-4-4-4 4"></path>
                                            </svg>
                                            <span>Upload &amp; Ingest Leads</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- RECENTLY UPLOADED FILES HISTORY TABLE -->
                <div class="portal-card-shell" style="margin-top: 1.5rem;">
                    <div class="portal-card-topbar" style="padding: 0.85rem 1.25rem;">
                        <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                            <span>📁</span> Recently Uploaded Files
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="crm-table" id="recentUploadedFilesTable">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>File Size</th>
                                    <th>Total Records</th>
                                    <th>New Leads Added</th>
                                    <th>Duplicates Skipped</th>
                                    <th>Upload Status</th>
                                    <th>Uploaded By</th>
                                    <th>Upload Date &amp; Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentUploadedFiles as $file)
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 8px;">
                                                <span style="font-size: 1.25rem;">📄</span>
                                                <strong style="font-family: 'JetBrains Mono', monospace; font-size: 0.86rem; color: #0f172a;">
                                                    {{ $file['filename'] ?? 'uploaded_leads.xlsx' }}
                                                </strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">
                                                {{ $file['filesize'] ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong style="font-size: 0.84rem; color: #0f172a;">
                                                {{ number_format($file['total_records'] ?? 0) }} Leads
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="tracking-pill pill-done" style="font-weight: 700;">
                                                ✓ {{ number_format($file['inserted_count'] ?? 0) }} New
                                            </span>
                                        </td>
                                        <td>
                                            @if(($file['duplicates_count'] ?? 0) > 0)
                                                <span class="tracking-pill pill-pending" style="font-weight: 700;">
                                                    ⚠️ {{ number_format($file['duplicates_count']) }} Skipped
                                                </span>
                                            @else
                                                <span class="tracking-pill pill-awaiting">0 Skipped</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="tracking-pill pill-done" style="font-weight: 700;">
                                                ✓ Completed
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.82rem; font-weight: 600; color: #0f172a;">
                                                {{ $file['uploaded_by'] ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span style="font-size: 0.78rem; color: #64748b;">
                                                {{ $file['uploaded_at'] ?? 'Just now' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="no-files-row">
                                        <td colspan="8" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                            No files uploaded yet. Select an Excel or CSV file above to ingest leads.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <script>
                    function escapeHtmlLead(text) {
                        if (!text) return '';
                        const div = document.createElement('div');
                        div.textContent = text;
                        return div.innerHTML;
                    }

                    window.openLeadUploadModal = function() {
                        const modal = document.getElementById('leadUploadModal');
                        if (modal) {
                            modal.classList.add('active');
                            document.body.style.overflow = 'hidden';
                        }
                    };

                    window.closeLeadUploadModal = function() {
                        const modal = document.getElementById('leadUploadModal');
                        if (modal) {
                            modal.classList.remove('active');
                            document.body.style.overflow = '';
                        }
                    };

                    document.addEventListener('keydown', function(e) {
                        if (e.key === 'Escape') {
                            const modal = document.getElementById('leadUploadModal');
                            if (modal && modal.classList.contains('active')) {
                                window.closeLeadUploadModal();
                            }
                        }
                    });

                    function handleLeadFileSelected(input) {
                        const file = input.files ? input.files[0] : null;
                        const btn = document.getElementById('btnSubmitUpload');
                        const mainTxt = document.getElementById('dropzoneMainText');
                        const subTxt = document.getElementById('dropzoneSubText');
                        const filePreview = document.getElementById('fileSelectedBox');
                        const previewName = document.getElementById('previewFileName');
                        const previewSize = document.getElementById('previewFileSize');

                        if (file) {
                            mainTxt.innerHTML = '📄 Selected: <strong style="color: #2563eb;">' + escapeHtmlLead(file.name) + '</strong>';
                            subTxt.textContent = 'Click to change file';
                            if (previewName) previewName.textContent = file.name;
                            if (previewSize) previewSize.textContent = (file.size / 1024).toFixed(1) + ' KB • Ready for upload';
                            if (filePreview) filePreview.style.display = 'block';
                            if (btn) {
                                btn.disabled = false;
                                btn.style.pointerEvents = 'auto';
                                btn.style.opacity = '1';
                                btn.innerHTML = `
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                        <path d="M12 12v9"></path>
                                        <path d="m16 16-4-4-4 4"></path>
                                    </svg>
                                    <span>Upload &amp; Ingest Leads (${escapeHtmlLead(file.name)})</span>
                                `;
                            }
                        } else {
                            mainTxt.textContent = 'Click to select Excel/CSV file or drag & drop here';
                            subTxt.textContent = 'Supports .xlsx, .xls, .csv • Max file size 20MB';
                            if (filePreview) filePreview.style.display = 'none';
                            if (btn) {
                                btn.disabled = true;
                                btn.innerHTML = `
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                        <path d="M12 12v9"></path>
                                        <path d="m16 16-4-4-4 4"></path>
                                    </svg>
                                    <span>Upload &amp; Ingest Leads</span>
                                `;
                            }
                        }
                    }

                    // Form submit handler with AJAX and instant feedback
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('leadExcelUploadForm');
                        if (form) {
                            form.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const btn = document.getElementById('btnSubmitUpload');
                                const input = document.getElementById('leadsFileInput');
                                const notifyArea = document.getElementById('uploadNotificationArea');
                                const progressDiv = document.getElementById('uploadProgressStatus');

                                if (!input || !input.files || input.files.length === 0) {
                                    alert('Please select an Excel or CSV file first.');
                                    return;
                                }

                                const file = input.files[0];

                                if (btn) {
                                    btn.style.pointerEvents = 'none';
                                    btn.style.opacity = '0.85';
                                    btn.innerHTML = '<span>⏳</span> Uploading and validating leads... Please wait';
                                }
                                if (progressDiv) {
                                    progressDiv.style.display = 'block';
                                }

                                const formData = new FormData(form);
                                const tokenInput = form.querySelector('input[name="_token"]');
                                const csrfToken = tokenInput ? tokenInput.value : '';

                                fetch(form.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': csrfToken
                                    }
                                })
                                .then(response => {
                                    return response.json().then(data => ({
                                        status: response.status,
                                        ok: response.ok,
                                        data: data
                                    }));
                                })
                                .then(res => {
                                    if (progressDiv) progressDiv.style.display = 'none';
                                    if (btn) {
                                        btn.style.pointerEvents = 'auto';
                                        btn.style.opacity = '1';
                                    }

                                    if (res.ok && res.data && res.data.success) {
                                        const d = res.data;
                                        // Show green success alert box
                                        if (notifyArea) {
                                            notifyArea.innerHTML = `
                                                <div class="upload-alert-box" style="background: #f0fdf4; border: 2px solid #22c55e; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 16px rgba(34,197,94,0.12); animation: fadeIn 0.3s ease;">
                                                    <div style="display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                                                        <div style="display: flex; align-items: flex-start; gap: 14px;">
                                                            <div style="width: 44px; height: 44px; border-radius: 50%; background: #dcfce7; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: #16a34a; flex-shrink: 0; font-weight: 800;">
                                                                ✓
                                                            </div>
                                                            <div>
                                                                <div style="font-size: 1.15rem; font-weight: 800; color: #15803d;">
                                                                    File Uploaded Successfully!
                                                                </div>
                                                                <div style="font-size: 0.9rem; font-weight: 600; color: #166534; margin-top: 4px;">
                                                                    ${escapeHtmlLead(d.message)}
                                                                </div>
                                                                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 10px; padding-top: 10px; border-top: 1px dashed #bbf7d0; font-size: 0.82rem;">
                                                                    <span style="background: #ffffff; border: 1px solid #bbf7d0; border-radius: 6px; padding: 4px 10px; font-weight: 700; color: #0f172a;">
                                                                        📊 Total Records: <span style="color: #2563eb;">${d.summary.total_rows}</span>
                                                                    </span>
                                                                    <span style="background: #ffffff; border: 1px solid #86efac; border-radius: 6px; padding: 4px 10px; font-weight: 700; color: #15803d;">
                                                                        🟢 New Leads Added: <span style="color: #16a34a;">${d.summary.inserted_count}</span>
                                                                    </span>
                                                                    ${d.summary.total_duplicates > 0 ? `
                                                                        <span style="background: #ffffff; border: 1px solid #fde047; border-radius: 6px; padding: 4px 10px; font-weight: 700; color: #854d0e;">
                                                                            ⚠️ Duplicates Skipped: <span style="color: #d97706;">${d.summary.total_duplicates}</span>
                                                                        </span>
                                                                    ` : ''}
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <a href="{{ route('leads.allocation', ['tab' => 'allocation']) }}" style="padding: 0.65rem 1.25rem; background: #16a34a; color: #ffffff; border-radius: 8px; font-size: 0.84rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; box-shadow: 0 2px 8px rgba(22,163,74,0.3);">
                                                            <span>Go to Leads Allocation &rarr;</span>
                                                        </a>
                                                    </div>
                                                </div>
                                            `;
                                            notifyArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                        }

                                        // Append to Recently Uploaded Files table
                                        if (d.file_record) {
                                            const tableBody = document.querySelector('#recentUploadedFilesTable tbody');
                                            if (tableBody) {
                                                const emptyRow = tableBody.querySelector('.no-files-row');
                                                if (emptyRow) emptyRow.remove();

                                                const tr = document.createElement('tr');
                                                tr.innerHTML = `
                                                    <td>
                                                        <div style="display: flex; align-items: center; gap: 8px;">
                                                            <span style="font-size: 1.25rem;">📄</span>
                                                            <strong style="font-family: 'JetBrains Mono', monospace; font-size: 0.86rem; color: #0f172a;">
                                                                ${escapeHtmlLead(d.file_record.filename)}
                                                            </strong>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <span style="font-size: 0.8rem; color: #64748b; font-weight: 600;">
                                                            ${escapeHtmlLead(d.file_record.filesize)}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong style="font-size: 0.84rem; color: #0f172a;">
                                                            ${d.file_record.total_records} Leads
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <span class="tracking-pill pill-done" style="font-weight: 700;">
                                                            ✓ ${d.file_record.inserted_count} New
                                                        </span>
                                                    </td>
                                                    <td>
                                                        ${d.file_record.duplicates_count > 0 ? `
                                                            <span class="tracking-pill pill-pending" style="font-weight: 700;">
                                                                ⚠️ ${d.file_record.duplicates_count} Skipped
                                                            </span>
                                                        ` : `
                                                            <span class="tracking-pill pill-awaiting">0 Skipped</span>
                                                        `}
                                                    </td>
                                                    <td>
                                                        <span class="tracking-pill pill-done" style="font-weight: 700;">
                                                            ✓ Completed
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span style="font-size: 0.82rem; font-weight: 600; color: #0f172a;">
                                                            ${escapeHtmlLead(d.file_record.uploaded_by)}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span style="font-size: 0.78rem; color: #64748b;">
                                                            ${escapeHtmlLead(d.file_record.uploaded_at)}
                                                        </span>
                                                    </td>
                                                `;
                                                tableBody.insertBefore(tr, tableBody.firstChild);
                                            }
                                        }

                                        // Update live metric counters in header
                                        const leadsBadge = document.getElementById('headerTotalLeadsBadge');
                                        const bulkBadge = document.getElementById('headerBulkLeadsBadge');
                                        if (d.summary) {
                                            const addedLeads = parseInt(d.summary.total_rows || d.summary.inserted_count || 0, 10) || 0;
                                            if (leadsBadge) {
                                                const currentLeads = parseInt((leadsBadge.textContent || '0').replace(/,/g, ''), 10) || 0;
                                                leadsBadge.textContent = (currentLeads + addedLeads).toLocaleString();
                                            }
                                            if (bulkBadge) {
                                                const currentBulk = parseInt((bulkBadge.textContent || '0').replace(/,/g, ''), 10) || 0;
                                                bulkBadge.textContent = (currentBulk + addedLeads).toLocaleString();
                                            }
                                        }

                                        // Reset form
                                        form.reset();
                                        handleLeadFileSelected(input);
                                        if (typeof window.closeLeadUploadModal === 'function') {
                                            window.closeLeadUploadModal();
                                        }
                                    } else {
                                        const errMsg = (res.data && res.data.message) ? res.data.message : 'An error occurred during file upload.';
                                        const modalNotify = document.getElementById('modalUploadNotificationArea');
                                        if (modalNotify) {
                                            modalNotify.innerHTML = `
                                                <div class="upload-alert-box" style="background: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.25rem;">
                                                    <div style="font-size: 0.95rem; font-weight: 800; color: #991b1b;">Upload Failed</div>
                                                    <div style="font-size: 0.84rem; color: #b91c1c; font-weight: 600; margin-top: 3px;">${escapeHtmlLead(errMsg)}</div>
                                                </div>
                                            `;
                                        }
                                        if (notifyArea) {
                                            notifyArea.innerHTML = `
                                                <div class="upload-alert-box" style="background: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem; animation: fadeIn 0.3s ease;">
                                                    <div style="display: flex; align-items: center; gap: 14px;">
                                                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #dc2626; flex-shrink: 0; font-weight: 800;">
                                                            ✕
                                                        </div>
                                                        <div>
                                                            <div style="font-size: 1.05rem; font-weight: 800; color: #991b1b;">
                                                                Upload Failed / Error
                                                            </div>
                                                            <div style="font-size: 0.86rem; color: #b91c1c; font-weight: 600; margin-top: 4px;">
                                                                ${escapeHtmlLead(errMsg)}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            `;
                                            notifyArea.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                                        }
                                        if (btn) {
                                            btn.innerHTML = `
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                                    <path d="M12 12v9"></path>
                                                    <path d="m16 16-4-4-4 4"></path>
                                                </svg>
                                                <span>Try Again (${escapeHtmlLead(file.name)})</span>
                                            `;
                                        }
                                    }
                                })
                                .catch(err => {
                                    if (progressDiv) progressDiv.style.display = 'none';
                                    if (btn) {
                                        btn.style.pointerEvents = 'auto';
                                        btn.style.opacity = '1';
                                        btn.disabled = false;
                                        btn.innerHTML = `
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                                <path d="M12 12v9"></path>
                                                <path d="m16 16-4-4-4 4"></path>
                                            </svg>
                                            <span>Upload &amp; Ingest Leads</span>
                                        `;
                                    }
                                    if (notifyArea) {
                                        notifyArea.innerHTML = `
                                            <div class="upload-alert-box" style="background: #fef2f2; border: 2px solid #ef4444; border-radius: 12px; padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;">
                                                <div style="display: flex; align-items: center; gap: 14px;">
                                                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #fee2e2; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; color: #dc2626; flex-shrink: 0; font-weight: 800;">
                                                        ✕
                                                    </div>
                                                    <div>
                                                        <div style="font-size: 1.05rem; font-weight: 800; color: #991b1b;">
                                                            Network / Server Error
                                                        </div>
                                                        <div style="font-size: 0.86rem; color: #b91c1c; font-weight: 600; margin-top: 4px;">
                                                            Could not complete file upload. Please check your connection or file size.
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                    }
                                });
                            });
                        }

                        // Drag and drop support
                        const dz = document.getElementById('uploadDropzone');
                        if (dz) {
                            ['dragenter', 'dragover'].forEach(eventName => {
                                dz.addEventListener(eventName, (e) => {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    dz.style.borderColor = '#2563eb';
                                    dz.style.background = '#eff6ff';
                                }, false);
                            });
                            ['dragleave', 'drop'].forEach(eventName => {
                                dz.addEventListener(eventName, (e) => {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    dz.style.borderColor = '#93c5fd';
                                    dz.style.background = '#f8fafc';
                                }, false);
                            });
                            dz.addEventListener('drop', (e) => {
                                const dt = e.dataTransfer;
                                const files = dt.files;
                                if (files.length > 0) {
                                    const input = document.getElementById('leadsFileInput');
                                    input.files = files;
                                    handleLeadFileSelected(input);
                                }
                            }, false);
                        }
                    });
                </script>
            @else
                <!-- PORTAL TABLE CARD -->
                <div class="portal-card-shell">
                <!-- Top Toolbar -->
                <div class="portal-card-topbar" style="padding: 0.55rem 0.95rem; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.65rem; width: 100%;">
                        <div class="filter-nav-pills" id="statusFilterPills">
                            @if($activeTab === 'input')
                                <button type="button" onclick="applyStatusFilter('all', this)" class="filter-nav-pill {{ ($statusFilter ?? 'all') === 'all' ? 'is-active' : '' }}" style="background: none; cursor: pointer;">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>All Leads
                                </button>
                                <button type="button" onclick="applyStatusFilter('telecaller', this)" class="filter-nav-pill {{ ($statusFilter ?? '') === 'telecaller' ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Telecaller Stage</button>
                                <button type="button" onclick="applyStatusFilter('register', this)" class="filter-nav-pill {{ ($statusFilter ?? '') === 'register' ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Register Form</button>
                                <button type="button" onclick="applyStatusFilter('payment', this)" class="filter-nav-pill {{ in_array(($statusFilter ?? ''), ['payment', 'stage1']) ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Payment Stage</button>
                                <button type="button" onclick="applyStatusFilter('personal', this)" class="filter-nav-pill {{ in_array(($statusFilter ?? ''), ['personal', 'stage2']) ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Personal Details</button>
                                <button type="button" onclick="applyStatusFilter('academic', this)" class="filter-nav-pill {{ in_array(($statusFilter ?? ''), ['academic', 'stage3']) ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Academic Details</button>
                            @elseif($activeTab === 'allotted')
                                <div style="font-size: 0.84rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                                    <span>📋</span> My Allotted Leads
                                </div>
                            @else
                                <div style="font-size: 0.84rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 6px;">
                                    <span>📋</span> Leads Table Directory
                                </div>
                            @endif
                        </div>

                        <div style="display: flex; gap: 0.45rem; align-items: center; flex-wrap: wrap;">
                            @if($activeTab !== 'allotted')
                                <select id="allocStatusFilterSelect" onchange="applyExtraFilter('alloc_status', this.value)" style="padding: 0.38rem 0.65rem; border: 1.5px solid var(--card-border); border-radius: 0.45rem; font-size: 0.78rem; font-weight: 600; color: #334155; background: #ffffff; outline: none;">
                                    <option value="all" {{ ($allocStatusFilter ?? 'all') === 'all' ? 'selected' : '' }}>Status: All Leads</option>
                                    <option value="unallocated" {{ ($allocStatusFilter ?? '') === 'unallocated' ? 'selected' : '' }}>Unallocated (empcode = 0)</option>
                                    <option value="allocated" {{ ($allocStatusFilter ?? '') === 'allocated' ? 'selected' : '' }}>Allocated (empcode != 0)</option>
                                </select>

                                <select id="callerFilterSelect" onchange="applyExtraFilter('caller', this.value)" style="padding: 0.38rem 0.65rem; border: 1.5px solid var(--card-border); border-radius: 0.45rem; font-size: 0.78rem; font-weight: 600; color: #334155; background: #ffffff; outline: none;">
                                    <option value="all">Telecaller: All</option>
                                    @foreach($telecallers as $tc)
                                        <option value="{{ $tc->emp_code }}" {{ ($callerFilter ?? '') === $tc->emp_code ? 'selected' : '' }}>{{ $tc->full_name }} ({{ $tc->emp_code }})</option>
                                    @endforeach
                                </select>
                            @endif

                            @if($activeTab === 'input')
                                <select id="programFilterSelect" onchange="applyExtraFilter('program', this.value)" style="padding: 0.38rem 0.65rem; border: 1.5px solid var(--card-border); border-radius: 0.45rem; font-size: 0.78rem; font-weight: 600; color: #334155; background: #ffffff; outline: none;">
                                    <option value="all">Program: All</option>
                                    @foreach($availablePrograms as $prog)
                                        <option value="{{ $prog->program_id }}" {{ (string)($programFilter ?? '') === (string)$prog->program_id ? 'selected' : '' }}>{{ $prog->program_short_name }}</option>
                                    @endforeach
                                </select>
                            @endif

                            <!-- Search Input -->
                            <div style="position: relative; display: flex; align-items: center;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 10px;">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                                <input type="text" id="liveSearchInput" value="{{ $searchQuery ?? '' }}" oninput="handleLiveSearch(this.value)" placeholder="{{ $activeTab === 'allocation' ? 'Search phone, empcode...' : 'Search name, phone, app #, CRM ID...' }}" style="padding: 0.4rem 2rem 0.4rem 2rem; border: 1.5px solid var(--card-border); border-radius: 0.45rem; font-size: 0.78rem; width: 190px; outline: none; transition: border-color 0.15s ease;">
                                <div class="live-search-spinner" id="searchSpinner"></div>
                            </div>

                            <!-- Filter Reset Button (Right of Search) -->
                            <button type="button" 
                                    id="btnResetFilters" 
                                    onclick="resetAllFilters()" 
                                    title="Reset all search & filters to default" 
                                    style="padding: 0.4rem 0.65rem; border: 1.5px solid var(--card-border); border-radius: 0.45rem; font-size: 0.78rem; font-weight: 600; color: #475569; background: #ffffff; cursor: pointer; display: inline-flex; align-items: center; gap: 4px; outline: none; transition: all 0.15s ease;"
                                    onmouseover="this.style.background='#f1f5f9'; this.style.color='#0f172a';"
                                    onmouseout="this.style.background='#ffffff'; this.style.color='#475569';">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/>
                                    <path d="M3 3v5h5"/>
                                </svg>
                                <span>Reset</span>
                            </button>

                            <!-- Table Size (Limit Enforced: Max Leads Table Size) (Right of Search) -->
                            <div style="display: inline-flex; align-items: center; gap: 4px; background: #ffffff; border: 1.5px solid var(--card-border); border-radius: 0.45rem; padding: 0.1rem 0.45rem; height: 32px;" title="Rows per page (Limit up to leads table size: {{ $maxPageLimit ?? 100 }})">
                                <label for="tablePageSizeInput" style="font-size: 0.74rem; font-weight: 700; color: #64748b; margin: 0; white-space: nowrap;">Size:</label>
                                <input type="number" 
                                       id="tablePageSizeInput" 
                                       min="1" 
                                       max="{{ $maxPageLimit ?? 100 }}" 
                                       value="{{ $perPage ?? request('per_page', 25) }}" 
                                       oninput="enforcePageSizeLimit(this)" 
                                       onchange="handlePageSizeChange(this.value)" 
                                       onkeydown="if(event.key === 'Enter'){ event.preventDefault(); handlePageSizeChange(this.value); }" 
                                       placeholder="25" 
                                       title="Enter number of rows per page (Max limit: {{ $maxPageLimit ?? 100 }})" 
                                       style="width: 65px; border: none; font-size: 0.78rem; font-weight: 700; color: #0f172a; text-align: center; outline: none; background: transparent; padding: 0;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clean Table Shell Container (Updated dynamically via Live Search) -->
                <div id="leadsTableContainer">
                    @include('leads::partials.leads_table', [
                        'leads' => $leads,
                        'activeTab' => $activeTab,
                        'stageEligibilityMap' => $stageEligibilityMap ?? [],
                        'latestStageTasksByPhone' => $latestStageTasksByPhone ?? collect(),
                        'latestStageTasksByAppli' => $latestStageTasksByAppli ?? collect(),
                    ])
                </div>
            </div>
            @endif
        </main>
    </div>
</div>

<!-- ================= POPUP ACTION MODAL ================= -->
<div class="wizard-overlay" id="wizardModal" onclick="closeWizardModalOnBackdrop(event)">
    <div class="wizard-modal-box" style="max-width: 580px;">
        <!-- Header -->
        <div class="wizard-modal-header">
            <div>
                <div class="candidate-header-info">
                    <span class="candidate-header-name" id="modalCandidateName">Candidate Assessment</span>
                    <span class="candidate-header-badge" id="modalCandidateAppNo">APP#</span>
                </div>
                <div class="candidate-header-sub" id="modalCandidateSub">Program: B.Tech | +91 9912996788</div>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeWizardModal()" title="Close Dialog">✕</button>
        </div>

        <!-- Modal Body Content -->
        <div class="modal-body-content" style="padding: 1.25rem 1.5rem;">
            <!-- Stage Call Task Details (Logged to crm.telecaller_leads_tasks) -->
            <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 0.85rem;">
                <div>
                    <label for="modalStageNameSelect" style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">
                        Stage Name <span style="color: #ef4444;">*</span>
                    </label>
                    <select id="modalStageNameSelect" class="incharge-select incharge-input-no-icon" style="height: 38px; font-size: 0.8rem;">
                        <option value="Telecaller Stage">Telecaller Stage</option>
                        <option value="Register Form completed">Register Form completed</option>
                        <option value="Payment Stage completed">Payment Stage completed</option>
                        <option value="Personal Details completed">Personal Details completed</option>
                        <option value="Academic Details completed">Academic Details completed</option>
                    </select>
                </div>

                <div>
                    <label for="modalStageCallPerson" style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">
                        Stage Call Person
                    </label>
                    <select id="modalStageCallPerson" class="incharge-select incharge-input-no-icon" style="height: 38px; font-size: 0.8rem;">
                        <option value="Student / Candidate">Student / Candidate</option>
                        <option value="Father">Father</option>
                        <option value="Mother">Mother</option>
                        <option value="Guardian">Guardian</option>
                        <option value="Brother / Sister">Brother / Sister</option>
                        <option value="Other Family Member">Other Family Member</option>
                    </select>
                </div>

                <div>
                    <label for="modalStageCallTime" style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">
                        Stage Call Date &amp; Time
                    </label>
                    <input type="datetime-local" id="modalStageCallTime" class="incharge-input incharge-input-no-icon" style="height: 38px; font-size: 0.8rem;">
                </div>

                <div>
                    <label for="modalCallDuration" style="display: block; font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem;">
                        Call Duration
                    </label>
                    <div class="duration-input-wrap" id="durationInputWrap">
                        <input type="text" id="modalCallDuration" class="incharge-input incharge-input-no-icon" style="height: 38px; font-size: 0.8rem; padding-right: 2.1rem; cursor: pointer;" placeholder="e.g. 5 mins" value="5 mins" onfocus="openDurationRoller()" onclick="openDurationRoller()">
                        <button type="button" class="duration-roller-trigger" onclick="toggleDurationRoller(event)" title="Select call duration">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Remarks -->
            <div class="remarks-field-box" style="margin-top: 1rem;">
                <label for="modalStageRemarks" style="font-size: 0.78rem; font-weight: 700; color: #475569; display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.35rem;">
                    <span>Telecaller Calling Remarks</span>
                    <span style="font-size: 0.68rem; color: #64748b; font-weight: 500; background: #e2e8f0; padding: 0.1rem 0.45rem; border-radius: 4px;">Saved in DB</span>
                </label>
                <textarea id="modalStageRemarks" class="remarks-textarea" rows="3" placeholder="Enter counseling notes, discussion summary, or callback details..."></textarea>
            </div>
        </div>

        <!-- Footer -->
        <div class="wizard-modal-footer">
            <div></div>
            <div style="display: flex; gap: 0.6rem; align-items: center;">
                <button type="button" class="tele-btn-action" style="background: #f8fafc; border: 1px solid var(--card-border); color: #475569; padding: 0.5rem 1rem;" onclick="closeWizardModal()">
                    Cancel
                </button>
                <button type="button" class="tele-btn-action tele-btn-wizard" onclick="saveActiveStageUpdate()" style="padding: 0.5rem 1.4rem;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                        <polyline points="17 21 17 13 7 13 7 21"></polyline>
                        <polyline points="7 3 7 8 15 8"></polyline>
                    </svg>
                    <span>Save Stage Status</span>
                </button>
            </div>
        </div>

        <!-- Student Previous Task History (shown when records exist in crm.telecaller_leads_tasks) -->
        <div id="modalStudentHistorySection" style="display: none; border-top: 1px solid var(--card-border); background: #f8fafc; padding: 0.9rem 1.75rem 1.15rem; max-height: 210px; overflow-y: auto;">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.55rem;">
                <span style="font-size: 0.74rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: #334155;">
                    Previous Telecaller History
                </span>
                <span id="modalHistoryCount" style="font-size: 0.7rem; font-weight: 700; color: #475569; background: #e2e8f0; padding: 1px 7px; border-radius: 999px;">0</span>
            </div>
            <div id="modalStudentHistoryList" style="display: flex; flex-direction: column; gap: 0.45rem;"></div>
        </div>
    </div>
</div>

<!-- ================= ADD LEAD INPUT MODAL ================= -->
<div class="wizard-overlay" id="addLeadModal" onclick="if(event.target.id==='addLeadModal') closeAddLeadModal()">
    <div class="wizard-modal-box" style="max-width: 520px;">
        <div class="wizard-modal-header">
            <div>
                <div class="candidate-header-name">Add New Candidate Lead</div>
                <div class="candidate-header-sub">Creates candidate in crm.student_registration with auto CRM ID</div>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeAddLeadModal()" title="Close">✕</button>
        </div>
        <form onsubmit="event.preventDefault(); submitAddLeadModal();" style="padding: 1.5rem 1.75rem;">
            <div style="display: grid; gap: 0.9rem;">
                <div>
                    <label class="incharge-label">Candidate Name <span class="required-dot">*</span></label>
                    <input type="text" id="newLeadName" class="incharge-input incharge-input-no-icon" required placeholder="Full Name">
                </div>
                <div>
                    <label class="incharge-label">Mobile Number (10 digits) <span class="required-dot">*</span></label>
                    <input type="text" id="newLeadPhone" class="incharge-input incharge-input-no-icon" required pattern="[0-9]{10}" maxlength="10" placeholder="e.g. 9876543210">
                </div>
                <div>
                    <label class="incharge-label">Email Address</label>
                    <input type="email" id="newLeadEmail" class="incharge-input incharge-input-no-icon" placeholder="student@example.com">
                </div>
                <div>
                    <label class="incharge-label">Program <span class="required-dot">*</span></label>
                    <select id="newLeadProgram" class="incharge-select incharge-input-no-icon" required>
                        @foreach($availablePrograms as $prog)
                            <option value="{{ $prog->program_id }}">{{ $prog->program_short_name }} — {{ $prog->program_long_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="incharge-label">Assign Telecaller (Optional)</label>
                    <select id="newLeadCaller" class="incharge-select incharge-input-no-icon">
                        <option value="">-- Leave Unallocated (Goes to Leads Allocation) --</option>
                        @foreach($telecallers as $tc)
                            <option value="{{ $tc->emp_code }}">{{ $tc->full_name }} ({{ $tc->emp_code }})</option>
                        @endforeach
                    </select>
                </div>
                <div id="addLeadNotice" style="display: none; font-size: 0.8rem; font-weight: 600;"></div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.65rem; margin-top: 1.25rem;">
                <button type="button" onclick="closeAddLeadModal()" class="tele-btn-action" style="background: #f8fafc; border: 1px solid var(--card-border); color: #475569; padding: 0.5rem 1rem;">Cancel</button>
                <button type="submit" id="btnSubmitNewLead" class="incharge-btn-submit" style="height: 36px; padding: 0 1.25rem;">Save Lead</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<!-- Call Duration 3-Column Roller Popover -->
<div id="durationRollerPopover" class="duration-roller-popover" role="dialog" aria-label="Call Duration Picker">
    <div class="duration-roller-headers">
        <div class="duration-roller-hdr">Hrs</div>
        <div class="duration-roller-hdr">Min</div>
        <div class="duration-roller-hdr">Sec</div>
    </div>
    <div class="duration-roller-cols">
        <div class="duration-roller-col" id="rollerColHrs"></div>
        <div class="duration-roller-col" id="rollerColMins"></div>
        <div class="duration-roller-col" id="rollerColSecs"></div>
    </div>
    <div class="duration-roller-footer">
        <span id="durationRollerPreview" style="font-size: 0.72rem; font-weight: 600; color: #475569;">00:05:00</span>
        <button type="button" class="duration-roller-done" onclick="closeDurationRoller()">Done</button>
    </div>
</div>

<!-- Clean Floating Toast Notification -->
<div id="teleToast">
    <span id="toastMsg">Stage updated successfully!</span>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let currentCandidate = null;

    function showToast(msg, isError = false) {
        const toast = document.getElementById('teleToast');
        const toastMsg = document.getElementById('toastMsg');
        toastMsg.textContent = msg;
        toast.style.borderLeftColor = isError ? '#ef4444' : '#10b981';
        toast.style.display = 'flex';
        setTimeout(() => { toast.style.display = 'none'; }, 2800);
    }

    function openWizardModal(applinano, name, phone, program, stages = {}, remarks = '', currentStage = '', allowedStages = null) {
        currentCandidate = { applinano, name, phone, program, stages, remarks: remarks || '', currentStage: currentStage || '' };

        document.getElementById('modalCandidateName').textContent = name || 'Candidate Lead';
        document.getElementById('modalCandidateAppNo').textContent = (applinano && applinano !== '0') ? `APP: ${applinano}` : `PHONE: ${phone}`;
        document.getElementById('modalCandidateSub').textContent = `Program: ${program || 'UG'} | Contact: +91 ${phone}`;

        const callTimeInput = document.getElementById('modalStageCallTime');
        if (callTimeInput) {
            const now = new Date();
            const offsetMs = now.getTimezoneOffset() * 60000;
            callTimeInput.value = new Date(now.getTime() - offsetMs).toISOString().slice(0, 16);
        }

        const durationInput = document.getElementById('modalCallDuration');
        if (durationInput && !durationInput.value) durationInput.value = '5 mins';

        const remarksInput = document.getElementById('modalStageRemarks');
        if (remarksInput) {
            remarksInput.value = remarks || '';
        }

        const stageSelect = document.getElementById('modalStageNameSelect');
        if (stageSelect) {
            const permissions = (allowedStages && typeof allowedStages === 'object') ? allowedStages : {};
            for (let i = 0; i < stageSelect.options.length; i++) {
                const opt = stageSelect.options[i];
                if (opt.value === 'Telecaller Stage') {
                    opt.disabled = false;
                } else {
                    opt.disabled = !(permissions[opt.value] === true);
                }
            }
            stageSelect.value = 'Telecaller Stage';
        }

        document.getElementById('wizardModal').classList.add('active');
        document.body.style.overflow = 'hidden';
        loadModalStudentHistory(applinano, phone);
    }

    function escapeHistoryHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function loadModalStudentHistory(applinano, phone) {
        const section = document.getElementById('modalStudentHistorySection');
        const list = document.getElementById('modalStudentHistoryList');
        const countEl = document.getElementById('modalHistoryCount');
        if (!section || !list) return;

        section.style.display = 'none';
        list.innerHTML = '';
        if (countEl) countEl.textContent = '0';

        const targetAppli = (applinano && applinano !== '') ? applinano : '0';
        const url = `/leads/${encodeURIComponent(targetAppli)}/history?phone=${encodeURIComponent(phone || '')}`;

        fetch(url, { headers: { 'Accept': 'application/json' } })
            .then(res => res.ok ? res.json() : null)
            .then(data => {
                if (!data || !data.success || !Array.isArray(data.history) || data.history.length === 0) {
                    section.style.display = 'none';
                    return;
                }

                if (countEl) countEl.textContent = String(data.history.length);
                list.innerHTML = data.history.map(item => `
                    <div style="background: #ffffff; border: 1px solid #e2e8f0; border-radius: 6px; padding: 0.55rem 0.75rem; font-size: 0.76rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-weight: 700; color: #0f172a;">${escapeHistoryHtml(item.stage_name)}</span>
                            <span style="font-size: 0.7rem; color: #64748b; font-family: 'JetBrains Mono', monospace;">
                                ${escapeHistoryHtml(item.stage_calltime)} &bull; ${escapeHistoryHtml(item.stage_call_duration)}
                            </span>
                        </div>
                        <div style="margin-top: 0.2rem; color: #475569; font-size: 0.73rem;">
                            <strong>Person:</strong> ${escapeHistoryHtml(item.stage_callperson)}${item.stage_remark ? ` &nbsp;|&nbsp; <strong>Remarks:</strong> ${escapeHistoryHtml(item.stage_remark)}` : ''}
                        </div>
                    </div>
                `).join('');
                section.style.display = 'block';
            })
            .catch(() => {
                section.style.display = 'none';
            });
    }

    const durationRollerState = { hrs: 0, mins: 5, secs: 0, initialized: false };

    function padTwo(val) {
        return String(val).padStart(2, '0');
    }

    function formatDurationText(hrs, mins, secs) {
        const parts = [];
        if (hrs > 0) parts.push(`${hrs} ${hrs === 1 ? 'hr' : 'hrs'}`);
        if (mins > 0 || (hrs === 0 && secs === 0)) parts.push(`${mins} ${mins === 1 ? 'min' : 'mins'}`);
        if (secs > 0) parts.push(`${secs} ${secs === 1 ? 'sec' : 'secs'}`);
        return parts.join(' ');
    }

    function parseDurationText(raw) {
        const str = String(raw || '').trim().toLowerCase();
        if (/^\d{1,2}:\d{2}(:\d{2})?$/.test(str)) {
            const segs = str.split(':').map(n => parseInt(n, 10) || 0);
            if (segs.length === 3) return { hrs: Math.min(23, segs[0]), mins: Math.min(59, segs[1]), secs: Math.min(59, segs[2]) };
            return { hrs: 0, mins: Math.min(59, segs[0]), secs: Math.min(59, segs[1]) };
        }
        const hMatch = str.match(/(\d+)\s*h/);
        const mMatch = str.match(/(\d+)\s*m/);
        const sMatch = str.match(/(\d+)\s*s/);
        const hrs = hMatch ? Math.min(23, parseInt(hMatch[1], 10)) : 0;
        const mins = mMatch ? Math.min(59, parseInt(mMatch[1], 10)) : 0;
        const secs = sMatch ? Math.min(59, parseInt(sMatch[1], 10)) : 0;
        if (!hMatch && !mMatch && !sMatch) return { hrs: 0, mins: 5, secs: 0 };
        return { hrs, mins, secs };
    }

    function buildRollerColumn(colId, maxVal, unitKey) {
        const col = document.getElementById(colId);
        if (!col) return;
        col.innerHTML = '';
        for (let i = 0; i <= maxVal; i++) {
            const item = document.createElement('div');
            item.className = 'duration-roller-item';
            item.dataset.val = String(i);
            item.textContent = padTwo(i);
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                durationRollerState[unitKey] = i;
                syncDurationRollerUI(true);
            });
            col.appendChild(item);
        }
    }

    function initDurationRoller() {
        if (durationRollerState.initialized) return;
        buildRollerColumn('rollerColHrs', 23, 'hrs');
        buildRollerColumn('rollerColMins', 59, 'mins');
        buildRollerColumn('rollerColSecs', 59, 'secs');
        durationRollerState.initialized = true;

        document.addEventListener('click', (e) => {
            const pop = document.getElementById('durationRollerPopover');
            const wrap = document.getElementById('durationInputWrap');
            if (!pop || !pop.classList.contains('open')) return;
            if (!pop.contains(e.target) && (!wrap || !wrap.contains(e.target))) {
                closeDurationRoller();
            }
        });
    }

    function syncDurationRollerUI(updateInput = false) {
        const { hrs, mins, secs } = durationRollerState;
        const map = [
            { colId: 'rollerColHrs', val: hrs },
            { colId: 'rollerColMins', val: mins },
            { colId: 'rollerColSecs', val: secs },
        ];
        map.forEach(({ colId, val }) => {
            const col = document.getElementById(colId);
            if (!col) return;
            const items = col.querySelectorAll('.duration-roller-item');
            items.forEach((el) => {
                const isSel = parseInt(el.dataset.val, 10) === val;
                el.classList.toggle('is-selected', isSel);
                if (isSel) {
                    const targetTop = Math.max(0, el.offsetTop - 32);
                    col.scrollTo({ top: targetTop, behavior: 'smooth' });
                }
            });
        });

        const preview = document.getElementById('durationRollerPreview');
        if (preview) preview.textContent = `${padTwo(hrs)}:${padTwo(mins)}:${padTwo(secs)}`;

        if (updateInput) {
            const input = document.getElementById('modalCallDuration');
            if (input) input.value = formatDurationText(hrs, mins, secs);
        }
    }

    function positionDurationRoller() {
        const pop = document.getElementById('durationRollerPopover');
        const input = document.getElementById('modalCallDuration');
        if (!pop || !input) return;
        const rect = input.getBoundingClientRect();
        const popHeight = 254;
        const spaceBelow = window.innerHeight - rect.bottom;
        const topPos = spaceBelow >= popHeight + 12 ? rect.bottom + 6 : Math.max(8, rect.top - popHeight - 6);
        const leftPos = Math.min(window.innerWidth - 196, Math.max(8, rect.right - 184));
        pop.style.top = `${topPos}px`;
        pop.style.left = `${leftPos}px`;
    }

    function openDurationRoller() {
        initDurationRoller();
        const input = document.getElementById('modalCallDuration');
        const parsed = parseDurationText(input ? input.value : '5 mins');
        durationRollerState.hrs = parsed.hrs;
        durationRollerState.mins = parsed.mins;
        durationRollerState.secs = parsed.secs;
        const pop = document.getElementById('durationRollerPopover');
        if (pop) {
            if (pop.parentElement !== document.body) {
                document.body.appendChild(pop);
            }
            positionDurationRoller();
            pop.classList.add('open');
            syncDurationRollerUI(false);
        }
    }

    function closeDurationRoller() {
        const pop = document.getElementById('durationRollerPopover');
        if (pop) pop.classList.remove('open');
    }

    function toggleDurationRoller(e) {
        if (e) e.stopPropagation();
        const pop = document.getElementById('durationRollerPopover');
        if (pop && pop.classList.contains('open')) {
            closeDurationRoller();
        } else {
            openDurationRoller();
        }
    }

    function closeWizardModal() {
        closeDurationRoller();
        document.getElementById('wizardModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function closeWizardModalOnBackdrop(e) {
        if (e.target.id === 'wizardModal') {
            closeWizardModal();
        }
    }

    function saveActiveStageUpdate() {
        if (!currentCandidate) return;
        const stageNameVal = document.getElementById('modalStageNameSelect')?.value || 'Telecaller Stage';
        const callPersonVal = document.getElementById('modalStageCallPerson')?.value || 'Student / Candidate';
        const callTimeVal = document.getElementById('modalStageCallTime')?.value || '';
        const callDurationVal = document.getElementById('modalCallDuration')?.value || '';
        const remarksVal = document.getElementById('modalStageRemarks')?.value || '';

        fetch(`/leads/${currentCandidate.applinano}/update-stage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                stage_name: stageNameVal,
                stage_callperson: callPersonVal,
                stage_calltime: callTimeVal,
                call_duration: callDurationVal,
                remarks: remarksVal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(`✓ ${stageNameVal} saved successfully!`);
                closeWizardModal();
                
                // Immediately refresh table in the background live without full page reload
                fetchLeadsTable(1, false);
            } else {
                showToast(data.message || 'Update failed', true);
            }
        })
        .catch(err => {
            showToast('Network error updating stage.', true);
        });
    }

    function updateLeadDisposition(applinano, newStatus) {
        fetch(`/leads/${applinano}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(`Lead status updated: ${newStatus}`);
                // Refresh table live
                fetchLeadsTable(1, false);
            } else {
                showToast(data.message || 'Status update failed', true);
            }
        })
        .catch(err => {
            showToast('Network error updating status.', true);
        });
    }

    // ================= LIVE SEARCH & FILTERING WITH DYNAMIC PAGINATION =================
    let searchDebounceTimer = null;
    const activeTabName = '{{ $activeTab ?? "input" }}';
    let activeFilterStatus = '{{ $statusFilter ?? "all" }}';
    let activeAllocStatus = '{{ $allocStatusFilter ?? "all" }}';
    let activeCallerFilter = '{{ $callerFilter ?? "all" }}';
    let activeProgramFilter = '{{ $programFilter ?? "all" }}';
    let currentSearchTerm = '{{ $searchQuery ?? "" }}';
    let activePerPage = parseInt('{{ $perPage ?? request("per_page", 25) }}', 10) || 25;
    const maxTableLimit = parseInt('{{ $maxPageLimit ?? 100 }}', 10) || 100;
    let currentPage = 1;

    function enforcePageSizeLimit(input) {
        let val = parseInt(input.value, 10);
        if (isNaN(val)) return;
        if (val > maxTableLimit) {
            input.value = maxTableLimit;
        } else if (val < 1 && input.value !== '') {
            input.value = 1;
        }
    }

    function handlePageSizeChange(val) {
        let parsed = parseInt(val, 10);
        if (isNaN(parsed) || parsed < 1) parsed = 25;
        if (parsed > maxTableLimit) parsed = maxTableLimit;

        const input = document.getElementById('tablePageSizeInput');
        if (input) input.value = parsed;

        activePerPage = parsed;
        fetchLeadsTable(1, true);
    }

    function resetAllFilters() {
        currentSearchTerm = '';
        const searchInput = document.getElementById('liveSearchInput');
        if (searchInput) searchInput.value = '';

        activeFilterStatus = 'all';
        document.querySelectorAll('#statusFilterPills .filter-nav-pill').forEach(btn => btn.classList.remove('is-active'));
        const allBtn = document.querySelector('#statusFilterPills .filter-nav-pill');
        if (allBtn) allBtn.classList.add('is-active');

        activeAllocStatus = 'all';
        const allocSelect = document.getElementById('allocStatusFilterSelect');
        if (allocSelect) allocSelect.value = 'all';

        activeCallerFilter = 'all';
        const callerSelect = document.getElementById('callerFilterSelect');
        if (callerSelect) callerSelect.value = 'all';

        activeProgramFilter = 'all';
        const progSelect = document.getElementById('programFilterSelect');
        if (progSelect) progSelect.value = 'all';

        activePerPage = 25;
        const sizeInput = document.getElementById('tablePageSizeInput');
        if (sizeInput) sizeInput.value = 25;

        fetchLeadsTable(1, true);
        showToast('✓ All filters reset to default');
    }

    function handleLiveSearch(query) {
        clearTimeout(searchDebounceTimer);
        const spinner = document.getElementById('searchSpinner');
        if (spinner) spinner.style.display = 'block';

        searchDebounceTimer = setTimeout(() => {
            currentSearchTerm = query;
            fetchLeadsTable(1);
        }, 300);
    }

    function applyStatusFilter(status, btnElement) {
        activeFilterStatus = status;
        document.querySelectorAll('#statusFilterPills .filter-nav-pill').forEach(el => el.classList.remove('is-active'));
        if (btnElement) btnElement.classList.add('is-active');

        fetchLeadsTable(1);
    }

    function applyExtraFilter(filterKey, val) {
        if (filterKey === 'alloc_status') activeAllocStatus = val || 'all';
        if (filterKey === 'caller') activeCallerFilter = val || 'all';
        if (filterKey === 'program') activeProgramFilter = val || 'all';
        fetchLeadsTable(1);
    }

    function fetchLeadsTable(page = 1, showSpinner = true) {
        currentPage = page;
        const spinner = document.getElementById('searchSpinner');
        if (showSpinner && spinner) spinner.style.display = 'block';

        const url = new URL('{{ route("leads.index") }}', window.location.origin);
        if (activeTabName) {
            url.searchParams.set('tab', activeTabName);
        }
        if (activeFilterStatus && activeFilterStatus !== 'all') {
            url.searchParams.set('status', activeFilterStatus);
        }
        if (activeAllocStatus && activeAllocStatus !== 'all') {
            url.searchParams.set('alloc_status', activeAllocStatus);
        }
        if (activeCallerFilter && activeCallerFilter !== 'all') {
            url.searchParams.set('caller', activeCallerFilter);
        }
        if (activeProgramFilter && activeProgramFilter !== 'all') {
            url.searchParams.set('program', activeProgramFilter);
        }
        if (currentSearchTerm.trim()) {
            url.searchParams.set('q', currentSearchTerm.trim());
        }
        if (activePerPage) {
            url.searchParams.set('per_page', activePerPage);
        }
        if (page > 1) {
            url.searchParams.set('page', page);
        }

        fetch(url.toString(), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(res => res.text())
        .then(html => {
            const container = document.getElementById('leadsTableContainer');
            if (container) {
                container.innerHTML = html;
                attachPaginationListeners();
            }
        })
        .catch(err => {
            console.error('Error fetching leads table:', err);
        })
        .finally(() => {
            if (spinner) spinner.style.display = 'none';
        });
    }

    function openAddLeadModal() {
        const modal = document.getElementById('addLeadModal');
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeAddLeadModal() {
        const modal = document.getElementById('addLeadModal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    function submitAddLeadModal() {
        const btn = document.getElementById('btnSubmitNewLead');
        const notice = document.getElementById('addLeadNotice');
        if (btn) btn.disabled = true;
        if (notice) notice.style.display = 'none';

        fetch('{{ route("leads.storeInput") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name: document.getElementById('newLeadName')?.value || '',
                phoneno: document.getElementById('newLeadPhone')?.value || '',
                emailid: document.getElementById('newLeadEmail')?.value || '',
                program_id: document.getElementById('newLeadProgram')?.value || '',
                empcode: document.getElementById('newLeadCaller')?.value || ''
            })
        })
        .then(r => r.json())
        .then(data => {
            if (btn) btn.disabled = false;
            if (data.success) {
                closeAddLeadModal();
                showToast(`✓ ${data.message}`);
                document.getElementById('newLeadName').value = '';
                document.getElementById('newLeadPhone').value = '';
                document.getElementById('newLeadEmail').value = '';
                fetchLeadsTable(1, true);
            } else {
                if (notice) {
                    notice.style.display = 'block';
                    notice.style.color = '#dc2626';
                    notice.textContent = '✕ ' + (data.message || 'Unable to save lead.');
                }
            }
        })
        .catch(err => {
            if (btn) btn.disabled = false;
            if (notice) {
                notice.style.display = 'block';
                notice.style.color = '#dc2626';
                notice.textContent = 'Server error while saving lead.';
            }
        });
    }

    function attachPaginationListeners() {
        const paginationLinks = document.querySelectorAll('#leadsTableContainer .tele-pagination-links a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                if (href) {
                    const url = new URL(href, window.location.origin);
                    const page = url.searchParams.get('page') || 1;
                    fetchLeadsTable(page);
                }
            });
        });
    }

    // ================= LEADS INCHARGE ALLOCATION ACTIONS =================
    function toggleSelectAllLeads(checked) {
        document.querySelectorAll('.lead-select-chk').forEach(chk => {
            chk.checked = checked;
        });
        updateSelectedLeadsCount();
    }

    function updateSelectedLeadsCount() {
        const selected = document.querySelectorAll('.lead-select-chk:checked');
        const badge = document.getElementById('selectedCountBadge');
        const badgeBox = document.getElementById('selectedCountBadgeBox');
        if (badge) {
            badge.textContent = `${selected.length} Leads Selected`;
        }
        if (badgeBox) {
            if (selected.length > 0) {
                badgeBox.classList.add('has-selected');
            } else {
                badgeBox.classList.remove('has-selected');
            }
        }
    }

    function onInchargeTargetChange(val) {
        const customGroup = document.getElementById('inchargeCustomCountGroup');
        if (customGroup) {
            customGroup.style.display = (val === 'custom') ? 'block' : 'none';
        }
    }

    function submitInchargeAllocation() {
        const empSelect = document.getElementById('inchargeTelecallerSelect');
        const empCode = empSelect?.value;
        if (!empCode) {
            alert('Please choose a telecaller from the dropdown.');
            empSelect?.focus();
            return;
        }

        const targetMode = document.getElementById('inchargeTargetSelect')?.value || 'selected';
        const stageName = 'Stage 1: Calling & Verification';
        const remarks = 'Allocated via Leads Incharge allocation portal';

        let leadKeys = [];
        if (targetMode === 'selected') {
            const checkedBoxes = Array.from(document.querySelectorAll('.lead-select-chk:checked'));
            if (checkedBoxes.length === 0) {
                alert('Please select at least one lead from the table using checkboxes, or choose a bucket size (100, 300, 500, 1000).');
                return;
            }
            leadKeys = checkedBoxes.map(chk => ({
                applino: chk.value,
                phoneno: chk.getAttribute('data-phone') || ''
            }));
        }

        const btn = document.getElementById('btnInchargeAllot');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = 'Allocating...';
        }

        fetch('/leads/allocate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                empcode: empCode,
                allot_mode: targetMode,
                lead_keys: leadKeys,
                stage_name: stageName,
                stage_remark: remarks
            })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span>📤</span> Allocate to Telecaller';
            }

            if (data.success) {
                showToast(`✓ ${data.message}`);
                // Refresh table live from page 1 so allocated rows move down to last
                fetchLeadsTable(1, true);
                // Reset selected checkboxes count
                const badge = document.getElementById('selectedCountBadge');
                if (badge) badge.textContent = '0 Leads Selected';
                const badgeBox = document.getElementById('selectedCountBadgeBox');
                if (badgeBox) badgeBox.classList.remove('has-selected');
                const selectAll = document.getElementById('selectAllLeadsCheckbox');
                if (selectAll) selectAll.checked = false;
            } else {
                showToast(data.message || 'Allocation failed', true);
            }
        })
        .catch(err => {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<span>📤</span> Allocate to Telecaller';
            }
            console.error('Allocation error:', err);
            showToast('Server error while allocating leads.', true);
        });
    }

    // Attach listeners on initial document load
    document.addEventListener('DOMContentLoaded', () => {
        attachPaginationListeners();

        // Optional Live polling: check for new candidate registrations every 15 seconds
        setInterval(() => {
            const modalOpen = document.getElementById('wizardModal')?.classList.contains('active');
            if (!modalOpen && !currentSearchTerm.trim()) {
                fetchLeadsTable(currentPage, false);
            }
        }, 15000);
    });
</script>
</body>
</html>
