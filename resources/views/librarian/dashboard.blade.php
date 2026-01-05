<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Pustakawan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Alert Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-yellow-800">Permintaan Pending</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ $stats['pending_requests'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('librarian.loans.index', ['status' => 'requested']) }}" class="mt-3 inline-block text-sm text-yellow-700 hover:text-yellow-900">Proses sekarang →</a>
                </div>

                <div class="bg-red-50 border-l-4 border-red-400 p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-red-800">Peminjaman Terlambat</p>
                            <p class="text-2xl font-bold text-red-900">{{ $stats['overdue_loans'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('librarian.loans.index', ['status' => 'overdue']) }}" class="mt-3 inline-block text-sm text-red-700 hover:text-red-900">Lihat detail →</a>
                </div>

                <div class="bg-purple-50 border-l-4 border-purple-400 p-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-400" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-purple-800">Review Menunggu</p>
                            <p class="text-2xl font-bold text-purple-900">{{ $stats['pending_reviews'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('librarian.reviews.index') }}" class="mt-3 inline-block text-sm text-purple-700 hover:text-purple-900">Moderasi →</a>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Pinjaman Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $stats['active_loans'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Siap Diambil</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $stats['ready_for_pickup'] }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Denda Belum Dibayar</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">Rp {{ number_format($stats['unpaid_fines'], 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Pengembalian Hari Ini</p>
                    <p class="text-2xl font-semibold text-gray-900 mt-1">{{ $stats['returned_today'] }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Pending Loan Requests --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Permintaan Terbaru</h3>
                            <a href="{{ route('librarian.loans.index', ['status' => 'requested']) }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat semua</a>
                        </div>
                        @forelse($pendingLoans as $loan)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div>
                                <p class="font-medium text-gray-900">{{ Str::limit($loan->book->title, 25) }}</p>
                                <p class="text-sm text-gray-500">{{ $loan->user->name }} • {{ $loan->request_date->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-2">
                                <form action="{{ route('librarian.loans.approve', $loan) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-xs font-medium text-green-700 bg-green-100 rounded hover:bg-green-200">
                                        Setujui
                                    </button>
                                </form>
                                <form action="{{ route('librarian.loans.reject', $loan) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada permintaan pending</p>
                        @endforelse
                    </div>
                </div>

                {{-- Overdue Loans --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Peminjaman Terlambat</h3>
                            <a href="{{ route('librarian.loans.index', ['status' => 'overdue']) }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat semua</a>
                        </div>
                        @forelse($overdueLoans as $loan)
                        <div class="flex items-center justify-between py-3 border-b last:border-0">
                            <div>
                                <p class="font-medium text-gray-900">{{ Str::limit($loan->book->title, 25) }}</p>
                                <p class="text-sm text-gray-500">{{ $loan->user->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-red-600">{{ abs($loan->days_remaining) }} hari</p>
                                <p class="text-xs text-gray-500">terlambat</p>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-4">Tidak ada peminjaman terlambat</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
