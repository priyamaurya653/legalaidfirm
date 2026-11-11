<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminSubscription extends Model
{
    public const STATUSES = [
        'active'    => 'Active',
        'expired'   => 'Expired',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'admin_id', 'plan_id', 'plan_name', 'price', 'duration_days', 'max_users', 'status', 'note',
        'assigned_by', 'assigned_at', 'accepted_at', 'starts_at', 'ends_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'price'        => 'decimal:2',
            'assigned_at'  => 'datetime',
            'accepted_at'  => 'datetime',
            'starts_at'    => 'datetime',
            'ends_at'      => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function admin(): BelongsTo    { return $this->belongsTo(User::class, 'admin_id'); }
    public function plan(): BelongsTo     { return $this->belongsTo(SubscriptionPlan::class, 'plan_id'); }
    public function assigner(): BelongsTo { return $this->belongsTo(User::class, 'assigned_by'); }

    /** abhi chalu subscription (active + end date baaki) */
    public function scopeCurrent(Builder $q): Builder
    {
        return $q->where('status', 'active')->where('starts_at', '<=', now())->where('ends_at', '>', now());
    }

    /** jinki date nikal gayi unhe "expired" mark karo (har request par sasta update) */
    public static function expireOld(): int
    {
        return static::where('status', 'active')->where('ends_at', '<=', now())->update(['status' => 'expired']);
    }

    /** admin ka chalu subscription */
    public static function currentFor(int $adminId): ?self
    {
        static::expireOld();

        return static::where('admin_id', $adminId)->current()->orderBy('ends_at', 'desc')->first();
    }

    public function daysLeft(): int
    {
        if ($this->status !== 'active' || ! $this->ends_at) {
            return 0;
        }

        return max(0, (int) ceil(now()->diffInSeconds($this->ends_at, false) / 86400));
    }

    public function statusLabel(): string
    {
        if ($this->status === 'active' && $this->starts_at && $this->starts_at->isFuture()) {
            return 'Scheduled';
        }

        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }
}