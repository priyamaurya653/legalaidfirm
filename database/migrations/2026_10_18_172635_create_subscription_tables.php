<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Subscriptions
 *  subscription_plans    – superadmin ke banaye plans (monthly)
 *  admin_subscriptions   – superadmin admin ko ACCEPT karke plan deta hai -> turant 1 mahina active
 *  subscription_requests – subscription khatam / nahi hai to admin superadmin ko request bhejta hai
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->decimal('price', 10, 2)->default(0);                  // per month (₹)
            $table->unsignedSmallInteger('duration_days')->default(30);    // 1 month
            $table->unsignedInteger('max_users')->nullable();              // null = unlimited users
            $table->text('description')->nullable();
            $table->json('features')->nullable();                          // ["Unlimited cases", ...]
            $table->string('status', 20)->default('active')->index();      // active | inactive
            $table->unsignedInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('admin_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            // plan ki copy (plan badle / delete ho to bhi record sahi rahe)
            $table->string('plan_name', 100);
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedSmallInteger('duration_days')->default(30);
            $table->unsignedInteger('max_users')->nullable();
            // active | expired | cancelled
            $table->string('status', 20)->default('active')->index();
            $table->text('note')->nullable();                              // superadmin ka message
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('accepted_at')->nullable();                  // superadmin ne kab accept kiya
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable()->index();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('subscription_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('plan_id')->nullable()->constrained('subscription_plans')->nullOnDelete();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('open')->index();        // open | resolved | dismissed
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_requests');
        Schema::dropIfExists('admin_subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};