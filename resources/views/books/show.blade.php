<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl p-6">
                <div class="flex flex-col md:flex-row gap-6">
                    <div class="md:w-1/3">
                        @if($book->image)
                            <img src="{{ Storage::url($book->image) }}" alt="{{ $book->title }}" class="w-full h-96 object-cover rounded-lg border">
                        @else
                            <div class="w-full h-96 bg-gray-200 rounded-lg border flex items-center justify-center">
                                <span class="text-gray-500">Tidak ada gambar</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="md:w-2/3">
                        <h1 class="text-2xl font-bold text-gray-900">{{ $book->title }}</h1>
                        <p class="text-lg text-gray-700 mt-1">{{ $book->author }}</p>
                        
                        <div class="mt-4 space-y-2">
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">Penerbit:</span>
                                <span class="text-gray-900">{{ $book->publisher }}</span>
                            </div>
                            
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">Tahun Terbit:</span>
                                <span class="text-gray-900">{{ $book->publication_year }}</span>
                            </div>
                            
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">Kategori:</span>
                                <span class="text-gray-900">{{ $book->category }}</span>
                            </div>
                            
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">Stok Tersedia:</span>
                                <span class="text-gray-900">{{ $book->stock }}</span>
                            </div>
                            
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">Maks Pinjam (Hari):</span>
                                <span class="text-gray-900">{{ $book->max_loan_days }}</span>
                            </div>
                            
                            <div class="flex">
                                <span class="font-medium w-32 text-gray-700">Denda/Hari:</span>
                                <span class="text-gray-900">Rp{{ number_format($book->daily_fine_rate, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        @if($book->description)
                            <div class="mt-6">
                                <h3 class="text-lg font-semibold text-gray-900">Deskripsi</h3>
                                <p class="mt-2 text-gray-700">{{ $book->description }}</p>
                            </div>
                        @endif>
                        
                        <div class="mt-6 flex space-x-4">
                            <a href="{{ route('books.edit', $book) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150">
                                Edit
                            </a>
                            <a href="{{ route('books.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition duration-150">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>