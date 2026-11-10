<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponInfo extends Model
{
    protected $table = 'finance.couponinfo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'couponsno',
        'admyear',
        'campuscode',
        'coupontype',
        'couponname',
        'coupcount',
        'coucharcount',
        'coupcurrcount',
        'discountamount',
        'couponcodestart',
        'currentcouponcode',
        'coupstatus',
        'applino',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected $casts = [
        'id' => 'integer',
        'coupcount' => 'integer',
        'coucharcount' => 'integer',
        'coupcurrcount' => 'integer',
        'discountamount' => 'decimal:2',
        'log_timestamp' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudentRegistration::class, 'applino', 'applinano');
    }
}

