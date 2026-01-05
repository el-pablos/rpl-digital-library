<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Loan;
use App\Models\Fine;
use App\Models\Review;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Tests for Member routes to ensure all views are properly rendered
 * and role-based access control works correctly.
 */
class MemberRoutesRenderViewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;
    protected Category $category;
    protected Book $book;
    protected Loan $loan;

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

        // Assign permissions
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(['manage books', 'manage users', 'manage categories', 'manage loans', 'manage reviews']);

        // Create category
        $this->category = Category::create([
            'name' => 'Fiksi',
            'slug' => 'fiksi',
        ]);

        // Create users
        $this->admin = User::factory()->create(['status' => 'active']);
        $this->admin->assignRole('admin');

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

        // Create a loan for member
        $this->loan = Loan::create([
            'user_id' => $this->member->id,
            'book_id' => $this->book->id,
            'status' => Loan::STATUS_ACTIVE,
            'request_date' => now()->subDays(2),
            'approval_date' => now()->subDays(1),
            'pickup_date' => now(),
            'due_date' => now()->addDays(14),
        ]);
    }

    // ==================== MEMBER DASHBOARD ====================

    public function test_member_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('dashboard');
        $response->assertViewHas('activeLoans');
        $response->assertViewHas('stats');
        $response->assertViewHas('recommendations');
    }

    public function test_guest_redirected_from_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    // ==================== BOOKS CATALOG ====================

    public function test_member_can_view_books_index(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertViewIs('books.index');
        $response->assertViewHas('books');
        $response->assertSee('Katalog Buku');
    }

    public function test_guest_can_view_books_index(): void
    {
        $response = $this->get(route('books.index'));

        $response->assertStatus(200);
        $response->assertViewIs('books.index');
    }

    public function test_member_can_view_book_show(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('books.show', $this->book));

        $response->assertStatus(200);
        $response->assertViewIs('books.show');
        $response->assertViewHas('book');
        $response->assertSee($this->book->title);
    }

    public function test_member_can_view_recommendations(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('books.recommendations'));

        $response->assertStatus(200);
        $response->assertViewIs('books.recommendations');
    }

    // ==================== MEMBER LOANS ====================

    public function test_member_can_view_own_loans(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('loans.index'));

        $response->assertStatus(200);
        $response->assertViewIs('loans.index');
        $response->assertViewHas('loans');
        $response->assertSee('Riwayat Peminjaman');
    }

    public function test_member_can_view_own_loan_show(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('loans.show', $this->loan));

        $response->assertStatus(200);
        $response->assertViewIs('loans.show');
        $response->assertViewHas('loan');
    }

    public function test_guest_redirected_from_loans(): void
    {
        $response = $this->get(route('loans.index'));

        $response->assertRedirect(route('login'));
    }

    // ==================== MEMBER FINES ====================

    public function test_member_can_view_own_fines(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('fines.index'));

        $response->assertStatus(200);
        $response->assertViewIs('fines.index');
        $response->assertViewHas('fines');
        $response->assertSee('Denda Saya');
    }

    public function test_guest_redirected_from_fines(): void
    {
        $response = $this->get(route('fines.index'));

        $response->assertRedirect(route('login'));
    }

    // ==================== MEMBER REVIEWS ====================

    public function test_member_can_view_own_reviews(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('reviews.user-reviews'));

        $response->assertStatus(200);
        $response->assertViewIs('reviews.user-reviews');
        $response->assertViewHas('reviews');
    }

    // ==================== NOTIFICATIONS ====================

    public function test_member_can_view_notifications(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('notifications.index'));

        $response->assertStatus(200);
        $response->assertViewIs('notifications.index');
        $response->assertViewHas('notifications');
    }

    // ==================== PROFILE ====================

    public function test_member_can_view_profile(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertViewIs('profile.edit');
        $response->assertViewHas('user');
    }
}
