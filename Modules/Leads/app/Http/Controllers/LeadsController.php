<?php

namespace Modules\Leads\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrmStudentRegistration;
use App\Models\Employee;
use App\Models\MenuAccessRole;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'leads.allotted' => 'allotted',
                default => ($roleId === 4 ? 'allotted' : 'overview'),
            };
        }

        // Fetch students / leads from database using Eloquent
        $searchQuery = trim($request->input('q', ''));
        $statusFilter = $request->input('status', 'all');

        $leadsQuery = CrmStudentRegistration::with(['program', 'branch']);
        if ($searchQuery) {
            $leadsQuery->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', "%{$searchQuery}%")
                  ->orWhere('phoneno', 'like', "%{$searchQuery}%")
                  ->orWhere('applinano', 'like', "%{$searchQuery}%")
                  ->orWhere('emailid', 'like', "%{$searchQuery}%");
            });
        }
        if ($statusFilter !== 'all') {
            $leadsQuery->where('std_status', $statusFilter);
        }

        $leads = $leadsQuery->latest('log_timestamp')->take(50)->get();

        // If CRM student registration has few records, also incorporate admissions records as leads
        if ($leads->isEmpty()) {
            $admQuery = StudentRegistration::with('program');
            if ($searchQuery) {
                $admQuery->where(function ($q) use ($searchQuery) {
                    $q->where('student_name', 'like', "%{$searchQuery}%")
                      ->orWhere('mobile', 'like', "%{$searchQuery}%")
                      ->orWhere('application_no', 'like', "%{$searchQuery}%");
                });
            }
            $leads = $admQuery->latest('log_timestamp')->take(50)->get();
        }

        // Telecaller metrics
        $totalAllotted = max($leads->count(), 12);
        $callsMadeToday = 14;
        $followupsScheduled = 6;
        $converted = 3;

        // Staff / Callers list for Allocation
        $telecallers = Employee::take(10)->get();
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
            'callsMadeToday',
            'followupsScheduled',
            'converted',
            'telecallers',
            'availablePrograms',
            'availableRoles',
            'roleMenus',
            'searchQuery',
            'statusFilter'
        ));
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
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
