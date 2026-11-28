<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Fee Checkout | VFSTR Applications</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: linear-gradient(135deg, #0b1329 0%, #1e293b 50%, #0f172a 100%);
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 2rem 1rem;
        }
        /* Single Column Unified Grid Card */
        .payu-single-card {
            background: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            max-width: 540px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 25px 65px -10px rgba(0, 0, 0, 0.45);
        }
        /* Card Top Brand Header */
        .payu-card-header {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #ffffff;
            padding: 1.75rem 2rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .brand-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-info img {
            height: 46px;
            width: auto;
            background: #ffffff;
            border-radius: 10px;
            padding: 4px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        .brand-title { font-size: 1.05rem; font-weight: 800; color: #ffffff; letter-spacing: -0.3px; }
        .brand-sub { font-size: 0.72rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .txn-chip {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.72rem;
            font-weight: 700;
            color: #38bdf8;
            background: rgba(56, 189, 248, 0.12);
            border: 1px solid rgba(56, 189, 248, 0.25);
            padding: 4px 9px;
            border-radius: 6px;
        }

        /* Card Main Body */
        .payu-card-body {
            padding: 1.75rem 2rem 2rem;
            background: #ffffff;
        }

        /* Hero Amount Banner */
        .amount-hero {
            background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
            border: 1.5px solid #a7f3d0;
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            position: relative;
        }
        .amount-hero-label {
            font-size: 0.75rem;
            font-weight: 700;
            color: #047857;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .amount-hero-val {
            font-size: 2.5rem;
            font-weight: 800;
            color: #065f46;
            margin: 4px 0 2px 0;
            font-family: 'JetBrains Mono', monospace;
            line-height: 1.1;
        }
        .amount-hero-sub {
            font-size: 0.76rem;
            color: #059669;
            font-weight: 500;
        }

        /* Fee & Candidate Breakdown List */
        .summary-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
            font-size: 0.825rem;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.45rem 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .summary-row:last-child {
            border-bottom: none;
            padding-top: 0.65rem;
            margin-top: 0.2rem;
            border-top: 1.5px solid #cbd5e1;
        }
        .sr-label { color: #64748b; font-weight: 500; }
        .sr-val { color: #0f172a; font-weight: 700; text-align: right; }
        .code-pill {
            font-family: 'JetBrains Mono', monospace;
            background: #ecfdf5;
            color: #047857;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: 700;
            border: 1px solid #a7f3d0;
        }

        /* Receipt Guarantee Notice */
        .receipt-notice {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 12px;
            padding: 0.85rem 1.15rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.8rem;
            color: #0369a1;
        }
        .receipt-notice-icon { font-size: 1.35rem; }

        /* PayU Official Submit Button */
        .btn-pay-now {
            width: 100%;
            min-height: 52px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 0.35rem 0.5rem;
            padding: 0.75rem 1.25rem;
            line-height: 1.25;
            transition: all 0.2s ease;
        }
        .btn-pay-now:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            box-shadow: 0 10px 25px -4px rgba(16, 185, 129, 0.55);
            transform: translateY(-1px);
        }
        .btn-pay-main {
            font-weight: 800;
        }
        .btn-pay-sub {
            font-weight: 600;
            font-size: 0.82rem;
            opacity: 0.95;
        }

        /* Test Simulator Button (Dev Only) */
        .simulator-box {
            margin-top: 1rem;
            padding-top: 0.85rem;
            border-top: 1px dashed #cbd5e1;
        }
        .btn-simulator {
            background: #f8fafc;
            border: 1.5px dashed #94a3b8;
            color: #475569;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.76rem;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            transition: all 0.15s ease;
        }
        .btn-simulator:hover {
            background: #f1f5f9;
            border-color: #64748b;
            color: #0f172a;
        }

        /* Footer Meta & Actions */
        .card-footer {
            margin-top: 1.25rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.78rem;
        }
        .btn-cancel-link {
            background: none;
            border: none;
            color: #dc2626;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.78rem;
        }
        .security-badge-strip {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.25rem;
            font-size: 0.72rem;
            color: #94a3b8;
        }

        /* Loading Spinner Overlay */
        .processing-overlay {
            display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.82); backdrop-filter: blur(6px);
            z-index: 9999; align-items: center; justify-content: center; flex-direction: column; color: #ffffff;
        }
        .processing-overlay.active { display: flex; }
        .spinner-ring {
            width: 54px; height: 54px; border: 4px solid rgba(16, 185, 129, 0.25); border-top-color: #10b981; border-radius: 50%; animation: spin 0.75s linear infinite; margin-bottom: 1.25rem;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Mobile Responsive Adjustments */
        @media (max-width: 640px) {
            body {
                align-items: flex-start;
                padding: 0.75rem 0.65rem 2.5rem;
                min-height: 100vh;
            }
            .payu-single-card {
                border-radius: 18px;
                box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.4);
                margin: 0 auto;
            }
            .payu-card-header {
                padding: 0.95rem 1rem 0.85rem;
                gap: 0.65rem;
            }
            .brand-info {
                gap: 8px;
            }
            .brand-info img {
                height: 36px;
                padding: 3px;
                border-radius: 8px;
            }
            .brand-title {
                font-size: 0.9rem;
                line-height: 1.2;
            }
            .brand-sub {
                font-size: 0.64rem;
                letter-spacing: 0.3px;
            }
            .txn-chip {
                font-size: 0.65rem;
                padding: 3px 7px;
                letter-spacing: 0.2px;
            }
            .payu-card-body {
                padding: 1rem 0.85rem 1.25rem;
            }
            .amount-hero {
                padding: 0.85rem 0.75rem;
                margin-bottom: 0.9rem;
                border-radius: 13px;
            }
            .amount-hero-label {
                font-size: 0.68rem;
                letter-spacing: 0.04em;
            }
            .amount-hero-val {
                font-size: 1.95rem;
                margin: 2px 0;
            }
            .amount-hero-sub {
                font-size: 0.72rem;
            }
            .summary-box {
                padding: 0.75rem 0.85rem;
                margin-bottom: 0.9rem;
                border-radius: 12px;
                font-size: 0.78rem;
            }
            .summary-row {
                padding: 0.4rem 0;
                gap: 0.5rem;
            }
            .sr-label {
                font-size: 0.78rem;
                flex: 1;
                min-width: 0;
                word-break: break-word;
            }
            .sr-val {
                font-size: 0.8rem;
                flex-shrink: 0;
            }
            .code-pill {
                font-size: 0.65rem;
                padding: 1px 5px;
            }
            .receipt-notice {
                padding: 0.65rem 0.75rem;
                margin-bottom: 1rem;
                border-radius: 10px;
                font-size: 0.74rem;
                line-height: 1.35;
                gap: 0.5rem;
                align-items: flex-start;
            }
            .receipt-notice-icon {
                font-size: 1.15rem;
                line-height: 1;
                margin-top: 1px;
            }
            .btn-pay-now {
                min-height: 48px;
                padding: 0.7rem 0.85rem;
                font-size: 0.92rem;
                border-radius: 10px;
                gap: 0.25rem 0.4rem;
            }
            .btn-pay-main {
                font-size: 0.94rem;
            }
            .btn-pay-sub {
                font-size: 0.75rem;
            }
            .simulator-box {
                margin-top: 0.75rem;
                padding-top: 0.65rem;
            }
            .btn-simulator {
                font-size: 0.72rem;
                padding: 8px 10px;
                line-height: 1.3;
                text-align: center;
            }
            .card-footer {
                margin-top: 0.85rem;
                font-size: 0.76rem;
            }
            .card-footer a, .card-footer button {
                padding: 4px 0;
            }
            .security-badge-strip {
                margin-top: 0.85rem;
                padding-top: 0.75rem;
                flex-wrap: wrap;
                gap: 0.35rem 0.65rem;
                font-size: 0.68rem;
            }
        }

        @media (max-width: 360px) {
            body {
                padding: 0.5rem 0.4rem 2rem;
            }
            .payu-card-header {
                padding: 0.85rem 0.75rem 0.75rem;
            }
            .payu-card-body {
                padding: 0.85rem 0.75rem 1rem;
            }
            .amount-hero-val {
                font-size: 1.75rem;
            }
            .btn-pay-main {
                font-size: 0.88rem;
            }
        }
    </style>
</head>
<body>

<!-- Unified Single Grid Card -->
<div class="payu-single-card">
    <!-- 1. Header -->
    <div class="payu-card-header">
        <div class="brand-info">
            <img src="/images/vignan-logo.png" alt="VFSTR">
            <div>
                <div class="brand-title">VIGNAN'S UNIVERSITY</div>
                <div class="brand-sub">Application Fee Settlement</div>
            </div>
        </div>
        <span class="txn-chip">TXN: {{ $txnid }}</span>
    </div>

    <!-- 2. Main Body -->
    <div class="payu-card-body">
        <!-- Hero Amount Banner -->
        <div class="amount-hero">
            <div class="amount-hero-label">Total Payable Amount</div>
            <div class="amount-hero-val">₹ {{ number_format($amount, 2) }}</div>
            <div class="amount-hero-sub">Stage 1 Application Registration Fee</div>
        </div>

        <!-- Candidate & Fee Breakdown -->
        <div class="summary-box">
            <div class="summary-row">
                <span class="sr-label">Candidate Name</span>
                <span class="sr-val">{{ $name }}</span>
            </div>
            <div class="summary-row">
                <span class="sr-label">Application Number</span>
                <span class="sr-val"><code style="font-family:'JetBrains Mono',monospace;">{{ $applino }}</code></span>
            </div>
            <div class="summary-row">
                <span class="sr-label">Registered Phone</span>
                <span class="sr-val">+91 {{ $phone }}</span>
            </div>
            <div class="summary-row">
                <span class="sr-label">Base Application Fee</span>
                <span class="sr-val">₹ {{ number_format($breakdown['base_fee'] ?? 1200, 2) }}</span>
            </div>

            @if(!empty($breakdown['def_discount']) && $breakdown['def_discount'] > 0)
                <div class="summary-row">
                    <span class="sr-label">Default Concession <span class="code-pill">{{ $breakdown['def_coupon'] }}</span></span>
                    <span class="sr-val" style="color: #059669;">- ₹ {{ number_format($breakdown['def_discount'], 2) }}</span>
                </div>
            @endif

            @if(!empty($breakdown['per_discount']) && $breakdown['per_discount'] > 0)
                <div class="summary-row">
                    <span class="sr-label">Personal Concession <span class="code-pill">{{ $breakdown['per_coupon'] }}</span></span>
                    <span class="sr-val" style="color: #059669;">- ₹ {{ number_format($breakdown['per_discount'], 2) }}</span>
                </div>
            @endif

            <div class="summary-row">
                <span class="sr-label" style="font-weight: 700; color: #0f172a;">Net Settlement Amount</span>
                <span class="sr-val" style="font-size: 1.05rem; font-weight: 800; color: #065f46;">₹ {{ number_format($amount, 2) }}</span>
            </div>
        </div>

        <!-- Official Receipt Guarantee Notice -->
        <div class="receipt-notice">
            <span class="receipt-notice-icon"></span>
            <div>
                <strong>Official Application Fee Receipt (Form VFSTR-REC):</strong> Upon confirmation, your digitally-verified university fee receipt is instantly generated and linked permanently to your student profile.
            </div>
        </div>

        <!-- Direct Secure Application Fee Settlement Form (No external PayU gateway redirect; zero 504 errors) -->
        <form action="{{ $gatewayUrl }}" method="POST" id="payuGatewayForm" onsubmit="const b=document.getElementById('btnPayNow'); const o=document.getElementById('processingOverlay'); if(b){ b.disabled=true; } if(o){ o.classList.add('active'); }">
            @csrf
    @foreach($params as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach

            <button type="submit" id="btnPayNow" class="btn-pay-now">
                <span class="btn-pay-main">💳 Confirm & Pay ₹{{ number_format($amount, 2) }}</span>
                <span class="btn-pay-sub">(Generate Official Receipt) &rarr;</span>
            </button>
        </form>

        {{-- 
            ENVIRONMENT RESTRICTION:
            The Test Simulator is STRICTLY HIDDEN in production (APP_ENV=production).
            It is ONLY rendered when running in local / testing development environments.
        --}}
        @if(app()->environment('local', 'testing') && config('app.debug'))
            <div class="simulator-box">
                <form action="{{ route('payu.response') }}" method="POST" id="payuCompleteForm">
                    @csrf
                    <input type="hidden" name="status" value="success">
                    <input type="hidden" name="txnid" value="{{ $txnid }}">
                    <input type="hidden" name="amount" value="{{ $amount }}">
                    <input type="hidden" name="udf1" value="{{ $applino }}">
                    <input type="hidden" name="udf4" value="{{ $params['udf4'] ?? '27EARLYB100' }}">
                    <input type="hidden" name="udf5" value="{{ $params['udf5'] ?? '' }}">
                    <input type="hidden" name="mihpayid" value="DEV{{ date('His') }}{{ rand(1000, 9999) }}">
                    <input type="hidden" name="bank_ref_no" value="BANK{{ date('Ymd') }}{{ rand(1000, 9999) }}">
                    <input type="hidden" name="mode" value="Dev Simulator">
                    <input type="hidden" name="firstname" value="{{ $name }}">
                    <input type="hidden" name="email" value="{{ $email }}">

                    <button type="submit" class="btn-simulator" title="Available only in local development">
                        <span>⚡ Test Simulator: Mark as Paid without Real Gateway (Dev Only)</span>
                    </button>
                </form>
            </div>
        @endif

        <form action="{{ route('payu.response') }}" method="POST" id="payuCancelForm" style="display: none;">
            @csrf
            <input type="hidden" name="status" value="failure">
            <input type="hidden" name="txnid" value="{{ $txnid }}">
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" name="udf1" value="{{ $applino }}">
            <input type="hidden" name="error_Message" value="Payment cancelled by candidate">
        </form>

        <!-- Footer Actions -->
        <div class="card-footer">
            <a href="{{ route('dashboards.index', ['step' => 4]) }}" style="color: #64748b; text-decoration: none; font-weight: 600;">
                &larr; Back to Fee Summary
            </a>
            <button type="button" class="btn-cancel-link" onclick="if(confirm('Are you sure you want to cancel payment?')){ document.getElementById('payuCancelForm').submit(); }">
                Cancel Payment
            </button>
        </div>

        <!-- Security Badges -->
        <div class="security-badge-strip">
            <span>🔒 256-Bit SSL TLS</span>
            <span>•</span>
            <span>🏛️ Official VFSTR Portal</span>
            <span>•</span>
            <span>✅ Instant Verified Receipt</span>
        </div>
    </div>
</div>

<div class="processing-overlay" id="processingOverlay">
    <div class="spinner-ring"></div>
    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 4px;">Processing Application Fee Settlement...</h3>
    <p style="font-size: 0.84rem; color: #cbd5e1;">Generating digitally-verified fee receipt and unlocking Stage 5</p>
</div>

</body>
</html>
