<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponInfo extends Model
{
    protected $table = 'finance.couponinfo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $guarded = ['id'];

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

    // ==========================================
    // ELOQUENT QUERY SCOPES (REPLACES RAW SQL)
    // ==========================================

    public function scopeActive($query)
    {
        return $query->where('coupstatus', 1);
    }

    public function scopePersonal($query)
    {
        return $query->where(function ($q) {
            $q->where('coupontype', 2)->orWhere('coupontype', 'personal');
        });
    }

    public function scopeDefault($query)
    {
        return $query->where(function ($q) {
            $q->where('coupontype', 1)->orWhere('coupontype', 'default');
        });
    }

    public function scopeByCandidate($query, $applino)
    {
        return $query->where('applino', $applino);
    }
}
