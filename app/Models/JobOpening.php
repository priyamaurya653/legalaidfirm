<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobOpening extends Model
{
    public const TYPES    = ['Full Time', 'Part Time', 'Internship', 'Contract'];
    public const STATUSES = ['active' => 'Active', 'closed' => 'Closed'];

    protected $fillable = [
        'title', 'job_type', 'experience', 'location', 'openings', 'salary',
        'description', 'requirements', 'last_date', 'sort_order', 'status', 'created_by', 'updated_by',
    ];

    protected function casts(): array
    {
        return ['last_date' => 'date', 'openings' => 'integer', 'sort_order' => 'integer'];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(CareerApplication::class);
    }

    /** website par dikhne layak: active + last date nikli na ho */
    public function scopeLive(Builder $q): Builder
    {
        return $q->where('status', 'active')
            ->where(fn ($w) => $w->whereNull('last_date')->orWhereDate('last_date', '>=', today()));
    }

    public function scopeOrdered(Builder $q): Builder
    {
        return $q->orderBy('sort_order')->latest();
    }

    public function isLive(): bool
    {
        return $this->status === 'active' && (! $this->last_date || $this->last_date->gte(today()));
    }
}