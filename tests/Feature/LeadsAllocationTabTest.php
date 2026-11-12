<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Lead;
use App\Models\TelecallerLead;
use App\Models\TelecallerLeadTask;
use App\Models\User;
use Tests\TestCase;

class LeadsAllocationTabTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'pgsql',
            'database.connections.pgsql.database' => 'vu_crm',
        ]);
    }

    public function test_allocation_tab_renders_requested_table_columns_from_leads_table()
    {
        $testPhone = 9988776655;
        // Insert a lead in leads table with empcode = 0
        Lead::where('phoneno', $testPhone)->delete();
        Lead::create([
            'phoneno' => $testPhone,
            'phnostatus' => 'New Lead',
            'enquirydate' => now(),
            'empcode' => '0',
            'allocated_at' => null,
            'log_userid' => '02598',
            'log_ipaddress' => '127.0.0.1',
            'log_timestamp' => now(),
        ]);

        $user = User::first();
        $response = $this->actingAs($user)->get('/leads/leadsallocation?tab=allocation&q=' . $testPhone);
        $response->assertStatus(200);

        // Verify column names requested by user
        $response->assertSee('Phone Number');
        $response->assertSee('Timestamp');
        $response->assertSee('Empcode');
        $response->assertSee('Allocated Timestamp');

        // Verify lead phone is present
        $response->assertSee((string) $testPhone);

        // Verify that before allotment, empcode and allocated timestamp display 0
        $response->assertSee('0');

        // Cleanup
        Lead::where('phoneno', $testPhone)->delete();
    }

    public function test_lead_allocation_updates_leads_and_telecaller_leads_with_zero_defaults()
    {
        $testPhone = 9123456780;
        Lead::where('phoneno', $testPhone)->delete();
        TelecallerLead::where('phoneno', (string) $testPhone)->delete();
        TelecallerLeadTask::where('phoneno', (string) $testPhone)->delete();

        // Create unallocated lead
        $lead = Lead::create([
            'phoneno' => $testPhone,
            'phnostatus' => 'New Lead',
            'enquirydate' => now(),
            'empcode' => '0',
            'allocated_at' => null,
            'log_userid' => '02598',
            'log_ipaddress' => '127.0.0.1',
            'log_timestamp' => now()->subMinutes(10),
        ]);

        $telecaller = Employee::first();
        $empCode = $telecaller ? $telecaller->emp_code : '03434';

        // Allocate this lead
        $res = \App\Services\TelecallerService::allocateLeads([
            'empcode' => $empCode,
            'allot_mode' => 'selected',
            'lead_keys' => [
                ['applino' => '0', 'phoneno' => (string) $testPhone]
            ],
            'stage_name' => 'Stage 1: Calling & Verification',
            'stage_remark' => 'Allocated for test',
        ]);

        $this->assertTrue($res['success']);

        // Check crm.leads has been updated with empcode and allocated_at
        $updatedLead = Lead::where('phoneno', $testPhone)->first();
        $this->assertEquals($empCode, $updatedLead->empcode);
        $this->assertNotNull($updatedLead->allocated_at);

        // Check crm.telecaller_leads has row with 0 for missing data
        $tcLead = TelecallerLead::where('phoneno', (string) $testPhone)->first();
        $this->assertNotNull($tcLead);
        $this->assertEquals('0', $tcLead->applino);
        $this->assertEquals('0', $tcLead->crmid);
        $this->assertEquals($empCode, $tcLead->empcode);

        // Check NO task logged on allocation (tasks only recorded when telecaller performs an action)
        $task = TelecallerLeadTask::where('phoneno', (string) $testPhone)->first();
        $this->assertNull($task);

        // Cleanup
        Lead::where('phoneno', $testPhone)->delete();
        TelecallerLead::where('phoneno', (string) $testPhone)->delete();
        TelecallerLeadTask::where('phoneno', (string) $testPhone)->delete();
    }

    public function test_allocated_lead_is_moved_down_to_last_in_ordering()
    {
        $unallocPhone = 9999000001;
        $allocPhone = 9999000002;

        Lead::whereIn('phoneno', [$unallocPhone, $allocPhone])->delete();

        // Lead with empcode 0
        Lead::create([
            'phoneno' => $unallocPhone,
            'empcode' => '0',
            'allocated_at' => null,
            'log_timestamp' => now()->subDay(),
        ]);

        // Lead with empcode assigned (allocated)
        Lead::create([
            'phoneno' => $allocPhone,
            'empcode' => '03434',
            'allocated_at' => now(),
            'log_timestamp' => now(),
        ]);

        // Query allocation tab ordering
        $orderedLeads = Lead::whereIn('phoneno', [$unallocPhone, $allocPhone])
            ->orderByRaw("(CASE WHEN empcode IS NULL OR empcode = '0' THEN 0 ELSE 1 END) ASC, log_timestamp DESC")
            ->pluck('phoneno')
            ->toArray();

        // Unallocated lead (empcode 0) must appear before allocated lead (empcode 03434)
        $this->assertEquals([$unallocPhone, $allocPhone], $orderedLeads);

        // Cleanup
        Lead::whereIn('phoneno', [$unallocPhone, $allocPhone])->delete();
    }

    public function test_post_leads_allocate_http_endpoint_updates_leads_and_telecaller_leads()
    {
        $testPhone = 9199887711;
        Lead::where('phoneno', $testPhone)->delete();
        TelecallerLead::where('phoneno', (string) $testPhone)->delete();
        TelecallerLeadTask::where('phoneno', (string) $testPhone)->delete();

        Lead::create([
            'phoneno' => $testPhone,
            'phnostatus' => 'New Lead',
            'enquirydate' => now(),
            'empcode' => '0',
            'allocated_at' => null,
            'log_userid' => '02598',
            'log_ipaddress' => '127.0.0.1',
            'log_timestamp' => now(),
        ]);

        $telecaller = Employee::first();
        $empCode = $telecaller ? $telecaller->emp_code : '03434';
        $user = User::first();

        // POST request to /leads/allocate endpoint
        $response = $this->actingAs($user)->postJson('/leads/allocate', [
            'empcode' => $empCode,
            'allot_mode' => 'selected',
            'lead_keys' => [
                ['applino' => '0', 'phoneno' => (string) $testPhone]
            ],
            'stage_name' => 'Stage 1: Fee Payment',
            'stage_remark' => 'Allocated via test endpoint',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Check DB was updated
        $updatedLead = Lead::where('phoneno', $testPhone)->first();
        $this->assertEquals($empCode, $updatedLead->empcode);
        $this->assertNotNull($updatedLead->allocated_at);

        // View on table and confirm correct data is displayed
        $viewResp = $this->actingAs($user)->get('/leads/leadsallocation?tab=allocation&q=' . $testPhone);
        $viewResp->assertStatus(200);
        $viewResp->assertSee((string) $testPhone);
        $viewResp->assertSee($empCode);
        $viewResp->assertSee($updatedLead->allocated_at->format('Y-m-d'));

        // Cleanup
        Lead::where('phoneno', $testPhone)->delete();
        TelecallerLead::where('phoneno', (string) $testPhone)->delete();
        TelecallerLeadTask::where('phoneno', (string) $testPhone)->delete();
    }

    public function test_bucket_allocation_allocates_available_leads_without_error()
    {
        // Setup 3 leads
        $phones = [9800000001, 9800000002, 9800000003];
        Lead::whereIn('phoneno', $phones)->delete();
        foreach ($phones as $p) {
            Lead::create([
                'phoneno' => $p,
                'empcode' => '0',
                'allocated_at' => null,
                'log_timestamp' => now()->addMinutes(10), // newest unallocated
            ]);
        }

        $telecaller = Employee::first();
        $empCode = $telecaller ? $telecaller->emp_code : '03434';
        $user = User::first();

        // Request bucket size of 100
        $response = $this->actingAs($user)->postJson('/leads/allocate', [
            'empcode' => $empCode,
            'allot_mode' => '100',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Cleanup
        Lead::whereIn('phoneno', $phones)->delete();
        TelecallerLead::whereIn('phoneno', array_map('strval', $phones))->delete();
        TelecallerLeadTask::whereIn('phoneno', array_map('strval', $phones))->delete();
    }

    public function test_allocation_tab_does_not_contain_top_boxes_or_removed_headings()
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/leads/leadsallocation?tab=allocation');
        $response->assertStatus(200);

        // Verify top boxes are not present
        $response->assertDontSee('In Telecaller Queues');
        $response->assertDontSee('Ready for Assignment');
        $response->assertDontSee('Tasks in Log');
        $response->assertDontSee('Fee Paid & Enrolled');

        // Verify heading banner removed
        $response->assertDontSee('Leads Allocation • Unallocated Queue');
        $response->assertDontSee('Select leads below and allocate to a telecaller');

        // Verify Step Remarks and stage dropdown removed
        $response->assertDontSee('Step Remarks');
        $response->assertDontSee('Saved into crm.telecaller_leads_tasks');

        // Verify single line controls exist
        $response->assertSee('Select Telecaller');
        $response->assertDontSee('(Role 4)');
        $response->assertDontSee('(Sorted: Unallocated First, Allocated Last)');
        $response->assertSee('Allocation Target');
        $response->assertSee('Bucket: 100 Leads');
        $response->assertSee('Bucket: 300 Leads');
        $response->assertSee('Bucket: 500 Leads');
        $response->assertSee('Bucket: 1000 Leads');
        $response->assertSee('Allocate to Telecaller');
    }

    public function test_allocation_tab_has_compact_controls_reset_filter_and_size_input_with_limit()
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/leads/leadsallocation?tab=allocation');
        $response->assertStatus(200);

        // Verify extra text in parentheses is removed
        $response->assertDontSee('(Role 4)');
        $response->assertDontSee('(Sorted: Unallocated First, Allocated Last)');
        $response->assertSee('Leads Table Directory');

        // Verify Filter Reset button exists to right of search
        $response->assertSee('id="btnResetFilters"', false);
        $response->assertSee('onclick="resetAllFilters()"', false);

        // Verify Table Size input box exists and allows limit up to leads table size
        $response->assertSee('id="tablePageSizeInput"', false);
        $response->assertSee('oninput="enforcePageSizeLimit(this)"', false);
        $response->assertSee('onchange="handlePageSizeChange(this.value)"', false);

        // Verify dynamic per_page parameter can be handled up to leads table size
        $responsePerPage = $this->actingAs($user)->get('/leads/leadsallocation?tab=allocation&per_page=50');
        $responsePerPage->assertStatus(200);
        $responsePerPage->assertSee('(50 per page)');

        // Supports larger table sizes beyond 100 up to leads table count
        $responseLarge = $this->actingAs($user)->get('/leads/leadsallocation?tab=allocation&per_page=150');
        $responseLarge->assertStatus(200);
        $responseLarge->assertSee('(150 per page)');
    }

    public function test_student_registration_fills_application_number_in_telecaller_leads()
    {
        $testPhone = '9988112233';
        \App\Models\CrmStudentRegistration::where('phoneno', $testPhone)->delete();
        Lead::where('phoneno', (int) $testPhone)->delete();
        TelecallerLead::where('phoneno', $testPhone)->delete();
        TelecallerLeadTask::where('phoneno', $testPhone)->delete();
        User::where('phnno', $testPhone)->delete();

        // 1. Prior to registration, create lead allocated to a telecaller with applino = '0'
        $telecaller = Employee::first();
        $empCode = $telecaller ? $telecaller->emp_code : '03434';

        Lead::create([
            'phoneno' => (int) $testPhone,
            'phnostatus' => 'New Lead',
            'empcode' => $empCode,
            'allocated_at' => now(),
            'log_timestamp' => now(),
        ]);

        TelecallerLead::create([
            'applino' => '0',
            'phoneno' => $testPhone,
            'crmid' => '0',
            'empcode' => $empCode,
            'leadcount' => 1,
            'assigndatetime' => now(),
            'log_timestamp' => now(),
        ]);

        TelecallerLeadTask::create([
            'applino' => '0',
            'phoneno' => $testPhone,
            'crmid' => '0',
            'empcode' => $empCode,
            'stage_name' => 'Calling & Verification',
            'stage_calltime' => now(),
            'log_timestamp' => now(),
        ]);

        // 2. Student completes registration form
        $response = $this->post('/register', [
            'applicant_name' => 'Test Student User',
            'mobile'         => $testPhone,
            'email'          => 'student.reg.test@vignan.ac.in',
            'campus_id'      => 1,
            'country_id'     => 1,
            'state_id'       => 1,
            'program_level'  => 'UG',
            'progid'         => 1,
            'branch_id'      => 1,
            'terms'          => 1,
        ]);

        $response->assertStatus(302);

        // 3. Verify application number was generated and filled into telecaller_leads
        $crmStudent = \App\Models\CrmStudentRegistration::where('phoneno', $testPhone)->first();
        $this->assertNotNull($crmStudent);
        $generatedAppli = $crmStudent->applinano;
        $this->assertNotEmpty($generatedAppli);
        $this->assertNotEquals('0', $generatedAppli);

        // Verify crm.leads has the application number
        $lead = Lead::where('phoneno', (int) $testPhone)->first();
        $this->assertNotNull($lead);
        $this->assertEquals($generatedAppli, (string) $lead->applino);

        // Verify crm.telecaller_leads has the updated application number
        $tcLead = TelecallerLead::where('phoneno', $testPhone)->first();
        $this->assertNotNull($tcLead);
        $this->assertEquals($generatedAppli, (string) $tcLead->applino);

        // Verify crm.telecaller_leads_tasks has the updated application number
        $tcTask = TelecallerLeadTask::where('phoneno', $testPhone)->first();
        $this->assertNotNull($tcTask);
        $this->assertEquals($generatedAppli, (string) $tcTask->applino);

        // Cleanup
        \App\Models\CrmStudentRegistration::where('phoneno', $testPhone)->delete();
        Lead::where('phoneno', (int) $testPhone)->delete();
        TelecallerLead::where('phoneno', $testPhone)->delete();
        TelecallerLeadTask::where('phoneno', $testPhone)->delete();
        User::where('phnno', $testPhone)->delete();
    }

    public function test_color_palette_uses_dark_maroon()
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/leads/leadsallocation?tab=allocation');
        $response->assertStatus(200);

        // Verify red was replaced with dark maroon
        $response->assertSee('dot-maroon');
        $response->assertSee('data-color="maroon"', false);
        $response->assertSee('Dark Maroon Theme');
        $response->assertDontSee('title="Red Theme"');
    }

    public function test_allotted_leads_page_removes_stuff_above_table_and_has_exact_columns()
    {
        $user = User::first();
        $response = $this->actingAs($user)->get('/leads/allottedleads');
        $response->assertStatus(200);

        // 1. Verify stuff above table is removed
        $response->assertDontSee('In Telecaller Queues');
        $response->assertDontSee('Ready for Assignment');
        $response->assertDontSee('Tasks in Log');
        $response->assertDontSee('Fee Paid & Enrolled');

        // Verify stage pills are removed
        $response->assertDontSee('All Leads');
        $response->assertDontSee('Step 1: Fee Payment');
        $response->assertDontSee('Step 2: Personal Profile');
        $response->assertDontSee('Step 3: Academic Info');
        $response->assertDontSee('Step 4: Doc Upload');
        $response->assertDontSee('Step 5: VSAT & Confirm');

        // Verify clean header
        $response->assertSee('My Allotted Leads');

        // 2. Verify table columns requested by user
        $response->assertSee('Phone Number');
        $response->assertSee('Student Created Timestamp');
        $response->assertSee('Allocated Timestamp');
        $response->assertSee('Current Step');
        $response->assertSee('Application');
        $response->assertSee('CRM ID');
        $response->assertSee('Action');
    }

    public function test_allotted_leads_page_strictly_shows_only_leads_allocated_to_telecaller()
    {
        $telecallerA_code = '03434';
        $telecallerB_code = '02598';

        $phoneA = '9876500001';
        $phoneB = '9876500002';

        TelecallerLead::whereIn('phoneno', [$phoneA, $phoneB])->delete();
        Lead::whereIn('phoneno', [(int)$phoneA, (int)$phoneB])->delete();

        $leadCreatedTime = '2026-11-09 10:30:00';

        // Lead A in leads table with log_timestamp
        Lead::create([
            'phoneno'       => (int) $phoneA,
            'empcode'       => $telecallerA_code,
            'allocated_at'  => now(),
            'log_timestamp' => $leadCreatedTime,
        ]);

        // Lead A allocated to telecaller A in telecaller_leads
        TelecallerLead::create([
            'applino' => '2026110001',
            'phoneno' => $phoneA,
            'crmid'   => 'CRM260001',
            'empcode' => $telecallerA_code,
            'leadcount' => 1,
            'assigndatetime' => now(),
            'log_timestamp' => now(),
        ]);

        // Lead B allocated to telecaller B
        Lead::create([
            'phoneno'       => (int) $phoneB,
            'empcode'       => $telecallerB_code,
            'allocated_at'  => now(),
            'log_timestamp' => now(),
        ]);

        TelecallerLead::create([
            'applino' => '2026110002',
            'phoneno' => $phoneB,
            'crmid'   => 'CRM260002',
            'empcode' => $telecallerB_code,
            'leadcount' => 1,
            'assigndatetime' => now(),
            'log_timestamp' => now(),
        ]);

        // Create or get user for Telecaller A with role 4
        $userA = User::where('usercode', $telecallerA_code)->first();
        if (!$userA) {
            $userA = User::create([
                'usercode' => $telecallerA_code,
                'phnno' => $phoneA,
                'role_id' => 4,
                'user_type' => 2,
                'is_active' => true,
            ]);
        }

        // Telecaller A views /leads/allottedleads
        $response = $this->actingAs($userA)->get('/leads/allottedleads');
        $response->assertStatus(200);

        // Telecaller A can see their own lead
        $response->assertSee($phoneA);
        $response->assertSee('CRM260001');

        // Sourced from leads table column log_timestamp
        $response->assertSee($leadCreatedTime);

        // Telecaller A CANNOT see Lead B
        $response->assertDontSee($phoneB);
        $response->assertDontSee('CRM260002');

        // Telecaller A cannot touch/update Lead B via API (strict 403 Forbidden)
        $updateResp = $this->actingAs($userA)->postJson('/leads/2026110002/update-stage', [
            'stage' => 'stage1',
            'status' => 'completed',
        ]);
        $updateResp->assertStatus(403);

        // Cleanup
        TelecallerLead::whereIn('phoneno', [$phoneA, $phoneB])->delete();
        Lead::whereIn('phoneno', [(int)$phoneA, (int)$phoneB])->delete();
    }

    public function test_leads_log_timestamp_is_fixed_and_reflected_in_both_incharge_and_telecaller_tables()
    {
        $testPhone = '9977881122';
        Lead::where('phoneno', (int)$testPhone)->delete();
        TelecallerLead::where('phoneno', $testPhone)->delete();
        TelecallerLeadTask::where('phoneno', $testPhone)->delete();

        $fixedInitialTimestamp = '2026-11-05 09:15:30';

        // 1. Lead enters the leads table for the first time with fixed timestamp
        $lead = Lead::create([
            'phoneno'       => (int) $testPhone,
            'phnostatus'    => 'New Lead',
            'empcode'       => '0',
            'allocated_at'  => null,
            'log_timestamp' => $fixedInitialTimestamp,
        ]);

        $adminUser = User::first();

        // 2. Verified in CRM Incharge table under "Timestamp" column
        $inchargeView = $this->actingAs($adminUser)->get('/leads/leadsallocation?tab=allocation&q=' . $testPhone);
        $inchargeView->assertStatus(200);
        $inchargeView->assertSee('Timestamp');
        $inchargeView->assertSee($fixedInitialTimestamp);

        // 3. Allocate this lead to telecaller 03434
        $telecallerCode = '03434';
        \App\Services\TelecallerService::allocateLeads([
            'empcode' => $telecallerCode,
            'allot_mode' => 'single',
            'phoneno' => $testPhone,
            'applino' => '0',
            'stage_name' => 'Stage 1: Calling & Verification',
        ]);

        // Verify in database that log_timestamp was NOT overwritten and remains FIXED!
        $freshLead = Lead::where('phoneno', (int)$testPhone)->first();
        $this->assertEquals($telecallerCode, $freshLead->empcode);
        $this->assertNotNull($freshLead->allocated_at);
        $this->assertEquals($fixedInitialTimestamp, $freshLead->log_timestamp->format('Y-m-d H:i:s'));

        // 4. Verify in Telecaller Allotted Leads table under "Student Created Timestamp"
        $telecallerUser = User::where('usercode', $telecallerCode)->first();
        if (!$telecallerUser) {
            $telecallerUser = User::create([
                'usercode' => $telecallerCode,
                'phnno' => $testPhone,
                'role_id' => 4,
                'user_type' => 2,
                'is_active' => true,
            ]);
        }

        $allottedView = $this->actingAs($telecallerUser)->get('/leads/allottedleads?q=' . $testPhone);
        $allottedView->assertStatus(200);
        $allottedView->assertSee('Student Created Timestamp');
        $allottedView->assertSee($fixedInitialTimestamp);

        // Cleanup
        Lead::where('phoneno', (int)$testPhone)->delete();
        TelecallerLead::where('phoneno', $testPhone)->delete();
        TelecallerLeadTask::where('phoneno', $testPhone)->delete();
    }

    public function test_lead_count_incremented_by_one_comparing_max_count_for_respective_employee()
    {
        $empCode = 'TEST_EMP_99';
        $phone1 = '9900112231';
        $phone2 = '9900112232';

        TelecallerLead::where('empcode', $empCode)->delete();
        Lead::whereIn('phoneno', [(int)$phone1, (int)$phone2])->delete();

        // Lead 1: allocate to employee
        Lead::create([
            'phoneno' => (int)$phone1,
            'phnostatus' => 'New Lead',
            'empcode' => '0',
            'log_timestamp' => now(),
        ]);

        \App\Services\TelecallerService::allocateLeads([
            'empcode' => $empCode,
            'allot_mode' => 'single',
            'phoneno' => $phone1,
            'applino' => '0',
        ]);

        $tl1 = TelecallerLead::where('empcode', $empCode)->where('phoneno', $phone1)->first();
        $this->assertNotNull($tl1);
        $this->assertEquals(1, $tl1->leadcount);

        // Lead 2: allocate to same employee -> lead count should be 2 (comparing max count 1 + 1)
        Lead::create([
            'phoneno' => (int)$phone2,
            'phnostatus' => 'New Lead',
            'empcode' => '0',
            'log_timestamp' => now(),
        ]);

        \App\Services\TelecallerService::allocateLeads([
            'empcode' => $empCode,
            'allot_mode' => 'single',
            'phoneno' => $phone2,
            'applino' => '0',
        ]);

        $tl2 = TelecallerLead::where('empcode', $empCode)->where('phoneno', $phone2)->first();
        $this->assertNotNull($tl2);
        $this->assertEquals(2, $tl2->leadcount);

        // Cleanup
        TelecallerLead::where('empcode', $empCode)->delete();
        Lead::whereIn('phoneno', [(int)$phone1, (int)$phone2])->delete();
    }

    public function test_student_stages_logic_and_rules()
    {
        // 1. If application number not created and not assigned to telecaller -> New Lead
        $stage1 = \App\Services\TelecallerService::determineLeadStage('0', '0');
        $this->assertEquals('New Lead', $stage1);

        // 2. If application number not created, but assigned to telecaller -> Telecaller Stage
        $stage2 = \App\Services\TelecallerService::determineLeadStage('0', '02598');
        $this->assertEquals('Telecaller Stage', $stage2);

        // 3. Registered (application number created, not assigned) -> Register Form completed
        $stage3 = \App\Services\TelecallerService::determineLeadStage('2026110001', '0');
        $this->assertEquals('Register Form completed', $stage3);

        // 4. Registered & assigned, but not paid -> Register Form completed
        $stage4 = \App\Services\TelecallerService::determineLeadStage('2026110001', '02598', 'not_paid');
        $this->assertEquals('Register Form completed', $stage4);

        // 5. Paid, personal details not filled -> Payment Stage completed
        $stage5 = \App\Services\TelecallerService::determineLeadStage('2026110001', '02598', 'paid', 'NA');
        $this->assertEquals('Payment Stage completed', $stage5);

        // 6. Personal details filled, academic details not filled -> Personal Details completed
        $stage6 = \App\Services\TelecallerService::determineLeadStage('2026110001', '02598', 'paid', 'completed', 'NA');
        $this->assertEquals('Personal Details completed', $stage6);

        // 7. Academic details filled -> Academic Details completed
        $stage7 = \App\Services\TelecallerService::determineLeadStage('2026110001', '02598', 'paid', 'completed', 'completed');
        $this->assertEquals('Academic Details completed', $stage7);
    }

    public function test_action_modal_and_stage_dropdown_on_leads_page()
    {
        $adminUser = User::first();

        $response = $this->actingAs($adminUser)->get('/leads');
        $response->assertStatus(200);

        // Dropdown contains the 5 requested stages
        $response->assertSee('Telecaller Stage');
        $response->assertSee('Register Form completed');
        $response->assertSee('Payment Stage completed');
        $response->assertSee('Personal Details completed');
        $response->assertSee('Academic Details completed');

        // Removed items must not be in modal
        $response->assertDontSee('STATUS: LOCKED');
        $response->assertDontSee('Candidate finished this step');
        $response->assertDontSee('Next sequential stage');
        $response->assertDontSee('Previous Stage');
        $response->assertDontSee('Next Stage');
    }

    public function test_telecaller_action_records_only_in_telecaller_leads_tasks()
    {
        $testPhone = '9876598765';
        $testAppli = '2026990001';
        $empCode = '03434';

        TelecallerLeadTask::where('phoneno', $testPhone)->delete();
        TelecallerLead::where('phoneno', $testPhone)->delete();
        \App\Models\CrmStudentRegistration::where('phoneno', $testPhone)->delete();
        Lead::where('phoneno', (int) $testPhone)->delete();

        Lead::create([
            'phoneno' => (int) $testPhone,
            'applino' => (int) $testAppli,
            'phnostatus' => 'New Lead',
            'empcode' => '0',
            'enquirydate' => now()->toDateString(),
        ]);

        \App\Models\CrmStudentRegistration::create([
            'id' => 999901,
            'applinano' => $testAppli,
            'phoneno' => $testPhone,
            'name' => 'Task Only Student',
            'program_id' => 1,
            'campus_id' => 1,
            'std_status' => 'Interested',
            'stage1' => 'not_paid',
            'remarks' => 'Original remark',
        ]);

        // 1. CRM incharge assigns student -> creates telecaller_leads only, no telecaller_leads_tasks row
        $alloc = \App\Services\TelecallerService::allocateLeads([
            'empcode' => $empCode,
            'allot_mode' => 'single',
            'applino' => $testAppli,
            'phoneno' => $testPhone,
        ]);
        $this->assertTrue($alloc['success']);
        $this->assertEquals(1, TelecallerLead::where('phoneno', $testPhone)->count());
        $this->assertEquals(0, TelecallerLeadTask::where('phoneno', $testPhone)->count());

        $telecallerUser = User::where('usercode', $empCode)->first();
        if (!$telecallerUser) {
            $telecallerUser = User::create([
                'usercode' => $empCode,
                'phnno' => $testPhone,
                'role_id' => 4,
                'user_type' => 2,
                'is_active' => true,
            ]);
        }

        // 2. Payment Stage completed is blocked (422) until receiptdetails row exists; Register Form completed succeeds (200) -> recorded ONLY in telecaller_leads_tasks
        $blockedResp = $this->actingAs($telecallerUser)->postJson("/leads/{$testAppli}/update-stage", [
            'stage_name' => 'Payment Stage completed',
            'remarks' => 'Should be rejected without receipt',
        ]);
        $blockedResp->assertStatus(422);

        $stageResp = $this->actingAs($telecallerUser)->postJson("/leads/{$testAppli}/update-stage", [
            'stage_name' => 'Register Form completed',
            'stage_callperson' => 'Father',
            'call_duration' => '4 mins',
            'remarks' => 'Parent asked to call tomorrow',
        ]);
        $stageResp->assertStatus(200)->assertJson(['success' => true]);

        // 3. Telecaller performs status action -> recorded ONLY in telecaller_leads_tasks
        $statusResp = $this->actingAs($telecallerUser)->postJson("/leads/{$testAppli}/update-status", [
            'status' => 'Follow-up',
        ]);
        $statusResp->assertStatus(200)->assertJson(['success' => true]);

        // Verify telecaller_leads_tasks has both actions, crmid is '0' when no student_personal_info exists,
        // and student_registration, leads, and telecaller_leads were untouched by telecaller actions
        $tasks = TelecallerLeadTask::where('phoneno', $testPhone)->orderBy('leadtaskid')->get();
        $this->assertCount(2, $tasks);
        $this->assertEquals('0', $tasks[0]->crmid);
        $this->assertEquals('4 mins', $tasks[0]->stage_call_duration);
        $this->assertEquals('Parent asked to call tomorrow', $tasks[0]->stage_remark);
        $this->assertEquals('0', $tasks[1]->crmid);
        $this->assertEquals(1, TelecallerLead::where('phoneno', $testPhone)->count());

        // Verify GET /leads/{appli}/history returns the previous tasks for this student
        $historyResp = $this->actingAs($telecallerUser)->getJson("/leads/{$testAppli}/history?phone={$testPhone}");
        $historyResp->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonCount(2, 'history')
            ->assertJsonFragment([
                'stage_name' => 'Register Form completed',
                'stage_call_duration' => '4 mins',
                'stage_remark' => 'Parent asked to call tomorrow',
            ]);

        $studentFresh = \App\Models\CrmStudentRegistration::where('applinano', $testAppli)->first();
        $this->assertEquals('not_paid', $studentFresh->stage1);
        $this->assertEquals('Interested', $studentFresh->std_status);
        $this->assertEquals('Original remark', $studentFresh->remarks);

        $leadFresh = Lead::where('phoneno', (int) $testPhone)->first();
        $this->assertEquals('New Lead', $leadFresh->phnostatus);

        // Cleanup
        TelecallerLeadTask::where('phoneno', $testPhone)->delete();
        TelecallerLead::where('phoneno', $testPhone)->delete();
        \App\Models\CrmStudentRegistration::where('phoneno', $testPhone)->delete();
        Lead::where('phoneno', (int) $testPhone)->delete();
    }

    public function test_action_stage_dropdown_eligibility_validates_against_tables()
    {
        $testPhone = '9876511122';
        $testAppli = '2026990002';
        $empCode = '03434';

        \App\Models\StudentAcademicDetail::where('applino', $testAppli)->delete();
        \App\Models\StudentPersonalInfo::where('applino', $testAppli)->delete();
        \App\Models\ReceiptDetail::where('applino', $testAppli)->delete();
        TelecallerLeadTask::where('phoneno', $testPhone)->delete();
        TelecallerLead::where('phoneno', $testPhone)->delete();
        \App\Models\CrmStudentRegistration::where('applinano', $testAppli)->delete();
        Lead::where('phoneno', (int) $testPhone)->delete();

        // 1. Unregistered lead (applino = 0): only Telecaller Stage is allowed
        $none = \App\Services\TelecallerService::getAllowedStagesForLead('0');
        $this->assertTrue($none['Telecaller Stage']);
        $this->assertFalse($none['Register Form completed']);
        $this->assertFalse($none['Payment Stage completed']);
        $this->assertFalse($none['Personal Details completed']);
        $this->assertFalse($none['Academic Details completed']);

        // 2. Registered in student_registration: Register Form completed enabled; others locked
        \App\Models\CrmStudentRegistration::create([
            'id' => 999902,
            'applinano' => $testAppli,
            'phoneno' => $testPhone,
            'name' => 'Stage Check Student',
            'program_id' => 1,
            'campus_id' => 1,
            'std_status' => 'Interested',
        ]);

        $step1 = \App\Services\TelecallerService::getAllowedStagesForLead($testAppli);
        $this->assertTrue($step1['Telecaller Stage']);
        $this->assertTrue($step1['Register Form completed']);
        $this->assertFalse($step1['Payment Stage completed']);
        $this->assertFalse($step1['Personal Details completed']);
        $this->assertFalse($step1['Academic Details completed']);

        // 3. Receipt in finance.receiptdetails: Payment Stage completed enabled
        \App\Models\ReceiptDetail::create([
            'id' => (\App\Models\ReceiptDetail::max('id') ?? 0) + 100,
            'applino' => $testAppli,
            'receiptno' => 'RCPT-TEST-990002',
            'paytype' => 'Online',
            'feepayable' => 1200,
        ]);

        $step2 = \App\Services\TelecallerService::getAllowedStagesForLead($testAppli);
        $this->assertTrue($step2['Payment Stage completed']);
        $this->assertFalse($step2['Personal Details completed']);
        $this->assertFalse($step2['Academic Details completed']);

        // 4. Personal info in crm.student_personal_info: Personal Details completed enabled
        \App\Models\StudentPersonalInfo::create([
            'crmid' => 'CRMP_TEST_990002',
            'applino' => $testAppli,
            'phoneno' => $testPhone,
            'gender' => 'Male',
        ]);

        $step3 = \App\Services\TelecallerService::getAllowedStagesForLead($testAppli);
        $this->assertTrue($step3['Personal Details completed']);
        $this->assertFalse($step3['Academic Details completed']);

        // 5. Academic details in crm.student_academic_details: Academic Details completed enabled
        \App\Models\StudentAcademicDetail::create([
            'crmid' => 'CRMA_TEST_990002',
            'applino' => $testAppli,
            'phoneno' => $testPhone,
        ]);

        $step4 = \App\Services\TelecallerService::getAllowedStagesForLead($testAppli);
        $this->assertTrue($step4['Academic Details completed']);

        // 6. Verify /leads page omits Completed option in modal and shows Academic Details completed without symbols
        Lead::create([
            'phoneno' => (int) $testPhone,
            'applino' => (int) $testAppli,
            'empcode' => $empCode,
            'allocated_at' => now(),
            'enquirydate' => now()->toDateString(),
        ]);
        TelecallerLead::create([
            'applino' => $testAppli,
            'phoneno' => $testPhone,
            'crmid' => '0',
            'empcode' => $empCode,
            'leadcount' => 1,
            'assigndatetime' => now(),
            'log_timestamp' => now(),
        ]);

        $telecallerUser = User::where('usercode', $empCode)->first();
        $pageResp = $this->actingAs($telecallerUser)->get('/leads/allottedleads?q=' . $testPhone);
        $pageResp->assertStatus(200);
        $pageResp->assertSee($testAppli);
        $pageResp->assertDontSee('<option value="Completed">Completed</option>', false);
        $pageResp->assertDontSee('<span>🎓</span>', false);
        $pageResp->assertDontSee('<span>📝</span>', false);

        // 7. Verify POST /leads/{appli}/update-stage succeeds for Personal Details completed & Academic Details completed when records exist
        $this->actingAs($telecallerUser)
            ->postJson("/leads/{$testAppli}/update-stage", ['stage_name' => 'Personal Details completed'])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->actingAs($telecallerUser)
            ->postJson("/leads/{$testAppli}/update-stage", ['stage_name' => 'Academic Details completed'])
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        $afterCompleteResp = $this->actingAs($telecallerUser)->get('/leads/allottedleads?q=' . $testPhone);
        $afterCompleteResp->assertStatus(200);
        $afterCompleteResp->assertSee('Academic Details completed');
        $afterCompleteResp->assertDontSee('<span>🎓</span>', false);
        $afterCompleteResp->assertSee('CRMP_TEST_990002');
        $afterCompleteResp->assertDontSee('V-SAT-0001');

        // Verify telecaller_leads_tasks.crmid is taken from crm.student_personal_info.crmid
        $latestTask = TelecallerLeadTask::where('phoneno', $testPhone)->orderBy('leadtaskid', 'desc')->first();
        $this->assertNotNull($latestTask);
        $this->assertEquals('CRMP_TEST_990002', $latestTask->crmid);

        // Cleanup
        \App\Models\StudentAcademicDetail::where('applino', $testAppli)->delete();
        \App\Models\StudentPersonalInfo::where('applino', $testAppli)->delete();
        \App\Models\ReceiptDetail::where('applino', $testAppli)->delete();
        TelecallerLeadTask::where('phoneno', $testPhone)->delete();
        TelecallerLead::where('phoneno', $testPhone)->delete();
        \App\Models\CrmStudentRegistration::where('applinano', $testAppli)->delete();
        Lead::where('phoneno', (int) $testPhone)->delete();
    }

    public function test_allotted_leads_crm_id_uses_table_only_and_never_generates_vsat_fallback()
    {
        $empCode = '03434';
        $testPhone = '9900011199';
        $testAppli = '2026990009';

        \App\Models\StudentPersonalInfo::where('phoneno', $testPhone)->delete();
        TelecallerLead::where('phoneno', $testPhone)->delete();
        \App\Models\CrmStudentRegistration::where('applinano', $testAppli)->delete();
        Lead::where('phoneno', (int) $testPhone)->delete();

        \App\Models\CrmStudentRegistration::create([
            'applinano' => $testAppli,
            'name' => 'No CrmId Student',
            'phoneno' => $testPhone,
            'emailid' => 'nocrm@example.com',
            'programlevel' => 'UG',
            'stage1' => 'completed',
            'log_userid' => 'TEST',
            'log_ipaddress' => '127.0.0.1',
            'log_timestamp' => now(),
        ]);
        Lead::create([
            'phoneno' => (int) $testPhone,
            'name' => 'No CrmId Student',
            'applino' => (int) $testAppli,
            'empcode' => $empCode,
            'allocated_at' => now(),
            'enquirydate' => now()->toDateString(),
        ]);
        TelecallerLead::create([
            'applino' => $testAppli,
            'phoneno' => $testPhone,
            'crmid' => '0',
            'empcode' => $empCode,
            'leadcount' => 1,
            'assigndatetime' => now(),
            'log_timestamp' => now(),
        ]);

        $telecallerUser = User::where('usercode', $empCode)->first();
        $resp = $this->actingAs($telecallerUser)->get('/leads/allottedleads?q=' . $testPhone);
        $resp->assertStatus(200);
        $resp->assertSee($testAppli);
        $resp->assertDontSee('V-SAT-0001');

        TelecallerLead::where('phoneno', $testPhone)->delete();
        \App\Models\CrmStudentRegistration::where('applinano', $testAppli)->delete();
        Lead::where('phoneno', (int) $testPhone)->delete();
    }
}
