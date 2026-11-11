<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Clients (superadmin panel)
 *  clients          – client + KYC (Aadhaar / PAN) + case kya hai + assigned admin
 *  client_documents – client ke documents (multiple)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // ---- basic (purane form wale) ----
            $table->string('name', 120);
            $table->string('phone', 15)->index();
            $table->string('email', 150)->nullable();
            $table->string('city', 80)->nullable();
            $table->text('address')->nullable();

            // ---- case kya hai + kisko assign ----
            $table->foreignId('practice_area_id')->nullable()->constrained('practice_areas')->nullOnDelete();
            $table->text('case_details')->nullable();
            $table->foreignId('advocate_id')->nullable()->constrained('users')->nullOnDelete();   // admin

            // ---- KYC ----
            $table->text('aadhaar_number');                    // encrypted
            $table->string('aadhaar_last4', 4)->index();
            $table->string('aadhaar_file')->nullable();        // private storage
            $table->text('pan_number')->nullable();            // encrypted
            $table->string('pan_file')->nullable();

            // ---- 4 extra fields ----
            $table->string('alt_phone', 15)->nullable();
            $table->string('state', 60)->nullable();
            $table->string('pincode', 6)->nullable();
            $table->string('status', 20)->default('active')->index();   // active | inactive

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('client_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
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
        Schema::dropIfExists('client_documents');
        Schema::dropIfExists('clients');
    }
};