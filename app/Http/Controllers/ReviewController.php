<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(
        protected ReviewService $reviewService
    ) {}

    /**
     * Store a new review.
     */
    public function store(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:2000',
        ]);

        try {
            $this->reviewService->createReview(
                auth()->user(),
                $book,
                $validated['rating'],
                $validated['review_text'] ?? null
            );

            return back()->with('success', 'Review berhasil dikirim. Menunggu moderasi.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update an existing review.
     */
    public function update(Request $request, Review $review): RedirectResponse
    {
        $this->authorize('update', $review);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'nullable|string|max:2000',
        ]);

        try {
            $this->reviewService->updateReview(
                $review,
                $validated['rating'],
                $validated['review_text'] ?? null
            );

            return back()->with('success', 'Review berhasil diperbarui. Menunggu moderasi ulang.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a review.
     */
    public function destroy(Review $review): RedirectResponse
    {
        $this->authorize('delete', $review);

        $review->delete();

        return back()->with('success', 'Review berhasil dihapus.');
    }

    /**
     * Vote on a review.
     */
    public function vote(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'vote_type' => 'required|in:helpful,not_helpful',
        ]);

        try {
            $this->reviewService->voteReview(
                $review,
                auth()->user(),
                $validated['vote_type']
            );

            return back()->with('success', 'Terima kasih atas feedback Anda.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove vote from a review.
     */
    public function removeVote(Review $review): RedirectResponse
    {
        $this->reviewService->removeVote($review, auth()->user());

        return back()->with('success', 'Vote berhasil dihapus.');
    }

    /**
     * Display user's reviews.
     */
    public function userReviews(): View
    {
        $reviews = auth()->user()->reviews()
            ->with('book')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('reviews.user-reviews', compact('reviews'));
    }
}
