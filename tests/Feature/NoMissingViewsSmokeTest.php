<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Smoke test to ensure no routes return 500 errors.
 * This test catches missing views, undefined variables, and other runtime errors.
 */
class NoMissingViewsSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $librarian;
    protected User $member;
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
        Permission::create(['name' => 'manage fines']);

        // Assign permissions
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(['manage books', 'manage users', 'manage categories', 'manage loans', 'manage reviews', 'manage fines']);

        $librarianRole = Role::findByName('librarian');
        $librarianRole->givePermissionTo(['manage loans', 'manage reviews', 'manage fines']);

        // Create parent and child categories
        $parentCategory = Category::create([
            'name' => 'Fiksi',
            'slug' => 'fiksi',
        ]);

        $this->category = Category::create([
            'name' => 'Novel',
            'slug' => 'novel',
            'parent_id' => $parentCategory->id,
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
            'available_copies' => 5,
        ]);
    }

    /**
     * Test all public/guest accessible routes don't return 500.
     */
    public function test_public_routes_no_500_errors(): void
    {
        $publicRoutes = [
            '/',
            '/books',
            '/books/' . $this->book->id,
            '/login',
            '/register',
            '/forgot-password',
        ];

        foreach ($publicRoutes as $route) {
            $response = $this->get($route);
            
            $this->assertNotEquals(
                500, 
                $response->getStatusCode(), 
                "Route {$route} returned 500 error"
            );
            
            // Also check for common error indicators
            $content = $response->getContent();
            $this->assertStringNotContainsString(
                'InvalidArgumentException', 
                $content,
                "Route {$route} has InvalidArgumentException (likely missing view)"
            );
        }
    }

    /**
     * Test all admin routes don't return 500 when accessed by admin.
     */
    public function test_admin_routes_no_500_errors(): void
    {
        $adminRoutes = [
            '/admin/dashboard',
            '/admin/books',
            '/admin/books/create',
            '/admin/books/' . $this->book->id,
            '/admin/books/' . $this->book->id . '/edit',
            '/admin/categories',
            '/admin/categories/create',
            '/admin/categories/' . $this->category->id . '/edit',
            '/admin/users',
            '/admin/users/create',
            '/admin/users/' . $this->member->id,
            '/admin/users/' . $this->member->id . '/edit',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->actingAs($this->admin)->get($route);
            
            $this->assertNotEquals(
                500, 
                $response->getStatusCode(), 
                "Admin route {$route} returned 500 error"
            );
            
            $content = $response->getContent();
            $this->assertStringNotContainsString(
                'InvalidArgumentException', 
                $content,
                "Admin route {$route} has InvalidArgumentException (likely missing view)"
            );
        }
    }

    /**
     * Test all librarian routes don't return 500 when accessed by librarian.
     */
    public function test_librarian_routes_no_500_errors(): void
    {
        $librarianRoutes = [
            '/librarian/dashboard',
            '/librarian/loans',
            '/librarian/loans/pending',
            '/librarian/loans/active',
            '/librarian/loans/awaiting-pickup',
            '/librarian/reviews',
            '/librarian/fines',
            '/librarian/fines/unpaid',
        ];

        foreach ($librarianRoutes as $route) {
            $response = $this->actingAs($this->librarian)->get($route);
            
            $this->assertNotEquals(
                500, 
                $response->getStatusCode(), 
                "Librarian route {$route} returned 500 error"
            );
            
            $content = $response->getContent();
            $this->assertStringNotContainsString(
                'InvalidArgumentException', 
                $content,
                "Librarian route {$route} has InvalidArgumentException (likely missing view)"
            );
        }
    }

    /**
     * Test all member routes don't return 500 when accessed by member.
     */
    public function test_member_routes_no_500_errors(): void
    {
        $memberRoutes = [
            '/dashboard',
            '/books',
            '/recommendations',
            '/my-loans',
            '/my-fines',
            '/my-reviews',
            '/notifications',
            '/profile',
        ];

        foreach ($memberRoutes as $route) {
            $response = $this->actingAs($this->member)->get($route);
            
            if ($response->getStatusCode() === 500) {
                // Debug: Get exception message from response
                $content = $response->getContent();
                if (preg_match('/(?:Exception|Error)[:\s]+([^<]+)/', $content, $matches)) {
                    $errorMessage = trim($matches[1] ?? 'Unknown error');
                    $this->fail("Member route {$route} returned 500 error: {$errorMessage}");
                }
            }
            
            $this->assertNotEquals(
                500, 
                $response->getStatusCode(), 
                "Member route {$route} returned 500 error"
            );
            
            $content = $response->getContent();
            $this->assertStringNotContainsString(
                'InvalidArgumentException', 
                $content,
                "Member route {$route} has InvalidArgumentException (likely missing view)"
            );
        }
    }

    /**
     * Test that all dashboard stats are properly defined to prevent undefined index errors.
     */
    public function test_dashboard_stats_properly_defined(): void
    {
        // Admin dashboard
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertViewHas('stats');
        $stats = $response->viewData('stats');
        $this->assertArrayHasKey('total_books', $stats);
        $this->assertArrayHasKey('total_users', $stats);
        $this->assertArrayHasKey('active_loans', $stats);

        // Librarian dashboard
        $response = $this->actingAs($this->librarian)->get('/librarian/dashboard');
        $response->assertViewHas('stats');
        $stats = $response->viewData('stats');
        $this->assertArrayHasKey('pending_requests', $stats);
        $this->assertArrayHasKey('overdue_loans', $stats);
        $this->assertArrayHasKey('pending_reviews', $stats);

        // Member dashboard
        $response = $this->actingAs($this->member)->get('/dashboard');
        $response->assertViewHas('stats');
        $stats = $response->viewData('stats');
        $this->assertArrayHasKey('total_borrowed', $stats);
        $this->assertArrayHasKey('currently_borrowed', $stats);
        $this->assertArrayHasKey('can_borrow', $stats);
    }
}
