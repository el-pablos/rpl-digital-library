<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoanWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $member;
    protected User $librarian;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'librarian']);
        Role::create(['name' => 'member']);
        
        // Create category and book
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
        
        $this->book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '978-1234567890',
            'category_id' => $category->id,
            'total_copies' => 5,
            'available_copies' => 5,
            'is_active' => true,
        ]);
        
        // Create member
        $this->member = User::factory()->create(['status' => 'active']);
        $this->member->assignRole('member');
        
        // Create librarian
        $this->librarian = User::factory()->create(['status' => 'active']);
        $this->librarian->assignRole('librarian');
    }

    public function test_member_can_view_book_catalog(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('books.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Test Book');
    }

    public function test_member_can_view_book_details(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('books.show', $this->book));
        
        $response->assertStatus(200);
        $response->assertSee('Test Book');
        $response->assertSee('Test Author');
    }

    public function test_member_can_request_loan(): void
    {
        $response = $this->actingAs($this->member)
            ->post(route('loans.store', $this->book));
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('loans', [
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_REQUESTED,
        ]);
        
        // Book available copies should decrease
        $this->book->refresh();
        $this->assertEquals(4, $this->book->available_copies);
    }

    public function test_member_can_view_own_loans(): void
    {
        Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_ACTIVE,
            'request_date' => now()->subDays(5),
            'pickup_date' => now()->subDays(3),
            'due_date' => now()->addDays(4),
        ]);
        
        $response = $this->actingAs($this->member)
            ->get(route('loans.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Test Book');
    }

    public function test_member_can_cancel_pending_request(): void
    {
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_REQUESTED,
            'request_date' => now(),
        ]);
        
        $this->book->update(['available_copies' => 4]);
        
        $response = $this->actingAs($this->member)
            ->post(route('loans.cancel', $loan));
        
        $response->assertRedirect();
        
        $loan->refresh();
        $this->assertEquals(Loan::STATUS_CANCELLED, $loan->status);
        
        $this->book->refresh();
        $this->assertEquals(5, $this->book->available_copies);
    }

    public function test_librarian_can_view_all_loans(): void
    {
        Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_REQUESTED,
            'request_date' => now(),
        ]);
        
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.loans.index'));
        
        $response->assertStatus(200);
    }

    public function test_librarian_can_approve_loan(): void
    {
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_REQUESTED,
            'request_date' => now(),
        ]);
        
        $response = $this->actingAs($this->librarian)
            ->post(route('librarian.loans.approve', $loan));
        
        $response->assertRedirect();
        
        $loan->refresh();
        $this->assertEquals(Loan::STATUS_APPROVED, $loan->status);
        $this->assertEquals($this->librarian->id, $loan->approved_by);
    }

    public function test_librarian_can_reject_loan(): void
    {
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_REQUESTED,
            'request_date' => now(),
        ]);
        
        $this->book->update(['available_copies' => 4]);
        
        $response = $this->actingAs($this->librarian)
            ->post(route('librarian.loans.reject', $loan), [
                'reason' => 'Book reserved for event',
            ]);
        
        $response->assertRedirect();
        
        $loan->refresh();
        $this->assertEquals(Loan::STATUS_REJECTED, $loan->status);
        
        $this->book->refresh();
        $this->assertEquals(5, $this->book->available_copies);
    }

    public function test_librarian_can_process_pickup(): void
    {
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_APPROVED,
            'request_date' => now()->subDay(),
            'approval_date' => now(),
            'approved_by' => $this->librarian->id,
        ]);
        
        $response = $this->actingAs($this->librarian)
            ->post(route('librarian.loans.pickup', $loan));
        
        $response->assertRedirect();
        
        $loan->refresh();
        $this->assertEquals(Loan::STATUS_ACTIVE, $loan->status);
        $this->assertNotNull($loan->pickup_date);
        $this->assertNotNull($loan->due_date);
    }

    public function test_librarian_can_process_return(): void
    {
        $this->book->update(['available_copies' => 4]);
        
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_ACTIVE,
            'request_date' => now()->subDays(10),
            'pickup_date' => now()->subDays(7),
            'due_date' => now()->addDay(),
        ]);
        
        $response = $this->actingAs($this->librarian)
            ->post(route('librarian.loans.return', $loan));
        
        $response->assertRedirect();
        
        $loan->refresh();
        $this->assertEquals(Loan::STATUS_RETURNED, $loan->status);
        $this->assertNotNull($loan->return_date);
        
        $this->book->refresh();
        $this->assertEquals(5, $this->book->available_copies);
    }

    public function test_member_can_renew_active_loan(): void
    {
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_ACTIVE,
            'request_date' => now()->subDays(5),
            'pickup_date' => now()->subDays(3),
            'due_date' => now()->addDays(4),
            'renewal_count' => 0,
        ]);
        
        $response = $this->actingAs($this->member)
            ->post(route('loans.renew', $loan));
        
        $response->assertRedirect();
        
        $loan->refresh();
        $this->assertEquals(1, $loan->renewal_count);
    }

    public function test_guest_cannot_request_loan(): void
    {
        $response = $this->post(route('loans.store', $this->book));
        
        $response->assertRedirect(route('login'));
    }

    public function test_member_cannot_access_librarian_routes(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('librarian.loans.index'));
        
        $response->assertStatus(403);
    }
}
