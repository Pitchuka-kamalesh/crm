<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptDetail extends Model
{
    protected $table = 'finance.receiptdetails';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'applino',
        'receiptno',
        'paytype',
        'feepayable',
        'defcouponamount',
        'percouponamount',
        'totalcouponamount',
        'txndatetime',
        'receiptdatetime',
        'log_userid',
        'log_ipaddress',
        'log_timestamp',
    ];

    protected $casts = [
        'id' => 'integer',
        'feepayable' => 'decimal:2',
        'defcouponamount' => 'decimal:2',
        'percouponamount' => 'decimal:2',
        'totalcouponamount' => 'decimal:2',
        'txndatetime' => 'datetime',
        'receiptdatetime' => 'datetime',
        'log_timestamp' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(CrmStudentRegistration::class, 'applino', 'applinano');
    }
}

