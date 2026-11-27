<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrmStudentRegistration;
use App\Models\PayDetail;
use App\Models\ReceiptDetail;
use Modules\Finance\Services\CouponService;
use Modules\Finance\Services\PayUService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class PayUController extends Controller
{
    public function __construct(protected PayUService $payuService) {}

    /* ─────────────────────────────────────────────
     |  1. INITIATE (auth + role:5)
     ───────────────────────────────────────────── */
    public function initiate(Request $request): View|RedirectResponse
    {
        $user       = Auth::user();
        $usercode   = (string) ($user?->usercode ?? '');
        $cleanPhone = preg_replace('/\D/', '', (string) ($user?->phnno ?? ''));
        if (strlen($cleanPhone) > 10) $cleanPhone = substr($cleanPhone, -10);

        // Exact match only — never pick another applicant's record
        $crmStudent = CrmStudentRegistration::where('applinano', $usercode)->first()
            ?? ($cleanPhone ? CrmStudentRegistration::where('phoneno', $cleanPhone)->first() : null);

        if (!$crmStudent) {
            return redirect()->route('dashboards.index', ['step' => 4])
                ->withErrors(['error' => 'Registration not found.']);
        }

        $applino = (string) $crmStudent->applinano;
        $name    = substr(trim((string) preg_replace('/[^a-zA-Z0-9\s]/', '', $crmStudent->name ?? $user?->name ?? 'Candidate')), 0, 50) ?: 'Candidate';
        $email   = filter_var(trim((string) ($crmStudent->emailid ?? '')), FILTER_VALIDATE_EMAIL) ?: 'admissions@vignan.ac.in';
        $phone   = strlen($cleanPhone) >= 10 ? $cleanPhone : '9999999999';

        $breakdown = CouponService::calculateFeeBreakdown($applino, CouponService::DEFAULT_ADMISSION_FEE);
        $amount    = max(0.00, round((float) $breakdown['net_payable'], 2));
        $txnid     = 'TXN' . date('ymdHis') . random_int(1000, 9999);
        $defCoupon = (string) ($breakdown['def_coupon'] ?? '');
        $perCoupon = (string) ($breakdown['per_coupon'] ?? '');

        // 100% concession — no gateway
        if ($amount <= 0) {
            DB::transaction(fn () => $this->recordSuccessfulPayment(
                $request, $applino, $txnid, 'COUPON_' . $txnid, 0.00,
                'Coupon', 'Coupon', '100% Concession', $breakdown, $defCoupon, $perCoupon
            ));
            return redirect()->to($this->receiptUrl($txnid));
        }

        $params = [
            'key'         => $this->payuService->getKey(),
            'txnid'       => $txnid,
            'amount'      => number_format($amount, 2, '.', ''),
            'productinfo' => 'VFSTR_ADMISSION_STAGE1',
            'firstname'   => $name,
            'lastname'    => '',
            'email'       => $email,
            'phone'       => $phone,
            'surl'        => route('payu.response'),
            'furl'        => route('payu.response'),
            'udf1'        => $applino,
            'udf2'        => (string) $crmStudent->id,
            'udf3'        => (string) $amount,
            'udf4'        => $defCoupon,
            'udf5'        => $perCoupon,
        ];
        $params['hash'] = $this->payuService->generateHash($params);

        // Pre-record initiated transaction
        PayDetail::firstOrCreate(['txncrmid' => $txnid], [
            'feeid'             => (PayDetail::max('feeid') ?? 0) + 1,
            'applino'           => $applino,
            'feepayable'        => (float) ($breakdown['base_fee'] ?? CouponService::DEFAULT_ADMISSION_FEE),
            'defcoupon'         => $defCoupon,
            'defcouponamount'   => (float) ($breakdown['def_discount'] ?? 0),
            'percoupon'         => $perCoupon,
            'percouponamount'   => (float) ($breakdown['per_discount'] ?? 0),
            'totalcouponamount' => (float) ($breakdown['total_discount'] ?? 0),
            'paidamount'        => 0.00,
            'paytype'           => 'PayU (Initiated)',
            'txndatetime'       => now(),
            'txnstatus'         => 'initiated',
            'txnremark'         => 'Initiated checkout',
            'log_userid'        => $applino,
            'log_ipaddress'     => $request->ip(),
        ]);

        return view('payments.payu_checkout', [
            'params'       => $params,
            'gatewayUrl'   => $this->payuService->getGatewayUrl(),
            'isProduction' => $this->payuService->isProduction(),
            'applino'      => $applino,
            'name'         => $name,
            'email'        => $email,
            'phone'        => $phone,
            'amount'       => $amount,
            'txnid'        => $txnid,
            'breakdown'    => $breakdown,
        ]);
    }

    /* ─────────────────────────────────────────────
     |  2. PAYU CALLBACK (public, no session, no CSRF)
     ───────────────────────────────────────────── */
    public function handleResponse(Request $request): RedirectResponse
    {
        $payload = $request->all();
        $status  = strtolower((string) $request->input('status', ''));
        $txnid   = (string) $request->input('txnid', '');
        $applino = (string) $request->input('udf1', '');
        $amount  = round((float) $request->input('amount', 0), 2);

        Log::info('[PayU Callback]', ['txnid' => $txnid, 'status' => $status, 'mihpayid' => $request->input('mihpayid')]);

        // 1. Verify PayU hash
        if (empty($payload['hash']) || !$this->payuService->verifyHash($payload)) {
            Log::warning('[PayU] Hash mismatch', ['txnid' => $txnid]);
            return redirect()->route('dashboards.index', ['step' => 4]);
        }

        $result = DB::transaction(function () use ($request, $status, $txnid, $applino, $amount) {
            // 2. Find + lock transaction
            $payDetail = PayDetail::where('txncrmid', $txnid)->lockForUpdate()->first();
            if (!$payDetail) {
                Log::error('[PayU] Transaction not found', ['txnid' => $txnid]);
                return 'notfound';
            }

            // 3. Duplicate callback
            if (in_array($payDetail->txnstatus, ['success', 'failed'], true)) {
                return $payDetail->txnstatus;
            }

            // 4. Failed / cancelled
            if ($status !== 'success') {
                $payDetail->update([
                    'txnstatus' => 'failed',
                    'txnerror'  => (string) $request->input('error_Message', 'Cancelled'),
                    'log_timestamp' => now(),
                ]);
                return 'failed';
            }

            // 5. Amount + applino must match what initiate() stored
            $expected = round((float) $payDetail->feepayable - (float) $payDetail->totalcouponamount, 2);
            if ($amount !== $expected || $payDetail->applino !== $applino) {
                Log::warning('[PayU] Amount/applino mismatch', [
                    'txnid' => $txnid, 'expected' => $expected, 'got' => $amount,
                    'db_applino' => $payDetail->applino, 'udf1' => $applino,
                ]);
                $payDetail->update(['txnstatus' => 'failed', 'txnerror' => 'Amount/applino mismatch', 'log_timestamp' => now()]);
                return 'failed';
            }

            // 6. Record success
            $breakdown = CouponService::calculateFeeBreakdown($applino, CouponService::DEFAULT_ADMISSION_FEE);
            $this->recordSuccessfulPayment(
                $request, $applino, $txnid,
                (string) $request->input('mihpayid', ''), $amount,
                'Online', 'Online',
                'Bank Ref: ' . (string) $request->input('bank_ref_num', ''),
                $breakdown,
                (string) $request->input('udf4', ''),
                (string) $request->input('udf5', '')
            );
            return 'success';
        });

        if ($result === 'notfound') abort(404, 'Transaction not found.');

        return $result === 'success'
            ? redirect()->to($this->receiptUrl($txnid))
            : redirect()->route('dashboards.index', ['step' => 4]);
    }

    /* ─────────────────────────────────────────────
     |  3. SIGNED RECEIPT (auth + signed:relative)
     ───────────────────────────────────────────── */
    public function receipt(Request $request): View|RedirectResponse
    {
        $payDetail = PayDetail::where('txncrmid', (string) $request->query('txnid', ''))
            ->where('applino', (string) (Auth::user()?->usercode ?? ''))
            ->where('txnstatus', 'success')
            ->firstOrFail();

        return $this->renderReceipt($payDetail->applino, $payDetail);
    }

    /* ─────────────────────────────────────────────
     |  4. MY RECEIPT (auth) — permanent link
     ───────────────────────────────────────────── */
    public function payu_receipt(Request $request): View|RedirectResponse
    {
        $usercode = (string) (Auth::user()?->usercode ?? '');
        $student  = CrmStudentRegistration::where('applinano', $usercode)->first();
        $applino  = (string) ($student?->applinano ?? $usercode);

        $payDetail = PayDetail::where('applino', $applino)
            ->where('txnstatus', 'success')
            ->orderByDesc('feeid')
            ->first();

        if (!$payDetail) {
            return redirect()->route('dashboards.index', ['step' => 4])
                ->withErrors(['error' => 'No successful payment.']);
        }

        return $this->renderReceipt($applino, $payDetail);
    }

    /* ─────────────────────────────────────────────
     |  Helpers
     ───────────────────────────────────────────── */
    private function receiptUrl(string $txnid): string
    {
        return URL::temporarySignedRoute('payu.receipt', now()->addMinutes(30), ['txnid' => $txnid], absolute: false);
    }

    private function renderReceipt(string $applino, PayDetail $payDetail): View
    {
        $student = CrmStudentRegistration::with(['program', 'branch'])->where('applinano', $applino)->first();
        $receipt = ReceiptDetail::where('applino', $applino)->orderByDesc('id')->first();

        return view('payments.receipt', [
            'user'        => Auth::user() ?? $student,
            'student'     => $student,
            'applino'     => $applino,
            'crmId'       => $student?->crmid ?: \App\Services\CrmIdService::getOrGenerateByAppli($applino),
            'programName' => $student?->program?->program_short_name ?? 'B.Tech',
            'payDetail'   => $payDetail,
            'receipt'     => $receipt,
            'baseFee'     => (float) ($payDetail->feepayable ?? 0),
            'defDiscount' => (float) ($payDetail->defcouponamount ?? 0),
            'perDiscount' => (float) ($payDetail->percouponamount ?? 0),
            'paidAmount'  => (float) ($payDetail->paidamount ?? 0),
        ]);
    }

    private function recordSuccessfulPayment(
        Request $request,
        string $applino,
        string $txnid,
        string $mihpayid,
        float $amount,
        string $payType,
        string $receiptPayType,
        string $txnRemark,
        array $breakdown,
        string $defCoupon,
        string $perCoupon
    ): string {
        $baseFee       = (float) ($breakdown['base_fee'] ?? 0);
        $defDiscount   = (float) ($breakdown['def_discount'] ?? 0);
        $perDiscount   = (float) ($breakdown['per_discount'] ?? 0);
        $totalDiscount = (float) ($breakdown['total_discount'] ?? 0);

        // 1. Update / create PayDetail
        $payAttrs = [
            'feepayable'        => $baseFee,
            'defcoupon'         => $defCoupon,
            'defcouponamount'   => $defDiscount,
            'percoupon'         => $perCoupon,
            'percouponamount'   => $perDiscount,
            'totalcouponamount' => $totalDiscount,
            'paidamount'        => $amount,
            'paytype'           => $payType,
            'txnpayuid'         => $mihpayid,
            'txnstatus'         => 'success',
            'txnremark'         => $txnRemark,
            'log_timestamp'     => now(),
        ];

        $payDetail = PayDetail::where('txncrmid', $txnid)->first();
        if ($payDetail) {
            $payDetail->update($payAttrs);
        } else {
            PayDetail::create(array_merge($payAttrs, [
                'feeid'         => (PayDetail::max('feeid') ?? 0) + 1,
                'applino'       => $applino,
                'txndatetime'   => now(),
                'txncrmid'      => $txnid,
                'log_userid'    => $applino,
                'log_ipaddress' => $request->ip(),
            ]));
        }

        // 2. Coupon usage
        CouponService::recordCouponUsage($defCoupon, $perCoupon);

        // 3. Receipt
        $receiptNo = 'REC-' . date('Y') . '-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        ReceiptDetail::create([
            'id'                => (ReceiptDetail::max('id') ?? 0) + 1,
            'applino'           => $applino,
            'receiptno'         => $receiptNo,
            'paytype'           => $receiptPayType,
            'feepayable'        => $amount,
            'defcouponamount'   => $defDiscount,
            'percouponamount'   => $perDiscount,
            'totalcouponamount' => $totalDiscount,
            'txndatetime'       => now(),
            'receiptdatetime'   => now(),
            'log_userid'        => $applino,
            'log_ipaddress'     => $request->ip(),
            'log_timestamp'     => now(),
        ]);

        // 4. Student stage — never auto-create an admitted student
        $studentReg = CrmStudentRegistration::where('applinano', $applino)->first();
        if ($studentReg) {
            $studentReg->update(['stage4' => 'paid', 'log_timestamp' => now()]);
        } else {
            Log::error('[PayU] Paid but no registration', ['applino' => $applino, 'txnid' => $txnid]);
        }

        return $receiptNo;
    }
}