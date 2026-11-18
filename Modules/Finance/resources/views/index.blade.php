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
                <button type="button" class="topbar-toggle-sidebar-btn" onclick="toggleSidebarMenu()" title="Open Navigation Menu" aria-label="Toggle Sidebar">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="6" x2="20" y2="6"/>
                        <line x1="4" y1="12" x2="20" y2="12"/>
                        <line x1="4" y1="18" x2="20" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="top-bar-right">
                <a href="{{ route('finance.paiddetails', ['tab' => 'paiddetails']) }}" class="btn-header-action" title="View Paid Details" style="display: inline-flex; align-items: center; gap: 0.45rem;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                        <line x1="16" y1="13" x2="8" y2="13"/>
                        <line x1="16" y1="17" x2="8" y2="17"/>
                    </svg>
                    <span>Paid Details</span>
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

            <!-- FINANCIAL METRICS (COMPACT SINGLE ROW, SINGLE LINE) -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
                <!-- Card 1: Total Revenue Collected (Single Line) -->
                <div class="metric-card" style="padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="width: 38px; height: 38px; border-radius: 9px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="6" width="20" height="12" rx="2"></rect>
                                <circle cx="12" cy="12" r="2"></circle>
                                <path d="M6 12h.01M18 12h.01"></path>
                            </svg>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap;">Total Revenue Collected</span>
                            <span style="font-size: 0.68rem; font-weight: 700; color: #059669; background: #ecfdf5; padding: 2px 7px; border-radius: 12px; border: 1px solid #a7f3d0; white-space: nowrap;">Real-time</span>
                        </div>
                    </div>
                    <div style="font-size: 1.45rem; font-weight: 800; color: #0f172a; font-family: 'JetBrains Mono', monospace; white-space: nowrap;">
                        ₹ {{ number_format($totalRevenue, 2) }}
                    </div>
                </div>

                <!-- Card 2: Verified Paid Students (Single Line) -->
                <div class="metric-card" style="padding: 0.75rem 1.25rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem; border-radius: 10px; border: 1px solid #e2e8f0; background: #ffffff; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03);">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="width: 38px; height: 38px; border-radius: 9px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <polyline points="16 11 18 13 22 9"></polyline>
                            </svg>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.04em; white-space: nowrap;">Verified Paid Students</span>
                            <span style="font-size: 0.68rem; font-weight: 700; color: #2563eb; background: #eff6ff; padding: 2px 7px; border-radius: 12px; border: 1px solid #bfdbfe; white-space: nowrap;">Reconciled</span>
                        </div>
                    </div>
                    <div style="font-size: 1.45rem; font-weight: 800; color: #0f172a; font-family: 'JetBrains Mono', monospace; white-space: nowrap;">
                        {{ $totalPaidStudents }}
                    </div>
                </div>
            </div>

            <!-- PAID DETAILS TABLE -->
            <div class="content-card">
                <div class="card-header-bar" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 1.15rem 1.5rem; border-bottom: 1px solid #e2e8f0;">
                    <div class="card-title" style="font-size: 1.1rem; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 0.5rem; margin: 0;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--theme-accent, #2563eb);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        <span>Paid Details Ledger</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div class="filter-count-badge" style="font-size: 0.8rem; font-weight: 700; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.4rem 0.85rem; border-radius: 20px;">
                            Verified Students: <strong id="paidCountDisplay">{{ $totalPaidStudents }}</strong>
                        </div>
                        <div style="position: relative;">
                            <input type="text" id="financeSearchInput" placeholder="Search by name, app no, receipt..." onkeyup="filterFinanceTable()" style="padding: 0.5rem 0.85rem 0.5rem 2.1rem; font-size: 0.825rem; border: 1px solid #cbd5e1; border-radius: 8px; width: 250px; outline: none; background: #ffffff;">
                            <svg style="position: absolute; left: 0.75rem; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none;" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8"></circle>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="crm-table" id="financeTable">
                        <thead>
                            <tr>
                                <th style="white-space: nowrap;">Receipt / Ref No</th>
                                <th style="white-space: nowrap;">Student Name</th>
                                <th style="white-space: nowrap;">Application #</th>
                                <th style="white-space: nowrap;">Fee Component</th>
                                <th style="white-space: nowrap;">Program / Branch</th>
                                <th style="white-space: nowrap;">Amount Paid</th>
                                <th style="white-space: nowrap;">Payment Mode</th>
                                <th style="white-space: nowrap;">Payment Date</th>
                                <th style="white-space: nowrap;">Payment Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paidStudents as $idx => $student)
                                @php
                                    $appNo = (string)($student->applinano ?? $student->application_no ?? 'N/A');
                                    $name = $student->name ?? $student->student_name ?? 'Candidate';
                                    $payRecord = $payDetails->get($appNo);
                                    $receiptRecord = $receiptDetails->get($appNo);

                                    $paidAmt = 1200.00;
                                    if ($payRecord && (float)($payRecord->paidamount ?? 0) > 0) {
                                        $paidAmt = (float)$payRecord->paidamount;
                                    } elseif ($receiptRecord && (float)($receiptRecord->feepayable ?? 0) > 0) {
                                        $paidAmt = (float)$receiptRecord->feepayable;
                                    }

                                    $receiptNo = $receiptRecord?->receiptno 
                                        ?? $payRecord?->txncrmid 
                                        ?? $payRecord?->txnpayuid 
                                        ?? 'REC-VERIFIED';

                                    $feeComponent = ($paidAmt >= 10000) 
                                        ? 'Admission & Seat Confirmation Deposit' 
                                        : 'Application & Registration Fee';

                                    $paymentMode = $payRecord?->paytype ?? $receiptRecord?->paytype ?? 'Online Gateway (PayU)';
                                    
                                    $txnDateRaw = $payRecord?->txndatetime 
                                        ?? $receiptRecord?->receiptdatetime 
                                        ?? $receiptRecord?->txndatetime 
                                        ?? $payRecord?->log_timestamp 
                                        ?? $student->log_timestamp;

                                    $txnDateFormatted = 'Recorded';
                                    if ($txnDateRaw) {
                                        try {
                                            $txnDateFormatted = \Carbon\Carbon::parse($txnDateRaw)->format('d M Y, h:i A');
                                        } catch (\Throwable $e) {
                                            $txnDateFormatted = (string)$txnDateRaw;
                                        }
                                    }

                                    $programName = data_get($student, 'branch.branch_long_name')
                                        ?? data_get($student, 'branch.branch_short_name')
                                        ?? data_get($student, 'program.program_long_name')
                                        ?? 'B.Tech Program';

                                    $studentPhone = data_get($student, 'phoneno') ?: (data_get($student, 'mobile') ?: data_get($student, 'phone'));
                                @endphp
                                <tr class="finance-row">
                                    <td style="white-space: nowrap;">
                                        <code style="font-size: 0.82rem; background: #f8fafc; padding: 0.3rem 0.65rem; border-radius: 6px; border: 1px solid #e2e8f0; color: #0f172a; white-space: nowrap; word-break: keep-all; display: inline-block; font-family: 'JetBrains Mono', monospace;">{{ $receiptNo }}</code>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <div style="font-weight: 700; color: #1e293b;">{{ $name }}</div>
                                        @if(!empty($studentPhone))
                                            <div style="font-size: 0.75rem; color: #64748b;">📞 {{ $studentPhone }}</div>
                                        @endif
                                    </td>
                                    <td style="white-space: nowrap;"><span style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 600; color: #334155;">{{ $appNo }}</span></td>
                                    <td style="white-space: nowrap;">
                                        <span style="font-size: 0.825rem; font-weight: 600; color: #334155; white-space: nowrap;">{{ $feeComponent }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; font-size: 0.85rem; color: #334155;">{{ $programName }}</div>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <strong style="color: #059669; font-size: 0.95rem; font-family: 'JetBrains Mono', monospace;">₹ {{ number_format($paidAmt, 2) }}</strong>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <span style="font-size: 0.825rem; color: #475569; display: inline-flex; align-items: center; gap: 0.35rem;">
                                            <span style="display: inline-block; width: 6px; height: 6px; background: #10b981; border-radius: 50%;"></span>
                                            {{ $paymentMode }}
                                        </span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <span style="font-size: 0.8rem; color: #64748b;">{{ $txnDateFormatted }}</span>
                                    </td>
                                    <td style="white-space: nowrap;">
                                        <span class="badge-status badge-paid" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; padding: 0.25rem 0.65rem; border-radius: 20px; font-weight: 700; font-size: 0.75rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                            Verified
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" style="text-align: center; padding: 3.5rem 1.5rem; color: #64748b;">
                                        <div style="width: 52px; height: 52px; margin: 0 auto 0.75rem; border-radius: 12px; background: #f1f5f9; color: #94a3b8; display: flex; align-items: center; justify-content: center;">
                                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                        </div>
                                        <div style="font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-bottom: 0.35rem;">No Verified Paid Students Found</div>
                                        <div style="font-size: 0.85rem; color: #94a3b8; max-width: 480px; margin: 0 auto; line-height: 1.5;">
                                            Only students who have completed payment (with verified payment status or transaction logs in <code>finance.paydetails</code>) are displayed here.
                                        </div>
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

<script>
function filterFinanceTable() {
    const input = document.getElementById('financeSearchInput');
    const filter = (input ? input.value : '').toLowerCase().trim();
    const rows = document.querySelectorAll('#financeTable tbody tr.finance-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        if (!filter || text.includes(filter)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    const display = document.getElementById('paidCountDisplay');
    if (display) {
        display.textContent = visibleCount;
    }
}
</script>
</body>
</html>
