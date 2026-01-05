<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class AdminBookManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $member;
    protected Category $category;

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
        
        // Assign permissions to admin
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(['manage books', 'manage users', 'manage categories']);
        
        $this->category = Category::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);
        
        $this->admin = User::factory()->create(['status' => 'active']);
        $this->admin->assignRole('admin');
        
        $this->member = User::factory()->create(['status' => 'active']);
        $this->member->assignRole('member');
    }

    public function test_admin_can_view_book_list(): void
    {
        Book::create([
            'isbn' => '978-1111111111',
            'title' => 'Test Book',
            'author' => 'Test Author',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        $response = $this->actingAs($this->admin)
            ->get(route('admin.books.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Test Book');
    }

    public function test_admin_can_create_book(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.books.store'), [
                'title' => 'New Book',
                'author' => 'New Author',
                'isbn' => '978-9876543210',
                'category_id' => $this->category->id,
                'publisher' => 'Test Publisher',
                'publication_year' => 2024,
                'total_copies' => 3,
                'available_copies' => 3,
                'description' => 'Book description',
                'is_active' => true,
            ]);
        
        $response->assertRedirect();
        
        $this->assertDatabaseHas('books', [
            'title' => 'New Book',
            'author' => 'New Author',
            'isbn' => '978-9876543210',
        ]);
    }

    public function test_admin_can_update_book(): void
    {
        $book = Book::create([
            'isbn' => '978-2222222222',
            'title' => 'Original Title',
            'author' => 'Original Author',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        $response = $this->actingAs($this->admin)
            ->put(route('admin.books.update', $book), [
                'isbn' => '978-2222222222',
                'title' => 'Updated Title',
                'author' => 'Updated Author',
                'category_id' => $this->category->id,
                'total_copies' => 10,
            ]);
        
        $response->assertRedirect();
        
        $book->refresh();
        $this->assertEquals('Updated Title', $book->title);
        $this->assertEquals('Updated Author', $book->author);
        $this->assertEquals(10, $book->total_copies);
    }

    public function test_admin_can_delete_book(): void
    {
        $book = Book::create([
            'isbn' => '978-3333333333',
            'title' => 'Book To Delete',
            'author' => 'Author',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.books.destroy', $book));
        
        $response->assertRedirect();
        
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    public function test_member_cannot_access_admin_routes(): void
    {
        $response = $this->actingAs($this->member)
            ->get(route('admin.books.index'));
        
        $response->assertStatus(403);
    }

    public function test_member_cannot_create_book(): void
    {
        $response = $this->actingAs($this->member)
            ->post(route('admin.books.store'), [
                'title' => 'Attempted Book',
                'author' => 'Author',
                'category_id' => $this->category->id,
                'total_copies' => 5,
                'available_copies' => 5,
            ]);
        
        $response->assertStatus(403);
    }

    public function test_admin_can_search_books(): void
    {
        Book::create([
            'isbn' => '978-4444444444',
            'title' => 'Programming in Python',
            'author' => 'John Doe',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        Book::create([
            'isbn' => '978-5555555555',
            'title' => 'Cooking Recipes',
            'author' => 'Jane Smith',
            'category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
        
        $response = $this->actingAs($this->admin)
            ->get(route('admin.books.index', ['search' => 'Python']));
        
        $response->assertStatus(200);
        $response->assertSee('Programming in Python');
        $response->assertDontSee('Cooking Recipes');
    }

    public function test_book_validation_requires_title(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.books.store'), [
                'author' => 'Author',
                'category_id' => $this->category->id,
                'total_copies' => 5,
                'available_copies' => 5,
            ]);
        
        $response->assertSessionHasErrors('title');
    }

    public function test_book_validation_requires_valid_category(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.books.store'), [
                'title' => 'Test Book',
                'author' => 'Author',
                'category_id' => 99999, // Non-existent
                'total_copies' => 5,
                'available_copies' => 5,
            ]);
        
        $response->assertSessionHasErrors('category_id');
    }
}
