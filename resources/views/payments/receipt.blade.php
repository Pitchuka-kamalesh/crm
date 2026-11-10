<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Fee Receipt | VFSTR Admissions</title>
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: #f1f5f9;
            color: #0f172a;
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        .receipt-wrap {
            max-width: 780px;
            margin: 0 auto;
        }
        .status-banner {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 0.9rem 1.25rem;
            border-radius: 12px;
            font-size: 0.86rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .receipt-card {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 35px rgba(15, 23, 42, 0.08);
        }
        .receipt-header {
            background: #0f172a;
            color: #ffffff;
            padding: 1.75rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .univ-brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .univ-brand img {
            height: 52px;
            width: auto;
            background: #ffffff;
            border-radius: 10px;
            padding: 4px;
        }
        .univ-title {
            font-size: 1.15rem;
            font-weight: 800;
            letter-spacing: -0.3px;
        }
        .univ-sub {
            font-size: 0.74rem;
            color: #94a3b8;
            margin-top: 2px;
        }
        .paid-stamp {
            background: #10b981;
            color: #052e16;
            padding: 0.45rem 1rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .receipt-body {
            padding: 2rem;
        }
        .meta-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 1px dashed #cbd5e1;
            margin-bottom: 1.5rem;
        }
        @media (max-width: 600px) {
            .meta-grid { grid-template-columns: 1fr; }
        }
        .meta-item-label {
            font-size: 0.72rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .meta-item-val {
            font-size: 0.92rem;
            font-weight: 700;
            color: #0f172a;
            margin-top: 2px;
        }
        .mono {
            font-family: 'JetBrains Mono', monospace;
        }
        .fee-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
            font-size: 0.88rem;
        }
        .fee-table th {
            background: #f8fafc;
            text-align: left;
            padding: 0.75rem 1rem;
            font-size: 0.74rem;
            text-transform: uppercase;
            color: #475569;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }
        .fee-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }
        .fee-table td.right, .fee-table th.right {
            text-align: right;
        }
        .discount-row td {
            color: #059669;
            font-weight: 600;
        }
        .total-row td {
            background: #f0fdf4;
            font-weight: 800;
            font-size: 1.05rem;
            color: #065f46;
            border-top: 2px solid #86efac;
            border-bottom: 2px solid #86efac;
        }
        .receipt-footer-note {
            font-size: 0.75rem;
            color: #64748b;
            background: #f8fafc;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            border: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .actions-bar {
            margin-top: 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }
        .btn-secondary {
            background: #ffffff;
            color: #334155;
            border: 1.5px solid #cbd5e1;
            padding: 0.65rem 1.25rem;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
        }
        .btn-secondary:hover { background: #f8fafc; color: #0f172a; }
        .btn-primary {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            padding: 0.7rem 1.45rem;
            border-radius: 10px;
            font-size: 0.88rem;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }
        .btn-primary:hover { box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35); }
        @media print {
            body { background: #ffffff; padding: 0; }
            .status-banner, .actions-bar { display: none !important; }
            .receipt-card { box-shadow: none; border: 1px solid #94a3b8; }
        }
    </style>
</head>
<body>
<div class="receipt-wrap">
    @if(session('status'))
        <div class="status-banner">
            <span>{{ session('status') }}</span>
            <a href="{{ route('dashboards.index', ['step' => 2]) }}" style="color: #065f46; font-weight: 800; text-decoration: underline; white-space: nowrap;">Continue Application &rarr;</a>
        </div>
    @endif

    <div class="receipt-card">
        <div class="receipt-header">
            <div class="univ-brand">
                <img src="/images/vignan-logo.png" alt="VFSTR">
                <div>
                    <div class="univ-title">VIGNAN'S FOUNDATION FOR SCIENCE, TECHNOLOGY &amp; RESEARCH</div>
                    <div class="univ-sub">(Deemed to be University) &bull; Official Admission Fee Receipt</div>
                </div>
            </div>
            <span class="paid-stamp">✓ Fee Paid &amp; Verified</span>
        </div>

        <div class="receipt-body">
            <div class="meta-grid">
                <div>
                    <div class="meta-item-label">Official Receipt No.</div>
                    <div class="meta-item-val mono">{{ $receipt?->receiptno ?? ('REC-2026-' . ($applino ?: 'VU')) }}</div>
                </div>
                <div>
                    <div class="meta-item-label">Payment Date &amp; Time</div>
                    <div class="meta-item-val">{{ ($receipt?->receiptdatetime ?? $payDetail?->txndatetime ?? now())->format('d M Y, h:i A') }}</div>
                </div>
                <div>
                    <div class="meta-item-label">Candidate Name</div>
                    <div class="meta-item-val">{{ $student?->name ?? $user?->name ?? 'Applicant' }}</div>
                </div>
                <div>
                    <div class="meta-item-label">Application # &amp; CRM ID</div>
                    <div class="meta-item-val mono">{{ $applino }} @if($crmId) &bull; {{ $crmId }} @endif</div>
                </div>
                <div>
                    <div class="meta-item-label">Mobile Number</div>
                    <div class="meta-item-val">+91 {{ $student?->phoneno ?? $user?->phnno ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="meta-item-label">Program Applied</div>
                    <div class="meta-item-val">{{ $programName ?: 'B.Tech Admission' }}</div>
                </div>
                <div>
                    <div class="meta-item-label">Transaction ID (CRM)</div>
                    <div class="meta-item-val mono">{{ $payDetail?->txncrmid ?? 'VERIFIED' }}</div>
                </div>
                <div>
                    <div class="meta-item-label">Gateway Ref / Payment Mode</div>
                    <div class="meta-item-val mono">{{ $payDetail?->txnpayuid ?? 'CONFIRMED' }} ({{ $payDetail?->paytype ?? $receipt?->paytype ?? 'PayU' }})</div>
                </div>
            </div>

            <table class="fee-table">
                <thead>
                    <tr>
                        <th>Fee Description</th>
                        <th class="right">Amount (INR)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Admission Registration Fee (Stage 1)</td>
                        <td class="right">₹ {{ number_format($baseFee, 2) }}</td>
                    </tr>
                    @if($defDiscount > 0)
                        <tr class="discount-row">
                            <td>Default Early Bird Concession ({{ $payDetail?->defcoupon ?: '27EARLYB100' }})</td>
                            <td class="right">- ₹ {{ number_format($defDiscount, 2) }}</td>
                        </tr>
                    @endif
                    @if($perDiscount > 0)
                        <tr class="discount-row">
                            <td>Personal Coupon Concession ({{ $payDetail?->percoupon ?: '' }})</td>
                            <td class="right">- ₹ {{ number_format($perDiscount, 2) }}</td>
                        </tr>
                    @endif
                    <tr class="total-row">
                        <td>Total Net Amount Paid</td>
                        <td class="right">₹ {{ number_format($paidAmount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="receipt-footer-note">
                <span>This is a computer-generated official payment receipt from Vignan CRM Portal. No physical signature required.</span>
                <span class="mono">Status: {{ strtoupper($payDetail?->txnstatus ?? 'SUCCESS') }}</span>
            </div>
        </div>
    </div>

    <div class="actions-bar">
        <div style="display: flex; gap: 0.65rem; flex-wrap: wrap;">
            <button type="button" onclick="window.print()" class="btn-secondary">
                <span>🖨️ Print / Save PDF</span>
            </button>
            <a href="{{ route('dashboards.index', ['step' => 1]) }}" class="btn-secondary">
                <span>&larr; View Portal Steps</span>
            </a>
        </div>
        <div style="display: flex; gap: 0.65rem; align-items: center;">
            <a href="{{ route('dashboards.index', ['step' => 2]) }}" class="btn-primary">
                <span>Continue to Step 2: Personal Profile &rarr;</span>
            </a>
        </div>
    </div>
</div>
</body>
</html>

