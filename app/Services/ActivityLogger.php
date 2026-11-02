<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLogger
{
    /**
     * Log an activity event.
     *
     * @param string $action Key identifier (e.g. 'course_created', 'user_login')
     * @param string $actionTitle Short human-readable title
     * @param string $module Module category ('Academics', 'Assignments', 'Materials', 'Students', 'Auth', 'Civil Services', 'System')
     * @param string $description Detailed message
     * @param string $severity 'info' | 'success' | 'warning' | 'danger'
     * @param array $options Additional options (department_id, entity_type, entity_id, entity_name, user_id, payload)
     * @return ActivityLog|null
     */
    public static function log(
        string $action,
        string $actionTitle,
        string $module,
        string $description,
        string $severity = 'info',
        array $options = []
    ): ?ActivityLog {
        try {
            /** @var User|null $user */
            $user = isset($options['user']) ? $options['user'] : (isset($options['user_id']) ? User::with('profile')->find($options['user_id']) : Auth::user());

            $departmentId = $options['department_id'] ?? null;
            $schoolId = $options['school_id'] ?? null;
            $userName = null;
            $userRole = null;

            if ($user) {
                $userName = trim(($user->profile->first_name ?? '') . ' ' . ($user->profile->last_name ?? ''));
                if (empty($userName)) {
                    $userName = $user->username;
                }
                $userRole = $user->role ?? ($user->profile->roles_id ?? null);
                
                if (!$departmentId && isset($user->profile->departments_id)) {
                    $departmentId = $user->profile->departments_id;
                }
                if (!$schoolId && isset($user->profile->schools_id)) {
                    $schoolId = $user->profile->schools_id;
                }
            }

            // Fallback for IP / User agent
            $ip = Request::ip() ?? '127.0.0.1';
            $userAgent = Request::header('User-Agent');
            $method = Request::method();
            $url = Request::fullUrl();

            return ActivityLog::create([
                'user_id'       => $user?->id,
                'department_id' => $departmentId,
                'school_id'     => $schoolId,
                'user_name'     => $userName ?? ($options['user_name'] ?? 'System / Anonymous'),
                'user_role'     => $userRole ?? ($options['user_role'] ?? 'guest'),
                'action'        => $action,
                'action_title'  => $actionTitle,
                'module'        => $module,
                'severity'      => $severity,
                'description'   => $description,
                'entity_type'   => $options['entity_type'] ?? null,
                'entity_id'     => isset($options['entity_id']) ? (string)$options['entity_id'] : null,
                'entity_name'   => $options['entity_name'] ?? null,
                'ip_address'    => $options['ip_address'] ?? $ip,
                'user_agent'    => $options['user_agent'] ?? $userAgent,
                'method'        => $options['method'] ?? $method,
                'url'           => $options['url'] ?? $url,
                'payload'       => $options['payload'] ?? null,
            ]);
        } catch (\Throwable $e) {
            // Silently fail or log to Laravel error log so normal application flow is never disrupted
            \Illuminate\Support\Facades\Log::warning('ActivityLogger failed: ' . $e->getMessage());
            return null;
        }
    }
}
