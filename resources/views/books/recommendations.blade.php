<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Rekomendasi untuk Anda') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Personalized Recommendations --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Buku yang Mungkin Anda Suka</h3>
                    <p class="text-sm text-gray-500 mb-6">Rekomendasi berdasarkan riwayat peminjaman dan preferensi Anda.</p>
                    
                    @if($recommendations->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($recommendations as $book)
                        <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <a href="{{ route('books.show', $book) }}" class="block">
                                <div class="aspect-[3/4] bg-gray-100 flex items-center justify-center">
                                    @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h4 class="font-medium text-gray-900 line-clamp-2">{{ $book->title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ $book->author }}</p>
                                    <div class="flex items-center justify-between mt-3">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            <span class="text-sm text-gray-600 ml-1">{{ number_format($book->average_rating, 1) }}</span>
                                        </div>
                                        @if($book->isAvailable())
                                        <span class="text-xs text-green-600">Tersedia</span>
                                        @else
                                        <span class="text-xs text-red-600">Habis</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-gray-500">Belum ada rekomendasi. Pinjam lebih banyak buku untuk mendapatkan rekomendasi yang lebih akurat!</p>
                    @endif
                </div>
            </div>

            {{-- Trending Books --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Sedang Tren</h3>
                    <p class="text-sm text-gray-500 mb-6">Buku yang paling banyak dipinjam dalam 30 hari terakhir.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($trending as $book)
                        <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <a href="{{ route('books.show', $book) }}" class="block">
                                <div class="aspect-[3/4] bg-gray-100 flex items-center justify-center">
                                    @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h4 class="font-medium text-gray-900 line-clamp-2">{{ $book->title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ $book->author }}</p>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Top Rated Books --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rating Tertinggi</h3>
                    <p class="text-sm text-gray-500 mb-6">Buku dengan rating terbaik dari pembaca.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($topRated as $book)
                        <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <a href="{{ route('books.show', $book) }}" class="block">
                                <div class="aspect-[3/4] bg-gray-100 flex items-center justify-center">
                                    @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h4 class="font-medium text-gray-900 line-clamp-2">{{ $book->title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ $book->author }}</p>
                                    <div class="flex items-center mt-2">
                                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        <span class="text-sm text-gray-600 ml-1">{{ number_format($book->average_rating, 1) }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- New Arrivals --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Buku Terbaru</h3>
                    <p class="text-sm text-gray-500 mb-6">Buku-buku yang baru ditambahkan ke koleksi.</p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($newArrivals as $book)
                        <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <a href="{{ route('books.show', $book) }}" class="block">
                                <div class="aspect-[3/4] bg-gray-100 flex items-center justify-center relative">
                                    <span class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded">Baru</span>
                                    @if($book->cover_image)
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                    @else
                                    <svg class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <h4 class="font-medium text-gray-900 line-clamp-2">{{ $book->title }}</h4>
                                    <p class="text-sm text-gray-500 mt-1">{{ $book->author }}</p>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
