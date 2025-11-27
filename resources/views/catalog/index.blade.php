<x-guest-layout>
    <div class="max-w-7xl mx-auto py-12 sm:px-6 lg:px-8">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">
            Katalog Lengkap Koleksi Buku
        </h2>

        <div class="bg-white p-6 shadow-md rounded-lg mb-8">
            <form action="{{ route('catalog.index') }}" method="GET" class="space-y-4">

                <div>
                    <input type="text" name="search" placeholder="Cari Judul atau Penulis..."
                        class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        value="{{ $request->search }}">
                </div>

                <div class="flex space-x-4">
                    <select name="category" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 flex-1">
                        <option value="all">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" {{ $request->category == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>

                    <select name="order" class="border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 flex-1">
                        <option value="latest" {{ $request->order == 'latest' ? 'selected' : '' }}>Tahun Terbit Terbaru</option>
                        <option value="oldest" {{ $request->order == 'oldest' ? 'selected' : '' }}>Tahun Terbit Terdahulu</option>
                        <option value="rating_desc" {{ $request->order == 'rating_desc' ? 'selected' : '' }}>Rating Tertinggi</option>
                        <option value="rating_asc" {{ $request->order == 'rating_asc' ? 'selected' : '' }}>Rating Terendah</option>
                    </select>

                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-md">
                        Filter
                    </button>
                    <a href="{{ route('catalog.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-md">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <div class="book-card-container">
            @forelse ($books as $book)
                <div class="book-card bg-white">
                    <a href="{{ route('catalog.show', $book) }}" class="block">
                        <div class="book-cover bg-gray-200 border border-gray-300">
                            {{ $book->title }}
                        </div>
                        <div class="p-3">
                            <h4 class="text-base font-semibold truncate">{{ $book->title }}</h4>
                            <p class="text-xs text-gray-500 truncate">{{ $book->author }}</p>
                            @php
                                // Ambil rating rata-rata dari atribut avg_rating yang di-join di controller
                                $rating = $book->avg_rating;
                            @endphp
                            <p class="text-xs font-bold text-yellow-500 mt-1">
                                {{ number_format($rating ?? 0, 1) }}/5.0
                            </p>
                            <span class="text-xs px-2 py-0.5 mt-1 inline-block rounded-full {{ $book->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                Stok: {{ $book->stock }}
                            </span>
                        </div>
                    </a>
                </div>
            @empty
                <p class="col-span-4 text-gray-500">Tidak ada buku yang cocok dengan kriteria Anda.</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $books->links() }}
        </div>
    </div>
</x-guest-layout>
