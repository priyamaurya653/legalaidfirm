<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Practice Areas (dynamic) – superadmin panel se add / edit / delete
 * Form me 2 main fields: name + type
 * Extra DB fields: slug, icon, image, short_description, description, sort_order, status, created_by, updated_by
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('practice_areas', function (Blueprint $table) {
            $table->id();

            // ---- form ke main fields ----
            $table->string('name', 120);
            $table->string('type', 60)->index();                                // user khud likhta hai, e.g. Litigation, Corporate

            // ---- extra fields ----
            $table->string('slug', 140)->unique();                              // URL: /practice-areas/{slug}
            $table->string('icon', 60)->nullable();                             // Font Awesome class, e.g. fa-solid fa-gavel
            $table->string('image')->nullable();                                // storage path ya full URL
            $table->string('short_description', 255)->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->string('status', 20)->default('active')->index();           // active | inactive
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('practice_areas');
    }
};