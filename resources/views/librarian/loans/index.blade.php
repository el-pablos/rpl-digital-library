<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Peminjaman') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
            @endif

            {{-- Filter Tabs --}}
            <div class="mb-6 border-b border-gray-200 bg-white rounded-t-lg px-6">
                <nav class="-mb-px flex space-x-8 overflow-x-auto">
                    <a href="{{ route('librarian.loans.index') }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua
                    </a>
                    <a href="{{ route('librarian.loans.index', ['status' => 'requested']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ request('status') == 'requested' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Permintaan ({{ $counts['requested'] ?? 0 }})
                    </a>
                    <a href="{{ route('librarian.loans.index', ['status' => 'approved']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ request('status') == 'approved' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Siap Diambil ({{ $counts['approved'] ?? 0 }})
                    </a>
                    <a href="{{ route('librarian.loans.index', ['status' => 'active']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ request('status') == 'active' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Aktif ({{ $counts['active'] ?? 0 }})
                    </a>
                    <a href="{{ route('librarian.loans.index', ['status' => 'overdue']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ request('status') == 'overdue' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Terlambat ({{ $counts['overdue'] ?? 0 }})
                    </a>
                    <a href="{{ route('librarian.loans.index', ['status' => 'returned']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm whitespace-nowrap {{ request('status') == 'returned' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Dikembalikan
                    </a>
                </nav>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peminjam</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buku</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($loans as $loan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-600">
                                            {{ strtoupper(substr($loan->user->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $loan->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $loan->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-medium text-gray-900">{{ Str::limit($loan->book->title, 30) }}</p>
                                    <p class="text-xs text-gray-500">{{ $loan->book->author }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <p>Request: {{ $loan->request_date->format('d M Y') }}</p>
                                    @if($loan->due_date)
                                    <p class="{{ $loan->status === 'overdue' ? 'text-red-600 font-semibold' : '' }}">
                                        Due: {{ $loan->due_date->format('d M Y') }}
                                    </p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($loan->status)
                                        @case('requested')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                            @break
                                        @case('approved')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Siap Diambil</span>
                                            @break
                                        @case('active')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktif</span>
                                            @break
                                        @case('overdue')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Terlambat</span>
                                            @break
                                        @case('returned')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Dikembalikan</span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                            @break
                                        @case('cancelled')
                                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Dibatalkan</span>
                                            @break
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <div class="flex items-center gap-2">
                                        @if($loan->status === 'requested')
                                        <form action="{{ route('librarian.loans.approve', $loan) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-green-700 bg-green-100 rounded hover:bg-green-200">
                                                Setujui
                                            </button>
                                        </form>
                                        <button onclick="document.getElementById('reject-modal-{{ $loan->id }}').classList.remove('hidden')" class="px-2 py-1 text-xs font-medium text-red-700 bg-red-100 rounded hover:bg-red-200">
                                            Tolak
                                        </button>
                                        @elseif($loan->status === 'approved')
                                        <form action="{{ route('librarian.loans.pickup', $loan) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-blue-700 bg-blue-100 rounded hover:bg-blue-200">
                                                Proses Ambil
                                            </button>
                                        </form>
                                        @elseif(in_array($loan->status, ['active', 'overdue']))
                                        <form action="{{ route('librarian.loans.return', $loan) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2 py-1 text-xs font-medium text-purple-700 bg-purple-100 rounded hover:bg-purple-200">
                                                Kembalikan
                                            </button>
                                        </form>
                                        @endif
                                        <a href="{{ route('librarian.loans.show', $loan) }}" class="px-2 py-1 text-xs font-medium text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            
                            {{-- Reject Modal --}}
                            @if($loan->status === 'requested')
                            <div id="reject-modal-{{ $loan->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Permintaan</h3>
                                    <form action="{{ route('librarian.loans.reject', $loan) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label for="reason-{{ $loan->id }}" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan</label>
                                            <textarea name="reason" id="reason-{{ $loan->id }}" rows="3" class="w-full rounded-md border-gray-300" placeholder="Berikan alasan penolakan..."></textarea>
                                        </div>
                                        <div class="flex justify-end gap-3">
                                            <button type="button" onclick="document.getElementById('reject-modal-{{ $loan->id }}').classList.add('hidden')" class="px-4 py-2 text-gray-700 bg-gray-100 rounded hover:bg-gray-200">
                                                Batal
                                            </button>
                                            <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded hover:bg-red-700">
                                                Tolak
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Tidak ada data peminjaman</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($loans->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $loans->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
