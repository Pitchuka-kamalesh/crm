<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Finance\Models\Coupous;
use App\Services\CouponService;

class CoupounController extends Controller
{
    /**
     * Display a listing of coupons.
     */
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $coupons = Coupous::query()
            ->when($search, function ($query, $term) {
                return $query->forSearch($term);
            })
            ->orderBy('id', 'desc')
            ->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($coupons);
        }

        return view('crm.couponinfo', [
            'coupons' => $coupons,
            'tab' => 'couponinfo',
        ]);
    }

    /**
     * Generate personal coupon for an applicant.
     */
    public function generatePersonalCoupon(Request $request): RedirectResponse
    {
        $request->validate([
            'applino' => 'required|string',
            'discount_amount' => 'required|numeric|min:1|max:1200',
        ]);

        $applino = trim($request->input('applino'));
        $discount = (float) $request->input('discount_amount', 500.00);
        $user = Auth::user();
        $usercode = $user?->usercode ?? 'SYSTEM';

        $res = CouponService::generatePersonalCoupon($applino, $discount, $usercode, $request->ip() ?? '127.0.0.1');

        if ($request->is('*percoupongen*') || $request->input('redirect_to') === 'percoupongen') {
            return redirect()->route('crm.percoupongen')
                ->with('status', $res['message']);
        }

        return redirect()->route('crm.couponinfo', ['tab' => 'couponinfo'])
            ->with('status', $res['message']);
    }

    /**
     * Validate coupon code via AJAX / JSON.
     */
    public function validateCoupon(Request $request)
    {
        $couponCode = trim($request->input('coupon_code', ''));
        $applino = trim($request->input('applino', ''));

        $coupon = Coupous::forCode($couponCode)->active()->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive coupon code.',
            ], 422);
        }

        if ($coupon->isPersonal() && $coupon->applino !== $applino) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon is assigned to another applicant.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'discount' => (float) $coupon->discount,
            'coupon_code' => $coupon->couponcode,
        ]);
    }

    /**
     * Remove / deactivate coupon.
     */
    public function destroy($id): RedirectResponse
    {
        $coupon = Coupous::find($id);
        if ($coupon) {
            $coupon->status = 0;
            $coupon->save();
        }

        return back()->with('status', 'Coupon deactivated successfully.');
    }
}
