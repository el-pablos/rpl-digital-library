<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Peminjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        {{-- Book Info --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Buku</h3>
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-20 h-28 bg-gray-100 rounded flex items-center justify-center">
                                    @if($loan->book->cover_image)
                                    <img src="{{ asset('storage/' . $loan->book->cover_image) }}" alt="" class="w-full h-full object-cover rounded">
                                    @else
                                    <svg class="h-10 w-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $loan->book->title }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $loan->book->author }}</p>
                                    <p class="text-xs text-gray-500 mt-1">ISBN: {{ $loan->book->isbn ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Member Info --}}
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Peminjam</h3>
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-lg font-medium text-gray-600">
                                    {{ strtoupper(substr($loan->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $loan->user->name }}</p>
                                    <p class="text-sm text-gray-600">{{ $loan->user->email }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $loan->user->phone ?? 'No phone' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="mt-8 pt-6 border-t">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Status Peminjaman</h3>
                        <div class="mb-4">
                            @switch($loan->status)
                                @case('requested')
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">Menunggu Persetujuan</span>
                                    @break
                                @case('approved')
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">Disetujui - Siap Diambil</span>
                                    @break
                                @case('active')
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Sedang Dipinjam</span>
                                    @break
                                @case('overdue')
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Terlambat</span>
                                    @break
                                @case('returned')
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Sudah Dikembalikan</span>
                                    @break
                                @case('rejected')
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">Ditolak</span>
                                    @break
                                @case('cancelled')
                                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">Dibatalkan</span>
                                    @break
                            @endswitch
                        </div>

                        <dl class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Request</dt>
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
                                <dd class="mt-1 text-sm {{ $loan->status === 'overdue' ? 'text-red-600 font-semibold' : 'text-gray-900' }}">{{ $loan->due_date->format('d M Y') }}</dd>
                            </div>
                            @endif
                            @if($loan->return_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Pengembalian</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $loan->return_date->format('d M Y, H:i') }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Perpanjangan</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $loan->renewal_count }}/{{ \App\Models\Loan::MAX_RENEWALS }}</dd>
                            </div>
                            @if($loan->approvedBy)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Diproses oleh</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $loan->approvedBy->name }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Notes --}}
                    @if($loan->notes)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Catatan</h4>
                        <p class="text-sm text-gray-600">{{ $loan->notes }}</p>
                    </div>
                    @endif

                    {{-- Fine --}}
                    @if($loan->fine)
                    <div class="mt-6 p-4 bg-red-50 rounded-lg">
                        <h4 class="text-sm font-medium text-red-800 mb-2">Denda</h4>
                        <p class="text-lg font-semibold text-red-900">Rp {{ number_format($loan->fine->amount, 0, ',', '.') }}</p>
                        <p class="text-sm text-red-700 mt-1">
                            Status: 
                            @if($loan->fine->status === 'paid')
                                <span class="text-green-600 font-medium">Sudah Dibayar</span>
                            @elseif($loan->fine->status === 'waived')
                                <span class="text-gray-600">Dihapuskan</span>
                            @else
                                <span class="text-red-600 font-semibold">Belum Dibayar</span>
                            @endif
                        </p>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="mt-8 pt-6 border-t flex items-center justify-between">
                        <a href="{{ route('librarian.loans.index') }}" class="text-gray-600 hover:text-gray-800">← Kembali</a>
                        <div class="flex gap-3">
                            @if($loan->status === 'requested')
                            <form action="{{ route('librarian.loans.approve', $loan) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                    Setujui
                                </button>
                            </form>
                            <form action="{{ route('librarian.loans.reject', $loan) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                    Tolak
                                </button>
                            </form>
                            @elseif($loan->status === 'approved')
                            <form action="{{ route('librarian.loans.pickup', $loan) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Proses Pengambilan
                                </button>
                            </form>
                            @elseif(in_array($loan->status, ['active', 'overdue']))
                            <form action="{{ route('librarian.loans.return', $loan) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                                    Proses Pengembalian
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
