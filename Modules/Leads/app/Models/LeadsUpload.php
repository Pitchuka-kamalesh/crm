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

    // Property aliases for flexible code access
    public function getFilenameAttribute(): ?string
    {
        return $this->attributes['file_name'] ?? null;
    }

    public function setFilenameAttribute(?string $value): void
    {
        $this->attributes['file_name'] = $value;
    }

    public function getFilesizeAttribute(): ?string
    {
        return $this->attributes['file_size'] ?? null;
    }

    public function setFilesizeAttribute(?string $value): void
    {
        $this->attributes['file_size'] = $value;
    }

    public function getInsertedCountAttribute(): int
    {
        return (int) ($this->attributes['new_leads_added'] ?? 0);
    }

    public function setInsertedCountAttribute(int $value): void
    {
        $this->attributes['new_leads_added'] = $value;
    }

    public function getDuplicatesCountAttribute(): int
    {
        return (int) ($this->attributes['duplicates_skipped'] ?? 0);
    }

    public function setDuplicatesCountAttribute(int $value): void
    {
        $this->attributes['duplicates_skipped'] = $value;
    }

    public function getUploadedAtAttribute(): mixed
    {
        return $this->attributes['upload_date_time'] ?? null;
    }

    public function setUploadedAtAttribute(mixed $value): void
    {
        $this->attributes['upload_date_time'] = $value;
    }
}
