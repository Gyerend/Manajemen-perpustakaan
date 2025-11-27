<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Fine;
use Carbon\Carbon;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil User yang sudah dibuat di UserSeeder
        $citra = User::where('email', 'citra@mail.com')->first();
        $dinda = User::where('email', 'dinda@mail.com')->first();
        $eko = User::where('email', 'eko@mail.com')->first();

        // Ambil Buku yang sudah dibuat di BookSeeder (asumsi ada 3 buku)
        $book1 = Book::find(1); // The Psychology of Money
        $book2 = Book::find(2); // Company of One
        $book3 = Book::find(3); // The Picture of Dorian Gray

        if (!$citra || !$dinda || !$book1 || !$book2 || !$book3) {
            echo "Pastikan UserSeeder dan BookSeeder sudah dijalankan dan menghasilkan data minimal 3 buku dan 2 mahasiswa.";
            return;
        }

        // --- SKENARIO 1: PINJAMAN AKTIF & NORMAL (Citra) ---
        // Peminjaman normal, belum jatuh tempo, bisa diperpanjang
        $loan1 = Loan::create([
            'user_id' => $citra->id,
            'book_id' => $book1->id,
            'loan_date' => Carbon::now()->subDays(3),
            'due_date' => Carbon::now()->addDays(4), // Jatuh tempo 4 hari lagi (max_loan_days = 7)
            'status' => 'borrowed',
        ]);
        $book1->decrement('stock');

        // --- SKENARIO 2: PINJAMAN TERLAMBAT & ADA DENDA (Dinda) ---
        // Peminjaman ini sudah terlambat 2 hari.
        $dueDate = Carbon::now()->subDays(2);
        $daysLate = 2;
        $dailyRate = $book2->daily_fine_rate;
        $amount = $daysLate * $dailyRate; // Denda total: 2 * 500 = 1000

        $loan2 = Loan::create([
            'user_id' => $dinda->id,
            'book_id' => $book2->id,
            'loan_date' => Carbon::now()->subDays($book2->max_loan_days + $daysLate),
            'due_date' => $dueDate,
            'status' => 'borrowed',
        ]);
        $book2->decrement('stock');

        // Tambahkan Denda yang Belum Lunas (Outstanding)
        Fine::create([
            'loan_id' => $loan2->id,
            'amount' => $amount,
            'reason' => "Denda tercatat saat pengembalian (simulasi terlambat).",
            'status' => 'outstanding',
        ]);

        // Pastikan Dinda diblokir (sudah kita set di UserSeeder, tapi set ulang di sini untuk jaga-jaga)
        $dinda->update(['is_blocked' => true]);


        // --- SKENARIO 3: RESERVASI (Eko) ---
        // Buku 3 direservasi oleh Eko karena stoknya akan kita kurangi menjadi 0
        $book3->update(['stock' => 0]);

        Loan::create([
            'user_id' => $eko->id,
            'book_id' => $book3->id,
            'loan_date' => null,
            'due_date' => null,
            'status' => 'reserved',
        ]);

        echo "Data dummy peminjaman, denda, dan reservasi berhasil ditambahkan!\n";
    }
}
