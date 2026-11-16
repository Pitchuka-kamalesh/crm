<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\CouponInfo;
use Tests\TestCase;

class KpiDashboardsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'pgsql',
            'database.connections.pgsql.database' => 'vu_crm26',
        ]);
    }

    public function test_student_kpi_dashboard_renders_with_conversion_funnel(): void
    {
        $user = User::first();
        $this->assertNotNull($user, 'A database user must exist to test authenticated KPI routes');

        $response = $this->actingAs($user)->get('/kpi/studentkpi?tab=studentkpi');

        $response->assertStatus(200);
        $response->assertSee('Student KPI &amp; Conversion Analytics', false);
        $response->assertSee('Stage 1 Fee Paid');
        $response->assertSee('Stage 2 Personal Profile');
        $response->assertSee('Stage 3 Academic Records');
        $response->assertSee('Stage 4 Documents');
        $response->assertSee('Stage 5 Examination');
        $response->assertSee('studentFunnelChart');
        $response->assertSee('branchDistributionChart');
        $response->assertSee('Verified Student Records');
    }

    public function test_telecaller_kpi_dashboard_renders_with_leaderboard(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/kpi/telecallerkpi?tab=telecallerkpi');

        $response->assertStatus(200);
        $response->assertSee('Telecaller KPI &amp; Outreach Analytics', false);
        $response->assertSee('Executive Telecaller Performance Leaderboard');
        $response->assertSee('counselorComparisonChart');
        $response->assertSee('dispositionChart');
        $response->assertSee('All Telecallers / Counselors');
        $response->assertSee('Candidate Outreach &amp; Call Task Logs', false);
    }

    public function test_leads_kpi_dashboard_renders_with_inflow_trends(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->get('/kpi/leadskpi?tab=leadskpi');

        $response->assertStatus(200);
        $response->assertSee('Leads KPI &amp; Acquisition Analytics', false);
        $response->assertSee('Total Lead Volume');
        $response->assertSee('Allocated Leads');
        $response->assertSee('Unallocated Pool');
        $response->assertSee('leadInflowChart');
        $response->assertSee('sourceDistributionChart');
        $response->assertSee('Bulk Excel (02598)');
        $response->assertSee('Raw Inquiries Preview Directory');
    }

    public function test_coupon_ledger_and_discount_coupon_routes_render_successfully(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response1 = $this->actingAs($user)->get('/crm/percoupongen');
        $response1->assertStatus(200);
        $response1->assertSee('Coupon Management &amp; Fee Concessions Ledger', false);

        // Dedicated Discount Coupon Master view
        $response2 = $this->actingAs($user)->get('/discountcoupon?tab=couponinfo');
        $response2->assertStatus(200);
        $response2->assertSee('Discount Coupon Master', false);
        $response2->assertSee('From Date');
        $response2->assertSee('Coupon Name');
        $response2->assertSee('Amount (₹)');
        $response2->assertSee('Save &amp; Activate Coupon', false);
    }

    public function test_discount_coupon_lifecycle_stops_previous_and_activates_new(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        // Remember initial active coupon to restore after test
        $initialActive = CouponInfo::where('coupstatus', 'ACTIVE')->first();

        try {
            // Clean up any test records
            CouponInfo::where('couponname', 'LIKE', 'Test %')->delete();

            // 1. Submit a new coupon starting today
            $response = $this->actingAs($user)->post('/crm/discountcoupon/store', [
                'coupstartdate'  => date('Y-m-d'),
                'couponname'     => 'Test Winter Concession',
                'discountamount' => 450.00,
                'coupstdcount'   => 200,
            ]);

            $response->assertRedirect('/discountcoupon?tab=couponinfo');
            $response->assertSessionHas('status');

            // Verify newly created coupon is active in database
            $newCoupon = CouponInfo::where('couponname', 'Test Winter Concession')->first();
            $this->assertNotNull($newCoupon);
            $this->assertEquals('ACTIVE', $newCoupon->coupstatus);
            $this->assertEquals('450.00', $newCoupon->discountamount);

            // 2. Submit another new coupon - verify the previous one stops
            $response2 = $this->actingAs($user)->post('/crm/discountcoupon/store', [
                'coupstartdate'  => date('Y-m-d'),
                'couponname'     => 'Test Spring Special',
                'discountamount' => 600.00,
                'coupstdcount'   => 300,
            ]);

            $response2->assertRedirect('/discountcoupon?tab=couponinfo');

            // Previous coupon must now be INACTIVE
            $prevCouponAfter = CouponInfo::find($newCoupon->id);
            $this->assertEquals('INACTIVE', $prevCouponAfter->coupstatus);
            $this->assertNotNull($prevCouponAfter->coupclosedate);

            // And latest coupon must be ACTIVE
            $latestCoupon = CouponInfo::where('couponname', 'Test Spring Special')->first();
            $this->assertNotNull($latestCoupon);
            $this->assertEquals('ACTIVE', $latestCoupon->coupstatus);

            // 3. Test stopping the active coupon
            $stopResponse = $this->actingAs($user)->post("/crm/discountcoupon/stop/{$latestCoupon->id}");
            $stopResponse->assertSessionHas('status');

            $stoppedCoupon = CouponInfo::find($latestCoupon->id);
            $this->assertEquals('INACTIVE', $stoppedCoupon->coupstatus);

            // 4. Test re-activating the stopped coupon
            $activateResponse = $this->actingAs($user)->post("/crm/discountcoupon/activate/{$stoppedCoupon->id}");
            $activateResponse->assertSessionHas('status');

            $reactivatedCoupon = CouponInfo::find($latestCoupon->id);
            $this->assertEquals('ACTIVE', $reactivatedCoupon->coupstatus);
        } finally {
            // Always clean up test records and restore original active coupon
            CouponInfo::where('couponname', 'LIKE', 'Test %')->delete();
            if ($initialActive) {
                CouponInfo::where('id', $initialActive->id)->update([
                    'coupstatus' => 'ACTIVE',
                    'coupclosedate' => null,
                ]);
            }
        }
    }

    public function test_payu_checkout_renders_clean_receipt_panel(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        $response = $this->actingAs($user)->post('/payu/initiate');
        $response->assertStatus(200);
        $response->assertSee('Admission Fee Settlement');
        $response->assertSee('Official Admission Fee Receipt (Form VFSTR-REC)');
        $response->assertDontSee('Choose a Payment Method');
        $response->assertDontSee('Select UPI App');
        $response->assertSee('Test Simulator: Mark as Paid without Real Gateway');
    }

    public function test_payu_checkout_strictly_hides_test_simulator_in_production(): void
    {
        $user = User::first();
        $this->assertNotNull($user);

        // Simulate production environment
        $originalEnv = app()->environment();
        app()->detectEnvironment(fn () => 'production');
        config(['app.env' => 'production', 'app.debug' => false]);

        $view = $this->actingAs($user)->view('payments.payu_checkout', [
            'amount' => 950.00,
            'applino' => '2027000010',
            'name' => 'lella',
            'phone' => '9440158454',
            'email' => 'lella@vignan.ac.in',
            'txnid' => 'TXNTEST123',
            'gatewayUrl' => 'https://test.payu.in/_payment',
            'params' => ['key' => 'TESTKEY'],
            'breakdown' => [
                'base_fee' => 1200,
                'def_discount' => 250,
                'def_coupon' => 'KEDAR',
                'net_payable' => 950,
            ],
        ]);

        $view->assertSee('Admission Fee Settlement');
        $view->assertDontSee('Test Simulator: Mark as Paid without Real Gateway');
        $view->assertDontSee('⚡ Test Simulator');

        // Revert environment
        app()->detectEnvironment(fn () => $originalEnv);
        config(['app.env' => $originalEnv, 'app.debug' => true]);
    }
}
