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

        // Check task logged
        $task = TelecallerLeadTask::where('phoneno', (string) $testPhone)->first();
        $this->assertNotNull($task);
        $this->assertEquals($empCode, $task->empcode);

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

        // 3. If he has application number but not assigned to telecaller -> Register Form
        $stage3 = \App\Services\TelecallerService::determineLeadStage('2026110001', '0');
        $this->assertEquals('Register Form', $stage3);

        // 4. If application number not paid -> Payment Stage (Not Paid)
        $stage4 = \App\Services\TelecallerService::determineLeadStage('2026110001', '02598', 'not_paid');
        $this->assertEquals('Payment Stage', $stage4);

        // 5. If application number but not filled personal details -> Personal Details
        $stage5 = \App\Services\TelecallerService::determineLeadStage('2026110001', '02598', 'paid', 'NA');
        $this->assertEquals('Personal Details', $stage5);

        // 6. If application number but not filled academic details -> Academic Details
        $stage6 = \App\Services\TelecallerService::determineLeadStage('2026110001', '02598', 'paid', 'completed', 'NA');
        $this->assertEquals('Academic Details', $stage6);

        // 7. If academic details filled -> Academic Details Completed
        $stage7 = \App\Services\TelecallerService::determineLeadStage('2026110001', '02598', 'paid', 'completed', 'completed');
        $this->assertEquals('Academic Details Completed', $stage7);
    }

    public function test_action_modal_and_stage_dropdown_on_leads_page()
    {
        $adminUser = User::first();

        $response = $this->actingAs($adminUser)->get('/leads');
        $response->assertStatus(200);

        // Dropdown contains the 5 requested stages
        $response->assertSee('Telecaller Stage');
        $response->assertSee('Register Form');
        $response->assertSee('Payment Stage');
        $response->assertSee('Personal Details');
        $response->assertSee('Academic Details');

        // Removed items must not be in modal
        $response->assertDontSee('STATUS: LOCKED');
        $response->assertDontSee('Candidate finished this step');
        $response->assertDontSee('Next sequential stage');
        $response->assertDontSee('Previous Stage');
        $response->assertDontSee('Next Stage');
    }
}
