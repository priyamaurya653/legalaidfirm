<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalCase extends Model
{
    public const STATUSES = [
        'active'  => 'Active',
        'hearing' => 'Hearing',
        'pending' => 'Pending',
        'closed'  => 'Closed',
    ];

    public const PRIORITIES = [
        'low'    => 'Low',
        'normal' => 'Normal',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ];

    protected $fillable = [
        'title', 'practice_area_id', 'advocate_id', 'next_hearing_date', 'status',
        'client_name', 'client_phone', 'client_email',
        'aadhaar_number', 'aadhaar_last4', 'aadhaar_file', 'pan_number', 'pan_file',
        'court_name', 'opponent_name', 'filing_date', 'priority', 'description',
        'created_by', 'updated_by',
    ];

    // Aadhaar / PAN kabhi bhi direct JSON me na jaaye
    protected $hidden = ['aadhaar_number', 'pan_number'];

    protected function casts(): array
    {
        return [
            'aadhaar_number'    => 'encrypted',
            'pan_number'        => 'encrypted',
            'next_hearing_date' => 'date',
            'filing_date'       => 'date',
        ];
    }

    protected static function booted(): void
    {
        // insert ke baad case number: LX-0001
        static::created(function (LegalCase $case) {
            if (! $case->case_no) {
                $case->case_no = 'LX-' . str_pad((string) $case->id, 4, '0', STR_PAD_LEFT);
                $case->saveQuietly();
            }
        });
    }

    /* ---------- relations ---------- */
    public function practiceArea(): BelongsTo { return $this->belongsTo(PracticeArea::class); }
    public function advocate(): BelongsTo     { return $this->belongsTo(User::class, 'advocate_id'); }
    public function creator(): BelongsTo      { return $this->belongsTo(User::class, 'created_by'); }
    public function editor(): BelongsTo       { return $this->belongsTo(User::class, 'updated_by'); }
    public function documents(): HasMany      { return $this->hasMany(CaseDocument::class); }

    /* ---------- scopes ---------- */
    /** superadmin = sab cases, admin (advocate) = sirf apne assigned cases */
    public function scopeVisibleTo(Builder $q, User $user): Builder
    {
        return $user->role === User::ROLE_SUPERADMIN ? $q : $q->where('advocate_id', $user->id);
    }

    public function isVisibleTo(User $user): bool
    {
        return $user->role === User::ROLE_SUPERADMIN || (int) $this->advocate_id === (int) $user->id;
    }

    /* ---------- helpers ---------- */
    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    public function maskedAadhaar(): string
    {
        return 'XXXX XXXX ' . $this->aadhaar_last4;
    }

    public function maskedPan(): ?string
    {
        $pan = $this->pan_number;

        return $pan ? substr($pan, 0, 2) . 'XXXXX' . substr($pan, -3) : null;
    }

    /** private storage folder */
    public function storageDir(): string
    {
        return 'cases/' . $this->id;
    }
}