<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('login_input', 150)->nullable();
            $table->string('role', 20)->nullable()->index();
            $table->string('status', 20)->default('success')->index();   // success | failed | blocked
            $table->string('reason')->nullable();
            $table->unsignedBigInteger('token_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device', 20)->nullable();
            $table->string('browser', 30)->nullable();
            $table->timestamp('logged_in_at')->useCurrent()->index();
            $table->timestamp('logged_out_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};