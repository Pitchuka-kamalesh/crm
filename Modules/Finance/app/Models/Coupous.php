<?php

namespace Modules\Finance\Models;

use App\Models\CrmStudentRegistration;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coupous extends Model
{
    use HasFactory;

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
        'id'             => 'integer',
        'coupcount'      => 'integer',
        'coucharcount'   => 'integer',
        'coupcurrcount'  => 'integer',
        'discountamount' => 'float',
        'discount'       => 'float',
        'log_timestamp'  => 'datetime',
    ];

    // ==========================================
    // RELATIONSHIPS
    // ==========================================

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudentRegistration::class, 'applino', 'applinano');
    }

    // ==========================================
    // ELOQUENT QUERY SCOPES
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('coupstatus', 1);
    }

    public function scopePersonal($query)
    {
        return $query->where(function ($q) {
            $q->where('coupontype', 2)->orWhere('coupontype', 'personal')->orWhere('coupontype', 'PERSONAL');
        });
    }

    public function scopeDefault($query)
    {
        return $query->where(function ($q) {
            $q->where('coupontype', 1)->orWhere('coupontype', 'default')->orWhere('coupontype', 'DEFAULT');
        });
    }

    public function scopeByCandidate($query, $applino)
    {
        return $query->where('applino', $applino);
    }
}
