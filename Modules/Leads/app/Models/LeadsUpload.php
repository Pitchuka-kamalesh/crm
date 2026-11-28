<?php

namespace Modules\Leads\Models;

use Illuminate\Database\Eloquent\Model;

class LeadsUpload extends Model
{
    protected $table = 'crm.leads_upload';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'file_name',
        'file_size',
        'total_records',
        'new_leads_added',
        'duplicates_skipped',
        'upload_status',
        'uploaded_by',
        'upload_date_time',
    ];

    protected function casts(): array
    {
        return [
            'total_records' => 'integer',
            'new_leads_added' => 'integer',
            'duplicates_skipped' => 'integer',
            'upload_date_time' => 'datetime',
        ];
    }

    public function getFilenameAttribute(): ?string
    {
        return $this->attributes['file_name'] ?? null;
    }

    public function getFilesizeAttribute(): ?string
    {
        return $this->attributes['file_size'] ?? null;
    }

    public function getInsertedCountAttribute(): int
    {
        return (int) ($this->attributes['new_leads_added'] ?? 0);
    }

    public function getDuplicatesCountAttribute(): int
    {
        return (int) ($this->attributes['duplicates_skipped'] ?? 0);
    }

    public function getUploadedAtAttribute(): mixed
    {
        return $this->attributes['upload_date_time'] ?? null;
    }
}
