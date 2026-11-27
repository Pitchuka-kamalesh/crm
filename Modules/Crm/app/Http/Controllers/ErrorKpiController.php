<?php

namespace Modules\Crm\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Crm\Services\ErrorMonitoringService;
use Modules\Crm\Models\SystemErrorLog;
use Modules\Auth\Services\RoleService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ErrorKpiController extends Controller
{
    /**
     * Display Error & System Logs KPI Dashboard with Role-based metrics.
     */
    public function index(Request $request): View
    {
        // Auto-sync any recent entries from storage/logs/laravel.log
        ErrorMonitoringService::syncFromLaravelLog(300);

        $timeFilter = $request->input('time', 'all');
        $roleFilter = $request->input('role');
        $statusFilter = $request->input('status');

        $metrics = ErrorMonitoringService::getKpiMetrics($timeFilter, $roleFilter, $statusFilter);
        $errors = ErrorMonitoringService::getFilteredErrors($request, 20);

        // Fetch tail of raw laravel.log for developer stream tab
        $rawLogLines = [];
        $logPath = storage_path('logs/laravel.log');
        if (File::exists($logPath)) {
            $all = explode("\n", File::get($logPath));
            $rawLogLines = array_slice($all, -80);
        }

        $user = Auth::user();
        $roleId = $user ? $user->getActiveRoleId() : RoleService::defaultStaffRoleId();

        return view('crm::kpi.errorkpi', [
            'metrics'       => $metrics,
            'errors'        => $errors,
            'rawLogLines'   => array_reverse(array_filter($rawLogLines)),
            'timeFilter'    => $timeFilter,
            'currentRole'   => $roleFilter,
            'currentStatus' => $statusFilter,
            'user'          => $user,
            'roleId'        => $roleId,
            'activeTab'     => 'errorkpi',
        ]);
    }

    /**
     * Export filtered error logs to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $errors = ErrorMonitoringService::getFilteredErrors($request, 5000);

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="error-logs-kpi-' . date('Y-m-d-His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($errors) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Date Time', 'Status Code', 'Level', 'Role Name', 'User Code', 'HTTP Method', 'URL', 'Error Problem', 'File Path', 'Line Number']);

            foreach ($errors as $err) {
                fputcsv($handle, [
                    $err->id,
                    $err->created_at,
                    $err->status_code,
                    $err->level,
                    $err->role_name,
                    $err->usercode ?: 'Guest',
                    $err->http_method,
                    $err->route_url,
                    $err->error_message,
                    $err->file_path,
                    $err->line_number,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Clear / truncate error logs table.
     */
    public function clearLogs(Request $request)
    {
        try {
            SystemErrorLog::truncate();
            return back()->with('status', 'System error logs cleared successfully.');
        } catch (\Throwable $e) {
            return back()->withErrors(['error' => 'Could not clear error logs: ' . $e->getMessage()]);
        }
    }
}
