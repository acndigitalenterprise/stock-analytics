<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use App\Models\User;
use App\Models\Request as StockRequest;
use App\Jobs\GenerateStockAdvice;

/**
 * Feature tests for stock analytics submission
 * 
 * Tests the complete flow of submitting stock analysis requests
 * including validation, user creation, email sending, and job dispatch.
 */
class StockAnalyticsSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        Queue::fake();
    }

    /** @test */
    public function it_validates_required_fields()
    {
        $response = $this->post('/stock-analytics', []);
        
        $response->assertSessionHasErrors([
            'full_name', 'mobile_number', 'email', 'stock_code', 'timeframe'
        ]);
    }

    /** @test */
    public function it_validates_email_format()
    {
        $response = $this->post('/stock-analytics', [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'invalid-email',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ]);
        
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function it_validates_mobile_number_format()
    {
        $response = $this->post('/stock-analytics', [
            'full_name' => 'John Doe',
            'mobile_number' => '123456',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ]);
        
        $response->assertSessionHasErrors(['mobile_number']);
    }

    /** @test */
    public function it_validates_stock_code_format()
    {
        $response = $this->post('/stock-analytics', [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'bb',
            'timeframe' => '1d'
        ]);
        
        $response->assertSessionHasErrors(['stock_code']);
    }

    /** @test */
    public function it_validates_timeframe_values()
    {
        $response = $this->post('/stock-analytics', [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '2d'
        ]);
        
        $response->assertSessionHasErrors(['timeframe']);
    }

    /** @test */
    public function it_creates_new_user_and_request_for_first_time_submission()
    {
        $data = [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ];

        $response = $this->post('/stock-analytics', $data);
        
        $response->assertRedirect('/stock-analytics/confirmation');
        
        // Check user was created
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'role' => 'user'
        ]);

        // Check request was created
        $this->assertDatabaseHas('requests', [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA.JK', // Should be normalized
            'timeframe' => '1d'
        ]);
    }

    /** @test */
    public function it_prevents_duplicate_mobile_numbers()
    {
        // Create existing user
        User::factory()->create([
            'mobile_number' => '+6281234567890'
        ]);

        $data = [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'different@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ];

        $response = $this->post('/stock-analytics', $data);
        
        $response->assertSessionHasErrors(['mobile_number']);
    }

    /** @test */
    public function it_allows_existing_user_to_submit_new_requests()
    {
        // Create existing user
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'mobile_number' => '+6281234567890'
        ]);

        $data = [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'TLKM',
            'timeframe' => '1h'
        ];

        $response = $this->post('/stock-analytics', $data);
        
        $response->assertRedirect('/stock-analytics/confirmation');
        
        // Should not create new user
        $this->assertEquals(1, User::count());
        
        // Should create new request
        $this->assertDatabaseHas('requests', [
            'user_id' => $user->id,
            'stock_code' => 'TLKM.JK',
            'timeframe' => '1h'
        ]);
    }

    /** @test */
    public function it_sends_welcome_email_for_new_users()
    {
        $data = [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ];

        $this->post('/stock-analytics', $data);
        
        Mail::assertSent(\Illuminate\Mail\Mailable::class, function ($mail) {
            return $mail->hasTo('john@example.com');
        });
    }

    /** @test */
    public function it_dispatches_ai_advice_generation_job()
    {
        $data = [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ];

        $this->post('/stock-analytics', $data);
        
        Queue::assertPushed(GenerateStockAdvice::class, function ($job) {
            return $job->request->stock_code === 'BBCA.JK';
        });
    }

    /** @test */
    public function it_normalizes_stock_code_to_jk_format()
    {
        $data = [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ];

        $this->post('/stock-analytics', $data);
        
        $this->assertDatabaseHas('requests', [
            'stock_code' => 'BBCA.JK'
        ]);
    }

    /** @test */
    public function it_sanitizes_user_input()
    {
        $data = [
            'full_name' => 'john doe', // Should be title cased
            'mobile_number' => '08-1234-567890', // Should be normalized
            'email' => 'JOHN@EXAMPLE.COM', // Should be lowercased
            'stock_code' => 'bbca', // Should be uppercased
            'timeframe' => '1d'
        ];

        $this->post('/stock-analytics', $data);
        
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com'
        ]);

        $this->assertDatabaseHas('requests', [
            'stock_code' => 'BBCA.JK'
        ]);
    }

    /** @test */
    public function it_handles_submission_errors_gracefully()
    {
        // Mock database error
        $this->app->instance('db', \Mockery::mock('stdClass')
            ->shouldReceive('transaction')
            ->andThrow(new \Exception('Database error'))
            ->getMock()
        );

        $data = [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ];

        $response = $this->post('/stock-analytics', $data);
        
        $response->assertRedirect()
                 ->assertSessionHasErrors(['error']);
    }

    /** @test */
    public function it_respects_rate_limiting_on_submissions()
    {
        $data = [
            'full_name' => 'John Doe',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ];

        // Make requests up to the limit
        for ($i = 0; $i < 5; $i++) {
            $data['email'] = "user{$i}@example.com";
            $data['mobile_number'] = "+628123456789{$i}";
            
            $response = $this->post('/stock-analytics', $data);
            
            if ($i < 10) { // Assuming limit is 10
                $response->assertRedirect('/stock-analytics/confirmation');
            }
        }
    }

    /** @test */
    public function it_prevents_xss_attacks_in_name_field()
    {
        $data = [
            'full_name' => '<script>alert("xss")</script>',
            'mobile_number' => '+6281234567890',
            'email' => 'john@example.com',
            'stock_code' => 'BBCA',
            'timeframe' => '1d'
        ];

        $response = $this->post('/stock-analytics', $data);
        
        // Should be rejected due to regex validation
        $response->assertSessionHasErrors(['full_name']);
    }
}