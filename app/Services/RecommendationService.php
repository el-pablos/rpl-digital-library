<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Recommendation;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Weight constants for hybrid algorithm.
     * Based on documentation: CB 40%, CF 30%, Pop 20%, Rec 10%
     */
    public const WEIGHT_CONTENT_BASED = 0.40;
    public const WEIGHT_COLLABORATIVE = 0.30;
    public const WEIGHT_POPULARITY = 0.20;
    public const WEIGHT_RECENCY = 0.10;

    /**
     * Generate personalized recommendations for a user.
     */
    public function generateRecommendations(User $user, int $limit = 10): EloquentCollection
    {
        // Get user's borrowing history
        $borrowedBookIds = $user->loans()
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_RETURNED])
            ->pluck('book_id')
            ->toArray();

        // Get user's preferred categories based on borrowing history
        $preferredCategories = $this->getUserPreferredCategories($user);

        // Get user's average rating to understand preference
        $userAvgRating = Review::where('user_id', $user->id)->avg('rating') ?? 4;

        // Calculate scores for all available books
        $books = Book::where('available_copies', '>', 0)
            ->whereNotIn('id', $borrowedBookIds)
            ->with('category', 'reviews')
            ->get();

        $scoredBooks = $books->map(function ($book) use ($preferredCategories, $userAvgRating, $user) {
            $score = $this->calculateHybridScore($book, $preferredCategories, $userAvgRating, $user);
            return [
                'book' => $book,
                'score' => $score,
                'components' => [
                    'content_based' => $this->calculateContentBasedScore($book, $preferredCategories),
                    'collaborative' => $this->calculateCollaborativeScore($book, $user),
                    'popularity' => $this->calculatePopularityScore($book),
                    'recency' => $this->calculateRecencyScore($book),
                ],
            ];
        })->sortByDesc('score')->take($limit);

        // Store recommendations in database
        $this->storeRecommendations($user, $scoredBooks);

        // Return as Eloquent Collection, preserving the score order
        $bookIds = $scoredBooks->pluck('book.id')->toArray();
        if (empty($bookIds)) {
            return new EloquentCollection();
        }
        
        $books = Book::whereIn('id', $bookIds)->get();
        
        // Sort by the original score order and convert back to EloquentCollection
        $sorted = $books->sortBy(function ($book) use ($bookIds) {
            return array_search($book->id, $bookIds);
        })->values();
        
        return new EloquentCollection($sorted->all());
    }

    /**
     * Calculate hybrid score for a book.
     */
    protected function calculateHybridScore(Book $book, array $preferredCategories, float $userAvgRating, User $user): float
    {
        $contentScore = $this->calculateContentBasedScore($book, $preferredCategories);
        $collaborativeScore = $this->calculateCollaborativeScore($book, $user);
        $popularityScore = $this->calculatePopularityScore($book);
        $recencyScore = $this->calculateRecencyScore($book);

        return (
            ($contentScore * self::WEIGHT_CONTENT_BASED) +
            ($collaborativeScore * self::WEIGHT_COLLABORATIVE) +
            ($popularityScore * self::WEIGHT_POPULARITY) +
            ($recencyScore * self::WEIGHT_RECENCY)
        );
    }

    /**
     * Content-based filtering: Match user preferences with book attributes.
     */
    protected function calculateContentBasedScore(Book $book, array $preferredCategories): float
    {
        if (empty($preferredCategories)) {
            return 0.5; // Neutral score if no history
        }

        $categoryId = $book->category_id;
        $parentCategoryId = $book->category?->parent_id;

        // Direct category match
        if (isset($preferredCategories[$categoryId])) {
            return min(1.0, $preferredCategories[$categoryId] * 1.5);
        }

        // Parent category match
        if ($parentCategoryId && isset($preferredCategories[$parentCategoryId])) {
            return min(1.0, $preferredCategories[$parentCategoryId] * 0.8);
        }

        return 0.2; // Low score for unrelated categories
    }

    /**
     * Collaborative filtering: Find similar users and their preferences.
     */
    protected function calculateCollaborativeScore(Book $book, User $user): float
    {
        // Find users who borrowed similar books
        $userBorrowedBooks = $user->loans()
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_RETURNED])
            ->pluck('book_id');

        if ($userBorrowedBooks->isEmpty()) {
            return 0.5; // Neutral for new users
        }

        // Find similar users (those who borrowed the same books)
        $similarUserIds = Loan::whereIn('book_id', $userBorrowedBooks)
            ->where('user_id', '!=', $user->id)
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_RETURNED])
            ->distinct()
            ->pluck('user_id');

        if ($similarUserIds->isEmpty()) {
            return 0.5;
        }

        // Check if similar users borrowed this book
        $similarUserBorrows = Loan::where('book_id', $book->id)
            ->whereIn('user_id', $similarUserIds)
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_RETURNED])
            ->count();

        // Calculate score based on how many similar users borrowed this book
        return min(1.0, $similarUserBorrows / max(1, $similarUserIds->count()));
    }

    /**
     * Popularity-based score: Based on loan count and ratings.
     */
    protected function calculatePopularityScore(Book $book): float
    {
        $loanCount = $book->loans()->count();
        $avgRating = $book->reviews()->avg('rating') ?? 0;
        $reviewCount = $book->reviews()->count();

        // Normalize loan count (assume 100 loans = max popularity)
        $loanScore = min(1.0, $loanCount / 100);

        // Rating score (5-star scale)
        $ratingScore = $avgRating / 5;

        // Weight by number of reviews (more reviews = more reliable)
        $reviewWeight = min(1.0, $reviewCount / 10);
        $weightedRating = ($ratingScore * $reviewWeight) + (0.5 * (1 - $reviewWeight));

        return ($loanScore * 0.5) + ($weightedRating * 0.5);
    }

    /**
     * Recency score: Favor newer publications.
     */
    protected function calculateRecencyScore(Book $book): float
    {
        $currentYear = (int) date('Y');
        $publicationYear = $book->publication_year ?? 2000;
        $age = max(0, $currentYear - $publicationYear);

        // Books less than 2 years old get full score
        // Score decreases for older books
        if ($age <= 2) {
            return 1.0;
        }

        return max(0.1, 1.0 - ($age - 2) * 0.05);
    }

    /**
     * Get user's preferred categories based on borrowing history.
     */
    protected function getUserPreferredCategories(User $user): array
    {
        $categoryScores = [];

        $borrowedBooks = $user->loans()
            ->with('book.category')
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_RETURNED])
            ->get()
            ->pluck('book')
            ->filter();

        $total = $borrowedBooks->count();
        if ($total === 0) {
            return [];
        }

        foreach ($borrowedBooks as $book) {
            $categoryId = $book->category_id;
            if ($categoryId) {
                $categoryScores[$categoryId] = ($categoryScores[$categoryId] ?? 0) + 1;
            }

            // Also count parent category
            $parentId = $book->category?->parent_id;
            if ($parentId) {
                $categoryScores[$parentId] = ($categoryScores[$parentId] ?? 0) + 0.5;
            }
        }

        // Normalize scores
        foreach ($categoryScores as $categoryId => $count) {
            $categoryScores[$categoryId] = $count / $total;
        }

        return $categoryScores;
    }

    /**
     * Store recommendations in database.
     */
    protected function storeRecommendations(User $user, $scoredBooks): void
    {
        // Clear old recommendations
        Recommendation::where('user_id', $user->id)->delete();

        foreach ($scoredBooks as $item) {
            // Store one recommendation per type with the component score
            foreach ($item['components'] as $type => $score) {
                Recommendation::create([
                    'user_id' => $user->id,
                    'book_id' => $item['book']->id,
                    'type' => $type,
                    'score' => $score,
                ]);
            }
        }
    }

    /**
     * Get cached recommendations for a user.
     */
    public function getCachedRecommendations(User $user, int $limit = 10): EloquentCollection
    {
        $recommendations = Recommendation::where('user_id', $user->id)
            ->with('book.category', 'book.reviews')
            ->orderByDesc('score')
            ->limit($limit)
            ->get();

        // If no cached recommendations, generate new ones
        if ($recommendations->isEmpty()) {
            return $this->generateRecommendations($user, $limit);
        }

        // pluck returns Support\Collection, convert to Eloquent\Collection
        return new EloquentCollection($recommendations->pluck('book')->all());
    }

    /**
     * Get trending books (most borrowed in last 30 days).
     */
    public function getTrendingBooks(int $limit = 10): EloquentCollection
    {
        return Book::withCount(['loans as recent_loans' => function ($query) {
            $query->where('created_at', '>=', now()->subDays(30));
        }])
            ->where('available_copies', '>', 0)
            ->orderByDesc('recent_loans')
            ->limit($limit)
            ->get();
    }

    /**
     * Get top rated books.
     */
    public function getTopRatedBooks(int $limit = 10): EloquentCollection
    {
        return Book::withAvg('reviews as avg_rating', 'rating')
            ->withCount('reviews')
            ->whereHas('reviews', function ($query) {
                // Subquery to filter books with at least 3 reviews
            }, '>=', 3) // Minimum 3 reviews
            ->where('available_copies', '>', 0)
            ->orderByDesc('avg_rating')
            ->limit($limit)
            ->get();
    }

    /**
     * Get new arrivals.
     */
    public function getNewArrivals(int $limit = 10): EloquentCollection
    {
        return Book::where('available_copies', '>', 0)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
