<?php

namespace Tests\Feature;

use App\Models\CrmStudentRegistration;
use App\Models\Lead;
use App\Models\LoginOtp;
use App\Models\User;
use Tests\TestCase;

class StudentMobileLoginTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'pgsql',
            'database.connections.pgsql.database' => 'vu_crm',
        ]);
    }

    public function test_student_entering_mobile_has_usercode_zero_and_unique_phone(): void
    {
        $testPhone1 = '9888877771';
        $testPhone2 = '9888877772';

        // Clean up
        foreach ([$testPhone1, $testPhone2] as $ph) {
            LoginOtp::where('identifier', $ph)->delete();
            User::where('phnno', $ph)->delete();
            Lead::where('phoneno', (int)$ph)->delete();
            CrmStudentRegistration::where('phoneno', $ph)->delete();
        }

        // Student 1 enters mobile number
        $resp1 = $this->postJson(route('login.send-otp'), ['phone' => $testPhone1]);
        $resp1->assertStatus(200);
        $resp1->assertJson(['success' => true]);

        // Student 2 enters mobile number
        $resp2 = $this->postJson(route('login.send-otp'), ['phone' => $testPhone2]);
        $resp2->assertStatus(200);
        $resp2->assertJson(['success' => true]);

        // Both must exist in master.login with usercode = '0' and unique phnno
        $user1 = User::where('phnno', $testPhone1)->first();
        $this->assertNotNull($user1);
        $this->assertEquals('0', $user1->usercode, 'Usercode should be zero before registration');
        $this->assertEquals(1, $user1->user_type, 'User type should be 1 (Student)');
        $this->assertEquals(5, $user1->role_id, 'Role ID should be 5 (Student)');

        $user2 = User::where('phnno', $testPhone2)->first();
        $this->assertNotNull($user2);
        $this->assertEquals('0', $user2->usercode, 'Usercode should be zero before registration');
        $this->assertEquals(1, $user2->user_type);
        $this->assertEquals(5, $user2->role_id);

        // Check login_otp also has usercode '0'
        $otp1 = LoginOtp::where('identifier', $testPhone1)->latest('otp_id')->first();
        $this->assertEquals('0', $otp1->usercode);

        // Clean up Student 2
        LoginOtp::where('identifier', $testPhone2)->delete();
        User::where('phnno', $testPhone2)->delete();
        Lead::where('phoneno', (int)$testPhone2)->delete();
    }

    public function test_filling_registration_form_generates_usercode_and_replaces_zero(): void
    {
        $testPhone = '9888877771';

        // Clean up first
        LoginOtp::where('identifier', $testPhone)->delete();
        User::where('phnno', $testPhone)->delete();
        Lead::where('phoneno', (int)$testPhone)->delete();
        CrmStudentRegistration::where('phoneno', $testPhone)->delete();

        // 1. Student enters mobile number -> usercode must be '0'
        $this->postJson(route('login.send-otp'), ['phone' => $testPhone]);
        $userBefore = User::where('phnno', $testPhone)->first();
        $this->assertNotNull($userBefore);
        $this->assertEquals('0', $userBefore->usercode, 'Usercode must be 0 prior to completing registration');

        // 2. Submit registration form for student
        $regData = [
            'applicant_name' => 'Test Candidate One',
            'mobile'         => $testPhone,
            'email'          => 'testcandidate1@example.com',
            'campus_id'      => '1',
            'program_level'  => 'UG',
            'progid'         => 1,
            'branch_id'      => 1,
            'ableto'         => '1',
        ];

        $regResponse = $this->post(route('register.post'), $regData);
        $regResponse->assertRedirect();

        // 2. Verify master.login usercode zero is replaced with the generated code
        $updatedUser = User::where('phnno', $testPhone)->first();
        $this->assertNotNull($updatedUser);
        $this->assertNotEquals('0', $updatedUser->usercode, 'Usercode zero must be replaced with generated code');
        $this->assertNotEmpty($updatedUser->usercode);

        // 3. Verify crm.student_registration has the exact same generated usercode
        $crmStudent = CrmStudentRegistration::where('phoneno', $testPhone)->first();
        $this->assertNotNull($crmStudent);
        $this->assertEquals($updatedUser->usercode, $crmStudent->applinano, 'Registration applinano must match generated usercode');

        // 4. Verify master.login_otp has been updated from '0' to the generated usercode
        $otp = LoginOtp::where('identifier', $testPhone)->latest('otp_id')->first();
        if ($otp) {
            $this->assertEquals($updatedUser->usercode, $otp->usercode);
        }

        // 5. Verify crm.leads has the generated usercode
        $lead = Lead::where('phoneno', (int)$testPhone)->first();
        $this->assertNotNull($lead);
        $this->assertEquals((int)$updatedUser->usercode, $lead->applino);

        // Clean up
        LoginOtp::where('identifier', $testPhone)->delete();
        User::where('phnno', $testPhone)->delete();
        Lead::where('phoneno', (int)$testPhone)->delete();
        CrmStudentRegistration::where('phoneno', $testPhone)->delete();
    }

    public function test_employee_entering_mobile_number_is_not_treated_as_student(): void
    {
        $employeePhone = '7075624098'; // Employee Kamalesh in sample data

        $response = $this->postJson(route('login.send-otp'), [
            'phone' => $employeePhone,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify that master.login for this phone does not have user_type = 1 (Student)
        $user = User::where('phnno', $employeePhone)->first();
        if ($user) {
            $this->assertNotEquals(1, $user->user_type, 'Employee should not have user_type 1 (Student)');
        }
    }

    public function test_step4_documents_are_not_autofilled_and_application_pdf_downloads(): void
    {
        $studentUser = User::where('user_type', 1)->first();
        $this->assertNotNull($studentUser, 'Expected at least one student user in vu_crm');

        $step4Response = $this->actingAs($studentUser)->get('/dashboards?step=4');
        $step4Response->assertStatus(200);
        $step4Response->assertDontSee('SSC_Marks_Memo_Verified.pdf');
        $step4Response->assertDontSee('Inter_Plus2_Memo_Verified.pdf');
        $step4Response->assertDontSee('Aadhaar_Front_Back.pdf');
        $step4Response->assertDontSee('Candidate_Passport_Photo.jpg');

        $appId = $studentUser->usercode ?: '2027000010';

        $showResponse = $this->actingAs($studentUser)->get("/application/{$appId}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('APPLICATION FORM');

        $pdfResponse = $this->actingAs($studentUser)->get("/application/{$appId}/download");
        $pdfResponse->assertStatus(200);
        $this->assertStringContainsString('application/pdf', (string) $pdfResponse->headers->get('content-type'));
        $this->assertStringStartsWith('%PDF-', (string) $pdfResponse->getContent());
    }
}
