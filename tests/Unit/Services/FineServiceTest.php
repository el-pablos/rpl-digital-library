<?php

namespace Tests\Unit\Services;

use App\Models\Fine;
use App\Models\Loan;
use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Services\FineService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FineServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FineService $fineService;
    protected User $member;
    protected Loan $loan;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->fineService = app(FineService::class);
        
        $category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
        
        $this->member = User::factory()->create(['status' => 'active']);
        
        $book = Book::create([
            'isbn' => '978-1234567890',
            'title' => 'Test Book',
            'author' => 'Test Author',
            'category_id' => $category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        $this->loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $book->id,
            'status' => Loan::STATUS_RETURNED,
            'request_date' => now()->subDays(20),
            'pickup_date' => now()->subDays(18),
            'due_date' => now()->subDays(5),
            'return_date' => now(),
        ]);
    }

    public function test_can_pay_fine(): void
    {
        $fine = Fine::create([
            'loan_id' => $this->loan->id,
            'user_id' => $this->member->id,
            'amount' => 5000,
            'reason' => 'Keterlambatan',
            'status' => Fine::STATUS_UNPAID,
        ]);
        
        $paid = $this->fineService->payFine($fine);
        
        $this->assertEquals(Fine::STATUS_PAID, $paid->status);
        $this->assertNotNull($paid->paid_at);
    }

    public function test_can_waive_fine(): void
    {
        $fine = Fine::create([
            'loan_id' => $this->loan->id,
            'user_id' => $this->member->id,
            'amount' => 5000,
            'reason' => 'Keterlambatan',
            'status' => Fine::STATUS_UNPAID,
        ]);
        
        $waived = $this->fineService->waiveFine($fine, 'Member request');
        
        $this->assertEquals(Fine::STATUS_WAIVED, $waived->status);
        $this->assertStringContainsString('Dihapuskan', $waived->reason);
    }

    public function test_get_unpaid_fines_for_user(): void
    {
        Fine::create([
            'loan_id' => $this->loan->id,
            'user_id' => $this->member->id,
            'amount' => 5000,
            'reason' => 'Keterlambatan',
            'status' => Fine::STATUS_UNPAID,
        ]);
        
        $unpaid = $this->fineService->getUnpaidFines($this->member);
        
        $this->assertCount(1, $unpaid);
        $this->assertEquals(5000, $unpaid->first()->amount);
    }

    public function test_get_total_unpaid_fines(): void
    {
        $loan2 = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->loan->book_id,
            'status' => Loan::STATUS_RETURNED,
            'request_date' => now()->subDays(30),
            'pickup_date' => now()->subDays(28),
            'due_date' => now()->subDays(10),
            'return_date' => now()->subDays(5),
        ]);
        
        Fine::create([
            'loan_id' => $this->loan->id,
            'user_id' => $this->member->id,
            'amount' => 5000,
            'reason' => 'Keterlambatan',
            'status' => Fine::STATUS_UNPAID,
        ]);
        
        Fine::create([
            'loan_id' => $loan2->id,
            'user_id' => $this->member->id,
            'amount' => 3000,
            'reason' => 'Keterlambatan',
            'status' => Fine::STATUS_UNPAID,
        ]);
        
        $total = $this->fineService->getTotalUnpaidFines($this->member);
        
        $this->assertEquals(8000, $total);
    }

    public function test_has_unpaid_fines(): void
    {
        $this->assertFalse($this->fineService->hasUnpaidFines($this->member));
        
        Fine::create([
            'loan_id' => $this->loan->id,
            'user_id' => $this->member->id,
            'amount' => 5000,
            'reason' => 'Keterlambatan',
            'status' => Fine::STATUS_UNPAID,
        ]);
        
        $this->assertTrue($this->fineService->hasUnpaidFines($this->member));
    }

    public function test_pay_all_fines(): void
    {
        $loan2 = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->loan->book_id,
            'status' => Loan::STATUS_RETURNED,
            'request_date' => now()->subDays(30),
            'pickup_date' => now()->subDays(28),
            'due_date' => now()->subDays(10),
            'return_date' => now()->subDays(5),
        ]);
        
        Fine::create([
            'loan_id' => $this->loan->id,
            'user_id' => $this->member->id,
            'amount' => 5000,
            'reason' => 'Keterlambatan',
            'status' => Fine::STATUS_UNPAID,
        ]);
        
        Fine::create([
            'loan_id' => $loan2->id,
            'user_id' => $this->member->id,
            'amount' => 3000,
            'reason' => 'Keterlambatan',
            'status' => Fine::STATUS_UNPAID,
        ]);
        
        $this->fineService->payAllFines($this->member);
        
        $this->assertFalse($this->fineService->hasUnpaidFines($this->member));
        $this->assertEquals(0, $this->fineService->getTotalUnpaidFines($this->member));
    }

    public function test_fine_statistics(): void
    {
        Fine::create([
            'loan_id' => $this->loan->id,
            'user_id' => $this->member->id,
            'amount' => 5000,
            'reason' => 'Keterlambatan',
            'status' => Fine::STATUS_UNPAID,
        ]);
        
        $stats = $this->fineService->getFineStatistics();
        
        $this->assertArrayHasKey('total_unpaid', $stats);
        $this->assertArrayHasKey('total_paid', $stats);
        $this->assertEquals(5000, $stats['total_unpaid']); // total_unpaid is the sum of amounts
    }
}
