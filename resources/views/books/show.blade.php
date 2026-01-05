<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Book Detail --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 md:p-8">
                    <div class="md:flex gap-8">
                        {{-- Cover Image --}}
                        <div class="md:w-1/3 mb-6 md:mb-0">
                            <div class="aspect-[3/4] bg-gray-100 rounded-lg overflow-hidden">
                                @if($book->cover_image)
                                <img src="{{ asset('storage/' . $book->cover_image) }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="h-24 w-24 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Book Info --}}
                        <div class="md:w-2/3">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $book->title }}</h1>
                            <p class="text-xl text-gray-600 mt-2">oleh {{ $book->author }}</p>

                            {{-- Rating --}}
                            <div class="flex items-center mt-4">
                                @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= $book->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                @endfor
                                <span class="ml-2 text-gray-600">
                                    {{ number_format($book->average_rating, 1) }} ({{ $book->reviews_count ?? $book->reviews->count() }} ulasan)
                                </span>
                            </div>

                            {{-- Availability Badge --}}
                            <div class="mt-4">
                                @if($book->isAvailable())
                                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-full">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $book->available_copies }} dari {{ $book->total_copies }} tersedia
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-red-700 bg-red-100 rounded-full">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                    Sedang dipinjam semua
                                </span>
                                @endif
                            </div>

                            {{-- Book Metadata --}}
                            <dl class="mt-6 grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">ISBN</dt>
                                    <dd class="text-sm text-gray-900">{{ $book->isbn ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Penerbit</dt>
                                    <dd class="text-sm text-gray-900">{{ $book->publisher ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tahun Terbit</dt>
                                    <dd class="text-sm text-gray-900">{{ $book->publication_year ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                    <dd class="text-sm text-gray-900">{{ $book->category->name ?? '-' }}</dd>
                                </div>
                            </dl>

                            {{-- Borrow Button --}}
                            @auth
                            <div class="mt-8">
                                @if($book->isAvailable() && auth()->user()->canBorrow())
                                    @if($hasActiveLoan)
                                    <p class="text-sm text-yellow-600">Anda sudah meminjam buku ini.</p>
                                    @elseif($hasPendingRequest)
                                    <p class="text-sm text-yellow-600">Anda memiliki permintaan yang sedang diproses untuk buku ini.</p>
                                    @else
                                    <form action="{{ route('loans.store', $book) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                            Ajukan Peminjaman
                                        </button>
                                    </form>
                                    @endif
                                @elseif(!auth()->user()->canBorrow())
                                <p class="text-sm text-red-600">Anda tidak dapat meminjam buku saat ini. Mungkin Anda memiliki denda yang belum dibayar atau sudah mencapai batas peminjaman.</p>
                                @else
                                <p class="text-sm text-gray-600">Buku tidak tersedia untuk dipinjam saat ini.</p>
                                @endif
                            </div>
                            @else
                            <div class="mt-8">
                                <a href="{{ route('login') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700">
                                    Login untuk Meminjam
                                </a>
                            </div>
                            @endauth
                        </div>
                    </div>

                    {{-- Description --}}
                    @if($book->description)
                    <div class="mt-8 pt-8 border-t">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Deskripsi</h2>
                        <div class="prose max-w-none text-gray-700">
                            {!! nl2br(e($book->description)) !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Reviews Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Ulasan</h2>

                    {{-- Write Review Form --}}
                    @auth
                    @if($canReview)
                    <div class="mb-8 p-6 bg-gray-50 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Tulis Ulasan</h3>
                        <form action="{{ route('reviews.store', $book) }}" method="POST">
                            @csrf
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                <div class="flex items-center gap-2" x-data="{ rating: 0, hover: 0 }">
                                    @for($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" required>
                                        <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </label>
                                    @endfor
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="review_text" class="block text-sm font-medium text-gray-700 mb-2">Ulasan</label>
                                <textarea name="review_text" id="review_text" rows="4" 
                                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                          placeholder="Bagikan pendapat Anda tentang buku ini..." required></textarea>
                            </div>

                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                                Kirim Ulasan
                            </button>
                        </form>
                    </div>
                    @endif
                    @endauth

                    {{-- Reviews List --}}
                    <div class="space-y-6">
                        @forelse($reviews as $review)
                        <div class="border-b pb-6 last:border-0">
                            <div class="flex items-start justify-between">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $review->user->name }}</p>
                                    <div class="flex items-center mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        @endfor
                                        <span class="ml-2 text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 text-gray-700">{{ $review->review_text }}</p>
                            
                            {{-- Vote Buttons --}}
                            @auth
                            <div class="mt-4 flex items-center gap-4 text-sm">
                                <form action="{{ route('reviews.vote', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="vote_type" value="helpful">
                                    <button type="submit" class="flex items-center text-gray-500 hover:text-green-600">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                        </svg>
                                        Membantu ({{ $review->helpful_count }})
                                    </button>
                                </form>
                                <form action="{{ route('reviews.vote', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="vote_type" value="not_helpful">
                                    <button type="submit" class="flex items-center text-gray-500 hover:text-red-600">
                                        <svg class="w-4 h-4 mr-1 transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                        </svg>
                                        ({{ $review->not_helpful_count }})
                                    </button>
                                </form>
                            </div>
                            @endauth
                        </div>
                        @empty
                        <p class="text-gray-500 text-center py-8">Belum ada ulasan untuk buku ini.</p>
                        @endforelse
                    </div>

                    {{-- Pagination --}}
                    @if($reviews->hasPages())
                    <div class="mt-6">
                        {{ $reviews->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
