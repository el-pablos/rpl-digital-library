<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pengguna') }}
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}" class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                    Edit
                </a>
                <a href="{{ route('admin.users.index') }}" class="text-gray-600 hover:text-gray-800">
                    ← Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- User Profile Card --}}
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-center">
                            <div class="w-24 h-24 mx-auto bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                <span class="text-3xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                            </div>
                            <h3 class="mt-4 text-xl font-semibold text-gray-900">{{ $user->name }}</h3>
                            <p class="text-gray-500">{{ $user->email }}</p>
                            
                            <div class="mt-4 flex justify-center gap-2">
                                @foreach($user->roles as $role)
                                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full
                                    @if($role->name === 'admin') bg-red-100 text-red-800
                                    @elseif($role->name === 'librarian') bg-purple-100 text-purple-800
                                    @else bg-blue-100 text-blue-800
                                    @endif">
                                    {{ ucfirst($role->name) }}
                                </span>
                                @endforeach
                                
                                <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full
                                    {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($user->status ?? 'active') }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t space-y-4">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Telepon</span>
                                <span class="text-sm font-medium text-gray-900">{{ $user->phone ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Bergabung</span>
                                <span class="text-sm font-medium text-gray-900">{{ $user->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-500">Verifikasi Email</span>
                                <span class="text-sm font-medium {{ $user->email_verified_at ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $user->email_verified_at ? 'Terverifikasi' : 'Belum' }}
                                </span>
                            </div>
                        </div>

                        @if($user->address)
                        <div class="mt-6 pt-6 border-t">
                            <h4 class="text-sm font-medium text-gray-500">Alamat</h4>
                            <p class="mt-2 text-sm text-gray-900">{{ $user->address }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Statistics & Activity --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Statistics Cards --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_loans'] }}</div>
                            <div class="text-sm text-gray-500">Total Peminjaman</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">{{ $statistics['active_loans'] }}</div>
                            <div class="text-sm text-gray-500">Peminjaman Aktif</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <div class="text-2xl font-bold text-purple-600">{{ $statistics['total_reviews'] }}</div>
                            <div class="text-sm text-gray-500">Total Ulasan</div>
                        </div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4">
                            <div class="text-2xl font-bold {{ $statistics['unpaid_fines'] > 0 ? 'text-red-600' : 'text-gray-600' }}">
                                Rp {{ number_format($statistics['unpaid_fines'], 0, ',', '.') }}
                            </div>
                            <div class="text-sm text-gray-500">Denda Belum Dibayar</div>
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
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Buku</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Pinjam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($user->loans as $loan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $loan->book->title ?? 'Buku Dihapus' }}
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

                    {{-- Recent Reviews --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="px-6 py-4 border-b">
                            <h3 class="text-lg font-semibold text-gray-900">Ulasan Terakhir</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @forelse($user->reviews as $review)
                            <div class="p-6">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900">{{ $review->book->title ?? 'Buku Dihapus' }}</h4>
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
