<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Perpus Digital') }} | Discover</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            /* Layout Grid untuk card buku */
            .book-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                gap: 20px;
            }
            .book-card-style {
                /* Estetika modern */
                border-radius: 16px;
                overflow: hidden;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                transition: transform 0.3s, box-shadow 0.3s;
                background-color: #ffffff;
            }
            .book-card-style:hover {
                transform: translateY(-8px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            .book-cover-placeholder {
                width: 100%;
                height: 250px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 600;
                text-align: center;
                padding: 15px;
                border-bottom: 1px solid #f3f4f6;
            }
        </style>
    </head>
    <body class="antialiased bg-gray-50">
        <div class="min-h-screen bg-gray-50">
            {{-- Menggunakan navigation layout yang sudah diperbaiki --}}
            @include('layouts.navigation')

            <main class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                    <h2 class="text-4xl font-extrabold text-gray-900 mb-8 tracking-tight">
                        Discover & Explore 📖
                    </h2>

                    <div class="bg-white p-6 shadow-xl rounded-2xl mb-12 border border-gray-100">
                        <form action="{{ route('catalog.index') }}" method="GET" class="flex flex-col md:flex-row space-y-3 md:space-y-0 md:space-x-4 items-center">
                            <svg class="w-6 h-6 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input type="text" name="search" placeholder="Cari buku (judul, penulis, atau kategori)..."
                                class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150"
                                {{-- FIX: Menggunakan $request->search yang dikirim Controller --}}
                                value="{{ $request->search ?? '' }}">
                            <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-150">
                                Cari
                            </button>
                        </form>
                    </div>

                    @if($recommendedBooks->count())
                        <div class="mb-14">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                                    <svg class="w-6 h-6 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.637-.921 1.936 0l1.545 4.757a1 1 0 00.95.69h5.002c.969 0 1.371 1.24.588 1.81l-4.043 2.956a1 1 0 00-.364 1.118l1.545 4.757c.3.921-.755 1.688-1.545 1.118l-4.043-2.956a1 1 0 00-1.176 0l-4.043 2.956c-.789.57-1.844-.197-1.545-1.118l1.545-4.757a1 1 0 00-.364-1.118L2.091 10.183c-.783-.57-.381-1.81.588-1.81h5.002a1 1 0 00.95-.69l1.545-4.757z"></path></svg>
                                    Rekomendasi Populer
                                </h3>
                                <a href="{{ route('catalog.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition duration-150">Lihat Semua Katalog &rarr;</a>
                            </div>
                            <div class="book-grid">
                                @foreach ($recommendedBooks as $book)
                                    <a href="{{ route('catalog.show', $book) }}" class="book-card-style">
                                        @if($book->image)
                                            <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}" class="w-full h-64 object-cover">
                                        @else
                                            <div class="book-cover-placeholder bg-yellow-50 border-yellow-200 text-yellow-800">
                                                {{ $book->title }}
                                            </div>
                                        @endif
                                        <div class="p-4">
                                            <h4 class="text-base font-semibold truncate text-gray-900">{{ $book->title }}</h4>
                                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $book->author }}</p>
                                            <div class="flex items-center mt-2">
                                                <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9.049 2.927c.3-.921 1.637-.921 1.936 0l1.545 4.757a1 1 0 00.95.69h5.002c.969 0 1.371 1.24.588 1.81l-4.043 2.956a1 1 0 00-.364 1.118l1.545 4.757c.3.921-.755 1.688-1.545 1.118l-4.043-2.956a1 1 0 00-1.176 0l-4.043 2.956c-.789.57-1.844-.197-1.545-1.118l1.545-4.757a1 1 0 00-.364-1.118L2.091 10.183c-.783-.57-.381-1.81.588-1.81h5.002a1 1 0 00.95-.69l1.545-4.757z"></path></svg>
                                                <p class="text-sm font-bold text-gray-700">
                                                    {{ number_format($book->reviews_avg_rating ?? 0, 1) }}/5.0
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($latestBooks->count())
                        <div class="mb-14">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                <svg class="w-6 h-6 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Buku Terbaru
                            </h3>
                            <div class="book-grid">
                                @foreach ($latestBooks as $book)
                                    <a href="{{ route('catalog.show', $book) }}" class="book-card-style">
                                        @if($book->image)
                                            <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}" class="w-full h-64 object-cover">
                                        @else
                                            <div class="book-cover-placeholder bg-green-50 border-green-200 text-green-800">
                                                {{ $book->title }}
                                            </div>
                                        @endif
                                        <div class="p-4">
                                            <h4 class="text-base font-semibold truncate text-gray-900">{{ $book->title }}</h4>
                                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $book->author }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($categories->count())
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                <svg class="w-6 h-6 text-indigo-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                                Jelajahi Kategori
                            </h3>
                            <div class="flex flex-wrap gap-4">
                                @foreach ($categories as $category)
                                    <a href="{{ route('catalog.index', ['category' => $category]) }}" class="bg-white p-4 rounded-xl shadow-md hover:shadow-lg transition duration-150 border border-indigo-100 flex items-center space-x-2">
                                        <span class="text-indigo-600 font-medium">{{ $category }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            </main>
        </div>
    </body>
</html>
