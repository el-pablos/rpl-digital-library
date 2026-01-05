<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Ulasan') }}
            </h2>
            <a href="{{ route('librarian.reviews.index') }}" class="text-gray-600 hover:text-gray-800">
                ← Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    {{-- Review Header --}}
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-lg font-medium text-gray-600">
                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $review->user->name ?? 'Pengguna Dihapus' }}</p>
                                <p class="text-sm text-gray-500">{{ $review->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <span class="inline-flex px-3 py-1 text-sm font-medium rounded-full
                            @if($review->status === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($review->status === 'approved') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($review->status) }}
                        </span>
                    </div>

                    {{-- Book Info --}}
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="text-sm font-medium text-gray-500 mb-1">Ulasan untuk:</h4>
                        <a href="{{ route('books.show', $review->book) }}" class="text-lg font-semibold text-blue-600 hover:text-blue-800">
                            {{ $review->book->title ?? 'Buku Dihapus' }}
                        </a>
                        @if($review->book)
                        <p class="text-sm text-gray-500">oleh {{ $review->book->author }}</p>
                        @endif
                    </div>

                    {{-- Rating --}}
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Rating</h4>
                        <div class="flex items-center gap-1">
                            @for($i = 1; $i <= 5; $i++)
                            <svg class="w-6 h-6 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            @endfor
                            <span class="ml-2 text-gray-600">({{ $review->rating }}/5)</span>
                        </div>
                    </div>

                    {{-- Comment --}}
                    @if($review->comment)
                    <div class="mb-6">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Komentar</h4>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-700 whitespace-pre-wrap">{{ $review->comment }}</p>
                        </div>
                    </div>
                    @endif

                    {{-- Actions --}}
                    @if($review->status === 'pending')
                    <div class="pt-6 border-t flex items-center gap-3">
                        <form action="{{ route('librarian.reviews.approve', $review) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                                Setujui Ulasan
                            </button>
                        </form>
                        <form action="{{ route('librarian.reviews.reject', $review) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700">
                                Tolak Ulasan
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
