<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'parent_menu_id',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_menu_id', 'menu_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_menu_id', 'menu_id');
    }

    public function accessRoles(): HasMany
    {
        return $this->hasMany(MenuAccessRole::class, 'menu_id', 'menu_id');
    }
}

