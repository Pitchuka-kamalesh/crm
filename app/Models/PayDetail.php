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
        'amountpaid',
        'paymentstatus',
        'txnid',
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
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'feeid' => 'integer',
        'feepayable' => 'float',
        'defcouponamount' => 'float',
        'percouponamount' => 'float',
        'totalcouponamount' => 'float',
        'paidamount' => 'float',
        'amountpaid' => 'float',
        'txndatetime' => 'datetime',
        'log_timestamp' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudentRegistration::class, 'applino', 'applinano');
    }

    public function receipt(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ReceiptDetail::class, 'applino', 'applino');
    }

    // ==========================================
    // ELOQUENT QUERY SCOPES (REPLACES RAW SQL)
    // ==========================================

    public function scopeSuccess($query)
    {
        return $query->where('txnstatus', 'success');
    }

    public function scopePending($query)
    {
        return $query->where('txnstatus', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('txnstatus', 'failed');
    }

    public function scopeByDateRange($query, $from, $to)
    {
        if (!empty($from) && !empty($to)) {
            return $query->whereBetween('txndatetime', [$from, $to]);
        }
        return $query;
    }
}
