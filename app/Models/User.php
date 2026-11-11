<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_ADMIN      = 'admin';      // Admin = Advocate
    public const ROLE_USER       = 'user';       // Website user / client

    public const ROLES = [self::ROLE_SUPERADMIN, self::ROLE_ADMIN, self::ROLE_USER];

    public const ROLE_LABELS = [
        self::ROLE_SUPERADMIN => 'Super Admin',
        self::ROLE_ADMIN      => 'Admin (Advocate)',
        self::ROLE_USER       => 'User',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'photo',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function advocateProfile(): HasOne
    {
        return $this->hasOne(AdvocateProfile::class);
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function loginLogs(): HasMany
    {
        return $this->hasMany(LoginLog::class);
    }

    /** kis admin (advocate) ne is user ko banaya */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** is admin / superadmin ko assign clients (Clients module) */
    public function assignedClients(): HasMany
    {
        return $this->hasMany(Client::class, 'advocate_id');
    }

    /** is admin ko assign cases */
    public function assignedCases(): HasMany
    {
        return $this->hasMany(LegalCase::class, 'advocate_id');
    }

    /** admin ke banaye users */
    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    public function roleLabel(): string
    {
        return self::ROLE_LABELS[$this->role] ?? ucfirst((string) $this->role);
    }

    /** Login ke baad kis dashboard par jaana hai */
    public function dashboardUrl(): string
    {
        return match ($this->role) {
            self::ROLE_SUPERADMIN => route('superadmin.dashboard'),   // /superadmin
            self::ROLE_ADMIN      => route('admin.dashboard'),        // /admin
            default               => route('user.dashboard'),         // /dashboard
        };
    }
}