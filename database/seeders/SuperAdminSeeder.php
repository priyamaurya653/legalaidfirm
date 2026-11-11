<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

/**
 * Superadmin banao / password reset karo:
 *
 *   php artisan superadmin:create
 *   php artisan superadmin:create --email=me@site.com --password=MyPass@123
 */
class CreateSuperAdmin extends Command
{
    protected $signature = 'superadmin:create
                            {--email= : Superadmin email (default: SUPERADMIN_EMAIL ya superadmin@lexoralegal.com)}
                            {--password= : Password (default: SUPERADMIN_PASSWORD ya Admin@12345)}
                            {--name= : Name}';

    protected $description = 'Create or reset the superadmin account (email + password login)';

    public function handle(): int
    {
        // zaroori columns check (migration chali hai ya nahi)
        foreach (['role', 'status', 'phone'] as $col) {
            if (! Schema::hasColumn('users', $col)) {
                $this->error("users table me '{$col}' column nahi hai. Pehle chalao:  php artisan migrate");
                return self::FAILURE;
            }
        }
        if (! Schema::hasTable('personal_access_tokens')) {
            $this->error("personal_access_tokens table nahi hai. Pehle chalao:  php artisan install:api  (phir php artisan migrate)");
            return self::FAILURE;
        }

        $email    = strtolower(trim($this->option('email') ?: env('SUPERADMIN_EMAIL', 'superadmin@lexoralegal.com')));
        $password = $this->option('password') ?: env('SUPERADMIN_PASSWORD', 'Admin@12345');
        $name     = $this->option('name') ?: env('SUPERADMIN_NAME', 'Super Admin');

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first() ?? new User();

        // forceFill: $fillable par depend nahi, Hash::make: 'hashed' cast ho ya na ho, dono me sahi
        $user->forceFill([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => User::ROLE_SUPERADMIN,
            'status'   => 'active',
        ]);
        if (! $user->phone) {
            $phone = env('SUPERADMIN_PHONE', '9999999999');
            $user->phone = User::where('phone', $phone)->where('id', '!=', $user->id ?? 0)->exists() ? null : $phone;
        }
        $user->save();

        $fresh = $user->fresh();
        $ok = Hash::check($password, $fresh->getRawOriginal('password'));

        $this->newLine();
        $this->info('✔ Superadmin ready');
        $this->table(['Field', 'Value'], [
            ['Email', $fresh->email],
            ['Password', $password],
            ['Role', $fresh->role],
            ['Status', $fresh->status],
            ['Password check', $ok ? 'OK ✔' : 'FAILED ✘'],
        ]);
        $this->line('Login: ' . url('/login') . '  →  ' . url('/superadmin'));

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}