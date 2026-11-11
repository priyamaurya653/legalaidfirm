<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 15)->nullable()->unique()->after('email');
            }
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 20)->default('user')->index()->after('phone');      // superadmin | admin | user
            }
            if (! Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('active')->index()->after('role');   // active | pending | inactive
            }
            if (! Schema::hasColumn('users', 'photo')) {
                $table->string('photo')->nullable()->after('status');
            }
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('photo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropColumn(['phone', 'role', 'status', 'photo', 'last_login_at']);
        });
    }
};