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
        'applino',
        'crmid',
        'coupcount',
        'coucharcount',
        'couponcodestart',
        'currentcouponcode',
        'discountamount',
        'discount',
        'coupcurrcount',
        'coupstatus',
        'usercode',
        'user_ip',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'id' => 'integer',
        'coupcount' => 'integer',
        'coucharcount' => 'integer',
        'coupcurrcount' => 'integer',
        'discountamount' => 'float',
        'discount' => 'float',
        'log_timestamp' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudentRegistration::class, 'applino', 'applinano');
    }
}
