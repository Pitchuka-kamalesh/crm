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
            ['name' => 'Unpaid Test', 'pwd' => bcrypt('secret'), 'role_id' => 5, 'user_type' => 1, 'usercode' => '2027999999']
        );
        CrmStudentRegistration::updateOrCreate(
            ['phoneno' => '9999900001'],
            ['applinano' => '2027999999', 'name' => 'Unpaid Test', 'stage1' => 'pending', 'stage4' => 'pending']
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
            ['name' => 'Unpaid Test', 'pwd' => bcrypt('secret'), 'role_id' => 5, 'user_type' => 1, 'usercode' => '2027999999']
        );
        CrmStudentRegistration::updateOrCreate(
            ['phoneno' => '9999900001'],
            ['applinano' => '2027999999', 'name' => 'Unpaid Test', 'stage1' => 'pending', 'stage4' => 'pending']
        );

        // Make sure user's student record allows step 2
        CrmStudentRegistration::where('phoneno', $user->phnno)->update([
            'stage1' => 'completed',
        ]);

        // Check step 2 view for UG: Higher Academic details must NOT appear
        $response = $this->actingAs($user)->get('/dashboards?step=2');
        $response->assertStatus(200);
        $response->assertDontSee('id="btn-toggle-ug-pg"', false);
        $response->assertDontSee('Undergraduate (B.Tech / Degree) Details');

        // Check step 2 view for PG: Higher Academic details MUST appear
        CrmStudentRegistration::where('phoneno', $user->phnno)->update(['programlevel' => 'PG', 'programid' => null]);
        $responsePg = $this->actingAs($user)->get('/dashboards?step=2');
        $responsePg->assertStatus(200);
        $responsePg->assertSee('Undergraduate (B.Tech / Degree) Details');

        // Restore to UG
        CrmStudentRegistration::where('phoneno', $user->phnno)->update(['programlevel' => 'UG', 'programid' => 1]);
    }

    public function test_exam_slot_booking_blocks_date_selection_when_in_exam_schedule(): void
    {
        $user = User::firstOrCreate(
            ['phnno' => '9999900001'],
            ['name' => 'Unpaid Test', 'pwd' => bcrypt('secret'), 'role_id' => 5, 'user_type' => 1, 'usercode' => '2027999999']
        );
        CrmStudentRegistration::updateOrCreate(
            ['phoneno' => '9999900001'],
            ['applinano' => '2027999999', 'name' => 'Unpaid Test', 'stage1' => 'pending', 'stage4' => 'pending']
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
}
