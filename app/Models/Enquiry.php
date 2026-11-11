<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enquiry extends Model
{
    public const STATUSES = [
        'new'       => 'New',
        'contacted' => 'Contacted',
        'closed'    => 'Closed',
    ];

    protected $fillable = [
        'name', 'email', 'phone', 'practice_area_id', 'practice_area',
        'message', 'status', 'read_at', 'ip', 'user_agent',
    ];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function practiceArea(): BelongsTo
    {
        return $this->belongsTo(PracticeArea::class);
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }
}