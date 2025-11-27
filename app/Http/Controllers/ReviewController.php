<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Menyimpan ulasan baru untuk buku.
     */
    public function store(Request $request, Book $book): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Validasi Input
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:500'],
        ]);

        // 2. Cek Otorisasi (Apakah Mahasiswa pernah pinjam dan sudah dikembalikan?)
        $hasReturned = $user->loans()->where('book_id', $book->id)
            ->where('status', 'returned')
            ->exists();

        // Cek apakah sudah pernah review
        $hasReviewed = $user->reviews()->where('book_id', $book->id)->exists();

        if (!$hasReturned || $hasReviewed) {
            return back()->with('error', 'Anda hanya dapat memberikan ulasan untuk buku yang telah dipinjam, dikembalikan, dan belum pernah diulas.');
        }

        // 3. Simpan Ulasan
        Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('status', 'Terima kasih! Ulasan Anda berhasil ditambahkan.');
    }
}
