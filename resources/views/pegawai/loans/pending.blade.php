<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Proses Pengembalian & Denda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-md" role="alert">
                    <p>{{ session('status') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-md" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl">
                <div class="p-6 text-gray-900">
                    <h3 class="text-xl font-bold mb-4">Daftar Pinjaman Aktif ({{ $loans->total() }})</h3>
                    <div class="mb-4">
                        <a href="{{ route('pegawai.reservations.pending') }}" class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center space-x-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-10 4h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span>Lihat Daftar Reservasi</span>
                        </a>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Peminjam</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Buku</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Jatuh Tempo</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Denda Potensial</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($loans as $loan)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loan->user->name }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-700">{{ $loan->book->title }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm
                                            {{ $loan->is_late ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            {{ $loan->due_date->format('d M Y') }}
                                            @if($loan->is_late)
                                                <span class="text-xs text-red-500 block">Lewat {{ (int) abs($loan->days_late) }} hari</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-medium rounded-full
                                                @if($loan->is_late) bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ $loan->is_late ? 'TERLAMBAT' : ucfirst($loan->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-red-700">
                                            Rp{{ number_format(abs($loan->potential_fine), 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            <form action="{{ route('pegawai.loans.return', $loan) }}" method="POST" class="inline" onsubmit="return confirm('Konfirmasi pengembalian buku {{ $loan->book->title }}?');">
                                                @csrf
                                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded-lg text-xs font-medium shadow-sm transition duration-150 flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                                                    <span>Kembalikan</span>
                                                </button>
                                            </form>

                                            @if($loan->outstanding_fine)
                                                {{-- Kita menggunakan ID denda yang ditemukan di controller --}}
                                                <form action="{{ route('pegawai.fines.pay', $loan->outstanding_fine) }}" method="POST" class="inline ml-2" onsubmit="return confirm('Konfirmasi pembayaran denda sebesar Rp{{ number_format($loan->outstanding_fine->amount, 0, ',', '.') }} untuk {{ $loan->user->name }}?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-1 px-3 rounded-lg text-xs font-medium shadow-sm transition duration-150 flex items-center space-x-1">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3 .895 3 2s-1.343 2-3 2-3 .895-3 2 1.343 2 3 2m-3-4h6m-9-4v10a2 2 0 002 2h10a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v2"></path></svg>
                                                        <span>Bayar Denda</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada pinjaman aktif saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $loans->links() }}
                    </div>
                </div>
            </div>



        </div>
    </div>
</x-app-layout>
