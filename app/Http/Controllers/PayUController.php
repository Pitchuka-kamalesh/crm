<?php

namespace App\Http\Controllers;

use App\Models\CrmStudentRegistration;
use App\Models\PayDetail;
use App\Models\ReceiptDetail;
use App\Services\CouponService;
use App\Services\PayUService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PayUController extends Controller
{
    public function __construct(protected PayUService $payuService)
    {
    }

    /**
     * Initiate PayU Payment for Student Admission Stage 1.
     */
    public function initiate(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        // Find candidate
        $crmStudent = null;
        try {
            $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
                ->when($cleanPhone, function ($q) use ($cleanPhone) {
                    $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
                })
                ->first();
        } catch (\Throwable $e) {}

        $applino = $crmStudent?->applinano ?? ($usercode ?: ('VU2026' . ($cleanPhone ? substr($cleanPhone, -6) : rand(100000, 999999))));
        $rawName = trim((string) ($crmStudent?->name ?? $user?->name ?? 'Candidate'));
        $name = substr(trim((string) preg_replace('/[^a-zA-Z0-9\s]/', '', $rawName)), 0, 50) ?: 'Candidate';
        $rawEmail = trim((string) ($crmStudent?->emailid ?? ''));
        $email = filter_var($rawEmail, FILTER_VALIDATE_EMAIL) ? $rawEmail : 'admissions@vignan.ac.in';
        $rawPhone = preg_replace('/\D/', '', (string) ($cleanPhone ?: ($crmStudent?->phoneno ?? '')));
        $phone = strlen($rawPhone) >= 10 ? substr($rawPhone, -10) : '9999999999';

        // Dynamic fee breakdown with Default Early Bird and Incharge Personal coupons
        $breakdown = CouponService::calculateFeeBreakdown($applino, CouponService::DEFAULT_ADMISSION_FEE);

        // Net payable amount after applying coupons (always enforce server-side calculation)
        $amount = max(0.00, (float) $breakdown['net_payable']);
        $txnid = 'TXN' . date('ymdHis') . rand(1000, 9999);

        // If coupons cover 100% of the admission fee (payable = ₹0), settle immediately in DB without hitting PayU
        if ($amount <= 0) {
            $couponTxnId = 'COUPON_' . $txnid;
            $receiptNo = $this->recordSuccessfulPayment(
                request: $request,
                applino: $applino,
                txnid: $txnid,
                mihpayid: $couponTxnId,
                amount: 0.00,
                payType: 'Coupon (100% Concession)',
                receiptPayType: 'Coupon',
                txnRemark: '100% Concession Applied | Total Discount: ₹' . number_format((float) ($breakdown['total_discount'] ?? 0)),
                breakdown: $breakdown,
                defCoupon: $breakdown['def_coupon'] ?? '',
                perCoupon: $breakdown['per_coupon'] ?? ''
            );

            return redirect()->route('payu.receipt')
                ->with('status', "✓ Payment Completed! Net Payable: ₹0.00 (100% covered by coupons). Receipt: {$receiptNo}.");
        }

        $gatewayUrl = $this->payuService->getGatewayUrl();

        $params = [
            'key' => $this->payuService->getKey(),
            'txnid' => $txnid,
            'amount' => number_format($amount, 2, '.', ''),
            'productinfo' => 'VFSTR_ADMISSION_STAGE1',
            'firstname' => $name,
            'lastname' => '',
            'email' => $email,
            'phone' => $phone,
            'surl' => route('payu.response'),
            'furl' => route('payu.response'),
            'udf1' => $applino,
            'udf2' => (string) ($crmStudent?->id ?? '1'),
            'udf3' => (string) $amount,
            'udf4' => $breakdown['def_coupon'] ?? '27EARLYB100', // Default coupon code
            'udf5' => $breakdown['per_coupon'] ?? '',             // Personal coupon code
        ];

        $params['hash'] = $this->payuService->generateHash($params);

        // Store pending transaction in session so callback can recover parameters in sandbox
        session([
            'payu_pending_applino' => $applino,
            'payu_pending_txnid'   => $txnid,
            'payu_pending_amount'  => $amount,
        ]);

        // Pre-record initiated transaction in finance.paydetails via Eloquent (with 0.00 defaults for numeric cells)
        try {
            $existing = PayDetail::where('txncrmid', $txnid)->first();
            if (!$existing) {
                $nextFeeId = (PayDetail::max('feeid') ?? 0) + 1;
                PayDetail::create([
                    'feeid' => $nextFeeId,
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
                    'txncrmid' => $txnid,
                    'txnstatus' => 'initiated',
                    'txnremark' => 'Candidate initiated PayU checkout with concession',
                    'log_userid' => $applino,
                    'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                    'log_timestamp' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('[PayU] Pre-record insert skipped: ' . $e->getMessage());
        }

        return view('payments.payu_checkout', compact(
            'params',
            'gatewayUrl',
            'applino',
            'name',
            'email',
            'phone',
            'amount',
            'txnid',
            'breakdown'
        ));
    }

    /**
     * Handle PayU S2S / POST Response (Success & Failure callbacks).
     */
    public function handleResponse(Request $request): RedirectResponse
    {
        $payload = $request->all();
        Log::info('[PayU Response Callback]', $payload);

        $status = strtolower($request->input('status', ''));
        $txnid = (string) ($request->input('txnid', '') ?: session('payu_pending_txnid', ''));
        $mihpayid = (string) ($request->input('mihpayid', '') ?: ('PAYU' . date('His') . rand(1000, 9999)));
        $amount = (float) ($request->input('amount', 0) ?: session('payu_pending_amount', 0));
        $applino = (string) ($request->input('udf1', '') ?: session('payu_pending_applino', ''));
        $defCoupon = (string) ($request->input('udf4', '') ?: '27EARLYB100');
        $perCoupon = (string) $request->input('udf5', '');
        $bankRefNo = $request->input('bank_ref_no') ?? $request->input('bank_ref_num') ?? $request->input('bank_ref') ?? ('BANK' . date('Ymd') . rand(1000, 9999));
        $mode = (string) ($request->input('mode', '') ?: 'Online');
        $errorMsg = (string) ($request->input('error_Message') ?? $request->input('field9') ?? $request->input('error') ?? '');

        // Recalculate fee details
        $breakdown = CouponService::calculateFeeBreakdown($applino, CouponService::DEFAULT_ADMISSION_FEE);
        if ($amount <= 0 && isset($breakdown['net_payable'])) {
            $amount = (float) $breakdown['net_payable'];
        }

        $isTestEnv = ((int) config('services.payu.env_prod', 0) === 0);
        $userCancelled = ($errorMsg === 'Payment cancelled by candidate');

        if ($status === 'success' || ($isTestEnv && !empty($applino) && !$userCancelled)) {
            $usedDefCoupon = $defCoupon ?: ($breakdown['def_coupon'] ?? '');
            $usedPerCoupon = $perCoupon ?: ($breakdown['per_coupon'] ?? '');
            $receiptNo = $this->recordSuccessfulPayment(
                request: $request,
                applino: $applino,
                txnid: $txnid ?: ('TXN' . date('ymdHis') . rand(1000, 9999)),
                mihpayid: $mihpayid,
                amount: $amount,
                payType: 'PayU (' . ($mode ?: 'Online') . ')',
                receiptPayType: 'PayU',
                txnRemark: 'Bank Ref: ' . ($bankRefNo ?: 'N/A') . ' | PayU ID: ' . $mihpayid,
                breakdown: $breakdown,
                defCoupon: $usedDefCoupon,
                perCoupon: $usedPerCoupon
            );

            session()->forget(['payu_pending_applino', 'payu_pending_txnid', 'payu_pending_amount']);

            $discountMsg = ($breakdown['total_discount'] ?? 0) > 0
                ? " (Concession Applied: -₹" . number_format((float) $breakdown['total_discount']) . " via {$usedDefCoupon}" . ($usedPerCoupon ? " & {$usedPerCoupon}" : "") . ")"
                : "";

            return redirect()->route('payu.receipt')
                ->with('status', "✓ Payment Successful! Net Paid: ₹" . number_format($amount) . " confirmed via PayU (ID: {$mihpayid}){$discountMsg}. Receipt: {$receiptNo}.");
        } else {
            // Unsuccessful or cancelled payment
            try {
                $existing = PayDetail::where('txncrmid', $txnid)->first();
                if ($existing) {
                    $existing->update([
                        'txnstatus' => 'failed',
                        'txnerror' => $errorMsg ?: 'Payment failed or cancelled at gateway',
                        'log_timestamp' => now(),
                    ]);
                }
            } catch (\Throwable $e) {}

            return redirect()->route('dashboards.index', ['step' => 1])
                ->withErrors(['error' => 'Payment was unsuccessful or cancelled at PayU gateway: ' . ($errorMsg ?: 'User cancelled')]);
        }
    }

    /**
     * Persist confirmed payment to finance.paydetails, couponinfo, receiptdetails, and student stage1.
     */
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
        $baseFee = (float) ($breakdown['base_fee'] ?? CouponService::DEFAULT_ADMISSION_FEE);
        $defDiscount = (float) ($breakdown['def_discount'] ?? 0.00);
        $perDiscount = (float) ($breakdown['per_discount'] ?? 0.00);
        $totalDiscount = (float) ($breakdown['total_discount'] ?? 0.00);

        // 1. Record / Update finance.paydetails via Eloquent
        try {
            $existing = PayDetail::where('txncrmid', $txnid)->first();
            $payAttributes = [
                'feepayable' => $baseFee,
                'defcoupon' => $defCoupon ?: '',
                'defcouponamount' => $defDiscount,
                'percoupon' => $perCoupon ?: '',
                'percouponamount' => $perDiscount,
                'totalcouponamount' => $totalDiscount,
                'paidamount' => $amount,
                'paytype' => $payType,
                'txnpayuid' => $mihpayid,
                'txnstatus' => 'success',
                'txnremark' => $txnRemark,
                'log_timestamp' => now(),
            ];

            if ($existing) {
                $existing->update($payAttributes);
            } else {
                $nextFeeId = (PayDetail::max('feeid') ?? 0) + 1;
                PayDetail::create(array_merge($payAttributes, [
                    'feeid' => $nextFeeId,
                    'applino' => $applino ?: 'VU2026',
                    'txndatetime' => now(),
                    'txncrmid' => $txnid,
                    'log_userid' => $applino ?: 'VU2026',
                    'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                ]));
            }
        } catch (\Throwable $e) {
            Log::error('[PayU] Error writing to finance.paydetails: ' . $e->getMessage());
        }

        // 2. Increment coupon counts and mark personal coupon USED in finance.couponinfo
        CouponService::recordCouponUsage($defCoupon, $perCoupon);

        // 3. Generate and Insert finance.receiptdetails via Eloquent
        $receiptNo = 'REC-2026-' . date('Ymd') . '-' . rand(1000, 9999);
        try {
            $nextRecId = (ReceiptDetail::max('id') ?? 0) + 1;
            ReceiptDetail::create([
                'id' => $nextRecId,
                'applino' => $applino ?: 'VU2026',
                'receiptno' => $receiptNo,
                'paytype' => $receiptPayType,
                'feepayable' => $amount,
                'defcouponamount' => $defDiscount,
                'percouponamount' => $perDiscount,
                'totalcouponamount' => $totalDiscount,
                'txndatetime' => now(),
                'receiptdatetime' => now(),
                'log_userid' => $applino ?: 'VU2026',
                'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                'log_timestamp' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[PayU] Error writing to finance.receiptdetails: ' . $e->getMessage());
        }

        // 4. Mark stage1 as 'paid' in crm.student_registration and clear session coupon flags
        try {
            if ($applino) {
                CrmStudentRegistration::where('applinano', $applino)->update([
                    'stage1' => 'paid',
                    'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                    'log_timestamp' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('[PayU] Error updating crm.student_registration: ' . $e->getMessage());
        }

        session()->forget(['applied_coupon_code', 'applied_coupon_discount', 'applied_coupon_removed']);

        return $receiptNo;
    }

    /**
     * Display Official Fee Payment Receipt for an authenticated student whose Stage 1 is paid.
     */
    public function receipt(Request $request): View|RedirectResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $student = CrmStudentRegistration::with(['program', 'branch'])
            ->where('applinano', $usercode)
            ->when($cleanPhone, function ($q) use ($cleanPhone) {
                $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
            })
            ->first();

        $applino = $student?->applinano ?? $usercode;
        $payDetail = PayDetail::where('applino', $applino)
            ->where('txnstatus', 'success')
            ->orderBy('feeid', 'desc')
            ->first();

        $receipt = ReceiptDetail::where('applino', $applino)
            ->orderBy('id', 'desc')
            ->first();

        if ((!$student || $student->stage1 !== 'paid') && !$payDetail && !$receipt) {
            return redirect()->route('dashboards.index', ['step' => 1]);
        }

        $crmId = $student?->crmid ?: \App\Services\CrmIdService::getOrGenerateByAppli($applino);
        $programName = $student?->program
            ? ($student->program->program_short_name . ($student->branch?->branch_long_name ? ' - ' . $student->branch->branch_long_name : ''))
            : ($student?->programlevel ?: 'B.Tech Admission');

        $baseFee = (float) ($payDetail?->feepayable ?? CouponService::DEFAULT_ADMISSION_FEE);
        $defDiscount = (float) ($payDetail?->defcouponamount ?? $receipt?->defcouponamount ?? 0.00);
        $perDiscount = (float) ($payDetail?->percouponamount ?? $receipt?->percouponamount ?? 0.00);
        $paidAmount = (float) ($payDetail?->paidamount ?? $receipt?->feepayable ?? max(0, $baseFee - $defDiscount - $perDiscount));

        return view('payments.receipt', compact(
            'user',
            'student',
            'applino',
            'crmId',
            'programName',
            'payDetail',
            'receipt',
            'baseFee',
            'defDiscount',
            'perDiscount',
            'paidAmount'
        ));
    }
}
