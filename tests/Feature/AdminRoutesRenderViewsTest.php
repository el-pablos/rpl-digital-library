<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use App\Models\Loan;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Tests for Admin routes to ensure all views are properly rendered
 * and role-based access control works correctly.
 */
class AdminRoutesRenderViewsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $librarian;
    protected User $member;
    protected Category $parentCategory;
    protected Category $category;
    protected Book $book;

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

        $librarianRole = Role::findByName('librarian');
        $librarianRole->givePermissionTo(['manage loans', 'manage reviews']);

        // Create categories (parent and child)
        $this->parentCategory = Category::create([
            'name' => 'Fiksi',
            'slug' => 'fiksi',
        ]);

        $this->category = Category::create([
            'name' => 'Novel',
            'slug' => 'novel',
            'parent_id' => $this->parentCategory->id,
        ]);

        // Create users
        $this->admin = User::factory()->create(['status' => 'active']);
        $this->admin->assignRole('admin');

        $this->librarian = User::factory()->create(['status' => 'active']);
        $this->librarian->assignRole('librarian');

        $this->member = User::factory()->create(['status' => 'active']);
        $this->member->assignRole('member');

        // Create a book for testing
        $this->book = Book::create([
            'isbn' => '978-1234567890',
            'title' => 'Test Book',
            'author' => 'Test Author',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
    }

    // ==================== ADMIN DASHBOARD ====================

    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('stats');
        $response->assertViewHas('loanStats');
        $response->assertViewHas('fineStats');
    }

    public function test_member_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('admin.dashboard'));

        $response->assertStatus(403);
    }

    public function test_guest_redirected_from_admin_dashboard(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('login'));
    }

    // ==================== ADMIN BOOKS ====================

    public function test_admin_can_view_books_index(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.books.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.books.index');
        $response->assertViewHas('books');
        $response->assertViewHas('categories');
        $response->assertSee('Kelola Buku');
    }

    public function test_admin_can_view_book_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.books.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.books.create');
        $response->assertViewHas('categories');
        $response->assertSee('Tambah Buku');
    }

    public function test_admin_can_view_book_edit_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.books.edit', $this->book));

        $response->assertStatus(200);
        $response->assertViewIs('admin.books.edit');
        $response->assertViewHas('book');
        $response->assertViewHas('categories');
        $response->assertSee('Edit Buku');
    }

    public function test_admin_can_view_book_show(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.books.show', $this->book));

        $response->assertStatus(200);
        $response->assertViewIs('admin.books.show');
        $response->assertViewHas('book');
        $response->assertSee($this->book->title);
    }

    public function test_member_cannot_access_admin_books(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('admin.books.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->member)
            ->get(route('admin.books.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->member)
            ->get(route('admin.books.edit', $this->book));
        $response->assertStatus(403);
    }

    // ==================== ADMIN CATEGORIES ====================

    public function test_admin_can_view_categories_index(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.index');
        $response->assertViewHas('categories');
        $response->assertSee('Kelola Kategori');
    }

    public function test_admin_can_view_category_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.create');
        $response->assertViewHas('parentCategories');
        $response->assertSee('Tambah Kategori');
    }

    public function test_admin_can_view_category_edit_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.categories.edit', $this->category));

        $response->assertStatus(200);
        $response->assertViewIs('admin.categories.edit');
        $response->assertViewHas('category');
        $response->assertViewHas('parentCategories');
        $response->assertSee('Edit Kategori');
    }

    public function test_member_cannot_access_admin_categories(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('admin.categories.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->member)
            ->get(route('admin.categories.create'));
        $response->assertStatus(403);
    }

    // ==================== ADMIN USERS ====================

    public function test_admin_can_view_users_index(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
        $response->assertViewHas('roles');
        $response->assertSee('Kelola Pengguna');
    }

    public function test_admin_can_view_user_create_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.create');
        $response->assertViewHas('roles');
        $response->assertSee('Tambah Pengguna');
    }

    public function test_admin_can_view_user_edit_form(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.edit', $this->member));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
        $response->assertViewHas('user');
        $response->assertViewHas('roles');
        $response->assertSee('Edit Pengguna');
    }

    public function test_admin_can_view_user_show(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.users.show', $this->member));

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.show');
        $response->assertViewHas('user');
        $response->assertViewHas('statistics');
        $response->assertSee($this->member->name);
    }

    public function test_librarian_cannot_access_admin_users(): void
    {
        $response = $this->actingAs($this->librarian)
            ->get(route('admin.users.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($this->librarian)
            ->get(route('admin.users.create'));
        $response->assertStatus(403);
    }

    public function test_member_cannot_access_admin_users(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('admin.users.index'));
        $response->assertStatus(403);
    }
}
