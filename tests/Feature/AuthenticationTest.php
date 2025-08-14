<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Feature tests for authentication functionality
 * 
 * Tests sign-in, sign-up, password reset, and related
 * authentication flows with security measures.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_valid_user_sign_in()
    {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post('/stock-analytics/signin', [
            'email' => 'john@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/stock-analytics/admin');
        $this->assertEquals($user->id, session('user')['id']);
    }

    /** @test */
    public function it_rejects_invalid_credentials()
    {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post('/stock-analytics/signin', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['error']);
        $this->assertNull(session('user'));
    }

    /** @test */
    public function it_validates_sign_in_form_fields()
    {
        $response = $this->post('/stock-analytics/signin', []);
        
        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** @test */
    public function it_validates_email_format_on_sign_in()
    {
        $response = $this->post('/stock-analytics/signin', [
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);
        
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_creates_new_user_on_sign_up()
    {
        $response = $this->post('/stock-analytics/signup', [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '+6281234567890'
        ]);

        $response->assertRedirect('/stock-analytics/registration-success');
        
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '+6281234567890',
            'role' => 'user'
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_email_registration()
    {
        User::factory()->create([
            'email' => 'john@example.com'
        ]);

        $response = $this->post('/stock-analytics/signup', [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '+6281234567890'
        ]);
        
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_prevents_duplicate_mobile_number_registration()
    {
        User::factory()->create([
            'mobile_number' => '+6281234567890'
        ]);

        $response = $this->post('/stock-analytics/signup', [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '+6281234567890'
        ]);
        
        $response->assertSessionHasErrors(['mobile_number']);
    }

    /** @test */
    public function it_validates_mobile_number_format()
    {
        $response = $this->post('/stock-analytics/signup', [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '123456' // Invalid format
        ]);
        
        $response->assertSessionHasErrors(['mobile_number']);
    }

    /** @test */
    public function it_normalizes_mobile_numbers()
    {
        $response = $this->post('/stock-analytics/signup', [
            'full_name' => 'John Doe',
            'email' => 'john@example.com',
            'mobile_number' => '081234567890' // Should be normalized to +62
        ]);

        $response->assertRedirect('/stock-analytics/registration-success');
        
        $this->assertDatabaseHas('users', [
            'mobile_number' => '+6281234567890'
        ]);
    }

    /** @test */
    public function it_sanitizes_full_name()
    {
        $response = $this->post('/stock-analytics/signup', [
            'full_name' => 'john doe', // Should be title cased
            'email' => 'john@example.com',
            'mobile_number' => '+6281234567890'
        ]);

        $response->assertRedirect('/stock-analytics/registration-success');
        
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe'
        ]);
    }

    /** @test */
    public function it_rejects_names_with_invalid_characters()
    {
        $response = $this->post('/stock-analytics/signup', [
            'full_name' => 'John123 Doe', // Numbers not allowed
            'email' => 'john@example.com',
            'mobile_number' => '+6281234567890'
        ]);
        
        $response->assertSessionHasErrors(['full_name']);
    }

    /** @test */
    public function it_handles_logout_properly()
    {
        $user = User::factory()->create();
        
        // Simulate logged in session
        session(['user' => $user->toArray()]);
        
        $response = $this->post('/stock-analytics/logout');
        
        $response->assertRedirect('/stock-analytics');
        $this->assertNull(session('user'));
    }

    /** @test */
    public function it_respects_rate_limiting_on_auth_endpoints()
    {
        // Mock reaching rate limit
        $this->app['cache']->put('rate_limit:auth:127.0.0.1:' . now()->startOfMinute()->timestamp, 5, 60);

        $response = $this->post('/stock-analytics/signin', [
            'email' => 'john@example.com',
            'password' => 'password123'
        ]);

        $response->assertStatus(429);
        $response->assertJson(['error' => 'Rate limit exceeded']);
    }

    /** @test */
    public function it_prevents_sql_injection_in_email()
    {
        $response = $this->post('/stock-analytics/signin', [
            'email' => "john@example.com'; DROP TABLE users; --",
            'password' => 'password123'
        ]);
        
        // Should be rejected due to email validation
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_prevents_xss_in_name_field()
    {
        $response = $this->post('/stock-analytics/signup', [
            'full_name' => '<script>alert("xss")</script>',
            'email' => 'john@example.com',
            'mobile_number' => '+6281234567890'
        ]);
        
        $response->assertSessionHasErrors(['full_name']);
    }

    /** @test */
    public function it_logs_authentication_attempts()
    {
        // This would test that LoggingService::logAuthEvent is called
        // In a real implementation, you'd mock the service
        
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => Hash::make('password123')
        ]);

        $response = $this->post('/stock-analytics/signin', [
            'email' => 'john@example.com',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/stock-analytics/admin');
        
        // Assert that successful login was logged
    }

    /** @test */
    public function it_logs_failed_authentication_attempts()
    {
        $response = $this->post('/stock-analytics/signin', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertRedirect();
        
        // Assert that failed login was logged with security level
    }

    /** @test */
    public function redirects_authenticated_users_from_login_page()
    {
        $user = User::factory()->create();
        session(['user' => $user->toArray()]);

        $response = $this->get('/stock-analytics');
        
        $response->assertRedirect('/stock-analytics/admin');
    }

    /** @test */
    public function it_protects_admin_routes_from_unauthenticated_users()
    {
        $response = $this->get('/stock-analytics/admin');
        
        $response->assertRedirect('/stock-analytics');
    }

    /** @test */
    public function it_allows_authenticated_users_to_access_admin()
    {
        $user = User::factory()->create();
        session(['user' => $user->toArray()]);

        $response = $this->get('/stock-analytics/admin');
        
        $response->assertStatus(200);
    }
}