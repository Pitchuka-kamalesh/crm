<?php

namespace App\Http\Controllers;

use App\Models\CrmStudentRegistration;
use App\Services\CouponService;
use App\Services\PayUService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $name = $crmStudent?->name ?? $user?->name ?? 'Candidate';
        $email = $crmStudent?->emailid ?? 'admissions@vignan.ac.in';
        $phone = $cleanPhone ?: ($crmStudent?->phoneno ?? '9999999999');

        // Dynamic fee breakdown with Default Early Bird and Incharge Personal coupons
        $breakdown = CouponService::calculateFeeBreakdown($applino, 25000.00);

        // Net payable amount after applying coupons
        $amount = (float) $request->input('amount', $breakdown['net_payable']);
        if ($amount <= 0) {
            $amount = $breakdown['net_payable'];
        }

        $txnid = date('dmYHis') . substr($applino, -6);
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
            'udf5' => $breakdown['per_coupon'] ?? '',             // Personal coupon code (last 8 digits)
        ];

        $params['hash'] = $this->payuService->generateHash($params);

        // Pre-record initiated transaction in finance.paydetails
        try {
            $existing = DB::table('finance.paydetails')->where('txncrmid', $txnid)->first();
            if (!$existing) {
                $nextFeeId = (DB::table('finance.paydetails')->max('feeid') ?? 0) + 1;
                DB::table('finance.paydetails')->insert([
                    'feeid' => $nextFeeId,
                    'applino' => $applino,
                    'feepayable' => $breakdown['base_fee'],
                    'defcoupon' => $breakdown['def_coupon'],
                    'defcouponamount' => $breakdown['def_discount'],
                    'percoupon' => $breakdown['per_coupon'],
                    'percouponamount' => $breakdown['per_discount'],
                    'totalcouponamount' => $breakdown['total_discount'],
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
        $txnid = (string) $request->input('txnid', '');
        $mihpayid = (string) $request->input('mihpayid', '');
        $amount = (float) $request->input('amount', 0);
        $applino = (string) $request->input('udf1', '');
        $defCoupon = (string) $request->input('udf4', '');
        $perCoupon = (string) $request->input('udf5', '');
        $bankRefNo = $request->input('bank_ref_no') ?? $request->input('bank_ref_num') ?? $request->input('bank_ref') ?? '';
        $mode = (string) $request->input('mode', 'Gateway');
        $errorMsg = (string) ($request->input('error_Message') ?? $request->input('field9') ?? '');

        // Recalculate fee details
        $breakdown = CouponService::calculateFeeBreakdown($applino, 25000.00);

        if ($status === 'success') {
            // 1. Record / Update finance.paydetails
            try {
                $existing = DB::table('finance.paydetails')->where('txncrmid', $txnid)->first();
                if ($existing) {
                    DB::table('finance.paydetails')->where('txncrmid', $txnid)->update([
                        'feepayable' => $breakdown['base_fee'],
                        'defcoupon' => $defCoupon ?: $breakdown['def_coupon'],
                        'defcouponamount' => $breakdown['def_discount'],
                        'percoupon' => $perCoupon ?: $breakdown['per_coupon'],
                        'percouponamount' => $breakdown['per_discount'],
                        'totalcouponamount' => $breakdown['total_discount'],
                        'paidamount' => $amount,
                        'paytype' => 'PayU (' . ($mode ?: 'Online') . ')',
                        'txnpayuid' => $mihpayid,
                        'txnstatus' => 'success',
                        'txnremark' => 'Bank Ref: ' . ($bankRefNo ?: 'N/A') . ' | PayU ID: ' . $mihpayid,
                        'log_timestamp' => now(),
                    ]);
                } else {
                    $nextFeeId = (DB::table('finance.paydetails')->max('feeid') ?? 0) + 1;
                    DB::table('finance.paydetails')->insert([
                        'feeid' => $nextFeeId,
                        'applino' => $applino ?: 'VU2026',
                        'feepayable' => $breakdown['base_fee'],
                        'defcoupon' => $defCoupon ?: $breakdown['def_coupon'],
                        'defcouponamount' => $breakdown['def_discount'],
                        'percoupon' => $perCoupon ?: $breakdown['per_coupon'],
                        'percouponamount' => $breakdown['per_discount'],
                        'totalcouponamount' => $breakdown['total_discount'],
                        'paidamount' => $amount,
                        'paytype' => 'PayU (' . ($mode ?: 'Online') . ')',
                        'txndatetime' => now(),
                        'txncrmid' => $txnid,
                        'txnpayuid' => $mihpayid,
                        'txnstatus' => 'success',
                        'txnremark' => 'Bank Ref: ' . ($bankRefNo ?: 'N/A') . ' | PayU ID: ' . $mihpayid,
                        'log_userid' => $applino,
                        'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                        'log_timestamp' => now(),
                    ]);
                }
            } catch (\Throwable $e) {
                Log::error('[PayU] Error writing to finance.paydetails: ' . $e->getMessage());
            }

            // 2. Increment coupon counts in finance.couponinfo
            CouponService::recordCouponUsage($defCoupon ?: $breakdown['def_coupon'], $perCoupon ?: $breakdown['per_coupon']);

            // 3. Generate and Insert finance.receiptdetails
            $receiptNo = 'REC-2026-' . date('Ymd') . '-' . rand(1000, 9999);
            try {
                $nextRecId = (DB::table('finance.receiptdetails')->max('id') ?? 0) + 1;
                DB::table('finance.receiptdetails')->insert([
                    'id' => $nextRecId,
                    'applino' => $applino ?: 'VU2026',
                    'receiptno' => $receiptNo,
                    'paytype' => 'PayU',
                    'feepayable' => $amount,
                    'defcouponamount' => $breakdown['def_discount'],
                    'percouponamount' => $breakdown['per_discount'],
                    'totalcouponamount' => $breakdown['total_discount'],
                    'txndatetime' => now(),
                    'receiptdatetime' => now(),
                    'log_userid' => $applino,
                    'log_ipaddress' => $request->ip() ?? '127.0.0.1',
                    'log_timestamp' => now(),
                ]);
            } catch (\Throwable $e) {
                Log::error('[PayU] Error writing to finance.receiptdetails: ' . $e->getMessage());
            }

            // 4. Mark stage1 as 'paid' in crm.student_registration
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

            $discountMsg = $breakdown['total_discount'] > 0 
                ? " (Concession Applied: -₹" . number_format($breakdown['total_discount']) . " via {$breakdown['def_coupon']}" . ($breakdown['per_coupon'] ? " & {$breakdown['per_coupon']}" : "") . ")" 
                : "";

            return redirect()->route('dashboards.index', ['step' => 2])
                ->with('status', "✓ Payment Successful! Net Paid: ₹" . number_format($amount) . " confirmed via PayU (ID: {$mihpayid}){$discountMsg}. Receipt: {$receiptNo}. Stage 1 completed!");
        } else {
            // Unsuccessful or cancelled payment
            try {
                $existing = DB::table('finance.paydetails')->where('txncrmid', $txnid)->first();
                if ($existing) {
                    DB::table('finance.paydetails')->where('txncrmid', $txnid)->update([
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
}
