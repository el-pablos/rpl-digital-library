<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    /**
     * Review status constants.
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'book_id',
        'rating',
        'review_text',
        'helpful_count',
        'not_helpful_count',
        'status',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'rating' => 'integer',
        'helpful_count' => 'integer',
        'not_helpful_count' => 'integer',
        'moderated_at' => 'datetime',
    ];

    /**
     * Get the user who wrote the review.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the book being reviewed.
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get all votes for this review.
     */
    public function votes(): HasMany
    {
        return $this->hasMany(ReviewVote::class);
    }

    /**
     * Check if a user has already voted on this review.
     */
    public function hasVotedBy(int $userId): bool
    {
        return $this->votes()->where('user_id', $userId)->exists();
    }

    /**
     * Get the vote type by a specific user.
     */
    public function getVoteByUser(int $userId): ?string
    {
        $vote = $this->votes()->where('user_id', $userId)->first();
        return $vote?->vote_type;
    }

    /**
     * Update helpful/not helpful counts based on votes.
     */
    public function updateVoteCounts(): void
    {
        $this->helpful_count = $this->votes()->where('vote_type', 'helpful')->count();
        $this->not_helpful_count = $this->votes()->where('vote_type', 'not_helpful')->count();
        $this->save();
    }

    /**
     * Check if the review is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the review is pending moderation.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Approve the review.
     */
    public function approve(): void
    {
        $this->status = 'approved';
        $this->save();
    }

    /**
     * Reject the review.
     */
    public function reject(): void
    {
        $this->status = 'rejected';
        $this->save();
    }

    /**
     * Scope for approved reviews.
     */
    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending reviews.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for ordering by most helpful.
     */
    public function scopeMostHelpful(Builder $query): Builder
    {
        return $query->orderByDesc('helpful_count');
    }

    /**
     * Scope for ordering by newest.
     */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Scope for ordering by highest rating.
     */
    public function scopeHighestRating(Builder $query): Builder
    {
        return $query->orderByDesc('rating');
    }

    /**
     * Scope for ordering by lowest rating.
     */
    public function scopeLowestRating(Builder $query): Builder
    {
        return $query->orderBy('rating');
    }
}
