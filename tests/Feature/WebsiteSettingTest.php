<?php

namespace Tests\Feature;

use App\Livewire\Admin\WebsiteSetting;
use App\Models\AuditLog;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class WebsiteSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_website_settings(): void
    {
        $response = $this->get(route('admin.settings'));
        $response->assertRedirect(route('login'));
    }

    public function test_voter_cannot_access_website_settings(): void
    {
        $voter = User::factory()->voter()->create();

        $response = $this->actingAs($voter)->get(route('admin.settings'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_website_settings(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.settings'));
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Website');
        $response->assertSee('Identitas & Branding', false);
    }

    public function test_admin_can_update_website_settings(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(WebsiteSetting::class)
            ->set('app_name', 'Sistem Pemilihan Digital 2026')
            ->set('institution_name', 'BEM Universitas Nusantara')
            ->set('app_tagline', 'Integritas, Transparansi, dan Akuntabilitas')
            ->set('contact_email', 'kpu@nusantara.ac.id')
            ->set('contact_phone', '+62 899-1234-5678')
            ->set('enable_public_monitor', false)
            ->set('enable_ballot_verification', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertDispatched('swal:success');

        // Verify updated in Setting model
        $this->assertEquals('Sistem Pemilihan Digital 2026', Setting::get('app_name'));
        $this->assertEquals('BEM Universitas Nusantara', Setting::get('institution_name'));
        $this->assertEquals('Integritas, Transparansi, dan Akuntabilitas', Setting::get('app_tagline'));
        $this->assertEquals('kpu@nusantara.ac.id', Setting::get('contact_email'));
        $this->assertEquals('0', Setting::get('enable_public_monitor'));

        // Verify audit log
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'UPDATE_WEBSITE_SETTINGS',
            'user_id' => $admin->id,
        ]);
    }

    public function test_website_setting_validation_errors(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(WebsiteSetting::class)
            ->set('app_name', '')
            ->set('institution_name', '')
            ->set('contact_email', 'bukan-email-valid')
            ->call('save')
            ->assertHasErrors(['app_name', 'institution_name', 'contact_email']);
    }
}
