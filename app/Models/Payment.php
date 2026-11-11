<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    public const MODES      = ['Cash', 'UPI', 'Bank Transfer', 'Cheque', 'Card', 'Other'];
    public const STATUSES   = ['received' => 'Received', 'pending' => 'Pending'];
    public const CATEGORIES = ['Consultation Fee', 'Retainer Fee', 'Court Fee', 'Case Fee', 'Documentation', 'Registration', 'Legal Notice', 'Drafting', 'Other'];

    protected $fillable = [
        'owner_id', 'receipt_no', 'client_name', 'client_phone', 'client_email', 'purpose', 'category',
        'amount', 'payment_mode', 'payment_date', 'reference_no', 'status', 'notes', 'attachment', 'attachment_name',
    ];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'payment_date' => 'date'];
    }

    protected static function booted(): void
    {
        static::created(function (Payment $p) {
            if (! $p->receipt_no) {
                $p->receipt_no = 'RCPT-' . str_pad((string) $p->id, 6, '0', STR_PAD_LEFT);
                $p->saveQuietly();
            }
        });
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /** SIRF apne payments */
    public function scopeOwnedBy(Builder $q, User $user): Builder
    {
        return $q->where('owner_id', $user->id);
    }
}