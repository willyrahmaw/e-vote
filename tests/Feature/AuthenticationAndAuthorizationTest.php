<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationAndAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_guest_cannot_access_admin(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('login'));
    }

    public function test_voter_cannot_access_admin_panel(): void
    {
        $voter = User::factory()->voter()->create();

        $response = $this->actingAs($voter)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertStatus(200);
    }

    public function test_admin_can_perform_actions_on_election_index(): void
    {
        $admin = User::factory()->admin()->create();
        $election = \App\Models\Election::factory()->draft()->create([
            'organization_id' => $admin->organization_id,
            'created_by' => $admin->id,
        ]);

        \Livewire\Livewire::actingAs($admin)
            ->test(\App\Livewire\Admin\ElectionIndex::class)
            ->call('publish', $election->id)
            ->call('activate', $election->id)
            ->call('end', $election->id)
            ->assertStatus(200);

        $this->assertEquals(\App\Enums\ElectionStatus::Ended, $election->fresh()->status);
    }
}
