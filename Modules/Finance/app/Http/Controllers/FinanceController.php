<?php

namespace Modules\Finance\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CrmStudentRegistration;
use App\Models\MenuAccessRole;
use App\Models\Program;
use App\Models\Role;
use App\Models\StudentRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class FinanceController extends Controller
{
    /**
     * Display the Finance & Paid Fee Collection Dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : 2;
        $roleName = $user ? $user->role_label : 'CRM Official';

        $currentRoute = $request->route() ? $request->route()->getName() : '';
        $activeTab = $request->query('tab');
        if (!$activeTab) {
            $activeTab = match ($currentRoute) {
                'finance.paiddetails' => 'paiddetails',
                default => 'overview',
            };
        }

        // Paid details & financial records from students
        $totalCrmStudents = 0;
        try {
            $totalCrmStudents = CrmStudentRegistration::count();
        } catch (\Throwable $e) {}

        $totalRegistered = 0;
        try {
            $totalRegistered = StudentRegistration::count();
        } catch (\Throwable $e) {}

        $totalApplications = max($totalRegistered, $totalCrmStudents);

        // Calculate sample financial figures based on registered candidates
        $applicationFeePerHead = 1200;
        $admissionFeePerHead = 25000;
        $totalAppFeesCollected = $totalApplications * $applicationFeePerHead;
        $totalAdmFeesCollected = $totalApplications * $admissionFeePerHead;
        $totalRevenue = $totalAppFeesCollected + $totalAdmFeesCollected;

        $paidStudents = collect();
        try {
            $paidStudents = CrmStudentRegistration::with(['program', 'branch'])->take(25)->get();
        } catch (\Throwable $e) {}

        if ($paidStudents->isEmpty()) {
            try {
                $paidStudents = StudentRegistration::take(25)->get();
            } catch (\Throwable $e) {}
        }

        // Available roles for preview switcher (strictly user's assigned roles from DB)
        $userAssigned = \App\Services\RoleService::getAssignedRoles($user->usercode);
        $availableRoles = !empty($userAssigned)
            ? collect($userAssigned)->map(fn($r) => (object)['role_id' => (int)$r['role_id'], 'role_name' => $r['role_name']])
            : Role::where('role_id', $roleId)->get();

        // Role-based assigned menus from master.menu_access_roles (with crm fallback)
        $roleMenus = MenuAccessRole::forRole($roleId);

        return view('finance::index', compact(
            'user',
            'roleId',
            'roleName',
            'activeTab',
            'totalApplications',
            'totalAppFeesCollected',
            'totalAdmFeesCollected',
            'totalRevenue',
            'paidStudents',
            'availableRoles',
            'roleMenus'
        ));
    }

    // Resource API stubs
    public function create() { return view('finance::create'); }
    public function store(Request $request) { return response()->json(['success' => true]); }
    public function show($id) { return view('finance::show'); }
    public function edit($id) { return view('finance::edit'); }
    public function update(Request $request, $id) { return response()->json(['success' => true]); }
    public function destroy($id) { return response()->json(['success' => true]); }
}
