<?php

namespace Tests\Unit\Services;

use App\Models\Book;
use App\Models\Category;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use App\Services\LoanService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LoanService $loanService;
    protected User $member;
    protected Book $book;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->loanService = app(LoanService::class);
        
        // Create category
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
        
        // Create a member user
        $this->member = User::factory()->create([
            'status' => 'active',
        ]);
        
        // Create a book
        $this->book = Book::create([
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '978-1234567890',
            'category_id' => $category->id,
            'total_copies' => 5,
            'available_copies' => 5,
            'is_active' => true,
        ]);
    }

    public function test_member_can_request_loan(): void
    {
        $loan = $this->loanService->requestLoan($this->member, $this->book);
        
        $this->assertNotNull($loan);
        $this->assertEquals(Loan::STATUS_REQUESTED, $loan->status);
        $this->assertEquals($this->member->id, $loan->user_id);
        $this->assertEquals($this->book->id, $loan->book_id);
        
        // Book available copies should decrease
        $this->book->refresh();
        $this->assertEquals(4, $this->book->available_copies);
    }

    public function test_member_cannot_request_when_book_unavailable(): void
    {
        $this->book->update(['available_copies' => 0]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Buku tidak tersedia');
        
        $this->loanService->requestLoan($this->member, $this->book);
    }

    public function test_member_cannot_exceed_max_loans(): void
    {
        // Create max active loans
        for ($i = 0; $i < User::MAX_ACTIVE_LOANS; $i++) {
            $book = Book::create([
                'title' => "Test Book $i",
                'author' => 'Test Author',
                'isbn' => '978-' . str_pad($i, 10, '0', STR_PAD_LEFT),
                'category_id' => $this->book->category_id,
                'total_copies' => 5,
                'available_copies' => 5,
            ]);
            
            Loan::create([
                'user_id' => $this->member->id,
                'book_id' => $book->id,
                'status' => Loan::STATUS_ACTIVE,
                'request_date' => now(),
                'pickup_date' => now(),
                'due_date' => now()->addDays(7),
            ]);
        }
        
        $this->expectException(\Exception::class);
        
        $this->loanService->requestLoan($this->member, $this->book);
    }

    public function test_librarian_can_approve_loan(): void
    {
        $librarian = User::factory()->create();
        
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_REQUESTED,
            'request_date' => now(),
        ]);
        
        $approved = $this->loanService->approveLoan($loan, $librarian);
        
        $this->assertEquals(Loan::STATUS_APPROVED, $approved->status);
        $this->assertEquals($librarian->id, $approved->approved_by);
        $this->assertNotNull($approved->approval_date);
    }

    public function test_librarian_can_reject_loan(): void
    {
        $librarian = User::factory()->create();
        
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_REQUESTED,
            'request_date' => now(),
        ]);
        
        $this->book->update(['available_copies' => 4]); // Reduced when requested
        
        $rejected = $this->loanService->rejectLoan($loan, $librarian, 'Test rejection reason');
        
        $this->assertEquals(Loan::STATUS_REJECTED, $rejected->status);
        $this->assertEquals('Test rejection reason', $rejected->notes);
        
        // Book should be available again
        $this->book->refresh();
        $this->assertEquals(5, $this->book->available_copies);
    }

    public function test_process_pickup_sets_due_date(): void
    {
        $librarian = User::factory()->create();
        
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_APPROVED,
            'request_date' => now(),
            'approval_date' => now(),
            'approved_by' => $librarian->id,
        ]);
        
        $picked = $this->loanService->processPickup($loan, $librarian);
        
        $this->assertEquals(Loan::STATUS_ACTIVE, $picked->status);
        $this->assertNotNull($picked->pickup_date);
        $this->assertNotNull($picked->due_date);
        $this->assertEquals(
            now()->addDays(Loan::LOAN_DURATION)->format('Y-m-d'),
            $picked->due_date->format('Y-m-d')
        );
    }

    public function test_process_return_increases_available_copies(): void
    {
        $this->book->update(['available_copies' => 4]);
        
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_ACTIVE,
            'request_date' => now()->subDays(10),
            'pickup_date' => now()->subDays(7),
            'due_date' => now()->addDays(1),
        ]);
        
        $result = $this->loanService->processReturn($loan);
        
        $this->assertEquals(Loan::STATUS_RETURNED, $result['loan']->status);
        $this->assertNotNull($result['loan']->return_date);
        
        $this->book->refresh();
        $this->assertEquals(5, $this->book->available_copies);
    }

    public function test_late_return_creates_fine(): void
    {
        $this->book->update(['available_copies' => 4]);
        
        // Set due date 5 days in the past
        $dueDate = now()->subDays(5)->format('Y-m-d');
        
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_OVERDUE,
            'request_date' => now()->subDays(15),
            'pickup_date' => now()->subDays(12),
            'due_date' => $dueDate,
        ]);
        
        // Refresh to get the loan with proper casted values
        $loan->refresh();
        
        $result = $this->loanService->processReturn($loan);
        
        $this->assertTrue($result['is_late']);
        $this->assertNotNull($result['fine']);
        
        // Grace period is 3 days, so 5 days late means 2 chargable days
        $expectedFine = (5 - Loan::GRACE_PERIOD) * Fine::FINE_PER_DAY;
        $this->assertEquals($expectedFine, $result['fine']->amount);
    }

    public function test_member_can_renew_loan(): void
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
        
        $renewed = $this->loanService->renewLoan($loan);
        
        $this->assertEquals(1, $renewed->renewal_count);
        $this->assertEquals(
            now()->addDays(4 + Loan::LOAN_DURATION)->format('Y-m-d'),
            $renewed->due_date->format('Y-m-d')
        );
    }

    public function test_cannot_renew_when_max_renewals_reached(): void
    {
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_ACTIVE,
            'request_date' => now()->subDays(30),
            'pickup_date' => now()->subDays(28),
            'due_date' => now()->addDays(1),
            'renewal_count' => Loan::MAX_RENEWALS,
        ]);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('tidak dapat diperpanjang');
        
        $this->loanService->renewLoan($loan);
    }

    public function test_cannot_renew_overdue_loan(): void
    {
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_OVERDUE,
            'request_date' => now()->subDays(15),
            'pickup_date' => now()->subDays(12),
            'due_date' => now()->subDays(5),
            'renewal_count' => 0,
        ]);
        
        $this->expectException(\Exception::class);
        
        $this->loanService->renewLoan($loan);
    }

    public function test_member_can_cancel_pending_request(): void
    {
        $this->book->update(['available_copies' => 4]);
        
        $loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_REQUESTED,
            'request_date' => now(),
        ]);
        
        $cancelled = $this->loanService->cancelLoan($loan);
        
        $this->assertEquals(Loan::STATUS_CANCELLED, $cancelled->status);
        
        $this->book->refresh();
        $this->assertEquals(5, $this->book->available_copies);
    }
}
