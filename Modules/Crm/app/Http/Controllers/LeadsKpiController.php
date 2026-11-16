<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Leads\Models\Lead;
use Modules\Auth\Models\Employee;
use Modules\Auth\Models\Role;
use Modules\Auth\Models\MenuAccessRole;
use Modules\Auth\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeadsKpiController extends Controller
{
    /**
     * Display the Leads KPI dashboard, Lead Inflow Trends & Acquisition Analytics.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : RoleService::defaultStaffRoleId();

        // Telecaller is restricted to Telecaller KPI only
        if (RoleService::isTelecaller($roleId)) {
            return redirect()->route('kpi.telecallerkpi')->with('error', 'Telecallers only have access to Telecaller KPI.');
        }

        $roleName = $user ? $user->role_label : 'CRM Official';
        $activeTab = 'leadskpi';

        // Role-based assigned menus for sidebar
        $roleMenus = collect();
        try {
            $roleMenus = MenuAccessRole::forRole($roleId);
        } catch (\Throwable $e) {}

        // Available roles for UI role switcher
        $availableRoles = collect();
        try {
            $userAssigned = RoleService::getAssignedRoles($user->usercode ?? '');
            $availableRoles = !empty($userAssigned)
                ? collect($userAssigned)->map(fn($r) => (object)['role_id' => (int)$r['role_id'], 'role_name' => $r['role_name']])
                : Role::where('role_id', $roleId)->get();
        } catch (\Throwable $e) {}

        // 1. Raw inquiries metrics from crm.leads
        $totalRawLeads = Lead::count();
        $allocatedCount = Lead::allocated()->count();
        $unallocatedCount = Lead::unallocated()->count();
        $allocationRate = $totalRawLeads > 0 ? round(($allocatedCount / $totalRawLeads) * 100, 2) : 0;

        // 2. Group leads by log_userid to distinguish Bulk Excel Uploads (02598) from Direct Portal registrations
        $rawSourceGroups = Lead::select('log_userid', DB::raw('count(*) as count'))
            ->groupBy('log_userid')
            ->orderByDesc('count')
            ->get();

        $bulkUploadCount = 0;
        $directPortalCount = 0;
        $otherStaffCount = 0;

        $sourceBreakdown = $rawSourceGroups->map(function ($row) use (&$bulkUploadCount, &$directPortalCount, &$otherStaffCount) {
            $userId = trim((string)$row->log_userid);
            $count = (int)$row->count;

            if ($userId === '02598') {
                $bulkUploadCount += $count;
                $label = 'Bulk Excel Upload (ID: 02598)';
                $type = 'bulk';
                $badgeColor = '#3b82f6';
            } elseif (in_array(strtoupper($userId), ['PORTAL', 'SELF_REGISTER', 'WEB', 'ONLINE'])) {
                $directPortalCount += $count;
                $label = 'Direct Portal Self-Registration';
                $type = 'portal';
                $badgeColor = '#10b981';
            } else {
                $otherStaffCount += $count;
                $label = 'Direct Staff Inflow (' . ($userId ?: 'Direct Inflow') . ')';
                $type = 'staff';
                $badgeColor = '#8b5cf6';
            }

            return [
                'user_id'     => $userId,
                'label'       => $label,
                'type'        => $type,
                'count'       => $count,
                'badge_color' => $badgeColor,
            ];
        });

        // 3. Chronological lead inflow trends over time by enquirydate using Chart.js area graphs
        $inflowTrends = Lead::select(DB::raw("DATE(enquirydate) as date_val"), DB::raw('count(*) as count'))
            ->whereNotNull('enquirydate')
            ->groupBy(DB::raw("DATE(enquirydate)"))
            ->orderBy('date_val', 'asc')
            ->get();

        $trendLabels = $inflowTrends->map(fn($t) => date('d M', strtotime($t->date_val)))->values()->all();
        $trendCounts = $inflowTrends->pluck('count')->values()->all();

        // Distinct available years and months for interactive filter dropdowns
        $availableYears = Lead::select(DB::raw("EXTRACT(YEAR FROM enquirydate) as y"))
            ->whereNotNull('enquirydate')
            ->groupBy(DB::raw("EXTRACT(YEAR FROM enquirydate)"))
            ->orderByDesc('y')
            ->pluck('y')
            ->map(fn($y) => (int)$y)
            ->filter()
            ->values();

        $availableMonths = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        // 4. Interactive filtering: Year, Month, Allocation Status, Search
        $filterYear = $request->input('year');
        $filterMonth = $request->input('month');
        $filterStatus = $request->input('status', 'all'); // 'all', 'allocated', 'unallocated'
        $filterSource = $request->input('source');
        $search = trim($request->input('q', ''));

        $query = Lead::with(['student.branch', 'student.program', 'employee']);

        if ($filterYear) {
            $query->whereYear('enquirydate', $filterYear);
        }

        if ($filterMonth) {
            $query->whereMonth('enquirydate', $filterMonth);
        }

        if ($filterStatus === 'allocated') {
            $query->allocated();
        } elseif ($filterStatus === 'unallocated') {
            $query->unallocated();
        }

        if ($filterSource) {
            if ($filterSource === 'bulk') {
                $query->where('log_userid', '02598');
            } elseif ($filterSource === 'portal') {
                $query->whereIn(DB::raw('UPPER(TRIM(log_userid))'), ['PORTAL', 'SELF_REGISTER']);
            }
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('phoneno', 'like', "%{$search}%")
                  ->orWhere('applino', 'like', "%{$search}%")
                  ->orWhere('empcode', 'like', "%{$search}%")
                  ->orWhere('log_userid', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $leads = $query->orderByDesc('enquirydate')->paginate(25)->withQueryString();

        return view('crm::kpi.leadskpi', compact(
            'user',
            'roleId',
            'roleName',
            'activeTab',
            'roleMenus',
            'availableRoles',
            'totalRawLeads',
            'allocatedCount',
            'unallocatedCount',
            'allocationRate',
            'bulkUploadCount',
            'directPortalCount',
            'otherStaffCount',
            'sourceBreakdown',
            'trendLabels',
            'trendCounts',
            'availableYears',
            'availableMonths',
            'filterYear',
            'filterMonth',
            'filterStatus',
            'filterSource',
            'search',
            'leads'
        ));
    }
}
