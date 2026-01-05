<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Peminjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filter Tabs --}}
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('loans.index') }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua
                    </a>
                    <a href="{{ route('loans.index', ['status' => 'active']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'active' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Aktif
                    </a>
                    <a href="{{ route('loans.index', ['status' => 'pending']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'pending' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pending
                    </a>
                    <a href="{{ route('loans.index', ['status' => 'returned']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'returned' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Dikembalikan
                    </a>
                </nav>
            </div>

            {{-- Loans Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Buku
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tanggal Pinjam
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jatuh Tempo
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($loans as $loan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-12 w-10 bg-gray-100 rounded flex items-center justify-center">
                                            @if($loan->book->cover_image)
                                            <img src="{{ asset('storage/' . $loan->book->cover_image) }}" alt="" class="h-12 w-10 object-cover rounded">
                                            @else
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('books.show', $loan->book) }}" class="text-sm font-medium text-gray-900 hover:text-blue-600">
                                                {{ Str::limit($loan->book->title, 40) }}
                                            </a>
                                            <div class="text-sm text-gray-500">{{ $loan->book->author }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $loan->pickup_date ? $loan->pickup_date->format('d M Y') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($loan->due_date)
                                        <span class="{{ $loan->status === 'overdue' ? 'text-red-600 font-semibold' : 'text-gray-500' }}">
                                            {{ $loan->due_date->format('d M Y') }}
                                        </span>
                                        @if($loan->status === 'active' && $loan->days_remaining <= 2)
                                            <span class="text-yellow-600 text-xs">({{ $loan->days_remaining }} hari lagi)</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($loan->status)
                                        @case('requested')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Menunggu Persetujuan
                                            </span>
                                            @break
                                        @case('approved')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Siap Diambil
                                            </span>
                                            @break
                                        @case('active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Sedang Dipinjam
                                            </span>
                                            @break
                                        @case('overdue')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Terlambat
                                            </span>
                                            @break
                                        @case('returned')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Dikembalikan
                                            </span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Ditolak
                                            </span>
                                            @break
                                        @case('cancelled')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Dibatalkan
                                            </span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('loans.show', $loan) }}" class="text-blue-600 hover:text-blue-800">
                                            Detail
                                        </a>
                                        @if($loan->canRenew())
                                        <form action="{{ route('loans.renew', $loan) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800">
                                                Perpanjang
                                            </button>
                                        </form>
                                        @endif
                                        @if($loan->status === 'requested')
                                        <form action="{{ route('loans.cancel', $loan) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Apakah Anda yakin ingin membatalkan permintaan ini?')">
                                                Batalkan
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <p class="mt-2 text-sm font-medium">Tidak ada data peminjaman</p>
                                    <p class="mt-1 text-sm">Mulai pinjam buku dari <a href="{{ route('books.index') }}" class="text-blue-600 hover:text-blue-800">katalog</a>.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($loans->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $loans->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
