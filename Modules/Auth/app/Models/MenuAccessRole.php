<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuAccessRole extends Model
{
    protected $table = 'master.menu_access_roles';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'menu_code',
        'menu_id',
        'role_id',
        'campus_id',
        'morder',
        'mposition',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\Menu::class, 'menu_id', 'menu_id');
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(\Modules\Auth\Models\Role::class, 'role_id', 'role_id');
    }

    public static function forRole(int $roleId)
    {
        try {
            $items = static::where('role_id', $roleId)->with('menu')->orderBy('morder')->get();
            if ($items->isNotEmpty()) {
                return $items;
            }
        } catch (\Throwable $e) {}

        try {
            $crmInstance = new static();
            $crmInstance->setTable('crm.menu_access_roles');
            $items = $crmInstance->newQuery()->where('role_id', $roleId)->with('menu')->orderBy('morder')->get();
            if ($items->isNotEmpty()) {
                return $items;
            }
        } catch (\Throwable $e) {}

        return collect();
    }
}

