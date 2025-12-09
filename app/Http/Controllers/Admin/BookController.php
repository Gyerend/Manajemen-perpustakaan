<?php

namespace App\Http\Controllers\Admin; // PASTIKAN NAMESPACE INI BENAR

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Aturan validasi buku.
     */
    protected function validationRules(): array
    {
        // ... (aturan validasi)
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'stock' => ['required', 'integer', 'min:0'],
            'max_loan_days' => ['required', 'integer', 'min:1'],
            'daily_fine_rate' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // max 2MB
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

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('books', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        Book::create($data);
        return redirect()->route('books.index')->with('status', 'Buku baru berhasil ditambahkan!');
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

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($book->image) {
                Storage::disk('public')->delete($book->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('books', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $book->update($data);
        return redirect()->route('books.index')->with('status', 'Data buku berhasil diperbarui!');
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(Book $book): View
    {
        return view('books.show', compact('book'));
    }

    /**
     * Menghapus buku. (DELETE)
     */
    public function destroy(Book $book): RedirectResponse
    {
        try {
            $activeLoans = $book->loans()->whereIn('status', ['borrowed', 'extended', 'reserved', 'reserved_active'])->count();

            if ($activeLoans > 0) {
                return redirect()->route('books.index')->with('error', 'Gagal menghapus! Buku masih memiliki '.$activeLoans.' pinjaman/reservasi aktif. Harus diselesaikan terlebih dahulu.');
            }

            $book->delete();

            return redirect()->route('books.index')->with('status', 'Buku berhasil dihapus dari koleksi.');
        } catch (\Exception $e) {
            return redirect()->route('books.index')->with('error', 'Gagal menghapus! Terjadi kesalahan database ('.$e->getMessage().'). Coba hapus loan/review terkait secara manual.');
        }
    }
}
