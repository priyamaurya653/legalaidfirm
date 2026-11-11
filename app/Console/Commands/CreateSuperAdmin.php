<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateSuperAdmin extends Command
{
    protected $signature = 'superadmin:create
                            {--email= : Superadmin email}
                            {--password= : Password}
                            {--name= : Name}';

    protected $description = 'Create or reset the superadmin account (email + password login)';

    public function handle(): int
    {
        foreach (['role', 'status', 'phone'] as $col) {
            if (! Schema::hasColumn('users', $col)) {
                $this->error("users table me '{$col}' column nahi hai. Pehle chalao:  php artisan migrate");
                return self::FAILURE;
            }
        }
        if (! Schema::hasTable('personal_access_tokens')) {
            $this->error('personal_access_tokens table nahi hai. Pehle chalao:  php artisan install:api');
            return self::FAILURE;
        }

        $email    = strtolower(trim($this->option('email') ?: env('SUPERADMIN_EMAIL', 'superadmin@lexoralegal.com')));
        $password = $this->option('password') ?: env('SUPERADMIN_PASSWORD', 'Admin@12345');
        $name     = $this->option('name') ?: env('SUPERADMIN_NAME', 'Super Admin');

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first() ?? new User();

        $user->forceFill([
            'name'     => $name,
            'email'    => $email,
            'password' => Hash::make($password),
            'role'     => 'superadmin',
            'status'   => 'active',
        ]);
        if (! $user->phone) {
            $phone = env('SUPERADMIN_PHONE', '9999999999');
            $user->phone = User::where('phone', $phone)->where('id', '!=', $user->id ?? 0)->exists() ? null : $phone;
        }
        $user->save();

        $fresh = $user->fresh();
        $ok = Hash::check($password, $fresh->getRawOriginal('password'));

        $this->info('✔ Superadmin ready');
        $this->table(['Field', 'Value'], [
            ['Email', $fresh->email],
            ['Password', $password],
            ['Role', $fresh->role],
            ['Status', $fresh->status],
            ['Password check', $ok ? 'OK ✔' : 'FAILED ✘'],
        ]);

        return $ok ? self::SUCCESS : self::FAILURE;
    }
}