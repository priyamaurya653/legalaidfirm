<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoginLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'login_input', 'role', 'status', 'reason', 'token_id',
        'ip_address', 'user_agent', 'device', 'browser', 'logged_in_at', 'logged_out_at',
    ];

    protected function casts(): array
    {
        return [
            'logged_in_at'  => 'datetime',
            'logged_out_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Login attempt DB me save karo (success | failed | blocked) */
    public static function record(Request $request, ?User $user, string $status, ?string $reason = null, ?int $tokenId = null, ?string $input = null): self
    {
        $ua = (string) $request->userAgent();

        return self::create([
            'user_id'      => $user?->id,
            'login_input'  => $input ? Str::limit($input, 150, '') : null,
            'role'         => $user?->role,
            'status'       => $status,
            'reason'       => $reason,
            'token_id'     => $tokenId,
            'ip_address'   => $request->ip(),
            'user_agent'   => Str::limit($ua, 255, ''),
            'device'       => preg_match('/ipad|tablet/i', $ua) ? 'Tablet' : (preg_match('/mobile|android|iphone/i', $ua) ? 'Mobile' : 'Desktop'),
            'browser'      => str_contains($ua, 'Edg/') ? 'Edge' : (str_contains($ua, 'Firefox/') ? 'Firefox' : (str_contains($ua, 'Chrome/') ? 'Chrome' : (str_contains($ua, 'Safari/') ? 'Safari' : 'Other'))),
            'logged_in_at' => now(),
        ]);
    }
}