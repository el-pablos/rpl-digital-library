<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Notification;
use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class ReviewService
{
    /**
     * Create a new review.
     */
    public function createReview(User $user, Book $book, int $rating, ?string $reviewText = null): Review
    {
        // Check if user already reviewed this book
        $existingReview = Review::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($existingReview) {
            throw new \Exception('Anda sudah memberikan review untuk buku ini.');
        }

        // Check if user has borrowed this book
        $hasBorrowed = $user->loans()
            ->where('book_id', $book->id)
            ->whereIn('status', ['active', 'returned'])
            ->exists();

        if (!$hasBorrowed) {
            throw new \Exception('Anda hanya dapat memberikan review untuk buku yang pernah Anda pinjam.');
        }

        return Review::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'rating' => $rating,
            'review_text' => $reviewText,
            'status' => Review::STATUS_PENDING,
        ]);
    }

    /**
     * Update an existing review.
     */
    public function updateReview(Review $review, int $rating, ?string $reviewText = null): Review
    {
        $review->update([
            'rating' => $rating,
            'review_text' => $reviewText,
            'status' => Review::STATUS_PENDING, // Re-submit for moderation
        ]);

        return $review->fresh();
    }

    /**
     * Approve a review.
     */
    public function approveReview(Review $review, User $librarian = null): Review
    {
        if ($review->status !== Review::STATUS_PENDING) {
            throw new \Exception('Review ini tidak dalam status pending.');
        }

        $updateData = [
            'status' => Review::STATUS_APPROVED,
            'moderated_at' => now(),
        ];

        if ($librarian) {
            $updateData['moderated_by'] = $librarian->id;
        }

        $review->update($updateData);

        // Notify the reviewer
        Notification::create([
            'user_id' => $review->user_id,
            'type' => Notification::TYPE_REVIEW_APPROVED,
            'message' => "Review Anda untuk buku \"{$review->book->title}\" telah disetujui.",
        ]);

        return $review->fresh();
    }

    /**
     * Reject a review.
     */
    public function rejectReview(Review $review, string $reason = null): Review
    {
        if ($review->status !== Review::STATUS_PENDING) {
            throw new \Exception('Review ini tidak dalam status pending.');
        }

        $review->update([
            'status' => Review::STATUS_REJECTED,
        ]);

        return $review->fresh();
    }

    /**
     * Vote on a review.
     */
    public function voteReview(Review $review, User $user, string $voteType): ReviewVote
    {
        if (!in_array($voteType, ['helpful', 'not_helpful'])) {
            throw new \Exception('Tipe vote tidak valid.');
        }

        if ($review->user_id === $user->id) {
            throw new \Exception('Anda tidak dapat vote review Anda sendiri.');
        }

        // Check if user already voted
        $existingVote = ReviewVote::where('review_id', $review->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote) {
            if ($existingVote->vote_type === $voteType) {
                // Same vote type - toggle (remove the vote)
                $existingVote->delete();
                $this->updateVoteCounts($review);
                return new ReviewVote(['review_id' => $review->id, 'user_id' => $user->id, 'vote_type' => $voteType]);
            }
            // Different vote type - update existing vote
            $existingVote->update(['vote_type' => $voteType]);
            $this->updateVoteCounts($review);
            return $existingVote->fresh();
        }

        // Create new vote
        $vote = ReviewVote::create([
            'review_id' => $review->id,
            'user_id' => $user->id,
            'vote_type' => $voteType,
        ]);

        $this->updateVoteCounts($review);

        return $vote;
    }

    /**
     * Remove vote from a review.
     */
    public function removeVote(Review $review, User $user): bool
    {
        $deleted = ReviewVote::where('review_id', $review->id)
            ->where('user_id', $user->id)
            ->delete();

        if ($deleted) {
            $this->updateVoteCounts($review);
        }

        return $deleted > 0;
    }

    /**
     * Update vote counts on a review.
     */
    protected function updateVoteCounts(Review $review): void
    {
        $review->update([
            'helpful_count' => $review->votes()->where('vote_type', 'helpful')->count(),
            'not_helpful_count' => $review->votes()->where('vote_type', 'not_helpful')->count(),
        ]);
    }

    /**
     * Get reviews for a book.
     */
    public function getBookReviews(Book $book, string $sort = 'newest', int $perPage = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Review::where('book_id', $book->id)
            ->where('status', Review::STATUS_APPROVED)
            ->with('user');

        switch ($sort) {
            case 'helpful':
                $query->orderByDesc('helpful_count');
                break;
            case 'highest':
                $query->orderByDesc('rating');
                break;
            case 'lowest':
                $query->orderBy('rating');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        return $query->paginate($perPage);
    }

    /**
     * Get pending reviews for moderation.
     */
    public function getPendingReviews(): Collection
    {
        return Review::where('status', Review::STATUS_PENDING)
            ->with('user', 'book')
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Get review statistics.
     */
    public function getReviewStatistics(): array
    {
        return [
            'total_approved' => Review::where('status', Review::STATUS_APPROVED)->count(),
            'pending_moderation' => Review::where('status', Review::STATUS_PENDING)->count(),
            'total_rejected' => Review::where('status', Review::STATUS_REJECTED)->count(),
            'average_rating' => round(Review::where('status', Review::STATUS_APPROVED)->avg('rating') ?? 0, 2),
        ];
    }

    /**
     * Get a book's average rating.
     */
    public function getBookAverageRating(Book $book): float
    {
        return round($book->reviews()
            ->where('status', Review::STATUS_APPROVED)
            ->avg('rating') ?? 0, 2);
    }

    /**
     * Get rating distribution for a book.
     */
    public function getBookRatingDistribution(Book $book): array
    {
        $distribution = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = $book->reviews()
                ->where('status', Review::STATUS_APPROVED)
                ->where('rating', $i)
                ->count();
        }

        return $distribution;
    }
}
