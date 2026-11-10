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

        /* Portal Table Card */
        .portal-card-shell {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 0.85rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }

        .portal-card-topbar {
            padding: 1.1rem 1.4rem;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
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
            padding: 0.75rem 1.25rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 1px solid var(--card-border);
            white-space: nowrap;
        }

        .tele-table td {
            padding: 0.95rem 1.25rem;
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
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.4rem;
            box-shadow: 0 4px 16px -2px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
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
                        @if($activeTab === 'allocation' || in_array($roleId, [2, 3]))
                            <span>Leads Incharge • Telecaller &amp; Step Allocation</span>
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

            @if(session('status'))
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 0.75rem 1.25rem; border-radius: 0.5rem; margin-bottom: 1.25rem; font-size: 0.82rem; font-weight: 600;">
                    ✓ {{ session('status') }}
                </div>
            @endif

            <!-- 4 METRIC STAT CARDS WITH SVG ICONS -->
            <div class="telecaller-stats-row">
                <div class="stat-card-modern">
                    <div class="stat-meta">
                        <span class="stat-title">Allotted Leads</span>
                        <span class="stat-number">{{ $totalAllotted }}</span>
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
                        <span class="stat-number" style="color: #b91c1c;">{{ $unallocatedCount ?? 0 }}</span>
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
                        <span class="stat-number">{{ $totalTasksCount ?? 0 }}</span>
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

            <!-- LEADS INCHARGE ALLOCATION CONTROL PANEL (Visible to Leads Incharge, Dean & on Allocation Tab) -->
            @if($activeTab === 'allocation' || in_array($roleId, [1, 2, 3]))
                <div class="allocation-command-card">
                    <!-- Header -->
                    <div class="incharge-header-wrap">
                        <div class="incharge-brand-box">
                            <div class="incharge-icon-emblem">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <polyline points="16 11 18 13 22 9"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="incharge-title-text">
                                    <span>Leads Incharge &bull; Telecaller &amp; Steps Allocation</span>
                                </h3>
                                <p class="incharge-sub-text">
                                    Allocate candidate lead numbers directly to telecaller queues in <code>crm.telecaller_leads</code> and assign action steps in <code>crm.telecaller_leads_tasks</code>.
                                </p>
                            </div>
                        </div>
                        <div>
                            <div class="incharge-selection-badge" id="selectedCountBadgeBox">
                                <span class="incharge-badge-dot"></span>
                                <span id="selectedCountBadge">0 Leads Selected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Form Body -->
                    <form id="leadInchargeAllotForm" onsubmit="event.preventDefault(); submitInchargeAllocation();">
                        <!-- Row 1: 3 Balanced Wide Columns -->
                        <div class="incharge-controls-grid">
                            <!-- 1. Select Telecaller -->
                            <div class="incharge-field-group">
                                <label for="inchargeTelecallerSelect" class="incharge-label">
                                    <span>Select Telecaller (Role 4)<span class="required-dot">*</span></span>
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
                                                {{ $tc->full_name }} ({{ $tc->emp_code }}) - Tele Caller
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- 2. Allocation Scope / Mode -->
                            <div class="incharge-field-group">
                                <label for="inchargeTargetSelect" class="incharge-label">
                                    <span>Allocation Target<span class="required-dot">*</span></span>
                                </label>
                                <div style="display: flex; gap: 0.5rem;">
                                    <div class="incharge-control-shell" style="flex: 1;">
                                        <span class="input-icon-left">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                                <circle cx="9" cy="7" r="4"/>
                                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                                                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                            </svg>
                                        </span>
                                        <select id="inchargeTargetSelect" class="incharge-select" onchange="onInchargeTargetChange(this.value)" required>
                                            <option value="selected">Selected Checked Leads</option>
                                            <option value="unallocated">All Unallocated Leads ({{ $unallocatedCount ?? 0 }})</option>
                                            <option value="first 10">First 10 Leads</option>
                                            <option value="first 25">First 25 Leads</option>
                                            <option value="first 50">First 50 Leads</option>
                                            <option value="custom">Custom Quantity...</option>
                                        </select>
                                    </div>
                                    <div id="inchargeCustomCountGroup" style="display: none; width: 85px;">
                                        <input type="number" id="inchargeCustomCount" class="incharge-input incharge-input-no-icon" min="1" max="500" value="15" title="Number of leads to allot" placeholder="Qty">
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Assign Step / Stage -->
                            <div class="incharge-field-group">
                                <label for="inchargeStageSelect" class="incharge-label">
                                    <span>Assign Step / Stage<span class="required-dot">*</span></span>
                                </label>
                                <div class="incharge-control-shell">
                                    <span class="input-icon-left">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="9 11 12 14 22 4"/>
                                            <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                                        </svg>
                                    </span>
                                    <select id="inchargeStageSelect" class="incharge-select" required>
                                        <option value="Stage 1: Fee Payment">Stage 1: Fee Payment</option>
                                        <option value="Stage 2: Personal Profile">Stage 2: Personal Profile</option>
                                        <option value="Stage 3: Academic Info">Stage 3: Academic Info</option>
                                        <option value="Stage 4: Doc Upload">Stage 4: Doc Upload</option>
                                        <option value="Stage 5: VSAT & Confirmation">Stage 5: VSAT &amp; Confirmation</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Row 2: Full Width Remarks + Pinned Allocate Button -->
                        <div class="incharge-action-row">
                            <div class="incharge-field-group">
                                <label for="inchargeRemarksInput" class="incharge-label">
                                    <span>Step Remarks</span>
                                    <span style="font-size: 0.68rem; color: #94a3b8; font-weight: 500;">Saved into crm.telecaller_leads_tasks</span>
                                </label>
                                <div class="incharge-control-shell">
                                    <span class="input-icon-left">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                                        </svg>
                                    </span>
                                    <input type="text" id="inchargeRemarksInput" class="incharge-input" placeholder="e.g. Call student for admission confirmation & seat booking" value="Initial telecalling allocation for admission counseling">
                                </div>
                            </div>

                            <div>
                                <button type="submit" id="btnInchargeAllot" class="incharge-btn-submit">
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
            @endif

            <!-- PORTAL TABLE CARD -->
            <div class="portal-card-shell">
                <!-- Top Toolbar -->
                <div class="portal-card-topbar">
                    <div class="filter-nav-pills" id="statusFilterPills">
                        <button type="button" onclick="applyStatusFilter('all', this)" class="filter-nav-pill {{ ($statusFilter ?? 'all') === 'all' ? 'is-active' : '' }}" style="background: none; cursor: pointer;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>All Leads
                        </button>
                        <button type="button" onclick="applyStatusFilter('stage1', this)" class="filter-nav-pill {{ ($statusFilter ?? '') === 'stage1' ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Step 1: Fee Payment</button>
                        <button type="button" onclick="applyStatusFilter('stage2', this)" class="filter-nav-pill {{ ($statusFilter ?? '') === 'stage2' ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Step 2: Personal Profile</button>
                        <button type="button" onclick="applyStatusFilter('stage3', this)" class="filter-nav-pill {{ ($statusFilter ?? '') === 'stage3' ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Step 3: Academic Info</button>
                        <button type="button" onclick="applyStatusFilter('stage4', this)" class="filter-nav-pill {{ ($statusFilter ?? '') === 'stage4' ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Step 4: Doc Upload</button>
                        <button type="button" onclick="applyStatusFilter('stage5', this)" class="filter-nav-pill {{ ($statusFilter ?? '') === 'stage5' ? 'is-active' : '' }}" style="background: none; cursor: pointer;">Step 5: VSAT &amp; Confirm</button>
                    </div>

                    <div style="display: flex; gap: 0.4rem; align-items: center;">
                        <div style="position: relative; display: flex; align-items: center;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 10px;">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                            <input type="text" id="liveSearchInput" value="{{ $searchQuery ?? '' }}" oninput="handleLiveSearch(this.value)" placeholder="Live search candidate, phone, app #..." style="padding: 0.45rem 2rem 0.45rem 2rem; border: 1.5px solid var(--card-border); border-radius: 0.45rem; font-size: 0.8rem; width: 250px; outline: none; transition: border-color 0.15s ease;">
                            <div class="live-search-spinner" id="searchSpinner"></div>
                        </div>
                    </div>
                </div>

                <!-- Clean Table Shell Container (Updated dynamically via Live Search) -->
                <div id="leadsTableContainer">
                    @include('leads::partials.leads_table', ['leads' => $leads])
                </div>
            </div>
        </main>
    </div>
</div>

<!-- ================= POPUP WIZARD MODAL ================= -->
<div class="wizard-overlay" id="wizardModal" onclick="closeWizardModalOnBackdrop(event)">
    <div class="wizard-modal-box">
        <!-- Header -->
        <div class="wizard-modal-header">
            <div>
                <div class="candidate-header-info">
                    <span class="candidate-header-name" id="modalCandidateName">Candidate Assessment</span>
                    <span class="candidate-header-badge" id="modalCandidateAppNo">APP#</span>
                </div>
                <div class="candidate-header-sub" id="modalCandidateSub">Program: B.Tech | +91 9912996788</div>
            </div>
            <button type="button" class="btn-close-modal" onclick="closeWizardModal()" title="Close Wizard">✕</button>
        </div>

        <!-- 5-Step Stepper Track -->
        <div class="stepper-nav-bar" id="modalStepperTrack">
            <!-- Dynamically injected tabs -->
        </div>

        <!-- Modal Body Content -->
        <div class="modal-body-content">
            <div class="assessment-panel">
                <div class="assessment-header">
                    <div>
                        <div class="stage-heading-title" id="activeStepTitle">
                            <span>Stage 1: Registration & Fee Payment</span>
                        </div>
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.15rem;" id="activeStepDesc">
                            Verify whether candidate completed the initial admission registration and submitted fee.
                        </div>
                    </div>
                    <span class="step-state-tag tag-progress" id="activeStepCurrentPill">STATUS: IN PROGRESS</span>
                </div>

                <div style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem;">
                    Stage Status:
                </div>

                <div class="assessment-status-options">
                    <label class="status-choice-card" id="label-completed" onclick="selectStatusOption('completed')">
                        <input type="radio" name="modal_stage_status" value="completed" style="accent-color: #10b981; margin-top: 3px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="20 6 9 17 4 12"></polyline>
                                </svg>
                                <span>Completed / Approved</span>
                            </div>
                            <div style="font-size: 0.72rem; color: #64748b; font-weight: 400; margin-top: 2px;">Candidate finished this step</div>
                        </div>
                    </label>

                    <label class="status-choice-card" id="label-inprogress" onclick="selectStatusOption('in_progress')">
                        <input type="radio" name="modal_stage_status" value="in_progress" style="accent-color: #2563eb; margin-top: 3px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <polyline points="12 6 12 12 16 14"></polyline>
                                </svg>
                                <span>In Progress</span>
                            </div>
                            <div style="font-size: 0.72rem; color: #64748b; font-weight: 400; margin-top: 2px;">Candidate working on it</div>
                        </div>
                    </label>

                    <label class="status-choice-card" id="label-locked" onclick="selectStatusOption('locked')">
                        <input type="radio" name="modal_stage_status" value="locked" style="accent-color: #94a3b8; margin-top: 3px;">
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.35rem;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                <span>Locked / Pending</span>
                            </div>
                            <div style="font-size: 0.72rem; color: #64748b; font-weight: 400; margin-top: 2px;">Next sequential stage</div>
                        </div>
                    </label>
                </div>

                <!-- Remarks (Optional) -->
                <div class="remarks-field-box">
                    <label for="modalStageRemarks" style="font-size: 0.78rem; font-weight: 700; color: #475569; display: flex; align-items: center; justify-content: space-between;">
                        <span>Telecaller Calling Remarks</span>
                        <span style="font-size: 0.68rem; color: #64748b; font-weight: 500; background: #e2e8f0; padding: 0.1rem 0.45rem; border-radius: 4px;">Optional</span>
                    </label>
                    <textarea id="modalStageRemarks" class="remarks-textarea" rows="2" placeholder="Enter counseling notes or callback details..."></textarea>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="wizard-modal-footer">
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" class="tele-btn-action" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 0.5rem 0.85rem;" id="btnPrevStage" onclick="navigateStep(-1)">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Previous Stage</span>
                </button>
                <button type="button" class="tele-btn-action" style="background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; padding: 0.5rem 0.85rem;" id="btnNextStage" onclick="navigateStep(1)">
                    <span>Next Stage</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </div>

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
    </div>
</div>

<!-- Toast -->
<div id="teleToast">
    <span id="toastMsg">Stage updated successfully!</span>
</div>

<script>
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const STAGES_META = [
        { key: 'stage1', num: 1, name: 'Fee Payment', desc: 'Seat confirmation registration fee payment via PayU and concession coupon allocation.' },
        { key: 'stage2', num: 2, name: 'Personal Profile', desc: 'Candidate personal info, parent details, permanent address, contact numbers, and category.' },
        { key: 'stage3', num: 3, name: 'Academic Info', desc: 'SSC / 10th and Intermediate / 12th / Diploma hall ticket numbers, board, and marks.' },
        { key: 'stage4', num: 4, name: 'Doc Upload', desc: 'Aadhaar card, 10th & 12th marks memos, study certificates, and photo upload verification.' },
        { key: 'stage5', num: 5, name: 'VSAT & Confirm', desc: 'VSAT rank verification, branch eligibility check, and provisional admission confirmation.' }
    ];

    let currentCandidate = null;
    let currentStepIndex = 0;
    let selectedStatus = 'completed';

    function showToast(msg, isError = false) {
        const toast = document.getElementById('teleToast');
        const toastMsg = document.getElementById('toastMsg');
        toastMsg.textContent = msg;
        toast.style.borderLeftColor = isError ? '#ef4444' : '#10b981';
        toast.style.display = 'flex';
        setTimeout(() => { toast.style.display = 'none'; }, 2800);
    }

    function openWizardModal(applinano, name, phone, program, stages, remarks = '') {
        currentCandidate = { applinano, name, phone, program, stages, remarks: remarks || '' };
        currentStepIndex = 0;

        document.getElementById('modalCandidateName').textContent = name;
        document.getElementById('modalCandidateAppNo').textContent = `APP: ${applinano}`;
        document.getElementById('modalCandidateSub').textContent = `Program: ${program} | Contact: +91 ${phone}`;

        renderModalStepper();
        loadStepDetail(0);

        document.getElementById('wizardModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeWizardModal() {
        document.getElementById('wizardModal').classList.remove('active');
        document.body.style.overflow = '';
    }

    function closeWizardModalOnBackdrop(e) {
        if (e.target.id === 'wizardModal') {
            closeWizardModal();
        }
    }

    function renderModalStepper() {
        const track = document.getElementById('modalStepperTrack');
        track.innerHTML = '';

        STAGES_META.forEach((stg, idx) => {
            const raw = (currentCandidate.stages[stg.key] || 'locked').toLowerCase();
            const isDone = ['completed', 'paid', 'approved'].includes(raw);
            const isInProg = ['in_progress', 'active', 'not_started', 'not_paid'].includes(raw);
            
            const tab = document.createElement('div');
            tab.className = `stepper-tab-item ${idx === currentStepIndex ? 'is-selected' : ''} ${isDone ? 'is-completed' : ''}`;
            tab.onclick = () => {
                currentStepIndex = idx;
                renderModalStepper();
                loadStepDetail(idx);
            };

            const tagClass = isDone ? 'tag-done' : (isInProg ? 'tag-progress' : 'tag-locked');
            const tagText = isDone ? 'Done' : (isInProg ? 'In Prog' : 'Lock');

            tab.innerHTML = `
                <div class="step-index-circle">${isDone ? '✓' : stg.num}</div>
                <div class="step-title-text">${stg.name}</div>
                <span class="step-state-tag ${tagClass}">${tagText}</span>
            `;
            track.appendChild(tab);
        });
    }

    function loadStepDetail(idx) {
        const stg = STAGES_META[idx];
        document.getElementById('activeStepTitle').textContent = `Stage ${stg.num}: ${stg.name}`;
        document.getElementById('activeStepDesc').textContent = stg.desc;

        const currentVal = (currentCandidate.stages[stg.key] || 'locked').toLowerCase();
        const isDone = ['completed', 'paid', 'approved'].includes(currentVal);
        const isInProg = ['in_progress', 'active', 'not_started', 'not_paid'].includes(currentVal);

        const pill = document.getElementById('activeStepCurrentPill');
        if (isDone) {
            pill.className = 'step-state-tag tag-done';
            pill.textContent = 'STATUS: COMPLETED';
            selectStatusOption('completed');
        } else if (isInProg) {
            pill.className = 'step-state-tag tag-progress';
            pill.textContent = 'STATUS: IN PROGRESS';
            selectStatusOption('in_progress');
        } else {
            pill.className = 'step-state-tag tag-locked';
            pill.textContent = 'STATUS: LOCKED';
            selectStatusOption('locked');
        }

        document.getElementById('btnPrevStage').disabled = (idx === 0);
        document.getElementById('btnNextStage').disabled = (idx === STAGES_META.length - 1);

        const remarksInput = document.getElementById('modalStageRemarks');
        if (remarksInput) {
            remarksInput.value = currentCandidate.remarks || '';
        }
    }

    function selectStatusOption(val) {
        selectedStatus = val;
        document.querySelectorAll('.status-choice-card').forEach(el => el.classList.remove('active-completed', 'active-progress', 'active-locked'));
        
        const radio = document.querySelector(`input[name="modal_stage_status"][value="${val}"]`);
        if (radio) radio.checked = true;

        if (val === 'completed') {
            document.getElementById('label-completed').classList.add('active-completed');
        } else if (val === 'in_progress') {
            document.getElementById('label-inprogress').classList.add('active-progress');
        } else {
            document.getElementById('label-locked').classList.add('active-locked');
        }
    }

    function navigateStep(direction) {
        const nextIdx = currentStepIndex + direction;
        if (nextIdx >= 0 && nextIdx < STAGES_META.length) {
            currentStepIndex = nextIdx;
            renderModalStepper();
            loadStepDetail(nextIdx);
        }
    }

    function saveActiveStageUpdate() {
        if (!currentCandidate) return;
        const stg = STAGES_META[currentStepIndex];
        const remarksVal = document.getElementById('modalStageRemarks')?.value || '';

        fetch(`/leads/${currentCandidate.applinano}/update-stage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                stage: stg.key,
                status: selectedStatus,
                remarks: remarksVal
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast(`✓ Stage ${stg.num} (${stg.name}) updated!`);
                currentCandidate.stages[stg.key] = selectedStatus;
                currentCandidate.remarks = remarksVal;
                renderModalStepper();
                loadStepDetail(currentStepIndex);
                
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

    // ================= LIVE SEARCH & FILTERING WITH 25 PAGINATION =================
    let searchDebounceTimer = null;
    let activeFilterStatus = '{{ $statusFilter ?? "all" }}';
    let currentSearchTerm = '{{ $searchQuery ?? "" }}';
    let currentPage = 1;

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

    function fetchLeadsTable(page = 1, showSpinner = true) {
        currentPage = page;
        const spinner = document.getElementById('searchSpinner');
        if (showSpinner && spinner) spinner.style.display = 'block';

        const url = new URL('{{ route("leads.index") }}', window.location.origin);
        if (activeFilterStatus && activeFilterStatus !== 'all') {
            url.searchParams.set('status', activeFilterStatus);
        }
        if (currentSearchTerm.trim()) {
            url.searchParams.set('q', currentSearchTerm.trim());
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
        const stageName = document.getElementById('inchargeStageSelect')?.value || 'Stage 1: Fee Payment';
        const remarks = document.getElementById('inchargeRemarksInput')?.value || '';
        const customCount = parseInt(document.getElementById('inchargeCustomCount')?.value || '15', 10);

        let leadKeys = [];
        if (targetMode === 'selected') {
            const checkedBoxes = Array.from(document.querySelectorAll('.lead-select-chk:checked'));
            if (checkedBoxes.length === 0) {
                alert('Please select at least one lead from the table using the checkboxes.');
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
                allot_count: customCount,
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
                // Refresh table live
                fetchLeadsTable(currentPage, true);
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
