<x-guest-layout>
    <style>
        /* CSS Ditingkatkan untuk tata letak Grid yang responsif */
        .book-card-container {
            /* Grid yang responsif, minimal 200px hingga 1fr */
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 24px; /* gap-6 */
        }
        .book-card {
            border-radius: 0.5rem; /* rounded-lg */
            transition: transform 0.2s, box-shadow 0.3s;
            overflow: hidden;
            height: 100%; /* Memastikan semua kartu memiliki tinggi yang sama dalam grid row */
        }
        .book-card:hover {
            transform: translateY(-4px); /* Efek melayang */
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }
        .book-cover {
            height: 220px; /* Tinggi cover yang proporsional untuk tampilan grid */
        }
    </style>

    <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">
            📚 Katalog Lengkap Koleksi Buku
        </h2>

        {{-- PERBAIKAN UI FORM FILTER (Lebih Ringkas) --}}
        <div class="bg-white p-6 shadow-xl rounded-xl mb-10 border border-gray-100">
            <form action="{{ route('catalog.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:gap-4 items-end">

                {{-- Kolom Pencarian --}}
                <div class="flex-grow">
                    <label for="search" class="sr-only">Cari Judul atau Penulis...</label>
                    <input id="search" type="text" name="search" placeholder="Cari Judul atau Penulis..."
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ $request->search }}">
                </div>

                {{-- Kolom Kategori --}}
                <div class="w-full md:w-1/4">
                    <label for="category" class="sr-only">Kategori</label>
                    <select id="category" name="category" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" {{ $request->category == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Kolom Urutan --}}
                <div class="w-full md:w-1/4">
                    <label for="order" class="sr-only">Urutkan Berdasarkan</label>
                    <select id="order" name="order" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="latest" {{ $request->order == 'latest' ? 'selected' : '' }}>Tahun Terbit Terbaru</option>
                        <option value="oldest" {{ $request->order == 'oldest' ? 'selected' : '' }}>Tahun Terbit Terdahulu</option>
                        <option value="rating_desc" {{ $request->order == 'rating_desc' ? 'selected' : '' }}>Rating Tertinggi</option>
                        <option value="rating_asc" {{ $request->order == 'rating_asc' ? 'selected' : '' }}>Rating Terendah</option>
                    </select>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex space-x-2 w-full md:w-auto">
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg w-full md:w-auto">
                        Filter
                    </button>
                    <a href="{{ route('catalog.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg w-full md:w-auto text-center">
                        Reset
                    </a>
                </div>
            </form>
        </div>
        {{-- END PERBAIKAN FORM FILTER --}}

        {{-- TAMPILAN KARTU BUKU (UI/UX Ditingkatkan) --}}
        <div class="book-card-container">
            @forelse ($books as $book)
                <div class="book-card bg-white shadow-lg border border-gray-200">
                    <a href="{{ route('catalog.show', $book) }}" class="block h-full">

                        {{-- GAMBAR SAMPUL --}}
                        <div class="book-cover bg-gray-200 border-b border-gray-300 flex items-center justify-center overflow-hidden">
                            @if ($book->cover_image)
                                <img src="{{ asset('storage/books/' . $book->cover_image) }}" alt="{{ $book->title }}" class="h-16 w-12 object-cover rounded shadow">
                            @else
                                <div class="p-3 text-center text-gray-500 font-medium text-sm">
                                    Sampul Tidak Tersedia
                                </div>
                            @endif
                        </div>

                        {{-- DETAIL BUKU --}}
                        <div class="p-3 flex flex-col justify-between h-[calc(100%-220px)]">
                            <div>
                                <h4 class="text-base font-semibold text-gray-900 leading-tight">{{ $book->title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $book->author }}</p>
                            </div>

                            <div class="mt-2">
                                @php
                                    // Ambil rating rata-rata dari atribut avg_rating yang di-join di controller
                                    $rating = $book->avg_rating;
                                @endphp
                                <p class="text-sm font-bold text-yellow-500 mt-1 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.637-.921 1.936 0l1.545 4.757a1 1 0 00.95.69h5.002c.969 0 1.371 1.24.588 1.81l-4.043 2.956a1 1 0 00-.364 1.118l1.545 4.757c.3.921-.755 1.688-1.545 1.118l-4.043-2.956a1 1 0 00-1.176 0l-4.043 2.956c-.789.57-1.844-.197-1.545-1.118l1.545-4.757a1 1 0 00-.364-1.118L2.091 10.183c-.783-.57-.381-1.81.588-1.81h5.002a1 1 0 00.95-.69l1.545-4.757z"></path></svg>
                                    {{ number_format($rating ?? 0, 1) }}/5.0
                                </p>
                                <span class="text-xs px-2 py-0.5 mt-1 inline-block rounded-full
                                    {{ $book->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}
                                    font-medium">
                                    Stok: {{ $book->stock }}
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <p class="col-span-full text-center py-10 text-gray-500 bg-white rounded-lg shadow-md">
                    😔 Tidak ada buku yang cocok dengan kriteria pencarian atau filter Anda.
                </p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $books->links() }}
        </div>
    </div>
</x-guest-layout>
