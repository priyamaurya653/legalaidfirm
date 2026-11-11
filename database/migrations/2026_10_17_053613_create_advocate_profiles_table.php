<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('advocate_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            // Personal
            $table->string('father_name', 100)->nullable();
            $table->date('dob')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('alt_phone', 15)->nullable();

            // Professional
            $table->string('enrollment_no', 50)->unique();
            $table->string('bar_council', 100);
            $table->date('enrollment_date')->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->string('qualification', 50)->nullable();
            $table->string('university', 150)->nullable();
            $table->unsignedSmallInteger('passing_year')->nullable();
            $table->json('specializations')->nullable();
            $table->string('courts')->nullable();
            $table->string('languages', 150)->nullable();
            $table->text('bio')->nullable();

            // Address
            $table->string('office_address');
            $table->string('city', 80);
            $table->string('state', 80);
            $table->string('pincode', 6);

            // Documents
            $table->string('id_proof_type', 30)->nullable();
            $table->string('id_proof_number', 30)->nullable();
            $table->string('id_proof')->nullable();
            $table->string('enrollment_certificate')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('advocate_profiles');
    }
};