<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Dipinjam</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['total_borrowed'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Sedang Dipinjam</div>
                    <div class="text-3xl font-bold text-blue-600">{{ $stats['currently_borrowed'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Review Ditulis</div>
                    <div class="text-3xl font-bold text-green-600">{{ $stats['reviews_written'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Status Peminjaman</div>
                    <div class="text-xl font-bold {{ $stats['can_borrow'] ? 'text-green-600' : 'text-red-600' }}">
                        {{ $stats['can_borrow'] ? 'Dapat Meminjam' : 'Tidak Dapat Meminjam' }}
                    </div>
                </div>
            </div>

            {{-- Alerts --}}
            @if($totalUnpaidFines > 0)
            <div class="mb-8 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            Anda memiliki denda yang belum dibayar sebesar <strong>Rp {{ number_format($totalUnpaidFines, 0, ',', '.') }}</strong>.
                            <a href="{{ route('fines.index') }}" class="underline font-medium">Lihat detail</a>
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Active Loans --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pinjaman Aktif</h3>
                        @forelse($activeLoans as $loan)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div>
                                <p class="font-medium text-gray-900">{{ $loan->book->title }}</p>
                                <p class="text-sm text-gray-500">
                                    Jatuh tempo: {{ $loan->due_date->format('d M Y') }}
                                    @if($loan->status === 'overdue')
                                    <span class="text-red-600 font-semibold">(Terlambat)</span>
                                    @elseif($loan->days_remaining <= 2)
                                    <span class="text-yellow-600">({{ $loan->days_remaining }} hari lagi)</span>
                                    @endif
                                </p>
                            </div>
                            @if($loan->canRenew())
                            <form action="{{ route('loans.renew', $loan) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm text-blue-600 hover:text-blue-800">Perpanjang</button>
                            </form>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-500">Tidak ada pinjaman aktif.</p>
                        @endforelse
                        <a href="{{ route('loans.index') }}" class="block mt-4 text-sm text-blue-600 hover:text-blue-800">Lihat semua pinjaman →</a>
                    </div>
                </div>

                {{-- Pending Requests --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Permintaan Pending</h3>
                        @forelse($pendingLoans as $loan)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div>
                                <p class="font-medium text-gray-900">{{ $loan->book->title }}</p>
                                <p class="text-sm text-gray-500">
                                    Status: 
                                    @if($loan->status === 'requested')
                                    <span class="text-yellow-600">Menunggu Persetujuan</span>
                                    @else
                                    <span class="text-green-600">Disetujui - Siap Diambil</span>
                                    @endif
                                </p>
                            </div>
                            @if($loan->status === 'requested')
                            <form action="{{ route('loans.cancel', $loan) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm text-red-600 hover:text-red-800">Batalkan</button>
                            </form>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-500">Tidak ada permintaan pending.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Notifications --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Notifikasi Terbaru</h3>
                        @forelse($unreadNotifications as $notification)
                        <div class="py-3 border-b last:border-0">
                            <p class="text-sm text-gray-900">{{ $notification->message }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        @empty
                        <p class="text-gray-500">Tidak ada notifikasi baru.</p>
                        @endforelse
                        <a href="{{ route('notifications.index') }}" class="block mt-4 text-sm text-blue-600 hover:text-blue-800">Lihat semua notifikasi →</a>
                    </div>
                </div>

                {{-- Recommendations --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rekomendasi untuk Anda</h3>
                        @forelse($recommendations as $book)
                        <div class="flex items-center py-3 border-b last:border-0">
                            <div class="flex-1">
                                <a href="{{ route('books.show', $book) }}" class="font-medium text-gray-900 hover:text-blue-600">{{ $book->title }}</a>
                                <p class="text-sm text-gray-500">{{ $book->author }}</p>
                            </div>
                            @if($book->isAvailable())
                            <span class="text-xs text-green-600">Tersedia</span>
                            @else
                            <span class="text-xs text-red-600">Habis</span>
                            @endif
                        </div>
                        @empty
                        <p class="text-gray-500">Belum ada rekomendasi.</p>
                        @endforelse
                        <a href="{{ route('books.recommendations') }}" class="block mt-4 text-sm text-blue-600 hover:text-blue-800">Lihat lebih banyak rekomendasi →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
