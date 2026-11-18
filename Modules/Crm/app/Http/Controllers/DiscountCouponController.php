<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CouponInfo;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DiscountCouponController extends Controller
{
    /**
     * Display the Discount Coupon Master interface (finance.couponinfo) using Eloquent models.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : \Modules\Auth\Services\RoleService::defaultStaffRoleId();
        $roleName = $user ? $user->role_label : 'CRM Official';
        $activeTab = $request->query('tab', 'discountcoupon');

        $roleMenus = collect();
        try {
            $roleMenus = \Modules\Auth\Models\MenuAccessRole::forRole($roleId);
        } catch (\Throwable $e) {}

        $availableRoles = collect();
        try {
            $userAssigned = $user ? $user->getAssignedRoles() : [];
            $availableRoles = !empty($userAssigned)
                ? collect($userAssigned)->map(fn($r) => (object)['role_id' => (int)$r['role_id'], 'role_name' => $r['role_name']])
                : \Modules\Auth\Models\Role::where('role_id', $roleId)->get();
        } catch (\Throwable $e) {}

        // Fetch currently ACTIVE discount coupon via Eloquent Model
        $activeCoupon = CouponInfo::where(function ($q) {
                $q->where('coupontype', 'DEFAULT')
                  ->orWhere('coupontype', 'BATCH')
                  ->orWhereNull('coupontype');
            })
            ->where('coupstatus', 'ACTIVE')
            ->orderBy('id', 'desc')
            ->first();

        // Fetch all coupons history via Eloquent Model
        $coupons = CouponInfo::orderBy('id', 'desc')->paginate(15);

        $totalCount = CouponInfo::count();
        $activeCount = CouponInfo::where('coupstatus', 'ACTIVE')->count();
        $inactiveCount = CouponInfo::where('coupstatus', '!=', 'ACTIVE')->count();

        return view('crm::discountcoupon', [
            'user'           => $user,
            'roleId'         => $roleId,
            'roleName'       => $roleName,
            'roleMenus'      => $roleMenus,
            'availableRoles' => $availableRoles,
            'activeCoupon'   => $activeCoupon,
            'coupons'        => $coupons,
            'totalCount'     => $totalCount,
            'activeCount'    => $activeCount,
            'inactiveCount'  => $inactiveCount,
            'activeTab'      => $activeTab,
            'todayDate'      => date('Y-m-d'),
        ]);
    }

    /**
     * Save a new discount coupon.
     * Stops the last active coupon and activates the new one from coupstartdate.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'coupstartdate'  => 'required|string',
            'couponname'     => 'required|string|max:255',
            'discountamount' => 'required|numeric|min:0|max:1200',
            'coupstdcount'   => 'nullable|integer|min:1',
        ]);

        $startDate = trim($request->input('coupstartdate'));
        $name      = trim($request->input('couponname'));
        $amount    = (float)$request->input('discountamount');
        $hasCount  = $request->filled('coupstdcount') && (int)$request->input('coupstdcount') > 0;
        $stdCount  = $hasCount ? (int)$request->input('coupstdcount') : null;
        $userCode  = Auth::user()?->usercode ?? '02598';
        $ip        = $request->ip() ?? '127.0.0.1';

        // Auto-generate clean coupon code identifier
        $code = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
        $code = substr($code, 0, 14);
        if (empty($code)) {
            $code = '27DISC' . (int)$amount;
        }

        // 1. STOP previously active discount coupon(s) via Eloquent Model
        // Closing date of previous coupon becomes the start date of this new coupon
        $previousActive = CouponInfo::where(function ($q) {
                $q->where('coupontype', 'DEFAULT')
                  ->orWhere('coupontype', 'BATCH')
                  ->orWhereNull('coupontype');
            })
            ->where('coupstatus', 'ACTIVE')
            ->get();

        if ($previousActive->isNotEmpty()) {
            CouponInfo::whereIn('id', $previousActive->pluck('id'))
                ->update([
                    'coupstatus'    => 'INACTIVE',
                    'coupclosedate' => $startDate,
                    'log_timestamp' => now(),
                ]);
        }

        // 2. Insert and ACTIVATE the new discount coupon via Eloquent Model
        CouponInfo::create([
            'couponyear'     => '2027-28',
            'campuscode'     => 'VAD',
            'coupontype'     => 'DEFAULT',
            'coupstartdate'  => $startDate,
            'couponname'     => $name,
            'discountamount' => number_format($amount, 2, '.', ''),
            'discount'       => $amount,
            'coupstdcount'   => $stdCount,
            'coupcount'      => $stdCount,
            'coupcurrcount'  => 0,
            'couponsno'      => $code,
            'coupstatus'     => 'ACTIVE',
            'coupclosedate'  => null,
            'log_userid'     => $userCode,
            'log_ipaddress'  => $ip,
            'log_timestamp'  => now(),
        ]);

        $stoppedNote = $previousActive->isNotEmpty()
            ? " Previous coupon ('{$previousActive->first()->couponname}') closed on {$startDate}."
            : "";

        $quotaMsg = $stdCount ? "for {$stdCount} students" : "for ALL students (works until stopped)";

        return redirect()->route('crm.discountcoupon', ['tab' => 'discountcoupon'])
            ->with('status', "✓ New discount coupon '{$name}' (₹" . number_format($amount, 2) . ") activated {$quotaMsg} starting from {$startDate}!{$stoppedNote}");
    }

    /**
     * Stop/Deactivate an active coupon via Eloquent Model.
     */
    public function stop(int $id): RedirectResponse
    {
        $coupon = CouponInfo::find($id);
        if (!$coupon) {
            return redirect()->back()->with('error', 'Coupon record not found.');
        }

        $coupon->update([
            'coupstatus'    => 'INACTIVE',
            'coupclosedate' => date('Y-m-d'),
            'log_timestamp' => now(),
        ]);

        return redirect()->back()
            ->with('status', "✓ Coupon '{$coupon->couponname}' has been stopped successfully.");
    }

    /**
     * Re-activate a previously stopped coupon via Eloquent Model.
     */
    public function activate(int $id): RedirectResponse
    {
        $target = CouponInfo::find($id);
        if (!$target) {
            return redirect()->back()->with('error', 'Coupon record not found.');
        }

        // Stop all other active default coupons
        CouponInfo::where('coupstatus', 'ACTIVE')
            ->update([
                'coupstatus'    => 'INACTIVE',
                'coupclosedate' => date('Y-m-d'),
                'log_timestamp' => now(),
            ]);

        // Activate target coupon
        $target->update([
            'coupstatus'    => 'ACTIVE',
            'coupclosedate' => null,
            'log_timestamp' => now(),
        ]);

        return redirect()->back()
            ->with('status', "✓ Coupon '{$target->couponname}' (₹" . number_format((float)$target->discountamount, 2) . ") is now ACTIVE.");
    }
}
