<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Peminjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    {{-- Book Info --}}
                    <div class="flex items-start gap-6 mb-8">
                        <div class="flex-shrink-0 w-24 h-32 bg-gray-100 rounded flex items-center justify-center">
                            @if($loan->book->cover_image)
                            <img src="{{ asset('storage/' . $loan->book->cover_image) }}" alt="" class="w-full h-full object-cover rounded">
                            @else
                            <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-semibold text-gray-900">
                                <a href="{{ route('books.show', $loan->book) }}" class="hover:text-blue-600">
                                    {{ $loan->book->title }}
                                </a>
                            </h3>
                            <p class="text-gray-600 mt-1">{{ $loan->book->author }}</p>
                            <p class="text-sm text-gray-500 mt-1">ISBN: {{ $loan->book->isbn ?? '-' }}</p>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <div class="mb-6">
                        @switch($loan->status)
                            @case('requested')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                    Menunggu Persetujuan
                                </span>
                                @break
                            @case('approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Disetujui - Siap Diambil
                                </span>
                                @break
                            @case('active')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Sedang Dipinjam
                                </span>
                                @break
                            @case('overdue')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Terlambat
                                </span>
                                @break
                            @case('returned')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    Sudah Dikembalikan
                                </span>
                                @break
                            @case('rejected')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    Ditolak
                                </span>
                                @break
                            @case('cancelled')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                    Dibatalkan
                                </span>
                                @break
                        @endswitch
                    </div>

                    {{-- Loan Details --}}
                    <dl class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Permintaan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $loan->request_date->format('d M Y, H:i') }}</dd>
                        </div>
                        @if($loan->approval_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Persetujuan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $loan->approval_date->format('d M Y, H:i') }}</dd>
                        </div>
                        @endif
                        @if($loan->pickup_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pengambilan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $loan->pickup_date->format('d M Y, H:i') }}</dd>
                        </div>
                        @endif
                        @if($loan->due_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jatuh Tempo</dt>
                            <dd class="mt-1 text-sm {{ $loan->status === 'overdue' ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                {{ $loan->due_date->format('d M Y') }}
                                @if($loan->status === 'active')
                                    ({{ $loan->days_remaining }} hari lagi)
                                @elseif($loan->status === 'overdue')
                                    ({{ abs($loan->days_remaining) }} hari terlambat)
                                @endif
                            </dd>
                        </div>
                        @endif
                        @if($loan->return_date)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tanggal Pengembalian</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $loan->return_date->format('d M Y, H:i') }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Jumlah Perpanjangan</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $loan->renewal_count }} dari {{ \App\Models\Loan::MAX_RENEWALS }} kali</dd>
                        </div>
                        @if($loan->approvedBy)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Diproses oleh</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $loan->approvedBy->name }}</dd>
                        </div>
                        @endif
                    </dl>

                    {{-- Notes --}}
                    @if($loan->notes)
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Catatan</h4>
                        <p class="text-sm text-gray-600">{{ $loan->notes }}</p>
                    </div>
                    @endif

                    {{-- Fine Info --}}
                    @if($loan->fine)
                    <div class="mb-8 p-4 bg-red-50 rounded-lg">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Denda</h4>
                        <p class="text-lg font-semibold text-red-900">Rp {{ number_format($loan->fine->amount, 0, ',', '.') }}</p>
                        <p class="text-sm text-red-700 mt-1">
                            Status: 
                            @if($loan->fine->status === 'paid')
                                <span class="text-green-600">Sudah Dibayar</span>
                            @elseif($loan->fine->status === 'waived')
                                <span class="text-gray-600">Dihapuskan</span>
                            @else
                                <span class="text-red-600 font-semibold">Belum Dibayar</span>
                            @endif
                        </p>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center gap-4 pt-6 border-t">
                        <a href="{{ route('loans.index') }}" class="text-gray-600 hover:text-gray-800">
                            ← Kembali ke Daftar
                        </a>
                        
                        @if($loan->canRenew())
                        <form action="{{ route('loans.renew', $loan) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                Perpanjang Peminjaman
                            </button>
                        </form>
                        @endif

                        @if($loan->status === 'requested')
                        <form action="{{ route('loans.cancel', $loan) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700" onclick="return confirm('Apakah Anda yakin ingin membatalkan permintaan ini?')">
                                Batalkan Permintaan
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
