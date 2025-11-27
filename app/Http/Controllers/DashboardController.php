<?php

namespace App\Http\Controllers; // <-- PASTIKAN NAMESPACE INI

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Loan;
use App\Models\Book;
use Illuminate\Support\Facades\DB;
use App\Models\Fine; // Tambahkan import Fine

class DashboardController extends Controller
{
    private const MAX_RECOMMENDATIONS = 4; // Batas rekomendasi

    /**
     * Helper: Mendapatkan rekomendasi buku berdasarkan riwayat Mahasiswa.
     */
    private function getRecommendedBooks(\App\Models\User $user)
    {
        // 1. Cari kategori yang paling sering dipinjam oleh Mahasiswa ini
        $topCategories = $user->loans()
            ->join('books', 'loans.book_id', '=', 'books.id')
            ->select('books.category', DB::raw('count(*) as total'))
            ->groupBy('books.category')
            ->orderByDesc('total')
            ->pluck('category')
            ->take(3);

        if ($topCategories->isEmpty()) {
            // Jika tidak ada riwayat, fallback ke rekomendasi populer (global)
            return Book::withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating')
                ->inRandomOrder()
                ->limit(self::MAX_RECOMMENDATIONS)
                ->get();
        }

        // 2. Cari buku dari kategori teratas yang BELUM dipinjam oleh Mahasiswa ini
        $recommendedBooks = Book::whereIn('category', $topCategories)
            ->whereDoesntHave('loans', function ($query) use ($user) {
                // Kecualikan buku yang sedang aktif dipinjam atau sudah pernah dipinjam
                $query->where('user_id', $user->id);
            })
            ->inRandomOrder()
            ->limit(self::MAX_RECOMMENDATIONS)
            ->get();

        return $recommendedBooks;
    }

    /**
     * Menampilkan Dashboard Mahasiswa.
     */
    public function showMahasiswaDashboard(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Ambil Pinjaman Aktif (Borrowed, Extended, Reserved)
        $activeLoans = $user->loans()
            ->with('book', 'fines')
            ->whereIn('status', ['borrowed', 'extended', 'reserved', 'reserved_active']) // FIX: Tambahkan reservasi
            ->orderBy('due_date', 'asc')
            ->get();

        // 2. Cek Status Denda Tertunggak
        $outstandingFines = $user->loans()
            ->with('fines', 'book')
            ->whereHas('fines', function($query) {
                $query->where('status', 'outstanding');
            })
            ->get();

        $totalFineAmount = $outstandingFines->sum(function($loan) {
            return $loan->fines->where('status', 'outstanding')->sum('amount');
        });

        // 3. Ambil Riwayat Pinjaman (Returned)
        $historyLoans = $user->loans()
            ->with('book')
            ->where('status', 'returned')
            ->orderBy('return_date', 'desc')
            ->take(5)
            ->get();

        // 4. Ambil Notifikasi
        $notifications = $user->notifications()
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // 5. Rekomendasi Buku
        $recommendedBooks = $this->getRecommendedBooks($user);

        // Hitung status keterlambatan untuk pinjaman aktif
        foreach ($activeLoans as $loan) {
            // Hanya hitung denda/terlambat untuk pinjaman yang *sudah* aktif (bukan reservasi)
            if (in_array($loan->status, ['borrowed', 'extended'])) {
                $dueDate = Carbon::parse($loan->due_date);
                $loan->is_late = Carbon::now()->greaterThan($dueDate);
                $loan->days_late = $loan->is_late ? Carbon::now()->diffInDays($dueDate) : 0;
                $loan->has_fine = $loan->fines->where('status', 'outstanding')->isNotEmpty();
            } else {
                $loan->is_late = false;
                $loan->days_late = 0;
                $loan->has_fine = false;
            }
        }

        return view('mahasiswa.dashboard', compact(
            'activeLoans',
            'outstandingFines',
            'totalFineAmount',
            'historyLoans',
            'notifications',
            'recommendedBooks',
            'user'
        ));
    }
}
