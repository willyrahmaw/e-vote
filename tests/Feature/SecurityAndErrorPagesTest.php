<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityAndErrorPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_responses_contain_security_headers(): void
    {
        $response = $this->get(route('login'));

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy');
        $response->assertHeader('Content-Security-Policy');
    }

    public function test_login_rate_limiting_blocks_brute_force_attacks(): void
    {
        RateLimiter::clear('attacker@example.com|127.0.0.1');

        // Perform 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post(route('login.post'), [
                'email' => 'attacker@example.com',
                'password' => 'wrongpassword' . $i,
            ]);
            $response->assertSessionHasErrors('email');
        }

        // 6th attempt should trigger rate limiting lockout
        $responseBlocked = $this->post(route('login.post'), [
            'email' => 'attacker@example.com',
            'password' => 'wrongpassword6',
        ]);

        $responseBlocked->assertSessionHasErrors('email');
        $errorMessage = session('errors')->first('email');
        $this->assertStringContainsString('Terlalu banyak percobaan masuk yang gagal', $errorMessage);
    }

    public function test_inactive_admin_cannot_access_admin_panel(): void
    {
        $inactiveAdmin = User::factory()->admin()->create([
            'is_active' => false,
        ]);

        $response = $this->actingAs($inactiveAdmin)->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->voter()->create([
            'email' => 'inactive@test.com',
            'password' => Hash::make('password123'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'inactive@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_custom_403_error_page_renders_cleanly(): void
    {
        $voter = User::factory()->voter()->create();

        $response = $this->actingAs($voter)->get(route('admin.dashboard'));
        $response->assertStatus(403);
        $response->assertSee('Error 403');
        $response->assertSee('Akses Ditolak');
    }

    public function test_custom_404_error_page_renders_cleanly(): void
    {
        $response = $this->get('/non-existent-random-route-xyz');
        $response->assertStatus(404);
        $response->assertSee('Error 404');
        $response->assertSee('Halaman Tidak Ditemukan');
    }
}
