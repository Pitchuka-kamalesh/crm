<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS & WhatsApp Gateway Tracker | Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Immediate Theme Preloader (Prevents Theme Flash) -->
    <script>
        (function(){
            document.documentElement.setAttribute('data-theme-color', localStorage.getItem('crm_theme_color') || 'red');
            document.documentElement.setAttribute('data-theme-mode', localStorage.getItem('crm_theme_mode') || 'light');
            if (localStorage.getItem('crm_sidebar_collapsed') === '1') {
                document.documentElement.classList.add('sidebar-is-collapsed');
            }
        })();
    </script>

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css">

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --brand-primary: #2563eb;
            --brand-primary-hover: #1d4ed8;
            --brand-whatsapp: #059669;
            --brand-whatsapp-dark: #047857;
            --bg-page: var(--bg-main, #f8fafc);
            --text-dark: var(--text-white, #0f172a);
            --text-slate: var(--text-slate, #334155);
            --text-muted: var(--text-muted, #64748b);
            --border-color: var(--card-border, #e2e8f0);
            --card-surface: var(--card-bg, #ffffff);
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--bg-page);
            color: var(--text-dark);
            min-height: 100vh;
            margin: 0;
            line-height: 1.5;
        }

        /* Main Container */
        .main-content {
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
            padding: 1.5rem 1.5rem 3rem 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Banner alerts */
        .alert-banner {
            padding: 0.85rem 1.25rem;
            border-radius: 12px;
            font-size: 0.86rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        /* Page Header */
        .page-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--text-dark);
        }

        .page-subtitle {
            font-size: 0.82rem;
            color: var(--text-muted);
            margin-top: 0.2rem;
        }

        /* Metrics Grid (All 5 Cards in a Single Row - Matching Leads Style) */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 1200px) {
            .metrics-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .metrics-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 500px) {
            .metrics-grid {
                grid-template-columns: 1fr;
            }
        }

        .metric-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 0.75rem;
            padding: 1.15rem 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
        }

        .metric-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            border-color: #cbd5e1;
        }

        .metric-meta {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .metric-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .metric-value {
            font-size: 1.65rem;
            font-weight: 800;
            color: var(--text-dark);
            line-height: 1.1;
        }

        .metric-sub {
            font-size: 0.72rem;
            color: #10b981;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            white-space: nowrap;
        }

        .metric-icon-disc {
            width: 44px;
            height: 44px;
            border-radius: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #475569;
            flex-shrink: 0;
        }

        .icon-disc-blue { background: #eff6ff; border-color: #dbeafe; color: #2563eb; }
        .icon-disc-green { background: #f0fdf4; border-color: #dcfce7; color: #16a34a; }
        .icon-disc-amber { background: #fffbeb; border-color: #fef3c7; color: #d97706; }
        .icon-disc-red { background: #fef2f2; border-color: #fee2e2; color: #dc2626; }
        .icon-disc-purple { background: #faf5ff; border-color: #f3e8ff; color: #9333ea; }

        /* Gateway Dispatcher Cards */
        .dispatchers-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 900px) {
            .dispatchers-grid {
                grid-template-columns: 1fr;
            }
        }

        .dispatch-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            padding: 1.35rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .dispatch-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--border-color);
        }

        .dispatch-title-box {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .dispatch-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 0.95rem;
        }

        .icon-wa { background: #059669; }
        .icon-sms { background: #2563eb; }

        .dispatch-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-dark);
        }

        .dispatch-meta {
            font-size: 0.72rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        .badge-live {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #86efac;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.18rem 0.55rem;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .dispatch-form {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem;
        }

        .form-group-field {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
        }

        .form-field-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
        }

        .form-control-input {
            width: 100%;
            padding: 0.55rem 0.85rem;
            border: 1.5px solid var(--border-color);
            border-radius: 0.5rem;
            font-size: 0.82rem;
            font-family: inherit;
            color: var(--text-dark);
            background: #ffffff;
            outline: none;
            transition: all 0.15s ease;
        }

        .form-control-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .btn-dispatch {
            padding: 0.65rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.82rem;
            font-weight: 700;
            color: #ffffff;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        .btn-dispatch-wa {
            background: #059669;
        }
        .btn-dispatch-wa:hover {
            background: #047857;
        }

        .btn-dispatch-sms {
            background: var(--theme-accent, #2563eb);
        }
        .btn-dispatch-sms:hover {
            background: var(--theme-accent-hover, #1d4ed8);
        }

        /* Tracker Logs Table Card */
        .table-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 0.85rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .table-card-header {
            padding: 1.1rem 1.4rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            gap: 1rem;
            background: #ffffff;
        }

        .table-card-header-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-form-single-row {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            width: 100%;
            flex-wrap: nowrap;
        }

        @media (max-width: 900px) {
            .filter-form-single-row {
                flex-wrap: wrap;
            }
        }

        .filter-select {
            padding: 0.45rem 0.85rem;
            border: 1.5px solid var(--border-color);
            border-radius: 0.45rem;
            font-size: 0.82rem;
            font-family: inherit;
            color: #334155;
            background: #ffffff;
            outline: none;
            cursor: pointer;
            min-width: 160px;
        }

        .filter-select:focus {
            border-color: var(--theme-accent, #2563eb);
            box-shadow: 0 0 0 3px var(--theme-accent-ring, rgba(37, 99, 235, 0.12));
        }

        .filter-search-box {
            position: relative;
            display: flex;
            align-items: center;
            flex: 1;
            min-width: 220px;
        }

        .filter-search {
            width: 100%;
            padding: 0.45rem 0.85rem 0.45rem 2rem;
            border: 1.5px solid var(--border-color);
            border-radius: 0.45rem;
            font-size: 0.82rem;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
            outline: none;
            transition: border-color 0.15s ease;
        }

        .filter-search:focus {
            border-color: var(--theme-accent, #2563eb);
            box-shadow: 0 0 0 3px var(--theme-accent-ring, rgba(37, 99, 235, 0.12));
        }

        .btn-filter {
            padding: 0.45rem 1.25rem;
            background: var(--theme-accent, #2563eb);
            color: #ffffff;
            border: none;
            border-radius: 0.45rem;
            font-size: 0.82rem;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-filter:hover {
            background: var(--theme-accent-hover, #1d4ed8);
        }

        .btn-reset {
            padding: 0.45rem 0.95rem;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid var(--border-color);
            border-radius: 0.45rem;
            font-size: 0.82rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .btn-reset:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.84rem;
            text-align: left;
        }

        .data-table th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.7rem;
            padding: 0.85rem 1.15rem;
            border-bottom: 1px solid var(--border-color);
            white-space: nowrap;
        }

        .data-table td {
            padding: 0.95rem 1.15rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            color: var(--text-dark);
        }

        .data-table tr:hover td {
            background: #f8fafc;
        }

        /* Badges */
        .badge-channel {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.22rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge-wa {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-sms-ch {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
        }

        /* Clean Status Pills & Action Button in Table */
        .status-pill-table {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.24rem 0.65rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            line-height: 1;
            white-space: nowrap;
            vertical-align: middle;
        }

        .status-pill-table .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
            margin: 0;
        }

        .status-submitted {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }
        .status-submitted .status-dot {
            background: #10b981;
            box-shadow: 0 0 4px #10b981;
        }

        .status-failed {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .status-failed .status-dot {
            background: #ef4444;
            box-shadow: 0 0 4px #ef4444;
        }

        [data-theme-mode="dark"] .status-submitted {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.3);
        }

        [data-theme-mode="dark"] .status-failed {
            background: rgba(239, 68, 68, 0.15);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.3);
        }

        .btn-view-json {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            padding: 0.35rem 0.65rem;
            background: #ffffff;
            color: #475569;
            border: 1px solid #cbd5e1;
            border-radius: 0.45rem;
            font-size: 0.74rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
            line-height: 1.2;
            text-decoration: none;
        }

        .btn-view-json:hover {
            background: #f1f5f9;
            color: #1e293b;
            border-color: #94a3b8;
        }

        [data-theme-mode="dark"] .btn-view-json {
            background: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
            border-color: rgba(255, 255, 255, 0.15);
        }
        [data-theme-mode="dark"] .btn-view-json:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.25);
        }

        /* Trace ID Pill with Click-to-Copy */
        .trace-id-wrapper {
            font-family: 'JetBrains Mono', 'Courier New', monospace;
            font-size: 0.72rem;
            color: var(--theme-accent, #2563eb);
            background: var(--theme-accent-light, #eff6ff);
            border: 1px solid var(--theme-accent-ring, #bfdbfe);
            padding: 0.22rem 0.55rem;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            cursor: pointer;
            transition: all 0.15s ease;
            max-width: 220px;
            user-select: none;
        }

        .trace-id-wrapper:hover {
            box-shadow: 0 1px 4px var(--theme-accent-ring, rgba(37, 99, 235, 0.15));
        }

        .trace-id-wrapper:active {
            transform: scale(0.98);
        }

        .trace-id-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            letter-spacing: -0.01em;
            font-weight: 600;
        }

        .trace-copy-icon {
            color: var(--theme-accent, #60a5fa);
            flex-shrink: 0;
            transition: color 0.15s ease;
        }

        .trace-id-wrapper:hover .trace-copy-icon {
            color: var(--theme-accent-hover, #2563eb);
        }

        [data-theme-mode="dark"] .trace-id-wrapper {
            background: var(--theme-accent-light, rgba(37, 99, 235, 0.12));
            color: var(--theme-accent, #93c5fd);
            border-color: var(--theme-accent-ring, rgba(37, 99, 235, 0.25));
        }

        [data-theme-mode="dark"] .trace-id-wrapper:hover {
            filter: brightness(1.15);
        }

        [data-theme-mode="dark"] .trace-copy-icon {
            color: var(--theme-accent, #60a5fa);
        }

        .table-user-primary {
            font-weight: 700;
            color: #0f172a;
            font-size: 0.83rem;
            line-height: 1.25;
            white-space: nowrap;
        }

        .table-sub-text {
            font-size: 0.72rem;
            color: #64748b;
            margin-top: 2px;
            white-space: nowrap;
        }

        .table-template-text {
            font-weight: 600;
            color: #334155;
            font-size: 0.75rem;
            background: #f1f5f9;
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
            display: inline-block;
            white-space: nowrap;
        }

        .mono-phone {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            color: var(--text-dark);
            white-space: nowrap;
        }

        .msg-excerpt {
            max-width: 250px;
            font-size: 0.78rem;
            color: var(--text-slate);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        [data-theme-mode="dark"] .table-user-primary {
            color: #f8fafc !important;
        }

        [data-theme-mode="dark"] .table-sub-text {
            color: #94a3b8 !important;
        }

        [data-theme-mode="dark"] .table-template-text {
            background: rgba(255, 255, 255, 0.06) !important;
            color: #cbd5e1 !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        [data-theme-mode="dark"] .mono-phone {
            color: #f8fafc !important;
        }

        [data-theme-mode="dark"] .msg-excerpt {
            color: #94a3b8 !important;
        }

        .table-pagination-area {
            padding: 1rem 1.4rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--card-surface);
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .table-pagination-area .pagination {
            display: flex;
            gap: 0.3rem;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
        }

        .table-pagination-area .page-item .page-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 0.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-slate);
            background: var(--card-surface);
            border: 1px solid var(--border-color);
            border-radius: 0.4rem;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .table-pagination-area .page-item.active .page-link {
            background: var(--theme-accent, #2563eb);
            color: #ffffff !important;
            border-color: var(--theme-accent, #2563eb);
            font-weight: 700;
        }

        .table-pagination-area .page-item.disabled .page-link {
            opacity: 0.45;
            cursor: not-allowed;
            background: var(--bg-page);
        }

        .table-pagination-area .page-item:not(.active):not(.disabled) .page-link:hover {
            background: var(--bg-page);
            border-color: #cbd5e1;
            color: var(--text-dark);
        }

        /* Header Action Controls (Time Filter & Refresh) */
        .header-actions-group {
            display: flex;
            align-items: center;
            gap: 0.65rem;
        }

        .time-segmented-control {
            display: inline-flex;
            align-items: center;
            background: #f1f5f9;
            padding: 3px;
            border-radius: 0.55rem;
            border: 1px solid var(--border-color);
            gap: 2px;
        }

        [data-theme-mode="dark"] .time-segmented-control {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .segment-btn {
            padding: 0.35rem 0.8rem;
            font-size: 0.76rem;
            font-weight: 600;
            color: var(--text-muted);
            border-radius: 0.45rem;
            text-decoration: none;
            border: none;
            background: transparent;
            transition: all 0.15s ease;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            line-height: 1.2;
        }

        .segment-btn:hover {
            color: var(--text-dark);
            text-decoration: none;
        }

        .segment-btn.active {
            background: #ffffff;
            color: #2563eb;
            font-weight: 700;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        [data-theme-mode="dark"] .segment-btn.active {
            background: var(--card-surface);
            color: #60a5fa;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        .btn-header-refresh {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.42rem 0.85rem;
            background: #ffffff;
            color: #475569;
            border: 1px solid var(--border-color);
            border-radius: 0.55rem;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            line-height: 1.2;
        }

        .btn-header-refresh:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        [data-theme-mode="dark"] .btn-header-refresh {
            background: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
            border-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme-mode="dark"] .btn-header-refresh:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        .btn-header-print {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.42rem 0.85rem;
            background: #ffffff;
            color: #1e293b;
            border: 1px solid var(--border-color);
            border-radius: 0.55rem;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            white-space: nowrap;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            line-height: 1.2;
        }

        .btn-header-print:hover {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #94a3b8;
        }

        [data-theme-mode="dark"] .btn-header-print {
            background: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
            border-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme-mode="dark"] .btn-header-print:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* ================= DARK MODE OVERRIDES (DISPATCHERS & TABLE) ================= */
        [data-theme-mode="dark"] .dispatch-card {
            background: #131d31;
            border-color: #1e293b;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        }

        [data-theme-mode="dark"] .dispatch-header {
            border-color: #1e293b;
        }

        [data-theme-mode="dark"] .dispatch-title {
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .dispatch-meta {
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .form-field-label {
            color: #cbd5e1;
        }

        [data-theme-mode="dark"] .form-control-input {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .form-control-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        [data-theme-mode="dark"] .badge-live {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.3);
        }

        [data-theme-mode="dark"] .table-card {
            background: #131d31;
            border-color: #1e293b;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
        }

        [data-theme-mode="dark"] .table-card-header {
            background: #131d31;
            border-color: #1e293b;
        }

        [data-theme-mode="dark"] .filter-select {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .filter-search {
            background: #0f172a;
            border-color: #334155;
            color: #f8fafc;
        }

        [data-theme-mode="dark"] .filter-search:focus,
        [data-theme-mode="dark"] .filter-select:focus {
            border-color: #3b82f6;
        }

        [data-theme-mode="dark"] .btn-reset {
            background: rgba(255, 255, 255, 0.05);
            color: #cbd5e1;
            border-color: rgba(255, 255, 255, 0.1);
        }

        [data-theme-mode="dark"] .btn-reset:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }

        [data-theme-mode="dark"] .data-table th {
            background: #0f172a;
            border-color: #1e293b;
            color: #94a3b8;
        }

        [data-theme-mode="dark"] .data-table td {
            border-color: #1e293b;
            color: #cbd5e1;
        }

        [data-theme-mode="dark"] .data-table tr:hover td {
            background: rgba(255, 255, 255, 0.03);
        }

        [data-theme-mode="dark"] .table-user-primary {
            color: #f8fafc !important;
        }

        [data-theme-mode="dark"] .badge-wa {
            background: rgba(16, 185, 129, 0.15);
            color: #6ee7b7;
            border-color: rgba(16, 185, 129, 0.3);
        }

        [data-theme-mode="dark"] .badge-sms-ch {
            background: rgba(37, 99, 235, 0.15);
            color: #93c5fd;
            border-color: rgba(37, 99, 235, 0.3);
        }

        /* ================= PRINT REPORT STYLES ================= */
        .print-only-header {
            display: none;
        }

        @media print {
            .crm-sidebar,
            .top-bar,
            .theme-preset-bar,
            .dispatchers-grid,
            .header-actions-group,
            .filter-form-single-row,
            .table-pagination-area,
            .btn-view-json,
            .alert-banner,
            .page-subtitle,
            .page-header,
            #jsonModal {
                display: none !important;
            }

            body, .app-wrapper, .main-content {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .main-content {
                padding: 10mm !important;
            }

            .print-only-header {
                display: block !important;
                margin-bottom: 1.25rem;
                padding-bottom: 0.85rem;
                border-bottom: 2px solid #0f172a;
            }

            .print-header-top {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 0.75rem;
            }

            .print-org-info h2 {
                font-size: 14pt;
                font-weight: 800;
                margin: 0;
                color: #0f172a;
                letter-spacing: -0.01em;
            }

            .print-org-info p {
                font-size: 8.5pt;
                margin: 2px 0 0;
                color: #475569;
            }

            .print-report-meta {
                background: #f8fafc;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                padding: 8px 12px;
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 8px;
                font-size: 8pt;
                color: #1e293b;
            }

            .metrics-grid {
                display: grid !important;
                grid-template-columns: repeat(5, 1fr) !important;
                gap: 6px !important;
                margin-bottom: 12px !important;
            }

            .metric-card {
                border: 1px solid #cbd5e1 !important;
                background: #ffffff !important;
                box-shadow: none !important;
                padding: 8px 10px !important;
            }

            .metric-label {
                font-size: 6.5pt !important;
                color: #64748b !important;
            }

            .metric-value {
                font-size: 13pt !important;
                color: #000000 !important;
            }

            .metric-sub {
                font-size: 6.5pt !important;
            }

            .metric-icon-disc {
                display: none !important;
            }

            .table-card {
                border: 1px solid #94a3b8 !important;
                box-shadow: none !important;
                background: #ffffff !important;
            }

            .table-card-header {
                padding: 8px 10px !important;
                border-bottom: 1px solid #cbd5e1 !important;
                background: #f8fafc !important;
            }

            .table-card-header h2 {
                font-size: 10pt !important;
                color: #000000 !important;
                margin: 0 !important;
            }

            .data-table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            .data-table th, .data-table td {
                border: 1px solid #cbd5e1 !important;
                padding: 5px 6px !important;
                font-size: 7.5pt !important;
                color: #000000 !important;
            }

            .data-table th {
                background: #f1f5f9 !important;
                font-weight: 700 !important;
            }

            .badge-channel {
                border: none !important;
                padding: 0 !important;
                font-weight: 700 !important;
            }

            .status-pill-table {
                border: none !important;
                padding: 0 !important;
                font-weight: 700 !important;
            }

            .trace-id-wrapper {
                border: none !important;
                padding: 0 !important;
                background: transparent !important;
                max-width: none !important;
            }

            .trace-copy-icon {
                display: none !important;
            }

            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }

        /* Modal dialog for raw JSON view */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            padding: 1.5rem;
        }

        .modal-card {
            background: var(--card-surface);
            border-radius: 12px;
            width: 100%;
            max-width: 640px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .modal-header {
            padding: 1.15rem 1.4rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .modal-title {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-dark);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.25rem;
            cursor: pointer;
            color: var(--text-muted);
        }

        .modal-body {
            padding: 1.25rem 1.4rem;
            max-height: 480px;
            overflow-y: auto;
        }

        .json-pre {
            background: #0f172a;
            color: #38bdf8;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
            white-space: pre-wrap;
        }

        /* Dark Mode Dedicated Overrides for SMS Tracker */
        [data-theme-mode="dark"] .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        [data-theme-mode="dark"] .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        [data-theme-mode="dark"] .badge-wa,
        [data-theme-mode="dark"] .status-submitted {
            background: rgba(16, 185, 129, 0.15);
            border-color: rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        [data-theme-mode="dark"] .badge-sms-ch {
            background: rgba(59, 130, 246, 0.15);
            border-color: rgba(59, 130, 246, 0.3);
            color: #93c5fd;
        }
        [data-theme-mode="dark"] .status-failed {
            background: rgba(239, 68, 68, 0.15);
            border-color: rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        [data-theme-mode="dark"] .badge-live {
            background: rgba(16, 185, 129, 0.18);
            border-color: rgba(16, 185, 129, 0.35);
            color: #a7f3d0;
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
                    <span class="breadcrumb-item">Gateway</span>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-active-tag">
                        <span class="breadcrumb-dot" style="background:#10b981;box-shadow:0 0 8px #10b981;"></span>
                        <span>SMS Tracker & Dispatches</span>
                    </span>
                </div>
            </div>

            <div class="top-bar-right">
                @include('layouts.role-switcher')
            </div>
        </header>

        <main class="main-content">

        <!-- Alerts -->
        @if(session('status'))
            <div class="alert-banner alert-success">
                <span>&bull; {{ session('status') }}</span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;font-weight:700;">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-banner alert-danger">
                <span>&bull; {{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;color:inherit;font-weight:700;">&times;</button>
            </div>
        @endif

        <!-- Official Print Header (Visible only when printed) -->
        <div class="print-only-header">
            <div class="print-header-top">
                <img src="/images/vignan-logo.png" alt="VFSTR Logo" style="height:46px;">
                <div class="print-org-info">
                    <h2>VIGNAN'S FOUNDATION FOR SCIENCE, TECHNOLOGY &amp; RESEARCH</h2>
                    <p>(Deemed to be University) &bull; Admissions Counseling &amp; Communication Wing</p>
                </div>
            </div>
            <div class="print-report-meta">
                <div><strong>Report Scope:</strong> {{ ($timeFilter ?? 'all') === 'today' ? "Today's Activity (" . date('d F Y') . ")" : "All Time Logs" }}</div>
                <div><strong>Officer:</strong> {{ session('login_name', 'Y. BHANUPRASAD') }} (Tele Caller)</div>
                <div><strong>Generated:</strong> {{ date('d M Y, h:i A') }}</div>
                <div><strong>Total Logs:</strong> {{ number_format($totalLogs) }} records</div>
            </div>
        </div>

        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">SMS &amp; WhatsApp Communication Tracker</h1>
                <p class="page-subtitle">Unified monitoring for SmartPing WhatsApp Gateway and JioCX DLT SMS logs, delivery receipts, and live dispatches.</p>
            </div>
            <div class="header-actions-group">
                <!-- Time Range Segmented Switch (All vs Today) -->
                <div class="time-segmented-control">
                    <a href="{{ route('sms.index', array_merge(request()->query(), ['time' => 'all'])) }}" class="segment-btn {{ ($timeFilter ?? 'all') === 'all' ? 'active' : '' }}">
                        <span>All Time</span>
                    </a>
                    <a href="{{ route('sms.index', array_merge(request()->query(), ['time' => 'today'])) }}" class="segment-btn {{ ($timeFilter ?? '') === 'today' ? 'active' : '' }}">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                            <line x1="16" y1="2" x2="16" y2="6"></line>
                            <line x1="8" y1="2" x2="8" y2="6"></line>
                            <line x1="3" y1="10" x2="21" y2="10"></line>
                        </svg>
                        <span>Today's Report</span>
                    </a>
                </div>

                <button type="button" class="btn-header-refresh" onclick="window.location.reload()" title="Connected to master.communication_logs. Click to refresh manually.">
                    <span style="display:inline-block;width:7px;height:7px;border-radius:50%;background:#10b981;box-shadow:0 0 6px #10b981;"></span>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                    </svg>
                    <span>Live Sync</span>
                </button>
            </div>
        </div>

        <!-- KPI Metrics (Showing All & Today Indicators) -->
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-meta">
                    <span class="metric-label">Total Dispatched</span>
                    <span class="metric-value">{{ number_format($totalLogs) }}</span>
                    <span class="metric-sub" style="color:#64748b;">Today: <b style="color:var(--text-dark);">{{ $todayDispatched }}</b></span>
                </div>
                <div class="metric-icon-disc icon-disc-purple">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="20" x2="18" y2="10"></line>
                        <line x1="12" y1="20" x2="12" y2="4"></line>
                        <line x1="6" y1="20" x2="6" y2="14"></line>
                    </svg>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-meta">
                    <span class="metric-label">WhatsApp</span>
                    <span class="metric-value" style="color:#16a34a;">{{ number_format($totalWhatsapp) }}</span>
                    <span class="metric-sub">SmartPing Gateway</span>
                </div>
                <div class="metric-icon-disc icon-disc-green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                    </svg>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-meta">
                    <span class="metric-label">Jio DLT SMS</span>
                    <span class="metric-value" style="color:#2563eb;">{{ number_format($totalSms) }}</span>
                    <span class="metric-sub" style="color:#2563eb;">JioCX Direct</span>
                </div>
                <div class="metric-icon-disc icon-disc-blue">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-meta">
                    <span class="metric-label">Delivered</span>
                    <span class="metric-value" style="color:#16a34a;">{{ number_format($totalSuccess) }}</span>
                    <span class="metric-sub">Today: <b style="color:#16a34a;">{{ $todaySuccess }}</b> sent</span>
                </div>
                <div class="metric-icon-disc icon-disc-green">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                </div>
            </div>

            <div class="metric-card">
                <div class="metric-meta">
                    <span class="metric-label">Failed</span>
                    <span class="metric-value" style="color:#dc2626;">{{ number_format($totalFailed) }}</span>
                    <span class="metric-sub" style="color:#dc2626;">Today: <b>{{ $todayFailed }}</b></span>
                </div>
                <div class="metric-icon-disc icon-disc-red">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Test Dispatchers -->
        <div class="dispatchers-grid">

            <!-- Card 1: SmartPing WhatsApp Gateway -->
            <div class="dispatch-card">
                <div class="dispatch-header">
                    <div class="dispatch-title-box">
                        <div class="dispatch-icon icon-wa">WA</div>
                        <div>
                            <h3 class="dispatch-title">SmartPing WhatsApp Gateway</h3>
                            <div class="dispatch-meta">Campaigns: vimsgridonetimepp / crmfirstinfo</div>
                        </div>
                    </div>
                    <span class="badge-live">Active JWT</span>
                </div>

                <form action="{{ route('sms.test-whatsapp') }}" method="POST" class="dispatch-form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group-field">
                            <label class="form-field-label">Recipient Mobile (10-digits)</label>
                            <input type="tel" name="phone" required maxlength="10" placeholder="e.g. 8309607291" class="form-control-input" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                        </div>
                        <div class="form-group-field">
                            <label class="form-field-label">Campaign Template</label>
                            <select name="campaign_type" class="form-control-input" id="wa-campaign-selector" onchange="toggleWaFields(this.value)">
                                <option value="otp">OTP Verification (vimsgridonetimepp)</option>
                                <option value="admission">Admission Details (crmfirstinfo)</option>
                            </select>
                        </div>
                    </div>

                    <div id="wa-admission-fields" style="display:none;gap:0.75rem;" class="form-row">
                        <div class="form-group-field">
                            <label class="form-field-label">Student Name</label>
                            <input type="text" name="test_name" placeholder="e.g. Ramesh Kethakana" class="form-control-input">
                        </div>
                        <div class="form-group-field">
                            <label class="form-field-label">Program Name</label>
                            <input type="text" name="test_program" placeholder="e.g. MBA / MCA" class="form-control-input">
                        </div>
                    </div>

                    <button type="submit" class="btn-dispatch btn-dispatch-wa">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Send WhatsApp via SmartPing</span>
                    </button>
                </form>
            </div>

            <!-- Card 2: Jio DLT SMS Gateway -->
            <div class="dispatch-card">
                <div class="dispatch-header">
                    <div class="dispatch-title-box">
                        <div class="dispatch-icon icon-sms">SMS</div>
                        <div>
                            <h3 class="dispatch-title">JioCX DLT SMS Gateway</h3>
                            <div class="dispatch-meta">Entity: 1101385640000011255 | Sender: VIGNAN</div>
                        </div>
                    </div>
                    <span class="badge-live">DLT Ready</span>
                </div>

                <form action="{{ route('sms.test-sms') }}" method="POST" class="dispatch-form">
                    @csrf
                    <div class="form-row">
                        <div class="form-group-field">
                            <label class="form-field-label">Recipient Mobile (10-digits)</label>
                            <input type="tel" name="phone" required maxlength="10" placeholder="e.g. 8309607291" class="form-control-input" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)">
                        </div>
                        <div class="form-group-field">
                            <label class="form-field-label">SMS Template</label>
                            <select name="sms_type" class="form-control-input" id="sms-template-selector" onchange="toggleSmsFields(this.value)">
                                <option value="admission">Admission Notice (1777179354065677511)</option>
                                <option value="otp">Login OTP (1777179265526294215)</option>
                            </select>
                        </div>
                    </div>

                    <div id="sms-admission-fields" style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div class="form-group-field">
                            <label class="form-field-label">Student Name</label>
                            <input type="text" name="student_name" placeholder="Candidate Name" class="form-control-input">
                        </div>
                        <div class="form-group-field">
                            <label class="form-field-label">Branch / Program</label>
                            <input type="text" name="program_name" placeholder="e.g. Computer Science" class="form-control-input">
                        </div>
                    </div>

                    <button type="submit" class="btn-dispatch btn-dispatch-sms">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span>Dispatch SMS via JioCX</span>
                    </button>
                </form>
            </div>

        </div>

        <!-- Communication Tracker Table -->
        <div class="table-card">
            <div class="table-card-header">
                <div class="table-card-header-top">
                    <div>
                        <h2 style="font-size:1.1rem;font-weight:800;color:var(--text-dark);">Live Gateway Dispatch Logs (master.communication_logs)</h2>
                        <p style="font-size:0.76rem;color:var(--text-muted);margin-top:2px;">Includes real-time delivery status, SmartPing trace IDs, and payload records.</p>
                    </div>
                </div>

                <!-- Single Row Filter Controls -->
                <form action="{{ route('sms.index') }}" method="GET" class="filter-form-single-row">
                    <select name="channel" class="filter-select" onchange="this.form.submit()">
                        <option value="all" {{ ($channel ?? '') === 'all' ? 'selected' : '' }}>All Channels</option>
                        <option value="whatsapp" {{ ($channel ?? '') === 'whatsapp' ? 'selected' : '' }}>WhatsApp Only</option>
                        <option value="sms" {{ ($channel ?? '') === 'sms' ? 'selected' : '' }}>SMS Only</option>
                    </select>

                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="all" {{ ($status ?? '') === 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="SUBMITTED" {{ ($status ?? '') === 'SUBMITTED' ? 'selected' : '' }}>SUBMITTED</option>
                        <option value="DELIVERED" {{ ($status ?? '') === 'DELIVERED' ? 'selected' : '' }}>DELIVERED</option>
                        <option value="FAILED" {{ ($status ?? '') === 'FAILED' ? 'selected' : '' }}>FAILED</option>
                    </select>

                    <div class="filter-search-box">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:absolute;left:10px;">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search recipient phone, reg no, trace ID..." class="filter-search">
                    </div>

                    <button type="submit" class="btn-filter">Filter Logs</button>
                    @if(!empty($channel) || !empty($status) || !empty($search))
                        <a href="{{ route('sms.index') }}" class="btn-reset">Reset</a>
                    @endif
                </form>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="white-space: nowrap;">ID &amp; Time</th>
                            <th style="white-space: nowrap; text-align: center;">Channel</th>
                            <th style="white-space: nowrap;">Recipient Mobile</th>
                            <th>User / Reg No</th>
                            <th style="white-space: nowrap;">Campaign / Template</th>
                            <th>Message Preview</th>
                            <th style="white-space: nowrap;">Trace ID</th>
                            <th style="white-space: nowrap; text-align: center;">Status</th>
                            <th style="white-space: nowrap; text-align: right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr>
                                <td style="white-space: nowrap;">
                                    <div class="table-user-primary">#{{ $log->id }}</div>
                                    <div class="table-sub-text">{{ $log->created_at?->format('d M, h:i A') ?? 'Just now' }}</div>
                                </td>
                                <td style="white-space: nowrap; text-align: center;">
                                    @if($log->channel === 'whatsapp')
                                        <span class="badge-channel badge-wa">
                                            <span>💬</span> WhatsApp
                                        </span>
                                    @else
                                        <span class="badge-channel badge-sms-ch">
                                            <span>📱</span> SMS
                                        </span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap;">
                                    <span class="mono-phone">+91 {{ $log->wapptophone }}</span>
                                </td>
                                <td>
                                    <div class="table-user-primary">{{ $log->wauser ?: 'Prospective Student' }}</div>
                                    <div class="table-sub-text">{{ $log->waregisterno ? 'Reg: ' . $log->waregisterno : 'N/A' }}</div>
                                </td>
                                <td style="white-space: nowrap;">
                                    <span class="table-template-text">{{ $log->template_name ?: 'vimsgridonetimepp' }}</span>
                                </td>
                                <td>
                                    <div class="msg-excerpt" title="{{ $log->wapptext }}">{{ $log->wapptext }}</div>
                                </td>
                                <td style="white-space: nowrap;">
                                    @if($log->jiotraceid)
                                        <div class="trace-id-wrapper" title="Click to copy Trace ID: {{ $log->jiotraceid }}" onclick="copyTraceId(this, '{{ $log->jiotraceid }}')">
                                            <span class="trace-id-text">{{ $log->jiotraceid }}</span>
                                            <svg class="trace-copy-icon" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect>
                                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <span style="color:#94a3b8;font-size:0.75rem;">—</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap; text-align: center;">
                                    @if(in_array($log->status, ['SUBMITTED', 'DELIVERED', 'SENT']))
                                        <span class="status-pill-table status-submitted"><span class="status-dot"></span> {{ $log->status }}</span>
                                    @else
                                        <span class="status-pill-table status-failed"><span class="status-dot"></span> {{ $log->status }}</span>
                                    @endif
                                </td>
                                <td style="white-space: nowrap; text-align: right;">
                                    <button type="button" class="btn-view-json" onclick="viewPayload({{ json_encode($log->response_payload ?? ['status' => $log->status]) }})">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="16 18 22 12 16 6"></polyline>
                                            <polyline points="8 6 2 12 8 18"></polyline>
                                        </svg>
                                        <span>View JSON</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" style="text-align:center;padding:2.5rem;color:#94a3b8;">
                                    No communication logs recorded yet. Send a test WhatsApp or SMS above or trigger an OTP from the login page!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="table-pagination-area">
                <div style="font-size:0.78rem;color:var(--text-muted);">
                    Showing <span style="font-weight:700;color:var(--text-dark);">{{ $logs->firstItem() ?? 0 }}</span> to <span style="font-weight:700;color:var(--text-dark);">{{ $logs->lastItem() ?? 0 }}</span> of <span style="font-weight:700;color:var(--text-dark);">{{ $logs->total() }}</span> records (10 per page)
                </div>
                <div>
                    {{ $logs->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>

    </main>
    </div>
</div>

    <!-- Modal for JSON Inspection -->
    <div id="jsonModal" class="modal-overlay" onclick="closeModal(event)">
        <div class="modal-card" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 class="modal-title">Gateway API Response Payload</h3>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <pre id="jsonContent" class="json-pre"></pre>
            </div>
        </div>
    </div>

    <script>
        function copyTraceId(element, text) {
            if (!text) return;
            navigator.clipboard.writeText(text).then(() => {
                const origHtml = element.innerHTML;
                element.innerHTML = `
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span style="color:#10b981;font-weight:700;">Copied!</span>
                `;
                element.style.borderColor = '#10b981';
                setTimeout(() => {
                    element.innerHTML = origHtml;
                    element.style.borderColor = '';
                }, 1600);
            }).catch(err => {
                // Fallback for non-https/older browsers
                const ta = document.createElement('textarea');
                ta.value = text;
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
                const origHtml = element.innerHTML;
                element.innerHTML = `
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    <span style="color:#10b981;font-weight:700;">Copied!</span>
                `;
                element.style.borderColor = '#10b981';
                setTimeout(() => {
                    element.innerHTML = origHtml;
                    element.style.borderColor = '';
                }, 1600);
            });
        }

        function toggleWaFields(val) {
            const fields = document.getElementById('wa-admission-fields');
            fields.style.display = (val === 'admission') ? 'grid' : 'none';
        }

        function toggleSmsFields(val) {
            const fields = document.getElementById('sms-admission-fields');
            fields.style.display = (val === 'admission') ? 'grid' : 'none';
        }

        function viewPayload(payload) {
            const modal = document.getElementById('jsonModal');
            const content = document.getElementById('jsonContent');
            content.textContent = JSON.stringify(payload, null, 2);
            modal.style.display = 'flex';
        }

        function closeModal(e) {
            document.getElementById('jsonModal').style.display = 'none';
        }

        // Live Real-Time Background Sync: auto-checks for new gateway dispatches every 20s
        setInterval(() => {
            const isModalOpen = document.getElementById('jsonModal')?.style.display === 'flex';
            const urlParams = new URLSearchParams(window.location.search);
            const hasSearch = urlParams.get('search') && urlParams.get('search').trim() !== '';
            const page = urlParams.get('page') || '1';
            const isTyping = document.activeElement && ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName);

            if (!isModalOpen && !hasSearch && page === '1' && !isTyping) {
                window.location.reload();
            }
        }, 20000);
    </script>
</body>
</html>
