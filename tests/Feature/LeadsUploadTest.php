<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class LeadsUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'pgsql',
            'database.connections.pgsql.database' => 'vu_crm',
        ]);
    }

    public function test_leads_upload_tab_renders_properly_for_crm_incharge(): void
    {
        $user = User::where('usercode', '02598')->first() ?? User::first();
        $response = $this->actingAs($user)->get('/leads/leadsupload');
        $response->assertStatus(200);
        $response->assertSee('Leads Upload');
        $response->assertSee('Download Sample Template');
        $response->assertSee('leads_file');
        $response->assertSee('uploadNotificationArea');
        $response->assertDontSee('}, false); })();');
    }

    public function test_download_sample_template_returns_csv(): void
    {
        $user = User::where('usercode', '02598')->first() ?? User::first();
        $response = $this->actingAs($user)->get('/leads/download-sample');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Mobile Number', $response->streamedContent());
    }

    public function test_upload_csv_with_mobile_numbers_inserts_into_crm_leads(): void
    {
        $phone1 = '96321' . rand(10000, 99999);
        $phone2 = '98543' . rand(10000, 99999);

        // Ensure not in DB
        Lead::whereIn('phoneno', [(int)$phone1, (int)$phone2])->delete();

        $csvData = "Mobile Number\n" . $phone1 . "\n+91 " . $phone2 . "\n" . $phone1 . "\n12345\n";
        $file = UploadedFile::fake()->createWithContent('leads_batch.csv', $csvData);

        $user = User::where('usercode', '02598')->first() ?? User::first();
        $response = $this->actingAs($user)->post('/leads/upload-excel', [
            'leads_file' => $file,
        ]);

        $response->assertRedirect('/leads/leadsupload?tab=upload');
        $response->assertSessionHas('upload_success');

        // Verify inserted in crm.leads
        $lead1 = Lead::where('phoneno', (int)$phone1)->first();
        $this->assertNotNull($lead1);
        $this->assertEquals('New Lead', $lead1->phnostatus);
        $this->assertEquals('0', $lead1->empcode);

        $lead2 = Lead::where('phoneno', (int)$phone2)->first();
        $this->assertNotNull($lead2);
        $this->assertEquals('New Lead', $lead2->phnostatus);
        $this->assertEquals('0', $lead2->empcode);

        // Clean up
        Lead::whereIn('phoneno', [(int)$phone1, [(int)$phone2]])->delete();
    }

    public function test_ajax_upload_returns_json_with_summary_and_file_record(): void
    {
        $phone1 = '97111' . rand(10000, 99999);
        $phone2 = '97222' . rand(10000, 99999);

        // Ensure not in DB
        Lead::whereIn('phoneno', [(int)$phone1, (int)$phone2])->delete();

        $csvData = "Mobile Number\n" . $phone1 . "\n" . $phone2 . "\n";
        $file = UploadedFile::fake()->createWithContent('ajax_leads.csv', $csvData);

        $user = User::where('usercode', '02598')->first() ?? User::first();
        $response = $this->actingAs($user)->postJson('/leads/upload-excel', [
            'leads_file' => $file,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'message',
            'summary' => [
                'total_rows',
                'inserted_count',
                'total_duplicates',
            ],
            'file_record' => [
                'filename',
                'filesize',
                'total_records',
                'inserted_count',
                'status',
            ],
        ]);
        $response->assertJson([
            'success' => true,
        ]);

        // Clean up
        Lead::whereIn('phoneno', [(int)$phone1, (int)$phone2])->delete();
    }
}

