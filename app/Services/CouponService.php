<?php

namespace App\Services;

use App\Models\CouponInfo;
use Illuminate\Support\Facades\Log;

class CouponService
{
    /**
     * Default Admission Registration Fee (INR).
     */
    public const DEFAULT_ADMISSION_FEE = 1200.00;

    /**
     * Get the currently active tiered default / early-bird coupon via Eloquent.
     * Tiers:
     * - Tier 1: 27EARLYB100 (first 100 members: 0-100) -> ₹500
     * - Tier 2: 27EARLYB200 (next 200 members: 100-300) -> ₹300
     * - Tier 3: 27EARLYB500 (next 500 members: 300-800) -> ₹200
     * - Tier 4: 27EARLYB50  (reserve 50 members: 800-850) -> ₹100
     */
    public static function getActiveDefaultCoupon(): ?CouponInfo
    {
        try {
            $coupon = CouponInfo::where(function ($q) {
                    $q->where('coupontype', 'DEFAULT')
                      ->orWhere('coupontype', 'BATCH');
                })
                ->where('coupstatus', 'ACTIVE')
                ->where(function ($q) {
                    $q->whereNull('coupcount')
                      ->orWhereColumn('coupcurrcount', '<', 'coupcount');
                })
                ->orderBy('id', 'asc')
                ->first();

            return $coupon;
        } catch (\Throwable $e) {
            Log::error('[CouponService] getActiveDefaultCoupon error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Format a personal coupon code derived from candidate application number:
     * - Takes the 8 digits of the application number (e.g. 2027000005 -> 27000005 or 2027000001 -> 27000001)
     * - Appends P1 suffix (e.g. 2027000005 -> 27000005P1)
     */
    public static function formatPersonalCouponCode(string $applino): string
    {
        $applino = trim($applino);
        $clean = preg_replace('/\D/', '', $applino);

        if (empty($clean)) {
            return $applino ? strtoupper(trim($applino)) : '27000001P1';
        }

        // If it already ends with P\d+ (e.g. 27000005P1 or 261106333P1), keep as-is
        if (preg_match('/P\d+$/i', $applino)) {
            return strtoupper($applino);
        }

        // If application number has 10 digits starting with 20 (e.g. 2027000001 or 2027000005),
        // take the eight digits (dropping '20') -> 27000001 or 27000005 -> 27000005P1
        if (str_starts_with($clean, '20') && strlen($clean) === 10) {
            $core = substr($clean, 2);
        } elseif (str_starts_with($clean, '20') && strlen($clean) > 10) {
            // For longer legacy numbers, also strip leading 20
            $core = substr($clean, 2);
        } elseif (strlen($clean) >= 8) {
            // Take the 8 digits
            $core = substr($clean, -8);
        } elseif (strlen($clean) === 7) {
            $core = '26' . $clean;
        } else {
            $core = $clean;
        }

        return strtoupper($core . 'P1');
    }

    /**
     * Get personal coupon for a student based on application number via Eloquent.
     * Looks up personal coupon by application number or formatted coupon code (e.g. 27000005P1).
     */
    public static function getPersonalCouponForStudent(string $applino): ?CouponInfo
    {
        if (empty($applino)) {
            return null;
        }

        $applino = trim($applino);
        $formattedCode = self::formatPersonalCouponCode($applino);
        $clean = preg_replace('/\D/', '', $applino);
        $last7 = strlen($clean) >= 7 ? substr($clean, -7) : $clean;
        $last8 = strlen($clean) >= 8 ? substr($clean, -8) : $clean;
        $eightDigits = (str_starts_with($clean, '20') && strlen($clean) === 10) ? substr($clean, 2) : $last8;

        try {
            return CouponInfo::where('coupontype', 'PERSONAL')
                ->where(function ($q) use ($applino, $formattedCode, $clean, $last7, $last8, $eightDigits) {
                    $q->where('applino', $applino)
                      ->orWhere('applino', $clean)
                      ->orWhere('couponsno', $formattedCode)
                      ->orWhere('couponsno', $eightDigits . 'P1')
                      ->orWhere('couponsno', $eightDigits)
                      ->orWhere('couponsno', $last8)
                      ->orWhere('couponsno', 'LIKE', '%' . $last8 . '%')
                      ->orWhere('couponsno', 'LIKE', '%' . $last7 . '%')
                      ->orWhere('couponsno', $applino);
                })
                ->orderByRaw("CASE WHEN coupstatus = 'ACTIVE' THEN 0 WHEN coupstatus = 'USED' THEN 1 ELSE 2 END")
                ->orderBy('id', 'desc')
                ->first();
        } catch (\Throwable $e) {
            Log::error('[CouponService] getPersonalCouponForStudent error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a personal coupon for an applicant by the CRM Incharge via Eloquent.
     * Enforces that the personal discount cross-checks the student's remaining amount
     * after applying the default early-bird coupon on the ₹1,200 admission fee.
     * Application number: e.g. 2027000005 -> formatted coupon: 27000005P1.
     */
    public static function generatePersonalCoupon(string $applino, float $discountAmount, string $userId = '03141', string $ip = '127.0.0.1'): array
    {
        $applino = trim($applino);
        $student = CrmIdService::findStudentByAppliOrCrmId($applino);
        if ($student) {
            $applino = $student->applinano;
            $crmId = $student->crmid ?? CrmIdService::generateForStudent($student);
        } else {
            $crmId = CrmIdService::getOrGenerateByAppli($applino);
        }

        $couponCode = self::formatPersonalCouponCode($applino);
        $clean = preg_replace('/\D/', '', $applino);
        $last7 = strlen($clean) >= 7 ? substr($clean, -7) : $clean;
        $last8 = strlen($clean) >= 8 ? substr($clean, -8) : $clean;
        $eightDigits = (str_starts_with($clean, '20') && strlen($clean) === 10) ? substr($clean, 2) : $last8;

        // Cross-check student remaining amount after active default coupon
        $baseFee = self::DEFAULT_ADMISSION_FEE;
        $defCoupon = self::getActiveDefaultCoupon();
        $defAmount = $defCoupon ? (float) $defCoupon->discountamount : 500.00;
        $maxAllowedPersonal = max(0, $baseFee - $defAmount); // e.g. 1200 - 500 = 700

        // Cap discount amount to remaining balance so fee cannot go negative
        if ($discountAmount > $maxAllowedPersonal) {
            $discountAmount = $maxAllowedPersonal;
        }

        // Check if personal coupon already exists for this candidate via Eloquent
        $existing = CouponInfo::where('coupontype', 'PERSONAL')
            ->where(function ($q) use ($applino, $couponCode, $clean, $last7, $last8, $eightDigits) {
                $q->where('applino', $applino)
                  ->orWhere('applino', $clean)
                  ->orWhere('couponsno', $couponCode)
                  ->orWhere('couponsno', $eightDigits . 'P1')
                  ->orWhere('couponsno', $eightDigits)
                  ->orWhere('couponsno', $last8)
                  ->orWhere('couponsno', 'LIKE', '%' . $last8 . '%')
                  ->orWhere('couponsno', 'LIKE', '%' . $last7 . '%');
            })
            ->first();

        if ($existing) {
            // Update coupon code to formatted format, discount amount & ensure active
            $existing->update([
                'couponsno' => $couponCode,
                'couponname' => "Personal Concession for {$crmId} (App #{$applino} - {$couponCode})",
                'coucharcount' => strlen($couponCode),
                'couponcodestart' => $couponCode,
                'currentcouponcode' => $couponCode,
                'discountamount' => $discountAmount,
                'coupstatus' => 'ACTIVE',
                'applino' => $applino,
                'log_userid' => $userId,
                'log_ipaddress' => $ip,
                'log_timestamp' => now(),
            ]);

            return [
                'success' => true,
                'code' => $couponCode,
                'discount' => $discountAmount,
                'crmid' => $crmId,
                'message' => "Personal coupon [{$couponCode}] updated to ₹" . number_format($discountAmount) . " for {$crmId} (App #{$applino})!",
            ];
        }

        // Insert new personal coupon with code = e.g. 261106333P1 via Eloquent
        $newId = (CouponInfo::max('id') ?? 0) + 1;

        CouponInfo::create([
            'id' => $newId,
            'couponsno' => $couponCode,
            'admyear' => '2026-27',
            'campuscode' => 'VAD',
            'coupontype' => 'PERSONAL',
            'couponname' => "Personal Concession for {$crmId} (App #{$applino} - {$couponCode})",
            'coupcount' => 1,
            'coucharcount' => strlen($couponCode),
            'coupcurrcount' => 0,
            'discountamount' => $discountAmount,
            'couponcodestart' => $couponCode,
            'currentcouponcode' => $couponCode,
            'coupstatus' => 'ACTIVE',
            'applino' => $applino,
            'log_userid' => $userId,
            'log_ipaddress' => $ip,
            'log_timestamp' => now(),
        ]);

        return [
            'success' => true,
            'code' => $couponCode,
            'discount' => $discountAmount,
            'crmid' => $crmId,
            'message' => "Personal coupon [{$couponCode}] generated successfully for {$crmId} (App #{$applino}) with ₹" . number_format($discountAmount) . " concession (Remaining after ₹" . number_format($defAmount) . " default tier)!",
        ];
    }

    /**
     * Primary entrypoint: Apply a personal coupon code for a candidate.
     */
    public static function applyPersonalCoupon(string $code, ?string $applino = null): array
    {
        return self::validateAndApplyPersonalCoupon($code, (string) ($applino ?? ''));
    }

    /**
     * Validate and apply a personal coupon entered by the candidate on Stage 1 via Eloquent.
     * Enforces all checks: existence, status, application number match, and remaining balance cap.
     */
    public static function validateAndApplyPersonalCoupon(string $code, string $applino = ''): array
    {
        $code = strtoupper(trim($code));
        $applino = trim($applino);

        if (empty($applino) && auth()->check()) {
            $applino = auth()->user()->usercode ?? '';
        }

        if (empty($code)) {
            return [
                'success' => false,
                'message' => 'Please enter a valid personal coupon code.',
            ];
        }

        // Look up coupon in finance.couponinfo via Eloquent
        $formattedCode = self::formatPersonalCouponCode($code);
        $coupon = CouponInfo::where('coupontype', 'PERSONAL')
            ->where(function ($q) use ($code, $formattedCode) {
                $q->where('couponsno', $code)
                  ->orWhere('couponsno', $formattedCode);
            })
            ->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => "Personal coupon [{$code}] not found in database. Please verify the code or contact admissions.",
            ];
        }

        // Verify status
        if ($coupon->coupstatus === 'USED') {
            return [
                'success' => false,
                'message' => "Personal coupon [{$coupon->couponsno}] has already been redeemed.",
            ];
        }

        if ($coupon->coupstatus !== 'ACTIVE') {
            return [
                'success' => false,
                'message' => "Personal coupon [{$coupon->couponsno}] is not active (Status: {$coupon->coupstatus}).",
            ];
        }

        // If coupon is strictly registered to a specific application number, verify ownership
        if (!empty($coupon->applino) && !empty($applino)) {
            $cleanApp = preg_replace('/\D/', '', $applino);
            $couponApp = preg_replace('/\D/', '', (string)$coupon->applino);
            $matches = ($cleanApp === $couponApp)
                || (strlen($cleanApp) >= 8 && strlen($couponApp) >= 8 && substr($cleanApp, -8) === substr($couponApp, -8))
                || (strlen($cleanApp) >= 7 && strlen($couponApp) >= 7 && substr($cleanApp, -7) === substr($couponApp, -7));

            if (!$matches) {
                return [
                    'success' => false,
                    'message' => "Personal coupon [{$coupon->couponsno}] is allocated to Application #{$coupon->applino}.",
                ];
            }
        }

        // Cross-check student remaining amount after active default coupon
        $baseFee = self::DEFAULT_ADMISSION_FEE;
        $defCoupon = self::getActiveDefaultCoupon();
        $defAmount = $defCoupon ? (float) $defCoupon->discountamount : 500.00;
        $remainingAmount = max(0, $baseFee - $defAmount);

        // Cap personal discount to remaining balance
        $personalDiscount = min((float) $coupon->discountamount, $remainingAmount);
        $totalDiscount = min($baseFee, $defAmount + $personalDiscount);
        $netPayable = max(0, $baseFee - $totalDiscount);

        // Store in session
        session()->forget('applied_coupon_removed');
        session([
            'applied_coupon_code' => $coupon->couponsno,
            'applied_coupon_discount' => $personalDiscount,
        ]);

        return [
            'success' => true,
            'code' => $coupon->couponsno,
            'discount' => $personalDiscount,
            'def_discount' => $defAmount,
            'def_coupon' => $defCoupon?->couponsno ?? '27EARLYB100',
            'def_coupon_code' => $defCoupon?->couponsno ?? '27EARLYB100',
            'per_coupon_code' => $coupon->couponsno,
            'per_discount' => $personalDiscount,
            'total_discount' => $totalDiscount,
            'net_payable' => $netPayable,
            'message' => "Personal coupon [{$coupon->couponsno}] applied successfully! -₹" . number_format($personalDiscount) . " concession granted.",
        ];
    }

    /**
     * Remove an applied personal coupon from the student session.
     */
    public static function removeAppliedCoupon(): array
    {
        session()->forget('applied_coupon_code');
        session()->forget('applied_coupon_discount');
        session(['applied_coupon_removed' => true]);

        $baseFee = self::DEFAULT_ADMISSION_FEE;
        $defCoupon = self::getActiveDefaultCoupon();
        $defAmount = $defCoupon ? (float) $defCoupon->discountamount : 500.00;
        $netPayable = max(0, $baseFee - $defAmount);

        return [
            'success' => true,
            'net_payable' => $netPayable,
            'message' => 'Personal coupon removed.',
        ];
    }

    /**
     * Calculate fee breakdown for Stage 1 Admission fee (Base ₹1,200) via Eloquent.
     */
    public static function calculateFeeBreakdown(string $applino, float $baseFee = self::DEFAULT_ADMISSION_FEE): array
    {
        $defCoupon = self::getActiveDefaultCoupon();
        $defCode = $defCoupon?->couponsno ?? '27EARLYB100';
        $defAmount = $defCoupon ? (float) $defCoupon->discountamount : 500.00;

        $perCoupon = null;
        $perCode = '';
        $perAmount = 0.00;

        // Check if candidate already has an allocated personal coupon
        if (!empty($applino)) {
            $candidatePer = self::getPersonalCouponForStudent($applino);
            if ($candidatePer && $candidatePer->coupstatus === 'ACTIVE') {
                $perCoupon = $candidatePer;
                $perCode = $perCoupon->couponsno ?? '';
                $perAmount = (float) ($perCoupon->discountamount ?? 0.00);
            }
        }

        // Cap personal discount to remaining balance after default coupon
        $maxPersonalDiscount = max(0, $baseFee - $defAmount);
        if ($perAmount > $maxPersonalDiscount) {
            $perAmount = $maxPersonalDiscount;
        }

        $totalDiscount = min($baseFee, $defAmount + $perAmount);
        $netPayable = max(0, $baseFee - $totalDiscount);

        return [
            'base_fee' => $baseFee,
            'def_coupon' => $defCode,
            'def_coupon_code' => $defCode,
            'def_discount' => $defAmount,
            'per_coupon' => $perCode,
            'per_coupon_code' => $perCode,
            'per_discount' => $perAmount,
            'total_discount' => $totalDiscount,
            'net_payable' => $netPayable,
            'def_coupon_obj' => $defCoupon,
            'per_coupon_obj' => $perCoupon,
        ];
    }

    /**
     * Calculate admission fee breakdown with session coupon support.
     */
    public static function calculateAdmissionFee(?string $applino = null): array
    {
        if (empty($applino) && auth()->check()) {
            $applino = auth()->user()->usercode ?? '';
        }

        $breakdown = self::calculateFeeBreakdown((string)($applino ?? ''), self::DEFAULT_ADMISSION_FEE);

        // Check if an applied coupon exists in session and not explicitly removed
        if (session()->has('applied_coupon_code') && !session('applied_coupon_removed')) {
            $sessionCode = (string) session('applied_coupon_code');
            $sessionDiscount = (float) session('applied_coupon_discount', 0.0);
            $breakdown['per_coupon'] = $sessionCode;
            $breakdown['per_coupon_code'] = $sessionCode;
            $breakdown['per_discount'] = $sessionDiscount;
            $breakdown['total_discount'] = min($breakdown['base_fee'], $breakdown['def_discount'] + $sessionDiscount);
            $breakdown['net_payable'] = max(0, $breakdown['base_fee'] - $breakdown['total_discount']);
        }

        return $breakdown;
    }

    /**
     * Record coupon redemption upon successful payment.
     */
    public static function recordCouponRedemption(string $applino, ?string $perCode = null, ?string $defCode = null): void
    {
        self::recordCouponUsage($defCode, $perCode);
    }

    /**
     * Record coupon usage upon successful fee payment via Eloquent.
     */
    public static function recordCouponUsage(?string $defCode, ?string $perCode): void
    {
        if ($defCode) {
            $def = CouponInfo::where('couponsno', $defCode)
                ->where(function ($q) {
                    $q->where('coupontype', 'DEFAULT')
                      ->orWhere('coupontype', 'BATCH');
                })
                ->first();

            if ($def) {
                $newCount = $def->coupcurrcount + 1;
                $newStatus = ($def->coupcount && $newCount >= $def->coupcount) ? 'COMPLETED' : 'ACTIVE';

                $def->update([
                    'coupcurrcount' => $newCount,
                    'coupstatus' => $newStatus,
                    'log_timestamp' => now(),
                ]);
            }
        }

        if ($perCode) {
            CouponInfo::where('couponsno', $perCode)
                ->where('coupontype', 'PERSONAL')
                ->update([
                    'coupcurrcount' => 1,
                    'coupstatus' => 'USED',
                    'log_timestamp' => now(),
                ]);
        }
    }
}
