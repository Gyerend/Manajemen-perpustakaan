<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Mahasiswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            @if (session('status'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p>{{ session('status') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            @if ($user->is_blocked || $totalFineAmount > 0)
                <div class="bg-red-50 border-2 border-red-500 text-red-800 p-6 rounded-lg shadow-lg">
                    <h3 class="text-xl font-bold mb-2">🚨 Peringatan Denda Tertunggak!</h3>
                    <p class="mb-3">Anda memiliki total denda tertunggak sebesar
                        <span class="font-extrabold text-2xl">Rp{{ number_format($totalFineAmount, 0, ',', '.') }}</span>.
                    </p>
                    @if ($user->is_blocked)
                        <p class="font-semibold">⚠️ Akses peminjaman Anda saat ini DIBLOKIR. Harap segera hubungi Pegawai Perpustakaan untuk pelunasan.</p>
                    @endif

                    {{-- Tampilkan detail denda --}}
                    @foreach($outstandingFines as $loan)
                        @foreach($loan->fines->where('status', 'outstanding') as $fine)
                            <p class="text-sm mt-1">- Denda Rp{{ abs(number_format($fine->amount, 0, ',', '.')) }} (Buku: {{ $loan->book->title }}, Alasan: {{ $fine->reason }})</p>
                        @endforeach
                    @endforeach
                </div>
            @endif

            @if($recommendedBooks->count())
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-4">Rekomendasi untuk Anda 🎯</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ($recommendedBooks as $book)
                                <a href="{{ route('catalog.show', $book) }}" class="p-3 border rounded-lg hover:bg-gray-50 transition">
                                    <p class="font-semibold truncate">{{ $book->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $book->author }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4 flex justify-between items-center">
                        Pemberitahuan Terbaru
                        <span class="text-sm font-normal text-gray-500">({{ $notifications->count() }} terbaru)</span>
                    </h3>
                    @forelse ($notifications as $notification)
                        <div class="border-b p-2 {{ $notification->is_read ? 'bg-white text-gray-600' : 'bg-indigo-50 font-medium text-indigo-800' }}">
                            <p class="font-semibold">{{ $notification->title }}</p>
                            <p class="text-sm">{{ $notification->message }}</p>
                            <p class="text-xs text-right text-gray-400">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-gray-500">Tidak ada pemberitahuan baru.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Aktivitas Aktif (Pinjaman & Reservasi) ({{ $activeLoans->count() }})</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jatuh Tempo/Tgl Reservasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($activeLoans as $loan)
                                    <tr>
                                        <td class="px-4 py-4 text-sm font-medium">{{ $loan->book->title }}</td>
                                        <td class="px-4 py-4 text-sm">
                                            @php
                                                $statusClass = 'bg-yellow-100 text-yellow-800';
                                                if ($loan->status == 'reserved' || $loan->status == 'reserved_active') {
                                                    $statusClass = 'bg-blue-100 text-blue-800';
                                                } elseif (($loan->has_fine ?? false) || ($loan->is_late ?? false)) {
                                                    $statusClass = 'bg-red-100 text-red-800';
                                                }
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm
                                            {{ ($loan->is_late ?? false) ? 'text-red-600 font-bold' : 'text-gray-900' }}">
                                            @if($loan->due_date)
                                                {{ $loan->due_date->format('d M Y') }}
                                                @if(($loan->is_late ?? false))
                                                    <span class="text-xs text-red-500 block">Terlambat {{ abs($loan->days_late) }} hari</span>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-sm">
                                            @if($loan->status == 'reserved' || $loan->status == 'reserved_active')
                                                <form action="{{ route('reservation.cancel', $loan) }}" method="POST" class="inline" onsubmit="return confirm('Anda yakin ingin membatalkan reservasi ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white py-1 px-3 rounded text-xs">
                                                        Batalkan Reservasi
                                                    </button>
                                                </form>
                                            @elseif(!($loan->is_late ?? false) && !($loan->has_fine ?? false) && ($loan->status == 'borrowed' || $loan->status == 'extended'))
                                                <form action="{{ route('loan.renew', $loan) }}" method="POST" class="inline" onsubmit="return confirm('Anda yakin ingin memperpanjang pinjaman ini?');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="bg-indigo-500 hover:bg-indigo-600 text-white py-1 px-3 rounded text-xs">
                                                        Perpanjang (7 Hari)
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-500 text-xs">Tidak ada aksi</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-4 text-center text-gray-500">Anda tidak memiliki pinjaman aktif atau reservasi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Riwayat Peminjaman Terbaru</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tgl Kembali</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($historyLoans as $loan)
                                    <tr>
                                        <td class="px-4 py-4 text-sm font-medium">{{ $loan->book->title }}</td>
                                        <td class="px-4 py-4 text-sm">{{ $loan->return_date ? $loan->return_date->format('d M Y') : '-' }}</td>
                                        <td class="px-4 py-4 text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Dikembalikan
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">Belum ada riwayat pengembalian.</td>
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
