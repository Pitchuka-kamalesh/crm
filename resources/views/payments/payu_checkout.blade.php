<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PayU Secure Checkout | VFSTR Admissions</title>
    <link rel="icon" type="image/png" href="/images/vignan-logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Plus Jakarta Sans', sans-serif; }
        body {
            background: #f1f5f9;
            color: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 1.5rem;
        }
        .payu-shell {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            max-width: 880px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
            display: grid;
            grid-template-columns: 340px 1fr;
        }
        @media (max-width: 820px) {
            .payu-shell {
                grid-template-columns: 1fr;
            }
        }
        .summary-pane {
            background: #0f172a;
            color: #f8fafc;
            padding: 2rem 1.75rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .brand-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
        }
        .brand-row img {
            height: 44px;
            width: auto;
            background: #ffffff;
            border-radius: 10px;
            padding: 4px;
        }
        .brand-title { font-size: 1rem; font-weight: 800; color: #ffffff; letter-spacing: -0.3px; }
        .brand-sub { font-size: 0.72rem; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
        .amount-banner {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.25);
            border-radius: 14px;
            padding: 1rem 1.15rem;
            margin-bottom: 1.35rem;
        }
        .amount-banner-label { font-size: 0.72rem; color: #94a3b8; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
        .amount-banner-val { font-size: 1.9rem; font-weight: 800; color: #10b981; margin-top: 2px; }
        .summary-list { display: flex; flex-direction: column; gap: 0.55rem; font-size: 0.8rem; }
        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 0.45rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .summary-item:last-child { border-bottom: none; }
        .s-label { color: #94a3b8; }
        .s-val { color: #f8fafc; font-weight: 600; text-align: right; }
        .code-pill {
            font-family: 'JetBrains Mono', monospace;
            background: rgba(16, 185, 129, 0.16);
            color: #34d399;
            padding: 1px 6px;
            border-radius: 4px;
            font-size: 0.72rem;
            font-weight: 700;
        }
        .ssl-footer {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.74rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .payu-badge { background: #10b981; color: #052e16; font-weight: 800; font-size: 0.72rem; padding: 3px 8px; border-radius: 6px; }
        .methods-pane { padding: 2rem; display: flex; flex-direction: column; justify-content: space-between; }
        .methods-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .methods-header h2 { font-size: 1.15rem; font-weight: 800; color: #0f172a; }
        .txn-chip { font-family: 'JetBrains Mono', monospace; font-size: 0.72rem; color: #475569; background: #f1f5f9; padding: 4px 8px; border-radius: 6px; }
        .method-tabs { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 1.25rem; }
        .method-tab {
            border: 1.5px solid #e2e8f0;
            background: #ffffff;
            border-radius: 10px;
            padding: 0.65rem 0.4rem;
            cursor: pointer;
            text-align: center;
            transition: all 0.15s ease;
            font-size: 0.75rem;
            font-weight: 700;
            color: #475569;
        }
        .method-tab:hover { border-color: #cbd5e1; background: #f8fafc; }
        .method-tab.is-active { border-color: #10b981; background: #ecfdf5; color: #065f46; box-shadow: 0 2px 8px rgba(16, 185, 129, 0.14); }
        .method-tab-icon { display: block; font-size: 1.15rem; margin-bottom: 2px; }
        .method-panel { display: none; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.15rem; margin-bottom: 1.25rem; }
        .method-panel.is-active { display: block; }
        .field-label { display: block; font-size: 0.76rem; font-weight: 700; color: #334155; margin-bottom: 0.35rem; }
        .field-input, .field-select {
            width: 100%; height: 40px; padding: 0 0.85rem; border: 1.5px solid #cbd5e1; border-radius: 8px;
            font-size: 0.84rem; background: #ffffff; color: #0f172a; outline: none;
        }
        .field-input:focus, .field-select:focus { border-color: #10b981; }
        .upi-apps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; margin-bottom: 0.9rem; }
        .upi-app-btn { border: 1px solid #cbd5e1; background: #ffffff; border-radius: 8px; padding: 0.55rem 0.25rem; font-size: 0.74rem; font-weight: 700; color: #1e293b; cursor: pointer; }
        .upi-app-btn.is-selected { border-color: #10b981; background: #ecfdf5; color: #047857; }
        .bank-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.5rem; margin-bottom: 0.85rem; }
        .bank-btn { border: 1px solid #cbd5e1; background: #ffffff; border-radius: 8px; padding: 0.6rem 0.5rem; font-size: 0.76rem; font-weight: 700; color: #1e293b; cursor: pointer; }
        .bank-btn.is-selected { border-color: #10b981; background: #ecfdf5; color: #047857; }
        .btn-pay-now {
            width: 100%; height: 46px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff; border: none; border-radius: 10px; font-size: 0.95rem; font-weight: 800; cursor: pointer;
            box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3); display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .btn-pay-now:hover { box-shadow: 0 6px 18px rgba(16, 185, 129, 0.38); }
        .footer-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 0.9rem; font-size: 0.76rem; }
        .btn-cancel-link { background: none; border: none; color: #dc2626; font-weight: 600; cursor: pointer; text-decoration: underline; font-size: 0.76rem; }
        .processing-overlay {
            display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.72); backdrop-filter: blur(4px);
            z-index: 9999; align-items: center; justify-content: center; flex-direction: column; color: #ffffff;
        }
        .processing-overlay.active { display: flex; }
        .spinner-ring { width: 52px; height: 52px; border: 4px solid rgba(16, 185, 129, 0.25); border-top-color: #10b981; border-radius: 50%; animation: spin 0.75s linear infinite; margin-bottom: 1rem; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="payu-shell">
    <div class="summary-pane">
        <div>
            <div class="brand-row">
                <img src="/images/vignan-logo.png" alt="VFSTR">
                <div>
                    <div class="brand-title">VIGNAN'S UNIVERSITY</div>
                    <div class="brand-sub">Admission Fee Checkout</div>
                </div>
            </div>

            <div class="amount-banner">
                <div class="amount-banner-label">Total Payable Now</div>
                <div class="amount-banner-val">₹ {{ number_format($amount, 2) }}</div>
            </div>

            <div class="summary-list">
                <div class="summary-item"><span class="s-label">Application #</span><span class="s-val"><code>{{ $applino }}</code></span></div>
                <div class="summary-item"><span class="s-label">Candidate Name</span><span class="s-val">{{ $name }}</span></div>
                <div class="summary-item"><span class="s-label">Mobile</span><span class="s-val">+91 {{ $phone }}</span></div>
                <div class="summary-item"><span class="s-label">Base Admission Fee</span><span class="s-val">₹ {{ number_format($breakdown['base_fee'] ?? 1200, 2) }}</span></div>

                @if(!empty($breakdown['def_discount']) && $breakdown['def_discount'] > 0)
                    <div class="summary-item">
                        <span class="s-label">Default <span class="code-pill">{{ $breakdown['def_coupon'] }}</span></span>
                        <span class="s-val" style="color: #34d399;">- ₹ {{ number_format($breakdown['def_discount'], 2) }}</span>
                    </div>
                @endif

                @if(!empty($breakdown['per_discount']) && $breakdown['per_discount'] > 0)
                    <div class="summary-item">
                        <span class="s-label">Personal <span class="code-pill">{{ $breakdown['per_coupon'] }}</span></span>
                        <span class="s-val" style="color: #34d399;">- ₹ {{ number_format($breakdown['per_discount'], 2) }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="ssl-footer">
            <span>🔒 256-Bit SSL Secured</span>
            <span class="payu-badge">PayU Checkout</span>
        </div>
    </div>

    <div class="methods-pane">
        <div>
            <div class="methods-header">
                <h2>Choose a Payment Method</h2>
                <span class="txn-chip">TXN: {{ $txnid }}</span>
            </div>

            <div class="method-tabs">
                <button type="button" class="method-tab is-active" onclick="selectPayMode('UPI', this)"><span class="method-tab-icon">📱</span>UPI / QR</button>
                <button type="button" class="method-tab" onclick="selectPayMode('CC', this)"><span class="method-tab-icon">💳</span>Card</button>
                <button type="button" class="method-tab" onclick="selectPayMode('NB', this)"><span class="method-tab-icon">🏦</span>NetBanking</button>
                <button type="button" class="method-tab" onclick="selectPayMode('WALLET', this)"><span class="method-tab-icon">👛</span>Wallets</button>
            </div>

            <div id="panel-UPI" class="method-panel is-active">
                <div class="field-label">Select UPI App</div>
                <div class="upi-apps">
                    <button type="button" class="upi-app-btn is-selected" onclick="pickOption(this, '.upi-app-btn')">GPay</button>
                    <button type="button" class="upi-app-btn" onclick="pickOption(this, '.upi-app-btn')">PhonePe</button>
                    <button type="button" class="upi-app-btn" onclick="pickOption(this, '.upi-app-btn')">Paytm</button>
                    <button type="button" class="upi-app-btn" onclick="pickOption(this, '.upi-app-btn')">BHIM</button>
                </div>
                <label class="field-label" for="upiIdInput">UPI ID / VPA</label>
                <input type="text" id="upiIdInput" class="field-input" value="{{ $phone }}@ybl" placeholder="mobilenumber@upi">
            </div>

            <div id="panel-CC" class="method-panel">
                <div style="margin-bottom: 0.75rem;">
                    <label class="field-label" for="cardNumInput">Card Number (Visa / Mastercard / RuPay)</label>
                    <input type="text" id="cardNumInput" class="field-input" value="4111 •••• •••• 1111" maxlength="19">
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                    <div><label class="field-label" for="cardExpInput">Expiry (MM/YY)</label><input type="text" id="cardExpInput" class="field-input" value="12/28" maxlength="5"></div>
                    <div><label class="field-label" for="cardCvvInput">CVV</label><input type="password" id="cardCvvInput" class="field-input" value="123" maxlength="4"></div>
                </div>
            </div>

            <div id="panel-NB" class="method-panel">
                <div class="field-label">Popular Indian Banks</div>
                <div class="bank-grid">
                    <button type="button" class="bank-btn is-selected" onclick="pickOption(this, '.bank-btn')">SBI</button>
                    <button type="button" class="bank-btn" onclick="pickOption(this, '.bank-btn')">HDFC Bank</button>
                    <button type="button" class="bank-btn" onclick="pickOption(this, '.bank-btn')">ICICI Bank</button>
                    <button type="button" class="bank-btn" onclick="pickOption(this, '.bank-btn')">Axis Bank</button>
                    <button type="button" class="bank-btn" onclick="pickOption(this, '.bank-btn')">Kotak</button>
                    <button type="button" class="bank-btn" onclick="pickOption(this, '.bank-btn')">Canara</button>
                </div>
            </div>

            <div id="panel-WALLET" class="method-panel">
                <div class="field-label">Select Mobile Wallet</div>
                <div class="bank-grid">
                    <button type="button" class="bank-btn is-selected" onclick="pickOption(this, '#panel-WALLET .bank-btn')">Paytm Wallet</button>
                    <button type="button" class="bank-btn" onclick="pickOption(this, '#panel-WALLET .bank-btn')">PhonePe</button>
                    <button type="button" class="bank-btn" onclick="pickOption(this, '#panel-WALLET .bank-btn')">Amazon Pay</button>
                </div>
            </div>
        </div>

        <div>
            <!-- Official Live PayU Gateway Form -->
            <form action="{{ $gatewayUrl }}" method="POST" id="payuGatewayForm" onsubmit="const b=document.getElementById('btnPayNow'); if(b){ b.disabled=true; b.innerHTML='⏳ Connecting to PayU Gateway...'; }">
                @foreach($params as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach

                <button type="submit" id="btnPayNow" class="btn-pay-now">
                    <span>🔒 Proceed to Official PayU Payment Gateway &rarr;</span>
                </button>
            </form>

            <!-- Test Mode Simulator Option -->
            <form action="{{ route('payu.response') }}" method="POST" id="payuCompleteForm" style="margin-top: 0.65rem;">
                @csrf
                <input type="hidden" name="status" value="success">
                <input type="hidden" name="txnid" value="{{ $txnid }}">
                <input type="hidden" name="amount" value="{{ $amount }}">
                <input type="hidden" name="udf1" value="{{ $applino }}">
                <input type="hidden" name="udf4" value="{{ $params['udf4'] ?? '27EARLYB100' }}">
                <input type="hidden" name="udf5" value="{{ $params['udf5'] ?? '' }}">
                <input type="hidden" name="mihpayid" value="PAYU{{ date('His') }}{{ rand(1000, 9999) }}">
                <input type="hidden" name="bank_ref_no" value="BANK{{ date('Ymd') }}{{ rand(1000, 9999) }}">
                <input type="hidden" name="mode" value="Demo/Test">
                <input type="hidden" name="firstname" value="{{ $name }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <button type="submit" style="background: none; border: 1px dashed #cbd5e1; color: #64748b; padding: 6px 12px; border-radius: 8px; font-size: 0.74rem; cursor: pointer; width: 100%;">
                    ⚡ Test Simulator: Mark as Paid without Real Gateway
                </button>
            </form>

            <form action="{{ route('payu.response') }}" method="POST" id="payuCancelForm" style="display: none;">
                @csrf
                <input type="hidden" name="status" value="failure">
                <input type="hidden" name="txnid" value="{{ $txnid }}">
                <input type="hidden" name="amount" value="{{ $amount }}">
                <input type="hidden" name="udf1" value="{{ $applino }}">
                <input type="hidden" name="error_Message" value="Payment cancelled by candidate">
            </form>

            <div class="footer-actions">
                <a href="{{ route('dashboards.index', ['step' => 1]) }}" style="color: #64748b; text-decoration: none; font-weight: 600;">
                    &larr; Back to Fee Summary
                </a>
                <button type="button" class="btn-cancel-link" onclick="document.getElementById('payuCancelForm').submit();">
                    Cancel Payment
                </button>
            </div>
        </div>
    </div>
</div>

<div class="processing-overlay" id="processingOverlay">
    <div class="spinner-ring"></div>
    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 4px;">Confirming PayU Payment...</h3>
    <p style="font-size: 0.82rem; color: #cbd5e1;">Generating your official receipt &amp; updating portal</p>
</div>

<script>
    function selectPayMode(mode, btn) {
        document.getElementById('selectedPayModeInput').value = mode;
        document.querySelectorAll('.method-tab').forEach(el => el.classList.remove('is-active'));
        if (btn) btn.classList.add('is-active');

        document.querySelectorAll('.method-panel').forEach(p => p.classList.remove('is-active'));
        const panel = document.getElementById('panel-' + mode);
        if (panel) panel.classList.add('is-active');
    }

    function pickOption(btn, selector) {
        document.querySelectorAll(selector).forEach(el => el.classList.remove('is-selected'));
        if (btn) btn.classList.add('is-selected');
    }

    function processPayUPayment() {
        const btn = document.getElementById('btnPayNow');
        const overlay = document.getElementById('processingOverlay');
        if (btn) btn.disabled = true;
        if (overlay) overlay.classList.add('active');

        setTimeout(() => {
            document.getElementById('payuCompleteForm').submit();
        }, 700);
    }
</script>

</body>
</html>
