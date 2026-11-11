<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    public const STATUSES = ['active' => 'Active', 'inactive' => 'Inactive'];

    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'city', 'address',
        'practice_area_id', 'case_details', 'advocate_id',
        'aadhaar_number', 'aadhaar_last4', 'aadhaar_file', 'pan_number', 'pan_file',
        'alt_phone', 'state', 'pincode', 'status',
        'created_by', 'updated_by',
    ];

    protected $hidden = ['aadhaar_number', 'pan_number'];

    protected function casts(): array
    {
        return [
            'aadhaar_number' => 'encrypted',
            'pan_number'     => 'encrypted',
        ];
    }

    /* ---------- relations ---------- */
    public function practiceArea(): BelongsTo { return $this->belongsTo(PracticeArea::class); }
    /** client ka login account (role = user) */
    public function user(): BelongsTo         { return $this->belongsTo(User::class, 'user_id'); }
    public function advocate(): BelongsTo     { return $this->belongsTo(User::class, 'advocate_id'); }
    public function creator(): BelongsTo      { return $this->belongsTo(User::class, 'created_by'); }
    public function editor(): BelongsTo       { return $this->belongsTo(User::class, 'updated_by'); }
    public function documents(): HasMany      { return $this->hasMany(ClientDocument::class); }

    /* ---------- scopes ---------- */
    /** superadmin = sab clients, admin = sirf jo use assign hain */
    /** superadmin = sab, admin = jo use assign hain YA jo usne khud banaye (superadmin ko assign kiye ho tab bhi) */
    public function scopeVisibleTo(Builder $q, User $user): Builder
    {
        return $user->role === User::ROLE_SUPERADMIN
            ? $q
            : $q->where(fn ($w) => $w->where('clients.advocate_id', $user->id)->orWhere('clients.created_by', $user->id));
    }

    public function isVisibleTo(User $user): bool
    {
        return $user->role === User::ROLE_SUPERADMIN
            || (int) $this->advocate_id === (int) $user->id
            || (int) $this->created_by === (int) $user->id;
    }

    /** "Cases" column – Cases module me isi mobile number wale cases */
    public function scopeWithCasesCount(Builder $q): Builder
    {
        return $q->addSelect(['cases_count' => LegalCase::selectRaw('COUNT(*)')->whereColumn('legal_cases.client_phone', 'clients.phone')]);
    }

    /* ---------- helpers ---------- */
    public function maskedAadhaar(): string
    {
        return 'XXXX XXXX ' . $this->aadhaar_last4;
    }

    public function maskedPan(): ?string
    {
        $pan = $this->pan_number;

        return $pan ? substr($pan, 0, 2) . 'XXXXX' . substr($pan, -3) : null;
    }

    public function storageDir(): string
    {
        return 'clients/' . $this->id;
    }
}