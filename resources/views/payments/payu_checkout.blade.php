<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecting to PayU Secure Gateway | VFSTR Admissions</title>
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: #0f172a;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .checkout-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 20px;
            max-width: 520px;
            width: 100%;
            padding: 2.25rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .brand-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 1.5rem;
        }
        .brand-logo img {
            height: 48px;
            width: auto;
        }
        .portal-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }
        .portal-sub {
            font-size: 0.75rem;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid rgba(16, 185, 129, 0.2);
            border-top-color: #10b981;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin: 1.25rem auto;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .info-table {
            width: 100%;
            text-align: left;
            margin: 1.5rem 0;
            background: #0f172a;
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid #334155;
            font-size: 0.88rem;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 0.45rem 0;
            border-bottom: 1px solid #1e293b;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #94a3b8;
            font-weight: 500;
        }
        .info-value {
            color: #ffffff;
            font-weight: 700;
        }
        .amount-highlight {
            font-size: 1.35rem;
            color: #10b981;
            font-weight: 800;
        }
        .discount-badge {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            padding: 2px 7px;
            border-radius: 4px;
            font-size: 0.76rem;
            font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
        }
        .btn-proceed {
            display: block;
            width: 100%;
            padding: 12px;
            background: #10b981;
            color: #ffffff;
            border: none;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }
        .btn-proceed:hover {
            background: #059669;
        }
        .simulate-box {
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px dashed #334155;
            font-size: 0.78rem;
            color: #94a3b8;
        }
        .btn-simulate {
            background: #334155;
            color: #cbd5e1;
            border: 1px solid #475569;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: all 0.2s;
        }
        .btn-simulate:hover {
            background: #475569;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="checkout-card">
    <div class="brand-logo">
        <img src="/images/vignan-logo.png" alt="VFSTR Vignan University">
        <div style="text-align: left;">
            <div class="portal-name">VFSTR ADMISSIONS</div>
            <div class="portal-sub">PayU Hosted Checkout</div>
        </div>
    </div>

    <div class="spinner"></div>
    <h2 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin-bottom: 4px;">Redirecting to PayU Gateway...</h2>
    <p style="font-size: 0.8rem; color: #94a3b8;">Please do not refresh or close this window.</p>

    <div class="info-table">
        <div class="info-row">
            <span class="info-label">Application #</span>
            <span class="info-value"><code>{{ $applino }}</code></span>
        </div>
        <div class="info-row">
            <span class="info-label">Candidate</span>
            <span class="info-value">{{ $name }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Base Admission Fee</span>
            <span class="info-value">₹ {{ number_format($breakdown['base_fee'] ?? 25000, 2) }}</span>
        </div>

        @if(!empty($breakdown['def_discount']) && $breakdown['def_discount'] > 0)
            <div class="info-row">
                <span class="info-label">Default Concession <span class="discount-badge">{{ $breakdown['def_coupon'] }}</span></span>
                <span class="info-value" style="color: #34d399;">- ₹ {{ number_format($breakdown['def_discount'], 2) }}</span>
            </div>
        @endif

        @if(!empty($breakdown['per_discount']) && $breakdown['per_discount'] > 0)
            <div class="info-row">
                <span class="info-label">Personal Concession <span class="discount-badge">{{ $breakdown['per_coupon'] }}</span></span>
                <span class="info-value" style="color: #34d399;">- ₹ {{ number_format($breakdown['per_discount'], 2) }}</span>
            </div>
        @endif

        @if(!empty($breakdown['total_discount']) && $breakdown['total_discount'] > 0)
            <div class="info-row" style="background: rgba(16, 185, 129, 0.08); padding: 0.4rem 0.6rem; border-radius: 6px;">
                <span class="info-label" style="color: #34d399; font-weight: 700;">Total Concession</span>
                <span class="info-value" style="color: #34d399;">- ₹ {{ number_format($breakdown['total_discount'], 2) }}</span>
            </div>
        @endif

        <div class="info-row" style="align-items: center; padding-top: 0.75rem;">
            <span class="info-label" style="font-size: 0.95rem;">Net Fee Payable</span>
            <span class="amount-highlight">₹ {{ number_format($amount, 2) }}</span>
        </div>
    </div>

    <!-- Official PayU Form (auto-submitting to PayU Gateway) -->
    <form action="{{ $gatewayUrl }}" id="payu_checkout_form" method="POST">
        @foreach($params as $key => $val)
            <input type="hidden" name="{{ $key }}" value="{{ $val }}">
        @endforeach
        <button type="submit" class="btn-proceed">
            Proceed to PayU Payment &rarr;
        </button>
    </form>

    <!-- Sandbox Testing Simulator -->
    <div class="simulate-box">
        <div>Testing in Sandbox Mode (<code>{{ $gatewayUrl }}</code>)</div>
        <form action="{{ route('payu.response') }}" method="POST" style="display: inline-block;">
            @csrf
            <input type="hidden" name="status" value="success">
            <input type="hidden" name="txnid" value="{{ $txnid }}">
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" name="udf1" value="{{ $applino }}">
            <input type="hidden" name="udf4" value="{{ $params['udf4'] ?? '27EARLYB100' }}">
            <input type="hidden" name="udf5" value="{{ $params['udf5'] ?? '' }}">
            <input type="hidden" name="mihpayid" value="PAYU{{ date('His') }}{{ rand(1000, 9999) }}">
            <input type="hidden" name="bank_ref_no" value="BANK{{ date('Ymd') }}{{ rand(1000, 9999) }}">
            <input type="hidden" name="mode" value="UPI/NetBanking">
            <input type="hidden" name="firstname" value="{{ $name }}">
            <input type="hidden" name="email" value="{{ $email }}">
            <button type="submit" class="btn-simulate">
                ⚡ Simulate Instant PayU Sandbox Callback (Test Mode)
            </button>
        </form>
    </div>
</div>

<script>
    // Automatically submit to PayU after 1.5 seconds so candidate sees the invoice details
    setTimeout(function() {
        const form = document.getElementById('payu_checkout_form');
        if (form && !window.location.search.includes('nosubmit')) {
            form.submit();
        }
    }, 1500);
</script>

</body>
</html>
