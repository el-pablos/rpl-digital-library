<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($book) ? __('Edit Buku') : __('Tambah Buku') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8">
                    <form action="{{ isset($book) ? route('admin.books.update', $book) : route('admin.books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if(isset($book))
                        @method('PUT')
                        @endif

                        <div class="space-y-6">
                            {{-- Title --}}
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700">Judul Buku <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="title" value="{{ old('title', $book->title ?? '') }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Author --}}
                            <div>
                                <label for="author" class="block text-sm font-medium text-gray-700">Penulis <span class="text-red-500">*</span></label>
                                <input type="text" name="author" id="author" value="{{ old('author', $book->author ?? '') }}" required
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('author')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- ISBN --}}
                            <div>
                                <label for="isbn" class="block text-sm font-medium text-gray-700">ISBN</label>
                                <input type="text" name="isbn" id="isbn" value="{{ old('isbn', $book->isbn ?? '') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('isbn')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Category --}}
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori <span class="text-red-500">*</span></label>
                                <select name="category_id" id="category_id" required
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id', $book->category_id ?? '') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- Publisher --}}
                                <div>
                                    <label for="publisher" class="block text-sm font-medium text-gray-700">Penerbit</label>
                                    <input type="text" name="publisher" id="publisher" value="{{ old('publisher', $book->publisher ?? '') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('publisher')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                {{-- Publication Year --}}
                                <div>
                                    <label for="publication_year" class="block text-sm font-medium text-gray-700">Tahun Terbit</label>
                                    <input type="number" name="publication_year" id="publication_year" value="{{ old('publication_year', $book->publication_year ?? '') }}" min="1900" max="{{ date('Y') }}"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('publication_year')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- Total Copies --}}
                                <div>
                                    <label for="total_copies" class="block text-sm font-medium text-gray-700">Jumlah Eksemplar <span class="text-red-500">*</span></label>
                                    <input type="number" name="total_copies" id="total_copies" value="{{ old('total_copies', $book->total_copies ?? 1) }}" min="1" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('total_copies')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                {{-- Available Copies --}}
                                <div>
                                    <label for="available_copies" class="block text-sm font-medium text-gray-700">Eksemplar Tersedia <span class="text-red-500">*</span></label>
                                    <input type="number" name="available_copies" id="available_copies" value="{{ old('available_copies', $book->available_copies ?? 1) }}" min="0" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('available_copies')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                                <textarea name="description" id="description" rows="4"
                                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $book->description ?? '') }}</textarea>
                                @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Cover Image --}}
                            <div>
                                <label for="cover_image" class="block text-sm font-medium text-gray-700">Cover Buku</label>
                                @if(isset($book) && $book->cover_image)
                                <div class="mt-2 mb-4">
                                    <img src="{{ asset('storage/' . $book->cover_image) }}" alt="" class="h-32 rounded">
                                </div>
                                @endif
                                <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('cover_image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Active Status --}}
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $book->is_active ?? true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-700">Aktif (dapat dipinjam)</label>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-between pt-6 border-t">
                            <a href="{{ route('admin.books.index') }}" class="text-gray-600 hover:text-gray-800">← Kembali</a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700">
                                {{ isset($book) ? 'Simpan Perubahan' : 'Tambah Buku' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
