<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <h3 class="text-2xl font-bold text-gray-900 mb-6">Ringkasan Sistem</h3>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                @php
                    $stats = [
                        ['title' => 'Total Koleksi Buku', 'value' => $totalBooks, 'icon' => 'book', 'color' => 'indigo'],
                        ['title' => 'Total Pengguna Aktif', 'value' => $totalUsers, 'icon' => 'users', 'color' => 'blue'],
                        ['title' => 'Pinjaman Aktif', 'value' => $activeLoansCount, 'icon' => 'loan', 'color' => 'green'],
                        ['title' => 'Denda Tertunggak (Rp)', 'value' => number_format($outstandingFines, 0, ',', '.'), 'icon' => 'cash', 'color' => 'red'],
                    ];
                    $iconMap = [
                        'book' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.414 9.362 5 8 5c-4 0-4 4-4 4v7c0 4 4 4 4 4h8c4 0 4-4 4-4v-7c0-4-4-4-4-4 0 0-1.5-.414-3.5-1.253zM18 9h-4M8 9H6"></path></svg>',
                        'users' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20h-5v-2a3 3 0 00-5.356-1.857M17 20v-2A7 7 0 005 18v2m12 0h-5v-2a3 3 0 00-5.356-1.857M6 9h-.356A3 3 0 002 11.233V14a2 2 0 002 2h16a2 2 0 002-2v-2.767A3 3 0 0018.356 9H18m-9.356 9A5 5 0 007 18h2.356M9 9h6m-6 0v2m0 4h6m-6-4h6m-6 4h6"></path></svg>',
                        'loan' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-3-3m3 3l-3 3M4 18v-7a4 4 0 014-4h8a4 4 0 014 4v7"></path></svg>',
                        'cash' => '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"></path></svg>',
                    ];
                @endphp

                @foreach ($stats as $stat)
                    <div class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-{{ $stat['color'] }}-500 transition duration-300 hover:shadow-xl">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-medium text-gray-500">{{ $stat['title'] }}</p>
                            <span class="text-{{ $stat['color'] }}-500">
                                {!! $iconMap[$stat['icon']] !!}
                            </span>
                        </div>
                        <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stat['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                 <div class="p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-xl
                    {{ $lowStockBooks > 0 ? 'border-l-4 border-red-500 bg-red-50' : 'bg-white' }}">
                    <h3 class="font-bold text-lg mb-2 text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.503-1.662 1.79-3.045l-6.928-13.855a2 2 0 00-3.58 0L3.648 19.955c-.713 1.383.25 3.045 1.79 3.045z"></path></svg>
                        Peringatan Inventaris
                    </h3>
                    <p class="text-gray-700">
                        Saat ini terdapat **{{ $lowStockBooks }}** buku dengan stok di bawah 5. Disarankan untuk segera melakukan *restock* atau pemesanan.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('admin.analytics.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">Lihat Laporan Analitik &rarr;</a>
                    </div>
                </div>

                 <div class="bg-white p-6 rounded-xl shadow-lg transition duration-300 hover:shadow-xl">
                    <h3 class="font-bold text-lg mb-4 text-gray-800">Akses Cepat</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <a href="{{ route('admin.users.index') }}" class="bg-indigo-50 p-4 rounded-lg text-indigo-800 font-semibold hover:bg-indigo-100 transition">Kelola Pengguna</a>
                        <a href="{{ route('books.collection.index') }}" class="bg-indigo-50 p-4 rounded-lg text-indigo-800 font-semibold hover:bg-indigo-100 transition">Kelola Koleksi</a>
                        <a href="{{ route('pegawai.loans.pending') }}" class="bg-indigo-50 p-4 rounded-lg text-indigo-800 font-semibold hover:bg-indigo-100 transition">Proses Pinjaman</a>
                        <a href="{{ route('admin.analytics.index') }}" class="bg-indigo-50 p-4 rounded-lg text-indigo-800 font-semibold hover:bg-indigo-100 transition">Laporan Analitik</a>
                    </div>
                 </div>
            </div>

        </div>
    </div>
</x-app-layout>
