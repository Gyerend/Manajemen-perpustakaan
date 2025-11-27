<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans');
            $table->decimal('amount', 8, 2); // Jumlah denda
            $table->text('reason'); // Alasan (misal: Keterlambatan 3 hari)
            $table->date('paid_at')->nullable(); // Tanggal pembayaran
            $table->enum('status', ['outstanding', 'paid'])->default('outstanding'); // Tertunggak / Lunas
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
