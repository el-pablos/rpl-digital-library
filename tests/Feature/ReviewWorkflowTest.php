<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReviewWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $member;
    protected User $librarian;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'librarian']);
        Role::create(['name' => 'member']);
        
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
        
        $this->book = Book::create([
            'isbn' => '978-1234567890',
            'title' => 'Test Book',
            'author' => 'Test Author',
            'category_id' => $category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        $this->member = User::factory()->create(['status' => 'active']);
        $this->member->assignRole('member');
        
        $this->librarian = User::factory()->create(['status' => 'active']);
        $this->librarian->assignRole('librarian');
        
        // Member has returned the book (can review)
        Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_RETURNED,
            'request_date' => now()->subDays(20),
            'pickup_date' => now()->subDays(18),
            'due_date' => now()->subDays(11),
            'return_date' => now()->subDays(5),
        ]);
    }

    public function test_member_can_submit_review(): void
    {
        $response = $this->actingAs($this->member)
            ->post(route('reviews.store', $this->book), [
                'rating' => 5,
                'review_text' => 'This is an excellent book!',
            ]);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('reviews', [
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'status' => Review::STATUS_PENDING,
        ]);
    }

    public function test_review_requires_valid_rating(): void
    {
        $response = $this->actingAs($this->member)
            ->post(route('reviews.store', $this->book), [
                'rating' => 6, // Invalid
                'review_text' => 'Test review',
            ]);
        
        $response->assertSessionHasErrors('rating');
    }

    public function test_librarian_can_view_pending_reviews(): void
    {
        Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'review_text' => 'Good book!',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.reviews.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Good book!');
    }

    public function test_librarian_can_approve_review(): void
    {
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'review_text' => 'Good book!',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $response = $this->actingAs($this->librarian)
            ->post(route('librarian.reviews.approve', $review));
        
        $response->assertRedirect();
        
        $review->refresh();
        $this->assertEquals(Review::STATUS_APPROVED, $review->status);
    }

    public function test_librarian_can_reject_review(): void
    {
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 1,
            'review_text' => 'Inappropriate content',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $response = $this->actingAs($this->librarian)
            ->post(route('librarian.reviews.reject', $review));
        
        $response->assertRedirect();
        
        $review->refresh();
        $this->assertEquals(Review::STATUS_REJECTED, $review->status);
    }

    public function test_approved_reviews_show_on_book_page(): void
    {
        Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 5,
            'review_text' => 'Excellent approved review',
            'status' => Review::STATUS_APPROVED,
        ]);
        
        $response = $this->actingAs($this->member)
            ->get(route('books.show', $this->book));
        
        $response->assertStatus(200);
        $response->assertSee('Excellent approved review');
    }

    public function test_pending_reviews_hidden_from_book_page(): void
    {
        Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 3,
            'review_text' => 'Pending review should not show',
            'status' => Review::STATUS_PENDING,
        ]);
        
        $anotherMember = User::factory()->create();
        $anotherMember->assignRole('member');
        
        $response = $this->actingAs($anotherMember)
            ->get(route('books.show', $this->book));
        
        $response->assertDontSee('Pending review should not show');
    }

    public function test_member_can_vote_on_review(): void
    {
        $voter = User::factory()->create(['status' => 'active']);
        $voter->assignRole('member');
        
        $review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'review_text' => 'Good book!',
            'status' => Review::STATUS_APPROVED,
            'helpful_count' => 0,
            'not_helpful_count' => 0,
        ]);
        
        $response = $this->actingAs($voter)
            ->post(route('reviews.vote', $review), [
                'vote_type' => 'helpful',
            ]);
        
        $response->assertRedirect();
        
        $review->refresh();
        $this->assertEquals(1, $review->helpful_count);
    }

    public function test_member_cannot_access_review_moderation(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('librarian.reviews.index'));
        
        $response->assertStatus(403);
    }
}
