<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticController extends Controller
{
    public function index(): View
    {
        // 1. Statistik Dasar
        $totalBooks = Book::count();
        $totalUsers = User::where('role', 'mahasiswa')->count();
        $activeLoansCount = Loan::whereIn('status', ['borrowed', 'extended'])->count();
        $outstandingFines = \App\Models\Fine::where('status', 'outstanding')->sum('amount');

        // --- Perhitungan Batas Waktu 6 Bulan (SQLite FIX) ---
        $sixMonthsAgo = Carbon::now()->subMonths(6)->format('Y-m-d H:i:s');

        // 2. Grafik: Tren Peminjaman (per bulan dalam 6 bulan terakhir)
        $loanTrends = Loan::select(
                DB::raw('COUNT(id) as count'),
                // FIX SQLITE: Menggunakan strftime('%Y-%m', column)
                DB::raw("strftime('%Y-%m', loan_date) as month_year")
            )
            // FIX SQLITE: Membandingkan dengan string tanggal yang diformat
            ->where('loan_date', '>=', $sixMonthsAgo)
            ->groupBy('month_year')
            ->orderBy('month_year')
            ->get();

        // 3. Buku Paling Populer (Top 5)
        $topBooks = Book::select('books.title', DB::raw('COUNT(loans.id) as total_loans'))
            ->join('loans', 'books.id', '=', 'loans.book_id')
            ->groupBy('books.title')
            ->orderByDesc('total_loans')
            ->take(5)
            ->get();

        // 4. Ketersediaan Stok (Buku dengan stok < 5)
        $lowStockBooks = Book::where('stock', '<', 5)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        return view('admin.analytics.index', compact(
            'totalBooks',
            'totalUsers',
            'activeLoansCount',
            'lowStockBooks',
            'outstandingFines',
            'loanTrends',
            'topBooks'
        ));
    }
}
