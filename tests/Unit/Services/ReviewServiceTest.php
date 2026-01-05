<?php

namespace Tests\Unit\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Review;
use App\Models\ReviewVote;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReviewService $reviewService;
    protected User $member;
    protected Book $book;
    protected Loan $loan;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->reviewService = app(ReviewService::class);
        
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
        
        $this->member = User::factory()->create(['status' => 'active']);
        
        $this->book = Book::create([
            'isbn' => '978-1234567890',
            'title' => 'Test Book',
            'author' => 'Test Author',
            'category_id' => $category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        // Create a returned loan (member has read the book)
        $this->loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_RETURNED,
            'request_date' => now()->subDays(20),
            'pickup_date' => now()->subDays(18),
            'due_date' => now()->subDays(11),
            'return_date' => now()->subDays(5),
        ]);
    }

    public function test_can_create_review(): void
    {
        $review = $this->reviewService->createReview(
            $this->member,
            $this->book,
            5,
            'This is an excellent book!'
        );
        
        $this->assertNotNull($review);
        $this->assertEquals($this->member->id, $review->user_id);
        $this->assertEquals($this->book->id, $review->book_id);
        $this->assertEquals(5, $review->rating);
        $this->assertEquals(Review::STATUS_PENDING, $review->status);
    }

    public function test_cannot_create_duplicate_review(): void
    {
        $this->reviewService->createReview(
            $this->member,
            $this->book,
            5,
            'First review'
        );
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('sudah memberikan review');
        
        $this->reviewService->createReview(
            $this->member,
            $this->book,
            4,
            'Second review'
        );
    }

    public function test_can_update_own_review(): void
    {
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'review_text' => 'Original review',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $updated = $this->reviewService->updateReview($review, 5, 'Updated review');
        
        $this->assertEquals(5, $updated->rating);
        $this->assertEquals('Updated review', $updated->review_text);
        $this->assertEquals(Review::STATUS_PENDING, $updated->status); // Reset to pending
    }

    public function test_librarian_can_approve_review(): void
    {
        $librarian = User::factory()->create();
        
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'review_text' => 'Great book!',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $approved = $this->reviewService->approveReview($review, $librarian);
        
        $this->assertEquals(Review::STATUS_APPROVED, $approved->status);
        $this->assertEquals($librarian->id, $approved->moderated_by);
        $this->assertNotNull($approved->moderated_at);
    }

    public function test_librarian_can_reject_review(): void
    {
        $librarian = User::factory()->create();
        
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 1,
            'review_text' => 'Inappropriate content',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $rejected = $this->reviewService->rejectReview($review, $librarian);
        
        $this->assertEquals(Review::STATUS_REJECTED, $rejected->status);
    }

    public function test_approved_review_updates_book_rating(): void
    {
        $librarian = User::factory()->create();
        
        // Create and approve a review
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'review_text' => 'Excellent!',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $this->reviewService->approveReview($review, $librarian);
        
        $this->book->refresh();
        $this->assertEquals(5.0, $this->book->average_rating);
    }

    public function test_can_vote_on_review(): void
    {
        $voter = User::factory()->create();
        
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'review_text' => 'Good book!',
            'status' => Review::STATUS_APPROVED,
            'helpful_count' => 0,
            'not_helpful_count' => 0,
        ]);
        
        $this->reviewService->voteReview($review, $voter, 'helpful');
        
        $review->refresh();
        $this->assertEquals(1, $review->helpful_count);
    }

    public function test_vote_toggles_when_same_type(): void
    {
        $voter = User::factory()->create();
        
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'review_text' => 'Good book!',
            'status' => Review::STATUS_APPROVED,
            'helpful_count' => 0,
            'not_helpful_count' => 0,
        ]);
        
        // First vote
        $this->reviewService->voteReview($review, $voter, 'helpful');
        $review->refresh();
        $this->assertEquals(1, $review->helpful_count);
        
        // Vote again with same type - should remove vote
        $this->reviewService->voteReview($review, $voter, 'helpful');
        $review->refresh();
        $this->assertEquals(0, $review->helpful_count);
    }

    public function test_vote_changes_when_different_type(): void
    {
        $voter = User::factory()->create();
        
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'review_text' => 'Good book!',
            'status' => Review::STATUS_APPROVED,
            'helpful_count' => 0,
            'not_helpful_count' => 0,
        ]);
        
        // First vote helpful
        $this->reviewService->voteReview($review, $voter, 'helpful');
        $review->refresh();
        $this->assertEquals(1, $review->helpful_count);
        $this->assertEquals(0, $review->not_helpful_count);
        
        // Change to not helpful
        $this->reviewService->voteReview($review, $voter, 'not_helpful');
        $review->refresh();
        $this->assertEquals(0, $review->helpful_count);
        $this->assertEquals(1, $review->not_helpful_count);
    }

    public function test_get_book_reviews_only_approved(): void
    {
        $librarian = User::factory()->create();
        
        // Create approved review
        Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'review_text' => 'Approved review',
            'status' => Review::STATUS_APPROVED,
        ]);
        
        // Create pending review
        $anotherUser = User::factory()->create();
        Review::create([
            'user_id' => $anotherUser->id,
            'book_id' => $this->book->id,
            'rating' => 3,
            'review_text' => 'Pending review',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $reviews = $this->reviewService->getBookReviews($this->book);
        
        $this->assertCount(1, $reviews);
        $this->assertEquals(Review::STATUS_APPROVED, $reviews->first()->status);
    }

    public function test_get_pending_reviews(): void
    {
        Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'review_text' => 'Pending review',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $pending = $this->reviewService->getPendingReviews();
        
        $this->assertCount(1, $pending);
    }
}
