<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name', 'price', 'duration_days', 'max_users', 'description', 'features', 'status', 'sort_order', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'price'         => 'decimal:2',
            'duration_days' => 'integer',
            'max_users'     => 'integer',
            'features'      => 'array',
            'sort_order'    => 'integer',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(AdminSubscription::class, 'plan_id');
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'active');
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('price');
    }
}