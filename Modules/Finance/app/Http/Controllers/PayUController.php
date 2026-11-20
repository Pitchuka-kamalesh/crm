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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class PayUController extends Controller
{
    public function __construct(protected PayUService $payuService) {}

    public function initiate(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) $cleanPhone = substr($cleanPhone, -10);

        $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
            ->when($cleanPhone, function ($q) use ($cleanPhone) {
                $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
            })->first();

        $applino = $crmStudent?->applinano ?? ($usercode ?: ('VU2026' . ($cleanPhone ? substr($cleanPhone, -6) : rand(100000, 999999))));
        $name = substr(trim((string) preg_replace('/[^a-zA-Z0-9\s]/', '', $crmStudent?->name ?? $user?->name ?? 'Candidate')), 0, 50) ?: 'Candidate';
        $email = filter_var(trim((string) ($crmStudent?->emailid ?? '')), FILTER_VALIDATE_EMAIL) ? $crmStudent->emailid : 'admissions@vignan.ac.in';
        $phone = strlen($cleanPhone) >= 10 ? substr($cleanPhone, -10) : '9999999999';

        $breakdown = CouponService::calculateFeeBreakdown($applino, CouponService::DEFAULT_ADMISSION_FEE);
        $amount = max(0.00, (float) $breakdown['net_payable']);
        $txnid = 'TXN' . date('ymdHis') . rand(1000, 9999);

        if ($amount <= 0) {
            $this->recordSuccessfulPayment($request, $applino, $txnid, 'COUPON_' . $txnid, 0.00, 'Coupon', 'Coupon', '100% Concession', $breakdown, $breakdown['def_coupon'] ?? '', $breakdown['per_coupon'] ?? '');
            return redirect()->to(URL::temporarySignedRoute('payu.receipt', now()->addMinutes(30), ['txnid' => $txnid]));
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
            'udf1'        => $applino, // CRITICAL: Pass applino to callback
            'udf2'        => (string) ($crmStudent?->id ?? '1'),
            'udf3'        => (string) $amount,
            'udf4'        => $breakdown['def_coupon'] ?? '',
            'udf5'        => $breakdown['per_coupon'] ?? '',
        ];
        $params['hash'] = $this->payuService->generateHash($params);

        // Pre-record initiated transaction
        PayDetail::firstOrCreate(['txncrmid' => $txnid], [
            'feeid' => (PayDetail::max('feeid') ?? 0) + 1,
            'applino' => $applino,
            'feepayable' => (float) ($breakdown['base_fee'] ?? CouponService::DEFAULT_ADMISSION_FEE),
            'defcoupon' => $breakdown['def_coupon'] ?: '',
            'defcouponamount' => (float) ($breakdown['def_discount'] ?? 0.00),
            'percoupon' => $breakdown['per_coupon'] ?: '',
            'percouponamount' => (float) ($breakdown['per_discount'] ?? 0.00),
            'totalcouponamount' => (float) ($breakdown['total_discount'] ?? 0.00),
            'paidamount' => 0.00,
            'paytype' => 'PayU (Initiated)',
            'txndatetime' => now(),
            'txnstatus' => 'initiated',
            'txnremark' => 'Initiated checkout',
            'log_userid' => $applino,
            'log_ipaddress' => $request->ip(),
        ]);

        return view('payments.payu_checkout', [
            'params' => $params, 'gatewayUrl' => $this->payuService->getGatewayUrl(),
            'isProduction' => $this->payuService->isProduction(), 'applino' => $applino,
            'name' => $name, 'email' => $email, 'phone' => $phone, 'amount' => $amount,
            'txnid' => $txnid, 'breakdown' => $breakdown
        ]);
    }

    public function handleResponse(Request $request): RedirectResponse
    {
        $payload = $request->all();
        Log::info('[PayU Callback]', $payload);

        $status = strtolower((string) $request->input('status', ''));
        $txnid = (string) $request->input('txnid', '');
        $applino = (string) $request->input('udf1', '');
        $amount = (float) $request->input('amount', 0);

        // 1. Mandatory Hash Check
        if (empty($payload['hash']) || !$this->payuService->verifyHash($payload)) {
            Log::warning('[PayU] Hash mismatch', ['txnid' => $txnid]);
            return redirect()->route('dashboards.index', ['step' => 4])->withErrors(['error' => 'Security signature mismatch.']);
        }

        // 2. Find DB Record
        $payDetail = PayDetail::where('txncrmid', $txnid)->first();
        if (!$payDetail) abort(404, 'Transaction not found.');

        // 3. Idempotency (Prevent duplicate receipts)
        if (in_array($payDetail->txnstatus, ['success', 'failed'])) {
            return $payDetail->txnstatus === 'success' 
                ? redirect()->to(URL::temporarySignedRoute('payu.receipt', now()->addMinutes(30), ['txnid' => $txnid]))
                : redirect()->route('dashboards.index', ['step' => 4])->withErrors(['error' => 'Already failed.']);
        }

        if ($status === 'success') {
            $breakdown = CouponService::calculateFeeBreakdown($applino, CouponService::DEFAULT_ADMISSION_FEE);
            $this->recordSuccessfulPayment($request, $applino, $txnid, (string)$request->input('mihpayid'), $amount, 'Online', 'Online', 'Bank Ref: '.$request->input('bank_ref_no'), $breakdown, $request->input('udf4'), $request->input('udf5'));
            
            return redirect()->to(URL::temporarySignedRoute('payu.receipt', now()->addMinutes(30), ['txnid' => $txnid]));
        }

        $payDetail->update(['txnstatus' => 'failed', 'txnerror' => $request->input('error_Message', 'Cancelled')]);
        return redirect()->route('dashboards.index', ['step' => 4])->withErrors(['error' => 'Payment failed.']);
    }

    public function receipt(Request $request): View|RedirectResponse
    {
        $txnid = (string) $request->query('txnid', '');
        $payDetail = PayDetail::where('txncrmid', $txnid)->firstOrFail();
        return $this->renderReceipt($payDetail->applino, $payDetail);
    }

    public function payu_receipt(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $student = CrmStudentRegistration::where('applinano', $usercode)->first();
        $applino = $student?->applinano ?? $usercode;

        $payDetail = PayDetail::where('applino', $applino)->where('txnstatus', 'success')->orderBy('feeid', 'desc')->first();
        if (!$payDetail) return redirect()->route('dashboards.index', ['step' => 4])->withErrors(['error' => 'No successful payment.']);

        return $this->renderReceipt($applino, $payDetail);
    }


    private function renderReceipt(string $applino, PayDetail $payDetail): View
    {
        $student = CrmStudentRegistration::with(['program', 'branch'])->where('applinano', $applino)->first();
        $receipt = ReceiptDetail::where('applino', $applino)->orderBy('id', 'desc')->first();
        
        return view('payments.receipt', [
            'user' => Auth::user() ?? $student, 'student' => $student, 'applino' => $applino,
            'crmId' => $student?->crmid ?: \App\Services\CrmIdService::getOrGenerateByAppli($applino),
            'programName' => $student?->program ? ($student->program->program_short_name) : 'B.Tech',
            'payDetail' => $payDetail, 'receipt' => $receipt,
            'baseFee' => (float)($payDetail->feepayable ?? 0), 'defDiscount' => (float)($payDetail->defcouponamount ?? 0),
            'perDiscount' => (float)($payDetail->percouponamount ?? 0), 'paidAmount' => (float)($payDetail->paidamount ?? 0),
        ]);
    }

    private function recordSuccessfulPayment(Request $request, string $applino, string $txnid, string $mihpayid, float $amount, string $payType, string $receiptPayType, string $txnRemark, array $breakdown, string $defCoupon,  $perCoupon): string
    {
        $baseFee = (float) ($breakdown['base_fee'] ?? 0);
        $defDiscount = (float) ($breakdown['def_discount'] ?? 0);
        $perDiscount = (float) ($breakdown['per_discount'] ?? 0);
        $totalDiscount = (float) ($breakdown['total_discount'] ?? 0);

        // 1. Update PayDetail
        $payDetail = PayDetail::where('txncrmid', $txnid)->first();
        $payAttrs = [
            'feepayable' => $baseFee, 'defcoupon' => $defCoupon ?: '', 'defcouponamount' => $defDiscount,
            'percoupon' => $perCoupon ?: '', 'percouponamount' => $perDiscount, 'totalcouponamount' => $totalDiscount,
            'paidamount' => $amount, 'paytype' => $payType, 'txnpayuid' => $mihpayid,
            'txnstatus' => 'success', 'txnremark' => $txnRemark, 'log_timestamp' => now(),
        ];
        if ($payDetail) { $payDetail->update($payAttrs); } 
        else {
            PayDetail::create(array_merge($payAttrs, [
                'feeid' => (PayDetail::max('feeid') ?? 0) + 1, 'applino' => $applino, 'txndatetime' => now(),
                'txncrmid' => $txnid, 'log_userid' => $applino, 'log_ipaddress' => $request->ip(),
            ]));
        }

        // 2. Record Coupon Usage
        CouponService::recordCouponUsage($defCoupon, $perCoupon);

        // 3. Create Receipt
        $receiptNo = 'REC-2026-' . date('Ymd') . '-' . rand(1000, 9999);
        ReceiptDetail::create([
            'id' => (ReceiptDetail::max('id') ?? 0) + 1, 'applino' => $applino, 'receiptno' => $receiptNo,
            'paytype' => $receiptPayType, 'feepayable' => $amount, 'defcouponamount' => $defDiscount,
            'percouponamount' => $perDiscount, 'totalcouponamount' => $totalDiscount,
            'txndatetime' => now(), 'receiptdatetime' => now(), 'log_userid' => $applino,
            'log_ipaddress' => $request->ip(), 'log_timestamp' => now(),
        ]);

        // 4. CRITICAL FIX: Update Student via $applino, NOT Auth::user()
        $studentReg = CrmStudentRegistration::where('applinano', $applino)->first();
        if ($studentReg) {
            $studentReg->update(['stage1' => 'paid', 'stage4' => 'paid', 'log_timestamp' => now()]);
        } else {
            CrmStudentRegistration::create([
                'applinano' => $applino, 'name' => $request->input('firstname', 'Candidate'),
                'phoneno' => $request->input('phone', '9999999999'), 'emailid' => $request->input('email', 'admissions@vignan.ac.in'),
                'campuscode' => 'VAD', 'programlevel' => 'UG', 'std_status' => 'Admitted',
                'stage1' => 'paid', 'stage4' => 'paid', 'stage2' => 'pending',
                'log_userid' => $applino, 'log_ipaddress' => $request->ip(), 'log_timestamp' => now(),
            ]);
        }

        return $receiptNo;
    }
}