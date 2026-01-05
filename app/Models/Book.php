<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'isbn',
        'title',
        'author',
        'publisher',
        'publication_year',
        'category_id',
        'description',
        'cover_image',
        'total_copies',
        'available_copies',
        'language',
        'pages',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'pages' => 'integer',
    ];

    /**
     * Get the category that the book belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all loans for this book.
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get all reviews for this book.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get approved reviews only.
     */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    /**
     * Get all recommendations for this book.
     */
    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    /**
     * Calculate average rating from approved reviews.
     */
    public function getAverageRatingAttribute(): ?float
    {
        $avg = $this->approvedReviews()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }

    /**
     * Get total review count.
     */
    public function getReviewCountAttribute(): int
    {
        return $this->approvedReviews()->count();
    }

    /**
     * Check if the book is available for borrowing.
     */
    public function isAvailable(): bool
    {
        return $this->available_copies > 0;
    }

    /**
     * Decrease available copies when a loan is approved.
     */
    public function decreaseAvailableCopies(): void
    {
        if ($this->available_copies > 0) {
            $this->decrement('available_copies');
        }
    }

    /**
     * Increase available copies when a book is returned.
     */
    public function increaseAvailableCopies(): void
    {
        if ($this->available_copies < $this->total_copies) {
            $this->increment('available_copies');
        }
    }

    /**
     * Scope for searching books.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('title', 'like', "%{$keyword}%")
              ->orWhere('author', 'like', "%{$keyword}%")
              ->orWhere('isbn', 'like', "%{$keyword}%")
              ->orWhere('description', 'like', "%{$keyword}%");
        });
    }

    /**
     * Scope for filtering by category.
     */
    public function scopeInCategory(Builder $query, ?int $categoryId): Builder
    {
        if (empty($categoryId)) {
            return $query;
        }

        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for available books only.
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('available_copies', '>', 0);
    }

    /**
     * Scope for popular books (most borrowed).
     */
    public function scopePopular(Builder $query): Builder
    {
        return $query->withCount(['loans' => function ($q) {
            $q->whereIn('status', ['active', 'returned']);
        }])->orderByDesc('loans_count');
    }

    /**
     * Scope for newest books.
     */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Scope for filtering by publication year.
     */
    public function scopePublishedIn(Builder $query, ?int $year): Builder
    {
        if (empty($year)) {
            return $query;
        }

        return $query->where('publication_year', $year);
    }
}
