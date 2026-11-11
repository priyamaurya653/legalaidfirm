<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Career
 *  job_openings        – superadmin job post karta hai (website Career page par dikhti hai)
 *  career_applications – website ka "Send Your Application" form
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);                                   // Associate Advocate – Litigation
            $table->string('job_type', 30)->default('Full Time');           // Full Time | Part Time | Internship | Contract
            $table->string('experience', 60)->nullable();                   // 2–5 Years
            $table->string('location', 100)->default('New Delhi');
            $table->unsignedSmallInteger('openings')->default(1);           // kitni vacancies
            $table->string('salary', 80)->nullable();                       // ₹4–6 LPA
            $table->text('description')->nullable();
            $table->text('requirements')->nullable();
            $table->date('last_date')->nullable();                          // is date ke baad website se hat jayegi
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('status', 20)->default('active')->index();       // active | closed
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('career_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_opening_id')->nullable()->constrained('job_openings')->nullOnDelete();
            $table->string('position', 150)->nullable();                    // job ka naam copy (job delete ho tab bhi dikhe)
            $table->string('name', 100);
            $table->string('email', 150);
            $table->string('phone', 15);
            $table->string('resume')->nullable();                           // private storage
            $table->string('resume_name')->nullable();
            $table->text('cover')->nullable();
            $table->string('status', 20)->default('new')->index();          // new | shortlisted | rejected | hired
            $table->text('notes')->nullable();                              // superadmin ke notes
            $table->timestamp('read_at')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('career_applications');
        Schema::dropIfExists('job_openings');
    }
};