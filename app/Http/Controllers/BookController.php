<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Services\RecommendationService;
use App\Services\ReviewService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookController extends Controller
{
    public function __construct(
        protected RecommendationService $recommendationService,
        protected ReviewService $reviewService
    ) {}

    /**
     * Display catalog of books.
     */
    public function index(Request $request): View
    {
        $query = Book::query()->with('category');

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($categoryId = $request->input('category')) {
            $category = Category::find($categoryId);
            if ($category) {
                // Include child categories
                $categoryIds = $category->descendants()->pluck('id')->push($category->id);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        // Language filter
        if ($language = $request->input('language')) {
            $query->where('language', $language);
        }

        // Availability filter
        if ($request->input('available_only')) {
            $query->where('available_copies', '>', 0);
        }

        // Year filter
        if ($yearFrom = $request->input('year_from')) {
            $query->where('publication_year', '>=', $yearFrom);
        }
        if ($yearTo = $request->input('year_to')) {
            $query->where('publication_year', '<=', $yearTo);
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'title_asc':
                $query->orderBy('title');
                break;
            case 'title_desc':
                $query->orderByDesc('title');
                break;
            case 'author':
                $query->orderBy('author');
                break;
            case 'year_newest':
                $query->orderByDesc('publication_year');
                break;
            case 'year_oldest':
                $query->orderBy('publication_year');
                break;
            case 'popular':
                $query->withCount('loans')->orderByDesc('loans_count');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $books = $query->paginate(12)->withQueryString();
        $categories = Category::whereNull('parent_id')->with('children')->get();
        $languages = Book::distinct()->pluck('language')->filter();

        return view('books.index', compact('books', 'categories', 'languages'));
    }

    /**
     * Display a specific book.
     */
    public function show(Book $book): View
    {
        $book->load(['category.parent', 'reviews' => function ($query) {
            $query->where('status', 'approved')
                ->with('user')
                ->orderByDesc('helpful_count')
                ->limit(5);
        }]);

        $reviews = $this->reviewService->getBookReviews($book, 'helpful');
        $avgRating = $this->reviewService->getBookAverageRating($book);
        $ratingDistribution = $this->reviewService->getBookRatingDistribution($book);

        // Get related books from same category
        $relatedBooks = Book::where('category_id', $book->category_id)
            ->where('id', '!=', $book->id)
            ->where('available_copies', '>', 0)
            ->limit(4)
            ->get();

        // Check if current user can review
        $canReview = false;
        $userReview = null;
        $hasActiveLoan = false;
        $hasPendingRequest = false;
        if (auth()->check()) {
            $user = auth()->user();
            $hasBorrowed = $user->loans()
                ->where('book_id', $book->id)
                ->whereIn('status', ['active', 'returned'])
                ->exists();
            $hasActiveLoan = $user->loans()
                ->where('book_id', $book->id)
                ->whereIn('status', ['active'])
                ->exists();
            $hasPendingRequest = $user->loans()
                ->where('book_id', $book->id)
                ->whereIn('status', ['requested', 'approved'])
                ->exists();
            $userReview = $book->reviews()->where('user_id', $user->id)->first();
            $canReview = $hasBorrowed && !$userReview;
        }

        return view('books.show', compact(
            'book',
            'reviews',
            'avgRating',
            'ratingDistribution',
            'relatedBooks',
            'canReview',
            'userReview',
            'hasActiveLoan',
            'hasPendingRequest'
        ));
    }

    /**
     * Display recommendations for authenticated user.
     */
    public function recommendations(): View
    {
        $user = auth()->user();
        
        $recommendations = $this->recommendationService->getCachedRecommendations($user);
        $trending = $this->recommendationService->getTrendingBooks(8);
        $topRated = $this->recommendationService->getTopRatedBooks(8);
        $newArrivals = $this->recommendationService->getNewArrivals(8);

        return view('books.recommendations', compact(
            'recommendations',
            'trending',
            'topRated',
            'newArrivals'
        ));
    }
}
