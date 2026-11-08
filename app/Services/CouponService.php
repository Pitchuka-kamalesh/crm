<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CouponService
{
    /**
     * Get the currently active tiered default / early-bird coupon.
     * Tiers:
     * - Tier 1: 27EARLYB100 (first 100 members: 0-100)
     * - Tier 2: 27EARLYB200 (next 200 members: 100-300)
     * - Tier 3: 27EARLYB500 (next 500 members: 300-800)
     * - Tier 4: 27EARLYB50  (reserve 50 members: 800-850)
     */
    public static function getActiveDefaultCoupon(): ?object
    {
        try {
            $coupon = DB::table('finance.couponinfo')
                ->where('coupontype', 'DEFAULT')
                ->where('coupstatus', 'ACTIVE')
                ->whereColumn('coupcurrcount', '<', 'coupcount')
                ->orderBy('id', 'asc')
                ->first();

            return $coupon;
        } catch (\Throwable $e) {
            Log::error('[CouponService] getActiveDefaultCoupon error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get personal coupon for a student based on application number.
     * Personal coupon code is derived from the last 8 digits of the application number (e.g., 61106333).
     */
    public static function getPersonalCouponForStudent(string $applino): ?object
    {
        if (empty($applino)) {
            return null;
        }

        $last8 = strlen($applino) >= 8 ? substr($applino, -8) : $applino;

        try {
            return DB::table('finance.couponinfo')
                ->where('coupontype', 'PERSONAL')
                ->where(function ($q) use ($applino, $last8) {
                    $q->where('applino', $applino)
                      ->orWhere('couponsno', $last8)
                      ->orWhere('couponsno', $applino);
                })
                ->where('coupstatus', 'ACTIVE')
                ->first();
        } catch (\Throwable $e) {
            Log::error('[CouponService] getPersonalCouponForStudent error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate a personal coupon for an applicant by the CRM Incharge.
     * Application number: e.g. 20261106333 -> last 8 digits: 61106333.
     */
    public static function generatePersonalCoupon(string $applino, float $discountAmount, string $userId = '03141', string $ip = '127.0.0.1'): array
    {
        $applino = trim($applino);
        $last8 = strlen($applino) >= 8 ? substr($applino, -8) : $applino;

        // Check if personal coupon already exists for this candidate
        $existing = DB::table('finance.couponinfo')
            ->where('coupontype', 'PERSONAL')
            ->where(function ($q) use ($applino, $last8) {
                $q->where('applino', $applino)->orWhere('couponsno', $last8);
            })
            ->first();

        if ($existing) {
            // Update discount amount & ensure active
            DB::table('finance.couponinfo')
                ->where('id', $existing->id)
                ->update([
                    'discountamount' => $discountAmount,
                    'coupstatus' => 'ACTIVE',
                    'log_userid' => $userId,
                    'log_ipaddress' => $ip,
                    'log_timestamp' => now(),
                ]);

            return [
                'success' => true,
                'code' => $existing->couponsno,
                'discount' => $discountAmount,
                'message' => "Personal coupon [{$existing->couponsno}] updated to ₹" . number_format($discountAmount) . " for App #{$applino}!",
            ];
        }

        // Insert new personal coupon with code = last 8 digits
        $newId = (DB::table('finance.couponinfo')->max('id') ?? 0) + 1;

        DB::table('finance.couponinfo')->insert([
            'id' => $newId,
            'couponsno' => $last8,
            'admyear' => '2026-27',
            'campuscode' => 'VAD',
            'coupontype' => 'PERSONAL',
            'couponname' => "Personal Concession for Applicant #{$applino}",
            'coupcount' => 1,
            'coucharcount' => strlen($last8),
            'coupcurrcount' => 0,
            'discountamount' => $discountAmount,
            'couponcodestart' => $last8,
            'currentcouponcode' => $last8,
            'coupstatus' => 'ACTIVE',
            'applino' => $applino,
            'log_userid' => $userId,
            'log_ipaddress' => $ip,
            'log_timestamp' => now(),
        ]);

        return [
            'success' => true,
            'code' => $last8,
            'discount' => $discountAmount,
            'message' => "Personal coupon [{$last8}] generated successfully for App #{$applino} with ₹" . number_format($discountAmount) . " concession!",
        ];
    }

    /**
     * Calculate fee breakdown for Stage 1 Admission fee.
     */
    public static function calculateFeeBreakdown(string $applino, float $baseFee = 25000.00): array
    {
        $defCoupon = self::getActiveDefaultCoupon();
        $defCode = $defCoupon?->couponsno ?? '27EARLYB100';
        $defAmount = $defCoupon ? (float) $defCoupon->discountamount : 5000.00;

        $perCoupon = self::getPersonalCouponForStudent($applino);
        $perCode = $perCoupon?->couponsno ?? '';
        $perAmount = $perCoupon ? (float) $perCoupon->discountamount : 0.00;

        $totalDiscount = $defAmount + $perAmount;
        $netPayable = max(0, $baseFee - $totalDiscount);

        return [
            'base_fee' => $baseFee,
            'def_coupon' => $defCode,
            'def_discount' => $defAmount,
            'per_coupon' => $perCode,
            'per_discount' => $perAmount,
            'total_discount' => $totalDiscount,
            'net_payable' => $netPayable,
            'def_coupon_obj' => $defCoupon,
            'per_coupon_obj' => $perCoupon,
        ];
    }

    /**
     * Record coupon usage upon successful fee payment.
     */
    public static function recordCouponUsage(?string $defCode, ?string $perCode): void
    {
        if ($defCode) {
            $def = DB::table('finance.couponinfo')
                ->where('couponsno', $defCode)
                ->where('coupontype', 'DEFAULT')
                ->first();

            if ($def) {
                $newCount = $def->coupcurrcount + 1;
                $newStatus = ($newCount >= $def->coupcount) ? 'COMPLETED' : 'ACTIVE';

                DB::table('finance.couponinfo')
                    ->where('id', $def->id)
                    ->update([
                        'coupcurrcount' => $newCount,
                        'coupstatus' => $newStatus,
                        'log_timestamp' => now(),
                    ]);
            }
        }

        if ($perCode) {
            DB::table('finance.couponinfo')
                ->where('couponsno', $perCode)
                ->where('coupontype', 'PERSONAL')
                ->update([
                    'coupcurrcount' => 1,
                    'coupstatus' => 'USED',
                    'log_timestamp' => now(),
                ]);
        }
    }
}

