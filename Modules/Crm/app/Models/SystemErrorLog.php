<?php

namespace Modules\Crm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class SystemErrorLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'master.system_error_logs';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model should be timestamped with updated_at.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status_code',
        'level',
        'error_message',
        'exception_class',
        'file_path',
        'line_number',
        'route_url',
        'http_method',
        'usercode',
        'role_id',
        'role_name',
        'ip_address',
        'user_agent',
        'stack_trace',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status_code' => 'integer',
        'line_number' => 'integer',
        'role_id'     => 'integer',
        'created_at'  => 'datetime',
    ];

    /**
     * Scope a query to only include records matching a status code.
     */
    public function scopeStatusCode(Builder $query, ?int $status): Builder
    {
        return $status ? $query->where('status_code', $status) : $query;
    }

    /**
     * Scope a query to only include records for a specific role.
     */
    public function scopeForRole(Builder $query, mixed $roleId): Builder
    {
        if ($roleId === null || $roleId === '') {
            return $query;
        }

        return $roleId === 'guest'
            ? $query->whereNull('role_id')
            : $query->where('role_id', (int) $roleId);
    }

    /**
     * Scope a query to filter by time range.
     */
    public function scopeForTime(Builder $query, ?string $timeFilter): Builder
    {
        return match ($timeFilter) {
            'today'     => $query->whereDate('created_at', today()),
            'yesterday' => $query->whereDate('created_at', today()->subDay()),
            'week'      => $query->where('created_at', '>=', now()->subDays(7)),
            default     => $query,
        };
    }
}
