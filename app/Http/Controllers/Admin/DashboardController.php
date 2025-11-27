<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Statistik Dasar
        $totalBooks = Book::count();
        $totalUsers = User::whereIn('role', ['mahasiswa', 'pegawai'])->count();
        $activeLoansCount = Loan::whereIn('status', ['borrowed', 'extended'])->count();
        $outstandingFines = \App\Models\Fine::where('status', 'outstanding')->sum('amount');

        // Buku Stok Rendah
        $lowStockBooks = Book::where('stock', '<', 5)->count();

        // PENTING: Semua variabel ini harus dikirim ke view
        return view('admin.dashboard', compact(
            'totalBooks',
            'totalUsers',
            'activeLoansCount',
            'lowStockBooks',
            'outstandingFines'
        ));
    }
}
