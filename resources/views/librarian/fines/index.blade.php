<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Kelola Denda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                <p class="text-green-700">{{ session('success') }}</p>
            </div>
            @endif

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Total Denda Belum Dibayar</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">Rp {{ number_format($stats['total_unpaid'] ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Pengguna dengan Denda</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['users_with_fines'] ?? 0 }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <p class="text-sm font-medium text-gray-500">Denda Dibayar Bulan Ini</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">Rp {{ number_format($stats['paid_this_month'] ?? 0, 0, ',', '.') }}</p>
                </div>
            </div>

            {{-- Filter Tabs --}}
            <div class="mb-6 border-b border-gray-200 bg-white rounded-t-lg px-6">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('librarian.fines.index') }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Semua
                    </a>
                    <a href="{{ route('librarian.fines.index', ['status' => 'unpaid']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'unpaid' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Belum Dibayar
                    </a>
                    <a href="{{ route('librarian.fines.index', ['status' => 'paid']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'paid' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Sudah Dibayar
                    </a>
                    <a href="{{ route('librarian.fines.index', ['status' => 'waived']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'waived' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Dihapuskan
                    </a>
                </nav>
            </div>

            {{-- Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Buku</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($fines as $fine)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-medium text-gray-600">
                                            {{ strtoupper(substr($fine->loan->user->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ $fine->loan->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $fine->loan->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ Str::limit($fine->loan->book->title, 30) }}</p>
                                    <p class="text-xs text-gray-500">{{ $fine->created_at->format('d M Y') }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($fine->amount, 0, ',', '.') }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $fine->reason ?? 'Keterlambatan' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($fine->status === 'paid')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Dibayar</span>
                                    @elseif($fine->status === 'waived')
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Dihapuskan</span>
                                    @else
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Belum Dibayar</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($fine->status === 'unpaid')
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('librarian.fines.pay', $fine) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800">Tandai Dibayar</button>
                                        </form>
                                        <form action="{{ route('librarian.fines.waive', $fine) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="text-gray-600 hover:text-gray-800" onclick="return confirm('Hapuskan denda ini?')">Hapuskan</button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">Tidak ada data denda</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($fines->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $fines->withQueryString()->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
