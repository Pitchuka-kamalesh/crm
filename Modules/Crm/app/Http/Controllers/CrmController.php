<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CrmController extends Controller
{
    /**
     * Display the CRM Landing Dashboard for CRM DEAN, CRM Incharge, and Staff.
     */
    public function index(): View
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : 1;
        $roleName = $user ? $user->role_label : 'CRM Official';

        // Summary counts from existing tables (Read-Only)
        $totalCrmRegistrations = 0;
        try {
            $totalCrmRegistrations = DB::table('crm.student_registration')->count();
        } catch (\Throwable $e) {}

        $totalAdmRegistrations = 0;
        try {
            $totalAdmRegistrations = DB::table('admissions.student_registrations')->count();
        } catch (\Throwable $e) {}

        $totalApplications = max($totalCrmRegistrations, $totalAdmRegistrations);

        $totalCommunications = 0;
        try {
            $totalCommunications = DB::table('master.communication_logs')->count();
        } catch (\Throwable $e) {}

        $totalPrograms = 0;
        try {
            $totalPrograms = DB::table('master.programs')->count();
        } catch (\Throwable $e) {}

        $totalBranches = 0;
        try {
            $totalBranches = DB::table('master.branches')->count();
        } catch (\Throwable $e) {}

        // Recent student registrations
        $recentStudents = collect();
        try {
            $recentStudents = DB::table('crm.student_registration')
                ->latest('log_timestamp')
                ->take(10)
                ->get();
        } catch (\Throwable $e) {}

        if ($recentStudents->isEmpty()) {
            try {
                $recentStudents = DB::table('admissions.student_registrations')
                    ->latest('log_timestamp')
                    ->take(10)
                    ->get();
            } catch (\Throwable $e) {}
        }

        // Available roles for UI role switcher
        $availableRoles = collect();
        try {
            $availableRoles = DB::table('master.roles')->orderBy('role_id')->get();
        } catch (\Throwable $e) {}

        return view('crm::index', compact(
            'user',
            'roleId',
            'roleName',
            'totalApplications',
            'totalCommunications',
            'totalPrograms',
            'totalBranches',
            'recentStudents',
            'availableRoles'
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
