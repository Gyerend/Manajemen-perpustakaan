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

            // PERBAIKAN 1: Tambahkan onDelete('cascade') untuk User (mendukung delete account)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // PERBAIKAN 2: Tambahkan onDelete('cascade') untuk Book (mendukung delete book)
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');

            // FIX 3: Pertahankan nullable() untuk Reservasi
            $table->date('loan_date')->nullable();
            $table->date('due_date')->nullable();
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
