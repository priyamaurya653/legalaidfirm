<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Website Contact page ka "Book a Consultation" form */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 150);
            $table->string('phone', 15);
            $table->foreignId('practice_area_id')->nullable()->constrained('practice_areas')->nullOnDelete();
            $table->string('practice_area', 120)->nullable();          // naam ki copy (area delete ho jaye tab bhi dikhe) / "Other"
            $table->text('message');
            $table->string('status', 20)->default('new')->index();      // new | contacted | closed
            $table->timestamp('read_at')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};