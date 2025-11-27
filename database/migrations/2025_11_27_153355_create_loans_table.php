<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('book_id')->constrained('books');

            // FIX: Tambahkan nullable() pada semua kolom tanggal
            $table->date('loan_date')->nullable(); // <-- Dibuat nullable untuk Reservasi
            $table->date('due_date')->nullable();  // <-- Dibuat nullable
            $table->date('return_date')->nullable();

            $table->enum('status', ['pending', 'borrowed', 'returned', 'extended', 'reserved', 'reserved_active'])->default('borrowed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
