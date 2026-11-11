<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvocateProfile extends Model
{
    public const FIELDS = [
        'father_name', 'dob', 'gender', 'alt_phone',
        'enrollment_no', 'bar_council', 'enrollment_date', 'experience_years',
        'qualification', 'university', 'passing_year', 'specializations',
        'courts', 'languages', 'bio',
        'office_address', 'city', 'state', 'pincode',
        'id_proof_type', 'id_proof_number',
    ];

    public const FILES = [
        'id_proof'               => 'advocates/id-proofs',
        'enrollment_certificate' => 'advocates/certificates',
    ];

    protected $fillable = [
        'user_id',
        'father_name', 'dob', 'gender', 'alt_phone',
        'enrollment_no', 'bar_council', 'enrollment_date', 'experience_years',
        'qualification', 'university', 'passing_year', 'specializations',
        'courts', 'languages', 'bio',
        'office_address', 'city', 'state', 'pincode',
        'id_proof_type', 'id_proof_number',
        'id_proof', 'enrollment_certificate',
    ];

    protected function casts(): array
    {
        return [
            'specializations' => 'array',
            'dob'             => 'date',
            'enrollment_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}