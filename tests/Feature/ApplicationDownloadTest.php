<?php

namespace Tests\Feature;

use App\Models\CrmStudentRegistration;
use App\Models\ExamSchedule;
use App\Models\User;
use Modules\Dashboards\Http\Controllers\ApplicationController;
use Tests\TestCase;

class ApplicationDownloadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'pgsql',
            'database.connections.pgsql.database' => 'vu_crm26',
        ]);
    }

    public function test_application_download_returns_pdf_attachment(): void
    {
        $id = 'VU2026909090';
        $controller = new ApplicationController();
        $response = $controller->download($id);

        $this->assertNotNull($response);
        $this->assertInstanceOf(\Illuminate\Http\Response::class, $response);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_application_download_http_route_is_publicly_accessible(): void
    {
        $response = $this->get('/application/VU2026909090/download');

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition'));
    }

    public function test_step1_date_of_birth_rejects_future_dates(): void
    {
        $user = User::firstOrCreate(
            ['phnno' => '9999900001'],
            ['name' => 'Unpaid Test', 'pwd' => bcrypt('secret'), 'role_id' => 5, 'user_type' => 1, 'usercode' => 'VU2027TEST99']
        );
        CrmStudentRegistration::updateOrCreate(
            ['phoneno' => '9999900001'],
            ['applinano' => 'VU2027TEST99', 'name' => 'Unpaid Test', 'stage1' => 'pending', 'stage4' => 'pending']
        );

        // Date of birth in the future
        $invalidDob = now()->addDay()->format('Y-m-d');
        $validDob = now()->subYears(18)->format('Y-m-d');

        $payload = [
            'step' => 1,
            'candidate_name' => 'Candidate Test',
            'parent_name' => 'Parent Test',
            'student_number' => '9999900001',
            'parent_phone_no' => '9876543211',
            'dob' => $invalidDob,
            'gender' => 'Male',
            'category' => 'OC',
            'country' => '1',
            'state' => '1',
            'district' => '1',
            'city_mandal' => 'Test City',
            'village' => 'Test Village',
            'pincode' => '522213',
        ];

        // Should fail validation due to future date
        $response = $this->actingAs($user)->post('/dashboards/save-step', $payload);
        $response->assertSessionHasErrors('dob');

        // Valid date in the past
        $payload['dob'] = $validDob;
        $validResponse = $this->actingAs($user)->post('/dashboards/save-step', $payload);
        $validResponse->assertSessionHasNoErrors();
    }

    public function test_step2_academic_details_view(): void
    {
        $user = User::firstOrCreate(
            ['phnno' => '9999900001'],
            ['name' => 'Unpaid Test', 'pwd' => bcrypt('secret'), 'role_id' => 5, 'user_type' => 1, 'usercode' => 'VU2027TEST99']
        );
        CrmStudentRegistration::updateOrCreate(
            ['phoneno' => '9999900001'],
            ['applinano' => 'VU2027TEST99', 'name' => 'Unpaid Test', 'stage1' => 'pending', 'stage4' => 'pending']
        );

        // Make sure user's student record allows step 2
        CrmStudentRegistration::where('phoneno', $user->phnno)->update([
            'stage1' => 'completed',
        ]);

        // Check step 2 view for UG: Higher Academic details must NOT appear
        $response = $this->actingAs($user)->get('/dashboards?step=2');
        $response->assertStatus(200);
        $response->assertDontSee('id="btn-toggle-ug-pg"', false);

        // Check step 2 view for PG: Higher Academic details MUST appear
        CrmStudentRegistration::where('phoneno', $user->phnno)->update(['programlevel' => 'PG', 'programid' => null]);
        $responsePg = $this->actingAs($user)->get('/dashboards?step=2');
        $responsePg->assertStatus(200);
        $responsePg->assertSee('Undergraduate (UG / B.Tech / Degree) Details');

        // Restore to UG
        CrmStudentRegistration::where('phoneno', $user->phnno)->update(['programlevel' => 'UG', 'programid' => 1]);
    }

    public function test_exam_slot_booking_blocks_date_selection_when_in_exam_schedule(): void
    {
        $user = User::firstOrCreate(
            ['phnno' => '9999900001'],
            ['name' => 'Unpaid Test', 'pwd' => bcrypt('secret'), 'role_id' => 5, 'user_type' => 1, 'usercode' => 'VU2027TEST99']
        );
        CrmStudentRegistration::updateOrCreate(
            ['phoneno' => '9999900001'],
            ['applinano' => 'VU2027TEST99', 'name' => 'Unpaid Test', 'stage1' => 'pending', 'stage4' => 'pending']
        );

        $student = CrmStudentRegistration::where('phoneno', $user->phnno)->first()
            ?? CrmStudentRegistration::first();
        $this->assertNotNull($student);

        $user->update([
            'usercode' => $student->applinano,
            'phnno' => $student->phoneno,
        ]);

        // Enable steps 1-4 for sequential step access (stage1..4 completed/paid)
        $student->update([
            'stage1' => 'paid',
            'stage2' => 'completed',
            'stage3' => 'completed',
            'stage4' => 'paid',
        ]);

        \App\Models\StudentPersonalInfo::updateOrCreate(
            ['phoneno' => $student->phoneno],
            [
                'crmid' => $student->crmid ?? 'V-SAT-0001',
                'applino' => $student->applinano,
                'name' => $student->name,
                'gender' => 'Male',
                'category' => 'OC',
            ]
        );

        \App\Models\StudentAcademicDetail::updateOrCreate(
            ['applino' => $student->applinano],
            [
                'crmid' => $student->crmid ?? 'V-SAT-0001',
                'phoneno' => $student->phoneno,
                'progmid' => $student->programid ?? 1,
                'branchid' => $student->branchid ?? 8,
                'tenth_school_name' => 'School Test',
                'pu2_college_name' => 'College Test',
            ]
        );

        // Case 1: Exam date in the immediate future past cutoff (e.g. tomorrow) -> locked
        ExamSchedule::updateOrCreate(
            ['applino' => $student->applinano],
            [
                'crmid' => $student->crmid ?? 'V-SAT-0001',
                'phoneno' => $student->phoneno,
                'examdate' => now()->subDay()->format('Y-m-d'),
                'exam_slot' => 'S1 11:00 AM to 1:00 PM',
                'exam_status' => 1,
            ]
        );

        $response = $this->actingAs($user)->get('/dashboards?step=5');
        $response->assertStatus(200);

        // Date input must be readonly and blocked
        $response->assertSee('readonly style=background-color:#f1f5f9;cursor:not-allowed;pointer-events:none;', false);
        $response->assertSee('Date Locked');

        // Case 2: Exam date with ample advance notice (e.g. 5 days away) -> editable
        ExamSchedule::updateOrCreate(
            ['applino' => $student->applinano],
            [
                'crmid' => $student->crmid ?? 'V-SAT-0001',
                'phoneno' => $student->phoneno,
                'examdate' => now()->addDays(5)->format('Y-m-d'),
                'exam_slot' => 'S1 11:00 AM to 1:00 PM',
                'exam_status' => 1,
            ]
        );

        $response2 = $this->actingAs($user)->get('/dashboards?step=5');
        $response2->assertStatus(200);
        $response2->assertSee('Reschedule Allowed');
    }

    public function test_crm_id_service_never_collides_with_existing_personal_info(): void
    {
        $existingId = \Modules\Dashboards\Models\StudentPersonalInfo::whereNotNull('crmid')->value('crmid');
        $this->assertNotEmpty($existingId);

        $dummyStudent = new \Modules\Crm\Models\CrmStudentRegistration();
        $dummyStudent->applinano = '9999999999';
        $dummyStudent->programlevel = 'UG';
        $dummyStudent->crmid = $existingId; // deliberately set to existing ID

        $generatedId = \Modules\Crm\Services\CrmIdService::generateForStudent($dummyStudent);
        $this->assertNotEquals($existingId, $generatedId);
        $this->assertFalse(\Modules\Crm\Services\CrmIdService::isCrmIdInUse($generatedId, '9999999999'));
    }

    public function test_stage2_doctoral_saves_and_renders_postgraduate_1_and_2(): void
    {
        $suffix = (string) rand(100000, 999999);
        $phdPhone = '9988' . $suffix;
        $phdApplino = 'VU2026' . $suffix;

        User::where('phnno', $phdPhone)->orWhere('usercode', $phdApplino)->delete();
        CrmStudentRegistration::where('phoneno', $phdPhone)->orWhere('applinano', $phdApplino)->delete();

        $user = User::create([
            'phnno' => $phdPhone,
            'name' => 'Doctoral Candidate',
            'pwd' => bcrypt('secret'),
            'role_id' => 5,
            'user_type' => 1,
            'usercode' => $phdApplino,
        ]);

        $student = CrmStudentRegistration::create([
            'phoneno' => $phdPhone,
            'applinano' => $phdApplino,
            'name' => 'Doctoral Candidate',
            'programlevel' => 'DOCTORAL',
            'stage1' => 'completed',
            'stage2' => 'pending',
            'stage4' => 'pending',
        ]);

        $payload = [
            'step' => 2,
            'degree_level' => 'phd',
            'school_co_name' => 'ZPH School',
            'boad_of_study' => 'State Board',
            'roll_no' => '10TH9988',
            'passing_year' => 2016,
            'tenth_marks_conducted' => 600,
            'tenth_marks_earned' => 550,
            'tenth_percentage' => 91.67,
            'tenth_marks_type' => 'Marks',

            'inter_college' => 'Junior College',
            'city' => 'Guntur',
            'jr_state' => 'Andhra Pradesh',
            'jr_district' => 'Guntur',
            'subset_choice' => 'Second Year',
            'inter_year' => 2018,
            'inter_hallticket' => 'INTER9988',
            'pu2_marks_type' => 'Marks',
            'pu2_marks_conducted' => 1000,
            'pu2_marks_earned' => 920,
            'pu2_percentage' => 92.00,

            // UG
            'ug_college_name' => 'University College of Eng',
            'ug_university_board' => 'JNTUK',
            'ug_roll_no' => '18UG1001',
            'ug_passed_year' => 2022,
            'ug_cgpa' => '8.80',
            'ug_current_status' => 'Completed',

            // PG 1
            'pg_college_name' => 'National Institute of Tech PG1',
            'pg_university_board' => 'NIT',
            'pg_roll_no' => '22PG1001',
            'pg_passed_year' => 2024,
            'pg_cgpa' => '9.20',
            'pg_current_status' => 'Completed',
            'pg_study_type' => 'Distance',
            'pg1_doc_type' => 'CMM',

            // PG 2
            'pg2_college_name' => 'Indian Institute of Tech PG2',
            'pg2_university_board' => 'IIT',
            'pg2_roll_no' => '24PG2001',
            'pg2_passed_year' => 2026,
            'pg2_cgpa' => '9.50',
            'pg2_current_status' => 'Completed',
            'pg2_study_type' => 'Online',
            'pg2_doc_type' => 'PC_OD',
        ];

        // Verify Step 2 view does not contain the removed 12 subfields
        $responsePhdStep2 = $this->actingAs($user)->get('/dashboards?step=2');
        $responsePhdStep2->assertStatus(200);
        $responsePhdStep2->assertSee('name="pg_study_type"', false);
        $responsePhdStep2->assertSee('name="pg2_study_type"', false);
        $responsePhdStep2->assertSee('name="pg1_doc_type"', false);
        $responsePhdStep2->assertSee('name="pg2_doc_type"', false);
        $responsePhdStep2->assertDontSee('name="pg1_cmm_memo_no"', false);
        $responsePhdStep2->assertDontSee('name="pg1_cmm_cgpa"', false);
        $responsePhdStep2->assertDontSee('name="pg1_cmm_year"', false);
        $responsePhdStep2->assertDontSee('name="pg1_pc_od_no"', false);
        $responsePhdStep2->assertDontSee('name="pg1_pc_od_year"', false);
        $responsePhdStep2->assertDontSee('name="pg1_pc_od_status"', false);
        $responsePhdStep2->assertDontSee('name="pg2_cmm_memo_no"', false);
        $responsePhdStep2->assertDontSee('name="pg2_cmm_cgpa"', false);
        $responsePhdStep2->assertDontSee('name="pg2_cmm_year"', false);
        $responsePhdStep2->assertDontSee('name="pg2_pc_od_no"', false);
        $responsePhdStep2->assertDontSee('name="pg2_pc_od_year"', false);
        $responsePhdStep2->assertDontSee('name="pg2_pc_od_status"', false);
        $responsePhdStep2->assertDontSee('Not Applicable');

        $saveResponse = $this->actingAs($user)->post('/dashboards/save-step', $payload);
        $saveResponse->assertRedirect();

        // Verify database records
        $savedDetail = \Modules\Dashboards\Models\StudentAcademicDetail::where('applino', $phdApplino)->first();
        $this->assertNotNull($savedDetail);
        $this->assertEquals('National Institute of Tech PG1', $savedDetail->pg_college_name);
        $this->assertEquals('Indian Institute of Tech PG2', $savedDetail->pg2_college_name);
        $this->assertEquals('24PG2001', $savedDetail->pg2_roll_no);
        $this->assertEquals(2026, $savedDetail->pg2_passed_year);
        $this->assertEquals('Distance', $savedDetail->pg_study_type);
        $this->assertEquals('Online', $savedDetail->pg2_study_type);
        $this->assertEquals('CMM', $savedDetail->pg1_doc_type);
        $this->assertEquals('PC_OD', $savedDetail->pg2_doc_type);

        // Verify ApplicationController builds PG 1 and PG 2
        $controller = new ApplicationController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('buildStudentApplicationData');
        $method->setAccessible(true);
        $appData = $method->invoke($controller, $phdApplino);

        $this->assertEquals('phd', $appData->degree_level);
        $this->assertEquals('National Institute of Tech PG1', $appData->academic->pg_college);
        $this->assertEquals('Indian Institute of Tech PG2', $appData->academic->pg2_college);

        // Verify blade views render Postgraduate 1 and Postgraduate 2 (PG programme / PG 2 programme for PhD)
        $view1 = view('dashboards::application-form', ['student' => $appData])->render();
        $this->assertStringContainsString('PG programme', $view1);
        $this->assertStringContainsString('National Institute of Tech PG1', $view1);
        $this->assertStringContainsString('PG 2 programme', $view1);
        $this->assertStringContainsString('Indian Institute of Tech PG2', $view1);

        $view2 = view('application-form', ['student' => $appData])->render();
        $this->assertStringContainsString('PG programme', $view2);
        $this->assertStringContainsString('National Institute of Tech PG1', $view2);
        $this->assertStringContainsString('PG 2 programme', $view2);
        $this->assertStringContainsString('Indian Institute of Tech PG2', $view2);
    }
}
