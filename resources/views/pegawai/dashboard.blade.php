<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pegawai Perpustakaan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Tugas Utama & Akses Cepat</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- Proses Pengembalian --}}
                <a href="{{ route('pegawai.loans.pending') }}" class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-green-500 transition duration-300 hover:shadow-xl hover:bg-green-50">
                    <div class="flex items-center space-x-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <div>
                            <p class="font-bold text-xl text-gray-800">Proses Pengembalian</p>
                            <p class="text-sm text-gray-500">Konfirmasi buku dikembalikan & catat denda.</p>
                        </div>
                    </div>
                </a>

                {{-- Kelola Koleksi --}}
                <a href="{{ route('books.index') }}" class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-indigo-500 transition duration-300 hover:shadow-xl hover:bg-indigo-50">
                    <div class="flex items-center space-x-4">
                         <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.414 9.362 5 8 5c-4 0-4 4-4 4v7c0 4 4 4 4 4h8c4 0 4-4 4-4v-7c0-4-4-4-4-4 0 0-1.5-.414-3.5-1.253zM18 9h-4M8 9H6"></path></svg>
                        <div>
                            <p class="font-bold text-xl text-gray-800">Kelola Koleksi Buku</p>
                            <p class="text-sm text-gray-500">Tambah, Edit, dan Perbarui Stok.</p>
                        </div>
                    </div>
                </a>

                {{-- Daftar Reservasi --}}
                <a href="{{ route('pegawai.reservations.pending') }}" class="bg-white p-6 rounded-xl shadow-lg border-l-4 border-blue-500 transition duration-300 hover:shadow-xl hover:bg-blue-50">
                    <div class="flex items-center space-x-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-10 4h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <div>
                            <p class="font-bold text-xl text-gray-800">Daftar Reservasi</p>
                            <p class="text-sm text-gray-500">Aktivasi reservasi yang menunggu.</p>
                        </div>
                    </div>
                </a>
            </div>

            <p class="text-gray-600 mt-8">Selamat bertugas, {{ Auth::user()->name }}!</p>
        </div>
    </div>
</x-app-layout>
