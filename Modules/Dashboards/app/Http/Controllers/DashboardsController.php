<?php

namespace Modules\Dashboards\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Campus;
use App\Models\CrmStudentRegistration;
use App\Models\Program;
use App\Models\StudentEducationInfo;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardsController extends Controller
{
    /**
     * Display the 8-Stage Student Admission & Onboarding Landing Page.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        // Active step (1 through 5, default 1)
        $activeStep = (int) $request->query('step', 1);
        if ($activeStep < 1 || $activeStep > 5) {
            $activeStep = 1;
        }

        // Resolve CRM student record from crm.student_registration
        $crmStudent = null;
        try {
            $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
                ->when($cleanPhone, function ($q) use ($cleanPhone) {
                    $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
                })
                ->first();
        } catch (\Throwable $e) {}

        $appNo = $crmStudent?->applinano ?: ($usercode ?: '');
        $regNo = ($crmStudent?->std_status === 'Admitted' || $crmStudent?->stage1 === 'paid') ? ($crmStudent?->applinano ?: '') : '';
        $displayName = trim((string) ($crmStudent?->name ?: ($user?->name ?: 'Applicant')));

        // Always display Candidate / Student role
        $roleName = ($user?->role_id === 5 || $user?->user_type === 1) ? 'Student Candidate' : ($user?->role_label ?: 'Student Candidate');

        // Resolve Program and Branch dynamically from DB (empty if not filled)
        $progId = $crmStudent?->programid;
        $program = $progId ? Program::where('program_id', $progId)->first() : null;
        $programName = $program ? ($program->program_short_name . ' - ' . $program->program_long_name) : ($crmStudent?->programlevel ?: '');

        $branchId = $crmStudent?->branchid;
        $branch = $branchId ? Branch::where('branch_id', $branchId)->first() : null;
        $branchName = $branch ? $branch->branch_long_name : '';

        $campusCode = $crmStudent?->campuscode;
        $campus = $campusCode ? Campus::where('campus_code', $campusCode)->first() : null;
        $campusName = $campus ? $campus->campus_short_name : '';

        $student = $crmStudent;
        $regulation = 'R25';

        // Fetch 10th and Inter educational info
        $edu10th = null;
        $eduInter = null;

        // Uploaded KYC documents
        $documents = collect();

        // 5-Stage Metadata definitions (strictly aligned with master.menus)
        $stageMeta = [
            1 => [
                'title' => 'Fee Payment',
                'subtitle' => 'Seat Confirmation & Fee Payment',
                'icon' => '💳',
            ],
            2 => [
                'title' => 'Personal Profile',
                'subtitle' => 'Candidate Bio & Contact Information',
                'icon' => '👤',
            ],
            3 => [
                'title' => 'Academic Info',
                'subtitle' => '10th SSC & Intermediate Details',
                'icon' => '🎓',
            ],
            4 => [
                'title' => 'Application Download',
                'subtitle' => 'Application PDF & Documents Download',
                'icon' => '📄',
            ],
            5 => [
                'title' => 'VSAT Results',
                'subtitle' => 'VSAT Scorecard & Merit Rank Details',
                'icon' => '📊',
            ],
        ];

        // Dynamically populate stage titles from master.menus table so DB updates reflect immediately
        try {
            $dbStageMenus = DB::table('master.menus')
                ->whereIn('menu_id', [109, 110, 111, 112, 113])
                ->orWhere('controller_ref', 'like', 'dashboards?step=%')
                ->get();

            $stepMap = [109 => 1, 110 => 2, 111 => 3, 112 => 4, 113 => 5];
            foreach ($dbStageMenus as $m) {
                $stepNum = null;
                if (!empty($m->controller_ref) && preg_match('/step\D*(\d+)/i', $m->controller_ref, $matches)) {
                    $stepNum = (int) $matches[1];
                } elseif (isset($stepMap[$m->menu_id])) {
                    $stepNum = $stepMap[$m->menu_id];
                }
                if ($stepNum && isset($stageMeta[$stepNum]) && !empty($m->menu_name)) {
                    $stageMeta[$stepNum]['title'] = trim((string) $m->menu_name);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('Could not load dynamic stage menus from master.menus: ' . $e->getMessage());
        }

        // Calculate stage statuses strictly from crmStudent DB columns
        $stages = [];
        for ($i = 1; $i <= 5; $i++) {
            $col = 'stage' . $i;
            $val = $crmStudent ? (string)$crmStudent->{$col} : '';
            // For stage 1, 'paid' is completed; for stages 2-5, 'completed' or 'paid' is completed
            $isCompleted = ($i === 1) ? ($val === 'paid') : ($val === 'completed' || $val === 'paid');

            $stages[$i] = [
                'title' => $stageMeta[$i]['title'],
                'subtitle' => $stageMeta[$i]['subtitle'],
                'icon' => $stageMeta[$i]['icon'],
                'status' => $isCompleted ? 'completed' : 'pending',
                'db_field' => $col,
                'db_value' => $val ?: ($i === 1 ? 'not_paid' : 'NA'),
            ];
        }

        // All stages are accessible on click as requested
        $maxAllowedStep = 5;

        // Overall progress percentage
        $completedCount = 0;
        foreach ($stages as $stg) {
            if ($stg['status'] === 'completed') {
                $completedCount++;
            }
        }
        $progressPercent = round(($completedCount / 5) * 100);

        return view('dashboards::index', compact(
            'user',
            'student',
            'crmStudent',
            'edu10th',
            'eduInter',
            'documents',
            'appNo',
            'regNo',
            'displayName',
            'roleName',
            'programName',
            'branchName',
            'campusName',
            'regulation',
            'activeStep',
            'maxAllowedStep',
            'stages',
            'completedCount',
            'progressPercent'
        ));
    }

    /**
     * Unified handler to save candidate details for any of the 8 steps.
     */
    public function saveStep(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $crmStudent = null;
        try {
            $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
                ->when($cleanPhone, function ($q) use ($cleanPhone) {
                    $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
                })
                ->first();
        } catch (\Throwable $e) {}

        $appNo = $crmStudent?->applinano ?? ($usercode ?: ('VU2026-' . ($cleanPhone ? substr($cleanPhone, -6) : 'APP')));
        $regNo = $crmStudent?->applinano ?? ($usercode ?: ('261FA' . ($cleanPhone ? substr($cleanPhone, -5) : '00001')));

        $step = (int) $request->input('step', 1);
        $nextStep = min(5, $step + 1);

        try {
            if ($step === 1) {
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage1' => 'paid',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = 2;
                $msg = 'Stage 1: Admission Fee Payment confirmed!';
            } elseif ($step >= 2 && $step <= 8) {
                if ($crmStudent) {
                    $crmStudent->update([
                        'stage' . $step => 'completed',
                        'log_ipaddress' => $request->ip(),
                        'log_timestamp' => now(),
                    ]);
                }
                $nextStep = min(8, $step + 1);
                $msg = 'Stage ' . $step . ' saved successfully!';
            } else {
                $msg = 'Stage updated successfully!';
            }

            return redirect()->route('dashboards.index', ['step' => $nextStep])
                ->with('status', $msg);

        } catch (Exception $e) {
            Log::error('[DashboardsController] saveStep error: ' . $e->getMessage());
            return redirect()->route('dashboards.index', ['step' => $step])
                ->withErrors(['error' => 'Failed to save stage details: ' . $e->getMessage()]);
        }
    }

    /**
     * Backward-compatible alias for existing form submissions.
     */
    public function saveEducation(Request $request): RedirectResponse
    {
        $request->merge(['step' => 6]);
        return $this->saveStep($request);
    }

    /**
     * Silent autosave on blur / change for any student dashboard input.
     */
    public function autosave(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $usercode = $user?->usercode ?? '';
        $cleanPhone = preg_replace('/\D/', '', $user?->phnno ?? '');
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $crmStudent = null;
        try {
            $crmStudent = CrmStudentRegistration::where('applinano', $usercode)
                ->when($cleanPhone, function ($q) use ($cleanPhone) {
                    $q->orWhere('phoneno', $cleanPhone)->orWhere('phoneno', 'like', "%{$cleanPhone}%");
                })
                ->first();
        } catch (\Throwable $e) {}

        $fieldName = (string)$request->input('field', '');
        $fieldValue = $request->input('value');
        $data = $request->except(['_token', 'field', 'value']);
        if (!empty($fieldName)) {
            $data[$fieldName] = $fieldValue;
        }

        try {
            if ($crmStudent) {
                $crmUpdates = [];
                if (isset($data['student_name']) || isset($data['name'])) {
                    $crmUpdates['name'] = $data['student_name'] ?? $data['name'];
                }
                if (isset($data['personal_email']) || isset($data['email'])) {
                    $crmUpdates['emailid'] = $data['personal_email'] ?? $data['email'];
                }
                if (isset($data['hostel_required'])) {
                    $crmUpdates['ableto'] = (bool)$data['hostel_required'];
                }
                if (!empty($crmUpdates)) {
                    $crmUpdates['log_ipaddress'] = $request->ip();
                    $crmUpdates['log_timestamp'] = now();
                    $crmStudent->update($crmUpdates);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[DashboardsController] autosave error: ' . $e->getMessage());
        }

        return response()->json(['success' => true]);
    }
}
