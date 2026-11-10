<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayDetail extends Model
{
    protected $table = 'finance.paydetails';
    protected $primaryKey = 'feeid';
    public $timestamps = false;

    protected $fillable = [
        'feeid',
        'applino',
        'feepayable',
        'defcoupon',
        'defcouponamount',
        'percoupon',
        'percouponamount',
        'totalcouponamount',
        'paidamount',
        'paytype',
        'txndatetime',
        'txncrmid',
        'txnpayuid',
        'txnerror',
        'txnremark',
        'txnstatus',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected $casts = [
        'feeid' => 'integer',
        'feepayable' => 'decimal:2',
        'defcouponamount' => 'decimal:2',
        'percouponamount' => 'decimal:2',
        'totalcouponamount' => 'decimal:2',
        'paidamount' => 'decimal:2',
        'txndatetime' => 'datetime',
        'log_timestamp' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudentRegistration::class, 'applino', 'applinano');
    }
}

