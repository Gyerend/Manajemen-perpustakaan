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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('author');
            $table->string('publisher');
            $table->year('publication_year');
            $table->string('category');
            $table->integer('stock'); // Jumlah stok tersedia
            $table->integer('max_loan_days'); // Maksimal waktu peminjaman (misal: 7 hari)
            $table->decimal('daily_fine_rate', 8, 2); // Denda per hari (misal: 1000.00)
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
