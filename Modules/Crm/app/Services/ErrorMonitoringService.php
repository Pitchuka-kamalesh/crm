<?php

namespace Modules\Crm\Services;

use Modules\Crm\Models\SystemErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class ErrorMonitoringService
{
    private static bool $tableEnsured = false;

    /**
     * Ensure master.system_error_logs exists with all required index columns.
     */
    public static function ensureTable(): void
    {
        if (self::$tableEnsured) {
            return;
        }

        try {
            DB::statement("
                CREATE TABLE IF NOT EXISTS master.system_error_logs (
                    id SERIAL PRIMARY KEY,
                    status_code INT DEFAULT 500,
                    level VARCHAR(20) DEFAULT 'ERROR',
                    error_message TEXT,
                    exception_class VARCHAR(255),
                    file_path VARCHAR(255),
                    line_number INT,
                    route_url VARCHAR(255),
                    http_method VARCHAR(10) DEFAULT 'GET',
                    usercode VARCHAR(50) NULL,
                    role_id INT NULL,
                    role_name VARCHAR(50) NULL,
                    ip_address VARCHAR(45) NULL,
                    user_agent TEXT NULL,
                    stack_trace TEXT NULL,
                    created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP
                );
            ");

            // Helpful query indexes
            DB::statement("CREATE INDEX IF NOT EXISTS idx_err_logs_status ON master.system_error_logs(status_code);");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_err_logs_role ON master.system_error_logs(role_id);");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_err_logs_created ON master.system_error_logs(created_at DESC);");

            self::$tableEnsured = true;
        } catch (Throwable $e) {
            // Silently continue if DB temporarily unavailable
        }
    }

    /**
     * Record an exception in real time using the SystemErrorLog Eloquent model.
     */
    public static function recordException(Throwable $e, ?Request $request = null): void
    {
        try {
            self::ensureTable();

            $request = $request ?? request();
            $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
            $user = Auth::user();

            $usercode = $user?->usercode ?? session('usercode') ?? null;
            $roleId = null;
            $roleName = 'Guest / Public';

            if ($user) {
                $roleId = method_exists($user, 'getActiveRoleId') ? (int) $user->getActiveRoleId() : (int) ($user->role_id ?? 1);
                $roleName = self::resolveRoleName($roleId, (int) ($user->user_type ?? 1));
            }

            $url = $request ? substr($request->fullUrl(), 0, 255) : 'CLI';
            $method = $request ? $request->method() : 'CLI';
            $ip = $request ? $request->ip() : '127.0.0.1';
            $ua = $request ? substr($request->userAgent() ?? '', 0, 500) : null;

            // Trim stack trace to first 25 frames
            $traceLines = explode("\n", $e->getTraceAsString());
            $compactTrace = implode("\n", array_slice($traceLines, 0, 25));

            // Eloquent Model creation (No DB::table)
            SystemErrorLog::create([
                'status_code'     => $statusCode,
                'level'           => $statusCode >= 500 ? 'ERROR' : ($statusCode === 403 ? 'WARNING' : 'INFO'),
                'error_message'   => substr($e->getMessage() ?: get_class($e), 0, 2000),
                'exception_class' => get_class($e),
                'file_path'       => substr($e->getFile(), 0, 255),
                'line_number'     => $e->getLine(),
                'route_url'       => $url,
                'http_method'     => $method,
                'usercode'        => $usercode,
                'role_id'         => $roleId,
                'role_name'       => $roleName,
                'ip_address'      => $ip,
                'user_agent'      => $ua,
                'stack_trace'     => $compactTrace,
                'created_at'      => now(),
            ]);
        } catch (Throwable $t) {
            // Never break application execution if error logging fails
        }
    }

    /**
     * Parse recent errors from laravel.log and import them via Eloquent model.
     */
    public static function syncFromLaravelLog(int $maxLines = 500): int
    {
        self::ensureTable();
        $logFile = storage_path('logs/laravel.log');
        if (!File::exists($logFile)) {
            return 0;
        }

        $content = File::get($logFile);
        $lines = explode("\n", $content);
        $totalLines = count($lines);
        $start = max(0, $totalLines - $maxLines);
        $recentLines = array_slice($lines, $start);

        $inserted = 0;
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] \w+\.(\w+): (.*?)(?: \{.*?\})?$/';

        foreach ($recentLines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match($pattern, $line, $m)) {
                $time = $m[1];
                $level = strtoupper($m[2]);
                $msg = $m[3];

                if (!in_array($level, ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY', 'WARNING'])) {
                    continue;
                }

                // Try to extract userId and role from line
                $usercode = null;
                $roleId = null;
                if (preg_match('/"userId":"([^"]+)"/', $line, $u)) {
                    $usercode = $u[1];
                }
                if (preg_match('/"role_id":(\d+)/', $line, $r)) {
                    $roleId = (int)$r[1];
                }

                // Status code inference
                $status = 500;
                if (str_contains($msg, '403') || str_contains($msg, 'Unauthorized') || str_contains($msg, 'Forbidden')) {
                    $status = 403;
                } elseif (str_contains($msg, '404') || str_contains($msg, 'NotFound')) {
                    $status = 404;
                }

                // Deduplicate by message & timestamp using Eloquent
                $exists = SystemErrorLog::where('error_message', substr($msg, 0, 500))
                    ->where('created_at', $time)
                    ->exists();

                if (!$exists) {
                    SystemErrorLog::create([
                        'status_code'     => $status,
                        'level'           => $level,
                        'error_message'   => substr($msg, 0, 2000),
                        'exception_class' => 'LoggedException',
                        'file_path'       => 'laravel.log',
                        'line_number'     => 0,
                        'route_url'       => '/system',
                        'http_method'     => 'LOG',
                        'usercode'        => $usercode,
                        'role_id'         => $roleId,
                        'role_name'       => self::resolveRoleName($roleId, $usercode ? 2 : 0),
                        'ip_address'      => '127.0.0.1',
                        'user_agent'      => 'System Logger',
                        'stack_trace'     => substr($line, 0, 1000),
                        'created_at'      => $time,
                    ]);
                    $inserted++;
                }
            }
        }

        return $inserted;
    }

    /**
     * Map numeric role_id to display name.
     */
    public static function resolveRoleName(?int $roleId, int $userType = 0): string
    {
        return match ($roleId) {
            1 => 'Employee / Staff',
            2 => 'CRM Incharge',
            3 => 'CRM Dean',
            4 => 'Telecaller',
            5 => 'Student / Applicant',
            default => ($userType === 2 ? 'Employee' : ($userType === 1 ? 'Student' : 'Guest / Public')),
        };
    }

    /**
     * Retrieve aggregated KPI metrics including role distribution via Eloquent model.
     */
    public static function getKpiMetrics(?string $timeFilter = 'all', ?string $roleFilter = null, ?string $statusFilter = null): array
    {
        self::ensureTable();

        // Eloquent Model Query Builder
        $query = SystemErrorLog::query();

        // Apply time scope / filter
        if ($timeFilter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($timeFilter === 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        } elseif ($timeFilter === 'week') {
            $query->where('created_at', '>=', now()->subDays(7));
        }

        if ($roleFilter !== null && $roleFilter !== '') {
            if ($roleFilter === 'guest') {
                $query->whereNull('role_id');
            } else {
                $query->where('role_id', (int) $roleFilter);
            }
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('status_code', (int) $statusFilter);
        }

        $allLogs = $query->get();

        $totalCount    = $allLogs->count();
        $count500      = $allLogs->where('status_code', 500)->count();
        $count403      = $allLogs->where('status_code', 403)->count();
        $count404      = $allLogs->where('status_code', 404)->count();
        $count400      = $allLogs->whereIn('status_code', [400, 419, 429])->count();
        $count503      = $allLogs->where('status_code', 503)->count();

        // Today's total count via Eloquent
        $todayCount = SystemErrorLog::whereDate('created_at', today())->count();

        // Breakdown by Role (Core requirement: "add one more kpi in the error KPI okay the role okay")
        $roleCounts = [
            '1' => ['label' => 'Staff / Employee (Role 1)', 'count' => 0, 'badge' => 'badge-info'],
            '2' => ['label' => 'CRM Incharge (Role 2)',     'count' => 0, 'badge' => 'badge-primary'],
            '3' => ['label' => 'CRM Dean (Role 3)',         'count' => 0, 'badge' => 'badge-warning'],
            '4' => ['label' => 'Telecaller (Role 4)',       'count' => 0, 'badge' => 'badge-success'],
            '5' => ['label' => 'Student / Applicant (Role 5)', 'count' => 0, 'badge' => 'badge-purple'],
            'guest' => ['label' => 'Guest / Unauthenticated', 'count' => 0, 'badge' => 'badge-secondary'],
        ];

        foreach ($allLogs as $log) {
            $r = $log->role_id !== null ? (string)$log->role_id : 'guest';
            if (isset($roleCounts[$r])) {
                $roleCounts[$r]['count']++;
            } else {
                $roleCounts['guest']['count']++;
            }
        }

        return [
            'total'          => $totalCount,
            'today'          => $todayCount,
            'count500'       => $count500,
            'count403'       => $count403,
            'count404'       => $count404,
            'count400'       => $count400,
            'count503'       => $count503,
            'roleCounts'     => $roleCounts,
            'impactedRoles'  => count(array_filter($roleCounts, fn($c) => $c['count'] > 0)),
        ];
    }

    /**
     * Get paginated error logs with search and filters using SystemErrorLog Eloquent model.
     */
    public static function getFilteredErrors(Request $request, int $perPage = 25)
    {
        self::ensureTable();

        $query = SystemErrorLog::query()->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status_code', (int)$request->input('status'));
        }

        if ($request->filled('role')) {
            $role = $request->input('role');
            if ($role === 'guest') {
                $query->whereNull('role_id');
            } else {
                $query->where('role_id', (int)$role);
            }
        }

        if ($request->filled('level')) {
            $query->where('level', strtoupper($request->input('level')));
        }

        $timeFilter = $request->input('time', 'all');
        if ($timeFilter === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($timeFilter === 'yesterday') {
            $query->whereDate('created_at', today()->subDay());
        } elseif ($timeFilter === 'week') {
            $query->where('created_at', '>=', now()->subDays(7));
        }

        if ($request->filled('search')) {
            $s = trim($request->input('search'));
            $query->where(function ($q) use ($s) {
                $q->where('error_message', 'like', "%{$s}%")
                  ->orWhere('route_url', 'like', "%{$s}%")
                  ->orWhere('exception_class', 'like', "%{$s}%")
                  ->orWhere('usercode', 'like', "%{$s}%")
                  ->orWhere('file_path', 'like', "%{$s}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
