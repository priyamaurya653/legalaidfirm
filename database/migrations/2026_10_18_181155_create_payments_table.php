<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Payments – har superadmin / admin apne client se mila payment manually darj karta hai
 * PRIVATE: sirf owner_id wala hi dekh / badal sakta hai (superadmin bhi dusre ka nahi dekhta)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();   // jisne entry ki (superadmin/admin)
            $table->string('receipt_no', 30)->nullable()->unique();                   // RCPT-000001 (auto)

            // ---- client (kisne diya) ----
            $table->string('client_name', 120);
            $table->string('client_phone', 15)->nullable();
            $table->string('client_email', 150)->nullable();

            // ---- kis cheez ke liye ----
            $table->string('purpose', 200);                                           // e.g. Bail application fee
            $table->string('category', 60)->nullable()->index();                      // Consultation, Court Fee …

            // ---- payment ----
            $table->decimal('amount', 12, 2);
            $table->string('payment_mode', 30)->default('Cash');                      // Cash | UPI | Bank Transfer | Cheque | Card | Other
            $table->date('payment_date')->index();
            $table->string('reference_no', 100)->nullable();                          // UPI txn id / cheque no
            $table->string('status', 20)->default('received')->index();              // received | pending
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();                                 // receipt / screenshot (private)
            $table->string('attachment_name')->nullable();

            $table->timestamps();
            $table->index(['owner_id', 'payment_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};