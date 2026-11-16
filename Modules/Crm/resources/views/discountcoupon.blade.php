<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discount Coupon Master - VFSTR Admissions CRM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css?v={{ file_exists(public_path('css/crm-dashboard.css')) ? filemtime(public_path('css/crm-dashboard.css')) : time() }}">

    <style>
        .coupon-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        .coupon-card-header {
            padding: 1.15rem 1.5rem;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .coupon-card-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .coupon-card-body {
            padding: 1.5rem;
        }
        .active-banner {
            background: linear-gradient(135deg, #064e3b 0%, #047857 100%);
            color: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(5, 150, 105, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.25rem;
        }
        .active-banner-empty {
            background: linear-gradient(135deg, #7c2d12 0%, #c2410c 100%);
            color: #ffffff;
            border-radius: 14px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 10px 25px -5px rgba(194, 65, 12, 0.3);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1.25rem;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin-bottom: 1.25rem;
        }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 700;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.45rem;
        }
        .form-control-custom {
            width: 100%;
            height: 44px;
            padding: 0 0.95rem;
            border: 1.5px solid #cbd5e1;
            border-radius: 8px;
            font-size: 0.92rem;
            font-family: inherit;
            color: #0f172a;
            background: #ffffff;
            outline: none;
            transition: all 0.15s ease;
            box-sizing: border-box;
        }
        .form-control-custom:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
        }
        .form-hint {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 0.35rem;
        }
        .btn-save-coupon {
            height: 44px;
            padding: 0 1.75rem;
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            color: #ffffff;
            border: none;
            border-radius: 8px;
            font-size: 0.92rem;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            transition: all 0.2s ease;
        }
        .btn-save-coupon:hover {
            background: linear-gradient(135deg, #4338ca 0%, #3730a3 100%);
            transform: translateY(-1px);
        }
        .btn-stop-coupon {
            padding: 6px 14px;
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-stop-coupon:hover {
            background: #fecaca;
        }
        .btn-activate-coupon {
            padding: 6px 14px;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .btn-activate-coupon:hover {
            background: #d1fae5;
        }
        .badge-status-active {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .badge-status-inactive {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            background: #f1f5f9;
            color: #64748b;
            border: 1px solid #cbd5e1;
        }
        /* Standalone Guarantee for Pagination & SVG chevrons */
        .table-pagination-area, .pagination-wrapper { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem; }
        .pagination, ul.pagination { display: inline-flex !important; gap: 4px !important; list-style: none !important; margin: 0 !important; padding: 0 !important; align-items: center !important; }
        .pagination .page-item { list-style: none !important; margin: 0 !important; padding: 0 !important; }
        .pagination .page-item .page-link, .pagination .page-item span.page-link, .pagination .page-item a.page-link {
            display: inline-flex !important; align-items: center !important; justify-content: center !important;
            min-width: 32px !important; height: 32px !important; padding: 0 8px !important; font-size: 0.8rem !important;
            font-weight: 600 !important; color: #475569 !important; background: #ffffff !important; border: 1px solid #cbd5e1 !important;
            border-radius: 6px !important; text-decoration: none !important; line-height: 1 !important;
        }
        .pagination .page-item .page-link:hover, .pagination .page-item a.page-link:hover { background: #f1f5f9 !important; border-color: #94a3b8 !important; color: #0f172a !important; }
        .pagination .page-item.active .page-link, .pagination .page-item.active span.page-link { background: #0284c7 !important; color: #ffffff !important; border-color: #0284c7 !important; font-weight: 700 !important; }
        .pagination .page-item.disabled .page-link, .pagination .page-item.disabled span.page-link { color: #94a3b8 !important; background: #f8fafc !important; border-color: #e2e8f0 !important; cursor: not-allowed !important; pointer-events: none !important; }
        .pagination svg, .pagination-wrapper svg, nav svg, svg.w-5, svg.h-5 { width: 14px !important; height: 14px !important; max-width: 14px !important; max-height: 14px !important; display: inline-block !important; vertical-align: middle !important; }
        .pagination-wrapper nav > div:first-child:not(:only-child) { display: none !important; }
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
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="top-bar-right">
                @include('layouts.role-switcher')
            </div>
        </header>

        <!-- Main Dashboard Body -->
        <main class="main-body">
            <!-- Flash Message Alerts -->
            @if(session('status'))
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 0.95rem 1.4rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                    <span>{{ session('status') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:#1e40af; font-size:1.2rem; cursor:pointer;">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div style="background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 0.95rem 1.4rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
                    <span>⚠️ {{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" style="background:none; border:none; color:#991b1b; font-size:1.2rem; cursor:pointer;">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div style="background: #fff7ed; border: 1px solid #fed7aa; color: #9a3412; padding: 0.95rem 1.4rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.88rem;">
                    <strong>Please check the form inputs:</strong>
                    <ul style="margin: 0.4rem 0 0 1.25rem; padding: 0;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 1. PAGE HEADER -->
            <div style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="font-size: 1.6rem; font-weight: 800; color: #0f172a; margin: 0 0 0.35rem 0; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🎟️</span> Discount Coupon Master
                    </h1>
                    <p style="font-size: 0.85rem; color: #64748b; margin: 0;">
                        Configure admission fee concessions in <code>finance.couponinfo</code>. Switching the active coupon automatically stops the previous one.
                    </p>
                </div>

                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <span style="font-size: 0.75rem; font-weight: 700; color: #0369a1; background: #e0f2fe; padding: 4px 10px; border-radius: 20px; border: 1px solid #bae6fd;">
                        Academic Year: 2027-28
                    </span>
                    <span style="font-size: 0.75rem; font-weight: 700; color: #4338ca; background: #e0e7ff; padding: 4px 10px; border-radius: 20px; border: 1px solid #c7d2fe;">
                        Campus: VAD (Vadlamudi)
                    </span>
                </div>
            </div>

            <!-- 2. CURRENT ACTIVE COUPON BANNER -->
            @if($activeCoupon)
                <div class="active-banner">
                    <div style="display: flex; align-items: center; gap: 1.25rem;">
                        <div style="width: 58px; height: 58px; background: rgba(255,255,255,0.2); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.85rem;">
                            🎟️
                        </div>
                        <div>
                            <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                <span style="background: #ffffff; color: #065f46; font-size: 0.72rem; font-weight: 800; padding: 2px 8px; border-radius: 12px; text-transform: uppercase;">
                                    ● Currently Active
                                </span>
                                <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; opacity: 0.9;">
                                    Code: <strong>{{ $activeCoupon->couponsno ?: '27EARLYB100' }}</strong>
                                </span>
                            </div>
                            <div style="font-size: 1.4rem; font-weight: 800; letter-spacing: -0.01em;">
                                {{ $activeCoupon->couponname }}
                            </div>
                            <div style="font-size: 0.825rem; opacity: 0.9; margin-top: 0.2rem;">
                                Active since: <strong>{{ $activeCoupon->coupstartdate ?: 'Today' }}</strong> • Base Fee: ₹1,200 &rarr; Net Payable: <strong>₹{{ number_format(max(0, 1200 - (float)$activeCoupon->discountamount), 2) }}</strong>
                            </div>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1.5rem; flex-wrap: wrap;">
                        <div style="text-align: right;">
                            <div style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; opacity: 0.85;">Concession Amount</div>
                            <div style="font-size: 2rem; font-weight: 800; font-family: 'JetBrains Mono', monospace; line-height: 1;">
                                -₹{{ number_format((float)$activeCoupon->discountamount, 2) }}
                            </div>
                        </div>

                        <form action="{{ route('crm.discountcoupon.stop', $activeCoupon->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to STOP this active coupon? Admission fee will revert to full base amount.');">
                            @csrf
                            <button type="submit" style="padding: 10px 18px; background: rgba(255,255,255,0.18); color: #ffffff; border: 1.5px solid rgba(255,255,255,0.4); border-radius: 8px; font-weight: 700; font-size: 0.85rem; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.18)'">
                                🛑 Stop Coupon
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="active-banner-empty">
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <div style="font-size: 2rem;">⚠️</div>
                        <div>
                            <div style="font-size: 1.15rem; font-weight: 800;">No Active Default Coupon</div>
                            <div style="font-size: 0.85rem; opacity: 0.95;">
                                There is currently no active default discount running in <code>finance.couponinfo</code>. All candidates pay the full base fee of ₹1,200.
                            </div>
                        </div>
                    </div>
                    <div style="font-size: 0.85rem; font-weight: 700; background: rgba(255,255,255,0.2); padding: 6px 12px; border-radius: 8px;">
                        Use form below to activate a coupon
                    </div>
                </div>
            @endif

            <!-- 3. CREATE & ACTIVATE NEW COUPON FORM CARD -->
            <div class="coupon-card">
                <div class="coupon-card-header">
                    <div>
                        <div class="coupon-card-title">
                            <span>✍️</span> Set / Change Discount Coupon
                        </div>
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.15rem;">
                            Saving this form will automatically <strong>stop</strong> the previous coupon and <strong>activate</strong> the new concession.
                        </div>
                    </div>

                    <div id="liveCalculationBadge" style="font-size: 0.8rem; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 5px 12px; border-radius: 8px; font-family: 'JetBrains Mono', monospace;">
                        Base: ₹1,200 - Concession: ₹500 = Net: ₹700
                    </div>
                </div>

                <div class="coupon-card-body">
                    <form action="{{ route('crm.discountcoupon.store') }}" method="POST" id="discountCouponForm" onsubmit="return confirmSaveNewCoupon();">
                        @csrf
                        <div class="form-grid">
                            <!-- 1. From Date -->
                            <div class="form-group">
                                <label for="coupstartdate">
                                    From Date *
                                </label>
                                <input type="date" 
                                       id="coupstartdate" 
                                       name="coupstartdate" 
                                       class="form-control-custom" 
                                       value="{{ old('coupstartdate', $todayDate) }}" 
                                       required>
                                <div class="form-hint">Date from which this discount is valid (defaults to today)</div>
                            </div>

                            <!-- 2. Coupon Name -->
                            <div class="form-group">
                                <label for="couponname">
                                    Coupon Name *
                                </label>
                                <input type="text" 
                                       id="couponname" 
                                       name="couponname" 
                                       class="form-control-custom" 
                                       placeholder="e.g. Early Bird Tier 1 Concession" 
                                       value="{{ old('couponname', $activeCoupon ? $activeCoupon->couponname : 'Early Bird Concession') }}" 
                                       required>
                                <div class="form-hint">Descriptive name shown on student fee receipt</div>
                            </div>

                            <!-- 3. Amount -->
                            <div class="form-group">
                                <label for="discountamount">
                                    Amount (₹) *
                                </label>
                                <input type="number" 
                                       id="discountamount" 
                                       name="discountamount" 
                                       class="form-control-custom" 
                                       placeholder="e.g. 500" 
                                       value="{{ old('discountamount', $activeCoupon ? (int)$activeCoupon->discountamount : 500) }}" 
                                       min="0" 
                                       max="1200" 
                                       step="50" 
                                       oninput="updateFeeCalculation(this.value)" 
                                       required>
                                <div class="form-hint">Discount deducted from ₹1,200 admission fee</div>
                            </div>

                            <!-- 4. Quota Count -->
                            <div class="form-group">
                                <label for="coupstdcount">
                                    Student Quota (coupstdcount)
                                </label>
                                <input type="number" 
                                       id="coupstdcount" 
                                       name="coupstdcount" 
                                       class="form-control-custom" 
                                       placeholder="e.g. 500" 
                                       value="{{ old('coupstdcount', 500) }}" 
                                       min="1">
                                <div class="form-hint">Maximum students eligible for this coupon</div>
                            </div>
                        </div>

                        <!-- Info Banner regarding auto-stop -->
                        <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px 16px; margin-bottom: 1.25rem; font-size: 0.825rem; color: #475569; display: flex; align-items: center; gap: 0.75rem;">
                            <span style="font-size: 1.25rem;">ℹ️</span>
                            <div>
                                <strong>System Behavior:</strong> When you click <strong>Save &amp; Activate Coupon</strong>, the database updates all previous active records in <code>finance.couponinfo</code> with <code>coupstatus = 'INACTIVE'</code> and <code>coupclosedate = From Date</code>. The new record becomes the sole <strong>ACTIVE</strong> default coupon for all new admissions.
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div style="display: flex; justify-content: flex-end; gap: 0.75rem; align-items: center;">
                            <button type="reset" style="padding: 0 1.25rem; height: 44px; background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-weight: 600; cursor: pointer;">
                                Reset Form
                            </button>
                            <button type="submit" class="btn-save-coupon">
                                <span>💾</span> Save &amp; Activate Coupon
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- 4. COUPON LEDGER & AUDIT HISTORY TABLE -->
            <div class="coupon-card">
                <div class="coupon-card-header">
                    <div>
                        <div class="coupon-card-title">
                            <span>📋</span> Coupon History &amp; Audit Ledger
                        </div>
                        <div style="font-size: 0.78rem; color: #64748b; margin-top: 0.15rem;">
                            All discount coupon revisions recorded in <code>finance.couponinfo</code> (Total: {{ number_format($totalCount) }} records)
                        </div>
                    </div>

                    <div style="display: flex; gap: 0.5rem;">
                        <span class="badge-status-active">
                            ● {{ $activeCount }} Active
                        </span>
                        <span class="badge-status-inactive">
                            ○ {{ $inactiveCount }} Inactive / Closed
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="crm-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Coupon Name &amp; Code</th>
                                <th>Concession Amount</th>
                                <th>From Date</th>
                                <th>Closed Date</th>
                                <th>Student Quota</th>
                                <th>Status</th>
                                <th>Logged By / Date</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($coupons as $coup)
                                @php
                                    $isActive = strtoupper(trim((string)$coup->coupstatus)) === 'ACTIVE';
                                @endphp
                                <tr style="{{ $isActive ? 'background: #f0fdf4;' : '' }}">
                                    <td>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; font-weight: 700; color: #64748b;">
                                            #{{ $coup->id }}
                                        </span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.88rem;">
                                            {{ $coup->couponname }}
                                        </div>
                                        <div style="font-size: 0.72rem; color: #64748b; font-family: 'JetBrains Mono', monospace; margin-top: 2px;">
                                            Code: <strong style="color: #4338ca;">{{ $coup->couponsno ?: 'DEFAULT' }}</strong> • Type: {{ $coup->coupontype ?? 'DEFAULT' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.95rem; font-weight: 800; color: {{ $isActive ? '#059669' : '#64748b' }};">
                                            ₹{{ number_format((float)$coup->discountamount, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.825rem; font-weight: 600; color: #1e293b;">
                                            {{ $coup->coupstartdate ?: '—' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-family: 'JetBrains Mono', monospace; font-size: 0.825rem; color: #64748b;">
                                            {{ $coup->coupclosedate ?: ($isActive ? 'Running' : '—') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span style="font-size: 0.8rem; color: #334155;">
                                            <strong>{{ $coup->coupcurrcount ?? 0 }}</strong> / {{ $coup->coupstdcount ?? ($coup->coupcount ?? 100) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($isActive)
                                            <span class="badge-status-active">
                                                ● ACTIVE
                                            </span>
                                        @else
                                            <span class="badge-status-inactive">
                                                ○ INACTIVE
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div style="font-size: 0.75rem; color: #64748b;">
                                            UID: <code>{{ $coup->log_userid ?: 'SYSTEM' }}</code>
                                        </div>
                                        <div style="font-size: 0.7rem; color: #94a3b8;">
                                            {{ $coup->log_timestamp ? date('d M Y, h:i A', strtotime($coup->log_timestamp)) : '—' }}
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        @if($isActive)
                                            <form action="{{ route('crm.discountcoupon.stop', $coup->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Stop this coupon?');">
                                                @csrf
                                                <button type="submit" class="btn-stop-coupon" title="Stop this coupon">
                                                    🛑 Stop
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('crm.discountcoupon.activate', $coup->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Activate this coupon? It will stop the currently active coupon.');">
                                                @csrf
                                                <button type="submit" class="btn-activate-coupon" title="Activate this coupon">
                                                    ✓ Activate
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 2.5rem; color: #94a3b8;">
                                        <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">🎟️</div>
                                        <div style="font-weight: 600;">No coupon records found in <code>finance.couponinfo</code>.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($coupons->hasPages())
                    <div class="table-pagination-area">
                        <div style="font-size: 0.825rem; color: #64748b;">
                            Showing <strong>{{ $coupons->firstItem() ?? 0 }}</strong> to <strong>{{ $coupons->lastItem() ?? 0 }}</strong> of <strong>{{ number_format($coupons->total()) }}</strong> records
                        </div>
                        <div class="pagination-wrapper">
                            {{ $coupons->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
</div>

<script>
    function updateFeeCalculation(discountVal) {
        const base = 1200;
        const discount = Math.min(base, Math.max(0, parseFloat(discountVal) || 0));
        const net = Math.max(0, base - discount);
        const badge = document.getElementById('liveCalculationBadge');
        if (badge) {
            badge.textContent = `Base: ₹1,200 - Concession: ₹${discount.toFixed(0)} = Net: ₹${net.toFixed(0)}`;
        }
    }

    function confirmSaveNewCoupon() {
        const name = document.getElementById('couponname').value.trim();
        const amount = document.getElementById('discountamount').value.trim();
        const fromDate = document.getElementById('coupstartdate').value.trim();
        return confirm(`Activate new coupon "${name}" with concession ₹${amount} starting ${fromDate}?\n\nNOTE: The previously active coupon will be stopped.`);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const amtInput = document.getElementById('discountamount');
        if (amtInput) {
            updateFeeCalculation(amtInput.value);
        }
    });
</script>
</body>
</html>
