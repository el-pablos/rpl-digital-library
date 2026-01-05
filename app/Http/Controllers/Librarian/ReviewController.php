<?php

namespace App\Http\Controllers\Librarian;

use App\Http\Controllers\Controller;
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
     * Display reviews for moderation.
     */
    public function index(Request $request): View
    {
        $query = Review::with('user', 'book');

        // Status filter (default to pending)
        $status = $request->input('status', 'pending');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Search
        if ($search = $request->input('search')) {
            $query->whereHas('book', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $reviews = $query->orderBy('created_at')->paginate(15)->withQueryString();
        
        // Add counts for tabs
        $counts = [
            'pending' => Review::where('status', 'pending')->count(),
        ];

        return view('librarian.reviews.index', compact('reviews', 'status', 'counts'));
    }

    /**
     * Approve a review.
     */
    public function approve(Review $review): RedirectResponse
    {
        try {
            $this->reviewService->approveReview($review);
            
            return back()->with('success', 'Review berhasil disetujui.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Reject a review.
     */
    public function reject(Request $request, Review $review): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $this->reviewService->rejectReview($review, $validated['reason'] ?? null);
            
            return back()->with('success', 'Review berhasil ditolak.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show review details.
     */
    public function show(Review $review): View
    {
        $review->load(['user', 'book', 'votes']);

        return view('librarian.reviews.show', compact('review'));
    }
}
