<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daftar Reservasi Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('status') }}</p>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">Reservasi Menunggu (Stok 0)</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buku</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemesanan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Pesan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($reservations as $reservation)
                                    <tr>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $reservation->book->title }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $reservation->user->name }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">{{ $reservation->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($reservation->status == 'reserved') bg-yellow-100 text-yellow-800
                                                @elseif($reservation->status == 'reserved_active') bg-green-100 text-green-800
                                                @endif">
                                                {{ ucfirst(str_replace('_', ' ', $reservation->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm">
                                            @if($reservation->status == 'reserved')
                                                <form action="{{ route('pegawai.reservations.activate', $reservation) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white py-1 px-3 rounded text-xs">
                                                        Aktifkan Reservasi
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-gray-500 text-xs">Menunggu Pinjam</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-4 text-center text-gray-500">Tidak ada reservasi buku yang menunggu saat ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $reservations->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
