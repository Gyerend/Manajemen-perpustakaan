<x-guest-layout>
    <style>
        /* Gaya dasar untuk rating */
        .rating-star {
            color: #f59e0b; /* yellow-500 */
        }
        /* Cover Placeholder lebih ramping dan profesional */
        .book-cover-detail {
            width: 100%;
            height: 350px; /* Sedikit lebih pendek */
            background-color: #f3f4f6;
            border: 2px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            text-align: center;
            padding: 20px;
            border-radius: 12px;
            color: #4b5563; /* text-gray-600 */
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.25rem;
        }
        /* Efek hover yang lebih jelas untuk kartu ulasan */
        .review-card {
            transition: box-shadow 0.3s, transform 0.3s;
        }
        .review-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
    </style>

    <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <div class="bg-white shadow-2xl sm:rounded-2xl overflow-hidden border border-gray-100">
            <div class="lg:flex">

                {{-- Bagian Kiri: Cover dan Informasi Pinjaman (Status & Aksi) --}}
                <div class="lg:w-1/3 p-8 bg-gray-50 border-r border-gray-100 flex flex-col">

                    {{-- Cover Image --}}
                    <div class="mb-8">
                        @if($book->image)
                            <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}" class="w-full h-96 object-cover rounded-xl border border-gray-300">
                        @else
                            <div class="book-cover-detail">
                                {{ $book->title }}
                            </div>
                        @endif
                    </div>

                    <h3 class="text-xl font-bold mb-4 text-gray-800 border-b pb-2 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        Status & Aksi
                    </h3>

                    {{-- Detail Pinjaman Cepat --}}
                    <div class="space-y-4 mb-8">
                        <p class="text-base flex justify-between items-center bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                            <span class="font-semibold text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Stok Tersedia:
                            </span>
                            <span class="px-3 py-1 rounded-full text-base font-bold {{ $book->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $book->stock }}
                            </span>
                        </p>
                        <p class="text-base flex justify-between items-center bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                            <span class="font-semibold text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Max Pinjam:
                            </span>
                            <span class="font-semibold text-gray-800">{{ $book->max_loan_days }} hari</span>
                        </p>
                        <p class="text-base flex justify-between items-center bg-white p-3 rounded-lg shadow-sm border border-gray-200">
                            <span class="font-semibold text-gray-600 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>
                                Denda/Hari:
                            </span>
                            <span class="font-bold text-red-600">Rp{{ number_format($book->daily_fine_rate, 0, ',', '.') }}</span>
                        </p>
                    </div>

                    {{-- Tombol Aksi --}}
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
                                    <button type="submit" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition duration-150 transform hover:scale-[1.01]">
                                        <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.405 9.493 5 8 5c-4.478 0-8 2.8-8 8s3.522 8 8 8c1.493 0 2.832-.405 4-1.008M12 6.253c1.168-.848 2.507-1.253 4-1.253 4.478 0 8 2.8 8 8s-3.522 8-8 8c-1.493 0-2.832-.405-4-1.008"></path></svg>
                                        Pinjam Buku Sekarang
                                    </button>
                                </form>
                            @elseif ($isBorrowed)
                                <button disabled class="block w-full text-center bg-gray-400 text-white font-bold py-3 px-4 rounded-xl shadow-inner">
                                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.007 12.007 0 002.944 12c0 3.072 1.282 5.864 3.391 7.798M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    Anda Sedang Meminjam Buku Ini
                                </button>
                            @elseif ($book->stock == 0 && !$hasReservation)
                                <form action="{{ route('reservation.reserve', $book) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="block w-full text-center bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition duration-150 transform hover:scale-[1.01]">
                                        <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2zm12 0h2m-2-4h2m-2-4h2m-2-4h2"></path></svg>
                                        Reservasi Buku (Stok Habis)
                                    </button>
                                </form>
                            @elseif ($isReservedActive)
                                <button disabled class="block w-full text-center bg-green-600 text-white font-bold py-3 px-4 rounded-xl shadow-lg">
                                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Reservasi Aktif! (Silakan Ambil)
                                </button>
                            @elseif ($hasReservation)
                                <button disabled class="block w-full text-center bg-gray-500 text-white font-bold py-3 px-4 rounded-xl shadow-lg">
                                    <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Menunggu Giliran Reservasi
                                </button>
                            @endif
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg transition duration-150 transform hover:scale-[1.01]">
                            <svg class="w-6 h-6 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3v-1m18-6v-1a3 3 0 00-3-3h-4"></path></svg>
                            Login untuk Pinjam/Reservasi
                        </a>
                    @endauth
                </div>

                {{-- Bagian Kanan: Detail dan Ulasan --}}
                <div class="lg:w-2/3 p-8">

                    {{-- Header Detail --}}
                    <span class="text-sm font-semibold text-indigo-600 uppercase tracking-widest bg-indigo-100 px-3 py-1 rounded-full">{{ $book->category }}</span>
                    <h1 class="text-4xl lg:text-5xl font-extrabold text-gray-900 mb-1 mt-3">{{ $book->title }}</h1>
                    <p class="text-lg lg:text-xl text-gray-600 mb-4">Oleh <span class="font-bold text-gray-800">{{ $book->author }}</span> (Penerbit: {{ $book->publisher }}, {{ $book->publication_year }})</p>

                    <div class="mb-10 border-b pb-6">
                        {{-- Rating Section --}}
                        <div class="flex items-center space-x-4 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                            {{-- Hitung rating rata-rata untuk tampilan yang lebih akurat --}}
                            @php
                                $displayRating = number_format($averageRating ?? 0, 1);
                                $fullStars = floor($averageRating ?? 0);
                                $totalReviews = $reviews->count() ?? 0;
                            @endphp
                            <span class="text-5xl font-extrabold text-yellow-600">{{ $displayRating }}</span>
                            <div class="flex flex-col">
                                <div class="flex">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-7 h-7 mr-0.5 rating-star {{ $i > $fullStars ? 'text-gray-300' : '' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.637-.921 1.936 0l1.545 4.757a1 1 0 00.95.69h5.002c.969 0 1.371 1.24.588 1.81l-4.043 2.956a1 1 0 00-.364 1.118l1.545 4.757c.3.921-.755 1.688-1.545 1.118l-4.043-2.956a1 1 0 00-1.176 0l-4.043 2.956c-.789.57-1.844-.197-1.545-1.118l1.545-4.757a1 1 0 00-.364-1.118L2.091 10.183c-.783-.57-.381-1.81.588-1.81h5.002a1 1 0 00.95-.69l1.545-4.757z"></path></svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-700 font-medium mt-1">Berdasarkan {{ $totalReviews }} ulasan</span>
                            </div>
                        </div>

                        {{-- Deskripsi --}}
                        <h3 class="text-2xl font-bold mt-8 mb-3 text-gray-800 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Deskripsi Buku
                        </h3>
                        <p class="text-gray-700 leading-relaxed text-base">{{ $book->description ?? 'Tidak ada deskripsi tersedia.' }}</p>
                    </div>

                    <h3 class="text-2xl font-bold mb-6 text-gray-800 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.637-.921 1.936 0l3.05 9.4c.1.3.1.6 0 .9l-3.05 9.4c-.3.921-1.637.921-1.936 0l-3.05-9.4c-.1-.3-.1-.6 0-.9l3.05-9.4z"></path></svg>
                        Ulasan Pembaca
                    </h3>

                    {{-- Form Review --}}
                    @if (isset($canReview) && $canReview)
                        <div class="bg-indigo-50 p-6 rounded-xl mb-10 border border-indigo-200 shadow-md">
                            <h4 class="font-bold text-xl text-indigo-800 mb-4 border-b pb-2">Tinggalkan Ulasan Anda</h4>
                            @if (session('error_review'))
                                <p class="text-sm text-red-600 bg-red-100 p-3 rounded-lg mb-3">{{ session('error_review') }}</p>
                            @endif

                            <form action="{{ route('reviews.store', $book) }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label for="rating" class="block text-sm font-medium text-gray-700 mb-1">Pilih Rating</label>
                                    <select id="rating" name="rating" required class="border-gray-300 rounded-lg text-base w-full shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">Pilih Rating</option>
                                        <option value="5">⭐⭐⭐⭐⭐ Sangat Baik</option>
                                        <option value="4">⭐⭐⭐⭐ Baik</option>
                                        <option value="3">⭐⭐⭐ Sedang</option>
                                        <option value="2">⭐⭐ Buruk</option>
                                        <option value="1">⭐ Sangat Buruk</option>
                                    </select>
                                    @error('rating')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <div class="mb-5">
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
                                    <textarea id="comment" name="comment" rows="3" placeholder="Tulis ulasan Anda..." required class="w-full border-gray-300 rounded-lg text-base shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('comment') }}</textarea>
                                    @error('comment')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                                </div>

                                <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg text-base font-semibold hover:bg-indigo-700 transition duration-150 shadow-md">Kirim Ulasan</button>
                            </form>
                        </div>
                    @endif

                    {{-- Daftar Ulasan --}}
                    <div class="space-y-6">
                        @forelse ($reviews as $review)
                            <div class="p-5 border border-gray-200 rounded-xl review-card bg-white">
                                <div class="flex justify-between items-center mb-2">
                                    <p class="font-extrabold text-lg text-gray-900 flex items-center">
                                        <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 12a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v14z"></path></svg>
                                        {{ $review->user->name }}
                                    </p>
                                    <span class="text-sm text-gray-500">{{ $review->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="text-yellow-500 mb-2 text-xl">
                                    @for ($i = 0; $i < $review->rating; $i++)
                                        <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.637-.921 1.936 0l1.545 4.757a1 1 0 00.95.69h5.002c.969 0 1.371 1.24.588 1.81l-4.043 2.956a1 1 0 00-.364 1.118l1.545 4.757c.3.921-.755 1.688-1.545 1.118l-4.043-2.956a1 1 0 00-1.176 0l-4.043 2.956c-.789.57-1.844-.197-1.545-1.118l1.545-4.757a1 1 0 00-.364-1.118L2.091 10.183c-.783-.57-.381-1.81.588-1.81h5.002a1 1 0 00.95-.69l1.545-4.757z"></path></svg>
                                    @endfor
                                </div>
                                <p class="text-gray-700 leading-relaxed text-base">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <div class="text-center py-8 bg-gray-50 border border-gray-200 rounded-xl">
                                <p class="text-gray-500 text-lg">Belum ada ulasan untuk buku ini. Jadilah yang pertama!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
