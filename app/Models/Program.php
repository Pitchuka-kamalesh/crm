<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $table = 'master.programs';
    protected $primaryKey = 'program_id';
    public $timestamps = false;

    protected $fillable = [
        'program_id',
        'campus_id',
        'program_category',
        'program_code',
        'program_duration',
        'program_level',
        'program_long_name',
        'program_mode',
        'program_seprt_code',
        'program_short_name',
        'program_start_year',
    ];
}

