<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table = 'master.menus';
    protected $primaryKey = 'menu_id';
    public $timestamps = false;

    protected $fillable = [
        'menu_id',
        'menu_code',
        'module_id',
        'menu_name',
        'controller_ref',
        'icon_name',
        'is_collapsible',
        'lock_status',
        'project_id',
        'campus_id',
        'created_by',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
        'parent_menu_id',
    ];

    protected $casts = [
        'menu_id' => 'integer',
        'module_id' => 'integer',
        'parent_menu_id' => 'integer',
        'is_collapsible' => 'boolean',
        'lock_status' => 'integer',
        'log_timestamp' => 'datetime',
    ];

    public function accessRoles(): HasMany
    {
        return $this->hasMany(\Modules\Auth\Models\MenuAccessRole::class, 'menu_id', 'menu_id');
    }
}

