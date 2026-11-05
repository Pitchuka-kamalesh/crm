<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Campus;
use App\Models\CommunicationLog;
use App\Models\CrmStudentRegistration;
use App\Models\MenuAccessRole;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CrmController extends Controller
{
    /**
     * Display the CRM Landing Dashboard for CRM DEAN, CRM Incharge, and Staff.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : 1;
        $roleName = $user ? $user->role_label : 'CRM Official';

        $currentRoute = $request->route() ? $request->route()->getName() : '';
        $activeTab = $request->query('tab');
        if (!$activeTab) {
            $activeTab = match ($currentRoute) {
                'crm.stddata' => 'stddata',
                'crm.stdsearch' => 'stdsearch',
                'kpi.index', 'kpi.studentkpi' => 'kpi',
                default => 'overview',
            };
        }

        // Summary counts from Eloquent Models (Read-Only)
        $totalCrmRegistrations = 0;
        try {
            $totalCrmRegistrations = CrmStudentRegistration::count();
        } catch (\Throwable $e) {}

        $totalAdmRegistrations = 0;
        try {
            $totalAdmRegistrations = StudentRegistration::count();
        } catch (\Throwable $e) {}

        $totalApplications = max($totalCrmRegistrations, $totalAdmRegistrations);

        $totalCommunications = 0;
        try {
            $totalCommunications = CommunicationLog::count();
        } catch (\Throwable $e) {}

        $totalPrograms = 0;
        try {
            $totalPrograms = Program::count();
        } catch (\Throwable $e) {}

        $totalBranches = 0;
        try {
            $totalBranches = Branch::count();
        } catch (\Throwable $e) {}

        $totalCampuses = 0;
        try {
            $totalCampuses = Campus::count();
        } catch (\Throwable $e) {}

        // Recent student registrations
        $recentStudents = collect();
        try {
            $recentStudents = CrmStudentRegistration::latest('log_timestamp')
                ->take(10)
                ->get();
        } catch (\Throwable $e) {}

        if ($recentStudents->isEmpty()) {
            try {
                $recentStudents = StudentRegistration::latest('log_timestamp')
                    ->take(10)
                    ->get();
            } catch (\Throwable $e) {}
        }

        // Search and full student data for stddata & stdsearch views
        $searchQuery = trim($request->input('q', ''));
        $allStudents = collect();
        try {
            $query = StudentRegistration::query();
            if ($searchQuery) {
                $query->where('student_name', 'like', "%{$searchQuery}%")
                    ->orWhere('application_no', 'like', "%{$searchQuery}%")
                    ->orWhere('mobile', 'like', "%{$searchQuery}%")
                    ->orWhere('register_no', 'like', "%{$searchQuery}%");
            }
            $allStudents = $query->take(50)->get();
            if ($allStudents->isEmpty() && !$searchQuery) {
                $allStudents = CrmStudentRegistration::take(50)->get();
            }
        } catch (\Throwable $e) {}

        // Campuses and Programs for Employee / Dean views
        $campuses = collect();
        try {
            $campuses = Campus::all();
        } catch (\Throwable $e) {}

        $programs = collect();
        try {
            $programs = Program::orderBy('program_level')->get();
        } catch (\Throwable $e) {}

        $branches = collect();
        try {
            $branches = Branch::take(50)->get();
        } catch (\Throwable $e) {}

        // Available roles for UI role switcher
        $availableRoles = collect();
        try {
            $availableRoles = Role::orderBy('role_id')->get();
        } catch (\Throwable $e) {}

        // Role-based assigned menus from master.menu_access_roles
        $roleMenus = collect();
        try {
            $roleMenus = MenuAccessRole::where('role_id', $roleId)->with('menu')->orderBy('morder')->get();
        } catch (\Throwable $e) {}

        return view('crm::index', compact(
            'user',
            'roleId',
            'roleName',
            'activeTab',
            'totalApplications',
            'totalCommunications',
            'totalPrograms',
            'totalBranches',
            'totalCampuses',
            'recentStudents',
            'allStudents',
            'campuses',
            'programs',
            'branches',
            'availableRoles',
            'roleMenus',
            'searchQuery'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('crm::create');
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
        return view('crm::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('crm::edit');
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
