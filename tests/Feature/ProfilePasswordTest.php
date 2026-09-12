<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Livewire\Profile\ProfileIndex;
use Tests\TestCase;

class ProfilePasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile(): void
    {
        $response = $this->get(route('profile'));
        $response->assertRedirect(route('login'));
    }

    public function test_voter_can_access_voter_profile(): void
    {
        $voter = User::factory()->voter()->create([
            'name' => 'Budi Pemilih',
            'email' => 'budi@test.com',
            'identifier' => 'NIM12345',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($voter)->get(route('voter.profile'));
        $response->assertStatus(200);
        $response->assertSee('Budi Pemilih');
        $response->assertSee('budi@test.com');
        $response->assertSee('NIM12345');
    }

    public function test_admin_can_access_admin_profile(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin Utama',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.profile'));
        $response->assertStatus(200);
        $response->assertSee('Admin Utama');
        $response->assertSee('admin@test.com');
    }

    public function test_user_cannot_update_password_with_incorrect_current_password(): void
    {
        $user = User::factory()->voter()->create([
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($user)
            ->test(ProfileIndex::class)
            ->set('current_password', 'wrong-current-password')
            ->set('password', 'SecretPass123!')
            ->set('password_confirmation', 'SecretPass123!')
            ->call('updatePassword')
            ->assertHasErrors(['current_password']);

        // Password in DB must remain unchanged
        $user->refresh();
        $this->assertTrue(Hash::check('password', $user->password));
    }

    public function test_user_cannot_update_password_with_mismatched_confirmation(): void
    {
        $user = User::factory()->voter()->create([
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($user)
            ->test(ProfileIndex::class)
            ->set('current_password', 'password')
            ->set('password', 'SecretPass123!')
            ->set('password_confirmation', 'DifferentPass456!')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }

    public function test_user_cannot_update_password_to_same_as_current(): void
    {
        $user = User::factory()->voter()->create([
            'password' => Hash::make('password'),
        ]);

        Livewire::actingAs($user)
            ->test(ProfileIndex::class)
            ->set('current_password', 'password')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('updatePassword')
            ->assertHasErrors(['password']);
    }

    public function test_user_can_successfully_update_password(): void
    {
        $user = User::factory()->voter()->create([
            'password' => Hash::make('password'),
            'password_changed_at' => null,
        ]);

        $this->assertTrue($user->isUsingDefaultPassword());

        Livewire::actingAs($user)
            ->test(ProfileIndex::class)
            ->set('current_password', 'password')
            ->set('password', 'NewSecurePassword123!')
            ->set('password_confirmation', 'NewSecurePassword123!')
            ->call('updatePassword')
            ->assertHasNoErrors()
            ->assertDispatched('swal:success');

        $user->refresh();
        $this->assertTrue(Hash::check('NewSecurePassword123!', $user->password));
        $this->assertFalse($user->isUsingDefaultPassword());
        $this->assertNotNull($user->password_changed_at);
    }

    public function test_default_password_warning_hero_banner_displayed_and_hidden_after_change(): void
    {
        $user = User::factory()->voter()->create([
            'password' => Hash::make('password'),
        ]);

        // When using default password, hero banner is visible on dashboard
        $response = $this->actingAs($user)->get(route('voter.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Segera Ganti Kata Sandi Bawaan Anda!');

        // Update password to new one
        Livewire::actingAs($user)
            ->test(ProfileIndex::class)
            ->set('current_password', 'password')
            ->set('password', 'BrandNewPassword2026!')
            ->set('password_confirmation', 'BrandNewPassword2026!')
            ->call('updatePassword');

        // After change, hero banner is no longer displayed on dashboard
        $responseAfter = $this->actingAs($user->fresh())->get(route('voter.dashboard'));
        $responseAfter->assertStatus(200);
        $responseAfter->assertDontSee('Segera Ganti Kata Sandi Bawaan Anda!');
    }
}
