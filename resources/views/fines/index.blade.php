<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Denda Saya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Summary Card --}}
            @if($totalUnpaid > 0)
            <div class="bg-red-50 border-l-4 border-red-400 p-6 mb-8 rounded">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-red-800">Total Denda Belum Dibayar</h3>
                        <p class="text-3xl font-bold text-red-900 mt-2">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</p>
                    </div>
                    <form action="{{ route('fines.pay-all') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700" onclick="return confirm('Bayar semua denda sebesar Rp {{ number_format($totalUnpaid, 0, ',', '.') }}?')">
                            Bayar Semua
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-green-50 border-l-4 border-green-400 p-6 mb-8 rounded">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-800 font-medium">Tidak ada denda yang harus dibayar. Terima kasih!</p>
                </div>
            </div>
            @endif

            {{-- Fines Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Denda</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Buku
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Jumlah
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Alasan
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
                            @forelse($fines as $fine)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ Str::limit($fine->loan->book->title, 40) }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $fine->created_at->format('d M Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">
                                        Rp {{ number_format($fine->amount, 0, ',', '.') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $fine->reason ?? 'Keterlambatan pengembalian' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($fine->status === 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Dibayar
                                    </span>
                                    @elseif($fine->status === 'waived')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Dihapuskan
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Belum Dibayar
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($fine->status === 'unpaid')
                                    <form action="{{ route('fines.pay', $fine) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-blue-600 hover:text-blue-800" onclick="return confirm('Bayar denda Rp {{ number_format($fine->amount, 0, ',', '.') }}?')">
                                            Bayar
                                        </button>
                                    </form>
                                    @else
                                    <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <p class="text-sm font-medium">Tidak ada riwayat denda</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($fines->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $fines->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
