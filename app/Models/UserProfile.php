<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    public const FIELDS = [
        'gender', 'dob', 'alt_phone', 'occupation',
        'address', 'city', 'state', 'pincode',
        'interested_in', 'heard_from',
    ];

    protected $fillable = [
        'user_id',
        'gender', 'dob', 'alt_phone', 'occupation',
        'address', 'city', 'state', 'pincode',
        'interested_in', 'heard_from',
    ];

    protected function casts(): array
    {
        return ['dob' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}