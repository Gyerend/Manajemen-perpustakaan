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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            // PERBAIKAN 1: Tambahkan onDelete('cascade') untuk User (Mendukung Delete Account)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // PERBAIKAN 2: Tambahkan onDelete('cascade') untuk Book (Mendukung Delete Book)
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');

            $table->unsignedTinyInteger('rating'); // 1 hingga 5
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
    };
