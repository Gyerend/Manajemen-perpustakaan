<x-guest-layout>
    <style>
        /* Gaya dasar untuk rating */
        .rating-star {
            color: #f59e0b; /* yellow-500 */
        }
        .book-cover-detail {
            width: 100%;
            height: 400px;
            background-color: #f3f4f6;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            text-align: center;
            padding: 20px;
            border-radius: 12px;
        }
        .review-card:hover {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl sm:rounded-2xl overflow-hidden border border-gray-100">
            <div class="lg:flex">

                {{-- Bagian Kiri: Cover dan Informasi Pinjaman --}}
                <div class="lg:w-1/3 p-8 bg-gray-50 border-r border-gray-100">

                    {{-- Cover Placeholder --}}
                    <div class="book-cover-detail mb-8 text-gray-800">
                        {{ $book->title }}
                    </div>

                    <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2">Status & Aksi</h3>

                    {{-- Detail Pinjaman Cepat --}}
                    <div class="space-y-3 mb-6">
                        <p class="text-sm flex justify-between">
                            <span class="font-semibold text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Stok Tersedia:
                            </span>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $book->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $book->stock }}
                            </span>
                        </p>
                        <p class="text-sm flex justify-between">
                            <span class="font-semibold text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Max Pinjam:
                            </span>
                            <span>{{ $book->max_loan_days }} hari</span>
                        </p>
                        <p class="text-sm flex justify-between">
                            <span class="font-semibold text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                Denda/Hari:
                            </span>
                            <span class="font-semibold">Rp{{ number_format($book->daily_fine_rate, 0, ',', '.') }}</span>
                        </p>
                    </div>

                    @auth
                        @if (Auth::user()->isMahasiswa())
                            @php
                                $hasReservation = Auth::user()->loans()->where('book_id', $book->id)
                                    ->whereIn('status', ['reserved', 'reserved_active'])
                                    ->exists();
                                $isReservedActive = Auth::user()->loans()->where('book_id', $book->id)
                                    ->where('status', 'reserved_active')
                                    ->exists();
                            @endphp

                            @if ($book->stock > 0 && !$isBorrowed)
                                <form action="{{ route('loan.borrow', $book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition duration-150">
                                        Pinjam Buku Sekarang
                                    </button>
                                </form>
                            @elseif ($isBorrowed)
                                <button disabled class="block w-full text-center bg-gray-400 text-white font-bold py-3 px-4 rounded-xl">
                                    Anda Sedang Meminjam Buku Ini
                                </button>
                            @elseif ($book->stock == 0 && !$hasReservation)
                                <form action="{{ route('reservation.reserve', $book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition duration-150">
                                        Reservasi Buku (Stok Habis)
                                    </button>
                                </form>
                            @elseif ($isReservedActive)
                                <button disabled class="block w-full text-center bg-green-600 text-white font-bold py-3 px-4 rounded-xl">
                                    Reservasi Aktif! (Silakan Ambil)
                                </button>
                            @elseif ($hasReservation)
                                <button disabled class="block w-full text-center bg-gray-500 text-white font-bold py-3 px-4 rounded-xl">
                                    Menunggu Giliran Reservasi
                                </button>
                            @endif
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition duration-150">
                            Login untuk Pinjam/Reservasi
                        </a>
                    @endauth
                </div>

                {{-- Bagian Kanan: Detail dan Ulasan --}}
                <div class="lg:w-2/3 p-8">

                    {{-- Header Detail --}}
                    <span class="text-sm font-semibold text-indigo-600 uppercase tracking-wider">{{ $book->category }}</span>
                    <h1 class="text-4xl font-extrabold text-gray-900 mb-1 mt-1">{{ $book->title }}</h1>
                    <p class="text-xl text-gray-600 mb-4">Oleh <span class="font-semibold">{{ $book->author }}</span> (Penerbit: {{ $book->publisher }}, {{ $book->publication_year }})</p>

                    {{-- Rating --}}
                    <div class="flex items-center space-x-4 mb-8 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <span class="text-4xl font-bold text-yellow-600">{{ number_format($averageRating ?? 0, 1) }}</span>
                        <div class="flex flex-col">
                             <div class="flex">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg class="w-6 h-6 mr-0.5 rating-star {{ $i > floor($averageRating ?? 0) ? 'text-gray-300' : '' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.637-.921 1.936 0l1.545 4.757a1 1 0 00.95.69h5.002c.969 0 1.371 1.24.588 1.81l-4.043 2.956a1 1 0 00-.364 1.118l1.545 4.757c.3.921-.755 1.688-1.545 1.118l-4.043-2.956a1 1 0 00-1.176 0l-4.043 2.956c-.789.57-1.844-.197-1.545-1.118l1.545-4.757a1 1 0 00-.364-1.118L2.091 10.183c-.783-.57-.381-1.81.588-1.81h5.002a1 1 0 00.95-.69l1.545-4.757z"></path></svg>
                                @endfor
                            </div>
                            <span class="text-sm text-gray-700">Berdasarkan {{ $reviews->count() }} ulasan</span>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-8 border-b pb-6">
                        <h3 class="text-2xl font-semibold mb-3 text-gray-800">Deskripsi Buku</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $book->description ?? 'Tidak ada deskripsi tersedia.' }}</p>
                    </div>

                    <h3 class="text-2xl font-semibold mb-6 text-gray-800">Ulasan Pembaca</h3>

                    {{-- Form Review --}}
                    @if (isset($canReview) && $canReview)
                        <div class="bg-indigo-50 p-5 rounded-xl mb-8 border border-indigo-200">
                            <h4 class="font-bold text-lg text-indigo-800 mb-3">Tinggalkan Ulasan Anda</h4>
                            @if (session('error_review'))
                                <p class="text-sm text-red-600 mb-3">{{ session('error_review') }}</p>
                            @endif

                            <form action="{{ route('reviews.store', $book) }}" method="POST">
                                @csrf

                                <div class="mb-3">
                                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">Pilih Rating</label>
                                    <select id="rating" name="rating" required class="border-gray-300 rounded-lg text-sm w-full shadow-sm focus:border-indigo-500">
                                        <option value="">Pilih Rating</option>
                                        <option value="5">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                        <option value="4">⭐⭐⭐⭐ Baik</option>
                                        <option value="3">⭐⭐⭐ Sedang</option>
                                        <option value="2">⭐⭐ Buruk</option>
                                        <option value="1">⭐ Sangat Buruk</option>
                                    </select>
                                    @error('rating')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div class="mb-4">
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
                                    <textarea id="comment" name="comment" rows="3" placeholder="Tulis ulasan Anda..." required class="w-full border-gray-300 rounded-lg text-sm shadow-sm focus:border-indigo-500"></textarea>
                                    @error('comment')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <button type="submit" class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition duration-150">Kirim Ulasan</button>
                            </form>
                        </div>
                    @endif

                    {{-- Daftar Ulasan --}}
                    <div class="space-y-6">
                        @forelse ($reviews as $review)
                            <div class="p-4 border border-gray-200 rounded-xl review-card bg-white">
                                <div class="flex justify-between items-start mb-2">
                                    <p class="font-bold text-lg text-gray-900">{{ $review->user->name }}</p>
                                    <span class="text-xs text-gray-500">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                                <p class="text-yellow-500 mb-2 text-xl">
                                    @for ($i = 0; $i < $review->rating; $i++)
                                        <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.637-.921 1.936 0l1.545 4.757a1 1 0 00.95.69h5.002c.969 0 1.371 1.24.588 1.81l-4.043 2.956a1 1 0 00-.364 1.118l1.545 4.757c.3.921-.755 1.688-1.545 1.118l-4.043-2.956a1 1 0 00-1.176 0l-4.043 2.956c-.789.57-1.844-.197-1.545-1.118l1.545-4.757a1 1 0 00-.364-1.118L2.091 10.183c-.783-.57-.381-1.81.588-1.81h5.002a1 1 0 00.95-.69l1.545-4.757z"></path></svg>
                                    @endfor
                                </p>
                                <p class="text-gray-700">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">Belum ada ulasan untuk buku ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
