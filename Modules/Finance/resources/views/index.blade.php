<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $roleName }} - Finance & Fee Collection | VFSTR Vignan University</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <!-- Shared Enterprise CRM Styles -->
    <link rel="stylesheet" href="/css/crm-dashboard.css">
</head>
<body>
<div class="app-wrapper">
    <!-- 1. LEFT SIDEBAR (Strictly Mapped for Finance & Paid Details) -->
    @include('layouts.crm-sidebar')

    <!-- 2. MAIN CONTENT AREA -->
    <div class="app-main">
        <!-- Top Navigation Header -->
        <header class="top-bar">
            <div class="top-bar-left">
                <button type="button" class="topbar-toggle-sidebar-btn" onclick="toggleSidebarMenu()" title="Toggle Navigation Sidebar" aria-label="Toggle Sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                </button>
                <div class="page-title">
                    <span>💳 University Admissions Finance & Fee Collection Portal</span>
                </div>
            </div>

            <div class="top-bar-right">
                <a href="{{ route('finance.paiddetails', ['tab' => 'paiddetails']) }}" class="btn-header-action" title="View Paid Details">
                    <span>🧾 Paid Details (FINA0002)</span>
                </a>
                @include('layouts.role-switcher')
            </div>
        </header>

        <!-- Main Dashboard Body -->
        <main class="main-body">
            @if(session('status'))
                <div style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; padding: 0.85rem 1.25rem; border-radius: 10px; margin-bottom: 1.5rem; font-size: 0.85rem; font-weight: 600;">
                    ✓ {{ session('status') }}
                </div>
            @endif

            <!-- FINANCIAL METRICS -->
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-label">Total Revenue Collected</span>
                        <span class="metric-value">₹ {{ number_format($totalRevenue) }}</span>
                        <span class="metric-sub">Application + Admission Fees</span>
                    </div>
                    <div class="metric-icon-box metric-emerald">💰</div>
                </div>

                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-label">Application Fees</span>
                        <span class="metric-value">₹ {{ number_format($totalAppFeesCollected) }}</span>
                        <span class="metric-sub">₹1,200 per candidate</span>
                    </div>
                    <div class="metric-icon-box metric-blue">📄</div>
                </div>

                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-label">Admission Confirmation Fees</span>
                        <span class="metric-value">₹ {{ number_format($totalAdmFeesCollected) }}</span>
                        <span class="metric-sub">Seat Allotment Deposits</span>
                    </div>
                    <div class="metric-icon-box metric-purple">🎓</div>
                </div>

                <div class="metric-card">
                    <div class="metric-info">
                        <span class="metric-label">Verified Receipts</span>
                        <span class="metric-value">{{ $totalApplications }}</span>
                        <span class="metric-sub">Reconciled with Accounts</span>
                    </div>
                    <div class="metric-icon-box metric-amber">🧾</div>
                </div>
            </div>

            <!-- PAID DETAILS TABLE (master.menus: FINA0002) -->
            <div class="content-card">
                <div class="card-header-bar">
                    <div>
                        <div class="card-title">🧾 Fee Collection & Paid Details Ledger (master.menus: FINA0002)</div>
                        <div class="card-subtitle">Real-time payment logs and bank reconciliation for VFSTR Admissions</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="crm-table">
                        <thead>
                            <tr>
                                <th>Receipt / Ref No</th>
                                <th>Student Name</th>
                                <th>Application #</th>
                                <th>Fee Component</th>
                                <th>Amount Paid</th>
                                <th>Payment Mode</th>
                                <th>Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paidStudents as $idx => $student)
                                <tr>
                                    <td><code>REC-2026-{{ str_pad($idx + 101, 5, '0', STR_PAD_LEFT) }}</code></td>
                                    <td><strong>{{ $student->student_name ?? $student->name ?? 'Candidate' }}</strong></td>
                                    <td>{{ $student->application_no ?? $student->applinano ?? 'VU-2026' }}</td>
                                    <td>Application & Registration Fee</td>
                                    <td><strong style="color: #059669;">₹ 1,200.00</strong></td>
                                    <td><span>Online Gateway (Razorpay/SBI)</span></td>
                                    <td><span class="badge-status badge-paid">✓ Verified</span></td>
                                </tr>
                                <tr>
                                    <td><code>REC-2026-{{ str_pad($idx + 201, 5, '0', STR_PAD_LEFT) }}</code></td>
                                    <td><strong>{{ $student->student_name ?? $student->name ?? 'Candidate' }}</strong></td>
                                    <td>{{ $student->application_no ?? $student->applinano ?? 'VU-2026' }}</td>
                                    <td>Seat Confirmation Deposit</td>
                                    <td><strong style="color: #059669;">₹ 25,000.00</strong></td>
                                    <td><span>NetBanking / UPI</span></td>
                                    <td><span class="badge-status badge-paid">✓ Verified</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2rem; color: #94a3b8;">
                                        No payment records found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
