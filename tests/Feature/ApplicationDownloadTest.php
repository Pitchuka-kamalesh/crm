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

    public function test_step2_date_of_birth_rejects_age_below_15(): void
    {
        $user = User::where('role_id', 5)->first() ?? User::first();
        $this->assertNotNull($user);

        // Date of birth 10 years ago (under 15 years old)
        $invalidDob = now()->subYears(10)->format('Y-m-d');
        $validDob = now()->subYears(18)->format('Y-m-d');

        $payload = [
            'step' => 2,
            'parent_name' => 'Parent Test',
            'student_number' => $user->phnno ?: '9888877771',
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

        // Should fail validation due to before_or_equal:now()-15 years
        $response = $this->actingAs($user)->post('/dashboards/save-step', $payload);
        $response->assertSessionHasErrors('dob');

        // Valid age >= 15
        $payload['dob'] = $validDob;
        $validResponse = $this->actingAs($user)->post('/dashboards/save-step', $payload);
        $validResponse->assertSessionHasNoErrors();
    }

    public function test_step3_removes_60_percent_option_and_diploma_choice(): void
    {
        $user = User::where('role_id', 5)->first() ?? User::first();
        $this->assertNotNull($user);

        // Make sure user's student record allows step 3
        CrmStudentRegistration::where('phoneno', $user->phnno)->update([
            'stage1' => 'paid',
            'stage2' => 'completed',
        ]);

        // Check step 3 view
        $response = $this->actingAs($user)->get('/dashboards?step=3');
        $response->assertStatus(200);

        // Option 60> must be removed
        $response->assertDontSee('>= 60%');
        $response->assertDontSee('Grade (>= 60%)');

        // In 12th details Course Choice, Diploma option must be removed
        $response->assertDontSee('value="Diploma"', false);
        $response->assertSee('value="Inter"', false);
    }

    public function test_exam_slot_booking_blocks_date_selection_when_in_exam_schedule(): void
    {
        $user = User::where('role_id', 5)->first() ?? User::first();
        $this->assertNotNull($user);

        $student = CrmStudentRegistration::where('phoneno', $user->phnno)->first()
            ?? CrmStudentRegistration::first();
        $this->assertNotNull($student);

        // Enable steps 1-4 for sequential step access
        $student->update([
            'stage1' => 'paid',
            'stage2' => 'completed',
            'stage3' => 'completed',
            'stage4' => 'completed',
        ]);

        // Ensure exam_schedule record exists
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

        $response = $this->actingAs($user)->get('/dashboards?step=5');
        $response->assertStatus(200);

        // Date input must be readonly and blocked
        $response->assertSee('readonly style=background-color:#f1f5f9;cursor:not-allowed;pointer-events:none;', false);
        $response->assertSee('Date Locked (Already Scheduled)');
    }
}
