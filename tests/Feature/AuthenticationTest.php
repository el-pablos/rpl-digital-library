<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'librarian']);
        Role::create(['name' => 'member']);
    }

    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
    }

    public function test_register_page_is_accessible(): void
    {
        $response = $this->get('/register');
        
        $response->assertStatus(200);
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        $user->assignRole('member');
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        
        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard'));
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('correct-password'),
        ]);
        
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);
        
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        
        $response = $this->actingAs($user)->post('/logout');
        
        $this->assertGuest();
    }

    public function test_new_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    public function test_guest_is_redirected_from_protected_routes(): void
    {
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('member');
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
    }

    public function test_admin_redirected_to_admin_dashboard(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        
        // Admin should be able to access admin dashboard
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_librarian_redirected_to_librarian_dashboard(): void
    {
        $librarian = User::factory()->create();
        $librarian->assignRole('librarian');
        
        // Librarian should be able to access librarian dashboard
        $response = $this->actingAs($librarian)->get(route('librarian.dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_suspended_user_cannot_borrow(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);
        $user->assignRole('member');
        
        $this->assertFalse($user->canBorrow());
    }

    public function test_active_user_can_borrow(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->assignRole('member');
        
        $this->assertTrue($user->canBorrow());
    }
}
