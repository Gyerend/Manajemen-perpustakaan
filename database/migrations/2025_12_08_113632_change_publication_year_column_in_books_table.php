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
        Schema::table('books', function (Blueprint $table) {
            // Ubah tipe data kolom 'publication_year' menjadi SMALLINT (cukup untuk menyimpan tahun)
            $table->smallInteger('publication_year')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            // Jika perlu rollback, kembalikan ke TINYINT.
            // CATATAN: Rollback ini akan gagal jika ada nilai > 127
            $table->tinyInteger('publication_year')->change();
        });
    }
};
