<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Aturan validasi buku.
     */
    protected function validationRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            // Validasi tahun agar tidak lebih dari tahun depan
            'publication_year' => ['required', 'digits:4', 'integer', 'max:' . (date('Y') + 1)],
            'category' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'max_loan_days' => ['required', 'integer', 'min:1'],
            'daily_fine_rate' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Menampilkan daftar semua buku. (READ)
     */
    public function index(): View
    {
        $books = Book::orderBy('title', 'asc')->paginate(10);
        return view('books.index', compact('books'));
    }

    /**
     * Menampilkan formulir untuk membuat buku baru. (CREATE - Form)
     */
    public function create(): View
    {
        return view('books.create');
    }

    /**
     * Menyimpan buku baru ke database. (CREATE - Store)
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->validationRules());
        Book::create($request->all());
        return redirect()->route('books.collection.index')->with('status', 'Buku baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan formulir edit buku. (UPDATE - Form)
     */
    public function edit(Book $book): View
    {
        return view('books.edit', compact('book'));
    }

    /**
     * Memperbarui data buku. (UPDATE - Store)
     */
    public function update(Request $request, Book $book): RedirectResponse
    {
        $request->validate($this->validationRules());
        $book->update($request->all());
        return redirect()->route('books.collection.index')->with('status', 'Data buku berhasil diperbarui!');
    }

    /**
     * Menghapus buku. (DELETE)
     */
    public function destroy(Book $book): RedirectResponse
    {
        // Catatan: Dalam proyek nyata, ini harus menghapus loans/reviews terkait
        $book->delete();
        return redirect()->route('books.collection.index')->with('status', 'Buku berhasil dihapus dari koleksi.');
    }
}
