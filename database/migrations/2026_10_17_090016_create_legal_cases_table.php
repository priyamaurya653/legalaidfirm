<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cases module
 *  legal_cases    – case + client + KYC (Aadhaar mandatory, PAN optional)
 *  case_documents – case ke saath upload hue documents (multiple)
 * (model ka naam "LegalCase" hai kyunki "Case" PHP ka reserved word hai)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_cases', function (Blueprint $table) {
            $table->id();
            $table->string('case_no', 30)->nullable()->unique();                   // LX-0001 (auto)

            // ---- case ----
            $table->string('title', 200);                                          // Matter
            $table->foreignId('practice_area_id')->nullable()->constrained('practice_areas')->nullOnDelete();
            $table->foreignId('advocate_id')->nullable()->constrained('users')->nullOnDelete();   // admin (advocate)
            $table->date('next_hearing_date')->nullable();
            $table->string('status', 20)->default('pending')->index();            // active | hearing | pending | closed

            // ---- client ----
            $table->string('client_name', 120);
            $table->string('client_phone', 15);

            // ---- 3 naye fields: Aadhaar (mandatory), PAN, Documents ----
            $table->text('aadhaar_number');                                        // encrypted
            $table->string('aadhaar_last4', 4)->index();                           // listing/search ke liye
            $table->string('aadhaar_file');                                        // private storage
            $table->text('pan_number')->nullable();                                // encrypted
            $table->string('pan_file')->nullable();

            // ---- extra DB fields ----
            $table->string('client_email', 150)->nullable();
            $table->string('court_name', 150)->nullable();
            $table->string('opponent_name', 150)->nullable();
            $table->date('filing_date')->nullable();
            $table->string('priority', 10)->default('normal');                     // low | normal | high | urgent
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('case_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('legal_case_id')->constrained('legal_cases')->cascadeOnDelete();
            $table->string('title', 150);
            $table->string('path');
            $table->string('original_name');
            $table->string('mime', 100)->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_documents');
        Schema::dropIfExists('legal_cases');
    }
};