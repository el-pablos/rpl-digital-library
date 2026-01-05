<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    /**
     * Display a listing of books.
     */
    public function index(Request $request): View
    {
        $query = Book::with('category');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        $books = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $categories = Category::all();

        return view('admin.books.index', compact('books', 'categories'));
    }

    /**
     * Show the form for creating a new book.
     */
    public function create(): View
    {
        $categories = Category::whereNotNull('parent_id')->get();

        return view('admin.books.create', compact('categories'));
    }

    /**
     * Store a newly created book.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'isbn' => 'required|string|max:20|unique:books,isbn',
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'total_copies' => 'required|integer|min:1',
            'language' => 'nullable|string|max:50',
            'pages' => 'nullable|integer|min:1',
        ]);

        $validated['available_copies'] = $validated['total_copies'];

        Book::create($validated);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Display the specified book.
     */
    public function show(Book $book): View
    {
        $book->load(['category.parent', 'loans' => function ($q) {
            $q->latest()->limit(10);
        }, 'reviews' => function ($q) {
            $q->latest()->limit(5);
        }]);

        return view('admin.books.show', compact('book'));
    }

    /**
     * Show the form for editing the specified book.
     */
    public function edit(Book $book): View
    {
        $categories = Category::whereNotNull('parent_id')->get();

        return view('admin.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified book.
     */
    public function update(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'isbn' => 'required|string|max:20|unique:books,isbn,' . $book->id,
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'publication_year' => 'nullable|integer|min:1000|max:' . (date('Y') + 1),
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'total_copies' => 'required|integer|min:1',
            'language' => 'nullable|string|max:50',
            'pages' => 'nullable|integer|min:1',
        ]);

        // Adjust available copies based on total copies change
        $copyDiff = $validated['total_copies'] - $book->total_copies;
        $validated['available_copies'] = max(0, $book->available_copies + $copyDiff);

        $book->update($validated);

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Remove the specified book.
     */
    public function destroy(Book $book): RedirectResponse
    {
        // Check if book has active loans
        if ($book->loans()->whereIn('status', ['requested', 'approved', 'active'])->exists()) {
            return back()->with('error', 'Buku tidak dapat dihapus karena masih memiliki pinjaman aktif.');
        }

        $book->delete();

        return redirect()
            ->route('admin.books.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
