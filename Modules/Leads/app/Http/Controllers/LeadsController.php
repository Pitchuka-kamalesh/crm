<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\Lead;
use App\Models\MenuAccessRole;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentRegistration;
use App\Models\TelecallerLead;
use App\Models\TelecallerLeadTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeadsController extends Controller
{
    /**
     * Display the Leads Calling & Allocation Dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : 4;
        $roleName = $user ? $user->role_label : 'Tele Caller';

        $currentRoute = $request->route() ? $request->route()->getName() : '';
        $activeTab = $request->query('tab');
        if (!$activeTab) {
            $activeTab = match ($currentRoute) {
                'leads.allocation' => 'allocation',
                'leads.input' => 'input',
                'leads.allotted' => 'allotted',
                default => ($roleId === 4 ? 'allotted' : 'input'),
            };
        }

        // Fetch students / leads from database using Eloquent
        $searchQuery = trim($request->input('q', ''));
        $statusFilter = $request->input('status', 'all');
        $callerFilter = $request->input('caller', 'all');
        $allocStatusFilter = $request->input('alloc_status', 'all');
        $programFilter = $request->input('program', 'all');

        $totalLeadsCount = Lead::count();
        $maxPageLimit = max(100, $totalLeadsCount);

        $perPage = (int) $request->input('per_page', 25);
        if ($perPage < 1) {
            $perPage = 25;
        } elseif ($perPage > $maxPageLimit) {
            $perPage = $maxPageLimit; // Allow limit up to total size of leads table
        }

        $studentMap = collect();
        $phoneStudentMap = collect();
        $leadModelMap = collect();

        if ($activeTab === 'allocation') {
            // New table sourced directly from database crm.leads
            $leadsQuery = Lead::query();

            if ($searchQuery) {
                $leadsQuery->where(function ($q) use ($searchQuery) {
                    $q->whereRaw("CAST(phoneno AS TEXT) LIKE ?", ["%{$searchQuery}%"])
                      ->orWhere('empcode', 'like', "%{$searchQuery}%");
                });
            }

            if ($callerFilter && $callerFilter !== 'all') {
                $leadsQuery->where('empcode', $callerFilter);
            }

            if ($allocStatusFilter === 'allocated') {
                $leadsQuery->whereNotNull('empcode')->where('empcode', '<>', '0');
            } elseif ($allocStatusFilter === 'unallocated') {
                $leadsQuery->where(function ($q) {
                    $q->whereNull('empcode')->orWhere('empcode', '0');
                });
            }

            // Keep unallocated leads on top, sorted oldest date/timestamp first and by phone number on ties (same for allocated)
            $leads = $leadsQuery
                ->orderByRaw("(CASE WHEN empcode IS NULL OR empcode = '' OR empcode = '0' THEN 0 ELSE 1 END) ASC")
                ->orderByRaw("date_trunc('second', COALESCE(log_timestamp, enquirydate::timestamp)) ASC NULLS LAST")
                ->orderBy('phoneno', 'asc')
                ->paginate($perPage)
                ->withQueryString();
        } elseif ($activeTab === 'allotted') {
            // Strict telecaller isolation: telecaller can ONLY view their own allocated leads
            $telecallerEmpCode = ($roleId === 4 || empty($callerFilter) || $callerFilter === 'all') 
                ? ($user?->usercode ?? '') 
                : $callerFilter;

            if ($roleId === 4) {
                $telecallerEmpCode = $user?->usercode ?? '';
            }

            // Sync any leads allocated in crm.leads to this telecaller that might be missing from crm.telecaller_leads
            if (!empty($telecallerEmpCode)) {
                $existingPhones = TelecallerLead::where('empcode', $telecallerEmpCode)
                    ->pluck('phoneno')
                    ->filter()
                    ->map(fn($p) => (int) $p)
                    ->toArray();

                $missingLeads = Lead::where('empcode', $telecallerEmpCode)
                    ->when(!empty($existingPhones), fn($q) => $q->whereNotIn('phoneno', $existingPhones))
                    ->get();

                $maxSyncLeadCount = (int) TelecallerLead::where('empcode', $telecallerEmpCode)->max('leadcount');
                foreach ($missingLeads as $ml) {
                    try {
                        $maxSyncLeadCount++;
                        TelecallerLead::create([
                            'applino' => !empty($ml->applino) ? (string) $ml->applino : '0',
                            'phoneno' => (string) $ml->phoneno,
                            'crmid'   => '0',
                            'empcode' => $telecallerEmpCode,
                            'leadcount' => $maxSyncLeadCount,
                            'assigndatetime' => $ml->allocated_at ?? now(),
                            'log_userid' => 'AUTO_SYNC',
                            'log_timestamp' => now(),
                        ]);
                    } catch (\Throwable $e) {}
                }
            }

            $allottedQuery = TelecallerLead::where('crm.telecaller_leads.empcode', $telecallerEmpCode)
                ->leftJoin('crm.leads', function ($join) {
                    $join->on(DB::raw("CAST(crm.leads.phoneno AS TEXT)"), '=', 'crm.telecaller_leads.phoneno');
                })
                ->leftJoin('crm.student_registration', function ($join) {
                    $join->on('crm.student_registration.phoneno', '=', 'crm.telecaller_leads.phoneno');
                })
                ->leftJoin('crm.student_personal_info', function ($join) {
                    $join->on('crm.student_personal_info.phoneno', '=', 'crm.telecaller_leads.phoneno');
                })
                ->select([
                    'crm.telecaller_leads.*',
                    'crm.leads.log_timestamp as lead_log_timestamp',
                    'crm.leads.enquirydate as lead_enquirydate',
                    'crm.leads.allocated_at as lead_allocated_at',
                    'crm.student_registration.name as student_name',
                    'crm.student_registration.applinano as student_applinano',
                    'crm.student_registration.programlevel as student_program',
                    'crm.student_registration.stage1 as student_stage1',
                    'crm.student_registration.stage2 as student_stage2',
                    'crm.student_registration.stage3 as student_stage3',
                    'crm.student_registration.stage4 as student_stage4',
                    'crm.student_registration.stage5 as student_stage5',
                    'crm.student_registration.remarks as student_remarks',
                    'crm.student_personal_info.crmid as personal_crmid',
                ]);

            if ($searchQuery) {
                $allottedQuery->where(function ($q) use ($searchQuery) {
                    $q->where('crm.telecaller_leads.phoneno', 'like', "%{$searchQuery}%")
                      ->orWhere('crm.telecaller_leads.applino', 'like', "%{$searchQuery}%")
                      ->orWhere('crm.telecaller_leads.crmid', 'like', "%{$searchQuery}%")
                      ->orWhere('crm.student_registration.name', 'ilike', "%{$searchQuery}%");
                });
            }

            $leads = $allottedQuery
                ->orderByRaw("COALESCE(crm.telecaller_leads.assigndatetime, crm.telecaller_leads.log_timestamp) DESC")
                ->paginate($perPage)
                ->withQueryString();
        } else {
            $allocatedApplis = TelecallerLead::pluck('applino');

            $leadsQuery = CrmStudentRegistration::with(['program', 'branch', 'campus', 'state', 'country']);
            if ($searchQuery) {
                $leadsQuery->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'ilike', "%{$searchQuery}%")
                      ->orWhere('phoneno', 'ilike', "%{$searchQuery}%")
                      ->orWhere('applinano', 'ilike', "%{$searchQuery}%")
                      ->orWhere('crmid', 'ilike', "%{$searchQuery}%")
                      ->orWhere('emailid', 'ilike', "%{$searchQuery}%");
                });
            }
            if ($statusFilter && $statusFilter !== 'all') {
                $sf = strtolower($statusFilter);
                if ($sf === 'telecaller' || $sf === 'telecaller stage') {
                    $leadsQuery->whereIn('applinano', $allocatedApplis);
                } elseif ($sf === 'register' || $sf === 'register form') {
                    $leadsQuery->whereNotIn('applinano', $allocatedApplis);
                } elseif ($sf === 'payment' || $sf === 'payment stage' || $sf === 'not paid stage' || $sf === 'stage1') {
                    $leadsQuery->where(function ($q) {
                        $q->whereNull('stage1')->orWhere('stage1', '!=', 'paid');
                    });
                } elseif ($sf === 'personal' || $sf === 'personal details' || $sf === 'stage2') {
                    $leadsQuery->where('stage1', 'paid')
                               ->where(function ($q) {
                                   $q->whereNull('stage2')->orWhere('stage2', '!=', 'completed');
                               });
                } elseif ($sf === 'academic' || $sf === 'academic details' || $sf === 'stage3') {
                    $leadsQuery->where('stage2', 'completed')
                               ->where(function ($q) {
                                   $q->whereNull('stage3')->orWhere('stage3', '!=', 'completed');
                               });
                } elseif (in_array($sf, ['stage4','stage5','step1','step2','step3','step4','step5'])) {
                    $col = str_replace('step', 'stage', $sf);
                    $leadsQuery->whereIn($col, ['completed', 'paid', 'approved', 'in_progress', 'active']);
                } else {
                    $leadsQuery->where('std_status', $statusFilter);
                }
            }

            if ($activeTab === 'input') {
                // On Lead input tab, reflect the entire dataset with optional allocation/telecaller/program filters
                if ($allocStatusFilter === 'allocated') {
                    $leadsQuery->whereIn('applinano', $allocatedApplis);
                } elseif ($allocStatusFilter === 'unallocated' && $allocatedApplis->isNotEmpty()) {
                    $leadsQuery->whereNotIn('applinano', $allocatedApplis);
                }

                if ($callerFilter && $callerFilter !== 'all') {
                    $callerApplis = TelecallerLead::where('empcode', $callerFilter)->pluck('applino');
                    $leadsQuery->whereIn('applinano', $callerApplis);
                }
            }

            if ($programFilter && $programFilter !== 'all') {
                $leadsQuery->where('program_id', $programFilter);
            }

            // If logged in as Tele Caller (Role 4) and on allotted tab, show assigned leads or all if not assigned
            if ($roleId === 4 && $activeTab === 'allotted' && $user) {
                $assignedApplis = TelecallerLead::where('empcode', $user->usercode)->pluck('applino');
                if ($assignedApplis->isNotEmpty()) {
                    $leadsQuery->whereIn('applinano', $assignedApplis);
                }
            }

            // Records per page with query strings preserved
            $leads = $leadsQuery->orderBy('log_timestamp', 'desc')->paginate($perPage)->withQueryString();

            // If CRM student registration is empty, fallback to crm.leads only when viewing full input without filters
            if ($leads->isEmpty() && !$searchQuery && $statusFilter === 'all' && $allocStatusFilter === 'all' && $callerFilter === 'all' && $programFilter === 'all') {
                $rawLeadsQuery = Lead::query();
                $leads = $rawLeadsQuery->orderBy('log_timestamp', 'desc')->paginate($perPage)->withQueryString();
            }
        }

        // Metrics from TelecallerService
        $metrics = \App\Services\TelecallerService::getMetrics();
        $totalAllotted = $metrics['total_allocated'];
        $unallocatedCount = $metrics['unallocated'];
        $totalTasksCount = $metrics['total_tasks'];
        $callsMadeToday = $totalTasksCount ?: 14;
        $followupsScheduled = CrmStudentRegistration::where('std_status', 'Follow-up')->count() ?: 6;
        $converted = CrmStudentRegistration::whereIn('std_status', ['Fee Paid', 'Registered'])->count() ?: 3;

        // DB allocations and page-scoped latest tasks from crm.telecaller_leads / crm.telecaller_leads_tasks
        $dbAllocations = collect();
        $latestTasks = collect();
        $latestStageTasksByPhone = collect();
        $latestStageTasksByAppli = collect();
        try {
            $dbAllocations = TelecallerLead::with('employee')
                ->get()
                ->map(function ($l) {
                    $l->emp_name = $l->employee?->full_name ?? $l->empcode;
                    return $l;
                })
                ->keyBy('applino');
        } catch (\Throwable $e) {}

        // Compute per-lead stage dropdown eligibility and latest telecaller stage task in batch for the current page
        $stageEligibilityMap = [];
        if ($activeTab !== 'allocation') {
            $pageApplinos = $leads->getCollection()->flatMap(function ($lead) {
                return [
                    $lead->student_applinano ?? null,
                    $lead->applinano ?? null,
                    $lead->applino ?? null,
                ];
            })->all();
            $stageEligibilityMap = \App\Services\TelecallerService::getStageEligibilityMap($pageApplinos);

            try {
                $pagePhones = $leads->getCollection()
                    ->pluck('phoneno')
                    ->filter()
                    ->map(fn ($p) => (string) $p)
                    ->unique()
                    ->values()
                    ->all();
                $cleanApplinos = array_values(array_unique(array_filter(
                    array_map(fn ($v) => trim((string) $v), $pageApplinos),
                    fn (string $v) => $v !== '' && $v !== '0'
                )));

                if (!empty($pagePhones) || !empty($cleanApplinos)) {
                    $pageTasks = TelecallerLeadTask::where(function ($q) use ($pagePhones, $cleanApplinos) {
                            if (!empty($pagePhones)) {
                                $q->whereIn('phoneno', $pagePhones);
                            }
                            if (!empty($cleanApplinos)) {
                                $q->orWhereIn('applino', $cleanApplinos);
                            }
                        })
                        ->orderBy('leadtaskid', 'desc')
                        ->get();

                    $latestTasks = $pageTasks->groupBy('applino');

                    $stageNames = ['Telecaller Stage', 'Register Form', 'Payment Stage', 'Personal Details', 'Academic Details'];
                    $stageTasks = $pageTasks->filter(fn ($t) => in_array(trim((string) $t->stage_name), $stageNames, true));

                    $latestStageTasksByPhone = $stageTasks
                        ->filter(fn ($t) => !empty($t->phoneno))
                        ->unique('phoneno')
                        ->keyBy('phoneno');
                    $latestStageTasksByAppli = $stageTasks
                        ->filter(fn ($t) => !empty($t->applino) && $t->applino !== '0')
                        ->unique('applino')
                        ->keyBy('applino');
                }
            } catch (\Throwable $e) {}
        }

        // If AJAX request (Live Search / Filtering), return the table rows partial
        if ($request->ajax()) {
            return view('leads::partials.leads_table', compact(
                'leads',
                'dbAllocations',
                'latestTasks',
                'latestStageTasksByPhone',
                'latestStageTasksByAppli',
                'activeTab',
                'stageEligibilityMap'
            ));
        }

        // Telecallers list (Role 4 in roles_assigned / employees)
        $telecallers = \App\Services\TelecallerService::getTelecallers();
        $availablePrograms = Program::orderBy('program_short_name')->get();

        // Available roles for preview switcher
        $availableRoles = Role::orderBy('role_id')->get();

        // Role-based assigned menus from master.menu_access_roles
        $roleMenus = MenuAccessRole::where('role_id', $roleId)->with('menu')->orderBy('morder')->get();

        return view('leads::index', compact(
            'user',
            'roleId',
            'roleName',
            'activeTab',
            'leads',
            'totalAllotted',
            'unallocatedCount',
            'totalTasksCount',
            'callsMadeToday',
            'followupsScheduled',
            'converted',
            'telecallers',
            'dbAllocations',
            'latestTasks',
            'latestStageTasksByPhone',
            'latestStageTasksByAppli',
            'stageEligibilityMap',
            'availablePrograms',
            'availableRoles',
            'roleMenus',
            'searchQuery',
            'statusFilter',
            'callerFilter',
            'allocStatusFilter',
            'programFilter',
            'perPage',
            'maxPageLimit',
            'totalLeadsCount',
            'studentMap',
            'phoneStudentMap',
            'leadModelMap'
        ));
    }

    /**
     * Store a newly inputted lead directly from Lead input tab into crm.student_registration.
     */
    public function storeLeadInput(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phoneno' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'emailid' => ['nullable', 'email', 'max:150'],
            'program_id' => ['required', 'integer'],
            'empcode' => ['nullable', 'string', 'max:50'],
        ]);

        $phone = trim($request->input('phoneno'));
        $existing = CrmStudentRegistration::where('phoneno', $phone)->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => "A lead with mobile +91 {$phone} already exists (App #{$existing->applinano}).",
            ], 422);
        }

        $maxApp = CrmStudentRegistration::max('applinano');
        $nextAppNo = is_numeric($maxApp) ? (string) ((int) $maxApp + 1) : '2027000001';
        $nextId = (CrmStudentRegistration::max('id') ?? 0) + 1;
        $programId = (int) $request->input('program_id');
        $prog = Program::find($programId);

        $student = CrmStudentRegistration::create([
            'id' => $nextId,
            'applinano' => $nextAppNo,
            'phoneno' => $phone,
            'name' => trim($request->input('name')),
            'emailid' => trim((string) $request->input('emailid', '')) ?: null,
            'program_id' => $programId,
            'programlevel' => $prog?->program_short_name ?? null,
            'campus_id' => 1,
            'std_status' => 'Interested',
            'stage1' => 'not_paid',
            'stage2' => 'locked',
            'stage3' => 'locked',
            'stage4' => 'locked',
            'stage5' => 'locked',
            'log_userid' => auth()->user()?->usercode ?? '02598',
            'log_ipaddress' => $request->ip() ?: '127.0.0.1',
            'log_timestamp' => now(),
        ]);

        $crmId = \App\Services\CrmIdService::generateForStudent($student);

        $empCode = trim((string) $request->input('empcode', ''));
        if ($empCode !== '') {
            \App\Services\TelecallerService::allocateLeads([
                'empcode' => $empCode,
                'allot_mode' => 'single',
                'applino' => $student->applinano,
                'phoneno' => $student->phoneno,
                'stage_name' => 'Stage 1: Calling & Verification',
                'stage_remark' => 'Assigned directly on Lead input',
                'log_userid' => auth()->user()?->usercode ?? '02598',
                'log_ipaddress' => $request->ip() ?: '127.0.0.1',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Lead [{$student->name}] added successfully (App #{$student->applinano}, CRM ID: {$crmId}).",
        ]);
    }

    /**
     * Update Stage Status (Stage 1 to 5) for a student registration from Telecaller wizard.
     */
    public function updateStage(Request $request, string $applinano)
    {
        $request->validate([
            'stage' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'stage_name' => ['nullable', 'string', 'max:255'],
            'stage_calltime' => ['nullable', 'string', 'max:60'],
            'stage_callperson' => ['nullable', 'string', 'max:255'],
            'call_duration' => ['nullable', 'string', 'max:50'],
            'remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $stageCol = $request->input('stage');
        $stageStatus = $request->input('status', 'completed');
        $customStageName = trim((string) $request->input('stage_name', ''));
        if ($customStageName === '' && $stageCol) {
            $customStageName = strtoupper($stageCol) . ' (' . ucfirst($stageStatus) . ')';
        }
        if ($customStageName === '') {
            $customStageName = 'Telecaller Stage';
        }

        $callTime = trim((string) $request->input('stage_calltime', ''));
        $callPerson = trim((string) $request->input('stage_callperson', ''));
        $callDuration = trim((string) $request->input('call_duration', ''));
        $remarks = trim((string) $request->input('remarks', ''));

        $fullRemark = $remarks ?: ('Stage status updated to ' . $customStageName);

        $user = auth()->user();
        $roleId = $user ? $user->getActiveRoleId() : 4;
        $cleanTarget = str_replace('APP', '', $applinano);

        // Strict telecaller isolation: telecaller can ONLY touch their own allocated leads
        if ($roleId === 4 && $user) {
            $empCode = $user->usercode;
            $isAllocated = TelecallerLead::where('empcode', $empCode)
                ->where(function ($q) use ($applinano, $cleanTarget) {
                    $q->where('applino', $applinano)
                      ->orWhere('phoneno', $cleanTarget)
                      ->orWhere('phoneno', $applinano);
                })
                ->exists();

            if (!$isAllocated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. This student lead is not allocated to you.'
                ], 403);
            }
        }

        $telecallerLead = TelecallerLead::where('applino', $applinano)
            ->orWhere('phoneno', $cleanTarget)
            ->orWhere('phoneno', $applinano)
            ->first();

        $student = CrmStudentRegistration::where('applinano', $applinano)
            ->orWhere('phoneno', $cleanTarget)
            ->first();

        $tApplino = $telecallerLead?->applino ?: ($student?->applinano ?: $applinano);
        $tPhoneno = $telecallerLead?->phoneno ?: ($student?->phoneno ?: $cleanTarget);
        $tEmpcode = $telecallerLead?->empcode ?: (auth()->user()?->usercode ?? '02598');

        $validationAppli = ($student?->applinano && $student->applinano !== '0')
            ? (string) $student->applinano
            : (string) $tApplino;
        $allowedStages = \App\Services\TelecallerService::getAllowedStagesForLead($validationAppli);
        if (array_key_exists($customStageName, $allowedStages) && !$allowedStages[$customStageName]) {
            return response()->json([
                'success' => false,
                'message' => "Cannot select '{$customStageName}': student record was not found in the required table.",
            ], 422);
        }

        // Record telecaller stage action strictly in crm.telecaller_leads_tasks
        try {
            $taskRes = \App\Services\TelecallerService::logLeadTask([
                'applino' => $tApplino,
                'phoneno' => $tPhoneno,
                'empcode' => $tEmpcode,
                'stage_name' => $customStageName,
                'stage_calltime' => $callTime ?: now(),
                'stage_call_duration' => $callDuration,
                'stage_remark' => $fullRemark,
                'stage_callperson' => $callPerson ?: ($student?->name ?: 'Student / Candidate'),
                'log_userid' => auth()->user()?->usercode ?? '02598',
                'log_ipaddress' => $request->ip() ?: '127.0.0.1',
            ]);
        } catch (\Throwable $e) {
            \Log::warning('[LeadsController] Failed to log lead task: ' . $e->getMessage());
            $taskRes = ['success' => false, 'message' => 'Failed to log telecaller task.'];
        }

        if (!($taskRes['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $taskRes['message'] ?? 'Failed to save telecaller task.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => $customStageName . ' saved successfully',
            'stage_name' => $customStageName,
            'status' => $stageStatus,
            'remarks' => $fullRemark,
        ]);
    }

    /**
     * Update Lead Call Status / Disposition (recorded strictly in crm.telecaller_leads_tasks).
     */
    public function updateStatus(Request $request, string $applinano)
    {
        $request->validate([
            'status' => ['required', 'string', 'max:50'],
        ]);

        $status = $request->input('status');

        $user = auth()->user();
        $roleId = $user ? $user->getActiveRoleId() : 4;
        $cleanTarget = str_replace('APP', '', $applinano);

        $student = CrmStudentRegistration::where('applinano', $applinano)
            ->orWhere('phoneno', $cleanTarget)
            ->first();

        // Strict telecaller isolation: telecaller can ONLY touch their own allocated leads
        if ($roleId === 4 && $user) {
            $empCode = $user->usercode;
            $isAllocated = TelecallerLead::where('empcode', $empCode)
                ->where(function ($q) use ($applinano, $cleanTarget, $student) {
                    $q->where('applino', $applinano)
                      ->orWhere('phoneno', $student?->phoneno ?? $cleanTarget)
                      ->orWhere('phoneno', $applinano);
                })
                ->exists();

            if (!$isAllocated) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. This student lead is not allocated to you.'
                ], 403);
            }
        }

        $telecallerLead = TelecallerLead::where('applino', $applinano)
            ->orWhere('phoneno', $cleanTarget)
            ->orWhere('phoneno', $applinano)
            ->first();

        $tApplino = $telecallerLead?->applino ?: ($student?->applinano ?: '0');
        $tPhoneno = $telecallerLead?->phoneno ?: ($student?->phoneno ?: $cleanTarget);
        $tEmpcode = $telecallerLead?->empcode ?: ($user?->usercode ?? '03434');

        // Record status update action strictly in crm.telecaller_leads_tasks
        try {
            $taskRes = \App\Services\TelecallerService::logLeadTask([
                'applino' => $tApplino,
                'phoneno' => $tPhoneno,
                'empcode' => $tEmpcode,
                'stage_name' => 'Call Status: ' . $status,
                'stage_remark' => 'Lead call status updated to ' . $status,
                'stage_callperson' => $student?->name ?: 'Student / Candidate',
                'log_userid' => $user?->usercode ?? '02598',
                'log_ipaddress' => $request->ip() ?: '127.0.0.1',
            ]);
        } catch (\Throwable $e) {
            \Log::warning('[LeadsController] Failed to log status task: ' . $e->getMessage());
            $taskRes = ['success' => false, 'message' => 'Failed to log lead status task.'];
        }

        if (!($taskRes['success'] ?? false)) {
            return response()->json([
                'success' => false,
                'message' => $taskRes['message'] ?? 'Failed to log lead status task.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'message' => 'Lead status updated to ' . $status,
            'status' => $status,
        ]);
    }

    /**
     * Allocate leads to a telecaller from the Leads Incharge allocation screen.
     * Persists into crm.telecaller_leads and updates crm.leads.
     */
    public function allocate(Request $request)
    {
        $res = \App\Services\TelecallerService::allocateLeads([
            'empcode' => $request->input('empcode'),
            'crmid' => auth()->user()?->usercode ?? '02598',
            'allot_mode' => $request->input('allot_mode', 'selected'),
            'applino' => $request->input('applino'),
            'phoneno' => $request->input('phoneno'),
            'lead_keys' => $request->input('lead_keys', []),
            'allot_count' => $request->input('allot_count', 25),
            'allot_date' => $request->input('allot_date'),
            'stage_name' => $request->input('stage_name', 'Stage 1: Calling & Verification'),
            'stage_remark' => $request->input('stage_remark', 'Allocated via Leads Incharge allocation portal'),
            'log_userid' => auth()->user()?->usercode ?? '02598',
            'log_ipaddress' => $request->ip() ?: '127.0.0.1',
        ]);

        if ($request->wantsJson()) {
            return response()->json($res, $res['success'] ? 200 : 422);
        }

        return redirect()->back()->with($res['success'] ? 'status' : 'error', $res['message']);
    }

    /**
     * Directly log a telecaller task / note for a lead.
     */
    public function logTask(Request $request)
    {
        $res = \App\Services\TelecallerService::logLeadTask([
            'applino' => $request->input('applino'),
            'phoneno' => $request->input('phoneno'),
            'empcode' => $request->input('empcode') ?: (auth()->user()?->usercode ?? '03434'),
            'stage_name' => $request->input('stage_name', 'Telecaller Follow-up'),
            'stage_remark' => $request->input('stage_remark', ''),
            'stage_callperson' => $request->input('stage_callperson', ''),
            'log_userid' => auth()->user()?->usercode ?? '02598',
            'log_ipaddress' => $request->ip() ?: '127.0.0.1',
        ]);

        return response()->json($res, $res['success'] ? 200 : 422);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('leads::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('leads::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('leads::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Fetch previous telecaller task history from crm.telecaller_leads_tasks for a lead.
     */
    public function taskHistory(Request $request, string $applinano): \Illuminate\Http\JsonResponse
    {
        $cleanAppli = trim(str_replace('APP', '', $applinano));
        $phone = trim((string) $request->query('phone', ''));
        $user = auth()->user();
        $roleId = $user ? $user->getActiveRoleId() : 4;

        if ($roleId === 4 && $user) {
            $empCode = $user->usercode;
            $isAllocated = TelecallerLead::where('empcode', $empCode)
                ->where(function ($q) use ($cleanAppli, $phone) {
                    if ($cleanAppli !== '' && $cleanAppli !== '0') {
                        $q->where('applino', $cleanAppli)->orWhere('phoneno', $cleanAppli);
                    }
                    if ($phone !== '' && $phone !== '0') {
                        $q->orWhere('phoneno', $phone);
                    }
                })
                ->exists();

            if (!$isAllocated) {
                return response()->json([
                    'success' => false,
                    'history' => [],
                    'message' => 'Unauthorized.',
                ], 403);
            }
        }

        $query = \App\Models\TelecallerLeadTask::query();
        $hasFilter = false;
        $query->where(function ($q) use ($cleanAppli, $phone, &$hasFilter) {
            if ($phone !== '' && $phone !== '0') {
                $q->orWhere('phoneno', $phone);
                $hasFilter = true;
            }
            if ($cleanAppli !== '' && $cleanAppli !== '0') {
                $q->orWhere('applino', $cleanAppli);
                if (!$phone) {
                    $q->orWhere('phoneno', $cleanAppli);
                }
                $hasFilter = true;
            }
        });

        if (!$hasFilter) {
            return response()->json(['success' => true, 'history' => []]);
        }

        $tasks = $query->orderBy('leadtaskid', 'desc')
            ->limit(25)
            ->get([
                'leadtaskid',
                'stage_name',
                'stage_calltime',
                'stage_call_duration',
                'stage_callperson',
                'stage_remark',
                'empcode',
                'log_timestamp',
            ])
            ->map(function ($t) {
                $callTime = $t->stage_calltime
                    ? $t->stage_calltime->format('d-m-Y h:i A')
                    : ($t->log_timestamp ? $t->log_timestamp->format('d-m-Y h:i A') : '');

                return [
                    'id' => $t->leadtaskid,
                    'stage_name' => $t->stage_name ?: 'Telecaller Stage',
                    'stage_calltime' => $callTime,
                    'stage_call_duration' => $t->stage_call_duration ?: '-',
                    'stage_callperson' => $t->stage_callperson ?: '-',
                    'stage_remark' => $t->stage_remark ?: '',
                    'empcode' => $t->empcode ?: '',
                ];
            })
            ->values();

        return response()->json([
            'success' => true,
            'history' => $tasks,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
