<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CatalogController extends Controller
{
    /**
     * Menampilkan Homepage (Discover) dengan Rekomendasi Buku Populer.
     */
    public function home(Request $request): View
    {
        // Mendapatkan 4 buku dengan rating tertinggi sebagai rekomendasi populer
        $recommendedBooks = Book::withAvg('reviews', 'rating')
            ->orderByDesc('reviews_avg_rating')
            ->take(4)
            ->get();

        // Mendapatkan daftar kategori unik
        $categories = Book::select('category')
            ->distinct()
            ->limit(10)
            ->pluck('category');

        // Mendapatkan buku terbaru (misal 4 terbaru)
        $latestBooks = Book::orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // FIX: Kirimkan objek $request ke view
        return view('welcome', compact('recommendedBooks', 'categories', 'latestBooks', 'request'));
    }

    /**
     * Menampilkan seluruh Katalog Buku dengan fitur filter dan search.
     */
    public function index(Request $request): View
    {
        $query = Book::query();

        // Fitur Pencarian (Judul atau Penulis)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%");
            });
        }

        // Fitur Filter (Kategori)
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->input('category'));
        }

        // Filter dan Order
        $order = $request->input('order', 'latest');

        // Join dengan reviews untuk sorting berdasarkan rating
        $query->leftJoin('reviews', 'books.id', '=', 'reviews.book_id')
              ->select('books.*', DB::raw('AVG(reviews.rating) as avg_rating'))
              ->groupBy('books.id');

        if ($order === 'rating_desc') {
            $query->orderByDesc('avg_rating');
        } elseif ($order === 'rating_asc') {
            $query->orderBy('avg_rating');
        } elseif ($order === 'oldest') {
            $query->orderBy('publication_year', 'asc');
        } else { // 'latest'
            $query->orderBy('publication_year', 'desc');
        }

        $books = $query->paginate(12)->withQueryString();

        // Ambil semua kategori untuk filter
        $categories = Book::select('category')->distinct()->pluck('category');

        return view('catalog.index', compact('books', 'categories', 'request'));
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(Book $book) // Hapus :View return type hint di sini sementara
    {
        // Load ulasan terkait buku
        $reviews = $book->reviews()->with('user')->orderBy('created_at', 'desc')->get();

        // Hitung rating rata-rata
        $averageRating = $book->reviews()->avg('rating');

        // Status peminjaman pengguna (untuk Mahasiswa)
        $isBorrowed = false;
        $canReview = false;

        $hasActiveReservation = \App\Models\Loan::where('book_id', $book->id)
            ->where('status', 'reserved_active')
            ->exists();

        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            if ($user->isMahasiswa()) {

                // Cek apakah sedang dipinjam
                $isBorrowed = $user->loans()->where('book_id', $book->id)
                    ->whereIn('status', ['borrowed', 'extended'])
                    ->exists();

                // Cek apakah pernah dipinjam dan dikembalikan (untuk Review)
                $hasReturned = $user->loans()->where('book_id', $book->id)
                    ->where('status', 'returned')
                    ->exists();

                // Cek apakah sudah pernah review
                $hasReviewed = $user->reviews()->where('book_id', $book->id)->exists();

                // Mahasiswa bisa review jika pernah pinjam dan sudah dikembalikan DAN belum review
                $canReview = $hasReturned && !$hasReviewed;
            }
        }

        // PERBAIKAN: Menggunakan response() untuk menambahkan header anti-caching
        return response()->view('catalog.show', compact('book', 'reviews', 'averageRating', 'isBorrowed', 'canReview', 'hasActiveReservation'))
          ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
          ->header('Pragma', 'no-cache');
    }
}
