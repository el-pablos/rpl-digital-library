<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Buku') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.books.edit', $book) }}" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                    Edit
                </a>
                <a href="{{ route('admin.books.index') }}" class="text-gray-600 hover:text-gray-800">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Book Info Card --}}
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        @if($book->cover_image)
                        <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-64 object-cover rounded-lg mb-4">
                        @else
                        <div class="w-full h-64 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg mb-4 flex items-center justify-center">
                            <svg class="w-24 h-24 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                        @endif

                        <h3 class="text-xl font-bold text-gray-900">{{ $book->title }}</h3>
                        <p class="text-gray-600">oleh {{ $book->author }}</p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($book->category)
                            <span class="inline-flex px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                {{ $book->category->name }}
                            </span>
                            @endif
                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full {{ $book->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $book->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <div class="mt-6 pt-6 border-t space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">ISBN</span>
                                <span class="font-medium text-gray-900">{{ $book->isbn ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Penerbit</span>
                                <span class="font-medium text-gray-900">{{ $book->publisher ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tahun Terbit</span>
                                <span class="font-medium text-gray-900">{{ $book->publication_year ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total Eksemplar</span>
                                <span class="font-medium text-gray-900">{{ $book->total_copies }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tersedia</span>
                                <span class="font-medium {{ $book->available_copies > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $book->available_copies }}
                                </span>
                            </div>
                        </div>

                        @if($book->description)
                        <div class="mt-6 pt-6 border-t">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Deskripsi</h4>
                            <p class="text-sm text-gray-700">{{ $book->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Loans & Reviews --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Statistics --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $book->loans->count() }}</div>
                            <div class="text-sm text-gray-500">Total Peminjaman</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $book->loans->where('status', 'active')->count() }}</div>
                            <div class="text-sm text-gray-500">Sedang Dipinjam</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 text-center">
                            <div class="text-2xl font-bold text-yellow-600">
                                @if($book->reviews->count() > 0)
                                    {{ number_format($book->reviews->avg('rating'), 1) }} ★
                                @else
                                    -
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">Rating ({{ $book->reviews->count() }} ulasan)</div>
                        </div>
                    </div>

                    {{-- Recent Loans --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Peminjaman Terakhir</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Peminjam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($book->loans as $loan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $loan->user->name ?? 'User Dihapus' }}</div>
                                            <div class="text-xs text-gray-500">{{ $loan->user->email ?? '' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $loan->borrowed_at->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                                @if($loan->status === 'active') bg-green-100 text-green-800
                                                @elseif($loan->status === 'overdue') bg-red-100 text-red-800
                                                @elseif($loan->status === 'returned') bg-gray-100 text-gray-800
                                                @else bg-yellow-100 text-yellow-800
                                                @endif">
                                                {{ ucfirst($loan->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                            Belum ada riwayat peminjaman
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Reviews --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Ulasan Terbaru</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @forelse($book->reviews as $review)
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $review->user->name ?? 'Anonim' }}</h4>
                                        <div class="flex items-center mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                @if($review->comment)
                                <p class="mt-2 text-sm text-gray-600">{{ $review->comment }}</p>
                                @endif
                            </div>
                            @empty
                            <div class="p-6 text-center text-gray-500">
                                Belum ada ulasan
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
