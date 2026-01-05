<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Moderasi Review') }}
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
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('librarian.reviews.index') }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ !request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Pending ({{ $counts['pending'] ?? 0 }})
                    </a>
                    <a href="{{ route('librarian.reviews.index', ['status' => 'approved']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'approved' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Disetujui
                    </a>
                    <a href="{{ route('librarian.reviews.index', ['status' => 'rejected']) }}" 
                       class="py-4 px-1 border-b-2 font-medium text-sm {{ request('status') == 'rejected' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                        Ditolak
                    </a>
                </nav>
            </div>

            {{-- Reviews List --}}
            <div class="space-y-6">
                @forelse($reviews as $review)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-4 mb-3">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
                                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="font-medium text-gray-900">{{ $review->user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        @endfor
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <p class="text-sm text-gray-500">Review untuk:</p>
                                    <a href="{{ route('books.show', $review->book) }}" class="font-medium text-blue-600 hover:text-blue-800">{{ $review->book->title }}</a>
                                </div>
                                
                                <p class="text-gray-700">{{ $review->review_text }}</p>
                            </div>
                            
                            <div class="ml-6">
                                @if($review->status === 'pending')
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($review->status === 'approved')
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Disetujui</span>
                                @else
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Ditolak</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($review->status === 'pending')
                        <div class="mt-4 pt-4 border-t flex items-center gap-3">
                            <form action="{{ route('librarian.reviews.approve', $review) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-green-700 bg-green-100 rounded hover:bg-green-200">
                                    Setujui
                                </button>
                            </form>
                            <form action="{{ route('librarian.reviews.reject', $review) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-red-700 bg-red-100 rounded hover:bg-red-200">
                                    Tolak
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center text-gray-500">
                        <p class="text-sm font-medium">Tidak ada review</p>
                    </div>
                </div>
                @endforelse
            </div>

            @if($reviews->hasPages())
            <div class="mt-6">
                {{ $reviews->withQueryString()->links() }}
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
