<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Loan;
use App\Models\Fine;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Tests for Librarian routes to ensure all views are properly rendered
 * and role-based access control works correctly.
 */
class LibrarianRoutesRenderViewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $librarian;
    protected User $member;
    protected Category $category;
    protected Book $book;
    protected Loan $loan;
    protected Review $review;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'librarian']);
        Role::create(['name' => 'member']);

        // Create permissions
        Permission::create(['name' => 'manage books']);
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage categories']);
        Permission::create(['name' => 'manage loans']);
        Permission::create(['name' => 'manage reviews']);
        Permission::create(['name' => 'manage fines']);

        // Assign permissions
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(['manage books', 'manage users', 'manage categories', 'manage loans', 'manage reviews', 'manage fines']);

        $librarianRole = Role::findByName('librarian');
        $librarianRole->givePermissionTo(['manage loans', 'manage reviews', 'manage fines']);

        // Create category
        $this->category = Category::create([
            'name' => 'Fiksi',
            'slug' => 'fiksi',
        ]);

        // Create users
        $this->admin = User::factory()->create(['status' => 'active']);
        $this->admin->assignRole('admin');

        $this->librarian = User::factory()->create(['status' => 'active']);
        $this->librarian->assignRole('librarian');

        $this->member = User::factory()->create(['status' => 'active']);
        $this->member->assignRole('member');

        // Create a book
        $this->book = Book::create([
            'isbn' => '978-1234567890',
            'title' => 'Test Book',
            'author' => 'Test Author',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 4,
        ]);

        // Create a loan
        $this->loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_REQUESTED,
            'request_date' => now(),
        ]);

        // Create a review
        $this->review = Review::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'rating' => 4,
            'comment' => 'Great book!',
            'status' => Review::STATUS_PENDING,
        ]);
    }

    // ==================== LIBRARIAN DASHBOARD ====================

    public function test_librarian_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.dashboard');
        $response->assertViewHas('stats');
        $response->assertViewHas('loanStats');
        $response->assertViewHas('pendingLoans');
    }

    public function test_admin_can_access_librarian_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('librarian.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.dashboard');
    }

    public function test_member_cannot_access_librarian_dashboard(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('librarian.dashboard'));

        $response->assertStatus(403);
    }

    public function test_guest_redirected_from_librarian_dashboard(): void
    {
        $response = $this->get(route('librarian.dashboard'));

        $response->assertRedirect(route('login'));
    }

    // ==================== LIBRARIAN LOANS ====================

    public function test_librarian_can_view_loans_index(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.loans.index'));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.loans.index');
        $response->assertViewHas('loans');
        $response->assertSee('Kelola Peminjaman');
    }

    public function test_librarian_can_view_pending_loans(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.loans.pending'));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.loans.index');
        $response->assertViewHas('loans');
    }

    public function test_librarian_can_view_active_loans(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.loans.active'));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.loans.index');
        $response->assertViewHas('loans');
    }

    public function test_librarian_can_view_awaiting_pickup_loans(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.loans.awaiting-pickup'));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.loans.index');
        $response->assertViewHas('loans');
    }

    public function test_librarian_can_view_loan_show(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.loans.show', $this->loan));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.loans.show');
        $response->assertViewHas('loan');
    }

    public function test_member_cannot_access_librarian_loans(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('librarian.loans.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->member)
            ->get(route('librarian.loans.show', $this->loan));
        $response->assertStatus(403);
    }

    // ==================== LIBRARIAN REVIEWS ====================

    public function test_librarian_can_view_reviews_index(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.reviews.index'));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.reviews.index');
        $response->assertViewHas('reviews');
        $response->assertSee('Moderasi Review');
    }

    public function test_librarian_can_view_review_show(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.reviews.show', $this->review));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.reviews.show');
        $response->assertViewHas('review');
    }

    public function test_member_cannot_access_librarian_reviews(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('librarian.reviews.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->member)
            ->get(route('librarian.reviews.show', $this->review));
        $response->assertStatus(403);
    }

    // ==================== LIBRARIAN FINES ====================

    public function test_librarian_can_view_fines_index(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.fines.index'));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.fines.index');
        $response->assertViewHas('fines');
        $response->assertSee('Kelola Denda');
    }

    public function test_librarian_can_view_unpaid_fines(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('librarian.fines.unpaid'));

        $response->assertStatus(200);
        $response->assertViewIs('librarian.fines.index');
        $response->assertViewHas('fines');
    }

    public function test_member_cannot_access_librarian_fines(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('librarian.fines.index'));
        $response->assertStatus(403);
    }
}
