<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CareerApplication extends Model
{
    public const STATUSES = [
        'new'         => 'New',
        'shortlisted' => 'Shortlisted',
        'rejected'    => 'Rejected',
        'hired'       => 'Hired',
    ];

    protected $fillable = [
        'job_opening_id', 'position', 'name', 'email', 'phone', 'resume', 'resume_name',
        'cover', 'status', 'notes', 'read_at', 'ip',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function job(): BelongsTo
    {
        return $this->belongsTo(JobOpening::class, 'job_opening_id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }
}