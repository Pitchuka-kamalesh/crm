<?php

namespace Modules\Finance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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

    /**
     * Relationship to student registration
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\CrmStudentRegistration::class, 'applino', 'applinano');
    }

    /**
     * Scope for application number
     */
    public function scopeForApplication(Builder $query, string $appliNo): Builder
    {
        return $query->where('applino', trim($appliNo));
    }

    /**
     * Scope for receipt number
     */
    public function scopeForReceipt(Builder $query, string $receiptNo): Builder
    {
        return $query->where('receiptno', trim($receiptNo));
    }
}

