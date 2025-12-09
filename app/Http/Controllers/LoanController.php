<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Fine;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    private const MAX_LOANS = 3;

    // Helper: Pengecekan dan Perhitungan Denda
    private function calculateFine(Loan $loan): ?Fine
    {
        $dueDate = Carbon::parse($loan->due_date);
        $returnDate = Carbon::now();

        if ($returnDate->greaterThan($dueDate)) {
            // Perbedaan hari yang dihitung harus absolut
            $daysLate = $returnDate->diffInDays($dueDate);
            $dailyRate = $loan->book->daily_fine_rate;
            $amount = $daysLate * $dailyRate;

            if ($amount > 0) {
                $fine = Fine::create([
                    'loan_id' => $loan->id,
                    'amount' => $amount,
                    'reason' => "Keterlambatan pengembalian selama {$daysLate} hari.",
                    'status' => 'outstanding',
                ]);

                Notification::create([
                    'user_id' => $loan->user_id,
                    'title' => 'Peringatan Denda Keterlambatan',
                    'message' => "Anda dikenakan denda sebesar Rp{$amount} untuk buku '{$loan->book->title}' karena terlambat {$daysLate} hari.",
                ]);

                $loan->user->update(['is_blocked' => true]);

                return $fine;
            }
        }
        return null;
    }

    // ===============================================
    // MAHASISWA ACTIONS
    // ===============================================

    /**
     * Mahasiswa: Proses Peminjaman Buku.
     */
    public function borrow(Book $book): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cari reservasi aktif milik user saat ini
        $activeReservation = $user->loans()
            ->where('book_id', $book->id)
            ->where('status', 'reserved_active')
            ->first();

        // 1. Cek Stok Awal
        if ($book->stock <= 0 && !$activeReservation) {
            return back()->with('error', 'Stok buku ini sedang tidak tersedia.');
        }

        // 2. Cek Reservasi Aktif Pengguna Lain
        $hasActiveReservationByOther = Loan::where('book_id', $book->id)
            ->where('status', 'reserved_active')
            ->where('user_id', '!=', $user->id)
            ->exists();

        // Jika stok = 1 dan ada reservasi aktif dari pengguna lain, tolak peminjaman langsung
        if ($book->stock === 1 && $hasActiveReservationByOther) {
            return back()->with('error', 'Stok buku tersisa 1 dan sudah dialokasikan untuk reservasi aktif oleh pengguna lain. Tidak dapat dipinjam langsung.');
        }

        // 3. Cek Blokir & Batas Maksimal Pinjam
        if ($user->is_blocked) {
            return back()->with('error', 'Peminjaman diblokir. Harap lunasi denda tertunggak Anda.');
        }

        $activeLoansCount = $user->loans()->whereIn('status', ['borrowed', 'extended'])->count();
        if ($activeLoansCount >= self::MAX_LOANS) {
            return back()->with('error', 'Anda telah mencapai batas maksimal peminjaman (' . self::MAX_LOANS . ' buku).');
        }

        // 4. Proses Peminjaman
        $loanToNotify = null;

        // Jika ada reservasi aktif milik user, ubah status reservasi menjadi 'borrowed'
        if ($activeReservation) {
            $activeReservation->update([
                'loan_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays($book->max_loan_days),
                'status' => 'borrowed',
            ]);
            // STOK TIDAK DI-DECREMENT karena diasumsikan sudah 0/dialokasikan saat reservasi diaktifkan.
            $loanToNotify = $activeReservation;

        } else {
            // Jika tidak ada reservasi aktif, buat pinjaman baru
            $loan = Loan::create([
                'user_id' => $user->id,
                'book_id' => $book->id,
                'loan_date' => Carbon::now(),
                'due_date' => Carbon::now()->addDays($book->max_loan_days),
                'status' => 'borrowed',
            ]);
            $loanToNotify = $loan;
        }

        $book->decrement('stock');

        // 5. Kirim Notifikasi Konfirmasi
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Peminjaman Berhasil',
            'message' => "Anda berhasil meminjam buku '{$book->title}'. Jatuh tempo pada: " . $loanToNotify->due_date->format('d F Y') . ".",
        ]);

        return redirect()->route('mahasiswa.dashboard')->with('status', 'Buku berhasil dipinjam! Cek detail di dashboard Anda.');
    }

    /**
     * Mahasiswa: Proses Perpanjangan Pinjaman.
     */
    public function renew(Loan $loan): RedirectResponse
    {
        if ($loan->user_id !== Auth::id() || $loan->status !== 'borrowed') {
            return back()->with('error', 'Peminjaman tidak valid atau sudah tidak aktif.');
        }

        if (Carbon::now()->greaterThan($loan->due_date)) {
            return back()->with('error', 'Peminjaman sudah jatuh tempo dan tidak bisa diperpanjang. Harap segera kembalikan.');
        }

        $newDueDate = Carbon::parse($loan->due_date)->addDays(7);
        $loan->update([
            'due_date' => $newDueDate,
            'status' => 'extended'
        ]);

        Notification::create([
            'user_id' => $loan->user_id,
            'title' => 'Perpanjangan Berhasil',
            'message' => "Peminjaman buku '{$loan->book->title}' berhasil diperpanjang. Jatuh tempo baru: " . $newDueDate->format('d F Y') . ".",
        ]);

        return back()->with('status', 'Masa peminjaman berhasil diperpanjang hingga ' . $newDueDate->format('d F Y'));
    }

    /**
     * Mahasiswa: Proses Reservasi Buku (BARU).
     */
    public function reserve(Book $book): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Cek stok (Reservasi hanya jika stok 0)
        if ($book->stock > 0) {
            return back()->with('error', 'Buku tersedia, silakan pinjam langsung.');
        }

        // 2. Cek apakah sudah pernah meminjam atau mereservasi
        $existingLoanOrReservation = $user->loans()->where('book_id', $book->id)
        ->whereIn('status', ['borrowed', 'extended', 'reserved', 'reserved_active']) // Menangkap semua status aktif/reserved
        ->exists();

        if ($existingLoanOrReservation) {
            // Pesan ini akan dikirimkan jika reservasi/pinjaman aktif sudah ada
            return back()->with('error', 'Anda sudah memiliki pinjaman atau reservasi aktif untuk buku ini.');
        }

        // 3. Buat Reservasi baru
        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'loan_date' => null, // Tgl pinjam dikosongkan
            'due_date' => null, // Jatuh tempo dikosongkan
            'status' => 'reserved',
        ]);

        Notification::create([
            'user_id' => $user->id,
            'title' => 'Reservasi Berhasil',
            'message' => "Reservasi buku '{$book->title}' berhasil dicatat. Anda akan diberitahu saat buku tersedia.",
        ]);

        return back()->with('status', 'Reservasi buku berhasil dicatat.');
    }

    /**
     * Mahasiswa: Batalkan Reservasi (BARU).
     */
    public function cancelReservation(Loan $loan): RedirectResponse
    {
        if ($loan->user_id !== Auth::id() || !in_array($loan->status, ['reserved', 'reserved_active'])) {
            return back()->with('error', 'Reservasi tidak valid atau Anda tidak memiliki izin.');
        }

        $loan->delete(); // Hapus entri reservasi

        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Reservasi Dibatalkan',
            'message' => "Reservasi buku '{$loan->book->title}' telah berhasil dibatalkan.",
        ]);

        return back()->with('status', 'Reservasi berhasil dibatalkan.');
    }

    // ===============================================
    // PEGAWAI ACTIONS (Updated)
    // ===============================================

    /**
     * Pegawai: Membatalkan/Menolak Reservasi (BARU).
     */
    public function processCancelReservation(Loan $loan): RedirectResponse
    {
        if (!in_array($loan->status, ['reserved', 'reserved_active'])) {
            return back()->with('error', 'Pinjaman ini bukan reservasi aktif atau menunggu.');
        }

        $bookTitle = $loan->book->title;
        $statusBefore = $loan->status;

        // Jika statusnya 'reserved_active', maka stok buku seharusnya 0.
        if ($statusBefore === 'reserved_active') {
            // Cek apakah ada reservasi lain yang menunggu untuk buku yang sama
            $hasOtherWaitingReservations = Loan::where('book_id', $loan->book_id)
                ->where('id', '!=', $loan->id)
                ->where('status', 'reserved')
                ->exists();

            // Jika tidak ada reservasi lain yang menunggu, naikkan stok buku.
            if (!$hasOtherWaitingReservations) {
                $loan->book->increment('stock');
            }
        }

        $loan->delete(); // Hapus entri reservasi

        Notification::create([
            'user_id' => $loan->user_id,
            'title' => 'Reservasi Dibatalkan/Ditolak',
            'message' => "Reservasi buku '{$bookTitle}' Anda telah dibatalkan oleh petugas perpustakaan. Status sebelumnya: " . ucfirst(str_replace('_', ' ', $statusBefore)) . ".",
        ]);

        return back()->with('status', 'Reservasi berhasil dibatalkan/dihapus.');
    }

    /**
     * Pegawai: Menampilkan daftar pinjaman aktif.
     */
    public function pendingLoans(): View
    {
        $loans = Loan::with(['user', 'book'])
                     ->whereIn('status', ['borrowed', 'extended'])
                     ->orderBy('due_date', 'asc')
                     ->paginate(15);

        foreach ($loans as $loan) {
            $dueDate = Carbon::parse($loan->due_date);
            $loan->is_late = Carbon::now()->greaterThan($dueDate);
            $loan->days_late = $loan->is_late ? Carbon::now()->diffInDays($dueDate) : 0;
            $loan->potential_fine = $loan->days_late * $loan->book->daily_fine_rate;
        }

        return view('pegawai.loans.pending', compact('loans'));
    }

    /**
     * Pegawai: Memproses Pengembalian Buku (Update untuk Cek Reservasi).
     */
    public function processReturn(Loan $loan): RedirectResponse
    {
        if (!in_array($loan->status, ['borrowed', 'extended'])) {
            return back()->with('error', 'Pinjaman ini sudah selesai atau tidak valid.');
        }

        // 1. Proses Pengembalian
        $loan->update([
            'return_date' => Carbon::now(),
            'status' => 'returned',
        ]);

        // 2. Hitung dan catat denda jika ada keterlambatan
        $fine = $this->calculateFine($loan);

        // 3. Kirim Notifikasi Pengembalian
        Notification::create([
            'user_id' => $loan->user_id,
            'title' => 'Pengembalian Diterima',
            'message' => "Pengembalian buku '{$loan->book->title}' telah dikonfirmasi. " . ($fine ? "Anda memiliki denda tertunggak sebesar Rp" . number_format($fine->amount, 0, ',', '.') . "." : ""),
        ]);

        $loan->book->increment('stock');
        return back()->with('status', 'Pengembalian buku berhasil diproses. Stok buku diperbarui.');
    }

    /**
     * Pegawai: Memproses Pembayaran Denda.
     */
    public function processFinePayment(Fine $fine): RedirectResponse
    {
        if ($fine->status === 'paid') {
            return back()->with('error', 'Denda ini sudah lunas.');
        }

        $fine->update([
            'paid_at' => Carbon::now(),
            'status' => 'paid',
        ]);

        // Cek denda tertunggak lainnya yang terkait dengan USER, bukan hanya loan ini
        $hasOutstandingFines = Fine::whereHas('loan', function($query) use ($fine) {
            $query->where('user_id', $fine->loan->user_id);
        })->where('status', 'outstanding')->exists();

        // Jika tidak ada denda tertunggak lagi untuk user ini, batalkan blokir
        if (!$hasOutstandingFines) {
            $fine->loan->user->update(['is_blocked' => false]);
        }

        Notification::create([
            'user_id' => $fine->loan->user_id,
            'title' => 'Pembayaran Denda Lunas',
            'message' => "Pembayaran denda sebesar Rp" . number_format($fine->amount, 0, ',', '.') . " telah lunas. Anda kini dapat meminjam kembali.",
        ]);

        return back()->with('status', 'Pembayaran denda berhasil dicatat. Status mahasiswa telah diperbarui.');
    }

    /**
     * Pegawai: Menampilkan Daftar Reservasi Menunggu (BARU).
     */
    public function pendingReservations(): View
    {
        $reservations = Loan::with(['user', 'book'])
            ->whereIn('status', ['reserved', 'reserved_active'])
            ->orderBy('created_at', 'asc')
            ->paginate(15);

        return view('pegawai.loans.reservations', compact('reservations'));
    }

    /**
     * Pegawai: Mengaktifkan Reservasi secara Manual (Jika diperlukan).
     */
    public function activateReservation(Loan $loan): RedirectResponse
    {
        if ($loan->status !== 'reserved') {
            return back()->with('error', 'Reservasi harus dalam status "reserved" untuk diaktifkan.');
        }

        // Cek Stok Buku
        if ($loan->book->stock <= 0) {
            return back()->with('error', 'Stok buku ini saat ini 0. Tidak dapat mengaktifkan reservasi secara manual.');
        }

        // Cek apakah buku sudah ada reservasi aktif lain
        $activeReservationExists = Loan::where('book_id', $loan->book_id)
            ->where('status', 'reserved_active')
            ->exists();

        if ($activeReservationExists) {
            return back()->with('error', 'Sudah ada reservasi aktif untuk buku ini. Batalkan reservasi aktif sebelumnya.');
        }

        // 1. Update status
        $loan->update(['status' => 'reserved_active']);

        //2 Stok dikurangi 1 dari stok yang tersedia. Karena stok > 0, stok akan berkurang.
        // Jika stok = 1, stok akan menjadi 0.
        // $loan->book->decrement('stock');

        // 3. Kirim notifikasi
        Notification::create([
            'user_id' => $loan->user_id,
            'title' => 'Buku Tersedia untuk Dipinjam!',
            'message' => "Buku yang Anda reservasi, '{$loan->book->title}', kini tersedia. Silakan pinjam dalam 24 jam.",
        ]);

        return back()->with('status', 'Reservasi berhasil diaktifkan dan notifikasi dikirim ke mahasiswa. Stok buku diatur menjadi 0.');
    }
}
