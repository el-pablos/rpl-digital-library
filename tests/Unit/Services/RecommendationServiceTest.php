<?php

namespace Tests\Unit\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\Review;
use App\Models\User;
use App\Services\RecommendationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecommendationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected RecommendationService $recommendationService;
    protected User $member;
    protected Category $category;
    protected int $isbnCounter = 0;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->recommendationService = app(RecommendationService::class);
        
        $this->category = Category::create([
            'name' => 'Fiction',
            'slug' => 'fiction',
        ]);
        
        $this->member = User::factory()->create(['status' => 'active']);
    }

    protected function generateIsbn(): string
    {
        $this->isbnCounter++;
        return '978-000000' . str_pad($this->isbnCounter, 4, '0', STR_PAD_LEFT);
    }

    public function test_get_trending_books(): void
    {
        // Create books with different loan counts
        $popularBook = Book::create([
            'isbn' => $this->generateIsbn(),
            'title' => 'Popular Book',
            'author' => 'Author 1',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 3,
        ]);
        
        $lessPopularBook = Book::create([
            'isbn' => $this->generateIsbn(),
            'title' => 'Less Popular Book',
            'author' => 'Author 2',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        // Create more loans for popular book
        for ($i = 0; $i < 5; $i++) {
            $user = User::factory()->create();
            Loan::create([
                'user_id' => $user->id,
                'book_id' => $popularBook->id,
                'status' => Loan::STATUS_RETURNED,
                'request_date' => now()->subDays(rand(1, 25)),
                'pickup_date' => now()->subDays(rand(1, 20)),
                'due_date' => now()->subDays(rand(1, 10)),
                'return_date' => now()->subDays(rand(1, 5)),
            ]);
        }
        
        $trending = $this->recommendationService->getTrendingBooks(10);
        
        $this->assertTrue($trending->contains($popularBook));
    }

    public function test_get_top_rated_books(): void
    {
        $highRatedBook = Book::create([
            'isbn' => $this->generateIsbn(),
            'title' => 'High Rated Book',
            'author' => 'Author 1',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
            'average_rating' => 4.8,
        ]);
        
        $lowRatedBook = Book::create([
            'isbn' => $this->generateIsbn(),
            'title' => 'Low Rated Book',
            'author' => 'Author 2',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
            'average_rating' => 2.5,
        ]);
        
        // Create additional users for reviews
        $user2 = User::factory()->create(['status' => 'active']);
        $user3 = User::factory()->create(['status' => 'active']);
        
        // Add at least 3 reviews for the high rated book (minimum required)
        Review::create([
            'user_id' => $this->member->id,
            'book_id' => $highRatedBook->id,
            'rating' => 5,
            'review_text' => 'Excellent book!',
            'status' => Review::STATUS_APPROVED,
        ]);
        
        Review::create([
            'user_id' => $user2->id,
            'book_id' => $highRatedBook->id,
            'rating' => 5,
            'review_text' => 'Amazing!',
            'status' => Review::STATUS_APPROVED,
        ]);
        
        Review::create([
            'user_id' => $user3->id,
            'book_id' => $highRatedBook->id,
            'rating' => 5,
            'review_text' => 'Must read!',
            'status' => Review::STATUS_APPROVED,
        ]);
        
        $topRated = $this->recommendationService->getTopRatedBooks(10);
        
        $this->assertNotEmpty($topRated);
        $this->assertEquals($highRatedBook->id, $topRated->first()->id);
    }

    public function test_get_new_arrivals(): void
    {
        $newBook = Book::create([
            'isbn' => $this->generateIsbn(),
            'title' => 'New Book',
            'author' => 'Author 1',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
            'created_at' => now(),
        ]);
        
        $oldBook = Book::create([
            'isbn' => $this->generateIsbn(),
            'title' => 'Old Book',
            'author' => 'Author 2',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
            'created_at' => now()->subMonths(6),
        ]);
        
        $newArrivals = $this->recommendationService->getNewArrivals(10);
        
        $this->assertEquals($newBook->id, $newArrivals->first()->id);
    }

    public function test_generate_recommendations_for_user(): void
    {
        // Create books in the same category
        $book1 = Book::create([
            'isbn' => $this->generateIsbn(),
            'title' => 'Book 1',
            'author' => 'Author 1',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        $book2 = Book::create([
            'isbn' => $this->generateIsbn(),
            'title' => 'Book 2',
            'author' => 'Author 1',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        // User borrowed book1
        Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $book1->id,
            'status' => Loan::STATUS_RETURNED,
            'request_date' => now()->subDays(15),
            'pickup_date' => now()->subDays(12),
            'due_date' => now()->subDays(5),
            'return_date' => now()->subDays(2),
        ]);
        
        $recommendations = $this->recommendationService->generateRecommendations($this->member, 5);
        
        // Should recommend book2 since it's in the same category and by the same author
        $this->assertNotEmpty($recommendations);
    }

    public function test_recommendations_exclude_already_borrowed(): void
    {
        $book1 = Book::create([
            'isbn' => $this->generateIsbn(),
            'title' => 'Already Borrowed Book',
            'author' => 'Author 1',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 4,
        ]);
        
        // Currently active loan
        Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $book1->id,
            'status' => Loan::STATUS_ACTIVE,
            'request_date' => now()->subDays(5),
            'pickup_date' => now()->subDays(3),
            'due_date' => now()->addDays(4),
        ]);
        
        $recommendations = $this->recommendationService->generateRecommendations($this->member, 5);
        
        // Should not include currently borrowed book
        $this->assertFalse($recommendations->contains('id', $book1->id));
    }
}
