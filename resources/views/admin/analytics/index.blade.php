<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan & Analitik Perpustakaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
                    <p class="text-sm font-medium text-gray-500">Total Koleksi Buku</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalBooks }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-indigo-500">
                    <p class="text-sm font-medium text-gray-500">Total Mahasiswa Terdaftar</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalUsers }}</p>
                </div>
                <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
                    <p class="text-sm font-medium text-gray-500">Pinjaman Aktif Saat Ini</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $activeLoansCount }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Tren Peminjaman (6 Bulan Terakhir)</h3>
                    {{-- Di sini Anda bisa mengintegrasikan Chart.js atau sejenisnya. Untuk demonstrasi, kita tampilkan data mentah. --}}
                    @if($loanTrends->isNotEmpty())
                        <ul class="space-y-2">
                            @foreach ($loanTrends as $trend)
                                <li class="flex justify-between border-b pb-1">
                                    <span class="text-gray-600">{{ $trend->month_year }}</span>
                                    <span class="font-bold">{{ $trend->count }} Peminjaman</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500">Data tren peminjaman 6 bulan terakhir tidak tersedia.</p>
                    @endif
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-lg font-semibold mb-4">Top 5 Buku Paling Sering Dipinjam</h3>
                    <ul class="space-y-2">
                        @forelse ($topBooks as $index => $book)
                            <li class="flex justify-between border-b pb-1">
                                <span class="font-medium text-gray-800">{{ $index + 1 }}. {{ $book->title }}</span>
                                <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded">{{ $book->total_loans }} Kali</span>
                            </li>
                        @empty
                            <p class="text-gray-500">Belum ada data peminjaman yang tercatat.</p>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">Buku dengan Stok Rendah (Stok < 5)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penulis</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stok Tersedia</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($lowStockBooks as $book)
                                    <tr class="bg-red-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-700">{{ $book->title }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-700">{{ $book->author }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-900">{{ $book->stock }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">Semua buku memiliki stok yang cukup.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
